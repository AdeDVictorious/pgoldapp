<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Traits\HttpResponses;
use App\Http\Requests\CreditWalletRequest;
use App\Http\Resources\UserResource;
use App\Jobs\CreateUserWallets;
use App\Filters\WalletTransactionFilter;
use Illuminate\Support\Str; 

use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    use HttpResponses; 
    
    //get_user function
    public function getUser(Request $request)
    {
        try {
            //get the user_id form decoded jwt 
            $decoded = $request->attributes->get('jwt');

            //check if the user exist
            $user = User::where('id', $decoded->id)->first();

            if (!$user) {
                //return response
                return $this->error([], 'user does not', 404);
            }

            $payload = new UserResource($user);
            
            //return response
            return $this->success([
                'user' => $payload,
            ], 'User record found successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->error([
                'error' => $th->getMessage()
            ], 'Error occurred', 500);
        }
    } 

    //get_user wallets function
    public function userWallets(Request $request)
    {
        try {
            $decoded = $request->attributes->get('jwt');

            $user = User::with(['wallet', 'cryptoWallets'])
                ->find($decoded->id);

            if (!$user) {
                return $this->error([], 'User does not exist', 404);
            }

            $payload = new UserResource($user);

            return $this->success([
                'naira_wallet' => $user->wallet,
                'crypto_wallets' => $user->cryptoWallets,
                'user' => $payload,
            ], 'Wallets retrieved successfully', 200);

        } catch (\Throwable $th) {
            return $this->error([
                'error' => $th->getMessage()
            ], 'Error occurred', 500);
        }
    }


    //fund user credit or deposit to wallet function
    public function creditWallet(CreditWalletRequest $request)
    {
        DB::beginTransaction();

        try {

            // Validate request
            $valid = $request->validated();

            if (empty($valid)) {
                return $this->error([], 'Kindly fill all required fields', 422);
            }

            //Get the user from the JWT attribute set in your middleware
            $decoded = $request->attributes->get('jwt');
            $user = User::find($decoded->id);

            if (!$user) {
                return $this->error([], 'User does not exist', 404);
            }

            // check if the wallet exists
            $wallet = Wallet::where('user_id', $user->id)->first();

            if (!$wallet) {
                return $this->error([], 'Wallet not found', 404);
            }

            //Update the wallet balance
            $wallet->increment('amount', $request->amount);

            //Create the transaction record
            $transaction = WalletTransaction::create([
                'user_id'     => $user->id,
                'type'        => $request->type ?? 'credit',
                'amount'      => $request->amount,
                'currency'    => $request->currency ?? 'NG', 
                'status'      => 'completed',
                'reference'   => 'TRX-' . strtoupper(uniqid()),
                'description' => $request->description ?? 'Funding wallet',
            ]);

            DB::commit();

            return $this->success([
                'balance' => $wallet->fresh()->amount,
                'transaction' => $transaction,
            ], 'Wallet credited successfully', 200);

        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->error([
                'error' => $th->getMessage()
            ], 'Transaction failed', 500);
        }
    }


    //users wallet transaction
    public function userTransactions(Request $request)
    {
        try {
            //code...
            // limit from the request,
            $limit = $request->query('limit', 15); 

            //the query with your filter
            $filter = new WalletTransactionFilter();
            $queryItems = $filter->transform($request); 

            $transactions = WalletTransaction::where('user_id', $request->attributes->get('jwt')->id)
                ->where($queryItems)
                ->orderBy('created_at', 'desc')
                ->paginate($limit) 
                ->withQueryString(); 

            return $this->success($transactions, 'Transactions retrieved');
            

        } catch (\Throwable $th) {
            //throw $th;
            return $this->error([
                'error' => $th->getMessage()
            ], 'Error occurred', 500);
        }

    }



}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Service\CoinGeckoService;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\CryptoWallet;
use App\Models\Trade;
use App\Traits\HttpResponses;
use App\Http\Requests\CreditCryptoWalletRequest;
use App\Http\Resources\UserResource;
use App\Jobs\CreateUserWallets;
use App\Filters\WalletTransactionFilter;
use Illuminate\Support\Str; 

use Illuminate\Support\Facades\Log;

class TradeController extends Controller
{
    use HttpResponses; 
    //
    protected CoinGeckoService $coinGeckoService;

    public function __construct(CoinGeckoService $coinGeckoService)
    {
        $this->coinGeckoService = $coinGeckoService;
    }

    // buy crypto using the naira wallet
    public function buyCrypto(CreditCryptoWalletRequest $request)
    {
        DB::beginTransaction();

        try {
            $valid = $request->validated();

            $currency = strtoupper($valid['currency']);
            $nairaAmount = (float) $valid['amount'];

            $decoded = $request->attributes->get('jwt');
            $userId = $decoded->id;

            $user = User::findOrFail($userId);

            $wallet = Wallet::where('user_id', $userId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($wallet->amount <= 0) {
                return $this->error([], 'Insufficient balance', 422);
            }

            //  Get live crypto rate from CoinGecko
            $rate = $this->coinGeckoService->getRate($currency);

            Log::info("Rate for {$currency}: " . $rate);

            $fee = $nairaAmount * 0.025;
            $totalCost = $nairaAmount + $fee;

            if ($wallet->amount < $totalCost) {
                return $this->error([], 'Insufficient balance', 422);
            }

            // Debit Naira wallet
            $wallet->decrement('amount', $totalCost);

            $cryptoWallet = CryptoWallet::where('user_id', $userId)
                ->where('currency', $currency)
                ->lockForUpdate()
                ->firstOrFail();

            $cryptoAmount = round($nairaAmount / $rate, 8);

            $cryptoWallet->increment('amount', $cryptoAmount);

            $transaction = Trade::create([
                'user_id' => $userId,
                'type' => 'buy',
                'amount' => $nairaAmount,
                'crypto_currency' => $currency,
                'crypto_amount' => $cryptoAmount,
                'naira_amount' => $nairaAmount,
                'status' => 'completed',
                'rate' => $rate,
                'fee' => $fee,
                'reference' => 'TRX-' . strtoupper(uniqid()),
            ]);

            DB::commit();

            return $this->success([
                'wallet_balance' => $wallet->fresh()->amount,
                'crypto_balance' => $cryptoWallet->fresh()->amount,
                'transaction' => $transaction,
            ], 'Crypto purchased successfully', 200);

        } catch (\Throwable $th) {

            DB::rollBack();

            return $this->error([
                'error' => $th->getMessage()
            ], 'Transaction failed', 500);
        }
    }

    // sell crypto or convert to naira_wallet
    public function sellCrypto(CreditCryptoWalletRequest $request)
    {
        DB::beginTransaction();

        try {

            // Validate request
            $valid = $request->validated();

            if (empty($valid)) {
                return $this->error([], 'Kindly fill all required fields', 422);
            }

            $decoded = $request->attributes->get('jwt');
            $userId = $decoded->id;

            //Setup Variables from Request
            $cryptoToSell = $request->amount; // Amount of Crypto (e.g., 0.005 BTC)
            $currency = $request->currency;    // e.g., 'BTC'

            // Get and Lock Wallets
            $cryptoWallet = CryptoWallet::where('user_id', $userId)
                ->where('currency', $currency)
                ->lockForUpdate()
                ->first();

            $nairaWallet = Wallet::where('user_id', $userId)
                ->lockForUpdate()
                ->first();

            //Validation
            if (!$cryptoWallet || $cryptoWallet->amount < $cryptoToSell) {
                return $this->error([], 'Insufficient crypto balance', 422);
            }

            if (!$nairaWallet) {
                return $this->error([], 'Naira wallet not initialized', 404);
            }

            // 4. Get Rate and Calculate Naira Value
            $rate = $this->coinGeckoService->getRate($currency);
            $grossNaira = $cryptoToSell * $rate;

            // Calculate Fee (2.5%)
            $fee = $grossNaira * 0.025;
            $netNaira = $grossNaira - $fee;

            //Execute Transfers
            $cryptoWallet->decrement('amount', $cryptoToSell);
            $nairaWallet->increment('amount', $netNaira);

            // Record the Trade to trade_record
            $transaction = Trade::create([
                'user_id'         => $userId,
                'type'            => 'sell',
                'status'          => 'completed',
                'naira_amount'    => $netNaira,
                'crypto_amount'   => $cryptoToSell,
                'crypto_currency' => $currency,
                'fee'             => $fee,
                'rate'            => $rate,
                'reference'       => 'SEL-' . strtoupper(uniqid()),
            ]);

            DB::commit();

            return $this->success([
                'naira_balance'  => $nairaWallet->fresh()->amount,
                'crypto_balance' => $cryptoWallet->fresh()->amount,
                // 'trade'          => new TradeResource($transaction),
            ], 'Crypto sold successfully', 200);

        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->error(['error' => $th->getMessage()], 'Transaction failed', 500);
        }
    }

    //users crypto_wallet transaction
    public function userCryptoTransactions(Request $request)
    {
        try {
            //code...
            // limit from the request,
            $limit = $request->query('limit', 15); 

            //the query with your filter
            $filter = new WalletTransactionFilter();
            $queryItems = $filter->transform($request); 

            $transactions = Trade::where('user_id', $request->attributes->get('jwt')->id)
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



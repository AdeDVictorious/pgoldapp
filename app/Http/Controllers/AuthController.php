<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Wallet;
use App\Traits\HttpResponses;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Http\Middleware\JwtAuth;
use App\Jobs\CreateUserWallets;
use Illuminate\Support\Facades\Log;


class AuthController extends Controller
{
    use HttpResponses; 

    //Register new user function
    public function register(RegisterRequest $request)
    {
        DB::beginTransaction();

        try {

            // Validate request
            $valid = $request->validated();

            if (empty($valid)) {
                return $this->error([], 'Kindly fill all required fields', 422);
            }

            $email = $valid['email'];


            // Check if user already exists
            $get_user = User::where('email', $email)->first();


            if ($get_user) {
                return $this->error([], 'User already exists', 400);
            }

            // Create user with HASHED password
            $user = User::create([
                'firstname' => $valid['firstname'],
                'lastname'  => $valid['lastname'],
                'email'     => $valid['email'],
                'password'  => Hash::make($valid['password']),
            ]);

            //create wallet for new user
            $wallet = Wallet::create([
                'user_id' => $user->id,
                'balance' => 0,
                'currency' => 'NG'
            ]);

            // Generate JWT
            $token = JwtAuth::generate([
                'id' => $user->id,
                'email' => $user->email,
            ]);

            DB::commit();   // commit if everything succeeds

            //dispatch the job
            CreateUserWallets::dispatch($user)->afterCommit();

            return $this->success([
                'user' => $user,
                'token' => $token,
            ], 'User registered successfully', 201);

        } catch (\Throwable $th) {

            DB::rollBack(); 

            return $this->error([
                'error' => $th->getMessage()
            ], 'Error occurred while creating user', 500);
        }
    }

    //user login function
    public function login(LoginRequest $request)
    {
        try {
            //check if the data is empty      
            $valid =  $request->validated();

            $email = $valid['email'];
            $password = $valid['password'];

            $user = User::where('email', $email)->first();

            //if user does not exist
            if(!$user){
                return $this->error([], 'user does not exist', 404);
            }

            //if user exist but if password does match
            if ($user && !Hash::check($password, $user->password)) {
                return $this->error([], 'incorrect email or password', 400);
            }

            // Generate JWT
            $token = JwtAuth::generate([
                'id' => $user->id,
                'email' => $email,
            ]);

            $payload = new UserResource($user);
            
            //return response
            return $this->success([
                'user' => $user,
                'token' => $token,
            ], 'User login successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->error([
                'error' => $th->getMessage()
            ], 'Error occurred', 500);
        }
    }    

}

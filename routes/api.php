<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TradeController;




/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register A-PI routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


//route unprotected,without jwt token or open route
Route::controller(AuthController::class)->group(function () {
    // POST
    Route::post('/signup',  'register')->name('api.register');
    Route::post('/login',  'login')->name('api.login');
});



////----- token authorization route -----/////
Route::middleware('jwt.auth')->group(function () {

    //users route controller
    Route::controller(UserController::class)->group(function () {

        // GET
        Route::get('/user', 'getUser')->name('getuser.user_id');
        Route::get('/user/wallets', 'userWallets')->name('user.wallets');
        Route::get('/wallet/transactions', 'userTransactions')->name('user.wallet_transactions');

        // //POST
        Route::post('/credit/wallet', 'creditWallet')->name('user.credit_wallet');
    });

    //order route controller
    Route::controller(TradeController::class)->group(function () {
        // GET
        Route::get('/crypto/transactions', 'userCryptoTransactions')->name('user.crypto_transactions');

        //POST
        Route::post('/buy/crypto', 'buyCrypto')->name('user.buy_crypto');
        Route::post('/sell/crypto', 'sellCrypto')->name('user.sell_crypto');
        Route::post('/convert/crypto', 'convertCrypto')->name('user.convert_crypto');

    });

});



// healthCheck route
Route::get('/healthCheck', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is up and running/working!'
    ]);
});



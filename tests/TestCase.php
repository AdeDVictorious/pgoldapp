<?php
// namespace Tests\Feature;

// use Tests\TestCase;
// use Illuminate\Foundation\Testing\RefreshDatabase;
// use Illuminate\Support\Facades\Http;
// use Laravel\Sanctum\Sanctum;
// use App\Models\User;
// use App\Models\Wallet;
// use App\Models\CryptoWallet;

// class TradeTest extends TestCase
// {
//     use RefreshDatabase;


//         /** @test */
//     public function it_can_create_a_user_with_wallets()
//     {
//         // 1. Test User Schema
//         $user = User::factory()->create([
//             'email' => 'tech@gemini.com'
//         ]);

//         $this->assertDatabaseHas('users', [
//             'email' => 'tech@gemini.com'
//         ]);
//         $this->assertIsString($user->id); // Verify UUID

//         // 2. Test CryptoWallet Schema & Relationship
//         $wallet = CryptoWallet::create([
//             'user_id' => $user->id,
//             'currency' => 'BTC',
//             'balance' => 0.5,
//         ]);

//         $this->assertDatabaseHas('crypto_wallets', [
//             'user_id' => $user->id,
//             'currency' => 'BTC'
//         ]);
        
//         // Verify the relationship works
//         $this->assertEquals($user->id, $wallet->user->id);
//     }

    // public function test_user_can_buy_crypto()
    // {
    //     // Fake CoinGecko API
    //     Http::fake([
    //         'api.coingecko.com/*' => Http::response([
    //             'bitcoin' => [
    //                 'ngn' => 10000000 // 10M per BTC
    //             ]
    //         ], 200)
    //     ]);

    //     $user = User::factory()->create();
    //     Sanctum::actingAs($user);

    //     $wallet = Wallet::create([
    //         'user_id' => $user->id,
    //         'amount' => 100000, 
    //         'currency' => 'NG',
    //         'is_active' => 'true'
    //     ]);

    //     $response = $this->postJson('/api/buy/crypto', [
    //         'currency' => 'BTC',
    //         'naira_amount' => 50000
    //     ]);

    //     $response->assertStatus(201);

    //     // Fee = 1.5% of 50k = 750
    //     $this->assertDatabaseHas('wallets', [
    //         'user_id' => $user->id,
    //         'balance' => 100000 - 50750
    //     ]);

    //     $this->assertDatabaseHas('crypto_wallets', [
    //         'user_id' => $user->id,
    //         'symbol' => 'BTC'
    //     ]);

    //     $this->assertDatabaseHas('trades', [
    //         'user_id' => $user->id,
    //         'type' => 'buy',
    //         'symbol' => 'BTC'
    //     ]);
    // }


    // public function test_user_can_sell_crypto()
    // {
    //     Http::fake([
    //         'api.coingecko.com/*' => Http::response([
    //             'bitcoin' => [
    //                 'ngn' => 10000000
    //             ]
    //         ], 200)
    //     ]);

    //     $user = User::factory()->create();
    //     Sanctum::actingAs($user);

    //     Wallet::create([
    //         'user_id' => $user->id,
    //         'balance' => 0
    //     ]);

    //     CryptoWallet::create([
    //         'user_id' => $user->id,
    //         'symbol' => 'BTC',
    //         'balance' => 0.01
    //     ]);

    //     $response = $this->postJson('/api/trades/sell', [
    //         'symbol' => 'BTC',
    //         'crypto_amount' => 0.005
    //     ]);

    //     $response->assertStatus(201);

    //     // 0.005 * 10,000,000 = 50,000
    //     // Fee 1.5% = 750
    //     // Final = 49,250

    //     $this->assertDatabaseHas('wallets', [
    //         'user_id' => $user->id,
    //         'balance' => 49250
    //     ]);

    //     $this->assertDatabaseHas('crypto_wallets', [
    //         'user_id' => $user->id,
    //         'symbol' => 'BTC',
    //         'balance' => 0.005
    //     ]);

    //     $this->assertDatabaseHas('trades', [
    //         'type' => 'sell',
    //         'symbol' => 'BTC'
    //     ]);
    // }


    // public function test_user_cannot_buy_with_insufficient_balance()
    // {
    //     Http::fake([
    //         'api.coingecko.com/*' => Http::response([
    //             'bitcoin' => [
    //                 'ngn' => 10000000
    //             ]
    //         ], 200)
    //     ]);

    //     $user = User::factory()->create();
    //     Sanctum::actingAs($user);

    //     Wallet::create([
    //         'user_id' => $user->id,
    //         'balance' => 1000
    //     ]);

    //     $response = $this->postJson('/api/trades/buy', [
    //         'symbol' => 'BTC',
    //         'naira_amount' => 50000
    //     ]);

    //     $response->assertStatus(400);

    //     $response->assertJson([
    //         'status' => false
    //     ]);
    // }

    // public function test_buy_fails_if_api_fails()
    // {
    //     Http::fake([
    //         'api.coingecko.com/*' => Http::response([], 500)
    //     ]);

    //     $user = User::factory()->create();
    //     Sanctum::actingAs($user);

    //     Wallet::create([
    //         'user_id' => $user->id,
    //         'balance' => 100000
    //     ]);

    //     $response = $this->postJson('/api/trades/buy', [
    //         'symbol' => 'BTC',
    //         'naira_amount' => 50000
    //     ]);

    //     $response->assertStatus(500);
    // }



// } -->


namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Create the application for testing.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../bootstrap/app.php';
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }
}
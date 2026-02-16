<?php

namespace Tests\Feature\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Wallet;
use App\Models\CryptoWallet;
use App\Models\Trade;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use App\Http\Middleware\JwtAuth;
use Laravel\Sanctum\Sanctum;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;

class SchemaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_user_with_wallets()
    {
        $user = User::factory()->create([
            'firstname' => 'Tech',
            'lastname' => 'guru',
            'email' => 'tech_oq@gmail.com',
            'password' => Hash::make('Password123!'),
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'tech_oq@gmail.com'
        ]);
        $this->assertIsString($user->id);
        Sanctum::actingAs($user);

        $crypto = CryptoWallet::create([
            'user_id' => $user->id,
            'currency' => 'BTC',
            'amount' => 0.5,
        ]);

        $this->assertDatabaseHas('crypto_wallets', [
            'user_id' => $user->id,
            'currency' => 'BTC'
        ]);

        $this->assertEquals($user->id, $crypto->user->id);
    }

    public function test_user_can_buy_crypto()
    {

        $user = User::factory()->create([
            'firstname' => 'Tech',
            'lastname' => 'guru',
            'email' => 'tech_b@gmail.com',
            'password' => Hash::make('Password123!'),
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'tech_b@gmail.com'
        ]);
        $this->assertIsString($user->id);

        Wallet::create([
            'user_id' => $user->id,
            'amount' => 100000,
            'currency' => 'NG',
            // 'is_active' => true
        ]);

        $crypto = CryptoWallet::create([
            'user_id' => $user->id,
            'currency' => 'USDT',
            'amount' => 0.5,
        ]);

        // Generate JWT
        $token = JwtAuth::generate([
            'id' => $user->id,
            'email' => $user->email,
        ]);

        $response = $this->withHeaders([
        'Authorization' => "Bearer {$token}",
        ])->postJson('/api/buy/crypto', [
            // 'user_id' => $user->id,
            'type' => 'buy',
            'currency' => 'USDT',
            'amount' => 5
        ]);

        // $response->dump();       // prints body + decoded json
        // $response->dumpHeaders();

        $response->assertStatus(200);

        // Assert wallet amount updated (column is `amount`)
        $this->assertDatabaseHas('wallets', [
            'user_id' => $user->id,
            // 'amount' => 49250
        ]);

        // crypto wallet uses `currency` and `amount`
        $this->assertDatabaseHas('crypto_wallets', [
            'user_id' => $user->id,
            'currency' => 'USDT'
        ]);

        // trades table uses `type` and `crypto_currency`
        $this->assertDatabaseHas('trades', [
            'user_id' => $user->id,
            'type' => 'buy',
            'crypto_currency' => 'USDT',
            'status' => 'completed'
        ]);
    }

    public function test_user_can_sell_crypto()
    {
        $user = User::factory()->create([
            'firstname' => 'Tech',
            'lastname' => 'guru',
            'email' => 'tech_b@gmail.com',
            'password' => Hash::make('Password123!'),
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'tech_b@gmail.com'
        ]);
        $this->assertIsString($user->id);

        Wallet::create([
            'user_id' => $user->id,
            'amount' => 100000,
            'currency' => 'NG',
        ]);

        $crypto = CryptoWallet::create([
            'user_id' => $user->id,
            'currency' => 'USDT',
            'amount' => 50.5,
        ]);

        // Generate JWT
        $token = JwtAuth::generate([
            'id' => $user->id,
            'email' => $user->email,
        ]);

        $response = $this->withHeaders([
        'Authorization' => "Bearer {$token}",
        ])->postJson('/api/sell/crypto', [
            // 'user_id' => $user->id,
            'type' => 'sell',
            'currency' => 'USDT',
            'amount' => 5
        ]);

        $response->assertStatus(200);

        // Assert wallet amount updated (column is `amount`)
        $this->assertDatabaseHas('wallets', [
            'user_id' => $user->id,
        ]);

        // crypto wallet uses `currency` and `amount`
        $this->assertDatabaseHas('crypto_wallets', [
            'user_id' => $user->id,
            'currency' => 'USDT'
        ]);

        // trades table uses `type` and `crypto_currency`
        $this->assertDatabaseHas('trades', [
            'user_id' => $user->id,
            'type' => 'sell',
            'crypto_currency' => 'USDT',
            'status' => 'completed'
        ]);
    }

    public function test_user_cannot_buy_with_insufficient_balance()
    {
        $user = User::factory()->create([
            'firstname' => 'Tech',
            'lastname' => 'guru',
            'email' => 'tech_b@gmail.com',
            'password' => Hash::make('Password123!'),
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'tech_b@gmail.com'
        ]);
        $this->assertIsString($user->id);

        Wallet::create([
            'user_id' => $user->id,
            'amount' => 100000,
            'currency' => 'NG',
            // 'is_active' => true
        ]);

        $crypto = CryptoWallet::create([
            'user_id' => $user->id,
            'currency' => 'USDT',
            'amount' => 50.5,
        ]);

        // Generate JWT
        $token = JwtAuth::generate([
            'id' => $user->id,
            'email' => $user->email,
        ]);

        $response = $this->withHeaders([
        'Authorization' => "Bearer {$token}",
        ])->postJson('/api/buy/crypto', [
            // 'user_id' => $user->id,
            'type' => 'sell',
            'currency' => 'USDT',
            'amount' => 5000000
        ]);

        $response->assertStatus(422);
    }
    
}
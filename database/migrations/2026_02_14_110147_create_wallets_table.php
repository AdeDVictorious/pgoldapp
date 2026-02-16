<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 20, 4)->default(0.0000);
            $table->string('currency')->default('NGN');
            $table->boolean('is_active')->default(true);      
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }


    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    // One-to-Many: A user has many crypto wallets (BTC, USDT, etc.)
    public function cryptoWallets()
    {
        return $this->hasMany(CryptoWallet::class);
    }
};

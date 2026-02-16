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
        Schema::create('crypto_wallets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->decimal('amount', 20, 8)->default(0.00000000);
            $table->enum('currency', ['USDT', 'ETH', 'BTC'])->default('USDT');
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->string('wallet_address')->unique();
            $table->boolean('is_active')->default(true); 
            $table->timestamps();

            //this prevent duplicate wallets (e.g., one user can only have ONE BTC wallet)
            $table->unique(['user_id', 'currency']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crypto_wallets');
    }
};

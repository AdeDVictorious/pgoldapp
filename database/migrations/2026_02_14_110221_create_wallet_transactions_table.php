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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');            
            $table->enum('currency', ['BTC', 'ETH', 'USDT', 'NG']);
            $table->enum('type', ['credit', 'debit', 'deposit']); // 'buy' (Naira -> Crypto), 'sell' (Crypto -> Naira)
            $table->decimal('amount', 20, 4); // The amount of crypto             
            // Status and Tracking
            $table->enum('status', ['pending', 'completed', 'cancelled', 'failed'])->default('pending');
            $table->string('reference')->unique(); // Unique trade ID (e.g., TRD-1029384)
            $table->string('description')->nullable(); // Unique trade ID (e.g., TRD-1029384)
            
            $table->timestamps();
            
            // Index for history lookups
            $table->index(['user_id', 'status']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};

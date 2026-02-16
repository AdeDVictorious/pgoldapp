<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // public function up(): void
    // {
    //     Schema::create('trades', function (Blueprint $table) {
    //         $table->uuid('id')->primary();
    //         $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
    //         $table->enum('transType', ['buy', 'sell']);
    //         $table->decimal('crypto_amount', 20, 8);
    //         $table->decimal('naira_amount', 20, 8);
    //         $table->decimal('rate', 20, 4);
    //         $table->decimal('fee', 20, 4);
    //         $table->timestamps();
    //     });
    // }


    public function up(): void
    {
        Schema::create('trades', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // This must be foreignUuid to match your User.php
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            
            // Trade Details
            $table->string('type'); // 'buy' or 'sell'
            $table->enum('crypto_currency', ['USDT', 'ETH', 'BTC'])->default('USDT');
            $table->decimal('crypto_amount', 20, 8);
            $table->decimal('naira_amount', 20, 4);
            $table->decimal('rate', 20, 2);
            $table->decimal('fee', 20, 2)->default(0);
            

            // Status and Tracking
            $table->enum('status', ['pending', 'completed', 'cancelled', 'failed'])->default('pending');
            $table->string('reference')->unique(); // For ledger reconciliation
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trades');
    }
};

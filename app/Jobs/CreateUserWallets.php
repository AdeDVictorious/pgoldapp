<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\CryptoWallet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CreateUserWallets implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Use Constructor Promotion to automatically assign $this->user
     */
    public function __construct(public User $user)
    {
        // This runs at the MOMENT of dispatching (in the Controller)
        Log::info("Job Queued for User ID: {$this->user->id}");
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('--- WALLET JOB STARTED ---');
        $currencies = ['BTC', 'USDT', 'ETH'];

        // Use a transaction inside the job so we don't end up with 
        // partial wallets if one creation fails.
        DB::transaction(function () use ($currencies) {
            foreach ($currencies as $currency) {
                CryptoWallet::create([
                    'user_id'  => $this->user->id,
                    'currency' => $currency,
                    'amount'  => 0,
                    // 'wallet_address',
                    // 'is_active'  
                ]);
            }
        });
    }
}
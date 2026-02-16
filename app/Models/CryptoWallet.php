<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str; 

class CryptoWallet extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasUuids, HasFactory, Notifiable;

    // UUID primary key
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id', 
        'amount', 
        'currency', 
        // 'wallet_address',
        // 'is_active'
    ];

    // Cast the amount to a decimal to prevent floating point math errors
    protected $casts = [
        'amount' => 'decimal:8',
    ];

    protected static function booted()
    {
        static::creating(function ($wallet) {
            // This runs right before the record is saved to Postgres
            if (empty($wallet->wallet_address)) {
                $wallet->wallet_address = strtoupper($wallet->currency) . '_' . (string) Str::uuid();
            }
        });
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class, 'wallet_id');
    }
}
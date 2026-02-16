<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasUuids, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    // UUID primary key
    protected $keyType = 'string';
    public $incrementing = false;


    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    // One-to-One: A user has exactly one Naira wallet
    public function wallet()
    {
        return $this->hasOne(Wallet::class, 'user_id');
    }

    // A user has many crypto wallets (BTC, ETH, USDT)
    public function cryptoWallets(): HasMany
    {
        return $this->hasMany(CryptoWallet::class, 'user_id');
    }

    // A user has many trades
    public function trades(): HasMany
    {
        return $this->hasMany(Trade::class, 'user_id');
    }

    // A user has many wallet transactions (the ledger)
    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class, 'user_id');
    }

}

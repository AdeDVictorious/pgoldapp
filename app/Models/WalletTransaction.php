<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class WalletTransaction extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasUuids, HasFactory, Notifiable;

    protected $fillable = [
        'user_id', 'type', 'currency',
        'amount', 'status', 'reference', 'description'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wallet()
    {
        return $this->belongsTo(CryptoWallet::class, 'user_id');
    }

    // Pro-Tip: Link transaction to trade via the reference string
    public function trade()
    {
        return $this->belongsTo(Trade::class, 'reference');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;



class Wallet extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasUuids, HasFactory, Notifiable;

    // UUID primary key
    protected $keyType = 'string';
    public $incrementing = false;
   
    protected $fillable = ['user_id', 'amount', 'currency', 'is_active'];

    // Cast the amount to a decimal to prevent floating point math errors
    protected $casts = [
        'amount' => 'decimal:4',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
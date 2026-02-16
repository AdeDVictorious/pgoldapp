<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
    return [
            'id'         => $this->id,
            'full_name'  => "{$this->firstname} {$this->lastname}",
            'email'      => $this->email,
            'phone'      => $this->phone_number,
            'location'   => [
                'lat'     => (float) $this->lat,
                'lng'     => (float) $this->lng,
                'country' => $this->country,
                'address' => $this->address,
            ],
            'status'     => [
                'is_active'    => $this->is_active,
                'is_suspended' => $this->is_suspended,
                'is_verified'  => !is_null($this->email_verified_at),
            ],
            // Load the wallets Created earlier
            'crypto_wallets'    => CryptoWalletResource::collection($this->whenLoaded('crypto_wallets')),
            'created_at' => $this->created_at->toDateTimeString(),

            ];
        }
    }

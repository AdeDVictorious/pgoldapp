<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CryptoWalletResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    // public function toArray(Request $request): array
    // {
    //     return parent::toArray($request);
    // }

    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'wallet_id'    => $this->wallet_id,
            'amount'       => (float) $this->amount,
            'type'         => $this->type,
            'status'       => $this->status,
            'reference'    => $this->reference,
            'date'         => $this->created_at->toDateTimeString(),
        ];
    }
    
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'type' => $this->type,
            'from_asset' => new AssetResource($this->whenLoaded('fromAsset')),
            'from_network' => new NetworkResource($this->whenLoaded('fromNetwork')),
            'from_address' => $this->from_address,
            'to_asset' => new AssetResource($this->whenLoaded('toAsset')),
            'to_network' => new NetworkResource($this->whenLoaded('toNetwork')),
            'to_address' => $this->to_address,
            'coin_amount' => $this->coin_amount,
            'dollar_amount' => $this->dollar_amount,
            'fee_coin' => $this->fee_coin ?? '',
            'fee_dollar' => $this->fee_dollar ?? '',
            'to_amount' => $this->to_amount ?? '',
            'to_dollar' => $this->to_dollar ?? '',
            'status' => $this->status,
            'date' => $this->created_at
        ];
    }
}

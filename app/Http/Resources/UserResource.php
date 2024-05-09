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
            'id' => $this->id,
            'nickname' => $this->name,
            'userip' => $this->userip,
            'device_id' => $this->device_id,
            'total_asset_amount' => $this->calculateTotalAssetAmount(),
            'assets' => UserAssetResource::collection($this->whenLoaded('assets')),
            'transactions' => TransactionResource::collection($this->whenLoaded('transactions')),
        ];
    }

    private function calculateTotalAssetAmount()
    {
        return $this->assets->sum('pivot.dollar_balance');
    }
}

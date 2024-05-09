<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAssetResource extends JsonResource
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
            'name' => $this->name,
            'symbol' => $this->abbr,
            'logo' => $this->logo,
            'dollar_rate' => $this->rate,
            'user_coin_balance' => $this->pivot->coin_balance,
            'balance_dollar_rate' => $this->pivot->dollar_balance,
        ];
    }
}

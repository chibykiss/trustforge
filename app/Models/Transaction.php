<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','from_asset_id','from_network_id','from_address','to_asset_id','to_network_id',
        'to_address','coin_amount','dollar_amount','fee_coin','fee_dollar', 'to_amount','to_dollar',
        'type','status','created_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function fromAsset()
    {
        return $this->belongsTo(Asset::class,'from_asset_id');
    }
    public function toAsset()
    {
        return $this->belongsTo(Asset::class,'to_asset_id');
    }

    public function fromNetwork()
    {
        return $this->belongsTo(Network::class,'from_network_id');
    }
    public function toNetwork()
    {
        return $this->belongsTo(Network::class,'to_network_id');
    }
}

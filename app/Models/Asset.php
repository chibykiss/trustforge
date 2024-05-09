<?php

namespace App\Models;

use App\Observers\AssetObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([AssetObserver::class])]
class Asset extends Model
{
    use HasFactory;

    protected $fillable = ['name','abbr','logo','rate'];

    public function users()
    {
        return $this->belongsToMany(User::class,'user_assets')->withPivot('coin_balance','dollar_balance')->withTimestamps();
    }

    public function networks()
    {
        return $this->belongsToMany(Network::class,'asset_networks')->withPivot('fee_percentage')->withTimestamps();
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function fromTransactions()
    {
        return $this->hasMany(Transaction::class,'from_asset_id');
    }

    public function toTransactions()
    {
        return $this->hasMany(Transaction::class,'to_asset_id');
    }

    public function fees()
    {
        return $this->hasMany(Fee::class);
    }
}

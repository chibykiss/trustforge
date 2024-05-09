<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Network extends Model
{
    use HasFactory;

    protected $fillable = ['name','symbol','logo'];

    public function assets()
    {
        return $this->belongsToMany(Asset::class,'asset_networks')->withPivot('fee_percentage')->withTimestamps();
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function fromTransactions()
    {
        return $this->hasMany(Transaction::class,'from_network_id');
    }

    public function toTransactions()
    {
        return $this->hasMany(Transaction::class,'to_network_id');
    }

    public function fees()
    {
        return $this->hasMany(Fee::class);
    }
}

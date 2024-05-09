<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = ['asset_id','network_id','address'];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function network()
    {
        return $this->belongsTo(Network::class);
    }


    public function users()
    {
        return $this->belongsToMany(User::class,'user_addresses')->withTimestamps();
    }
}

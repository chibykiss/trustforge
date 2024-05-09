<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    use HasFactory;

    protected $fillable = ['asset_id','network_id','type','percentage'];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function network()
    {
        return $this->belongsTo(Network::class);
    }
}

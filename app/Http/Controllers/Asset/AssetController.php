<?php

namespace App\Http\Controllers\Asset;


use App\Http\Controllers\Controller;
use App\Http\Resources\AssetResource;
use App\Models\Asset;
use App\Traits\JsonResponses;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    use JsonResponses;

    public function __invoke()
    {
        $assets =  Asset::all();

       // return $assets;

        return $this->success(data: AssetResource::collection($assets));
    }
}

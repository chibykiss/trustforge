<?php

namespace App\Http\Controllers\Network;

use App\Http\Controllers\Controller;
use App\Http\Resources\NetworkResource;
use App\Models\Network;
use App\Traits\JsonResponses;
use Illuminate\Http\Request;

class NetworkController extends Controller
{
    use JsonResponses;
    
    public function __invoke()
    {
        $networks = Network::all();

        //return $networks;

        return $this->success(data: NetworkResource::collection($networks));
    }
}

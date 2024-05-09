<?php

namespace App\Http\Controllers\Address;

use App\Http\Controllers\Controller;
use App\Http\Resources\AddressResource;
use App\Models\User;
use App\Traits\JsonResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    use JsonResponses;
    public function getUserAssetAddress($asset,$network)
    {
        $user = User::with('addresses')->where('id',Auth::id())->first();

        //return $user;

        $userAssetAddress = $user->addresses()->with('asset','network')->where([
            ['asset_id',$asset],
            ['network_id',$network]
        ])->first();

        return $this->success(data: new AddressResource($userAssetAddress));
        //return $userAssetAddress;
    }

    public function getUserAddresses()
    {
        $user = User::with('addresses')->where('id',Auth::id())->first();

        $userAddresses = $user->addresses()->with('asset','network')->get();

        return $this->success(data: AddressResource::collection($userAddresses));
    }
}

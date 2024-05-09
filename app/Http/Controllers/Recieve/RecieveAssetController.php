<?php

namespace App\Http\Controllers\Recieve;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RecieveAssetController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'from' => 'required|numeric|exists:assets,id',
            'from_network' => 'required|numeric|exists:networks,id',
            'from_address' => 'required|string|exists:addresses,address',
            'to' => 'required|numeric|exists:assets,id',
            'to_network' => 'required|numeric|exists:networks,id',
            'to_address' => 'required|string',
            'amount' => 'required|numeric'
        ]);


        
    }
}

<?php

namespace App\Http\Controllers\Swap;

use App\Events\TransactionCreated;
use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Fee;
use App\Models\Transaction;
use App\Traits\JsonResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SwapAssetController extends Controller
{
    use JsonResponses;
    
    public function __invoke(Request $request)
    {

        $request->validate([
            'from' => 'required|numeric|exists:assets,id',
            'from_network' => 'required|numeric|exists:networks,id',
            'from_address' => 'required|string|exists:addresses,address',
            'to' => 'required|numeric|exists:assets,id',
            'to_network' => 'required|numeric|exists:networks,id',
            'to_address' => 'required|string|exists:addresses,address',
            'amount' => 'required|numeric'
        ]);

            //check if user has coin in his balanace
            $balance = DB::table('user_assets')->where([
                ['user_id',Auth::id()],
                ['asset_id',$request->from]
            ])->first()->coin_balance;
    
            if($request->amount > $balance)
            {
                return $this->error(message:'you do not have enough balance');
            }
        

        //get transaction fee ---- this has to be removed
        $fee = Fee::select('percentage')->where([
            ['asset_id',$request->from],
            ['network_id',$request->from_network],
            ['type','swap']
        ])->first();

        if(!$fee)
        {
            return $this->error(message:'Transaction fee not set');
        }

        //fee percentage to amount in coin
        $charge = ($fee->percentage/100) * $request->amount;

        //get asset rate
        $rate = Asset::where('id',$request->from)->first()->rate;

        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'from_asset_id' => $request->from,
            'from_network_id' => $request->from_network,
            'from_address' => $request->from_address,
            'to_asset_id' => $request->to,
            'to_network_id' => $request->to_network,
            'to_address' => $request->to_address,
            'coin_amount' => $request->amount,
            'dollar_amount' => $request->amount * $rate,
            'fee_coin' => $charge,
            'fee_dollar' => $charge * $rate,
            'type' => 'swap',
        ]);

        TransactionCreated::dispatch($transaction);
        return $transaction;
    }
}

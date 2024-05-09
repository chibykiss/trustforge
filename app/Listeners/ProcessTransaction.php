<?php

namespace App\Listeners;

use App\Events\TransactionCreated;
use App\Models\Asset;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessTransaction implements ShouldQueue
{

    public $delay = 60;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TransactionCreated $event): void
    {
        //Log::channel('forge')->info('transaction',$event->transaction->toArray());

        $transaction = $event->transaction;

        $frate = Asset::where('id',$transaction->from_asset_id)->first()->rate;
        $trate = Asset::where('id',$transaction->to_asset_id)->first()->rate;
      

        if($transaction->type === 'swap')
        {
               //remove the fee, before converting
            $amount_to_convert = $transaction->coin_amount - $transaction->fee_coin;

            $to_amount = $this->roundDecimal(($amount_to_convert * $frate)/$trate);

            $to_dollar = $to_amount * $trate;

            //subtract the amount from the from asset
            DB::table('user_assets')->where([
                ['user_id',$transaction->user_id],
                ['asset_id',$transaction->from_asset_id]
            ])->decrementEach(['coin_balance' => $transaction->coin_amount,'dollar_balance' => $transaction->coin_amount * $frate]);
    
            //add the conversion to the to asset
            DB::table('user_assets')->where([
                ['user_id',$transaction->user_id],
                ['asset_id',$transaction->to_asset_id]
            ])->incrementEach(['coin_balance' => $to_amount,'dollar_balance' => $to_dollar]);

            $transaction->update([
                'to_amount' => $to_amount,
                'to_dollar' => $to_dollar,
                'status' => 'completed'
            ]);

        }elseif($transaction->type === 'send')
        {
            $amount = $transaction->coin_amount + $transaction->fee_coin;

            DB::table('user_assets')->where([
                ['user_id',$transaction->user_id],
                ['asset_id',$transaction->from_asset_id]
            ])->decrementEach(['coin_balance' => $amount,'dollar_balance' => $amount * $frate]);

            $transaction->update([
                'to_amount' => $transaction->coin_amount,
                'to_dollar' => $transaction->coin_amount * $trate,
                'status' => 'completed'
            ]);
        }else{

            DB::table('user_assets')->where([
                ['user_id',$transaction->user_id],
                ['asset_id',$transaction->to_asset_id]
            ])->incrementEach(['coin_balance' => $transaction->coin_amount,'dollar_balance' => $transaction->coin_amount * $trate]);

            $transaction->update([
                'to_amount' => $transaction->coin_amount,
                'to_dollar' => $transaction->coin_amount * $trate,
                'status' => 'completed'
            ]);
        }
    }

    private function roundDecimal($number) {
        if (strpos($number, '.') !== false) {
            $number = round($number,10);
          }
          
          return $number;
      }
}

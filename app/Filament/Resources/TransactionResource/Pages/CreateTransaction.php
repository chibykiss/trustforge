<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\Asset;
use App\Models\Fee;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    private $created_transaction;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        

        if($data['type'] === 'recieve'){
              
            $rate = Asset::where('id',$data['to_asset_id'])->first()->rate;

            $data['dollar_amount'] = $data['coin_amount'] * $rate;

            return $data;
        }

            //check if user has coin in his balanace

            $balance = DB::table('user_assets')->where([
                ['user_id',$data['user_id']],
                ['asset_id',$data['from_asset_id']]
            ])->first()->coin_balance;
    
            if($data['coin_amount'] > $balance)
            {
                Notification::make()
                ->danger()
                ->body('you dont have enough balance')
                ->send();
                $this->halt();
            }
        

        //get transaction fee
        $fee = Fee::select('percentage')->where([
            ['asset_id',$data['from_asset_id']],
            ['network_id',$data['from_network_id']],
            ['type',$data['type']]
        ])->first();

        if(!$fee)
        {
            Notification::make()
            ->danger()
            ->body('No fee has been set for this asset')
            ->send();

            $this->halt();
        }

        //fee percentage to amount in coin
        $charge = ($fee->percentage/100) * $data['coin_amount'];

        //get asset rate
        $rate = Asset::where('id',$data['from_asset_id'])->first()->rate;

        $data['dollar_amount'] = $data['coin_amount'] * $rate;

        $data['fee_coin'] = $charge;

        $data['fee_dollar'] = $charge * $rate;

    
        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $this->created_transaction = static::getModel()::create($data);
        return $this->created_transaction;
    }

    protected function afterCreate(): void
    {
        $transaction = $this->created_transaction;

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
                'to_dollar' => $to_dollar
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
                'to_dollar' => $transaction->coin_amount * $trate
            ]);
        }else{

            DB::table('user_assets')->where([
                ['user_id',$transaction->user_id],
                ['asset_id',$transaction->to_asset_id]
            ])->incrementEach(['coin_balance' => $transaction->coin_amount,'dollar_balance' => $transaction->coin_amount * $trate]);

            $transaction->update([
                'to_amount' => $transaction->coin_amount,
                'to_dollar' => $transaction->coin_amount * $trate
            ]);
        }
        

     
    }


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    private function roundDecimal($number) {
        if (strpos($number, '.') !== false) {
            $number = round($number,10);
          }
          
          return $number;
      }
        
}

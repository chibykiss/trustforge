<?php

namespace App\Http\Controllers\Schedule;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AutomatePrice extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
     
        $assetp = $this->assetPrices();

        Log::channel('forge')->info('update asset price command ran');

        $userb = $this->userBalance();

        Log::channel('forge')->info('user_balance price command ran');
    }

    private function assetPrices()
    {
        $assets = Asset::all();

        $new_arr = [];

        foreach($assets as $asset){
            if($asset->automatic_price === 1)
            {
               $new_arr[] = $this->getAutoRate($asset->abbr,$asset->name);
                $price = $this->getAutoRate($asset->abbr,$asset->name);
                $asset->rate = $price;
                $asset->save();
            }
        }

        return $new_arr;
    }

    private function userBalance()
    {
        $asset_prices = $this->getRecentAssetPrices();

        //return $asset_prices;

        $users = User::with('assets')->whereNotNull('phrase')->get();

        //return $users;

       $prices = [];
       
        foreach ($users as $user)
        {
            if($user->automatic_price === 1)
            {
                foreach ($user->assets as $user_asset)
                {
                    $price_value = $this->customRound($asset_prices[$user_asset->abbr]);

                    $prices[] = $user_asset->pivot->coin_balance * $price_value;

                    $new_bal = $user_asset->pivot->coin_balance * $price_value;

                    $user->assets()->updateExistingPivot($user_asset->id, ['dollar_balance' => $new_bal]);
                }
            }
        }

        return $prices;

    }

    private function getRecentAssetPrices()
    {
        $prices = Asset::select(['abbr','rate'])->get()->toArray();
        // ->pluck(['abbr','rate'])->toArray();

        $result = [];
        foreach ($prices as $item) {
            $result[$item['abbr']] = $item['rate'];
        }

        return $result;
    }

    private function getAutoRate($symbol,$name)
    {
        $response = Http::withHeaders([
            'X-CMC_PRO_API_KEY' => config('services.coinmarketcap.endpoint')
        ])->get("https://pro-api.coinmarketcap.com/v2/tools/price-conversion?amount=1&symbol=$symbol&convert=USD");

        
        if($response->status() !== 200)
        {
            return $response->object();
        }
        
       // $body = new Fluent($response->object());
        $body = $response->object();

        $data = $body?->data;

        //return $data;

        if(is_array($data))
        {
            $object = $this->findObjectByName($data,$name);
            if ($object) $usd_price = $object?->quote?->USD?->price;
            //return $object;
        }else{
            $usd_price = $data?->quote?->USD?->price;
        }

        return $usd_price;
    }

    private  function findObjectByName($array, $name) {
        $lowerCaseName = strtolower($name);
        foreach ($array as $obj) {
            if (stripos($obj?->name, $lowerCaseName) !== false) {
                return $obj;
            }
        }
        return null; // Return null if no match is found
    }

    private function customRound($number)
    {
        if ((int)$number != 0) {
            return round($number, 2);
        } else {
            return $number;
        }
    }
}

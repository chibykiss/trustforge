<?php

namespace App\Console\Commands;

use App\Models\Asset;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateAssetPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-asset-prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
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

        Log::channel('forge')->info('update price command ran',$new_arr);
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
            if (stripos($obj->name, $lowerCaseName) !== false) {
                return $obj;
            }
        }
        return null; // Return null if no match is found
    }
}

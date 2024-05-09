<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\JsonResponses;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use JsonResponses;
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $request->validate(['pin' => 'required|string']);

        $user = User::where([
            ['device_id',$request->device_id],
            ['pin',$request->pin]
        ]);

        //return $user;

        if($user->exists())
        {
            
            $user = $user->first();

            $user = $user->load([
                'assets',
                'transactions' => function ($query) {
                    $query->with('toAsset', 'fromAsset');
                }]);

            return $this->success(message:'logged in',data:[
                'token' =>   $user->createToken($request->device_id)->plainTextToken,
                //'user' => $user,
                'user' => new UserResource($user),
            ]);
        }

        return $this->error(message:'Invalid credentials',code:404);
    }
}

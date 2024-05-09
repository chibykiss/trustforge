<?php

use App\Http\Controllers\Address\AddressController;
use App\Http\Controllers\Asset\AssetController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Network\NetworkController;
use App\Http\Controllers\Price\AutomatePriceController;
use App\Http\Controllers\Recieve\RecieveAssetController;
use App\Http\Controllers\Send\SendAssetController;
use App\Http\Controllers\Swap\SwapAssetController;
use App\Http\Controllers\Transactions\HistoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('auth/register', [AuthController::class,'register']);
Route::get('auth/get-phrase', [AuthController::class,'getPhrase']);
Route::post('auth/login', LoginController::class);  
Route::get('auth/logout', [AuthController::class,'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function(){
    Route::get('test', [AuthController::class,'test']);

    Route::get('automate/assets-price',[AutomatePriceController::class,'assetPrice']);
    Route::get('automate/user-balance',[AutomatePriceController::class,'userBalance']);

    Route::get('assets', AssetController::class);
    Route::get('networks', NetworkController::class);

    Route::post('swap-asset', SwapAssetController::class);

    Route::post('send-asset', SendAssetController::class);

    Route::post('recieve-asset', RecieveAssetController::class);

    Route::get('transaction-history',[HistoryController::class,'alltransactions']);
    Route::get('transaction/{id}',[HistoryController::class,'assetTransactions']);

    Route::get('asset-address/{asset}/{network}', [AddressController::class,'getUserAssetAddress']);
    Route::get('user-addresses', [AddressController::class,'getUserAddresses']);
});
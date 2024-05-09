<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Traits\JsonResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    use JsonResponses;
    public function allTransactions()
    {
        $transactions = Transaction::with(['fromAsset','toAsset'])
        ->where('user_id',Auth::id())->get();

        return $this->success(data: TransactionResource::collection($transactions));
        //  return $transactions;
    }

    public function assetTransactions($id)
    {
        $transactions = Transaction::with(['fromAsset','toAsset'])
        ->where(function ($query) use ($id) {
            $query->where('user_id', Auth::id())
                ->where(function ($subQuery) use ($id) {
                    $subQuery->where('from_asset_id', $id)
                        ->orWhere('to_asset_id', $id);
                });
        })
        ->get();

        return $this->success(data: TransactionResource::collection($transactions));
        //return $transactions;
    }
}

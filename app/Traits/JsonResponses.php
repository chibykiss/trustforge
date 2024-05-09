<?php

namespace App\Traits;

trait JsonResponses
{
    public function success($status = 'success', $message = 'request was successful', $data = [], int $code = 200)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ],$code);
    }



    public function error($status = 'error', $message = 'an error occured', $data = [], int $code = 200)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ],$code);
    }
}

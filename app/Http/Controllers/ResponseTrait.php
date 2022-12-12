<?php

namespace App\Http\Controllers;

trait ResponseTrait
{
    public function success($message = '', $data = [], $status = 200)
    {
        return response([
            'success' => true,
            'data' => $data,
            'message' => $message,
        ], $status);
    }

    public function failure($message = '', $status = 422)
    {
        return response([
            'success' => false,
            'message' => $message,
        ], $status);
    }
}

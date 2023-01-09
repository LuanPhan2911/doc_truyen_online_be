<?php

namespace App\Traits;

trait ResponseTrait
{
    public function success($data = [], $status = 200)
    {
        return response([
            'success' => true,
            'data' => $data,
        ], $status);
    }

    public function failure($error = [], $status = 400)
    {
        return response([
            'success' => false,
            'error' => $error
        ], $status);
    }
}

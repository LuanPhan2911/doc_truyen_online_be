<?php

namespace App\Traits;

trait ResponseTrait
{
    public function success($response = [], $status = 200)
    {
        $response['success'] = true;
        return response($response, $status);
    }

    public function failure($response = [], $status = 400)
    {
        $response['success'] = false;
        return response($response, $status);
    }
}

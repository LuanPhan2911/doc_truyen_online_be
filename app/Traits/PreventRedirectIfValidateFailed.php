<?php

namespace App\Traits;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

trait PreventRedirectIfValidateFailed
{
    protected  function failedValidation(Validator $validator)
    {
        $errors = empty($this->hiddenError) ? $validator->errors() : null;

        throw new HttpResponseException(response()->json(
            [
                'errors' => $errors,
                'success' => false,
                'message' => "Some invalid data existed!"
            ],
            400
        ));
    }
}

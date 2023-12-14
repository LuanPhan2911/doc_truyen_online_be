<?php

namespace App\Http\Requests;

use App\Traits\PreventRedirectIfValidateFailed;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    use PreventRedirectIfValidateFailed;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "name" => ["bail", "string", "nullable"],
            "description" => ["string", "nullable"],
            "gender" => ["boolean", "nullable"],
            "avatar" => ["image",  "max:10240", "nullable"],
            "birth_date" => ["nullable",]
        ];
    }
}

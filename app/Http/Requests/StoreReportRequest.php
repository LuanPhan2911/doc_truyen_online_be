<?php

namespace App\Http\Requests;

use App\Traits\PreventRedirectIfValidateFailed;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReportRequest extends FormRequest
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
            "user_id" => [
                "required",
                Rule::exists("users", "id"),
            ],
            "message" => [
                "string",
                "required",
            ],
            "reportedId" => [
                "required"
            ],
            "type" => [
                "required"
            ]
        ];
    }
}

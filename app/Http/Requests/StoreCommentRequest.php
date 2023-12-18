<?php

namespace App\Http\Requests;

use App\Enums\TypeCommentEnum;
use App\Models\User;
use App\Traits\PreventRedirectIfValidateFailed;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCommentRequest extends FormRequest
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
            "message" => [
                "string",
                "required"
            ],
            "parent_id" => [
                "nullable"
            ],
            'user_id' => [
                "required",
                Rule::exists(User::class, "id")
            ],
            "commentedId" => [
                "required",
            ],
            "commentable_type" => [
                "required",
            ],
            'type' => [
                'required',
                Rule::in(TypeCommentEnum::getValues()),
            ],
            'is_leak' => [
                'required',
                'boolean'
            ]
        ];
    }
}

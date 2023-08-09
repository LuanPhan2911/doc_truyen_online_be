<?php

namespace App\Http\Requests;


use App\Models\Story;
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
            "story_id" => [
                "required",
                Rule::exists(Story::class, "id"),
            ]
        ];
    }
}

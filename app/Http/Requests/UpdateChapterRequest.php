<?php

namespace App\Http\Requests;

use App\Models\Story;
use App\Traits\PreventRedirectIfValidateFailed;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateChapterRequest extends FormRequest
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
            'name' => [
                'required'
            ],
            'story_id' => [
                Rule::exists(Story::class, 'id'),
                'required',
            ],

            'content' => [
                'required',
            ]
        ];
    }
}

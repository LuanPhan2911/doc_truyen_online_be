<?php

namespace App\Http\Requests;

use App\Enums\StatusStoryEnum;
use App\Enums\ViewStoryEnum;
use App\Models\Author;
use App\Models\Genre;
use App\Models\Story;
use App\Models\User;
use App\Traits\PreventRedirectIfValidateFailed;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStoryRequest extends FormRequest
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
                'required',

            ],
            'description' => [
                'required'
            ],
            'avatar' => [
                'image',
                'max:10240'
            ],
            'view' => [
                'required',
                Rule::in(ViewStoryEnum::getValues())
            ],
            'user_id' => [
                'required',
                Rule::exists(User::class, 'id')
            ],
            'genres_id' => [
                'required',
                Rule::exists(Genre::class, 'id'),
            ],
            "author_id" => [
                "required",
                Rule::exists(Author::class, 'id'),
            ]
        ];
    }
}

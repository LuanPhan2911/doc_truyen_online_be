<?php

namespace App\Http\Requests;

use App\Traits\PreventRedirectIfValidateFailed;
use Illuminate\Foundation\Http\FormRequest;

class StoreRateStoryRequest extends FormRequest
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
            'characteristic' => [
                'bail',
                'required',
                'min:0',
                'max:5'
            ],
            'plot' => [
                'bail',
                'required',
                'min:0',
                'max:5'
            ],
            'world_building' => [
                'bail',
                'required',
                'min:0',
                'max:5'
            ],
            'quality_convert' => [
                'bail',
                'required',
                'min:0',
                'max:5'
            ],
            'comment_id' => [
                'bail',
                'required',
                'exists:comments,id'
            ]
        ];
    }
}

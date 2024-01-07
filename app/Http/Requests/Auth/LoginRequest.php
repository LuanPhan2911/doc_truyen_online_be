<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Traits\PreventRedirectIfValidateFailed;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoginRequest extends FormRequest
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
            'email' => [
                'bail',
                'required',
                'email',
                Rule::exists(User::class, 'email'),
            ],
            'password' => [
                'bail',
                'min:1',
                'required',
            ],
            'device_name' => [
                'required',
                'in:web,mobile',
                'bail',
            ],

        ];
    }
}

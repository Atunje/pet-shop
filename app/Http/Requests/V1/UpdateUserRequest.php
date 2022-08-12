<?php

namespace App\Http\Requests\V1;

use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends APIFormRequest
{
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
     * @return array<string, string|null>
     */
    public function rules()
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($this->user()),
            ],
            'phone_number' => [
                'required',
                'string',
                Rule::unique('users')->ignore($this->user()),
            ],
            'password' => ['required', 'confirmed', Password::defaults()],
            'avatar' => 'required|uuid',
            'address' => 'required|string',
            'is_marketing' => 'nullable|string',
        ];
    }
}

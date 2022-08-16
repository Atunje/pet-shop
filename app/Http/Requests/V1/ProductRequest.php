<?php

namespace App\Http\Requests\V1;

use Illuminate\Validation\Rule;

class ProductRequest extends APIFormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'price' => 'required|numeric',
            'description' => 'required|string',
            'metadata' => 'required|json',
            'category_uuid' => 'required|uuid|exists:categories,uuid',
        ];

        if (isset($this->product)) {
            $rules['title'] = [
                'required',
                'string',
                'max:255',
                Rule::unique('products')->ignore($this->product),
            ];
        } else {
            $rules['title'] = 'required|string|max:255';
        }

        return $rules;
    }
}

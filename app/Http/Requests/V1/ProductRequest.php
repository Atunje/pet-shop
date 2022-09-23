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
     * Convert all json fields to arrays.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'metadata' => json_decode(strval($this->input('metadata')), true),
        ]);
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
            'metadata' => 'required|array',
            'metadata.brand' => 'required|uuid|exists:brands,uuid',
            'metadata.image' => 'required|uuid|exists:files,uuid',
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

    public function messages()
    {
        $messages = parent::messages();
        $messages['metadata.array'] = 'The metadata supplied is not a valid json array';

        return $messages;
    }
}

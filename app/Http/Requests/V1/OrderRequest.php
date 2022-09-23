<?php

namespace App\Http\Requests\V1;

use Illuminate\Validation\Rule;

class OrderRequest extends APIFormRequest
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
            'products' => json_decode(strval($this->input('products')), true),
            'address' => json_decode(strval($this->input('address')), true),
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
            'order_status_uuid' => 'required|uuid|exists:order_statuses,uuid',
            'payment_uuid' => 'required|uuid|exists:payments,uuid|unique:orders',
            'products' => 'required|array',
            'products.*.uuid' => 'uuid|exists:products',
            'products.*.quantity' => 'numeric',
            'address' => 'required|array',
            'address.billing' => 'required|string',
            'address.shipping' => 'required|string',
        ];

        if (isset($this->order)) {
            $rules['payment_uuid'] = [
                'required',
                'uuid',
                'exists:payments,uuid',
                Rule::unique('orders')->ignore($this->order),
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        $messages = parent::messages();
        $messages['address.array'] = 'The address supplied is not a valid json array';
        $messages['products.array'] = 'The products supplied is not a valid json array';

        return $messages;
    }
}

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

    public function validator($factory)
    {
        return $factory->make(
            $this->sanitize(), $this->container->call([$this, 'rules']), $this->messages()
        );
    }

    public function sanitize()
    {
        $this->merge([
            'products' => json_decode($this->input('products'), true),
            'address' => json_decode($this->input('address'), true),
        ]);
        return $this->all();
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
            'products' => 'required|array',
            'products.*.uuid' => 'uuid|exists:products',
            'products.*.quantity' => 'numeric',
            'address' => 'required|array',
            'address.billing' => 'required|string',
            'address.shipping' => 'required|string'
        ];

        if (isset($this->order)) {
            $rules['payment_uuid'] = [
                'required',
                'uuid',
                'exists:payments,uuid',
                Rule::unique('orders')->ignore($this->order),
            ];
        } else {
            $rules['payment_uuid'] = 'required|uuid|exists:payments,uuid|unique:orders';
        }

        return $rules;
    }
}

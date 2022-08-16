<?php

namespace App\Http\Requests\V1;

use Illuminate\Validation\Rule;

class OrderStatusRequest extends APIFormRequest
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
        if (isset($this->order_status)) {
            return [
                'title' => [
                    'required',
                    'string',
                    Rule::unique('order_statuses')->ignore($this->order_status),
                ],
            ];
        }

        return [
            'title' => 'required|string|unique:order_statuses',
        ];
    }
}

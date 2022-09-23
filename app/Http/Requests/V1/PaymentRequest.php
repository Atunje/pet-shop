<?php

namespace App\Http\Requests\V1;

class PaymentRequest extends APIFormRequest
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
            'details' => json_decode(strval($this->input('details')), true),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'type' => 'required|string|in:credit_card,bank_transfer,cash_on_delivery',
            'details' => 'required|array',
        ];
    }

    public function messages(): array
    {
        $messages = parent::messages();
        $messages['details.array'] = 'The details supplied is not a valid json array';

        return $messages;
    }
}

<?php

namespace App\Http\Requests\V1;

use App\DTOs\FilterParams;
use Illuminate\Foundation\Http\FormRequest;

class UserFilterRequest extends FormRequest
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

    protected function prepareForValidation(): void
    {
        $this->merge([
            'limit' => $this->limit ?? 10, //default to 10 records
            'desc' => $this->desc ?? true, //default to true
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
            'page' => 'integer|nullable',
            'limit' => 'integer|nullable',
            'sort_by' => 'string|nullable',
            'desc' => 'boolean|nullable',
            'date_range' => 'array|nullable',
            'first_name' => 'string|nullable',
            'email' => 'email|nullable',
            'phone_number' => 'string|nullable',
            'address' => 'string|nullable',
            'created_at' => 'date|nullable',
            'is_marketing' => 'boolean|nullable',
        ];
    }

    public function filterParams(): FilterParams
    {
        return new FilterParams((array) $this->validated());
    }
}

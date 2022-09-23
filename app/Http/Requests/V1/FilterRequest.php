<?php

namespace App\Http\Requests\V1;

use App\DTOs\FilterParams;

class FilterRequest extends APIFormRequest
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

        if (! empty($this->date_range)) {
            $this->merge([
                'date_range' => json_decode(strval($this->date_range), true), //default to 10 records
            ]);
        }
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
            'date_range.from' => 'date|nullable',
            'date_range.to' => 'date|nullable',
        ];
    }

    public function messages(): array
    {
        $messages = parent::messages();
        $messages['date_range.array'] = 'The date range must be a json array.';

        return $messages;
    }

    public function filterParams(): FilterParams
    {
        return new FilterParams((array) $this->validated());
    }
}

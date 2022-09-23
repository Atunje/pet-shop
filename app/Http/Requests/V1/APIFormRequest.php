<?php

namespace App\Http\Requests\V1;

use App\Traits\HandlesResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

abstract class APIFormRequest extends FormRequest
{
    use HandlesResponse;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    abstract public function authorize();

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    abstract public function rules();

    /**
     * Customize the response when validation fails.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->jsonResponse(
                status_code: Response::HTTP_UNPROCESSABLE_ENTITY,
                error: __('validation.invalid_inputs'),
                errors: $validator->errors()->toArray()
            )
        );
    }

    public function validFields(): array
    {
        return (array) $this->validated();
    }
}

<?php

namespace App\Http\Requests\V1;

use App\Traits\HandlesResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

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
    abstract function rules();

    /**
     * Customize the response when validation fails
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->jsonResponse(
                status_code: Response::HTTP_UNPROCESSABLE_ENTITY,
                error: __('validation.invalid_inputs'),
                errors: $validator->errors()
            )
        );
    }
}

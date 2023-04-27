<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class PerfilRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email'     => 'required|string|email:rfc,dns',
            'password'  => 'required|string|min:8'
        ];
    }

    /**
     * Get the validation messages.
     *
     */
    public function messages(): array
    {
        return [
            'required'  => 'Preencha este campo.',
            'email'     => 'Formato de E-mail inválido.',
            'min'       => 'O campo informado deve ter no mínimo 8 digitos.'
        ];
    }

    public function failedValidation(Validator $validator) {
        throw new ValidationException(
            $validator,
            response()->json([
                'status' => 500,
                'message' => $validator->errors(),
                'data' => null
            ], 500)
        );
    }
}

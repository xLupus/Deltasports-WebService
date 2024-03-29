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
            'name'      => 'required|string',
            'email'     => 'required|string|email:rfc,dns|unique:USUARIO,USUARIO_EMAIL'
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
            'unique'    => 'O E-mail informado já existe.',
            'min'       => 'O campo informado deve ter no mínimo 8 digitos.'
        ];
    }

    public function failedValidation(Validator $validator) {
        throw new ValidationException(
            $validator,
            response()->json([
                'status' => 422,
                'message' => $validator->errors(),
                'data' => null
            ], 422)
        );
    }
}

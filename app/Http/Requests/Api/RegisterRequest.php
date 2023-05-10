<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class RegisterRequest extends FormRequest
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
            'name'                  => 'required|string|max:50|not_regex:/[^A-Za-z ]/',
            'email'                 => 'required|string|email:rfc,dns|unique:USUARIO,USUARIO_EMAIL',
            'password'              => 'required|string|confirmed|min:8',
            'password_confirmation' => 'required|string|min:8',
            'cpf'                   => 'required|string|digits_between:11,11|unique:USUARIO,USUARIO_CPF'
        ];
    }

    /**
     * Get the validation messages.
     *
     */
    public function messages(): array
    {
        return [
            'max'                   => 'O Máximo de caracteres foi excedido',
            'required'              => 'Preencha este campo.',
            'not_regex'             => 'O campo informado não aceita números e/ ou símbolos.',
            'min'                   => 'O campo informado deve ter no mínimo 8 digitos.',
            'confirmed'             => 'As senhas informadas não correspondem.',
            'email'                 => 'O formato de E-mail é inválido.',
            'email.unique'          => 'O E-mail informado já existe.',
            'cpf.unique'            => 'O CPF informado já existe.',
            'cpf.digits_between'    => 'O campo informado deve ter 11 digitos.'
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

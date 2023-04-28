<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class CarrinhoRequest extends FormRequest
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
            'product' => 'required|numeric|gt:0',
            'qtd'     => 'required|numeric|gte:0'
        ];
    }

    /**
     * Get the validation messages.
     *
     */
    public function messages(): array
    {
        return [
            'required'  => 'Preencha este campo',
            'numeric'   => 'O campo informado deve ser numérico',
            'gt'        => 'O campo de produto precisa ter um valor maior que 0',
            'gte'       => 'O campo de quantidade precisa ter um valor maior ou igual a 0'
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

<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Endereco;

class EnderecoRequest extends FormRequest
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
            'name'          => 'required|max:70',
            'street'        => 'required|max:70',
            'number'        => 'required|max:10',
            'complement'    => 'max:70',
            'zip_code'      => 'required|regex:/^\d{8}/',
            'city'          => 'required|max:70|not_regex:/[0-9]/',
            'state'         => 'required|max:2|not_regex:/[0-9 ]/'
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
            'complement.not_regex'  => 'O campo informado não aceita símbolos.',
            'regex'                 => 'O campo informado não corresponde ao formato adequado',
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

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $endereco = $this->AddressExists();

            if (count($endereco) > 0)  $validator->errors()->add('address', 'Você já cadastrou um endereço com essas informações');
        });
    }

    public function AddressExists() {
        return Endereco::where('USUARIO_ID', auth()->user()->USUARIO_ID)
            ->where('ENDERECO_NOME', $this->name)
            ->where('ENDERECO_LOGRADOURO', $this->street)
            ->where('ENDERECO_NUMERO', $this->number)
            ->where('ENDERECO_COMPLEMENTO', $this->complement)
            ->where('ENDERECO_CEP', $this->zip_code)
            ->where('ENDERECO_CIDADE', $this->city)
            ->where('ENDERECO_ESTADO', $this->state)
            ->where('ENDERECO_APAGADO', 0)
            ->get();
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class PerfilController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name'                  => 'required|string|max:50|not_regex:/[^A-Za-z]/',
                'email'                 => 'required|string|email:rfc,dns|unique:USUARIO,USUARIO_EMAIL',
                'password'              => 'required|string|confirmed|min:8',
                'password_confirmation' => 'required|string|min:8',
                'cpf'                   => 'required|string|digits_between:11,11|unique:USUARIO,USUARIO_CPF'
            ], [
                'max'                   => 'O Máximo de caracteres foi excedido',
                'required'              => 'Preencha este campo.',
                'not_regex'             => 'O campo informado não aceita números e/ ou símbolos.',
                'min'                   => 'O campo informado deve ter no mínimo 8 digitos.',
                'confirmed'             => 'As senhas informadas não correspondem.',
                'email'                 => 'O formato de E-mail  é inválido.',
                'email.unique'          => 'O E-mail informado já existe.',
                'cpf.unique'            => 'O CPF informado já existe.',
                'cpf.digits_between'    => 'O campo informado deve ter 11 digitos.',
            ]);

            if($validator->fails()) throw new ValidationException($validator);

            $user = User::create([
                'USUARIO_NOME'  => $request['name'],
                'USUARIO_EMAIL' => $request['email'],
                'USUARIO_SENHA' => Hash::make($request['password']),
                'USUARIO_CPF'   => $request['cpf'],
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Usuário cadastrado com sucesso!',
                'data' => $user
            ], 200);
        } catch (\Throwable $err) {
            return $this->exceptions($err, $validator);
        }
    }

    public function show()
    {
        $user = auth()->user();

        return response()->json([
            'status' => 200,
            'message' => 'Usuário retornado com sucesso!',
            'data' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}

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
/*     public function show()
    {
        //dd($request->id);
        $user = auth()->user();

        return response()->json([
            'status' => 200,
            'message' => 'Usuário retornado com sucesso!',
            'data' => $user
        ]);
    } */

    public function update(Request $request, User $user)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email'     => 'required|string|email:rfc,dns',
                'password'  => 'required|string|min:8',
            ], [
                'required'  => 'Preencha este campo.',
                'email'     => 'Formato de E-mail inválido.',
                'min'       => 'O campo informado deve ter no mínimo 8 digitos.',
            ]);

            if($validator->fails()) throw new ValidationException($validator);

            $data = $request->only('email', 'password'); //mostra os dados

            $user = User::update([
                'USUARIO_EMAIL' => $request['email'],
                'USUARIO_SENHA' => Hash::make($request['password']),
            ]);

            if($user) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Usuário atualizado com sucesso!'
                ], 200);
            } else {
                return response()->json([
                    'status' => 200,
                    'message' => 'Erro ao atualizar'
                ], 200);
            }
        } catch (\Throwable $err) {
            return $this->exceptions($err, $validator);
        }
    }
}

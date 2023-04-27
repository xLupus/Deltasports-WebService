<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PerfilRequest;
use App\Http\Resources\Api\UserResource;
use App\Models\User;

class PerfilController extends Controller
{
    public function show() {
        return response()->json([
            'status'    => 200,
            'message'   => 'Usuário retornado com sucesso!',
            'data'      => new UserResource(auth()->user())
        ], 200);
    }

    public function update(PerfilRequest $request, User $user) {
        try {
            $user = User::find(auth()->user()->USUARIO_ID);

            $user->USUARIO_EMAIL = $request['email'];
            $user->USUARIO_SENHA = bcrypt($request['password']);

            $user->update();

            if($user) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Usuário atualizado com sucesso!',
                    'data' => null
                ], 200);
            } else {
                return response()->json([
                    'status' => 200,
                    'message' => 'Erro ao atualizar usuário',
                    'data' => null
                ], 200);
            }
        } catch (\Throwable $err) {
            switch (get_class($err)) {
                case \Illuminate\Database\Eloquent\ModelNotFoundException::class:
                    return response()->json([
                        'status' => 404,
                        'message' => $err->getMessage(),
                        'data' => null
                    ], 404);
                    break;

                case \Illuminate\Database\QueryException::class:
                    return response()->json([
                        'status' => 500,
                        'message' => $err->getMessage(),
                        'data' => null
                    ], 500);
                    break;

                default:
                    return response()->json([
                        'status' => 500,
                        'mensage' => 'Erro interno',
                        'data' => null
                    ], 500);
                    break;
            }
        }
    }
}

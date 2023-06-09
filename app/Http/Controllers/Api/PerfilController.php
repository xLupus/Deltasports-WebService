<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PerfilRequest;
use App\Http\Resources\Api\UserResource;
use App\Traits\Exception as Errors;
use App\Models\User;

class PerfilController extends Controller
{
    use Errors;

    public function show() {
        try {
            return response()->json([
                'status'    => 200,
                'message'   => 'Usuário retornado com sucesso!',
                'data'      => new UserResource(auth()->user())
            ], 200);
        } catch (\Throwable $err) {
            return $this->exceptions($err);
        }
    }

    public function update(PerfilRequest $request) {
        try {
            $user = User::find(auth()->user()->USUARIO_ID);

            $user->USUARIO_NOME     = $request['name'];
            $user->USUARIO_EMAIL    = $request['email'];

            $user->update();

            return response()->json([
                'status'    => 200,
                'message'   => 'Usuário atualizado com sucesso!',
                'data'      => null
            ], 200);
        } catch (\Throwable $err) {
            return $this->exceptions($err);
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;

use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Resources\Api\UserResource;

class AuthController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api')->except('register', 'login');
    }

    public function register(RegisterRequest $request) {
        try {
            $user = new User();

            $user->USUARIO_NOME     = $request['name'];
            $user->USUARIO_EMAIL    = $request['email'];
            $user->USUARIO_SENHA    = bcrypt($request['password']);
            $user->USUARIO_CPF      = $request['cpf'];

            $user->save();

            return response()->json([
                'status' => 200,
                'message' => 'Usuário cadastrado com sucesso!',
                'data' => new UserResource($user),
            ], 200);
        } catch (\Throwable $err) {
            return $this->exceptions($err);
        }
    }

    public function login(LoginRequest $request) {
        try {
            $credentials = ['USUARIO_EMAIL' => $request['email'], 'password' => $request['password']]; //nome da coluna aqui deve ser 'password' para funcionar (**MUDAR APENAS NA MODEL**)

            if($token = auth()->attempt($credentials)) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Usuário logado com sucesso!',
                    'data' => new UserResource(auth()->user()),
                    'authorization' => [
                        'token' => $token,
                        'type' => 'bearer',
                    ]
                ], 200);
            } else {
                return response()->json([
                    'status'    => 401,
                    'message'   => 'O Email ou senha estão incorretos.',
                    'data'      => $request->only('email', 'password') //mostra os dados
                ], 401);
            }
        } catch (\Throwable $err) {
            return $this->exceptions($err);
        }
    }

    public function logout() {
        try { //com o token
            auth()->logout();

            return response()->json([
                'status'    => 200,
                'message'   => 'Usuário deslogado com sucesso!',
                'data'      => null
            ], 200);
        } catch (\Throwable $err) {
            return $this->exceptions($err);
        }
    }

    public function refresh() {
        return response()->json([
            'status'    => 200,
            'message'   => 'Token revalidado com sucesso!',
            'data'      => new UserResource(auth()->user()),
            'authorization' => [
                'token'     => auth()->refresh(),
                'type'      => 'bearer',
            ]
        ], 200);
    }

    //Exceptions
    public static function exceptions($err) {
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

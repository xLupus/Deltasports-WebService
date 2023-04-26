<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except('register', 'login');
    }

    public function register(Request $request) {
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

    public function login(Request $request) {
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
            $credentials = ['USUARIO_EMAIL' => $request['email'], 'password' => $request['password']]; //nome da coluna aqui deve ser 'password' para funcionar (**MUDAR APENAS NA MODEL**)

            if($token = auth()->attempt($credentials)) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Usuário logado com sucesso!',
                    'data' => auth()->user(),
                    'authorization' => [
                        'token' => $token,
                        'type' => 'bearer',
                    ]
                ], 200);
            } else {
                return response()->json([
                    'status'    => 401,
                    'message'   => 'Email ou senha incorretos.',
                    'data'      => $data
                ], 401);
            }
        } catch (\Throwable $err) {
            return $this->exceptions($err, $validator);
        }
    }

    public function logout() {
        try { //com o token
            $user = auth()->logout();

            return response()->json([
                'status'    => 200,
                'message'   => 'Usuário deslogado com sucesso!',
                'data'      => $user
            ], 200);
        } catch (\Throwable $err) {
            $this->exceptions($err);
        }
    }

    public function refresh() {
        return response()->json([
            'status'    => 200,
            'message'   => 'Token revalidado com sucesso!',
            'data'      => auth()->user(),
            'authorization' => [
                'token'     => auth()->refresh(),
                'type'      => 'bearer',
            ]
        ], 200);
    }

    public function user() {
        return response()->json([
            'status'    => 200,
            'message'   => 'Retornado usuário' ,
            'data'      => auth()->user()
        ], 200);
    }

    //Exceptions
    public function exceptions($err, $validator = null) {
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

            case \Illuminate\Validation\ValidationException::class:
                return response()->json([
                    'status' => 500,
                    'message' => $validator->errors(),
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

<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['register', 'login', 'logout']]); //middleware de autenticação
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name'                  => 'required|string|max:50|not_regex:/[^A-Za-z]/',
            'email'                 => 'required|string|email:rfc,dns|unique:USUARIO,USUARIO_EMAIL',
            'password'              => 'required|string|confirmed|min:8',
            'password_confirmation' => 'required|string|min:8',
            'cpf'                   => 'required|string|digits_between:11,11|unique:USUARIO,USUARIO_CPF'
        ], [
            'max'                   => 'Máximo de caracteres excedido',
            'required'              => 'Preencha este campo.',
            'not_regex'             => 'O campo informado não aceita números e símbolos.',
            'min'                   => 'O campo informado deve ter no mínimo 8 digitos.',
            'confirmed'             => 'As senhas informadas não correspondem.',
            'email'                 => 'Formato de E-mail inválido.',
            'email.unique'          => 'O E-mail informado já existe.',
            'cpf.unique'            => 'O CPF informado já existe.',
            'cpf.digits_between'    => 'O campo informado deve ter 11 digitos.',
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => 500,
                'message' => $validator->messages(),
                'data' => null
            ], 500);
        }

        try {
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
            return response()->json([
                'status' => 500,
                'message' => 'Erro ao cadastrar usuário!',
                'data' => null
            ], 500);
        }
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email'     => 'required|string|email:rfc,dns',
            'password'  => 'required|string|min:8',
        ], [
            'required'      => 'Preencha este campo.',
            'email'         => 'Formato de E-mail inválido.',
            'min'           => 'O campo informado deve ter no mínimo 8 digitos.',
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => 500,
                'message' => $validator->messages(),
                'data' => null
            ], 500);
        }

        $data = $request->only('email', 'password'); //mostra os dados
        $credentials = ['USUARIO_EMAIL' => $request['email'], 'password' => $request['password']]; //nome da coluna aqui deve ser 'password' para funcionar (**MUDAR APENAS NA MODEL**)

        try {
            if(! $token = Auth::attempt($credentials)) {
                return response()->json([
                    'status'    => 401,
                    'message'   => 'Email ou senha incorretos.',
                    'data'      => $data
                ], 401);
            }
        } catch (\Throwable $err) {
            return response()->json([
                'status'    => 500,
                'message'   => 'Falha ao logar.',
                'data'      => $data
            ], 500);
        }

        $user = Auth::user();

        return response()->json([
            'status' => 200,
            'message' => 'Usuário logado com sucesso!',
            'data' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ], 200);
    }

    public function logout() {
        try { //com o token
            $user = Auth::logout();

            return response()->json([
                'status'    => 200,
                'message'   => 'Usuário deslogado com sucesso!',
                'data'      => $user
            ], 200);
        } catch (\Throwable $err) {
            return response()->json([
                'status'    => 500,
                'message'   => 'Erro ao deslogar usuário.',
                'data'      => null
            ], 500);
        }
    }

    public function refresh() {
        return response()->json([
            'status' => 200,
            'message' => 'Token revalidado com sucesso!',
            'data' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ], 200);
    }
}

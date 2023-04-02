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
            'nome'                  => 'required|string|max:50|not_regex:/[^A-Za-z0-9]/',
            'email'                 => 'required|string|email:rfc,dns|unique:USUARIO,USUARIO_EMAIL',
            'senha'                 => 'required|string|confirmed|min:8',
            'senha_confirmation'    => 'required|string|min:8',
            'cpf'                   => 'required|string|digits_between:11,11|unique:USUARIO,USUARIO_CPF'
        ], [
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
                'Status' => 500,
                'Message' => $validator->messages(),
                'Data' => null
            ], 500);
        }

        try {
            $user = User::create([
                'USUARIO_NOME'  => $request['nome'],
                'USUARIO_EMAIL' => $request['email'],
                'USUARIO_SENHA' => Hash::make($request['senha']),
                'USUARIO_CPF'   => $request['cpf'],
            ]);

            return response()->json([
                'Status' => 200,
                'Message' => 'Usuário cadastrado com sucesso!',
                'Data' => $user
            ], 200);
        } catch (\Throwable $err) {
            return response()->json([
                'Status' => 500,
                'Message' => 'Erro ao cadastrar usuário!',
                'Data' => null
            ], 500);
        }
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email:rfc,dns',
            'senha' => 'required|string|min:8',
        ], [
            'required'      => 'Preencha este campo.',
            'email'         => 'Formato de E-mail inválido.',
            'min'           => 'O campo informado deve ter no mínimo 8 digitos.',
        ]);

        if($validator->fails()) {
            return response()->json([
                'Status' => 500,
                'Message' => $validator->messages(),
                'Data' => null
            ], 500);
        }

        $data = $request->only('email', 'senha'); //mostra os dados
        $credentials = ['USUARIO_EMAIL' => $request['email'], 'password' => $request['senha']]; //nome da coluna aqui deve ser 'password' para funcionar (**MUDAR APENAS NA MODEL**)

        try {
            if(! $token = Auth::attempt($credentials)) {
                return response()->json([
                    'Status'    => 401,
                    'Message'   => 'Email ou senha incorretos.',
                    'Data'      => $data
                ], 401);
            }
        } catch (\Throwable $err) {
            return response()->json([
                'Status'    => 500,
                'Message'   => 'Falha ao logar.',
                'Data'      => $data
            ], 500);
        }

        $user = Auth::user();

        return response()->json([
            'Status' => 200,
            'Message' => 'Usuário logado com sucesso!',
            'Data' => $user,
            'Authorisation' => [
                'Token' => $token,
                'Type' => 'bearer',
            ]
        ], 200);
    }

    public function logout() {
        try { //com o token
            $user = Auth::logout();

            return response()->json([
                'Status'    => 200,
                'Message'   => 'Usuário deslogado com sucesso!',
                'Data'      => $user
            ], 200);
        } catch (\Throwable $err) {
            return response()->json([
                'Status'    => 500,
                'Message'   => 'Erro ao deslogar usuário.',
                'Data'      => null
            ], 500);
        }
    }

    public function refresh() {
        return response()->json([
            'Status' => 200,
            'Message' => 'Token revalidado com sucesso!',
            'Data' => Auth::user(),
            'Authorisation' => [
                'Token' => Auth::refresh(),
                'Type' => 'bearer',
            ]
        ], 200);
    }
}

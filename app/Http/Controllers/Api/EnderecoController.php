<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\EnderecoRequest;
use App\Http\Resources\Api\EnderecoResource;
use App\Models\Endereco;
use App\Traits\Exception as Errors;

class EnderecoController extends Controller
{
    use Errors;

    /**
     * Display the specified resource.
     */
    public function index()
    {
        try {
            $enderecos = Endereco::where('ENDERECO_APAGADO', 0)
                ->where('USUARIO_ID', auth()->user()->USUARIO_ID)
                ->get();

            if(count($enderecos) === 0) {
                return response()->json([
                    'status'    => 404,
                    'message'   => 'Nenhum endereço encontrado...',
                    'data'      => null
                ], 404);
            }

            return response()->json([
                'status'    => 200,
                'message'   => 'Endereços retornados com sucesso!',
                'data'      => EnderecoResource::collection($enderecos)
            ]);
        } catch (\Throwable $err) {
            return $this->exceptions($err);
        }
    }

    /**
     * Display the specified resource.
    */
    public function show($enderecoId) {
        try {
            $endereco = Endereco::where('ENDERECO_ID', $enderecoId)
                ->where('ENDERECO_APAGADO', 0)
                ->where('USUARIO_ID', auth()->user()->USUARIO_ID)
                ->first();

            if(!$endereco) {
                return response()->json([
                    'status'    => 404,
                    'message'   => 'Endereço não encontrado...',
                    'data'      => null
                ], 404);
            }

            return response()->json([
                'status'    => 200,
                'message'   => 'Endereço retornado com sucesso!',
                'data'      => new EnderecoResource($endereco)
            ]);
        } catch (\Throwable $err) {
            return $this->exceptions($err);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EnderecoRequest $request)
    {
        try {
            $endereco = new Endereco();

            $endereco->USUARIO_ID           = auth()->user()->USUARIO_ID;
            $endereco->ENDERECO_NOME        = $request['name'];
            $endereco->ENDERECO_LOGRADOURO  = $request['street'];
            $endereco->ENDERECO_NUMERO      = $request['number'];
            $endereco->ENDERECO_COMPLEMENTO = $request['complement'];
            $endereco->ENDERECO_CEP         = $request['zip_code'];
            $endereco->ENDERECO_CIDADE      = $request['city'];
            $endereco->ENDERECO_ESTADO      = $request['state'];
            $endereco->ENDERECO_APAGADO     = 0;

            $endereco->save();

            return response()->json([
                'status'    => 201,
                'message'   => 'Endereço cadastrado com sucesso!',
                'data'      => new EnderecoResource($endereco)
            ], 201);
        } catch (\Throwable $err) {
            return $this->exceptions($err);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EnderecoRequest $request, $enderecoId)
    {
        try {
            $endereco = Endereco::where('ENDERECO_ID', $enderecoId)
                ->where('ENDERECO_APAGADO', 0)
                ->where('USUARIO_ID', auth()->user()->USUARIO_ID)
                ->first();

            if(!$endereco) {
                return response()->json([
                    'status'    => 404,
                    'message'   => 'Endereço não encontrado...',
                    'data'      => null
                ], 404);
            }

            $endereco->ENDERECO_NOME        = $request['name'];
            $endereco->ENDERECO_LOGRADOURO  = $request['street'];
            $endereco->ENDERECO_NUMERO      = $request['number'];
            $endereco->ENDERECO_COMPLEMENTO = $request['complement'];
            $endereco->ENDERECO_CEP         = $request['zip_code'];
            $endereco->ENDERECO_CIDADE      = $request['city'];
            $endereco->ENDERECO_ESTADO      = $request['state'];

            $endereco->update();

            return response()->json([
                'status'    => 200,
                'message'   => 'Endereço atualizado com sucesso!',
                'data'      => new EnderecoResource($endereco)
            ]);
        } catch (\Throwable $err) {
            return $this->exceptions($err);
        }
    }

     /**
     * 'Delete' the specified resource in storage.
     */
    public function destroy($enderecoId)
    {
        try {
            $endereco = Endereco::where('ENDERECO_ID', $enderecoId)
                ->where('ENDERECO_APAGADO', 0)
                ->where('USUARIO_ID', auth()->user()->USUARIO_ID)
                ->first();

            if(!$endereco) {
                return response()->json([
                    'status'    => 404,
                    'message'   => 'Endereço não encontrado...',
                    'data'      => null
                ], 404);
            }

            $endereco->ENDERECO_APAGADO = 1;

            $endereco->update();

            return response()->json([
                'status'    => 200,
                'message'   => 'Endereço removido com sucesso!',
                'data'      => null
            ]);
        } catch (\Throwable $err) {
            return $this->exceptions($err);
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Categoria;
use App\Models\Produto;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CategoriaResource;
use App\Http\Resources\Api\ProdutoResource;
use App\Traits\Exception as Errors;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CategoriaController extends Controller
{
    use Errors;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $categories = Categoria::ativos()->get();

            return response()->json([
                'status'        => 200,
                'message'       => 'Categorias retornadas com sucesso!',
                'data'          => [
                    'categories' => CategoriaResource::collection($categories)
                ]
            ]);
        } catch (\Throwable $err) {
            return $this->exceptions($err);
        }
    }

    /**
     * Display the products from a specified resource.
     */
    public function showProducts(Request $request)
    {
        try {
            $validator = Validator::make(['id' => $request['id']], ['id' => 'required|integer|numeric']);

            if($validator->fails()) {
                throw ValidationException::withMessages([
                    'id' => 'O paramêtro informado deve ser numérico'
                ]);
            }

            $categoriaId = $request->id;

            $produtos = Produto::ativos()->where('CATEGORIA_ID', $categoriaId)->get();

            return response()->json([
                'status'  => 200,
                'message' => 'Produtos da categoria retornados com sucesso!',
                'data'    => ProdutoResource::collection($produtos)
            ]);
        } catch (\Throwable $err) {
            return $this->exceptions($err);
        }
    }
}

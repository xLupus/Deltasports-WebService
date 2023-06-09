<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Categoria;
use App\Models\Produto;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CategoriaResource;
use App\Http\Resources\Api\ProdutoResource;
use App\Traits\Exception as Errors;

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

            if(count($categories) === 0) {
                return response()->json([
                    'status'    => 404,
                    'message'   => 'Nenhuma categoria foi encontrada...',
                    'data'      => null
                ], 404);
            }

            return response()->json([
                'status'        => 200,
                'message'       => 'Categorias retornadas com sucesso!',
                'data'          =>  CategoriaResource::collection($categories)
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
            $categoriaId    = $request->id;
            $produtos       = Produto::ativos()->where('CATEGORIA_ID', $categoriaId)->get();
            $categoria      = Categoria::ativos()->where('CATEGORIA_ID', $categoriaId)->first();

            if(count($produtos) === 0 && !$categoria) {
                return response()->json([
                    'status'    => 404,
                    'message'   => 'Não foi possível encontrar a categoria informada...',
                    'data'      => null
                ], 404);
            }

            return response()->json([
                'status'  => 200,
                'message' => 'Produtos da categoria retornados com sucesso!',
                'data'    => [
                    'category'  =>  new CategoriaResource ($categoria),
                    'products'  =>  ProdutoResource::collection($produtos)
                ]
            ]);
        } catch (\Throwable $err) {
            return $this->exceptions($err);
        }
    }
}

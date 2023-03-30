<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;
use App\Models\Produto;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Categoria::ativos()->get();

        return response()->json([
            "status"     => 200,
            "message"    => null,
            "categories" => $categories
        ]);
    }

    /**
     * Display the products from a specified resource.
     */
    public function showProducts(Request $request)
    {
        $categoriaId = $request->id;
        $produtos = Produto::ativos()->where('CATEGORIA_ID', $categoriaId)->get();
        return response()->json(["produtos" => $produtos]);
    }
}

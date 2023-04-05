<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Categoria;
use App\Http\Controllers\Controller;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Categoria::where('CATEGORIA_ATIVO', TRUE)
                                ->whereRelation('produtos', 'PRODUTO_ATIVO', TRUE)
                                ->get();

        return response()->json([
            "status"     => 200,
            "message"    => null,
            "categories" => $categories
        ]);
    }

    /**
     * Display the products from a specified resource.
     */
    public function show(Categoria $categoria)
    {
        //
    }

}

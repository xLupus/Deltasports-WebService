<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;

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

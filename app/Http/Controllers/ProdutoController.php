<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produto;

class ProdutoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Produto::with(['categoria', 'imagens', 'estoque'])
                                  ->where('PRODUTO_ATIVO', TRUE)
                                  ->whereRelation('estoque', 'PRODUTO_QTD', '>', 0)
                                  ->get();

        return response()->json($products);
    }


    /**
     * Display the specified resource.
     */
    public function show(Produto $produto)
    {
        //
    }
}

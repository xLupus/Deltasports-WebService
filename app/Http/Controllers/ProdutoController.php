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
        $products = Produto::ativos();

        return response()->json([
            "status"    => 200,
            "message"   => "Todos os produtos",
            "products"  => $products
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $productId = $request->id;
        
        $product = Produto::with(['categoria', 'imagens', 'estoque'])
                                ->where('PRODUTO_ATIVO', TRUE)
                                ->whereRelation('estoque', 'PRODUTO_QTD', '>', 0)
                                ->where('PRODUTO_ID', $productId)
                                ->get();

        return response()->json([
            "status"    => 200,
            "message"   => null,
            "product"   => $product
        ]);
    }
}

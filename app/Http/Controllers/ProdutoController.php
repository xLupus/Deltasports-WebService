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
        
        $product = Produto::ativos()->where('PRODUTO_ID', $productId)
                                    ->first();

        return response()->json([
            "status"    => 200,
            "message"   => null,
            "product"   => $product
        ]);
    }
}

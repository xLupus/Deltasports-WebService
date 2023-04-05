<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\ProdutoResource;
use Illuminate\Http\Request;
use App\Models\Produto;
use App\Http\Controllers\Controller;

class ProdutoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Produto::ativos()->get();
        
        return response()->json([
            "status"    => 200,
            "message"   => "Todos os produtos",
            "products"  => ProdutoResource::collection($products)
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $productId = $request->id;
        
        $product = Produto::ativos()->where('PRODUTO_ID', $productId)
                                    ->get();

        return response()->json([
            "status"    => 200,
            "message"   => null,
            "product"   => ProdutoResource::collection($product) //Enter pq new n foi
        ]);
    }
}

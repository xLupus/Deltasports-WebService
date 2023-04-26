<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Carrinho;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarrinhoController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!ctype_digit($request->id)) {
            return response()->json([
                'status'  => 400,
                'message' => 'O parametro precisar ser numerico'
            ], 400);
        }

        $productId = $request->id;

        $cart = Carrinho::where([
            'USUARIO_ID' => Auth::user()->USUARIO_ID,
            'PRODUTO_ID' => $productId
        ])->first(); //pega uma

        if ($cart) {
            $estoque = Produto::where('PRODUTO_ID', $productId)->first()->estoque->PRODUTO_QTD;

            if ($request->qtd > 0) //se o estoque for maior que a soma
                $cart->update(['ITEM_QTD' => $request->qtd > $estoque ? $estoque : $request->qtd]);
            else
                $cart->update(['ITEM_QTD' => 0]);

        } else {
            $cart = Carrinho::create([
                'USUARIO_ID' => Auth::user()->USUARIO_ID,
                'PRODUTO_ID' => $productId,
                'ITEM_QTD'   => $request->qtd
            ]);
        }

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     * TODO - Pegar o id do usuario pela sessao que n sei onde faz
     */
    public function show(Request $request)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Carrinho $carrinho)
    {
        //
    }

    public function deleteOne(Request $request)
    {

    }

    public function deleteAll(Request $request)
    {

    }
}

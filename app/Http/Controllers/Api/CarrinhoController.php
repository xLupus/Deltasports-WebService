<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Carrinho;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CarrinhoController extends Controller
{
    public function show()
    {
        $user = auth()->user()->USUARIO_ID;

        $cart = Carrinho::where('USUARIO_ID', $user)
                    ->join('PRODUTO');

        return response()->json([
            'status' => 200,
            'data'   => $cart
        ]);

        if ($cart) {
        } else {
        }

        dd($cart);
    }

    public function store(Request $request)
    {
        $data_validation = Validator::make($request->only(['product', 'qtd']), [
            'product' => 'required|numeric|gt:0',
            'qtd'     => 'required|numeric|gte:0'
        ], [
            'product.required' => 'Campo de produto é obrigatorio',
            'product.numeric'  => 'O campo de produto precisa ser numerico',
            'qtd.required'     => 'Campo de quantidade é Obrigatorio',
            'qtd.numeric'      => 'O campo de quantidade precisa ser numerico',
            'gt'               => 'O campo de produto precisa ter o valor maior que 0',
            'gte'              => 'O campo de quantidade precisa ter o valor maior ou igual a 0'
        ]);

        dd(auth()->user());
        if ($data_validation->fails()) {
            return response()->json([
                'status' => 401,
                'errors' => $data_validation->errors()
            ], 401);
        }

        $productId = $request->input('product');
        $qtd       = $request->input('qtd');

        $cart = Carrinho::where([
            'USUARIO_ID' => Auth::user()->USUARIO_ID,
            'PRODUTO_ID' => $productId
        ])->first();

        if ($cart) {
            $estoque = Produto::ativos()->where('PRODUTO_ID', $productId)->first()->estoque->PRODUTO_QTD;

            if ($qtd > 0) //se o estoque for maior que a soma
                $cart->update(['ITEM_QTD' => $qtd > $estoque ? $estoque : $qtd]);
            else
                $cart->update(['ITEM_QTD' => 0]);
        } else {
            try {
                Carrinho::create([
                    'USUARIO_ID' => Auth::user()->USUARIO_ID,
                    'PRODUTO_ID' => $productId,
                    'ITEM_QTD'   => $request->qtd
                ]);
            } catch (\Error $err) {
                return response()->json([
                    'status' => 401,
                    'errors' => $err
                ], 401);
            }
        }

        return response()->json([
            'status' => 201
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Carrinho $carrinho)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Carrinho $carrinho)
    {

    }
}

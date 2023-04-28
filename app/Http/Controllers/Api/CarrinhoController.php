<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CarrinhoRequest;
use App\Http\Resources\Api\CarrinhoResource;
use App\Models\Carrinho;
use App\Models\Produto;
use App\Traits\Exception as Errors;

class CarrinhoController extends Controller
{
    use Errors;

    public function show()
    {
        try {
            $userId = auth()->user()->USUARIO_ID;
            $cart = Carrinho::where('USUARIO_ID', $userId)
                ->join('PRODUTO', 'CARRINHO_ITEM.PRODUTO_ID', '=', 'PRODUTO.PRODUTO_ID')
                ->get();

            if (count($cart) > 0) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Carrinho retornado com sucesso!',
                    'data' => [
                        'user' => [
                            'id' => $userId
                        ],
                        'cart' => CarrinhoResource::collection($cart)
                    ]
                ]);
            } else {
                return response()->json([
                    'status' => 200,
                    'message' => 'O carrinho informado não existe',
                    'data'   => null
                ]);
            }
        } catch (\Throwable $err) {
            return $this->exceptions($err);
        }
    }

    public function store(CarrinhoRequest $request)
    {
        try {
            $productId = $request['product'];
            $qtd       = $request['qtd'];

            $cart = Carrinho::where([
                'USUARIO_ID' => auth()->user()->USUARIO_ID,
                'PRODUTO_ID' => $productId
            ])->first();

            if ($cart) { //se tiver carrinho
                $estoque = Produto::ativos()->where('PRODUTO_ID', $productId)->first()->estoque->PRODUTO_QTD;

                if ($qtd > 0) //se o estoque for maior que a soma
                    $cart->update(['ITEM_QTD' => $qtd > $estoque ? $estoque : $qtd]);
                else
                    $cart->update(['ITEM_QTD' => 0]);

                return response()->json([
                    'status' => 200,
                    'message' => 'Produtos atualizados no carrinho com sucesso!',
                    'data' => new CarrinhoResource($cart)
                ], 200);
            } else {
                $cart = new Carrinho();

                $cart->USUARIO_ID   = auth()->user()->USUARIO_ID;
                $cart->PRODUTO_ID   = $productId;
                $cart->ITEM_QTD     = $request->qtd;

                $cart->save();

                return response()->json([
                    'status' => 200,
                    'message' => 'Produtos inseridos no carrinho com sucesso!',
                    'data' => null
                ], 200);
            }
        } catch (\Throwable $err) {
            return $this->exceptions($err);
        }
    }
}

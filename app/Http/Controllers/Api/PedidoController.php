<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\EnderecoResource;
use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Http\Resources\Api\Pedido\PedidoIndexResource;
use App\Http\Resources\Api\Pedido\PedidoShowResource;
use App\Models\Endereco;
use App\Models\Carrinho;
use App\Models\ProdutoEstoque;
use Illuminate\Support\Facades\Auth;
use App\Traits\Exception as Errors;

class PedidoController extends Controller
{
    use Errors;

    public function index()
    {
        try {
            $pedidos = Pedido::where('USUARIO_ID', auth()->user()->USUARIO_ID)->get();

            if(count($pedidos) === 0) {
                return response()->json([
                    'status'    => 404,
                    'message'   => 'Nenhum pedido foi encontrado ...',
                    'data'      => null
                ], 404);
            }

            return response()->json([
                'status'    => 200,
                'message'   => 'Pedidos retornados com sucesso!',
                'data'      => PedidoIndexResource::collection($pedidos)
            ]);

        } catch (\Throwable $err) {
            dd($err);
            return $this->exceptions($err);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        try{
            $dataCompra = new \DateTime('', new \DateTimeZone('America/Sao_Paulo'));

            $produtosCarrinho = Carrinho::where('USUARIO_ID', auth()->user()->USUARIO_ID)
                ->where('ITEM_QTD', '>', 0)
                ->get()
                ->all();

            if (count($produtosCarrinho) === 0) {
                return response()->json([
                    'status'    => 404,
                    'message'   => 'Não existem items no carrinho.',
                    'data'      => null
                ], 404);
            }

            $pedido = Pedido::create([
                'USUARIO_ID'  => auth()->user()->USUARIO_ID,
                'STATUS_ID'   => 2, //pendente
                'PEDIDO_DATA' => $dataCompra->format('Y-m-d')
            ]);

            if ( isset($pedido->PEDIDO_ID) ) {
                foreach ($produtosCarrinho as $produto) {
                    $desconto = $produto->produto->PRODUTO_PRECO - $produto->produto->PRODUTO_DESCONTO;

                    PedidoItem::create([
                        'PRODUTO_ID' => $produto->PRODUTO_ID,
                        'PEDIDO_ID'  => $pedido->PEDIDO_ID,
                        'ITEM_QTD'   => $produto->ITEM_QTD < 0 ? 0 : $produto->ITEM_QTD,
                        'ITEM_PRECO' => $desconto < 0 ? 0 : $desconto
                    ]);

                    $estoqueAtual = ProdutoEstoque::where('PRODUTO_ID', $produto->PRODUTO_ID)->first()->PRODUTO_QTD;

                    ProdutoEstoque::where('PRODUTO_ID',  $produto->PRODUTO_ID)
                        ->update(['PRODUTO_QTD' => $estoqueAtual - $produto->ITEM_QTD]);

                    Carrinho::where('USUARIO_ID', Auth::user()->USUARIO_ID)
                        ->where('PRODUTO_ID',  $produto->PRODUTO_ID)
                        ->update(['ITEM_QTD' => 0]);
                }
            }

            return response()->json([
                'status'    => 200,
                'message'   => 'Pedido realizado com sucesso!',
                'data'      => null
            ], 200);
        } catch (\Throwable $err) {
            return $this->exceptions($err);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Pedido  $pedido
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        try{
            $precoTotal = 0;
            $pedidoId   = intval($request->id);

            $endereco   = Endereco::where('USUARIO_ID', auth()->user()->USUARIO_ID)
                ->get()
                ->last();

            $items      = PedidoItem::where('PEDIDO_ID', $pedidoId)->get();

            if (!isset($items[0]) || $items[0]->pedido->USUARIO_ID != auth()->user()->USUARIO_ID) {
                return response()->json([
                    'status'    => 404,
                    'message'   => 'Pedido não encontrado...',
                    'data'      => null
                ], 404);
            }

            foreach ($items as $item)
                $precoTotal += $item->ITEM_QTD * $item->ITEM_PRECO;

            return response()->json([
                'status'        => 200,
                'message'       => 'Pedido retornado com sucesso!',
                'data'          => [
                    'id'            => $pedidoId,
                    'items'         => PedidoShowResource::collection($items),
                    'address'       => new EnderecoResource($endereco),
                    'total_price'   => $precoTotal
                ]
            ]);
        } catch (\Throwable $err) {
            return $this->exceptions($err);
        }
    }
}

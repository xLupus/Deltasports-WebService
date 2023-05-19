<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\PedidoStatus;
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
            $pedidos = Pedido::where('USUARIO_ID', Auth::user()->USUARIO_ID)->get();

            return response()->json([
             'status'     => 200,
             'message'    => null,
             'data' => $pedidos
             ]);
             
        } catch (\Throwable $err) {
            return $this->exceptions($err);
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $dataCompra = new \DateTime('', new \DateTimeZone('America/Sao_Paulo'));
    
            $produtosCarrinho = Carrinho::where('USUARIO_ID', Auth::user()->USUARIO_ID)
                ->where('ITEM_QTD', '>', 0)->get()->all();
            if (count($produtosCarrinho) === 0) {
                return response()->json([
                    'status'    => 500,
                    'message'   => 'Não existem items no carrinho.',
                    'data'      => null
                ], 500);
            }
            $pedido = Pedido::create([
                'USUARIO_ID'  => Auth::user()->USUARIO_ID,
                'STATUS_ID'   => 2, //pendente
                'PEDIDO_DATA' => $dataCompra->format('Y-m-d')
            ]);
    
            if ( isset($pedido->PEDIDO_ID) ) {
                foreach ($produtosCarrinho as $product) {
                    $desconto = $product->product->PRODUTO_PRECO - $product->product->PRODUTO_DESCONTO;
                    PedidoItem::create([
                        'PRODUTO_ID' => $product->PRODUTO_ID,
                        'PEDIDO_ID'  => $pedido->PEDIDO_ID,
                        'ITEM_QTD'   => $product->ITEM_QTD < 0 ? 0 : $product->ITEM_QTD, 
                        'ITEM_PRECO' => $desconto < 0 ? 0 : $desconto
                    ]);
                    
                    $estoqueAtual = ProdutoEstoque::where('PRODUTO_ID', $product->PRODUTO_ID)->first()->PRODUTO_QTD;
    
                    ProdutoEstoque::where('PRODUTO_ID',  $product->PRODUTO_ID)
                        ->update(['PRODUTO_QTD' => $estoqueAtual - $product->ITEM_QTD]);
    
                    Carrinho::where('USUARIO_ID', Auth::user()->USUARIO_ID)
                        ->where('PRODUTO_ID',  $product->PRODUTO_ID)
                        ->update(['ITEM_QTD' => 0]);
                }
            }

            return response()->json([
                'status'    => 200,
                'message'   => 'Pedido realizado com sucesso!'
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
            // $endereco   = Endereco::where('USUARIO_ID', Auth::user()->USUARIO_ID)->get()->last()
            $items      = PedidoItem::where('PEDIDO_ID', $request->id)->get();
            if (!isset($items[0]) || $items[0]->pedido->USUARIO_ID != Auth::user()->USUARIO_ID)
                return response()->json([
                    'status'      => 200,
                    'teste'   => $items
                ]);
    
            foreach ($items as $item)
                $precoTotal += $item->ITEM_QTD * $item->ITEM_PRECO;
    
            return response()->json([
                'items'      => $items,
                'precoTotal' => $precoTotal,
                // 'endereco'   => $endereco
            ]);

        } catch (\Throwable $err) {
            return $this->exceptions($err);
        }
    }
}

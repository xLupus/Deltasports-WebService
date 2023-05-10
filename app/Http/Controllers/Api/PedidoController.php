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

class PedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $pedidos = Pedido::where('USUARIO_ID', Auth::user()->USUARIO_ID)->paginate(10);

       return response()->json([
        'status'     => 200,
        'message'    => null,
        'data' => $pedidos
    ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $usuario  = Auth::user();

        $endereco = Endereco::where('USUARIO_ID', Auth::user()->USUARIO_ID)->get()->last();

        $produtos = Carrinho::where('USUARIO_ID', Auth::user()->USUARIO_ID)
            ->where('ITEM_QTD', '>', 0)->get()->all();

            return response()->json([
                'status'     => 200,
                'message'    => null,
                'data' => $usuario, $endereco, $produtos
            ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $dataCompra = new \DateTime('', new \DateTimeZone('America/Sao_Paulo'));

        $produtosCarrinho = Carrinho::where('USUARIO_ID', Auth::user()->USUARIO_ID)
            ->where('ITEM_QTD', '>', 0)->get()->all();

        $pedido = Pedido::create([
            'USUARIO_ID'  => Auth::user()->USUARIO_ID,
            'STATUS_ID'   => 1, //pendente
            'PEDIDO_DATA' => $dataCompra->format('Y-m-d')
        ]);

        if ( isset($pedido->PEDIDO_ID) ) {
            foreach ($produtosCarrinho as $livro) {
                PedidoItem::create([
                    'PRODUTO_ID' => $livro->PRODUTO_ID,
                    'PEDIDO_ID'  => $pedido->PEDIDO_ID,
                    'ITEM_QTD'   => $livro->ITEM_QTD,
                    'ITEM_PRECO' => $livro->produto->PRODUTO_PRECO - $livro->produto->PRODUTO_DESCONTO
                ]);

                $estoqueAtual = ProdutoEstoque::where('PRODUTO_ID', $livro->PRODUTO_ID)->first()->PRODUTO_QTD;

                ProdutoEstoque::where('PRODUTO_ID',  $livro->PRODUTO_ID)
                    ->update(['PRODUTO_QTD' => $estoqueAtual - $livro->ITEM_QTD]);

                Carrinho::where('USUARIO_ID', Auth::user()->USUARIO_ID)
                    ->where('PRODUTO_ID',  $livro->PRODUTO_ID)
                    ->update(['ITEM_QTD' => 0]);
            }
        }

        session()->flash('success', 'Pedido Realizado com Sucesso!');

        return redirect()->route('pedido', $pedido->PEDIDO_ID);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Pedido  $pedido
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $precoTotal = 0;
        $endereco   = Endereco::where('USUARIO_ID', Auth::user()->USUARIO_ID)->get()->last();
        $items      = PedidoItem::where('PEDIDO_ID', $request->id)->get();

        if (!isset($items[0]) || $items[0]->pedido->USUARIO_ID != Auth::user()->USUARIO_ID)
            return redirect()->route('pedidos');

        foreach ($items as $item)
            $precoTotal += $item->ITEM_QTD * $item->ITEM_PRECO;

        return view('user.pedido')->with([
            'items'      => $items,
            'precoTotal' => $precoTotal,
            'endereco'   => $endereco
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\ProdutoResource;
use Illuminate\Http\Request;
use App\Models\Produto;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class ProdutoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Produto::ativos();

        $statusMessage = 0;
        $message = 'Todos os Produtos';
        $products = [];

        $filterQuery = $request->input('filter', null);

        if ($filterQuery) {
            $filterAcceptColumns = ['categoria'];

            [$filterColumn, $filterParam] = explode(':', $filterQuery);

            if (in_array($filterColumn, $filterAcceptColumns)) {
                if ($filterColumn == 'categoria') {
                    $query->whereRelation('categoria', 'CATEGORIA_NOME', '=', $filterParam);

                    $statusMessage = 200;
                }
            } else {
                // TODO - Qual a regra de negocio nesse caso
            }
        }

        $orderQuery = $request->input('order', null);

        if ($orderQuery) {
            $orderAcceptColumns = ['categoria', 'nome', 'preco'];

            $sorts = explode(',', $orderQuery);

            foreach ($sorts as $sortColumn) {
                $sortDirection = $orderQuery[0] == '-' ? 'DESC' : 'ASC';

                $sortColumn = ltrim($sortColumn, '-'); //retira espaco em branco ou outro caracter do comeco da string 

                if (in_array($sortColumn, $orderAcceptColumns)) {
                    if ($sortColumn == 'nome') {
                        $query->orderBy('PRODUTO_NOME', $sortDirection);
                    }

                    if ($sortColumn == 'preco') {
                        $query->orderBy('PRODUTO_PRECO', $sortDirection);
                    }

                    if ($sortColumn == 'categoria') {
                        // TODO
                    }
                } else {
                    // TODO - Qual a regra de negocio nesse caso
                }
            }
        }

        $page = $request->input('page', 1); // TODO - Validar se é numero 
        $productsPerPage = 10;
        
        try {
            $query->offset(($page - 1) * $productsPerPage)->limit($productsPerPage);
            $products = $query->get();

            $numberOfProducts = $products->count();
            $numberOfPages = ceil($numberOfProducts / $productsPerPage);
    
            $meta = [
                'numberOfPages'    => $numberOfPages,
                'numberOfProducts' => $numberOfProducts,
                'currentPage'      => $page,
                'productsPerPage'  => $productsPerPage
            ];
    
            return response()->json([
                "status"    => $statusMessage,
                "message"   => "Todos os produtos",
                "meta"      => $meta,
                "products"  => ProdutoResource::collection($products)
            ]);
        } catch (\Exception $err) {
            $class = get_class($err);

            return response()->json([
                "status"    => 500,
                "message"   => "Ops! Ocorreu um erro, por favor tente novamente."
            ]);
        }        
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

    public function search(Request $request)
    {
        $query = $request->name;
        $produto = Produto::ativos()->where('PRODUTO_NOME', 'like', '%' . $query . '%')->get();
        return response()->json(['Produtos' => $produto], 200);
        // return view('produto.search', ['produto' => $produto]);
    }
}

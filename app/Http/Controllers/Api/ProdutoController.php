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
     * TODO - Aplicar as regras de negocio
     * TODO - Remover codigo desnecessario
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
                return response()->json([
                    'status' => 400,
                    'message' => 'Paramêtro de filtro invalido'
                ], 400);
            }
        } else {
            $statusMessage = 200;
        }

        if ($statusMessage == 200) {
            $orderQuery = $request->input('order', null);

            if ($orderQuery) {
                $orderAcceptColumns = ['nome', 'preco'];
                $sorts = explode(',', $orderQuery);

                foreach ($sorts as $sortColumn) {
                    $sortDirection = $orderQuery[0] == '-' ? 'DESC' : 'ASC';
                    $sortColumn = ltrim($sortColumn, '-'); //retira espaco em branco ou outro caracter do comeco da string 

                    if (in_array($sortColumn, $orderAcceptColumns)) {
                        $sortColumn == 'nome' && $query->orderBy('PRODUTO_NOME', $sortDirection);
                        $sortColumn == 'preco' && $query->orderBy('PRODUTO_PRECO', $sortDirection);
                    }
                }
            }
        }

        $page = ctype_digit($request->input('page')) ? $request->input('page') : 1;

        $productsPerPage = 10;

        try {
            $query->offset(($page - 1) * $productsPerPage)->limit($productsPerPage);
            $products = $query->get();

            $totalOfProducts = $products->count();
            $numberOfPages   = ceil($totalOfProducts / $productsPerPage);

            $meta = [
                'total_pages'    => $numberOfPages,
                'total_items'    => $totalOfProducts,
                'current_page'   => $page,
                'items_per_page' => $productsPerPage
            ];

            return response()->json([
                "status"  => $statusMessage,
                "message" => $message,
                "meta"    => $meta,
                "data"    => ProdutoResource::collection($products)
            ]);
        } catch (\Exception $err) {
            //TODO - Fazer a verificacao de erro
            $classError = get_class($err);

            return response()->json([
                "status"  => 500,
                "message" => "Ops! Ocorreu um erro, por favor tente novamente."
            ]);
        }
    }


    /**
     * Display the specified resource.
     * TODO - Analisar a validacao dps, to com sono
     */
    public function show(Request $request)
    {
        if (!ctype_digit($request->id)) {
            return response()->json([
                'status'  => 400,
                'message' => 'O parametro precisar ser numerico'
            ], 400);
        }

        try {
            $product = Produto::ativos()->where('PRODUTO_ID', $request->id)->get();

            return response()->json([
                "status"    => 200,
                "message"   => null,
                "data"      => ProdutoResource::collection($product)
            ]);
        } catch (\Exception $err) {
            //TODO - Fazer a verificacao de erro
            $classError = get_class($err);

            return response()->json([
                "status"  => 500,
                "message" => "Ops! Ocorreu um erro, por favor tente novamente."
            ]);
        } 
    }

    /**
     * 
     * TODO - Fazer validações e tratamento de erros
     */
    public function search(Request $request)
    {
        $query = $request->name; 
        
        try {
            $product = Produto::ativos()->where('PRODUTO_NOME', 'like', '%' . $query . '%')->get();

            return response()->json([
                'status'  => 200,
                'message' => "A pesquisa por $query resultou em:",
                'data'    => ProdutoResource::collection($product)
            ], 200);

        } catch (\Exception $err) {
            //TODO - Fazer a verificacao de erro
            $classError = get_class($err);

            return response()->json([
                "status"  => 500,
                "message" => "Ops! Ocorreu um erro, por favor tente novamente."
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\ProdutoResource;
use Illuminate\Http\Request;
use App\Models\Produto;
use App\Http\Controllers\Controller;
use App\Traits\Exception as Errors;

class ProdutoController extends Controller
{
    use Errors;

    public function index(Request $request)
    {
        try {
            $query = Produto::ativos();

            $statusMessage = 0;
            $message = 'Produtos retornados com sucesso!';
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
                        'message' => 'Paramêtro de filtro invalido',
                        'data' => null
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
                        $sortColumn = ltrim($sortColumn, '-'); //retira espaco em branco ou outro caracter do começo da string

                        if (in_array($sortColumn, $orderAcceptColumns)) {
                            $sortColumn == 'nome' && $query->orderBy('PRODUTO_NOME', $sortDirection);
                            $sortColumn == 'preco' && $query->orderBy('PRODUTO_PRECO', $sortDirection);
                        }
                    }
                }
            }

            $page = ctype_digit($request->input('page')) ? $request->input('page') : 1;

            $productsPerPage = 10;

            $query->offset(($page - 1) * $productsPerPage)->limit($productsPerPage);
            $products = $query->get();

            $totalOfProducts = $products->count();
            $numberOfPages   = ceil($totalOfProducts / $productsPerPage);

            $meta = [
                'total_pages'    => $numberOfPages,
                'total_items'    => $totalOfProducts,
                'current_page'   => intval($page),
                'items_per_page' => $productsPerPage
            ];

            return response()->json([
                'status'  => $statusMessage,
                'message' => $message,
                'meta'    => $meta,
                'data'    => ProdutoResource::collection($products)
            ]);
        } catch (\Throwable $err) {
            return $this->exceptions($err);
        }
    }

    public function show(Request $request)
    {
        try {
            $product = Produto::ativos()->where('PRODUTO_ID', $request->id)->get();

            if(count($product) === 0) {
                return response()->json([
                    'status'    => 404,
                    'message'   => 'Não foi possível encontrar o produto informado.',
                    'data'      => null
                ], 404);
            }

            return response()->json([
                'status'    => 200,
                'message'   => 'Produto retornado com sucesso!',
                'data'      => ProdutoResource::collection($product)
            ]);
        } catch (\Throwable $err) {
            return $this->exceptions($err);
        }
    }

    public function search(Request $request)
    {
        try {
            $query = $request->name;
            $product = Produto::ativos()->where('PRODUTO_NOME', 'LIKE', '%' . $query . '%')->get();

            if(count($product) === 0) {
                return response()->json([
                    'status'    => 404,
                    'message'   => "A pesquisa por $query não encontrou resultados correspondentes.",
                    'data'      => null
                ], 404);
            }

            return response()->json([
                'status'  => 200,
                'message' => "A pesquisa por $query resultou em:",
                'data'    => ProdutoResource::collection($product)
            ], 200);
        } catch (\Throwable $err) {
            return $this->exceptions($err);
        }
    }
}

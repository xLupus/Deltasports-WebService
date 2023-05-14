<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProdutoResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->PRODUTO_ID,
            'name'          => $this->PRODUTO_NOME,
            'description'   => $this->PRODUTO_DESC,
            'price'         => $this->PRODUTO_PRECO,
            'discount'      => $this->PRODUTO_DESCONTO,

            'category' => [
                'id'            => $this->categoria->CATEGORIA_ID,
                'name'          => $this->categoria->CATEGORIA_NOME,
                'description'   => $this->categoria->CATEGORIA_DESC,
            ],

            'images'    => ImagensResource::collection($this->imagens),
            'stock'     => $this->estoque->PRODUTO_QTD
        ];
    }
}

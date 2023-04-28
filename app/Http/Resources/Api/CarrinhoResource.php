<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarrinhoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'product' => [
                'id' => $this->PRODUTO_ID,
                'qtd' => $this->ITEM_QTD,
                'name' => $this->PRODUTO_NOME,
                'description' => $this->PRODUTO_DESC,
                'price' => $this->PRODUTO_PRECO,
                'discount' => $this->PRODUTO_DESCONTO,
            ],
        ];
    }
}

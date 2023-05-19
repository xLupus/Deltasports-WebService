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
        if($this->PRODUTO_NOME && $this->PRODUTO_DESC && $this->PRODUTO_PRECO && $this->PRODUTO_DESCONTO) {
            return [
                'id' => $this->PRODUTO_ID,
                'qtd' => $this->ITEM_QTD,
                'name' => $this->PRODUTO_NOME,
                'description' => $this->PRODUTO_DESC,
                'price' => $this->PRODUTO_PRECO,
                'discount' => $this->PRODUTO_DESCONTO
            ];
        } else {
            return [
                'id' => $this->PRODUTO_ID,
                'qtd' => $this->ITEM_QTD
            ];
        }
    }
}

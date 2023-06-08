<?php

namespace App\Http\Resources\Api\Pedido;

use App\Http\Resources\Api\ProdutoResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PedidoShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'qtd'           => $this->ITEM_QTD,
            'price'         => $this->ITEM_PRECO,
            'status'        => $this->pedido->STATUS_ID,
            'date'          => $this->pedido->PEDIDO_DATA,

            'product'       => new ProdutoResource($this->itens)
        ];
    }
}

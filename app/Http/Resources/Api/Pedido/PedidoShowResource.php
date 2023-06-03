<?php

namespace App\Http\Resources\Api\Pedido;

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
            'id'            => $this->PEDIDO_ID,
            'product_id'    => $this->PRODUTO_ID,
            'qtd'           => $this->ITEM_QTD,
            'price'         => $this->ITEM_PRECO,
            'status'        => $this->pedido->STATUS_ID,
            'date'          => $this->pedido->PEDIDO_DATA,
        ];
    }
}

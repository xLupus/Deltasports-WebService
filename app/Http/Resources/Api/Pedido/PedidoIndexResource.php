<?php

namespace App\Http\Resources\Api\Pedido;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PedidoIndexResource extends JsonResource
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
            'status'        => $this->STATUS_ID,
            'date'          => $this->PEDIDO_DATA
        ];
    }
}

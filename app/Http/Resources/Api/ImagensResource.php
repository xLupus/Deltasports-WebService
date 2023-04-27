<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImagensResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->IMAGEM_ID ?? null,
            'order' => $this->IMAGEM_ORDEM ?? null,
            'url' => $this->IMAGEM_URL ?? null,
            'product_id' => $this->PRODUTO_ID ?? null
        ];
    }
}

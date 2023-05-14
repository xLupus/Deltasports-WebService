<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoriaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->CATEGORIA_ID,
            'nome' => $this->CATEGORIA_NOME,
            'desc' => $this->CATEGORIA_DESC,
            'active' => $this->CATEGORIA_ATIVO
        ];
    }
}

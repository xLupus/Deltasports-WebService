<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
                'id'    => $this->USUARIO_ID,
                'name'  => $this->USUARIO_NOME,
                'email' => $this->USUARIO_EMAIL,
                'cpf'   => $this->USUARIO_CPF
        ];
    }
}

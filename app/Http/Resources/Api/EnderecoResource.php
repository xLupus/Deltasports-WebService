<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnderecoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->ENDERECO_ID,
            'name'          => $this->ENDERECO_NOME,
            'street'        => $this->ENDERECO_LOGRADOURO,
            'number'        => $this->ENDERECO_NUMERO,
            'complement'    => $this->ENDERECO_COMPLEMENTO,
            'zip_code'      => $this->ENDERECO_CEP,
            'city'          => $this->ENDERECO_CIDADE,
            'state'         => $this->ENDERECO_ESTADO
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrinho extends Model
{
    use HasFactory;

    protected $table = 'CARRINHO_ITEM';

    public $timestamps = false;

    protected $fillable = [
        'USUARIO_ID',
        'PRODUTO_ID',
        'ITEM_QTD',
    ];

    protected function setKeysForSaveQuery($query) //seleciona as foreign keys
    {
        $query->where('USUARIO_ID', '=', $this->getAttribute('USUARIO_ID'))
            ->where('PRODUTO_ID', '=', $this->getAttribute('PRODUTO_ID'));

        return $query;
    }

}

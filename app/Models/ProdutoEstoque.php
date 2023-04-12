<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoEstoque extends Model
{
    use HasFactory;

    protected $table = 'PRODUTO_ESTOQUE';

    public $timestamps = false;

    protected $primaryKey = 'PRODUTO_ID';
}

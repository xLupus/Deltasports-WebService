<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'CATEGORIA';

    protected $primaryKey = 'CATEGORIA_ID';

    public $timestamps = false;

    public function produtos()
    {
        return $this->hasMany(Produto::class, 'CATEGORIA_ID')->where('PRODUTO_ATIVO', TRUE);
    }
}

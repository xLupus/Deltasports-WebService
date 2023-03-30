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

    
    public static function ativos()
    {
        return Categoria::where('CATEGORIA_ATIVO', TRUE)
                                ->whereRelation('produtos', 'PRODUTO_ATIVO', TRUE);
    }


    public function produtos()
    {
        return $this->hasMany(Produto::class, 'CATEGORIA_ID')
                    ->where('PRODUTO_ATIVO', TRUE)
                    ->whereRelation('estoque', 'PRODUTO_QTD', '>', 0);
    }
}

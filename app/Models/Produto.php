<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;

    protected $table = 'PRODUTO';

    protected $primaryKey = 'PRODUTO_ID';

    public $timestamps = false; 

    public static function ativos()
    {
        return Produto::with(['categoria', 'imagens', 'estoque'])
                            ->where('PRODUTO_ATIVO', TRUE)                            
                            ->whereRelation('categoria', 'CATEGORIA_ATIVO', TRUE)
                            ->whereRelation('estoque', 'PRODUTO_QTD', '>', 0);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'CATEGORIA_ID');
    }

    public function imagens()
    {
        return $this->hasMany(ProdutoImagem::class, 'PRODUTO_ID')
                    ->orderBy('IMAGEM_ORDEM', 'ASC');
    }

    public function estoque()
    {
        return $this->belongsTo(ProdutoEstoque::class, 'PRODUTO_ID');
    }
}

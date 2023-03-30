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

    public function categoria() {
        return $this->belongsTo(Categoria::class, 'CATEGORIA_ID');
    }

}

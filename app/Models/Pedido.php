<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PedidoItem;
use App\Models\PedidoStatus;
use Illuminate\Support\Facades\Auth;

class Pedido extends Model
{
    use HasFactory;

    protected $table = 'PEDIDO';

    protected $primaryKey = 'PEDIDO_ID';

    protected $fillable = [
        'USUARIO_ID',
        'STATUS_ID',
        'PEDIDO_DATA'
    ];

    public $timestamps = false;

    public function status() {
        return $this->belongsTo(PedidoStatus::class, 'STATUS_ID');
    }

    public function itens() {
        return $this->hasMany(PedidoItem::class, 'PEDIDO_ID');
    }
}

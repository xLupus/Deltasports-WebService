<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoStatus extends Model
{
    use HasFactory;

    protected $table = 'PEDIDO_STATUS';

    protected $primaryKey = 'STATUS_ID';

    public $timestamps = false;
}

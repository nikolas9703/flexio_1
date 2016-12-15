<?php
namespace Flexio\Modulo\Pedidos\Models;

use \Illuminate\Database\Eloquent\Model;



class PedidosCat extends Model
{
    protected $table        = 'ped_pedidos_cat';
    protected $fillable     = ['etiqueta'];
    protected $guarded      = ['id_cat'];
    public $timestamps      = false;
    
}

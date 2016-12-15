<?php namespace Flexio\Modulo\Pedidos\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class PedidosItems extends Model
{
    
    protected $table = 'ped_pedidos_inv_items';
    protected $fillable = ['categoria_id', 'id_pedido', 'id_item', 'observacion', 'cuenta', 'cantidad', 'unidad','atributo_id','atributo_text'];
    protected $guarded = ['id'];
    public $timestamps = false;
    
    public function item(){
        
        return $this->belongsTo('Flexio\Modulo\Inventarios\Models\Items', 'id_item');
        
    }
    
    public function atributo(){
        
        $this->belongsTo('Flexio\Modulo\Atributos\Models\Atributos', 'atributo_id')
                ->where('atr_atributos.atributable_type','Items_orm');
        
    }
    
}
<?php
namespace Flexio\Modulo\Inventarios\Models;

use \Illuminate\Database\Eloquent\Model as Model;

class LinesItems extends Model
{
    protected $table        = 'lines_items';
    protected $fillable     = ['categoria_id','tipoable_id','tipoable_type','item_id','cantidad'];
    protected $guarded      = ['id'];
    public $timestamps      = false;

    //relaciones
    public function tipoable()
    {
        return $this->morphTo();
    }

    public function item(){

        return $this->belongsTo('Flexio\Modulo\Inventarios\Models\Items', 'item_id');

    }

    public function seriales()
    {
        return $this->belongsToMany('Flexio\Modulo\Inventarios\Models\Seriales','inv_items_seriales_lines', 'line_id', 'serial_id');
    }

     public function setCantidadAttribute($value){

        $this->attributes['cantidad'] =  str_replace(",", "", $value);

    }

     public function setPrecioTotalAttribute($value){

        $this->attributes['precio_total'] =  str_replace(",", "", $value);
    }
}

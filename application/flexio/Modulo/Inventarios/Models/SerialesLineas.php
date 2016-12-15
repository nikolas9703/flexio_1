<?php
namespace Flexio\Modulo\Inventarios\Models;

use \Illuminate\Database\Eloquent\Model as Model;

class SerialesLineas extends Model
{
    protected $table        = 'inv_items_seriales_lines';
    protected $fillable     = ['serial_id', 'line_id'];
    protected $guarded      = ['id'];
    public $timestamps      = false;

    //relaciones
    function line_item()
    {
      return $this->belongsTo('Flexio\Modulo\Inventarios\Models\LinesItems', 'line_id');
    }
    
    function linesitems_ordenCompra(){
      return $this->belongsTo(LinesItems::class,'line_id')->where('tipoable_type','=',OrdenesCompra::class);
    }

    function serie(){
      return $this->belongsTo(Seriales::class,'serial_id');
    }
}

<?php
namespace Flexio\Modulo\Inventarios\Models;

use \Illuminate\Database\Eloquent\Model as Model;

class PreciosAlquiler extends Model
{
    protected $table        = 'inv_items_precios_alquiler';
    protected $fillable     = [
      'id_item', 'id_precio', 'hora', 'diario',
      'semanal', 'mensual','tarifa_4_horas', 'tarifa_6_dias',
      'tarifa_15_dias','tarifa_28_dias','tarifa_30_dias'
    ];
    protected $guarded      = ['id'];

}

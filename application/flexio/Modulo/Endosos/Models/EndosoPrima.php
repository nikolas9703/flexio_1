<?php 

namespace Flexio\Modulo\Endosos\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;



class EndosoPrima extends Model
{
  protected $table = 'end_endosos_prima';
  protected $fillable = ['id','id_endoso','prima_anual','impuesto','otros','descuentos','total','frecuencia_pago','metodo_pago','fecha_primer_pago','cantidad_pagos','sitio_pago','centro_facturacion','direccion_pago']; 
  protected $guarded = ['id'];
  public $timestamps = false;


  public function __construct(array $attributes = array()){
    $this->setRawAttributes(array_merge($this->attributes, array(
      'updated_at' => date('Y-m-d H:s:i'),
      'created_at' => date('Y-m-d H:s:i')
    )), true);
    parent::__construct($attributes);
  }

}

<?php 

namespace Flexio\Modulo\Endosos\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;



class EndosoVigencia extends Model
{
  protected $table = 'end_endosos_vigencia';
  protected $fillable = ['id','id_endoso','vigencia_desde','vigencia_hasta','suma_asegurada','tipo_pagador','pagador','poliza_declarativa']; 
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

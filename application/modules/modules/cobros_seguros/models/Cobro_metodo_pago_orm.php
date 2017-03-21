<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Cobro_metodo_pago_orm extends Model
{

    protected $table = 'cob_cobro_metodo_pago';

    protected $fillable = ['cobro_id','tipo_pago','total_pagado','referencia'];

    protected $guarded = ['id'];

    public function __construct(){

    }

    public function cobro(){
      return $this->belongsTo('Cobro_orm', 'cobro_id');
    }
    public function catalogo_metodo_pago(){
        return $this->belongsTo('Cobro_catalogo_orm','tipo_pago','etiqueta');
    }



}

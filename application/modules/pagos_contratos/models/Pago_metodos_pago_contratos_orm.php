<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Pago_metodos_pago_contratos_orm extends Model
{

    protected $table = 'pag_pagos_metodo_pago';

    protected $fillable = ['pago_id','tipo_pago','total_pagado','referencia'];

    protected $guarded = ['id'];

    public function __construct(){

    }

    public function pago(){
        return $this->belongsTo('Pagos_contratos_orm', 'pago_id');
    }
    
    public function catalogo_metodo_pago(){
        return $this->belongsTo('Pago_catalogos_contratos_orm','tipo_pago','etiqueta');
    }



}

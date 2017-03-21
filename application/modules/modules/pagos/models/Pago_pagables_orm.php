<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Pago_pagables_orm extends Model
{
    protected $table = 'pag_pagos_pagables';

    //facturas de compra//planilla//etc
    function pagable(){
        return $this->morphTo();
    }

    function pagos(){
        return $this->belongsTo('Pagos_orm','pago_id');
    }
}

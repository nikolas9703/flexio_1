<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Cobro_factura_orm extends Model
{
    protected $table = 'cob_cobro_facturas';

    function facturas(){
      return $this->belongsTo('Factura_orm','factura_id');
    }

    function cobros(){
      return $this->belongsTo('Cobro_orm','cobro_id');
    }
}

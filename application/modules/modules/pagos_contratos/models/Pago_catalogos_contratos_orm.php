<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Pago_catalogos_contratos_orm extends Model
{
    protected $table    = 'cob_cobro_catalogo';//uso el mismo catalogo de cobros
    protected $guarded  = ['id'];
    
    public function scopeEtapas($query){
        return $query->where('tipo','etapa');
    }
}

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Abono_metodos_abono_orm extends Model
{

    protected $table = 'abo_abonos_metodo_abono';

    protected $fillable = ['pago_id','tipo_abono','total_abonado','referencia'];

    protected $guarded = ['id'];

    public function __construct(){

    }

    public function abono(){
        return $this->belongsTo('Abonos_orm', 'abono_id');
    }
    
    public function catalogo_metodo_abono(){
        return $this->belongsTo('Abono_catalogos_orm','tipo_abono','etiqueta');
    }
    
}

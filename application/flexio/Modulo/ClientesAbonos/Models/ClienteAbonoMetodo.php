<?php 
namespace Flexio\Modulo\ClientesAbonos\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Clientes_abonos_metodos_abono_orm extends Model
{ 

    protected $table = 'cab_clientes_abonos_metodo_abono';

    protected $fillable = ['pago_id','tipo_abono','total_abonado','referencia'];

    protected $guarded = ['id'];

    public function __construct(){

    }

    public function abono(){
        return $this->belongsTo('Clientes_abonos_orm', 'abono_id');
    }
    
    public function catalogo_metodo_abono(){
        return $this->belongsTo('Clinetes_abonos_catalogos_orm','tipo_abono','etiqueta');
    }
    
}

<?php 
namespace Flexio\Modulo\ClientesAbonos\Models;
use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Clientes_abonos_catalogos_orm extends Model
{ 
    protected $table    = 'cob_cobro_catalogo';//uso el mismo catalogo de cobros
    protected $guarded  = ['id'];
    
    public function scopeEtapas($query){
        return $query->where('tipo','etapa');
    }
}

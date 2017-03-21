<?php 

namespace Flexio\Modulo\RemesasEntrantes\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Empresa\Models\Empresa;
use Flexio\Modulo\Cobros\Models\Cobro;

class RemesasEntrantes extends Model
{
    protected $table = 'seg_remesas_entrantes';
    protected $fillable = ['id','uuid_remesa_entrante','no_remesa','pagos_remesados','aseguradora_id','monto','fecha', 'usuario_id','estado', 'empresa_id', 'created_at', 'updated_at','fecha_desde','fecha_hasta','ramos_id','fecha_liquidada'];
    protected $guarded = ['id'];
    public $timestamps = false;


    public function __construct(array $attributes = array()){
        $this->setRawAttributes(array_merge($this->attributes, array(
            'uuid_remesa_entrante' => Capsule::raw("ORDER_UUID(uuid())")
        )), true);
        parent::__construct($attributes);
    }
	
	public function scopeDeEmpresa($query, $empresa_id) {
        return $query->where("seg_remesas_entrantes.empresa_id", $empresa_id);
    }  

    public static function findByUuid($uuid){
        return self::where('uuid_remesa_entrante',hex2bin($uuid))->first();
    }
	
	public function datosCobro() {
        return $this->hasOne(Cobro::class, 'num_remesa_entrante', 'no_remesa');
    }
}

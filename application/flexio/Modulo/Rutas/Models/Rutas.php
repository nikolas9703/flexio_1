<?php
namespace Flexio\Modulo\Rutas\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Usuarios\Models\Usuarios;
use Flexio\Modulo\Geo\Models\Provincia;
use Flexio\Modulo\Geo\Models\Corregimiento;
use Flexio\Modulo\Geo\Models\Distrito;

class Rutas extends Model{


	protected $table = 'seg_rutas';
	protected $fillable = ['uiid_ruta','nombre_ruta','provincia_id','distrito_id','corregimiento_id','nombre_mensajero','empresa_id','usuario_id','created_at','updated_at','estado'];
	protected $guarded = ['id'];
	
	public function creadopor() {
        return $this->hasOne(Usuarios::class, 'id', 'creado_por');
    }
	
	public function datosProvincia() {
        return $this->belongsTo(Provincia::class, 'provincia_id', 'id');
    }
	
	public function datosCorregimiento() {
        return $this->belongsTo(Corregimiento::class, 'corregimiento_id', 'id');
    }
	
	public function datosDistrito() {
        return $this->belongsTo(Distrito::class, 'distrito_id', 'id');
    }
}

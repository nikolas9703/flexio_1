<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Permisos_trabajo_orm extends Model
{
	protected $table = 'perm_permisos';
	protected $fillable = ['empresa_id', 'colaborador_id', 'tipo_permiso_id', 'fecha_desde', 'fecha_hasta', 'estado_id', 'cuenta_pasivo_id', 'observaciones', 'constancia_permiso', 'archivo_ruta', 'archivo_nombre', 'creado_por'];
	protected $guarded = ['id'];
	
	function acciones(){
		return $this->morphMany('Accion_personal_orm', 'accionable');
	}
	
	public function colaborador(){
		return $this->hasOne('Colaboradores_orm', 'id', 'colaborador_id');
	}
	
	public function estado(){
		return $this->hasOne('Estado_permisos_trabajo_orm', 'id_cat', 'estado_id');
	}
}

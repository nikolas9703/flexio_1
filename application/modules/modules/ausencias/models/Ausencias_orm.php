<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Ausencias_orm extends Model
{
	protected $table = 'aus_ausencias';
	protected $fillable = ['empresa_id', 'colaborador_id', 'tipo_ausencia_id', 'justificacion_id', 'cuenta_pasivo_id', 'fecha_desde', 'fecha_hasta', 'observaciones', 'estado_id', 'creado_por'];
	protected $guarded = ['id'];

	function acciones(){
		return $this->morphMany('Accion_personal_orm', 'accionable');
	}
	
	public function colaborador(){
		return $this->hasOne('Colaboradores_orm', 'id', 'colaborador_id');
	}
	
	public function estado(){
		return $this->hasOne('Estado_ausencias_orm', 'id_cat', 'estado_id');
	}
}

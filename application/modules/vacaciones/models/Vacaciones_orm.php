<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Vacaciones_orm extends Model
{
	protected $table = 'vac_vacaciones';
	protected $fillable = ['empresa_id', 'colaborador_id', 'dias_disponibles', 'fecha_desde', 'fecha_hasta', 'fecha_pago', 'cantidad_dias', 'estado_id', 'pago_inmediato_id', 'cuenta_pasivo_id', 'observaciones', 'archivo_ruta', 'archivo_nombre', 'creado_por'];
	protected $guarded = ['id'];

	function acciones(){
		return $this->morphMany('Accion_personal_orm', 'accionable');
	}
	
	public function colaborador(){
		return $this->hasOne('Colaboradores_orm', 'id', 'colaborador_id');
	}
	
	public function estado(){
		return $this->hasOne('Estado_vacaciones_orm', 'id_cat', 'estado_id');
	}
}

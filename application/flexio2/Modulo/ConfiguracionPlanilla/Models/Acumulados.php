<?php
namespace Flexio\Modulo\ConfiguracionPlanilla\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Acumulados extends Model{

	protected $table = 'pln_config_acumulados';
	protected $fillable = ['id','nombre','descripcion','tipo_acumulado','cuenta_pasivo_id','maximo_acumulable','fecha_corte','estado_id','empresa_id','creado_por','uuid_acumulado'];
	protected $guarded = ['id'];
	public $timestamps = false;
/*
	public function cuenta_pasivo(){
		return $this->hasOne('Cuentas_orm', 'id', 'cuenta_pasivo_id');
	}*/

	public function formula(){
		return $this->hasOne(AcumuladoConstructor::Class, 'acumulado_id');
	}


}

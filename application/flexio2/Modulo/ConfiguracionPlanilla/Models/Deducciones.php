<?php
namespace Flexio\Modulo\ConfiguracionPlanilla\Models;
  
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Deducciones extends Model{

	protected $table = 'pln_config_deducciones';
	protected $fillable = ['id','nombre','cuenta_pasivo_id','rata_colaborador','rata_colaborador_tipo','rata_patrono','rata_patrono_tipo','descripcion','estado_id','empresa_id','creado_por','uuid_deduccion','limite1','limite2'];
	protected $guarded = ['id'];
	public $timestamps = false;
	
	public function cuenta_pasivo(){
		return $this->hasOne('Cuentas_orm', 'id', 'cuenta_pasivo_id');
	}
	//Esta relacion es obsoleta, ya se esta usando por cambio en el diseño
	public function contructores(){
		return $this->hasMany('Deducciones_contructores_orm', 'deduccion_id');
	}
	
	 
}
 

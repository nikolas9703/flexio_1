<?php
namespace Flexio\Modulo\ConfiguracionPlanilla\Models;
  
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Modulos\Models\Catalogos;

class LiquidacionPagos extends Model{

   
	protected $table = 'pln_config_liquidaciones_pagos';
	protected $fillable = [
	'liquidacion_id',
	'tipo_pago_id' 
	 ];
	protected $guarded = ['id'];
	public $timestamps = false;
	
	
	/*public function deducciones_aplicables()
	{
		return $this->belongsToMany('Deducciones_orm', 'pln_config_liquidaciones_pagos_deducciones', 'pago_id', 'deduccion_id');
	}*/
	public function deducciones_aplicables()
	{
		return $this->hasMany('LiquidacionPagosDeduccion', 'deduccion_id');
	}
	public function deducciones()
	{
		return $this->hasMany(LiquidacionPagosDeduccion::Class, 'pago_id');
	}
	public function tipoPago()
	{
 		return $this->hasOne(Catalogos::Class, 'id_cat', 'tipo_pago_id');
	}
}
 

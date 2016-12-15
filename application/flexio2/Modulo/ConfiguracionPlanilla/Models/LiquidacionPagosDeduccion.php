<?php
namespace Flexio\Modulo\ConfiguracionPlanilla\Models;
  
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\ConfiguracionPlanilla\Models\Deducciones;


class LiquidacionPagosDeduccion extends Model{
    
	protected $table = 'pln_config_liquidaciones_pagos_deducciones';
	protected $fillable = [
	'pago_id',
	'deduccion_id' 
	 ];
	protected $guarded = ['id'];
	public $timestamps = false;
	
	public function deduccion_info()
	{
		
	//	return $this->belongsTo(Deducciones::Class );
		return $this->belongsTo(Deducciones::Class, 'deduccion_id', 'id');
		//return $this->belongsToMany(Deducciones::Class, 'pln_config_liquidaciones_pagos_deducciones','deduccion_id', 'id');
	}
	
}
 

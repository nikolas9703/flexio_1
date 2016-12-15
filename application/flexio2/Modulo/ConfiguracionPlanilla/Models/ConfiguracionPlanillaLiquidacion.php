<?php
namespace Flexio\Modulo\ConfiguracionPlanilla\Models;

use Illuminate\Database\Eloquent\Model      as Model;
use Illuminate\Database\Capsule\Manager     as Capsule;
use Flexio\Modulo\ConfiguracionPlanilla\Models\LiquidacionPagos;
use Flexio\Modulo\ConfiguracionPlanilla\Models\LiquidacionPagosDeduccion;
use Carbon\Carbon                           as Carbon;
use Flexio\Modulo\Modulos\Models\Catalogos;


class ConfiguracionPlanillaLiquidacion extends Model
{
    protected $table = 'pln_config_liquidaciones';
    protected $fillable = [
        'tipo_liquidacion',
        'estado_id',
        'empresa_id',
        'creado_por',
        'uuid_liquidacion',
        'fecha_creacion'
    ];
    protected $guarded = ['id','uuid_liquidacion'];


    public function __construct(array $attributes = array())
    {
        $this->setRawAttributes(array_merge($this->attributes, array(
            'uuid_liquidacion' => Capsule::raw("ORDER_UUID(uuid())")
        )), true);
        parent::__construct($attributes);
    }

    //Gets
    public function getUuidLiquidacionAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function empresa() {
    	return $this->belongsTo('Empresa_orm', 'empresa_id');
    }
    public function tipo_liquidacion() {
    	return $this->belongsTo('Catalogos_orm', 'tipo_liquidacion');
    }
    
 	public function pagos_aplicables()
 	{
   		return $this->belongsToMany('Catalogos_orm', 'pln_config_liquidaciones_pagos', 'liquidacion_id', 'tipo_pago_id')->where('identificador','=','pagos_aplicables_liquidaciones');
 	} 

 	public function pagos_acumulados()
 	{
   		return $this->belongsToMany('Catalogos_orm', 'pln_config_liquidaciones_pagos', 'liquidacion_id', 'tipo_pago_id')->where('identificador','=','pagos_aplicables_liquidaciones_acumulado');
  	}
  	
  	public function deducciones_pagos()
  	{
  		return $this->belongsToMany('LiquidacionPagosDeduccion', 'pln_config_liquidaciones_pagos', 'liquidacion_id', 'tipo_pago_id');
  	}
  	
  	/*public function pagos()
  	{
  		return $this->hasMany(LiquidacionPagos::Class, );
  	}*/
  	
  	public function pagos()
	{
		return $this->hasMany(LiquidacionPagos::Class, 'liquidacion_id');
	}
	
 	
  

}
?>
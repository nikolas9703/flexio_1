<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Pagadas_orm extends Model
{
	protected $table = 'pln_pagadas';
	protected $fillable = ['salario_bruto','planilla_id','colaborador_id','estado','fecha_inicial','fecha_final', 'fecha_pago', 'salario_mensual_promedio', 'salario_anual_promedio', 'total_devengado_hasta_fecha', 'indemnizacion_proporcional','planilla_codigo','ciclo_de_pago','centro_contable','rata','salario_neto','deducciones_total'];
	protected $guarded = ['id'];
        protected $fecha_pago = ['fecha_pago'];
	public $timestamps = false;
	
 	public function acumulados()
	{
		return $this->hasMany('Pagadas_acumulados_orm', 'planilla_pagada_id');
	}
 	public function deducciones()
	{
		return $this->hasMany('Pagadas_deducciones_orm', 'planilla_pagada_id');
	}
  	public function descuentos()
 	{
 		return $this->hasMany('Pagadas_descuentos_orm', 'planilla_pagada_id');
 	}
 	public function ingresos()
 	{
  		return $this->hasMany('Pagadas_ingresos_orm', 'planilla_pagada_id');
 	}
 	public function calculos()
 	{
  		return $this->hasMany('Pagadas_calculos_orm', 'planilla_pagada_id');
 	}
        public function getFechaPagoAttribute($fecha_pago){
        return Carbon::createFromFormat('Y-m-d', $fecha_pago)->format('d/m/Y');
    }
 	
 	  
}


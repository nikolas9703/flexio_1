<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Pagadas_ingresos_orm extends Model
{
	protected $table = 'pln_pagadas_ingresos';
	protected $fillable = ['planilla_pagada_id', 'detalle','cantidad_horas','rata','calculo','fecha_creacion'];
	protected $guarded = ['id'];
	public $timestamps = false;
	
	
	/*public static function salario_bruto_oncemeses($colaborador_id = NULL){
	
	
		$salario_bruto_oncemeses = 0;
		$fecha_hoy = date('Y-m-j');
	
		$ultimo_periodo = strtotime ( '-11 month' , strtotime ( $fecha_hoy ) ) ;
		$ultimo_periodo = date ( 'Y-m-j' , $ultimo_periodo );
	
		$salario_oncemeses = Capsule::table('pln_pagadas_colaborador as cerrada')
		->where('cerrada.colaborador_id', $colaborador_id)
		->where('cerrada.fecha_inicial', ">", $ultimo_periodo)
		->where('cerrada.fecha_final', "<=", $fecha_hoy)
		->distinct()
		->get();
		$i= 0;
		if(!empty($salario_oncemeses)){
			foreach($salario_oncemeses as $row){
	
				$salario_bruto_oncemeses += $row->salario_bruto;
				++$i;
			}
			if($i < 11){
				$salario_bruto_oncemeses = ($salario_bruto_oncemeses/$i)*11;
			}
		}
			
		return $salario_bruto_oncemeses;
	
	}*/
}
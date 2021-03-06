 <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Planilla_vacacion_orm extends Model
{
	protected $table = 'pln_planilla_vacacion';
	protected $fillable = [ 'planilla_id','vacacion_id','estado_ingreso_horas'];
	protected $guarded = ['id'];
	public $timestamps = false;


	//Salario que ha ganado en este periodo de vacaciones
	public static function salario_total_devengado_vacacion($colaborador_id = NULL, $periodo = array(), $fecha_inicio_contrato = NULL ){

 		if(empty($periodo)){
			$vacaciones =  Planilla_vacacion_orm::periodo_vacaciones($fecha_inicio_contrato);
 	  		/*$inicial 	= $vacaciones['inicial'];
	  		$final 		= $vacaciones['final'];*/
        $inicial = isset($vacaciones['inicial'])?$vacaciones['inicial']:"";
        $final = isset($vacaciones['final'])?$vacaciones['final']:"";
      }else{
			$inicial 	= $periodo['inicial'];
			$final 		= $periodo['final'];
		}

		$salario_devengado = Capsule::table('pln_pagadas_colaborador as cerrada')
		->leftJoin('pln_planilla as pln', 'pln.id', '=', 'cerrada.planilla_id')
		->leftJoin('mod_catalogos as cat', 'cat.id_cat', '=', 'pln.tipo_id')
		->where('cerrada.colaborador_id', $colaborador_id)
		->where('cat.identificador', 'Tipo Planilla')
		->where('cat.valor', 'regular')
		->where('pln.rango_fecha1', ">=", $inicial)
		->where('pln.rango_fecha2', "<=", $final)
		->sum("salario_bruto");

		return $salario_devengado;
 	}
 	public static function salario_devengado_vacaciones_vencidas($colaborador_id = NULL, $periodo = array(), $fecha_inicio_contrato = NULL ){

  			$inicial 	= $periodo['inicial'];
 			$final 		= $periodo['final'];

 		$salario_devengado = Capsule::table('pln_pagadas_colaborador as cerrada')
 		->leftJoin('pln_planilla as pln', 'pln.id', '=', 'cerrada.planilla_id')
 		->leftJoin('mod_catalogos as cat', 'cat.id_cat', '=', 'pln.tipo_id')
 		->where('cerrada.colaborador_id', $colaborador_id)
 		->where('cat.identificador', 'Tipo Planilla')
 		->where('cat.valor', 'regular')
 		->where('pln.rango_fecha1', ">=", $inicial)
 		->where('pln.rango_fecha2', "<=", $final)
 		->get(array("pln.*","cerrada.*"));

 		return $salario_devengado;


 	}
		public static function periodo_vacaciones($fecha_contrato = NULL){

			$almanenando_fechas = array();
		$i=0;
		while($fecha_contrato < date("Y-m-d")) {

			$almanenando_fechas[] = $fecha_contrato;
			$nueva_fecha = strtotime ( '+1 year' , strtotime ( $fecha_contrato ) ) ;

			if($i>0){
				$periodos[] = array(
						"inicial"	=>$almanenando_fechas[$i-1],
						"final"	=> $fecha_contrato,
				);
			}
			$fecha_contrato = date ( 'Y-m-d' , $nueva_fecha );
			++$i;
		}
		if(count($almanenando_fechas)>0){
			if($almanenando_fechas[$i-1] < date("Y-m-d")){
				$periodos[] = array(
						"inicial"	=>$almanenando_fechas[$i-1],
						"final"	=> date("Y-m-d"),
				);
			}
			return end($periodos);

		}else{
			return array();
		}



	}


}

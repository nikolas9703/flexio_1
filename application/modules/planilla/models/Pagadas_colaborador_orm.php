<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
 class Pagadas_colaborador_orm extends Model
{
	protected $table = 'pln_pagadas_colaborador';
	protected $fillable = ['salario_bruto','salario_neto','planilla_id','uuid_colaborador','colaborador_id','contrato_id',  'fecha_pago','fecha_creacion','fecha_cierre_planilla'];
	protected $guarded = ['id'];
	public $timestamps = true;

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


 	public function planilla()
 	{
 		return $this->hasOne('Planilla_orm','id','planilla_id');
 	}

 	public static function  periodo_decimo_tercer_mes(){

 		$periodo  = array();
  		$dias_decimo[] = date("Y")."-04-15";
 		$dias_decimo[] = date("Y")."-08-15";
 		$dias_decimo[] = date("Y")."-12-15";

 		if(date("Y-m-d") < $dias_decimo[0] ){

 			$ano_anterior = mktime(0, 0, 0, date("m"),   date("d"),   date("Y")-1);
 			$ano_anterior = date ( 'Y' , $ano_anterior );

 			$periodo = array(
 					"inicial" => $ano_anterior.'-12-15',
 					"final" =>$dias_decimo[0]
 			);
 		}
 		else if( date("Y-m-d") > $dias_decimo[0] &&   date("Y-m-d") < $dias_decimo[1] ){
 			$periodo = array(
 					"inicial" =>$dias_decimo[0],
 					"final" =>$dias_decimo[1]
 			);
 		}
 		else if(date("Y-m-d") > $dias_decimo[1] &&   date("Y-m-d") < $dias_decimo[2] ){
 			$periodo = array(
 					"inicial" =>$dias_decimo[1],
 					"final" =>$dias_decimo[2]
 			);
 		}
 		else{

 			$ano_siguiente = mktime(0, 0, 0, date("m"),   date("d"),   date("Y")+1);
 			$ano_siguiente = date ( 'Y' , $ano_siguiente );

 			$periodo = array(
 					"inicial" =>$dias_decimo[2],
 					"final" => $ano_siguiente."-04-15"
 			);
 		}

 		return $periodo;
 	}

   	public static function  salario_total_devengado_decimo($colaborador_id = NULL, $fecha_planilla = array(), $ciclo = false){

 		if($ciclo == true){
  			$ciclo_decimo = Pagadas_colaborador_orm::periodo_decimo_tercer_mes();
 			$inicial = $ciclo_decimo['inicial'];
 			$final = $ciclo_decimo['final'];
 		}else{

	 		$inicial = $fecha_planilla['fecha_rango1'];
	 		$final 	 = $fecha_planilla['fecha_rango2'];
 		}

 		$salario_devengado_decimo = Capsule::table('pln_pagadas_colaborador as cerrada')
 		->leftJoin('pln_planilla as pln', 'pln.id', '=', 'cerrada.planilla_id')
 		->leftJoin('mod_catalogos as cat', 'cat.id_cat', '=', 'pln.tipo_id')
 		->where('cerrada.colaborador_id', $colaborador_id)
 		->where('cat.identificador', 'Tipo Planilla')
 		->where('cat.valor', 'regular')
 		->where('pln.rango_fecha1', ">=", $inicial)
 		->where('pln.rango_fecha2', "<=", $final)
 		->sum("cerrada.salario_bruto");

  		return $salario_devengado_decimo;

 	}
 	//Salario que ha ganado en todo el contrato
 	public static function  salario_total_devengado($colaborador_id = NULL){

 		$salario_devengado = Capsule::table('pln_pagadas_colaborador as cerrada')
 		->leftJoin('pln_planilla as pln', 'pln.id', '=', 'cerrada.planilla_id')
 		->leftJoin('mod_catalogos as cat', 'cat.id_cat', '=', 'pln.tipo_id')
 		->where('cerrada.colaborador_id', $colaborador_id)
 		->where('cat.identificador', 'Tipo Planilla')
 		->where('cat.valor', 'regular')
 		->sum("salario_bruto");

 		return $salario_devengado;

 	}

 	public static function salario_bruto_anual($colaborador_id = NULL){

 		$salario_bruto_trecemeses = 0;
 		$fecha_hoy = date('Y-m-j');

 		$ultimo_periodo = strtotime ( '-12 month' , strtotime ( $fecha_hoy ) ) ;
 		$ultimo_periodo = date ( 'Y-m-j' , $ultimo_periodo );
 		$salario_anual = Capsule::table('pln_pagadas_colaborador as cerrada')
 		->leftJoin('pln_planilla as pln', 'pln.id', '=', 'cerrada.planilla_id')
 		->leftJoin('mod_catalogos as cat', 'cat.id_cat', '=', 'pln.tipo_id')
 		->where('cerrada.colaborador_id', $colaborador_id)
 		->where('cat.identificador', 'Tipo Planilla')
 		->where('cat.valor', 'regular')
 		->where('pln.rango_fecha1', ">", $ultimo_periodo)
 		->where('pln.rango_fecha2', "<=", $fecha_hoy)
 		->orderBy('pln.rango_fecha1',' DESC')
 		->get();

  		$i= $sumatoria_salario_bruto = $total_anual = 0;
 		if(!empty($salario_anual)){
 			foreach($salario_anual as $row){

 				$sumatoria_salario_bruto += $row->salario_bruto;
 				$fecha_inicial = $row->rango_fecha1;

 				++$i;
 			}

  			$cantidad_meses = Pagadas_colaborador_orm::meses_entre_fechas($fecha_inicial);
 			if($cantidad_meses < 12){
 				$total_anual = ($sumatoria_salario_bruto/$cantidad_meses)*12;
 			}else{
 				$total_anual = $sumatoria_salario_bruto;
 			}

 		}

 		return $total_anual;

 	}

 	public static function salarios_ganados_decimo($colaborador_id = NULL, $fecha_planilla = array()){
  		$resultado = array();
 		$inicial = $fecha_planilla['rango_fecha1'];
 		$final 	 = $fecha_planilla['rango_fecha2'];

 		$salarios_devengados = Capsule::table('pln_pagadas_colaborador as cerrada')
 		->leftJoin('pln_planilla as pln', 'pln.id', '=', 'cerrada.planilla_id')
 		->leftJoin('mod_catalogos as cat', 'cat.id_cat', '=', 'pln.tipo_id')
 		->where('cerrada.colaborador_id', $colaborador_id)
 		->where('cat.identificador', 'Tipo Planilla')
 		->where('cat.valor', 'regular')
 		->where('pln.rango_fecha1', ">=", $inicial)
 		->where('pln.rango_fecha2', "<=", $final)
 		->orderBy('pln.rango_fecha2',' desc')
 		->get(array("cerrada.*","pln.*"));
 		if(!empty($salarios_devengados)){
 			foreach($salarios_devengados as $salario){
 				$dateTime = new DateTime($salario->rango_fecha2);
 				$fecha = $dateTime->format('M d, Y');
   				$resultado[] = array(
  						"salario_bruto"=> $salario->salario_bruto,
  						"fecha_final" => $fecha//$salario->rango_fecha2
  				);
 			}
 		}

  		return array_reverse($resultado , true);
 	}
 	  public static function  meses_entre_fechas($fecha_inicial = NULL){

 		$fecha_hoy = date('Y-m-d');


 		$fechainicial 	= new DateTime($fecha_inicial);
 		$fechafinal 	= new DateTime($fecha_hoy);

 		$diferencia = $fechainicial->diff($fechafinal);
 		$meses = ( $diferencia->y * 12 ) + $diferencia->m;


 		return $meses+1;
 	}

         public static function seguro_social($clause){
        $tipo_deduccion = 31;
        $query = self::with(array('deducciones' => function($query) use($tipo_deduccion){
        $query->where("deduccion_id", $tipo_deduccion);
         }));


        $query->where($clause);


        return $query->get();


        }
        public static function seguro_educativo($clause){
        $tipo_deduccion = 33;
        $query = self::with(array('deducciones' => function($query) use($tipo_deduccion){
        $query->where("deduccion_id", $tipo_deduccion);
         }));


        $query->where($clause);


        return $query->get();


        }

        public static function impuesto_renta($clause){
        $tipo_deduccion = 32;
        $query = self::with(array('deducciones' => function($query) use($tipo_deduccion){
        $query->where("deduccion_id", $tipo_deduccion);
         }));


        $query->where($clause);


        return $query->get();


        }

        public static function cuota_sindical($clause){
        $tipo_deduccion = 35;
        $query = self::with(array('deducciones' => function($query) use($tipo_deduccion){
        $query->where("deduccion_id", $tipo_deduccion);
         }));


        $query->where($clause);


        return $query->get();


        }
}

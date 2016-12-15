<?php
namespace Flexio\Modulo\Planilla\Repository;

use Flexio\Modulo\Planilla\Models\Planilla;
use Flexio\Modulo\Planilla\Models\Pagadas\PagadasColaborador;
use Flexio\Modulo\Planilla\Models\Pagadas\PagadasIngresos;
use Flexio\Modulo\Planilla\Models\Pagadas\PagadasDeducciones;
use Flexio\Modulo\Planilla\Models\Pagadas\PagadasDescuentos;

use Flexio\Modulo\Planilla\Repository\IndemnizacionRepository;
use Flexio\Modulo\Planilla\Repository\PrimaAntiguedadRepository;
use Flexio\Modulo\Planilla\Repository\VacacionProporcionalRepository;
use Flexio\Modulo\Planilla\Repository\Regular\PlanillaRegularRepository;

 class LiquidacionPagadaRepository {

 protected $indemnizacionRepository;
 protected $primaAntiguedadRepository;
 protected $vacacionProporcionalRepository;
 protected $decimoTercerRepository;
 protected $planillaRegularRepository;

 function __construct() {
     $this->indemnizacionRepository     = new  IndemnizacionRepository();
     $this->primaAntiguedadRepository   = new  PrimaAntiguedadRepository();
     $this->vacacionProporcionalRepository  = new  VacacionProporcionalRepository();
     $this->decimoTercerRepository      = new  DecimoTercerRepository();
     $this->planillaRegularRepository      = new  PlanillaRegularRepository();
 }


  function actualizando_planilla($objetoPlanilla) {
  		$planilla = Planilla::where('id', '=', $objetoPlanilla->id)->update(array("estado_id"=> 14));
  		//$planilla = Planilla::find($objetoPlanilla->id);
   		//$planila->liquidacionesPlanilla()->delete();
  		return $planilla;
   }

  function coleccion_tablas($objetoReporte) {

   	$tabla['ingresos'] 		= $this->haciendo_tabla_ingresos($objetoReporte);
   	$tabla['deducciones'] 	= $this->haciendo_tabla_deducciones($objetoReporte);
   	$tabla['descuentos'] 	= $this->haciendo_tabla_descuentos($objetoReporte);
   	$tabla['calculos'] 		= $this->haciendo_tabla_calculos($objetoReporte);
   	return $tabla;
  }

  private function calculando_dias_laborados($fecha_ingreso) {
	  	$startTimeStamp = strtotime($fecha_ingreso);
	  	$endTimeStamp = strtotime(date("Y-m-d"));
	  	$timeDiff = abs($endTimeStamp - $startTimeStamp);
	  	$numberDays = $timeDiff/86400;  // 86400 seconds in one day
	  	$numberDays = intval($numberDays);
	  	$cantidad_ano = $numberDays/365;

	  	return $cantidad_ano;
  }
  private function haciendo_tabla_calculos($objetoReporte) {

 		   	$fecha_ingreso = $objetoReporte[0]['colaborador']['colaboradores_contratos'][0]['fecha_ingreso'];
 		   	$cantidad_ano = $this->calculando_dias_laborados($fecha_ingreso);

 		  	$lista = [];
		  	$total_devengado = 0;
		  	if(!empty($objetoReporte[0]['colaborador']['salarios_devengados']))
		  	{
		  		foreach($objetoReporte[0]['colaborador']['salarios_devengados'] as $salarios){
		   			$total_devengado += $salarios['salario_bruto'];
		  		}
		  	}

 		  	$anual_promedio = ($cantidad_ano>0)?$total_devengado/$cantidad_ano:0;
		  	$lista[] = array(
		  			"detalle" => 'Salario mensual promedio',
		   			"monto" => $anual_promedio/12,
		   	);
		  	$lista[] = array(
		  			"detalle" => 'Salario anual promedio',
		  			"monto" => $anual_promedio
		  	);
		  	$lista[] = array(
		  			"detalle" => 'Total Devengado',
		  			"monto" => $total_devengado
		  	);
		  	return $lista;
  }


  private function haciendo_tabla_descuentos($objetoReporte) {
	  	$lista = [];

	  	if(!empty($objetoReporte[0]['pagos']))
	  	{
	  		foreach($objetoReporte[0]['pagos'] as $pago){

	  			if(!empty($pago['descuentos'])){
	  				foreach ($pago['descuentos'] as $descuento){
   	  					$lista[] = array(
	   							"codigo" => $descuento['codigo'],
	   							"acreedor" => $descuento['acreedor'],
	   							"monto" => $descuento['monto'],
	  							"descuento" => $descuento['monto'],
	  							"tipo" => $descuento['nombre'],
	  							"monto_adeudado" =>$descuento['monto_adeudado']
	   					);
	  				}
	  			}

	  		}
	  	}
    	return $lista;
    }
 private function haciendo_tabla_ingresos($objetoReporte) {
  	$ingresos = [];

  	if(!empty($objetoReporte[0]['pagos']))
  	{
   		foreach($objetoReporte[0]['pagos'] as $pago){
   			$ingresos[] = array(
  					"detalle" => $pago['nombre'],
  					"cantidad_horas" => 0,
  					"rata" => 0,
  					"calculo" =>$pago['monto']
  			);
  		}
  	}

  	if(isset($objetoReporte[0]['prima_productividad']) && $objetoReporte[0]['prima_productividad'] > 0){
  	    $ingresos[] = array(
  	        "detalle" => 'Prima de Productividad',
  	        "cantidad_horas" => 0,
  	        "rata" => 0,
  	        "calculo" =>$objetoReporte[0]['prima_productividad']
  	    );
  	}
  	return $ingresos;
  }
  private function haciendo_tabla_deducciones($objetoReporte) {

   	$lista = [];

  	if(!empty($objetoReporte[0]['pagos']))
  	{
  		foreach($objetoReporte[0]['pagos'] as $pago){

  			if(!empty($pago['deducciones'])){
  				foreach ($pago['deducciones'] as $deduccion){
  					$lista[] = array(
   							"nombre" => $deduccion['nombre'],
  							"descuento" => $deduccion['monto'],
  							"tipo" => $pago['nombre']
   					);
  				}
  			}

  		}
  	}

   	return $lista;
  }
  function cerrando_planilla($objetoPlanilla, $objetoConfig) {


   		$colaboradores	= $this->formando_info_colab($objetoPlanilla, $objetoConfig);

  		if(!empty($colaboradores)){
  			foreach($colaboradores as $salario){
     			$matriz 			= $this->formando_arrays_creacion($objetoPlanilla, $salario);

   				if(!empty($matriz['colaborador']))
   					$pagadas = PagadasColaborador::create($matriz['colaborador']);
   				if(!empty($matriz['ingresos']))
  					$pagadas->ingresos()->saveMany($matriz['ingresos']);
  				if(!empty($matriz['deducciones']))
  					$pagadas->deducciones()->saveMany($matriz['deducciones']);
  				if(!empty($matriz['descuentos']))
   					$pagadas->descuentos()->saveMany($matriz['descuentos']);
  			}
  		}
  		return $pagadas;
  }

  function reporte_colaborador($objetoPlanilla, $objetoConfig) {
     	$valores_salarios = $this->formando_info_colab($objetoPlanilla, $objetoConfig);
    	return $valores_salarios;
  }

  private function formando_arrays_creacion($objetoPlanilla= array(), $data = array()) {
   		$matriz = [];
   		$matriz['colaborador'] = $this->set_pagadas_colaborador($objetoPlanilla, $data);
   			if(!empty($data['pagos'] )){
	   	 		foreach($data['pagos']  as $pago){
 	   	 			$ingreso = $this->set_pagadas_ingresos($pago);
	   	 			$matriz['ingresos'][] = new PagadasIngresos($ingreso);

  	   	 			if( !empty($pago['deducciones'])){
	   	 				foreach($pago['deducciones']  as $deduccion){

 	   	 					$deduccion_list  = $this->set_pagadas_deducciones($deduccion);
	   	 					$matriz['deducciones'][] = new PagadasDeducciones($deduccion_list);
 	   	 				}
 	   	 			}
 	   	 			if( !empty($pago['descuentos'])){
 	   	 				foreach($pago['descuentos']  as $valor){

 	   	 					$descuento  = $this->set_pagadas_descuento($valor);
 	   	 					$matriz['descuentos'][] = new PagadasDescuentos($descuento);
 	   	 				}
 	   	 			}
 	   	 		}
	   	 	}

    		return $matriz;
   }

  function calculo_valores($objetoPlanilla, $objetoConfig) {

   	$total_planilla = $total_deduccion = 0;
  	$data = [];
  	$valores_salarios = $this->formando_info_colab($objetoPlanilla, $objetoConfig);
   	$data['cantidad_colaboradores'] = count($valores_salarios);


	   if(!empty($valores_salarios )){
	   	 foreach($valores_salarios  as $valor){
	   	 	if(!empty($valor['pagos'] )){
	   	 		foreach($valor['pagos']  as $pago){
	   	 			$total_planilla += $pago['monto'];
 	   	 			if( !empty($pago['deducciones'])){
	   	 				foreach($pago['deducciones']  as $deduccion){
	   	 					$total_deduccion += $deduccion['monto'];
	   	 				}
 	   	 			}
 	   	 		}
	   	 	}
 	   	 }
	 }

  	$data['total_planilla'] = $total_planilla;
  	$data['total_deduccion'] =$total_deduccion;
   	return $this->formando_totales_modal($data);
  }

   	private function formando_totales_modal($data = array()) {
 	  	$deduccion_total = $data['total_deduccion']>0?number_format($data['total_deduccion'],2):0;
 	  	$deduccion_calculo = $data['total_deduccion']>0?number_format(($data['total_deduccion']/$data['total_planilla'])*100,2):0;

	  	$data['total_deduccion'] 			=  $deduccion_total;
	  	$data['deducciones_porcentaje'] 	=  $deduccion_calculo.'%';
	  	$data['deducciones_progress_bar']   =  $deduccion_calculo;

	  	$salario_neto = $data['total_planilla'] - $data['total_deduccion'];
	  	$salario_neto_total =  $salario_neto>0?number_format($salario_neto,2):0;
	  	$neto_total = $salario_neto>0?number_format((($salario_neto)/$data['total_planilla'])*100,2):0;

	  	$data['salario_neto']			   =   	$salario_neto_total;
	  	$data['salario_neto_porcentaje']   =  	$neto_total.'%';
	  	$data['salario_neto_progress_bar'] =   	$neto_total;

	  	return $data;
  }

  private function lista_descuentos_by_colaborador($ObjectDescuento) {
   	$m = 0;
   	$lista_pagos = [];
  	if(!empty($ObjectDescuento)){
  		foreach($ObjectDescuento as $descuento){

    		$lista_pagos[$m]['nombre'] 	= "Descuento 1";
  			$lista_pagos[$m]['monto'] 	= $descuento['monto_ciclo'];
  			$lista_pagos[$m]['codigo'] 	= $descuento['codigo'];
  			$lista_pagos[$m]['acreedor']= $descuento['acreedor']['nombre'];
  			$lista_pagos[$m]['descuento_id']= $descuento['id'];
  			$lista_pagos[$m]['tipo_descuento_id']=  $descuento['tipo_descuento_id'];
  			$lista_pagos[$m]['monto_adeudado']=  $descuento['monto_adeudado'];
  			++$m;
  		}
  	}
   	return $lista_pagos;
  }


  private function lista_pagos_liquidacion($tipo_pago, $ObjetoColaborador = array()) {

   	$monto = '0.00';
  	switch ($tipo_pago) {
  		case 104://Salario devengado no pagado
  			$monto = $ObjetoColaborador['salario_devengado_no_pagado'];
  			break;

  		case 105://Prima de antig�edad proporcional
  			$monto =$ObjetoColaborador['prima_antiguedad_proporcional']; //
   			break;

  		case 106://Preaviso
  			$monto =$ObjetoColaborador['preaviso'];
  			break;

  		case 107://Indemnizaci�n
  			$monto = $ObjetoColaborador['indemnizacion'];
  			break;

  		case 108://XIII Mes proporcional
  			$monto = $ObjetoColaborador['decimo_mes'];
  			break;

  		case 109://Vacaciones proporcionales
  			$monto = $ObjetoColaborador['vacaciones_proporcionales'];
  			break;

  		default:
  			$monto = "0.00";
  	}
  	 return $monto;
  }


  private function divisiones_devengados($ObjetoColaborador) {

    $ingresos_regulares = $this->planillaRegularRepository->ingresos_regulares($ObjetoColaborador);

   	$monto['salario_devengado_no_pagado']        = $ingresos_regulares['salario_bruto'];
   	$monto['prima_antiguedad_proporcional']      = $this->primaAntiguedadRepository->prima_antiguedad_proporcional($ObjetoColaborador);
  	$monto['preaviso']                           = 0;
  	$monto['indemnizacion']                      = $this->indemnizacionRepository->indemnizacion_proporcional($ObjetoColaborador);
  	$monto['decimo_mes']                         = $this->decimoTercerRepository->decimo_tercer_mes($ObjetoColaborador);
  	$monto['vacaciones_proporcionales']          = $this->vacacionProporcionalRepository->vacacion_proporcional($ObjetoColaborador);
  	$monto['prima_productividad']                = $ingresos_regulares['prima_produccion'];
  	return $monto;

  }
  private function formando_info_colab($planilla, $config) {
   	$info_planilla = $planilla->toArray();

   	//Inicio:: Por cada Liquidacion existente es un colaborador correspondiente, por endede la cantidad de liquidaciones es la cantidad de colaboradores
  	$i = 0;
  	if(!empty($info_planilla['liquidaciones'])) {
  		foreach($info_planilla['liquidaciones'] as $info ){
     		$salarios_divididos = $this->divisiones_devengados($info);
     		$lista_pagos[$i] 	= array_merge($info, $salarios_divididos );
  			++$i;
  		}
  	}

    //Logica para sacar los calculos de todos los colaboradores que estan en la liquidacion
  	$info_config = $config->toArray();
  	$pago_cont  = 0;
  	if(!empty($info_config['pagos'])) {
  		foreach($info_config['pagos'] as $pago ){
     		for($m = 0; $m < $i; ++$m){ //Aqui estan guardadas la informacion de los colaboradores
	  				$lista_pagos[$m]['pagos'][$pago_cont]['nombre'] = $pago['tipo_pago']['etiqueta'];
 	  				$monto = $this->lista_pagos_liquidacion($pago['tipo_pago']['id_cat'],$lista_pagos[$m]);
	  				$lista_pagos[$m]['pagos'][$pago_cont]['monto'] = $monto;
	  				$pago['tipo_pago']['monto'] = $monto;
	  				if(!empty($pago['deducciones'])){
	  					$ded_cont = $des_cont = 0;
	  					$descuentos_directos = isset($lista_pagos[$m]['colaborador']['descuentos_directos'])?$lista_pagos[$m]['colaborador']['descuentos_directos']:array();
 	  					list($deduccion, $descuento) = $this->calculos_deducciones($pago, $descuentos_directos, $lista_pagos[$m]['colaborador']);

 	  					$lista_pagos[$m]['pagos'][$pago_cont]['deducciones'] 	= $deduccion;
 	  					$lista_pagos[$m]['pagos'][$pago_cont]['descuentos'] 	= $descuento;
 	  				}
  			}
  			++$pago_cont;
    	}
  	}


  	//$this->_agregando_prima_productividad($lista_pagos, $salarios_divididos );
     return $lista_pagos;
  }

  /*private function _agregando_prima_productividad($lista_pagos = array(), $salarios_divididos = array()) {
 dd($lista_pagos);
  }*/
  private function set_pagadas_colaborador($objetoPlanilla = array(), $data = array()) {
    	return  array(
  			'salario_bruto' => 0.00,
  			'planilla_id' => $objetoPlanilla->id,
  			'uuid_colaborador' => $data['colaborador']['uuid_colaborador'],
  			'colaborador_id' => $data['colaborador']['id'],
  			'rata' => 0,
  			'salario_neto' => 0,
  			'contrato_id' => $data['colaborador']['colaboradores_contratos'][0]['id']
  	);

  }

   private function set_pagadas_deducciones($data = array()) {
  	return  array(
  			'nombre' => $data['nombre'],
  			'descuento' =>  $data['monto'],
  			'saldo' => '0'
  	);
  }

  private function set_pagadas_ingresos($data = array()) {
   	return  array(
  			'detalle' => $data['nombre'],
  			'cantidad_horas' => 0,
  			'rata' => '0',
  			'calculo' => $data['monto']
  	);

  }
  private function set_pagadas_descuento($data = array()) {
   	return  array(
  			'codigo' => $data['codigo'],
  			'acreedor' => $data['acreedor'],
  			'monto_ciclo' => $data['monto'],
  			'descuento_id' => $data['descuento_id'],
  			'tipo_descuento_id' => $data['tipo_descuento_id']
  	);
   }

   function calculos_deducciones( $pago, $objectDescuento, $ObjetoColaborador) {
    		$contador = 0;
    	$matriz_deduccion = $descuentos = [];
	   	$deducido = 0;
 	   	foreach($pago['deducciones'] as $deduccion ){
 		   		if(!empty($deduccion['deduccion_info'])){

		   			$salario_bruto = $pago['tipo_pago']['monto'];

		   			if($deduccion['deduccion_info']['nombre'] != 'Descuento Directo'){ //Deduccion Normal
		   			//if(strpos($deduccion['deduccion_info']['nombre'],'Descuentos directo') === 0){ //Deduccion Normal
		   				if( $deduccion['deduccion_info']['rata_colaborador_tipo'] == "Porcentual" ){
		   					$rata = $deduccion['deduccion_info']['rata_colaborador']/100;
		   				}
		   				else if( $deduccion['deduccion_info']["rata_colaborador_tipo"] == "Monto" ){
		   					$rata =  $deduccion['deduccion_info']['rata_colaborador'];
		   				}

		   				if($deduccion['deduccion_info']['nombre'] != 'Impuesto Sobre la Renta') {
                                                        if( $deduccion['deduccion_info']['rata_colaborador_tipo'] == "Porcentual" ){
                                                           $deducido = $rata*$salario_bruto;
                                                        }else{
                                                            $deducido = $rata;
                                                        }
		   				}
		   				else{ //Es impuesto sobre la renta

		   					//$salario_promedio_anual = $salario_bruto*12;
		   					$salario_promedio_anual = $salario_bruto;

		   					$monto_excedente =  ($deduccion['deduccion_info']['limite2']-$deduccion['deduccion_info']['limite1'])*$rata;

		   					//Formula Para sacar Impuesto Sobre la Renta
		   					if($salario_promedio_anual > 0 && $salario_promedio_anual<= $deduccion['deduccion_info']['limite1']){
		   						$deducido = 0;
		   					}
		   					else if($salario_promedio_anual > $deduccion['deduccion_info']['limite1']+1  && $salario_promedio_anual<= $deduccion['deduccion_info']['limite2']){
		   						$excedente = $salario_promedio_anual - $deduccion['deduccion_info']['limite1'];
		   						$deducido = $excedente*$rata;
		   					}
		   					else if($salario_promedio_anual >  $deduccion['deduccion_info']['limite2']+1){
		   						$excedente = $salario_promedio_anual -  $deduccion['deduccion_info']['limite2']+1;
		   						$deducido = $monto_excedente + $excedente*0.25;
		   					}


		   				}

		   				$matriz_deduccion[$contador]['nombre']= $deduccion['deduccion_info']['nombre'] ;
		   				$matriz_deduccion[$contador]['monto'] = (float)$deducido;

		   			}else{
  		   				$descuentos = $this->lista_descuentos_by_colaborador($objectDescuento);
 		   			}
		   		}


		   	++$contador;
	   	}
     	return array($matriz_deduccion,$descuentos);
   	}


}

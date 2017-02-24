<?php
namespace Flexio\Modulo\Planilla\Repository;

use Flexio\Modulo\Planilla\Models\Planilla;
use Flexio\Modulo\Planilla\Models\Pagadas\PagadasColaborador;
use Flexio\Modulo\Planilla\Models\Pagadas\PagadasIngresos;
use Flexio\Modulo\Planilla\Models\Pagadas\PagadasDeducciones;
use Flexio\Modulo\Planilla\Models\Pagadas\PagadasAcumulados;
use Flexio\Modulo\Planilla\Models\Pagadas\PagadasDescuentos;
use Flexio\Modulo\Planilla\Models\Pagadas\PagadasCalculos;
use Flexio\Modulo\Colaboradores\Models\ColaboradoresContratos;
use Flexio\Modulo\Colaboradores\Models\BaseAcumulados;
use Flexio\Modulo\Planilla\Repository\PlanillaRepository;
use Flexio\Modulo\DescuentosDirectos\Models\DescuentosDirectos;
use Illuminate\Database\Capsule\Manager as Capsule;


class PagadasRepository{
protected $planillaRepository;

	function __construct() {
        $this->planillaRepository = new PlanillaRepository();
  }
	public function sacando_acumulados($info_general_planilla){


		 $acumulado_decimo = $acumulado_vacacion = $acumulado_prima = $acumulado_asistencia = 'no';
 			if(count($info_general_planilla->acumulados2)){
				foreach ($info_general_planilla->acumulados2 as  $value) {
 						if(preg_match('/XIII Mes proporcional/', $value->acumulado_info->nombre)){
							   $acumulado_decimo = 'si';
						}
						if(preg_match('/Vacaciones proporcional/', $value->acumulado_info->nombre)){
							  $acumulado_vacacion = 'si';
						}
						if(preg_match('/Prima de Antiguedad/',$value->acumulado_info->nombre)){
							   $acumulado_prima = 'si';
						}
						if(preg_match('/Asistencia/', $value->acumulado_info->nombre)){
							  $acumulado_asistencia = 'si';
						}
				}
			}

   			return [
				'vacacion_acumulado'   =>  $acumulado_vacacion,
				'decimo_tercermes'   =>  $acumulado_decimo,
				'prima_antiguedad'   =>  $acumulado_prima,
				'asistencia'   =>  $acumulado_asistencia
			];

	}
	public function cambiando_estado_pagada($planilla_id){

		$result = PagadasColaborador::where('planilla_id', $planilla_id)->update(['estado_pago' => 'pagado']);
		return $result;

	}



	public function crear($info_planilla, $info_general_planilla){

		  $error_rollback = [];
			$lista_acumulados = $this->sacando_acumulados($info_general_planilla);

		  $tipo_planilla = $info_general_planilla->tipo_id;
			if(count($info_planilla)) {

						try {

							foreach($info_planilla as $info )
							{
													$info['fecha_cierre_planilla'] = $info_general_planilla->rango_fecha2; //Se usa mientras en págadas_colaborador
													Capsule::beginTransaction();

																$pagadasColaborador = PagadasColaborador::create($this->collection_tabla_pagadas_colaborador($info, $lista_acumulados));

																$pagadasColaborador->ingresos()->saveMany($this->collection_tabla_ingresos($info, $tipo_planilla));
																$pagadasColaborador->deducciones()->saveMany($this->collection_tabla_deducciones($info));
																$pagadasColaborador->acumulados()->saveMany($this->collection_tabla_acumulados($info));
																$pagadasColaborador->descuentos()->saveMany($this->collection_tabla_descuentos($info));
																$pagadasColaborador->calculos()->saveMany($this->collection_tabla_calculos($info));


																if(!count($pagadasColaborador)){
																	$error_rollback[] = 1;
																}


							}
 							Planilla::where('id', $info_planilla[0]['planilla_id'])->update(['estado_id' => 14]);

							if(count($error_rollback)>0){
									Capsule::rollback();
							}

						}catch (\Exception $e) {
 								Capsule::rollback();
						}
					//	Capsule::commit();
 		 }

		 return $pagadasColaborador;
	}

	public function findBy($clause = array())
	{
			$pagadas = PagadasColaborador::dePlanilla($clause["planilla_id"]);
			$this->_filtros($pagadas, $clause);
			return $pagadas->first();
	}
	private function _filtros($pagadas, $clause)
	{
 			if(isset($clause["colaborador_id"]) and !empty($clause["colaborador_id"])){$pagadas->deColaborador($clause["colaborador_id"]);}
	}
 function find($id) {
    return PagadasColaborador::find($id);
  }

	private function guardandoBaseAcumulados($acumulado, $colaborador_id)
	{
  		$acumulado_base = BaseAcumulados::where('acumulado_id', $acumulado['id'])->where('colaborador_id',$colaborador_id)->first();
 			if(count($acumulado_base)){
   			 $acumulado_base->acumulado_planilla =$acumulado_base->acumulado_planilla + $acumulado['acumulado_planilla'];
				 $acumulado_base->save();
 			}
  	}

	private function guardandoVacaciones($salario_bruto, $contrato_id)
	{
 			$vacacion = ColaboradoresContratos::where('id', $contrato_id)->get()->first();
			$total_acumulado = $vacacion->vacacion_acumulado + $salario_bruto;
			ColaboradoresContratos::where('id', $contrato_id)->update(['vacacion_acumulado' => $total_acumulado]);
	}
	private function guardandoDecimo($salario_bruto, $contrato_id)
	{
 			$decimo_tercermes = ColaboradoresContratos::where('id', $contrato_id)->get()->first();
			$total_acumulado = $decimo_tercermes->decimo_tercermes+$salario_bruto;
			ColaboradoresContratos::where('id', $contrato_id)->update(['decimo_tercermes' => $total_acumulado]);
	}
	private function  collection_tabla_acumulados($info){

 				 $acumulado_fila = $acumulados = [];
				 if(count($info['pagos'])){
						 foreach ($info['pagos'] as $pago) {
							 if(isset($pago['acumulados']) && count($pago['acumulados'])){
 								 foreach ($pago['acumulados'] as $acumulado) {

 									 $acumulado_fila = array(
										 'acumulado_id'=>$acumulado['id'],
										 'nombre'=>$acumulado['nombre'],
										 'acumulado'=>$acumulado['acumulado'],
										 'acumulado_planilla'=>$acumulado['acumulado_planilla'],
										 'saldo'=>0,
  									 );
									 $acumulados[] 				= new PagadasAcumulados($acumulado_fila);


									 if(preg_match('/Vacaciones proporcional/', $acumulado['nombre'])){
										 if(isset($info['colaborador']['colaboradores_contratos'][0]['id'])){
											 $this->guardandoVacaciones($info['salario_devengado_no_pagado'], $info['colaborador']['colaboradores_contratos'][0]['id']);
										 }

									 }
									 if(preg_match('/XIII Mes proporcional/', $acumulado['nombre'])){
										 if(isset($info['colaborador']['colaboradores_contratos'][0]['id']))
									 	  	$this->guardandoDecimo($info['salario_devengado_no_pagado'], $info['colaborador']['colaboradores_contratos'][0]['id']);
									 }

									  if(count($acumulado) && !empty($info['colaborador_id']))
												$this->guardandoBaseAcumulados($acumulado, $info['colaborador_id']);

  									}
							 }
							}
								return  $acumulados;
				 }
  	}
 private function  collection_tabla_deducciones($info){

	 			$deducciones_fila =$deducciones =  [];
        if(count($info['pagos'])){
            foreach ($info['pagos'] as $pago) {


              if(count($pago['deducciones'])){
                foreach ($pago['deducciones'] as $deduccion) {

                  $deducciones_fila = array(
                    'deduccion_id'=>$deduccion['id'],
                    'nombre'=>$deduccion['nombre'],
                    'descuento'=>$deduccion['monto'],
                    'descuento_patronal'=>$deduccion['monto_patronal'],
                    'saldo'=>0,
                  );
									$deducciones[] 				= new PagadasDeducciones($deducciones_fila);
                 }
              }
             }
               return  $deducciones;
        }

 }

		private function  collection_tabla_calculos($info){


 			$ciclo_id = $info['colaborador']['ciclo_id'];
			$salario_devengado_no_pagado = $info['colaborador']['salario_devengado_no_pagado'];
			if($ciclo_id == 64){ //Quincenal
						$salario_promedio_anual =(float)$salario_devengado_no_pagado*2*13;
			}
			if($ciclo_id == 61){ //Bisemanal
							$salario_promedio_anual =(float)$salario_devengado_no_pagado*2*13;
			}
			if($ciclo_id == 62){ //mensual
						$salario_promedio_anual =(float)$salario_devengado_no_pagado*12;
			}
			if($ciclo_id == 63){ //semanal
						$salario_promedio_anual =(float)$salario_devengado_no_pagado*4*13;
			}

		$fecha_inicio_labores = ($info['colaborador']['colaboradores_contratos']?$info['colaborador']['colaboradores_contratos'][0]['fecha_ingreso']:'');

		$total_devengado = isset($info['colaborador']['total_devengado'])?$info['colaborador']['total_devengado']:0;
		$cantidad_ano = $this->planillaRepository->calculando_dias_laborados($fecha_inicio_labores);
		$cantidad_ano = ($cantidad_ano>0)?$cantidad_ano:0;
		//$anual_promedio = ($cantidad_ano>0)?$total_devengado/$cantidad_ano:0;

		$variables_indeminz = [
			"horas_semanales"=>$info['colaborador']['horas_semanales'],
			"rata_hora"=>$info['colaborador']['rata_hora'],
			"cantidad_ano"=>$cantidad_ano
		];

		$indemnizacion_proporcional = $this->planillaRepository->calculando_indemnizacion_proporcional($variables_indeminz);

  				$calculos_fila = array(
					'salario_mensual_promedio'=>$salario_promedio_anual/13,
					'salario_anual_promedio' => $salario_promedio_anual,
					'total_devengado'=>$total_devengado,
					'indemnizacion_proporcional'=>$indemnizacion_proporcional,
				);

				$calculos[] 				= new PagadasCalculos($calculos_fila);

				return $calculos;
		}
private function  collection_tabla_descuentos($info){
 		$descuentos = [];
       if(count($info['pagos'])){
				   	try {
  							foreach ($info['pagos'] as $pago) {

			             if(count($pago['descuentos'])){

 											if(isset($pago['descuentos']['monto_gran_total'])){
												unset($pago['descuentos']['monto_gran_total']);
											}
			               foreach ($pago['descuentos'] as $descuento) {
			                  $descuento_fila = array(
			                   'codigo'=>$descuento['codigo'],
			                   'acreedor'=>$descuento['acreedor'],
			                   'monto_ciclo'=>$descuento['monto_ciclo'],
			                   'saldo_restante'=>$descuento['monto_adeudado']-$descuento['monto_ciclo'],
			                   'descuento_id'=>$descuento['descuento_id'],
			                   'descuento'=>$descuento['monto_ciclo'],
			                   'tipo_descuento_id'=>$descuento['tipo_descuento_id'],
			                 );
											 $descuentos[] 				= new PagadasDescuentos($descuento_fila);

											 //Afectacion de descuentos al momento de cerrar la planilla
											 	$descuento_afectado = DescuentosDirectos::find($descuento['descuento_id']);

												$monto_adeudado_nuevo =$descuento_afectado->monto_adeudado-$descuento['monto_ciclo'];
												$descuento_afectado->monto_adeudado = $monto_adeudado_nuevo;
							          $descuento_afectado->save();
			               }
			             }
			            }
						}
						catch(ValidationException $e){
 			     		// Rollback
			     		Capsule::rollback();
 			     		exit;
			     	}
						Capsule::commit();
            return  $descuentos;
       }
}

 private function  collection_tabla_pagadas_colaborador($info, $acumulados = []){

      return  array(
      'salario_bruto' =>$info['colaborador']['salario_devengado_no_pagado'],
      'planilla_id' =>$info['planilla_id'],
      'uuid_colaborador' =>hex2bin($info['colaborador']['uuid_colaborador']),
      'colaborador_id' =>$info['colaborador']['id'],
      'salario_neto' => $info['colaborador']['salario_neto'],
      'fecha_pago' =>'0000-00-00',
      'contrato_id' =>isset($info['colaborador']['colaboradores_contratos'][0]['id'])?$info['colaborador']['colaboradores_contratos'][0]['id']:0,
      'fecha_cierre_planilla' => $info['fecha_cierre_planilla'],
      'estado_pago'   =>  'no_pagado',
      'vacacion_acumulado'   =>  $acumulados['vacacion_acumulado'],
      'decimo_tercermes'   =>  $acumulados['decimo_tercermes'],
      'prima_antiguedad'   =>  $acumulados['prima_antiguedad'],
      'asistencia'   =>  $acumulados['asistencia']
  );
 }

private function  collection_tabla_ingresos($info, $tipo_planilla){
			$ingresos = [];
			if($tipo_planilla == 79){
				if($info['colaborador']['tipo_salario'] == 'Hora'){
         foreach($info['ingreso_horas'] as $valor){
					 $ingresos_separados = $this->planillaRepository->formula_calculo($valor, $info['colaborador']['rata_hora']);
					 if(count($ingresos_separados)>0){
						 foreach ($ingresos_separados as $key => $ingreso) {
 						 			$ingresos[] 				= new PagadasIngresos($ingreso);
 						}
					 }
           }

					 		 $buscando_gasto_representacion = collect($info['pagos'])->filter(function($item) {
					 		 	return $item['nombre'] == 'Gasto de representacion';
					 		 })->first();
					 		 if(!empty($buscando_gasto_representacion)){
					 		  $ingreso =
					 					 		 [
					 					 			 "detalle" => "Gasto de representacion",
					 					 				"calculo" => $buscando_gasto_representacion['monto'],
					 					 				"recargo_monto" => $buscando_gasto_representacion['monto'],
					 					 				"recargo_cuenta_id" => $info['colaborador']['cuenta_gasto_representacion_id'], // TODO PROBAR DATOS DE CAMPO DEBITO DE GASTO DE REPRESENTACION
					 					 				//"fecha_transaccion" => "2017-01-07"
					 					 		 ];

					 					 		 $ingresos[] 				= new PagadasIngresos($ingreso);
					 			 }
         }else{
           foreach($info['pagos'] as $pago){
                  $ingresos_fila = array(
                   "detalle" => $pago['nombre'],
                   "cantidad_horas" => 0,
                   "rata" =>0,
                   "calculo" =>$pago['monto']
               );


 							$ingresos[] 				= new PagadasIngresos($ingresos_fila);

             }

       }


			}
			else{
				foreach($info['pagos'] as $pago){
							 $ingresos_fila = array(
								"detalle" => 'Decimo tercer mes',
								"cantidad_horas" => 0,
								"rata" =>0,
								"calculo" =>$pago['monto']
						);
					 $ingresos[] 				= new PagadasIngresos($ingresos_fila);

					}
			}
       return  $ingresos;
}



 private function set_pagadas_colaborador($objetoPlanilla = array(), $data = array()) {

  	return  array(
  			'salario_bruto' => 0.00,
  			'planilla_id' => $objetoPlanilla->id,
  			'uuid_colaborador' => '',
  			'colaborador_id' => $data['colaborador']['id'],
  			'rata' => 0,
  			'salario_neto' => 0
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

}

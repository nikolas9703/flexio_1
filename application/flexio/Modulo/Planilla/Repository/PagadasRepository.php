<?php
namespace Flexio\Modulo\Planilla\Repository;

use Flexio\Modulo\Planilla\Models\Planilla;
use Flexio\Modulo\Planilla\Models\Pagadas\PagadasColaborador;
use Flexio\Modulo\Planilla\Models\Pagadas\PagadasIngresos;
use Flexio\Modulo\Planilla\Models\Pagadas\PagadasDeducciones;
use Flexio\Modulo\Planilla\Models\Pagadas\PagadasAcumulados;
use Flexio\Modulo\Planilla\Models\Pagadas\PagadasDescuentos;
use Flexio\Modulo\Planilla\Models\Pagadas\PagadasCalculos;
use Flexio\Modulo\Planilla\Repository\PlanillaRepository;
use Flexio\Modulo\DescuentosDirectos\Models\DescuentosDirectos;
use Illuminate\Database\Capsule\Manager as Capsule;


class PagadasRepository{
protected $planillaRepository;

	function __construct() {
        $this->planillaRepository = new PlanillaRepository();
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
	private function  collection_tabla_acumulados($info){

 				 $acumulado_fila = [];
				 if(count($info['pagos'])){
						 foreach ($info['pagos'] as $pago) {
							 if(count($pago['acumulados'])){

								 foreach ($pago['acumulados'] as $acumulado) {
 									 $acumulado_fila = array(
										 'acumulado_id'=>$acumulado['id'],
										 'nombre'=>$acumulado['nombre'],
										 'acumulado'=>$acumulado['acumulado'],
										 'acumulado_planilla'=>$acumulado['acumulado_planilla'],
										 'saldo'=>0,
  									 );
									 $acumulados[] 				= new PagadasAcumulados($acumulado_fila);
									}
							 }
							}
								return  $acumulados;
				 }
	}
 private function  collection_tabla_deducciones($info){

	 			$deducciones_fila = [];
        if(count($info['pagos'])){
            foreach ($info['pagos'] as $pago) {
              if(count($pago['deducciones'])){
                foreach ($pago['deducciones'] as $deduccion) {

                  $deducciones_fila = array(
                    'deduccion_id'=>$deduccion['id'],
                    'nombre'=>$deduccion['nombre'],
                    'descuento'=>$deduccion['monto'],
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
			                   'saldo_restante'=>$descuento['monto_adeudado'],
			                   'descuento_id'=>$descuento['descuento_id'],
			                   'descuento'=>$descuento['monto_ciclo'],
			                   'tipo_descuento_id'=>$descuento['tipo_descuento_id'],
			                 );
											 $descuentos[] 				= new PagadasDescuentos($descuento_fila);

											 //Afectacion de descuentos al momento de cerrar la planilla
											 	$descuento_afectado = DescuentosDirectos::find($descuento['descuento_id']);
												$monto_adeudado_nuevo =$descuento_afectado->inicial-$descuento['monto_ciclo'];
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

 private function  collection_tabla_pagadas_colaborador($info){
     return  array(
      'salario_bruto' =>$info['colaborador']['salario_devengado_no_pagado'],
      'planilla_id' =>$info['planilla_id'],
      'uuid_colaborador' =>hex2bin($info['colaborador']['uuid_colaborador']),
      'colaborador_id' =>$info['colaborador']['id'],
      //'rata' =>$info['colaborador']['rata_hora'],
      'salario_neto' => $info['colaborador']['salario_neto'],
      'fecha_inicial' =>'0000-00-00',
      'fecha_final' =>'0000-00-00',
      'fecha_pago' =>'0000-00-00',
      'contrato_id' =>isset($info['colaborador']['colaboradores_contratos'][0]['id'])?$info['colaborador']['colaboradores_contratos'][0]['id']:0,
      'fecha_cierre_planilla' => date("Y-m-d"),
      'estado_pago'   =>  'no_pagado'
  );
 }

private function  collection_tabla_ingresos($info){

       if($info['colaborador']['tipo_salario'] == 'Hora'){
        foreach($info['ingreso_horas'] as $valor){
          //$ingresos[] = $this->planillaRepository->formula_calculo($valor, $info['colaborador']['rata_hora']);
					$ingresos[] 				= new PagadasIngresos($this->planillaRepository->formula_calculo($valor, $info['colaborador']['rata_hora']));
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
      return  $ingresos;
}
   public function collection_transacciones($pagadasColaborador){
		$informacion =[];


		dd($pagadasColaborador->toArray());
		/*$i = 0;
		 foreach ($calculos_globales as  $colaborador) {

 					$debitos[$i] 	= $this->collection_debitos($colaborador);
 					$creditos[$i] = $this->collection_creditos($colaborador);
					++$i;
		 }
		 $informacion =
		 [
			 	 "debitos" 				=> $debitos,
				 "creditos" 			=> $creditos,
				 "id" 						=>$planilla->id,
				 "empresa_id" 		=>$planilla->empresa_id,
				 "codigo" 				=>$planilla->codigo,
				 'linkable_id'		=>$planilla->id,
				 'linkable_type'	=> get_class($planilla)
	 	];*/
		return $informacion;
	}
  public function crear($info_planilla){

      if(count($info_planilla)) {
        foreach($info_planilla as $info )
        {
              $pagadasColaborador = PagadasColaborador::create($this->collection_tabla_pagadas_colaborador($info));
    					$pagadasColaborador->ingresos()->saveMany($this->collection_tabla_ingresos($info));
					 	  $pagadasColaborador->deducciones()->saveMany($this->collection_tabla_deducciones($info));
							$pagadasColaborador->acumulados()->saveMany($this->collection_tabla_acumulados($info));
							$pagadasColaborador->descuentos()->saveMany($this->collection_tabla_descuentos($info));
							$pagadasColaborador->calculos()->saveMany($this->collection_tabla_calculos($info));
         }
  					Planilla::where('id', $info_planilla[0]['planilla_id'])->update(['estado_id' => 14]);
     }

		 return $pagadasColaborador;
  }

  /*public function findBy($clause)
  {
      $PagadaColaborador = PagadasColaborador::where(function($query) use ($clause){

          $this->_filtros($query, $clause);

      });

      return $PagadaColaborador->first();
  }*/

  /*private function _filtros($query, $clause)
  {
      if(isset($clause['planilla_id']) and !empty($clause['planilla_id'])){$query->wherePlanillaId($clause['planilla_id']);}
      if(isset($clause['colaborador_id']) and !empty($clause['colaborador_id'])){$query->whereColaboradorId($clause['colaborador_id']);}
  }*/

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

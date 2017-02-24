<?php
namespace Flexio\Modulo\Planilla\Repository;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Planilla\Models\Planilla;
use Flexio\Modulo\Comentario\Models\Comentario;

class VacacionRepository{

    function reporte_colaborador($objetoPlanilla) {
          $valores_salarios = $this->formando_info_colab($objetoPlanilla);
         	return $valores_salarios;
    }
     private function formando_info_colab($info_planilla) {


       $planilla_id = $info_planilla->id;
       $deducciones = $info_planilla->deducciones2->toArray();
       $acumulados = $info_planilla->acumulados2->toArray();

      	$i = 0;

      	if(count($info_planilla->vacaciones2)) {
      		foreach($info_planilla->vacaciones2 as $info ){

            $descuentos_directos = [];

            if(count($info->colaborador->descuentos_directos)){
                $descuentos_directos = $info->colaborador->descuentos_directos;
             }

             $salarios_divididos = $this->divisiones_devengados($info->colaborador->colaboradores_contratos[0]->salarios_devengados_contrato_vacaciones, $info_planilla);


             $lista_pagos[$i]  	= array_merge($info->colaborador->toArray(), $salarios_divididos);




             $lista_pagos[$i]['colaborador']['descuentos_directos'] 	= $descuentos_directos;

       			++$i;
      		}
      	}


       		for($m = 0; $m < $i; ++$m){ //Aqui estan guardadas la informacion de los colaboradores

    	  				$lista_pagos[$m]['pagos'][0]['nombre'] = 'Vacaciones Acumuladas';
    	  				$lista_pagos[$m]['pagos'][0]['monto'] =  $lista_pagos[$m]['salario_devengado_no_pagado'];
                $lista_pagos[$m]['colaborador']['salario_devengado_no_pagado'] = $lista_pagos[$m]['salario_devengado_no_pagado'];
                $lista_pagos[$m]['planilla_id'] =$planilla_id;
   	  					$descuentos_directos = $lista_pagos[$m]['colaborador']['descuentos_directos'];


    	  				list($deduccion, $descuento, $deducido_gran_total,  $descuento_gran_total) = $this->calculos_deducciones($deducciones, $descuentos_directos, $lista_pagos[$m]['colaborador'], $info_planilla);
   	  					$lista_pagos[$m]['pagos'][0]['deducciones']  = $deduccion;
   	  					$lista_pagos[$m]['pagos'][0]['descuentos'] 	 = $descuento;
                $lista_pagos[$m]['colaborador']['salario_neto'] = $lista_pagos[$m]['salario_devengado_no_pagado'] - ($deducido_gran_total + $descuento_gran_total);
      			}
           return $lista_pagos;
      }

      private function divisiones_devengados($ObjetoPagados, $ObjetoPlanilla) {





         $monto['salario_devengado_no_pagado']        = 0;
         $monto['prima_produccion']        = 0;
          if(count($ObjetoPagados)){
            $monto['salario_devengado_no_pagado']        = $ObjetoPagados->avg('salario_bruto');
           }


           return $monto;
      }


      //Funcon solo para exportar a excel
      function coleccion_datos_csv($objetoReporte) {

        $csvdata = [];
        if(count($objetoReporte)){
          $i=0;
           foreach ($objetoReporte as  $value) {

              $deducciones_divididos = $this->dividiendo_deducciones($value['pagos'][0]['deducciones'], $value['salario_devengado_no_pagado']);
              $descuentos = $this->sumatoria_descuentos($value['pagos'][0]['descuentos']);
              $csvdata[$i]['centro_contable'] = $value['centro_contable']['nombre'];
              $csvdata[$i]['posicion'] = $value['cargo']['nombre'];
              $csvdata[$i]['nombre'] = $value['nombre_completo'];
              $csvdata[$i]["cedula"] = $value['cedula'];
              $csvdata[$i]["rata_hora"] =  $value['rata_hora'];
              $csvdata[$i]["hr"] = 0;
              $csvdata[$i]["he"] = 0;
              $csvdata[$i]["isr"] = number_format($deducciones_divididos['isr'],2);
              $csvdata[$i]["se"] = number_format($deducciones_divididos['se'],2);
              $csvdata[$i]["ss"] = number_format($deducciones_divididos['ss'],2);
              $csvdata[$i]["otros"] = number_format($deducciones_divididos['otros'],2);
              $csvdata[$i]["deducciones"] = number_format($deducciones_divididos['totales'],2);
              $csvdata[$i]["descuento"] =  $descuentos;
              $csvdata[$i]["salario_bruto"] = number_format($value['salario_devengado_no_pagado'],2);
              $csvdata[$i]["salario_neto"] = number_format($value['salario_devengado_no_pagado']-$deducciones_divididos['totales'] - $descuentos,2);
              ++$i;
           }
        }
          return $csvdata;
      }

      private function sumatoria_descuentos($descuentos) {




        $sumatoria = 0;
      if(!empty($descuentos))
        foreach ($descuentos as $value) {
           $sumatoria += $value['monto'];
        }
          return $sumatoria;
      }

      private function dividiendo_deducciones($deducciones, $rata) {

        $deducciones_divididos['ss'] = $deducciones_divididos['isr'] = $deducciones_divididos['se']  = $deducciones_divididos['otros'] = $deducciones_divididos['totales'] = 0;
        if(!empty($deducciones)){
          foreach ($deducciones as $value) {

            if($value['key'] == 1 || preg_match("/seguro social/i", $value['nombre'] )){
                $deducciones_divididos['ss'] += $value['monto'];
            }
            else if($value['key'] == 2 || preg_match("/sobre la renta/i", $value['nombre'] )){ //isr
                $deducciones_divididos['isr'] += $value['monto'];
            }
            else if( preg_match("/seguro educativo/i", $value['nombre'] )){ //isr
                $deducciones_divididos['se'] += $value['monto'];
            }
            else{
                $deducciones_divididos['otros'] += $value['monto'];
            }
                $deducciones_divididos['totales'] += $value['monto'];
          }
        }

        return $deducciones_divididos;
      }

      private function dividiendo_salario($reporte, $rata) {
        $sumatoria = $sumatoria_no_regular =  0;
        if(!empty($reporte)){
          foreach ($reporte as   $value) {
             //$resultado = $this->formula_calculo($value, $rata);
              $ingresos_separados = $this->planillaRepository->formula_calculo($value, $rata);
          					 if(count($ingresos_separados)>0){
          						 foreach ($ingresos_separados as $key => $ingreso) {
           						 			//$ingresos[] 				= new PagadasIngresos($ingreso);
                            if(!preg_match("/HR/i", $ingreso['detalle'])){
                               $sumatoria +=$ingreso['calculo'];
                            }else{
                               $sumatoria_no_regular +=$ingreso['calculo'];
                            }
           						}
          					 }


           }
        }
        $totales = [];
        $totales = array(
          "regular" => $sumatoria,
          "no_regular" => $sumatoria_no_regular
        );
         return $totales;
      }

      function coleccion_tablas($objetoReporte) {

        $tabla['ingresos'] 		= $this->haciendo_tabla_ingresos($objetoReporte);
        $tabla['deducciones'] 	= $this->haciendo_tabla_deducciones($objetoReporte);
        $tabla['descuentos'] 	= $this->haciendo_tabla_descuentos($objetoReporte);
        $tabla['calculos'] 		= [];
        //$tabla['calculos'] 		= $this->haciendo_tabla_calculos($objetoReporte);
        $tabla['acumulados'] 		=[];
      //  $tabla['acumulados'] 		= $this->haciendo_tabla_acumulados($objetoReporte);
        return $tabla;
      }

      function calculos_acumulados( $lista_acumulados, $ObjetoColaborador, $planilla) {

          $calculo = $calculo_acumulado_planilla = 0;
          $calculo_salario_bruto = $ObjetoColaborador['salario_devengado_no_pagado'];
          $resultado_acumulados = array();

             if(!empty($lista_acumulados)){
              foreach($lista_acumulados as $acumulado){

                if(isset($acumulado['acumulado_info']['formula'])){
                    $formula_acumulado = $acumulado['acumulado_info']['formula'];
                    $operador = $formula_acumulado['operador_valor'];
                    $variable_operador = 0;


                    switch ($operador) {
                     case 'total_devengado':
                        $variable_operador = $ObjetoColaborador['total_devengado'];
                        break;

                     default:
                        $variable_operador = $ObjetoColaborador['total_devengado'];
                       break;
                   }
                    if($formula_acumulado['tipo_calculo_uno'] != ''){
                     if($formula_acumulado['tipo_calculo_uno'] == 'Multiplicado por' ){
                        $calculo = $variable_operador*$formula_acumulado['valor_calculo_uno'];
                        $calculo_acumulado_planilla = $calculo_salario_bruto*$formula_acumulado['valor_calculo_uno'];

                     }
                     elseif($formula_acumulado['tipo_calculo_uno'] == 'Dividido por' ){
                        $calculo = $variable_operador/$formula_acumulado['valor_calculo_uno'];
                        $calculo_acumulado_planilla = $calculo_salario_bruto/$formula_acumulado['valor_calculo_uno'];
                     }
                   }

                   if($formula_acumulado['tipo_calculo_dos'] != ''){
                     if($formula_acumulado['tipo_calculo_dos'] == 'Multiplicado por' ){
                          $calculo = $calculo*$formula_acumulado['valor_calculo_dos'];
                          $calculo_acumulado_planilla = $calculo_salario_bruto*$formula_acumulado['valor_calculo_dos'];
                     }
                     elseif($formula_acumulado['tipo_calculo_dos'] == 'Dividido por' ){
                          $calculo = $calculo/$formula_acumulado['valor_calculo_dos'];
                          $calculo_acumulado_planilla = $calculo_salario_bruto/$formula_acumulado['valor_calculo_dos'];
                     }
                   }

                }else{
                    $calculo = $calculo_acumulado_planilla= 0;
                }

                $resultado_acumulados[] = array(
                    "id" 		=>$acumulado['acumulado_info']['id'],
                    "nombre" 	=>$acumulado['acumulado_info']['nombre'],
                    "acumulado" =>$calculo,
                    "acumulado_planilla" =>$calculo_acumulado_planilla,
                );

              }
            }

        return $resultado_acumulados;
      }
      function calculos_deducciones( $deducciones,  $objectDescuento, $ObjetoColaborador, $planilla) {



        	$contador = $deducido = $deducido_gran_total = 0;
       	  $matriz_deduccion = $descuentos = [];

    	   	foreach($deducciones as  $deduccion ){

     		   		if(!empty($deduccion['deduccion_info'])){
   		   			       $salario_bruto = $ObjetoColaborador['salario_devengado_no_pagado'];

   		   			  if(!preg_match("/descuento/i", $deduccion['deduccion_info']['nombre']))
                {
 		   				            if( $deduccion['deduccion_info']['rata_colaborador_tipo'] == "Porcentual" ){
   		   					               $rata = $deduccion['deduccion_info']['rata_colaborador']/100;
   		   				          }
   		   				          else if( $deduccion['deduccion_info']["rata_colaborador_tipo"] == "Monto" ){
   		   					               $rata =  $deduccion['deduccion_info']['rata_colaborador'];
   		   				          }

                          if(!preg_match("/Sobre la Renta/i", $deduccion['deduccion_info']['nombre'])){

                                     if( $deduccion['deduccion_info']['rata_colaborador_tipo'] == "Porcentual" ){
                                            $deducido = $rata*$salario_bruto;
                                    }else{
                                             $deducido = $rata;
                                    }
             		   				}
     		   				        else{ //Es impuesto sobre la renta

                            $limite1 = $deduccion['deduccion_info']['limite1'];
                            $limite2 = $deduccion['deduccion_info']['limite2'];

                            if($ObjetoColaborador['deduccion_tipo_declarante_id'] == 34) //Conjunta
                            {

                              $limite1 = 11800;
                              $limite2 = 50800;
                            }


                             $salario_promedio_anual =(float)$salario_bruto*12;

      		   					      $monto_excedente =  ($limite2-$limite1)*$rata;

             		   					if($salario_promedio_anual > 0 && $salario_promedio_anual<= $limite1){
             		   						$deducido = 0;
             		   					}
             		   					else if($salario_promedio_anual > $limite1+1  && $salario_promedio_anual<= $limite2){
             		   						$excedente = $salario_promedio_anual - $limite1;
             		   						$deducido = $excedente*$rata;
             		   					}
             		   					else if($salario_promedio_anual >  $limite2+1){

             		   						$excedente = $salario_promedio_anual -  $limite2;
             		   						$deducido = $monto_excedente + $excedente*0.25;

             		   					}
                            $deducido =(float)$deducido/12;

     		   				      }
                      //Deducido Total Se usa para sacar el salario_neto
                      $deducido_gran_total +=$deducido;
   		   				      $matriz_deduccion[$contador]['id']= $deduccion['deduccion_info']['id'] ;
   		   				      $matriz_deduccion[$contador]['key']= $deduccion['deduccion_info']['key'] ;
   		   				      $matriz_deduccion[$contador]['nombre']= $deduccion['deduccion_info']['nombre'] ;
   		   				      $matriz_deduccion[$contador]['monto'] = (float)$deducido;



   		   			}
              else{
                  $descuentos = $this->lista_descuentos_by_colaborador($objectDescuento);
              }
   		   		}
   		   	++$contador;
   	   	}

          $monto_g_total = isset($descuentos['monto_gran_total'])?$descuentos['monto_gran_total']:0;
         	return array($matriz_deduccion,$descuentos, $deducido_gran_total,$monto_g_total );
      	}

      private function lista_descuentos_by_colaborador($ObjectDescuento) {

        	$m = $monto_ciclo_total = 0;
         	$lista_pagos = [];
        	if(!empty($ObjectDescuento)){
        		foreach($ObjectDescuento  as $descuento){
          		$lista_pagos[$m]['nombre'] 	= "Descuento 1";
        			$lista_pagos[$m]['monto'] 	= $descuento['monto_ciclo'];
        			$lista_pagos[$m]['codigo'] 	= $descuento['codigo'];
        			$lista_pagos[$m]['acreedor']= isset($descuento['acreedor']['nombre'])?$descuento['acreedor']['nombre']:'Sin Acreedor';
        			$lista_pagos[$m]['descuento_id']= $descuento['id'];
        			$lista_pagos[$m]['tipo_descuento_id']=  $descuento['tipo_descuento_id'];
        			$lista_pagos[$m]['monto_adeudado']=  $descuento['monto_adeudado'];
        			$lista_pagos[$m]['monto_ciclo']=  $descuento['monto_ciclo'];
              $monto_ciclo_total+=$descuento['monto_ciclo'];
        			++$m;
        		}
            $lista_pagos['monto_gran_total'] 	= $monto_ciclo_total;
        	}
         	return $lista_pagos;
        }
        //FORMULA UNICA PARA CALCULAR LOS DATA ENTRY ES EL UNICO LUGAR DONDE ESTA y donde debe estar :)
        public function formula_calculo($info= array(), $rata_hora=0){

                  $rata =  $info['recargo']['porcentaje_hora']*$rata_hora;
                  $porcentaje_beneficio = $beneficio_monto = 0;
                  $nombre_beneficio = '';
                  $calculo = $info['cantidad_horas']*$rata;
                  $calculo_inicial = $calculo;
                  if(isset($info['beneficio'])){
                        $porcentaje_beneficio = ($info['beneficio']['modificador_actual']/100)+1.00;
                        $nombre_beneficio = "(".$info['beneficio']['nombre'].')';
                        $rata = $rata*$porcentaje_beneficio;
                        $calculo = $calculo*$porcentaje_beneficio;
                        $beneficio_monto = $calculo - $calculo_inicial;
                  }
                  $ingresos  = array(
                        "detalle" => $info['recargo']['nombre'].$nombre_beneficio,
                        "cantidad_horas" => $info['cantidad_horas'],
                        "rata" => $rata,
                        "calculo" =>$calculo,
                        "beneficio_cuenta_id" =>$info['cuenta_gasto_id'],
                        "beneficio_id" => $info['beneficio_id'],
                        "recargo_cuenta_id" => $info['cuenta_costo_id'],
                        "recargo_id" =>$info['recargo_id'],
                        "recargo_monto" =>$calculo_inicial,
                        "beneficio_monto" => $beneficio_monto
                  );
                  return $ingresos;
            }


        private function haciendo_tabla_acumulados($objetoReporte) {

          $acumulados = [];
          if(!empty($objetoReporte))
          {
            foreach($objetoReporte as $pago){
              foreach($pago['pagos'] as $info){
                foreach($info['acumulados'] as $acumul){
                     $acumulados[] = array(
                      "nombre" => $acumul['nombre'],
                      "acumulado" =>$acumul['acumulado']
                    );
                   }
               }
           }
            return $acumulados;
          }
        }
        private function haciendo_tabla_ingresos($objetoReporte) {

           $ingresos = [];
           if(!empty($objetoReporte))
           {
             foreach($objetoReporte as $pago){
                  $ingresos[] = array(
                     "detalle" => "Vacaciones",
                     "cantidad_horas" => 0,
                     "rata" =>0,
                     "calculo" =>$pago['salario_devengado_no_pagado']/11
                 );
             }
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
                       "tipo" => $pago['nombre'],
                       "saldo" => 0.00
                   );
                 }
               }

             }
           }
           return $lista;
         }

         private function haciendo_tabla_descuentos($objetoReporte) {
             $lista = [];

             if(!empty($objetoReporte[0]['pagos']))
             {
               foreach($objetoReporte[0]['pagos'] as $pago){

                 if(!empty($pago['descuentos'])){
                    if(isset($pago['descuentos']['monto_gran_total']))
                      unset($pago['descuentos']['monto_gran_total']);
                   foreach ($pago['descuentos'] as $descuento){
                       $lista[] = array(
                         "codigo" => $descuento['codigo'],
                         "acreedor" => isset($descuento['acreedor'])?$descuento['acreedor']:'',
                         "monto" => $descuento['monto'],
                         "descuento" => $descuento['monto'],
                         "tipo" => $descuento['nombre'],
                         "monto_adeudado" =>$descuento['monto_adeudado'],
                         "monto_ciclo" =>$descuento['monto_ciclo'],
                         "saldo_restante" =>$descuento['monto_adeudado']-$descuento['monto_ciclo']
                     );
                   }
                 }
               }
             }
             return $lista;
           }

          /* private function haciendo_tabla_calculos($objetoReporte) {

                $ciclo_id= $objetoReporte[0]['colaborador']['ciclo_id'];
               $salario_devengado_no_pagado = $objetoReporte[0]['colaborador']['salario_devengado_no_pagado'];
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

          		 $fecha_ingreso = isset($objetoReporte[0]['colaborador']['colaboradores_contratos'][0]['fecha_ingreso'])?$objetoReporte[0]['colaborador']['colaboradores_contratos'][0]['fecha_ingreso']:'El usuario no tiene contrato';
           		 $cantidad_ano = $this->calculando_dias_laborados($fecha_ingreso);
           		  $lista = [];
         		  	$total_devengado = $objetoReporte[0]['colaborador']['total_devengado'];
          		  //$anual_promedio = ($cantidad_ano>0)?$total_devengado/$cantidad_ano:0;
         		  	$lista['salario_mensual_promedio'] = array(
         		  			"detalle" => 'Salario mensual promedio',
         		   			"monto" => $salario_promedio_anual/13,
         		   	);
         		  	$lista['salario_anual_promedio'] = array(
         		  			"detalle" => 'Salario anual promedio',
         		  			"monto" => $salario_promedio_anual
         		  	);
         		  	$lista['total_devengado'] = array(
         		  			"detalle" => 'Total Devengado',
         		  			"monto" => $total_devengado
         		  	);

                $variables_indeminz = [
                  "horas_semanales"=>$objetoReporte[0]['colaborador']['horas_semanales'],
                  "rata_hora"=>$objetoReporte[0]['colaborador']['rata_hora'],
                  "cantidad_ano"=>$cantidad_ano
                ];
                $lista['indemnizacion_proporcional'] = array(
         		  			"detalle" => 'Indemnizacion proporcional',
         		  			"monto" => $this->calculando_indemnizacion_proporcional($variables_indeminz)
         		  	);
         		  	return $lista;
           }*/

           public function calculando_dias_laborados($fecha_ingreso) {
         	  	$startTimeStamp = strtotime($fecha_ingreso);
         	  	$endTimeStamp = strtotime(date("Y-m-d"));
         	  	$timeDiff = abs($endTimeStamp - $startTimeStamp);
         	  	$numberDays = $timeDiff/86400;  // 86400 seconds in one day
         	  	$numberDays = intval($numberDays);
         	  	$cantidad_ano = $numberDays/365;

         	  	return $cantidad_ano;
           }
           public function calculando_indemnizacion_proporcional($variables){

             $horas_semanales = isset($variables['horas_semanales'])?$variables['horas_semanales']:0;
             $rata_hora = isset($variables['rata_hora'])?$variables['rata_hora']:0;
             $cantidad_ano = isset($variables['cantidad_ano'])?$variables['cantidad_ano']:0;


             $total = 0;
             if($cantidad_ano > 10){
                $total = 3.4*$horas_semanales*$rata_hora*$cantidad_ano; //3.4 Semanas X Años Laborados
             }else{
                $total =  $horas_semanales*$rata_hora*$cantidad_ano;
             }
              return $total;
           }

          /* private function ingresos_regulares_no_liquidacion(   $ObjetoColaborador, $ObjetoPlanilla ) {
             if($ObjetoColaborador->tipo_salario == 'Mensual'){
               $salario_bruto_mensual = $ObjetoColaborador->salario_mensual;
             }else{
               $salario_bruto_mensual = 800;
               //$salario_bruto_mensual = $ObjetoColaborador->salario_promedio_mensual;
             }
                $retun_salario = array(
                  "salario_bruto_mensual" => $salario_bruto_mensual,
                  "salario_bruto" => $salario_bruto_mensual,
                  "prima_produccion" => 0
              );
               return $retun_salario;
          }*/

          private function salarios_horas_rata_no_liquidacion( $ObjetoColaborador ) {

              $salarios = [];
              $acumulado_salario_bruto =  $suma_horas_prima = 0;
               $rata = $ObjetoColaborador['colaborador']['rata_hora'];
              if( !empty($ObjetoColaborador) ){
                foreach ($ObjetoColaborador['ingreso_horas'] as   $value) {

                       //$resultado = $this->formula_calculo($value, $rata);

                       $ingresos_separados = $this->planillaRepository->formula_calculo($value, $rata);
                    					 if(count($ingresos_separados)>0){
                    						 foreach ($ingresos_separados as $key => $ingreso) {
                                   if(preg_match("/prima/i", $ingreso['detalle'])){
                                       $suma_horas_prima +=$ingreso['calculo'];
                                   }else{
                                      $acumulado_salario_bruto +=$ingreso['calculo'];
                                   }
                     						}
                    					 }



                }
              }

              $salarios =  $this->_calculo_prima_productividad($acumulado_salario_bruto, $suma_horas_prima );

              return $salarios;
          }

          private function _calculo_prima_productividad($acumulado_salario_bruto, $suma_horas_prima ) {


              $nuevo_salario_bruto = 0;
              $mitad_salario_bruto = $acumulado_salario_bruto*0.50;

              if($suma_horas_prima > $mitad_salario_bruto){ //Debe Conciderarse como Salario Base
                  $excendente = $suma_horas_prima - $mitad_salario_bruto;
                  $nuevo_salario_bruto = $excendente+$acumulado_salario_bruto;
                  $prima_produccion = $mitad_salario_bruto;
              }else{
                  $prima_produccion = $suma_horas_prima;
                  $nuevo_salario_bruto = $acumulado_salario_bruto;
              }

             return array(
                 "salario_bruto"=>$nuevo_salario_bruto,
                 "prima_produccion" => $prima_produccion
             );
          }
  }

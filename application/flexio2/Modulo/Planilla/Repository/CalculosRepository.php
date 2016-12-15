<?php
namespace Flexio\Modulo\Planilla\Repository;

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Planilla\Models\Planilla;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\Planilla\Repository\Regular\PlanillaRegularRepository;



class CalculosRepository{

  protected $planillaRegularRepository;

  function __construct() {

      $this->planillaRegularRepository      = new  PlanillaRegularRepository();
  }


      function collecion_cerrar_planilla_regular($objetoReporte) {

            $salario_bruto = $descuento_total = 0;
            if(count($objetoReporte)){
              foreach ($objetoReporte as   $value) {
                          $salario_bruto += $value['salario_devengado_no_pagado'];

                          if(!empty($value['pagos'])){
                            foreach ($value['pagos'] as   $pago) {
                                if(count($pago['deducciones'])>0){
                                  foreach ($pago['deducciones'] as   $deduccion) {
                                        $descuento_total += $deduccion['monto'];
                                  }
                                }
                                  if(count($pago['descuentos'])>0){
                                   foreach ($pago['descuentos'] as   $descuento) {

                                        $descuento_total += $descuento['monto'];
                                  }
                                }
                            }
                          }
              }
            }


            $resultado['cantidad_colaboradores']  = count($objetoReporte);
            $resultado['salario_bruto'] 			    = number_format($salario_bruto,2);
            $neto = $salario_bruto-$descuento_total;
            $resultado['salario_neto']  			       =  number_format($neto,2);
            $resultado['salario_neto_porcentaje']    =  ($salario_bruto>0)?(number_format((($neto)/$salario_bruto)*100,2)):'0.00';
            $resultado['salario_neto_progress_bar']  =  ($salario_bruto>0)?(number_format((($neto)/$salario_bruto)*100,2)):'0.00'.'%';

            $resultado['bonificaciones']  			       =  number_format(0,2);
            $resultado['bonificaciones_porcentaje']    =  '0.00';
            $resultado['bonificaciones_progress_bar']  =  '0 %';

            $resultado['descuentos']  				      = number_format($descuento_total,2);
            $resultado['descuentos_porcentaje']  	  = ($salario_bruto>0)?(number_format(($descuento_total/$salario_bruto)*100,2)):'0.00';
            $resultado['descuentos_progress_bar']  	= ($salario_bruto>0)?(number_format(($descuento_total/$salario_bruto)*100,2)."%"):'0%';

            return $resultado;

      }
  }

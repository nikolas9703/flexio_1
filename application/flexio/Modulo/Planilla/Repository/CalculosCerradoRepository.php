<?php
namespace Flexio\Modulo\Planilla\Repository;

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Planilla\Models\Planilla;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\Planilla\Repository\Regular\PlanillaRegularRepository;



class CalculosCerradoRepository{


       function collecion_excel_cerrada_regular($objetoReporte) {
                 $i=0;

                 foreach ($objetoReporte->colaboradores_pagadas as  $value) {

                   $salarios_divididos = $this->dividiendo_salario($value->ingresos);

                   $deducciones_divididos = $this->dividiendo_deducciones($value->deducciones, $value->descuentos);
                     $descuentos = $this->sumatoria_descuentos($value['pagos'][0]['descuentos']);

                  $csvdata[$i]['centro_contable'] = isset($value->colaborador->centro_contable->nombre)?$value->colaborador->centro_contable->nombre:'';
                   $csvdata[$i]['posicion'] = isset($value->colaborador->cargo->nombre)?$value->colaborador->cargo->nombre:'';
                   $csvdata[$i]['nombre'] = isset($value->colaborador->nombre_completo)?$value->colaborador->nombre_completo:'';
                   $csvdata[$i]["cedula"] = isset($value->colaborador->cedula)?$value->colaborador->cedula:'';
                    $csvdata[$i]["rata_hora"] =  $value->colaborador->rata_hora;
                    $csvdata[$i]["hr"] = number_format($salarios_divididos['regular'],2);
                    $csvdata[$i]["he"] = number_format($salarios_divididos['no_regular'],2);

                    $csvdata[$i]["isr"] = number_format($deducciones_divididos['isr'],2);
                    $csvdata[$i]["se"] = number_format($deducciones_divididos['se'],2);
                    $csvdata[$i]["ss"] = number_format($deducciones_divididos['ss'],2);
                    $csvdata[$i]["otros"] = number_format($deducciones_divididos['otros'],2);

                    $csvdata[$i]["deducciones"] = number_format($deducciones_divididos['totales'],2);
                    $csvdata[$i]["descuento"] =  number_format($deducciones_divididos['descuentos'],2);;
                    $csvdata[$i]["salario_bruto"] = number_format($value->salario_bruto,2);
//                    $csvdata[$i]["salario_neto"] = number_format($value->salario_bruto-$deducciones_divididos['totales'],2);
                    $csvdata[$i]["salario_neto"] =  number_format($value->salario_neto,2);

                    ++$i;
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

      private function dividiendo_salario($ingresos) {

         $sumatoria = $sumatoria_no_regular =  0;
        if(!empty($ingresos)){
          foreach ($ingresos as   $value) {

            if(preg_match("/HR/i", $value->detalle))
            {
                $sumatoria +=$value->calculo;
            }else{
                $sumatoria_no_regular +=$value->calculo;

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

      private function dividiendo_deducciones($deducciones, $descuentos) {

          $deducciones_divididos['ss'] = $deducciones_divididos['isr'] = $deducciones_divididos['se']  = $deducciones_divididos['otros'] = $deducciones_divididos['totales'] = $deducciones_divididos['descuentos']= 0;
        if(!empty($deducciones)){
          foreach ($deducciones as $value) {

             if( preg_match("/seguro social/i", $value->nombre )){
                  $deducciones_divididos['ss'] += $value->descuento;
            }
            else if (preg_match("/sobre la renta/i", $value->nombre )){ //isr
                $deducciones_divididos['isr'] += $value->descuento;
            }
            else if( preg_match("/seguro educativo/i", $value->nombre )){ //isr
                $deducciones_divididos['se'] += $value->descuento;
            }
            else{
                $deducciones_divididos['otros'] +=$value->descuento;
            }
            $deducciones_divididos['totales'] += $value->descuento;
          }
        }

        if(!empty($descuentos)){
          foreach ($descuentos as $descuento) {

             $deducciones_divididos['descuentos'] += $descuento->monto_ciclo;
          }
        }

         return $deducciones_divididos;
      }

  }

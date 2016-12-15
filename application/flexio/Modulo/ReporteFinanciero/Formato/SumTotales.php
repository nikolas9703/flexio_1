<?php
namespace Flexio\Modulo\ReporteFinanciero\Formato;

class SumTotales{
  function addColumnAndSumData($cuentas,$column="Totales"){

    return $cuentas->map(function($cuenta) use($column){
              $total = 0;

              foreach($cuenta as $key=>$value){
                if(!in_array($key,['id','nombre','codigo','padre_id'])){
                  $total += $value;
                }
              }

            $cuenta->{$column} = $total;
            return $cuenta;
           });

  }

  function addColumnAndSumArray($cuentas,$column="Totales"){

   $totales = $cuentas->map(function($cuenta) use($column){
              $total = 0;
              $cuenta = collect($cuenta);
              foreach($cuenta as $key=>$value){
                if(!in_array($key,['id','nombre','codigo','padre_id','tipo'])){
                  $total += (float)$value;
                }
              }

            $cuenta->put($column,$total);
            return $cuenta;
           });

           return $totales;
  }


  function sumarAddTotal($array,$columna="total"){
    return array_merge($array,[$columna =>array_sum($array)]);
  }
}

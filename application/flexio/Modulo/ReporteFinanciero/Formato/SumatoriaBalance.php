<?php
namespace Flexio\Modulo\ReporteFinanciero\Formato;

class SumatoriaBalance{


  function acumulado($cuentas){

   return  $cuentas->map(function($cuenta){
     $total = 0;
     $cuenta = collect($cuenta)->reverse();
     foreach($cuenta as $key=>$value){

       if(!in_array($key,['id','nombre','codigo','padre_id'])){
          $total = $total + $value;
         $cuenta->put($key, $total);
       }
     }
     return $cuenta->reverse();
   });


  }

}

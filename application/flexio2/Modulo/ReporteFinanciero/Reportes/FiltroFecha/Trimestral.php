<?php
namespace Flexio\Modulo\ReporteFinanciero\Reportes\FiltroFecha;
use Carbon\Carbon as Carbon;
class Trimestral extends RangoFecha{

  public function filtro($cantidad, $fecha)
  {
      $trimestral = 2;
      $fechas = [];
      $i = 0;
      for($i; $i <= $cantidad; $i++){
        if($i==0){
          $fechas[] = [
            $fecha->copy()->subMonths($trimestral)->startOfMonth()->toDateTimeString(),
            $fecha->endOfDay()->toDateTimeString()
          ];
        }else{
          $fechas[] = [
            $fecha->copy()->subMonths($trimestral)->startOfMonth()->toDateTimeString(),
            $fecha->copy()->endOfMonth()->toDateTimeString()
          ];
        }

        $fecha->subMonths($trimestral+1);
      }
      return $fechas;
   }

   public function filtroBalance($cantidad, $fecha){
     $trimestral = 3;
     $fechas = [];
     $i = 0;
     for($i; $i <= $cantidad; $i++){
       if($i==0){
         $fechas[] = [
           $fecha->copy()->subMonths($trimestral-1)->startOfMonth()->toDateTimeString(),
           $fecha->endOfDay()->toDateTimeString()
         ];
       }else{
         $fechas[] = [
           $i!=$cantidad?
           $fecha->copy()->subMonths($trimestral-1)->startOfMonth()->toDateTimeString():
           Carbon::createFromDate('2009', $fecha->copy()->subMonths($trimestral-1)->startOfMonth()->month, $fecha->copy()->day)->startOfMonth()->toDateTimeString(),
           $fecha->copy()->endOfMonth()->toDateTimeString()
         ];
       }

       $fecha->subMonths($trimestral);
     }
     return $fechas;
   }

}

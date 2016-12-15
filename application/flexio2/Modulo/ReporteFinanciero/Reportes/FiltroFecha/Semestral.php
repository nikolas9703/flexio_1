<?php
namespace Flexio\Modulo\ReporteFinanciero\Reportes\FiltroFecha;
use Carbon\Carbon as Carbon;
class Semestral extends RangoFecha
{
  public function filtro($cantidad, $fecha){
    $semestral = 6;
    $fechas=[];
    $i=0;
    for($i; $i <= $cantidad; $i++){
      if($i == 0){
        $fechas[] = [$fecha->copy()->subMonths($semestral-1)->startOfMonth()->toDateTimeString(), $fecha->endOfDay()->toDateTimeString()];
     }else{
       $fechas[] = [$fecha->copy()->subMonths($semestral-1)->startOfMonth()->toDateTimeString(), $fecha->copy()->endOfMonth()->endOfDay()->toDateTimeString()];
     }
      $fecha->subMonths($semestral);
    }
    return $fechas;
  }

  public function filtroBalance($cantidad, $fecha){
    $semestral = 6;
    $fechas=[];
    $i=0;
    for($i; $i <= $cantidad; $i++){
      if($i == 0){
        $fechas[] = [$fecha->copy()->subMonths($semestral-1)->startOfMonth()->toDateTimeString(), $fecha->endOfDay()->toDateTimeString()];
     }else{

       $fechas[] = [
         $i!=$cantidad?
         $fecha->copy()->subMonths($semestral-1)->startOfMonth()->toDateTimeString():
       Carbon::createFromDate('2009', $fecha->copy()->subMonths($semestral-1)->startOfMonth()->month, $fecha->copy()->day)->startOfMonth()->toDateTimeString()
       , $fecha->copy()->endOfMonth()->endOfDay()->toDateTimeString()];
     }
      $fecha->subMonths($semestral);
    }
    return $fechas;
  }
}

<?php
namespace Flexio\Modulo\ReporteFinanciero\Reportes\FiltroFecha;
use Carbon\Carbon as Carbon;
class Anual extends RangoFecha
{
  public function filtro($cantidad, $fecha){

    $fechas=[];
    $i=0;
    for($i; $i <= $cantidad; $i++){
      if($i == 0){
        $fechas[] = [
           $fecha->copy()->subMonths(12)->startOfDay()->toDateTimeString(), $fecha->endOfDay()->toDateTimeString()
        ];
      }else{
        $fechas[] = [
        $fecha->copy()->subMonths(12)->startOfDay()->toDateTimeString(), $fecha->endOfDay()->toDateTimeString()
        ];
      }
      $fecha->subMonths(12);
    }
    return $fechas;
  }

  public function filtroBalance($cantidad, $fecha){

    $fechas=[];
    $i=0;
    for($i; $i <= $cantidad; $i++){
      if($i == 0){
        $fechas[] = [
           $fecha->copy()->subMonths(12)->startOfDay()->toDateTimeString(), $fecha->startOfDay()->toDateTimeString()
        ];
      }else{
        $fechas[] = [
        $i!=$cantidad? $fecha->copy()->subMonths(12)->startOfDay()->toDateTimeString():
        Carbon::createFromDate('2007',   $fecha->copy()->month, $fecha->copy()->day)->startOfDay()->toDateTimeString()
        , $fecha->startOfDay()->toDateTimeString()
        ];
      }
      $fecha->subMonths(12);
    }
    return $fechas;
  }


}

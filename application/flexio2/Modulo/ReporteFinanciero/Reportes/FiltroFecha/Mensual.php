<?php
namespace Flexio\Modulo\ReporteFinanciero\Reportes\FiltroFecha;

class Mensual extends RangoFecha
{
  public function filtro($cantidad, $fecha){

    $fechas=[];
    $i=0;
    for($i; $i <= $cantidad; $i++){

      if($i==0){
        $fechas[] = [$fecha->copy()->startOfMonth()->toDateTimeString(),$fecha->endOfDay()->toDateTimeString()];
      }else{
        $fechas[] = [$fecha->startOfMonth()->toDateTimeString(),$fecha->endOfMonth()->toDateTimeString()];
      }
      $fecha->previous();
      $fecha->subMonth()->toDateTimeString();
      //endOfMonth o startOfMonth
    }
    return $fechas;
  }
}

<?php
namespace Flexio\Modulo\ReporteFinanciero\Reportes\FiltroFecha;

class Semanal extends RangoFecha
{
  public function filtro($cantidad, $fecha){


    $fechas=[];
    $i=1;
    for($i; $i <= $cantidad; $i++){
      $fechas[] = $fecha->format("W")."-".$fecha->year;
      $fecha->addWeek()->startOfDay();
    }
    return $fechas;
  }
}

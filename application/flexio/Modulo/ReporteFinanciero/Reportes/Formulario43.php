<?php
namespace Flexio\Modulo\ReporteFinanciero\Reportes;

use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\ReporteFinanciero\Reportes\Formulario43\Reporte43;

class Formulario43{

  function getReporte($datos_reporte){
    $datos = [];
    $year = $datos_reporte['year'];
    $mes  = $datos_reporte['mes'];
    $fecha = Carbon::createFromDate($year, $mes, $this->hoy());

    $datos['empresa_id'] = $datos_reporte['empresa_id'];
    $datos['fecha'] = $fecha;

    $consulta = new Reporte43($datos);
    return $consulta->generar();
  }

  protected function hoy(){
    return Carbon::now()->day;
  }

}

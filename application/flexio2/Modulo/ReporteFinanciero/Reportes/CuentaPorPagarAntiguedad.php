<?php
namespace Flexio\Modulo\ReporteFinanciero\Reportes;
use Carbon\Carbon as Carbon;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\ReporteFinanciero\Reportes\CuentaPorPagarAntiguedad\ProveedorFacturas;

class CuentaPorPagarAntiguedad{


  public function getReporte($datos_reporte){
    //$year = $datos_reporte['year'];
    //$mes  = $datos_reporte['mes'];
    //$fecha = Carbon::createFromDate($year, $mes, $this->hoy());
    $fecha = Carbon::now();
    $cuenta_por_pagar = new ProveedorFacturas($fecha);
    return $cuenta_por_pagar->consulta($datos_reporte['empresa_id']);
  }

  protected function hoy(){
     return Carbon::now()->day;
  }
}

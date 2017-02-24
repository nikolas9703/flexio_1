<?php
namespace Flexio\Modulo\ReporteFinanciero\Reportes;
use Carbon\Carbon as Carbon;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\ReporteFinanciero\Reportes\TransaccionesPorCentroContable\TransaccionesInfo;

class TransaccionesPorCentroContable{

  protected $fecha_inicial;

  public function getReporte($datos_reporte) {
    $fecha_desde = Carbon::createFromFormat('d/m/Y', $datos_reporte['fecha_desde'])->startOfDay();
    $fecha_hasta = Carbon::createFromFormat('d/m/Y', $datos_reporte['fecha_hasta'])->endOfDay();
    $clause = array_merge($datos_reporte,['fecha_inicio'=>$fecha_desde,'fecha_final'=>$fecha_hasta]);

    $transacciones = $this->info($clause);
    $parametros = $this->infoParametros($clause);
    $totales = $this->totales($clause);

    return ["transacciones" => $transacciones, "parametros" => $parametros, "totales" => $totales];
  }

  function info($clause) {
    $transacciones = (new TransaccionesInfo($clause))->listar();
    return $transacciones;
  }

  function infoParametros($clause) {
    $info = (new TransaccionesInfo($clause))->infoParametros();
    return $info;
  }

  function totales($clause) {
      $transacciones = (new TransaccionesInfo($clause));
      return [
        "total_debito"  => $transacciones->sumaDebito(),
        "total_credito" => $transacciones->sumaCredito(),
      ];
  }
}

<?php
namespace Flexio\Modulo\ReporteFinanciero\Reportes;
use Carbon\Carbon as Carbon;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\ReporteFinanciero\Reportes\CostoPorCentroCompras\FacturaCompraReporte;

class CostoPorCentroCompras{

  protected $fecha_inicial;

  public function getReporte($datos_reporte) {
    $fecha_desde = Carbon::createFromFormat('d/m/Y', $datos_reporte['fecha_desde'])->startOfDay();
    $fecha_hasta = Carbon::createFromFormat('d/m/Y', $datos_reporte['fecha_hasta'])->endOfDay();
    $clause = array_merge($datos_reporte,['fecha_inicio'=>$fecha_desde,'fecha_final'=>$fecha_hasta]);

    $detalle = $this->informeDeCosto($clause);
    $parametros = $this->infoParametros($clause);
    $totales = $this->totales($clause);

    return ["detalle" => $detalle, "parametros" => $parametros, "totales" => $totales];
  }

  function informeDeCosto($clause) {
    $facturas = (new FacturaCompraReporte($clause))->listar();
    return $facturas;
  }

  function infoParametros($clause) {
    $info = (new FacturaCompraReporte($clause))->infoParametros();
    return $info;
  }

  function totales($clause) {
      $factura = (new FacturaCompraReporte($clause));
      return [
        "subtotal"  => $factura->sumaSubtotales(),
        "descuento" => $factura->sumaDescuentos(),
        "impuesto"  => $factura->sumaImpuestos(),
        "total"     => $factura->sumaTotales(),
        "retenido"  => $factura->sumaRetenido(),
      ];
  }
}

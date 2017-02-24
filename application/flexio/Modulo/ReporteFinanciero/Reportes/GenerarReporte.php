<?php
namespace Flexio\Modulo\ReporteFinanciero\Reportes;
use \Exception as Exception;
class GenerarReporte implements InterfaceGenerarReporte{

  function generar($tipo=[])
  {

    //refactorizar porque se esta rompiendo la creacion de clase open / close
    $array_reportes = [
        'balance_situacion' => ReporteBalanceSituacion::class,
        'ganancias_perdidas' => GananciasPerdidas::class,
        'estado_cuenta_proveedor' => EstadoCuentaProveedor::class,
        'cuenta_por_pagar_por_antiguedad'=>CuentaPorPagarAntiguedad::class,
        'cuenta_por_cobrar_por_antiguedad'=>CuentaPorCobrarAntiguedad::class,
        'estado_de_cuenta_de_cliente' => EstadoCuentaCliente::class,
        'impuestos_sobre_ventas'=> ImpuestosSobreVentas::class,
        'impuestos_sobre_itbms'=> ImpuestosSobreItbms::class,
        'formulario43'=>Formulario43::class,
        'formulario433' => Formulario433::class,
        'costo_por_centro_compras'=>CostoPorCentroCompras::class,
        'transacciones_por_centro_contable'=> TransaccionesPorCentroContable::class,
        'reporte_de_caja' => ReporteDeCaja::class];

    if (!array_key_exists($tipo['tipo'], $array_reportes)) {
      throw new Exception("El key para este reporte no existe");
    }

    if (!class_exists($array_reportes[$tipo['tipo']])){
      throw new Exception("La clase para este reporte no existe");
    }

    return (new $array_reportes[$tipo['tipo']])->getReporte($tipo);

  }

}

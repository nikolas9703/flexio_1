<?php
namespace Flexio\Strategy\Reportes;

class ReporteDatosFormulario implements InterfaceReporte{

  function datoFormulario($tipo)
  {
    $reportes_catalogos = ['balance_situacion' => CatalogoBalanceSituacion::class,'ganancias_perdidas' => CatalogoGananciasPerdidas::class,'estado_cuenta_proveedor'=>CatalogoEstadoCuentaProveedor::class,'cuenta_por_pagar_por_antiguedad'=>CatalogoCuentaPorPagarAntiguedad::class,'cuenta_por_cobrar_por_antiguedad'=>CatalogoCuentaPorPagarAntiguedad::class,'estado_de_cuenta_de_cliente'=>CatalogoEstadoCuentaCliente::class,'impuestos_sobre_ventas'=>CatalogoImpuestoSobreVenta::class,'flujo_efectivo'=>CatalogoBalanceSituacion::class,'formulario43' => CatalogoImpuestoSobreVenta::class,'formulario433' => CatalogoImpuestoSobreVenta::class];

    if (!array_key_exists($tipo, $reportes_catalogos)) {
      throw new \Exception("El key para este reporte no existe");
    }

    if (!class_exists($reportes_catalogos[$tipo])){
      throw new \Exception("La clase para este reporte no existe");
    }

    return (new $reportes_catalogos[$tipo])->getCatalogos();
  }

}

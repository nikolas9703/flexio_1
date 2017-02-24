<?php
namespace Flexio\Modulo\ReporteFinanciero\Reportes;
use Carbon\Carbon as Carbon;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\ReporteFinanciero\Reportes\EstadoCuentaProveedor\ProveedorInfo;
use Flexio\Modulo\ReporteFinanciero\Reportes\ImpuestoSobreItbms\FacturaCompraReporte;

class ImpuestosSobreItbms{

  protected $fecha_inicial;
  protected $cuenta_por_pagar;
  protected $cuenta_facturas_compras;


  public function getReporte($datos_reporte){
     
    $fecha_desde = Carbon::createFromFormat('d/m/Y', $datos_reporte['fecha_desde'])->startOfDay();
    $fecha_hasta = Carbon::createFromFormat('d/m/Y', $datos_reporte['fecha_hasta'])->endOfDay();

    $datos_reporte = array_merge($datos_reporte,['fecha_inicio'=>$fecha_desde,'fecha_final'=>$fecha_hasta]);
    //info proveedor

    $proveedor = $this->proveedorInfo($datos_reporte);//listo
    $resumen = $this->resumenDeCuenta($datos_reporte,$proveedor);
    //$this->resumenDeCuenta($datos_reporte);
    //$detalle = $this->estadoDeCuenta($proveedor , $datos_reporte);
    return ['proveedor' => $proveedor,'resumen'=>$resumen];
  }

  function proveedorInfo($datos){
    //buscar proveedor id, empresa_id
    $proveedor = (new ProveedorInfo($datos['proveedor'],$datos['empresa_id']))->info();
    return $proveedor->get()->first();
  }

  function resumenDeCuenta($datos_reporte, $proveedor){

    $factura = new FacturaCompraReporte($datos_reporte, $proveedor);
    $total_facturado = $factura->total_facturado();
    $total_itbms = $factura->total_itbms();
    $total_retenido = $factura->total_retenido();  

    return ['total_facturado'=> $total_facturado, 'total_itbms' => $total_itbms, 'total_retenido' => $total_retenido];

  }
}

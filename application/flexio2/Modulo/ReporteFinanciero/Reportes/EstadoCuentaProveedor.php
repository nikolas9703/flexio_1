<?php
namespace Flexio\Modulo\ReporteFinanciero\Reportes;
use Carbon\Carbon as Carbon;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\ReporteFinanciero\Reportes\EstadoCuentaProveedor\ProveedorInfo;
use Flexio\Modulo\ReporteFinanciero\Reportes\EstadoCuentaProveedor\FacturaCompraReporte;

class EstadoCuentaProveedor{

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
    $detalle = $this->estadoDeCuenta($proveedor , $datos_reporte);
    return ['proveedor' => $proveedor,'resumen'=>$resumen,'detalle'=>$detalle];
  }

  function proveedorInfo($datos){
    //buscar proveedor id, empresa_id
    $proveedor = (new ProveedorInfo($datos['proveedor'],$datos['empresa_id']))->info();
    return $proveedor->get()->first();
  }

  function resumenDeCuenta($datos_reporte, $proveedor){

    $factura = new FacturaCompraReporte($datos_reporte, $proveedor);
    $balance_inicial = $factura->balance_inicial();
    $facturado = $factura->facturado();
    $pagado = $factura->pagado();
    $notas_debito =  $factura->notas_debito();
    $balance_final =  $factura->balance_final();

    return ['balance_inicial'=> $balance_inicial, 'facturado'=>$facturado, 'pagado'=>$pagado,'nota_debido'=>$notas_debito,'balance_final'=>$balance_final];

  }

  function estadoDeCuenta($proveedor,$datos_reporte){

    $facturasObj = $proveedor->facturas()->where(function($query)use($datos_reporte){
      $query->whereIn('estado_id',[13,14,15,16]);
      $query->whereBetween('created_at',[$datos_reporte['fecha_inicio'], $datos_reporte['fecha_final']]);
    })->get();
    //carga las facturas con pagos_aplicados
    $facturasObj->load('pagos_aplicados');
    $detalles = [];
    foreach($facturasObj as $factura){
      //$detalle
       array_push($detalles,['created_at'=>$factura->created_at,'codigo'=>$factura->codigo,'total'=>$factura->total,'detalle'=>'Factura #'.$factura->codigo]);
      if(!empty($factura->pagos_aplicados)){
        foreach($factura->pagos_aplicados as $pago){
           array_push($detalles,['created_at'=>$pago->created_at,'codigo'=>$pago->codigo,'total'=>$pago->pivot->monto_pagado,'detalle'=>'Pago a Factura #'.$factura->codigo]);
        }
      }
    }

    return $detalles;

  }


}

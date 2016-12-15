<?php
namespace Flexio\Modulo\ReporteFinanciero\Reportes;
use Carbon\Carbon as Carbon;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\ReporteFinanciero\Reportes\EstadoCuentaCliente\ClienteInfo;
use Flexio\Modulo\ReporteFinanciero\Reportes\EstadoCuentaCliente\FacturaVentaReporte;

class EstadoCuentaCliente
{

  protected $fecha_inicial;
  protected $cuenta_por_pagar;
  protected $cuenta_facturas_compras;


  public function getReporte($datos_reporte)
  {

    $fecha_desde = Carbon::createFromFormat('d/m/Y', $datos_reporte['fecha_desde'])->startOfDay();
    $fecha_hasta = Carbon::createFromFormat('d/m/Y', $datos_reporte['fecha_hasta'])->endOfDay();

    $datos_reporte = array_merge($datos_reporte,['fecha_inicio'=>$fecha_desde,'fecha_final'=>$fecha_hasta]);
    //info cliente
    $cliente = $this->clienteInfo($datos_reporte);//listo
    $resumen = $this->resumenDeCuenta($datos_reporte,$cliente);

    //$this->resumenDeCuenta($datos_reporte);
    $detalle = $this->estadoDeCuenta($cliente , $datos_reporte);
    return ['cliente' => $cliente,'resumen'=>$resumen,'detalle'=>$detalle];
  }

  function clienteInfo($datos)
  {
    //buscar cliente id, empresa_id
    $cliente = (new ClienteInfo($datos['cliente'],$datos['empresa_id']))->info();
    $dato_cliente = $cliente->get()->first();
    if(isset($datos['centro_facturacion_id'])) return $dato_cliente->load(['centro_facturable' => function($query) use($datos){
       $query->where('id',$datos['centro_facturacion_id']);
    }]);
    return $dato_cliente;
  }

  function resumenDeCuenta($datos_reporte, $cliente)
  {

    $factura = new FacturaVentaReporte($datos_reporte, $cliente);
    $balance_inicial = $factura->balance_inicial();
    $facturado = $factura->facturado();
    $cobrado = $factura->cobrado();
    $notas_credito =  $factura->notas_credito();
    $balance_final =  $factura->balance_final();

    return ['balance_inicial'=> $balance_inicial, 'facturado'=>$facturado, 'cobrado'=>$cobrado,'nota_credito'=>$notas_credito,'balance_final'=>$balance_final];

  }

  function estadoDeCuenta($cliente,$datos_reporte)
  {

    $facturasObj = $cliente->facturas()->where(function($query)use($datos_reporte){
      $query->whereIn('estado',['por_aprobar','por_cobrar','cobrado_parcial','cobrado_completo']);
      if(isset($datos_reporte['centro_facturacion_id'])) $query->where('centro_facturacion_id',$datos_reporte['centro_facturacion_id']);
      $query->whereBetween('created_at',[$datos_reporte['fecha_inicio'], $datos_reporte['fecha_final']]);
    })->get();
    //carga las facturas con pagos_aplicados

    $facturasObj->load('factura_cobros_aplicados');
    $detalles = [];
    foreach($facturasObj as $factura){
      //$detalle
       array_push($detalles,['created_at'=>$factura->created_at->format('d/m/Y'),'codigo'=>$factura->codigo,'total'=>$factura->total,'detalle'=>'Factura #'.$factura->codigo]);
      if(!empty($factura->factura_cobros_aplicados))
      {
        foreach($factura->factura_cobros_aplicados as $pago)
        {

           array_push($detalles,['created_at'=>$pago->created_at->format('d/m/Y'),'codigo'=>$pago->codigo,'total'=>$pago->pivot->monto_pagado,'detalle'=>'Cobro a Factura #'.$factura->codigo]);
        }
      }
    }

    return $detalles;

  }


}

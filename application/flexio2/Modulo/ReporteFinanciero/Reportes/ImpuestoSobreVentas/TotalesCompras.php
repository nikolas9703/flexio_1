<?php
namespace Flexio\Modulo\ReporteFinanciero\Reportes\ImpuestoSobreVentas;
use Flexio\Modulo\FacturasCompras\Models\FacturaCompra;
use Flexio\Library\Util\FlexioSession;

class TotalesCompras{

  protected $facturaCompra;
  protected $session;

  function __construct(){
    $this->facturaCompra = new FacturaCompra;
    $this->session = new FlexioSession;
  }

  function consulta($fecha){
    $factutaCompra=  $this->comprasFacturas($fecha);
    return $factutaCompra;
  }

  function comprasFacturas($fecha){
    $facturas = $this->facturaCompra->where(function($query) use($fecha){
      $query->where('fecha_desde', '<=', $fecha);
      $query->where('estado_id', '<>', 17);
      $query->where('empresa_id',$this->session->empresaId());
    })->get();

    $facturaCompras =  $facturas->load('facturas_compras_items.impuesto');
    return $this->formatoArray($facturaCompras);
  }

  function formatoArray($facturas){

    $totalVentas = collect(['subtotal' => $facturas->sum('subtotal')]);
    $impuestoNombre = [];
    $dataItems = $facturas->flatMap(function($factura){
          return $factura->facturas_compras_items;
    });
    //  dd($dataItems->toArray());
    $total_impuestos = $dataItems->groupBy('impuesto.nombre')->map(function($lines_items){
          $coleccion =  $lines_items->groupBy('impuesto_id')->map(function($item){
            return $item->sum('impuestos');})->first();
          return $coleccion;
    });

    $info_ventas = $totalVentas->merge($total_impuestos)->all();

    foreach($info_ventas as $key=>$value){
     $impuestoNombre = array_merge($impuestoNombre,[str_slug($key,'-')=>$value]);
    }
    unset($impuestoNombre['']);

    return  $impuestoNombre;
  }

  function NotaDebito(){}
}

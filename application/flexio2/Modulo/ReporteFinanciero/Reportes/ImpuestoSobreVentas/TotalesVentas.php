<?php
namespace Flexio\Modulo\ReporteFinanciero\Reportes\ImpuestoSobreVentas;
use Flexio\Modulo\FacturasVentas\Models\FacturaVenta;
use Flexio\Library\Util\FlexioSession;


class TotalesVentas{
  protected $facturaVenta;
  protected $session;

  function __construct(){
    $this->facturaVenta = new FacturaVenta;
    $this->session = new FlexioSession;
  }

  function consulta($fecha){
   return  $this->ventasFacturas($fecha);
  }


  function ventasFacturas($fecha){

    $facturas = $this->facturaVenta->where(function($query) use($fecha){
      $query->where('fecha_desde', '<=', $fecha);
      $query->where('estado', '<>', 'anulada');
      $query->where('empresa_id', $this->session->empresaId());
    })->get();

    $facturas->load('items.impuesto');
    return $this->formatoArray($facturas);

  }

  function formatoArray($facturas){

    $totalVentas = collect(['subtotal' => $facturas->sum('subtotal')]);
    $impuestoNombre = [];
    $dataItems = $facturas->flatMap(function($factura){
          return $factura->items;
    });

    $total_impuestos = $dataItems->groupBy('impuesto.nombre')->map(function($lines_items){
          $coleccion =  $lines_items->groupBy('impuesto_id')->map(function($item){
            return $item->sum('impuesto_total');})->first();
          return $coleccion;
    });

    $info_ventas = $totalVentas->merge($total_impuestos)->all();

    foreach($info_ventas as $key=>$value){
     $impuestoNombre = array_merge($impuestoNombre,[str_slug($key,'-')=>$value]);
    }

    return  $impuestoNombre;
  }

}

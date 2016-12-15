<?php
namespace Flexio\Modulo\ReporteFinanciero\Reportes\ImpuestoSobreVentas;
use Flexio\Modulo\NotaCredito\Models\NotaCredito;
use Flexio\Library\Util\FlexioSession;

class TotalesNotasCredito{
  protected $nota_credito;
  protected $session;

  function __construct(){
    $this->nota_credito = new NotaCredito;
    $this->session = new FlexioSession;
  }

  function consulta($fecha){
   return  $this->ventasNotasCredito($fecha);
  }

  function ventasNotasCredito($fecha){
    $notas_creditos = $this->nota_credito->where(function($query) use($fecha){
      $query->where('fecha', '<=', $fecha);
      $query->where('estado', '<>', 'anulado');
      $query->where('empresa_id', $this->session->empresaId());
    })->get();

    $notas_creditos->load('items.impuesto');
    return $this->formatoArray($notas_creditos);
  }

  function formatoArray($notas_creditos){
    $total_notas_credito = collect(['subtotal' => $notas_creditos->sum('subtotal')]);
    $impuestoNombre = [];
    $dataItems = $notas_creditos->flatMap(function($nota_credito){
          return $nota_credito->items;
    });

    $total_impuestos = $dataItems->groupBy('impuesto.nombre')->map(function($lines_items){
          $coleccion =  $lines_items->groupBy('impuesto_id')->map(function($item){
            return $item->sum('impuesto_total');})->first();
          return $coleccion;
    });

    $info_ventas = $total_notas_credito->merge($total_impuestos)->all();

    foreach($info_ventas as $key=>$value){
     $impuestoNombre = array_merge($impuestoNombre,[str_slug($key,'-')=>$value]);
    }
    unset($impuestoNombre['']);
    return  $impuestoNombre;
  }
}

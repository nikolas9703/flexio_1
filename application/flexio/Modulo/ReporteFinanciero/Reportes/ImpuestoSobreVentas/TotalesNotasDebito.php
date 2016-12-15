<?php
namespace Flexio\Modulo\ReporteFinanciero\Reportes\ImpuestoSobreVentas;
use Flexio\Modulo\NotaDebito\Models\NotaDebito;
use Flexio\Library\Util\FlexioSession;

class TotalesNotasDebito{
  protected $nota_debito;
  protected $session;

  function __construct(){
    $this->nota_debito = new NotaDebito;
    $this->session = new FlexioSession;
  }

  function consulta($fecha){
   return  $this->comprasNotasDebito($fecha);
  }

  function comprasNotasDebito($fecha){
    $notas_debitos = $this->nota_debito->where(function($query) use($fecha){
      $query->where('fecha', '<=', $fecha);
      $query->where('estado', '<>', 'anulado');
      $query->where('empresa_id', $this->session->empresaId());
    })->get();

    $notas_debitos->load('items.impuesto');
    return $this->formatoArray($notas_debitos);
  }

  function formatoArray($notas_debitos){
    $total_notas_debito = collect(['subtotal' => $notas_debitos->sum('subtotal')]);
    $impuestoNombre = [];
    $dataItems = $notas_debitos->flatMap(function($nota_debito){
          return $nota_debito->items;
    });

    $total_impuestos = $dataItems->groupBy('impuesto.nombre')->map(function($lines_items){
          $coleccion =  $lines_items->groupBy('impuesto_id')->map(function($item){
            return $item->sum('impuesto_total');})->first();
          return $coleccion;
    });

    $info_compras = $total_notas_debito->merge($total_impuestos)->all();

    foreach($info_compras as $key=>$value){
     $impuestoNombre = array_merge($impuestoNombre,[str_slug($key,"-")=>$value]);
    }
    unset($impuestoNombre['']);
    return  $impuestoNombre;
  }
}

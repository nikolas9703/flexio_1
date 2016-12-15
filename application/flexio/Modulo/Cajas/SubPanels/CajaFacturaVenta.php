<?php
namespace Flexio\Modulo\Cajas\SubPanels;
use Flexio\Modulo\FacturasVentas\Models\FacturaVenta;

class CajaFacturaVenta{

  function getFacturaVenta($clause = array(), $sidx = null, $sord = null, $limit = null, $start = null){
    $factura = FacturaVenta::whereHas('cobros',function($cobro) use($clause){
      $cobro->where('cob_cobros.empresa_id',$clause['empresa_id']);
      $cobro->where('depositable_id',$clause['caja_id']);
    });
    if (!is_null($sidx) && !is_null($sord)) $factura->orderBy($sidx, $sord);
    if (!is_null($limit) && !is_null($start))$factura->skip($start)->take($limit);

    return $factura;
  }

}

<?php
namespace Flexio\Modulo\FacturasVentas\Repository;
use Flexio\Modulo\FacturasVentas\Models\FacturaVentaCatalogo as FacturaVentaCatalogo;

class FacturaVentaCatalogoRepository{

  function getEtapas(){
    return FacturaVentaCatalogo::estadosFacturaVenta()->get();
  }

  function getTerminoPago(){
    return FacturaVentaCatalogo::terminoFacturaVenta()->get();
  }
}

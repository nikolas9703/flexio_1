<?php
namespace Flexio\Modulo\OrdenesAlquiler\Repository;
use Flexio\Modulo\OrdenesAlquiler\Models\OrdenVentaAlquilerCatalogo as OrdenVentaCatalogo;

class OrdenVentaCatalogoRepository{

  function getEtapas(){
    return OrdenVentaCatalogo::where('tipo','etapa')->get(array('etiqueta','valor'));
  }

  function getTerminoPago(){
    return OrdenVentaCatalogo::where('tipo','termino_pago')->get(array('id','etiqueta','valor'));
  }
}

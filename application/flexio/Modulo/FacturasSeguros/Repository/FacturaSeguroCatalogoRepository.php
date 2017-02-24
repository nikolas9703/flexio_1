<?php
namespace Flexio\Modulo\FacturasSeguros\Repository;
use Flexio\Modulo\FacturasSeguros\Models\FacturaSeguroCatalogo as FacturaSeguroCatalogo;

class FacturaSeguroCatalogoRepository{

  function getEtapas(){
    return FacturaSeguroCatalogo::estadosFacturaSeguro()->get();
  }

  function getTerminoPago(){
    return FacturaSeguroCatalogo::terminoFacturaSeguro()->get();
  }
}

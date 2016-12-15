<?php
namespace Flexio\Modulo\Devoluciones\Repository;
use Flexio\Modulo\Devoluciones\Models\DevolucionCatalogo as DevolucionCatalogo;

class DevolucionCatalogoRepository{

  function getEtapas(){
    return DevolucionCatalogo::estados()->get();
  }

  function getRazon(){
    return DevolucionCatalogo::razon()->get();
  }
}

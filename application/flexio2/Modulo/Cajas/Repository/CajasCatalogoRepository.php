<?php
namespace Flexio\Modulo\Cajas\Repository;
use Flexio\Modulo\Cajas\Models\CajasCatalogo as CajasCatalogo;

class CajasCatalogoRepository{
  function getEstados(){
    return CajasCatalogo::whereIn("id_cat", array(1,2))->get();
  }
}

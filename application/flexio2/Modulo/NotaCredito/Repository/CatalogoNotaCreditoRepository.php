<?php
namespace Flexio\Modulo\NotaCredito\Repository;
use Flexio\Modulo\NotaCredito\Models\CatalogoNotaCredito as CatalogoNotaCredito;

class CatalogoNotaCreditoRepository{

function getEtapas(){
  return CatalogoNotaCredito::estados()->get();
}

}

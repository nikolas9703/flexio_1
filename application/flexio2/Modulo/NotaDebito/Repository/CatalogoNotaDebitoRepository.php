<?php
namespace Flexio\Modulo\NotaDebito\Repository;
use Flexio\Modulo\NotaDebito\Models\CatalogoNotaDebito as CatalogoNotaDebito;

class CatalogoNotaDebitoRepository{

function getEtapas(){
  return CatalogoNotaDebito::estados()->get();
}

}

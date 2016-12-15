<?php
namespace Flexio\Modulo\Cobros\Repository;
use Flexio\Modulo\Cobros\Models\CatalogoCobro as CatalogoCobro;

class CatalogoCobroRepository{

  public static function getEtapas(){
    return CatalogoCobro::estados()->get();
  }

  public static function getMetodoCobro(){
    return CatalogoCobro::metodoCobrado()->get();
  }

  public static function getTipoCobro(){
    return CatalogoCobro::tipoCobro()->get();
  }
}

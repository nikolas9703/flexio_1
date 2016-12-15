<?php
namespace Flexio\Modulo\Pagos\Repository;
use Flexio\Modulo\Pagos\Models\PagosCatalogos as CatalogoPago;

class CatalogoPagoRepository{
  
  public static function getTipoCobro(){
    return CatalogoPago::tipoCobro()->get();
  }

}

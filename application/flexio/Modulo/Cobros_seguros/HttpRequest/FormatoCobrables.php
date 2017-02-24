<?php
namespace Flexio\Modulo\Cobros_seguros\HttpRequest;
use Flexio\Modulo\Cobros_seguros\Models\CobroFactura;

class FormatoCobrables{


  public static function formato($elementos){


      $cobrado = $elementos->map(function($item){
          return CobroFactura::register($item);
      });
      return $cobrado->all();
  }
}

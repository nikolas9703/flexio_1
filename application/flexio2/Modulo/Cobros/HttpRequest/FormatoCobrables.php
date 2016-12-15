<?php
namespace Flexio\Modulo\Cobros\HttpRequest;
use Flexio\Modulo\Cobros\Models\CobroFactura;

class FormatoCobrables{


  public static function formato($elementos){


      $cobrado = $elementos->map(function($item){
          return CobroFactura::register($item);
      });
      return $cobrado->all();
  }
}

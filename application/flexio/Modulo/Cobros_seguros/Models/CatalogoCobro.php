<?php
namespace Flexio\Modulo\Cobros_seguros\Models;
use Flexio\Modulo\Catalogos\Models\Catalogo;

class CatalogoCobro
{
  protected $catalogo;


  public static function estados()
  {
      return Catalogo::where(function($query){
        $query->where('modulo','cobro');
        $query->where('tipo','estado');
      })->get();
  }

  public static function metodoCobro()
  {
    return Catalogo::where(function($query){
      $query->where('modulo','cobro_seguros');
      $query->where('tipo','metodo_cobro_seguros');
    })->get();
  }

  public static function tipoCobro()
  {
    return Catalogo::where(function($query){
      $query->where('modulo','cobro');
      $query->where('tipo','tipo_cobro');
    })->get();
  }
}

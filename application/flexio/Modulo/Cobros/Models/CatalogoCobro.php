<?php
namespace Flexio\Modulo\Cobros\Models;
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
      $query->where('modulo','cobro');
      $query->where('tipo','metodo_cobro');
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

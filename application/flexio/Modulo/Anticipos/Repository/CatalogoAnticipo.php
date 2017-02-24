<?php
namespace Flexio\Modulo\Anticipos\Repository;
use Flexio\Modulo\Catalogos\Models\Catalogo;

class CatalogoAnticipo{

  public function getEstados(){
    return Catalogo::where(function($query){
      $query->where('modulo','anticipo')
            ->where('activo',1)
            ->where('tipo','estado');
    })->get(['id','etiqueta','valor']);
  }

  public function getMetodoAnticipo(){
    return Catalogo::where(function($query){
      $query->where('modulo','anticipo')
              ->where('activo',1)
            ->where('tipo','metodo_anticipo');
    })->get(['id','etiqueta','valor']);
  }
}

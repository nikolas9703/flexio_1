<?php
namespace Flexio\Modulo\Politicas\Repository;
use Flexio\Modulo\Politicas\Models\PoliticasCatalogo;

class PoliticasCatalogosRepository{

  function getTransacciones(){
    return PoliticasCatalogo::all();
  }

   function getTransaccionesById($id){
    return PoliticasCatalogo::where('id', $id)->get(array('valor','etiqueta'));
  }
}

<?php
namespace Flexio\Modulo\EntradaManuales\Repository;

use Flexio\Modulo\EntradaManuales\Models\EntradaManual;

class RepositorioEntradaManual
{
    public $builder;

    function __construct(){
      $this->builder = (new EntradaManual)->newQuery();
    }

  function getEntradasManuales($empresa_id = null){
      if(!is_null($empresa_id)){
          return $this->builder->where('empresa_id',$empresa_id);
      }

      return $this;
  }

    function conFiltro($campo){
        if(!is_array($campo)){
            throw new Exception('campo debe ser un array.');
        }
        $this->builder->filtro($campo);
        return $this;
    }

    function sort($columna="id", $orden="desc"){
        $this->builder->orderBy($columna, $orden);
        return $this;
    }

    function fetch(){
        return $this->builder->get();
    }
}

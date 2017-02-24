<?php
namespace Flexio\Modulo\Catalogos\Services;

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Provider\QueryFilters;
use Carbon\Carbon as Carbon;

class CatalogoQueryFilters extends QueryFilters{

  function tipo($valor){
    return $this->builder->where('tipo',$valor);
  }

  function modulo($valor){
    return $this->builder->where('modulo',$valor);
  }

  function activo($valor){
    return $this->builder->where('activo',$valor);
  }
}

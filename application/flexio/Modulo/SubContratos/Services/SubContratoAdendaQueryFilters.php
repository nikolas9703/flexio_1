<?php
namespace Flexio\Modulo\SubContratos\Services;

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Provider\QueryFilters;
use Carbon\Carbon as Carbon;

class SubContratoAdendaQueryFilters extends QueryFilters{


  function empresa($empresa){
    return $this->builder->where('empresa_id',$empresa);
  }

  function subcontrato($subcontrato){
    return $this->builder->where('subcontrato_id',$subcontrato);
  }

}

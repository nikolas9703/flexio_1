<?php
namespace Flexio\Modulo\CentrosContables\Service;

use Flexio\Provider\QueryFilters;
use Carbon\Carbon as Carbon;

class CentroContableFilters extends QueryFilters{

    public function q($q)
    {
        return $this->builder->where(function($query) use ($q){
            $query->where('nombre', 'like', "%$q%");
        });
    }

    public function empresa($empresa)
    {
      return $this->builder->where('empresa_id',$empresa);
    }
}

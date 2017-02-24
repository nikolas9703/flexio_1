<?php
namespace Flexio\Modulo\OrdenesCompra\Services;

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Provider\QueryFilters;
use Carbon\Carbon as Carbon;

class OrdenCompraFilters extends QueryFilters{

    public function centros_contables($centros)
    {
        if(!in_array('todos', $centros))
        {
            return $this->builder->whereHas("centro_contable_query", function($centro_contable) use ($centros){
                $centro_contable->whereIn('cen_centros.id', $centros);
            });
        }
        return $this->builder;
    }

}

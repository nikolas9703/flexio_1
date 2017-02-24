<?php
namespace Flexio\Modulo\CentroFacturable\Services;

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Provider\QueryFilters;
use Carbon\Carbon;


class CentroFacturableFilters extends QueryFilters
{

    public function cliente($cliente_id)
    {
        return $this->builder->where('cliente_id', $cliente_id);
    }

}

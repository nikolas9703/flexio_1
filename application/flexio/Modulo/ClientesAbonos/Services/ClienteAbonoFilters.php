<?php

namespace Flexio\Modulo\ClientesAbonos\Services;

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Provider\QueryFilters;
use Carbon\Carbon;


class ClienteAbonoFilters extends QueryFilters
{

    public function cliente($cliente_id)
    {
        return $this->builder->where('cliente_id', $cliente_id);
    }

}

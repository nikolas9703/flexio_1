<?php
namespace Flexio\Modulo\Oportunidades\Services;

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Provider\QueryFilters;
use Carbon\Carbon;


class OportunidadFilters extends QueryFilters
{

    public function cliente($cliente_id)
    {
        return $this->builder->whereHas('cliente', function($cliente) use ($cliente_id) {
            $cliente->whereId($cliente_id);
        });
    }

    public function uuid_oportunidad($uuid_oportunidad)
    {
        if(is_array($uuid_oportunidad))
        {
            return $this->builder->whereIn('uuid_oportunidad', array_map(function($row){return hex2bin($row);}, $uuid_oportunidad));
        }
        return $this->builder->where('uuid_oportunidad', hex2bin($uuid_oportunidad));
    }


}

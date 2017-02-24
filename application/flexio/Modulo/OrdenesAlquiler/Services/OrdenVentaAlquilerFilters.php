<?php
namespace Flexio\Modulo\OrdenesAlquiler\Services;

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Provider\QueryFilters;
use Carbon\Carbon;

class OrdenVentaAlquilerFilters extends QueryFilters {
    
    public function contrato_alquiler($contrato_alquiler_id) {
        return $this->builder->where('contrato_id', $contrato_alquiler_id)->get();
    }
    
}
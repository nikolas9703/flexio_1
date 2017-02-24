<?php
namespace Flexio\Modulo\Inventarios\Services;

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Provider\QueryFilters;
use Carbon\Carbon;

class ItemFilters extends QueryFilters {
    
     public function contrato_alquiler($contrato_alquiler_id) {
        
        return $this->builder->whereHas('contrato_alquiler',function($query)use($contrato_alquiler_id){
         $query->where('conalq_contratos_alquiler.id', $contrato_alquiler_id);
        });
    }
}
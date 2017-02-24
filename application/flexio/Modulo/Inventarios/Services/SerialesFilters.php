<?php
namespace Flexio\Modulo\Inventarios\Services;

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Provider\QueryFilters;
use Carbon\Carbon;

class SerialesFilters extends QueryFilters {
    
    public function contrato_alquiler($contrato_alquiler_id) {
        
        /*return $this->builder->whereHas('serialesByContratoAlquilerId', function($query) use ($contrato_alquiler_id) {
            $query->where('contratos_items.contratable_id', $contrato_alquiler_id);
        });*/
        
        return $this->builder->whereExists(function($query)use($contrato_alquiler_id) {
            $query->select(Capsule::raw(1))
                    ->from('contratos_items')
                    ->join('contratos_items_detalles', 'contratos_items_detalles.relacion_id', '=', 'contratos_items.id')
                    ->whereRaw('contratos_items_detalles.serie = inv_items_seriales.nombre')
                    ->where('contratos_items.contratable_id', '=', $contrato_alquiler_id);
        })->get();
    }
}
<?php
namespace Flexio\Modulo\DevolucionesAlquiler\Services;

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Provider\QueryFilters;
use Carbon\Carbon;


class DevolucionAlquilerFilters extends QueryFilters
{

    public function serie($serie_id)
    {
        return  $this->builder->where(function($q) use ($serie_id){
                    $q->where(function($q2) use ($serie_id){
                        $q2->whereHas("lines_items", function($line_item) use ($serie_id){
                            $line_item->whereHas('seriales', function($serie) use ($serie_id){
                                $serie->where('inv_items_seriales.id', $serie_id);
                            });
                        });
                    });
                });
    }
    
    public function contrato_alquiler($contrato_alquiler_id) {
        return $this->builder->whereHas('contratos_alquiler_retornos', function($query) use ($contrato_alquiler_id) {
            $query->where('contratos_items.contratable_id', $contrato_alquiler_id);
        });
    }

}

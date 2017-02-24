<?php
namespace Flexio\Modulo\Entradas\Services;

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Provider\QueryFilters;
use Carbon\Carbon;


class EntradaFilters extends QueryFilters
{

    public function serie($serie_id)
    {
        return  $this->builder->where(function($q) use ($serie_id){
                    $q->where("operacion_type", "Flexio\Modulo\OrdenesCompra\Models\OrdenesCompra")
                    ->whereHas("orden", function($q2) use ($serie_id){
                        $q2->whereHas("lines_items", function($line_item) use ($serie_id){
                            $line_item->whereHas('seriales', function($serie) use ($serie_id){
                                $serie->where('inv_items_seriales.id', $serie_id);
                            });
                        });
                    })
                    ->orWhere("operacion_type", "Flexio\Modulo\Traslados\Models\Traslados")
                    ->whereHas("traslado", function($traslado) use ($serie_id){
                        $traslado->whereHas("lines_items", function($line_item) use ($serie_id){
                            $line_item->whereHas('seriales', function($serie) use ($serie_id){
                                $serie->where('inv_items_seriales.id', $serie_id);
                            });
                        });
                    });
                });
    }

}

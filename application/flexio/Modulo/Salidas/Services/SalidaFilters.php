<?php
namespace Flexio\Modulo\Salidas\Services;

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Provider\QueryFilters;
use Carbon\Carbon;


class SalidaFilters extends QueryFilters
{

    public function serie($serie_id)
    {
        return  $this->builder->where(function($q) use ($serie_id){
                    $q->where("operacion_type", "Flexio\Modulo\Traslados\Models\Traslados")
                    ->whereHas("traslado", function($q2) use ($serie_id){
                        $q2->whereHas("lines_items", function($line_item) use ($serie_id){
                            $line_item->whereHas('seriales', function($serie) use ($serie_id){
                                $serie->where('inv_items_seriales.id', $serie_id);
                            });
                        });
                    })
                    ->orWhere("operacion_type", "Flexio\Modulo\Consumos\Models\Consumos")
                    ->whereHas("consumo", function($q2) use ($serie_id){
                        $q2->whereHas("lines_items", function($line_item) use ($serie_id){
                            $line_item->whereHas('seriales', function($serie) use ($serie_id){
                                $serie->where('inv_items_seriales.id', $serie_id);
                            });
                        });
                    })
                    ->orWhere("operacion_type", "Flexio\Modulo\OrdenesVentas\Models\OrdenVenta")
                    ->whereHas("orden_venta", function($q2) use ($serie_id){
                        $q2->whereHas("lines_items", function($line_item) use ($serie_id){
                            $line_item->whereHas('seriales', function($serie) use ($serie_id){
                                $serie->where('inv_items_seriales.id', $serie_id);
                            });
                        });
                    });
                });
    }

}

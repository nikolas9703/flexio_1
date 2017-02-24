<?php

namespace Flexio\Modulo\Traslados\Services;

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Provider\QueryFilters;
use Carbon\Carbon;


class TrasladoFilters extends QueryFilters
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

    public function empresa($empresa_id)
    {
        return $this->builder->where('id_empresa', $empresa_id);
    }

    public function numero_traslado($numero_traslado)
    {
        $aux = str_replace('TRAS', '', $numero_traslado);
        return $this->builder->where('numero', 'like', "%$aux%");
    }

    public function de_bodega($uuid_bodega)
    {
        return $this->builder->where('uuid_lugar_anterior', hex2bin($uuid_bodega));
    }

    public function a_bodega($uuid_bodega)
    {
        return $this->builder->where('uuid_lugar', hex2bin($uuid_bodega));
    }

    public function fecha_solicitud($fecha)
    {
        return $this->builder->whereDate('fecha_creacion', '=', Carbon::createFromFormat('d/m/Y', $fecha)->format('Y-m-d'));
    }

    public function fecha_entrega($fecha)
    {
        return $this->builder->whereDate('fecha_entrega', '=', Carbon::createFromFormat('d/m/Y', $fecha)->format('Y-m-d'));
    }

    public function estado($estado)
    {
        return $this->builder->where('id_estado', $estado);
    }

}

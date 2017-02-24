<?php
namespace Flexio\Modulo\Inventarios\Filters;

class SerialesFilters {

    public function serialesNoAjustesNegativosDeBodega($seriales, $uuid_bodega)
    {
        return $seriales
        ->filter(function($serial) use ($uuid_bodega){
            $ultimo_movimiento  = $serial->seriales_lineas->sortByDesc("id")->values();
            $aux                = $ultimo_movimiento[0]->line_item;

            return $aux->tipoable->bodega->uuid_bodega == $uuid_bodega and !($aux->tipoable->tipo_ajuste_id == "1" and $aux->tipoable_type == 'Flexio\\Modulo\\Ajustes\\Models\\Ajustes');
        })->values();
    }

    public function serialesNoConsumosAprobadosDeBodega($seriales, $uuid_bodega)
    {
        return $seriales
        ->filter(function($serial) use ($uuid_bodega){
            $ultimo_movimiento  = $serial->seriales_lineas->sortByDesc("id")->values();
            $aux                = count($ultimo_movimiento) ? $ultimo_movimiento[0]->line_item : null;
            if(is_null($aux) || !count($aux->tipoable) || !count($aux->tipoable->bodega)) return false;
            return $aux->tipoable->bodega->uuid_bodega == $uuid_bodega and !($aux->tipoable->estado_id == "2" and $aux->tipoable_type == 'Flexio\\Modulo\\Consumos\\Models\\Consumos');
        })->values();
    }

    public function serialesNoOrdenesVentasAprobadasDeBodega($seriales, $uuid_bodega)
    {
        return $seriales
        ->filter(function($serial) use ($uuid_bodega){
            $ultimo_movimiento  = $serial->seriales_lineas->sortByDesc("id")->values();
            $aux                = $ultimo_movimiento[0]->line_item;

            return $aux->tipoable->bodega->uuid_bodega == $uuid_bodega and !($aux->tipoable->estado == "por_facturar" and $aux->tipoable_type == 'Flexio\\Modulo\\OrdenesVentas\\Models\\OrdenVenta');
        })->values();
    }

    public function serialesNoFacturasVentasAprobadasDeBodega($seriales, $uuid_bodega)
    {
        return $seriales
        ->filter(function($serial) use ($uuid_bodega){
            $ultimo_movimiento  = $serial->seriales_lineas->sortByDesc("id")->values();
            $aux                = $ultimo_movimiento[0]->line_item;

            return $aux->tipoable->bodega->uuid_bodega == $uuid_bodega and !($aux->tipoable->estado == "por_cobrar" and $aux->tipoable_type == 'Flexio\\Modulo\\FacturasVentas\\Models\\FacturaVenta');
        })->values();
    }

    public function serialesNoTrasladadosDeBodega($seriales, $uuid_bodega)
    {
        return $seriales
        ->filter(function($serial) use ($uuid_bodega){
            $ultimo_movimiento  = $serial->seriales_lineas->sortByDesc("id")->values();
            $aux                = $ultimo_movimiento[0]->line_item;

            return $aux->tipoable->bodega->uuid_bodega == $uuid_bodega and !($aux->tipoable->id_estado > "1" and $aux->tipoable_type == 'Flexio\\Modulo\\Traslados\\Models\\Traslados');
        })->values();
    }
}

<?php

namespace Flexio\Modulo\ContratosAlquiler\Traits;

use Flexio\Modulo\Inventarios\Models\LinesItems;
use Flexio\Modulo\Inventarios\Models\Seriales;

trait Bitacora
{
    public function createBitacora($dirty, $aux = [])
    {
        if(isset($dirty['serie']) && !empty($dirty['serie']))
        {
            $operacion = (new $dirty['operacion_type'])->find($dirty['operacion_id']);
            $line_item_array = $this->_line_item_array($dirty);
            $serie = Seriales::where('item_id', $line_item_array['item_id'])->where('nombre', $dirty['serie'])->first();
            $this->_update_serie($serie, $operacion, $dirty, $aux);

            if(isset($aux['serie']) && !empty($aux['serie'])){
                $line_item = LinesItems::create($line_item_array);
                $line_item->seriales()->sync([$serie->id]);
            }

        }
    }

    private function _update_serie($serie, $operacion, $dirty, $aux)
    {
        if(!preg_match("/RT/i", $operacion->codigo)){
            $serie->estado = $operacion->estado_id == '4' ? 'no_disponible' : 'disponible';
        }else{
            $serie->estado = $operacion->estado_id == '2' ? 'disponible' : 'no_disponible';
        }

        $serie->bodega_id = $dirty['bodega_id'];
        $serie->cliente_id = $operacion->cliente_id;
        $serie->centro_facturacion_id = isset($operacion->centro_facturacion_id) ? $operacion->centro_facturacion_id : 0;
        $serie->save();
    }

    private function _line_item_array($dirty)
    {
        $contrato_alquiler_item = (new $dirty['relacion_type'])->find($dirty['relacion_id']);
        return [
            'categoria_id' => $contrato_alquiler_item->categoria_id,
            'tipoable_id' => $dirty['operacion_id'],
            'tipoable_type' => $dirty['operacion_type'],
            'item_id' => $contrato_alquiler_item->item_id,
            'cantidad' => $dirty['cantidad']
        ];
    }
}

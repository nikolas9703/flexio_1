<?php

namespace Flexio\Modulo\MovimientosMonetarios\Transform;

use Carbon\Carbon;

class MovimientoReciboTransform
{
    public function campo($campo)
    {
        $fecha = Carbon::createFromFormat('d/m/Y', $campo['campo']['fecha_entrada']);
        return array_merge(
            $campo['campo'],
            $campo,
            [
                'narracion' => $campo['campo']['nombre'],
                'created_at' => $fecha,
                'cliente_id' => $campo['empezable_type'] == 'cliente' ? $campo['empezable_id'] : 0,
                'proveedor_id' => $campo['empezable_type'] == 'proveedor' ? $campo['empezable_id'] : 0,
                'estado' => 1,//desing no support this input
                'fecha_inicio' => $fecha,
            ]
        );
    }
}

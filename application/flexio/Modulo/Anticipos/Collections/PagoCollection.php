<?php
namespace Flexio\Modulo\Anticipos\Collections;

class PagoCollection{

    function anticipos_para_pagos($anticipos){
        return $anticipos->map(function($anticipo){
            return [
                'id' => $anticipo->id,
                'nombre' => "{$anticipo->codigo} - {$anticipo->anticipable->nombre}",
                'proveedor_id' => $anticipo->anticipable_id,
                'pagables' => [
                    ['pagable_id' => $anticipo->id,
                    'pagable_type' => get_class($anticipo),
                    'monto_pagado' => 0,
                    'numero_documento' => $anticipo->codigo,
                    'fecha_emision' => $anticipo->fecha_anticipo,
                    'total' => $anticipo->monto,
                    'pagado' => $anticipo->pago_pagado,
                    'saldo' => $anticipo->pago_saldo]
                ]
            ];
        });
    }

}

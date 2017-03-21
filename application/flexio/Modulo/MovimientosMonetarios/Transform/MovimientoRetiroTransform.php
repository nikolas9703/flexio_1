<?php

namespace Flexio\Modulo\MovimientosMonetarios\Transform;

use Carbon\Carbon;

class MovimientoRetiroTransform
{
    protected $metodos_pago = [
        '17' => 'efectivo',
        '18' => 'credito_favor',
        '19' => 'cheque',
        '20' => 'tarjeta_credito',
        '21' => 'ach',
        '22' => 'transferencia_internacional'
    ];

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
                //i dont must forget payament details
            ]
        );
    }

    public function pago($retiro_dinero)
    {
        $monto = $retiro_dinero->items->sum('debito');
        return [
            'empezable_type' => 'movimiento_monetario',
            'empezable_id' => $retiro_dinero->id,
            'campo' => [
                "fecha_pago" => $retiro_dinero->present()->created_at,
                "proveedor_id" => $retiro_dinero->proveedor_id,
                "estado" => "por_aprobar",
                "monto_pagado" => $monto,
                "depositable_type" => "banco",
                "depositable_id" => $retiro_dinero->cuenta_id,
                "total_pagado" => $monto,
                "empresa_id" => $retiro_dinero->empresa_id,
                'empezable_type' => 'movimiento_monetario',
                'empezable_id' => $retiro_dinero->id,
                'formulario' => 'movimiento_monetario'
            ],
            'items' => [
                ['monto_pagado' => $monto, 'pagable_id' => $retiro_dinero->id, 'pagable_type' => get_class($retiro_dinero)]
            ],
            'metodo_pago' => [
                ["tipo_pago" => "efectivo", "total_pagado" => $monto, "referencia" => ["nombre_banco_ach" => "" , "cuenta_proveedor" => ""]]
            ]
        ];
    }
}

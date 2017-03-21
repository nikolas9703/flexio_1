<?php

namespace Flexio\Modulo\MovimientosMonetarios\Collection;

use Flexio\Collections\Collection;
use Flexio\Modulo\MovimientosMonetarios\Models\MovimientoRecibo;

class MovimientoReciboCollection extends Collection
{
    public function __construct(MovimientoRecibo $scope)
    {
        parent::__construct($scope);
    }

    public function ver()
    {
        $movimiento_monetario = $this->scope;
        return Collect(array_merge(
            $movimiento_monetario->toArray(),
            [
                'nombre' => $movimiento_monetario->narracion,
                'fecha_entrada' => $movimiento_monetario->present()->created_at,
                'transacciones' => $movimiento_monetario->items,
                'empezable' => [
                    'type' => count($movimiento_monetario->proveedor) ? 'proveedor' : 'cliente',
                    'id' => count($movimiento_monetario->proveedor) ? $movimiento_monetario->proveedor_id : $movimiento_monetario->cliente_id
                ],
                'landing_comments' => $movimiento_monetario->landing_comments
            ]
        ));
    }
}

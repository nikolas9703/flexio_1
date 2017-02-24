<?php

namespace Flexio\Modulo\FacturasCompras\Listeners;

class CreditoAplicado
{
    public function handle($event)
    {
        $event->actualizarEstado();
    }
}

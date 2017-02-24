<?php

namespace Flexio\Modulo\FacturasCompras\Listeners;

class UpdateInvoice
{
    public function handle($event)
    {
        $event->updateOperationState();
    }
}

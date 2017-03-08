<?php

namespace Flexio\Modulo\OrdenesCompra\Listeners;

class UpdatePedido
{
    public function handle($event)
    {
        $event->updateOperationState();
    }
}

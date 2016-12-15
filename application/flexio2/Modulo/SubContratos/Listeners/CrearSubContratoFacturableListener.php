<?php
namespace Flexio\Modulo\SubContratos\Listeners;

use Flexio\Modulo\SubContratos\Events\SubContratoFacturableEvent as SubContratoFacturableEvent;

class CrearSubContratoFacturableListener
{
    public function handle(SubContratoFacturableEvent $event)
    {
        $event->relacionSubContrato();
    }
}

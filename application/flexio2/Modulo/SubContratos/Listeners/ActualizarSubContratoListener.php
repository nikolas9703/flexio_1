<?php
namespace Flexio\Modulo\SubContratos\Listeners;

use Flexio\Modulo\SubContratos\Events\ActualizarSubContratoMontoEvent as ActualizarSubContratoMontoEvent;

class ActualizarSubContratoListener
{
    public function handle(ActualizarSubContratoMontoEvent $event)
    {
        $event->actualizarSubContratoMonto();
    }
}

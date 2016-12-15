<?php
namespace Flexio\Modulo\Contratos\Listeners;
use Flexio\Modulo\Contratos\Events\ActualizarContratoMontoEvent as ActualizarContratoMontoEvent;

class ActualizarContratoListener{
  public function handle(ActualizarContratoMontoEvent $event)
    {
       $event->actualizarContratoMonto();
    }
}

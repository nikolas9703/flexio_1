<?php
namespace Flexio\Modulo\ContratosAlquiler\Listeners;
use Flexio\Modulo\ContratosAlquiler\Events\ActualizarContratoAlquilerMontoEvent;

class ActualizarContratoAlquilerListener{
  public function handle(ActualizarContratoAlquilerMontoEvent $event)
    {
       $event->actualizarContratoMonto();
    }
}

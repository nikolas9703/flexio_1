<?php
namespace Flexio\Modulo\ContratosAlquiler\Listeners;
use Flexio\Modulo\ContratosAlquiler\Events\ContratoAlquilerFacturableEvent as ContratoAlquilerFacturableEvent;

class CrearContratoAlquilerFacturableListener{
  public function handle(ContratoAlquilerFacturableEvent $event)
    {
       $event->relacionContrato();
    }
}

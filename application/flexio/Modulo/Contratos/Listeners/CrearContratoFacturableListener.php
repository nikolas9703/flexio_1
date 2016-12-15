<?php
namespace Flexio\Modulo\Contratos\Listeners;
use Flexio\Modulo\Contratos\Events\ContratoFacturableEvent as ContratoFacturableEvent;

class CrearContratoFacturableListener{
  public function handle(ContratoFacturableEvent $event)
    {
       $event->relacionContrato();
    }
}

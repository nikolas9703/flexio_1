<?php
namespace Flexio\Modulo\ContratosAlquiler\Listeners;
use Flexio\Modulo\ContratosAlquiler\Events\CambiarEstadoCargosContratoAlquilerEvent;

class CambiarEstadoCargosContratoAlquilerListener{
  public function handle(CambiarEstadoCargosContratoAlquilerEvent $event)
    {
       $event->actualizarEstadoCargosContrato();
    }
}

<?php
namespace Flexio\Modulo\Cajas\Listeners;
use Flexio\Modulo\Cajas\Events\ActualizarCajaSaldoEvent as ActualizarCajaSaldoEvent;

class ActualizarCajaListener{
  public function handle(ActualizarCajaSaldoEvent $event)
    {
       $event->actualizarCajaSaldo();
    }
}

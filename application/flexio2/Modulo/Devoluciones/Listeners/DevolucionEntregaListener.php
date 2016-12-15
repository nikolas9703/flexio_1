<?php
namespace Flexio\Modulo\Devoluciones\Listeners;
use Flexio\Modulo\Devoluciones\Events\DevolucionEntregaEvent as DevolucionEntregaEvent;

class DevolucionEntregaListener{
  public function handle(DevolucionEntregaEvent $event)
    {
       $event->hacerEntrada();
    }
}

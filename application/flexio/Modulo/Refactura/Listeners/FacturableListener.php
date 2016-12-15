<?php
namespace Flexio\Modulo\Refactura\Listeners;
use Flexio\Modulo\Refactura\Events\FacturableEvent as FacturableEvent;

class FacturableListener{
  public function handle(FacturableEvent $event)
    {
       $event->refacturar();
    }
}

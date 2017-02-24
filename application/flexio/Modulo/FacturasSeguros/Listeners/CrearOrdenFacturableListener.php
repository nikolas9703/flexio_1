<?php
namespace Flexio\Modulo\FacturasSeguros\Listeners;
use Flexio\Modulo\FacturasSeguros\Events\OrdenVentaFacturableEvent as OrdenVentaFacturableEvent;

class CrearOrdenFacturableListener{
  public function handle(OrdenVentaFacturableEvent $event)
    {
       $event->relacionOrdenVenta();
    }
}

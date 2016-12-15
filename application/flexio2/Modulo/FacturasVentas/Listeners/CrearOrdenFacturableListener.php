<?php
namespace Flexio\Modulo\FacturasVentas\Listeners;
use Flexio\Modulo\FacturasVentas\Events\OrdenVentaFacturableEvent as OrdenVentaFacturableEvent;

class CrearOrdenFacturableListener{
  public function handle(OrdenVentaFacturableEvent $event)
    {
       $event->relacionOrdenVenta();
    }
}

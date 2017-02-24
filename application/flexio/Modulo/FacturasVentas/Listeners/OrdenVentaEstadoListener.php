<?php
namespace Flexio\Modulo\FacturasVentas\Listeners;
use Flexio\Modulo\FacturasVentas\Events\CambiandoEstadoOrdenVenta;

class OrdenVentaEstadoListener{
  public function handle(CambiandoEstadoOrdenVenta $event)
    {
       $event->cambiarEstado();
    }
}

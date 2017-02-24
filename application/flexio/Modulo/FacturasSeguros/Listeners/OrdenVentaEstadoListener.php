<?php
namespace Flexio\Modulo\FacturasSeguros\Listeners;
use Flexio\Modulo\FacturasSeguros\Events\CambiandoEstadoOrdenVenta;

class OrdenVentaEstadoListener{
  public function handle(CambiandoEstadoOrdenVenta $event)
    {
       $event->cambiarEstado();
    }
}

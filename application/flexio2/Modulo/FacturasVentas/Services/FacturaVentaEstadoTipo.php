<?php
namespace Flexio\Modulo\FacturasVentas\Services;

abstract class FacturaVentaEstadoTipo
{
    abstract public function getValorSpan(FacturaVentaEstado $estado);
}

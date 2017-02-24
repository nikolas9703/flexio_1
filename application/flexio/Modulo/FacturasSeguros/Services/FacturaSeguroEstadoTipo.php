<?php
namespace Flexio\Modulo\FacturasSeguros\Services;

abstract class FacturaSeguroEstadoTipo
{
    abstract public function getValorSpan(FacturaSeguroEstado $estado);
}

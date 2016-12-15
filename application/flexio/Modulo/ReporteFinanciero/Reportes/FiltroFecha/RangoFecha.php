<?php
namespace Flexio\Modulo\ReporteFinanciero\Reportes\FiltroFecha;

abstract class RangoFecha
{
  abstract public function filtro($cantidad, $fecha);
}

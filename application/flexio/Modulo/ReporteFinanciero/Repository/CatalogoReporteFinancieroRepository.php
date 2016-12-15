<?php
namespace Flexio\Modulo\ReporteFinanciero\Repository;
use Flexio\Modulo\ReporteFinanciero\Models\CatalogoReporteFinanciero;
use Flexio\Modulo\Modulos\Models\Catalogos;
use Flexio\Library\Util\CatalogoYear;


class CatalogoReporteFinancieroRepository{

  function tipoReporte(){
    return CatalogoReporteFinanciero::reporte()->get();
  }

  function meses(){
      return Catalogos::activo()->meses()->get(['valor','etiqueta']);
  }

  function getYears(){
    return CatalogoYear::get();
  }
}

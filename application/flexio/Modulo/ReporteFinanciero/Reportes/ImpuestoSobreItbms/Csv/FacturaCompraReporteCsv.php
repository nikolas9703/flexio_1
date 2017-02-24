<?php
namespace Flexio\Modulo\ReporteFinanciero\Reportes\ImpuestoSobreItbms\Csv;
use Flexio\Library\Util\FormatoMoneda;
class FacturaCompraReporteCsv{

  function csv($datos,$csv){

    $resumen = $datos['resumen'];

    $csv->insertOne(['Resumen de Cuenta']);
    $csv->insertOne(['Total facturado ',FormatoMoneda::numero($resumen['total_facturado'])]);
    $csv->insertOne(['Total ITBMS ',FormatoMoneda::numero($resumen['total_itbms'])]);
    $csv->insertOne(['Total retenido ',FormatoMoneda::numero($resumen['total_retenido'])]);
  }
}

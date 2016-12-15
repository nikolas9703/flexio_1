<?php
namespace Flexio\Modulo\ReporteFinanciero\Reportes\EstadoCuentaProveedor\Csv;
use Flexio\Library\Util\FormatoMoneda;
class EstadoCuentaProveedorCsv{

  function csv($datos,$csv){

    $resumen = $datos['resumen'];

    $csv->insertOne(['Resumen de Cuenta']);
    $csv->insertOne(['Balance inicial '.$datos['fecha_inicial'],FormatoMoneda::numero($resumen['balance_inicial'])]);
    $csv->insertOne(['Facturado',FormatoMoneda::numero($resumen['facturado'])]);
    $csv->insertOne(['Pagado',FormatoMoneda::numero($resumen['pagado'])]);
    $csv->insertOne(['Balance final '.$datos['fecha_final'], FormatoMoneda::numero($resumen['balance_final'])]);
    $csv->insertOne($csv->getNewline());
    $csv->insertOne($csv->getNewline());
    $csv->insertOne(['Fecha','Detalle','Monto','Balance']);
    $this->tabla_csv($csv,$datos['detalle']);
    $csv->insertOne($csv->getNewline());
    $csv->insertOne(['','','','Pagar Total']);
    $csv->insertOne(['','','', FormatoMoneda::numero($resumen['balance_final'])]);
  }

  function tabla_csv($csv, $datos){
    $i=0;
    $datos_csv= [];
    foreach($datos as $fila){

      $monto = FormatoMoneda::numero((float)$fila['total']);

       if(starts_with($fila['codigo'],'PGO')){
         $monto = FormatoMoneda::numero((float)$fila['total']);
       }

      $datos_csv[$i] = [$fila['created_at'],$fila['detalle'],$monto,FormatoMoneda::numero($fila['balance'])];
      $i++;
    }

    $csv->insertAll($datos_csv);


  }
}

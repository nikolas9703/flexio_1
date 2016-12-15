<?php
namespace Flexio\Modulo\ReporteFinanciero\Reportes;
use Flexio\Modulo\ReporteFinanciero\Reportes\ImpuestoSobreVentas\TotalesVentas;
use Flexio\Modulo\ReporteFinanciero\Reportes\ImpuestoSobreVentas\TotalesCompras;
use Flexio\Modulo\ReporteFinanciero\Reportes\ImpuestoSobreVentas\TotalesNotasCredito;
use Flexio\Modulo\ReporteFinanciero\Reportes\ImpuestoSobreVentas\TotalesNotasDebito;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\ReporteFinanciero\Formato\SumTotales;

class ImpuestosSobreVentas{
  
  function getReporte($datos_reporte){
      
      //dd($datos_reporte);
    $data=[];
    $year = $datos_reporte['year'];
    $mes  = $datos_reporte['mes'];

    $fecha = Carbon::createFromDate($year, $mes, $this->hoy())->endofMonth()->endOfDay()->toDateTimeString();

    //datas de ventas
    $dataVenta = new TotalesVentas;
    $ventas = $dataVenta->consulta($fecha);

    //datas de compras
    $dataCompra = new TotalesCompras;
    $compras = $dataCompra->consulta($fecha);

    //data de notas credito

    $dataNotaCredito = new TotalesNotasCredito;
    $notas_credito = $dataNotaCredito->consulta($fecha);

    //data de notas debito

    $dataNotaDebito = new TotalesNotasDebito;
    $notas_debito = $dataNotaDebito->consulta($fecha);

    //// obtener los kyes del los arrays para que hacer el match.
    $datos = ['ventas'=>$ventas, 'compras'=>$compras, 'notas_credito'=>$notas_credito, 'notas_debito'=>$notas_debito];
    $nuevoformato = $this->getKeyOfArrayForReport($datos);


    $ventas = array_merge($nuevoformato, $ventas);
    $compras = array_merge($nuevoformato, $compras);
    $notas_credito = array_merge($nuevoformato, $notas_credito);
    $notas_debito = array_merge($nuevoformato, $notas_debito);

    $ventas = (new SumTotales)->sumarAddTotal($ventas);
    $compras = (new SumTotales)->sumarAddTotal($compras);
    $notas_credito = (new SumTotales)->sumarAddTotal($notas_credito);
    $notas_debito = (new SumTotales)->sumarAddTotal($notas_debito);

    return ['ventas'=>$ventas, 'compras'=>$compras, 'notas_credito'=>$notas_credito, 'notas_debito'=>$notas_debito];
  }

  protected function hoy(){
     return Carbon::now()->day;
  }

  protected function getKeyOfArrayForReport($keys){

    $ventas = array_fill_keys(array_keys($keys['ventas']), 0);
    $compras = array_fill_keys(array_keys($keys['compras']), 0);


    $arrayFormat = $ventas + $compras;
    return $arrayFormat;
  }

}

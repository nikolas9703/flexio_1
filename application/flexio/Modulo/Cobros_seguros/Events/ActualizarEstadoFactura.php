<?php

namespace Flexio\Modulo\Cobros_seguros\Events;

use Flexio\Modulo\FacturasSeguros\Models\FacturaSeguro;

class ActualizarEstadoFactura{

  public static function manupilarEstado($facturaId){

    $facturas= FacturaSeguro::whereIn('id',$facturaId)->get();

    $facturas->each(function($factura){
      if($factura->total_facturado() < $factura->total){
        (new ActualizarEstadoFactura)->setEstadoFactura($factura,'cobrado_parcial');
      }else if($factura->total_facturado() == $factura->total){
       (new ActualizarEstadoFactura)->setEstadoFactura($factura);
      }
    });

  }

  public static function manupilarSaldo($facturaId){
		$facturas= FacturaSeguro::whereIn('id',$facturaId)->get();
		$facturas->each(function($factura){
			if($factura->total_facturado() < $factura->total){
				$saldo = $factura->total - $factura->total_facturado();
			}else{
				$saldo = 0;
			}
			(new ActualizarEstadoFactura)->setSaldoFactura($factura,$saldo);
		});
  }
  
  function setEstadoFactura($factura, $estado = 'cobrado_completo'){
    $factura->estado = $estado;
    $factura->save();
  }
  
  function setSaldoFactura($factura, $saldo){
    $factura->saldo = $saldo;
    $factura->save();
  }

}

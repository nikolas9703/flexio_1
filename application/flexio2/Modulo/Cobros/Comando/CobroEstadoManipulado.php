<?php
namespace Flexio\Modulo\Cobros\Comando;
use Flexio\Strategy\Transacciones\Anular\AnularTransaccion;
use Flexio\Modulo\Cobros\Transaccion\AnularTransaccionCobro;
use Flexio\Modulo\Cliente\Models\Cliente;
use Flexio\Modulo\FacturasVentas\Models\FacturaVenta;

class CobroEstadoManipulado{

  function anulado($cobro){
    $estado ='por_cobrar';
    $transaccion = new AnularTransaccion;
    $transaccion->anular($cobro, new AnularTransaccionCobro);

    $cobro->load('cobros_facturas','metodo_cobro');

    $facturas_relacion =[];
    foreach($cobro->cobros_facturas as $factura){
      $factura->transaccion = 0;
      $facturas_relacion[] = ['cobrable_id'=>$factura->cobrable_id,'monto_pagado'=>$factura->monto_pagado];
      $factura->save();
    }


    $this->actualizarEstadosFacturas($facturas_relacion);
    $this->actualizarCreditioCliente($cobro);
  }

  function actualizarEstadosFacturas($facturas_relacion){
    $collecion_facturas = collect($facturas_relacion);

    $reduceFacturas = $collecion_facturas->groupBy('cobrable_id')->map(function($a, $key){
    return ['cobrable_id' => $key, 'monto_pagado'=> $a->sum('monto_pagado') ];
    })->values();

    $reduceFacturas->map(function($fac){
      $factura = FacturaVenta::find($fac['cobrable_id']);
      if($factura->total_facturado() == 0){
        $factura->estado = 'por_cobrar';
      }else{
        $factura->estado = 'cobrado_parcial';
      }
      $factura->save();
    });

  }

  function actualizarCreditioCliente($cobro){
    $cliente_id = $cobro->cliente_id;

    $total = $cobro->metodo_cobro->where('tipo_pago', 'credito_favor')->sum('total_pagado');

    if($total > 0){
      $cliente = Cliente::find($cliente_id);
      $cliente->credito_favor = $cliente->credito_favor + $total;
      $cliente->save();
    }

  }

}

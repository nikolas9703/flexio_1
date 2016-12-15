<?php

namespace Flexio\Modulo\Cobros\Events;

use Flexio\Modulo\Cliente\Models\Cliente;

class ActualizarCreditoCliente{


  public static function actualizar($cobro){
      $cliente_id = $cobro->cliente_id;

      $total = $cobro->metodo_cobro->where('tipo_pago', 'credito_favor')->sum('total_pagado');

      if($total > 0){
        $cliente = Cliente::find($cliente_id);
        $cliente->credito_favor = $cliente->credito_favor - $total;
        $cliente->save();
      }

  }

  function si_aplica_credito($metodo_pago_post){

    return $metodo_pago_post->has('credito_favor');
  }
}

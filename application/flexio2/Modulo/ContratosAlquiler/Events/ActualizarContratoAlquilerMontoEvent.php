<?php
namespace Flexio\Modulo\ContratosAlquiler\Events;

class ActualizarContratoAlquilerMontoEvent{
  protected $adenda;
  protected $contrato_alquiler;
  function __construct($adenda, $contrato)
  {
    $this->adenda = $adenda;
    $this->contrato_alquiler = $contrato;
  }

  function actualizarContratoMonto(){
    $monto_acumulado = $this->adenda->monto_acumulado;
    $this->contrato_alquiler->monto_contrato =  $monto_acumulado;
    $this->contrato_alquiler->save();
  }
}

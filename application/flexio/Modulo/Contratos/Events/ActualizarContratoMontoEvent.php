<?php
namespace Flexio\Modulo\Contratos\Events;

class ActualizarContratoMontoEvent{
  protected $adenda;
  protected $contrato;
  function __construct($adenda, $contrato)
  {
    $this->adenda = $adenda;
    $this->contrato = $contrato;
  }

  function actualizarContratoMonto(){
    $monto_acumulado = $this->adenda->monto_acumulado;
    $this->contrato->monto_contrato =  $monto_acumulado;
    $this->contrato->save();
  }
}

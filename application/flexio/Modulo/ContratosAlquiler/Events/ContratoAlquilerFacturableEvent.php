<?php
namespace Flexio\Modulo\ContratosAlquiler\Events;

class ContratoAlquilerFacturableEvent{
  protected $factura;
  protected $contrato_alquiler;
  function __construct($modelFactura, $modelContratoAlquiler)
  {
    $this->factura = $modelFactura;
    $this->contrato_alquiler = $modelContratoAlquiler;
  }

  function relacionContrato(){
    //dd($this->factura->contrato_alquiler());
      $this->factura->contratos_alquiler()->save($this->contrato_alquiler, ['empresa_id'=> $this->contrato_alquiler->empresa_id]);
  }
}

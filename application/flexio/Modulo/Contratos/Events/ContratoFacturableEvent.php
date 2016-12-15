<?php
namespace Flexio\Modulo\Contratos\Events;

class ContratoFacturableEvent{
  protected $factura;
  protected $contrato;
  function __construct($modelFactura, $modelContrato)
  {
    $this->factura = $modelFactura;
    $this->contrato = $modelContrato;
  }

  function relacionContrato(){
      $this->factura->contratos()
      ->save($this->contrato,['empresa_id'=> $this->contrato->empresa_id]);
  }
}

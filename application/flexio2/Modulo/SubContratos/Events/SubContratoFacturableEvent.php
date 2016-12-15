<?php
namespace Flexio\Modulo\SubContratos\Events;

class SubContratoFacturableEvent
{
    protected $factura;
    protected $subcontrato;

    public function __construct($modelFactura, $modelSubContrato)
    {
        $this->factura = $modelFactura;
        $this->subcontrato = $modelSubContrato;
    }

    public function relacionSubContrato()
    {
        $this->factura->subcontratos()
        ->save($this->subcontrato, ['empresa_id' => $this->subcontrato->empresa_id]);
    }
}

<?php
namespace Flexio\Modulo\SubContratos\Events;

class ActualizarSubContratoMontoEvent
{
    protected $adenda;
    protected $subcontrato;

    public function __construct($adenda, $subcontrato)
    {
        $this->adenda = $adenda;
        $this->subcontrato = $subcontrato;
    }

    public function actualizarSubContratoMonto()
    {
        $monto_acumulado = $this->adenda->monto_acumulado;
        $this->subcontrato->monto_subcontrato =  $monto_acumulado;
        $this->subcontrato->save();
    }
}

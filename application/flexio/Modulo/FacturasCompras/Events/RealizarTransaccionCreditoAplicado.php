<?php

namespace Flexio\Modulo\FacturasCompras\Events;

use Flexio\Modulo\FacturasCompras\Transacciones\CreditoAplicadoTransaccion;
use Flexio\Modulo\CreditosAplicados\Models\CreditoAplicado;

class RealizarTransaccionCreditoAplicado
{
    public $credito_aplicado;

    public function __construct(CreditoAplicado $credito_aplicado)
    {
        $this->credito_aplicado = $credito_aplicado;
    }

    public function hacer()
    {
        $transaccion = new CreditoAplicadoTransaccion();
        $credito_aplicado = $this->credito_aplicado;
        $transaccion->hacerTransaccion($credito_aplicado);
    }
}

<?php

namespace Flexio\Modulo\NotaDebito\Events;

use Flexio\Modulo\NotaDebito\Models\NotaDebito;
use Flexio\Modulo\FacturasCompras\Models\FacturaCompra;

class NotaCreditoFacturaAnulada
{
    protected $notaCredito;
    function __construct($notaCredito){
        $this->notaCredito = $notaCredito;
    }

    function hacer(){
        $nota_debito = $this->notaCredito;
        $factura = FacturaCompra::find($nota_debito->factura->id);
        $factura->estado_id = (round($nota_debito->factura->monto, 2) == round($nota_debito->factura->saldo, 2)) ? 14 : 15;
        $factura->save();
        //pagada_parcial  15 | //16 pagada_completa
    }
}

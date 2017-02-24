<?php

namespace Flexio\Modulo\NotaDebito\Events;

use Flexio\Modulo\NotaDebito\Models\NotaDebito;
use Flexio\Modulo\FacturasCompras\Models\FacturaCompra;

class NotaCreditoFacturaAprobada
{
    protected $notaCredito;
    function __construct($notaCredito){
        $this->notaCredito = $notaCredito;
    }

    function hacer(){
        $nota_debito = $this->notaCredito;
        $factura = FacturaCompra::find($nota_debito->factura->id);
        $factura->estado_id = (round($nota_debito->total, 2) < round($nota_debito->factura->saldo, 2)) ? 15 : 16;
        $factura->save();
        //pagada_parcial  15 | //16 pagada_completa
    }
}

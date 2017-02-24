<?php

namespace Flexio\Modulo\NotaDebito\Events;

use Flexio\Modulo\NotaDebito\Models\NotaDebito;
use Flexio\Modulo\Proveedores\Models\Proveedores;

class NotaCreditoProveedorAnulada
{
    protected $notaCredito;
    function __construct($notaCredito){
        $this->notaCredito = $notaCredito;
    }

    function hacer(){
        $nota_debito = $this->notaCredito;
        $proveedor = Proveedores::find($nota_debito->proveedor_id);
        $proveedor->credito -= $nota_debito->total;
        $proveedor->save();
    }
}

<?php

namespace Flexio\Modulo\FacturasCompras\Events;

use Flexio\Modulo\FacturasCompras\Models\FacturaCompra;

class FacturaCompraEvents
{
    public $factura;
    public $operations = ['Ordenes_orm' => 'updateOrder', 'Flexio\Modulo\SubContratos\Models\SubContrato' => 'updateSubContrato'];

    public function __construct(FacturaCompra $factura)
    {
        $this->factura = $factura;
    }

    public function actualizarEstado()
    {
        $factura = $this->factura;
        if(round($this->factura->saldo, 2) == 0){
            $factura->estado_id = 16;//Full
        }else{
            $factura->estado_id = 15;//Partial
        }
        $factura->save();
    }

    public function updateOperationState()
    {
        $factura = $this->factura;
        $anulada = $factura->estado->etiqueta == 'anulada';
        call_user_func_array([$this, $this->operations[$factura->operacion_type]], [$factura, $anulada]);
    }

    private function updateOrder($factura, $anulada = false)
    {
        $factura->operacion->actualizarEstado($anulada);
    }

    private function updateSubContrato($factura, $anulada = false)
    {
        //no logic defined...
    }
}

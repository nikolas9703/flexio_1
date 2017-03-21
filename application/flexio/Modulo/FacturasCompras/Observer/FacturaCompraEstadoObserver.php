<?php

namespace Flexio\Modulo\FacturasCompras\Observer;

use Flexio\Modulo\FacturasCompras\Models\FacturaCompra;
use Flexio\Library\Util\FlexioSession;

class FacturaCompraEstadoObserver
{
    /**
     * para escuchar el evento del modelo.
     *
     * @param  object  $facturaCompra
     * @return void
     */
    public function updating(FacturaCompra $facturaCompra){

        $cambio = $facturaCompra->getDirty();

        if(isset($cambio['estado_id'])){
            $this->cambiosEstado($facturaCompra);
        }
    }

    protected function cambiosEstado($facturaCompra){
        $cambio = $facturaCompra->getDirty();
        $original = $facturaCompra->getOriginal();
        // estado anterior , el estado nuevo
        if($original["estado_id"] =="13" && $cambio['estado_id']=="14"){
            $this->por_aprobar_aprobado($facturaCompra);
        }

        if($original["estado_id"] =="14" && $cambio['estado_id']=="17"){
            $this->aprobado_anulado($facturaCompra);
        }

        if($original["estado_id"] =="14" && $cambio['estado_id']=="20"){
            $this->aprobado_anulado($facturaCompra);
        }


    }
    /**
     * transaccion de la nota de credito
     *
     * @param  object  $notaDebito
     * @return void
     */
    private function por_aprobar_aprobado($facturaCompra){
         $facturasComprasTransacciones =  new \Flexio\Modulo\FacturasCompras\Transacciones\FacturasComprasTransacciones;
         $facturasComprasTransacciones->haceTransaccion($facturaCompra);
    }

    /**
    * reversa los cambios de pasar una nota aprobado
    *
    * @param  object  $facturaCompra
    * @return void
    */

    private function aprobado_anulado($facturaCompra){

            $transaccionAnular = new \Flexio\Modulo\FacturasCompras\TransaccionesAnular\FacturasComprasAnular;
            $transaccionAnular->haceTransaccion($facturaCompra);
    }
}

<?php

namespace Flexio\Modulo\NotaDebito\Observers;

use Flexio\Modulo\NotaDebito\Models\NotaDebito;
use Flexio\Library\Util\FlexioSession;

class NotaCreditoEventos
{
    /**
     * para escuchar el evento del modelo.
     *
     * @param  object  $notaDebito
     * @return void
     */
    public function updating(NotaDebito $notaDebito){

        $cambio = $notaDebito->getDirty();

        if(isset($cambio['estado'])){
            $this->cambiosEstado($notaDebito);
        }
    }

    protected function cambiosEstado($notaDebito){
        $cambio = $notaDebito->getDirty();
        $original = $notaDebito->getOriginal();

        if($original["estado"] =="por_aprobar" && $cambio['estado']=="aprobado"){
            $this->por_aprobar_aprobado($notaDebito);
        }

        if($original["estado"] =="aprobado" && $cambio['estado']=="anulado"){
            $this->aprobado_anulado($notaDebito);
        }


    }
    /**
     * ejecuta las funcionalidades para pasar un nota a aprobado
     *
     * @param  object  $notaDebito
     * @return void
     */
    private function por_aprobar_aprobado($notaDebito){

        //eventos
        //
        // 1.1 realiza la transaccion
        // 1.2 actualiza estado factura o el saldo del proveedor
        $transaccionNotaDebito = new \Flexio\Modulo\NotaDebito\Transacciones\NotasDebitosFacturas;
        $transaccionNotaDebito->haceTransaccion($notaDebito);
        if(count($notaDebito->factura)){
            $notaAprobada = new \Flexio\Modulo\NotaDebito\Events\NotaCreditoFacturaAprobada($notaDebito);
            $notaAprobada->hacer();
        }else{
          $notaAprobada = new \Flexio\Modulo\NotaDebito\Events\NotaCreditoProveedorAprobada($notaDebito);
          $notaAprobada->hacer();
        }
    }

    /**
    * reversa los cambios de pasar una nota aprobado
    *
    * @param  object  $notaDebito
    * @return void
    */

    private function aprobado_anulado($notaDebito){

        $transaccionNotaDebito = new \Flexio\Modulo\NotaDebito\Transacciones\AnularTransaccionNotaDebito;
        $transaccionNotaDebito->deshacerTransaccion($notaDebito);
        if(count($notaDebito->factura)){
            $notaAnulada = new \Flexio\Modulo\NotaDebito\Events\NotaCreditoFacturaAnulada($notaDebito);
            $notaAnulada->hacer();
        }else{
          $notaAnulada = new \Flexio\Modulo\NotaDebito\Events\NotaCreditoProveedorAnulada($notaDebito);
          $notaAnulada->hacer();
        }
        //3.  de aprobado a anulado
        //2.1 elimina la transaccion
        //2.2 reversa el cambio en factura o en el saldo del proveedor
    }
}

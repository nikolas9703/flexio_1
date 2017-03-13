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
     * transaccion de la nota de credito
     *
     * @param  object  $notaDebito
     * @return void
     */
    private function por_aprobar_aprobado($notaDebito){

        //eventos
        //
        // 1.1 realiza la transaccion
        // 1.2 actualiza estado factura o el saldo del proveedor
        

        if(count($notaDebito->factura)){
            $transaccionNotaDebitoFactura = new \Flexio\Modulo\NotaDebito\Transacciones\NotaCreditoFacturaCompras;
            $transaccionNotaDebitoFactura->haceTransaccion($notaDebito);


            $notaAprobada = new \Flexio\Modulo\NotaDebito\Events\NotaCreditoFacturaAprobada($notaDebito);
            $notaAprobada->hacer();
        }else{

          $transaccionNotaDebitoProveedor = new \Flexio\Modulo\NotaDebito\Transacciones\NotaCreditoProveedor;
          $transaccionNotaDebitoProveedor->haceTransaccion($notaDebito);

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

        
        if(count($notaDebito->factura)){
            $transaccionNotaDebitoAnular = new \Flexio\Modulo\NotaDebito\Transacciones\AnularNotaCreditoFacturaCompra;
            $transaccionNotaDebitoAnular->haceTransaccion($notaDebito);

            $notaAnulada = new \Flexio\Modulo\NotaDebito\Events\NotaCreditoFacturaAnulada($notaDebito);
            $notaAnulada->hacer();
        }else{
          $transaccionNotaDebitoAnular = new \Flexio\Modulo\NotaDebito\Transacciones\AnularNotaCreditoProveedor;
          $transaccionNotaDebitoAnular->haceTransaccion($notaDebito);

          $notaAnulada = new \Flexio\Modulo\NotaDebito\Events\NotaCreditoProveedorAnulada($notaDebito);
          $notaAnulada->hacer();
        }
        //3.  de aprobado a anulado
        //2.1 elimina la transaccion
        //2.2 reversa el cambio en factura o en el saldo del proveedor
    }
}

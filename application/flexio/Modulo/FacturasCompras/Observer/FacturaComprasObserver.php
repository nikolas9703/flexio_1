<?php
/**
 * Created by PhpStorm.
 * User: Ivan Cubilla
 * Date: 6/1/17
 * Time: 3:08 PM
 */

namespace Flexio\Modulo\FacturasCompras\Observer;

use Flexio\Library\Util\FlexioSession;
use Flexio\Modulo\Historial\Models\Historial;
use Flexio\Modulo\FacturasCompras\Models\FacturaCompra;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\FacturasCompras\Models\FacturaCompraCatalogo as FacturaCompraCatalogo;

class FacturaComprasObserver
{
    public function created($facturaCompra = null)
    {
        $cambio = $facturaCompra->getDirty();
            if(isset($cambio['estado_id'])){
                //$facturaCompra->sendNotify($cambio['estado_id']);
            }
        $creado_por = FlexioSession::now();
        $created = [
            'antes' => $this->antes($facturaCompra),
            'despues' => $this->despues($facturaCompra),
            'usuario_id' => $creado_por->usuarioId(),
            'descripcion' => "Estado: Por aprobar",
            'titulo' => 'Se creó la factura de compra'
        ];
        $facturaCompra->historial()->save(new Historial($created));
    }

    public function updating($facturaCompra = null){

        $creado_por = FlexioSession::now();
        $cambio = $facturaCompra->getDirty();
            if(isset($cambio['estado_id'])){
                //$facturaCompra->sendNotify($cambio['estado_id']);
            }
        $updated = [
            'antes' => $this->antes($facturaCompra),
            'despues' => $this->despues($facturaCompra),
            'titulo' => 'Se actualizó la factura de compra',
            'usuario_id' => $creado_por->usuarioId(),
            'descripcion' => $this->descripcion($facturaCompra),
            'tipo'=> 'actualizado'
        ];

        $facturaCompra->historial()->save(new Historial($updated));
    }

    private function antes($facturaCompra){
        $data = $facturaCompra->fresh();
        return array_intersect_key($data->toArray(), $facturaCompra->getDirty());
    }

    private function despues($facturaCompra){
        return$facturaCompra->getDirty();
    }

    private function descripcion($facturaCompra){
        $cambio = $facturaCompra->getDirty();
        $original = $facturaCompra->getOriginal();
        $facturaCompraCatalogo = new FacturaCompraCatalogo();
        $estado_1 = $facturaCompraCatalogo->estado($original['estado_id']);
        $estado_2 = $facturaCompraCatalogo->estado($cambio['estado_id']);
        $descripcion = "<b style='color:#0080FF; font-size:15px;'>Cambio de estado</b></br></br>";
        $descripcion .= "Estado actual: ".$estado_2[0]->valor.'</br></br>';
        $descripcion .= "Estado anterior: ".$estado_1[0]->valor;
        return  $descripcion;
    }
}

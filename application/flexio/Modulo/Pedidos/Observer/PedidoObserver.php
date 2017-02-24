<?php

namespace Flexio\Modulo\Pedidos\Observer;

use Flexio\Modulo\Pedidos\Models\Pedidos;
use Flexio\Library\Util\FlexioSession;
use Flexio\Modulo\Historial\Models\Historial;


class PedidoObserver
{
    public $catalogo = [1=>'Por aprobar',2 => 'En cotizaci&oacute;n',3 => 'Parcial', 4 => 'Procesado',5=>'Anulado'];

    public function created(Pedidos $pedido)
    {

        $creado_por = FlexioSession::now();
        $created = [
            'antes' => $this->antes($pedido),
			'despues' => $this->despues($pedido),
            'usuario_id' => $creado_por->usuarioId(),
            'titulo' => 'Se creó el pedido',
            'descripcion' => ""
        ];
        $pedido->historial()->save(new Historial($created));
    }

    public function updating(Pedidos $pedido){

        $creado_por = FlexioSession::now();
        $value = $this->descripcion($pedido);
            $updated = [
                'antes' => $this->antes($pedido),
                'despues' => $this->despues($pedido),
                'usuario_id' => $creado_por->usuarioId(),
                'titulo' => is_array($value)?$value['titulo']:"",
                'tipo'=> 'actualizado',
                'descripcion' => is_array($value)?$value['descripcion']:""
            ];
        $pedido->historial()->save(new Historial($updated));
    }

    private function antes($pedido){
        $data = $pedido->fresh();
        return array_intersect_key($data->toArray(), $pedido->getDirty());
    }


    private function despues($pedido){
        return $pedido->getDirty();
    }

    private function descripcion($pedido){
        $cambio = $pedido->getDirty();
        $original = $pedido->getOriginal();
        $descripcion = "";
         if(isset($cambio['id_estado'])){
             $descripcion = [
                 'titulo' => 'Se actualizó el pedido',
                 'descripcion' => "Cambio de estado: ". $this->catalogo[$original['id_estado']]." a ".$pedido->estado->etiqueta
             ];
         }elseif(isset($cambio['validado']) && $cambio['validado'] == 'si'){
             $descripcion = [
                 'titulo' => 'Se ha validado el pedido',
                 'descripcion' => "Con estado: ". $this->catalogo[$original['id_estado']]
             ];
         }
         return  $descripcion;
    }



}

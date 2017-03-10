<?php

namespace Flexio\Modulo\OrdenesCompra\Events;

use Flexio\Modulo\OrdenesCompra\Models\OrdenesCompra;
use Flexio\Modulo\OrdenesCompra\Repository\OrdenesCompraRepository;
use Flexio\Modulo\Pedidos\Models\Pedidos;

class OrdenesCompraEvents
{
    public $orden;
    public $ordenesRep;
    public $operations = ['Flexio\Modulo\Pedidos\Models\Pedidos' => 'updatePedido', 'Flexio\Modulo\Entradas\Models\Entradas' => 'updateEntrada'];

    public function __construct(OrdenesCompra $orden)
    {
        $this->orden = $orden;
        $this->ordenesRep = new OrdenesCompraRepository();
    }

    public function actualizarEstado()
    {
        $orden = $this->orden;           
        if($orden->id_estado = 2 && $orden->pedido->comprable){
            $orden->pedido->id_estado = 4;
            $orden->pedido->save();            
        }       
    }

    public function updateOperationState()
    {
        $orden = $this->orden;
        $anulada = $orden->estado->etiqueta == 'anulada';        
        $orden->operacion_type = 'Flexio\Modulo\Pedidos\Models\Pedidos';
        ;
       // dd($orden->operacion_type);
        call_user_func_array([$this, $this->operations[$orden->operacion_type]], [$orden, $anulada]);
    }

    private function updatePedido($orden, $anulada = false)
    {
        $post['campo']['empresa_id'] = $orden->id_empresa;
        $this->ordenesRep->_createEntrada($orden, $post);
        $proceso = $this->actualizarEstado($anulada);        
    }
}

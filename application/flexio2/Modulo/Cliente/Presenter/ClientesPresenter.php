<?php

namespace Flexio\Modulo\Cliente\Presenter;
use Flexio\Presenter\Presenter;

class ClientesPresenter extends Presenter{
    protected $cliente;

    private $labelEstado = [


        'activo' =>'label-successful',
        'inactivo' =>'label-danger',
        'por_aprobar' =>'label-warning',
        'bloqueado' =>'label-dark',

    ];

    public function __construct($cliente) {
        $this->cliente = $cliente;
    }

    function estado_label() {
        //areglar este metodo para facturas compras
        if (is_null($this->cliente->estados_asignados)) {
            return '';
        }

        $color = '';
        if(array_key_exists($this->cliente->estado, $this->labelEstado)){
            $color = $this->labelEstado[$this->cliente->estado];
        }

        return '<label class="label '.$color.'">'.$this->cliente->estados_asignados->valor.'</label>';

    }
}
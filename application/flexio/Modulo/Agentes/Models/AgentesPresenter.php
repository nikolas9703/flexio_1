<?php

namespace Flexio\Modulo\Agentes;
use Flexio\Presenter\Presenter;

class AgentesPresenter extends Presenter{
    protected $agentes;

    private $labelEstado = [
        'Activo' =>'label-successful',
        'Inactivo' =>'label-danger',
        'Por Aprobar' =>'label-warning',
    ];

    public function __construct($agentes) {
        $this->agentes = $agentes;
    }

    function estado_label() {
        //areglar este metodo para facturas compras
        if (is_null($this->agentes->estado)) {
            return '';
        }

        $color = '';
        if(array_key_exists($this->agentes->estado, $this->labelEstado)){
            $color = $this->labelEstado[$this->agentes->estado];
        }

        return '<label class="label '.$color.'">'.$this->agentes->estado.'</label>';

    }
}
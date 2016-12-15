<?php

namespace Flexio\Modulo\aseguradoras\Presenter;
use Flexio\Presenter\Presenter;

class AseguradorasPresenter extends Presenter{
    protected $aseguradoras;

    private $labelEstado = [
        'Activo' =>'label-successful',
        'Inactivo' =>'label-danger',
        'Por aprobar' =>'label-warning',
    ];

    public function __construct($aseguradoras) {
        $this->aseguradoras = $aseguradoras;
    }

    function estado_label() {
        //areglar este metodo para facturas compras
        if (is_null($this->aseguradoras->estado)) {
            return '';
        }

        $color = '';
        if(array_key_exists($this->aseguradoras->estado, $this->labelEstado)){
            $color = $this->labelEstado[$this->aseguradoras->estado];
        }

        return '<label class="label '.$color.'">'.$this->aseguradoras->estado.'</label>';

    }
}
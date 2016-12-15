<?php

namespace Flexio\Modulo\Ajustadores\Presenter;

use Flexio\Presenter\Presenter;

class AjustadoresPresenter extends Presenter {

    protected $ajustadores;
    private $labelEstado = [
        'Activo' => 'label-successful',
        'Inactivo' => 'label-danger',
        'Por aprobar' => 'label-warning',
    ];

    public function __construct($ajustadores) {
        $this->ajustadores = $ajustadores;
    }

    function estado_label() {
        //areglar este metodo para facturas compras
        if (is_null($this->ajustadores->estado)) {
            return '';
        }

        $color = '';
        if (array_key_exists($this->ajustadores->estado, $this->labelEstado)) {
            $color = $this->labelEstado[$this->ajustadores->estado];
        }

        return '<label class="label ' . $color . '">' . $this->ajustadores->estado . '</label>';
    }

}

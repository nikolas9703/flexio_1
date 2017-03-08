<?php

namespace Flexio\Modulo\Inventarios\Presenter;

use Flexio\Presenter\Presenter;

class DatoAdicionalPresenter extends Presenter
{
    protected $dato_adicional;

    private $labelEstado = [
        'inactivo' => 'label-danger',
        'activo' => 'label-successful',
    ];

    private $estadoValor = [
        'inactivo' => 'Inactivo',
        'activo' => 'Activo',
    ];

    public function __construct($dato_adicional)
    {
        $this->dato_adicional = $dato_adicional;
    }

    public function estado()
    {
        $color = '';
        if (array_key_exists($this->dato_adicional->estado, $this->labelEstado)) {
            $color = $this->labelEstado[$this->dato_adicional->estado];
        }
        return '<label data-id="'.$this->dato_adicional->id.'" class="label cambiar-estado-btn '.$color.'">'.$this->estadoValor[$this->dato_adicional->estado].'</label>';
    }

    public function requerido()
    {
        return $this->dato_adicional->requerido == 'si' ? 'S&iacute;' : 'No';
    }

    public function en_busqueda_avanzada()
    {
        return $this->dato_adicional->en_busqueda_avanzada == 'si' ? 'S&iacute;' : 'No';
    }
}

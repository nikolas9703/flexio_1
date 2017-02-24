<?php

namespace Flexio\Modulo\traslados\Presenter;

use Flexio\Presenter\Presenter;
use Flexio\Library\Util\FormatoMoneda;
use Carbon\Carbon;


class TrasladoPresenter extends Presenter
{

    protected $traslado;

    private $labelEstado = [
        '1' => 'label-warning',//por enviar
        '2' => 'label-warning',//en transito
        '3' => 'label-successful',//recibido
    ];


    public function __construct($traslado)
    {
        $this->traslado = $traslado;
    }

    public function fecha_entrega()
    {
        return $this->traslado->fecha_entrega != '0000-00-00 00:00:00' ? Carbon::createFromFormat("Y-m-d H:i:s", $this->traslado->fecha_entrega)->format('d-m-Y') : '';
    }

    public function estado_label()
    {
        if (is_null($this->traslado->estado)) {
            return '';
        }

        $color="";
        $cambiarEstado="";
        if(array_key_exists($this->traslado->id_estado, $this->labelEstado))
        {
            $color = $this->labelEstado[$this->traslado->id_estado];
        }

        return '<label id="'.$this->traslado->id.'" class="label '.$color.'">'.$this->traslado->estado->etiqueta.'</label>';
    }

}

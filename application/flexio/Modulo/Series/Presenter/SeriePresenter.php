<?php namespace Flexio\Modulo\Series\Presenter;

use Flexio\Presenter\Presenter;
use Flexio\Library\Util\FormatoMoneda;

class SeriePresenter extends Presenter {

    protected $serie;

    private $labelEstado = [
        'no_disponible'=>'label-warning',
        'disponible'=>'label-successful'
    ];


    public function __construct($serie) {
        $this->serie = $serie;
    }

    public function estado() {
        if (!count($this->serie->catalogo_estado)) {
            return '';
        }
        try{
            $color = $this->labelEstado[$this->serie->estado];
            return '<label class="label '.$color.'">'.$this->serie->catalogo_estado->valor.'</label>';
        }catch(\Exception $e){
            return '<label class="label">'.$this->serie->catalogo_estado->valor.'</label>';
        }
    }

    public function adquisicion()
    {
        if(is_numeric($this->serie->adquisicion))
        {
            return '<label class="label-outline outline-success" style="border: 2px solid #1A7BB9;color: #1A7BB9;">$' . FormatoMoneda::numero($this->serie->adquisicion) . '</label>';
        }

        return '0.00';
    }

    public function otros_costos()
    {
        if(is_numeric($this->serie->otros_costos))
        {
            return '<label class="label-outline outline-danger">$' . FormatoMoneda::numero($this->serie->otros_costos) . '</label>';
        }

        return '0.00';
    }

    public function valor_actual()
    {
        if(is_numeric($this->serie->valor_actual))
        {
            return '<label class="label-outline outline-info">$' . FormatoMoneda::numero($this->serie->valor_actual) . '</label>';
        }

        return '0.00';
    }



}

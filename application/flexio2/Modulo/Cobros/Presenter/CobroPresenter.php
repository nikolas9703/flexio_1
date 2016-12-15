<?php

namespace Flexio\Modulo\Cobros\Presenter;
use Flexio\Presenter\Presenter;
use Flexio\Library\Util\FormatoMoneda;

class CobroPresenter extends Presenter{

  protected $cobro;

  private $labelEstado = [
    'anulado'=>'label-dark',
    'aplicado'=>'label-successful'
  ];

  private $labelOutline = [
    'anulado' => 'outline-dark',
    'aplicado' => 'outline-success'
  ];

  public function __construct($cobro) {
    $this->cobro = $cobro;
  }


  function estado_label() {

    if (is_null($this->cobro->catalogo_estado)) {
      return '';
    }

    $color="";
    if(array_key_exists($this->cobro->estado, $this->labelEstado)){
       $color = $this->labelEstado[$this->cobro->estado];
    }

    return '<label class="label '.$color.'">'.$this->cobro->catalogo_estado->valor.'</label>';
  }

  function monto_pagado() {
   if(is_numeric($this->cobro->monto_pagado)) {
    $color="";

    if(array_key_exists($this->cobro->estado, $this->labelOutline)){
       $color = $this->labelOutline[$this->cobro->estado];
    }

    return '<label class="label-outline '.$color.'">$' . FormatoMoneda::numero($this->cobro->monto_pagado) . '</label>';
   }

   return '';
  }

  function metodo_pago(){

    if(is_null($this->cobro->metodo_cobro)){
      return '';
    }
    $tipos=[];
    foreach($this->cobro->metodo_cobro as $metodo){
      if(!is_null($metodo->catalogo_metodo_pago)){
        $tipos[] = $metodo->catalogo_metodo_pago->valor;
      }
    }

    return implode(",", $tipos);

  }



}

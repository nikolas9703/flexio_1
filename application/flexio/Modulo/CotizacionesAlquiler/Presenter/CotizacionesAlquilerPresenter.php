<?php

namespace Flexio\Modulo\CotizacionesAlquiler\Presenter;
use Flexio\Presenter\Presenter;
use Flexio\Library\Util\FormatoMoneda;

class CotizacionesAlquilerPresenter extends Presenter{

  protected $scope;

  protected $color_estado = [
       'por_aprobar'=>'label-warning',
       'aprobado'=>'label-info',
       'anulado'=>'label-dark',
       'ganado'=>'label-successful',
       'ganada'=>'label-successful',
       'perdido'=>'label-danger',
       'abierta'=>'label-plain'

   ];

  public function __construct($modelo) {
    $this->scope = $modelo;
  }


  function estado_label() {

    if (is_null($this->scope->etapa_catalogo)) {
      return '';
    }

    if(array_key_exists($this->scope->estado, $this->color_estado)){
       $color = $this->color_estado[$this->scope->estado];
       return '<label class="label '.$color.'">'.$this->scope->etapa_catalogo->valor.'</label>';
    }
  }



}

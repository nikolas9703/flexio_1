<?php

namespace Flexio\Modulo\Devoluciones\Presenter;
use Flexio\Presenter\Presenter;
use Flexio\Library\Util\FormatoMoneda;

class DevolucionPresenter extends Presenter{

  protected $devolucion;

  private $labelEstado = [
    'por_aprobar'=>'label-warning',
    'aprobada'=>'label-successful',
    'anulada' => 'label-dark'
  ];


  public function __construct($devolucion) {
    $this->devolucion = $devolucion;
  }


  function estado_label() {
    if (is_null($this->devolucion->etapa_catalogo)) {
      return '';
    }
    try{
      $color = $this->labelEstado[$this->devolucion->estado];
      return '<label class="label '.$color.'">'.$this->devolucion->etapa_catalogo->valor.'</label>';
    }catch(\Exception $e){
      return '<label class="label">'.$this->devolucion->etapa_catalogo->valor.'</label>';
    }
  }


 

}

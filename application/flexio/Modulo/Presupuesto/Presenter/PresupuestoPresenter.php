<?php

namespace Flexio\Modulo\Presupuesto\Presenter;
use Flexio\Presenter\Presenter;
use Flexio\Library\Util\FormatoMoneda;

class PresupuestoPresenter extends Presenter{

  protected $presupuesto;

  private $labelEstado = [
    'avance'=>'label-info',
    'periodo'=>'label-successful'
  ];

  private $labelOutline = [
    'anulada' => 'outline-dark',
    'aplicado' => 'outline-success'
  ];

  public function __construct($presupuesto) {
    $this->presupuesto = $presupuesto;
  }


  function estado_label() {
      $color = $this->labelEstado[$this->presupuesto->tipo];
      return '<label class="label '.$color.'">'.$this->presupuesto->tipo_formato.'</label>';
  }

}

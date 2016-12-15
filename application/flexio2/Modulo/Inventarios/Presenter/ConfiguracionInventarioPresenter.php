<?php

namespace Flexio\Modulo\Inventarios\Presenter;
use Flexio\Presenter\Presenter;
use Flexio\Library\Util\FormatoMoneda;

class ConfiguracionInventarioPresenter extends Presenter{

  protected $config;

  private $labelEstado = [
    1 =>'label-successful',
    2 =>'label-danger',
  ];

  private $labelRazonAjuste = [
    6 =>'label-successful',
    7 =>'label-danger',
  ];

  public function __construct($config) {
    $this->config = $config;
  }


  function estado_label() {
    try{
      $color = $this->labelEstado[$this->config->estado];
      $estado =  ($this->config->estado == 1)?'Activa':'Inactiva';
      return '<label class="label '.$color.'">'.$this->config->estadoReferencia->etiqueta.'</label>';
    }catch(\Exception $e){
      return '';
    }
  }

  function estado_ajuste(){
    try{
      $color = $this->labelRazonAjuste[$this->config->estado_id];
      return '<label class="label '.$color.'">'.$this->config->estado->etiqueta.'</label>';
    }catch(\Exception $e){
      return '';
    }
  }


}

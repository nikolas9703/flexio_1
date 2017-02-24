<?php

namespace Flexio\Modulo\ConfiguracionCompras\Presenter;
use Flexio\Presenter\Presenter;
use Flexio\Library\Util\FormatoMoneda;

class ConfiguracionCompraPresenter extends Presenter{

  protected $config;

  private $labelEstado = [
    1 =>'label-successful',
    0 =>'label-danger',
    19 =>'label-successful',
    20 =>'label-danger',
  ];

  public function __construct($config) {
    $this->config = $config;
  }


  function estado_label() {
      
    /*if (is_null($this->config->estado_cheque)) {
      return '';
    }*/
    try{
      $color = $this->labelEstado[$this->config->estado];
      $estado =  ($this->config->estado == 1)?'Activa':'Inactiva'; 
      return '<label class="label '.$color.'">'.$estado.'</label>';
    }catch(\Exception $e){
      return '';
    }
  }
    function estado_label_doc() {

        /*if (is_null($this->config->estado_cheque)) {
          return '';
        }*/
        try{
            $color = $this->labelEstado[$this->config->estado];
            $estado =  ($this->config->estado == 19)?'Activa':'Inactiva';
            return '<label class="label '.$color.'">'.$estado.'</label>';
        }catch(\Exception $e){
            return '';
        }
    }


}

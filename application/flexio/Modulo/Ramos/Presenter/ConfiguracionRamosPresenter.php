<?php 
namespace Flexio\Modulo\Ramo\Presenter;
use Flexio\Presenter\Presenter;
use Flexio\Library\Util\FormatoMoneda;

class ConfiguracionRamoPresenter extends Presenter{

  protected $config;

  private $labelEstado = [
    1 =>'label-successful',
    2 =>'label-danger',
  ];

  public function __construct($config) {
    $this->config = $config;
  }


  function estado_label() {
    try{
      $color = $this->labelEstado[$this->config->estado];
      $estado =  ($this->config->estado == 1)?'Habilitado':'Deshabilitado';
      return '<label class="label '.$color.'">'.$this->config->estadoReferencia->etiqueta.'</label>';
    }catch(\Exception $e){
      return '';
    }
  }


}

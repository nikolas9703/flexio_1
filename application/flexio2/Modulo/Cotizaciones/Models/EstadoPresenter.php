<?php
namespace Flexio\Modulo\Cotizaciones\Models;

class EstadoPresenter{

  protected $cotizacion;
  protected $color_estado = [
      'por_aprobar'=>'label-warning',
      'aprobado'=>'label-info',
      'anulado'=>'label-dark',
      'ganado'=>'label-successful',
      'ganada'=>'label-successful',
      'perdido'=>'label-danger',
      'abierta'=>'label-plain'

  ];

  function __construct($cotizacion){
    $this->cotizacion = $cotizacion;
  }

  function estado_label(){
    if (is_null($this->cotizacion->etapa_catalogo)) {
      return '';
    }
    $color = $this->color_estado[$this->cotizacion->etapa_catalogo->etiqueta];
    return '<label class="label '.$color.'">'.$this->cotizacion->etapa_catalogo->valor.'</label>';
  }

  public function __get($propiedad){
    if(method_exists($this, $propiedad)){
      return call_user_func([$this, $propiedad]);
    }
  }
}

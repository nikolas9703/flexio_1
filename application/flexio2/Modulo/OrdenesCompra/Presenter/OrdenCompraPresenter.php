<?php

namespace Flexio\Modulo\OrdenesCompra\Presenter;
use Flexio\Presenter\Presenter;
use Flexio\Library\Util\FormatoMoneda;

class OrdenCompraPresenter extends Presenter{

  protected $ordenCompra;

  private $labelEstado = [
    1 => 'label-warning',
    2 => 'label-danger',
    3 => 'label-warning',  
    4 => 'label-successful',
    5 => 'label-dark'
  ];

  private $labelMonto = [
    1 => 'outline-warning',
    2 => 'outline-danger',
    3 => 'outline-warning',  
    4 => 'outline-success',
    5 => 'outline-dark'
  ];


  public function __construct($ordenCompra) {
    $this->ordenCompra = $ordenCompra;
  }


  function estado_label() {
    if(is_null($this->ordenCompra->estado)){
        return '';
    }
    $color = $this->labelEstado[$this->ordenCompra->id_estado];
    return '<label class="label '.$color.'">'.$this->ordenCompra->estado->etiqueta.'</label>';
  }

  function monto() {
   if(is_null($this->ordenCompra->estado)){
        return '';
    }

    try{
     $color = $this->labelMonto[$this->ordenCompra->id_estado];
     return '<label class="label-outline '.$color.'">$' . FormatoMoneda::numero($this->ordenCompra->monto) . '</label>';
     }catch(\Exception $e){
      return '<label class="label-outline">$' . FormatoMoneda::numero($this->ordenCompra->monto) . '</label>';
    }
  }

 

}

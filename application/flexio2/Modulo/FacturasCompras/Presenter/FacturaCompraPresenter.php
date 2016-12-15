<?php

namespace Flexio\Modulo\FacturasCompras\Presenter;
use Flexio\Presenter\Presenter;
use Flexio\Library\Util\FormatoMoneda;

class FacturaCompraPresenter extends Presenter{

  protected $facturaCompra;

  private $labelEstado = [
    
    13 =>'label-warning',
    14 =>'label-warning',
    15 =>'label-warning',
    16 =>'label-successful',
    17 =>'label-dark',
    20 =>'label-danger',
  ];

  public function __construct($facturaCompra) {
    $this->facturaCompra = $facturaCompra;
  }


  function estado_label() {
      //areglar este metodo para facturas compras
    if (is_null($this->facturaCompra->estado)) {
      return '';
    }

    $color = '';
      if(array_key_exists($this->facturaCompra->estado_id, $this->labelEstado)){
          $color = $this->labelEstado[$this->facturaCompra->estado_id];
      }

      return '<label class="label '.$color.'">'.$this->facturaCompra->estado->valor.'</label>';

  }

  function total() {
   if(is_numeric($this->facturaCompra->total)) {
     return '<label class="label-outline outline-success">$' . FormatoMoneda::numero($this->facturaCompra->total) . '</label>';
   }   

   return '';
  }

  function saldo() {
    return '<label class="label-outline outline-danger">$' . FormatoMoneda::numero($this->facturaCompra->saldo) . '</label>';
  }

  function vendedor(){}

  function cliente(){}


}

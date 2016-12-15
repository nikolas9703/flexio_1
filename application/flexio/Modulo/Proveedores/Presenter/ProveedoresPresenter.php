<?php

namespace Flexio\Modulo\Proveedores\Presenter;
use Flexio\Presenter\Presenter;
use Flexio\Library\Util\FormatoMoneda;

class ProveedoresPresenter extends Presenter{

  protected $proveedor;

  private $labelEstado = [
    'activo'=>'label-successful',
    'inactivo'=>'label-successful',
    'por_aprobar' => 'label-warning'
  ];


  public function __construct($proveedor) {
    $this->proveedor = $proveedor;
  }


  function estado_label() {
    
  }

  function saldo_pendiente() {
   
  }

 

}

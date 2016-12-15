<?php

namespace Flexio\Modulo\Contratos\Presenter;
use Flexio\Presenter\Presenter;
use Flexio\Library\Util\FormatoMoneda;

class ContratoPresenter extends Presenter{

  protected $contrato;

  public function __construct($contrato){
    $this->contrato = $contrato;
  }


  function monto_original(){
    return '<label class="label-outline outline-info">$'.number_format($this->contrato->monto_original(), 2, '.', ',').'</label>';
  }

  function monto_adenda(){
    return '<label class="label-outline outline-info">$'.number_format($this->contrato->monto_adenda(), 2, '.', ',').'</label>';
  }

  function monto_contrato(){
    return '<label class="label-outline outline-info">$'.number_format($this->contrato->monto_contrato, 2, '.', ',').'</label>';
  }

  function por_facturar(){
    return '<label class="label-outline outline-danger">$'.number_format($this->contrato->por_facturar(), 2, '.', ',').'</label>';
  }

  function facturado(){
    return '<label class="label-outline outline-success">$'.number_format($this->contrato->facturas_habilitadas->sum('total'), 2, '.', ',').'</label>';
  }


}

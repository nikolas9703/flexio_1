<?php

namespace Flexio\Modulo\SubContratos\Presenter;
use Flexio\Presenter\Presenter;
use Flexio\Library\Util\FormatoMoneda;

class SubContratoPresenter extends Presenter{

  protected $subContrato;
  private $labelEstado = [
     'por_aprobar' => '#F0AD4E',
     'vigente'=>'#5CB85C',
     'terminado'=>'#5BC0DE',
     'anulado' => '#222222'
  ];

  public function __construct($subContrato){
    $this->Subcontrato = $subContrato;
  }

  public function estado()
  {
    return '<label class="label label-warning" style="background:'.$this->labelEstado[$this->Subcontrato->estado].'">'.$this->Subcontrato->catalogo_estado->valor.'</label>';
  }


  function monto_original(){
    return '<label class="label-outline outline-info">$'.number_format($this->Subcontrato->monto_original(), 2, '.', ',').'</label>';
  }

  function monto_adenda(){
    return '<label class="label-outline outline-info">$'.number_format($this->Subcontrato->monto_adenda(), 2, '.', ',').'</label>';
  }

  function monto_subcontrato(){
    return '<label class="label-outline outline-info">$'.number_format($this->Subcontrato->monto_subcontrato, 2, '.', ',').'</label>';
  }

  function por_facturar(){
    return '<label class="label-outline outline-danger">$'.number_format($this->Subcontrato->por_facturar(), 2, '.', ',').'</label>';
  }

  function facturado(){
    $subtotal = $this->Subcontrato->facturas_habilitadas->map(function($subtotal){
      return [
        'precio_subtotal' => $subtotal->facturas_items->sum(function($precio_subtotal){return $precio_subtotal->cantidad * $precio_subtotal->precio_unidad;})
      ];  
      });    
    return '<label class="label-outline outline-success">$'.number_format($subtotal->sum('precio_subtotal'), 2, '.', ',').'</label>';
  }


}

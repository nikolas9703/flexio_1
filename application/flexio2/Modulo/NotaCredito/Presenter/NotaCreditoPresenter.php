<?php

namespace Flexio\Modulo\NotaCredito\Presenter;
use Flexio\Presenter\Presenter;
use Flexio\Library\Util\FormatoMoneda;

class NotaCreditoPresenter extends Presenter{

  protected $notaCredito;

  private $labelEstado = [
    'anulado'=>'label-dark',
    'aprobado'=>'label-successful',
    'por_aprobar' => 'label-warning'
  ];


  public function __construct($notaCredito) {
    $this->notaCredito = $notaCredito;
  }


  function estado_label() {
    if (is_null($this->notaCredito->etapa_catalogo)) {
      return '';
    }
    try{
      $color = $this->labelEstado[$this->notaCredito->estado];
      return '<label class="label '.$color.'">'.$this->notaCredito->etapa_catalogo->valor.'</label>';
    }catch(\Exception $e){
      return '<label class="label">'.$this->notaCredito->etapa_catalogo->valor.'</label>';
    }
  }

  function saldo_pendiente() {
   if(is_numeric($this->notaCredito->total)) {
  
     return '<label class="label-outline outline-danger">$' . FormatoMoneda::numero($this->notaCredito->total) . '</label>';
    
   }   

   return '';
  }

 

}

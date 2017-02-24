<?php
namespace Flexio\Modulo\Cajas\Presenter;
use Flexio\Presenter\Presenter;
use Flexio\Library\Util\FormatoMoneda;


class TransferenciasPresenter extends Presenter{

  protected $transferencia;

   private $labelEstado = [
    'por_aprobar' => 'label-danger',
    'en_transito' => 'label-warning',
    'anulado'=>'label-dark',
    'aprobado'=>'label-successful',
    'realizado'=>'label-successful'
  ];

  private $labelOutline = [
    'por_aprobar' => 'outline-danger',
    'en_transito' => 'outline-warning',
    'anulado' => 'outline-dark',
    'aprobado' => 'outline-success',
    'realizado' => 'outline-success'
  ];
  public function __construct($transferencia) {
    $this->transferencia = $transferencia;
  }

   function estado_label() {

     if (is_null($this->transferencia->catalogo_estado)) {
      return '';
    }
       $color="";
       if(array_key_exists($this->transferencia->estado, $this->labelEstado)){
         $color = $this->labelEstado[$this->transferencia->estado];
       }
        return '<label id="'.$this->transferencia->id.'" class="label '.$color.'">'.$this->transferencia->catalogo_estado->valor.'</label>';
  }

  function monto() {
    $color="";
    if(array_key_exists($this->transferencia->estado, $this->labelOutline)){
     $color = $this->labelOutline[$this->transferencia->estado];
     }
     return '<label class="label-outline '.$color.'">$' . FormatoMoneda::numero($this->transferencia->monto) . '</label>';
  }

 }

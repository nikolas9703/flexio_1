<?php

namespace Flexio\Modulo\FacturasSeguros\Presenter;
use Flexio\Presenter\Presenter;
use Flexio\Library\Util\FormatoMoneda;

class FacturaSeguroPresenter extends Presenter{

  protected $FacturaSeguro;

  private $labelEstado = [
    'por_cobrar'=>'label-danger',
    'por_aprobar'=>'label-warning',
    'cobrado_parcial'=>'label-warning',
    'anulada'=>'label-dark',
    'cobrado_completo'=>'label-successful'
  ];

  public function __construct($FacturaSeguro) {
    $this->FacturaSeguro = $FacturaSeguro;
  }


  function estado_label() {
    if (is_null($this->FacturaSeguro->etapa_catalogo)) {
      return '';
    }
    try{
      $color = $this->labelEstado[$this->FacturaSeguro->estado];
      return '<label class="btn btn-xs btn-block '.$color.' cambioindividual0" style="color: white">'.$this->FacturaSeguro->etapa_catalogo->valor.'</label>';
    }catch(\Exception $e){
      return '';
    }
  }

  function total() {
   if(is_numeric($this->FacturaSeguro->total)) {
     return '<label class="label-outline outline-success">$' . FormatoMoneda::numero($this->FacturaSeguro->total) . '</label>';
   }

   return '';
  }

  function saldo_pendiente() {
      if ($this->FacturaSeguro->estado == 'anulada') {
          $saldo = 0;
      } else {
        $total = $this->FacturaSeguro->total;
        $cobrado = $this->FacturaSeguro->factura_cobros_aplicados()->sum('cob_cobro_facturas.monto_pagado');
        $notas_credito = $this->FacturaSeguro->nota_credito_aprobada()->sum('venta_nota_creditos.total');
        $saldo = $total - $cobrado - $notas_credito;
      }
    return '<label class="label-outline outline-danger">$' . FormatoMoneda::numero($saldo) . '</label>';
  }

  function vendedor(){}

  function cliente(){}


}

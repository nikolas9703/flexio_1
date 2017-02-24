<?php

namespace Flexio\Modulo\FacturasVentas\Presenter;
use Flexio\Presenter\Presenter;
use Flexio\Library\Util\FormatoMoneda;

class FacturaVentaPresenter extends Presenter{

  protected $facturaVenta;

  private $labelEstado = [
    'por_cobrar'=>'label-danger',
    'por_aprobar'=>'label-warning',
    'cobrado_parcial'=>'label-warning',
    'anulada'=>'label-dark',
    'cobrado_completo'=>'label-successful'
  ];

  public function __construct($facturaVenta) {
    $this->facturaVenta = $facturaVenta;
  }


  function estado_label() {
    if (is_null($this->facturaVenta->etapa_catalogo)) {
      return '';
    }
    try{
      $color = $this->labelEstado[$this->facturaVenta->estado];
      return '<label class="label '.$color.'">'.$this->facturaVenta->etapa_catalogo->valor.'</label>';
    }catch(\Exception $e){
      return '';
    }
  }

  function total() {
   if(is_numeric($this->facturaVenta->total)) {
     return '<label class="label-outline outline-success">$' . FormatoMoneda::numero($this->facturaVenta->total) . '</label>';
   }

   return '';
  }

  function saldo_pendiente() {
      if ($this->facturaVenta->estado == 'anulada') {
          $saldo = 0;
      } else {
        $total = $this->facturaVenta->total;
        $cobrado = $this->facturaVenta->factura_cobros_aplicados()->sum('cob_cobro_facturas.monto_pagado');
        $notas_credito = $this->facturaVenta->nota_credito_aprobada()->sum('venta_nota_creditos.total');
        $saldo = $total - $cobrado - $notas_credito;
      }
    return '<label class="label-outline outline-danger">$' . FormatoMoneda::numero($saldo) . '</label>';
  }

  function vendedor(){}

  function cliente(){}


}

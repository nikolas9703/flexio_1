<?php

namespace Flexio\Modulo\Pagos\Validators;

use Flexio\Modulo\Proveedores\Repository\ProveedoresRepository;
use Flexio\Modulo\Pagos\Transacciones\PagosProveedor as PagoTransaccionRepository;
//use Flexio\Modulo\ConfiguracionCompras\Repository\ChequesRepository;

class PagoValidator
{

  protected $ProveedoresRepository;
  protected $PagoTransaccionRepository;

  public function __construct()
  {
    $this->ProveedoresRepository = new ProveedoresRepository;
    $this->PagoTransaccionRepository = new PagoTransaccionRepository;
  }

  public function _sePuedeAplicarPago($pago) {

    $pagables = ($pago->formulario !== "planilla") ? $pago->facturas : $pago->planillas;
    if(count($pagables) > 0){
        foreach ($pagables as $pagable) {//facturas o planillas
            if (round($pagable->pivot->monto_pagado, 2) > round($pagable->saldo, 2)) {
                throw new \Exception('El monto pagado es mayor al saldo pendiente.');
            }
        }
    }
    return true;
  }

  private function _aplicarPago($pago, $post)
  {
    if ($pago->estado != $post["campo"]["estado"] && $post["campo"]["estado"] == "aplicado" && $this->_sePuedeAplicarPago($pago)) {

      if ($post["metodo_pago"][0]["tipo_pago"] == "aplicar_credito" and ! $this->ProveedoresRepository->restar_credito($post["campo"]["proveedor_id"], $post["campo"]["monto_pagado"], $pago)) {
        throw new \Exception('El cr&eacute;dito del proveedor es inferior al monto pagado.');
      }
      //transacciones de pagos
      $this->PagoTransaccionRepository->haceTransaccion($pago);
    }
  }

  private function _anularPago($pago, $post)
  {
    if ($pago->estado != $post["campo"]["estado"] && $post["campo"]["estado"] == "anulado") {

      if($post["metodo_pago"][0]["tipo_pago"] == "aplicar_credito")
      {
        $this->ProveedoresRepository->sumar_credito($post["campo"]["proveedor_id"], $post["campo"]["monto_pagado"]);
      }
      if($post["metodo_pago"][0]["tipo_pago"] == "cheque")
      {
          $cheques = \Flexio\Modulo\ConfiguracionCompras\Models\Cheques::where(['pago_id' => $pago->id])->get();
          if(count($cheques))
          {
              $cheques->each(function($cheque){
                $cheque->estado_id = 3;//anulado
                $cheque->save();
              });
          }
      }
      $this->PagoTransaccionRepository->deshaceTransaccion($pago);
    }
  }

  public function post_validate($post)
  {
    $pago = $post["campo"];

    if(!isset($pago["proveedor_id"]) || empty($pago["proveedor_id"])){throw new \Exception('No se puede obtener el proveedor asociado al pago (Proveedores/Detalle)');}
    if(!isset($pago["estado"]) || empty($pago["estado"])){throw new \Exception('No se puede determinar el estado del pago. Por favor intente de nuevo (Pagos/Detalle)');}

  }

  public function change_state_validate($pago, $post)
  {
    $this->_aplicarPago($pago, $post);
    $this->_anularPago($pago, $post);
  }

}

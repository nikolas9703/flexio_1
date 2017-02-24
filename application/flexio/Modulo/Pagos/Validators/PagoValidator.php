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

    public function _sePuedeAplicarPago($pago)
    {
        if($pago->formulario == 'retenido'){
            return $this->_sePuedeAplicarPagoRetenido($pago);
        }
        if($pago->formulario == 'movimiento_monetario'){
            $pagables = $pago->movimientos_monetarios;
        }else{
            $pagables = ($pago->formulario !== "planilla") ? $pago->facturas : $pago->planillas;
        }
        if(count($pagables) > 0){
            $error = "";
            foreach ($pagables as $pagable) {//facturas o planillas
                if (round($pagable->pivot->monto_pagado, 2) > round($pagable->saldo, 2)) {
                    throw new \Exception("El monto pagado es mayor al saldo pendiente al Nro. Documento {$pagable->codigo}.");
                }elseif ((round($pagable->pagos_todos_suma, 2) - round($pagable->pagos_aplicados_suma, 2)) > round($pagable->saldo, 2)) {
                    $error .= "Los pagos asociados al Nro. Documento {$pagable->codigo} son mayor que el saldo pendiente<br><br>";
                }
            }
            if(strlen($error) > 0){throw new \Exception($error);}
        }
        return true;
    }

    public function _sePuedeAplicarPagoRetenido($pago)
    {
        $pagables = $pago->facturas;
        if(count($pagables) > 0){
            $error = "";
            foreach ($pagables as $pagable) {//facturas o planillas
                if (round($pagable->pivot->monto_pagado, 2) > round($pagable->retenido_por_pagar, 2)) {
                    throw new \Exception('El monto pagado es mayor al retenido pendiente por pagar.');
                }elseif ((round($pagable->pagos_retenidos_todos_suma, 2) - round($pagable->retenido_pagado, 2)) > round($pagable->retenido_por_pagar, 2)) {
                    $error .= "Los pagos asociados al Nro. Documento {$pagable->codigo} son mayor que el retenido pendiente por pagar<br><br>";
                }
            }
            if(strlen($error) > 0){throw new \Exception($error);}
        }
        return true;
    }

  private function _aplicarPago($pago, $post)
  {
    $transferencia = [];
    if($pago->formulario == 'transferencia'){ //Nuevo codigo para transferencias desde caja
        $transferencia = $pago->transferencias->first();
     }

    //Si a un pago de tipo cheque se cambia de estado a un estado por aplicar este debe Generar un cheque estado por Imprimir
    if($pago->estado != $post["campo"]["estado"] && $post["campo"]["estado"] == "por_aplicar" && isset($pago->metodo_pago[0]->tipo_pago) && $pago->metodo_pago[0]->tipo_pago =='cheque'){

       $array_cheque = [];
       $array_cheque['monto']          = $pago->monto_pagado;
       $array_cheque['empresa_id']     = $pago->empresa_id;
       $array_cheque['pago_id']        = $pago->id;
       $array_cheque['fecha_cheque']   = date("Y-m-d H:i:s");
       $array_cheque['estado_id'] = 1;  //se crea el cheque por imprimir

       // No debe crear un cheque incompleto. requirimiento card -> 1073
       // comentado: @josecoder
       //
       //$newCheque = new \Flexio\Modulo\ConfiguracionCompras\Repository\ChequesRepository();
       //$newCheque->crear($array_cheque);
    }

    if ($pago->estado != $post["campo"]["estado"] && ($post["campo"]["estado"] == "aplicado" || $post["campo"]["estado"] == "creado") && $this->_sePuedeAplicarPago($pago)) {

       /*if ($pago->metodo_pago[0]->tipo_pago == "aplicar_credito" and ! $this->ProveedoresRepository->restar_credito($pago->proveedor_id, $post["campo"]["monto_pagado"], $pago)) {
        throw new \Exception('El cr&eacute;dito del proveedor es inferior al monto pagado.');
      }*/

      if($post["campo"]["estado"] == "aplicado"){
          if(count($transferencia)>0){
              $transferencia->estado = 'realizado';//Flujo  Card: 1436
              $transferencia->save();
         }
         //transacciones de pagos
         $this->PagoTransaccionRepository->haceTransaccion($pago);
      }

    }
  }

  private function _anularPago($pago, $post)
  {
    if ($pago->estado != $post["campo"]["estado"] && $post["campo"]["estado"] == "anulado") {

      /*if($post["metodo_pago"][0]["tipo_pago"] == "aplicar_credito")
      {
        $this->ProveedoresRepository->sumar_credito($post["campo"]["proveedor_id"], $post["campo"]["monto_pagado"]);
      }*/
      if($pago->metodo_pago[0]->tipo_pago == "cheque")
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

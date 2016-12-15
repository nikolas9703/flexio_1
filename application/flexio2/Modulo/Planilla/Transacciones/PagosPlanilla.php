<?php

namespace Flexio\Modulo\Planilla\Transacciones;

use Flexio\Repository\SysTransaccion\SysTransaccionRepository as SysTransaccionRepository;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable as AsientoContable;
use Flexio\Modulo\ConfiguracionContabilidad\Models\CuentaPlanilla;

use Illuminate\Database\Capsule\Manager as Capsule;


class PagosPlanilla {

    protected $SysTransaccionRepository;

    public function __construct() {
        $this->SysTransaccionRepository = new SysTransaccionRepository();
    }

    public function deshaceTransaccion($planilla, $colaborador_id)
    {
        $clause      = [
           "empresa_id"    => $planilla->empresa_id,
           "nombre"        => 'TransaccionPlanilla'.'-'.$planilla->codigo.'-'.$planilla->empresa_id,
       ];
         $transaccion = $this->SysTransaccionRepository->findBy($clause);
         if(count($transaccion))
        {
              Capsule::transaction(function() use($transaccion, $colaborador_id){
              $transacciones = $transaccion->transaccion();
              $transacciones->deColaborador($colaborador_id);
              $transacciones->delete();

             if(count($transaccion->transaccion()->get()) == 0)
               $transaccion->delete();

                if(is_null($transaccion)){throw new \Exception('No se pudo eliminar la transacción');}
            });
        }
    }

    public function haceTransaccion($planilla)
    {

         $clause      = [
            "empresa_id"    => $planilla->empresa_id,
            "nombre"        => 'TransaccionPlanilla'.'-'.$planilla->codigo.'-'.$planilla->empresa_id,
        ];
        $transaccion = $this->SysTransaccionRepository->findBy($clause);

        if(!count($transaccion))
        {
            $sysTransaccion         = new SysTransaccionRepository;
            $modeloSysTransaccion   = "";
            $infoSysTransaccion     = array(
                  'codigo'=>'Sys',
                  'nombre'=>$clause["nombre"],
                  'empresa_id'=>$planilla->empresa_id,
                  'linkable_id'=>$planilla->id,
                  'linkable_type'=> get_class($planilla)
            );
              Capsule::transaction(function() use($sysTransaccion, $modeloSysTransaccion, $infoSysTransaccion, $planilla){
                 $modeloSysTransaccion =  $sysTransaccion->create($infoSysTransaccion);
                 $modeloSysTransaccion->transaccion()->saveMany($this->transacciones($planilla));
                if(is_null($modeloSysTransaccion)){throw new \Exception('No se pudo hacer la transacción');}
            });

        }

    }


    public function transacciones($planilla = array())
    {
         return array_merge($this->_debito($planilla),$this->_credito($planilla));
    }


    private function _debito($planilla)
    {

        $cuenta_id  = $planilla->pasivo_id;
        $asientos   = [];
        foreach($planilla->colaboradores_pagadas as $pago)
        {
              if($pago->colaborador->tipo_salario == 'Hora'){
                if($pago->ingresos){
                 foreach($pago->ingresos as $ingreso)
                 {
                    if($ingreso->recargo_monto > 0){
                      $asientos[] = new AsientoContable([
                          'codigo'        => $planilla->codigo,
                          'nombre'        => $planilla->codigo. ' - '.$pago->colaborador->codigo.' - Recargo - '.$ingreso->detalle,
                          'debito'        => $ingreso->recargo_monto,
                          'cuenta_id'     => $ingreso->recargo_cuenta_id,//$deduccion->deduccion_info->cuenta_pasivo_id,
                          'empresa_id'    => $planilla->empresa_id,
                          'colaborador_id'    => $pago->colaborador->id
                      ]);
                    }

                   if($ingreso->beneficio_monto > 0){
                     $asientos[] = new AsientoContable([
                         'codigo'        => $planilla->codigo,
                         'nombre'        => $planilla->codigo. ' - '.$pago->colaborador->codigo.' - Beneficio - '.$ingreso->detalle,
                         'debito'        => $ingreso->beneficio_monto,
                         'cuenta_id'     => $ingreso->beneficio_cuenta_id,//$deduccion->deduccion_info->cuenta_pasivo_id,
                         'empresa_id'    => $planilla->empresa_id,
                         'colaborador_id'    => $pago->colaborador->id
                     ]);
                   }
                 }
               }
             }
            if($pago->salario_neto > 0){
              $asientos[] = new AsientoContable([
                  'codigo'        => $planilla->codigo,
                  'nombre'        => $planilla->codigo. ' - '.$pago->colaborador->codigo,
                  'debito'        => $pago->salario_neto,
                  'cuenta_id'     => $cuenta_id,
                  'empresa_id'    => $planilla->empresa_id,
                  'colaborador_id'    => $pago->colaborador->id
              ]);
            }


             if($pago->deducciones){
              foreach($pago->deducciones as $deduccion)
              {
                if( $deduccion->descuento > 0){
                  $asientos[] = new AsientoContable([
                      'codigo'        => $planilla->codigo,
                      'nombre'        => $planilla->codigo. ' - '.$pago->colaborador->codigo.' - '.$deduccion->nombre,
                      'debito'        => $deduccion->descuento,
                      'cuenta_id'     => $cuenta_id,//$deduccion->deduccion_info->cuenta_pasivo_id,
                      'empresa_id'    => $planilla->empresa_id,
                      'colaborador_id'    => $pago->colaborador->id
                  ]);
                }

              }
            }

            if($pago->acumulados){
             foreach($pago->acumulados as $acumulado)
             {
               if( $acumulado->acumulado_planilla > 0){
                 $asientos[] = new AsientoContable([
                     'codigo'        => $planilla->codigo,
                     'nombre'        => $planilla->codigo. ' - '.$pago->colaborador->codigo.' - '.$deduccion->nombre,
                     'debito'        => $acumulado->acumulado_planilla,
                     'cuenta_id'     => $cuenta_id,//$deduccion->deduccion_info->cuenta_pasivo_id,
                     'empresa_id'    => $planilla->empresa_id,
                     'colaborador_id'    => $pago->colaborador->id
                 ]);
               }

             }
           }

            if($pago->descuentos){
              foreach($pago->descuentos as $descuento)
              {
                if( $descuento->monto_ciclo > 0){
                  $asientos[] = new AsientoContable([
                     'codigo'        => $planilla->codigo,
                     'nombre'        => $planilla->codigo.' - '.$pago->colaborador->codigo.' - '.$descuento->codigo,
                     'debito'        => $descuento->monto_ciclo,
                     'cuenta_id'     => $cuenta_id,//$descuento->descuentos->plan_contable_id,
                     'empresa_id'    => $planilla->empresa_id,
                     'colaborador_id'    => $pago->colaborador->id
                 ]);
                }

              }
            }
        }

        return $asientos;
    }


    private function _credito($planilla){

      $cuenta   = $this->_getCuentaIdCredito($planilla);
        $asientos   = [];
      foreach($planilla->colaboradores_pagadas as $pago)
      {
            if($pago->colaborador->tipo_salario == 'Hora'){
              if($pago->ingresos){
               foreach($pago->ingresos as $ingreso)
               {
                   if( $ingreso->recargo_monto > 0){
                     $asientos[] = new AsientoContable([
                         'codigo'        => $planilla->codigo,
                         'nombre'        => $planilla->codigo. ' - '.$pago->colaborador->codigo.' - Recargo - '.$ingreso->detalle,
                         'credito'        => $ingreso->recargo_monto,
                         'cuenta_id'     => $cuenta->cuenta_id,//$deduccion->deduccion_info->cuenta_pasivo_id,
                         'empresa_id'    => $planilla->empresa_id,
                         'colaborador_id'    => $pago->colaborador->id
                     ]);
                   }

                 if($ingreso->beneficio_monto > 0){
                   $asientos[] = new AsientoContable([
                       'codigo'        => $planilla->codigo,
                       'nombre'        => $planilla->codigo. ' - '.$pago->colaborador->codigo.' - Beneficio - '.$ingreso->detalle,
                       'credito'        => $ingreso->beneficio_monto,
                       'cuenta_id'     => $cuenta->cuenta_id,//$deduccion->deduccion_info->cuenta_pasivo_id,
                       'empresa_id'    => $planilla->empresa_id,
                       'colaborador_id'    => $pago->colaborador->id
                   ]);
                 }
               }
             }
           }
            if($pago->salario_neto > 0){
              $asientos[] = new AsientoContable([
                  'codigo'        => $planilla->codigo,
                  'nombre'        => $planilla->codigo. ' - '.$pago->colaborador->codigo,
                  'credito'        => $pago->salario_neto,
                  'cuenta_id'     => $cuenta->cuenta_id,
                  'empresa_id'    => $planilla->empresa_id,
                  'colaborador_id'    => $pago->colaborador->id
              ]);
            }

          if($pago->deducciones){
            foreach($pago->deducciones as $deduccion)
            {
              if($deduccion->descuento > 0){
                $asientos[] = new AsientoContable([
                    'codigo'        => $planilla->codigo,
                    'nombre'        => $planilla->codigo. ' - '.$pago->colaborador->codigo.' - '.$deduccion->nombre,
                    'credito'        => $deduccion->descuento,
                    'cuenta_id'     => $deduccion->deduccion_info->cuenta_pasivo_id,
                    'empresa_id'    => $planilla->empresa_id,
                    'colaborador_id'    => $pago->colaborador->id
                ]);
              }

            }


          }
          if($pago->acumulados){
            foreach($pago->acumulados as $acumulado)
            {
                if($acumulado->acumulado_planilla > 0){
                    $asientos[] = new AsientoContable([
                       'codigo'        => $planilla->codigo,
                       'nombre'        => $planilla->codigo. ' - '.$pago->colaborador->codigo.' - '.$deduccion->nombre,
                       'credito'        => $acumulado->acumulado_planilla,
                       'cuenta_id'     => $acumulado->acumulado_info->cuenta_pasivo_id,
                       'empresa_id'    => $planilla->empresa_id,
                       'colaborador_id'    => $pago->colaborador->id
                    ]);
                }

            }
          }
           if($pago->descuentos){
            foreach($pago->descuentos as $descuento)
            {
                 if($descuento->monto_ciclo > 0){
                  $asientos[] = new AsientoContable([
                      'codigo'        => $planilla->codigo,
                      'nombre'        => $planilla->codigo. ' - '.$descuento->codigo,
                      'credito'        => $descuento->monto_ciclo,
                      'cuenta_id'     => $descuento->info_descuento->plan_contable_id,
                      'empresa_id'    => $planilla->empresa_id,
                      'colaborador_id'    => $pago->colaborador->id
                  ]);
                }

            }
          }
      }
       return $asientos;
    }


    private function _getCuentaIdCredito($planilla)
    {
         return CuentaPlanilla::where("empresa_id","=",$planilla->empresa_id)->first();
     }

}

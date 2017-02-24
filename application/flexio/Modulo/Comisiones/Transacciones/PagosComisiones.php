<?php

namespace Flexio\Modulo\Comisiones\Transacciones;

use Flexio\Repository\SysTransaccion\SysTransaccionRepository as SysTransaccionRepository;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable as AsientoContable;
//use Flexio\Modulo\ConfiguracionContabilidad\Models\CuentaPlanilla;

use Illuminate\Database\Capsule\Manager as Capsule;


class PagosComisiones {

    protected $SysTransaccionRepository;

    public function __construct() {
        $this->SysTransaccionRepository = new SysTransaccionRepository();
    }

    public function deshaceTransaccion($pagoextra, $colaborador_id)
    {
        $clause      = [
           "empresa_id"    => $pagoextra->empresa_id,
           "nombre"        => 'TransaccionPagoExtraordinario'.'-'.$pagoextra->numero.'-'.$pagoextra->empresa_id,
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

    public function haceTransaccion($pagoextra)
    {

          $clause      = [
            "empresa_id"    => $pagoextra->empresa_id,
            "nombre"        => 'TransaccionPagoExtraordinario'.'-'.$pagoextra->numero.'-'.$pagoextra->empresa_id,
        ];
        $transaccion = $this->SysTransaccionRepository->findBy($clause);

        if(!count($transaccion))
        {
            $sysTransaccion         = new SysTransaccionRepository;
            $modeloSysTransaccion   = "";
            $infoSysTransaccion     = array(
                  'codigo'=>'Sys',
                  'nombre'=>$clause["nombre"],
                  'empresa_id'=>$pagoextra->empresa_id,
                  'linkable_id'=>$pagoextra->id,
                  'linkable_type'=> get_class($pagoextra)
            );
              Capsule::transaction(function() use($sysTransaccion, $modeloSysTransaccion, $infoSysTransaccion, $pagoextra){
                 $modeloSysTransaccion =  $sysTransaccion->create($infoSysTransaccion);
                 $modeloSysTransaccion->transaccion()->saveMany($this->transacciones($pagoextra));
                if(is_null($modeloSysTransaccion)){throw new \Exception('No se pudo hacer la transacción');}
            });

        }

    }


    public function transacciones($pagoextra = array())
    {
         return array_merge($this->_debito($pagoextra),$this->_credito($pagoextra));
    }


    private function _debito($pagoextra)
    {

        $cuenta_id  = $pagoextra->cuenta_id_activo;
        $asientos   = [];
        foreach($pagoextra->colaboradores as $pago)
        {
             if(($pago->monto_neto ) > 0){
              $asientos[] = new AsientoContable([
                  'codigo'        => $pagoextra->numero,
                  'nombre'        => $pagoextra->numero. ' - Salario neto - '.$pago->colaborador->codigo,
                  'debito'        => $pago->monto_neto,
                  'cuenta_id'     => $cuenta_id,
                  'empresa_id'    => $pagoextra->empresa_id,
                  'colaborador_id'    => $pago->colaborador->id
              ]);
            }

              if($pago->deducciones_aplicados){
              foreach($pago->deducciones_aplicados as $deduccion)
              {
                 if( $deduccion->monto > 0){

                  $asientos[] = new AsientoContable([
                      'codigo'        => $pagoextra->numero,
                      'nombre'        => $pagoextra->numero. ' - '.$pago->colaborador->codigo.' - Deduccion - '.$deduccion->deduccion_dependiente[0]->deduccion_info->nombre,
                      'debito'        => $deduccion->monto,
                      'cuenta_id'     => $deduccion->deduccion_dependiente[0]->deduccion_info->cuenta_pasivo_id,
                      'empresa_id'    => $pagoextra->empresa_id,
                      'colaborador_id'    => $pago->colaborador->id
                  ]);
                }

              }
            }

            if($pagoextra->acumulados_aplicados){
             foreach($pagoextra->acumulados as $acumulado)
             {
               if( $acumulado->monto > 0){
                 $asientos[] = new AsientoContable([
                     'codigo'        => $pagoextra->numero,
                     'nombre'        => $pagoextra->numero. ' - '.$pago->colaborador->codigo.' - Acumulado - '.$acumulado->acumulado_dependiente[0]->acumulado_info->nombre,
                     'debito'        => $acumulado->monto,
                     'cuenta_id'     => $deduccion->acumulado_dependiente[0]->acumulado_info->cuenta_pasivo_id,
                     'empresa_id'    => $pagoextra->empresa_id,
                     'colaborador_id'    => $pago->colaborador->id
                 ]);
               }

             }
           }

        }

        return $asientos;
    }


    private function _credito($pagoextra){

      $cuenta_id  = $this->_getCuentaIdCredito();
      $asientos   = [];
      foreach($pagoextra->colaboradores as $pago)
      {
           if(($pago->monto_neto ) > 0){
            $asientos[] = new AsientoContable([
                'codigo'        => $pagoextra->numero,
                'nombre'        => $pagoextra->numero. ' - Monto neto - '.$pago->colaborador->codigo,
                'credito'        => $pago->monto_neto,
                'cuenta_id'     => $cuenta_id,
                'empresa_id'    => $pagoextra->empresa_id,
                'colaborador_id'    => $pago->colaborador->id
            ]);
          }

            if($pago->deducciones_aplicados){
            foreach($pago->deducciones_aplicados as $deduccion)
            {
               if( $deduccion->monto > 0){

                $asientos[] = new AsientoContable([
                    'codigo'        => $pagoextra->numero,
                    'nombre'        => $pagoextra->numero. ' - '.$pago->colaborador->codigo.' - Deduccion - '.$deduccion->deduccion_dependiente[0]->deduccion_info->nombre,
                    'credito'        => $deduccion->monto,
                    'cuenta_id'     => $cuenta_id,
                    'empresa_id'    => $pagoextra->empresa_id,
                    'colaborador_id'    => $pago->colaborador->id
                ]);
              }

            }
          }

          if($pagoextra->acumulados_aplicados){
           foreach($pagoextra->acumulados as $acumulado)
           {
             if( $acumulado->monto > 0){
               $asientos[] = new AsientoContable([
                   'codigo'        => $pagoextra->numero,
                   'nombre'        => $pagoextra->numero. ' - '.$pago->colaborador->codigo.' - Acumulado - '.$acumulado->acumulado_dependiente[0]->acumulado_info->nombre,
                   'credito'        => $acumulado->monto,
                   'cuenta_id'     => $cuenta_id,
                   'empresa_id'    => $pagoextra->empresa_id,
                   'colaborador_id'    => $pago->colaborador->id
               ]);
             }

           }
         }

      }

      return $asientos;
    }


    private function _getCuentaIdCredito()
    {
      return '1122';
        // return CuentaPlanilla::where("empresa_id","=",$planilla->empresa_id)->first();
     }

}

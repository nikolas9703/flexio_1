<?php

namespace Flexio\Modulo\Pagos\Transacciones;

use Flexio\Repository\SysTransaccion\SysTransaccionRepository as SysTransaccionRepository;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable as AsientoContable;
use Flexio\Modulo\Cajas\Models\Cajas;
use Illuminate\Database\Capsule\Manager as Capsule;


class PagosProveedor {
    
    protected $SysTransaccionRepository;

    public function __construct() {
        $this->SysTransaccionRepository = new SysTransaccionRepository();
    }

    public function haceTransaccion($pago)
    {
        $clause      = [
            "empresa_id"    => $pago->empresa_id,
            "nombre"        => 'TransaccionPago'.'-'.$pago->codigo.'-'.$pago->empresa_id,
        ];
        $transaccion = $this->SysTransaccionRepository->findBy($clause);

        if(!count($transaccion))
        {
            $sysTransaccion         = new SysTransaccionRepository;
            $modeloSysTransaccion   = "";
            $infoSysTransaccion     = array('codigo'=>'Sys','nombre'=>$clause["nombre"],'empresa_id'=>$pago->empresa_id,'linkable_id'=>$pago->id,'linkable_type'=> get_class($pago));

            Capsule::transaction(function() use($sysTransaccion, $modeloSysTransaccion, $infoSysTransaccion, $pago){
                $modeloSysTransaccion =  $sysTransaccion->create($infoSysTransaccion);
                $modeloSysTransaccion->transaccion()->saveMany($this->transacciones($pago));
                if(is_null($modeloSysTransaccion)){throw new \Exception('No se pudo hacer la transacción');}
            });

        }
            
    }
    
    public function deshaceTransaccion($pago)
    {
        $clause      = [
            "empresa_id"    => $pago->empresa_id,
            "nombre"        => 'TransaccionPago'.'-'.$pago->codigo.'-'.$pago->empresa_id,
        ];
        $transaccion = $this->SysTransaccionRepository->findBy($clause);

        if(count($transaccion))
        {
            Capsule::transaction(function() use($transaccion){
                $transaccion->transaccion()->delete();
                $transaccion->delete();
                if(is_null($transaccion)){throw new \Exception('No se pudo hacer la transacción');}
            });
        }
    }
    
    public function transacciones($pago)
    {
        return array_merge($this->_debito($pago),$this->_credito($pago));
    }


    private function _debito($pago)
    {
        
        $cuenta_id  = $this->_getCuentaIdDebito($pago);
        $asientos   = [];        
        foreach($pago->facturas as $factura)
        {
            $asientos[] = new AsientoContable([
                'codigo'        => $pago->codigo,
                'nombre'        => $pago->codigo. ' - '.$factura->codigo,
                'debito'        => $factura->pivot->monto_pagado,
                'cuenta_id'     => $cuenta_id,
                'empresa_id'    => $pago->empresa_id
            ]);
        }        
        
        return $asientos;
    }

    private function _credito($pago){
        
        $cuenta_id  = $this->_getCuentaIdCredito($pago);
        $asientos   = [];
        
        foreach($pago->facturas as $factura)
        {    
        $asientos[] = new AsientoContable([
            'codigo'        => $pago->codigo,
            'nombre'        => $pago->codigo. ' - '.$factura->codigo,
            'credito'       => $pago->monto_pagado,
            'cuenta_id'     => $cuenta_id,
            'empresa_id'    => $pago->empresa_id
        ]);
        }
        
        if($pago->depositable_type == "Flexio\Modulo\Cajas\Models\Cajas"){ 
        $cuenta_id = $pago->depositable_id;     
        $caja = Cajas::find($cuenta_id);
        $caja->saldo =  $caja->saldo - $pago->monto_pagado;        
        $caja->save();               
        }
          
        
        return $asientos;
    }
    
    private function _getCuentaIdDebito($pago)
    {
        return $pago->empresa->cuenta_por_pagar_proveedores->first()->cuenta_id;             
       
    }
    
    private function _getCuentaIdCredito($pago)
    {
        $cuenta_id = 0;
        if($pago->metodo_pago[0]->tipo_pago == 'aplicar_credito')
        {
            $cuenta_id = $pago->empresa->cuenta_abonar_proveedores->first()->cuenta_id;
        }
        elseif($pago->depositable_type == "Flexio\Modulo\Cajas\Models\Cajas"){        
            $cuenta_id = $pago->empresa->cuenta_caja_menuda->cuenta_id;            
        }
        else
        {
            $cuenta_id = $pago->depositable_id;
        }
        return $cuenta_id;
    }
    
}

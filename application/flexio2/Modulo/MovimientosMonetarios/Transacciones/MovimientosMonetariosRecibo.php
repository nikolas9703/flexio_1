<?php

namespace Flexio\Modulo\MovimientosMonetarios\Transacciones;

use Flexio\Repository\SysTransaccion\SysTransaccionRepository as SysTransaccionRepository;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable as AsientoContable;
use Illuminate\Database\Capsule\Manager as Capsule;

class MovimientosMonetariosRecibo {
    
    protected $SysTransaccionRepository;

    public function __construct() {
        $this->SysTransaccionRepository = new SysTransaccionRepository();
    }

    public function haceTransaccion($recibo_dinero)
    {
        
        $clause      = [
            "empresa_id"    => $recibo_dinero->empresa_id,
            "nombre"        => 'TransaccionReciboDinero'.'-'.$recibo_dinero->fresh()->codigo.'-'.$recibo_dinero->empresa_id,
        ];
        $transaccion = $this->SysTransaccionRepository->findBy($clause);

        if(!count($transaccion))
        {
            $sysTransaccion         = new SysTransaccionRepository;
            $modeloSysTransaccion   = "";
            $infoSysTransaccion     = array('codigo'=>'Sys','nombre'=>$clause["nombre"],'empresa_id'=>$recibo_dinero->empresa_id,'linkable_id'=>$recibo_dinero->id,'linkable_type'=> get_class($recibo_dinero));

            Capsule::transaction(function() use($sysTransaccion, $modeloSysTransaccion, $infoSysTransaccion, $recibo_dinero){
                $modeloSysTransaccion =  $sysTransaccion->create($infoSysTransaccion);
                $modeloSysTransaccion->transaccion()->saveMany($this->transacciones($recibo_dinero->fresh()));
                if(is_null($modeloSysTransaccion)){throw new \Exception('No se pudo hacer la transacción');}
            });

        }
         
    }
    
    public function deshaceTransaccion($recibo_dinero)
    {
        //...
    }
    
    public function transacciones($recibo_dinero)
    {
        return array_merge($this->_debito($recibo_dinero),$this->_credito($recibo_dinero));
    }


    private function _debito($recibo_dinero)
    {
        
        $cuenta_id  = $this->_getCuentaIdDebito($recibo_dinero);
        $asientos   = [];
        
        
        $asientos[] = new AsientoContable([
            'codigo'        => $recibo_dinero->codigo,
            'nombre'        => $recibo_dinero->codigo. ' - '.$recibo_dinero->cliente->nombre,
            'debito'        => $recibo_dinero->items->sum("credito"),
            'cuenta_id'     => $cuenta_id,
            'empresa_id'    => $recibo_dinero->empresa_id
        ]);
        
        return $asientos;
    }

    private function _credito($recibo_dinero){
        
        
        $asientos   = [];
        
        foreach ($recibo_dinero->items as $item)
        {
            $cuenta_id  = $this->_getCuentaIdCredito($item);
            $asientos[] = new AsientoContable([
                'codigo'        => $recibo_dinero->codigo,
                'nombre'        => $recibo_dinero->codigo. ' - '.$recibo_dinero->cliente->nombre,
                'credito'       => $item->credito,
                'cuenta_id'     => $cuenta_id,
                'empresa_id'    => $recibo_dinero->empresa_id
            ]);
        }
        
        return $asientos;
    }
    
    private function _getCuentaIdDebito($recibo_dinero)
    {
        return $recibo_dinero->cuenta_id;
    }
    
    private function _getCuentaIdCredito($item)
    {
        return $item->cuenta_id;
    }
    
}

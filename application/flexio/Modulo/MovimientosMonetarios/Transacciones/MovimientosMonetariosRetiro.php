<?php

namespace Flexio\Modulo\MovimientosMonetarios\Transacciones;

use Flexio\Repository\SysTransaccion\SysTransaccionRepository as SysTransaccionRepository;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable as AsientoContable;
use Illuminate\Database\Capsule\Manager as Capsule;

class MovimientosMonetariosRetiro {
    
    protected $SysTransaccionRepository;

    public function __construct() {
        $this->SysTransaccionRepository = new SysTransaccionRepository();
    }

    public function haceTransaccion($retiro_dinero)
    {
        
        $clause      = [
            "empresa_id"    => $retiro_dinero->empresa_id,
            "nombre"        => 'TransaccionReciboDinero'.'-'.$retiro_dinero->fresh()->codigo.'-'.$retiro_dinero->empresa_id,
        ];
        $transaccion = $this->SysTransaccionRepository->findBy($clause);

        if(!count($transaccion))
        {
            $sysTransaccion         = new SysTransaccionRepository;
            $modeloSysTransaccion   = "";
            $infoSysTransaccion     = array('codigo'=>'Sys','nombre'=>$clause["nombre"],'empresa_id'=>$retiro_dinero->empresa_id,'linkable_id'=>$retiro_dinero->id,'linkable_type'=> get_class($retiro_dinero));

            Capsule::transaction(function() use($sysTransaccion, $modeloSysTransaccion, $infoSysTransaccion, $retiro_dinero){
                $modeloSysTransaccion =  $sysTransaccion->create($infoSysTransaccion);
                $modeloSysTransaccion->transaccion()->saveMany($this->transacciones($retiro_dinero->fresh()));
                if(is_null($modeloSysTransaccion)){throw new \Exception('No se pudo hacer la transacción');}
            });

        }
         
    }
    
    public function deshaceTransaccion($retiro_dinero)
    {
        //...
    }
    
    public function transacciones($retiro_dinero)
    {
        return array_merge($this->_debito($retiro_dinero),$this->_credito($retiro_dinero));
    }


    private function _credito($retiro_dinero)
    {
        
        $cuenta_id  = $this->_getCuentaIdDebito($retiro_dinero);
        $asientos   = [];
        
        
        $asientos[] = new AsientoContable([
            'codigo'        => $retiro_dinero->codigo,
            'nombre'        => $retiro_dinero->codigo. ' - '.$retiro_dinero->cliente->nombre,
            'debito'        => $retiro_dinero->items->sum("credito"),
            'cuenta_id'     => $cuenta_id,
            'empresa_id'    => $retiro_dinero->empresa_id
        ]);
        
        return $asientos;
    }

    private function _debito($retiro_dinero){
        
        
        $asientos   = [];
        
        foreach ($retiro_dinero->items as $item)
        {
            $cuenta_id  = $this->_getCuentaIdCredito($item);
            $asientos[] = new AsientoContable([
                'codigo'        => $retiro_dinero->codigo,
                'nombre'        => $retiro_dinero->codigo. ' - '.$retiro_dinero->cliente->nombre,
                'credito'       => $item->credito,
                'cuenta_id'     => $cuenta_id,
                'empresa_id'    => $retiro_dinero->empresa_id
            ]);
        }
        
        return $asientos;
    }
    
    private function _getCuentaIdDebito($retiro_dinero)
    {
        return $retiro_dinero->cuenta_id;
    }
    
    private function _getCuentaIdCredito($item)
    {
        return $item->cuenta_id;
    }
    
}

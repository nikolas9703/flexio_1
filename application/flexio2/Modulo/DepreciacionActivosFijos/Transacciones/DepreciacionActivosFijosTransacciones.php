<?php

namespace Flexio\Modulo\DepreciacionActivosFijos\Transacciones;

use Flexio\Repository\SysTransaccion\SysTransaccionRepository as SysTransaccionRepository;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable as AsientoContable;
use Illuminate\Database\Capsule\Manager as Capsule;


class DepreciacionActivosFijosTransacciones {
    
    protected $SysTransaccionRepository;

    public function __construct() {
        $this->SysTransaccionRepository = new SysTransaccionRepository();
    }

    public function haceTransaccion($depreciacion_activo_fijo)
    {
        //dd($depreciacion_activo_fijo->toArray(), $depreciacion_activo_fijo->items->toArray());
        $clause      = [
            "empresa_id"    => $depreciacion_activo_fijo->empresa_id,
            "nombre"        => 'TransaccionDepreciacionActivoFijo'.'-'.$depreciacion_activo_fijo->codigo.'-'.$depreciacion_activo_fijo->empresa_id,
        ];
        $transaccion = $this->SysTransaccionRepository->findBy($clause);

        if(!count($transaccion))
        {
            $sysTransaccion         = new SysTransaccionRepository;
            $modeloSysTransaccion   = "";
            $infoSysTransaccion     = array('codigo'=>'Sys','nombre'=>$clause["nombre"],'empresa_id'=>$depreciacion_activo_fijo->empresa_id,'linkable_id'=>$depreciacion_activo_fijo->id,'linkable_type'=> get_class($depreciacion_activo_fijo));

            Capsule::transaction(function() use($sysTransaccion, $modeloSysTransaccion, $infoSysTransaccion, $depreciacion_activo_fijo){
                $modeloSysTransaccion =  $sysTransaccion->create($infoSysTransaccion);
                $modeloSysTransaccion->transaccion()->saveMany($this->transacciones($depreciacion_activo_fijo));
                if(is_null($modeloSysTransaccion)){throw new \Exception('No se pudo hacer la transacción');}
            });

        }
        
        //dd($depreciacion_activo_fijo->toArray());
          
    }
    
    public function deshaceTransaccion($depreciacion_activo_fijo)
    {
        //...
    }
    
    public function transacciones($depreciacion_activo_fijo)
    {
        return array_merge($this->_debito($depreciacion_activo_fijo),$this->_credito($depreciacion_activo_fijo));
    }
    
    
    private function _debito($depreciacion_activo_fijo)
    {
        $asientos   = [];
        
        //sumatoria agrupada por tipos de impuestos
        foreach($depreciacion_activo_fijo->items  as $item)
        {
            $cuenta = $this->_getCuentaDebito($item);
            $asientos[] = new AsientoContable([
                'codigo'        => $depreciacion_activo_fijo->codigo,
                'nombre'        => $depreciacion_activo_fijo->codigo. ' - '.$item->item->codigo,
                'debito'        => $item->monto_depreciado,
                'cuenta_id'     => $cuenta->id,
                'empresa_id'    => $depreciacion_activo_fijo->empresa_id
            ]);
        }
        
        return $asientos;
    }

    private function _credito($depreciacion_activo_fijo){
        
        $asientos   = [];
        
        foreach ($depreciacion_activo_fijo->items as $item)
        {
            $cuenta     = $this->_getCuentaIdCredito($item);
            $asientos[] = new AsientoContable([
                'codigo'        => $depreciacion_activo_fijo->codigo,
                'nombre'        => $depreciacion_activo_fijo->codigo. " - " .$item->item->codigo,
                'credito'       => $item->monto_depreciado,
                'cuenta_id'     => $cuenta->id,
                'empresa_id'    => $depreciacion_activo_fijo->empresa_id
            ]);
        }
        
        return $asientos;
    }
    
    private function _getCuentaDebito($item)
    {
        return $item->item->cuenta_costo;
    }
    
    private function _getCuentaIdCredito($item)
    {
        return $item->item->cuenta_activo;
    }
    
}

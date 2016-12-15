<?php

namespace Flexio\Modulo\Salidas\Transacciones;

use Flexio\Repository\SysTransaccion\SysTransaccionRepository as SysTransaccionRepository;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable as AsientoContable;
 
//repositorios
use Flexio\Modulo\Contabilidad\Repository\ImpuestosRepository;
use Flexio\Modulo\Contabilidad\Repository\CuentasRepository;

class Ajuste {
    
    protected $SysTransaccionRepository;
    protected $ImpuestosRepository;
    protected $CuentasRepository;

    public function __construct()
    {
        $this->SysTransaccionRepository = new SysTransaccionRepository();
        $this->ImpuestosRepository      = new ImpuestosRepository();
        $this->CuentasRepository        = new CuentasRepository();
    }
    
    public function _crearTransaccion($salida)
    {
    	$asiento = [];
     	if(!empty($salida->items)){
    		foreach($salida->items as $item){
     			$asiento[] = $this->transacciones($item, $salida);
    		}
    	}
    	
    	return $asiento;
    }
    
    public function transacciones($item, $salida)
    {
          return array_merge( $this->_debito($item, $salida),$this->_credito($item, $salida) );
    }
    
    //$item = $registro->operacion->items->find($itemNoEncontrado['id']);
    private function _credito($item, $salida){
    	 
     	$asientos   = [];
    
    	$asientos[] = new AsientoContable([
    			'codigo'        => $salida->numero,
    			'nombre'        => $salida->numero. ' - '.$item->codigo,
    			'credito'       => $item->costo_promedio*$item->pivot->cantidad,
    			'cuenta_id'     => $item->cuenta_activo->id,
    			'empresa_id'    => $salida->empresa_id
    			]);
     	return $asientos;
    }
    
    private function _debito($item, $salida)
    {
       
    	$asientos   = [];
    
    	$asientos[] = new AsientoContable([
    			'codigo'        => $salida->numero,
    			'nombre'        => $salida->numero. ' - '.$item->codigo,
    			'debito'        => $item->costo_promedio*$item->pivot->cantidad,
    			'cuenta_id'     =>  $item->cuenta_costo->id,
    			'empresa_id'    => $salida->empresa_id
    			]);
    	
    	return $asientos;
    }
     
}

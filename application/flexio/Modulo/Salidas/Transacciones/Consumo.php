<?php

namespace Flexio\Modulo\Salidas\Transacciones;

use Flexio\Repository\SysTransaccion\SysTransaccionRepository as SysTransaccionRepository;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable as AsientoContable;
 
//repositorios
use Flexio\Modulo\Contabilidad\Repository\ImpuestosRepository;
use Flexio\Modulo\Contabilidad\Repository\CuentasRepository;

class Consumo {
    
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
    
  	private function transacciones($item, $salida){
   		 return array_merge($this->debito($item, $salida),$this->credito($item, $salida));
  	}
  	
  	private function credito($item, $salida){
  	
  		$asientos   = [];
  	
  		$asientos[] = new AsientoContable([
  				'codigo'        => $item->codigo,
  				'nombre'        => $salida->numero. ' - '.$item->id,
  				'credito'       => $item->costo_promedio*$item->pivot->cantidad,
  				'cuenta_id'     => $item->cuenta_activo->id,
  				'empresa_id'    => $item->empresa_id
  				]);
  	
  		return $asientos;
  	}
  	
    public function debito($item, $salida)
    {
    	$asientos   = [];
        $asientos[] = new AsientoContable([
            'codigo'        => $item->codigo,
            'nombre'        => $ajustes->numero. ' - '.$item->id,
            'debito'        => $item->costo_promedio*$item->pivot->cantidad,
            'cuenta_id'     => $item->cuenta_costo->id,
            'empresa_id'    => $item->empresa_id
        ]);
        return $asientos;
    }
   
     
}

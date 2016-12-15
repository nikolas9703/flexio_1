<?php

namespace Flexio\Modulo\Ajustes\Transacciones;

use Flexio\Repository\SysTransaccion\SysTransaccionRepository as SysTransaccionRepository;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable as AsientoContable;

//repositorios
use Flexio\Modulo\Contabilidad\Repository\ImpuestosRepository;
use Flexio\Modulo\Contabilidad\Repository\CuentasRepository;

class Positivo {

    protected $SysTransaccionRepository;
    protected $ImpuestosRepository;
    protected $CuentasRepository;

    public function __construct()
    {
        $this->SysTransaccionRepository = new SysTransaccionRepository();
        $this->ImpuestosRepository      = new ImpuestosRepository();
        $this->CuentasRepository        = new CuentasRepository();
    }

    public function _crearTransaccion($ajustes)
    {
    	$asiento = [];
     	if(!empty($ajustes->items)){
    		foreach($ajustes->items as $item){
     			$asiento[] = $this->transacciones($item, $ajustes);
    		}
    	}

    	return $asiento;
    }

  	private function transacciones($item, $ajustes){

   		 return array_merge( $this->debito($item, $ajustes),$this->credito($item, $ajustes) );
  	}

    public function debito($item, $ajustes)
    {
      $asientos   = [];
      if(empty($item->cuenta_activo_id)){throw new \Exception("{$item->nombre} no tiene cuenta de activo seleccionada <a href=\"{$item->enlace}\">(Item/Datos generales)</a>");}

      $asientos[] = new AsientoContable([
         'codigo' => $item->codigo,
         'nombre' => $ajustes->numero. ' - '.$item->id,
         'debito' => $item->costo_promedio*$item->pivot->cantidad,
         'cuenta_id' => $item->cuenta_activo_id,
         'empresa_id' => $item->empresa_id
      ]);

      return $asientos;
    }

    private function credito($item, $ajustes)
    {
      $asientos = [];
      if(empty($item->pivot->cuenta_id)){throw new \Exception("{$item->nombre} no tiene cuenta de costo seleccionada <a href=\"#\">(Ajuste/Datos generales)</a>");}

     	$asientos[] = new AsientoContable([
    		'codigo' => $item->codigo,
    		'nombre' => $ajustes->numero. ' - '.$item->id,
    		'credito' =>  $item->costo_promedio*$item->pivot->cantidad,
    		'cuenta_id' => $item->pivot->cuenta_id,
    		'empresa_id' => $item->empresa_id
    	]);

    	return $asientos;
    }

}

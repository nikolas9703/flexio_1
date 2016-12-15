<?php
 
namespace Flexio\Modulo\Cajas\Transacciones;

use Flexio\Repository\SysTransaccion\SysTransaccionRepository as SysTransaccionRepository;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable as AsientoContable;
use Illuminate\Database\Capsule\Manager as Capsule;
 

class CajasTransacciones {
    
    protected $SysTransaccionRepository;
    //protected $out;
     
     public function __construct() {
     	
    	$this->SysTransaccionRepository = new SysTransaccionRepository();
    	//$this->out = new AsientoContable;
      }

    public function hacerTransaccion($transferencia, $numero)
    {
         
         $clause      = [
            "empresa_id"    => $transferencia->empresa_id,
            "nombre"        => 'TransaccionCaja'.'-'.$numero.'-'.$transferencia->empresa_id,
        ];
         
         
        $transaccion = $this->SysTransaccionRepository->findBy($clause);
        
        if(!count($transaccion))
        {
            $sysTransaccion         = new SysTransaccionRepository;
            $modeloSysTransaccion   = "";
            $infoSysTransaccion     = array('codigo'=>'Sys','nombre'=>$clause["nombre"],'empresa_id'=>$transferencia->empresa_id,'linkable_id'=>$transferencia->id,'linkable_type'=> get_class($transferencia));

            Capsule::transaction(function() use($sysTransaccion, $modeloSysTransaccion, $infoSysTransaccion, $transferencia, $numero){
             
                $modeloSysTransaccion =  $sysTransaccion->create($infoSysTransaccion);
      
                $modeloSysTransaccion->transaccion()->saveMany($this->transacciones($transferencia, $numero));
                
                 if(is_null($modeloSysTransaccion)){throw new \Exception('No se pudo hacer la transacción');}
            });
         }
 	 }
    
    
    public function transacciones($transferencia, $numero){
       	return array_merge($this->debito($transferencia, $numero),$this->credito($transferencia, $numero));
    }
     
    public function debito($transferencia, $numero)
    {
    	
       	$asientos   = [];
       	
    	$asientos[] = new AsientoContable([
    			'codigo'        => $transferencia->caja_id,
    			'nombre'        => $numero,
    			'debito'        => $transferencia->monto,
    			'cuenta_id'     => $transferencia->empresa->cuenta_caja_menuda->cuenta_id,
    			'empresa_id'    => $transferencia->empresa_id
    			]);
    	return $asientos;
    }
    public function credito($transferencia, $numero){
      	$asientos   = [];
     	$asientos[] = new AsientoContable([
    			'codigo'        => $transferencia->caja_id,
    			'nombre'        => $numero,
    			'credito'       => $transferencia->monto,
    			'cuenta_id'     => $transferencia->cuenta->id,
    			'empresa_id'    => $transferencia->empresa_id
    			]);
    	return $asientos;
    }
     
     

   
    
}
 
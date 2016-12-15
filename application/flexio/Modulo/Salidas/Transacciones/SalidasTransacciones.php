<?php
 
namespace Flexio\Modulo\Salidas\Transacciones;

use Flexio\Repository\SysTransaccion\SysTransaccionRepository as SysTransaccionRepository;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable as AsientoContable;
use Illuminate\Database\Capsule\Manager as Capsule;

  
class SalidasTransacciones {
    
    protected $SysTransaccionRepository;
    protected $ImpuestosRepositoy;

    public function __construct() {
        $this->SysTransaccionRepository = new SysTransaccionRepository();
     }

    public function hacerTransaccion($salida)
    {
      	$items = $salida->items;
    	 
         $clause      = [
            "empresa_id"    => $salida->empresa_id,
            "nombre"        => 'TransaccionSalida'.'-'.$salida->numero.'-'.$salida->empresa_id,
        ];
        $transaccion = $this->SysTransaccionRepository->findBy($clause);
		
        if(!count($transaccion))
        {
            $sysTransaccion         = new SysTransaccionRepository;
            $modeloSysTransaccion   = "";
            $infoSysTransaccion     = array('codigo'=>'Sys','nombre'=>$clause["nombre"],'empresa_id'=>$salida->empresa_id,'linkable_id'=>$salida->id,'linkable_type'=> get_class($salida));

            Capsule::transaction(function() use($sysTransaccion, $modeloSysTransaccion, $infoSysTransaccion, $salida){
                $modeloSysTransaccion =  $sysTransaccion->create($infoSysTransaccion);
                  
                //$asientos_array = $this->_crearTransaccion($salida);
                $asientos_array = $this->transaccionesItems($salida);
                 foreach($asientos_array as $asientos){
                	//$modeloSysTransaccion->transaccion()->saveMany($this->transacciones($items));
                	$modeloSysTransaccion->transaccion()->saveMany($asientos);
                 }
                
                 if(is_null($modeloSysTransaccion)){throw new \Exception('No se pudo hacer la transacción');}
            });

        }
        
     }
     
     private function transaccionesItems( $salida ){
 	     
     	$asientos = [];
      	if( $salida->operacion_type == 'Flexio\Modulo\Consumos\Models\Consumos') //Consumo
     	{
     		$className  = "Flexio\\Modulo\\Salidas\\Transacciones\\Consumo";
     		$asientos = ( new $className )->_crearTransaccion($salida);
     	}
     	else if(  $salida->operacion_type == 'Flexio\Modulo\Ajustes\Models\Ajustes') //Ajuste
     	{
     		$className  = "Flexio\\Modulo\\Salidas\\Transacciones\\Ajuste";
     		$asientos = ( new $className )->_crearTransaccion($salida);
     	}
     	 
     	return $asientos;
     }
     
}
 
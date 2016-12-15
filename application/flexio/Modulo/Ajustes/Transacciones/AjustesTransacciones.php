<?php
 
namespace Flexio\Modulo\Ajustes\Transacciones;

use Flexio\Repository\SysTransaccion\SysTransaccionRepository as SysTransaccionRepository;
use Illuminate\Database\Capsule\Manager as Capsule;
 

class AjustesTransacciones {
    
    protected $SysTransaccionRepository;
    protected $ImpuestosRepositoy;

    public function __construct() {
        $this->SysTransaccionRepository = new SysTransaccionRepository();
        //$this->ImpuestosRepositoy       = new ImpuestosRepository();
    }

    public function hacerTransaccion($ajuste)
    {
    	
     	$items = $ajuste->items;
 
         $clause      = [
            "empresa_id"    => $ajuste->empresa_id,
            "nombre"        => 'TransaccionAjuste'.'-'.$ajuste->numero.'-'.$ajuste->empresa_id,
        ];
        $transaccion = $this->SysTransaccionRepository->findBy($clause);

        if(!count($transaccion))
        {
            $sysTransaccion         = new SysTransaccionRepository;
            $modeloSysTransaccion   = "";
            $infoSysTransaccion     = array('codigo'=>'Sys','nombre'=>$clause["nombre"],'empresa_id'=>$ajuste->empresa_id,'linkable_id'=>$ajuste->id,'linkable_type'=> get_class($ajuste));

            Capsule::transaction(function() use($sysTransaccion, $modeloSysTransaccion, $infoSysTransaccion, $ajuste){
                $modeloSysTransaccion =  $sysTransaccion->create($infoSysTransaccion);
                
                $asientos_array = $this->transaccionesItems($ajuste);
                foreach($asientos_array as $asientos){
                	$modeloSysTransaccion->transaccion()->saveMany($asientos);
                	 
                }
                
                
                if(is_null($modeloSysTransaccion)){throw new \Exception('No se pudo hacer la transacción');}
            });

        }
        
            
    }
    
     private function transaccionesItems( $ajuste ){
    
    	if( $ajuste->tipo_ajuste_id == 2) //Positivo
    	{
     		$className  = "Flexio\\Modulo\\Ajustes\\Transacciones\\Positivo";
    		$asientos = ( new $className )->_crearTransaccion($ajuste);
     	}
    	else if( $ajuste->tipo_ajuste_id == 1) //Negativo
    	{
     		$className  = "Flexio\\Modulo\\Ajustes\\Transacciones\\Negativo";
    		$asientos = ( new $className )->_crearTransaccion($ajuste);
    	}
     
    	return $asientos;
    }
    
    
}
 
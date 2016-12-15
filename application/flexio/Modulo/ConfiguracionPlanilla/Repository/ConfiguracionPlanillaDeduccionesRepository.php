<?php
namespace Flexio\Modulo\ConfiguracionPlanilla\Repository;
use Illuminate\Database\Capsule\Manager as Capsule;

/*use Flexio\Modulo\ConfiguracionPlanilla\Models\ConfiguracionPlanillaLiquidacion;
use Flexio\Modulo\ConfiguracionPlanilla\Models\LiquidacionPagos;
use Flexio\Modulo\ConfiguracionPlanilla\Models\LiquidacionPagosDeduccion;*/
 

class ConfiguracionPlanillaDeduccionesRepository{

	
	
	/*function create($created) {
		
 		$liquidacion = ConfiguracionPlanillaLiquidacion::create($created['general']);
		$this->creandoPagos($liquidacion, array_merge($created['general'],$created['normales'])); 
		$this->creandoPagos($liquidacion, array_merge($created['general'],$created['acumulados'])); 
		 
		return $liquidacion;
	}
	
	function update($update) {
 
		
 		$liquidacion = 	ConfiguracionPlanillaLiquidacion::where('id', $update['general']['id'])->update([
	    				'tipo_liquidacion' 	=> $update['general']['tipo_liquidacion'],
	    				'estado_id'		 	=> $update['general']['estado_id'] 
	    ]);
 		
  		$objeto = $this->find($update['general']['id']);
 		$this->removiendoPagos($objeto);
 		$this->removiendoPagos($objeto);
 		$this->creandoPagos($objeto, array_merge($update['general'],$update['normales']));
 		$this->creandoPagos($objeto, array_merge($update['general'],$update['acumulados']));
		return $liquidacion;
	}
	 
	 

	//Elimina completamente la liquidacion
 	function eliminarLiquidacion($id)
	{
   		 $objeto = $this->find($id);
   		 $this->removiendoPagos($objeto);
   		 $result = $objeto->delete();
   		 return $result;
   		 
 	}
 	//Elimina completamente la fila de pagos
	function eliminarRegistroPago($id)
	{
		$objeto = LiquidacionPagos::find($id);
		$objeto->deducciones()->delete();
		$objeto->delete();
		
 		return $objeto;
	}
	
	
	
	function find($id)
	{
		return ConfiguracionPlanillaLiquidacion::find($id);
	}
	*/
 
	
}	
?>
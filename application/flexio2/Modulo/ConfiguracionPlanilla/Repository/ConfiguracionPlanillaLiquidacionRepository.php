<?php
namespace Flexio\Modulo\ConfiguracionPlanilla\Repository;
use Illuminate\Database\Capsule\Manager as Capsule;

use Flexio\Modulo\ConfiguracionPlanilla\Models\ConfiguracionPlanillaLiquidacion;
use Flexio\Modulo\ConfiguracionPlanilla\Models\LiquidacionPagos;
use Flexio\Modulo\ConfiguracionPlanilla\Models\LiquidacionPagosDeduccion;
 

class ConfiguracionPlanillaLiquidacionRepository{

	function create($created) {
		
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
	
	function removiendoPagos($objeto)
	{
 
  		$objeto->load("pagos", "pagos.deducciones");
		
 		foreach($objeto['pagos'] as $pago){
 			foreach($pago['deducciones'] as $deduccion){
 				 LiquidacionPagosDeduccion::where('id', '=', $deduccion->id)->delete();
 			}
 		}
		$objeto->pagos()->delete();
 			
 		return $objeto;
	}
	
	function creandoPagos($objeto, $filas)
	{
   		if(count($filas['pago']) > 0){
  			foreach($filas['pago'] as  $valor){
				
				$pago = new LiquidacionPagos;
				$pago->liquidacion_id  = $objeto->id;
				$pago->tipo_pago_id    = $valor['id'];
 				$pago->save();
				if(count($valor['deduccion'])>0){
					
					
					$deducciones = array();
					foreach($valor['deduccion'] as  $deduccion_valor){

						$deduccion 				= 	new LiquidacionPagosDeduccion;
						$deduccion->pago_id 	=	$pago->id;
						$deduccion->deduccion_id=	$deduccion_valor;
						$deduccion->save();
					}
 				}
   			}	
		}
 		 return $pago; 
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
	
	function find222($id)
	{
		return ConfiguracionPlanillaLiquidacion::find($id);
	}
	function findByTipoPago($id_pago){
		return ConfiguracionPlanillaLiquidacion::where('tipo_liquidacion',$id_pago)->first();
	}
 
 	function lista_totales($clause=array()){
		return ConfiguracionPlanillaLiquidacion::where(function($query) use($clause){
			$query->where('empresa_id','=',$clause['empresa_id']);
 		})->count();
	}
	 
	function separando_array($array = array())
	{
		$list = [];
		$pagos_aplicables = '';
		if(!empty( $array)){
			foreach( $array as $value){
				$list[] = $value["etiqueta"];
			}
		}
		$pagos  = implode(', ', $list);
		return $pagos;
	}
	
	
	public function pagos_aplicables($liquidacion)
	{
		$result = Capsule::table('pln_config_liquidaciones AS liq')
		->leftJoin('pln_config_liquidaciones_pagos AS pagos', 'pagos.liquidacion_id', '=', 'liq.id')
		->leftJoin('mod_catalogos AS cat', 'cat.id_cat', '=', 'pagos.tipo_pago_id')
		->leftJoin('pln_config_liquidaciones_pagos_deducciones AS ded', 'ded.pago_id', '=', 'pagos.id')
		->where('liq.id', $liquidacion)
		->where('cat.identificador', 'pagos_aplicables_liquidaciones')
		->get(array('pagos.id','pagos.tipo_pago_id','ded.deduccion_id', 'ded.pago_id'));
		 
			
		return $result;
	}
	 
	public function pagos_acumulados($liquidacion)
	{
		$result = Capsule::table('pln_config_liquidaciones AS liq')
		->leftJoin('pln_config_liquidaciones_pagos AS pagos', 'pagos.liquidacion_id', '=', 'liq.id')
		->leftJoin('mod_catalogos AS cat', 'cat.id_cat', '=', 'pagos.tipo_pago_id')
		->leftJoin('pln_config_liquidaciones_pagos_deducciones AS ded', 'ded.pago_id', '=', 'pagos.id')
		->where('liq.id', $liquidacion)
		->where('cat.identificador', 'pagos_aplicables_liquidaciones_acumulado')
		->get(array('pagos.id','pagos.tipo_pago_id','ded.deduccion_id', 'ded.pago_id'));
		 
		return $result;
	}
	
	public function listar($clause = array(), $sidx = NULL, $sord = NULL, $limit = NULL, $start = NULL) {
		$query = ConfiguracionPlanillaLiquidacion::with(array("empresa",'tipo_liquidacion','pagos_aplicables','pagos_acumulados'));
	
		//Si existen variables de limite
		if($clause!=NULL && !empty($clause) && is_array($clause))
		{
			foreach($clause AS $field => $value)
			{
				/*if($field == "nombre_centro"){
					continue;
				}*/
	
				//Verificar si el campo tiene el simbolo @ y removerselo.
				if(preg_match('/@/i', $field)){
					$field = str_replace("@", "", $field);
				}
	
				//verificar si valor es array
				if(is_array($value)){
					$query->where($field, $value[0], $value[1]);
				}else{
					$query->where($field, '=', $value);
				}
			}
		}
	
		if($sidx!=NULL && $sord!=NULL) $query->orderBy($sidx, $sord);
		if($limit!=NULL) $query->skip($start)->take($limit);
		return $query->get();
	}
	
}	
?>
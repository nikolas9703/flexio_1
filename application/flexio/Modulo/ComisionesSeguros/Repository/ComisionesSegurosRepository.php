<?php
namespace Flexio\Modulo\ComisionesSeguros\Repository;

use Flexio\Modulo\ComisionesSeguros\Models\ComisionesSeguros;
use Flexio\Modulo\ComisionesSeguros\Models\SegComisionesParticipacion;

class ComisionesSegurosRepository {
	
	public function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {	
	//filtros
        $comisiones = ComisionesSeguros::select('seg_comisiones.*','seg_aseguradoras.nombre as nom_aseguradora','cob_cobros.codigo as codigo_cobro','cob_cobros.uuid_cobro as uuid_cobro_seguro')->deEmpresa($clause["empresa_id"]); 
       
		if(isset($clause["fecha1"]) && $clause["fecha1"]!=NULL && !empty($clause["fecha1"])){
			//var_dump($clause["fecha1"]);
			$comisiones->whereRaw("DATE(fecha) >= '".$clause["fecha1"]."'");
		}
		
		if(isset($clause["fecha2"]) && $clause["fecha2"]!=NULL && !empty($clause["fecha2"])){
			//var_dump($clause["fecha1"]);
			$comisiones->whereRaw("DATE(fecha) <= '".$clause["fecha2"]."'");
		}
		
		$comisiones->join("seg_aseguradoras", "seg_comisiones.id_aseguradora", "=", "seg_aseguradoras.id");
		$comisiones->join("cob_cobros", "seg_comisiones.id_cobro", "=", "cob_cobros.id");
		
		unset($clause["empresa_id"]);
		unset($clause["fecha1"]);
		unset($clause["fecha2"]);
		if($clause!=NULL && !empty($clause) && is_array($clause))
        {
                foreach($clause AS $field => $value)
                {  
                        //verificar si valor es array
                        if(is_array($value)){
                                $comisiones->where($field, $value[0], $value[1]);
                        }else{
							if($field=='no_cobro')
							{
								$comisiones->where('cob_cobros.codigo', 'LIKE', '%'.$value.'%');
							}
							else{
								$comisiones->where($field, '=', $value);
							}
                        }
                }
        }
		
		if(preg_match("/(fecha)/i", $sidx))
		{
			$comisiones->orderByRaw('FIELD(seg_comisiones.estado,"por_liquidar","con_diferencia","liquidada")');
			$comisiones->orderBy("seg_comisiones.no_comision", 'desc');
		}
		
		//Si existen variables de orden
        if($sidx!=NULL && $sord!=NULL){
                if(!preg_match("/(cargo|departamento|centro_contable)/i", $sidx)){
						if($sidx=='aseguradora_id')
						{
							$comisiones->orderBy('seg_aseguradoras.nombre', $sord);
						}
						else if($sidx=='no_recibo')
						{
							$comisiones->orderBy('cob_cobros.codigo', $sord);
						}
						else if($sidx=='usuario_id')
						{
							$comisiones->orderBy('usuarios.nombre','usuarios.apellido', $sord);
						}
						else{
							$comisiones->orderBy($sidx, $sord);
						}
                }
        }   
		//Si existen variables de limite	
		
		return $comisiones->get();
	}
	
	public function exportar($clause){
		$comisiones = ComisionesSeguros::select('seg_comisiones.*','seg_aseguradoras.nombre as nom_aseguradora','cob_cobros.codigo as codigo_cobro','cob_cobros.uuid_cobro as uuid_cobro_seguro')
		->join("seg_aseguradoras", "seg_comisiones.id_aseguradora", "=", "seg_aseguradoras.id")
		->join("cob_cobros", "seg_comisiones.id_cobro", "=", "cob_cobros.id") 
		->whereIn('seg_comisiones.id',$clause['id'])
		->orderBy('seg_comisiones.fecha','DESC'); 
		
		return $comisiones->get();
       
	}
	
	public function consultarComisionesProcesar($remesa,$aseguradora,$ramos,$fecha1,$fecha2,$empresa,$no_recibo){
		if($remesa != NULL){

			$comisiones=ComisionesSeguros::where('id_empresa',$empresa)
			->leftJoin('cob_cobros','seg_comisiones.id_cobro','=','cob_cobros.id')
			->where('seg_comisiones.estado','por_liquidar');
			
			if(isset($fecha1) && $fecha1!=NULL && !empty($fecha1) && $fecha1!=""){
				$fecha1=date('Y-m-d', strtotime($fecha1));
				$comisiones->whereRaw("DATE(seg_comisiones.fecha) >= '".$fecha1."'");
			}
			if(isset($fecha2) && $fecha2!=NULL && !empty($fecha2) && $fecha2!=""){
				$fecha2=date('Y-m-d', strtotime($fecha2));
				$comisiones->whereRaw("DATE(seg_comisiones.fecha) <= '".$fecha2."'");
			}
			if(!in_array('todos',$ramos))
			{
				$comisiones->whereIn('seg_comisiones.id_ramo',$ramos);
			}
			$comisiones->where('seg_comisiones.id_remesa',$remesa);
			
			$comisiones->orWhere(function($query) use ($empresa,$fecha1,$fecha2,$ramos)
			{
				$query=ComisionesSeguros::where('id_empresa',$empresa)
				->where('seg_comisiones.estado','por_liquidar')
				->leftJoin("seg_comisiones_remesas", "seg_comisiones_remesas.id_comision", "=", "seg_comisiones.id");
			
				if(isset($fecha1) && $fecha1!=NULL && !empty($fecha1) && $fecha1!=""){
					$fecha1=date('Y-m-d', strtotime($fecha1));
					$query->whereRaw("DATE(seg_comisiones.fecha) >= '".$fecha1."'");
				}
				if(isset($fecha2) && $fecha2!=NULL && !empty($fecha2) && $fecha2!=""){
					$fecha2=date('Y-m-d', strtotime($fecha2));
					$query->whereRaw("DATE(seg_comisiones.fecha) <= '".$fecha2."'");
				}
				if(!in_array('todos',$ramos))
				{
					$query->whereIn('seg_comisiones.id_ramo',$ramos);
				}
				$query->whereNull('seg_comisiones_remesas.id_comision');
			});
			
			return $comisiones->select('seg_comisiones.*','cob_cobros.num_remesa_entrante as num_remesa_entrante');
		}else{

			$comisiones=ComisionesSeguros::where('id_empresa',$empresa)
			->leftJoin('cob_cobros','seg_comisiones.id_cobro','=','cob_cobros.id')
			->where('seg_comisiones.estado','por_liquidar')
			->where('seg_comisiones.id_aseguradora',$aseguradora);
			if($fecha1 != '' && $fecha2 != ''){
				$fecha1=date('Y-m-d', strtotime($fecha1));
				$fecha2=date('Y-m-d', strtotime($fecha2));
				$comisiones->whereRaw('DATE(cob_cobros.fecha_pago) >= "'.$fecha1.'" AND DATE(cob_cobros.fecha_pago) <= "'.$fecha2.'" ');
			}
			if(isset($ramos) && !in_array('todos',$ramos)){
				$comisiones->whereIn('seg_comisiones.id_ramo',$ramos);
			}
			if(isset($no_recibo) && !empty($no_recibo)){
				$comisiones->leftJoin("seg_comisiones_participacion", "seg_comisiones_participacion.comision_id", "=", "seg_comisiones.id")
				->where('seg_comisiones_participacion.no_recibo',$no_recibo);
			}	
			return $comisiones->select('seg_comisiones.*','cob_cobros.num_remesa_entrante as num_remesa_entrante');
		}
	}
	
	public function consultarComisionesLiquidada($remesa,$aseguradora,$ramos,$fecha1,$fecha2,$empresa,$no_recibo){
		if($remesa != NULL){
			$comisiones=ComisionesSeguros::leftJoin('cob_cobros','seg_comisiones.id_cobro','=','cob_cobros.id')->where('id_empresa',$empresa);
			
			if(isset($fecha1) && $fecha1!=NULL && !empty($fecha1) && $fecha1!=""){
				$fecha1=date('Y-m-d', strtotime($fecha1));
				$comisiones->whereRaw("DATE(seg_comisiones.fecha) >= '".$fecha1."'");
			}
			if(isset($fecha2) && $fecha2!=NULL && !empty($fecha2) && $fecha2!=""){
				$fecha2=date('Y-m-d', strtotime($fecha2));
				$comisiones->whereRaw("DATE(seg_comisiones.fecha) <= '".$fecha2."'");
			}
			if(!in_array('todos',$ramos))
			{
				$comisiones->whereIn('seg_comisiones.id_ramo',$ramos);
			}
			$comisiones->where('seg_comisiones.id_remesa',$remesa);
			
			return $comisiones->select('seg_comisiones.*','cob_cobros.num_remesa_entrante as num_remesa_entrante');
		}else{

			$comisiones=ComisionesSeguros::where('id_empresa',$empresa)
			->leftJoin('cob_cobros','seg_comisiones.id_cobro','=','cob_cobros.id')
			->where('seg_comisiones.id_aseguradora',$aseguradora);
			if($fecha1 != '' && $fecha2 != ''){
				$fecha1=date('Y-m-d', strtotime($fecha1));
				$fecha2=date('Y-m-d', strtotime($fecha2));
				$comisiones->whereRaw('DATE(cob_cobros.fecha_pago) >= "'.$fecha1.'" AND DATE(cob_cobros.fecha_pago) <= "'.$fecha2.'" ');
			}
			if(isset($ramos) && !in_array('todos',$ramos)){
				$comisiones->whereIn('seg_comisiones.id_ramo',$ramos);
			}
			if(isset($no_recibo) && !empty($no_recibo)){
				$comisiones->leftJoin("seg_comisiones_participacion", "seg_comisiones_participacion.comision_id", "=", "seg_comisiones.id")
				->where('seg_comisiones_participacion.no_recibo',$no_recibo);
			}	
			return $comisiones->select('seg_comisiones.*','cob_cobros.num_remesa_entrante as num_remesa_entrante');
		}
	}

	public function getComisionesAgentes($id_agente,$empresa,$fecha1,$fecha2)
	{
		$comisiones=SegComisionesParticipacion::select('seg_comisiones_participacion.*')->where('seg_comisiones.id_empresa',$empresa)->where('seg_comisiones_participacion.agente_id',$id_agente)
		->whereIn('seg_comisiones.estado',array('liquidada', 'pagada_parcial'))
		->whereNull('seg_comisiones_participacion.no_recibo')
		->leftJoin("seg_comisiones", "seg_comisiones.id", "=", "seg_comisiones_participacion.comision_id");

		//->whereNull('seg_honorarios_part.id_comision_part')
		//->leftJoin("seg_honorarios_part", "seg_honorarios_part.id_comision_part", "=", "seg_comisiones_participacion.comision_id");

		//var_dump($comisiones);
		if(isset($fecha1) && $fecha1!=NULL && !empty($fecha1) && $fecha1!=""){
			$fecha1=date('Y-m-d', strtotime($fecha1));
			$comisiones->whereRaw("DATE(fecha) >= '".$fecha1."'");
		}
		if(isset($fecha2) && $fecha2!=NULL && !empty($fecha2) && $fecha2!=""){
			$fecha2=date('Y-m-d', strtotime($fecha2));
			$comisiones->whereRaw("DATE(fecha) <= '".$fecha2."'");
		}
		
			
		return $comisiones->get();
	}
	
	public function getComisionesAgentesGuardadas($comision,$id_agente,$fecha1=NULL,$fecha2=NULL)
	{
		$comisiones=SegComisionesParticipacion::select('seg_comisiones_participacion.*')->where('seg_comisiones_participacion.agente_id',$id_agente)
		->leftJoin("seg_comisiones", "seg_comisiones.id", "=", "seg_comisiones_participacion.comision_id")
		->whereIn('comision_id',$comision);
		
		if(isset($fecha1) && $fecha1!=NULL && !empty($fecha1) && $fecha1!=""){
			$fecha1=date('Y-m-d', strtotime($fecha1));
			$comisiones->whereRaw("DATE(seg_comisiones.fecha) >= '".$fecha1."'");
		}
		if(isset($fecha2) && $fecha2!=NULL && !empty($fecha2) && $fecha2!=""){
			$fecha2=date('Y-m-d', strtotime($fecha2));
			$comisiones->whereRaw("DATE(seg_comisiones.fecha) <= '".$fecha2."'");
		}
		
		
		return $comisiones->get();
	}
}
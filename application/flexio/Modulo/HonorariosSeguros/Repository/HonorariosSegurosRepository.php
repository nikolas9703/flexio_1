<?php
namespace Flexio\Modulo\HonorariosSeguros\Repository;

use Flexio\Modulo\HonorariosSeguros\Models\HonorariosSeguros;
//use Flexio\Modulo\ComisionesSeguros\Models\SegComisionesParticipacion;

class HonorariosSegurosRepository {
	
	public function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {	
	//filtros
        $honorarios = HonorariosSeguros::select('seg_honorarios.*','agt_agentes.nombre as nom_agente','usuarios.nombre as nom_usuario', 'usuarios.apellido as ape_usuario')->deEmpresa($clause["empresa_id"])
		->join("agt_agentes", "agt_agentes.id", "=", "seg_honorarios.agente_id")
		->join("usuarios", "usuarios.id", "=", "seg_honorarios.usuario_id"); 		
       
		if(isset($clause["fecha1"]) && $clause["fecha1"]!=NULL && !empty($clause["fecha1"])){
			//var_dump($clause["fecha1"]);
			$honorarios->whereRaw("DATE(seg_honorarios.created_at) >= '".$clause["fecha1"]."'");
		}
		
		if(isset($clause["fecha2"]) && $clause["fecha2"]!=NULL && !empty($clause["fecha2"])){
			//var_dump($clause["fecha1"]);
			$honorarios->whereRaw("DATE(seg_honorarios.created_at) <= '".$clause["fecha2"]."'");
		}
		
		unset($clause["empresa_id"]);
		unset($clause["fecha1"]);
		unset($clause["fecha2"]);
		if($clause!=NULL && !empty($clause) && is_array($clause))
        {
                foreach($clause AS $field => $value)
                {  
                        //verificar si valor es array
                        if(is_array($value)){
                                $honorarios->where($field, $value[0], $value[1]);
                        }else{
							if($field=='no_cobro')
							{
								$honorarios->where('cob_cobros.codigo', 'LIKE', '%'.$value.'%');
							}
							else{
								$honorarios->where($field, '=', $value);
							}
                        }
                }
        }
		
		if(preg_match("/(fecha)/i", $sidx))
		{
			$honorarios->orderByRaw('FIELD(seg_honorarios.estado,"en_proceso","por_pagar","pagada")');
			$honorarios->orderBy("seg_honorarios.no_honorario", 'desc');
		}
		
		//Si existen variables de orden
        if($sidx!=NULL && $sord!=NULL){
			if(!preg_match("/(fecha|cargo|departamento|centro_contable)/i", $sidx)){
					if($sidx=='agente_id')
					{
						$honorarios->orderBy('agt_agentes.nombre', $sord);
					}
					else if($sidx=='usuario_id')
					{
						$honorarios->orderBy('usuarios.nombre', $sord)
						->orderBy('usuarios.apellido',$sord);
					}
					else{
						$honorarios->orderBy($sidx, $sord);
					}
			}
        }   
		//Si existen variables de limite	
		
		return $honorarios->get();
	}
	
	public function exportar($clause=array()) {	
	//filtros
        $honorarios = HonorariosSeguros::select('seg_honorarios.*','agt_agentes.nombre as nom_agente','usuarios.nombre as nom_usuario', 'usuarios.apellido as ape_usuario')
		->whereIn('seg_honorarios.id',$clause['id'])
		->join("agt_agentes", "agt_agentes.id", "=", "seg_honorarios.agente_id")
		->join("usuarios", "usuarios.id", "=", "seg_honorarios.usuario_id"); 		
         
		//Si existen variables de limite	
		
		return $honorarios->get();
	}
}
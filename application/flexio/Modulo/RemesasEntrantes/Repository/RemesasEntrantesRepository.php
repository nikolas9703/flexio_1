<?php
namespace Flexio\Modulo\RemesasEntrantes\Repository;

use Flexio\Modulo\RemesasEntrantes\Models\RemesasEntrantes;

class RemesasEntrantesRepository {
	
   public function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {	
	//filtros
        $remesas_entrantes = RemesasEntrantes::select('seg_remesas_entrantes.*','seg_aseguradoras.nombre as nom_aseguradora',
		'usuarios.nombre as nom_usuario','usuarios.apellido as ape_usuario')->deEmpresa($clause["empresa_id"]); 
       
		if(isset($clause["fecha1"]) && $clause["fecha1"]!=NULL && !empty($clause["fecha1"])){
			//var_dump($clause["fecha1"]);
			$remesas_entrantes->whereRaw("DATE(fecha) >= '".$clause["fecha1"]."'");
		}
		
		if(isset($clause["fecha2"]) && $clause["fecha2"]!=NULL && !empty($clause["fecha2"])){
			//var_dump($clause["fecha1"]);
			$remesas_entrantes->whereRaw("DATE(fecha) <= '".$clause["fecha2"]."'");
		}
		
		$remesas_entrantes->join("seg_aseguradoras", "seg_remesas_entrantes.aseguradora_id", "=", "seg_aseguradoras.id");
		$remesas_entrantes->join("usuarios", "seg_remesas_entrantes.usuario_id", "=", "usuarios.id");
		
		unset($clause["empresa_id"]);
		unset($clause["fecha1"]);
		unset($clause["fecha2"]);
		if($clause!=NULL && !empty($clause) && is_array($clause))
        {
                foreach($clause AS $field => $value)
                {  
                        //verificar si valor es array
                        if(is_array($value)){
                                $remesas_entrantes->where($field, $value[0], $value[1]);
                        }else{
                                $remesas_entrantes->where($field, '=', $value);
                        }
                }
        }
		
		//Si existen variables de orden
        if($sidx!=NULL && $sord!=NULL){
                if(!preg_match("/(cargo|departamento|centro_contable)/i", $sidx)){
						if($sidx=='aseguradora_id')
						{
							$remesas_entrantes->orderBy('seg_aseguradoras.nombre', $sord);
						}
						if($sidx=='usuario_id')
						{
							$remesas_entrantes->orderBy('usuarios.nombre','usuarios.apellido', $sord);
						}
						else{
							$remesas_entrantes->orderBy($sidx, $sord);
						}
                }
        }   
		//Si existen variables de limite	
		
		return $remesas_entrantes->get();
	}
	
	public function exportar($clause){
		$remesas_entrantes = RemesasEntrantes::select('seg_remesas_entrantes.*','seg_aseguradoras.nombre as nom_aseguradora',
		'usuarios.nombre as nom_usuario','usuarios.apellido as ape_usuario')
		->join("seg_aseguradoras", "seg_remesas_entrantes.aseguradora_id", "=", "seg_aseguradoras.id")
		->join("usuarios", "seg_remesas_entrantes.usuario_id", "=", "usuarios.id")
		->whereIn('seg_remesas_entrantes.id',$clause['id'])
		->orderBy('seg_remesas_entrantes.created_at','DESC'); 
		
		return $remesas_entrantes->get();
       
	}
	
	public static function findByUuid($uuid){
        return RemesasEntrantes::where('uuid_remesa_entrante',hex2bin($uuid))->first();
    }
	
}
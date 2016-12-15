<?php
namespace Flexio\Modulo\aseguradoras\Repository;

use Flexio\Modulo\aseguradoras\Models\Aseguradoras;

class AseguradorasRepository {
	
   public function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {	
	//filtros
        $aseguradoras = Aseguradoras::deEmpresa($clause["empresa_id"]); 
       
		//Si existen variables de orden
        if($sidx != 'estado'){
        $aseguradoras->orderBy('estado', 'ASC');
        }
		//Si existen variables de orden
        if($sidx!=NULL && $sord!=NULL){
                if(!preg_match("/(cargo|departamento|centro_contable)/i", $sidx)){
                        $query->orderBy($sidx, $sord);
                }
        }   
		//Si existen variables de limite	
		
		return $aseguradoras->get();
	}
    public function listar_aseguradoras($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {	
		$query = Aseguradoras::with(array('creadopor' => function($query) use($clause, $sidx, $sord){
			if(!empty($sidx) && preg_match("/cargo/i", $sidx)){
				$query->orderBy("nombre", $sord);
			}
		}));
        if($clause!=NULL && !empty($clause) && is_array($clause))
        {
                foreach($clause AS $field => $value)
                {  
                        //verificar si valor es array
                        if(is_array($value)){
                                $query->where($field, $value[0], $value[1]);
                        }else{
                                $query->where($field, '=', $value);
                        }
                }
        }
		//Si existen variables de orden
        if($sidx!=NULL && $sord!=NULL){
                if(!preg_match("/(cargo|departamento|centro_contable)/i", $sidx)){
                        $query->orderBy($sidx, $sord);
                }
        }

        //Si existen variables de limite
        if($limit!=NULL) $query->skip($start)->take($limit);
		
		//var_dump($query->get());
        return $query->get();
    }
	
	public function exportar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {	
	//filtros
        $aseguradoras = Aseguradoras::whereIn('id',$clause["id"]);
		
		return $aseguradoras->get();
	}
	
	public function verAseguradora($id) {	
	//filtros
        $aseguradoras = Aseguradoras::where("uuid_aseguradora", $id); 
       
		return $aseguradoras->first();
	}
}
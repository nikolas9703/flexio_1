<?php
namespace Flexio\Modulo\SegAseguradoraContacto\Repository;

use Flexio\Modulo\SegAseguradoraContacto\Models\SegAseguradoraContacto;

class SegAseguradoraContactoRepository {
	
   public function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {	
	//filtros
        $contactos = SegAseguradoraContacto::DeAseguradora($clause["aseguradora_id"]); 
       
		//Si existen variables de orden
        if($sidx != 'estado'){
        $contactos->orderBy('nombre', 'ASC');
        }
		if($sidx!=NULL && $sord!=NULL){
		$contactos->orderBy($sidx, $sord);               
		}       
		//Si existen variables de limite	
		
		return $contactos->get();
	}
	
	public function listar_contactos($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {	
		$query = SegAseguradoraContacto::with(array('creadopor' => function($query) use($clause, $sidx, $sord){
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
								if($field=='id')
								{
									$query->whereIn('id',$value);
								}
								else{
									$query->where($field, $value[0], $value[1]);
								}
                        }else{
                                $query->where($field, '=', $value);
                        }
                }
        }
		//Si existen variables de orden
        if($sidx!=NULL && $sord!=NULL){
                if(!preg_match("/(departamento|centro_contable)/i", $sidx)){
                        $query->orderBy($sidx, $sord);
                }
        }

        //Si existen variables de limite
        if($limit!=NULL) $query->skip($start)->take($limit);
		
		//var_dump($query->get());
        return $query->get();
    }
	//filtros
		public function verContacto($id) {	
		//filtros
			$contacto = SegAseguradoraContacto::where("id", $id); 
		   
			return $contacto->first();
		}
		
		public function verContactoUiid($id) {	
		//filtros
			$contacto = SegAseguradoraContacto::where("uuid_contacto", $id); 
		   
			return $contacto->first();
		}
		
		public function cambiarPrincipal($id) {	
		//filtros
			$contactos = SegAseguradoraContacto::where('aseguradora_id', $id)->update(array('contacto_principal' => 0));
			//$contacto = SegAseguradoraContacto::where("uuid_aseguradora", $id); 

			return $contactos;
		}
}
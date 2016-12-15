<?php
namespace Flexio\Modulo\SegAseguradoraContacto\Repository;

use Flexio\Modulo\SegAseguradoraContacto\Models\SegAseguradoraContacto;

class SegAseguradoraContactoRepository {
	
   public function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {	
	//filtros
        $contactos = SegAseguradoraContacto::DeAseguradora($clause["aseguradora_id"]); 
       
		//Si existen variables de orden
        if($sidx != 'estado'){
        $contactos->orderBy('estado', 'ASC');
        }
		if($sidx!=NULL && $sord!=NULL){
		$contactos->orderBy($sidx, $sord);               
		}       
		//Si existen variables de limite	
		
		return $contactos->get();
	}
	//filtros
		public function verContacto($id) {	
		//filtros
			$contacto = SegAseguradoraContacto::where("uuid_contacto", $id); 
		   
			return $contacto->first();
		}
}
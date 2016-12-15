<?php
namespace Flexio\Modulo\Solicitudes\Repository;

use Flexio\Modulo\Solicitudes\Models\Solicitudes;
use Flexio\Modulo\Usuarios\Models\Usuarios;
use Flexio\Modulo\Cliente\Models\Cliente;

class SolicitudesRepository {
    
   public function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {	
	//filtros
        $solicitudes = Solicitudes::deEmpresa($clause["empresa_id"]); 
       
		//Si existen variables de orden
        if($sidx != 'estado'){
        $solicitudes->orderBy('estado', 'ASC');
        }
		if($sidx!=NULL && $sord!=NULL){
		$solicitudes->orderBy($sidx, $sord);               
		}       
		//Si existen variables de limite	
		return $solicitudes->get();
	}
    public function listar_solicitudes($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {	
	$query = Solicitudes::with(array('cliente', 'aseguradora', 'tipo', 'usuario' => function($query) use($clause, $sidx, $sord){
			if(!empty($sidx) && preg_match("/cargo/i", $sidx)){
				$query->orderBy("nombre", $sord);
			}
		}));
        
        if(!empty($clause['cliente'])){        
        $cliente_data = $clause['cliente'];    
        $cliente = Cliente::where("nombre", $cliente_data[0], $cliente_data[1])->get(array('id'))->toArray();       
        if(!empty($cliente)){
                $cliente_id = (!empty($cliente) ? array_map(function($cliente){ return $cliente["id"]; }, $cliente) : "");
                 
                $query->whereIn("cliente_id", $cliente_id);              
        }
        }
        if(!empty($clause['ramo'])){           
        $query->whereIn("ramo", $clause['ramo']);    
        }
        unset($clause['cliente']);
        unset($clause['ramo']);
        
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
        //return $query->get(array('id', Capsule::raw("CONCAT_WS(' ', IF(nombre != '', nombre, ''), IF(apellido != '', apellido, '')) AS nombre"), 'cedula', 'created_at', Capsule::raw("HEX(uuid_colaborador) AS uuid")));
        return $query->get();
    }
}
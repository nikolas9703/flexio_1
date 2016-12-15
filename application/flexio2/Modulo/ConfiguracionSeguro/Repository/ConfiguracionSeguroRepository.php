<?php
namespace Flexio\Modulo\ConfiguracionSeguro\Repository;

//models
use Flexio\Modulo\ConfiguracionSeguro\Models\ConfiguracionSeguro;
                                             
class ConfiguracionSeguroRepository
{
    
    
    public function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) { 
    //filtros
        $ramos = ConfiguracionSeguro::deEmpresa($clause["empresa_id"]); 
       
        //Si existen variables de orden
        if($sidx != 'estado'){
        $ramos->orderBy('estado', 'ASC');
        }
        if($sidx!=NULL && $sord!=NULL){
        $ramos->orderBy($sidx, $sord);   Cache::put('a', $value, $minutes);            
        }       
        //Si existen variables de limite    
        
        return $ramos->get();
    }

     public function listar_ramos($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {   
    
        $query = ConfiguracionSeguro::with(array('creadopor' => function($query) use($clause, $sidx, $sord){
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
        //return $query->get(array('id', Capsule::raw("CONCAT_WS(' ', IF(nombre != '', nombre, ''), IF(apellido != '', apellido, '')) AS nombre"), 'cedula', 'created_at', Capsule::raw("HEX(uuid_colaborador) AS uuid")));
        return $query->get();
    }

}

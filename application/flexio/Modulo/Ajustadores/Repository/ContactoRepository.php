<?php
namespace Flexio\Modulo\Ajustadores\Repository;
use Flexio\Modulo\Ajustadores\Models\AjustadoresContacto;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class ContactoRepository{
    public function find($contacto_id) {
        return AjustadoresContacto::find($contacto_id);
    }   
    public function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {
      $query = AjustadoresContacto::where(function($query) use($clause){          
          if(isset($clause['ajustador_id']))$query->where('ajustador_id', '=', $clause["ajustador_id"]);         
          if(isset($clause['id']))$query->whereIn('id', $clause["id"]);         
          if(isset($clause['nombre']))$nombre = '%' . $clause['nombre'] . '%';
          if(isset($clause['nombre']))$query->where('nombre','LIKE' , $nombre);
          if(isset($clause['cargo']))$cargo = '%' . $clause['cargo'] . '%';
          if(isset($clause['cargo']))$query->where('cargo','LIKE' , $cargo);
          if(isset($clause['telefono']))$telefono = '%' . $clause['telefono'] . '%';
          if(isset($clause['telefono']))$query->where('telefono','LIKE' ,$telefono);
          if(isset($clause['celular']))$celular = '%' . $clause['celular'] . '%';
          if(isset($clause['celular']))$query->where('celular','LIKE' ,$celular);
          if(isset($clause['email']))$email = '%' . $clause['email'] . '%';
          if(isset($clause['email']))$query->where('email','LIKE',$email);          
          if(isset($clause['ajustadores']))$query->whereIn('id',$clause['ajustadores']);         
      });
      if(!empty($clause['created_at']))
    {
            foreach($clause AS $field => $value)
            {
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
  
function asignar_contacto_principal($clause) {
    Capsule::beginTransaction();
    	
    	try {
    	$ajustador_id = $clause['ajustador_id'];
    	$contacto_id = $clause['id'];
    
    	//Si el $fieldset es vacio
    	if(empty($ajustador_id) || empty($contacto_id)){
    		return false;
    	}
    $contactos = AjustadoresContacto::where('ajustador_id', $ajustador_id)->get();
    $contacto_check = AjustadoresContacto::find($contacto_id);      
     foreach($contactos as $row){
         
         $row->principal = 0;
         $row->save();
     
     }
    $contacto_check->principal = 1;
    $contacto_check->save();     
     } catch(ValidationException $e){
    				
    		// Rollback
    		Capsule::rollback();
    				
    		log_message("error", "MODULO: ". __METHOD__ .", Linea: ". __LINE__ ." --> ". $e->getMessage().".\r\n");
    				
    		echo json_encode(array(
    			"guardado" => false,
    			"mensaje" => "Hubo un error tratando de ". (!empty($contacto) ? "actualizar" : "guardar") ." el descuento."
    		));
    		exit;
    	}
    	
    	// If we reach here, then
    	// data is valid and working.
    	// Commit the queries!
    	Capsule::commit();
    	
    	
    }  
  
}

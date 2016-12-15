<?php
namespace Flexio\Modulo\InteresesAsegurados\Repository;

use Flexio\Modulo\InteresesAsegurados\Models\InteresesAsegurados;
use Flexio\Modulo\InteresesAsegurados\Models\InteresesPersonas;
use Flexio\Modulo\InteresesAsegurados\Models\VehiculoAsegurados as VehiculoAsegurados;
use Flexio\Modulo\InteresesAsegurados\Models\InteresesAsegurados_cat as InteresesAsegurados_cat;
use Flexio\Modulo\InteresesAsegurados\Models\ProyectoAsegurados as ProyectoAsegurados;
use Flexio\Modulo\InteresesAsegurados\Models\CargaAsegurados as CargaAsegurados;


class InteresesAseguradosRepository{
	public function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {	
	//filtros
        $intereses_asegurados = InteresesAsegurados::deEmpresa($clause["empresa_id"])->conTipo(); 
       
		//Si existen variables de orden
        if($sidx != 'estado'){
        $intereses_asegurados->orderBy('estado', 'ASC');
        }
		//Si existen variables de orden
        if($sidx!=NULL && $sord!=NULL){
                if(!preg_match("/(cargo|departamento|centro_contable)/i", $sidx)){
                        $query->orderBy($sidx, $sord);
                }
        }   
		//Si existen variables de limite	
		
		return $intereses_asegurados->get();
	}
	public function listar_intereses_asegurados($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {	
		$query = InteresesAsegurados::with(array('creadopor' => function($query) use($clause, $sidx, $sord){
			if(!empty($sidx) && preg_match("/cargo/i", $sidx)){
				$query->orderBy("nombre", $sord);
			}
		}));
        if($clause!=NULL && !empty($clause) && is_array($clause))
        {
                foreach($clause AS $field => $value)
                {  
						if($field=='tipo'|| ($field=='id' && count($value)))
						{
							$query->whereIn($field, $value);
						}
                        //verificar si valor es array
                        else if(is_array($value)){
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
    public function count($clause = array()) {
        $intereses_asegurados = InteresesAsegurados::deEmpresa($clause["empresa_id"]);        
        //filtros
        $this->_filtros($intereses_asegurados, $clause);        
        return $intereses_asegurados->count();
    }
	
	 public function listar_vehiculo($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {
	//filtros
        $intereses_asegurados = VehiculoAsegurados::deEmpresaVehiculo($clause["empresa_id"]);        
        $this->_filtros($intereses_asegurados, $clause);	
		//Si existen variables de orden
		if($sidx!=NULL && $sord!=NULL){
		$intereses_asegurados->orderBy($sidx, $sord);
		}
        
		//Si existen variables de limite		
		return $intereses_asegurados->get();
	}

    public function listar_carga($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {
    //filtros
        $intereses_asegurados = CargaAsegurados::deEmpresaCarga($clause["empresa_id"]);        
        $this->_filtros($intereses_asegurados, $clause);    
        //Si existen variables de orden
        if($sidx!=NULL && $sord!=NULL){
        $intereses_asegurados->orderBy($sidx, $sord);
        }
        
        //Si existen variables de limite        
        return $intereses_asegurados->get();
    }

	 private function _filtros($intereses_asegurados, $clause) {
        if(isset($clause["intereses"]) and !empty($clause["intereses"])){$intereses_asegurados->deUuid($clause["intereses"]);}
        if(isset($clause["numero"]) and !empty($clause["numero"])){$intereses_asegurados->deNumero($clause["numero"]);}
        if(isset($clause["tipo"]) and !empty($clause["tipo"])){$intereses_asegurados->deTipo($clause["tipo"]);}
        if(isset($clause["identificacion"]) and !empty($clause["identificacion"])){$intereses_asegurados->deIdentificacion($clause["identificacion"]);}
        if(isset($clause["estado"]) and !empty($clause["estado"])){$intereses_asegurados->deEstado($clause["estado"]);}
    }
	public static function identificacion($chasis) {
        return VehiculoAsegurados::where('chasis', $chasis)->first();      
	}
	public static function identificacionUuid($chasis,$uuid) {
        return VehiculoAsegurados::where('chasis', $chasis)->where('id','!=',$uuid)->first();      
	}
	 public function consultaOrden($orden) {
        $orden_dev = ProyectoAsegurados::where('no_orden', '=', $orden);
        return $orden_dev->get();
    }
	public function verInteresAsegurado($id) {	
	//filtros
        $interes_asegurado = InteresesAsegurados::where("uuid_intereses", $id); 
       
		return $interes_asegurado->first();
	}

    public static function identificacion_carga($no_liquidacion) {
            return CargaAsegurados::where('no_liquidacion', $no_liquidacion)->first();      
      }
    public static function identificacion_persona($identificacion) {
        return InteresesPersonas::where('identificacion', $identificacion)->first();      
  }

  public function listar_persona($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {
    //filtros
        $intereses_asegurados = InteresesPersonas::deEmpresaPersona($clause["empresa_id"]);        
        $this->_filtros($intereses_asegurados, $clause);    
        //Si existen variables de orden
        if($sidx!=NULL && $sord!=NULL){
        $intereses_asegurados->orderBy($sidx, $sord);
        }
        
        //Si existen variables de limite        
        return $intereses_asegurados->get();
    }
}

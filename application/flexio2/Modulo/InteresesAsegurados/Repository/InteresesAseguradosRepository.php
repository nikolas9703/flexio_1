<?php
namespace Flexio\Modulo\InteresesAsegurados\Repository;

use Flexio\Modulo\InteresesAsegurados\Models\InteresesAsegurados as InteresesAsegurados;
use Flexio\Modulo\InteresesAsegurados\Models\InteresesPersonas as InteresesPersonas;
use Flexio\Modulo\InteresesAsegurados\Models\VehiculoAsegurados as VehiculoAsegurados;
use Flexio\Modulo\InteresesAsegurados\Models\AereoAsegurados as AereoAsegurados;
use Flexio\Modulo\InteresesAsegurados\Models\MaritimoAsegurados as MaritimoAsegurados;
use Flexio\Modulo\InteresesAsegurados\Models\ProyectoAsegurados as ProyectoAsegurados;
use Flexio\Modulo\InteresesAsegurados\Models\CargaAsegurados as CargaAsegurados;
use Flexio\Modulo\InteresesAsegurados\Models\InteresesAsegurados_cat as InteresesAsegurados_cat;
use Flexio\Modulo\InteresesAsegurados\Models\ArticuloAsegurados as ArticuloAsegurados;
use Flexio\Modulo\InteresesAsegurados\Models\UbicacionAsegurados as UbicacionAsegurados;

class InteresesAseguradosRepository implements InteresesAseguradosInterface{
    
    public function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {	
	//filtros
        $intereses_asegurados = InteresesAsegurados::deEmpresa($clause["empresa_id"])->conTipo();        
        $this->_filtros($intereses_asegurados, $clause);	
		//Si existen variables de orden
        if($sidx != 'estado'){
        $intereses_asegurados->orderBy('estado', 'ASC');
        }
		if($sidx!=NULL && $sord!=NULL){
		$intereses_asegurados->orderBy($sidx, $sord);               
		}       
		//Si existen variables de limite	
		return $intereses_asegurados->get();
	}
    public function listar_aereo($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {
	//filtros
        $intereses_asegurados = AereoAsegurados::deEmpresaAereo($clause["empresa_id"]);        
        $this->_filtros($intereses_asegurados, $clause);	
		//Si existen variables de orden
		if($sidx!=NULL && $sord!=NULL){
		$intereses_asegurados->orderBy($sidx, $sord);
		}
        
		//Si existen variables de limite		
		return $intereses_asegurados->get();
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
    public function listar_maritimo($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {
	//filtros
        $intereses_asegurados = MaritimoAsegurados::deEmpresaMaritimo($clause["empresa_id"]);        
        $this->_filtros($intereses_asegurados, $clause);	
		//Si existen variables de orden
		if($sidx!=NULL && $sord!=NULL){
		$intereses_asegurados->orderBy($sidx, $sord);
		}
        
		//Si existen variables de limite		
		return $intereses_asegurados->get();
	}
    public function listar_proyecto($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {
	//filtros
        $intereses_asegurados = ProyectoAsegurados::deEmpresaProyecto($clause["empresa_id"]);        
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
    public function listar_articulo($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {
	//filtros
        $intereses_asegurados = ArticuloAsegurados::deEmpresaArticulo($clause["empresa_id"]);        
        $this->_filtros($intereses_asegurados, $clause);	
		//Si existen variables de orden
		if($sidx!=NULL && $sord!=NULL){
		$intereses_asegurados->orderBy($sidx, $sord);
		}
        
		//Si existen variables de limite		
		return $intereses_asegurados->get();
                
    }
    public function listar_ubicacion($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {
	//filtros
        $intereses_asegurados = UbicacionAsegurados::deEmpresaUbicacion($clause["empresa_id"]);        
        $this->_filtros($intereses_asegurados, $clause);	
		//Si existen variables de orden
		if($sidx!=NULL && $sord!=NULL){
		$intereses_asegurados->orderBy($sidx, $sord);
		}
        
		//Si existen variables de limite		
		return $intereses_asegurados->get();
                
    }
    public function count($clause = array()) {
        $intereses_asegurados = InteresesAsegurados::deEmpresa($clause["empresa_id"]);        
        //filtros
        $this->_filtros($intereses_asegurados, $clause);        
        return $intereses_asegurados->count();
    }
    
    private function _filtros($intereses_asegurados, $clause) {
        if(isset($clause["intereses"]) and !empty($clause["intereses"])){$intereses_asegurados->deUuid($clause["intereses"]);}
        if(isset($clause["numero"]) and !empty($clause["numero"])){$intereses_asegurados->deNumero($clause["numero"]);}
        if(isset($clause["tipo"]) and !empty($clause["tipo"])){$intereses_asegurados->deTipo($clause["tipo"]);}
        if(isset($clause["identificacion"]) and !empty($clause["identificacion"])){$intereses_asegurados->deIdentificacion($clause["identificacion"]);}
        if(isset($clause["estado"]) and !empty($clause["estado"])){$intereses_asegurados->deEstado($clause["estado"]);}
    }
    public static function buscar($identificacion) {
        return VehiculoAsegurados::where('chasis', $identificacion)->first();      
  }
  
  public static function identificacion($chasis) {
        return VehiculoAsegurados::where('chasis', $chasis)->first();      
  }
  public static function identificacion_maritimo($chasis) {
        return MaritimoAsegurados::where('serie', $chasis)->first();      
  }
  public static function identificacion_ubicacion($direccion) {
        return UbicacionAsegurados::where('direccion', $direccion)->first();      
  }
  public static function identificacion_aereo($chasis) {
        return AereoAsegurados::where('serie', $chasis)->first();      
  }
  public static function identificacion_persona($identificacion) {
        return InteresesPersonas::where('identificacion', $identificacion)->first();      
  }
  public static function identificacion_proyecto($nombre_proyecto=NULL, $no_orden=NULL) {
        return ProyectoAsegurados::where(array('nombre_proyecto'=> $nombre_proyecto, 'no_orden' => $no_orden))->first();      
  }
  public static function identificacion_carga($no_liquidacion) {
        return CargaAsegurados::where('no_liquidacion', $no_liquidacion)->first();      
  }
    
}

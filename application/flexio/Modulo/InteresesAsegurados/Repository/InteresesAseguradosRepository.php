<?php
namespace Flexio\Modulo\InteresesAsegurados\Repository;

use Flexio\Modulo\InteresesAsegurados\Models\InteresesPersonas;
use Flexio\Modulo\InteresesAsegurados\Models\InteresesAsegurados;
use Flexio\Modulo\InteresesAsegurados\Models\VehiculoAsegurados as VehiculoAsegurados;
use Flexio\Modulo\InteresesAsegurados\Models\InteresesAsegurados_cat as InteresesAsegurados_cat;
use Flexio\Modulo\InteresesAsegurados\Models\ProyectoAsegurados as ProyectoAsegurados;
use Flexio\Modulo\InteresesAsegurados\Models\CargaAsegurados as CargaAsegurados;
use Flexio\Modulo\InteresesAsegurados\Models\AereoAsegurados as AereoAsegurados;
use Flexio\Modulo\InteresesAsegurados\Models\MaritimoAsegurados as MaritimoAsegurados;
use Flexio\Modulo\InteresesAsegurados\Models\ArticuloAsegrado as ArticuloAsegrado;
use Flexio\Modulo\InteresesAsegurados\Models\UbicacionAsegurados as UbicacionAsegurados;
use Flexio\Modulo\Polizas\Models\Polizas as PolizasModel;

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
public static function identificacionUuid($motor,$uuid) {
    return VehiculoAsegurados::where('motor', $motor)->where('id','!=',$uuid)->first();      
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
public function consultaUbicacion($direccion) {
    $orden_dev = UbicacionAsegurados::where('direccion', '=', $direccion);
    return $orden_dev->get();
}

public static function identificacion_carga($no_liquidacion) {
    return CargaAsegurados::where('no_liquidacion', $no_liquidacion)->first();      
}
public static function identificacion_aereo($serie) {
    return AereoAsegurados::where('serie', $serie)->first();      
}

public function validarCarga($uuid, $no_liquidacion) {  
    //filtros
    $interes_asegurado = InteresesAsegurados::where("uuid_intereses", $uuid)->where("identificacion", $no_liquidacion); 
    
    return $interes_asegurado->count();
}

public function validarSerieAereo($uuid, $serie) {  
    //filtros
    $interes_asegurado = InteresesAsegurados::where("uuid_intereses", $uuid)->where("identificacion", $serie); 
    
    return $interes_asegurado->count();
}

public function consultaSerie($serie) {
    $orden_dev = MaritimoAsegurados::where('serie', '=', $serie);
    return $orden_dev->get();
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

	public function listar_polizas($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){
        $query = PolizasModel::select("pol_polizas.*","cli_clientes.nombre as nombre_cliente","seg_aseguradoras.nombre as nombre_aseguradora")->with(array('aseguradorafk'))->where(array('pol_polizas.empresa_id' => $clause['empresa_id']))->join("seg_solicitudes","seg_solicitudes.numero","=","pol_polizas.solicitud")->join("seg_aseguradoras","seg_aseguradoras.id","=","pol_polizas.aseguradora_id")->join("cli_clientes","cli_clientes.id","=","pol_polizas.cliente")->whereIn("seg_solicitudes.id",$clause["ids_sol"]);
		
		unset($clause["ids_sol"]);
		unset($clause["empresa_id"]);
		
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
                $query->join('seg_ramos_usuarios', 'seg_ramos_usuarios.id_ramo', '=', 'seg_solicitudes.ramo_id');
                $query->where("seg_ramos_usuarios.id_usuario", $clause['usuario_id']);
                $query->groupBy('seg_solicitudes.id');
                unset($clause['usuario_id']);
		
		if($sord!=NULL && ($sidx!=NULL && $sidx!="nombre")){
			$query->orderBy($sidx, $sord);
		}else{
			$query->orderBy("fin_vigencia", "desc");
		}
		if($limit!=NULL) $query->skip($start)->take($limit);

        return $query->get();
	}

	public function exportarPolizasIntereses($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){
		$query = PolizasModel::with(array('clientefk', 'aseguradorafk'))->whereIn("id",$clause["id"]);
		
		return $query->get();
	}

}

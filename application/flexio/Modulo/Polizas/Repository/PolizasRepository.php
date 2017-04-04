<?php
namespace Flexio\Modulo\Polizas\Repository;

use Flexio\Modulo\Polizas\Models\Polizas;
use Flexio\Modulo\InteresesAsegurados\Models\InteresesAsegurados;
use Flexio\Modulo\Cobros\Models\CobroFactura;
use Flexio\Modulo\Polizas\Models\PolizasAcreedores;
use Flexio\Modulo\Polizas\Models\PolizasAcreedores_detalles;

class PolizasRepository{
	
	public function exportar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){
	//filtros
        $polizas = Polizas::with(array('clientefk', 'categoriafk', 'usuariofk','aseguradorafk'));
		
		$polizas->whereIn('id',$clause["id"]);
		
		return $polizas->get();
	}
	
	/*
	public function listar_intereses_asegurados($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){

		$query = InteresesAsegurados::select("int_intereses_asegurados.*","int_intereses_asegurados_detalles.fecha_inclusion","int_intereses_asegurados_detalles.fecha_exclusion","int_intereses_asegurados_detalles.id as id_detalle","usuarios.nombre","usuarios.apellido","seg_ramos_tipo_interes.nombre as etiqueta")->join("int_intereses_asegurados_detalles","int_intereses_asegurados_detalles.id_intereses","=","int_intereses_asegurados.id")->join("usuarios","usuarios.id","=","int_intereses_asegurados.creado_por")->join("seg_ramos_tipo_interes","seg_ramos_tipo_interes.id","=","int_intereses_asegurados.interesestable_type");
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
			if($sidx=="estado" AND $sord=="ASC"){
				$query->orderBy("estado", $sord)->orderBy("int_intereses_asegurados_detalles.fecha_inclusion","DESC");
			}else{
				$query->orderBy($sidx, $sord);
			}
		}

		//Si existen variables de limite
		if($limit!=NULL){
			$query->skip($start)->take($limit);
		}
		return $query->get();
	}
	*/
	
	public function exportarInteresesPolizas($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){
		$query = InteresesAsegurados::select("int_intereses_asegurados.*","int_intereses_asegurados_detalles.fecha_inclusion","int_intereses_asegurados_detalles.fecha_exclusion","int_intereses_asegurados_detalles.id as id_detalle")->join("int_intereses_asegurados_detalles","int_intereses_asegurados_detalles.id_intereses","=","int_intereses_asegurados.id");			
		$query->where("int_intereses_asegurados_detalles.id_solicitudes",$clause["id_solicitudes"]);
		$query->whereIn("int_intereses_asegurados_detalles.id_intereses", $clause["id"])->orderBy("estado", "ASC")->orderBy("int_intereses_asegurados_detalles.fecha_inclusion","DESC");
		
		return $query->get();
	}

	public function listar_renovaciones_asegurados($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){
		
		$ids = array();
		$query1 = Polizas::select("id", "renovacion_id")->where("categoria",'45')->where("id", $clause['pol_polizas.id'])->first();
		if (!is_null($query1)) {
			if ($query1->renovacion_id != 0) {
				array_push($ids, $query1->renovacion_id);
				$idant = $query1->renovacion_id;
				$x=1;
			}else{
				$x=0;
			}
			while ($x>0) {
				//$query2 = Polizas::select("id", "renovacion_id")->where("id", $idant)->where("categoria",'45')->first();
				$query2 = Polizas::select("id", "renovacion_id")->where("id", $idant)->first();				
				if (!is_null($query2) && $query2->renovacion_id != 0) {
					//array_push($ids, $query2->id);
					array_push($ids, $query2->renovacion_id);
					$idant = $query2->renovacion_id;
					$x=1;
				}else{
					$x=0;
				}				
			}
		}	
		

		unset($clause['pol_polizas.id']);
		if (empty($ids)) {
			array_push($ids, "");
		}

		$query = Polizas::select("pol_polizas.id as id", "pol_polizas.inicio_vigencia", "pol_polizas.fin_vigencia", "pol_polizas.updated_at", "usuarios.nombre", "usuarios.apellido", "pol_polizas.numero", "pol_polizas.uuid_polizas")->join("usuarios","usuarios.id","=","pol_polizas.usuario")->whereIn("pol_polizas.id", $ids);

		if($clause!=NULL && !empty($clause) && is_array($clause))
		{
			foreach($clause AS $field => $value)
			{
				if(is_array($value)){
					$query->where($field, $value[0], $value[1]);
				}else{
					$query->where($field, '=', $value);
				}
			}
		}
		//Si existen variables de orden
		if($sidx!=NULL && $sord!=NULL){
			if($sidx=="estado" AND $sord=="ASC"){
				$query->orderBy("estado", $sord)->orderBy("int_intereses_asegurados_detalles.fecha_inclusion","DESC");
			}else{
				$query->orderBy($sidx, $sord);
			}
		}

		//Si existen variables de limite
		if($limit!=NULL) $query->skip($start)->take($limit);

		//var_dump($query->get());
		return $query->get();
	}

	public function exportarRenovacionesPolizas($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){
		$query = Polizas::select("pol_polizas.id as id", "pol_polizas.inicio_vigencia", "pol_polizas.fin_vigencia", "pol_polizas.updated_at", "usuarios.nombre", "usuarios.apellido", "pol_polizas.numero", "pol_polizas.uuid_polizas")->join("usuarios","usuarios.id","=","pol_polizas.usuario")->whereIn("pol_polizas.id", $clause['pol_polizas.id']);
		
		//Si existen variables de orden
		if($sidx!=NULL && $sord!=NULL){
			if($sidx=="estado" AND $sord=="ASC"){
				$query->orderBy("estado", $sord)->orderBy("int_intereses_asegurados_detalles.fecha_inclusion","DESC");
			}else{
				$query->orderBy($sidx, $sord);
			}
		}

		//Si existen variables de limite
		if($limit!=NULL) $query->skip($start)->take($limit);

		//var_dump($query->get());
		return $query->get();
	}

	public function total_facturado_polizas($ids) {
    	$query = CobroFactura::whereIn("cobrable_id", $ids)->where('transaccion',1)->sum('monto_pagado');
      return $query;
    }

    public function verAcreedores($id_poliza){
        $acreedores = PolizasAcreedores::where('id_poliza',$id_solicitudes);
        
        return $acreedores->get();

    }

    public function verAcreedoresDetalle($id){
        $acreedores = PolizasAcreedores_detalles::where('idinteres_detalle',$id);
        
        return $acreedores->get();

    }
}
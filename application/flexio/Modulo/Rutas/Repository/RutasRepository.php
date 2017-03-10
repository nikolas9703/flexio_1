<?php
namespace Flexio\Modulo\rutas\Repository;

use Flexio\Modulo\rutas\Models\Rutas;

class RutasRepository {
    public function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {	
		$query = Rutas::select('seg_rutas.*')->join('geo_provincias', 'seg_rutas.provincia_id', '=', 'geo_provincias.id')
		->join('geo_distritos', 'seg_rutas.distrito_id', '=', 'geo_distritos.id')
		->join('geo_corregimientos', 'seg_rutas.corregimiento_id', '=', 'geo_corregimientos.id');
		
		if($clause!=NULL && !empty($clause) && is_array($clause))
        {
                foreach($clause AS $field => $value)
                {  
					//verificar si valor es array
					if(is_array($value)){
						if($field=='provincia')
						{
							$query->where('geo_provincias.nombre', $value[0], $value[1]);
						}
						else if($field=='distrito')
						{
							$query->where('geo_distritos.nombre', $value[0], $value[1]);
						}
						else if($field=='corregimiento')
						{
							$query->where('geo_corregimientos.nombre', $value[0], $value[1]);
						}
						else
							$query->where($field, $value[0], $value[1]);
					}else{
							$query->where($field, '=', $value);
					}
                }
        }
		
		if(preg_match("/(nombre_ruta1)/i", $sidx)){
			$sidx='nombre_ruta';
			$query->orderBy("geo_provincias.nombre", 'asc')
			->orderBy("geo_distritos.nombre", 'asc')
			->orderBy("geo_corregimientos.nombre", 'asc')
			->orderBy("geo_corregimientos.nombre", 'asc')
			->orderBy("seg_rutas.nombre_mensajero", 'asc');
		}
				
		//Si existen variables de orden
        if($sidx!=NULL && $sord!=NULL){
				//var_dump($sidx);
                if(!preg_match("/(cargo|departamento|centro_contable)/i", $sidx)){
                    if($sidx=='provincia')
					{
						$query->orderBy('geo_provincias.nombre', $sord);
					}
					else if($sidx=='distrito')
					{
						$query->orderBy('geo_distritos.nombre', $sord);
					}
					else if($sidx=='corregimiento')
					{
						$query->orderBy('geo_corregimientos.nombre', $sord);
					}
					else{
						$query->orderBy('seg_rutas.'.$sidx, $sord); 
					}
                }
        }

        //Si existen variables de limite
        if($limit!=NULL) $query->skip($start)->take($limit);
		
		//var_dump($query->get());
        return $query->get();
    }
	
	public function exportar($clause=array()) {	
		$query = Rutas::whereIn('id',$clause['id']);
        return $query->get();
    }
}
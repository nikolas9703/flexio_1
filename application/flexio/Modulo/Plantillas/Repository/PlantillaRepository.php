<?php
namespace Flexio\Modulo\Plantillas\Repository;

use Flexio\Modulo\Plantillas\Models\Plantilla as Plantilla;
use Flexio\Modulo\Comentario\Models\Comentario;

class PlantillaRepository implements PlantillaInterface {
	public function find($id){
		return Plantilla::find($id);
	}
	function getAll($clause = array()){
		return Plantilla::with(array('tipo'))->where('estado', '=', $clause["estado"])->get();
	}
	function getAllGroupByTipo($clause = array()){
		$plantillas = Plantilla::with(array('tipo'))->where('estado', '=', $clause["estado"])->get();
		
		if(empty($plantillas)){
			return false;
		}
		
		$grupos = array();
		$i=0;
		foreach($plantillas AS $plantilla){
			$i = empty($grupos[$plantilla->tipo->nombre]) ? 0 : $i;
			$grupos[$plantilla->tipo->nombre][$i]["id"] = $plantilla->id;
			$grupos[$plantilla->tipo->nombre][$i]["nombre"] = $plantilla->nombre;
			$i++;
		}
		return $grupos;
	}
	function getCotizacionValidas($clause){
		return $cotizacion = Plantilla::where(function($query)use($clause){
			$query->where('empresa_id', '=', $clause['empresa_id']);
			$query->whereNotIn('estado', array(
				'anulada',
				'perdida' 
			));
		})->get();
	}
	function create($created){
		$cotizacion = Plantilla::create($created['cotizacion']);
		$lineItem = new LineItemTransformer();
		$items = $lineItem->crearInstancia($created['lineitem']);
		$cotizacion->items()->saveMany($items);
		return $cotizacion;
	} 
	function update($update){
		/*
		 * $cotizacion = Plantilla::find($update['cotizacion']['cotizacion_id']); $cotizacion->update($update['cotizacion']); $lineItem = new LineItemTransformer; $items = $lineItem->crearInstancia($update['lineitem']); $cotizacion->items()->saveMany($items); return $cotizacion;
		 */
	}
	function findByUuid($uuid){
		return Plantilla::where('uuid_cotizacion', hex2bin($uuid))->first();
	}
	function listar($clause = array(), $sidx = NULL, $sord = NULL, $limit = NULL, $start = NULL){
		$cotizacion = Plantilla::where(function($query)use($clause){
			
			if(isset($clause['cliente_id']))
				$query->where('cliente_id', '=', $clause['cliente_id']);
			if(isset($clause['id']))
				$query->where('id', '=', $clause['id']);
			if(isset($clause['etapa']))
				$query->where('estado', '=', $clause['etapa']);
			if(isset($clause['creado_por']))
				$query->where('creado_por', '=', $clause['creado_por']);
			if(isset($clause['fecha_desde']))
				$query->where('fecha_desde', '<=', $clause['fecha_desde']);
			if(isset($clause['fecha_hasta']))
				$query->where('fecha_hasta', '>=', $clause['fecha_hasta']);
                        if(isset($clause['nombre']))
				$query->where('nombre', '=', $clause['nombre']);
		});
		if($sidx !== NULL && $sord !== NULL)
			$cotizacion->orderBy($sidx, $sord);
		if($limit != NULL)
			$cotizacion->skip($start)->take($limit);
		return $cotizacion->get();
	}

    function agregarComentario($id, $comentarios) {
        $plantilla = Plantilla::find($id);
        $comentario = new Comentario($comentarios);
        $plantilla->comentario_timeline()->save($comentario);
        return $plantilla;
    }
}

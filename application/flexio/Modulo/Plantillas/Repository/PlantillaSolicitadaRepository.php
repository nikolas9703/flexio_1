<?php

namespace Flexio\Modulo\Plantillas\Repository;

use Flexio\Modulo\Plantillas\Models\PlantillaSolicitada as PlantillaSolicitada;
use Flexio\Modulo\Plantillas\Models\Plantilla as Plantilla;
use Flexio\Modulo\Plantillas\Models\PlantillaCatalogo as PlantillaCatalogo;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Comentario\Models\Comentario;

class PlantillaSolicitadaRepository implements PlantillaInterface {

    function find($id) {
        return PlantillaSolicitada::find($id);
    }   

    function getAll($clause = array()) {
        return PlantillaSolicitada::with(array('tipo'))->where('estado', '=', $clause["estado"])->get();
    }

    function getAllGroupByTipo($clause = array()) {
        $plantillas = PlantillaSolicitada::with(array('tipo'))->where('estado', '=', $clause["estado"])->get();

        if (empty($plantillas)) {
            return false;
        }

        $grupos = array();
        $i = 0; 
        foreach ($plantillas AS $plantilla) {
            $i = empty($grupos[$plantilla->tipo->nombre]) ? 0 : $i;
            $grupos[$plantilla->tipo->nombre][$i]["id"] = $plantilla->id;
            $grupos[$plantilla->tipo->nombre][$i]["nombre"] = $plantilla->nombre;
            $i++;
        }
        return $grupos;
    }

    function create($created) {
        $plantilla = PlantillaSolicitada::create($created);
        return $plantilla;
    }

    function update($update) {
        $plantilla = PlantillaSolicitada::where('id', '=', $update['id'])->update($update);
        // User::where('votes', '>', 100)->update(array('status' => 2));
        return $plantilla;
    }

    function findByUuid($uuid) {
        return PlantillaSolicitada::where('uuid_plantilla',hex2bin($uuid))->first();
    }

    function nombre_plantilla() {
        return $this->belongsTo(Plantilla::class, 'id', 'plantilla_id');
    }

    public function estado() {
        return $this->belongsTo(PlantillaCatalogo::class, 'id', 'estado_id');
    }

    function listar($clause = array(), $sidx = NULL, $sord = NULL, $limit = NULL, $start = NULL) {
        
        $query = PlantillaSolicitada::with(array('estado', 'nombre_plantilla'))->where(function($query) use($clause){
                   $query->where('empresa_id','=',$clause['empresa_id']);
                        if(isset($clause['plantilla_id'])) $query->where('plantilla_id','=',$clause['plantilla_id']);
                        if(isset($clause['estado_id'])) $query->where('estado_id','=',$clause['estado_id']);
                       //  if(isset($clause['fecha_desde']) && isset($clause['fecha_hasta']))$query->whereBetween('created_at', array($clause['fecha_desde'], $clause['fecha_hasta']));
                        if(isset($clause['fecha_desde']))$query->whereDate('created_at','>=',$clause['fecha_desde']);
			if(isset($clause['fecha_hasta']))$query->whereDate('created_at','<=',$clause['fecha_hasta']);
			if(isset($clause['colaborador_id']))$query->where('colaborador_id','=',$clause['colaborador_id']);
		});
       
        if ($sidx !== NULL && $sord !== NULL)$query->orderBy($sidx, $sord);
        if ($limit != NULL)$query->skip($start)->take($limit);
        
        return $query->get();
    }

    function exportar($clause = array()) {
                
          $query = PlantillaSolicitada::with(array('estado', 'nombre_plantilla'))->where(function($query) use($clause) {
            $query->whereIn("id", $clause['id']);
        });
        return $query->get();
    }
    
    function descargar_pdf($clause = array()) {
        
        $query = PlantillaSolicitada::with(array('estado', 'nombre_plantilla'))->where(function($query) use($clause){
		});
        if($clause!=NULL && !empty($clause) && is_array($clause))
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
        return $query->get();
    }
   /* public function landing_comments() {
        return $this->morphMany(Comentario::class,'comentable');
    }*/
      function ver($clause= array()){
         $query = PlantillaSolicitada::with(array('estado', 'nombre_plantilla'))->where(function($query) use($clause){
      
                   $query->where(Capsule::raw('HEX(uuid_plantilla)'),'=',$clause['uuid_plantilla']);
		});
     
        return $query->get();
    }
    function agregarComentario($id, $comentarios) {
        $plantilla = PlantillaSolicitada::find($id);
        $comentario = new Comentario($comentarios);
        $plantilla->comentario_timeline()->save($comentario);
        return $plantilla;
    }
    
}

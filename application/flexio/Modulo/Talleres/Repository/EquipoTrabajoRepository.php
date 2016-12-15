<?php
namespace Flexio\Modulo\Talleres\Repository;

use Flexio\Modulo\Talleres\Models\EquipoTrabajo;
use Flexio\Modulo\Talleres\Models\EquipoColaboradores;
use Flexio\Modulo\Talleres\Models\EquipoCentrosContables;
use Flexio\Library\Util\FormRequest;
use Illuminate\Http\Request;
use Flexio\Modulo\Talleres\Transform\EquipoCentrosContablesTransformer;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\OrdenesTrabajo\Models\Servicios;
use Flexio\Modulo\OrdenesTrabajo\Repository\OrdenesTrabajoRepository;
use Flexio\Modulo\Comentario\Models\Comentario;

class  EquipoTrabajoRepository implements EquipoTrabajoInterface{

    function __construct() {
        $this->request = Request::capture();
    }

    public function find($id) {
    	return EquipoTrabajo::where("id", $id)->with(array("ordenes_trabajo", "colaboradores" => function($query) {
			$query->with(array("colaborador"));
		}))->first();
    }
    public function findByUuid($uuid) {
        return EquipoTrabajo::where("uuid_equipo", hex2bin($uuid))->with(array("colaboradores" => function($query) {
			$query->with(array("colaborador"));
		}, "centros"))->first();
    }
    function getAll($clause) {
    	return EquipoTrabajo::where(function($query) use($clause) {
    		$query->where('empresa_id', '=', $clause['empresa_id']);
    	})->get();
    }
    public function listar($clause, $sidx, $sord, $limit, $start) {
        // TODO: Implement listar() method.
        $query = EquipoTrabajo::with(array("estado" => function($query) use($sidx, $sord){
			if(!empty($sidx) && preg_match("/estado/i", $sidx)){
				$query->orderBy("etiqueta", $sord);
			}
		}));
        
        	//Si existen variables de limite
        	if($clause!=NULL && !empty($clause) && is_array($clause))
        	{
        		foreach($clause AS $field => $value)
        		{
        			if($field == "colaborador"  || $field == "id" || $field == "nombre_centro"){
        				continue;
        			}
        	
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
        
       //Si existen variables de orden
		if($sidx!=NULL && $sord!=NULL){
			if(!preg_match("/(cargo|departamento|centro_contable)/i", $sidx)){
				$query->orderBy($sidx, $sord);
			}
		}
		 
		//Si existen variables de limite
		if($limit!=NULL) $query->skip($start)->take($limit);
		
        return $query->get();
    }

    public function guardar($empresa_id=NULL, $codigo=NULL) {

    	$campos = FormRequest::data_formulario($this->request->input('campo'));
        
        $id = $this->request->input('id');
        $centros = $this->request->input('centro_contable_id');
        $colaboradores = $this->request->input('to');
        $result = array();
        $dato ='';
        if(empty($id)){

            $campos['empresa_id'] = $empresa_id;
            if($codigo < 10) {
                $campos['codigo'] = "EQP1600".$codigo;
            }else {
                $campos['codigo'] = "EQP160".$codigo;
            }

            unset($campos["guardar"]);
            $equipo = EquipoTrabajo::create($campos);
           	
            if(!empty($centros)){
            	$centros = (!empty($centros) ? array_map(function($centros){
            		 
            		$centroArray = explode("-", $centros);
            		$centro_padre_id = $centroArray[0];
            		$centro_id = $centroArray[1];
            		$departamento_id = $centroArray[2];
            		 
            		return array(
            			"centro_padre_id" => $centro_padre_id,
            			"centro_id" => $centro_id,
            			"departamento_id" => $departamento_id
            		);
            	}, $centros) : "");
            	
            	//Crear Instancia
            	$centroTransformer = new EquipoCentrosContablesTransformer;
            	$centros = $centroTransformer->crearInstancia($centros);
            		
            	//Guardar
            	$equipo->centros()->saveMany($centros);
            }
            
            if(!empty($colaboradores)){
            	
            	foreach ($colaboradores as $colaborador_id){
            		$check = EquipoColaboradores::buscar($equipo->id, $colaborador_id);
            		$existColaborador = $check->get()->toArray();
            		if(empty($existColaborador)){
            			EquipoColaboradores::equipoColaborador($equipo->id, $colaborador_id);
            		}
            	}
            }
             
        }else {

            $trabajo = EquipoTrabajo::find($id)->toArray();
            $ordenes = Servicios::where("equipo_id", $id)->get(array('orden_id'))->toArray();
            $orden_id = (!empty($ordenes) ? array_map(function ($ordenes) {
                return $ordenes["orden_id"];
            }, $ordenes) : "");

            $or = new OrdenesTrabajoRepository;
            $estados = $or->getOrdenes($orden_id)->toArray();
           // dd($campos);
            //if (count($estados) == 0 && $trabajo['estado_id'] == $campos["estado_id"]) {
            unset($campos["guardar"]);

            if (!empty($campos)) {
                if ($trabajo['estado_id'] == $campos["estado_id"] && $campos["ordenes_atender"]  >= count($estados)){
                    EquipoTrabajo::where('id', $id)->update($campos);
                }elseif ($campos["ordenes_atender"]  >= count($estados) && count($estados) == 0){
                    EquipoTrabajo::where('id', $id)->update($campos);
                }elseif (count($estados) == 0 && $trabajo['estado_id'] != $campos["estado_id"] && $campos["ordenes_atender"]  >= count($estados)){
                    EquipoTrabajo::where('id', $id)->update($campos);
                }else{
                    $dato = 'odt_abierta';
                }

            }

            $equipo = EquipoTrabajo::find($id);

            if (!empty($colaboradores)) {

                foreach ($colaboradores as $colaborador_id) {

                    $existColaborador = EquipoColaboradores::where('equipo_trabajo_id', $id)->where('colaborador_id', $colaborador_id);

                    if (empty($existColaborador->get()->toArray())) {
                        EquipoColaboradores::equipoColaborador($id, $colaborador_id);
                    } else {
                        $existColaborador->update(array(
                            'equipo_trabajo_id' => $id,
                            'colaborador_id' => $colaborador_id
                        ));
                    }
                }
            }

            if (!empty($centros)) {
                $centros = (!empty($centros) ? array_map(function ($centros) use ($id) {

                    $centroArray = explode("-", $centros);
                    $centro_padre_id = $centroArray[0];
                    $centro_id = $centroArray[1];
                    $departamento_id = $centroArray[2];

                    $check = EquipoCentrosContables::where("equipo_id", $id)
                        ->where("centro_padre_id", $centro_padre_id)
                        ->where("centro_id", $centro_id)
                        ->where("departamento_id", $departamento_id)
                        ->get()
                        ->toArray();

                    return array(
                        "id" => !empty($check) ? $check[0]["id"] : "",
                        "centro_padre_id" => $centro_padre_id,
                        "centro_id" => $centro_id,
                        "departamento_id" => $departamento_id
                    );
                }, $centros) : "");

                //Crear Instancia
                $centroTransformer = new EquipoCentrosContablesTransformer;
                $centros = $centroTransformer->crearInstancia($centros);

                //Guardar
                $equipo->centros()->saveMany($centros);
            }
        /*}else{
            $equipo = 'odt_abierta';
            }*/
        }
        $result['result'] = $equipo;
        $result['odt_abierta'] = $dato;
        return $result;
    }

    function agregarComentario($id, $comentarios) {
        $equipo = EquipoTrabajo::find($id);
        $comentario = new Comentario($comentarios);
        $equipo->comentario_timeline()->save($comentario);
        return $equipo;
    }
}
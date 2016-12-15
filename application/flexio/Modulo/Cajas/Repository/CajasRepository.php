<?php
namespace Flexio\Modulo\Cajas\Repository;
use Flexio\Modulo\Cajas\Models\Cajas as Caja;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Comentario\Models\Comentario;

class CajasRepository{

     public function get($clause = array(), $sidx = null, $sord = null, $limit = null, $start = null) {
        $clientes = Caja::where(function($query) use ($clause){

            $query->where('empresa_id', '=', $clause['empresa_id']);

        });

        if($sidx !== null && $sord !== null){$clientes->orderBy($sidx, $sord);}
        if($limit != null){$clientes->skip($start)->take($limit);}
        return $clientes->get();
    }

       public function getCollectionCajas($cajas){



        return $cajas->map(function($caja) {

            //$centro_facturable = $cliente->centro_facturable;
            return [
                'id' => $caja->id,
                'nombre' => "{$caja->nombre}",
                'cuenta_id' => $caja->id,
             ];
        });

    }

	function find($id) {
		return Caja::find($id);
	}
	function getAll($clause) {
		return Caja::where(function ($query) use($clause) {
			$query->where('empresa_id', '=', $clause['empresa_id']);
			if (! empty($clause['formulario']))
				$query->whereIn('formulario', $clause['formulario']);
			if (! empty($clause['estado']))
				$query->whereIn('estado', $clause['estado']);
		})->get();
	}
	function create($created) {

		if(empty($created['caja_id'])){
			$created["uuid_caja"] = Capsule::raw("ORDER_UUID(uuid())");
			$caja = Caja::create($created);
		}else{
			unset($created["numero"]);
			$caja = Caja::find($created['caja_id']);
			$caja->update($created);
		}
		return $caja;

	}
	function update($update) {
		return Caja::update($update);
	}

        function cambiandoSaldo($caja, $saldo_nuevo) {


                if($saldo_nuevo <= (float) $caja->limite){


                    $actualizado["saldo"] = $saldo_nuevo;
                     //$caja = Caja::find($caja->id);
                    $caja->update($actualizado);
                }
	}



	function findByUuid($uuid) {
		return Caja::where('uuid_caja', hex2bin($uuid))->first();
	}
	public function delete($condicion) {
		return Caja::where(function($query) use($condicion) {
			$query->where('empresa_id', '=', $condicion ['empresa_id'] );
		})->delete();
	}
	/**
	 * @function de listar y busqueda
	 */
	public function listar($clause = array(), $sidx = NULL, $sord = NULL, $limit = NULL, $start = NULL) {
		$query = Caja::with(array("centro", "responsable"));

        if(isset($clause['centros_contables'])){
            $query->whereIn('ca_cajas.centro_id' ,$clause['centros_contables']);
            unset($clause['centros_contables']);
        }

		//Si existen variables de limite
		if($clause!=NULL && !empty($clause) && is_array($clause))
		{
			foreach($clause AS $field => $value)
			{
				if($field == "nombre_centro"){
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

	  	if($sidx!=NULL && $sord!=NULL) $query->orderBy($sidx, $sord);
	  	if($limit!=NULL) $query->skip($start)->take($limit);
		return $query->get();
	}

    function agregarComentario($id, $comentarios) {
        $caja = Caja::find($id);
        $comentario = new Comentario($comentarios);
        $caja->comentario_timeline()->save($comentario);
        return $caja;
    }
}

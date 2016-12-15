<?php
namespace Flexio\Modulo\Cajas\Repository;
use Flexio\Modulo\Cajas\Models\Transferencias as Transferencias;
use Flexio\Modulo\Cajas\Models\TransferenciasPagos as TransferenciasPagos;

use Flexio\Modulo\ConfiguracionCompras\Repository\ChequesRepository;

class TransferirCajaRepository{
	  function __construct(){
             $this->ChequesRepository = new ChequesRepository();
             $this->CajasRepository = new CajasRepository();

         }
	function find($id) {
		return Transferencias::find($id);
	}
	function getAll($clause) {
		return Transferencias::where(function ($query) use($clause) {
			$query->where('empresa_id', '=', $clause['empresa_id']);
			if (! empty($clause['estado']))
				$query->whereIn('estado', $clause['estado']);
		})->get();
	}
	function create($created) {
  		$array_pagos = [];

	    $fieldset = array(
	    	"empresa_id" => $created["empresa_id"],
	    	"caja_id" => $created["caja_id"],
	    	"cuenta_id" => $created["cuenta_id"],
	    	"numero" => $created["numero"],
	    	"monto" => $created["monto"],
	    	"fecha" => $created["fecha"],
	    	"creado_por" => $created["creado_por"],
                "transferencia_desde" => isset($created["transferencia_desde"])?$created["transferencia_desde"]:0,
                "tipo_transferencia_hasta" => isset($created["tipo_transferencia_hasta"])?$created["tipo_transferencia_hasta"]:''
	    );


 	    $transferencia = Transferencias::create($fieldset);
            $tipopagos = $created['tipospago'];

	    foreach($tipopagos as $pago){
	      $array_pagos[] = new TransferenciasPagos($pago);
	    }
 	    $transferencia->pagos()->saveMany($array_pagos);

           /* if($created["transferencia_desde"] == 1 )//Crear cheques cuando es Transferencia desde
            {
                $this->createCheque($created);
            }*/
	    return $transferencia;
	}
        function rebajaCaja($transferencia, $caja) {
             return $caja->saldo - $transferencia->monto;
        }
         function subiendoCaja($transferencia, $caja) {
             return $caja->saldo + $transferencia->monto;
        }
	function update($update) {
		return Transferencias::update($update);
	}
	function findByUuid($uuid) {
		return Transferencias::where('uuid_caja', hex2bin($uuid))->first();
	}
	public function delete($condicion) {
		return Transferencias::where(function($query) use($condicion) {
			$query->where('empresa_id', '=', $condicion ['empresa_id'] );
		})->delete();
	}
	/**
	 * @function de listar y busqueda
	 */
	public function listar($clause = array(), $sidx = NULL, $sord = NULL, $limit = NULL, $start = NULL) {

		$query = Transferencias::with(array("pagos.pago_info", "cuenta", "caja"));

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

       public function tranferir($transferir_id = NULL){
            $query = Transferencias::with(array("pagos", "cuenta", "caja"))->where('id', '=', $transferir_id);
            //$query->where('caja_id', '=', $caja_id);
            return $query->get();
        }
}

<?php

namespace Flexio\Repository\ConfiguracionContabilidad;
use Flexio\Modulo\ConfiguracionContabilidad\Models\CajaMenuda as CajaMenuda;
use Flexio\Repository\InterfaceRepository as InterfaceRepository;

// cargar el modelo codeigniter transaccion
class CajaMenudaRepository implements InterfaceRepository {
    
    public function findBy($clause)
    {
        //...
    }
	public function find($id) {
		return CajaMenuda::find($id);
	}
	public function create($create) {
		return CajaMenuda::create($create);
	}
	public function update($update) {
		return CajaMenuda::update($update);
	}
	public function delete($condicion) {
		return CajaMenuda::where(function($query) use($condicion) {
			$query->where('empresa_id', '=', $condicion ['empresa_id'] );
			$query->where('cuenta_id', '=', $condicion ['cuenta_id'] );
		} )->delete();
	}
	public function getAll($empresa = []) {
		if(empty($empresa ))
			return $empresa;
		return CajaMenuda::where($empresa )->get();
	}
	public function tieneCuenta($empresa = []) {
		if(empty($empresa ))
			return false;
		if(CajaMenuda::where($empresa)->get()->count() > 0) {
			return true;
		} else {
			return false;
		}
	}
	public function tienes_transacciones($condicion = []) {
		$caja_menuda = CajaMenuda::where($condicion)->get()->last();
		return $caja_menuda->tienes_transacciones();
	}
}

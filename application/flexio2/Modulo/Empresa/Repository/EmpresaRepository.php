<?php
namespace Flexio\Modulo\Empresa\Repository;

use Flexio\Modulo\Empresa\Models\Empresa;

class EmpresaRepository{

  function getAll($clause=[],$columns=['*']){
    return Empresa::where($clause)->get($columns);
  }

  public function findByUuid($uuid) {
    return Empresa::where('uuid_empresa', hex2bin($uuid))->first();
  }

  function find($id) {
		return Empresa::find($id);
	}

}

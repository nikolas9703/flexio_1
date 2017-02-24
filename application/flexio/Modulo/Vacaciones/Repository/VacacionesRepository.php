<?php
namespace Flexio\Modulo\Vacaciones\Repository;


use Flexio\Modulo\Vacaciones\Models\Vacaciones;

class VacacionesRepository{


  function findByUuid($uuid) {

    	return Vacaciones::where('uuid_planilla',hex2bin($uuid))->first();
  }

	function find($id) {
 		return Vacaciones::find($id);
	}

  }

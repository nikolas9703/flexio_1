<?php
namespace Flexio\Modulo\Talleres\Repository;
use Flexio\Modulo\Talleres\Models\EquipoTrabajoCatalogo;
use Illuminate\Database\Capsule\Manager as Capsule;

class EquipoTrabajoCatalogoRepository {
	
	private $orderfield = "orden";
	private $ordertype = "ASC";
	
	function getEstados() {
		return EquipoTrabajoCatalogo::estados()->orderBy($this->orderfield, $this->ordertype)->get();
	}
}

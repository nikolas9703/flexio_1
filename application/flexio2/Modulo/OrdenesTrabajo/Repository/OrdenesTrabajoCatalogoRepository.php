<?php
namespace Flexio\Modulo\OrdenesTrabajo\Repository;
use Flexio\Modulo\OrdenesTrabajo\Models\OrdenesTrabajoCatalogo;
use Illuminate\Database\Capsule\Manager as Capsule;

class OrdenesTrabajoCatalogoRepository {
	
	private $orderfield = "orden";
	private $ordertype = "ASC";
	
	function getEstados() {
		return OrdenesTrabajoCatalogo::estados()->orderBy($this->orderfield, $this->ordertype)->get();
	}
	function getTiposOrden() {
		return OrdenesTrabajoCatalogo::tipoOrden()->orderBy($this->orderfield, $this->ordertype)->get();
	}
	function getListaTipoPrecio() {
		return OrdenesTrabajoCatalogo::listaPrecio()->orderBy($this->orderfield, $this->ordertype)->get();
	}
	function getFacturable() {
		return OrdenesTrabajoCatalogo::facturable()->orderBy($this->orderfield, $this->ordertype)->get();
	}
	function getOrdenDesde() {
		return OrdenesTrabajoCatalogo::ordenDesde()->orderBy($this->orderfield, $this->ordertype)->get();
	}
}

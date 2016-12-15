<?php
namespace Flexio\Modulo\Plantillas\Repository;
use Flexio\Modulo\Plantillas\Models\PlantillaCatalogo as PlantillaCatalogo;

class PlantillaCatalogoRepository{
	function getPrefijos(){
		return PlantillaCatalogo::where('identificador', 'prefijo')->get(array('id', 'etiqueta'));
	}
	function getEstados(){
		return PlantillaCatalogo::where('identificador', 'estado')->get(array('id', 'etiqueta'));
	}
}

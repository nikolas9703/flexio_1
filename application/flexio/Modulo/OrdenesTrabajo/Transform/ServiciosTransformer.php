<?php
namespace Flexio\Modulo\OrdenesTrabajo\Transform;

use Flexio\Modulo\OrdenesTrabajo\Models\Servicios;

class ServiciosTransformer {
	public function crearInstancia($linesItems) {
		$model = [];
		foreach ($linesItems as $item){
			
			unset($item["piezas"]);
			
			if(isset($item['id'])) {
				array_push($model, $this->setData($item));
			}else {
				array_push($model, new Servicios($item));
			}
		}
		return $model;
	}
	function setData($item) {
		$line = Servicios::find ( $item ['id'] );
		foreach ( $item as $key => $value ) {
			
			if ($key != 'id')
				$line->{$key} = $value;
		}
		return $line;
	}
}

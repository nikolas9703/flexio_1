<?php
namespace Flexio\Modulo\OrdenesTrabajo\Transform;

use Flexio\Modulo\OrdenesTrabajo\Models\Piezas;

class PiezasTransformer {
	public function crearInstancia($linesItems) {
		$model = [];
		foreach($linesItems AS $item) {
			if(isset($item['id'])) {
				array_push($model, $this->setData($item));
			} else {
				array_push($model, new Piezas($item));
			}
		}
		return $model;
	}
	function setData($item) {
		$line = Piezas::find($item['id']);
		foreach ( $item as $key => $value ) {
			if($key=='' || $key =='id' || $value==''){
				continue;
			}
			
			$line->{$key} = $value;
		}
		return $line;
	}
}

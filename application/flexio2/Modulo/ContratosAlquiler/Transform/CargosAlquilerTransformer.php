<?php
namespace Flexio\Modulo\ContratosAlquiler\Transform;
use Flexio\Modulo\ContratosAlquiler\Models\CargosAlquiler;
class CargosAlquilerTransformer{
	public function crearInstancia($linesItems){
		$model = [];
		foreach ($linesItems AS $item){
			if(isset($item['id'])) {
				array_push($model, $this->setData($item));
			}else {
				array_push($model, new CargosAlquiler($item));
			}
		}
		return $model;
	}
	function setData($item) {
		$line = CargosAlquiler::find ( $item ['id'] );
		foreach ( $item as $key => $value ) {
			if ($key != 'id')
				$line->{$key} = $value;
		}
		return $line;
	}
}

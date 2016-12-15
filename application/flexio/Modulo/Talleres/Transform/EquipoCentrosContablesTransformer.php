<?php
namespace Flexio\Modulo\Talleres\Transform;

use Flexio\Modulo\Talleres\Models\EquipoCentrosContables;

class EquipoCentrosContablesTransformer {
	public function crearInstancia($linesItems) {
		$model = [];
		foreach($linesItems AS $item) {
			if(isset($item['id'])) {
				array_push($model, $this->setData($item));
			} else {
				array_push($model, new EquipoCentrosContables($item));
			}
		}
		return $model;
	}
	function setData($item) {
		$line = EquipoCentrosContables::find($item['id']);
		foreach ( $item as $key => $value ) {
			if($key=='' || $key =='id' || $value==''){
				continue;
			}
			
			$line->{$key} = $value;
		}
		return $line;
	}
}

<?php
namespace Flexio\Jobs\Pedidos;

use Carbon\Carbon;
use Flexio\Library\Util\Utiles;
use Flexio\Modulo\Pedidos\Models\Pedidos;

/**
* 
*/
class PedidosDescargar 
{	
	private $pedidosModel;
	private $fillableNames= array(
		['Fechas' => 'fecha_creacion'],
		['Número' => 'numero'],
		['Referencia' => 'referencia'],
		['Centro' => 'uuid_centro','foreignKey'=>'centro_contable']
		);
	function __construct()
	{
		# code...
		$this->pedidosModel = new Pedidos();
		
	}

	function getFillableNames(){
		return $this->fillableNames;
	}

	function getAllRows($clause){
		$resulset = [];
		$rowOptions=$this->fillableNames;
		$rows = $this->pedidosModel->where($clause)
		->limit(5)
		->get();
		if(empty($rows)){
			exit;
		}	
		foreach ($rows as $key => $data) {
				
			$tmp = array();
			foreach ($rowOptions as $tableColumn) {
					# code...
				foreach ($tableColumn as $name => $columnName) {
						# code...
					
					$values=[$name => $name == "foreignKey" ? $data->$columnName->nombre: $data->$columnName];
					 array_push($tmp, $values);
					 
				}
				$resulset[$key] = $tmp;
				

			}

		}

		return $resulset;
	}

}
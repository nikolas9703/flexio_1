<?php
namespace Flexio\Jobs\ContratosAlquiler;

use Carbon\Carbon;
use Flexio\Library\Util\Utiles;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\ContratosAlquiler\Repository\CargosRepository;
use Flexio\Jobs\ContratosAlquiler\CortesFacturacion;

class CronCortesFacturacionAlquiler {

	public $CargosRepository;
	public $CortesFacturacion;

	public function __construct() {
		$this->CortesFacturacion = new CortesFacturacion();
		$this->CargosRepository = new CargosRepository();
	}

	public function ejecutar() {

		//Listado de contratos por facturar
		$contratos = $this->cargosPorFacturar();
		//dd($contratos);

		if(empty($contratos)){
			return false;
		}

		//Recorrer y verificar los tiempos
		//de ejecucion de los contratos de
		//de alquiler de cada empresa.
		foreach ($contratos AS $contrato) {
			//Contrato Info
			$items_cargos = !empty($contrato["items"]) ? $contrato["items"] : array();

			//Verificar si tiene cargos
			//en los items del contrato.
			if(empty($items_cargos)){
				continue;
			}

			//Verificar contrato a facturar
			$this->CortesFacturacion->verificar($contrato);
		}
	}

	/**
	 * Retorna los items de cargos de contratos de alquiler
	 * por facturar, agrupados por empresa.
	 *
	 * @return Array
	 */
	private function cargosPorFacturar() {
		return $this->CargosRepository->getCargosDeContratoPorfacturar(NULL, true);
	}
}

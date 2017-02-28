<?php
namespace Flexio\Jobs\ContratosAlquiler;

use Carbon\Carbon;
use Flexio\Library\Util\Utiles;
use Flexio\Jobs\ContratosAlquiler\CargosProgramados;
use Flexio\Jobs\ContratosAlquiler\CargosAnticipados;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\EntregasAlquiler\Models\EntregasAlquiler;
use Flexio\Modulo\ContratosAlquiler\Models\CargosAlquiler;
use Flexio\Modulo\DevolucionesAlquiler\Models\DevolucionesAlquilerCatalogos;

class CronCargosAlquiler {

	protected $CargosProgramados;
	protected $CargosAnticipados;

	public function __construct() {
		$this->CargosProgramados = new CargosProgramados();
		$this->CargosAnticipados = new CargosAnticipados();
	}

	public function ejecutar() {

		//Listado de items entregados
		$entregados = $this->alquileresEntregados();

		if(empty(collect($entregados)->toArray())){
			return false;
		}
		//dd($entregados);

		//Recorrer y verificar items entregados
		$contador = 1;
		foreach ($entregados AS $entrega) {

			$items = !empty($entrega["items"]) ? $entrega["items"] : array();
			$facturar_contra_entrega = !empty($entrega["facturar_contra_entrega"]) ? $entrega["facturar_contra_entrega"] : "";

			//----------------------------
			// Verificar si la entrega
			// tiene items
			//----------------------------
			if(empty($items)){
				continue;
			}
			//------------------------------
			// Verificar si el contrato debe
			// ser facturado contra entrega.
			//------------------------------
			if(preg_match("/si/i", $facturar_contra_entrega)){

				//Calcular cantidad de cargos a generar
				$this->CargosAnticipados->calcular($entrega);
				continue;
			}

			//------------------------------
			// Los Cargos Programados se ejecutan
			// segun el el tiempo transcurrido entre
			// la fecha de entrega y el periodo tarifario.
			//------------------------------
			$this->CargosProgramados->registrar($entrega);

			$contador++;

			//finalizar procesos
			if($contador==count($entregados)){
				exec("kill $(ps aux | grep '[p]hp' | awk '{print $2}')");
			}
		}
	}

	/**
	 * Retorna las entregas con estado "Entregado"
	 * con su contrato de alquiler relacionado
	 * y sus items que no hallan sido devuelto.
	 *
	 */
	public function alquileresEntregados() {

		//Entregas
		$ResultEntregas = EntregasAlquiler::with(array("contrato_alquiler.corte_facturacion","contrato_alquiler.facturar_contra_entrega","contrato_alquiler.calculo_costo_retorno","contrato_alquiler.contratos_items", "contrato_alquiler.contratos_items.ciclo", "contrato_alquiler.contratos_items.item", "contrato_alquiler.contratos_items.item.precios_alquiler"))->estadoEntregado()->whereHas('contrato_alquiler', function($contratos_items) {
			$contratos_items->estadoValido();
		})->get()->toArray();

		$entregados = array();
		if(!empty($ResultEntregas)){

			$i=0;
			foreach ($ResultEntregas AS $entrega) {

				$entrega_id 			= !empty($entrega["id"]) ? $entrega["id"] : "";
				$contratos_id 		= !empty($entrega["contrato_alquiler"]["id"]) ? $entrega["contrato_alquiler"]["id"] : "";
				$contratos_items 	= !empty($entrega["contrato_alquiler"]["contratos_items"]) ? $entrega["contrato_alquiler"]["contratos_items"] : "";
				$corte_facturacion = !empty($entrega["contrato_alquiler"]["corte_facturacion"]) ? $entrega["contrato_alquiler"]["corte_facturacion"]["valor"] : "";
				$dia_corte = !empty($entrega["contrato_alquiler"]["dia_corte"]) ? $entrega["contrato_alquiler"]["dia_corte"] : "";
				$facturar_contra_entrega = !empty($entrega["contrato_alquiler"]["facturar_contra_entrega"]) ? $entrega["contrato_alquiler"]["facturar_contra_entrega"]["valor"] : "";
				$calculo_costo_retorno = !empty($entrega["contrato_alquiler"]["calculo_costo_retorno"]) ? $entrega["contrato_alquiler"]["calculo_costo_retorno"]["valor"] : "";
				$lista_precio_alquiler_id = !empty($entrega["contrato_alquiler"]["lista_precio_alquiler_id"]) ? $entrega["contrato_alquiler"]["lista_precio_alquiler_id"] : "";
				$fecha_entrega 		= !empty($entrega["fecha_entrega"]) ? $entrega["fecha_entrega"] : "";
				$empresa_id 			= !empty($entrega["empresa_id"]) ? $entrega["empresa_id"] : "";

				// Info de Entrega
				$entregados[$i]["entrega_id"] = $entrega_id;
				$entregados[$i]["empresa_id"]	= $empresa_id;
				$entregados[$i]["fecha_entrega"] 	= $fecha_entrega;
				$entregados[$i]["dia_corte"] = $dia_corte;
				$entregados[$i]["corte_facturacion"] 	= $corte_facturacion;
				$entregados[$i]["facturar_contra_entrega"] 	= $facturar_contra_entrega;
				$entregados[$i]["calculo_costo_retorno"] 	= $calculo_costo_retorno;
				$entregados[$i]["lista_precio_alquiler_id"] = $lista_precio_alquiler_id;

				//verificar que items de contrato
				//no este vacio
				if(empty($contratos_items)){
					continue;
				}



				$j=0;
				foreach ($contratos_items AS $item) {

					$item_entrega_id = !empty($item["contratos_items_detalles_entregas"][0]["operacion_id"]) ? $item["contratos_items_detalles_entregas"][0]["operacion_id"] : "";

					//Verificar el id de la entrega del item
					//con la entrega actualizar_chosen
					if($entrega_id != $item_entrega_id){
						continue;
					}

					//Info de Items
					$itemsEntregados = !empty($item["contratos_items_detalles_entregas"]) ? $item["contratos_items_detalles_entregas"] : "";
					$devolucionesArray = !empty($item["contratos_items_detalles_devoluciones"]) ? $item["contratos_items_detalles_devoluciones"] : "";

					//Obtner la series de los items entregados
					$series = collect($itemsEntregados)->pluck('serie')->reject(function ($name) { return empty($name); });

					$item_id 		= !empty($item["item_id"]) ? $item["item_id"] : "";
					$cantidad 	= !empty($item["contratos_items_detalles_entregas"][0]["cantidad"]) ? count($item["contratos_items_detalles_entregas"]) : "";
					$tarifa 		= !empty($item["tarifa"]) ? $item["tarifa"] : "";
					$ciclo 			= !empty($item["ciclo"]) ? $item["ciclo"]["valor"] : "";
					$ciclo_id 	= !empty($item["ciclo"]) ? $item["ciclo"]["id"] : "";
					$impuesto 	= !empty($item["impuesto"]) ? $item["impuesto"] : "";
					$descuento 	= !empty($item["descuento"]) ? $item["descuento"] : "";
					$precios_alquiler = !empty($item["item"]["precios_alquiler"]) ? $item["item"]["precios_alquiler"] : array();

					//Entregados
					$entregados[$i]["items"][$j]["item_id"] 	= $item_id;
					$entregados[$i]["items"][$j]["cantidad"] 	= $cantidad;
					$entregados[$i]["items"][$j]["tarifa"] 		= $tarifa;
					$entregados[$i]["items"][$j]["ciclo"] 		= str_replace("tarifa_","",$ciclo);
					$entregados[$i]["items"][$j]["ciclo_id"] 	= $ciclo_id;
					$entregados[$i]["items"][$j]["impuesto"] 	= $impuesto;
					$entregados[$i]["items"][$j]["descuento"]	= $descuento;
					$entregados[$i]["items"][$j]["contrato_id"]	= $contratos_id;
					$entregados[$i]["items"][$j]["precios_alquiler"] = $precios_alquiler;


					if(!empty($series->toArray()) && $series->count() > 0){

						// Verificar series devueltas
						$series_devueltas = CargosAlquiler::where("cargoable_id", $entrega_id)
										->where("empresa_id", $empresa_id)
										->where('devuelto', 1)
										->where("item_id", $item_id)
										->whereIn("serie", $series)
										->get(array("serie"))
										->pluck('serie');

						// Si se han devuelto algunos items serializados
						// obtener las series que no han sido devueltas.
						if(!empty(collect($series_devueltas)->toArray())) {
							$series = $series->diff($series_devueltas);
						}

						$entregados[$i]["items"][$j]["series"] = $series->toArray();

					}else {

						//Items devueltos no serializados
						$devueltos = CargosAlquiler::where("cargoable_id", $entrega_id)
								->where("empresa_id", $empresa_id)
								->where('devuelto', 1)
								->where("item_id", $item_id)
								->get(array("serie"))
								->count();

							// si se han devueltos algunos items
							// restar la cantidad devuelta
							// a la cantidad tota de items.
							/*if($devueltos > 0){
								$entregados[$i]["items"][$j]["cantidad"] 	= $cantidad-$devueltos;
							}*/
					}

					//Array items devueltos
					$devoluciones = !empty($devolucionesArray) ? array_map(function($devolucionesArray) {
						if(empty($devolucionesArray["operacion"]["estado_id"])){
							continue;
						}
						$estadoINFO = DevolucionesAlquilerCatalogos::where('id', $devolucionesArray["operacion"]["estado_id"])->get(array('valor'))->toArray();
						if(!empty($estadoINFO) && preg_match('/devuelto/', $estadoINFO[0]["valor"])){
							return array(
								"cantidad" => $devolucionesArray["cantidad"],
								"serie" => $devolucionesArray["serie"],
								"fecha_devolucion" => $devolucionesArray["operacion"]["fecha_devolucion"]
							);
						}
					}, $devolucionesArray) : array();

					//items devueltos
					$entregados[$i]["items"][$j]["devoluciones"] = $devoluciones;

					$j++;
				}
				$i++;
			}
		}

		return collect($entregados);
	}
}

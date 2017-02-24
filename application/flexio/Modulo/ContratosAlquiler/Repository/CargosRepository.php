<?php
namespace Flexio\Modulo\ContratosAlquiler\Repository;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\ContratosAlquiler\Models\CargosAlquiler;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Inventarios\Models\Items;
use Flexio\Modulo\ContratosAlquiler\Models\ContratosAlquiler;

class CargosRepository{

	function find($id) {
		return CargosAlquiler::find($id);
	}

	private function storeCambiarEstado($cargo, $estado)
	{
		if($estado == "anulado" && !$cargo->se_puede_anular)
		{
			return "<span><i class=\"fa fa-times\"></i> {$cargo->numero} no se puede anular</span><br>";
		}

		$cargo->estado = $estado;
		$cargo->save();
		return "<span><i class=\"fa fa-check\"></i> {$cargo->numero} fue actualizado correctamente</span><br>";
	}

	public function cambiarEstado($cargos, $estado)
	{
		$aux = [
			"success" => true,
			"mensaje" => ""
		];

		foreach($cargos as $cargo)
		{
			$aux["mensaje"] .= $this->storeCambiarEstado($cargo, $estado);
		}

		return $aux;
	}

	function getAll($clause) {
		return CargosAlquiler::where(function ($query) use($clause) {
			$query->where('empresa_id', '=', $clause['empresa_id']);
			if (! empty($clause['formulario']))
				$query->whereIn('formulario', $clause['formulario']);
			if (! empty($clause['estado']))
				$query->whereIn('estado', $clause['estado']);
		})->get();
	}
	function create($created) {

		if(empty($created['caja_id'])){
			$created["uuid_caja"] = Capsule::raw("ORDER_UUID(uuid())");
			$caja = CargosAlquiler::create($created);
		}else{
			unset($created["numero"]);
			$caja = CargosAlquiler::find($created['caja_id']);
			$caja->update($created);
		}
		return $caja;

	}
	function update($update) {
		return CargosAlquiler::update($update);
	}
	function findByUuid($uuid) {
		return CargosAlquiler::where('uuid_caja', hex2bin($uuid))->first();
	}
	public function delete($condicion) {
		return CargosAlquiler::where(function($query) use($condicion) {
			$query->where('empresa_id', '=', $condicion ['empresa_id'] );
		})->delete();
	}

	public function getCargosDeContratoPorfacturar($clause=NULL, $empresa_agrupado=false, $filtrar_todo_estado=NULL) {

		//Cargos por facturar
		$query = CargosAlquiler::with(array("entregas_alquiler", "entregas_alquiler.cliente", "item", "entregas_alquiler.contrato_alquiler", "entregas_alquiler.contrato_alquiler.facturar_contra_entrega", "entregas_alquiler.contrato_alquiler.corte_facturacion", "entregas_alquiler.contrato_alquiler.contratos_items", "entregas_alquiler.contrato_alquiler.contratos_items.item", "entregas_alquiler.contrato_alquiler.contratos_items.impuestoinfo"));

		if(empty($filtrar_todo_estado) && $filtrar_todo_estado == NULL){
			$query->porFacturar();
		}

		$Result = $query->clauseFiltro($clause)->get()->toArray();

		$info = array();
		//Esta condicion es para retornar los cargos agrupados
		//por Empresa.
		if($empresa_agrupado==true) {

			//Agrupar por empresa
			$EmpresaCargosArray = collect($Result)->groupBy(function ($item, $key) {
	    	return $item['empresa_id'];
			});

			if(empty($EmpresaCargosArray)){
				return false;
			}

			$i=0;
			foreach ($EmpresaCargosArray AS $empresa_id => $ContratosArray) {

				//agrupar cargos/items por contrato
				$Contratos = collect($ContratosArray)->groupBy(function ($item, $key) {
		    	return $item['contrato_id'];
				});

				foreach ($Contratos AS $contrato_id => $contrato) {

					$cargos_id_array = $contrato->pluck('id')->reject(function ($name) { return empty($name); });

					//agrupar cargos por cantidad procesada
					$CargosArray = collect($contrato)->groupBy(function ($item, $key) {
			    	return $item['cantidad'];
					});

					list($datos, $items) = $this->armarDatosCargos($CargosArray);

					$info[$i]["items"] = $items;
					$info[$i]["empresa_id"] = $empresa_id;
					$info[$i]["contrato_id"] = $contrato_id;
					$info[$i]["cargos"] = $cargos_id_array;
					$info[$i] = array_merge($datos, $info[$i]);
					$i++;

					/*if($contrato_id!=1){
						dd($info);
					}*/
				}
			}

		} else {

			$cargos_id_array = collect($Result)->pluck('id')->reject(function ($name) { return empty($name); });

			//agrupar cargos por cantidad procesada
			$CargosArray = collect($Result)->groupBy(function ($item, $key) {
	    	return $item['cantidad'];
			});

			if(empty($CargosArray)){
				return false;
			}

			list($datos, $items) = $this->armarDatosCargos($CargosArray);
			$info["items"] = $items;
			$info["cargos"] = $cargos_id_array;
			$info = array_merge($info, $datos);
		}

		return $info;
	}

	/**
	 * Arma los items de contrato de alquiler
	 * agrupado por periodo de cargos.
	 *
	 * @param  Array $CargosArray [description]
	 * @return Array
	 */
	private function armarDatosCargos($CargosArray=array()) {

			if(empty($CargosArray)) {
				return array();
			}
			$CargosArray = collect(array_values( collect($CargosArray)->toArray()));

			//-----------------------------------------------
			// Obtener los cargos de ITEM NO DEVUELTOS
			//-----------------------------------------------
			$cargosCompletos = array();
			$cargosRetorno = array();
			foreach ($CargosArray AS $cargos) {
				foreach ($cargos AS $cargo) {
					if(is_numeric($cargo["devuelto"]) && $cargo["devuelto"]===0){
						$cargosCompletos[] = $cargo;
					}else{
						$cargosRetorno[] = $cargo;
					}
				}
			}

			list($cargos_completos, $info) = $this->armarItemCargos([collect($cargosCompletos)]);
			$items = $cargos_completos;

			//-----------------------------------------------
			// Obtener los cargos de ITEM DEVUELTOS
			// Cargos con tarifa prorrateada/escalonada
			//-----------------------------------------------
			if(!empty($cargosRetorno)){

				$cargosRetornoGroupByItem=array();
				foreach ($cargosRetorno as $retorno) {
				    $cargosRetornoGroupByItem[$retorno["item_id"]][] = $retorno;
				}

				foreach ($cargosRetornoGroupByItem as $cargo) {
					list($cargos_retorno, $info) = $this->armarItemCargos([collect($cargo)]);
					$retornos[] = !empty($cargos_retorno[0]) ? $cargos_retorno[0] : array();
				}
				$items = array_merge($cargos_completos, $retornos);
			}

			return array(
				$info,
				$items
			);
	}

	private function armarItemCargos($cargos) {

		$items = array();
		$info=array();
		$i=0;
		$j=0;

		foreach ($cargos as $cargosarray) {
			if(empty($cargosarray[$i])){
				continue;
			}

			$primercargo = $cargosarray->first();
			$ultimocargo = $cargosarray->last();
			$fecha_primercargo 	= !empty($primercargo["fecha_cargo"]) ? Carbon::parse($primercargo["fecha_cargo"]) : "";
			$fecha_ultimocargo 	= !empty($ultimocargo["fecha_cargo"]) ? Carbon::parse($ultimocargo["fecha_cargo"]) : "";
			$fecha_entrega 			= !empty($cargosarray[0]["entregas_alquiler"]["fecha_entrega"]) ? $cargosarray[0]["entregas_alquiler"]["fecha_entrega"] : "";
			$cantidad_periodos 	= $cargosarray->count();
			$total 							= $cargosarray->sum("total_cargo");
			$rango_fecha 				= !empty($fecha_primercargo) ? $fecha_primercargo->format("d/m/Y") . " - ". $fecha_ultimocargo->format("d/m/Y") : "";

			$empresa_id = !empty($cargosarray[$i]["empresa_id"]) ? $cargosarray[$i]["empresa_id"] : "";
			$cantidad_devuelta = !empty($cargosarray[$i]["cantidad_devuelta"]) ? $cargosarray[$i]["cantidad_devuelta"] : 0;

			$cliente_id	= !empty($cargosarray[0]["entregas_alquiler"]["contrato_alquiler"]["cliente_id"]) ? $cargosarray[0]["entregas_alquiler"]["contrato_alquiler"]["cliente_id"] : "";
			$contrato_id	= !empty($cargosarray[0]["entregas_alquiler"]["contrato_alquiler"]["id"]) ? $cargosarray[0]["entregas_alquiler"]["contrato_alquiler"]["id"] : "";
			$centro_contable_id	= !empty($cargosarray[0]["entregas_alquiler"]["contrato_alquiler"]["centro_contable_id"]) ? $cargosarray[0]["entregas_alquiler"]["contrato_alquiler"]["centro_contable_id"] : "";
			$centro_facturacion_id = !empty($cargosarray[0]["entregas_alquiler"]["contrato_alquiler"]["centro_facturacion_id"]) ? $cargosarray[0]["entregas_alquiler"]["contrato_alquiler"]["centro_facturacion_id"] : "";
			$corte_facturacion_id = !empty($cargosarray[0]["entregas_alquiler"]["contrato_alquiler"]["corte_facturacion_id"]) ? $cargosarray[0]["entregas_alquiler"]["contrato_alquiler"]["corte_facturacion_id"] : "";
			$corte_facturacion = !empty($cargosarray[0]["entregas_alquiler"]["contrato_alquiler"]["corte_facturacion"]["nombre"]) ? $cargosarray[0]["entregas_alquiler"]["contrato_alquiler"]["corte_facturacion"]["nombre"] : "";
			$dia_corte 	= !empty($cargosarray[0]["entregas_alquiler"]["contrato_alquiler"]["dia_corte"]) ? $cargosarray[0]["entregas_alquiler"]["contrato_alquiler"]["dia_corte"] : "";
			$lista_precio_alquiler_id = !empty($cargosarray[0]["entregas_alquiler"]["contrato_alquiler"]["lista_precio_alquiler_id"]) ? $cargosarray[0]["entregas_alquiler"]["contrato_alquiler"]["lista_precio_alquiler_id"] : "";
			$creado_por	= !empty($cargosarray[0]["entregas_alquiler"]["contrato_alquiler"]["created_by"]) ? $cargosarray[0]["entregas_alquiler"]["contrato_alquiler"]["created_by"] : "";
			$facturar_contra_entrega = !empty($cargosarray[0]["entregas_alquiler"]["contrato_alquiler"]["facturar_contra_entrega"]) ? $cargosarray[0]["entregas_alquiler"]["contrato_alquiler"]["facturar_contra_entrega"]["valor"] : "";
			$contratos_items = !empty($cargosarray[$i]["entregas_alquiler"]["contrato_alquiler"]["contratos_items"]) ? $cargosarray[$i]["entregas_alquiler"]["contrato_alquiler"]["contratos_items"] : array();

			if(empty($contratos_items)){
				continue;
			}

			foreach ($contratos_items AS $item) {

				$item_id = !empty($item["item"]["id"]) ? $item["item"]["id"] : "";

				if($cargosarray[$i]["item_id"] != $item_id){
					continue;
				}

				//Dinamico - varios items
				$item_nombre = !empty($item["item"]["nombre"]) ? $item["item"]["nombre"] : "";
				$categoria_id	= !empty($item["categoria_id"]) ? (int)$item["categoria_id"] : "";
				$atributo_id	= !empty($item["atributo_id"]) ? (int)$item["atributo_id"] : "";
				$atributo_text	= !empty($item["atributo_text"]) ? (int)$item["atributo_text"] : "";
				$impuesto	= !empty($item["impuestoinfo"]["impuesto"]) ? $item["impuestoinfo"]["impuesto"] : "";
				$impuesto_id	= !empty($item["impuesto_id"]) ? (int)$item["impuesto_id"] : "";
				$impuesto_total	= !empty($item["impuesto_total"]) ? $item["impuesto_total"] : "";
				$cuenta_id	= !empty($item["cuenta_id"]) ? (int)$item["cuenta_id"] : "";
				$descuento	= !empty($item["descuento"]) ? (int)$item["descuento"] : "";
				$descuento_total	= !empty($item["descuento_total"]) ? (int)$item["descuento_total"] : "";
				$cantidad	= !empty($item["cantidad"]) ? (int)$item["cantidad"] : "";

				$periodo_id		= !empty($item["ciclo_id"]) ? $item["ciclo_id"] : "";
				$periodo 			= !empty($item["periodo_tarifario"]) ? ucFirst(str_replace("_", " ", $item["periodo_tarifario"])) : "";

				$devuelto 		= ($cantidad - $cantidad_devuelta)===0 ? true : false;
				$tarifa 			= !empty($cargosarray[0]["tarifa"]) ? $cargosarray[0]["tarifa"] : "";
				$tarifa 			= $devuelto==false ? $tarifa : $cargosarray[0]["tarifa"];
				$tarifa_monto = $tarifa * $cantidad;

				$tarifa_fecha_desde = !empty($fecha_primercargo) ? $fecha_primercargo->format("Y-m-d H:i:s") : "";
				$tarifa_fecha_hasta = !empty($fecha_ultimocargo) ? $fecha_ultimocargo->format("Y-m-d H:i:s") : "";

				if(preg_match("/si/i", $facturar_contra_entrega) && preg_match("/mensual|15|28|30/i", $periodo)){
					$cantidad_periodos 	= 1;
					$tarifa_fecha_desde = $fecha_entrega->format("Y-m-d H:i:s");
					$rango_fecha 				= $fecha_entrega->format("d/m/Y");

					if(preg_match("/mensual/i", $periodo)){
						$tarifa_fecha_hasta = Carbon::parse($fecha_entrega)->addMonths(1)->format("Y-m-d H:i:s");
						$rango_fecha .= " - ". Carbon::parse($fecha_entrega)->addMonths(1)->format("d/m/Y");
					}else if(preg_match("/15|28|30/i", $periodo)){
						$lapso = intval($periodo);
						$tarifa_fecha_hasta = Carbon::parse($fecha_entrega)->addDays($lapso)->format("Y-m-d H:i:s");
						$rango_fecha .= " - ". Carbon::parse($fecha_entrega)->addDays($lapso)->format("d/m/Y");
					}
				}

				$info = array(
					"centro_contable_id" => $centro_contable_id,
					"centro_facturacion_id" => $centro_facturacion_id,
					"creado_por" => $creado_por,
					"cliente_id" => $cliente_id,
					"fecha_entrega" => $fecha_entrega,
					"contrato_id" => $contrato_id,
					"facturar_contra_entrega" => $facturar_contra_entrega,
					"corte_facturacion_id" => $corte_facturacion_id,
					"corte_facturacion" => $corte_facturacion,
					"lista_precio_alquiler_id" => $lista_precio_alquiler_id,
					"dia_corte" => $dia_corte
				);

				$items[$j] = array(
					"item" => array(
						"id" => $item_id,
						"nombre" => $item_nombre
					),
					"categoria_id" => $categoria_id,
					"atributo_id" => $atributo_id,
					"atributo_text" => $atributo_text,
					"cuenta_id" => $cuenta_id,
					"descuento" => $descuento,
					"impuesto" => $impuesto,
					"impuesto_id" => $impuesto_id,
					"impuesto_total" => $impuesto_total,
					"descuento_total" => $descuento_total,
					"cantidad" 	=> $cantidad,
					"rango_fecha" 		=> $rango_fecha,
					"periodo" => array(
						"id" => $periodo_id,
						"nombre" => $periodo
					),
					"monto" 										=> $tarifa ,
					"tarifa_fecha_desde" 				=> $tarifa_fecha_desde,
					"tarifa_fecha_hasta" 				=> $tarifa_fecha_hasta,
					"tarifa_pactada" 						=> $tarifa,
					"tarifa_monto" 							=> $tarifa_monto,
					"precio_total" 							=> $total,
					"tarifa_cantidad_periodo" 	=> $cantidad_periodos,
				);
				$j++;
			}

			$i++;
		}

		return array(
			$items,
			$info
		);
	}

	/**
	 * @function de listar y busqueda
	 */
	public function listar($clause = array(), $sidx = NULL, $sord = NULL, $limit = NULL, $start = NULL) {
		$query = CargosAlquiler::with(array("entregas_alquiler", "item", "entregas_alquiler.contrato_alquiler"))->clauseFiltro($clause);
	  if($sidx!=NULL && $sord!=NULL) $query->orderBy($sidx, $sord);
	  if($limit!=NULL) $query->skip($start)->take($limit);
		return $query->get();
	}

    function agregarComentario($id, $comentarios) {
        $caja = CargosAlquiler::find($id);
        $comentario = new Comentario($comentarios);
        $caja->comentario_timeline()->save($comentario);
        return $caja;
    }
}

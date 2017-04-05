<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Menu_orm extends Model
{
	protected $table = 'modulos';
	protected $fillable = ['nombre', 'tipo', 'grupo', 'agrupador'];
	protected $guarded = ['id'];
	public $timestamps = false;

	/**
	 * Retorna listado de Estados
	 */
	/*public static function lista(){
		return Capsule::table('col_colaboradores_campos AS colcam')
			->leftJoin('col_colaboradores_cat AS colcat', 'colcat.id_campo', '=', 'colcam.id_campo')
			->where('colcam.nombre_campo', '=', 'estado_id')
			->get(array('colcat.id_cat', 'colcat.etiqueta'));
	}*/

	/**
	 * Retorna listado (Menu Superior)
	 *
	 * El menu superior se arma en base a la columna
	 * agrupador.
	 *
	 */
	public static function lista_menu_superior() {
		$results = Capsule::table('modulos')
				->where('estado', 1)
				->where('tipo', 'addon')
				->distinct()
				->orderBy('agrupador_orden','desc')
				->get(array('agrupador','controlador', 'menu','agrupador_orden'));

		$menu_agrupadores = array();
		if(!empty($results))
		{
			foreach($results AS $result)
			{
				if(empty($result->menu)){
					continue;
				}

				$agrupadores = (array)json_decode($result->agrupador);

				if(!empty($agrupadores["nombre"]) && is_array($agrupadores["nombre"]))
				{
					$j=0;
					foreach($agrupadores["nombre"] AS $agrupador)
					{
						if(!is_string($agrupador)){

							foreach ((array)$agrupador AS $nombre_agrupador => $extra_info){

								$menuhash = trim(strtolower($nombre_agrupador));
								$key = Util::multiarray_buscar_valor($menuhash, "grupo", $menu_agrupadores);

								if(!empty($menu_agrupadores[$key])){

									//insertar modulo en el arreglo
									$menu_agrupadores[$key]["modulos"][] = $result->controlador;

								}else{
									$menu_agrupadores[] = array(
										"grupo" => $menuhash,
										"nombre" => $nombre_agrupador,
										"modulos" => array($result->controlador)
									);
									$j++;
								}
							}
						}
						else{

							$menuhash = trim(strtolower($agrupador));
							$key = Util::multiarray_buscar_valor($menuhash, "grupo", $menu_agrupadores);

							if(!empty($menu_agrupadores[$key])){

								//insertar modulo en el arreglo
								$menu_agrupadores[$key]["modulos"][] = $result->controlador;
							}else{
								$menu_agrupadores[] = array(
										"grupo" => $menuhash,
										"nombre" => $agrupador,
										"modulos" => array($result->controlador)
								);
								$j++;
							}
						}
					}
				}
			}

		}

		return $menu_agrupadores;
	}

	/**
	 * Retorna listado (Menu Lateral)
	 *
	 * El menu laterar se arma en base al nombre del modulo
	 * y se agrupa en base a la columna grupo.
	 */
	public static function lista_menu_lateral($agrupador=NULL) {
		if($agrupador==NULL){
			return false;
		}
		$a = array();
		$results = Capsule::table('modulos')
				->where('estado', 1)
				->where('agrupador', 'LIKE', "%$agrupador%")
				->distinct()
				->get(array('agrupador', 'nombre', 'grupo', 'menu', 'controlador'));

		$menu = array();
		if(!empty($results))
		{
			foreach($results AS $result)
			{
				$menu_data = (array)json_decode($result->menu, true);

				$grupo = $result->grupo;
				$controlador = $result->controlador;

				if(empty($grupo) || empty($menu_data["link"])){

					continue;
				}

				//Verificar si existen otros modulos con este agrupador
				//si existe otros modulo, quiere decir que hay que agrupar
				//el modulo, de lo contrario no va agrupado.
				$agrupar = Capsule::table('modulos')
						->where('estado', 1)
						->where('grupo', "$grupo")
						->distinct()
						->get(array('id'));

				//Verificar si es un arreglo de links
				if(Util::is_two_dimensional($menu_data["link"]))
				{
					foreach($menu_data["link"] AS $menudata)
					{
						$nombre = !empty($menudata["nombre"]) ? $menudata["nombre"] : $result->nombre;
						$url = !empty($menudata["url"]) && !empty($menudata["url"]) ? $menudata["url"] : "";
						$orden = !empty($menudata["link"]) && !empty($menudata["orden"]) ? $menudata["orden"] : 0;

						$key = Util::multiarray_buscar_valor($controlador, "controlador", $menu);

						if(empty($menu[$key])){
							$a[] = $key;
							$menu[$grupo]["link"][] = array(
								"nombre" => $nombre,
								"controlador" => $controlador,
								"url" => $url,
								"orden" => $orden
							);
						}
					}

				}else {

					$nombre = !empty($menu_data["link"]["nombre"]) && !empty($menu_data["link"]["nombre"]) ? $menu_data["link"]["nombre"] : $result->nombre;
					$url = !empty($menu_data["link"]) && !empty($menu_data["link"]["url"]) ? $menu_data["link"]["url"] : "";
					$orden = !empty($menu_data["link"]) && !empty($menu_data["link"]["orden"]) ? $menu_data["link"]["orden"] : 0;

					$key = Util::multiarray_buscar_valor($controlador, "controlador", $menu);

					if(!empty($menu[$key])){
                        $a[] = $key;
						$menu[$grupo]["link"][$key] = array(
							"nombre" => $nombre,
							"controlador" => $controlador,
							"url" => $url,
							"orden" => $orden
						);
					}else{

						$menu[$grupo]["link"][] = array(
							"nombre" => $nombre,
							"controlador" => $controlador,
							"url" => $url,
							"orden" => $orden
						);
					}
				}

				//Obtener el orden del grupo
				//Si tiene configurado un orden segun
				//el agrupador donde se encuentra
				$agrupadores = Util::arrayCastRecursive(json_decode($result->agrupador));

				$grupo_orden = 0;
				if(!empty($agrupadores) && is_array($agrupadores)){
					if(!empty($agrupadores["nombre"][0]) && is_array($agrupadores["nombre"][0])){
						foreach($agrupadores["nombre"][0] AS $agrupadornombre => $info){
							if(preg_match("/($agrupador)/i",  strtolower($agrupadornombre))) $grupo_orden = $info["grupo_orden"];
						}
					}else{
						foreach($agrupadores["nombre"] AS $agrupadornombre => $array){
							if(preg_match("/($agrupador)/i",  strtolower($agrupadornombre))) $grupo_orden = $array["grupo_orden"];
						}
					}
				}

				$menu[$grupo]["grupo_orden"] = $grupo_orden;
				$menu[$grupo]["grupo"] = $grupo;
				$menu[$grupo]["agrupar"] = trim($nombre)!=trim($grupo) ? 1 : 0;


				//Ordenar Enlaces
				$links = $menu[$grupo]["link"];
				unset($menu[$grupo]["link"]);
				usort($links, function($a, $b) {
					return $a['orden'] - $b['orden'];
				});
				$menu[$grupo]["link"] = $links;
			}
		}

		return $menu;
	}

}

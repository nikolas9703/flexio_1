<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Menu
 *
 * Modulo para generar menu dinamico
 *
 * @package    PensaApp
 * @subpackage Controller
 * @category   Controllers
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  23/11/2015
 */

use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

class Menu extends CRM_Controller
{
    /**
     * @var
     */
	private $cache;

	/**
	 * @var int
	 */
	protected $empresa_id;
    protected $moduloActual;
    protected $response;
	/**
	 *
	 */
	function __construct() {
		parent::__construct();

		$this->load->model('menu_orm');
		$this->load->model('usuarios/empresa_orm');

		//Obtener el id_empresa de session
		$uuid_empresa = $this->session->userdata('uuid_empresa');
		$empresa = Empresa_orm::findByUuid($uuid_empresa);
        $this->moduloActual = new Illuminate\Http\Response;
        $this->response = new Response();
		$this->empresa_id = $empresa->id;
		//Inicializar variable cache
       	$this->cache = Cache::inicializar();
    }

    /**
     * Seleccionar Lista de Menu Superior
     */
    public function navbar() {
        //Consultar Cache de Menu
        //$menus = $this->cache->get("Menutop-". $this->uuid_usuario . $this->empresa_id);

        //Verificar si existe Cache
        //if($menus == null){
        	$menus = Menu_orm::lista_menu_superior();

        	//Si no existe guardar cache
        	$this->cache->set("Menutop-". $this->uuid_usuario . $this->empresa_id, $menus);
        //}

        //Verificar Permisos
        if(!empty($menus)){
        	foreach ($menus AS $key => $menu){

        		//verificar si tiene permiso al menu
        		if(!Auth::has_module_access($menu["modulos"])){

        			//Si no tiene permiso quitar el menu de la lista.
        			unset($menus[$key]);
        		}else{

        			//Eliminar indice modulos
        			unset($menus[$key]["modulos"]);
        		}
        	}
        }

        $json = json_encode($menus);
    	echo $json;
    	exit;
    }

    /**
     * Seleccionar Lista de Menu Lateral
     */
    public function sidebar() {
    	$menu = array();
    	$agrupador = $this->input->post('grupo', true);
        //echo $agrupador;
      // $agrupador tiene el nombre del menu en la barra superior
        //$this->response->headers->clearCookie('modulo_padre');
    	$hashcache = str_replace(" ", "", $agrupador);
        //$this->response->headers->setCookie(new Cookie('modulo_padre', $hashcache));
        $this->session->set_userdata('modulo_padre', $hashcache);
    	//Consultar Cache de Menu
    	$menus = $this->cache->get("Menuside-". $hashcache ."-". $this->uuid_usuario . $this->empresa_id);

    	//Verificar si existe Cache
    	if($menus == null){
    		$menus = Menu_orm::lista_menu_lateral($agrupador);

    		//Si no existe guardar cache
    		$this->cache->set("Menuside-". $hashcache ."-". $this->uuid_usuario . $this->empresa_id, $menus);
    	}

    	if(empty($menus)){
    		return false;
    	}

    	//Ordenar arreglo por el campo orden
    	usort($menus, function($a, $b) {
    		return $a['grupo_orden'] - $b['grupo_orden'];
    	});

    	$i=0;
    	foreach($menus AS $index => $menuarray)
    	{
    		$grupo = !empty($menuarray["grupo"]) ? $menuarray["grupo"] : "";

    		if(!empty($menuarray["agrupar"]) && $menuarray["agrupar"] == 1)
    		{
    			$menu[$i]["nombre"] = $grupo;
    			$menu[$i]["url"] = "#";
    			$menu[$i]["classnombre"] = "dropdown";

    			$j=0;
    			foreach($menuarray["link"] AS $key => $menudata){

    				if(is_string($key)){
    					continue;
    				}

    				$url = !empty($menudata["url"]) ? base_url($menudata["url"]) : base_url($menudata["controlador"]."/index");

    				//verificar si tiene permiso al menu
    				if(!Auth::has_permission("acceso", $menudata["url"])){
    					continue;
    				}

    				$menu[$i]["navsecond"][$j]["nombre"] = $menudata["nombre"];
    				$menu[$i]["navsecond"][$j]["url"] = $url;
    				$j++;
    			}
    			$i++;
    		}
    		else
    		{
    			foreach($menuarray["link"] AS $key => $menudata){

    				if(is_string($key)){
    					continue;
    				}

    				$url = !empty($menudata["url"]) ? base_url($menudata["url"]) : base_url($menudata["controlador"]."/index");

    				//verificar si tiene permiso al menu
    				if(!Auth::has_permission("acceso", $menudata["url"])){
                        continue;
    				}

    				$menu[$i]["nombre"] = $menudata["nombre"];
    				$menu[$i]["url"] = $url;
    				$i++;
    			}
    		}
    	}

    	$json = json_encode($menu);
    	echo $json;
    	exit;
    }

    /**
     * Limpiar cache de menu de usuario
     *
     * @return void
     */
    public static function limpiar_cache() {
    	$this->cache->delete("Menuside-". $hashcache ."-". $this->uuid_usuario . $this->empresa_id);
    	$this->cache->delete("Menutop-". $this->uuid_usuario . $this->empresa_id);
    }
}

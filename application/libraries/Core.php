<?php
/**
 * Core
 *
 * Libreria para Administrar los que son por defecto en el sistema.
 *
 * @package    PensaApp
 * @subpackage Library
 * @category   Libraries
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @version    1.7 - 07/12/2015
 * @copyright  @jluispinilla
 * @since      23/10/2014
 *
 */
use Illuminate\Database\Capsule\Manager as Capsule;

class Core
{
	protected static $ci;

    private static $modulos = array();

    private static $modulos_cargados = false;

    private static $modulos_registrados = false;

    protected static $cache;

    private static $uuid_empresa;

	function __construct(){

		self::$ci =& get_instance();

		//Inicializar variable cache
		self::$cache = Cache::inicializar();

		//Establecer uuid de empresa
		self::$uuid_empresa = self::$ci->session->userdata('uuid_empresa');

		//Obtener registro de cache
		self::$modulos = self::$cache->get("lista-modulos-". self::$uuid_empresa);

		//Verificar si ya estan los modulos cargados o no
		if(empty(self::$modulos)){
			//Cargar Modulos
			self::cargar_modulos();
		}

		self::$ci->load->model('modulos/modulos_orm');
		self::$ci->load->model('modulos/recursos_orm');
		self::$ci->load->model('modulos/permisos_orm');

		//Obtener registro de cache
		self::$modulos_registrados = self::$cache->get("registro-modulos-". self::$uuid_empresa);

		//Verificra si ya se registraron los modulos o no.
        if(self::$modulos_registrados == null){
        	//Registrar Modulos
        	self::registrar_modulos();
		}
	}

	/**
     * Esta funcion lee los archivos config.php y route.php de cada modulo
     * Para guardar la informacion de cada modulo, permisos y rutas.
     *
     */
    public static function registrar_modulos()
	{
		if(empty(self::$uuid_empresa)){
			return false;
		}

		if(!empty(self::$modulos))
        {
            $modulo_id = "";
            foreach (self::$modulos AS $index => $modulo)
            {
            	$nombre_modulo = strtolower($modulo["modulo"]["nombre"]);
							$controlador = strtolower($modulo["modulo"]["controlador"]);

                //No registrar el modulo "login"
                if (preg_match("/login/i", $nombre_modulo)) {
                	continue;
                }

                //Verificar si el modulo aun no esta registrado
                $modulo_id = self::verificar_modulo($nombre_modulo);

                //Verificar si el modulo no existe
                //Si es un plugin solo registrarlo en la tabla de modulos como inactivo
                if(!empty($modulo_id)){

                    //Actualizar informacio del modulo.
                    self::actualizar_modulo($modulo_id, $modulo['modulo']);

                } else{

                	//De lo contrario, Guardar informacio del modulo.
                	$modulo_id = self::guardar_modulo($modulo['modulo']);
                }

                //Obtener arreglo de permisos
                $permisos = (!empty($modulo['modulo']) && !empty($modulo['modulo']['permisos']) ? $modulo['modulo']['permisos'] : array());

                //Verificar si aun no esta registrados los $routes de este modulo
                if(!empty($modulo_id) && isset($modulo['routes']) && !empty($modulo['routes']))
                {
                    foreach ($modulo['routes'] AS $key => $value)
                    {
                        //No registrar url's ajax
                    		if(preg_match("/ajax/i", $key) || preg_match("/oculto/", $key) || preg_match("/filtar/i", $key) || preg_match("/subpanel/i", $key)){
													continue;
												}

												$checkResource = self::verificar_recurso($modulo_id, $key);


                        if(empty($checkResource))
                        {
                            //Si no existe este route, insertarlo como un recurso nuevo
                            $recurso_id = self::guardar_recurso($modulo_id, $key);

                            //Guardar los permisos para este recurso
                            self::guardar_recurso_permisos($recurso_id, $permisos, $controlador);

                            //Activar modulo del core
                        	//self::activar_modulo($modulo_id);
                        }
                        else
                        {
                        	$recurso_id = $checkResource[0]["id"];

                            //Guardar los permisos para este recurso
                            self::actualizar_recurso_permisos($checkResource[0]["id"], $permisos, $controlador);

                            //Activar modulo del core
                        	//self::activar_modulo($modulo_id);
                    	}

                    	$recursosArray["noexiste"][] = array($recurso_id, $checkResource, $permisos);

                    }
                }
            }

            //Guardar en cache
            self::$cache->set("registro-modulos-". self::$uuid_empresa, true, 3600);
        }
	}

	/**
	 * Verificar si el modulo ya existe en DB.
	 *
	 * @param  string $nombre
	 * @return array
	 */
    public static function verificar_modulo($nombre_modulo)
    {
    	$result = Modulos_orm::where("nombre", $nombre_modulo)->get()->toArray();
    	return !empty($result[0]["id"]) ? $result[0]["id"] : "";
    }

    /**
     * Guardar informacion del modulo en DB.
     *
     * @param  array $modulo
     * @return int
     */
    private static function guardar_modulo($modulo)
    {

         if(empty($modulo)){
            return false;
        }

        $nombre_modulo = (!empty($modulo['nombre']) ? $modulo['nombre'] : "");

        $fieldset = array(
            'nombre'        => $nombre_modulo,
            'descripcion'   => (!empty($modulo['descripcion']) ? $modulo['descripcion'] : ""),
            'controlador'   => (!empty($modulo['controlador']) ? strtolower($modulo['controlador']) : ""),
        	'icono'       	=> (!empty($modulo['icono']) ? $modulo['icono'] : ""),
        	'version'       => (!empty($modulo['version']) ? $modulo['version'] : ""),
        	'tipo'			=> (!empty($modulo['tipo']) ? $modulo['tipo'] : ""),
        	'grupo'        	=> (!empty($modulo['grupo']) ? $modulo['grupo'] : ""),
        	'agrupador'		=> (!empty($modulo['agrupador']) ? '{"nombre":'. (count($modulo['agrupador']) > 1 ? json_encode($modulo['agrupador']) : "[".json_encode($modulo['agrupador'])."]")  .'}' : ""),
        	'agrupador_orden'	=> (!empty($modulo['agrupador_orden']) ? $modulo['agrupador_orden'] : "0"),
        	'menu'			=> (!empty($modulo['menu']) ? '{"link":'. (count($modulo['menu']) > 1 ? json_encode($modulo['menu']) : "[".json_encode($modulo['menu'])."]")  .'}' : ""),
        );
         return Capsule::transaction(function() use ($fieldset){
        	try{
        		$cargo = Modulos_orm::create($fieldset);
        		return $cargo->id;
        	} catch (Illuminate\Database\QueryException $e) {
        		log_message("error", "LIBRERIA: ". __METHOD__ .", Linea: ". __LINE__ .", Error No: ". $e->errorInfo[1] .", Mensaje:  --> ". $e->getMessage().".\r\n");
        	}
        });
    }

    /**
     * Actualizar informacion del modulo en DB.
     *
     * @param  int $modulo_id
     * @return int
     */
    private static function actualizar_modulo($modulo_id=NULL, $modulo)
    {
    	if($modulo_id==NULL){
    		return false;
    	}

    	$nombre 		= (!empty($modulo['nombre']) ? $modulo['nombre'] : "");
    	$descripcion   	= (!empty($modulo['descripcion']) ? $modulo['descripcion'] : "");
    	$controlador  	= (!empty($modulo['controlador']) ? strtolower($modulo['controlador']) : "");
    	$icono       	= (!empty($modulo['icono']) ? $modulo['icono'] : "");
    	$version      	= (!empty($modulo['version']) ? $modulo['version'] : "");
    	$tipo			= (!empty($modulo['tipo']) ? $modulo['tipo'] : "");
    	$grupo     		= (!empty($modulo['grupo']) ? $modulo['grupo'] : "");
    	$agrupador		= (!empty($modulo['agrupador']) ? '{"nombre":'. (count($modulo['agrupador']) > 1 ? json_encode($modulo['agrupador']) : "[".json_encode($modulo['agrupador'])."]")  .'}' : "");
    	$menu			= (!empty($modulo['menu']) ? '{"link":'. (count($modulo['menu']) > 1 ? json_encode($modulo['menu']) : "[".json_encode($modulo['menu'])."]")  .'}' : "");

    	$fieldset = array(
    		'nombre'        => $nombre,
    		'descripcion'   => $descripcion,
    		'controlador'   => $controlador,
    		'icono'       	=> $icono,
    		'version'       => $version,
    		'tipo'			=> $tipo,
    		'grupo'        	=> $grupo,
    		'agrupador'		=> $agrupador,
    		'menu'			=> $menu
    	);

    	Capsule::transaction(function() use ($modulo_id, $fieldset){
    		try{
	    		Modulos_orm::where("id", $modulo_id)->update($fieldset);
    		} catch (Illuminate\Database\QueryException $e) {
    			log_message("error", "LIBRERIA: ". __METHOD__ .", Linea: ". __LINE__ .", Error No: ". $e->errorInfo[1] .", Mensaje:  --> ". $e->getMessage().".\r\n". PHP_EOL);
    		}
    	});

    }

    private static function activar_modulo($modulo_id)
    {
    	if(empty($modulo_id)){
    		return false;
    	}

    	$fieldset = array(
    		'estado' => 1
    	);
    	$clause = array(
    		"id" => $modulo_id
    	);
    	self::$ci->db->where($clause)->update('modulos', $fieldset);
    }

    /**
     * Consultar la tabla de recursos si existe el nombre de route solicitado
     *
     * @param  string $route
     * @return array  resource_id
     */
    public static function verificar_recurso($modulo_id, $route)
    {
    	$result = Recursos_orm::where("nombre", $route)->where("modulo_id", $modulo_id)->get()->toArray();
    	return $result;
    }

    /**
     * Consultar la informacion de un route ya registrado.
     *
     * @param  string $recurso_id
     * @return array
     */
    private static function seleccionar_recurso($recurso_id)
    {
        $fields = array(
            "id",
            "nombre",
        );
        $clause = array(
            "id" => $recurso_id
        );
        $result = self::$ci->db->select($fields)
                    ->from('recursos')
                    ->where($clause)
                    ->get()
                    ->result_array();
        return $result;
    }

    /**
     * Guardar el route de un modulo en la tabla de recurso.
     *
     * @param  int $modulo_id
     * @param  string $route
     * @return int  resource_id
     */
    private static function guardar_recurso($modulo_id, $route)
    {
		if(empty($route)){
			return false;
		}

		try {
			$fieldset = array(
	            'nombre' => $route,
	            'modulo_id' => $modulo_id
	        );
	        $result = Recursos_orm::create($fieldset);
	        return $result->id;
        } catch (Exception $e) {
        	log_message("error", "function guardar_recurso LIBRERIA: Core --> ". $e->getMessage().".\r\n");
        }
    }

    /**
     * Guardar los permisos asignados a un recurso.
     *
     * @param  int $recurso_id
     * @param  array $permissions
     * @param  array $module_name
     * @return none
     */
    public static function guardar_recurso_permisos($recurso_id, $permisos, $nombre_modulo=NULL)
    {
        if(empty($permisos)){
            return false;
        }

        $checkRecurso = self::seleccionar_recurso($recurso_id);

        if(empty($checkRecurso)){
        	return false;
        }

        $nombre_recurso  = $checkRecurso[0]['nombre'];
        $nombre_recurso  = str_replace("/(:num)", "", $nombre_recurso);
        $nombre_recurso  = str_replace("/(:any)", "", $nombre_recurso);
        $nombre_recurso  = str_replace("/(:an", "", $nombre_recurso);
        $nombre_recurso  = str_replace(strtolower($nombre_modulo)."/", "", $nombre_recurso);

        foreach ($permisos AS $nombre_permiso => $alias)
        {
            if($nombre_permiso == "" || $nombre_recurso == ""){
            	continue;
            }

            //Verificar si el permiso tiene el caracter rayita abajo
            //que estamos usando cuando se trata de una matriz de permisos
            if (preg_match("/__/i", $nombre_permiso))
            {
            	$check =  Permisos_orm::where("nombre", $nombre_permiso)->get()->toArray();
            	if(!Util::is_array_empty($check)){
            		continue;
            	}

                //Verficar si el nombre del route concuerda con el nombre de permiso
                if (preg_match("/^".$nombre_recurso."/i", $nombre_permiso))
                {
                	$fieldset = array(
                        'nombre' => $nombre_permiso,
                        'recurso_id' => $recurso_id,
                    );
                	Permisos_orm::create($fieldset);
                }else{
                	continue;
                }
            }
            else
            {
                $fieldset = array(
                	'nombre' => $nombre_permiso,
                	'recurso_id' => $recurso_id,
                );
                Permisos_orm::create($fieldset);
            }
        }
    }

    public static function actualizar_recurso_permisos($recurso_id, $permisos, $nombre_modulo=NULL)
    {
    	if(empty($permisos)){
    		return false;
    	}

    	$permisos_no_registrados = array();
    	$permisos_registrados = array();

    	$result =  self::$ci->db->select("nombre")
		    	->from('permisos')
		    	->where("recurso_id", $recurso_id)
		    	->get()
		    	->result_array();

    	//Armar arreglo de permisos registrados
    	if(!empty($result)){
    		$i=0;
    		foreach($result AS $permiso){
    			$permisos_registrados[$i] = $permiso["nombre"];
    			$i++;
    		}
    	}

    	$permisos_no_registrados = array_diff(array_keys($permisos), $permisos_registrados);

    	//Verificar si hay permisos no registrados
    	if(empty($permisos_no_registrados)){
    		return true;
    	}

    	$checkResource = self::seleccionar_recurso($recurso_id);
    	$nombre_recurso  = !empty($checkResource[0]['nombre_recurso']) ? $checkResource[0]['nombre_recurso'] : "";
    	$nombre_recurso  = str_replace("/(:num)", "", $nombre_recurso);
    	$nombre_recurso  = str_replace("/(:any)", "", $nombre_recurso);
    	$nombre_recurso  = str_replace("/(:an", "", $nombre_recurso);
    	$nombre_recurso  = str_replace(strtolower($nombre_modulo)."/", "", $nombre_recurso);

    	foreach ($permisos_no_registrados AS $nombre_permiso)
    	{
    		// ---------------------------------------
    		// Begin Transaction
    		// docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
    		// ---------------------------------------
    		// self::$ci->db->trans_start();

    		//verificar si nombre es vacio
    		if(trim($nombre_permiso) == ""){
    			continue;
    		}

    		//Verificar si el permiso tiene el caracter rayita abajo
    		//que estamos usando cuando se trata de una matriz de permisos
    		if (preg_match("/__/i", $nombre_permiso))
    		{

    			//verificar si nombre recurso es vacio
    			if(trim($nombre_recurso) == "" || trim($nombre_permiso) == ""){
    				continue;
    			}

    			$PP[] = array($nombre_permiso, $nombre_recurso);

    			//Verficar si el nombre del route concuerda con el nombre de permiso
    			if (preg_match("/^(".$nombre_recurso.")/iS", $nombre_permiso))
                {
    				$fieldset = array(
    					'nombre' => $nombre_permiso,
    					'recurso_id' => $recurso_id
    				);
    				self::$ci->db->insert('permisos', $fieldset);
    			}
    		}
    		else
    		{
    			$fieldset = array(
    				'nombre' => $nombre_permiso,
    				'recurso_id' => $recurso_id
    			);
    			 self::$ci->db->insert('permisos', $fieldset);
    		}

    		// ---------------------------------------
    		// End Transaction
    		// ---------------------------------------
    		//self::$ci->db->trans_complete();
    	}
    }

	/**
     * Esta funcion lee los archivos config.php y route.php de cada modulo,
     * y los carga en el objeto $modulos.
     *
     * @return array
     */
    public static function cargar_modulos()
	{
		if(empty(self::$uuid_empresa)){
			return false;
		}

		$products = self::$cache->get("product_page");

		$modulos = array();
        if($handle = opendir(self::$ci->config->item('modules_locations'))){
            while(($section = readdir($handle)) !== false){
                if($section === '.' || $section === '..') {
                    continue;
                }
                if(!empty($section))
                {
                    $checkRouteFile = Modules::find('routes.php', $section, 'config/');

                    if(!empty($checkRouteFile))
                    {
                        $routePath = self::$ci->config->item('modules_locations') . $section .'/config/routes.php';
                        $configPath = self::$ci->config->item('modules_locations') . $section .'/config/config.php';

                        if(file_exists($routePath) && file_exists($configPath))
                        {
                            include($routePath);
                            include($configPath);

                            //Verificar si el array $route existe y no es vacio
                            if(isset($route) && !empty($route))
                            {
                                //Verificar si el array $config existe y no es vacio
                                if(isset($config) && !empty($config)){
                                    $modulos[$section]["modulo"] = $config['modulo_config'];
                                    $modulos[$section]["modulo"]["controlador"] = $section;
                                }

                                $modulos[$section]["routes"] = $route;

                                unset($route);
                                unset($config);
                            }
                        }
                    }
                }
            }
            closedir($handle);
       }

        //Guardar en Cache
        self::$cache->set("lista-modulos-". self::$uuid_empresa, $modulos, 3600);
    }

}

/* End of file Module_manager.php */

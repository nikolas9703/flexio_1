<?php
class Modulos_model extends CI_Model
{
	/*
	 * Arreglo de los modulos que estan Activos
	 */
	private $modulos_activos = array();

	/*
	 * Ubicacion de los Modulos
	 */
	private $ruta_modulos;

	/*
	 * Ruta del Modulo Actual
	 */
	private $ruta_modulo_actual;

	/*
	 * Nombre del Modulo Actual
	 */
	private $nombre_modulo_actual;


	public function __construct() {
		parent::__construct ();

		// load the file helper
		$this->load->helper('file');

		$this->ruta_modulos = $this->config->item('modules_locations');
	}

	/**
	 * Conteo de los roles existentes
	 *
	 * @return [array] [description]
	 */
	function contar_modulos($clause)
	{
		$result = $this->db->select("id")
				->distinct()
				->from ('modulos')
				->where($clause)
				->get()
				->result_array();
		return $result;
	}

	/**
	 * Listar los estados de los modulos.
	 *
	 * @return array
	 */
	function listar_estados_modulo() {
	    foreach ($search_in as $element) {
	        if ( ($element === $search_for) || (is_array($element) && $this->multi_array_search($search_for, $element)) ){
	            return true;
	        }
	    }
	    return false;
	}

	/**
	 * Query para listar los modulos
	 *
	 * @param integer $sidx [description]
	 * @param integer $sord [description]
	 * @param integer $limit [description]
	 * @param integer $start [description]
	 * @return [array] [description]
	 */
	function listar_modulos($clause, $sidx = 1, $sord = 1, $limit = 0, $start = 0)
	{
		$fields = array (
			"id",
			"nombre",
			"descripcion",
			"estado",
			"version",
			"tipo"
		);
		$result = $this->db->select($fields)
				->distinct()
				->from ('modulos')
				->where($clause)
				->order_by($sidx, $sord)
				->limit($limit, $start)
				->get()
				->result_array();
 		return $result;
	}

	/**
	 * Obtener la ruta de ubicacion de los archivos
	 * de configuracion e instalacion del modulo.
	 *
	 * @param int $id_modulo
	 * @return string
	 */
	private function obtener_modulo_archivo($id_modulo, $file)
	{
		$result = $this->db->select("controlador")
				->distinct()
				->from('modulos')
				->where("id", $id_modulo)
				->get()
				->result_array();
		$this->nombre_modulo_actual = !empty($result) ? $result[0]["controlador"] : "";

		//Armar la ruta del modulo que estamos instalando
		return $this->ruta_modulos . $this->nombre_modulo_actual . "/config/". $file;
	}

	function instalar_rutas()
	{
		$id_modulo = $this->input->post('id_modulo', true);

		$config_file = $this->obtener_modulo_archivo($id_modulo, "config.php");
		$routes_file = $this->obtener_modulo_archivo($id_modulo, "routes.php");

		if(file_exists($config_file) && file_exists($routes_file)){

			include($config_file);
			include($routes_file);

			$permisos = $config["modulo_config"]["permisos"];

			//Si el arreglo de rutas existe
			if(!empty($route))
			{
				foreach ($route AS $key => $value)
				{
					//No registrar url's ajax
					if (!preg_match("/ajax/i", $key) && !preg_match("/oculto/i", $key) && !preg_match("/filtar/i", $key) && !preg_match("/subpanel/i", $key))
					{
						$existe_recurso = $this->verificar_recurso($key);

						if(empty($existe_recurso))
						{
							//Si no existe este route, insertarlo como un recurso nuevo
							$id_recurso = $this->guardar_recurso($id_modulo, $key);

							//Guardar los permisos para este recurso
							$this->guardar_recurso_permisos($id_recurso, $permisos);

							//Activar modulo del core
							$this->activar_modulo($id_modulo);
						}
						else
						{
							//Guardar los permisos para este recurso
							$this->actualizar_recurso_permisos($existe_recurso[0]["id"], $permisos);

							//Activar modulo del core
							$this->activar_modulo($id_modulo);
						}
					}
				}

				//Pausa de 5 segundos
				sleep(5);

				//Retornar respuesta
				return array(
					"respuesta" => true,
					"mensaje" => ""
				);
			}
		}
		else
		{
			log_message("error", "MODULO: Modulos --> No se pudo leer los archivos config.php o routes.php.");

			return array(
				"respuesta" => false,
				"mensaje" => "Hubo un error al tratar de registrar las rutas del nodulo."
			);
		}
	}

	/**
	 * Consultar la tabla de recursos si existe el nombre de route solicitado
	 *
	 * @param  string $route
	 * @return array  resource_id
	 */
	private function verificar_recurso($route)
	{
		$fields = array(
			"id",
			"nombre_recurso"
		);
		$clause = array(
			"nombre_recurso" => $route
		);
		$result = $this->db->select("id")
			->from('recursos')
			->where($clause)
			->get()
			->result_array();
		return $result;
	}

	/**
	 * Guardar el route de un modulo en la tabla de recurso.
	 *
	 * @param  int $module_id
	 * @param  string $route
	 * @return int  resource_id
	 */
	private function guardar_recurso($module_id, $route)
	{
		if(empty($route)){
			return false;
		}

		$fieldset = array(
			'nombre_recurso'    => $route,
			'id_modulo'         => $module_id
		);
		$this->db->insert('recursos', $fieldset);


		return $this->db->insert_id();
	}

	/**
	 * Guardar los permisos asignados a un recurso.
	 *
	 * @param  int $resource_id
	 * @param  array $permissions
	 * @param  array $module_name
	 * @return none
	 */
	private function guardar_recurso_permisos($id_recurso, $permisos)
	{
		if(empty($permisos)){
			return false;
		}

		$checkResource = $this->seleccionar_recurso($id_recurso);
		$nombre_recurso  = !empty($checkResource[0]['nombre_recurso']) ? $checkResource[0]['nombre_recurso'] : "";
		$nombre_recurso  = str_replace("/(:num)", "", $nombre_recurso);
		$nombre_recurso  = str_replace("/(:any)", "", $nombre_recurso);
		$nombre_recurso  = str_replace("/(:an", "", $nombre_recurso);
		$nombre_recurso  = str_replace(strtolower($this->nombre_modulo_actual)."/", "", $nombre_recurso);

		echo $nombre_recurso." <br>";

		foreach ($permisos AS $nombre_permiso => $alias)
		{
			// ---------------------------------------
			// Begin Transaction
			// docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
			// ---------------------------------------
			$this->db->trans_start();

			//Verificar si el permiso tiene el caracter rayita abajo
			//que estamos usando cuando se trata de una matriz de permisos
			if (preg_match("/__/i", $nombre_permiso))
			{
				//verificar si nombre recurso es vacio
				if(trim($nombre_recurso) == "" || trim($nombre_permiso) == ""){
					continue;
				}

				//Verficar si el nombre del route concuerda con el nombre de permiso
				if (preg_match("/".$nombre_recurso."/is", $nombre_permiso))
				{
					$fieldset = array(
						'nombre_permiso' => $nombre_permiso,
						'id_recurso' => $id_recurso
					);
					$this->db->insert('permisos', $fieldset);
				}
			}
			else
			{
				$fieldset = array(
					'nombre_permiso' => $nombre_permiso,
					'id_recurso' => $id_recurso
				);
				$this->db->insert('permisos', $fieldset);
			}

			// ---------------------------------------
			// End Transaction
			// ---------------------------------------
			$this->db->trans_complete();
		}
	}

	private static function actualizar_recurso_permisos($id_recurso, $permisos)
	{
		if(empty($permisos)){
			return false;
		}

		$permisos_no_registrados = array();
		$permisos_registrados = array();

		$result = $this->db->select("nombre_permiso")
				->from('permisos')
				->where("id_recurso", $id_recurso)
				->get()
				->result_array();

		//Armar arreglo de permisos registrados
		if(!empty($result)){
			$i=0;
			foreach($result AS $permiso){
				$permisos_registrados[$i] = $permiso["nombre_permiso"];
				$i++;
			}
		}

		$permisos_no_registrados = array_diff(array_keys($permisos), $permisos_registrados);

		//Verificar si hay permisos no registrados
		if(empty($permisos_no_registrados)){
			return true;
		}

		$checkResource = $this->seleccionar_recurso($id_recurso);
		$nombre_recurso  = !empty($checkResource[0]['nombre_recurso']) ? $checkResource[0]['nombre_recurso'] : "";
		$nombre_recurso  = str_replace("/(:num)", "", $nombre_recurso);
		$nombre_recurso  = str_replace("/(:any)", "", $nombre_recurso);
		$nombre_recurso  = str_replace("/(:an", "", $nombre_recurso);
		$nombre_recurso  = str_replace(strtolower($this->nombre_modulo_actual)."/", "", $nombre_recurso);

		//echo $nombre_recurso." <br>";

		foreach ($permisos_no_registrados AS $nombre_permiso)
		{
			// ---------------------------------------
			// Begin Transaction
			// docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
			// ---------------------------------------
			$this->db->trans_start();

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

				//Verficar si el nombre del route concuerda con el nombre de permiso
				if (preg_match("/".$nombre_recurso."/is", $nombre_permiso))
				{
					$fieldset = array(
						'nombre_permiso' => $nombre_permiso,
						'id_recurso' => $id_recurso
					);
					$this->db->insert('permisos', $fieldset);
				}
			}
			else
			{
				$fieldset = array(
					'nombre_permiso' => $nombre_permiso,
					'id_recurso' => $id_recurso
				);
				$this->db->insert('permisos', $fieldset);
			}

			// ---------------------------------------
			// End Transaction
			// ---------------------------------------
			$this->db->trans_complete();
		}

		return true;
	}

	/**
	 * Consultar la informacion de un route ya registrado.
	 *
	 * @param  string $resource_id
	 * @return array
	 */
	private function seleccionar_recurso($resource_id)
	{
		$fields = array(
			"id",
			"nombre_recurso",
		);
		$clause = array(
			"id" => $resource_id
		);
		$result = $this->db->select($fields)
			->from('recursos')
			->where($clause)
			->get()
			->result_array();
		return $result;
	}

	/**
	 * Consultar la informacion de un modulo en especifico
	 *
	 * @param  string $resource_id
	 * @return array
	 */
	public function seleccionar_informacion_modulo($clause = array())
	{
		$fields = array(
			"m.*"
		);

		$result = $this->db->select($fields)
			->from('modulos as m')
			->where($clause)
			->get()
			->result_array();
	 	return $result;
	}
	public function activar_modulo($id_modulo)
	{
		if(empty($id_modulo)){
			return false;
		}

		// ---------------------------------------
		// Begin Transaction
		// docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
		// ---------------------------------------
		$this->db->trans_start();

		$fieldset = array(
			'estado' => 1
		);
		$clause = array(
			"id" => $id_modulo,
			"tipo" => "addon"
		);
		$this->db->where($clause)->update('modulos', $fieldset);

		// ---------------------------------------
		// End Transaction
		// ---------------------------------------
		$this->db->trans_complete();
	}

	function instalar_db()
	{
		$id_modulo = $this->input->post('id_modulo', true);

		//Activar modulo

		/*$archivo_sql = $this->obtener_modulo_archivo($id_modulo, "install.sql");

		$sql = "";
		//Verificar si el archivo existe
		if(file_exists($archivo_sql)){

			//Leer archivo SQL
			$sql = read_file($archivo_sql);

			//Trim it
			$sql = trim($sql);

			//Pausa de 5 segundos
			sleep(3);

			//Crear Tabla del Modulo en la DB
			$result = $this->db->query($sql);

			//Respuesta
			return array(
				"respuesta" => $result,
				"mensaje" => $result == true ? "Se ha activado el modulo satisfactoriamente." :"Hubo un error al tratar de activar el modulo."
			);
		}
		else
		{
			//Si el archivo no existe continuar igualmente
			//Retornar true

			//Pausa de 2 segundos
			sleep(2);

			//Respuesta
			return array(
				"respuesta" => true,
				"mensaje" => "Se ha activado el modulo satisfactoriamente."
			);
		}*/
	}

	function desactivar_modulo()
	{
		$id_modulo = $this->input->post ('id_modulo', true);

		//Retorna false si el nombre es vacio
		if(empty($id_modulo)){
			return false;
		}

		//
		// Begin Transaction
		// docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
		//
		$this->db->trans_start();

		//Guardar datos generales de la Orden
		$fieldset = array (
			"estado" => 0
		);
		$clause = array(
			"id" => $id_modulo
		);
		$this->db->where($clause)->update('modulos', $fieldset);

		// ---------------------------------------
		// End Transaction
		$this->db->trans_complete();

		// Managing Errors
		if ($this->db->trans_status() === FALSE) {

			log_message("error", "MODULO: Modulos --> No se pudo desactivar el modulo en DB.");

			return array(
				"respuesta" => false,
				"mensaje" => "Hubo un error al tratar de desactivar el modulo seleccionado."
			);

		} else {
			return array(
				"respuesta" => true,
				"mensaje" => "Se ha desactivado el modulo satisfactoriamente."
			);
		}
	}

	/**
     * Consulta la lista de modulos activos
     * y arma un arreglo agrupado por el campo,
     * grupo.
     *
     * @return array
     */
	function comp_listar_modulos_activos()
	{
		$fields = array (
			"id",
			"nombre",
			"descripcion",
			"controlador",
			"grupo",
			"tipo"
		);
		$clause = array (
			"estado" => 1
		);
		$modulos = $this->db->select($fields)
				->distinct()
				->from ('modulos')
				->where($clause)->order_by('nombre', 'ASC')
				->get()
				->result_array();
 		if(!empty($modulos))
		{
			foreach($modulos AS $modulo)
			{
				$modulo_id = (!empty($modulo['id']) ? $modulo['id'] : "");
				$modulo_nombre = (!empty($modulo['nombre']) ? $modulo['nombre'] : "");
				$modulo_tipo = (!empty($modulo['tipo']) ? $modulo['tipo'] : "");
				$modulo_grupo = (!empty($modulo['grupo']) ? $modulo['grupo'] : "");
				$controller_name = (!empty($modulo['controlador']) ? $modulo['controlador'] : "");
				$descripcion_modulo = (!empty($modulo['descripcion']) ? $modulo['descripcion'] : "");

				//Crear array con informacion del modulo
				$this->modulos_activos[$modulo_grupo][$controller_name]['modulo_id'] = $modulo_id;
				$this->modulos_activos[$modulo_grupo][$controller_name]['modulo_nombre'] = $modulo_nombre;
				$this->modulos_activos[$modulo_grupo][$controller_name]['modulo_descripcion'] = $descripcion_modulo;

				//Buscar las rutas de este modulo
				$fields = array(
					"id",
					"nombre"
				);
				$clause = array(
					"modulo_id" => $modulo_id
				);
				$resources = $this->db->select($fields)
					->from('recursos')
					->where($clause)
					->get()
					->result_array();

				if(!empty($resources))
				{
					$indx = 0;
					foreach ($resources AS $resource)
					{
						$this->modulos_activos[$modulo_grupo][$controller_name]['resources'][$indx]['id'] = $resource['id'];
						$this->modulos_activos[$modulo_grupo][$controller_name]['resources'][$indx]['resource_name'] = trim($resource['nombre']);
						$indx++;
					}
				}

				//Buscar los permisos de este modulo
				$configPath = $this->config->item('modules_locations') . $controller_name .'/config/config.php';

				if(file_exists($configPath))
				{
					include($configPath);

					//Check if exist $config module information
					if(isset($config) && !empty($config) && isset($config['modulo_config']['permisos'])){
						$this->modulos_activos[$modulo_grupo][$controller_name]['permissions'] = $config['modulo_config']['permisos'];
					}
					unset($config);
				}

			}

			/*echo "<pre>";
			print_r($this->modulos_activos);
			echo "</pre>";
			die();*/

			return $this->modulos_activos;
		}
	}
}
?>

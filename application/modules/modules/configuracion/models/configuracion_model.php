<?php
class Configuracion_model extends CI_Model
{
	/**
	 * Ruta de la carpeta modules
	 *
	 *  @var $ruta_modulos
	 */
	private static $ruta_modulos;

	protected $modulo;

	protected $campo;

	public function __construct() {
		parent::__construct ();

		self::$ruta_modulos = $this->config->item('modules_locations');
	}

	/**
	 * Conteo de los roles existentes
	 *
	 * @return [array] [description]
	 */
	function seleccionar_catalogo($sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
	{
		$this->modulo = $this->input->post('modulo', true);
		$this->campo = $this->input->post('campo', true);
		$busqueda = $this->input->post('busqueda', true);

		list($modulo_tabla_campos, $modulo_tabla_catalogo) = self::modulo_info();

		$fields = array (
			"cat.id_cat",
			"cat.etiqueta",
		);
		$clause = array(
			"campo.nombre_campo" => $this->campo,
		);

		//Si existe busqueda
		if(!empty($busqueda)){
			$clause["cat.etiqueta LIKE '%$busqueda%'"] = NULL;
		}

		$this->db->select($fields)
			->distinct()
			->from($modulo_tabla_catalogo ." AS cat")
			->join("$modulo_tabla_campos AS campo", 'campo.id_campo = cat.id_campo', 'LEFT')
			->where($clause);

			if($sidx!=NULL && $sord!=NULL){
				$this->db->order_by($sidx, $sord);
			}

			if($limit!=NULL){
				$this->db->limit($limit, $start);
				return $this->db->get()->result_array();
			}else{
				return $this->db->count_all_results();
			}
	}

	private function modulo_info()
	{
		//Obtener informacion del prefijo del modulo
		$module_config_path = empty($this->modulo) ? "" : self::$ruta_modulos . $this->modulo ."/config/";
		include($module_config_path ."/config.php") ;

		//Armar el nombre de la tabla de catlogo del modulo
		$modulo_tabla_campos = $config['modulo_config']['prefijo'] == '' ? $modulo_info[0]["controlador"] .'_campos' : $config['modulo_config']['prefijo'].'_'. $this->modulo .'_campos';

		//Armar el nombre de la tabla de catlogo del modulo
		$modulo_tabla_catalogo = $config['modulo_config']['prefijo'] == '' ? $modulo_info[0]["controlador"] .'_cat' : $config['modulo_config']['prefijo'].'_'. $this->modulo .'_cat';

		//Consultar Informacion de Catalogo en DB
		$fields = array (
			"cat.id_cat",
			"cat.id_campo",
			"cat.etiqueta",
		);
		$catalogo = $this->db->select($fields)
				->distinct()
				->from($modulo_tabla_catalogo ." AS cat")
				->join("$modulo_tabla_campos AS campo", 'campo.id_campo = cat.id_campo', 'LEFT')
				->where("campo.nombre_campo", $this->campo)
				->get()->result_array();
		$id_campo = !empty($catalogo) ? $catalogo[0]["id_campo"] : "";

		return array(
			$modulo_tabla_campos,
			$modulo_tabla_catalogo,
			$id_campo
		);
	}

	function guardar_editar_catalogo()
	{
		$this->modulo = $this->input->post('modulo', true);
		$this->campo = $this->input->post('campo', true);
		$etiqueta = $this->input->post('valor', true);
		$id_cat = $this->input->post('id_cat', true);

		if(empty($etiqueta) || empty($this->modulo)){
			return false;
		}

		//El nombre del valor va en minuscula
		//los espacios reemplazados por raya abajo.
		$valor = str_replace(" ", "_", strtolower($etiqueta));

		list($modulo_tabla_campos, $modulo_tabla_catalogo, $id_campo) = self::modulo_info();

		//
		// Begin Transaction
		// docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
		//
		$this->db->trans_start();

		$fieldset = array(
			"valor" => $valor,
			"etiqueta" => $etiqueta,
		);
		$clause = array(
			"id_cat" => $id_cat
		);

		//Si existe la variable $id_cat
		//Se trata de una Actualizacion
		if(!empty($id_cat)){

			//Actualizar Cliente Potencial
			$this->db->where($clause)->update($modulo_tabla_catalogo, $fieldset);
		}else{

			$fieldset["id_campo"] = $id_campo;

			//Guardar Catalogo
			$this->db->insert($modulo_tabla_catalogo, $fieldset);
		}

		//---------------------------------------
		//End Transaction
		$this->db->trans_complete();

		// Managing Errors
		if ($this->db->trans_status() === FALSE) {

			log_message("error", "MODULO: Configuracion --> No se pudo guadar/editar el valor de catalogo en DB.");

			return array(
				"respuesta" => false,
				"mensaje" => "Hubo un error al tratar de guardar el valor en el catalogo"
			);

		} else {

			return array(
				"respuesta" => true,
				"mensaje" => "Se ha guardado el valor en el catalogo satisfactoriamente."
			);
		}
	}

	/**
	 * Esta funcion elimina un valor del
	 * catalogo del modulo seleccionado.
	 *
	 * @return boolean|multitype:boolean string
	 */
	function eliminar_catalogo()
	{
		$this->modulo = $this->input->post('modulo', true);
		$this->campo = $this->input->post('campo', true);
		$id_cat = $this->input->post('id_cat', true);

		if(empty($id_cat) || empty($this->modulo)){
			return false;
		}

		//
		// Begin Transaction
		// docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
		//
		$this->db->trans_start();

		list($modulo_tabla_campos, $modulo_tabla_catalogo, $id_campo) = self::modulo_info();

		//Borrar la comision seleccionada
		$clause = array(
			"id_cat" => $id_cat,
			"id_campo" => $id_campo
		);
		$this->db->where($clause)->delete($modulo_tabla_catalogo);

		// ---------------------------------------
		// End Transaction
		$this->db->trans_complete();

		// Managing Errors
		if ($this->db->trans_status() === FALSE) {

			log_message("error", "MODULO: Configuracion --> No se pudo eliminar el valor del catalogo.");

			return array(
				"respuesta" => false,
				"mensaje" => "Hubo un error al tratar de eliminar el valor seleccionado"
			);

		} else {

			return array(
				"respuesta" => true,
				"mensaje" => "Se ha eliminado el valor del catalogo satisfactoriamente."
			);
		}
	}
	function guardar_jobs(){
		if(isset($_POST)){
			$tipo = $this->input->post('tipo');
			$notificacion = $_POST['reporte'];
			foreach ($notificacion as $value) {

				$dataInsert= array();

				$dataInsert['id_job'] = $value['id_reporte'];
				$dataInsert['id_rol'] = $value['id_rol'];
				if(isset($value['activo']))$dataInsert['estado'] = $value['activo'];
				$dataInsert['tiempo_ejecucion'] = date('Y-m-d H:i:s',strtotime($value['fecha_ejecucion']));



				if(!array_key_exists('id_usuario', $value)) {
					$roles = $this->roles_model->seleccionar_usuarios_por_rol($value['id_rol']);
					$uuid_usuarios =array();
					foreach($roles as $usuario ){
						array_push($uuid_usuarios, $usuario['uuid_usuario']);
					}
					$dataInsert['uuid_usuarios']= json_encode(array('mostrar' => false, 'uuid_usuarios' => $uuid_usuarios));
				}else{
					$dataInsert['uuid_usuarios']= json_encode(array('mostrar' => true, 'uuid_usuarios' => $value['id_usuario']));
				}

				$this->db->set('tipo',$tipo);
				if(!isset($value['id'])){
				  $this->db->insert('configuracion_notificaciones_reportes', $dataInsert);
				}else{
					  $this->db->where('id',$value['id']);
					  $this->db->update('configuracion_notificaciones_reportes', $dataInsert);
				}
			}
			$response = array();
			if(!$this->db->_error_message()){
				$mensaje = $tipo == 'oportunidad'? "nuevas notificaciones":"nuevos reportes";
				$response['estado'] = 200;
				$response['mensaje'] = "se crear&oacute;n ".$mensaje;
			}else{
				$response['estado'] = 500;
				$response['errors'] = $this->db->_error_message();
			}
			return $response;
		}
	}
	function eliminar_job($id){
		$this->db->delete('configuracion_notificaciones_reportes', array('id' => $id));
		$results = array();
		if($this->db->affected_rows()){
				$results['estado'] = 200;
		}elseif ($this->db->_error_message()) {
			$results['estado'] = 500;
		}else{
			$results['estado'] = 500;
		}
		return $results;
	}

	function get_configuraciones_reporte($tipo=array()){
			$responses = array();
			if(!empty($tipo)){
				$tipo_reporte = $tipo['tipo'] == 'oportunidad'? "notificacion":'reporte';
			 $resultados = Jobs::mostrar_jobs($tipo);
			 		foreach ($resultados as $resultado) {
			 		$responses = array('id'=>$resultado['id'],
					'id_job'=> array('id' =>$resultado['id_job'],'jobs' => $this->db->query('select id, descripcion from jobs where tipo = "'.$tipo_reporte.'"') ),
				  'estado' => $resultado['estado'],
				  'tiempo_ejecucion'=> $resultado['tiempo_ejecucion'],
				  'id_rol' =>array('id'=>$resultado['id_rol'],'roles' =>Jobs::getRoles(),'usuarios' => $this->roles_model->seleccionar_usuarios_por_rol($resultado['id_rol']) ),
				  'uuid_usuarios' => json_decode($resultado['uuid_usuarios']));
			 		}
			}
			return json_encode($responses);
	}
}

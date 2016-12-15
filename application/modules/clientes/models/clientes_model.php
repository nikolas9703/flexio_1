<?php
class Clientes_model extends CI_Model 
{
	private $modulo;
	private $ruta_modulos;
	private $modulo_controlador;
	private static $modulo_campos;
	private static $campos_completados;
	
	public function __construct() {
		parent::__construct ();

		$this->modulo = $this->router->fetch_module();
		$this->ruta_modulos = $this->config->item('modules_locations');
		$this->modulo_controlador = $this->router->fetch_class();
	}
	
	/**
	 * Conteo de los roles existentes
	 *
	 * @return [array] [description]
	 */
	function contar_clientes($clause)
	{
		$fields = array (
			"cl.id_cliente"
		);
		$result = $this->db->select($fields)
			->distinct()
			->from('cl_clientes AS cl')
			->join('cl_clientes_cat AS ccat', 'ccat.id_cat = cl.id_tipo_cliente', 'LEFT')
			->join('usuarios AS usr', 'usr.uuid_usuario = cl.id_asignado', 'LEFT')
			->join('cl_clientes_sociedades AS csoc', 'csoc.uuid_cliente = cl.uuid_cliente', 'LEFT')
			->join('cl_cliente_sociedades_contactos AS csocon', 'csocon.uuid_cliente = cl.uuid_cliente', 'LEFT')
			->join('con_contactos AS con', 'con.uuid_contacto = csocon.uuid_contacto', 'LEFT')
			->join('cl_cliente_correos AS cco', 'cco.uuid_cliente = cl.uuid_cliente', 'LEFT')
			->join('cl_cliente_telefonos AS ctel', 'ctel.uuid_cliente = cl.uuid_cliente', 'LEFT')
			->where($clause)
			->get()
			->result_array();
		return $result;
	}
	/**
	 * [list_roles description]
	 *
	 * @param integer $sidx [description]
	 * @param integer $sord [description]
	 * @param integer $limit [description]
	 * @param integer $start [description]
	 * @return [array] [description]
	 */
	function listar_clientes($clause, $sidx = 1, $sord = 1, $limit = 0, $start = 0)
	{
		$i= 0;
		$result = array();
		$fields = array (
			"cl.id_cliente",
			"HEX(cl.uuid_cliente) AS uuid_cliente",
            "cl.nombre",
            "cl.razon_social",
			"cl.apellido",
			"cl.cedula",
            "cl.ruc",
			"cl.imagen_archivo",
			"cl.id_tipo_cliente",
			"CONCAT_WS(' ', IF(usr.nombre != '', usr.nombre, ''), IF(usr.apellido != '', usr.apellido, '')) AS usuario_asignado",
		);
		$query = $this->db->select($fields)
				->distinct()
				->from('cl_clientes AS cl')
				->join('cl_clientes_cat AS ccat', 'ccat.id_cat = cl.id_tipo_cliente', 'LEFT')
				->join('usuarios AS usr', 'usr.uuid_usuario = cl.id_asignado', 'LEFT')
				->join('cl_clientes_sociedades AS csoc', 'csoc.uuid_cliente = cl.uuid_cliente', 'LEFT')
				->join('cl_cliente_sociedades_contactos AS csocon', 'csocon.uuid_cliente = cl.uuid_cliente', 'LEFT')
				->join('con_contactos AS con', 'con.uuid_contacto = csocon.uuid_contacto', 'LEFT')
				->join('cl_cliente_correos AS cco', 'cco.uuid_cliente = cl.uuid_cliente', 'LEFT')
				->join('cl_cliente_telefonos AS ctel', 'ctel.uuid_cliente = cl.uuid_cliente', 'LEFT')
				->where($clause)
				->order_by($sidx, $sord)
				->limit($limit, $start)
				->get()
				->result_array();
		if(!empty($query)){
			foreach($query as $row){
				$uuid = $row['uuid_cliente'];
				list($fecha, $hace) = $this->actividades_model->seleccionar_ultimo_contacto(
						array (
								"act.uuid_cliente = UNHEX('$uuid')" => NULL,
								"act.completada = 1" => NULL
						)
				);
				$result[$i]['ultimo_contacto'] = $hace;
				$result[$i]['id_cliente'] 	= $row['id_cliente'];
				$result[$i]['uuid_cliente'] = $row['uuid_cliente'];
                $result[$i]['nombre'] 		= $row['nombre'];
                $result[$i]['nombre_comercial'] 		= $row['nombre'];
                $result[$i]['razon_social'] 		= $row['razon_social'];
                $result[$i]['apellido'] 	= $row['apellido'];
                $result[$i]['ruc'] 	= $row['ruc'];
				$result[$i]['cedula'] 		= $row['cedula'];
				$result[$i]['imagen_archivo']  = $row['imagen_archivo'];
				$result[$i]['id_tipo_cliente'] = $row['id_tipo_cliente'];
				$result[$i]['usuario_asignado']= $row['usuario_asignado'];
				++$i;
			}
	
		}
	
	
		return $result;
	}
	
	/**
	 * Guardar Formulario de Cliente Juridico
	 *
	 * @return boolean
	 */
	function guardar_cliente()
	{
		if(Util::is_array_empty($_POST)){
			return false;
		}
		
		//Init Fieldset variable
		$fieldset = array();
		
		//Remover el boton de submit que por default
		//viene con el valor "Guardar"
		unset($_POST["campo"]["guardarContacto"]);
		unset($_POST["campo"]["guardar"]);
		
		//Recorrer arreglo e insertar los valores que no estan vacios
		//en el fieldset
		foreach ($_POST["campo"] AS $fieldname => $fieldvalue) {
			if(empty($fieldvalue)){
				continue;
			}
	
			//check if is an array
			if(is_array($fieldvalue)){
				foreach ($fieldvalue AS $name => $value) {
					if($value != ""){
						if(preg_match("/id_asignado/i", $name)){
							$fieldset["$name = UNHEX('$value')"] = NULL;
							
						}else{
							$fieldset[$name] = $this->security->xss_clean($value);
						}
					}
				}
			}else{
				
				if(preg_match("/id_asignado/i", $fieldname)){
						
					//$fieldset["$fieldname = UNHEX('$fieldvalue')"] = NULL;
					$this->db->set($fieldname, "UNHEX('$fieldvalue')", FALSE);
				}else{
					$fieldset[$fieldname] = $fieldvalue;
				}
				
			}
		}
	
		//Si el $fieldset es vacio
		if(Util::is_array_empty($fieldset)){
			return false;
		}
		
		//
		// Begin Transaction
		// docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
		//
		$this->db->trans_start();
	
		//Campos adicionales
		$this->db->set('uuid_cliente', 'ORDER_UUID(uuid())', FALSE);
		$fieldset["creado_por"] = $this->session->userdata('id_usuario');
		$fieldset["fecha_creacion"] = date('Y-m-d H-i-s');
	
		//Guardar Cliente
		$this->db->insert('cl_clientes', $fieldset);
		$idCliente = $this->db->insert_id();
		
		/*
		 * Sacar el UUID del Cliente
		 */
		$fields = array(
			"uuid_cliente",
			"HEX(uuid_cliente) AS huuid_cliente"
		);
		$result = $this->db->select($fields)
			->distinct()
			->from('cl_clientes')
			->where ("id_cliente", $idCliente)
			->get()
			->result_array();
		
		//Si la informacion del cliente ya se ha guardado
		//y ya tenemos el ID del cliente,
		if(!empty($idCliente))
		{
			//--------------------
			// GUARDAR TELEFONOS
			//--------------------
			if(!empty($_POST["telefonos"])){
				foreach ($_POST["telefonos"]["no_telefono"] AS $telefono)
				{
					//Si es vacio continuar al siguiente telefono
					if(empty($telefono) && $telefono == ""){
						continue;
					}
					
					//Guardar datos
					$fieldset = array(
						'uuid_cliente' => $result[0]["uuid_cliente"],
						'no_telefono' => $this->security->xss_clean($telefono)
					);
					$this->db->insert('cl_cliente_telefonos', $fieldset);
				}
			}
			
			//--------------------
			// GUARDAR CORREOS
			//--------------------
			if(!empty($_POST["correos"])){
				foreach ($_POST["correos"]["correo"] AS $correo)
				{
					//Si es vacio continuar al siguiente telefono
					if(empty($correo) && $correo == ""){
						continue;
					}
					
					//Guardar datos
					$fieldset = array(
						'uuid_cliente' => $result[0]["uuid_cliente"],
						'correo' => $this->security->xss_clean($correo)
					);
					$this->db->insert('cl_cliente_correos', $fieldset);
				}
			}
			
			//--------------------
			// GUARDAR SOCIEDADES
			//--------------------
			if(!empty($_POST["sociedades"])){
				foreach ($_POST["sociedades"] AS $sociedad)
				{
					//Si es vacio continuar al siguiente telefono
					if(empty($sociedad)){
						continue;
					}
						
					//Guardar datos
					$fieldset = array(
						'uuid_cliente' => $result[0]["uuid_cliente"],
						'ruc' => $sociedad["ruc"],
						'razon_social' => $sociedad["razon_social"],
						'nombre_comercial' => $sociedad["nombre_comercial"],
						'fecha_creacion' => date('Y-m-d H-i-s')
					);
					$this->db->set('uuid_sociedad', 'ORDER_UUID(uuid())', FALSE);
					$this->db->insert('cl_clientes_sociedades', $fieldset);
				}
			}

			//Subir Archivos
			$files = $_FILES;
			if(!empty($files['campo']))
			{
				$filename = $files['campo']['name']['imagen_archivo'];
				$filename = str_replace(" ","_", $filename);
				$filename = str_replace("-","_", $filename);
				$file_name = "cl_". $filename;
				$config['upload_path']      = './public/uploads/clientes/';
				$config['file_name']        = $file_name;
				$config['allowed_types']    = '*';
				
				$extension = ".".end(explode('.', $files['campo']['name']['imagen_archivo']));
				
				$_FILES['campo']['name']     = $files['campo']['name']['imagen_archivo'];
				$_FILES['campo']['type']     = $files['campo']['type']['imagen_archivo'];
				$_FILES['campo']['tmp_name'] = $files['campo']['tmp_name']['imagen_archivo'];
				$_FILES['campo']['error']    = $files['campo']['error']['imagen_archivo'];
				$_FILES['campo']['size']     = $files['campo']['size']['imagen_archivo'];
				$_FILES['campo']['filename'] = $config['file_name']. $extension;
		
				$this->load->library('upload', $config);
				$this->upload->initialize($config);
			
				if($this->upload->do_upload("campo"))
				{
					$fileINFO = $this->upload->data();
		
					//Guardar datos de los archivos
					$fieldset = array(
						'imagen_archivo' => "clientes/".$fileINFO["file_name"]
					);
					$clause = array(
						"id_cliente" => $idCliente
					);
					$this->db->where($clause)->update('cl_clientes', $fieldset);
				}
			}
		}
		
		//---------------------------------------
		//End Transaction
		$this->db->trans_complete();
	
		// Managing Errors
		if ($this->db->trans_status() === FALSE) {
				
			log_message("error", "MODULO: Clientes --> No se pudo guadar los datos del cliente en DB.");
			return false;
				
		} else {
				
			//guardar el id en variable de session
			$this->session->set_userdata('idCliente', $result[0]["huuid_cliente"]);
			
			return true;
		}
	}
	
	/**
	 * Actualizar Datos de un Cliente Juridico
	 * 
	 * @param string $id_cliente
	 * @return boolean
	 */
	function actualizar_cliente($id_cliente=NULL)
	{
            
            
		if(Util::is_array_empty($_POST) || $id_cliente==NULL){
			return false;
		}
                
                
                
                
		//Init Fieldset variable
		$fieldset = array();

		//Remover el boton de submit que por default
		//viene con el valor "Guardar"
		unset($_POST["campo"]["guardarContacto"]);
		unset($_POST["campo"]["guardar"]);
		
		//Verificar si existe el POST de campo
		
		if(!empty($_POST["campo"])){
			
			//Recorrer arreglo e insertar los valores que no estan vacios
			//en el fieldset
			foreach ($_POST["campo"] AS $fieldname => $fieldvalue) {
				if(empty($fieldvalue)){
					continue;
				}
			
				//check if is an array
				if(is_array($fieldvalue)){
					foreach ($fieldvalue AS $name => $value) {
						if($value != ""){
							if(preg_match("/id_asignado/i", $name)){
								$fieldset["$name = UNHEX('$value')"] = NULL;
									
							}else{
								$fieldset[$name] = $this->security->xss_clean($value);
							}
						}
					}
				}else{
			
					if(preg_match("/id_asignado/i", $fieldname)){
						$this->db->set($fieldname, "UNHEX('$fieldvalue')", FALSE);
					}else{
						$fieldset[$fieldname] = $fieldvalue;
					}
			
				}
			}
		}
                
		
		if( isset($_POST["agentes"]['guardarAgente'])){  //PestaÃ±a de Agentes.
			unset($_POST["agentes"]["guardarAgente"]);
			if(!empty($_POST["agentes"])){
		
				foreach ($_POST["agentes"]  AS $agente_valor)
				{
					 
					if(empty($agente_valor) && $agente_valor == ""){
						continue;
					}
					$id_agente = !empty($agente_valor['id_agente']) ? $agente_valor['id_agente']: "";
					//Verificar si la poliza ya existe
					$clause = array(
							'id_agente' => $this->security->xss_clean( $id_agente  ),
					);
					$nombre_agente = $agente_valor['nombre'];
					$checkPoliza = $this->db->select()
					->distinct()
					->from('cl_cliente_agentes')
					->where($clause)
					->get()
					->result_array();
					//Guardar datos
					$fieldsetAgente = array(
 							'id_tipo_comision' => $this->security->xss_clean($agente_valor['id_tipo_comision']),
							'porcentage_comision' => $this->security->xss_clean($agente_valor['porcentage_comision'])
					);
					$this->db->set("nombre", "UNHEX('$nombre_agente')", FALSE);
					$this->db->set("uuid_cliente", "UNHEX('$id_cliente')", FALSE);
					if(empty($checkPoliza))
					{
		
						$this->db->insert('cl_cliente_agentes', $fieldsetAgente);
					}
					else{
						//Actualizar Poliza
						$this->db->where($clause)->update('cl_cliente_agentes', $fieldsetAgente);
					}
		
				}
		
			}
			 
		
		}
		
		
                               
                if( isset($_POST["poliza"]['guardarPolizas'])){  //PestaÃ±a de Polizas.
                    
                    
			if(!empty($_POST["poliza"])){
                            
                                $clause = array(
                                    "uuid_cliente = UNHEX('$id_cliente')" => NULL
                                );

                                $this->db
                                ->where($clause)
                                ->delete('cl_cliente_polizas');
	
				foreach ($_POST["poliza"]  AS $poliza_valor)
				{
					if(empty($poliza_valor) && $poliza_valor == ""){
						continue;
					}
                                        
                                        if(empty($poliza_valor["id_aseguradora"]) || empty($poliza_valor["id_ramo"])){
                                            continue;
					}
	
					//Guardar datos
					$fieldset = array(
							'id_aseguradora' => $this->security->xss_clean($poliza_valor['id_aseguradora']),
							'id_ramo' => $this->security->xss_clean($poliza_valor['id_ramo'])
					);
                                        
                                        $this->db->set("uuid_cliente", "UNHEX('$id_cliente')", FALSE);
                                        $this->db->insert('cl_cliente_polizas', $fieldset);
					
	
				}
	
			}
                        return true;
				
		} //fin pestaña poliza
                
                //Si el $fieldset es vacio
		if(Util::is_array_empty($fieldset)){
			return false;
		}
                
                //
		// Begin Transaction
		// docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
		//
		$this->db->trans_start();
                
                
                
                
		
		$clause = array(
			"uuid_cliente = UNHEX('$id_cliente')" => NULL
		);
		
		//Actualizar Cliente Potencial
		$this->db->where($clause)->update('cl_clientes', $fieldset);

		//--------------------
		// ACTUALIZAR TELEFONOS
		//--------------------
		if(!empty($_POST["telefonos"])){
			foreach ($_POST["telefonos"]["no_telefono"] AS $telefono)
			{
				//Si es vacio continuar al siguiente telefono
				if(empty($telefono) && $telefono == ""){
					continue;
				}
				
				//Verificar si el telefono ya existe
				$clause = array(
					"uuid_cliente = UNHEX('$id_cliente')" => NULL,
					'no_telefono' => $this->security->xss_clean($telefono)
				);
				$checkTelefono = $this->db->select()
					->distinct()
					->from('cl_cliente_telefonos')
					->where($clause)
					->get()
					->result_array();
				
				if(empty($checkTelefono))
				{	
					//Si no existe Guardarlo
					$fieldset = array(
						'no_telefono' => $this->security->xss_clean($telefono)
					);
					$this->db->set("uuid_cliente", "UNHEX('$id_cliente')", FALSE);
					$this->db->insert('cl_cliente_telefonos', $fieldset);
				}
			}
		}
				
		//--------------------
		// GUARDAR CORREOS
		//--------------------
		if(!empty($_POST["correos"])){
			foreach ($_POST["correos"]["correo"] AS $correo)
			{
				//Si es vacio continuar al siguiente telefono
				if(empty($correo) && $correo == ""){
					continue;
				}
				
				//Verificar si el telefono ya existe
				$clause = array(
					"uuid_cliente = UNHEX('$id_cliente')" => NULL,
					'correo' => $this->security->xss_clean($correo)
				);
				$checkCorreo = $this->db->select()
					->distinct()
					->from('cl_cliente_correos')
					->where($clause)
					->get()
					->result_array();
				
				if(empty($checkCorreo))
				{
					//Guardar datos
					$fieldset = array(
						'correo' => $this->security->xss_clean($correo)
					);
					$this->db->set("uuid_cliente", "UNHEX('$id_cliente')", FALSE);
					$this->db->insert('cl_cliente_correos', $fieldset);
				}
			}
		}
				
		//--------------------
		// GUARDAR SOCIEDADES
		//--------------------
		if(!empty($_POST["sociedades"])){
			foreach ($_POST["sociedades"] AS $sociedad)
			{
				//Si es vacio continuar al siguiente telefono
				if(empty($sociedad)){
					continue;
				}
				
				$uuid_sociedad = !empty($sociedad["uuid_sociedad"]) ? $sociedad["uuid_sociedad"] : "";

				//Arreglo de campos a guardar
				$fieldset = array(
					'ruc' => $sociedad["ruc"],
					'razon_social' => $sociedad["razon_social"],
					'nombre_comercial' => $sociedad["nombre_comercial"]
				);
				
				//Verificar si la sociedad ya existe.
				$clause = array(
					"uuid_cliente = UNHEX('$id_cliente')" => NULL,
					"uuid_sociedad = UNHEX('$uuid_sociedad')" => NULL,
				);
				$checkSociedad = $this->db->select()
					->distinct()
					->from('cl_clientes_sociedades')
					->where($clause)
					->get()
					->result_array();
				
				if(empty($checkSociedad))
				{
					//Si el $fieldset es vacio
					if(Util::is_array_empty($fieldset)){
						continue;
					}
					
					//Guardar datos
					$fieldset['fecha_creacion'] = date('Y-m-d H-i-s');
					
					$this->db->set("uuid_cliente", "UNHEX('$id_cliente')", FALSE);
					$this->db->set('uuid_sociedad', 'ORDER_UUID(uuid())', FALSE);
					$this->db->insert('cl_clientes_sociedades', $fieldset);
				}
				else
				{
					//Actualizar Cliente Potencial
					$this->db->where($clause)->update('cl_clientes_sociedades', $fieldset);
				}
			}
		}

		//Subir Imagen de cliente
		$files = $_FILES;
		if(!empty($files['campo']) && empty($files['campo']["error"]["imagen_archivo"]))
		{
			$filename = $files['campo']['name']['imagen_archivo'];
			$filename = str_replace(" ","_", $filename);
			$filename = str_replace("-","_", $filename);
			$file_name = "cl_". $filename;
			
			$config['upload_path']      = './public/uploads/clientes/';
			$config['file_name']        = $file_name;
			$config['allowed_types']    = '*';
		
			$extension = ".".end(explode('.', $files['campo']['name']['imagen_archivo']));
		
			$_FILES['campo']['name']     = $files['campo']['name']['imagen_archivo'];
			$_FILES['campo']['type']     = $files['campo']['type']['imagen_archivo'];
			$_FILES['campo']['tmp_name'] = $files['campo']['tmp_name']['imagen_archivo'];
			$_FILES['campo']['error']    = $files['campo']['error']['imagen_archivo'];
			$_FILES['campo']['size']     = $files['campo']['size']['imagen_archivo'];
			$_FILES['campo']['filename'] = $config['file_name']. $extension;
		
			$this->load->library('upload', $config);
			$this->upload->initialize($config);
				
			if($this->upload->do_upload("campo"))
			{
				//Antes de guardar, verificar si el cliente
				//ya tiene una imagen para borrarla
				$clause = array(
						"uuid_cliente = UNHEX('$id_cliente')" => NULL,
				);
				$checkCliente = $this->db->select("imagen_archivo")
						->distinct()
						->from('cl_clientes')
						->where($clause)
						->get()
						->result_array();
					
				if(!empty($checkCliente)){
				
					$imagen_vieja = !empty($checkCliente[0]["imagen_archivo"]) ? realpath(str_replace("system/", "", BASEPATH). "public/uploads/". $checkCliente[0]["imagen_archivo"]) : "";
				
					if(file_exists($imagen_vieja)) {
							
						//Si existe la imagen BORRARLA
						unlink($imagen_vieja);
					}
				}

				$fileINFO = $this->upload->data();
				
				//Guardar datos de los archivos
				$fieldset = array(
					'imagen_archivo' => "clientes/".$fileINFO["file_name"]
				);
				$clause = array(
					"uuid_cliente = UNHEX('$id_cliente')" => NULL,
				);
				$this->db->where($clause)->update('cl_clientes', $fieldset);
			}
		}
		
		//---------------------------------------
		//End Transaction
		$this->db->trans_complete();
		
		// Managing Errors
		if ($this->db->trans_status() === FALSE) {
		
			log_message("error", "MODULO: Clientes --> No se pudo guadar los datos del cliente en DB.");
			return false;
		
		} else {
		
			//guardar el id en variable de session
			$this->session->set_userdata('updatedCliente', $id_cliente);
			
			//Limpiar cache de clientes
			CRM_Controller::$cache->delete("contarClientes");
			CRM_Controller::$cache->delete("listaClientes");
			CRM_Controller::$cache->delete("infoCliente". $id_cliente);
			CRM_Controller::$cache->delete("registrosClientes");
			
			return true;
		}
	}
	
	function guardar_propiedades_juridico($id_cliente=NULL)
	{
		if(Util::is_array_empty($_POST) || $id_cliente==NULL){
			return false;
		}
		
		//Init Fieldset variable
		$fieldset = array();
		
		//Remover el boton de submit que por default
		//viene con el valor "Guardar"
		unset($_POST["propiedades"]["guardarPropiedad"]);
		
		//
		// Begin Transaction
		// docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
		//
		$this->db->trans_start();

		//--------------------
		// GUARDAR PROPIEDADES
		// TAB: Propiedades
		//--------------------
		
		if(!empty($_POST["propiedades"])){
			
			foreach($_POST["propiedades"] AS $propiedad)
			{
				if($propiedad == "Guardar"){
					continue;
				}
				
				$id_propiedad = !empty($propiedad['id_propiedad']) ? $propiedad['id_propiedad'] : "";
				
				//Guardar datos
				$fieldset = array(
					'propiedad_nombre' => $this->security->xss_clean($propiedad['propiedad_nombre']),
					'provincia' => $this->security->xss_clean($propiedad['provincia']),
					'no_local' => $this->security->xss_clean($propiedad['no_local']),
					'no_finca' => $this->security->xss_clean($propiedad['no_finca']),
					'area' => $this->security->xss_clean($propiedad['area']),
					'id_tipo_propiedad' => $this->security->xss_clean($propiedad['id_tipo_propiedad'])
				);
				
				//Clausula
				$clause = array(
					"uuid_cliente = UNHEX('$id_cliente')" => NULL,
					'id_propiedad' => $id_propiedad,
				);
	
				//Verificar si la propiedad ya existe.
				$checkPropiedad = $this->db->select()
					->distinct()
					->from('cl_cliente_propiedades')
					->where($clause)
					->get()
					->result_array();
	
				if(empty($checkPropiedad))
				{
					$this->db->set("uuid_cliente", "UNHEX('$id_cliente')", FALSE);
					$this->db->insert('cl_cliente_propiedades', $fieldset);
				}
				else
				{
					//Actualizar Cliente Potencial
					$this->db->where($clause)->update('cl_cliente_propiedades', $fieldset);
				}
				
			}
		}
		
		//---------------------------------------
		//End Transaction
		$this->db->trans_complete();
		
		// Managing Errors
		if ($this->db->trans_status() === FALSE) {
		
			log_message("error", "MODULO: Clientes --> No se pudo guadar las propiedades del cliente juridico en DB.");
			return false;
		
		} else {
		
			//Limpiar cache de clientes
			CRM_Controller::$cache->delete("contarClientes");
			CRM_Controller::$cache->delete("listaClientes");
			CRM_Controller::$cache->delete("infoCliente". $id_cliente);
		
			return true;
		}
	}
	
	/**
	 * Seleccionar la informacion de un cliente especifico.
	 *
	 * @return array
	 */
	function seleccionar_informacion_de_cliente($id_cliente=NULL)
	{
		if(empty($id_cliente)){
			return false;
		}
	
		$result = array();
		$fields = array(
			"id_tipo_cliente",
			"id_cliente",
			"imagen_archivo",
			"nombre",
                    "razon_social",
			"web",
			"id_toma_contacto",
			"direccion",
			"id_estado",
			"id_motivo",
			"HEX(id_asignado) AS id_asignado",
			"comentarios",
		);
		$clause = array(
			"uuid_cliente = UNHEX('$id_cliente')" => NULL
		);
		$cliente = $this->db->select($fields)
			->distinct()
			->from('cl_clientes')
			->where ($clause)
			->get()
			->result_array();
		
		if(!empty($cliente))
		{
			$result["id_tipo_cliente"] = !empty($cliente[0]["id_tipo_cliente"]) ? $cliente[0]["id_tipo_cliente"] : "";
			$result["imagen_archivo"] = !empty($cliente[0]["imagen_archivo"]) ? $cliente[0]["imagen_archivo"] : "";
			$result["nombre"] = !empty($cliente[0]["nombre"]) ? $cliente[0]["nombre"] : "";
			$result["web"] = !empty($cliente[0]["web"]) ? $cliente[0]["web"] : "";
			$result["id_toma_contacto"] = !empty($cliente[0]["id_toma_contacto"]) ? $cliente[0]["id_toma_contacto"] : "";
			$result["direccion"] = !empty($cliente[0]["direccion"]) ? $cliente[0]["direccion"] : "";
			$result["id_estado"] = !empty($cliente[0]["id_estado"]) ? $cliente[0]["id_estado"] : "";
			$result["id_motivo"] = !empty($cliente[0]["id_motivo"]) ? $cliente[0]["id_motivo"] : "";
			$result["id_asignado"] = !empty($cliente[0]["id_asignado"]) ? $cliente[0]["id_asignado"] : "";
			$result["comentarios"] = !empty($cliente[0]["comentarios"]) ? $cliente[0]["comentarios"] : "";
                        $result["razon_social"] = !empty($cliente[0]["razon_social"]) ? $cliente[0]["razon_social"] : "";
			
			//-------------------------
			// Telefonos del Cliente
			//-------------------------
			$fields = array(
				"id_telefono",
				"no_telefono"
			);
			$clause = array(
				"uuid_cliente = UNHEX('$id_cliente')" => NULL
			);
			$telefonos = $this->db->select($fields)
				->distinct()
				->from('cl_cliente_telefonos')
				->where($clause)
				->get()
				->result_array();
			if(!empty($telefonos)){
				$result["no_telefono"] = $telefonos;
			}
			
			//-------------------------
			// Correos del Cliente
			//-------------------------
			$fields = array(
				"id_correo",
				"correo"
			);
			$correos = $this->db->select($fields)
				->distinct()
				->from('cl_cliente_correos')
				->where($clause)
				->get()
				->result_array();
			if(!empty($correos)){
				$result["correo"] = $correos;
			}
			
			//Agentes del Cliente
			$fields = array(
					"HEX(nombre) AS nombre",
					"id_tipo_comision",
					"porcentage_comision"
			);
			$agents = $this->db->select($fields)
			->distinct()
			->from('cl_cliente_agentes')
			->where($clause)
			->get()
			->result_array();
			if(!empty($agents)){
				$result["agentes"] = $agents;
			}
			//-------------------------
			// Sociedades del Cliente
			//-------------------------
			$fields = array(
				"id_sociedad",
				"HEX(uuid_sociedad) AS uuid_sociedad",
				"ruc",
				"razon_social",
				"nombre_comercial"
			);
			$sociedades = $this->db->select($fields)
				->distinct()
				->from('cl_clientes_sociedades')
				->where($clause)
				->get()
				->result_array();
			if(!empty($sociedades)){
				$result["sociedades"] = $sociedades;
			}
			
			
			//Agentes del Cliente
			$fields = array(
					"id_agente",
					"HEX(nombre) AS nombre",
					"id_tipo_comision",
					"porcentage_comision"
			);
			$agents = $this->db->select($fields)
			->distinct()
			->from('cl_cliente_agentes')
			->where($clause)
			->get()
			->result_array();
			if(!empty($agents)){
				$result["agentes"] = $agents;
			}
			
			//-------------------------
			// Propiedades del Cliente
			//-------------------------
			$fields = array(
				"*"
			);
			$propiedades = $this->db->select($fields)
				->distinct()
				->from('cl_cliente_propiedades')
				->where($clause)
				->get()
				->result_array();
			if(!empty($propiedades)){
				$result["propiedades"] = $propiedades;
			}
                        
                        //-------------------------
			// Polizas del Cliente
			//-------------------------
			$fields = array(
					"id_poliza",
					"id_aseguradora",
					"id_ramo"
			);
			$polizas = $this->db->select($fields)
			->distinct()
			->from('cl_cliente_polizas')
			->where($clause)
			->get()
			->result_array();
			if(!empty($polizas)){
				$result["poliza"] = $polizas;
			}
		}
		
		return $result;
	}
	
	/**
	 * Esta funcion elmina el telefono seleccionado 
	 * relacionado de cliente actual que estamos viendo.
	 * 
	 * @return boolean|multitype:boolean string
	 */
	function eliminar_telefono_cliente()
	{
		$id_cliente = $this->input->post('id_cliente', true);
		$no_telefono = $this->input->post('no_telefono', true);
		
		//Retorna false si el nombre es vacio
		if(empty($no_telefono)){
			return false;
		}
		
		//
		// Begin Transaction
		// docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
		//
		$this->db->trans_start();
		
		//Borrar el telefono seleccionado
		$clause = array(
			"HEX(uuid_cliente)" => $id_cliente,
			"no_telefono" => $no_telefono
		);
		$this->db->where($clause)
			->delete('cl_cliente_telefonos');
		
		// ---------------------------------------
		// End Transaction
		$this->db->trans_complete();
		
		// Managing Errors
		if ($this->db->trans_status() === FALSE) {
		
			log_message("error", "MODULO: Cliente --> No se pudo eliminar el telefono del cliente.");
		
			return array(
				"respuesta" => false,
				"mensaje" => "Hubo un error al tratar de eliminar el telefono seleccionado"
			);
		
		} else {
			
			//Limpiar cache de clientes
			CRM_Controller::$cache->delete("contarClientes");
			CRM_Controller::$cache->delete("listaClientes");
			CRM_Controller::$cache->delete("infoCliente". $id_cliente);
			CRM_Controller::$cache->delete("registrosClientes");
			
			return array(
				"respuesta" => true,
				"mensaje" => "Se ha eliminado el telefono seleccionado satisfactoriamente."
			);
		}
	}
	
	/**
	 * Esta funcion elimina el correo seleccionado
	 * relacionado de cliente actual que estamos viendo.
	 *
	 * @return boolean|multitype:boolean string
	 */
	function eliminar_correo_cliente()
	{
		$id_cliente = $this->input->post('id_cliente', true);
		$correo = $this->input->post('correo', true);
		
		//Retorna false si el nombre es vacio
		if(empty($correo)){
			return false;
		}
		
		//
		// Begin Transaction
		// docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
		//
		$this->db->trans_start();
		
		//Borrar el correo seleccionado
		$clause = array(
			"HEX(uuid_cliente)" => $id_cliente,
			"correo" => $correo
		);
		$this->db->where($clause)->delete('cl_cliente_correos');
		
		// ---------------------------------------
		// End Transaction
		$this->db->trans_complete();
		
		// Managing Errors
		if ($this->db->trans_status() === FALSE) {
		
			log_message("error", "MODULO: Cliente --> No se pudo eliminar el correo del cliente.");
		
			return array(
					"respuesta" => false,
					"mensaje" => "Hubo un error al tratar de eliminar el correo seleccionado"
			);
		
		} else {
			
			//Limpiar cache de clientes
			CRM_Controller::$cache->delete("contarClientes");
			CRM_Controller::$cache->delete("listaClientes");
			CRM_Controller::$cache->delete("infoCliente". $id_cliente);
			CRM_Controller::$cache->delete("registrosClientes");
			
			return array(
					"respuesta" => true,
					"mensaje" => "Se ha eliminado el correo seleccionado satisfactoriamente."
			);
		}
	}
	
	/**
	 * Esta funcion elmina una sociedad seleccionada 
	 * relacionada al cliente actual que estamos viendo.
	 *
	 * @return boolean|multitype:boolean string
	 */
	function eliminar_sociedad_cliente()
	{
		$id_cliente = $this->input->post('id_cliente', true);
		$id_sociedad = $this->input->post('id_sociedad', true);
		
		//Retorna false si el nombre es vacio
		if(empty($id_sociedad)){
			return false;
		}
		
		//
		// Begin Transaction
		// docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
		//
		$this->db->trans_start();
		
		//Borrar el telefono seleccionado
		$clause = array(
			"HEX(uuid_cliente)" => $id_cliente,
			"id_sociedad" => $id_sociedad
		);
		$this->db->where($clause)
			->delete('cl_clientes_sociedades');
		
		// ---------------------------------------
		// End Transaction
		$this->db->trans_complete();
		
		// Managing Errors
		if ($this->db->trans_status() === FALSE) {
		
			log_message("error", "MODULO: Cliente --> No se pudo eliminar la sociedad del cliente.");
		
			return array(
				"respuesta" => false,
				"mensaje" => "Hubo un error al tratar de eliminar la sociedad seleccionada"
			);
		
		} else {
			
			//Limpiar cache de clientes
			CRM_Controller::$cache->delete("contarClientes");
			CRM_Controller::$cache->delete("listaClientes");
			CRM_Controller::$cache->delete("infoCliente". $id_cliente);
			CRM_Controller::$cache->delete("registrosClientes");
			
			return array(
				"respuesta" => true,
				"mensaje" => "Se ha eliminado la sociedad seleccionada satisfactoriamente."
			);
		}
	}
	
 		
	function calcular_porcentaje_completado($id_cliente=NULL)
	{
		if($id_cliente==NULL){
			return false;
		}
		
		$module_config_path = empty($this->modulo) ? "" : $this->ruta_modulos . $this->modulo ."/config/";
		include($module_config_path ."/config.php") ;
		
		$tabla_campos = $config['modulo_config']['prefijo'] == '' ? $this->modulo_controlador .'_campos' : $config['modulo_config']['prefijo'] .'_'. $this->modulo_controlador .'_campos';
			
		//Destruir variable
		unset($config);
	
		//Query
		$fields = array(
			"pc.id_campo",
			"uc.nombre_campo",
			"uc.agrupador_campo",
			"uc.tabla_relacional",
			"tc.nombre AS tipo" ,
		);
		$clause = array(
			"v.id_modulo" => CRM_Controller::$id_modulo,
			"v.vista" => $this->router->method,
			"uc.estado" => "activo",
			"p.pestana" => "Datos del Cliente"
		);
		$modulosCampos = $this->db->select($fields)
			->from('mod_vistas v' )
			->join('mod_pestanas p', 'p.id_vista = v.id_vista', 'LEFT OUTER')
			->join('mod_formularios f', 'f.id_pestana = p.id_pestana', 'LEFT OUTER')
			->join('mod_paneles pa', 'pa.id_formulario = f.id_formulario', 'LEFT OUTER')
			->join('mod_panel_campos pc', 'pc.id_panel=pa.id_panel', 'LEFT OUTER')
			->join($tabla_campos."  uc", "uc.id_campo = pc.id_campo", 'LEFT OUTER')
			->join('mod_tipo_campos tc', 'tc.id_tipo_campo = uc.id_tipo_campo', 'LEFT OUTER')
			->where($clause)
			->where_not_in("tc.nombre", array("button", "link", "submit"))
			->where_not_in("uc.nombre_campo", array("id_motivo"))
			->get()
			->result_array();
		
 		
 		if(!empty($modulosCampos))
		{
			$i=0;
			foreach($modulosCampos AS $result)
			{
				if(!empty($result["tabla_relacional"]) && $result["tabla_relacional"] != "usuarios")
				{
					$existing_key = Util::array_search_key(self::$modulo_campos, $result["tabla_relacional"]);
						
					if(!empty(self::$modulo_campos[$existing_key])){
						self::$modulo_campos[$existing_key][$result["tabla_relacional"]][] = $result;
					}else{
						self::$modulo_campos[$i][$result["tabla_relacional"]][] = $result;
					}
				}else{
					self::$modulo_campos[$i] = $result;
				}
				$i++;
			}
			
			//Recorrer arreglo para verificar cuales
			//estan llenos y cuales no.
			if(!empty(self::$modulo_campos))
			{
				//Reinicar vaariable
				self::$campos_completados = array();
                                
                                $total_campos               = 0;
                                $total_campos_completados   = 0;
                                foreach(self::$modulo_campos AS $campos)
				{
					//Verificar si el campos es un arreglo de 
					//dos dimensiones
					if(Util::is_two_dimensional($campos))
					{
						//Recorre Arreglo Bidimensional
						foreach($campos AS $tabla => $campoArray)
						{
 							//Recorrer arreglo de campos
							foreach($campoArray AS $campo)
							{
								$nombre_campo = str_replace("]", "", str_replace("[", "", $campo["nombre_campo"]));
                                                                
                                                                $clause = array (
									"uuid_cliente = UNHEX('$id_cliente')" => NULL,
									$nombre_campo.' <>' => ''
								);
								$result = $this->db->select($nombre_campo)
									->distinct()
									->from($tabla)
									->where($clause)
									->limit(1)
									->get()
									->result_array();
 								
								if(!empty($result)){
									//Si el campo esta lleno, marcarlo como lleno
									if(!empty($result[0][$nombre_campo]) && $result[0][$nombre_campo] != ""){
										self::$campos_completados[] = $nombre_campo ." = ".$result[0][$nombre_campo];
                                                                                
                                                                                if(isset($campos["tipo"]) and $campos["tipo"] != "checkbox")
                                                                                    $total_campos_completados += 1;
									}
                                                                        
                                                                        if(isset($campos["tipo"]) and $campos["tipo"] != "checkbox")
                                                                            $total_campos += 1;
								}
							}
							
						}
					}
					else
					{	 
						$nombre_campo = str_replace("]", "", str_replace("[", "", $campos["nombre_campo"]));
                                                
                                                		
						$clause = array (
							"uuid_cliente = UNHEX('$id_cliente')" => NULL,
						);
						$result = $this->db->select($nombre_campo)
							->distinct()
							->from("cl_clientes")
							->where($clause)
							->get()
							->result_array();
						if(!empty($result)){
							//Si el campo esta lleno, marcarlo como lleno
							if(!empty($result[0][$nombre_campo]) && $result[0][$nombre_campo] != ""){
 								self::$campos_completados[] = $nombre_campo ." = ".$result[0][$nombre_campo];
                                                                
                                                                if(isset($campos["tipo"]) and $campos["tipo"] != "checkbox")
                                                                    $total_campos_completados += 1;
                                                                
                                                                //cuando el estado civil = 56 - Soltero
                                                                //total_campos_completados suma dos el mismo ciclo
                                                                //para evitar que muestre un % menos a 100 por no 
                                                                //escribir el apellido de casada
                                                                if($nombre_campo == "estado_civil" && $result[0][$nombre_campo] == "56")
                                                                    $total_campos_completados += 1;
							}
                                                        
                                                        if(isset($campos["tipo"]) and $campos["tipo"] != "checkbox")
                                                            $total_campos += 1;
						}
					}
				}
			}
		}
 		/**
		 * Calcular en porcentaje la cantidad de campos
		 * que tiene completado VS la cantidad total de campos.
		 */
		//$total_campos = count($modulosCampos);
                
                $porcentaje_completado  = round(($total_campos_completados * 100 / $total_campos), 0, PHP_ROUND_HALF_UP);
                $porcentaje_completado  = $porcentaje_completado > 100 ? 100 : $porcentaje_completado;

                
//		echo "<br>TOTAL CAMPOS: " .$total_campos."<br>";
//		echo "CAMPOS COMPLETADO: " .$total_campos_completados."<br>";
//		echo "PORCENTAJE: " .$porcentaje_completado;
//		
//		echo "<pre>";
//		print_r(self::$campos_completados);
//		echo "<pre>";
//		die();
		
		return $porcentaje_completado;
	}
	
	function seleccionar_cliente()
	{
		$uuid_cliente = $this->input->post('uuid_cliente', true);
		
		if($uuid_cliente==NULL){
			return false;
		}
		
		$fields = array (
			"id_cliente",
			"nombre",
			"ccat.valor AS tipo_cliente",
		);
		$clause = array (
			"uuid_cliente = UNHEX('$uuid_cliente')" => NULL,
		);
		$result = $this->db->select($fields)
			->distinct()
			->from("cl_clientes AS cl")
			->join('cl_clientes_cat AS ccat', 'ccat.id_cat = cl.id_tipo_cliente', 'LEFT')
			->where($clause)
			->get()
			->result_array();
		return !empty($result) ? $result[0] : "";
	}
	
	/**
	 * Seleccionar los RUC de un cliente
	 * 
	 * @param string $uuid_cliente
	 * @return array
	 */
	function seleccionar_cliente_sociedades($uuid_cliente=NULL)
	{
		if($uuid_cliente==NULL){
			return false;
		}
		
		$fields = array (
			"id_sociedad",
			"ruc",
			"nombre_comercial",
		);
		$clause = array (
			"uuid_cliente = UNHEX('$uuid_cliente')" => NULL,
		);
		return $this->db->select($fields)
			->distinct()
			->from("cl_clientes_sociedades")
			->where($clause)
			->get()
			->result_array();
	}
	
	/**
	 * Seleccionar los Telefonos de un cliente
	 *
	 * @param string $uuid_cliente
	 * @return array
	 */
	function seleccionar_cliente_telefonos($uuid_cliente=NULL)
	{
		if($uuid_cliente==NULL){
			return false;
		}
	
		$fields = array (
			"id_telefono",
			"no_telefono",
		);
		$clause = array (
			"uuid_cliente = UNHEX('$uuid_cliente')" => NULL,
		);
		return $this->db->select($fields)
			->distinct()
			->from("cl_cliente_telefonos")
			->where($clause)
			->get()
			->result_array();
	}
	
	/**
	 * Seleccionar los Correos de un cliente
	 *
	 * @param string $uuid_cliente
	 * @return array
	 */
	function seleccionar_cliente_correos($uuid_cliente=NULL)
	{
		if($uuid_cliente==NULL){
			return false;
		}
	
		$fields = array (
			"id_correo",
			"correo",
		);
		$clause = array (
			"uuid_cliente = UNHEX('$uuid_cliente')" => NULL,
		);
		return $this->db->select($fields)
			->distinct()
			->from("cl_cliente_correos")
			->where($clause)
			->get()
			->result_array();
	}
	
	/**
	 * Seleccionar los nombres comerciales
	 *
	 * @param string $uuid_cliente
	 * @return array
	 */
	public function seleccionar_nombres_comerciales(){
		$uuid_cliente = $this->input->post('uuid_cliente', true);
		$fields = array(
 				"HEX(cs.uuid_sociedad) AS uuid_sociedad",
				"cs.nombre_comercial" 
				
 		);
		
		$clause["HEX(cs.uuid_cliente)= '".$uuid_cliente."'"] = NULL;
		 
		$lista = $this->db->select($fields)
		->from('cl_clientes_sociedades cs ')
 		->where($clause)
		->order_by("cs.nombre_comercial", "ASC")
		->get()
		->result_array();
		 
		return $lista;
	}
	
	
	/**
	 * Seleccionar los contactos de nombres comerciales
	 *
 	 * @return array
	 */
	public function seleccionar_contactos_comerciales(){
		$uuid_sociedad = $this->input->post('uuid_sociedad', true);
		$fields = array(
				"HEX(sc.uuid_sociedad) AS uuid_sociedad",
				"concat(con.nombre,' ',con.apellido) as nombre"
	
		);
	
		$clause["HEX(sc.uuid_sociedad)= '".$uuid_sociedad."'"] = NULL;
			
		$lista = $this->db->select($fields)
		->from('cl_cliente_sociedades_contactos as sc')
		->join('con_contactos as con', 'con.uuid_contacto = sc.uuid_contacto', 'LEFT OUTER')
		->where($clause)
		->order_by("con.nombre", "ASC")
		->get()
		->result_array();
			
		return $lista;
 	}
	
	/*
	 * Buscar la informacion de una Sociedad
	 * de un cliente especifico
	 */
	function comp__seleccionar_sociedad($clause=array())
	{
		if(empty($clause)){
			return false;
		}
		
		$result = $this->db->select()
			->distinct()
			->from('cl_clientes_sociedades')
			->where($clause)
			->get()
			->result_array();
		return $result;
	}
	/**
	 * Guardar formulario de crear Cliente Natural.
	 *
	 * @return boolean
	 */
	function guardar_cliente_natural()
	{
	
	
		if(Util::is_array_empty($_POST)){
			return false;
		}
	
		//Init Fieldset variable
		$fieldset = array();
                
		//Remover el boton de submit que por default
		unset($_POST["campo"]["guardarPrincipal"]);
	
		//Recorrer arreglo e insertar los valores que no estan vacios
		//en el fieldset
		foreach ($_POST["campo"] AS $fieldname => $fieldvalue) {
			if(empty($fieldvalue)){
				continue;
			}
	
			//check if is an array
			if(is_array($fieldvalue)){
				foreach ($fieldvalue AS $name => $value) {
					if($value != ""){
						if(preg_match("/id_asignado/i", $name)){
							$fieldset["$name = UNHEX('$value')"] = NULL;
	
						}else{
							$fieldset[$name] = $this->security->xss_clean($value);
						}
					}
				}
			}else{
	
				if(preg_match("/id_asignado/i", $fieldname)){
	
					//$fieldset["$fieldname = UNHEX('$fieldvalue')"] = NULL;
					$this->db->set($fieldname, "UNHEX('$fieldvalue')", FALSE);
				}else{
					$fieldset[$fieldname] = $fieldvalue;
				}
	
			}
		}
		//Si el $fieldset es vacio
		if(Util::is_array_empty($fieldset)){
			return false;
		}
	
		//
		// Begin Transaction
		// docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
		//
		$this->db->trans_start();
		$fieldset["exonerado"] = 0;
		if(isset($_POST['campo']['exonerado'] )){
			$fieldset["exonerado"] = 1;
		}
			
		//Campos adicionales
		$this->db->set('uuid_cliente', 'ORDER_UUID(uuid())', FALSE);
		$fieldset["creado_por"] = $this->session->userdata('id_usuario');
		$fieldset["fecha_creacion"] = date('Y-m-d H-i-s');
	
		//Guardar Cliente Natural
		$this->db->insert('cl_clientes', $fieldset);
		$idCliente = $this->db->insert_id();
	
	
		$fields = array(
				"HEX(uuid_cliente) AS uuid_cliente_var",
				"uuid_cliente",
		);
		$result = $this->db->select($fields)
		->distinct()
		->from('cl_clientes')
		->where ("id_cliente", $idCliente)
		->get()
		->result_array();
	
		//Si la informacion del cliente ya se ha guardado
		//y ya tenemos el ID del cliente,
		if(!empty($idCliente))
		{
			//--------------------
			// GUARDAR TELEFONOS y CELULARES
			//--------------------
				
			//FunciÃ³n para insertar uno o multiples Celulares
			if(!empty($_POST["celular"])){
	
					
				foreach ($_POST["celular"]["celular"] AS $celular)
				{
	
	
					//Si es vacio continuar al siguiente telefono
					if(empty($celular) && $celular == ""){
						continue;
					}
	
					//Guardar datos
					$fieldset = array(
							'uuid_cliente' => $result[0]["uuid_cliente"],
							'no_celular' => $this->security->xss_clean($celular)
					);
					$this->db->insert('cl_cliente_telefonos', $fieldset);
				}
			}
			//FunciÃ³n para insertar uno o multiples Telefonos
			if(!empty($_POST["telefono"]["no_telefono"])){
	
				//Guardar datos
				$fieldset = array(
						'uuid_cliente' => $result[0]["uuid_cliente"],
						'no_telefono' => $this->security->xss_clean($_POST["telefono"]["no_telefono"])
				);
				$this->db->insert('cl_cliente_telefonos', $fieldset);
	
			}
				
			//FunciÃ³n para insertar uno o multiples Correos
			if(!empty($_POST["correos"])){
				foreach ($_POST["correos"]["correo"] AS $correo)
				{
						
 					//Si es vacio continuar al siguiente telefono
					if(empty($correo) && $correo == ""){
						continue;
					}
						
					//Guardar datos
					$fieldset = array(
							'uuid_cliente' => $result[0]["uuid_cliente"],
							'correo' => $this->security->xss_clean($correo)
					);
					$this->db->insert('cl_cliente_correos', $fieldset);
				}
			}
		}
	
	
	
	
		//Subir Archivos
		$files = $_FILES;
		if(!empty($files['campo']))
		{
			$filename = $files['campo']['name']['imagen_archivo'];
			$filename = str_replace(" ","_", $filename);
			$filename = str_replace("-","_", $filename);
			$file_name = "cl_". $filename;
			$config['upload_path']      = './public/uploads/clientes/';
			$config['file_name']        = $file_name;
			$config['allowed_types']    = '*';
	
			$extension = ".".end(explode('.', $files['campo']['name']['imagen_archivo']));
	
			$_FILES['campo']['name']     = $files['campo']['name']['imagen_archivo'];
			$_FILES['campo']['type']     = $files['campo']['type']['imagen_archivo'];
			$_FILES['campo']['tmp_name'] = $files['campo']['tmp_name']['imagen_archivo'];
			$_FILES['campo']['error']    = $files['campo']['error']['imagen_archivo'];
			$_FILES['campo']['size']     = $files['campo']['size']['imagen_archivo'];
			$_FILES['campo']['filename'] = $config['file_name']. $extension;
	
			$this->load->library('upload', $config);
			$this->upload->initialize($config);
	
			if($this->upload->do_upload("campo"))
			{
				$fileINFO = $this->upload->data();
	
				//Guardar datos de los archivos
				$fieldset = array(
						'imagen_archivo' => 'clientes/'.$fileINFO["file_name"]
				);
				$clause = array(
						"id_cliente" => $idCliente
				);
				$this->db->where($clause)->update('cl_clientes', $fieldset);
			}
		}
	
	
		//---------------------------------------
		//End Transaction
		$this->db->trans_complete();
	
		// Managing Errors
		if ($this->db->trans_status() === FALSE) {
	
			log_message("error", "MODULO: Clientes --> No se pudo guadar los datos del cliente en DB.");
			return false;
	
		} else {
	
			//guardar el id en variable de session
			$this->session->set_userdata('uuid_cliente', $result[0]["uuid_cliente_var"]);
	
			return true;
		}
	}

	function actualizar_cliente_natural($id_cliente=NULL)
	{
		 
		
		 
		if(Util::is_array_empty($_POST) || $id_cliente==NULL){
			return false;
		}
 	
		//Init Fieldset variable
		$fieldset = array();
		 
		if( isset($_POST["campo"]['guardarPasatiempos'])){  //PestaÃ±a de Propiedaddes.
			if(!empty($_POST["campo"])){
				//Limpio Antes de llenar nuevamente
				$clause = array(
						"uuid_cliente = UNHEX('$id_cliente')" => NULL,
 				);
				$this->db->where($clause)
				->delete('cl_cliente_info_personal');
				
				foreach ($_POST["campo"]['salud']  AS $salud)
				{
 							//Guardar datos
						$fieldset = array(
 								'id_tipo' => $this->security->xss_clean($salud)
						);
						$this->db->set("uuid_cliente", "UNHEX('$id_cliente')", FALSE);
						$this->db->insert('cl_cliente_info_personal', $fieldset);
					 
				}
				foreach ($_POST["campo"]['intereses']  AS $intereses)
				{
					//Guardar datos
					$fieldset = array(
							'id_tipo' => $this->security->xss_clean($intereses)
					);
					$this->db->set("uuid_cliente", "UNHEX('$id_cliente')", FALSE);
					$this->db->insert('cl_cliente_info_personal', $fieldset);
				
				}
				foreach ($_POST["campo"]['pasatiempo']  AS $pasatiempo)
				{
					//Guardar datos
					$fieldset = array(
							'id_tipo' => $this->security->xss_clean($pasatiempo)
					);
					$this->db->set("uuid_cliente", "UNHEX('$id_cliente')", FALSE);
					$this->db->insert('cl_cliente_info_personal', $fieldset);
 				}
			
			}
  			
		}	
		
	 
 		if( isset($_POST["campo"]['guardarPropiedad'])){  //PestaÃ±a de Propiedaddes.
			if(!empty($_POST["propiedades"])){
			
				foreach ($_POST["propiedades"]  AS $propiedad)
				{
					//Si es vacio continuar al siguiente telefono
					if(empty($propiedad) && $propiedad == ""){
						continue;
					}
					$id_propiedad = !empty($propiedad["id_propiedad"]) ? $propiedad["id_propiedad"] : "";
 					//Verificar si la Propiedad ya existe
					$clause = array(
							'id_propiedad' => $this->security->xss_clean($id_propiedad),
 					);
			
					$checkPoliza = $this->db->select()
					->distinct()
					->from('cl_cliente_propiedades')
					->where($clause)
					->get()
					->result_array();
					
					$fieldset = array(
							'propiedad_nombre' => $this->security->xss_clean($propiedad['propiedad_nombre']),
							'provincia' => $this->security->xss_clean($propiedad['provincia']),
							'no_local' => $this->security->xss_clean($propiedad['no_local']),
							'no_finca' => $this->security->xss_clean($propiedad['no_finca']),
							'area' => $this->security->xss_clean($propiedad['area']),
							'id_tipo_propiedad' => $this->security->xss_clean($propiedad['id_tipo_propiedad'])
					);
					
					if(empty($checkPoliza))
					{
						//Guardar datos
						$this->db->set("uuid_cliente", "UNHEX('$id_cliente')", FALSE);
						$this->db->insert('cl_cliente_propiedades', $fieldset);
					} 
					else{
						//Actualizar Cliente Potencial
						$this->db->where($clause)->update('cl_cliente_propiedades', $fieldset);
						
					}
			
				}
				 
			
			}
			
		}
  		if( isset($_POST["campo"]['guardarPolizas'])){  //PestaÃ±a de Polizas.
			if(!empty($_POST["poliza"])){
	
				foreach ($_POST["poliza"]  AS $poliza_valor)
				{
					if(empty($poliza_valor) && $poliza_valor == ""){
						continue;
					}
					$id_poliza = !empty($poliza_valor['id_poliza']) ? $poliza_valor['id_poliza']: "";
					//Verificar si la poliza ya existe
					$clause = array(
 							'id_poliza' => $this->security->xss_clean( $id_poliza ),
 					);
	
					$checkPoliza = $this->db->select()
					->distinct()
					->from('cl_cliente_polizas')
					->where($clause)
					->get()
					->result_array();
					//Guardar datos
					$fieldset = array(
							'id_aseguradora' => $this->security->xss_clean($poliza_valor['id_aseguradora']),
							'id_ramo' => $this->security->xss_clean($poliza_valor['id_ramo'])
					);
 					if(empty($checkPoliza))
					{
						
						$this->db->set("uuid_cliente", "UNHEX('$id_cliente')", FALSE);
						$this->db->insert('cl_cliente_polizas', $fieldset);
					}
					else{
						//Actualizar Poliza
						$this->db->where($clause)->update('cl_cliente_polizas', $fieldset);
					}
	
				}
	
			}
				
		}

	 
		if( isset($_POST["campo"]['guardarAgentes'])){  //PestaÃ±a de Agentes.
			if(!empty($_POST["agentes"])){
		
				foreach ($_POST["agentes"]  AS $agente_valor)
				{
					if(empty($agente_valor) && $agente_valor == ""){
						continue;
					}
					$id_agente = !empty($agente_valor['id_agente']) ? $agente_valor['id_agente']: "";
					//Verificar si la poliza ya existe
					$clause = array(
							'id_agente' => $this->security->xss_clean( $id_agente  ),
					);
					 
					$nombre_agente = $agente_valor['nombre'];
					$checkPoliza = $this->db->select()
					->distinct()
					->from('cl_cliente_agentes')
					->where($clause)
					->get()
					->result_array();
					//Guardar datos
					$fieldset = array(
							//'id_aseguradora' => $this->security->xss_clean($agente_valor['id_aseguradora']),
							'id_tipo_comision' => $this->security->xss_clean($agente_valor['id_tipo_comision']),
							'porcentage_comision' => $this->security->xss_clean($agente_valor['porcentage_comision'])
					);
					$this->db->set("nombre", "UNHEX('$nombre_agente')", FALSE);
					$this->db->set("uuid_cliente", "UNHEX('$id_cliente')", FALSE);
					if(empty($checkPoliza))
					{
						
 						$this->db->insert('cl_cliente_agentes', $fieldset);
					}
					else{
						//Actualizar Poliza
						$this->db->where($clause)->update('cl_cliente_agentes', $fieldset);
					}
		
				}
		
			}
		
		}
		 
		if( isset($_POST["campo"]['guardarAdicional'])){  //PestaÃ±a Principal. Datos del Contacto
	
			unset($_POST["campo"]["guardarAdicional"]);
			//Recorrer arreglo e insertar los valores que no estan vacios
			foreach ($_POST["campo"] AS $fieldname => $fieldvalue) {
				if(empty($fieldvalue)){
					continue;
				}
					
				//check if is an array
				if(is_array($fieldvalue)){
					foreach ($fieldvalue AS $name => $value) {
						if($value != ""){
							if(preg_match("/id_asignado/i", $name)){
								$fieldset["$name = UNHEX('$value')"] = NULL;
									
							}else{
								$fieldset[$name] = $this->security->xss_clean($value);
							}
						}
					}
				}else{
	
					if(preg_match("/id_asignado/i", $fieldname)){
						$this->db->set($fieldname, "UNHEX('$fieldvalue')", FALSE);
					}else{
						$fieldset[$fieldname] = $fieldvalue;
					}
	
				}
			}
	
			$fieldset["hijo"] = 0;
			if(isset($_POST['campo']['hijo'] )){
				$fieldset["hijo"] = 1;
			}
				
			if(Util::is_array_empty($fieldset)){
				return false;
			}
	
			$this->db->trans_start();
	
			$clause = array(
					"uuid_cliente = UNHEX('$id_cliente')" => NULL
			);
	
			//Actualizar Cliente Natural
			$this->db->where($clause)->update('cl_clientes', $fieldset);
			
 		///--------------------
		// GUARDAR HIJOS
		//--------------------
			if(!empty($_POST["hijos"])){
				foreach ($_POST["hijos"]  AS $hijo)
				{
				 
					//Si es vacio continuar al siguiente telefono
 					if( $hijo["fecha_nacimiento"] ==  "" ){
						continue;
					}
					
					$uuid_hijo = !empty($hijo["uuid_hijo"]) ? $hijo["uuid_hijo"] : "";
					
					//Arreglo de campos a guardar
					$fieldset = array(
							'fecha_nacimiento' 	 => $hijo["fecha_nacimiento"],
							'id_sexo'			 => $hijo["id_sexo"]
 					);
					 
					
					//Verificar si el Hijo ya existe
					$clause = array(
							"uuid_cliente = UNHEX('$id_cliente')" => NULL,
							"uuid_hijo 	  = UNHEX('$uuid_hijo')" => NULL
 					);
					$checkHijo = $this->db->select()
					->distinct()
					->from('cl_cliente_hijos')
					->where($clause)
					->get()
					->result_array();
						
					if(empty($checkHijo))
					{

						$this->db->set("uuid_cliente", "UNHEX('$id_cliente')", FALSE);
						$this->db->set('uuid_hijo', 'ORDER_UUID(uuid())', FALSE);
						$this->db->insert('cl_cliente_hijos', $fieldset);
  					}else
					{
						//Actualizar Cliente Potencial
						$this->db->where($clause)->update('cl_cliente_hijos', $fieldset);
					}
				}
 			}
				
		}
	
		if( isset($_POST["campo"]['guardarPrincipal'])){  //PestaÃ±a Principal. Datos del Contacto
	
			unset($_POST["campo"]["guardarPrincipal"]);
			//Recorrer arreglo e insertar los valores que no estan vacios
			foreach ($_POST["campo"] AS $fieldname => $fieldvalue) {
				//if(empty($fieldvalue)){
				//	continue;
				//}
                                //Es necesario poder recibir campos vacios...
					
				//check if is an array
				if(is_array($fieldvalue)){
					foreach ($fieldvalue AS $name => $value) {
						if($value != ""){
							if(preg_match("/id_asignado/i", $name)){
								$fieldset["$name = UNHEX('$value')"] = NULL;
									
							}else{
								$fieldset[$name] = $this->security->xss_clean($value);
							}
						}
					}
				}else{
	
					if(preg_match("/id_asignado/i", $fieldname)){
						$this->db->set($fieldname, "UNHEX('$fieldvalue')", FALSE);
					}else{
						$fieldset[$fieldname] = $fieldvalue;
					}
	
				}
			}
	
			$fieldset["exonerado"] = 0;
			if(isset($_POST['campo']['exonerado'] )){
				$fieldset["exonerado"] = 1;
			}
				
	
			if(Util::is_array_empty($fieldset)){
				return false;
			}
	
			$this->db->trans_start();
	
			$clause = array(
					"uuid_cliente = UNHEX('$id_cliente')" => NULL
			);
	
			//Actualizar Cliente Natural
			$this->db->where($clause)->update('cl_clientes', $fieldset);
				
			//Parte donde se verificar y se actualizar el telefono del clientes
	
			if(!empty($_POST["telefono"]['no_telefono'])){
				$telefono = $_POST["telefono"]['no_telefono'];
				//Si es vacio continuar al siguiente telefono
				if(empty($telefono) && $telefono == ""){
					continue;
				}
					
				//Verificar si el telefono ya existe
				$clause = array(
						"uuid_cliente = UNHEX('$id_cliente')" => NULL,
						'no_telefono' => $this->security->xss_clean($telefono)
				);
				$checkTelefono = $this->db->select()
				->distinct()
				->from('cl_cliente_telefonos')
				->where($clause)
				->get()
				->result_array();
	
				$fieldset = array(
						'no_telefono' => $this->security->xss_clean($telefono)
				);
				if( count($checkTelefono) < 1){ //No existe
					$clause = array(
							"uuid_cliente = UNHEX('$id_cliente')" => NULL,
							'no_telefono  <> ' => ''
					);
					$this->db->where($clause)
					->delete('cl_cliente_telefonos');
	
					$this->db->set("uuid_cliente", "UNHEX('$id_cliente')", FALSE);
					$this->db->insert('cl_cliente_telefonos', $fieldset);
				}
	
			}
				
 			//Parte donde se verificar y se actualizar el correo del clientes
	
			if(!empty($_POST["correos"])){
				foreach ($_POST["correos"]["correo"] AS $correo)
				{
					//Si es vacio continuar al siguiente telefono
					if(empty($correo) && $correo == ""){
						continue;
					}
	
					//Verificar si el correo ya existe
					$clause = array(
							"uuid_cliente = UNHEX('$id_cliente')" => NULL,
							'correo' => $this->security->xss_clean($correo)
					);
					$checkCorreo = $this->db->select()
					->distinct()
					->from('cl_cliente_correos')
					->where($clause)
					->get()
					->result_array();
	
					if(empty($checkCorreo))
					{
						//Guardar datos
						$fieldset = array(
								'correo' => $this->security->xss_clean($correo)
						);
						$this->db->set("uuid_cliente", "UNHEX('$id_cliente')", FALSE);
						$this->db->insert('cl_cliente_correos', $fieldset);
					}
				}
			}
				
			//Parte donde se verificar y se actualizar el celular del clientes
	
			if(!empty($_POST["celular"])){
				foreach ($_POST["celular"]["no_celular"] AS $celular)
				{
					//Si es vacio continuar al siguiente telefono
					if(empty($celular) && $celular == ""){
						continue;
					}
	
					//Verificar si el correo ya existe
					$clause = array(
							"uuid_cliente = UNHEX('$id_cliente')" => NULL,
							'no_celular' => $this->security->xss_clean($celular)
					);
					$checkCelular = $this->db->select()
					->distinct()
					->from('cl_cliente_telefonos')
					->where($clause)
					->get()
					->result_array();
	
					if(empty($checkCelular))
					{
						//Guardar datos
						$fieldset = array(
								'no_celular' => $this->security->xss_clean($celular)
						);
						$this->db->set("uuid_cliente", "UNHEX('$id_cliente')", FALSE);
						$this->db->insert('cl_cliente_telefonos', $fieldset);
						 
					}
				}
			}
	
	
			//Subir Imagen de cliente
			$files = $_FILES;
	
				
			if(!empty($files['campo']['name']['imagen_archivo']))
			{
 
				$clause = array(
						"uuid_cliente = UNHEX('$id_cliente')" => NULL,
				);
				$checkCliente = $this->db->select("imagen_archivo")
				->distinct()
				->from('cl_clientes')
				->where($clause)
				->get()
				->result_array();
	
				if(!empty($checkCliente)){
	
					$imagen_vieja = !empty($checkCliente[0]["imagen_archivo"]) ? realpath(str_replace("system/", "", BASEPATH). "public/uploads/". $checkCliente[0]["imagen_archivo"]) : "";
	
					if(file_exists($imagen_vieja)) {
	
						//Si existe la imagen BORRARLA
						unlink($imagen_vieja);
					}
				}
	
				$filename = $files['campo']['name']['imagen_archivo'];
				$filename = str_replace(" ","_", $filename);
				$filename = str_replace("-","_", $filename);
				$file_name = "cl_". $filename;
	
				$config['upload_path']      = './public/uploads/clientes/';
				$config['file_name']        = $file_name;
				$config['allowed_types']    = '*';
	
				$extension = ".".end(explode('.', $files['campo']['name']['imagen_archivo']));
	
				$_FILES['campo']['name']     = $files['campo']['name']['imagen_archivo'];
				$_FILES['campo']['type']     = $files['campo']['type']['imagen_archivo'];
				$_FILES['campo']['tmp_name'] = $files['campo']['tmp_name']['imagen_archivo'];
				$_FILES['campo']['error']    = $files['campo']['error']['imagen_archivo'];
				$_FILES['campo']['size']     = $files['campo']['size']['imagen_archivo'];
				$_FILES['campo']['filename'] = $config['file_name']. $extension;
	
				$this->load->library('upload', $config);
				$this->upload->initialize($config);
	
				if($this->upload->do_upload("campo"))
				{
					$fileINFO = $this->upload->data();
	
					//Guardar datos de los archivos
					$fieldset = array(
							'imagen_archivo' => "clientes/".$fileINFO["file_name"]
					);
					$clause = array(
							"uuid_cliente = UNHEX('$id_cliente')" => NULL,
					);
					$this->db->where($clause)->update('cl_clientes', $fieldset);
				}
			}
	
	
	
		}
	
		//---------------------------------------
		//End Transaction
		$this->db->trans_complete();
	
		// Managing Errors
		if ($this->db->trans_status() === FALSE) {
	
			log_message("error", "MODULO: Clientes --> No se pudo guadar los datos del cliente en DB.");
			return false;
	
		} else {
	
			//guardar el id en variable de session
			$this->session->set_userdata('updatedCliente', $id_cliente);
	
			//Limpiar cache de clientes
			CRM_Controller::$cache->delete("contarClientes");
			CRM_Controller::$cache->delete("listaClientes");
			CRM_Controller::$cache->delete("infoCliente". $id_cliente);
			CRM_Controller::$cache->delete("registrosClientes");
			return true;
		}
	}
	
	
	

	/**
	 * Esta funcion elmina la propiedad seleccionado
	 * relacionado de cliente actual que estamos viendo.
	 *
	 * @return boolean|multitype:boolean string
	 */
	function eliminar_poliza_cliente()
	{
		$id_poliza = $this->input->post('id_poliza', true);
	
		//Retorna false si el nombre es vacio
		if(empty( $id_poliza )){
			return false;
		}
	
		//
		// Begin Transaction
		// docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
		//
		$this->db->trans_start();
	
		//Borrar el telefono seleccionado
		$clause = array(
				"id_poliza" => $id_poliza
		);
		$this->db->where($clause)
		->delete('cl_cliente_polizas');
		//echo $this->db->last_query();
		// ---------------------------------------
		// End Transaction
		$this->db->trans_complete();
	
		// Managing Errors
		if ($this->db->trans_status() === FALSE) {
	
			log_message("error", "MODULO: Cliente --> No se pudo eliminar la póliza del cliente.");
	
			return array(
					"respuesta" => false,
					"mensaje" => "Hubo un error al tratar de eliminar la póliza  seleccionada"
			);
	
		} else {
			return array(
					"respuesta" => true,
					"mensaje" => "Se ha eliminado la póliza  seleccionada satisfactoriamente."
			);
		}
	}
	
	/**
	 * Esta funcion elmina la propiedad seleccionado
	 * relacionado de cliente actual que estamos viendo.
	 *
	 * @return boolean|multitype:boolean string
	 */
	function eliminar_propiedad_cliente()
	{
		$id_propiedad = $this->input->post('id_propiedad', true);
	
		//Retorna false si el nombre es vacio
		if(empty( $id_propiedad )){
			return false;
		}
	
		//
		// Begin Transaction
		// docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
		//
		$this->db->trans_start();
	
		//Borrar el telefono seleccionado
		$clause = array(
			"id_propiedad" => $id_propiedad
		);
		$this->db->where($clause)
		->delete('cl_cliente_propiedades');
		//echo $this->db->last_query();
		// ---------------------------------------
		// End Transaction
		$this->db->trans_complete();
	
		// Managing Errors
		if ($this->db->trans_status() === FALSE) {
	
			log_message("error", "MODULO: Cliente --> No se pudo eliminar la propiedad del cliente.");
	
			return array(
					"respuesta" => false,
					"mensaje" => "Hubo un error al tratar de eliminar la propiedad  seleccionada"
			);
	
		} else {
			return array(
					"respuesta" => true,
					"mensaje" => "Se ha eliminado la propiedad seleccionada satisfactoriamente."
			);
		}
	}
	/**
	 * Esta funcion elmina el agente seleccionado
 	 *
	 * @return boolean|multitype:boolean string
	 */
	function eliminar_agente_cliente()
	{
		$id_agente = $this->input->post('id_agente', true);
	
		//Retorna false si el nombre es vacio
		if(empty( $id_agente )){
			return false;
		}
	
		//
		// Begin Transaction
		// docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
		//
		$this->db->trans_start();
	
		//Borrar el telefono seleccionado
		$clause = array(
				"id_agente" => $id_agente
		);
		$this->db->where($clause)
		->delete('cl_cliente_agentes');
		//echo $this->db->last_query();
		// ---------------------------------------
		// End Transaction
		$this->db->trans_complete();
	
		// Managing Errors
		if ($this->db->trans_status() === FALSE) {
	
			log_message("error", "MODULO: Cliente --> No se pudo eliminar el agente del cliente.");
	
			return array(
					"respuesta" => false,
					"mensaje" => "Hubo un error al tratar de eliminar el agente seleccionada"
			);
	
		} else {
			return array(
					"respuesta" => true,
					"mensaje" => "Se ha eliminado el agente seleccionada satisfactoriamente."
			);
		}
	}
	
	/**
	 * Esta funcion elmina el hijo seleccionado
	 * relacionado de cliente actual que estamos viendo.
	 *
	 * @return boolean|multitype:boolean string
	 */
	function eliminar_hijo_cliente()
	{
		$id_hijo = $this->input->post('id_hijo', true);
 	
		//Retorna false si el nombre es vacio
		if(empty($id_hijo)){
			return false;
		}
	
		//
		// Begin Transaction
		// docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
		//
		$this->db->trans_start();
	
		//Borrar el telefono seleccionado
		$clause = array(
  				"id_hijo" => $id_hijo
 		);
		$this->db->where($clause)
		->delete('cl_cliente_hijos');
		//echo $this->db->last_query();
		// ---------------------------------------
		// End Transaction
		$this->db->trans_complete();
	
		// Managing Errors
		if ($this->db->trans_status() === FALSE) {
	
			log_message("error", "MODULO: Cliente --> No se pudo eliminar el hijo del cliente.");
	
			return array(
					"respuesta" => false,
					"mensaje" => "Hubo un error al tratar de eliminar el hijo seleccionado"
			);
	
		} else {
			return array(
					"respuesta" => true,
					"mensaje" => "Se ha eliminado el hijo seleccionado satisfactoriamente."
			);
		}
	}
	
	/**
	 * Esta funcion elmina el telefono seleccionado
	 * relacionado de cliente actual que estamos viendo.
	 *
	 * @return boolean|multitype:boolean string
	 */
	function eliminar_celular_cliente()
	{
		$id_cliente = $this->input->post('id_cliente', true);
		$no_celular = $this->input->post('no_celular', true);
	
		//Retorna false si el nombre es vacio
		if(empty($no_celular)){
			return false;
		}
	
		//
		// Begin Transaction
		// docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
		//
		$this->db->trans_start();
	
		//Borrar el telefono seleccionado
		$clause = array(
			 "HEX(uuid_cliente)" => $id_cliente,
				"no_celular" => $no_celular
		);
		$this->db->where($clause)
		->delete('cl_cliente_telefonos');
		//echo $this->db->last_query();
		// ---------------------------------------
		// End Transaction
		$this->db->trans_complete();
	
		// Managing Errors
		if ($this->db->trans_status() === FALSE) {
	
			log_message("error", "MODULO: Cliente --> No se pudo eliminar el telefono del cliente.");
	
			return array(
					"respuesta" => false,
					"mensaje" => "Hubo un error al tratar de eliminar el telefono seleccionado"
			);
	
		} else {
			return array(
					"respuesta" => true,
					"mensaje" => "Se ha eliminado el telefono seleccionado satisfactoriamente."
			);
		}
	}

	/**
	 * Seleccionar la informacion de un cliente Natural.
	 *
	 * @return array
	 */
	function seleccionar_informacion_de_cliente_Natural($id_cliente=NULL)
	{
		if(empty($id_cliente)){
			return false;
		}
	
		$result = array();
		$fields = array(
				"*",
				"HEX(id_asignado) AS id_asignado",
		);
		$clause = array(
				"uuid_cliente = UNHEX('$id_cliente')" => NULL
		);
		$cliente = $this->db->select($fields)
		->distinct()
		->from('cl_clientes')
		->where ($clause)
		->get()
		->result_array();
	
		if(!empty($cliente))
		{
			$result["imagen_archivo"] = !empty($cliente[0]["imagen_archivo"]) ? $cliente[0]["imagen_archivo"] : "";
			$result["nombre"] = !empty($cliente[0]["nombre"]) ? $cliente[0]["nombre"] : "";
			$result["apellido"] = !empty($cliente[0]["apellido"]) ? $cliente[0]["apellido"] : "";
			$result["apellido_materno"] = !empty($cliente[0]["apellido_materno"]) ? $cliente[0]["apellido_materno"] : "";
			$result["estado_civil"] = !empty($cliente[0]["estado_civil"]) ? $cliente[0]["estado_civil"] : "";
			$result["apellido_casada"] = !empty($cliente[0]["apellido_casada"]) ? $cliente[0]["apellido_casada"] : "";
			$result["id_sexo"] = !empty($cliente[0]["id_sexo"]) ? $cliente[0]["id_sexo"] : "";
			$result["fecha_nacimiento"] = !empty($cliente[0]["fecha_nacimiento"]) ? $cliente[0]["fecha_nacimiento"] : "";
			$result["id_nacionalidad"] = !empty($cliente[0]["id_nacionalidad"]) ? $cliente[0]["id_nacionalidad"] : "";
			$result["pais_origen"] = !empty($cliente[0]["pais_origen"]) ? $cliente[0]["pais_origen"] : "";
			$result["cargo"] = !empty($cliente[0]["cargo"]) ? $cliente[0]["cargo"] : "";
			$result["cedula"] = !empty($cliente[0]["cedula"]) ? $cliente[0]["cedula"] : "";
			$result["exonerado"] = !empty($cliente[0]["exonerado"]) ? $cliente[0]["exonerado"] : "";
			$result["id_tipo_contacto"] = !empty($cliente[0]["id_tipo_contacto"]) ? $cliente[0]["id_tipo_contacto"] : "";
			$result["id_toma_contacto"] = !empty($cliente[0]["id_toma_contacto"]) ? $cliente[0]["id_toma_contacto"] : "";
			$result["direccion"] = !empty($cliente[0]["direccion"]) ? $cliente[0]["direccion"] : "";
			$result["comentarios"] = !empty($cliente[0]["comentarios"]) ? $cliente[0]["comentarios"] : "";
			$result["id_asignado"] = !empty($cliente[0]["id_asignado"]) ? $cliente[0]["id_asignado"] : "";
	
			//PestaÃ±a Informacion Adicional
	
			$result["ocupacion"] = !empty($cliente[0]["id_asignado"]) ? $cliente[0]["ocupacion"] : "";
			$result["lugar_trabajo"] = !empty($cliente[0]["lugar_trabajo"]) ? $cliente[0]["lugar_trabajo"] : "";
			$result["direccion_trabajo"] = !empty($cliente[0]["direccion_trabajo"]) ? $cliente[0]["direccion_trabajo"] : "";
			$result["direccion_cobro"] = !empty($cliente[0]["direccion_cobro"]) ? $cliente[0]["direccion_cobro"] : "";
			$result["conyugue_nombre"] = !empty($cliente[0]["conyugue_nombre"]) ? $cliente[0]["conyugue_nombre"] : "";
			$result["conyugue_fecha_nacimiento"] = !empty($cliente[0]["conyugue_fecha_nacimiento"]) ? $cliente[0]["conyugue_fecha_nacimiento"] : "";
			$result["ingreso_familiar"] = !empty($cliente[0]["ingreso_familiar"]) ? $cliente[0]["ingreso_familiar"] : "";
	
			$result["hijo"] = !empty($cliente[0]["hijo"]) ? $cliente[0]["hijo"] : "";
				
	
			//Telefonos del Cliente
			$fields = array(
					"id_telefono",
					"no_telefono",
					"no_celular"
			);
			$clause = array(
					"uuid_cliente = UNHEX('$id_cliente')" => NULL
			);
			$telefonos = $this->db->select($fields)
			->distinct()
			->from('cl_cliente_telefonos')
			->where($clause)
			->get()
			->result_array();
			if(!empty($telefonos)){
 				foreach($telefonos as $valor_telefono){
	
					if( $valor_telefono['no_celular'] !='' ) {
						$result["no_celular"][] = $valor_telefono;
 					}
					else if( $valor_telefono['no_telefono']!=''){
						$result["no_telefono"] = $valor_telefono['no_telefono'];
					}
				}
	
			}
 
 			//Correos del Cliente
			$fields = array(
					"id_correo",
					"correo"
			);
			$correos = $this->db->select($fields)
			->distinct()
			->from('cl_cliente_correos')
			->where($clause)
			->get()
			->result_array();
			if(!empty($correos)){
				$result["correo"] = $correos;
			}
			 
			//Polizas del Cliente
			$fields = array(
					"id_poliza",
					"id_aseguradora",
					"id_ramo"
			);
			$polizas = $this->db->select($fields)
			->distinct()
			->from('cl_cliente_polizas')
			->where($clause)
			->get()
			->result_array();
			if(!empty($polizas)){
				$result["poliza"] = $polizas;
			}
	
			//Agentes del Cliente
			$fields = array(
 					"id_agente",
 					"HEX(nombre) AS nombre",
					"id_tipo_comision",
					"porcentage_comision"
			);
			$agents = $this->db->select($fields)
			->distinct()
			->from('cl_cliente_agentes')
			->where($clause)
			->get()
			->result_array();
			if(!empty($agents)){
				$result["agentes"] = $agents;
			}
			
			
			//Propiedades del Cliente
			$fields = array(
					"*" 
			);
			$propiedades = $this->db->select($fields)
			->distinct()
			->from('cl_cliente_propiedades')
			->where($clause)
			->get()
			->result_array();
			if(!empty($propiedades)){
				$result["propiedades"] = $propiedades;
			}
			
			//Pasatiempos - Salud - ETC
		$fields = array(
					"cc.nombre_campo","cat.id_cat ","cat.etiqueta "
			);
			
			$info_pers = $this->db->select($fields)
			->distinct()
			->from('cl_cliente_info_personal as p')
			->join('cl_clientes_cat AS cat', 'cat.id_cat = p.id_tipo', 'LEFT')
			->join('cl_clientes_campos AS cc', 'cc.id_campo = cat.id_campo', 'LEFT')
				
			->where($clause)
			->get()
			->result_array();
			
			 if(!empty($info_pers)){
				
				 foreach($info_pers as $row){
				 	if($row['nombre_campo'] == 'salud][')
				 	{
				 	 
				 		$result['salud'][] = array(
				 			'nombre_campo' 	=> $row['nombre_campo'],
				 			'id_cat' 		=> $row['id_cat'],
				 			'etiqueta' 		=> $row['etiqueta']
				 		); //$row['id_cat'];
				 	}
				 	else if($row['nombre_campo'] == 'pasatiempo][')
				 	{
				 		$result['pasatiempo'][] = array(
				 			'nombre_campo' 	=> $row['nombre_campo'],
				 			'id_cat' 		=> $row['id_cat'],
				 			'etiqueta' 		=> $row['etiqueta']
				 		); //$row['id_cat'];
				 	}
				 	else if($row['nombre_campo'] == 'intereses][')
				 	{
				 		$result['intereses'][] = array(
				 			'nombre_campo' 	=> $row['nombre_campo'],
				 			'id_cat' 		=> $row['id_cat'],
				 			'etiqueta' 		=> $row['etiqueta']
				 		); //$row['id_cat'];
				 	}
				 	
				 }
			} 
			
				
	
			//Hijos del Cliente
			$fields = array(
					"id_hijo",
					"id_sexo",
					"fecha_nacimiento",
					"HEX(uuid_hijo) AS uuid_hijo"
			);
	
			$result["hijos"] = array();
			$hijos = $this->db->select($fields)
			->distinct()
			->from('cl_cliente_hijos')
			->where($clause)
			->get()
			->result_array();
			if(!empty($hijos)){
				$result["hijos"] = $hijos;
			}
	
		}
		 
		//hijos[0][hijo_sexo]
		 
		return $result;
	}
	
	function generar_csv($id_clientes)
	{
		$id_clientes = (!empty($id_clientes) ? implode(', ', array_map(function($id_clientes){
			return "'".$id_clientes."'";;
		}, $id_clientes)) : "");
	
		$sql = " SELECT DISTINCT 
		CONCAT_WS(' ', IF(cl.nombre != '', cl.nombre, ''), IF(cl.apellido != '', cl.apellido, '')) AS Nombre, csoc.nombre_comercial AS 'Razon Social',
		CONCAT_WS(' ', IF(csoc.ruc != '', csoc.ruc, ''), IF(cl.cedula != '', cl.cedula, '')) AS 'RUC/Cedula', 
		CONCAT_WS(' ', IF(ctel.no_telefono != '', ctel.no_telefono, ''), IF(ctel.no_celular != '', ctel.no_celular, '')) AS Telefono,
		`cco`.`correo` AS Correo,  CONCAT_WS(' ', IF(usr.nombre != '', usr.nombre, ''), 
		IF(usr.apellido != '', usr.apellido, '')) AS Usuario 
		FROM (`cl_clientes` AS cl)
		LEFT JOIN `cl_clientes_cat` AS ccat ON `ccat`.`id_cat` = `cl`.`id_tipo_cliente` 
		LEFT JOIN `usuarios` AS usr ON `usr`.`uuid_usuario` = `cl`.`id_asignado` 
		LEFT JOIN `cl_clientes_sociedades` AS csoc ON `csoc`.`uuid_cliente` = `cl`.`uuid_cliente` 
		LEFT JOIN `cl_cliente_correos` AS cco ON `cco`.`uuid_cliente` = `cl`.`uuid_cliente` 
		LEFT JOIN `cl_cliente_telefonos` AS ctel ON `ctel`.`uuid_cliente` = `cl`.`uuid_cliente`  
		WHERE HEX(cl.uuid_cliente) IN(". $id_clientes .") GROUP BY cl.id_cliente ";
 		$query = $this->db->query($sql);
		
		return $this->dbutil->csv_from_result($query);
	}
}
?>

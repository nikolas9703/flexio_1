<?php
class Clientes_potenciales_model extends CI_Model 
{
	public function __construct() {
		parent::__construct();
		$this->uuid_usuario = CRM_Controller::$uuid_usuario;
		$this->load->library('Notifications');
		//HMVC Load Modules
		//$this->load->module(array('contactos'));
	}
	
	/**
	 * Conteo de clientes potenciales existentes
	 *
	 * @return [array] [description]
	 */
	function contar_clientes_potenciales($clause)
	{
		$fields = array (
			"cp.id_cliente_potencial",
		);
		$result = $this->db->select($fields)
				->distinct()
				->from('cp_clientes_potenciales AS cp')
				->join('cp_clientes_potenciales_cat AS cpcat', 'cpcat.id_cat = cp.id_toma_contacto', 'LEFT')
				->where($clause)
				->get()
				->result_array();
		return $result;
	}
	
	/**
	 * Listar Clientes Potenciales
	 *
	 * @param integer $sidx [description]
	 * @param integer $sord [description]
	 * @param integer $limit [description]
	 * @param integer $start [description]
	 * @return [array] [description]
	 */
	function listar_clientes_potenciales($clause, $sidx = 1, $sord = 1, $limit = 0, $start = 0) 
	{
		$fields = array (
			"cp.id_cliente_potencial",
			"HEX(cp.uuid_cliente_potencial) AS uuid_cliente_potencial",
			"CONCAT_WS(' ', IF(cp.nombre != '', cp.nombre, ''), IF(cp.apellido != '', cp.apellido, '')) AS nombre_cliente",
			"cp.apellido",
			"cp.compania",
			"cp.telefono",
			"cp.correo",
			"cpcat.etiqueta AS toma_contacto"
		);
		$result = $this->db->select($fields)
				->distinct()
				->from('cp_clientes_potenciales AS cp')
				->join('cp_clientes_potenciales_cat AS cpcat', 'cpcat.id_cat = cp.id_toma_contacto', 'LEFT')
				->where ($clause)
				->order_by($sidx, $sord)
				->limit($limit, $start)
				->get()
				->result_array();
		return $result;
	}
	
	/**
	 * Guardar formulario de crear Cliente Potencial.
	 * 
	 * @return boolean
	 */
	function guardar_cliente_potencial()
	{
		if(Util::is_array_empty($_POST)){
			return false;
		}
		
		//Init Fieldset variable
        $fieldset = array();

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
                        $fieldset[$name] = $this->security->xss_clean($value);
                    }
                }
            }else{
                 $fieldset[$fieldname] = $fieldvalue;
            }
        }
        
        //Verificar si esto se corrigio con la funcion, no tomar en cuenta los botones 
	    unset($fieldset['guardar']);
        
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
        $this->db->set('uuid_cliente_potencial', 'ORDER_UUID(uuid())', FALSE);
        $this->db->set("uuid_creado_por", "UNHEX('$this->uuid_usuario')", FALSE);
        $fieldset["creado_por"] = $this->session->userdata('id_usuario');
        $fieldset["fecha_creacion"] = date('Y-m-d H-i-s');
        
        //Guardar Cliente Potencial
        $this->db->insert('cp_clientes_potenciales', $fieldset);
        $idClientePotencial = $this->db->insert_id();
        
        //---------------------------------------
        //End Transaction
		$this->db->trans_complete();
				
		// Managing Errors
		if ($this->db->trans_status() === FALSE) {
			 
			log_message("error", "MODULO: Clientes Potenciales --> No se pudo guadar los datos del cliente potencial en DB.");
			return false;
			
		} else {	
			//Util::is_array_empty(array());
			Notifications::guardar_notificacion(
			array(
				"tipo_notificacion"=>'creacion',
				"modulo"=>"clientes_potenciales",
				"id"=>$idClientePotencial
			));
			//guardar el id en variable de session
			$this->session->set_userdata('idClientePotencial', $idClientePotencial);
			
		
			//Limpiar cache de clientes potenciales
			//CRM_Controller::$cache->delete("contarClientesPotenciales");
			//CRM_Controller::$cache->delete("listaClientesPotenciales");
			//CRM_Controller::$cache->delete("infoClientePotencial". $idClientePotencial);
			
			return true;
		}
	}
	
	/**
	 * Seleccionar la informacion de un cliente especifico.
	 *
	 * @return array
	 */
	function seleccionar_informacion_de_cliente_potencial($uuid_cliente=NULL)
	{
		$uuid_cliente = $uuid_cliente == NULL ? $this->input->post('uuid_cliente', true) : $uuid_cliente;
                
		
		if($uuid_cliente == NULL || empty($uuid_cliente)){
			return false;
		}
		
		$fields = array (
			"cp.nombre",
			"cp.apellido",
			"cp.compania",
			"cp.telefono",
			"cp.correo",
			"cp.id_toma_contacto",
			"cp.comentarios"
		);
                
                
		$clause = array(
			"cp.uuid_cliente_potencial = UNHEX('$uuid_cliente')" => NULL
		);
		$result = $this->db->select($fields)
				->distinct()
				->from('cp_clientes_potenciales AS cp')
				->join('cp_clientes_potenciales_cat AS cpcat', 'cpcat.id_cat = cp.id_toma_contacto', 'LEFT')
				->where ($clause)
				->get()
				->result_array();
		return !empty($result) ? $result[0] : array();
	}
	
	/**
	 * Guardar formulario de editar Cliente Potencial.
	 *
	 * @return boolean
	 */
	function editar_cliente_potencial($id_cliente=NULL)
	{

	   //Verificar si el POST no es vacio
		if(Util::is_array_empty($_POST)){
			return false;
		}
		
		//Luego verificar si el id_cliente no es vacio
		if(empty($id_cliente)){
			return false;
		}
		
		//Init Fieldset variable
		$fieldset = array();
		
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
						$fieldset[$name] = $this->security->xss_clean($value);
					}
				}
			}else{
				$fieldset[$fieldname] = $fieldvalue;
			}
		}
		
		unset($fieldset['guardar']);
		
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
			"uuid_cliente_potencial = UNHEX('$id_cliente')" => NULL
		);
		
		//Actualizar Cliente Potencial
		$this->db->where($clause)->update('cp_clientes_potenciales', $fieldset);
		
		//---------------------------------------
		//End Transaction
		$this->db->trans_complete();
		
		// Managing Errors
		if ($this->db->trans_status() === FALSE) {
				
			log_message("error", "MODULO: Clientes Potenciales --> No se pudo actualizar los datos del cliente potencial en DB.");
			return false;
				
		} else {
				
			//guardar el id en variable de session
			$this->session->set_userdata('actualizadoClientePotencial', $id_cliente);
			
			//Limpiar cache de clientes potenciales
			CRM_Controller::$cache->delete("contarClientesPotenciales");
			CRM_Controller::$cache->delete("listaClientesPotenciales");
			CRM_Controller::$cache->delete("infoClientePotencial". $id_cliente);
				
			return true;
		}
	}
	
	function generar_csv($id_clientes)
	{
		$id_clientes = (!empty($id_clientes) ? implode(', ', array_map(function($id_clientes){
			return "'".$id_clientes."'";;
		}, $id_clientes)) : "");
		
		$sql = "SELECT DISTINCT  `cp`.`compania` as Compania, CONCAT(`cp`.`nombre`,' ' , `cp`.`apellido`) as Nombre, `cp`.`telefono` AS Telefono, `cp`.`correo` as Correo, `cpcat`.`etiqueta` AS 'Toma de Contacto'   
				FROM (`cp_clientes_potenciales` AS cp) 
				LEFT JOIN `cp_clientes_potenciales_cat` AS cpcat ON `cpcat`.`id_cat` = `cp`.`id_toma_contacto` 
				WHERE HEX(uuid_cliente_potencial) 
				IN(". $id_clientes .")";
		
		$query = $this->db->query($sql);
		return $this->dbutil->csv_from_result($query);
	}
	
	function eliminar()
	{
		$id_clientes = $this->input->post('id_clientes', true);

		//Retorna false si $id_clientes es vacio
		if(empty($id_clientes)){
			return false;
		}
		
		//
		// Begin Transaction
		// docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
		//
		$this->db->trans_start();
		
		//Borrar el cliente seleccionado
		$this->db->where_in("HEX(uuid_cliente_potencial)", $id_clientes)->delete('cp_clientes_potenciales');

		// ---------------------------------------
		// End Transaction
		$this->db->trans_complete();
		
		// Managing Errors
		if ($this->db->trans_status() === FALSE) {
				
			log_message("error", "MODULO: Cliente Potenciales --> No se pudo eliminar cliente potencial en DB.");
				
			return array(
				"respuesta" => false,
				"mensaje" => "Hubo un error al tratar de eliminar ". ( count($id_clientes) > 1 ? "los clientes seleccionados" : "el cliente seleccionado" )
			);
		
		} else {
			
			//Limpiar cache de clientes potenciales
			CRM_Controller::$cache->delete("contarClientesPotenciales");
			CRM_Controller::$cache->delete("listaClientesPotenciales");
			
			return array(
				"respuesta" => true,
				"mensaje" => "Se ha eliminado ". ( count($id_clientes) > 1 ? "los clientes seleccionados satisfactoriamente." : "el cliente seleccionado satisfactoriamente." )
			);
		}
	}
	
	function convertir_juridico()
	{
		$id_clientes = $this->input->post('id_clientes', true);

		//Retorna false si $id_clientes es vacio
		if(empty($id_clientes)){
			return false;
		}
		
		//
		// Begin Transaction
		// docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
		//
		$this->db->trans_start();
		
		foreach($id_clientes AS $id_cliente_potencial)
		{
			//Seleccionar informacion del Cliente Potencial
			$fields = array (
				"cp.nombre",
				"cp.apellido",
				"cp.compania",
				"cp.telefono",
				"cp.correo",
				"cpcat.etiqueta"
			);
			$clause = array(
				"cp.uuid_cliente_potencial = UNHEX('$id_cliente_potencial')" => NULL
			);
			$result = $this->db->select($fields)
					->distinct()
					->from('cp_clientes_potenciales AS cp')
					->join('cp_clientes_potenciales_cat AS cpcat', 'cpcat.id_cat = cp.id_toma_contacto', 'LEFT')
					->where ($clause)
					->get()
					->result_array();
			
			if(empty($result)){
				continue;
			}
			
			$nombre	= !empty($result[0]["nombre"]) ? $result[0]["nombre"] : "";
			$apellido = !empty($result[0]["apellido"]) ? $result[0]["apellido"] : "";
			$compania = !empty($result[0]["compania"]) ? $result[0]["compania"] : "";
			$etiqueta = !empty($result[0]["etiqueta"]) ? $result[0]["etiqueta"] : "";
			$telefono = !empty($result[0]["telefono"]) ? $result[0]["telefono"] : "";
			$correo = !empty($result[0]["correo"]) ? $result[0]["correo"] : "";
			
			//Seleccionar el id_cat de 
			//toma de contactos de cliente
			$clause = array(
				"id_campo" => 12,
				"etiqueta" => $etiqueta
			);
			$result = $this->db->select("id_cat")
					->distinct()
					->from('cl_clientes_cat')
					->where ($clause)
					->get()
					->result_array();
			$id_toma_contacto = !empty($result[0]["id_cat"]) ? $result[0]["id_cat"] : "";
	
			//Guardar Cliente
			$fieldset = array(
				"id_tipo_cliente" => 1,
				"nombre" => $compania,
				"creado_por" => $this->session->userdata('id_usuario'),
				"fecha_creacion" => date('Y-m-d H-i-s')
			);
			$this->db->set('uuid_cliente', 'ORDER_UUID(uuid())', FALSE);
			$this->db->insert('cl_clientes', $fieldset);
			$id_cliente = $this->db->insert_id();
			
			//Seleccionar UUID del Cliente Ingresado
			$result = $this->db->select("uuid_cliente")
					->distinct()
					->from('cl_clientes')
					->where("id_cliente", $id_cliente)
					->get()
					->result_array();
			$uuid_cliente = !empty($result[0]["uuid_cliente"]) ? $result[0]["uuid_cliente"] : "";
			
			//Guardar Demas Informacion Como Contacto
			$fieldset = array(
				"nombre" => $nombre,
				"apellido" => $apellido,
				"telefono" => $telefono,
				"email" => $correo,
				"creado_por" => $this->session->userdata('id_usuario'),
				"uuid_cliente" => $uuid_cliente,
			);
			$this->contactos->comp__guardar_contacto($fieldset);
			
			
			//Eliminar Cliente Potencial
			//Borrar el cliente seleccionado
			$this->db->where_in("HEX(uuid_cliente_potencial)", $id_cliente_potencial)->delete('cp_clientes_potenciales');
		}
		
		//---------------------------------------
		//End Transaction
		$this->db->trans_complete();
			
		// Managing Errors
		if ($this->db->trans_status() === FALSE) {
				
			log_message("error", "MODULO: Clientes Potenciales --> No se pudo exportar los datos del Cliente Potencial hacia juridico en DB.");
		
			return array(
				"respuesta" => false,
				"mensaje" => "Hubo un error al tratar de exportar el cliente potencial."
			);
				
		} else {
		
			//Limpiar cache de clientes potenciales
			CRM_Controller::$cache->delete("contarClientesPotenciales");
			CRM_Controller::$cache->delete("listaClientesPotenciales");
			
			return array(
				"respuesta" => true,
				"mensaje" => "Se ha convertido el Cliente Potencial a Cliente Juridico satisfactoriamente."
			);
		}
	}
	function convertir_natural()
	{
		$id_clientes = $this->input->post('id_clientes', true);
	
		//Retorna false si $id_clientes es vacio
		if(empty($id_clientes)){
			return false;
		}
	
		//
		// Begin Transaction
		// docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
		//
		$this->db->trans_start();
	
		foreach($id_clientes AS $id_cliente_potencial)
		{
			//Seleccionar informacion del Cliente Potencial
			$fields = array (
					"cp.nombre",
					"cp.apellido",
					"cp.compania",
					"cp.telefono",
					"cp.correo"
			);
			$clause = array(
					"cp.uuid_cliente_potencial = UNHEX('$id_cliente_potencial')" => NULL
			);
			$result = $this->db->select($fields)
			->distinct()
			->from('cp_clientes_potenciales AS cp')
			->where ($clause)
			->get()
			->result_array();
				
			if(empty($result)){
				continue;
			}
				
			$nombre	= !empty($result[0]["nombre"]) ? $result[0]["nombre"] : "";
			$apellido = !empty($result[0]["apellido"]) ? $result[0]["apellido"] : "";
			//$compania = !empty($result[0]["compania"]) ? $result[0]["compania"] : "";
			$etiqueta = !empty($result[0]["etiqueta"]) ? $result[0]["etiqueta"] : "";
			$telefono = !empty($result[0]["telefono"]) ? $result[0]["telefono"] : "";
			$correo = !empty($result[0]["correo"]) ? $result[0]["correo"] : "";
				
			//Seleccionar el id_cat de
			//toma de contactos de cliente
			$clause = array(
					"id_campo" => 12,
					"etiqueta" => $etiqueta
			);
			$result = $this->db->select("id_cat")
			->distinct()
			->from('cl_clientes_cat')
			->where ($clause)
			->get()
			->result_array();
			$id_toma_contacto = !empty($result[0]["id_cat"]) ? $result[0]["id_cat"] : "";
	
			//Guardar Cliente Potencial
			$fieldset = array(
					"id_tipo_cliente" => 78,
					"nombre" => $nombre,
					"creado_por" => $this->session->userdata('id_usuario'),
					"fecha_creacion" => date('Y-m-d H-i-s')
			);
			$this->db->set('uuid_cliente', 'ORDER_UUID(uuid())', FALSE);
			$this->db->insert('cl_clientes', $fieldset);
			$id_cliente = $this->db->insert_id();
				
			//Seleccionar UUID del Cliente Ingresado
			$result = $this->db->select("uuid_cliente")
			->distinct()
			->from('cl_clientes')
			->where("id_cliente", $id_cliente)
			->get()
			->result_array();
			$uuid_cliente = !empty($result[0]["uuid_cliente"]) ? $result[0]["uuid_cliente"] : "";
				
			//Guardando Correos del Cliente
			$fieldset = array(
  					"correo" => $correo,
 					"uuid_cliente" => $uuid_cliente,
			);
			$this->db->insert('cl_cliente_correos', $fieldset);

			//Guardando Telefonos del Cliente
			$fieldset = array(
					"no_telefono" => $telefono,
					"uuid_cliente" => $uuid_cliente,
			);
			$this->db->insert('cl_cliente_telefonos', $fieldset);
			
				
			//Eliminar Cliente Potencial
			//Borrar el cliente seleccionado
			$this->db->where_in("HEX(uuid_cliente_potencial)", $id_cliente_potencial)->delete('cp_clientes_potenciales');
		}
	
		//---------------------------------------
		//End Transaction
		$this->db->trans_complete();
			
		// Managing Errors
		if ($this->db->trans_status() === FALSE) {
	
			log_message("error", "MODULO: Clientes Potenciales --> No se pudo exportar los datos del Cliente Potencial hacia Natural en DB.");
	
			return array(
					"respuesta" => false,
					"mensaje" => "Hubo un error al tratar de exportar el cliente potencial."
			);
	
		} else {
	
			//Limpiar cache de clientes potenciales
			CRM_Controller::$cache->delete("contarClientesPotenciales");
			CRM_Controller::$cache->delete("listaClientesPotenciales");
			
			return array(
					"respuesta" => true,
					"mensaje" => "Se ha convertido el Cliente Potencial a Cliente Natural satisfactoriamente."
			);
		}
	}
	
}
?>
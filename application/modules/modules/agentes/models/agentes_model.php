<?php
class Agentes_model extends CI_Model 
{
	public function __construct() {
		parent::__construct ();

	}
	
	/**
	 * Conteo de los roles existentes
	 *
	 * @return [array] [description]
	 */
	function contar_agentes($clause)
	{
		$fields = array (
			"agt.id_agente"
		);
		$result = $this->db->select($fields)
			->distinct()
			->from('agt_agentes AS agt')
			->where($clause)
			->get()
			->result_array();
		return $result;
	}
	
	/**
	 * Listar agentes
	 *
	 * @param integer $sidx [description]
	 * @param integer $sord [description]
	 * @param integer $limit [description]
	 * @param integer $start [description]
	 * @return [array] [description]
	 */
	function listar_agentes($clause, $sidx = 1, $sord = 1, $limit = 0, $start = 0)
	{
		$fields = array (
			"agt.id_agente",
			"HEX(agt.uuid_agente) AS uuid_agente",
			"CONCAT_WS(' ', IF(agt.nombre != '', agt.nombre, ''), IF(agt.apellido != '', agt.apellido, '')) AS nombre_agente",
			"agt.cedula",
			"agt.telefono",
			"agt.correo",
			"agt.porcentaje_participacion",
			"agt.fecha_creacion",
		);
		$result = $this->db->select($fields)
			->distinct()
			->from('agt_agentes AS agt')
			->where ($clause)
			->order_by($sidx, $sord)
			->limit($limit, $start)
			->get()
			->result_array();
		return $result;
	}
	
	/**
	 * Guardar Formulario de Cliente Juridico
	 *
	 * @return boolean
	 */
	function guardar_agente()
	{
		if(Util::is_array_empty($_POST)){
			return false;
		}
	
		//Init Fieldset variable
		$fieldset = array();
	
		//Remover el boton de submit que por default
		//viene con el valor "Guardar"
		unset($_POST["campo"]["guardarFormBtn"]);
	
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
						if(preg_match("/uuid_/i", $name)){
							$fieldset["$name = UNHEX('$value')"] = NULL;
								
						}
						else if(preg_match("/fecha/i", $name)){
							//Darle mformato a la fecha
							$fieldset[$name] = date("Y-m-d", strtotime($value));
						}
						else{
							$fieldset[$name] = $this->security->xss_clean($value);
						}
					}
				}
			}else{
	
				if(preg_match("/uuid_/i", $fieldname)){
						
					$this->db->set($fieldname, "UNHEX('$fieldvalue')", FALSE);
				}
				else if(preg_match("/fecha/i", $fieldname)){
					//Darle mformato a la fecha
					$fieldset[$fieldname] = date("Y-m-d", strtotime($fieldvalue));
				}
				else{
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
		$this->db->set('uuid_agente', 'ORDER_UUID(uuid())', FALSE);
		$fieldset["creado_por"] = $this->session->userdata('id_usuario');
		$fieldset["fecha_creacion"] = date('Y-m-d H-i-s');
	
		//Guardar Cliente
		$this->db->insert('agt_agentes', $fieldset);
		$idagente = $this->db->insert_id();

		//---------------------------------------
		//End Transaction
		$this->db->trans_complete();
	
		// Managing Errors
		if ($this->db->trans_status() === FALSE) {
	
			log_message("error", "MODULO: agentes --> No se pudo guadar los datos del agente en DB.");
			return false;
	
		} else {
	
			//guardar el id en variable de session
			$this->session->set_userdata('idAgente', $idagente);
				
			return true;
		}
	}
	
	/**
	 * Seleccionar informacion de la agente
	 * para rellenar valores del formulario de
	 * Editar agente.
	 *
	 * @param string $id_agente
	 * @return boolean|multitype:string unknown
	 */
	function seleccionar_informacion_agente($id_agente=NULL)
	{
		if(empty($id_agente)){
			return false;
		}
	
		$fields = array(
			"agt.id_agente ",
			"HEX(agt.uuid_agente) AS uuid_agente",
			"agt.nombre",
			"agt.apellido",
			"agt.cedula",
			"agt.telefono",
			"agt.correo",
			"agt.porcentaje_participacion",
		);
		$clause = array(
			"uuid_agente = UNHEX('$id_agente')" => NULL
		);
		$result = $this->db->select($fields)
			->distinct()
			->from('agt_agentes AS agt')
			->where ($clause)
			->get()
			->result_array();
		
		return $result;
	}
	
	/**
	 * Actualizar Datos de una Oportunidad
	 *
	 * @param string $id_cliente
	 * @return boolean
	 */
	function actualizar_agente($id_agente=NULL)
	{
		if(Util::is_array_empty($_POST) || $id_agente==NULL){
			return false;
		}
	
		//Init Fieldset variable
		$fieldset = array();
	
		/*echo "<pre>";
		print_r($_POST);
		echo "</pre>";
		die();*/
	
		//Remover el boton de submit que por default
		//viene con el valor "Guardar"
		unset($_POST["campo"]["guardarFormBtn"]);
	
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
							if(preg_match("/uuid_/i", $name)){
								$fieldset["$name = UNHEX('$value')"] = NULL;
	
							}
							else if(preg_match("/fecha/i", $name)){
								//Darle mformato a la fecha
								$fieldset[$name] = date("Y-m-d", strtotime($value));
							}
							else{
								$fieldset[$name] = $this->security->xss_clean($value);
							}
						}
					}
				}else{
						
					if(preg_match("/uuid_/i", $fieldname)){
	
						$this->db->set($fieldname, "UNHEX('$fieldvalue')", FALSE);
					}
					else if(preg_match("/fecha/i", $fieldname)){
						//Darle mformato a la fecha
						$fieldset[$fieldname] = date("Y-m-d", strtotime($fieldvalue));
					}
					else{
						$fieldset[$fieldname] = $fieldvalue;
					}
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
	
		$clause = array(
			"uuid_agente = UNHEX('$id_agente')" => NULL
		);
	
		//Actualizar Cliente Potencial
		$this->db->where($clause)->update('agt_agentes', $fieldset);

		//---------------------------------------
		//End Transaction
		$this->db->trans_complete();
	
		// Managing Errors
		if ($this->db->trans_status() === FALSE) {
	
			log_message("error", "MODULO: Agentes --> No se pudo actualizar los datos de la oportunidad en DB.");
			return false;
	
		} else {
	
			//guardar el id en variable de session
			$this->session->set_userdata('updatedAgente', $id_agente);
	
			//Limpiar cache de oportunidades
			CRM_Controller::$cache->delete("infoAgente". $id_agente);
	
			return true;
		}
	}
	
}
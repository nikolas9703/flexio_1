<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon;

class Roles_model extends CI_Model
{
	// Role Info
	private $role_id;
	private $role_name;
	private $role_description;
	private $role_status;

	// Permission Info
	private $permission_id;
	private $permission_name;
	private $resource_id;
	private $role_permission_id;

	private $empresa_id;

	public function __construct() {
		parent::__construct ();

		$this->load->model('usuarios/empresa_orm');

		$uuid_empresa = $this->session->userdata('uuid_empresa');

		if($uuid_empresa != ""){
			$empresa = Empresa_orm::findByUuid($uuid_empresa);
			$this->empresa_id = $empresa->id;
		}
	}

	/**
	 * Conteo de los roles existentes
	 *
	 * @return [array] [description]
	 */
	function contar_roles($clause)
	{
		$fields = array (
			"id"
		);
		$result = $this->db->select($fields)
				->distinct()
				->from('roles')
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
	function listar_roles($clause, $sidx = 1, $sord = 1, $limit = 0, $start = 0)
	{
		$fields = array (
			"id",
			"nombre",
			"descripcion",
			"estado"
		);
		$result = $this->db->select($fields)
				->distinct()->from('roles')
				->where ($clause)
				->order_by($sidx, $sord)
				->limit($limit, $start)
				->get()
				->result_array();
		return $result;
	}

	public function seleccionar_rol($id_rol = NULL)
	{
		if ($id_rol == NULL) {
			return false;
		}

		// Obtener Informacion del Rol (nombre rol, descripcion, status)
		$fields = array (
			"id",
			"nombre",
			"descripcion",
			"estado"
		);
		$clause = array (
			"id" => $id_rol
		);
		$rol = $this->db->select($fields)
				->distinct()
				->from('roles')
				->where($clause)
				->where($clause)
				->get()
				->result_array();

		$result = array ();
		if(!empty($rol))
		{
			$result["id"] 			= $rol[0]["id"];
			$result["nombre"] 		= $rol[0]["nombre"];
			$result["descripcion"] 	= $rol[0]["descripcion"];
			$result["estado"] 		= $rol[0]["estado"];

			// Obtener los modulos, recursos y permisos,
			// a los que tiene acceso este rol.
			$fields = array (
				"mods.id",
				"mods.controlador"
			);
			$clause = array (
				"rop.rol_id" => $id_rol
			);
			$modulos = $this->db->select( $fields )
					->distinct ()
					->from('roles_permisos AS rop')
					->join('permisos AS perm', 'perm.id = rop.permiso_id', 'LEFT')
					->join('recursos AS rec', 'rec.id = perm.recurso_id', 'LEFT')
					->join('modulos AS mods', 'mods.id = rec.modulo_id', 'LEFT')
					->where($clause)
					->get()
					->result_array();

			if(!empty($modulos))
			{
				$i = 0;
				foreach($modulos as $modulo)
				{
					// Obtener los recursos del modulo
					$fields = array(
						"rec.id",
						"rec.nombre",
						"perm.recurso_id"
					);
					$clause = array (
						"rec.modulo_id" => $modulo["id"]
					);
					$recursos = $this->db->select($fields)
							->distinct()
							->from('roles_permisos AS rop')
							->join('permisos AS perm', 'perm.id = rop.permiso_id', 'LEFT')
							->join('recursos AS rec', 'rec.id = perm.recurso_id', 'LEFT')
							->where($clause )
							->get()
							->result_array();

					if(!empty($recursos))
					{
						$j=0;
						foreach($recursos AS $recurso)
						{
							// Obtener los permisos del recurso
							$fields = array (
								"perm.id",
								"perm.nombre"
							);
							$clause = array (
								"perm.recurso_id" => $recurso["recurso_id"],
								"rop.rol_id" => $id_rol
							);
							$permisos = $this->db->select($fields)
									->distinct()
									->from('roles_permisos AS rop')
									->join('permisos AS perm', 'perm.id = rop.permiso_id', 'LEFT')
									->where($clause )
									->get()
									->result_array();

							if (!empty($permisos)){
								$k = 0;
								foreach($permisos as $permiso){
									if ($recurso["nombre"] != "") {
										$result ["modulos"][$i][$modulo["controlador"]]["resources"][$recurso ["nombre"]][$k] = $permiso["nombre"];
									}
									$k ++;
								}
							}
							$j ++;
						}
					}
					$i ++;
				}
			}
		}

		return $result;
	}


    function guardar_permisos()
	{
		$this->role_id = $this->input->post('role_id', true);

		if(empty($this->role_id)){
			log_message("error", "MODULO: Roles --> El rol_id se envio sin valor.");
			return false;
		}

		//
		// Begin Transaction
		// docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
		//
		$this->db->trans_start ();

		if (! empty( $_POST['modulo'] ))
		{
                    foreach( $_POST['modulo'] AS $modulo_id => $module )
                    {
                        foreach ($module['recurso'] AS $recurso_id => $permisos )
                        {
                            foreach ($permisos as $nombre_permiso => $status)
                            {




                                //Guardar solo si no existe el permiso
                                $fields = array(
                                    "perm.id"
                                );
                                $clause = array(
                                    'rop.rol_id'    => $this->role_id,
                                    'perm.nombre'   => $nombre_permiso,
                                    'perm.recurso_id'   => $recurso_id
                                );


                                $checkPermiso   = $this->db->select($fields)
                                                ->distinct()
                                                ->from('permisos AS perm')
                                                ->join('roles_permisos AS rop', 'rop.permiso_id = perm.id', 'LEFT')
                                                ->where($clause)
                                                ->get()
                                                ->result_array();
                                if(empty($checkPermiso))
                                {
                                    //Obtener el id del permiso
                                    $clause = array (
                                        'nombre' => $nombre_permiso,
                                        'recurso_id' => $recurso_id
                                    );

                                    $permisoINFO    = $this->db->select("id")
                                                    ->distinct()
                                                    ->from('permisos')
                                                    ->where($clause)
                                                    ->get()
                                                    ->result_array();

                                    $id_permiso = !empty($permisoINFO) ? $permisoINFO[0]["id"] : "";

                                    if(empty($id_permiso) || $id_permiso == 0){
                                        continue;
                                    }

                                    // Asignarle este permiso al rol creado.
                                    $fieldset = array (
                                        'rol_id' => $this->role_id,
                                        'permiso_id' => $id_permiso
                                    );

                                    $this->db->insert('roles_permisos', $fieldset);
                                }//check permiso
                            }//permisos
                        }//recursos
                    }//modulos
		}//post
		//die;
		// ---------------------------------------
		// End Transaction
		$this->db->trans_complete();

		// Managing Errors
		if ($this->db->trans_status() === FALSE) {

			log_message("error", "MODULO: Roles --> No se guardo alguno de los query a tabla de permisos o roles_permisos.");
			return false;

		} else {

			// guardar el id de rol en variable de session
			$this->session->set_userdata('permisos_creados', $this->role_id);
			return true;
		}
	}

	public function eliminar_permiso()
	{
		$this->role_id = $this->input->post ('id_rol', true);

 		if (!empty($_POST['modulo']))
 		{
 			//
			// Begin Transaction
			// docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
			//
			$this->db->trans_start ();

 			foreach($_POST['modulo'] as $modulo_id => $modulo)
 			{
				foreach ($modulo['recurso'] as $recurso_id => $permisos)
				{
					foreach($permisos as $nombre_permiso => $status)
					{
						// Verificar si en realidad existe este permiso
						$fields = array (
							"perm.id AS permiso_id",
							"rop.id AS rol_id"
						);
						$clause = array (
							'rop.rol_id' => $this->role_id,
							'perm.nombre' => $nombre_permiso,
							'perm.recurso_id' => $recurso_id
						);
						$checkPermiso = $this->db->select($fields)
								->distinct()
								->from('permisos AS perm')
								->join('roles_permisos AS rop', 'rop.permiso_id = perm.id', 'LEFT')
								->where($clause )
								->get()
								->result_array();

 						if(!empty($checkPermiso))
 						{
 							//Borrar relacion (permiso - rol)
							$clause = array (
								"id" => $checkPermiso[0]['rol_id']
							);
							$this->db->where($clause)->delete('roles_permisos');
  						}
					}
				}
			}

			// ---------------------------------------
			// End Transaction
			$this->db->trans_complete ();

			// Managing Errors
			if ($this->db->trans_status () === FALSE) {
				return false;
			} else {
				return true;
			}
		}
	}

	public function duplicar_rol()
	{
		$rol_id = $this->input->post ('rol_id', true);

		//Retorna false si el nombre es vacio
		if(empty($rol_id)){
			return false;
		}

		//Informacion del Rol
		$rol = $this->db->select()
				->distinct()
				->from("roles")
				->where("id", $rol_id)
				->get()
				->result_array();

		$nombre 	= Util::verificar_valor($rol[0]["nombre"]);
		$empresa_id = Util::verificar_valor($rol[0]["empresa_id"]);
		$descripcion = Util::verificar_valor($rol[0]["descripcion"]);
		$superuser 	= Util::verificar_valor($rol[0]["superuser"]);
		$default 	= Util::verificar_valor($rol[0]["default"]);
		$estado 	= Util::verificar_valor($rol[0]["estado"]);
		$link 		= Util::verificar_valor($rol[0]["link_inicial"]);
		$default 	= Util::verificar_valor($rol[0]["default"]);

		$fieldset = array (
			'empresa_id' => $empresa_id,
			'nombre' => $nombre,
			'descripcion' => $descripcion,
			'superuser' => $superuser,
			'default' => $default,
			'estado' => $estado,
			'link_inicial' => $link,
			'created_at' => date('Y-m-d h:i:s'),
			'updated_at' => date('Y-m-d h:i:s')
		);

		//
		// Begin Transaction
		// docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
		//
		$this->db->trans_start();

		// Insertar role
		$this->db->insert('roles', $fieldset);
		$this->role_id = $this->db->insert_id();


		//Verificar si este rol tiene creado permisos
		//Primero verificamos si el rol existe
		$permisos = $this->db->select("permiso_id")
				->distinct()
				->from("roles_permisos")
				->where("rol_id", $rol_id)
				->get()
				->result_array();

		if(!empty($permisos))
		{
			foreach($permisos AS $permiso)
			{
				// Insertar permiso
				$fieldset = array (
					'rol_id' => $this->role_id,
					'permiso_id' => $permiso["permiso_id"]
				);
				$this->db->insert('roles_permisos', $fieldset);
			}
		}

		//Guardar relacion Empresa/Rol
		// Insertar permiso
		$fieldset = array (
			'rol_id' => $this->role_id,
			'empresa_id' => $this->empresa_id
		);
		$this->db->insert('empresas_has_roles', $fieldset);

		// ---------------------------------------
		// End Transaction
		$this->db->trans_complete();

		// Managing Errors
		if ($this->db->trans_status() === FALSE) {

			log_message("error", "MODULO: Roles --> No se pudo duplicar los datos del rol en DB.");

			return array(
				"respuesta" => false,
				"mensaje" => "Hubo un error al tratar de duplicar el rol seleccionado."
			);

		}else {

			return array(
				"respuesta" => true,
				"mensaje" => "El rol se ha duplicado satisfactoriamente."
			);
		}
	}

	public function activar_desactivar_rol()
	{
		$rol_id = $this->input->post ('rol_id', true);
		$estado = $this->input->post ('estado', true);
		$estado_mensaje = $this->input->post ('estado_mensaje', true);

		//Retorna false si el nombre es vacio
		if(empty($rol_id)){
			return false;
		}

		// PRIMERO
		// Verificar si un usuario tiene este rol
		// asignado, de lo contrario no se podra
		// desactivar este rol.
		$verificar_rol_usuario = $this->db->select()
				->distinct()
				->from("usuarios_has_roles")
				->where("role_id", $rol_id)
				->get()
				->result_array();

		if(!empty($verificar_rol_usuario) && $estado==0){
			return array(
				"respuesta" => false,
				"mensaje" => "No es posible desactivar este rol. Hay usuarios que tienen este rol asignado."
			);
		}

		//
		// Begin Transaction
		// docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
		//
		$this->db->trans_start();

		//Guardar datos generales de la Orden
		$fieldset = array(
			'estado' => ($estado==0 ? 0 : 1)
		);
		$clause = array(
			"id" => $rol_id
		);
		$this->db->where($clause)->update('roles', $fieldset);

		// ---------------------------------------
		// End Transaction
		$this->db->trans_complete();

		// Managing Errors
		if ($this->db->trans_status() === FALSE) {

			log_message("error", "MODULO: Roles --> No se pudo activar/desactivar el rol en DB.");

			return array(
				"respuesta" => false,
				"mensaje" => "Hubo un error al tratar de duplicar el rol seleccionado."
			);

		} else {
			return array(
				"respuesta" => true,
				"mensaje" => "Se ha ". $estado_mensaje ." el rol satisfactoriamente."
			);
		}
	}

	public function seleccionar_roles($clause = array()) {
		$fields = array (
			"id_rol",
			"nombre_rol"
		);
		$result = $this->db->select($fields)
				->distinct()->from ('roles')
				->order_by('nombre_rol', 'ASC')
				->where($clause)
				->get()
				->result_array();

		return $result;
	}
}
?>

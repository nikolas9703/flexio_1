<?php
class Usuarios_model extends CI_Model
{
  protected $id_usuario;
  protected $id_rol;
  protected $nombre;
  protected $apellido;
  protected $correo;
  protected $usuario;
  protected $password;

  protected $id_categoria;
  protected $ch_comision;
  protected $comision;
  protected $reporta_usuario;
  protected $reporta_rol;

  protected $long_minima_usuario;
  protected $uso_correo;
  protected $long_maxima_usuario;
  protected $editar_perfil;

  protected $id_configuracion;
  protected $long_minima_contrasena;
  protected $expira_despues_dias;
  protected $notificacion_usuarios_expiracion;
  protected $configuracion_avanzada;
  protected $minima_cantidad_letras;
  protected $restringir_contrasena_vieja;
  protected $minima_cantidad_numeros;
  protected $minima_cantidad_caracteres;
  //protected $cambiar_contrasena_login;
  //protected $cambiar_contrasena;
  protected $contr_notificar_antes_dias;


   public function __construct()
  {
    parent::__construct();
  }

  function contar_usuarios($clause)
  {
      $fields = array(
        "usr.id_usuario"
      );
      $result = $this->db->select($fields)
            ->distinct()
            ->from('usuarios AS usr')
            ->join('usuario_rol AS usrol', 'usrol.id_usuario = usr.id_usuario', 'LEFT')
            ->join('roles AS rol', 'rol.id_rol = usrol.id_rol', 'LEFT')
            ->where($clause)
            ->group_by("usr.id_usuario")
            ->get()
            ->result_array();

      return $result;
  }

  function listar_usuarios($clause, $sidx=1, $sord=1, $limit=0, $start=0)
  {
      $fields = array(
        "usr.id_usuario",
        "usr.nombre",
        "usr.apellido",
        "usr.ch_comision",
        "usr.comision",
        "usr.reporta_rol",
        "usr.reporta_usuario",
        "usr.id_categoria",
      	"HEX(uuid_usuario) AS uuid_usuario",
      	"HEX(uuid_categoria) AS uuid_categoria",
        "usr.usuario",
      	"usr.telefono",
        "usr.email",
        "usr.status",
        "rol.nombre_rol"
      );
      $result = $this->db->select($fields)
            ->distinct()
            ->from('usuarios AS usr')
            ->join('usuario_rol AS usrol', 'usrol.id_usuario = usr.id_usuario', 'LEFT')
            ->join('roles AS rol', 'rol.id_rol = usrol.id_rol', 'LEFT')
            ->where($clause)
            ->order_by($sidx, $sord)
            ->group_by("usr.id_usuario")
            ->limit($limit, $start)
            ->get()
            ->result_array();
       return $result;
  }


  /*
   * Seleccionar informacion de politicas de contraseña
  */
  public function seleccionar_politicas($clause = array()) {
  	$fields = array (
  			'*'
  	);
  	$result = $this->db->select($fields)
  	->distinct()->from ('configuracion_sistema')
   	->get()
  	->result_array();

  	if(!empty($result))
  	{



  		$result['usuario']['long_minima_usuario'] = $result[0]['usu_long_minima_usuario'];
  		$result['usuario']['long_maxima_usuario'] = $result[0]['usu_long_maxima_usuario'];
  		$result['usuario']['uso_correo']   		  = $result[0]['usu_uso_correo'];
  		$result['usuario']['editar_perfil']    	  = $result[0]['usu_editar_perfil'];

  		$result['contrasena']['long_minima_contrasena']   	= $result[0]['contr_long_minima_contrasena'];
  		$result['contrasena']['expira_despues_dias']   		=$result[0]['contr_expira_despues_dias'];
  		$result['contrasena']['contr_notificar_antes_dias']   		=$result[0]['contr_notificar_antes_dias'];
  		$result['contrasena']['configuracion_avanzada'] 	= $result[0]['contr_configuracion_avanzada'];
  		$result['contrasena']['notificacion_usuarios_expiracion']  	= $result[0]['contr_notificacion_usuarios_expiracion'];
   		$result['contrasena']['minima_cantidad_letras']  	= $result[0]['contr_minima_cantidad_letras'];
  		$result['contrasena']['minima_cantidad_numeros']  	= $result[0]['contr_minima_cantidad_numeros'];
  		$result['contrasena']['minima_cantidad_caracteres'] = $result[0]['contr_minima_cantidad_caracteres'];
  		$result['contrasena']['restringir_contrasena_vieja']= $result[0]['contr_restringir_contrasena_vieja'];
  		//$result['contrasena']['cambiar_contrasena_login']  	= $result[0]['contr_cambiar_contrasena_login'];
  		//$result['contrasena']['cambiar_contrasena']  		= $result[0]['contr_cambiar_contrasena'];

  	}
  	return $result;
  }



  /*
   * Seleccionar roles que sean distinto al admin
  */
  public function seleccionar_roles($clause = array()) {
  	$fields = array (
  			"id",
  			"nombre"
  	);
  	$result = $this->db->select($fields)
  	->distinct()->from ('roles')
  	->order_by('nombre', 'ASC')
  	->where($clause)
  	->get()
  	->result_array();

  	return $result;
  }

  /*
   * Seleccionar roles que sean distinto al admin
  */
  public function seleccionar_categoria() {
  	$fields = array (
  			"id_categoria",
   			"HEX(uuid_categoria) AS uuid_categoria",
  			"nombre"
  	);
  	$result = $this->db->select($fields)
  	->distinct()->from ('usuarios_categoria')
  	->order_by('nombre', 'ASC')
  	->get()
  	->result_array();

  	return $result;
  }

  /*
   * Seleccionar roles via Variable
  */
  public function seleccionar_roles_usuario_byId($id = NULL) {

  	 //'<a href="' . base_url ( "usuarios/ver-usuario-admin/" . $row ['id_usuario'] ) . '" >Ver Usuario</a>'
  	$fields = array (
  			"ur.id","ur.id_usuario","ur.id_rol","r.*"
  	);
  	$clause = array (
  			"ur.id_usuario"=>$id
  	);

   	$result =  $this->db->select($fields)
  	->from('usuario_rol ur' )
     ->join('roles r', 'r.id_rol = ur.id_rol', 'LEFT OUTER')
   	->where($clause)
  	->get()
  	->result_array();
   	$return = '';
   	foreach($result as $value){
   		//$return .= $value['nombre_rol'].',';  //El Original
  		$return .= '<a href="' . base_url ( "roles/editar-permisos/" . $value['id_rol'] ) . '" >'.$value['nombre_rol'].'</a>'.',';
   	}

   	return array(trim($return, ','), $result);
  }
  /*
   * Seleccionar roles x usuario
  */
  public function seleccionar_roles_usuario() {

  	$this->id_usuario 	= $this->input->post ('id_usuario', true);
  	$fields = array (
  			"*"
  	);
  	$clause = array (
  			"ur.id_usuario"=>$this->id_usuario
  	);

  	$result =  $this->db->select($fields)
  	->from('usuario_rol ur' )
  	->join('roles r', 'r.id_rol = ur.id_rol', 'LEFT OUTER')
  	->where($clause)
  	->get()
  	->result_array();

  	$i = 0;
  	foreach($result as $value){

  		$return[$i]['id'] = $value['id_rol'];
  		$return[$i]['nombre'] =$value['nombre_rol'];
  		++$i;
  	}


  	return $return;
  }

  /*
   * Enviarle correo de bienvenida al usuario
  */
  public function correo_bienvenida()
  {

  	if(empty($this->id_usuario)){
  		return false;
  	}

   	//Encriptar el texto
  	$this->encrypt->set_cipher(MCRYPT_RIJNDAEL_128);

  	//Leer archivo html que contiene el texto del correo
  	$filepath = realpath('./application/modules/usuarios/emails/bienvenida.html');

  	$htmlmail = read_file($filepath);
  	$htmlmail = str_replace("{usuario}", $this->usuario, $htmlmail); //
  	$htmlmail = str_replace("{contrasena}",  $this->password , $htmlmail);  // Listo
  	$htmlmail = str_replace("{link_site}",  base_url("/") , $htmlmail);  // Listo
  	$htmlmail = str_replace("__YEAR__", date('Y'), $htmlmail);

  	//Titulo en el correo
  	$titulo = "Bienvenidos a Nuestro Sitio";

  	//Enviar el correo
  	$this->email->from('no-reply@erp.com', 'CRM Base');
  	$this->email->to($this->correo);
  	$this->email->subject($titulo);
  	$this->email->message($htmlmail);

  	if( $this->email->send()){

  		return true;
  	}else{

  		return false;
  	}
  }
	function verficar_usuario()
	{
		$usuario = $this->input->post ('usuario', true);

		//Retorna false si el nombre es vacio
		if(empty($usuario)){
			return false;
		}

		//Verificar si el usuario ya existe
		$clause = array(
			"usuario" => $usuario
		);
		$check = $this->db->select("id_usuario")
				->distinct()
				->from('usuarios')
				->where($clause)
				->get()
				->result_array();

		return !empty($check) ? false : true;
	}

	function crear_usuario()
	{

 		$politicas = array();
		$politicas = $this->seleccionar_politicas ();


		$this->correo 			= $this->input->post ('email', true);
		$this->usuario 			= $this->input->post ('usuario', true);
		$id_roles 				= $this->input->post ('id_roles', true );
		$this->id_categoria		= $this->input->post ('id_categoria', true );
		$this->ch_comision		= ( isset( $_POST['ch_comision'] ) &&  $_POST['ch_comision'] == 'on' )?1:0;
		$this->comision 		= (  isset( $_POST['ch_comision'] ) &&  $_POST['ch_comision'] == 'on' )?$this->input->post ('comision', true ):0;
		$this->reporta_usuario	= $this->input->post ('reporta_usuario', true );
		$this->reporta_rol		= $this->input->post ('reporta_rol', true );
 		$enviar_correo 			= 'on';
  		$rand_password = substr(md5(microtime()),rand(0,26),5);
		$this->password = $rand_password;
 		$uuid_categoria = $_POST['id_categoria'];
 		//Retorna false si el nombre es vacio
		if(empty($this->correo) && !empty($id_roles)){
			return array(
				"respuesta" => false,
				"mensaje" => "Usted debe llenar todos los campos."
			);
		}

		$fieldset = array (
			'usuario' 	=> $this->usuario,
			'email' 	=> $this->correo,
			'id_categoria' 	=> $this->id_categoria,
			'ch_comision' 	=> $this->ch_comision,
			'comision' 	=> $this->comision,
			'reporta_rol' 	=> $this->reporta_rol,
			'reporta_usuario' 	=> $this->reporta_usuario,
			'fecha_cracion' 	=> date("Y-m-d H:m:s"),
			'recovery_time' 	=> date("Y-m-d H:m:s"),
			'last_recovery_time' 	=> date("Y-m-d H:m:s"),
			'imagen_archivo' => 'usuarios/3812421761430576231.png',
			'status' 	=> 'Pendiente',
			'password' 	=> $this->encrypt->encode($rand_password),
 		);

		$this->db->set("uuid_categoria", "UNHEX('$uuid_categoria')", FALSE);

		// Begin Transaction
		// docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
		//
		$this->db->trans_start();

		// Insertar usuario
		$this->db->set('uuid_usuario', 'ORDER_UUID(uuid())', FALSE);
		$this->db->insert('usuarios', $fieldset);
		$this->id_usuario = $this->db->insert_id();


		$fieldset = array (
				'usuario' 	=> $this->usuario,
				'email' 	=> $this->correo,
				'id_categoria' 	=> $this->id_categoria,
				'ch_comision' 	=> $this->ch_comision,
				'comision' 	=> $this->comision,
				'reporta_rol' 	=> $this->reporta_rol,
				'reporta_usuario' 	=> $this->reporta_usuario,
				'fecha_cracion' 	=> date("Y-m-d H:m:s"),
				'recovery_time' 	=> date("Y-m-d H:m:s"),
				'last_recovery_time' 	=> date("Y-m-d H:m:s"),
				'imagen_archivo' => 'usuarios/3812421761430576231.png',
				'status' 	=> 'Pendiente',
				'password' 	=> $this->encrypt->encode($rand_password),
		);


 		//Guardar Agencias, Departamentos y Celulas Relacionadas
		if(!empty($_POST['id_roles']))
		{
			//Recorrer arreglo de agencias
			foreach ($_POST['id_roles'] AS $rol)
			{

				if($rol == ""){
					continue;
				}

				//Guardar Agencia
				$fieldset = array(
						'id_rol'  => $rol,
						'id_usuario'  => $this->id_usuario
				);
				$this->db->set('uuid_usuario_rol', 'ORDER_UUID(uuid())', FALSE);
				$this->db->insert('usuario_rol', $fieldset);
			}
		}

		// ---------------------------------------
		// End Transaction
		$this->db->trans_complete();

		// Managing Errors
		if ($this->db->trans_status() === FALSE) {

			log_message("error", "MODULO: Usuarios --> No se pudo crear el usuario en DB.");
			return array(
				"respuesta" => false,
				"mensaje" => "Hubo un error al tratar de guardar el usuario."
			);

		}else {
			//Al concluir todo, se debe enviar un correo siempre y cuando tenga activado el checkbox
			if($enviar_correo=='on')
				$this->correo_bienvenida(); //Validar

			return array(
				"respuesta" => true,
				"mensaje" => "El usuario se ha creado satisfactoriamente."
			);
		}
	}
	function editar_usuario()
	{
  		$politicas = array();
		$politicas = $this->seleccionar_politicas ();


		//$this->correo 	= $this->input->post ('email', true);
		$this->id_usuario 	= $this->input->post ('id_usuario', true);
		$id_roles 		= $this->input->post ('id_roles', true );
		$this->id_categoria		= $this->input->post ('id_categoria', true );
		$this->ch_comision		= ( isset( $_POST['ch_comision'] ) &&  $_POST['ch_comision'] == 'on' )?1:0;
		$this->comision 		= (  isset( $_POST['ch_comision'] ) &&  $_POST['ch_comision'] == 'on' )?$this->input->post ('comision', true ):0;
		$this->reporta_usuario	= $this->input->post ('reporta_usuario', true );
		$this->reporta_rol		= $this->input->post ('reporta_rol', true );
		$uuid_categoria = $_POST['id_categoria'];
		//$enviar_correo 	= 'on';

 		$this->db->trans_start();


 		$fieldset = array (
  				'id_categoria' 	=> $this->id_categoria,
 				'ch_comision' 	=> $this->ch_comision,
 				'comision' 	=> $this->comision,
 				'reporta_rol' 	=> $this->reporta_rol,
 				'reporta_usuario' 	=> $this->reporta_usuario,
  		);

 		$this->db->set("uuid_categoria", "UNHEX('$uuid_categoria')", FALSE);

 		$this->db->where('id_usuario', $this->id_usuario);
 		$this->db->update('usuarios', $fieldset);


 		$this->db->where('id_usuario', $this->id_usuario);
 		$this->db->delete('usuario_rol');
		//Update Roles
		if(!empty($_POST['id_roles']))
		{

 			//Recorrer arreglo de agencias
			foreach ($_POST['id_roles'] AS $rol)
			{

				if($rol == ""){
					continue;
				}

				//Actualizar Roles
				 $fieldset = array(
						'id_rol'  => $rol,
						'id_usuario'  => $this->id_usuario
				);
				 $this->db->set('uuid_usuario_rol', 'ORDER_UUID(uuid())', FALSE);
 				$this->db->insert('usuario_rol', $fieldset);
			}


		}
	  /*else{
	  	$this->db->where('id_usuario', $this->id_usuario);
	  	$this->db->delete('usuario_rol');
	  }*/
		// ---------------------------------------
		// End Transaction
		$this->db->trans_complete();

		// Managing Errors
		if ($this->db->trans_status() === FALSE) {

			log_message("error", "MODULO: Usuarios --> No se pudo editar el usuario en DB.");
			return array(
					"respuesta" => false,
					"mensaje" => "Hubo un error al tratar de editar el usuario."
			);

		}else {

 			return array(
					"respuesta" => true,
					"mensaje" => "El usuario se ha editado satisfactoriamente."
			);
		}
	}
  function guardar_usuario()
  {
      $this->nombre           = $this->input->post('nombre', true);
      $this->apellido         = $this->input->post('apellido', true);
      $this->correo            = $this->input->post('email', true);
      $this->usuario          = $this->input->post('usuario', true);
      $this->telefono          = $this->input->post('telefono', true);
      $this->extension          = $this->input->post('extension', true);
      $this->password         = $this->input->post('password', true);
      $this->id_rol           = $this->input->post('id_rol', true);

      //Encriptar password
      $this->password = $this->encrypt->encode($this->password);

      //
      // Begin Transaction
      // docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
      //
      $this->db->trans_start();

      //Guardar informacion Personal del usuario
      $fieldset = array(
        'nombre'      => $this->nombre,
        'apellido'    => $this->apellido,
        'email'       => $this->correo,
        'usuario'     => $this->usuario,
        'telefono'     => $this->telefono,
        'extension'     => $this->extension,
        'password'    => $this->password
      );
     // $fieldset["$name = UNHEX('$value')"] = NULL;
     /* $this->db->insert('usuarios', $fieldset);
      $this->id_usuario = $this->db->insert_id();
	 */
      //Guardar el rol del usuario
      $fieldset = array(
        'id_usuario'  => $this->id_usuario,
        'id_rol'      => $this->id_rol
      );
      $this->db->set('uuid_usuario_rol', 'ORDER_UUID(uuid())', FALSE);
      $this->db->insert('usuario_rol', $fieldset);

      //---------------------------------------
      //End Transaction
      $this->db->trans_complete();

      //Managing Errors
      if($this->db->trans_status() === FALSE){
        return false;
      }else{

        //guardar el numero de solicitud en variable de session
        $this->session->set_userdata('user_created', $this->id_usuario);

        return true;
      }
  }


  public function listar_modulo_campos( ){

	  	$fields = array(
	  			"*"
	  	);
	  	$clause = array(
	  			"v.id_modulo" =>  CRM_Controller::$id_modulo,
	  			"v.vista" => 'ver_usuario'
	  	);

	  	$fields = array(
	  			"*"
	  	);

	  	$result =  $this->db->select($fields)
	  	->from('mod_vistas v' )
	  	->join('mod_pestanas p', 'p.id_vista = v.id_vista', 'LEFT OUTER')
	  	->join('mod_formularios f', 'f.id_pestana = p.id_pestana', 'LEFT OUTER')
	  	->join('mod_paneles pa', 'pa.id_formulario = f.id_formulario', 'LEFT OUTER')
	  	->join('mod_panel_campos pc', 'pc.id_panel=pa.id_panel', 'LEFT OUTER')
	  	->join('usuarios_campos  uc', "uc.id_campo = pc.id_campo", 'LEFT OUTER')
	  	->join('mod_tipo_campos  tc', "tc.id_tipo_campo = uc.id_tipo_campo", 'LEFT OUTER')
	  	->where($clause)
	  	->get()
	  	->result_array();

	  	return $result;
  }
  /**
   * Actualiza un usuario
   *
   * @param
   * @return tru or false
   */
  public function actualizar_usuario()
  {

  	$fieldset = $campos_tablas =  array();
  	$this->id_usuario = $_POST['campo']['id_usuario'];
  	$this->password = $_POST['campo']['password'];

  	$files = $_FILES;

	  	if($files['campo']['name']['imagen_archivo'] !=''){

	  		$file_name = rand().time();
	  		$config['upload_path'] = './public/uploads/usuarios/';
	  		$config['file_name'] = $file_name;
	  		$config['allowed_types'] = '*';

	  		$extension = ".".end(explode('.', $files['campo']['name']['imagen_archivo']));

	  		$_FILES['userfile']['name']= $files['campo']['name']['imagen_archivo'];
	  		$_FILES['userfile']['type']= $files['campo']['type']['imagen_archivo'];
	  		$_FILES['userfile']['tmp_name']= $files['campo']['tmp_name']['imagen_archivo'];
	  		$_FILES['userfile']['error']= $files['campo']['error']['imagen_archivo'];
	  		$_FILES['userfile']['size']= $files['campo']['size']['imagen_archivo'];
	  		$_FILES['userfile']['filename']= $config['file_name'].$extension;

	  		$this->load->library('upload', $config);

	  		$this->upload->initialize($config);
	  		$this->upload->do_upload();



	  	}

    	$campos_tablas = $this->listar_modulo_campos();

  	  if(count($campos_tablas)>0)
	  	foreach($campos_tablas  as $valores){

	 	  	if( trim( $valores['nombre'] ) == 'text'  || trim( $valores['nombre'] ) == 'hidden' || trim( $valores['nombre'] ) == 'email' ||   trim( $valores['nombre'] ) == 'file_imagen' )
		  	{

 	 	   		if($valores['nombre_campo']== 'imagen_archivo' && $files['campo']['name']['imagen_archivo'] !=''  ){

 	 	   			$fieldset[$valores['nombre_campo'] ] = 'usuarios/'.$_FILES['userfile']['filename'];
 	 	   			$data = array(
 	 	   					'imagen_archivo'      	=> 'usuarios/'.$_FILES['userfile']['filename']
 	 	   			);
 	 	   			$this->session->set_userdata($data);

	 	   		}else if($valores['nombre_campo'] != 'imagen_archivo'){

	 	   			$fieldset[$valores['nombre_campo']] = $_POST['campo'][$valores['nombre_campo']];
	 	   		}
 		  	}
	  	}

	  	$data = array(
 	  			'nombre'        => $_POST['campo']['nombre'],
	  			'apellido'      => $_POST['campo']['apellido'],
  	  	);
	  	$this->session->set_userdata($data);


	   	//
	  	// Begin Transaction
	  	// docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
	  	//
	  	$this->db->trans_start();
	  	//Verificar si introdujo password nuevo
  		if($this->password != ""){

   			  	$this->password = $this->encrypt->encode(trim($this->password));
 			  	$fieldset['password'] = $this->password;
 			  	$fieldset['status'] = 'Activo';
 			  	$fieldset['last_recovery_time'] = date("Y-m-d H:m:s");

 			  	/**
 			  	 * @desc Se crean los logs para no repetir contraseña
 			  	 */

   			  	$fieldset_log = array(
 			  			'id_usuario' => $this->id_usuario ,
 			  			'password'   => $this->password
 			  	);
 			  	$this->db->insert('usuarios_passwords_logs', $fieldset_log);

 			  	//Crear Variables de Session
 			  	$data = array(
    			  		'status'      	=>'Activo',
   			  	);
 			  	$this->session->set_userdata($data);


		}

   		$clause = array(
  			"id_usuario" => $this->id_usuario
  		);
  		$this->db->where($clause)
  		->update('usuarios', $fieldset);


        //End Transaction
      	$this->db->trans_complete();

        //Managing Errors
         if($this->db->trans_status() === FALSE){
		          return false;
		 }else{

		  //guardar el numero de usuario en variable de session
		 $this->session->set_userdata('usuario_actualizado', $this->id_usuario);
		 		  	return true;
		 }


  }
  public function actualizar_usuario_admin()
  {

  	$fieldset = $campos_tablas =  array();
  	$this->id_usuario = $_POST['campo']['id_usuario'];
  	$this->password = $_POST['campo']['password'];

  	$files = $_FILES;

  	if($files['campo']['name']['imagen_archivo'] !=''     ){

  		$file_name = rand().time();
  		$config['upload_path'] = './public/uploads/usuarios/';
  		$config['file_name'] = $file_name;
  		$config['allowed_types'] = '*';

  		$extension = ".".end(explode('.', $files['campo']['name']['imagen_archivo']));

  		$_FILES['userfile']['name']= $files['campo']['name']['imagen_archivo'];
  		$_FILES['userfile']['type']= $files['campo']['type']['imagen_archivo'];
  		$_FILES['userfile']['tmp_name']= $files['campo']['tmp_name']['imagen_archivo'];
  		$_FILES['userfile']['error']= $files['campo']['error']['imagen_archivo'];
  		$_FILES['userfile']['size']= $files['campo']['size']['imagen_archivo'];
  		$_FILES['userfile']['filename']= $config['file_name'].$extension;

  		$this->load->library('upload', $config);

  		$this->upload->initialize($config);
  		$this->upload->do_upload();



  	}

  	$campos_tablas = $this->listar_modulo_campos();

  	if( $this->id_usuario == $this->session->userdata('id_usuario')  ){
  		$data = array(
  				'nombre'      	=> $_POST['campo']['nombre'],
  				'apellido'      	=> $_POST['campo']['apellido']
  		);
  		$this->session->set_userdata($data);
  	}



  	if(count($campos_tablas)>0)
  	foreach($campos_tablas  as $valores){
   		if( trim( $valores['nombre'] ) == 'text'  || trim( $valores['nombre'] ) == 'hidden' || trim( $valores['nombre'] ) == 'email' ||   trim( $valores['nombre'] ) == 'file_imagen' )
  		{
   			if($valores['nombre_campo']== 'imagen_archivo' && $files['campo']['name']['imagen_archivo'] !=''  ){

  				$fieldset[$valores['nombre_campo'] ] = 'usuarios/'.$_FILES['userfile']['filename'];
  				if( $this->id_usuario == $this->session->userdata('id_usuario')  ){
  					$data = array(
  							'imagen_archivo'      	=> 'usuarios/'.$_FILES['userfile']['filename']
  					);
  					$this->session->set_userdata($data);
  				}

  			}else if($valores['nombre_campo'] != 'imagen_archivo'){

  				$fieldset[$valores['nombre_campo']] = $_POST['campo'][$valores['nombre_campo']];

  			}
  		}
  	}


  	//
  	// Begin Transaction
  	// docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
  	//
  	$this->db->trans_start();
  	//Verificar si introdujo password nuevo
  	if($this->password != ""){

  		$this->password = $this->encrypt->encode(trim($this->password));
  		$fieldset['password'] = $this->password;
  		$fieldset['status'] = 'Activo';
  		$fieldset['last_recovery_time'] = date("Y-m-d H:m:s");

  		//Crear Variables de Session
  		$data = array(
  				'status'      	=>' Activo'
  		);
  		$this->session->set_userdata($data);


  	}

  	$clause = array(
  			"id_usuario" => $this->id_usuario
  	);
  	$this->db->where($clause)
  	->update('usuarios', $fieldset);


  	//End Transaction
  	$this->db->trans_complete();

  	//Managing Errors
  	if($this->db->trans_status() === FALSE){
  		return false;
  	}else{

  		//guardar el numero de usuario en variable de session
  		$this->session->set_userdata('usuario_actualizado', $this->id_usuario);
  		return true;
  	}


  }
  /*
   * Funcion que actualiza las politicas de Usuario
   * */


  public function guardar_politicas_usuario(){


	  	$this->id_configuracion  = 1;
 	  	$this->long_minima_usuario  = $this->input->post('long_minima_usuario', true);
	  	$this->uso_correo           = ($_POST['uso_correo'] == 'on' && isset($_POST['uso_correo'] ))?1:0;
	  	$this->long_maxima_usuario  = $this->input->post('long_maxima_usuario', true);
	  	$this->editar_perfil        = ($_POST['editar_perfil']   == 'on' && isset($_POST['editar_perfil']))?1:0;

	  	//
	  	// Begin Transaction
	  	// docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
	  	//
	  	$this->db->trans_start();

	  	//-------------------------------------------
	  	// Actualizar Informacion POlicticas de Usuario
	  	//-------------------------------------------
	  	$fieldset = array(
		  	'usu_long_minima_usuario'   => $this->long_minima_usuario,
		  	'usu_uso_correo'    		=> $this->uso_correo,
		  	'usu_long_maxima_usuario'   => $this->long_maxima_usuario,
		  	'usu_editar_perfil'     	=> $this->editar_perfil
	    );

 	  	$clause = array(
	  		"id_configuracion" => $this->id_configuracion
	  	);
	    $this->db->where($clause)
	  	->update('configuracion_sistema', $fieldset);


 	  	//---------------------------------------
	  	//End Transaction
	  	$this->db->trans_complete();

	  	//Managing Errors
	  	if($this->db->trans_status() === FALSE){
	  		log_message("error", "MODULO: Usuarios --> No se actualizó las politicas de Usuario.");
	  		return false;
	  	}else{

 	  		$this->session->set_userdata('politicas_usuario_actualizado', $this->id_configuracion);

	  	return true;
	  	}
  }

  public function guardar_politicas_contrasena(){


		  	$this->id_configuracion  				= 1;
		   	$this->long_minima_contrasena  			= $this->input->post('long_minima_contrasena', true);
		  	$this->expira_despues_dias  			= $this->input->post('expira_despues_dias', true);
		   	$this->configuracion_avanzada 			= ($_POST['configuracion_avanzada'] == 'on' && isset($_POST['configuracion_avanzada']))?1:0;
		   	$this->notificacion_usuarios_expiracion = ( $_POST['notificacion_usuarios_expiracion'] == 'on' && isset($_POST['notificacion_usuarios_expiracion']))?1:0;
		  	$this->minima_cantidad_letras  		= $this->input->post('minima_cantidad_letras', true);
		  	$this->restringir_contrasena_vieja  = ($_POST['restringir_contrasena_vieja']  == 'on' && isset($_POST['restringir_contrasena_vieja']))?1:0;
		  	$this->minima_cantidad_numeros  	= $this->input->post('minima_cantidad_numeros', true);
		  	$this->minima_cantidad_caracteres  	= $this->input->post('minima_cantidad_caracteres', true);
		  	//$this->cambiar_contrasena_login  	= ($_POST['cambiar_contrasena_login']  == 'on' && isset($_POST['cambiar_contrasena_login'] ))?1:0;
		  	//$this->cambiar_contrasena  			= ($_POST['cambiar_contrasena'] == 'on' && isset($_POST['cambiar_contrasena']))?1:0;
		  	$this->contr_notificar_antes_dias  = $this->input->post('contr_notificar_antes_dias', true);

		  	$fieldset = array(
		  	 		'contr_long_minima_contrasena'    		=> $this->long_minima_contrasena,
		  			'contr_expira_despues_dias'    		=> $this->expira_despues_dias,
		  			'contr_configuracion_avanzada'   	=> $this->configuracion_avanzada,
		  			'contr_notificacion_usuarios_expiracion'   => $this->notificacion_usuarios_expiracion,
		  			//'contr_minima_cantidad_letras'     	=> $this->minima_cantidad_letras,
		  			'contr_restringir_contrasena_vieja' => $this->restringir_contrasena_vieja,
		  			//'contr_minima_cantidad_numeros'     => $this->minima_cantidad_numeros,
		  			//'minima_cantidad_caracteres'     	=> $this->minima_cantidad_caracteres,
		  			//'contr_cambiar_contrasena_login'    => $this->cambiar_contrasena_login,
		  			//'contr_cambiar_contrasena'     		=> $this->cambiar_contrasena,
		  			//'contr_notificar_antes_dias'     	=> $this->contr_notificar_antes_dias

		  	);

  			if($this->configuracion_avanzada == 1 ){
	  				$fieldset_avanzado = array(
	  						'contr_minima_cantidad_letras'     	=> $this->minima_cantidad_letras,
	  						'contr_minima_cantidad_numeros'     => $this->minima_cantidad_numeros,
	  						'contr_minima_cantidad_caracteres'     	=> $this->minima_cantidad_caracteres,

	  				);
	  				$fieldset = array_merge($fieldset, $fieldset_avanzado);
  			}
  			if($this->notificacion_usuarios_expiracion == 1 ){
  				$fieldset_expiracion = array(
  						'contr_notificar_antes_dias'     	=> $this->contr_notificar_antes_dias,
  				);
  				$fieldset = array_merge($fieldset, $fieldset_expiracion);
  			}


  	//
  	// Begin Transaction
  	// docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
  	//
  	$this->db->trans_start();

  	//-------------------------------------------
  	// Actualizar Informacion Policticas de Contraseña
  	//-------------------------------------------


  	$clause = array(
  			"id_configuracion" => $this->id_configuracion
  	);
  	$this->db->where($clause)
  	->update('configuracion_sistema', $fieldset);


  	//---------------------------------------
  	//End Transaction
  	$this->db->trans_complete();

  	//Managing Errors
  	if($this->db->trans_status() === FALSE){
  		log_message("error", "MODULO: Usuarios --> No se actualizó las politicas de Usuario.");
  		return false;
  	}else{

  		$this->session->set_userdata('politicas_contrasena_actualizado', $this->id_configuracion);

  		return true;
  	}
  }



  public function validando_ultimas_contrasenas()
  {

  		$politicas = $this->seleccionar_politicas(); //Me retorna los valores para las validaciones de Usuario y Password
   		if($politicas['contrasena']['restringir_contrasena_vieja'] == 1){
  			//Lenando el array de las ultimas 10 o menos contraseñas
  			$password_array = $this->seleccionando_ultimas_contrasenas();

  			if (in_array($this->input->get('contrasena', true), $password_array)) {
  				return true;
  			}else{
  			 	return false;
  			}
  		}
  		else{
  			return false;
  		}
  }

  /*
   * Verificar el id suario
  * Creando array de los ultimos 10 contraseñas
  */
  public function seleccionando_ultimas_contrasenas()
  {
  	$result = $contrasenas  =  array();

  	$fields = array(
  			"logs.*",

  	);

  	$clause = array( 'logs.id_usuario '=> $this->session->userdata('id_usuario'));


  	$result = $this->db->select($fields)
  	->from('usuarios_passwords_logs AS logs')
  	->where($clause)
  	->order_by('creacion', 'DESC')
  	->limit(10, 0)
  	->get()
  	->result_array();


  	if(!empty($result)){

  		foreach ($result AS $valores){
     			$contrasenas[] = $this->encrypt->decode($valores['password']);
   		}
  	}

  	return $contrasenas;
  }


  public function update_usuario()
  {

      $this->id_usuario       = $this->input->post('id_usuario', true);
      $this->nombre           = $this->input->post('nombre', true);
      $this->apellido         = $this->input->post('apellido', true);
      $this->correo            = $this->input->post('email', true);
      $this->usuario          = $this->input->post('usuario', true);
      $this->telefono          = $this->input->post('telefono', true);
      $this->extension          = $this->input->post('extension', true);
      $this->password         = $this->input->post('password', true);
      $this->id_rol           = $this->input->post('id_rol', true);

      //
      // Begin Transaction
      // docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
      //
      $this->db->trans_start();

      //-------------------------------------------
      // Actualizar Informacion Personal del usuario
      //-------------------------------------------
      $fieldset = array(
        'nombre'      => $this->nombre,
        'apellido'    => $this->apellido,
        'email'       => $this->correo,
        'usuario'     => $this->usuario,
        'telefono'     => $this->telefono,
        'extension'     => $this->extension
      );

      //Verificar si introdujo password nuevo
      if($this->password != ""){
          //Encriptar password
          $this->password = $this->encrypt->encode($this->password);

          $fieldset['password'] = $this->password;
      }
      $clause = array(
          "id" => $this->id_usuario
      );
      $this->db->where($clause)
            ->update('usuarios', $fieldset);

      //-------------------------------------------
      // Actualizar el rol del usuario
      //-------------------------------------------
      print_r($this);
      $fieldset = array(
        'role_id' => $this->id_rol
      );
      $clause = array(
          "id" => $this->id_usuario
      );
      $this->db->where($clause)
            ->update('usuario_has_roles', $fieldset);

      //---------------------------------------
      //End Transaction
      $this->db->trans_complete();

      //Managing Errors
      if($this->db->trans_status() === FALSE){
      	log_message("error", "MODULO: Usuarios --> Tipo de error.");
        return false;
      }else{

        //guardar el numero de solicitud en variable de session
        $this->session->set_userdata('user_updated', $this->id_usuario);

        return true;
      }
  }




  public function seleccionar_usuario_info($id_usuario = NULL  )
  {
      $result = array();
      $fields = array(
        "usr.id_usuario",
        "usr.nombre",
        "usr.nombre",
        "usr.apellido",
        "usr.usuario",
        "usr.password",
        "usr.last_recovery_time",
        "usr.telefono",
        "usr.imagen_archivo",
        "usr.extension",
        "usr.status",
        "usr.email",
      	"usr.last_login",
        "urol.id_rol"
      );
      $clause = array(
        "usr.id_usuario" => $id_usuario
      );
      $usuarioINFO = $this->db->select($fields)
            ->distinct()
            ->from('usuarios AS usr')
            ->join('usuario_rol AS urol', 'urol.id_usuario = usr.id_usuario', 'LEFT')
            ->where($clause)
            ->get()
            ->result_array();

      if(!empty($usuarioINFO))
      {

      	  $ultima_fecha  = ($usuarioINFO[0]['last_login']=='')?'Sin Fecha':date("d-M-Y", strtotime($usuarioINFO[0]['last_login']));
      	  $ultimo_acceso = ($usuarioINFO[0]['last_login']=='')?'Sin Acceso':$this->timeElapsedString(  $usuarioINFO[0]['last_login'] );

          $result['id_usuario'] = $usuarioINFO[0]['id_usuario'];
          $result['nombre']     = $usuarioINFO[0]['nombre'];
          $result['apellido']   = $usuarioINFO[0]['apellido'];
          $result['usuario']    = $usuarioINFO[0]['usuario'];
          $result['telefono']   = $usuarioINFO[0]['telefono'];
          $result['password']   = $usuarioINFO[0]['password'];
          $result['last_recovery_time']   = $usuarioINFO[0]['last_recovery_time'];
          $result['imagen_archivo']     = $usuarioINFO[0]['imagen_archivo'];
          $result['extension']  = $usuarioINFO[0]['extension'];
          $result['status']     = $usuarioINFO[0]['status'];
          $result['email']      = $usuarioINFO[0]['email'];
          $result['id_rol']     = $usuarioINFO[0]['id_rol'];
          $result['last_login'] = $ultima_fecha;
          $result['hace']       = $ultimo_acceso;
      }

      return $result;
  }
public function timeElapsedString($datetime = array(), $full = false)
    {
        $now = new DateTime;
        $ago = new DateTime($datetime);

        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'a&ntilde;o(s)',
            'm' => 'Mes(es)',
            'w' => 'Semana(s)',
            'd' => 'dia(s)',
            'h' => 'hora(s)',
            'i' => 'Minuto(s)',
            's' => 'Segundo(s)',
        );
        foreach ($string as $k => &$v) {

            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? '' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full){
        	$string = array_slice($string, 0, 1);

        }

         $last = $string ?  ' hace '.implode(', ', $string)  : 'justo ahora';



         return $last;
    }
  function actualizar_desactivar_usuario()
  {
      $id_usuario = $this->input->post('id_usuario', true);

      //
      // Begin Transaction
      // docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
      //
      $this->db->trans_start();

      $fieldset = array(
        'status' => 'Inactivo'
      );
      $clause = array(
          "id_usuario" => $id_usuario
      );
      $this->db->where($clause)
            ->update('usuarios', $fieldset);

      //---------------------------------------
      //End Transaction
      $this->db->trans_complete();

      //Managing Errors
      return $this->db->trans_status() === FALSE ? false : true;
  }

  function actualizar_activar_usuario()
  {
      $id_usuario = $this->input->post('id_usuario', true);

      //
      // Begin Transaction
      // docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
      //
      $this->db->trans_start();

      $fieldset = array(
        'status' => 'Activo'
      );
      $clause = array(
          "id_usuario" => $id_usuario
      );
      $this->db->where($clause)
            ->update('usuarios', $fieldset);

      //---------------------------------------
      //End Transaction
      $this->db->trans_complete();

      //Managing Errors
      return $this->db->trans_status() === FALSE ? false : true;
  }
  function getRandomNumber($length = NULL) {
   	   	$characters = '0123456789';
	   	$string = '';

	  	for ($i = 1; $i <= $length; $i++) {
	  		$string .= $characters[mt_rand(0, strlen($characters) - 1)];
	  	}

	  	return $string;
  }
  function getRandomString($length = NULL) {
 	  	$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	  	$string = '';

	  	for ($i =1; $i <= $length; $i++) {
	  		$string .= $characters[mt_rand(0, strlen($characters) - 1)];
	  	}

  	return $string;
  }
  function getRandomCaracter($length = NULL) {
  	$characters = '#$%&;:';
  	$string = '';

  	for ($i =1; $i <= $length; $i++) {
  		$string .= $characters[mt_rand(0, strlen($characters) - 1)];
  	}

  	return $string;
  }

  function usuarios_actividades($fecha,$subordinados){


  	if(!empty($fecha)){
  		switch(key($fecha)){
  			case 'hoy':
  				$clause ='fecha_creacion BETWEEN "'.$fecha['hoy'].' 00:00:00" AND "'.$fecha['hoy'].' 23:59:59"';
  				break;
  			case 'ayer':
  				$clause = 'fecha_creacion BETWEEN "'.$fecha['ayer'].' 00:00:00" AND "'.$fecha['ayer'].' 23:59:59"';
  				break;
  			case 'esta_semana':
  				$clause = 'YEARWEEK(fecha_creacion) = "'.$fecha['esta_semana'].'"';
  				break;
  			case 'ultima_semana':
  				$clause = 'YEARWEEK(fecha_creacion) = "'.$fecha['ultima_semana'].'"';
  				break;
  			case 'este_mes':
  				$clause = 'EXTRACT(YEAR_MONTH FROM fecha_creacion) = "'.$fecha['este_mes'].'"';
  				break;
  			case 'ultimo_mes':
  				$clause = 'EXTRACT(YEAR_MONTH FROM fecha_creacion) = "'.$fecha['ultimo_mes'].'"';
  				break;
  		}

  	}

  	if(empty($subordinados)){
  	$tipo_actividad = $this->db->query("SELECT DISTINCT CONCAT (nombre, ' ', apellido) AS agente , COUNT(llamada) AS llamada, COUNT(reunion) AS reunion,
COUNT(tarea) AS tarea,COUNT(presentacion) AS presentacion
FROM ( SELECT DISTINCT
IF (HEX(uuid_tipo_actividad)='11EFBFBD18EFBFBDD2AE4608EFBFBD76',1 ,NULL) AS Llamada,
IF (HEX(uuid_tipo_actividad)='11E518FED2AE48F39B76BC764E1054A0',1 ,NULL) AS reunion,
IF (HEX(uuid_tipo_actividad)='11E518FED2AE49B99B76BC764E1054A0',1 ,NULL) AS tarea,
IF (HEX(uuid_tipo_actividad)='11E518FED2AE4A739B76BC764E1054A0',1 ,NULL) AS presentacion,
uuid_asignado,creado_por, id_actividad,fecha_creacion
FROM act_actividades) AS t, usuarios WHERE uuid_usuario IN (uuid_asignado) AND $clause GROUP BY agente")->result_array();
  	}else{
  		$uuid = "'".implode("','", $subordinados['uuid_usuario'])."'";

  		$tipo_actividad = $this->db->query("SELECT DISTINCT  agente , COUNT(llamada) AS llamada, COUNT(reunion) AS reunion,
  				COUNT(tarea) AS tarea,COUNT(presentacion) AS presentacion
  				FROM (SELECT DISTINCT
  				(SELECT DISTINCT CONCAT (nombre, ' ', apellido) FROM usuarios where uuid_usuario = uuid_asignado) AS agente,
  				IF (HEX(uuid_tipo_actividad)='11EFBFBD18EFBFBDD2AE4608EFBFBD76',1 ,NULL) AS Llamada,
  				IF (HEX(uuid_tipo_actividad)='11E518FED2AE48F39B76BC764E1054A0',1 ,NULL) AS reunion,
  				IF (HEX(uuid_tipo_actividad)='11E518FED2AE49B99B76BC764E1054A0',1 ,NULL) AS tarea,
  				IF (HEX(uuid_tipo_actividad)='11E518FED2AE4A739B76BC764E1054A0',1 ,NULL) AS presentacion,
  				uuid_asignado,creado_por, id_actividad,fecha_creacion
  				FROM act_actividades WHERE HEX(uuid_asignado) IN ($uuid)) AS t  WHERE $clause GROUP BY agente")->result_array();
  	}

  	//echo $this->db->last_query();
  	$row = array();
  	$var = array();
  	$total_categoria = 0;
  	foreach($tipo_actividad as  $count_total){
  		$total_categoria+= $count_total['llamada'] + $count_total['reunion'] + $count_total['tarea'] + $count_total['presentacion'];
  	}

  	foreach($tipo_actividad as $key => $categoria){
  		$total = $categoria['llamada'] + $categoria['reunion'] + $categoria['tarea'] + $categoria['presentacion'];
  		$var = array('<span class="pie">'."$total/$total_categoria".'</span>',$categoria['agente'],$categoria['llamada'],$categoria['reunion'], $categoria['tarea'],$categoria['presentacion'],$total);
  		$row[] = array('id'=>$key, 'cell'=>$var);

  	}

  	return  array('rows' => $row,'records'=>count($tipo_actividad),'total'=> count($row), 'page'=> 1);

  }

  function popular_tabla_perfil(){


  	$tipo_actividad= $this->db->query("select nombre from act_tipo_actividades ")->result_array();
  	$colnames = array();
  	$colmodel = array();
  	foreach($tipo_actividad as $j=>$col){
  		$colnames[]= $col['nombre'];
  		$colmodel[] = array('name'=>$col['nombre'], 'index'=>$col['nombre'], 'width'=>60,'formatter'=> "integer");

  	}
  	array_unshift($colnames, "", "Usuarios");
  	array_unshift($colmodel, array('name'=>"vacio", 'index'=>"vacio", 'width'=>10), array('name'=>'Usuarios', 'index'=>"Usuarios", 'width'=>60));
  	array_push($colmodel,array('name'=>"Total", 'index'=>"Total", 'width'=>10,'formatter'=> "integer"));
  	array_push($colnames,"Total");

  	return  array('colName' => $colnames,'colModel' => $colmodel);

  }

  function getMontoOportunidad($etapa, $usuario,$fecha){

  	$fields = array (
  			"IFNULL(SUM(opp.valor_oportunidad),0) AS monto_total",
  			"COUNT(opp.id_oportunidad) AS total_oportunidades"
  	);

  	$this->db->select($fields);
  	$this->db->distinct();
  	$this->db->from('opp_oportunidades AS opp');
  	$this->db->join('opp_oportunidades_cat AS ocat', 'ocat.id_cat = opp.id_etapa_venta', 'LEFT');
  	$this->db->where('HEX(id_asignado)',$usuario);
  	switch($etapa){

  		case 'ganadas':
  			$this->db->where('ocat.id_cat',2);
  	    break;
  		case 'perdidas':
  			$this->db->where('ocat.id_cat',3);
  		break;
  		case 'nuevas':
  			$this->db->where_in('ocat.id_cat',array(1,4,5));
  		break;

  	}

  	if(!empty($fecha)){
  		switch(key($fecha)){
  			case 'hoy':
  				$this->db->where('opp.fecha_creacion BETWEEN "'.$fecha['hoy'].' 00:00:00" AND "'.$fecha['hoy'].' 23:59:59"');
  				break;
  			case 'ayer':
  				$this->db->where('opp.fecha_creacion BETWEEN "'.$fecha['ayer'].' 00:00:00" AND "'.$fecha['ayer'].' 23:59:59"');
  				break;
  			case 'esta_semana':
  				$this->db->where('YEARWEEK(opp.fecha_creacion) =',$fecha['esta_semana']);
  				break;
  			case 'ultima_semana':
  				$this->db->where('YEARWEEK(opp.fecha_creacion) =',$fecha['ultima_semana']);
  				break;
  			case 'este_mes':
  				$this->db->where('EXTRACT(YEAR_MONTH FROM opp.fecha_creacion) =',$fecha['este_mes']);
  				break;
  			case 'ultimo_mes':
  				$this->db->where('EXTRACT(YEAR_MONTH FROM opp.fecha_creacion) =',$fecha['ultimo_mes']);
  				break;
  		}
  	}


    $result = $this->db->get()->row_array();
  	//print_r($result);
   return $result;

  }


}

?>

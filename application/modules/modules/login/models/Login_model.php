<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Login_model extends CI_Model
{
    protected $id_usuario;
    protected $username;
    private $login_attempts;

    function __construct(){
        parent::__construct();
    }

    /*
     * Verificar si el nombre de usuario
     * concuerda con algun usuario registrado
     */
    public function check_username()
    {
        $this->username = $this->input->post('username', true);

        $fields = array(
        	"HEX(usr.uuid_usuario) AS huuid_usuario",
            "usr.id",
            "usr.password",
            "usr.nombre",
            "usr.apellido",
            "usr.imagen_archivo",
        	"usr.estado",
        	"usr.email",
        	"usr.login_attemps",
        	//"usr.last_recovery_time"
        );

        $clause = 'usr.usuario ="'.$this->username.'" AND (usr.estado="Activo" OR usr.estado="Pendiente" OR usr.estado="Expirado")';

        $result = $this->db->select($fields)
                    ->from('usuarios AS usr')
                    ->join('usuarios_has_roles` AS urr', 'urr.usuario_id = usr.id', 'LEFT')
                    ->where($clause)
                    ->get()
                    ->result_array();
         //Si existe el usuario, establecer algunas variables
        if(!empty($result)){
        	$this->id_usuario = $result[0]['id'];
        	$this->login_attempts = $result[0]['login_attemps'];
        }

        return $result;
    }

    /*
     * Verificar el id suario
    * Creando array de los ultimos 10 contraseñas
    */
    public function seleccionar_ultimas_contrasenas($id_usuario)
    {
    	$result = $contrasenas  =  array();

    	$fields = array(
    			"logs.*",

    	);

    	$clause = array( 'logs.id_usuario '=> $id_usuario);


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
    /*
     * Verificar el id suario
    * concuerda con algun usuario registrado
    */
    public function seleccionar_id_usuario($usuario)
    {


    	$fields = array(
    			"usr.*",

    	);

    	$clause = array( 'usr.usuario '=> $usuario);


    	$result = $this->db->select($fields)
    	->from('usuarios AS usr')
    	->where($clause)
    	->get()
    	->result_array();
    	//Si existe el usuario, establecer algunas variables
    	if(!empty($result)){
    		$this->id_usuario = $result[0]['id'];
    	}

    	return $result;
    }
    public function update_last_login()
    {
    	//
    	// Begin Transaction
    	// docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
    	//
    	$this->db->trans_start();

    	$fieldset = array (
    		"last_login" => date('Y-m-d H:i:s'),
    		"last_login_ip_address" => $this->input->ip_address()
    	);
    	$clause = array (
    		"id" => $this->id_usuario
    	);
    	$this->db->where($clause)->update('usuarios', $fieldset);

    	//---------------------------------------
    	//End Transaction
    	$this->db->trans_complete();
    }

    /**
     * @author Pensanomica
     * Cambio en el estado del Usuario, activo a Expirado
     */
    public function actualizar_estado()
    {
    	//
    	// Begin Transaction
    	// docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
    	//
    	$this->db->trans_start();

    	$fieldset = array (
    			"status" => 'Expirado'
    	);
    	$clause = array (
    			"id_usuario" => $this->id,
    	);
    	$this->db->where($clause)->update('usuarios', $fieldset);

    	//---------------------------------------
    	//End Transaction
    	$this->db->trans_complete();
    }
    public function login_attempts()
    {
    	return $this->login_attempts;
    }

    public function increase_login_attempt()
    {
		//
		// Begin Transaction
		// docs: https://ellislab.com/codeigniter/user-guide/database/transactions.html
		//
      	$this->db->trans_start();

      	$this->login_attempts = $this->login_attempts+1;

		$fieldset = array (
			"login_attempts" => $this->login_attempts
		);
		$clause = array (
			"usuario" => $this->username
		);
		$this->db->where($clause)->update('usuarios', $fieldset);

		//---------------------------------------
		//End Transaction
		$this->db->trans_complete();

		// Managing Errors
		// Si no hubo error en el query.
		if ($this->db->trans_status() === TRUE){

			// Login failed ...
			log_message("error", "MODULO: Login --> Intento de login fallido.");
		}
    }

    /*
     * Verificar si el email existe en la base de datos.
     */
    public function checkEmail()
    {
        $email = $this->input->post('email', true);

        $fields = array(
            "id_usuario",
            "email",
            "nombre",
            "apellido",
            "email",
        );
        $clause = array(
            "email" => $email
        );
        $result = $this->db->select($fields)
                    ->from('usuarios')
                    ->where($clause)
                    ->get()
                    ->result_array();
        return $result;
    }


    /*
     * Guardar token y fecha en que se esta solicitando
     * Restablecimiento de contraseña
     */
    public function save_recovery_request($username, $token)
    {
        if($username == ""){
        	return false;
        }

        //Begin Transaction
        $this->db->trans_start();

        $fieldset = array(
        	'recovery_token' => $token,
        	'recovery_time' => date('Y-m-d h:i:s'),
          'estado' => 'Activo'
        );
        $clause = array(
        	"usuario" => $username
        );
        $this->db->where($clause)->update('usuarios', $fieldset);

        //End Transaction
        $this->db->trans_complete();

        //Managing Errors
        if($this->db->trans_status() === FALSE){
        	return false;
        }else{
        	return true;
        }
    }

    /*
     * Verificar si el token existe
     */
    public function check_token($username, $token)
    {
        if($username != "" && $token != ""){

            $fields = array(
            	"recovery_time"
            );
            $clause = array(
                "usuario" => $username,
                "recovery_token" => $token
            );
            $result = $this->db->select($fields)
                        ->from('usuarios')
                        ->where($clause)
                        ->get()
                        ->result_array();
            return $result;

        }else{
            return false;
        }
    }

    /*
     * Limpiar campo token y fecha
     */
    public function reset_recover_request($username)
    {
        if($username != "")
        {
            //Begin Transaction
            $this->db->trans_start();

            $fieldset = array(
              'recovery_token' => "",
              'recovery_time' => ""
            );
            $clause = array(
              "usuario" => $username
            );
            $this->db->where($clause)
                    ->update('usuarios', $fieldset);

            //End Transaction
            $this->db->trans_complete();

        }else{
            return false;
        }
    }

    public function update_password($username)
    {
    	//Buscando el id del usuario
    	$usuario =  $this->seleccionar_id_usuario($username);
      	$politicas = $this->usuarios->usuarios_model->seleccionar_politicas(); //Me retorna los valores para las validaciones de Usuario y Password
    	if($politicas['contrasena']['restringir_contrasena_vieja'] == 1){
	    		//Lenando el array de las ultimas 10 o menos contraseñas
	    		$password_array = $this->seleccionar_ultimas_contrasenas($usuario[0]['id']);

	    		if (in_array($this->input->post('password1', true), $password_array)) {
	    			return false;
	    		}/*else{
	    		echo "ES NUEVO";
	    		}*/
    	}


        $newPassword = $this->input->post('password1', true);
        $newPassword = $this->encrypt->encode($newPassword);


        //Begin Transaction
        $this->db->trans_start();

        //ACTUALIZAR PASSWORD
        $fieldset = array(
          	'password' => $newPassword,
        	'last_recovery_time' => date("Y-m-d H:i:s")
        );
        $clause = array(
          "usuario" => $username
        );
        $this->db->where($clause)
                ->update('usuarios', $fieldset);

		 /**
		  * @desc Se crean los logs para no repetir contraseña
 		*/

        $fieldset = array(
        		'id_usuario' =>$usuario[0]['id'],
        		'password' => $newPassword
        );


        $this->db->insert('usuarios_passwords_logs', $fieldset);


        //End Transaction
        $this->db->trans_complete();

        //Managing Errors
        if($this->db->trans_status() === FALSE){
         	return false;
        }else{
          	return true;
        }
    }

}

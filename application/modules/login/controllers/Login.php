<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Login\Models\Auth;
use Flexio\Modulo\Usuarios\FormRequest\ActivarUsuario;

class Login extends CRM_Controller {

    private $user_info;
    private $username;
    protected $auth;

    function __construct() {
        parent::__construct();


        $config = Array(
            'mailtype' => 'html',
            'charset' => 'utf-8',
            'wordwrap' => TRUE
        );

        $this->load->helper(array('file', 'string', 'cookie'));
        $this->load->model('login_model');
        $this->load->model('login_orm');
        $this->load->model('sistema/sistema_orm');
        $this->load->library(array('encrypt'));
        $this->load->library('email', $config);

        //Encriptar el texto
        $this->encrypt->set_cipher(MCRYPT_RIJNDAEL_256);
        $this->auth = new Auth;
        //For Debug Code
        //$this->output->enable_profiler(ENVIRONMENT == "development");
        $this->load->module(array('usuarios', 'roles', 'sistema'));

    }

    public function index() {

        $data = $politicas = array();
        $tipo_error = '';
        //  $politicas = $this->usuarios->usuarios_model->seleccionar_politicas(); //Me retorna los valores para las validaciones de Usuario y Password
        $politicas = Sistema_orm::seleccionar_politicas();
        $quedan = "";
        if ($_POST) {
            $this->form_validation->set_rules('username', 'Nombre de Usuario', 'trim|required|min_length[3]|max_length[150]');
            $this->form_validation->set_rules('password', 'Contrasena', 'trim|required|min_length[3]|max_length[150]');

            //Verificar si los campos requeridos
            //no estan vacios
            if ($this->form_validation->run() == true) {
                //Verificar en base de datos si el
                //usuario existe y si tiene su cuenta activa.
                //$this->user_info = $this->login_model->check_username();
                $clause = array('email' => $this->input->post('username', true),
                    'estado' => 'Activo');
                $usuario = Auth::check_username($clause);
                $password = $this->input->post("password", true);
                if ($usuario !== null) {
                    if ($this->encrypt->decode($usuario->password) == $password) {
                        $this->user_info = $usuario->toArray();
                    }
                }
                //Si existe el usuario
                if (!empty($this->user_info)) {



                    //Verificar si coinciden los passwords
                    /*  if($this->encrypt->decode($this->user_info['password']) == $password)
                      { */
                    //Si esta activado el vencimiento de password
                    if (isset($politicas['contrasena']['expira_despues_dias']) && $politicas['contrasena']['expira_despues_dias'] > 0 && $this->user_info['id'] <> 1) {

                        $ultima_actualizacion = explode(' ', $this->user_info['last_recovery_time']);
                        $ahora = time();
                        $_ultimafecha = strtotime($ultima_actualizacion[0]);
                        $fechadiff = $ahora - $_ultimafecha;
                        $han_pasado = floor($fechadiff / (60 * 60 * 24)); //Has Pasado x dias desde la actualiacion del password
                        $quedan = $politicas['contrasena']['expira_despues_dias'] - $han_pasado; //Quedan x dias para vencer

                        if ($politicas['contrasena']['notificacion_usuarios_expiracion'] == 1) { //Habilitado notificacion a Usuarios
                            if ($quedan < 1) { //Esta vencido el password
                                $tipo_error = "password vencido";
                            } else { //No esta vencido
                                if ($quedan > 0 && $quedan <= $politicas['contrasena']['contr_notificar_antes_dias']) { //Te quedan Pocos dias para el vencimiento
                                    $tipo_error = "pocos dias";
                                }
                            }
                        } else { //No esta habilitado la notificacion
                            if ($quedan < 1) { //Esta vencido el password
                                $tipo_error = "password vencido";
                            }
                        }
                    }

                    if ($tipo_error == 'password vencido') {
                        //Se hace un cambio en el estado del usuario al estado Expirado
                        $this->auth->actualizar_estado();
                        $data['message']['content'] = 'Has introducido una contrase&ntilde;a vencida.';
                    } else {

                        $mensaje = 'Su contrase&ntilde;a vencer&aacute; en los pr&oacute;ximos (' . $quedan . ') dias.  Le recomendamos actualizar su contrase&ntilde;a.';

                        //Crear Variables de Session

                        $usuario_empresa = $usuario->empresas()->where('default', 1)->first();

                        /*$mapidrol = $usuario->roles->where('empresa_id', $usuario_empresa->id)->map(function($idrol) {
                            return $idrol->id;
                        });*/ //Este pedazo se debe activar, apenas se corrigen los permisos de roles,

                       $mapidrol = $usuario->roles->map(function($idrol) {
                            return $idrol->id;
                        });
                        $data = array(
                            'huuid_usuario' => $this->user_info['uuid_usuario'],
                            'id_usuario' => $this->user_info['id'],
                            'nombre' => $this->user_info['nombre'],
                            'apellido' => $this->user_info['apellido'],
                            'estado' => $this->user_info['estado'],
                            'por_vencer' => ($tipo_error == 'pocos dias') ? $mensaje : '',
                            'imagen_archivo' => $this->user_info['imagen_archivo'],
                            'roles' => $mapidrol->toArray()
                        );

                        if (isset($usuario_empresa->uuid_empresa)) {
                            $data['uuid_empresa'] = $usuario_empresa->uuid_empresa;
                        }
                        $this->session->set_userdata($data);

                        //Actualizar datos de login
                        //validar si el usuario logueado
                        // 1. rol de usuario para crear empresas si tiene, redireccionar al algun modulos
                        // si no tiene empresas redireccionar a crear empresas
                        // 2. el usuario no tiene rol de crear empresas redireccionar a algun modulo con la empresa que pertenese

                        $this->auth->update_last_login($this->user_info['id']);
                        //Redireccionar dependiendo si el usuario es nuevo
                        if ($data['estado'] == 'Activo') {
                            //redirect('usuarios/listar-usuarios');
                            $mapid = $usuario->roles->map(function($idrol) {
                                return $idrol->id;
                            });

                            // rol de administrador para crear empresa y organizacion
                            if (in_array(2, $mapid->toArray()) || in_array(1, $mapid->toArray()))
                            {
                                if($usuario->empresas->count()> 0)
                                {
                                    redirect('/');
                                }
                                redirect('usuarios/listar_empresa');
                            }elseif(in_array(3, $mapid->toArray())) {
                                redirect('/');
                            }
                        }else{ //
                            $mensaje = array('clase' => 'alert-danger', 'contenido' => 'Has introducido un usuario o una contrase&ntilde;a incorrecta.');
                            $this->session->set_flashdata('mensaje', $mensaje);
                            redirect('/login');
                            //redireccionar a usuarios no activos
                        }
                    }

                    /*  }
                      else{
                      $data['message']['content'] = 'Has introducido una contrase&ntilde;a incorrecta.';
                      } */
                } else {
                    $mensaje = array('clase' => 'alert-danger', 'contenido' => 'Has introducido un usuario o una contrase&ntilde;a incorrecta.');
                    $this->session->set_flashdata('mensaje', $mensaje);
                    redirect('login');
                }
            } else {
                $mensaje = array('clase' => 'alert-warning', 'contenido' => 'Por favor introduzca sus datos.');
                $this->session->set_flashdata('mensaje', $mensaje);
                redirect('login');
            }
        }


        // $data['cambiar_password']=  $politicas['contrasena']['cambiar_contrasena_login'];
        //session not defined
        $session['isset_session'] = "EMPTY";

        $this->assets->agregar_css(array('public/assets/css/modules/stylesheets/login.css'));
        $this->template->agregar_titulo_header('Login');
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    public function logout() {
        $this->session->sess_destroy();

        redirect('login');
    }

    public function checksess() {
        $this->session->sess_update();
    }

    public function forget() {
        $data = array();

        if (isset($_POST)) {
            $this->form_validation->set_rules('username', 'Usuario', 'required|trim');
            $this->form_validation->set_message('required', 'Por favor, introduce tu nombre de usuario.');
            $this->form_validation->set_error_delimiters('<label class="required">', '</label>');

            //Verificar si los campos requeridos no estan vacios
            if ($this->form_validation->run()) {

                //Establecer username
                $this->username = $this->input->post('username', true);

                //Verificar que el correo exista en la DB.
                $check_username = $this->login_model->check_username();

                if (!empty($check_username)) {

                    //Verificar si se envio el correo
                    if ($this->send_recovey_email($check_username)) {
                        $mensaje = array('clase' => 'alert-success', 'contenido' => 'Te hemos enviado un correo electronico con las instrucciones para recuperar tu contrase&ntilde;a. Por favor, comprueba que no haya acabado en tu correo basura.');
                        $this->session->set_flashdata('mensaje', $mensaje);
                        redirect('login/forget');
                    }
                } else {
                    $mensaje = array('clase' => 'alert-danger', 'contenido' => 'Lo sentimos, el usuario introducido no esta registrado.');
                    $this->session->set_flashdata('mensaje', $mensaje);
                    redirect('login');
                }
            }
        }

        $this->template->agregar_titulo_header('Recuperar Contrase&ntilde;a');
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    /*
     * Enviarle correo al usuario con el enlace para
     * recuperar su contrase�a.
     */

    public function send_recovey_email($userINFO) {
        if (empty($userINFO)) {
            return false;
        }

        //Datos del usuario
        $email = $userINFO[0]['email'];

        //Texto que servira como token
        $string = date('dhis') . random_string('alnum', 10);

        //Encriptar token
        $this->encrypt->set_cipher(MCRYPT_RIJNDAEL_128);
        $token = $this->encrypt->encode($string);
        $token = preg_replace('/[^\p{L}\p{N}\s]/u', '', $token);
        $url = base_url('login/recover/?usr=' . $this->username . '&token=' . $token);

        $filepath = realpath('./public/templates/email/crear_cuenta/recover_password.html');

        //Verificar primero si el template de recuperar password existe
        if (!file_exists($filepath)) {
            //No se encontro el archivo ...
            log_message("error", "MODULO: Login --> No se encontro la plantilla de Recuperar Contrase&ntilde;a.");
            return false;
        }

        //Leer archivo html que contiene el texto del correo
        $htmlmail = read_file($filepath);

        //Reemplazar valores en el archivo html
        //__SITE_URL__
        //__URL_PASSWORD_RECOVER__
        //__YEAR__
        $htmlmail = str_replace("__SITE_URL__", base_url('/'), $htmlmail);
        $htmlmail = str_replace("__URL_PASSWORD_RECOVER__", $url, $htmlmail);
        $htmlmail = str_replace("__YEAR__", date('Y'), $htmlmail);

        //Enviar el correo
        $this->email->from('no-reply@starholding.com', 'CRM+');
        $this->email->to($email);
        $this->email->subject('Recuperación de Contraseña CRM+');
        $this->email->message($htmlmail);

        //Guardar Token y fecha en que se esta solicitando la recuperacion
        $this->login_model->save_recovery_request($this->username, $token);

        return $this->email->send();
    }

    /*
     * Este formulario permitira al usuario recuperar
     * su contraseña, si cuenta con un token valido.
     */

    public function recover() {
        $data = array();

        $politicas = $this->usuarios->usuarios_model->seleccionar_politicas(); //Me retorna las politicas de Password
        $username = $this->input->get('usr', TRUE);
        $data['politicas'] = $politicas;

        $token = $this->input->get('token', TRUE);

        //Verificar si token existe
        $tokenINFO = $this->login_model->check_token($username, $token);

        if (!empty($tokenINFO)) {
            //Verificar si ya se expiro el tiempo (24 hrs)
            //Para restablecer la contrase�a
            $date1 = $tokenINFO[0]['recovery_time'];
            $date2 = date('Y-m-d h:i:s');
            $timestamp1 = strtotime($date1);
            $timestamp2 = strtotime($date2);
            $horasTranscurridas = number_format(abs($timestamp2 - $timestamp1) / (60 * 60), 0);

            if ($horasTranscurridas <= 7) {

                $data['username'] = $username;
                $data['token'] = $token;

                //Set validation rules
                $this->form_validation->set_rules('password1', 'Contrase&ntilde;a', 'required|trim|min_length[3]|max_length[12]');
                $this->form_validation->set_rules('password2', 'Contrase&ntilde;a', 'required|trim|matches[password1]');
                $this->form_validation->set_message('matches', 'Las contrase&ntilde;as no coinciden.');
                $this->form_validation->set_message('required', 'Campo requerido.');
                $this->form_validation->set_error_delimiters('<label class="required">', '</label>');

                if (!empty($_POST)) {
                    //Verificar si los campos requeridos no estan vacios
                    if ($this->form_validation->run()) {

                        $response = $this->login_model->update_password($username);

                        if ($response == true) {

                            //Remover el token y la fecha
                            $this->login_model->reset_recover_request($username);

                            //Redireccionar para la pagina de Inicio
                            $mensaje = array('clase' => 'alert-success', 'contenido' => 'La contrase&ntilde;a fue Actualizada.');
                            $this->session->set_flashdata('mensaje', $mensaje);
                            redirect('/login');
                        } else {
                            $mensaje = array('clase' => 'alert-danger', 'contenido' => 'Hubo un error tratando de cambiar la contrase&ntilde;a. Esta contraseña ya ha sido usado en las últimas 10 veces.');
                            $this->session->set_flashdata('mensaje', $mensaje);
                            redirect('/login');
                        }
                    }
                }
            } else {

                //Remover el token y la fecha
                //Para que el usuaro ya no pueda hacer
                //uso de este token.
                $this->login_model->reset_recover_request($username);

                //Redireccionar para la pagina de Inicio
                redirect('/');
            }
        } else {
            //Redireccionar para la pagina de Inicio
            $mensaje = array('clase' => 'alert-danger', 'contenido' => 'Hubo un error tratando de cambiar la contrase&ntilde;a. Esta contraseña ya ha sido usado en las últimas 10 veces.');
            $this->session->set_flashdata('mensaje', $mensaje);
            redirect('/');
        }
        $this->assets->agregar_js(array(
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/plugins/jquery/jqgrid/plugins/jQuery.jqGrid.columnToggle.js',
            'public/assets/js/plugins/jquery/switchery.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/modules/login/recover.js'
        ));
        $this->template->agregar_titulo_header('Recuperar Contrase&ntilde;a');
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    function crear_cuenta() {
        $data = array();

        $this->form_validation->set_rules('nombre', 'Nombre', 'trim|required|min_length[3]|max_length[25]');
        $this->form_validation->set_rules('apellido', 'Apellido', 'trim|required|min_length[3]|max_length[25]');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|min_length[3]|max_length[50]|valid_email|is_unique[usuarios.email]');
        $this->form_validation->set_rules('password', 'Password', 'required|trim|required|min_length[3]|max_length[25]');
        $this->form_validation->set_rules('repetir_password', 'Password Confirmation', 'required|matches[password]');

        $this->assets->agregar_css(array('public/assets/css/modules/stylesheets/login.css'));
        $this->template->agregar_titulo_header('Crear Cuenta');
        $this->template->agregar_contenido($data);
        if ($this->form_validation->run() == FALSE) {
            $this->template->visualizar();
        } else {
            $datos = [
                'nombre' => $this->input->post('nombre'),
                'apellido' => $this->input->post('apellido'),
                'email' => $this->input->post('email'),
                'usuario' => $this->input->post('email'),
                'password' => $this->encrypt->encode($this->input->post('password')),
                'estado' => 'Pendiente',
                'recovery_token' => Util::generate_ramdom_token(),
                //'uuid_usuario' => 1,
                "last_login" => date('Y-m-d H:i:s'),
                "fecha_creacion" => date('Y-m-d H:i:s'),
                "last_login_ip_address" => $this->input->ip_address(),
                "ip_address" => $this->input->ip_address()
            ];

            try {
                Capsule::beginTransaction();

                $UsuariosRepository = new Flexio\Modulo\Usuarios\Repository\UsuariosRepository();
                $user = $UsuariosRepository->create($datos);
                $user->roles()->attach(2, array('empresa_id' => 0));

                Capsule::commit();
            } catch (Illuminate\Database\QueryException $e) {
                 Capsule::rollBack();
            }

            $data = array();
            if ($user) {
                $this->send_crear_cuentaEmail($user->id);
                $mensaje = array('clase' => 'alert-success', 'contenido' => 'Te hemos enviado un correo electronico con las instrucciones.');
                $this->session->set_flashdata('mensaje', $mensaje);
            } else {
                $mensaje = array('clase' => 'alert-danger', 'contenido' => 'Especificar Error.');
                $this->session->set_flashdata('mensaje', $mensaje);
            }
            //$this->template->visualizar($data);
            redirect('login');
        }
    }

    function send_crear_cuentaEmail($userid) {
        $usuario = Login_orm::find($userid);

        if (!empty($usuario)) {
            $url = base_url('login/activar_usuarios/?email=' . $usuario->email . '&token=' . $usuario->recovery_token);

            $filepath = realpath('./public/templates/email/crear_cuenta/crear_cuenta.html');

            //Verificar primero si el template de recuperar password existe
            if (!file_exists($filepath)) {
                //No se encontro el archivo ...
                log_message("error", "MODULO: Login --> No se encontro la plantilla de crear cuenta");
                return false;
            }

            //Leer archivo html que contiene el texto del correo
            $htmlmail = read_file($filepath);

            //Reemplazar valores en el archivo html
            //__SITE_URL__
            //__URL_PASSWORD_RECOVER__
            //__YEAR__
            $htmlmail = str_replace("__SITE_URL__", base_url('/'), $htmlmail);
            $htmlmail = str_replace("__URL_CREAR_CUENTA__", $url, $htmlmail);
            $htmlmail = str_replace("__YEAR__", date('Y'), $htmlmail);

            //Enviar el correo
            $this->email->from('no-reply@pensanomica.com', 'Flexio');
            $this->email->to($usuario->email);
            $this->email->subject('Creación de cuenta Flexio');
            $this->email->message($htmlmail);

            return $this->email->send();
        } else {
            return false;
        }
    }

    function activar_usuarios(){
        $email = $this->input->get('email', TRUE);
        $token = $this->input->get('token', TRUE);
        $condicion = array('email' => $email, 'recovery_token' => $token, 'estado' => 'Pendiente');
        //$usuario = Login_orm::validar_token($condicion);
        $usuario = (new ActivarUsuario)->activar($condicion);

        if (is_null($usuario)) {
            $mensaje = "su cuenta ha sido desactivada o no existe en el sistema";
            $mensaje = array('clase' => 'alert-danger', 'contenido' => $mensaje);
            $this->session->set_flashdata('mensaje', $mensaje);
            //Establecer el mensaje cuando no existe ese usuario
            redirect('login');
        }
        $mensaje = "su cuenta se encuenta activa";
        $mensaje = array('clase' => 'alert-success', 'contenido' => $mensaje);
        $this->session->set_flashdata('mensaje', $mensaje);
        redirect('login');
    }

}

?>

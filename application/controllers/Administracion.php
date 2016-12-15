<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

use Flexio\Modulo\Usuarios\Repository\UsuariosRepository;
use Flexio\Library\Util\AuthUser;
use Flexio\Modulo\Usuarios\FormRequest;

class Administracion extends CRM_Controller{

  protected $usuarioRepository;
  protected $uuidUsuario;

  function __construct() {
    parent::__construct();
    $this->load->library(array('encrypt'));
    $this->usuarioRepository = new UsuariosRepository;
    $this->uuidUsuario = $this->session->userdata('huuid_usuario');
  }

  function perfil(){

    $mensaje="";
    $this->template->agregar_titulo_header('Perfil: Usuario');
    $this->template->agregar_breadcrumb(array(
            "titulo" => '<i class="fa fa-user"></i> Perfil: Usuario',
    ));
    $this->_js();

    if (!is_null($this->session->flashdata('mensaje'))) {
      $mensaje = collect($this->session->flashdata('mensaje'));
    }

    $this->assets->agregar_var_js(array(
            "toast_mensaje" => $mensaje,
    ));
    $usuario = AuthUser::getUser();
    $data = array('usuario'=>$usuario);
    $this->template->agregar_contenido($data);
    $this->template->visualizar();
  }

  function cambiar_password(){
    $mensaje="";
    $this->template->agregar_titulo_header('Perfil: Usuario');
    $this->template->agregar_breadcrumb(array(
            "titulo" => '<i class="fa fa-user"></i> Perfil: Usuario',
    ));
    $this->_js();

    if (!is_null($this->session->flashdata('mensaje'))) {
      $mensaje = collect($this->session->flashdata('mensaje'));
    }

    $this->assets->agregar_var_js(array(
            "toast_mensaje" => $mensaje,
    ));
    $usuario = AuthUser::getUser();
    $data = array('usuario'=>$usuario);
    $this->template->agregar_contenido($data);
    $this->template->visualizar();
  }

  function guardar_password(){
    $usuario = AuthUser::getUser();
    $current_password = $this->input->post('campo[current_password]');
    $password = $this->input->post('campo[current_password]');
    $this->encrypt->set_cipher(MCRYPT_RIJNDAEL_256);
    if($this->encrypt->decode($usuario->password) != $current_password) {
      $mensaje = array('titulo' =>'cambiar contrase&ntilde;a','tipo' => 'error', 'mensaje' => '<b>¡Error!</b> Las Contrase&ntilde;a no coinciden con la anterior');
      $this->session->set_flashdata('mensaje', $mensaje);
      redirect(base_url('administracion/cambiar_password'));
    }
    $password = $this->encrypt->encode($password);
    $usuario->password = $password;
    $usuario->save();

    $mensaje = array('titulo' =>'cambiar contrase&ntilde;a','tipo' => 'success', 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente');
    $this->session->set_flashdata('mensaje', $mensaje);
    redirect(base_url('administracion/perfil'));

  }

  function guardar_perfil(){

    $perfilGuardar =  new Flexio\Modulo\Usuarios\FormRequest\ActualizarPerfil;

    $usuario = $perfilGuardar->guardar();
    if(!is_null($usuario)){
    $mensaje = array('titulo' =>'perfil','tipo' => 'success', 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente ');
    $this->session->set_flashdata('mensaje', $mensaje);
    redirect(base_url('administracion/perfil'));
    }
  }


  private function _js(){
    $this->assets->agregar_js(array(
      'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
      'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
      'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
      'public/resources/compile/bundle.js',
      'public/assets/js/modules/administracion/perfil_formulario.js'
    ));
  }


}

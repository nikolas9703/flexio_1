<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

use Flexio\Modulo\Comentario\Repository\ComentarioRepository;


use Carbon\Carbon as Carbon;
class Landing_page extends CRM_Controller{

  protected $comentario;

  function __construct(){
    Carbon::setLocale('es');
    setlocale(LC_TIME, 'es_ES.utf8');
    $this->comentario = new ComentarioRepository();
    error_reporting(E_ERROR);
  }

  function index(){

    try{
      $comentarios = $this->comentario->getComentariosForLandingPage();
    }catch(\Exception $e){
      $comentarios = collect(['errors'=>'Existe un modulo sin relacion a comentarios']);
    }

    $this->_css();
    $this->_js();
    $data=[];
    $data['nombre_usuario'] = $this->comentario->getUsuario();
    $this->assets->agregar_var_js(array(
      "flexio_mensaje" => Flexio\Library\Toast::getStoreFlashdata(),
      "landing" => $comentarios,
      "uuid_empresa"=> $this->session->userdata('uuid_empresa'),
      "uuid_usuario" => $this->session->userdata('huuid_usuario')
    ));
    $breadcrumb = array( "titulo" => '<i class="fa fa-comments-o"></i>');
    $this->template->agregar_titulo_header('Listado de Comentarios');
    $this->template->agregar_breadcrumb($breadcrumb);
    $this->template->agregar_contenido($data);
    $this->template->visualizar($breadcrumb);
  }

  function guardar(){
      try{
          $comentario = new Flexio\Modulo\Comentario\HttpRequest\RequestGuardarComentario;
          $response = $comentario->guardar();
      }catch(\Exception $e){
          //enviar error
          log_message('error', __METHOD__." -> Linea: ". __LINE__ ." --> ". $e->getMessage()."\r\n");
          $response=['errors'=>'lo sentimos no se puedo enviar el comentario'];
      }
      $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
          ->set_output(json_encode($response))->_display();
      exit;
  }

  private function _js(){
    $this->assets->agregar_js(array(
      'public/assets/js/default/jquery-ui.min.js',
      'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
      'public/assets/js/modules/landing_page/componente.texto_comentario.js',
      'public/assets/js/modules/landing_page/componente.paginador.js',
      'public/assets/js/modules/landing_page/componente.comentario_modulo.js',
      'public/assets/js/modules/landing_page/componente.landing.js',
      'public/assets/js/modules/landing_page/vue.landing_page.js',
    ));
  }

  private function _css(){
    $this->assets->agregar_css(array(
      'public/assets/css/default/ui/base/jquery-ui.css',
      'public/assets/css/default/ui/base/jquery-ui.theme.css',
      'public/assets/css/modules/stylesheets/landing_page.css'
    ));
  }
}

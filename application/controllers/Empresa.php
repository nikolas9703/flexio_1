<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
use Flexio\Modulo\Usuarios\Repository\UsuariosRepository;

class Empresa extends CRM_Controller
{
  	private $cache;
   private $usuarioRepo;
    function __construct(){
		parent::__construct();
      $this->load->model('usuarios/empresa_orm');
      $this->cache = Cache::inicializar();
      $this->usuarioRepo = new UsuariosRepository;
    }

    public function lista()
    {
      $uuid_usuario = $this->session->userdata('huuid_usuario');
      $uuid_empresa = $this->session->userdata('uuid_empresa');
      
      if(empty($uuid_usuario) || empty($uuid_empresa)){
         $this->server_response([]);
      }
      $lista_empresa = $this->cache->get("menuListaEmpresa-". $uuid_usuario.'-'. $uuid_empresa);

      if(is_null($lista_empresa)){
         $lista_empresa = [];
         $usuario = $this->usuarioRepo->findByUuid($uuid_usuario);
         $usuarioEmpresas = $usuario->empresas;
         foreach ($usuarioEmpresas as $lempresa) {
           array_push($lista_empresa,array('uuid_empresa'=>$lempresa->uuid_empresa,'nombre'=>$lempresa->nombre,'logo'=>$lempresa->logo));
         }
         $this->cache->set("menuListaEmpresa-". $uuid_usuario.'-'. $uuid_empresa, $lista_empresa,3600);
      }

      $default_empresa = $this->cache->get("empresaDefault-". $uuid_usuario.'-'. $uuid_empresa);
      if(is_null($default_empresa)){
          $buscarEmpresa = Empresa_orm::findByUuid($uuid_empresa);//1
          $default_empresa = ['uuid_empresa'=>$buscarEmpresa->uuid_empresa,'nombre'=>$buscarEmpresa->nombre,'logo'=>$buscarEmpresa->logo];
          $this->cache->set("empresaDefault-". $uuid_usuario.'-'. $uuid_empresa, $default_empresa,3600);
      }

      $response = array('lista' => empty($lista_empresa)?NULL:$lista_empresa, 'default' => empty($default_empresa)?NULL:$default_empresa);
      $this->server_response($response);
    }

    public function cambio()
    {
        $data = array();
        $uuid_empresa = $this->input->post('uuid_empresa');
        $data['uuid_empresa'] = $uuid_empresa;
        $this->session->set_userdata($data);
        $this->cache->delete("usuario-roles-". $this->session->userdata('huuid_usuario'));

    }

    function server_response($response){
      $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response))->_display();
    	exit;
   }
}

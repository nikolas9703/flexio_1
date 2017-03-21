<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
use Flexio\Modulo\Busqueda\Repository\BusquedaRepository;
class Busqueda extends CRM_Controller{
  protected $BusquedaRepository;
  function __construct() {
    parent::__construct();
    $this->BusquedaRepository = new BusquedaRepository;
   }
  public function ajax_get_variables()
  {
        if(!$this->input->is_ajax_request()){
            return false;
        }
        $busqueda_id   = $this->input->post('id');
        try{
          $response = $this->BusquedaRepository->find($busqueda_id);
        }catch(\Exception $e){
            //enviar error
            log_message('error', __METHOD__." -> Linea: ". __LINE__ ." --> ". $e->getMessage()."\r\n");
            $response=['errors'=>'lo sentimos no se pudo encontrar el buscador'];
        }
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        	->set_output(json_encode($response))->_display();
        	exit;
  }
  public function ajax_borrar_variables(){
       if(!$this->input->is_ajax_request()){
          return false;
      }
      try{
        $post = $post = $this->input->post();
        $response = $this->BusquedaRepository->borrar($post['id']);
      }catch(\Exception $e){
        echo json_encode(array(
            "response" => false,
        ));
        exit;
      }
      echo json_encode(array(
          "response" => true,
      ));
      exit;
  }

  public function ajax_guardar_variables()
  {
        if(!$this->input->is_ajax_request()){
            return false;
        }
        try{
          $post = $post = $this->input->post();
          $response = $this->BusquedaRepository->create($post);
        }catch(\Exception $e){
          echo json_encode(array(
              "response" => false,
          ));
          exit;
        }
        echo json_encode(array(
  					"response" => true,
   			));
  			exit;
  }
}

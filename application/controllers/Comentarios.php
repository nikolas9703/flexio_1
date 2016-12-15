<?php if (!defined('BASEPATH')) exit('No direct script access allowed');



class Comentarios extends CRM_Controller{
  function __construct() {
    parent::__construct();


  }

  public function ajax_guardar()
  {
        if(!$this->input->is_ajax_request()){
            return false;
        }
        $campos = [];
        $model_id   = $this->input->post('modelId');
        $modelo   = $this->input->post('modelo');
        $comentario = $this->input->post('comentario');
        $usuario_id = $this->session->userdata('id_usuario');

        $campos['usuario_id'] = $usuario_id;
        $campos['comentario'] = $comentario;
        $campos['comentable_id'] = $model_id;
        $campos['comentable_type'] = $modelo;

        $guardarComentario = new Flexio\Modulo\Comentario\HttpRequest\RequestGuardarComentario;
        try{
          $response = $guardarComentario->agregar_comentario($campos);

        }catch(\Exception $e){
            //enviar error
            log_message('error', __METHOD__." -> Linea: ". __LINE__ ." --> ". $e->getMessage()."\r\n");
            $response=['errors'=>'lo sentimos no se puedo enviar el comentario'];
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        	->set_output(json_encode($response))->_display();
        	exit;
  }
}

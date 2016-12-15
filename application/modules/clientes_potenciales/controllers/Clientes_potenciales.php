<?php

/**
 * Clienctes Potenciales
 *
 * Modulo de clientes potenciales
 *
 * @package    PensaApp
 * @subpackage Controller
 * @category   Controllers
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 *
 */
use League\Csv\Writer as Writer;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\FormularioDocumentos AS FormularioDocumentos;

//repositorios
use Flexio\Modulo\ClientesPotenciales\Repository\ClientesPotencialesRepository;
use Flexio\Modulo\ClientesPotenciales\Repository\ClientesPotencialesCatRepository;
use Flexio\Modulo\Cliente\Repository\ClienteRepository;

class Clientes_potenciales extends CRM_Controller {

    private $id_empresa;
    private $id_usuario;
    private $empresaObj;

    protected $ClientesPotencialesRepository;
    protected $ClientesPotencialesCatRepository;
    protected $ClienteRepository;

    function __construct() {
        parent::__construct();
        $this->load->model('usuarios/Usuario_orm');
        $this->load->model('clientes_potenciales_orm');
        $this->load->model('Catalogo_toma_contacto_orm');
//Cargar Clase Util de Base de Datos
        $this->load->dbutil();
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresaObj = new Buscar(new Empresa_orm, 'uuid_empresa');
        $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
        $this->id_usuario = $this->session->userdata("id_usuario");
//    echo $this->id_usuario;
        $this->id_empresa = $this->empresaObj->id;
        $this->ClienteRepository = new ClienteRepository;

//HMVC Load Modules
        $this->load->module(array('contactos'));


        //repositorios
        $this->ClientesPotencialesRepository    = new ClientesPotencialesRepository();
        $this->ClientesPotencialesCatRepository = new ClientesPotencialesCatRepository();
    }

    public function listar_clientes_potenciales() {

        $data = array();
        $mensaje = array();


        $this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
            'public/assets/css/plugins/jquery/switchery.min.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
            'public/assets/css/plugins/jquery/fileinput/fileinput.css',
            'public/assets/css/modules/stylesheets/clientes_potenciales.css',
            //'public/assets/css/plugins/jquery/toastr.min.css',
        ));
        $this->assets->agregar_js(array(
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/plugins/jquery/jqgrid/plugins/jQuery.jqGrid.columnToggle.js',
            'public/assets/js/plugins/jquery/switchery.min.js',
            'public/assets/js/moment-with-locales-290.js',
            /*'public/assets/js/plugins/jquery/fileinput/fileinput.js',
            'public/assets/js/plugins/jquery/fileinput/fileinput_locale_es.js',*/
          //  'public/assets/js/default/toast.controller.js',
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/plugins/bootstrap/bootstrap-tabdrop.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
        //    'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
          //  'public/assets/js/default/formulario.js',
            'public/assets/js/modules/clientes_potenciales/listar_clientes_potenciales.js',
              'public/assets/js/default/toast.controller.js'
        ));

        $breadcrumb = array(
            "titulo" => '<i class="fa fa-line-chart"></i> Clientes Potenciales',
            "ruta" => array(
                0 => array(
                    "nombre" => "Ventas",
                    "activo" => false
                ),
                1 => array(
                    "nombre" => '<b>Clientes Potenciales</b>',
                    "activo" => true
                )
            ),
            "filtro" => false,
            "menu" => array(
                "nombre" => "Crear",
                "url" => "clientes_potenciales/crear",
                "opciones" => array()
            )
        );

        if(!is_null($this->session->flashdata('mensaje'))){
        		$mensaje = json_encode($this->session->flashdata('mensaje'));
       	}else{
        		$mensaje = '';
       	}
        $this->assets->agregar_var_js(array(
            "toast_mensaje" => $mensaje
        ));

        if ($this->auth->has_permission('acceso', 'clientes_potenciales/crear')) {
            $breadcrumb["menu"] = array(
                "url" => 'clientes_potenciales/crear',
                "nombre" => "Crear"
            );

            $menuOpciones["#eliminarClientePotencialBtn"] = "Eliminar";
            $menuOpciones["#exportarClientePotencialBtn"] = "Exportar";
        }

        $breadcrumb["menu"]["opciones"] = $menuOpciones;

        $this->template->agregar_titulo_header('Clientes Potenciales');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }

    public function ajax_listar_clientes_potenciales() {

         if(!$this->input->is_ajax_request()){return false;}

         $clause['empresa_id']   = $this->id_empresa;

        $nombre      = $this->input->post('nombre',TRUE);
        $telefono      = $this->input->post('telefono',TRUE);
        $correo      = $this->input->post('correo',TRUE);

        $clause['nombre']   = $nombre;
        $clause['telefono']   = $telefono;
        $clause['correo']     = $correo;

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = $this->ClientesPotencialesRepository->count($clause);

        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $registros = $this->ClientesPotencialesRepository->get($clause ,$sidx, $sord, $limit, $start);



        $response          = new stdClass();
        $response->page    = $page;
        $response->total   = $total_pages;
        $response->records = $count;

        if ( count($registros)) {
             foreach ($registros as $i => $row){
                 $correos =  !empty($row->correos_asignados->toArray())?$row->correos_asignados->toArray():[];
                $telefonos =  !empty($row->telefonos_asignados->toArray())?$row->telefonos_asignados->toArray():[];

                $_correos =  array_map(function($correos){ return $correos["correo"]; }, $correos) ;
	              $_telefonos =  array_map(function($telefonos){ return $telefonos["telefono"]; }, $telefonos) ;

                $uuid_cliente_potencial = bin2hex($row->uuid_cliente_potencial);

                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-rol="' . $row["id_cliente_potencial"] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $hidden_options = "";

                 $hidden_options .= '<a href="'.$row->enlace.'" data-id="' . $row->id_cliente_potencial . '" class="exportarTablaCliente btn btn-block btn-outline btn-success">Ver Detalle</a>';
                //$hidden_options .= '<a href="#" data-id="' . $row->id_cliente_potencial . '" class="exportarTablaCliente btn btn-block btn-outline btn-success" id="convertirACliente">Convertir a Cliente</a>';
                $hidden_options .= '<a href="'. base_url('clientes/crear/'. $row->uuid_cliente_potencial) .'"   class="btn btn-block btn-outline btn-success">Convertir a Cliente</a>';
                $hidden_options .= '<a href="#" data-id="' . $row->id_cliente_potencial . '" class="eliminarClientePotencialBtn btn btn-block btn-outline btn-success">Eliminar</a>';

                $response->rows[$i]["id"] = $row['id_cliente_potencial'];
                $response->rows[$i]["cell"] = array(

                    $row->numero_documento_enlace,
                    $_telefonos,
                    $_correos,
                    isset($row->toma_contacto->etiqueta)?$row->toma_contacto->etiqueta:'',
                    $link_option,
                    $hidden_options
                );
                $i++;
            }
        }
         echo json_encode($response);
        exit;
     }

    /**
     * Cargar Vista de Tabla
     *
     * @return void
     */
    public function ocultotabla() {
//If ajax request
        $this->assets->agregar_js(array(
            'public/assets/js/modules/clientes_potenciales/tabla.js'
        ));

        $this->load->view('tabla');
    }

    /**
     * Cargar Vista de Grid
     *
     * @return void
     */
    public function grid() {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/clientes_potenciales/grid.js'
        ));

        $data = array(
            "clientes" => $this->input->post("clientes")
        );

        $this->load->view('grid', $data);
    }

    /**
     * Pantalla de Crear Cliente Potencial
     *
     * @return void
     */
    public function crear_cliente_potencial() {
        $data = array();
        $mensaje = array();
        $empresa_id = $this->empresaObj->id;

// Verificar si tiene permiso de crear
        if (!$this->auth->has_permission('crear-cliente-potencial__acceso', 'clientes_potenciales/crear')) {
            redirect("/");
        }

        if (!empty($_POST)) {
            $response = clientes_potenciales_orm::guardar_cliente_potencial($empresa_id);

            if ($response == true) {
                redirect(base_url('clientes_potenciales/listar'));
            }
        }

//Introducir mensaje de error al arreglo
//para mostrarlo en caso de haber error
        $data["message"] = $mensaje;
        $this->_addMainCss();
        $this->_addMainJS();
        $this->assets->agregar_css(array(
            'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
            'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
            'public/assets/css/modules/stylesheets/clientes_potenciales.css',
         ));
        $this->assets->agregar_js(array(
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/default/lodash.min.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/default/formulario.js'
        ));

        $data['info']['toma_contacto'] =  $this->ClienteRepository->getTomaContacto();
        $this->template->agregar_titulo_header('Clientes Potenciales');
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-line-chart"></i> Clientes Potenciales: Crear',
            "ruta" => array(
                0 => array(
                    "nombre" => "Ventas",
                    "activo" => false
                ),
                1 => array(
                    "nombre" => 'Clientes Potenciales',
                    "url" => 'clientes_potenciales/listar',
                    "activo" => false
                ),
                2 => array(
                    "nombre" => '<b>Crear</b>',
                    "activo" => true
                )
            )
//   )
        );

        $this->assets->agregar_var_js(array(
             "vista"=>"crear"
         ));

        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    private function _addMainCss() {
        //main
        $this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/plugins/jquery/switchery.min.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
            'public/assets/css/plugins/bootstrap/bootstrap-tagsinput.css',
            'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
            'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
            'public/assets/css/plugins/jquery/fileinput/fileinput.css',
            'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
            'public/assets/css/plugins/bootstrap/select2.min.css',
        ));
    }

    private function _addMainJS() {
        $this->assets->agregar_js(array(
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/plugins/bootstrap/bootstrap-tabdrop.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/combodate/momentjs.js',//required by datetimepicker - moment.js
            'public/assets/js/moment-with-locales-290.js',//required by datetimepicker - moment.locale.js
            'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
            'public/assets/js/default/formulario.js',
            'public/assets/js/plugins/bootstrap/select2/select2.min.js',
            'public/assets/js/plugins/bootstrap/select2/es.js',
            'public/assets/js/default/vue/directives/select2.js'
        ));
    }

    public function ocultoformulario($data = array()) {


        $this->assets->agregar_js(array(
            //'public/assets/js/modules/clientes_potenciales/formulario.js',
            'public/assets/js/modules/clientes_potenciales/vue.correo-cliente.js',
            'public/assets/js/modules/clientes_potenciales/vue.telefono-cliente.js',
            'public/assets/js/modules/clientes_potenciales/crear_cliente_potencial.js'
        ));

        $data['toma_contactos'] = $this->ClienteRepository->getTomaContacto();

        // $data['toma_contactos'] = $this->ClientesPotencialesCatRepository->get(['id_campo'=>'4'], 'etiqueta', 'asc');

        $this->load->view('formulario', $data);
    }

    public function guardar() {

         $post = $this->input->post();
        if(!empty($post))
    	{

           $response = false;
            Capsule::transaction(
                function() use (&$response, $post)
                {
                    if(!empty($post["campo"]["id_cliente_potencial"]))
                    {
                        $cliente_potencial = $this->ClientesPotencialesRepository->findBy(['empresa_id'=>$this->id_empresa,'id_cliente_potencial'=>$post["campo"]["id_cliente_potencial"]]);
                        //$response = $this->ClientesPotencialesRepository->save($registro, $post);
                        $response = $this->ClientesPotencialesRepository->guardar($post, $cliente_potencial);
                    }
                    else{ //Creando un nuevo cliente potencial
                     $post['campo']['creado_por']=$this->id_usuario;
                     $post['campo']['empresa_id']=$this->id_empresa;

                      $response = $this->ClientesPotencialesRepository->guardar($post);
                    }

                  }
            );

            if ($response) {

                $mensaje = array('estado' => 200,'clase' => 'alert-success', 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente');

            } else {
                  $mensaje = array('estado' => 500,'clase' => 'alert-danger', 'mensaje' => '<strong>¡Error!</strong> Su solicitud no fue procesada');
            }

            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('clientes_potenciales/listar'));


    	}
    }


    public function editar($uuid=NULL, $data = []) {
         //obtengo los CSS y JavaScript necesarios para la vista
        $this->_addMainCSS();
        $this->_addMainJS();
        $this->assets->agregar_js(array(
        		'public/assets/js/default/vue.js',
        		'public/assets/js/default/vue-validator.min.js',
        		'public/assets/js/default/vue-resource.min.js',
            'public/assets/js/plugins/jquery/combodate/combodate.js',
            'public/assets/js/default/tabla-dinamica.jquery.js'
    	));

        $cliente = $this->ClientesPotencialesRepository->findBy(['empresa_id'=>$this->id_empresa,'uuid_cliente_potencial'=>$uuid]);
        $cliente->load('comentario_timeline','telefonos_asignados','correos_asignados');
          $this->assets->agregar_var_js(array(
        		"acceso" => 1,
        		"vista" => "ver",
         		"lista_correo" => $cliente->correos_asignados,
         		"lista_telefonos" => $cliente->telefonos_asignados,
         		"cliente" => $cliente
        ));


        $data["campos"]["campos"] = $cliente;


        $breadcrumb = array(
            "titulo" => '<i class="fa fa-line-chart"></i> Clientes Potenciales: '.$cliente->nombre,
            "ruta" => array(
                0 => array(
                    "nombre" => "Ventas",
                    "activo" => false
                ),
                1 => array(
                    "nombre" => 'Clientes Potenciales',
                    "url" => 'clientes_potenciales/listar',
                    "activo" => false
                ),
                2 => array(
                    "nombre" => '<b>Detalle</b>',
                    "activo" => true
                )
            )
      //   )
        );

      $this->template->agregar_titulo_header('Clientes Potenciales');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar();
    }

    /**
     * Funcion para exportar los clientes potenciales
     * seleccionados a formato CSV.
     *
     * return void
     */
    public function exportar() {

        if (empty($_POST)) {
            exit();
        }

        $ids = $this->input->post('ids', true);
         $id_clientes = explode(",", $ids);
         if (empty($id_clientes)) {
            return false;
        }
        $clause = array(
            "id_cliente_potencial" => $id_clientes
        );

        $clientes = clientes_potenciales_orm::lista($clause)->toArray();
         if (empty($clientes)) {
            return false;
        }

        $i = 0;
        foreach ($clientes as $row) {

            $datos_excel[$i]['nombre'] = Util::verificar_valor($row['nombre']);
            $datos_excel[$i]['telefono'] = $row['telefono'];
            $datos_excel[$i]['correo'] = $row['correo'];
            $datos_excel[$i]['toma_contacto'] = utf8_decode(Util::verificar_valor($row['toma_contacto']['etiqueta']));
            $i++;
        }
        $csv = Writer::createFromFileObject(new SplTempFileObject());
        $csv->insertOne([
            'Nombre',
            'Telefono',
            'Correo',
            'Toma Contacto'
        ]);
        $csv->insertAll($datos_excel);
        $csv->output("ClientePotencial-" . date('ymd') . ".csv");
        exit;
    }

    /**
     * Funcion para eliminar uno o varios
     * clientes potenciales seleccionados.
     *
     * @return array
     */
    function eliminar() {

        $id_clientes = $this->input->post('id_clientes', true);
        $element = $id_clientes[0];
        // dd($element);
        $idss = explode(",", $element);
        // dd($idss);
        if (empty($idss)) {
            return false;
        }
        $clause = array(
            "id" => $idss
        );
        if (!$this->input->is_ajax_request()) {
            return false;
        }
        // dd($id_clientes);
        $response = clientes_potenciales_orm::eliminar($idss);
        //dd($response);
        $json = '{"results":[' . json_encode($response) . ']}';
        echo $json;
        exit;
    }

    /**
     * Funcion para convertir uno o varios
     * clientes potenciales seleccionados.
     *
     * @return array
     */
    function ajax_convertir_juridico() {
//Just Allow ajax request
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        $response = clientes_potenciales_orm::convertir_juridico();

        $json = '{"results":[' . json_encode($response) . ']}';
        echo $json;
        exit;
    }

    public function ajax_eliminar() {

        if (!$this->input->is_ajax_request()) {return false;}

        $clause                 = $this->input->post();
        $clause['empresa_id']   = $this->id_empresa;

        $response = [
            'mensaje' => [
                'tipo'  => $this->ClientesPotencialesRepository->delete($clause) ? 'success' : 'error'
            ]
        ];

        $json = json_encode($response);
        echo $json;
        exit;
    }

    /**
     * Funcion para convertir uno o varios
     * clientes potenciales seleccionados.
     *
     * @return array
     */
    function ajax_convertir_natural() {
//Just Allow ajax request
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        $response = clientes_potenciales_orm::convertir_natural();

        $json = '{"results":[' . json_encode($response) . ']}';
        echo $json;
        exit;
    }

    /**
     * Seleccionar la informacion de
     * un cliente Potencial.
     *
     * @return boolean
     */
    function ajax_seleccionar_cliente_potencial() {
//Just Allow ajax request
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        $response = clientes_potenciales_orm::seleccionar_informacion_de_cliente_potencial();

        $json = '{"results":[' . json_encode($response) . ']}';
        echo $json;
        exit;
    }
    function ocultoformulariocomentarios() {

    	$data = array();

    	$this->assets->agregar_js(array(
    			'public/assets/js/plugins/ckeditor/ckeditor.js',
    			'public/assets/js/plugins/ckeditor/adapters/jquery.js',
    			'public/assets/js/modules/clientes_potenciales/vue.comentario.js',
    			'public/assets/js/modules/clientes_potenciales/formulario_comentario.js'
    	));

    	$this->load->view('formulario_comentarios');
    	$this->load->view('comentarios');
    }

    function ajax_guardar_comentario() {

    	if(!$this->input->is_ajax_request()){
    		return false;
    	}

    	$model_id   = $this->input->post('modelId');
    	$comentario = $this->input->post('comentario');
     	$comentario = ['comentario'=>$comentario,'usuario_id'=>$this->id_usuario];

    	$cliente = $this->ClientesPotencialesRepository->agregarComentario($model_id, $comentario);
    	$cliente->load('comentario_timeline');


    		$this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
    		->set_output(json_encode($cliente->comentario_timeline->toArray()))->_display();
    		exit;

    }
}

?>

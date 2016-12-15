<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Colaboradores
 * 
 * Modulo para administrar la creacion, edicion de solicitudes.
 *
 * @package    PensaApp
 * @subpackage Controller
 * @category   Controllers
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  05/22/2015
 */

use Illuminate\Database\Capsule\Manager as Capsule;
use League\Csv\Writer as Writer;
use Carbon\Carbon;
use Flexio\Modulo\aseguradoras\Repository\AseguradorasRepository as AseguradorasRepository;
use Flexio\Modulo\aseguradoras\Models\Aseguradoras as AseguradorasModel;
use Flexio\Modulo\aseguradoras\Models\Aseguradoras_orm as Aseguradoras_ormModel;

class aseguradoras extends CRM_Controller
{
	/**
	 * @var int
	 */
	protected $usuario_id;
	
	/**
	 * @var int
	 */
	protected $empresa_id;
	
	/**
	 * @var int
	 */
	protected $modulo_id;
	
	/**
	 * @var string
	 */
	protected $nombre_modulo;
	
    protected $SegurosAseguradorasRepository;    

	/**
	 * @var string
	 */
	protected $upload_folder = './public/uploads/';
	
	function __construct() {
        parent::__construct();
		
        //$this->load->model('Planes_orm');
        //$this->load->model('Coberturas_orm');
        $this->load->model('usuarios/usuario_orm');
        
        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();
        
        //Obtener el id de usuario de session
        $uuid_usuario = $this->session->userdata('huuid_usuario');
        $usuario = Usuario_orm::findByUuid($uuid_usuario);
        
        $this->usuario_id = $usuario->id;
         
        //Obtener el id_empresa de session
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $this->empresa_id = $empresa->id;
		
		$this->AseguradorasRepository = new AseguradorasRepository();
}
public function listar() {
	
		$data = array();
    	
        $this->_Css();   
        $this->_js();
        
    	$this->assets->agregar_js(array(
        'public/assets/js/modules/aseguradoras/listar.js'
      ));
    	
    	
    	//defino mi mensaje
        if(!is_null($this->session->flashdata('mensaje'))){
        $mensaje = json_encode($this->session->flashdata('mensaje'));
        }else{
        $mensaje = '';
        }
        $this->assets->agregar_var_js(array(
        "toast_mensaje" => $mensaje
        ));
    	
    	//Verificar permisos para crear
    	$breadcrumb = array(
            "titulo" => '<i class="fa fa-archive"></i> Aseguradoras',
            "ruta" => array(
                0 => array("nombre" => "Seguros", "url" => "#",  "activo" => false),
                1 => array("nombre" => '<b>Aseguradoras</b>', "activo" => true)
            ),
            "filtro"    => false,
            "menu"      => array()
        );
        
        if ($this->auth->has_permission('acceso', 'aseguradoras/crear')){
            $breadcrumb["menu"] = array(
    		"url"	=> 'aseguradoras/crear',
    		"nombre" => "Crear"
    	);
			$menuOpciones["#cambiarEstadoLnk"] = "Cambiar Estado";
            $menuOpciones["#exportarSolicitudesLnk"] = "Exportar";
            $breadcrumb["menu"]["opciones"] = $menuOpciones;
        }
        
        //Menu para crear
        $clause = array('empresa_id' => $this->empresa_id);
		
        //$data['menu_crear'] = array('nombre'=>1); 
        /*//catalogo para buscador        
        $data['aseguradoras'] = Aseguradoras_orm::where($clause)->get();
        $data['tipo'] = Catalogo_tipo_poliza_orm::get();
        $data['usuarios'] = usuario_orm::where('estado', 'Activo')->get();
        /*$clause2['empresa_id'] = $this->empresa_id;*/        
        
    	$this->template->agregar_titulo_header('Listado de Aseguradoras');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar($breadcrumb);
		
}

public function crear() { 
    $acceso = 1;
    $mensaje = array();

    if(!$this->auth->has_permission('acceso')){
      // No, tiene permiso, redireccionarlo.
      $acceso = 0;
      $mensaje = array('estado'=>500, 'mensaje'=>' <b>Usted no cuenta con permiso para esta solicitud</b>','clase'=>'alert-danger');
    }

    $this->_Css();   
    $this->_js();
    $this->assets->agregar_js(array(       
     'public/assets/js/modules/seguros_aseguradoras/formulario.js',   
     'public/assets/js/modules/seguros_aseguradoras/crear.vue.js',
     'public/assets/js/modules/seguros_aseguradoras/component.vue.js',  
     'public/assets/js/modules/seguros_aseguradoras/plugins.js'   
    ));

      $data=array();      
      $this->assets->agregar_var_js(array(
        "vista" => 'crear',
        "acceso" => $acceso,
      ));
      
     
    $breadcrumb = array(
      "titulo" => '<i class="fa fa-archive"></i> Aseguradoras: Crear / ',
      "ruta" => array(
            0 => array("nombre" => "Seguros", "url" => "#",  "activo" => false),
            1 => array("nombre" => 'Aseguradoras',"url" => "seguros_aseguradoras/listar", "activo" => false),
            2 => array("nombre" => '<b>Crear</b>', "activo" => true)
        ),
      "filtro"    => false,
      "menu"      => array()
    );
    $data['mensaje'] = $mensaje;
    $this->template->agregar_titulo_header('Aseguradoras: Crear');
    $this->template->agregar_breadcrumb($breadcrumb);
    $this->template->agregar_contenido($data);
    $this->template->visualizar();    
    
}

function ocultoformulario() {
        $clause = array('empresa_id' => $this->empresa_id);        
        $this->assets->agregar_var_js(array(
        ));
        
        $this->load->view('formulario');
}

function guardar() {
    if($_POST){
    unset($_POST["campo"]["guardar"]);
    $campo = Util::set_fieldset("campo");    
    Capsule::beginTransaction();
    try {
    if(empty($campo['uuid'])){ 
    $campo["uuid_aseguradoras"] = Capsule::raw("ORDER_UUID(uuid())");
    $clause['empresa_id'] = $this->empresa_id;
    $total = $this->solicitudesRepository->listar($clause);
    $year = Carbon::now()->format('y');
    $codigo = Util::generar_codigo($_POST['codigo_ramo'] . "-" . $year , count($total) + 1);
    $campo["numero"] = $codigo;
    $campo["usuario_id"] = $this->session->userdata['id_usuario'];
    $campo["empresa_id"] = $this->empresa_id;    
    $date = Carbon::now();
    $date = $date->format('Y-m-d');
    $campo['fecha_creacion'] = $date;   
    $solicitudes = $this->solicitudesModel->create($campo); 
    }else{
    echo "hola mundo";
    }
    Capsule::commit();
    }catch(ValidationException $e){
    log_message('error', $e);
    Capsule::rollback();
    }
    if(!is_null($solicitudes)){    
        $mensaje = array('estado' => 200, 'mensaje' =>'<b>¡&Eacute;xito!</b> Se ha guardado correctamente'); 
  
    }else{
        $mensaje = array('class' =>'alert-danger', 'contenido' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
    }


    }else{
            $mensaje = array('class' =>'alert-warning', 'contenido' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
    }
    
    $this->session->set_flashdata('mensaje', $mensaje);
    redirect(base_url('solicitudes/listar'));
}

	public function ocultotabla() {
    	$this->assets->agregar_js(array(
    		'public/assets/js/modules/aseguradoras/tabla.js'
    	));
    	
    	$this->load->view('tabla');
    }

	public function ajax_listar($grid=NULL) {    	
    	$clause = array(
    		"empresa_id" =>  $this->empresa_id
    	);
    	$nombre 	= $this->input->post('nombre', true);
    	$ruc 	= $this->input->post('ruc', true);
    	$telefono 	= $this->input->post('telefono', true);
    	$email 		= $this->input->post('email', true);
    	$direccion    	= $this->input->post('direccion', true);
		$aseguradora 	= $this->input->post('aseguradora', true);
    	
    	if(!empty($nombre)){
    		$clause["nombre"] = array('LIKE', "%$nombre%");
    	}
    	if(!empty($ruc)){
    		$clause["ruc"] = array('LIKE', "%$ruc%");
    	}
    	if(!empty($telefono)){
    		$clause["telefono"] = array('LIKE', "%$telefono%");
    	}
    	if(!empty($email)){
    		$clause["email"] = array('LIKE', "%$email%");
    	}
    	if(!empty($direccion)){
    		$clause["direccion"] = array('LIKE', "%$direccion%");
    	}
		
		if(!empty($aseguradora)){
    		$clause["creado_por"] = $aseguradora;
    	}
       
    	list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
		
    	$count = $this->AseguradorasRepository->listar_aseguradoras($clause, NULL, NULL, NULL, NULL)->count();
    		
    	list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
    		 
    	$rows = $this->AseguradorasRepository->listar_aseguradoras($clause, $sidx, $sord, $limit, $start);
    	
    	//Constructing a JSON
    	$response = new stdClass();
    	$response->page     = $page;
    	$response->total    = $total_pages;
    	$response->records  = $count;
    	$response->result 	= array();
    	$i=0;
    
    	if(!empty($rows)){
    		foreach ($rows AS $i => $row){
            $uuid_aseguradora = bin2hex($row->uuid_aseguradora);
            $now = Carbon::now();
            $hidden_options = ""; 
            $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row->id .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

            $estado = "Pendiente";
            $estado_color = trim($estado) == "Pendiente" ? 'background-color:#F8AD46' : 'background-color: red';
            
            $response->rows[$i]["id"] = $row->id;
            $response->rows[$i]["cell"] = array(
                    '<a href="'. base_url('seguros_aseguradoras/ver/'. $uuid_aseguradora) .'" style="color:blue;">'. $row->nombre.'</a>',  
					$row->ruc,
					$row->telefono,
					$row->email,
					$row->direccion,
                    $link_option,
                    $hidden_options                   
            );
    $i++;
    		}
    	}
    	echo json_encode($response);
    	exit;
    } 

private function _js() {
    $this->assets->agregar_js(array(
        'public/assets/js/default/jquery-ui.min.js',
        'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
        'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
        'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
        'public/assets/js/default/jquery.inputmask.bundle.min.js',
        'public/assets/js/plugins/jquery/jquery.webui-popover.js',
        'public/assets/js/plugins/jquery/jquery.sticky.js',
        'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
        'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
        'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
        'public/assets/js/plugins/jquery/chosen.jquery.min.js',
        'public/assets/js/moment-with-locales-290.js',
        'public/assets/js/plugins/jquery/switchery.min.js',
        'public/assets/js/plugins/bootstrap/jquery.bootstrap-touchspin.js',
        'public/assets/js/plugins/bootstrap/daterangepicker.js',
        'public/assets/js/default/formulario.js',
        'public/assets/js/plugins/jquery/fileupload/jquery.fileupload.js',
        'public/assets/js/default/toast.controller.js',
        'public/assets/js/plugins/bootstrap/select2/select2.min.js',
        'public/assets/js/plugins/bootstrap/select2/es.js'
  ));
  }

  private function _css() {
    $this->assets->agregar_css(array(
        'public/assets/css/default/ui/base/jquery-ui.css',
        'public/assets/css/default/ui/base/jquery-ui.theme.css',
        'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
        'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
        'public/assets/css/plugins/jquery/switchery.min.css',
        'public/assets/css/plugins/jquery/chosen/chosen.min.css',
        'public/assets/css/plugins/bootstrap/bootstrap-tagsinput.css',
        'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
        'public/assets/css/plugins/jquery/fileinput/fileinput.css',
        'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
        'public/assets/css/plugins/bootstrap/awesome-bootstrap-checkbox.css',
        'public/assets/css/plugins/jquery/toastr.min.css',
        'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
        'public/assets/css/plugins/bootstrap/select2.min.css',
    ));
  }    
}
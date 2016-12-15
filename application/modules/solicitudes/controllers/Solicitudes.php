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
use Flexio\Modulo\Cliente\Repository\ClienteRepository as clienteRepository;
use Flexio\Modulo\Solicitudes\Repository\SolicitudesRepository as solicitudesRepository;
use Flexio\Modulo\Cliente\Models\Cliente as clienteModel;
use Flexio\Modulo\Contabilidad\Models\Impuestos as impuestosModel;
use Flexio\Modulo\Solicitudes\Models\Solicitudes as solicitudesModel;
use Flexio\Modulo\CentroFacturable\Models\CentroFacturable as centroModel;
use Flexio\Modulo\CentroFacturable\Repository\CentroFacturableRepository as centroRepository;

class Solicitudes extends CRM_Controller
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
	
    private $acreedoresRep;
    private $clienteModel;
    private $impuestosModel;
    private $solicitudesModel;
    private $centroModel;
    protected $clienteRepository;
    protected $PlantillaRepository;
    protected $DocumentosRepository;    
    protected $solicitudesRepository;    
    protected $centroRepository;    

	/**
	 * @var string
	 */
	protected $upload_folder = './public/uploads/';
	
	function __construct() {
        parent::__construct();

        $this->load->model("bodegas/Bodegas_orm");
        $this->load->model("agentes/Agentes_orm");
        $this->load->model("modulos/Modulos_orm");
        $this->load->model('modulos/Catalogos_orm');
        $this->load->model('clientes/Catalogo_orm');
        $this->load->model('aseguradoras/Ramos_orm');
        $this->load->model('aseguradoras/Planes_orm');
        $this->load->model('aseguradoras/Aseguradoras_orm');
        $this->load->model('aseguradoras/Coberturas_orm');
        $this->load->model('configuracion_seguros/Comisiones_orm');
        $this->load->model('aseguradoras/Catalogo_tipo_intereses_orm');
        $this->load->model('aseguradoras/Catalogo_tipo_poliza_orm');
        $this->load->library('orm/catalogo_orm');
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
        $this->clienteRepository = new clienteRepository();
        $this->solicitudesRepository = new solicitudesRepository();
        $this->centroRepository = new centroRepository();
        $this->clienteModel = new clienteModel();
        $this->planesModel = new Planes_orm();
        $this->impuestosModel = new impuestosModel();
        $this->solicitudesModel = new solicitudesModel();
        $this->centroModel = new centroModel();
}
public function listar() {
    	$data = array();
    	
        $this->_Css();   
        $this->_js();
        
    	$this->assets->agregar_js(array(
        'public/assets/js/modules/solicitudes/listar.js'
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
            "titulo" => '<i class="fa fa-archive"></i> Solicitudes',
            "ruta" => array(
                0 => array("nombre" => "Seguros", "url" => "#",  "activo" => false),
                1 => array("nombre" => '<b>Solicitudes</b>', "activo" => true)
            ),
            "filtro"    => false,
            "menu"      => array()
        );
        
        if ($this->auth->has_permission('acceso', 'solicitudes/crear')){
            $breadcrumb["menu"] = array(
    		"url"	=> 'javascript:',
    		"clase" => 'modalOpcionesCrear',
    		"nombre" => "Crear"
    	);
            $menuOpciones["#exportarSolicitudesLnk"] = "Exportar";
            $breadcrumb["menu"]["opciones"] = $menuOpciones;
        }
        
        //Menu para crear
        $clause = array('empresa_id' => $this->empresa_id);
        $data['menu_crear'] = Ramos_orm::listar_cuentas($clause); 
        //catalogo para buscador        
        $data['aseguradoras'] = Aseguradoras_orm::where($clause)->get();
        $data['tipo'] = Catalogo_tipo_poliza_orm::get();
        $data['usuarios'] = usuario_orm::where('estado', 'Activo')->get();
        /*$clause2['empresa_id'] = $this->empresa_id;*/        
        
    	$this->template->agregar_titulo_header('Solicitudes');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar($breadcrumb);
}

public function ocultotabla() {
    	$this->assets->agregar_js(array(
    		'public/assets/js/modules/solicitudes/tabla.js'
    	));
    	
    	$this->load->view('tabla');
    }
    
public function ajax_listar($grid=NULL) {    	
    	$clause = array(
    		"empresa_id" =>  $this->empresa_id
    	);
    	$numero 	= $this->input->post('numero', true);
    	$cliente 	= $this->input->post('cliente', true);
    	$aseguradora 	= $this->input->post('aseguradora', true);
    	$ramo 		= $this->input->post('ramo', true);
    	$tipo    	= $this->input->post('tipo', true);
    	$fecha_creacion = $this->input->post('fecha_creacion', true);
    	$usuario        = $this->input->post('usuario', true);
    	$estado         = $this->input->post('estado', true);
    	
    	if(!empty($numero)){
    		$clause["numero"] = array('LIKE', "%$numero%");
    	}
    	if(!empty($cliente)){
    		$clause["cliente"] = array('LIKE', "%$cliente%");
    	}
    	if(!empty($aseguradora)){
    		$clause["aseguradora_id"] = $aseguradora;
    	}
    	if(!empty($ramo)){            
    		$clause["ramo"] = $ramo;
    	}
    	if(!empty($tipo)){
    		$clause["id_tipo_poliza"] = $tipo;
    	}
        if( !empty($fecha_creacion)){    		
    		$fecha_inicio = date("Y-m-d", strtotime($fecha_creacion));
    		$clause["fecha_creacion"] = array('=', $fecha_inicio);
    	}
    	if(!empty($usuario)){
    		$clause["usuario_id"] = $usuario;
    	}
    	if(!empty($estado)){
    		$clause["equipoid"] = $equipoid;
    	}
    	list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
    		
    	$count = $this->solicitudesRepository->listar_solicitudes($clause, NULL, NULL, NULL, NULL)->count();
    		
    	list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
    		 
    	$rows = $this->solicitudesRepository->listar_solicitudes($clause, $sidx, $sord, $limit, $start);
    	
    	//Constructing a JSON
    	$response = new stdClass();
    	$response->page     = $page;
    	$response->total    = $total_pages;
    	$response->records  = $count;
    	$response->result 	= array();
    	$i=0;
    
    	if(!empty($rows)){
    		foreach ($rows AS $i => $row){
            $uuid_solicitudes = bin2hex($row->uuid_solicitudes);
            $uuid_cliente = $row->cliente->uuid_cliente;
            $uuid_aseguradora = $row->aseguradora->uuid_aseguradora;
            $now = Carbon::now();
            $hidden_options = ""; 
            $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row->id .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
            //$hidden_options .= '<a href="'. base_url('colaboradores/ver/'. $uuid_colaborador) .'" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success">Ver Detalle</a>';

            $estado = "Pendiente";
            $estado_color = trim($estado) == "Pendiente" ? 'background-color:#F8AD46' : 'background-color: red';
            
            $response->rows[$i]["id"] = $row->id;
            $response->rows[$i]["cell"] = array(
                    '<a href="'. base_url('solicitudes/ver/'. $uuid_solicitudes) .'" style="color:blue;">'. Util::verificar_valor($row->numero) .'</a>',
                    '<a href="'. base_url('clientes/ver/'. $uuid_cliente) .'" style="color:blue;">'. Util::verificar_valor($row->cliente->nombre) .'</a>',                    
                    '<a href="'. base_url('aseguradoras/editar/'. $uuid_aseguradora) .'" style="color:blue;">'. Util::verificar_valor($row->aseguradora->nombre) .'</a>',                    
                    Util::verificar_valor($row->ramo),
                    Util::verificar_valor($row->tipo->nombre),
                    ($row->created_at->diff($now)->days < 1) ? '1' : $row->created_at->diffForHumans($now),
                    //$row->created_at->diffForHumans(),
                    $row->created_at !="" ? Carbon::createFromFormat('Y-m-d H:i:s', $row->created_at)->format('d/m/Y') : "",
                    Util::verificar_valor($row->usuario->nombre . " " . $row->usuario->apellido),
                    !empty($estado) ? '<span style="color:white; '. $estado_color .'" class="btn btn-xs btn-block">'. $estado .'</span>' : "",
                    $link_option,
                    $hidden_options                   
            );
    $i++;
    		}
    	}
    	echo json_encode($response);
    	exit;
    }    

public function crear() { 
    $acceso = 1;
    $mensaje = array();
    $solicitudes_id = !empty($_POST['solicitud_id']) ? $_POST['solicitud_id'] : '';
    $solicitudes_titulo = Ramos_orm::find($solicitudes_id);
    $titulo = $solicitudes_titulo->descripcion;
    $ramo = $solicitudes_titulo->nombre;
    $tipo_poliza = $solicitudes_titulo->id_tipo_poliza;
    $codigo_ramo = $solicitudes_titulo->codigo_ramo;
   
    if(!$this->auth->has_permission('acceso')){
      // No, tiene permiso, redireccionarlo.
      $acceso = 0;
      $mensaje = array('estado'=>500, 'mensaje'=>' <b>Usted no cuenta con permiso para esta solicitud</b>','clase'=>'alert-danger');
    }

    $this->_Css();   
    $this->_js();
    $this->assets->agregar_js(array(       
     'public/assets/js/modules/solicitudes/formulario.js',   
     'public/assets/js/modules/solicitudes/crear.vue.js',
     'public/assets/js/modules/solicitudes/component.vue.js',  
     'public/assets/js/modules/solicitudes/plugins.js'   
    ));
    //Catalogos
    $catalogo_clientes = Catalogos_orm::where('identificador','like','Identificacion')->orderBy("orden")->get(array('valor','etiqueta')); 
    $pagador = Catalogos_orm::where('identificador', 'like', 'pagador_seguros')->orderBy("orden")->get(array('valor', 'etiqueta'));  
    $cantidad_pagos = Catalogos_orm::where('identificador', 'like', 'cantidad_pagos')->orderBy("orden")->get(array('valor', 'etiqueta'));
    $frecuencia_pagos = Catalogos_orm::where('identificador', 'like', 'frecuencia_pagos')->orderBy("orden")->get(array('valor', 'etiqueta'));    
    $metodo_pago = Catalogos_orm::where('identificador', 'like', 'metodo_pago')->orderBy("orden")->get(array('valor', 'etiqueta'));
    $sitio_pago = Catalogos_orm::where('identificador', 'like', 'sitio_pago')->orderBy("orden")->get(array('valor', 'etiqueta'));
    //$centro_facturacion = Catalogos_orm::where('identificador', 'like', 'centro_facturacion')->orderBy("orden")->get(array('valor', 'etiqueta'));    
    $agentes = Agentes_orm::orderBy("nombre")->get(array('id', 'nombre', 'apellido'));
      $data=array();      
      $this->assets->agregar_var_js(array(
        "vista" => 'crear',
        "acceso" => $acceso,
        "solicitud_id" => $solicitudes_id,
        "catalogo_clientes" => $catalogo_clientes,
        "pagador" => $pagador,
        "cantidad_pagos" => $cantidad_pagos,
        "frecuencia_pagos" => $frecuencia_pagos,
        "metodo_pago" => $metodo_pago,
        "sitio_pago" => $sitio_pago,        
        "agentes" => $agentes,
        "ramo" => $ramo,
        "id_tipo_poliza" => $tipo_poliza,
        "codigo_ramo" => $codigo_ramo
      ));
      
     
    $breadcrumb = array(
      "titulo" => '<i class="fa fa-archive"></i> Solicitudes: Crear / ' . $titulo,
      "ruta" => array(
            0 => array("nombre" => "Seguros", "url" => "#",  "activo" => false),
            1 => array("nombre" => 'Solicitudes', "activo" => false),
            2 => array("nombre" => '<b>Crear</b>', "activo" => true)
        ),
      "filtro"    => false,
      "menu"      => array()
    );
    $data['mensaje'] = $mensaje;
    $this->template->agregar_titulo_header('Solicitudes: Crear');
    $this->template->agregar_breadcrumb($breadcrumb);
    $this->template->agregar_contenido($data);
    $this->template->visualizar();    
    
}

function ajax_get_clientes() {

    $clause['empresa_id'] = $this->empresa_id;
    $clause['tipo_identificacion'] = $_POST['tipo_cliente'];
    $clientes = $this->clienteRepository->getClientesPorTipo($clause)->get()->toArray();   
    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($clientes))->_display();

    exit;
}
function ajax_get_centro_facturable() {

    $clause['empresa_id'] = $this->empresa_id;
    $clause['cliente_id'] = $_POST['cliente_id'];
    $centro_facturacion = $this->centroModel->where($clause)->get()->toArray();
    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($centro_facturacion))->_display();

    exit;
}
function ajax_get_direccion() {

    $clause['empresa_id'] = $this->empresa_id;
    $clause['id'] = $_POST['centro_id'];
    $direccion = $this->centroModel->where($clause)->get()->toArray();
    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($direccion))->_display();

    exit;
}
function ajax_get_cliente() {

    $clause['empresa_id'] = $this->empresa_id;
    $clause['id'] = $_POST['cliente_id'];
    $cliente = $this->clienteModel->where($clause)->first()->toArray();    
    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($cliente))->_display();

    exit;
}
function ajax_get_planes() {
    $clause['id_aseguradora'] = $_POST['aseguradora_id'];
    $planes = $this->planesModel->getPlanes($clause)->get()->toArray();
    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($planes))->_display();

    exit;
}
function ajax_get_comision() {
    $clause['id_planes'] = $_POST['id_planes'];
    $clause['inicio'] = 1;
    $comisiones = Comisiones_orm::where($clause)->get()->toArray();
    $impuesto_plan = Planes_orm::where('id', $clause['id_planes'])->get()->toArray();
    $clause2['id'] = $impuesto_plan[0]['id_impuesto'];
    $clause2['empresa_id'] = $this->empresa_id;
    $impuesto = $this->impuestosModel->where($clause2)->get(array('id', 'nombre', 'impuesto'))->toArray();    
    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode(array($comisiones, $impuesto)))->_display();

    exit;
} 
function ajax_get_coberturas() {
    $clause['id_planes'] = $_POST['plan_id'];
    $coberturas = Coberturas_orm::where($clause)->get()->toArray();    
    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($coberturas))->_display();
    exit;
}

function ajax_get_porcentaje() {
    $clause['id'] = $_POST['agente_id'];
    $agentes = Agentes_orm::where($clause)->get()->toArray();    
    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($agentes))->_display();
    exit;
}

function ocultoformulario() {
        $provincias = Catalogo_orm::where('tipo', '=', 'provincias')->get(array('id', 'valor'));
        $letras = Catalogos_orm::where('identificador','like','Letra')->get(array('valor','etiqueta'));
        $clause = array('empresa_id' => $this->empresa_id);        
        $aseguradoras = Aseguradoras_orm::where($clause)->get(array('id', 'nombre'));
        
        $this->assets->agregar_var_js(array(
                "provincias" => $provincias,
                "letras" => $letras,
                "aseguradoras" => $aseguradoras
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
    $campo["uuid_solicitudes"] = Capsule::raw("ORDER_UUID(uuid())");
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
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Colaboradores
 * 
 * Modulo para administrar la creacion, edicion de colaboradores.
 *
 * @package    PensaApp
 * @subpackage Controller
 * @category   Controllers
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  05/22/2015
 */

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\FormularioDocumentos AS FormularioDocumentos;
use League\Csv\Writer as Writer;
use Carbon\Carbon;

class Evaluaciones extends CRM_Controller
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
	
	/**
	 * @var string
	 */
	protected $upload_folder = './public/uploads/';
	
	function __construct()
    {
        parent::__construct();

        $this->load->model('evaluaciones_orm');
        $this->load->model('estado_orm');
        $this->load->model('accion_personal/accion_personal_orm');
        $this->load->model('colaboradores/colaboradores_orm');
        $this->load->model('configuracion_rrhh/tiempo_contratacion_orm');
        $this->load->model('configuracion_rrhh/cargos_orm');
        $this->load->model('configuracion_rrhh/departamentos_orm');
        $this->load->model('contabilidad/centros_orm');
        $this->load->model('usuarios/usuario_orm');
        $this->load->model('usuarios/empresa_orm');
        $this->load->model("centros/Centros_orm");
        $this->load->library('orm/catalogo_orm');
        
        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();
        
        //HMVC Load Modules
        $this->load->module(array('consumos'));
        
        //Obtener el id de usuario de session
        $uuid_usuario = $this->session->userdata('huuid_usuario');
        $usuario = Usuario_orm::findByUuid($uuid_usuario);
        
        $this->usuario_id = $usuario->id;
         
        //Obtener el id_empresa de session
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $this->empresa_id = $empresa->id;
        
        //Obtener el id de modulo
        $controllername = $this->router->fetch_class();
        $modulo = Modulos_orm::where("controlador", $controllername)->get()->toArray();
        $this->modulo_id = $modulo[0]["id"];
        
        $this->nombre_modulo = $this->router->fetch_class();
    }

    public function listar()
    {
    	$data = array(
    		"estados" => Estado_orm::lista(),
    		"lista_departamentos" => Departamentos_orm::lista($this->empresa_id),
    	);
    	
    	$this->assets->agregar_css(array(
    		'public/assets/css/default/ui/base/jquery-ui.css',
    		'public/assets/css/default/ui/base/jquery-ui.theme.css',
    		'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
    		'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
    		'public/assets/css/plugins/jquery/chosen/chosen.min.css',
    		'public/assets/css/plugins/jquery/jquery.webui-popover.css',
    		'public/assets/css/plugins/bootstrap/jquery.bootstrap-touchspin.css',
    		'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
        	'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
    	));
    	$this->assets->agregar_js(array(
    		'public/assets/js/default/jquery-ui.min.js',
    		'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
    		'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
    		'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
    		'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
    		'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
    		'public/assets/js/plugins/jquery/jquery.webui-popover.js',
    		'public/assets/js/plugins/jquery/jquery.sticky.js',
    		'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
    		'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
    		'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
    		'public/assets/js/plugins/jquery/chosen.jquery.min.js',
    		'public/assets/js/moment-with-locales-290.js',
    		'public/assets/js/plugins/bootstrap/jquery.bootstrap-touchspin.js',
    		'public/assets/js/plugins/bootstrap/daterangepicker.js',
    		'public/assets/js/default/formulario.js',
    		'public/assets/js/modules/colaboradores/listar.js',
    	));
    	
    	//Agregra variables PHP como variables JS
    	$this->assets->agregar_var_js(array(
    		"grid_url" => 'colaboradores/ajax-listar/grid'
    	));
    	
    	//Opcion Default
    	$menuOpciones = array(
    		//"#crearLnk" => "Crear",
    		//"#activarLnk" => "Activar",
    		//"#inactivarLnk" => "Inactivar",
    		//"#exportarLnk" => "Exportar",
    	);
		
    	//Breadcrum Array
    	$breadcrumb = array(
    		"titulo" => 'Colaboradores',
    		"filtro" => true
    	);
    	
    	//Verificar permisos para crear
    	if($this->auth->has_permission('acceso', 'colaboradores/crear')){
    		$breadcrumb["menu"] = array(
    			"url"	 => 'colaboradores/crear',
    			"nombre" => "Crear"
    		);
    		
    		$menuOpciones["#activarColaboradorLnk"] = "Activar";
    		$menuOpciones["#trasladarColaboradorLnk"] = "Trasladar";
    		$menuOpciones["#liquidarColaboradorLnk"] = "Liquidar";
    		$menuOpciones["#exportarColaboradorLnk"] = "Exportar";
    	}
    	
    	$breadcrumb["menu"]["opciones"] = $menuOpciones;
    	
    	$this->template->agregar_titulo_header('Listado de Colaboradores');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar($breadcrumb);
    }
    
    public function ajax_listar($grid=NULL)
    {
    	Capsule::enableQueryLog();
    	
    	$clause = array(
    		"empresa_id" =>  $this->empresa_id
    	);
    	$nombre 	= $this->input->post('nombre', true);
    	$cedula 	= $this->input->post('cedula', true);
    	$estado_id 	= $this->input->post('estado_id', true);
    	$cargo 		= $this->input->post('cargo', true);
    	$codigo 	= $this->input->post('codigo', true);
    	$departamento_id = $this->input->post('departamento_id', true);
    	$nombre_centro = $this->input->post('nombre_centro_contable', true);
    	$fecha_contratacion_desde = $this->input->post('fecha_contratacion_desde', true);
    	$fecha_contratacion_hasta = $this->input->post('fecha_contratacion_hasta', true);
    	
    	if(!empty($nombre)){
    		$clause["nombre"] = array('LIKE', "%$nombre%");
    	}
    	if(!empty($cedula)){
    		$clause["cedula"] = array('LIKE', "%$cedula%");
    	}
    	if( !empty($fecha_contratacion_desde)){
    		$fecha_contratacion_desde = str_replace('/', '-', $fecha_contratacion_desde);
    		$fecha_inicio = date("Y-m-d H:i:s", strtotime($fecha_contratacion_desde));
    		$clause["created_at"] = array('>=', $fecha_inicio);
    	}
    	if( !empty($fecha_contratacion_hasta)){
    		$fecha_contratacion_hasta = str_replace('/', '-', $fecha_contratacion_hasta);
    		$fecha_fin = date("Y-m-d 23:59:59", strtotime($fecha_contratacion_hasta));
    		$clause["created_at@"] = array('<=', $fecha_fin);
    	}
    	if(!empty($estado_id)){
    		$clause["estado_id"] = $estado_id;
    	}
    	if(!empty($cargo)){
    		$clause["cargo"] = array('LIKE', "%$cargo%");
    	}
    	if(!empty($departamento_id)){
    		$clause["departamento_id"] = array($departamento_id);
    	}
    	if(!empty($nombre_centro)){
    		$clause["nombre_centro"] = array('LIKE', "%$nombre_centro%");
    	}
    	if( !empty($codigo)){
    		//["codigo"] = array('LIKE', "%$codigo%");
    	}
    	
    	list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
    		
    	$count = Colaboradores_orm::listar($clause, NULL, NULL, NULL, NULL)->count();
    		
    	list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
    		 
    	$rows = Colaboradores_orm::listar($clause, $sidx, $sord, $limit, $start);
    	
    	//Constructing a JSON
    	$response = new stdClass();
    	$response->page     = $page;
    	$response->total    = $total_pages;
    	$response->records  = $count;
    	$response->result 	= array();
    	$i=0;
    	
    	if(!empty($rows->toArray())){
    		foreach ($rows->toArray() AS $i => $row){
    	
    			$uuid_colaborador = bin2hex($row['uuid_colaborador']);
    			$nombre = Util::verificar_valor($row['nombre']);
    			$apellido = Util::verificar_valor($row['apellido']);
    			
    			if(!empty($grid)){
    			
    				$response->result[$i]["id"] = $row['id'];
    				$response->result[$i]["perfil"] = array(
    					"imagen" => base_url('/public/assets/images/default_avatar.png'),
    					"cargo" => Util::verificar_valor($row["cargo"]["nombre"])
    				);
    				$response->result[$i]["datos"] = array(
    					"Nombre" => $nombre. " ". $apellido,
    					"Cedula" => Util::verificar_valor($row['cedula']),
    					"Fecha Contratacion" => Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d/m/Y'),
    					"Estado" => Util::verificar_valor($row["estado"]["etiqueta"])
    				);
    				 
    			}else{
    				
    				$hidden_options = "";
    				$link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row['id'] .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
    				$hidden_options .= '<a href="'. base_url('colaboradores/ver/'. $uuid_colaborador) .'" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success">Ver Detalle</a>';
    				$hidden_options .= '<a href="#collapse0000" class="btn btn-block btn-outline btn-success" data-toggle="collapse">Crea acci&oacute;n de personal</a>';
    				$hidden_options .= '<div id="collapse0000" class="collapse">
					<ul class="list-group clear-list">
						<li class="m-sm"><a href="#">Vacaciones</a></li>
						<li class="m-sm"><a href="#">Ausencias</a></li>
						<li class="m-sm"><a href="#">Incapacidades</a></li>
						<li class="m-sm"><a href="#">Lincencias</a></li>
						<li class="m-sm"><a href="#">Permisos</a></li>
    					<li class="m-sm"><a href="#">Liquidaciones</a></li>
    					<li class="m-sm"><a href="#">Evaluaciones</a></li>
					</ul>
					</div>';
    				$hidden_options .= '<a href="#" class="btn btn-block btn-outline btn-success m-t-xs" data-id="'. $row['id'] .'" data-uuid="'. $uuid_colaborador .'" >Crear descuento directo</a>';
    				$hidden_options .= '<a href="#" class="btn btn-block btn-outline btn-success subirArchivoBtn" data-id="'. $row['id'] .'" data-uuid="'. $uuid_colaborador .'" >Subir archivo</a>';
    				$hidden_options .= '<a href="#" class="btn btn-block btn-outline btn-success" data-id="'. $row['id'] .'" data-uuid="'. $uuid_colaborador .'" >Crear consumo</a>';

    				$response->rows[$i]["id"] = $row['id'];
    				$response->rows[$i]["cell"] = array(
    					'',
    					$nombre. " ". $apellido,
    					Util::verificar_valor($row['cedula']),
    					$row['created_at'] !="" ? Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d/m/Y') : "",
    					Util::verificar_valor($row["centro_contable"]["nombre"]),
    					Util::verificar_valor($row["departamento"]["nombre"]),
    					Util::verificar_valor($row["cargo"]["nombre"]),
    					Util::verificar_valor($row["tipo_salario"]),
    					Util::verificar_valor($row["estado"]["etiqueta"]),
    					$link_option,
    					$hidden_options,
    					$row["departamento"]["id"],
    					$row["ciclo_id"]
    				);
    			}
    			
    			$i++;
    		}
    	}

    	echo json_encode($response);
    	exit;
    }
    
    /**
     * Cargar Vista Parcial de Tabla
     *
     * @return void
     */
    /*public function ocultotabla()
    {
    	$this->assets->agregar_js(array(
    		'public/assets/js/modules/colaboradores/tabla.js'
    	));
    	
    	$this->load->view('tabla');
    }*/
    
    /**
     * Cargar Vista Parcial de Formulario
     *
     * @return void
     */
    /*public function ocultoformulario($data=NULL)
    {
    	$this->assets->agregar_js(array(
    		'public/assets/js/modules/colaboradores/crear.js'
    	));
    	 
    	$this->load->view('formulario', $data);
    }*/
    
    
    /**
     * Cargar Vista Parcial de Tabla de Evaluaciones
     *
     * @return void
     */
    public function ocultotablaevaluaciones()
    {
    	$this->assets->agregar_js(array(
    		'public/assets/js/modules/colaboradores/busqueda-evaluacion.controller.js',
    		'public/assets/js/modules/colaboradores/tabla-evaluaciones.js'
    	));
    
    	$this->load->view('tabla-evaluaciones');
    }

    /**
     * Cargar Vista Parcial de Evaluacion
     *
     * @return void
     */
    public function formulario($data=NULL)
    {
    	$this->assets->agregar_js(array(
    		'public/assets/js/modules/evaluaciones/formulario-evaluacion.controller.js'
    	));
    	
    	$this->load->view('formulario', $data);
    }
    
    /**
     * Cargar Vista Parcial de Evaluacion
     *
     * @return void
     */
    public function formularioparcial($data=NULL)
    {
    	/*$this->assets->agregar_js(array(
    		'public/assets/js/modules/evaluaciones/formulario-evaluacion.controller.js'
    	));*/
    	 
    	$this->template->vista_parcial(array(
    		'evaluaciones',
    		'crear'
    	));
    }
	
    function evaluacion($colaborador_uuid=NULL)
    {
    	$data = array();
    	$mensaje = array();
    	$titulo_formulario = "Formulario Evaluacion";
    	 
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar();
    }
    
    function crear($colaborador_uuid=NULL)
    {
    	$data = array();
    	$mensaje = array();
    	$titulo_formulario = "Formulario de Evaluaci&oacute;n";
    
    	$breadcrumb = array(
    		"titulo" => $titulo_formulario,
    	);
    	
    	$this->assets->agregar_css(array(
    		'public/assets/css/plugins/bootstrap/awesome-bootstrap-checkbox.css',
    		'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
    		'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
    		'public/assets/css/default/ui/base/jquery-ui.css',
    		'public/assets/css/default/ui/base/jquery-ui.theme.css',
    		'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
    		'public/assets/css/plugins/bootstrap/jquery.bootstrap-touchspin.css',
    		'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
    		'public/assets/css/plugins/jquery/jquery.webui-popover.css',
    		'public/assets/css/plugins/jquery/chosen/chosen.min.css',
    	));
    	$this->assets->agregar_js(array(
    		'public/assets/js/default/jquery-ui.min.js',
    		'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
    		'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
    		'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
    		'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
    		'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
    		'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
    		'public/assets/js/plugins/jquery/combodate/combodate.js',
    		'public/assets/js/plugins/jquery/combodate/momentjs.js',
    		'public/assets/js/plugins/jquery/chosen.jquery.min.js',
    		'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
    		'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
    		'public/assets/js/moment-with-locales-290.js',
    		'public/assets/js/plugins/bootstrap/daterangepicker.js',
    		'public/assets/js/default/tabla-dinamica.jquery.js',
    		'public/assets/js/default/toast.controller.js',
    	));
    	 
    	$this->template->agregar_titulo_header('Evaluaciones');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar();
    }
    
    
    public function ajax_listar_evaluaciones($grid=NULL)
    {
    	$clause = array();
    	
    	$colaborador_id 	= $this->input->post('colaborador_id', true);
    	$tipo_evaluacion_id = $this->input->post('tipo_evaluacion_id', true);
    	$centro_contable_id = $this->input->post('centro_contable_id', true);
    	$resultado_id 		= $this->input->post('resultado_id', true);
    	$fecha_evaluacion 	= $this->input->post('fecha_evaluacion', true);
    	$usuario_id 		= $this->input->post('usuario_id', true);
    	$numero_evaluacion 	= $this->input->post('numero_evaluacion', true);

    	//Filtro default evaluaciones relacionadas a colaborador
    	$clause["colaborador_id"] = $colaborador_id;
    	$clause["empresa_id"] = $this->empresa_id;
    	
    	//Filtros de busqueda
    	if(!empty($numero_evaluacion)){
    		$clause["numero"] = array('LIKE', "%$numero_evaluacion%");
    	}
    	if( !empty($fecha_evaluacion)){
    		$fecha_evaluacion = str_replace('/', '-', $fecha_evaluacion);
    		$fecha_evaluacion = date("Y-m-d", strtotime($fecha_evaluacion));
    		$clause["fecha"] = $fecha_evaluacion;
    	}
    	if(!empty($tipo_evaluacion_id)){
    		$clause["tipo_evaluacion_id"] = $tipo_evaluacion_id;
    	}
    	if(!empty($centro_contable_id)){
    		$clause["centro_contable_id"] = $centro_contable_id;
    	}
    	if(!empty($resultado_id)){
    		$clause["resultado_id"] = $resultado_id;
    	}
    	if(!empty($usuario_id)){
    		$clause["creado_por"] = $usuario_id;
    	}
    	
    	list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
    
    	$count = Evaluaciones_orm::listar($clause, NULL, NULL, NULL, NULL)->count();
    
    	list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
    	 
    	$rows = Evaluaciones_orm::listar($clause, $sidx, $sord, $limit, $start);
    	
    	//Constructing a JSON
    	$response = new stdClass();
    	$response->page     = $page;
    	$response->total    = $total_pages;
    	$response->records  = $count;
    	$i=0;
    	
    	if(!empty($rows->toArray())){
    		foreach ($rows->toArray() AS $i => $row){
    			
    			$fecha = Util::verificar_valor($row['fecha']);
    			$fecha = date("d/m/Y", strtotime($fecha));
    			$nombre = Util::verificar_valor($row['usuario']['nombre']);
    			$apellido = Util::verificar_valor($row['usuario']['apellido']);
    			 
    			if(!empty($grid)){
    				 
    				/*$response->result[$i]["id"] = $row['id'];
    				$response->result[$i]["perfil"] = array(
    					"imagen" => base_url('/public/assets/images/default_avatar.png'),
    					"cargo" => Util::verificar_valor($row["cargo"]["nombre"])
    				);
    				$response->result[$i]["datos"] = array(
    					"Nombre" => $nombre. " ". $apellido,
    					"Cedula" => Util::verificar_valor($row['cedula']),
    					"Fecha Contratacion" => Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d/m/Y'),
    					"Estado" => Util::verificar_valor($row["estado"]["etiqueta"])
    				);*/
    					
    			}else{
    
    				$hidden_options = "";
    				$link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row['id'] .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
    				$hidden_options .= '<a href="#" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success descargarEvaluacionBtn">Descargar</a>';
    				$hidden_options .= '<a href="#" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success editarEvaluacionBtn">Editar</a>';
    				
    				$response->rows[$i]["id"] = $row['id'];
    				$response->rows[$i]["cell"] = array(
    					Util::verificar_valor($row['numero']),
    					$fecha,
    					Util::verificar_valor($row["tipo_evaluacion"]["etiqueta"]),
    					Util::verificar_valor($row['departamento']['nombre']),
    					Util::verificar_valor($row["centro_contable"]["nombre"]),
    					Util::verificar_valor($row['cargo']['nombre']),
    					$nombre. " ". $apellido,
    					Util::verificar_valor($row['observaciones']),
    					Util::verificar_valor($row['calificacion']),
    					Util::verificar_valor($row['resultado']['etiqueta']),
    					$link_option,
    					$hidden_options,
    					Util::verificar_valor($row['archivo_ruta']),
    					Util::verificar_valor($row['archivo_nombre'])
    				);
    			}
    			 
    			$i++;
    		}
    	}
    	 
    	echo json_encode($response);
    	exit;
    }
    
    public function ajax_guardar_evaluacion()
    {
    	/*echo "<pre>";
    	print_r($_POST);
    	print_r($_FILES);
    	echo "</pre>";
    	die();*/
    	
    	/**
    	 * Inicializar Transaccion
    	 */
    	Capsule::beginTransaction();
    	 
    	try {
    		 
    		$evaluacion_id		= $this->input->post('evaluacion_id', true);
    		$colaborador_id 	= $this->input->post('colaborador_id', true);
    		$numero 			= $this->input->post('numero', true);
    		$fecha 				= $this->input->post('fecha', true);
    		$fecha			 	= !empty($fecha) ? str_replace('/', '-', $fecha) : "";
    		$fecha 				= !empty($fecha) ? date("Y-m-d", strtotime($fecha)) : "";
    		$tipo_evaluacion_id = $this->input->post('tipo_evaluacion_id', true);
    		$estado_id 			= $this->input->post('estado_id', true);
    		$resultado_id 		= $this->input->post('resultado_id', true);
    		$creado_por 		= $this->input->post('creado_por', true);
    		$calificacion 		= $this->input->post('calificacion', true);
    		$evaluado_por		= $this->input->post('evaluado_por', true);
    		$documento_evaluacion = $this->input->post('documento_evaluacion', true);
    		$documento_evaluacion = $documento_evaluacion == true ? 1 : 0;
    		$observaciones 		= $this->input->post('observaciones', true);
    		
    		//Verificar si existe $evaluacion_id
    		$evaluacion = Evaluaciones_orm::find($evaluacion_id);

    		$colaborador = Colaboradores_orm::where("id", $colaborador_id)->with(['centro_contable', 'departamento', 'cargo'])->get()->toArray();
    		$colaborador = !empty($colaborador) ? $colaborador[0] : array();
    		
    		$centro_contable_id = !empty($colaborador["centro_contable_id"]) ? $colaborador["centro_contable_id"] : "";
    		$departamento_id 	= !empty($colaborador["departamento_id"]) ? $colaborador["departamento_id"] : "";
    		$cargo_id 			= !empty($colaborador["cargo_id"]) ? $colaborador["cargo_id"] : "";
    
    		if(!empty($evaluacion))
    		{
    			$evaluacion->empresa_id 		= $this->empresa_id;
    			//$evaluacion->numero 			= $numero;
    			$evaluacion->fecha 				= $fecha;
    			$evaluacion->tipo_evaluacion_id = $tipo_evaluacion_id;
    			$evaluacion->centro_contable_id = $centro_contable_id;
    			$evaluacion->departamento_id 	= $departamento_id;
    			$evaluacion->cargo_id 			= $cargo_id;
    			$evaluacion->estado_id 			= $estado_id;
    			$evaluacion->resultado_id 		= $resultado_id;
    			$evaluacion->calificacion 		= $calificacion;
    			$evaluacion->evaluado_por		= $evaluado_por;
    			$evaluacion->documento_evaluacion = $documento_evaluacion;
    			$evaluacion->observaciones 		= $observaciones;
    			$evaluacion->save();
    			
    			//Actualizar tabla accion personal
    			$evaluacion->acciones()->where("accionable_id", $evaluacion_id)->update([
    				"colaborador_id" => $colaborador_id,
    				"centro_contable_id" => $centro_contable_id,
    				"departamento_id" => $departamento_id,
    				"cargo_id " => $cargo_id,
    				"nombre_completo" => !empty($colaborador["nombre_completo"]) ? $colaborador["nombre_completo"] : "",
    				"centro_contable" => !empty($colaborador) ? $colaborador["centro_contable"]["nombre"] : "",
    				"departamento" => !empty($colaborador) ? $colaborador["departamento"]["nombre"] : "",
    				"cargo" => !empty($colaborador) ? $colaborador["cargo"]["nombre"] : "",
    				"cedula" => !empty($colaborador) ? $colaborador["cedula"] : "",
    			]);
    		}
    		else
    		{
    			$fieldset = array(
    				"empresa_id" 		=> $this->empresa_id,
    				"colaborador_id" 	=> $colaborador_id,
    				"fecha" 			=> $fecha,
    				"tipo_evaluacion_id" => $tipo_evaluacion_id,
    				"centro_contable_id" => $centro_contable_id,
    				"departamento_id"	=> $departamento_id,
    				"cargo_id"			=> $cargo_id,
    				"resultado_id" 		=> $resultado_id,
    				"estado_id" 		=> $estado_id,
    				"calificacion" 		=> $calificacion,
    				"documento_evaluacion" => $documento_evaluacion,
    				"observaciones"		=> $observaciones,
    				"evaluado_por" 		=> $evaluado_por,
    				"creado_por" 		=> $this->usuario_id,
    			);
    			 
    			//--------------------
    			// Guardar Evaluacion
    			//--------------------
    			$evaluacion = Evaluaciones_orm::create($fieldset);
    			$evaluacion->acciones()->saveMany([new Accion_personal_orm([
    				"empresa_id" => $this->empresa_id,
    				"no_accion" => Capsule::raw("NO_ACCION_PERSONAL('AP', ". $this->empresa_id .")"),
    				"colaborador_id" => $colaborador_id,
    				"centro_contable_id" => $centro_contable_id,
    				"departamento_id" => $departamento_id,
    				"cargo_id " => $cargo_id,
    				"nombre_completo" => !empty($colaborador) ? $colaborador["nombre_completo"] : "",
    				"centro_contable" => !empty($colaborador) ? $colaborador["centro_contable"]["nombre"] : "",
    				"departamento" => !empty($colaborador) ? $colaborador["departamento"]["nombre"] : "",
    				"cargo" => !empty($colaborador) ? $colaborador["cargo"]["nombre"] : "",
    				"cedula" => !empty($colaborador) ? $colaborador["cedula"] : "",
    			])]);
    		}
    
    		//--------------------
    		// Subir documento
    		//--------------------
    		if(!empty($_FILES))
    		{
    			$modulo_folder = $this->upload_folder . trim($this->nombre_modulo);
    			$empresa_folder = $modulo_folder ."/". $this->empresa_id;
    			$archivo_ruta = "public/uploads/" . trim($this->nombre_modulo) ."/". $this->empresa_id;
    
    			//Verificar si existe la carpeta
    			//del modulo de colaboradores en uploads
    			if (!file_exists($modulo_folder)) {
    				try{
    					mkdir($modulo_folder, 0777, true);
    				} catch (Exception $e) {
    					log_message("error", "MODULO: ". __METHOD__ .", Linea: ". __LINE__ ." --> ". $e->getMessage().".\r\n");
    				}
    			}
    	   
    			//Verificar si existe la carpeta
    			//de la empresa existe, dentro
    			//del modulo.
    			if (!file_exists($empresa_folder)) {
    				try{
    					mkdir($empresa_folder, 0777, true);
    				} catch (Exception $e){
    					log_message("error", "MODULO: ". __METHOD__ .", Linea: ". __LINE__ ." --> ". $e->getMessage().".\r\n");
    				}
    			}
    	   
    			$config = new \Flow\Config(array(
    				'tempDir' => $modulo_folder
    			));
    	   
    			//Inicializar Flow
    			$request = new \Flow\Request();
    	   
    			//Armar Nomre de archivo corto.
    			$filename = $this->input->post('flowFilename', true);
    			$extension = pathinfo($filename, PATHINFO_EXTENSION);
    			$file_name = "eval-". rand().time() . "." . $extension;
    
    			//Subir Archivo
    			if(Flow\Basic::save($empresa_folder . '/' . $file_name, $config, $request)){
    
    				$evaluacion = Evaluaciones_orm::find($evaluacion->id);
    				$evaluacion->archivo_ruta = $archivo_ruta;
    				$evaluacion->archivo_nombre = $file_name;
    				$evaluacion->save();
    
    			}else{
    				log_message("error", "MODULO: ". __METHOD__ .", Linea: ". __LINE__ ." --> No se pudo subir el archivo de evaluacion.\r\n");
    			}
    		}
    		 
    	} catch(ValidationException $e){
    
    		// Rollback
    		Capsule::rollback();
    
    		log_message("error", "MODULO: ". __METHOD__ .", Linea: ". __LINE__ ." --> ". $e->getMessage().".\r\n");
    
    		echo json_encode(array(
    			"guardado" => false,
    			"mensaje" => "Hubo un error tratando de ". (!empty($evaluacion_id) ? "actualizar" : "guardar") ." la evaluacion."
    		));
    		exit;
    	}
    	 
    	// If we reach here, then
    	// data is valid and working.
    	// Commit the queries!
    	Capsule::commit();
    	 
    	$this->session->set_flashdata('mensaje', "Se ha ". (!empty($evaluacion_id) ? "actualizado" : "guardado") ." la evaluacion satisfactoriamente.");
    	 
    	echo json_encode(array(
    		"guardado" => true,
    		"mensaje" => "Se ha ". (!empty($evaluacion_id) ? "actualizado" : "guardado") ." la evaluacion satisfactoriamente."
    	));
    	exit;
    }
    
    public function ajax_seleccionar_evaluacion()
    {
    	$colaborador_id =  $this->input->post('colaborador_id', true);
    	$evaluacion_id =  $this->input->post('id', true);
    	 
    	if(empty($evaluacion_id)){
    		return false;
    	}
    	 
    	$evaluacion = Evaluaciones_orm::where("id", $evaluacion_id)->where("empresa_id", $this->empresa_id)->get()->toArray();
    	if(!empty($evaluacion)){
    		$evaluacion = $evaluacion[0];
    	
    		if(!empty($evaluacion["fecha"])){
    			$evaluacion["fecha"] = date("d/m/Y", strtotime($evaluacion["fecha"]));
    		}
    	}
    	
    	echo json_encode($evaluacion);
    	exit;
    }
}

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
use Flexio\Modulo\AccionPersonal\Repository\AccionPersonalRepository as AccionPersonalRepository;
use Carbon\Carbon;
class Accion_personal extends CRM_Controller
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
    private $AccionPersonalRepository;
	function __construct()
    {
        parent::__construct();
        $this->load->model('accion_personal_orm');
        $this->load->model('ausencias/ausencias_orm');
        $this->load->model('ausencias/estado_ausencias_orm');
        $this->load->model('vacaciones/vacaciones_orm');
        $this->load->model('vacaciones/estado_vacaciones_orm');
        $this->load->model('incapacidades/incapacidades_orm');
        $this->load->model('incapacidades/estado_incapacidades_orm');
        $this->load->model('licencias/licencias_orm');
        $this->load->model('licencias/estado_licencias_orm');
        $this->load->model('permisos/permisos_trabajo_orm');
        $this->load->model('permisos/estado_permisos_trabajo_orm');
        $this->load->model('liquidaciones/liquidaciones_orm');
        $this->load->model('liquidaciones/estado_liquidaciones_orm');
        $this->load->model('evaluaciones/evaluaciones_orm');
        $this->load->model('evaluaciones/estado_orm');
        $this->load->model('colaboradores/colaboradores_orm');
        $this->load->model('colaboradores/estado_orm');
        $this->load->model('usuarios/usuario_orm');
        $this->load->model('usuarios/empresa_orm');
        $this->load->library('orm/catalogo_orm');
        $this->load->model('configuracion_rrhh/cargos_orm');
        $this->load->model('configuracion_rrhh/departamentos_orm');
        $this->load->model('evaluaciones/evaluaciones_orm');
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
        //Repository
        $this->AccionPersonalRepository = new AccionPersonalRepository();
        /*$lista = Accion_personal_orm::with(array('vacaciones' => function($query){
        	$query->with(['colaborador' => function($query){
        		$query->with(['centro_contable', 'departamento', 'cargo']);
        	}]);
        }, 'ausencias' => function($query){
        	$query->with(['colaborador' => function($query){
        		$query->with(['centro_contable', 'departamento', 'cargo']);
        	}]);
        }, 'incapacidades' => function($query){
        	$query->with(['colaborador' => function($query){
        		$query->with(['centro_contable', 'departamento', 'cargo']);
        	}]);
        }, 'licencias' => function($query){
        	$query->with(['colaborador' => function($query){
        		$query->with(['centro_contable', 'departamento', 'cargo']);
        	}]);
        }, 'permisos' => function($query){
        	$query->with(['colaborador' => function($query){
        		$query->with(['centro_contable', 'departamento', 'cargo']);
        	}]);
        }, 'liquidaciones' => function($query){
        	$query->with(['colaborador' => function($query){
        		$query->with(['centro_contable', 'departamento', 'cargo']);
        	}]);
        }, 'evaluaciones' => function($query){
        	$query->with(['colaborador' => function($query){
        		$query->with(['centro_contable', 'departamento', 'cargo']);
        	}]);
        }))->where('empresa_id', 1)->get();*/
        /*echo "<pre>";
        print_r($lista);
        echo "</pre>"; */
    }
    public function listar()
    {
    	$data = array(
    		"lista_cargos" => Cargos_orm::lista($this->empresa_id),
    	);
    	//Seleccionar Centros Contables
    	$cat_centros = Capsule::select(Capsule::raw("SELECT * FROM cen_centros WHERE empresa_id = :empresa_id1 AND estado='Activo' AND id NOT IN (SELECT padre_id FROM cen_centros WHERE empresa_id = :empresa_id2 AND estado='Activo') ORDER BY nombre ASC"), array(
    		'empresa_id1' => $this->empresa_id,
    		'empresa_id2' => $this->empresa_id
    	));
    	$cat_centros = (!empty($cat_centros) ? array_map(function($cat_centros){ return array("id" => $cat_centros->id, "nombre" => $cat_centros->nombre); }, $cat_centros) : "");
    	$data["lista_centros"] = $cat_centros;
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
    		'public/assets/js/plugins/jszip/jszip.min.js',
    		'public/assets/js/plugins/jszip/jszip-utils.js',
    		'public/assets/js/plugins/jszip/FileSaver.js',
    		'public/assets/js/moment-with-locales-290.js',
    		'public/assets/js/plugins/bootstrap/jquery.bootstrap-touchspin.js',
    		'public/assets/js/plugins/bootstrap/daterangepicker.js',
    		'public/assets/js/default/toast.controller.js',
    		'public/assets/js/default/formulario.js',
    	));
    	//------------------------------------------
    	// Para mensaje de creacion satisfactoria
    	//------------------------------------------
    	$mensaje = !empty($this->session->flashdata('mensaje')) ? json_encode(array('estado' => 200, 'mensaje' => $this->session->flashdata('mensaje'))) : '';
    	$this->assets->agregar_var_js(array(
    		"toast_mensaje" => $mensaje
    	));
    	//Agregra variables PHP como variables JS
    	$this->assets->agregar_var_js(array(
    		"grid_url" => 'colaboradores/ajax-listar/grid'
    	));
    	//Opcion Default
    	$menuOpciones = array();
    	//Breadcrum Array

			$breadcrumb = array(
					"titulo" => '<i class="fa fa-users"></i> Acciones de Personal',
 				"menu" => array(
					"nombre" => $this->auth->has_permission('acceso', 'planilla/crear')?"Crear":'',
				"url"	 => $this->auth->has_permission('acceso', 'planilla/crear')?"planilla/crear":'',
					"opciones" => array()
				),
				"ruta" => array(
					0 => array(
							"nombre" => "Recursos humanos",
							"activo" => false,
					 ),
						 1=> array(
								"nombre" => '<b>Acciones de personal</b>',
								"activo" => false
 						 )
				),
		);
    	//Verificar permisos para crear
    	//if($this->auth->has_permission('acceso', 'colaboradores/crear')){
    	$breadcrumb["menu"] = array(
    		"url"	=> 'javascript:',
    		"clase" 	=> 'modalOpcionesCrear',
    		"nombre" => "Crear"
    	);
    	$menuOpciones["#pagarVacacionesLnk"] = "Pagar Vacaciones";
    	$menuOpciones["#pagarLicenciasLnk"] = "Pagar Licencia";
    	$menuOpciones["#pagarLiquidacionesLnk"] = "Liquidar";
    	$menuOpciones["#exportarrLnk"] = "Exportar";
    	//}
    	$breadcrumb["menu"]["opciones"] = $menuOpciones;
    	$this->template->agregar_titulo_header('Acciones de Personal');
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
    	$estado_planilla 	= $this->input->post('estado_planilla', true);
    	$vacacion_id 	= $this->input->post('vacacion_id', true);
    	$licencia_id 	= $this->input->post('licencia_id', true);
    	$liquidacion_id = $this->input->post('liquidacion_id', true);
    	$nombre_colaborador = $this->input->post('nombre_colaborador', true);
    	$no_colaborador = $this->input->post('no_colaborador', true);
    	$cedula 		= $this->input->post('cedula', true);
    	$centro_id 		= $this->input->post('centro_id', true);
    	$cargo_id 		= $this->input->post('cargo_id', true);
    	$tipo_accion 	= $this->input->post('tipo_accion', true);
    	$colaborador_id = $this->input->post('colaborador_id', true);
    	$estado = $this->input->post('estado', true);
    	$fecha_desde 	= $this->input->post('fecha_desde', true);
    	$fecha_hasta 	= $this->input->post('fecha_hasta', true);
    	if(!empty($nombre_colaborador)){
    		$clause["nombre_completo"] = array("LIKE", "%$nombre_colaborador%");
    	}
    	if(!empty($no_colaborador)){
    		$clause["no_colaborador"] = array('LIKE', "%$no_colaborador%");
    	}
    	if(!empty($cedula)){
    		$clause["cedula"] = array('LIKE', "%$cedula%");
    	}
    	if(!empty($centro_id)){
    		$clause["centro_contable_id"] = $centro_id;
    	}
    	if(!empty($cargo_id)){
    		$clause["cargo_id"] = $cargo_id;
    	}
    	if(!empty($tipo_accion)){
    		$clause["accionable_type"] = array('LIKE', "%$tipo_accion%");
    	}
    	if(!empty($vacacion_id)){
    		$clause["vacacion_id"] = $vacacion_id;
    	}
    	if(!empty($licencia_id)){
    		$clause["licencia_id"] = $licencia_id;
    	}
    	if(!empty($liquidacion_id)){
    		$clause["liquidacion_id"] = $liquidacion_id;
    	}
    	if(!empty($colaborador_id)){
    		$clause["colaborador_id"] = $colaborador_id;
    	}
    	if(!empty($estado)){
    		$clause["estado"] = array('LIKE', "%$estado%");
    	}
    	if( !empty($fecha_desde)){
    		$fecha_desde = str_replace('/', '-', $fecha_desde);
    		$fecha_inicio = date("Y-m-d H:i:s", strtotime($fecha_desde));
    		$clause["created_at"] = array('>=', $fecha_inicio);
    	}
    	if( !empty($fecha_hasta)){
    		$fecha_hasta = str_replace('/', '-', $fecha_hasta);
    		$fecha_fin = date("Y-m-d 23:59:59", strtotime($fecha_hasta));
    		$clause["created_at@"] = array('<=', $fecha_fin);
    	}
    	list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
    	//$count = count(self::listarAccionesPersonales($clause, NULL, NULL, NULL, NULL));
    	$count = Accion_personal_orm::listar($clause, NULL, NULL, NULL, NULL)->count();
    	list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
    	//$rows = self::listarAccionesPersonales($clause, $sidx, $sord, $limit, $start);
    	$rows = Accion_personal_orm::listar($clause, $sidx, $sord, $limit, $start);
    	//Constructing a JSON
    	$response = new stdClass();
    	$response->page     = $page;
    	$response->total    = $total_pages;
    	$response->records  = $count;
    	$response->result 	= array();
    	$i=0;
    	if(!empty($rows->toArray())){
    		foreach ($rows->toArray() AS $i => $row){
                $modelo_accion = 'Flexio\Modulo\AccionPersonal\Models\AccionPersonal';
    			$tipo_accion = strtolower(str_replace("_trabajo", "", str_replace("_orm", "", $row['accionable_type'])));
    			$estado = Util::verificar_valor($row[$tipo_accion]['estado']['etiqueta']);
    			$estado_color = trim($estado) == "Aprobado" ? 'background-color:#5CB85C' : (trim($estado) == "Enviado" ? 'background-color: #f8ac59' : 'background-color: red');
    			$centro_contable = Util::verificar_valor($row['centro_contable']);
    			$area_negocio = Util::verificar_valor($row['departamento']);
    			$area_negocio = !empty($area_negocio) ? "/".$area_negocio : "";
    			$archivo_ruta = !empty($row[$tipo_accion]['archivo_ruta']) ? $row[$tipo_accion]['archivo_ruta'] : "";
    			$archivo_nombre = !empty($row[$tipo_accion]['archivo_nombre']) ? $row[$tipo_accion]['archivo_nombre'] : "";
    			$hidden_options = "";
    			$link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row['id'] .'" data-accion-id="'. $row[$tipo_accion]['id'] .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
    			$hidden_options .= '<a href="#" data-formulario="'. strtolower($tipo_accion) .'" data-id="'. $row['id'] .'" data-accion-id="'. $row[$tipo_accion]['id'] .'" class="btn btn-block btn-outline btn-success verDetalle">Ver Detalle</a>';
			 if(preg_match("/planilla/i", $_SERVER['HTTP_REFERER'])){

				 if(!empty($row['liquidaciones'])){
				 		$hidden_options .= '<a href="'. base_url('planilla/reporte-liquidacion/'.bin2hex($row[$tipo_accion]['colaborador']['uuid_colaborador'])."~".$row[$tipo_accion]['id']) .'"  data-id="'. $row['id'] .'"  class="btn btn-block btn-outline btn-success">Ver Detalle Planilla</a>';
					}
				 	if(!empty($row['vacaciones'])){
 			 		if($estado_planilla == 'cerrada'){
						$hidden_options .= '<a href="'. base_url('planilla/ver_reporte_cerradas/'.$row[$tipo_accion]['colaborador']['uuid_colaborador']."~".$row[$tipo_accion]['id']."~vacacion") .'"  data-id="'. $row['id'] .'"  class="btn btn-block btn-outline btn-success">Ver Detalle Planilla</a>';
					}
					else{
						$hidden_options .= '<a href="'. base_url('planilla/ver_reporte_vacaciones/'.$row[$tipo_accion]['colaborador']['uuid_colaborador']."~".$row[$tipo_accion]['id']) .'"  data-id="'. $row['id'] .'"  class="btn btn-block btn-outline btn-success">Ver Detalle Planilla</a>';
					}
				}


				}
     			if(preg_match("/incapacidad/i", $tipo_accion)){
    				$archivo_nombre = array();
    				$archivo_ruta = !empty($row[$tipo_accion]['certificado_ruta']) ? $row[$tipo_accion]['certificado_ruta'] : (!empty($row[$tipo_accion]['carta_ruta']) ? $row[$tipo_accion]['carta_ruta'] : "");
    				if($row[$tipo_accion]['certificado_nombre']){
    					$archivo_nombre[] = $row[$tipo_accion]['certificado_nombre'];
    				}
    				if($row[$tipo_accion]['carta_nombre']){
    					$archivo_nombre[] = $row[$tipo_accion]['carta_nombre'];
    				}
						if($row[$tipo_accion]['cons_inst_medica_nombre'])
						{
    					$archivo_nombre[] = $row[$tipo_accion]['cons_inst_medica_nombre'];
    				}
						if($row[$tipo_accion]['ord_med_hospt_nombre'])
						{
    					$archivo_nombre[] = $row[$tipo_accion]['ord_med_hospt_nombre'];
    				}
						if($row[$tipo_accion]['ord_css_pens_nombre'])
						{
    					$archivo_nombre[] = $row[$tipo_accion]['ord_css_pens_nombre'];
    				}
						if($row[$tipo_accion]['desg_sal_nombre'])
						{
    					$archivo_nombre[] = $row[$tipo_accion]['desg_sal_nombre'];
    				}
						if($row[$tipo_accion]['report_acc_trab_nombre'])
						{
    					$archivo_nombre[] = $row[$tipo_accion]['report_acc_trab_nombre'];
    				}
						if($row[$tipo_accion]['cert_incp_accid_trab_nombre'])
						{
    					$archivo_nombre[] = $row[$tipo_accion]['cert_incp_accid_trab_nombre'];
    				}
    				$archivo_nombre = json_encode($archivo_nombre);
    			}
    			//Verificar si la accion personal tiene archivo para descargar
    			if(!empty($archivo_nombre)){
    				$hidden_options .= '<a href="#" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success descargarAdjuntoBtn">Descargar</a>';
    			}
                //$accion_personal = $this->AccionPersonalRepository::where('id',$row['id'])->first();
               // if ($row['id'] == '99'){ dd($row['id'], $accion_personal->toArray());}
    			$response->rows[$i]["id"] =  $row['id'];
    			$response->rows[$i]["cell"] = array(
    				'<a href="#" data-formulario="'. strtolower($tipo_accion).'" data-id="'. $row['id'].'" data-accion-id="'. $row[$tipo_accion]['id'] .'" class="verDetalle" style="color:blue;">'. Util::verificar_valor($row['no_accion']) .'</a>',
    				ucFirst($tipo_accion),
    				'<a href="'. base_url('colaboradores/ver/'. $row[$tipo_accion]['colaborador']['uuid_colaborador']) .'" style="color:blue;">'. Util::verificar_valor($row['nombre_completo']) .'</a>',
    				Util::verificar_valor($row['cedula']),
    				$centro_contable . $area_negocio,
    				!empty($estado) ? '<label style="color:white; '. $estado_color .'" class="label">'. $estado .'</label>' : "",
    				$link_option,
    				$hidden_options,
    				$archivo_ruta,
    				$archivo_nombre,
    				$row['accionable_id']
    			);
    			$i++;
    		}
    	}
    	echo json_encode($response);
    	exit;
    }
    /**
     * Cargar Vista Parcial de Tabla de Evaluaciones
     *
     * @return void
     */
    public function ocultotabla($modulo_id=NULL)
    {
    	$this->assets->agregar_js(array(
    		'public/assets/js/modules/accion_personal/listar.js',
    		'public/assets/js/modules/accion_personal/tabla.js'
    	));
    	if(preg_match("/colaboradores/i", $this->router->fetch_class())){
    		$this->assets->agregar_js(array(
    			'public/assets/js/plugins/jszip/jszip.min.js',
    			'public/assets/js/plugins/jszip/jszip-utils.js',
    			'public/assets/js/plugins/jszip/FileSaver.js',
    		));
    	}
    	//Filtrar seleccion en tabla para modulo de Planilla
    	if(preg_match("/planilla/i", $this->router->fetch_class())){
    		if(is_array($modulo_id)){
    			$index = "";
    			$moduloval = "";
    			foreach ($modulo_id AS $index => $value) {
    				$index = preg_replace("/(es|s)$/i", "_id", $index);
    				$moduloval = $value;
    			}
    			if(!empty($index)){
    				$this->assets->agregar_var_js(array(
    					$index => json_encode($moduloval)
    				));
    			}
    		}else{
    			$this->assets->agregar_var_js(array(
    				"modulo_id" => $modulo_id
    			));
    		}
    	}
    	$this->load->view('tabla');
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
    function evaluacion($colaborador_uuid=NULL)
    {
    	$data = array();
    	$mensaje = array();
    	$titulo_formulario = "Formulario Evaluacion";
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar();
    }
    function crear($formulario=NULL, $id=NULL)
    {
        //$id = $this->input->post("ids");
       // dd($id);
    	$data = array();
    	$mensaje = array();
    	//Verificar si existe variable $formulario
    	if($id != NULL){
  			$titulo_formulario = '<i class="fa fa-users"></i> Acci&oacute;n Personal: Editar';
				$breadcrumb = array(
					"titulo" => $titulo_formulario,
				);



            $accion_personal = $this->AccionPersonalRepository->find($id);
           // dd($accion_personal);
	        $accion_personal->load('comentario_timeline');
            // dd($accion_personal);
    		$this->assets->agregar_var_js(array(
    				"formulario_seleccionado" => $formulario,
            "comentario_accion_personal" => (isset($accion_personal->comentario_timeline)) ? $accion_personal->comentario_timeline : ""
    		));
            $data['vista'] = 'ver';
						$detalle = 'Detalle';
    	}else{
            $titulo_formulario = '<i class="fa fa-users"></i> Acci&oacute;n Personal: Crear';
            $this->assets->agregar_var_js(array(
                "formulario_seleccionado" => $formulario
            ));
            $data['vista'] = 'crear';
						$detalle = 'Crear';
        }

				$breadcrumb = array(
						"titulo" => '<i class="fa fa-users"></i> Acciones de Personal',
					"menu" => array(
						"nombre" => $this->auth->has_permission('acceso', 'planilla/crear')?"Crear":'',
					"url"	 => $this->auth->has_permission('acceso', 'planilla/crear')?"planilla/crear":'',
						"opciones" => array()
					),
					"ruta" => array(
						0 => array(
								"nombre" => "Recursos humanos",
								"activo" => false,
						 ),
							 1=> array(
									"nombre" => 'Acciones de personal',
									"activo" => false,
									"url"=>"accion_personal/listar"
							 ),
							 2=> array(
								 "nombre" => '<b>'.$detalle.'</b>',
								 "activo" => true
							)
					),
			);
    	$colaboradores = Colaboradores_orm::lista($this->empresa_id);
    	$colaboradores = (!empty($colaboradores) ? array_map(function($colaboradores){ return array("id" => $colaboradores["id"], "nombre" => $colaboradores["nombre"]." ".$colaboradores["apellido"]); }, $colaboradores) : "");
    	$data["colaboradores"] = $colaboradores;
    	/*$this->assets->agregar_var_js(array(
    		"colaboradores" => json_encode($colaboradores),
    	));*/
    	$this->assets->agregar_css(array(
    		'public/assets/css/plugins/bootstrap/awesome-bootstrap-checkbox.css',
    		'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
    		'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
    		'public/assets/css/plugins/jquery/chosen/chosen.min.css',
    	));
    	$this->assets->agregar_js(array(
    		'public/assets/js/default/jquery-ui.min.js',
    		'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
    		'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
    		'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
    		'public/assets/js/moment-with-locales-290.js',
    		'public/assets/js/plugins/bootstrap/daterangepicker.js',
    		'public/assets/js/plugins/jquery/chosen.jquery.min.js',
    		'public/assets/js/default/formulario.js',
    		'public/assets/js/modules/accion_personal/crear.js',
            'public/resources/compile/modulos/accion_personal/comentario-accion-personal.js'
    	));
    	$this->template->agregar_titulo_header('Acci&oacute;n Personal');
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
}

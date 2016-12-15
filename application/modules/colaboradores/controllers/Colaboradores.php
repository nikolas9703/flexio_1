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
use League\Csv\Writer as Writer;
use Carbon\Carbon;
use Flexio\Modulo\Acreedores\Repository\AcreedoresRepository as acreedoresRep;
use Flexio\Modulo\Plantillas\Repository\PlantillaSolicitadaRepository as plantillaSolRep;
use Flexio\Modulo\Plantillas\Repository\PlantillaRepository as PlantillaRepository;
use Flexio\Modulo\Colaboradores\Models\Colaboradores as ColaboradoresModel;
use Flexio\Modulo\Colaboradores\Repository\ColaboradoresRepository as ColaboradoresRepository;
use Flexio\Modulo\Colaboradores\Models\Familia as FamiliaModel;

class Colaboradores extends CRM_Controller
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
    protected $PlantillaRepository;
    protected $DocumentosRepository;
    private  $ColaboradoresRepository;

	/**
	 * @var string
	 */
	protected $upload_folder = './public/uploads/';

	function __construct()
    {
        parent::__construct();

        $this->load->model('colaboradores_orm');
        $this->load->model('colaboradores_contratos_orm');

        $this->load->model('beneficiarios_orm');
        $this->load->model('dependientes_orm');
        $this->load->model('deducciones_orm');
        $this->load->model('estudios_orm');
        $this->load->model('estado_orm');
        $this->load->model('catalogo_requisitos_orm');
        $this->load->model('requisitos_orm');
        //$this->load->model('evaluaciones_orm');

        $this->load->model('descuentos/DescuentosCat_orm');

        $this->load->model('entrega_inventario_orm');
        $this->load->model('configuracion_rrhh/tiempo_contratacion_orm');
        $this->load->model('configuracion_rrhh/cargos_orm');
        $this->load->model('configuracion_rrhh/departamentos_orm');
        $this->load->model('contabilidad/centros_orm');
        $this->load->model('experiencia_laboral_orm');
        $this->load->model('usuarios/usuario_orm');
        $this->load->model('usuarios/empresa_orm');
        $this->load->model("inventarios/Items_orm");
        $this->load->model("inventarios/Items_categorias_orm");
        $this->load->model("entradas/Entradas_orm");
        $this->load->model("centros/Centros_orm");
        $this->load->model("ordenes/Ordenes_orm");
        $this->load->model("ordenes/Ordenes_items_orm");
        $this->load->model("ordenes_ventas/Orden_ventas_orm");
        $this->load->model("ordenes_ventas/Ordenes_venta_item_orm");
        $this->load->model("consumos/Consumos_orm");
        $this->load->model("consumos/Consumos_items_orm");
        $this->load->model("traslados/Traslados_orm");
        $this->load->model("traslados/Traslados_items_orm");
        $this->load->model("salidas/Salidas_orm");
        $this->load->model("bodegas/Bodegas_orm");
        $this->load->model("modulos/Modulos_orm");
        $this->load->library('orm/catalogo_orm');

        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();

        //HMVC Load Modules
        $this->load->module(array('consumos', 'documentos'));

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

        $this->acreedoresRep = new acreedoresRep();
        $this->plantillaSolRep = new plantillaSolRep();
        $this->PlantillaRepository = new PlantillaRepository();
        $this->ColaboradoresRepository = new ColaboradoresRepository();
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
    		'public/assets/css/plugins/jquery/jquery.fileupload.css',
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
    		'public/assets/js/plugins/jquery/fileupload/jquery.fileupload.js',
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
    		"titulo" => '<i class="fa fa-users"></i> Colaboradores',
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
    	$equipoid = $this->input->post('equipoid', true);

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
    	if(!empty($equipoid)){
    		$clause["equipoid"] = $equipoid;
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

    	/*echo "<pre>";
    	print_r($rows);
    	echo "</pre>";
    	die();*/

    	if(!empty($rows->toArray())){
    		foreach ($rows->toArray() AS $i => $row){

    			$uuid_colaborador = $row['uuid_colaborador'];
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

    				if(preg_match("/colaboradores/i", $_SERVER['HTTP_REFERER']))
    				{
	    				$hidden_options .= '<a href="#collapse0000" class="btn btn-block btn-outline btn-success" data-toggle="collapse">Crear acci&oacute;n de personal</a>';

	    				$hidden_options .= '<div id="collapse0000" class="collapse">
						<ul id="accionesPersonal" class="list-group clear-list">
							<li class="m-sm"><a href="#" data-colaborador="'. $row['id'] .'" data-formulario="vacaciones">Vacaciones</a></li>
							<li class="m-sm"><a href="#" data-colaborador="'. $row['id'] .'" data-formulario="ausencias">Ausencias</a></li>
							<li class="m-sm"><a href="#" data-colaborador="'. $row['id'] .'" data-formulario="incapacidades">Incapacidades</a></li>
							<li class="m-sm"><a href="#" data-colaborador="'. $row['id'] .'" data-formulario="licencias">Licencias</a></li>
							<li class="m-sm"><a href="#" data-colaborador="'. $row['id'] .'" data-formulario="permisos">Permisos</a></li>
	    					<li class="m-sm"><a href="#" data-colaborador="'. $row['id'] .'" data-formulario="liquidaciones">Liquidaciones</a></li>
	    					<li class="m-sm"><a href="#" data-colaborador="'. $row['id'] .'" data-formulario="evaluaciones">Evaluaciones</a></li>
						</ul>
						</div>';
	    				$hidden_options .= '<a href="#" class="btn btn-block btn-outline btn-success m-t-xs crearDescuentoBtn" data-id="'. $row['id'] .'">Crear descuento directo</a>';
	    				$hidden_options .= '<a href="#" class="btn btn-block btn-outline btn-success subirArchivoBtn" data-id="'. $row['id'] .'" data-uuid="'. $uuid_colaborador .'" >Subir archivo</a>';
	    				$hidden_options .= '<a href="#" class="btn btn-block btn-outline btn-success crearConsumo" data-id="'. $row['id'] .'" data-uuid="'. $uuid_colaborador .'" >Crear consumo</a>';
	                    $hidden_options .= '<a href="#" class="btn btn-block btn-outline btn-success crearPlantillaBtn" data-id="'. $row['id'] .'">Generar carta de plantillas</a>';

	                    if($row['estado']['etiqueta'] == "Inactivo"){
	                    	$hidden_options .= '<a href="'. base_url('colaboradores/recontratacion/'. $uuid_colaborador) .'" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success">Recontratar</a>';
	                    }
    				}

    				//Este boton se muestra cuando aparece subpanel de colaboradores
    				//en modulo de equipo de trabajo.
                    if(preg_match("/talleres\/ver/i", $_SERVER['HTTP_REFERER'])){
                    	$hidden_options .= '<a href="#" id="eliminarDeEquipoTrabajo" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success">Eliminar</a>';
                    }

    				$estado = Util::verificar_valor($row["estado"]["etiqueta"]);
    				$estado_color = trim($estado) == "Activo" ? 'background-color:#5CB85C' : 'background-color: red';


    				$nombre_colaborador =  $nombre. " ". $apellido;
    				$centro_contable = Util::verificar_valor($row["centro_contable"]["nombre"]);

    				//Mostrar estos datos cuando se muestra en subpanel en modulo de Equipo de Trabajo
    				if(preg_match("/talleres\/ver/i", $_SERVER['HTTP_REFERER'])){
    					$nombre_colaborador = $nombre_colaborador. " ". Util::verificar_valor($row['cedula']);
    					$centro_contable = $centro_contable."/".Util::verificar_valor($row["departamento"]["nombre"]);
    				}

    				$response->rows[$i]["id"] = $row['id'];
    				$response->rows[$i]["cell"] = array(
    					'<a href="'. base_url('colaboradores/ver/'. $uuid_colaborador) .'" style="color:blue;">'. Util::verificar_valor($row['codigo']) .'</a>',
    					$nombre_colaborador,
    					Util::verificar_valor($row['cedula']),
    					$row['created_at'] !="" ? Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d/m/Y') : "",
    					$centro_contable,
    					Util::verificar_valor($row["departamento"]["nombre"]),
    					Util::verificar_valor($row["cargo"]["nombre"]),
    					Util::verificar_valor($row["tipo_salario"]),
    					!empty($estado) ? '<span style="color:white; '. $estado_color .'" class="btn btn-xs btn-block">'. $estado .'</span>' : "",
    					$link_option,
    					$hidden_options,
    					$row["departamento"]["id"],
    					$row["ciclo_id"]
    				);
    			}

    			$i++;
    		}
    	}

    	//print_r(Capsule::getQueryLog());

    	echo json_encode($response);
    	exit;
    }

    public function ajax_listar_recontratacion($grid=NULL)
    {
    	Capsule::enableQueryLog();

        $colaborador_id = $this->input->post('colaborador_id', true);
    	$clause = array(
    		"empresa_id" =>  $this->empresa_id,
                "colaborador_id" => $colaborador_id,
                "estado" => 1
    	);
    	$no_contrato 	= $this->input->post('no_contrato', true);
    	$fecha_contratacion_desde = $this->input->post('fecha_contratacion_desde', true);
    	$fecha_contratacion_hasta = $this->input->post('fecha_contratacion_hasta', true);

    	if(!empty($no_contrato)){
    		$clause["id"] = array('LIKE', "%$no_contrato%");
    	}
    	if( !empty($fecha_contratacion_desde)){
    		$fecha_contratacion_desde = str_replace('/', '-', $fecha_contratacion_desde);
    		$fecha_inicio = date("Y-m-d H:i:s", strtotime($fecha_contratacion_desde));
    		$clause["fecha_ingreso"] = array('>=', $fecha_inicio);
    	}
    	if( !empty($fecha_contratacion_hasta)){
    		$fecha_contratacion_hasta = str_replace('/', '-', $fecha_contratacion_hasta);
    		$fecha_fin = date("Y-m-d 23:59:59", strtotime($fecha_contratacion_hasta));
    		$clause["fecha_salida"] = array('<=', $fecha_fin);
    	}


    	list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();

    	$count = Colaboradores_contratos_orm::colaboradores_contratos($clause, NULL, NULL, NULL, NULL)->count();

    	list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

    	$rows = Colaboradores_contratos_orm::colaboradores_contratos($clause, $sidx, $sord, $limit, $start);
    	//Constructing a JSON
    	$response = new stdClass();
    	$response->page     = $page;
    	$response->total    = $total_pages;
    	$response->records  = $count;
    	$response->result 	= array();
    	$i=0;


    	if(!empty($rows->toArray())){
    		foreach ($rows->toArray() AS $i => $row){

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

                        $clause = array('empresa_id' => $this->empresa_id, 'colaborador_id' => $colaborador_id, 'plantilla_id' => 5);
                        $descargar_file = $this->plantillaSolRep->descargar_pdf($clause)->toArray();

                        foreach($descargar_file AS $info){

                        $vista_previa = "ver/" . bin2hex($info['uuid_plantilla']);
                        $hidden_options = '<a href="'. site_url() . "plantillas/" . $vista_previa .'" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success">Vista Previa</a>';

                        }

    				$response->rows[$i]["id"] = $row['id'];
    				$response->rows[$i]["cell"] = array(
    					'<a href="#" style="color:blue;">'. str_pad(Util::verificar_valor($row['id']), 4, '0', STR_PAD_LEFT) .'</a>',
    					Util::verificar_valor($row['centro_contable']['nombre']),
    				    	$row['fecha_ingreso'] !="" ? Carbon::createFromFormat('Y-m-d', $row['fecha_ingreso'])->format('d/m/Y') : "",
                                    	$row['fecha_salida'] != "" ? Carbon::createFromFormat('Y-m-d', $row['fecha_salida'])->format('d/m/Y') : "No liquidado",
                                        $link_option,
                                        $hidden_options
    				);
    			}

    			$i++;
    		}
    	}

    	//print_r(Capsule::getQueryLog());

    	echo json_encode($response);
    	exit;
    }

    /**
     * Cargar Vista Parcial de Tabla
     *
     * @return void
     */
    public function ocultotabla()
    {
    	$this->assets->agregar_js(array(
    		'public/assets/js/modules/colaboradores/tabla.js'
    	));

    	$this->load->view('tabla');
    }

    public function ocultorecontrataciontabla()
    {
    	$this->assets->agregar_js(array(
    		'public/assets/js/modules/colaboradores/tabla_recontratacion.js'
    	));

    	$this->load->view('tabla_recontratacion');
    }

    /**
     * Cargar Vista Parcial de Formulario
     *
     * @return void
     */
    public function ocultoformulario($data=NULL)
    {
    	$this->assets->agregar_js(array(
    		'public/assets/js/modules/colaboradores/crear.js'
    	));

    	$this->load->view('formulario', $data);
    }


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
     * Cargar Vista Parcial de Tabla de Entrega de Inventario
     *
     * @return void
     */
    public function ocultotablainventario()
    {
    	$this->assets->agregar_js(array(
    		'public/assets/js/modules/colaboradores/busqueda-entrega-inventario.controller.js',
    		'public/assets/js/modules/colaboradores/tabla-entrega-inventario.js'
    	));

    	$this->load->view('tabla-entrega-inventario');
    }

    function crear($colaborador_uuid=NULL)
    {
    	$data = array();
    	$mensaje = array();
    	$breadcrumb = array();
    	$menuOpciones = array();
    	$titulo_formulario = '<i class="fa fa-users"></i>  Formulario de Contrataci&oacute;n';
			//dd($_POST["campo"]);
    	if(!empty($_POST["campo"]["guardarDatosEspecificosFormBtn"]))
			{
     		if(!empty($_POST["campo"]["guardarDatosEspecificosFormBtn"]))
				{
    			$this->guardar_datos_especificos_colaborador($colaborador_uuid);
    		}
    		else if(!empty($_POST["campo"]["guardarFormulario82Btn"]))
				{

    			$this->guardar_deducciones($colaborador_uuid);
    		}
    	else
		  {

    			$this->guardar_colaborador($colaborador_uuid);
    		}
    	}

    	//Verificar si existe variable
    	//para buscar informacion del colaborador
    	$colaborador_info = array();
    	$colaborador_id = NULL;
    	if($colaborador_uuid!=NULL)
    	{
    		$colaborador_info = Colaboradores_orm::with(array('estado', 'dependientes', 'familia', 'deducciones', 'estudios', 'experiencia', 'beneficiario_principal' => function($query){
    			$query->where('tipo', '=', 'principal');
    		}, 'beneficiario_contingente' => function($query){
    			$query->where('tipo', '=', 'contingente');
    		}, 'beneficiario_pariente' => function($query){
    			$query->where('tipo', '=', 'pariente');
    		}))->where(Capsule::raw("HEX(uuid_colaborador)"), "=", $colaborador_uuid)->get()->toArray();

    		$colaborador_id = !empty($colaborador_info[0]["id"]) ? $colaborador_info[0]["id"] : "";
    	}

    	//-----------------------------------
    	// Seleccionar listado de requisitos
    	//-----------------------------------
    	$requisitos_lista = Requisitos_orm::lista_requisitos($colaborador_id);

    	//Agregar variable js de requisitos y colaborador_id
    	$this->assets->agregar_var_js(array(
    		"requisitos" => json_encode($requisitos_lista)
    	));

    	//Agregar controlador angular de requisitos
    	$this->assets->agregar_js(array(
    		'public/assets/js/modules/colaboradores/requisitos.controller.js',
    		'public/assets/js/plugins/bootstrap/ladda/spin.min.js',
    		'public/assets/js/plugins/bootstrap/ladda/ladda.min.js',
    		'public/assets/js/plugins/jquery/jquery.slimscroll.js'
    	));
    	$this->assets->agregar_css(array(
    		'public/assets/css/plugins/bootstrap/ladda/ladda-themeless.min.css',
    	));

    	$colaborador_info = array();
    	if($colaborador_uuid!=NULL)
    	{
             	$colaborador_info = Colaboradores_orm::with(array('estado', 'dependientes', 'familia', 'deducciones', 'estudios', 'experiencia', 'beneficiario_principal' => function($query){
                $query->where('tipo', '=', 'principal');
            }, 'beneficiario_contingente' => function($query){
                $query->where('tipo', '=', 'contingente');
            }, 'beneficiario_pariente' => function($query){
                $query->where('tipo', '=', 'pariente');
            }))->where(Capsule::raw("HEX(uuid_colaborador)"), "=", $colaborador_uuid)->get()->toArray();

            //Agregar Informacion del Colaborador del Array
            $data["info"] = $colaborador_info[0];

            $nombre = !empty($colaborador_info[0]["nombre"]) ? $colaborador_info[0]["nombre"] : "";
            $apellido = !empty($colaborador_info[0]["apellido"]) ? $colaborador_info[0]["apellido"] : "";
            $titulo_formulario = '<i class="fa fa-users"></i>  Detalle de '.$nombre. ' '. $apellido;
            $colaborador_coment = $this->ColaboradoresRepository->findByUuid($colaborador_uuid);
            $colaborador_coment->load('comentario_timeline');
            //----------------------------
            // Agregra variables PHP como variables JS
            //----------------------------
            $this->assets->agregar_var_js(array(
            	"colaborador_id" => $colaborador_id,
            	"colaborador_uuid" => $colaborador_uuid,
            	"permiso_editar" => $this->auth->has_permission('ver__editarColaborador', 'colaboradores/ver/(:any)') ? 'true' : 'false',
                'vista' => 'ver',
                "coment" =>(isset($colaborador_coment->comentario_timeline)) ? $colaborador_coment->comentario_timeline : "",
            ));

            //Verificar centro_contable_id y crear variable js
            if(!empty($colaborador_info[0]["centro_contable_id"]) && $colaborador_info[0]["centro_contable_id"] != ""){
            	$this->assets->agregar_var_js(array(
            		"selected_centro_contable_id" => $colaborador_info[0]["centro_contable_id"]
            	));
            }

            //Verificar cargo_id y crear variable js
            if(!empty($colaborador_info[0]["departamento_id"])){
            	$this->assets->agregar_var_js(array(
            		"selected_departamento_id" => $colaborador_info[0]["departamento_id"],
            		"s_departamento_id" => $colaborador_info[0]["departamento_id"]
            	));
            }

            //Verificar salario_mensual y crear variable js
            if(!empty($colaborador_info[0]["salario_mensual"]) && $colaborador_info[0]["salario_mensual"] != ""){
            	$this->assets->agregar_var_js(array(
            		"salario_mensual" => $colaborador_info[0]["salario_mensual"]
            	));
            }

            //Verificar cargo_id y crear variable js
            if(!empty($colaborador_info[0]["cargo_id"]) && $colaborador_info[0]["cargo_id"] != ""){
            	$this->assets->agregar_var_js(array(
            		"cargo_id" => $colaborador_info[0]["cargo_id"]
            	));
            }

            //Verificar rata_hora y crear variable js
            if(!empty($colaborador_info[0]["rata_hora"]) && $colaborador_info[0]["rata_hora"] != ""){
            	$this->assets->agregar_var_js(array(
            		"rata_hora" => $colaborador_info[0]["rata_hora"]
            	));
            }

            //Verificar fecha_inicio_labores y crear variable js
            if(!empty($colaborador_info[0]["fecha_inicio_labores"]) && $colaborador_info[0]["fecha_inicio_labores"] != ""){
            	$this->assets->agregar_var_js(array(
            		"fecha_inicio_labores" => date("d/m/Y", strtotime($colaborador_info[0]["fecha_inicio_labores"]))
            	));
            }

            //------------------------------------------
            // Para mensaje de creacion satisfactoria
            //------------------------------------------
           /* $mensaje = !empty($this->session->flashdata('mensaje')) ? json_encode(array('estado' => 200, 'mensaje' => $this->session->flashdata('mensaje'))) : '';
            $seccion_accordion = !empty($this->session->flashdata('seccion-accordion')) ? $this->session->flashdata('seccion-accordion') : '';
            $this->assets->agregar_var_js(array(
            	"toast_mensaje" => $mensaje
            ));

            if(!empty($seccion_accordion)){
            	$this->assets->agregar_var_js(array(
            		"seccion_accordion" => $seccion_accordion
            	));
            }*/

            //------------------------------------------
            // Seccion: Evaluaciones
            //------------------------------------------
            // Catalogos para busqueda de Evaluaciones
            //------------------------------------------
            /*$cat_tipo_evaluaciones = Capsule::select(Capsule::raw("SELECT id_cat AS id, etiqueta AS nombre FROM col_colaboradores_cat WHERE id_campo = 154"));
            $cat_centros = Capsule::select(Capsule::raw("SELECT * FROM cen_centros WHERE empresa_id = :empresa_id1 AND id NOT IN (SELECT padre_id FROM cen_centros WHERE empresa_id = :empresa_id2) ORDER BY nombre ASC"), array(
            	'empresa_id1' => $this->empresa_id,
            	'empresa_id2' => $this->empresa_id
            ));
            $cat_centros = (!empty($cat_centros) ? array_map(function($cat_centros){ return array("id" => $cat_centros->id, "nombre" => $cat_centros->nombre); }, $cat_centros) : "");
            $cat_resultados = Catalogo_orm::where("identificador", "Calificacion 1")->get(array(Capsule::raw("id_cat AS id"), Capsule::raw("etiqueta AS nombre")))->toArray();
            $cat_usuarios = Usuario_orm::where("estado", "Activo")->get(array("id", "nombre", "apellido", Capsule::raw("CONCAT_WS(' ', IF(nombre != '', nombre, ''), IF(apellido != '', apellido, '')) AS nombre")))->toArray();

            $this->assets->agregar_var_js(array(
            	"cat_tipo_evaluaciones" => json_encode($cat_tipo_evaluaciones),
            	"cat_centros" 		=> json_encode($cat_centros),
            	"cat_resultados" 	=> json_encode($cat_resultados),
            	"cat_usuarios" 	=> json_encode($cat_usuarios),
            ));*/

            //------------------------------------------
            // Seccion: Inventario
            //------------------------------------------
            // Catalogos para busqueda de Inventario
            //------------------------------------------
            /*$cat_duracion = Estado_orm::where("id_campo", 171)->get(array(Capsule::raw("id_cat AS id"), Capsule::raw("etiqueta AS nombre")))->toArray();
            $cat_tipo_reemplazo = Estado_orm::where("id_campo", 179)->get(array(Capsule::raw("id_cat AS id"), Capsule::raw("etiqueta AS nombre")))->toArray();
            $this->assets->agregar_var_js(array(
            	"cat_duracion" => json_encode($cat_duracion),
            	"cat_tipo_reemplazo" => json_encode($cat_tipo_reemplazo),
            ));*/

            //------------------------------------------
            // Para subpanel de Descuentos
            //------------------------------------------
            $data["descuentos"] = Descuentoscat_orm::listaDescuentos();
            $data["acreedores_list"] = $this->acreedoresRep->get(array('empresa_id' => $this->empresa_id));
            $grupo_plantillas = $this->PlantillaRepository->getAllGroupByTipo(array("estado" => 1));
            $data["plantillas"] = $grupo_plantillas;

            $menuOpciones = array(
            	"#colaboradorTab, #accionPersonalTabla" => "Ver Detalle",
            	'#vacacionTab, #accionPersonalTabla' 	=> "Crear vacaciones",
            	"#ausenciaTab, #accionPersonalTabla" 	=> "Crear ausencia",
            	"#incapacidadTab, #accionPersonalTabla" => "Crear incapacidad",
            	"#licenciaTab, #accionPersonalTabla" 	=> "Crear licencias",
            	"#permisoTab, #accionPersonalTabla" 	=> "Crear permiso",
            	"#liquidacionTab, #accionPersonalTabla" => "Crear liquidaci&oacute;n",
            	"#evaluacionTab, #accionPersonalTabla" 	=> "Crear evaluaci&oacute;n",
            	"#descuentoTab, #descuentosTabla" 	=> "Crear descuento",
            	"#plantillaTab, #plantillasTabla" 	=> "Crear Plantilla",
            	//"#j" => "Llenar plantilla",
            	//"#k" => "Subir Documento"
            );

            $breadcrumb["menu"] =array(
            	"nombre" => "Acci&oacute;n",
            	"url"	 => "#",
            	"clase" 	=> 'opcionesToggleTabs',
            	"opciones" => $menuOpciones
            );
    	}

        // Para recontratar
            $urlArray = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $segments = explode('/', $urlArray);
            $numSegments = count($segments);
            $currentSegment = $segments[$numSegments - 2];
            $currentSegment2 = $segments[$numSegments - 2];
            $data["recontratacion"] = $currentSegment;
            $data["crear_colaborador"] = $currentSegment2;

    	$this->assets->agregar_css(array(
    		'public/assets/css/default/ui/base/jquery-ui.css',
    		'public/assets/css/default/ui/base/jquery-ui.theme.css',
    		'public/assets/css/plugins/bootstrap/awesome-bootstrap-checkbox.css',
    		'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
    		'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
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
    		'public/assets/js/plugins/jquery/jquery.sticky.js',
    		'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
    		'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
    		'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/plugins/jquery/combodate/combodate.js',
    		'public/assets/js/plugins/jquery/combodate/momentjs.js',
    		'public/assets/js/plugins/jquery/chosen.jquery.min.js',
    		//'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
    		//'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
    		'public/assets/js/plugins/jquery/jquery.webui-popover.js',
    		'public/assets/js/plugins/bootstrap/jquery.bootstrap-touchspin.js',
    		'public/assets/js/moment-with-locales-290.js',
    		'public/assets/js/plugins/bootstrap/daterangepicker.js',
    		'public/assets/js/default/tabla-dinamica.jquery.js',
    		'public/assets/js/default/opciones-toggle-tabs.js',
    		'public/assets/js/default/toast.controller.js',
    		'public/assets/js/default/formulario.js',
                'public/assets/js/default/jquery.inputmask.bundle.min.js',
    	));

    	$breadcrumb["titulo"] = $titulo_formulario;

    	$this->template->agregar_titulo_header('Colaboradores');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar();
    }

    /**
     * Cargar Vista Parcial de Evaluacion
     *
     * @return void
     */
    public function formulario_evaluacion()
    {
    	$this->assets->agregar_js(array(
    		'public/assets/js/modules/colaboradores/formulario-evaluacion.controller.js'
    	));

    	$this->template->vista_parcial(array (
    		'colaboradores',
    		'evaluacion'
    	));
    }

    /**
     * Cargar Vista Parcial de Entrega de Inventario
     *
     * @return void
     */
    public function formulario_entrega_inventario()
    {
    	$this->assets->agregar_js(array(
    		'public/assets/js/modules/colaboradores/formulario-entrega-inventario.controller.js'
    	));

    	$this->template->vista_parcial(array (
    		'colaboradores',
    		'entrega_inventario'
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

    function entrega_inventario($colaborador_uuid=NULL)
    {
    	$data = array();
    	$mensaje = array();
    	$titulo_formulario = "Formulario Entrega de Inventario";

    	$this->template->agregar_contenido($data);
    	$this->template->visualizar();
    }

    private function  guardar_colaborador($colaborador_uuid=NULL)
    {
        unset($_POST["campo"]["guardarFormBtn"]);
    	// Para recontratar
            $urlArray = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $segments = explode('/', $urlArray);
            $numSegments = count($segments);
            $currentSegment = $segments[$numSegments - 2];
            $currentSegment2 = $segments[$numSegments - 1];
    	    $fieldset = Util::set_fieldset("campo");
    	    unset($fieldset[0]);

    	/**
    	 * Inicializar Transaccion
    	*/
    	Capsule::beginTransaction();

    	try {

    		//Si existe cariable $colaborador_uuid, verificar si el colaborador existe
    		$colaborador_info = Colaboradores_orm::where(Capsule::raw("HEX(uuid_colaborador)"), "=", $colaborador_uuid)->get(array("id"))->toArray();
            $datos = $_POST["campo"];
            //dd($datos);
    		if(!empty($colaborador_info))
    		{
     			//Actualizar registro
    			//Colaboradores_orm::where(Capsule::raw("HEX(uuid_colaborador)"), "=", $colaborador_uuid)->update($fieldset);
    			//$colaborador = Colaboradores_orm::find($colaborador_info[0]["id"]);

                $colaborador = $this->ColaboradoresRepository->find($colaborador_info[0]["id"]);
                $colaborador->update($fieldset);


                /*$colaborador->estado_id = $datos['estado_id'];
                $colaborador->codigo = $datos['codigo'];
                $colaborador->nombre = $datos['nombre'];
                $colaborador->segundo_nombre = $datos['segundo_nombre'];
                $colaborador->apellido = $datos['apellido'];
                $colaborador->apellido_materno = $datos['apellido_materno'];
                $colaborador->cedula = $datos['cedula'];
                $colaborador->provincia_id = !empty($datos['provincia_id'])?$datos['provincia_id']:'0';
                $colaborador->letra_id = !empty($datos['letra_id'])?$datos['letra_id']:'0';
                $colaborador->tomo = $datos['tomo'];
                $colaborador->asiento = $datos['asiento'];
                $colaborador->no_pasaporte = $datos['no_pasaporte'];
                $colaborador->seguro_social = $datos['seguro_social'];
                $colaborador->sexo_id = !empty($datos['sexo_id'])?$datos['sexo_id']:'0';
                $colaborador->estado_civil_id = !empty($datos['estado_civil_id'])?$datos['estado_civil_id']:'0';
                $colaborador->fecha_nacimiento = !empty($datos['fecha_nacimiento'])?date("Y-m-d", $datos['fecha_nacimiento']):date("Y-m-d", '0000-00-00');
                $colaborador->edad = $datos['edad'];
                $colaborador->lugar_nacimiento = $datos['lugar_nacimiento'];
                $colaborador->telefono_residencial = $datos['telefono_residencial'];
                $colaborador->celular = $datos['celular'];
                $colaborador->email = $datos['email'];
                $colaborador->direccion = $datos['direccion'];
                $colaborador->centro_contable_id = !empty($datos['centro_contable_id'])?$datos['centro_contable_id']:'0';
                $colaborador->departamento_id = !empty($datos['departamento_id'])?$datos['departamento_id']:'0';
                $colaborador->cargo_id = !empty($datos['cargo_id'])?$datos['cargo_id']:'0';
                $colaborador->tipo_salario = $datos['tipo_salario'];
                $colaborador->salario_mensual = !empty($datos['salario_mensual'])?$datos['salario_mensual']:'0';
                $colaborador->rata_hora = !empty($datos['rata_hora'])?$datos['rata_hora']:'0';
                $colaborador->ciclo_id = $datos['ciclo_id'];
                $colaborador->horas_semanales = $datos['horas_semanales'];
                $colaborador->fecha_inicio_labores = $datos['fecha_inicio_labores'];
                //$colaborador-> = $datos[''];


                $colaborador->save($colaborador);*/

    		}
    		else
    		{
    			//Crear registro por primera vez
    			$fieldset["uuid_colaborador"] = Capsule::raw("ORDER_UUID(uuid())");
    			$fieldset["codigo"] = Capsule::raw("NO_COLABORADOR('COL', ". $this->empresa_id .")");
    			$fieldset["empresa_id"] = $this->empresa_id;
    			$fieldset["creado_por"] = $this->usuario_id;

    			/**
    			 * Guardar Colaborador
    			 */
    			$colaborador = Colaboradores_orm::create($fieldset);
    		}

    		/**
    		 * Guardar Dependientes
    		 */
    		$dependientes = array();

    		/**
    		 * Verificar si existe datos en arreglo
    		*/
    		if(!empty($_POST["dependientes"]))
    		{

    			if(Util::is_array_empty($_POST["dependientes"]) == false){
    				//Recorrer los dependientes
    				$j=0;
    				foreach ($_POST["dependientes"] AS $dependiente){
    					$fieldset = Util::set_fieldset("dependientes", $j);

    					if(empty($fieldset["nombre"])){
    						continue;
    					}

    					//Verificamos el id viene con algun valor
    					if(!empty($fieldset["id"]) && $fieldset["id"] != ""){

    						//Si existe id, actualizamos la data
    						Dependientes_orm::where("id", "=", $fieldset["id"])->update($fieldset);

    					}else{

    						//De lo contrario lo insertamos en el array
    						//para guardar la data por primera vez.
    						$dependientes[] = new Dependientes_orm($fieldset);
    					}

    					$j++;
    				}
    			}

    			if(!empty($dependientes)){
    				/**
    				 * Guardar relacion de colaborador > dependientes
    				 */
    				$colaborador->dependientes()->saveMany($dependientes);
    			}

                        if($currentSegment == "recontratacion"){
                            $date = DateTime::createFromFormat('d/m/Y', $_POST["campo"]["fecha_inicio_labores"]);
                            $contratos = array();
                            $contratos["fecha_ingreso"] = $date->format("Y-m-d");
                            $contratos["fecha_salida"] = NULL;
                            $contratos["empresa_id"] = $this->empresa_id;
                            $contratos["estado"] = 1;
                            $contratos["centro_contable"] = $_POST["campo"]["centro_contable_id"];
                            $contratos["fecha_creacion"] = date("Y-m-d");

                            $contratos_guardar[] = new Colaboradores_contratos_orm($contratos);

                            $colaborador->colaboradores_contratos()->saveMany($contratos_guardar);
                        }
                        else if($currentSegment2 == "crear"){
                            $contratos = array();
                            $contratos["fecha_ingreso"] = (!empty($fieldset["fecha_inicio_labores"]))?$fieldset["fecha_inicio_labores"]:date("Y-m-d");
                            $contratos["fecha_salida"] = NULL;
                            $contratos["empresa_id"] = $this->empresa_id;
                            $contratos["estado"] = 1;
                            $contratos["centro_contable"] = (!empty($fieldset["centro_contable_id"]))?$fieldset["centro_contable_id"]:'';
                            $contratos["fecha_creacion"] = date("Y-m-d");
                            $contratos_guardar[] = new Colaboradores_contratos_orm($contratos);
                            $colaborador->colaboradores_contratos()->saveMany($contratos_guardar);
                         }

    		}

                /**
    		 * Guardar Familia
    		 */
    		$familia = array();

    		/**
    		 * Verificar si existe datos en arreglo
    		*/
    		if(!empty($_POST["familia"]))
    		{
    			if(Util::is_array_empty($_POST["familia"]) == false){
    				//Recorrer los dependientes
    				$j=0;
    				foreach ($_POST["familia"] AS $info_familia){
    					$fieldset = Util::set_fieldset("familia", $j);

    					if(empty($fieldset["nombre"])){
    						continue;
    					}

    					//Verificamos el id viene con algun valor
    					if(!empty($fieldset["id"]) && $fieldset["id"] != ""){

    						//Si existe id, actualizamos la data
    						familiaModel::where("id", "=", $fieldset["id"])->update($fieldset);

    					}else{

    						//De lo contrario lo insertamos en el array
    						//para guardar la data por primera vez.
    						$familia[] = new familiaModel($fieldset);
    					}

    					$j++;
    				}
    			}

    			if(!empty($familia)){
    				/**
    				 * Guardar relacion de colaborador > dependientes
    				 */
    				$colaborador->familia()->saveMany($familia);
    			}

    		}

    		/**
    		 * Guardar Estudios
    		 */
    		$estudios = array();

    		/**
    		 * Verificar si existe datos en arreglo
    		*/
    		if(!empty($_POST["estudios"]))
    		{
    			if(Util::is_array_empty($_POST["estudios"]) == false){
    				//Recorrer los dependientes
    				$j=0;
    				foreach ($_POST["estudios"] AS $estudio){
    					$fieldset = Util::set_fieldset("estudios", $j);

    					if(empty($fieldset["grado_academico_id"]) && empty($fieldset["titulo"])){
    						continue;
    					}

    					//Verificamos el id viene con algun valor
    					if(!empty($fieldset["id"]) && $fieldset["id"] != ""){

    						//Si existe id, actualizamos la data
    						Estudios_orm::where("id", "=", $fieldset["id"])->update($fieldset);

    					}else{

    						//De lo contrario lo insertamos en el array
    						//para guardar la data por primera vez.
    						$estudios[] = new Estudios_orm($fieldset);
    					}

    					$j++;
    				}
    			}

    			if(!empty($estudios)){
    				/**
    				 * Guardar relacion de colaborador > estudios
    				 */
    				$colaborador->estudios()->saveMany($estudios);
    			}
    		}

    		/**
    		 * Guardar Experiencia
    		 */
    		$experiencia = array();

    		/**
    		 * Verificar si existe datos en arreglo
    		*/
    		if(!empty($_POST["experiencia"]))
    		{
    			if(Util::is_array_empty($_POST["experiencia"]) == false){
    				//Recorrer los dependientes
    				$j=0;
    				foreach ($_POST["experiencia"] AS $exp){
    					$fieldset = Util::set_fieldset("experiencia", $j);

    					if(empty($fieldset["empresa"]) && empty($fieldset["ocupacion"])){
    						continue;
    					}

    					//Verificamos el id viene con algun valor
    					if(!empty($fieldset["id"]) && $fieldset["id"] != ""){

    						//Si existe id, actualizamos la data
    						Experiencia_laboral_orm::where("id", "=", $fieldset["id"])->update($fieldset);

    					}else{

    						//De lo contrario lo insertamos en el array
    						//para guardar la data por primera vez.
    						$experiencia[] = new Experiencia_laboral_orm($fieldset);
    					}

    					$j++;
    				}
    			}

    			if(!empty($experiencia)){
    				/**
    				 * Guardar relacion de colaborador > estudios
    				 */
    				$colaborador->experiencia()->saveMany($experiencia);
    			}
    		}

    	} catch(ValidationException $e){

    		// Rollback
    		Capsule::rollback();
    	}

    	// If we reach here, then
    	// data is valid and working.
    	// Commit the queries!
    	Capsule::commit();

    	//Redireccionar
    	redirect(base_url('colaboradores/listar'));
    }

    private function guardar_datos_especificos_colaborador($colaborador_uuid=NULL)
    {
    	if($colaborador_uuid==NULL){
    		return false;
    	}
			$id_forma_pago = $_POST["campo"]["forma_pago_id"];

    	unset($_POST["campo"]["guardarDatosEspecificosFormBtn"]);
    	unset($_POST["campo"]["space"]);

    	/**
    	 * Inicializar Transaccion
    	 */
    	Capsule::beginTransaction();

    	try {

    		$fieldset = Util::set_fieldset("campo");

    		/**
    		 * Guardar Datos Especificos Colaborador
    		 */
    		Colaboradores_orm::where(Capsule::raw("HEX(uuid_colaborador)"), "=", $colaborador_uuid)->update($fieldset);

    		$colaborador_info = Colaboradores_orm::where(Capsule::raw("HEX(uuid_colaborador)"), "=", $colaborador_uuid)->get(array("id"))->toArray();
    		$colaborador = Colaboradores_orm::find($colaborador_info[0]["id"]);
            $colaborador2 = $this->ColaboradoresRepository->find($colaborador_info[0]["id"]);

    		/**
    		 * Verificar si existe Beneficiarios Principales
    		 */
    		if(!empty($_POST["beneficiario_principal"]))
    		{
    			$beneficiario_principal = array();

    			if(Util::is_array_empty($_POST["beneficiario_principal"]) == false){
    				//Recorrer los beneficiarios
    				$j=0;
    				foreach ($_POST["beneficiario_principal"] AS $beneficiario){

    					$fieldset = Util::set_fieldset("beneficiario_principal", $j);

    					if(empty($fieldset["nombre"])){
    						continue;
    					}

    					//Verificamos el id viene con algun valor
    					if(!empty($fieldset["id"]) && $fieldset["id"] != ""){

    						//Si existe id, actualizamos la data
    						Beneficiarios_orm::where("id", "=", $fieldset["id"])->update($fieldset);

    					}else{

    						//De lo contrario lo insertamos en el array
    						//para guardar la data por primera vez.
    						$fieldset["tipo"] = "principal";
    						$fieldset["creado_por"] = $this->usuario_id;
    						$beneficiario_principal[] = new Beneficiarios_orm($fieldset);
    					}

    					$j++;
    				}
    			}

    			if(!empty($beneficiario_principal)){
    				/**
    				 * Guardar relacion de colaborador > beneficiarios principales
    				 */
                    $colaborador2->beneficiarios()->saveMany($beneficiario_principal);
    			}
    		}

    		/**
    		 * Verificar si existe Beneficiarios Contingentes
    		 */
    		if(!empty($_POST["beneficiario_contingente"]))
    		{
    			$beneficiario_contingente = array();

    			if(Util::is_array_empty($_POST["beneficiario_contingente"]) == false){
    				//Recorrer los beneficiarios
    				$j=0;
    				foreach ($_POST["beneficiario_contingente"] AS $beneficiario){
    					$fieldset = Util::set_fieldset("beneficiario_contingente", $j);

    					if(empty($fieldset["nombre"])){
    						continue;
    					}

    					//Verificamos el id viene con algun valor
    					if(!empty($fieldset["id"]) && $fieldset["id"] != ""){

    						//Si existe id, actualizamos la data
    						Beneficiarios_orm::where("id", "=", $fieldset["id"])->update($fieldset);

    					}else{

    						//De lo contrario lo insertamos en el array
    						//para guardar la data por primera vez.
    						$fieldset["tipo"] = "contingente";
    						$fieldset["creado_por"] = $this->usuario_id;
    						$beneficiario_contingente[] = new Beneficiarios_orm($fieldset);
    					}

    					$j++;
    				}
    			}

    			if(!empty($beneficiario_contingente)){
	    			/**
	    			 * Guardar relacion de colaborador > beneficiarios contingentes
	    			 */
	    			$colaborador->beneficiarios()->saveMany($beneficiario_contingente);
    			}
    		}

    		/**
    		 * Verificar si existe Beneficiario Pariente
    		 */
    		if(!empty($_POST["beneficiario_pariente"]))
    		{
    			$beneficiario_pariente = array();

    			if(Util::is_array_empty($_POST["beneficiario_pariente"]) == false){
    				//Recorrer los beneficiarios
    				$j=0;
    				foreach ($_POST["beneficiario_pariente"] AS $beneficiario){
    					$fieldset = Util::set_fieldset("beneficiario_pariente", $j);

    					if(empty($fieldset["nombre"])){
    						continue;
    					}

    					//Verificamos el id viene con algun valor
    					if(!empty($fieldset["id"]) && $fieldset["id"] != ""){

    						//Si existe id, actualizamos la data
    						Beneficiarios_orm::where("id", "=", $fieldset["id"])->update($fieldset);

    					}else{

    						//De lo contrario lo insertamos en el array
    						//para guardar la data por primera vez.
    						$fieldset["tipo"] = "pariente";
    						$fieldset["creado_por"] = $this->usuario_id;
    						$beneficiario_pariente[] = new Beneficiarios_orm($fieldset);
    					}

    					$j++;
    				}
    			}

    			if(!empty($beneficiario_pariente)){
	    			/**
	    			 * Guardar relacion de colaborador > beneficiarios parientes
	    			 */
	    			$colaborador->beneficiarios()->saveMany($beneficiario_pariente);
    			}
    		}



    	} catch(ValidationException $e){

    		// Rollback
    		Capsule::rollback();
    	}

    	// If we reach here, then
    	// data is valid and working.
    	// Commit the queries!
    	Capsule::commit();

    	//Redireccionar
    	redirect(base_url('colaboradores/listar'));
    }

    private function guardar_deducciones($colaborador_uuid=NULL)
    {
    	if($colaborador_uuid==NULL){
    		return false;
    	}

    	//Quitar del arreglo campos que no se van a guardar en la tabla.
    	unset($_POST["campo"]["guardarFormulario82Btn"]);
    	unset($_POST["campo"]["space"]);
    	unset($_POST["campo"]["nombre"]);
    	unset($_POST["campo"]["segundo_nombre"]);
    	unset($_POST["campo"]["apellido_materno"]);
    	unset($_POST["campo"]["cedula"]);
    	unset($_POST["campo"]["estado_civil_id"]);
    	unset($_POST["campo"]["telefono_residencial"]);
    	unset($_POST["campo"]["direccion"]);
    	unset($_POST["campo"]["firma_contributente"]);
    	unset($_POST["campo"]["firma_conyugue"]);

    	/**
    	 * Inicializar Transaccion
    	 */
    	Capsule::beginTransaction();

    	try {

    		$fieldset = Util::set_fieldset("campo");

    		//Si existe cariable $colaborador_uuid, verificar si el colaborador existe
    		$colaborador_info = Colaboradores_orm::where(Capsule::raw("HEX(uuid_colaborador)"), "=", $colaborador_uuid)->get(array("id"))->toArray();

    		//Actualizar registro
    		//Colaboradores_orm::where(Capsule::raw("HEX(uuid_colaborador)"), "=", $colaborador_uuid)->update($fieldset);
            $this->ColaboradoresRepository->findByUuid($colaborador_uuid)->update($fieldset);
    		$colaborador = Colaboradores_orm::find($colaborador_info[0]["id"]);
            $colaborador2 = $this->ColaboradoresRepository->find($colaborador_info[0]["id"]);

    		/**
    		 * Guardar Deducciones
    		 */
    		$deducciones = array();

    		/**
    		 * Verificar si existe datos en arreglo
    		*/
    		if(!empty($_POST["deducciones"]))
    		{
    			if(Util::is_array_empty($_POST["deducciones"]) == false){
    				//Recorrer los dependientes
    				$j=0;
    				foreach ($_POST["deducciones"] AS $deduccion){
    					$fieldset = Util::set_fieldset("deducciones", $j);

    					if(empty($fieldset["nombre"])){
    						continue;
    					}

    					//Verificamos el id viene con algun valor
    					if(!empty($fieldset["id"]) && $fieldset["id"] != ""){

    						//Si existe id, actualizamos la data
    						Deducciones_orm::where("id", "=", $fieldset["id"])->update($fieldset);

    					}else{

    						//De lo contrario lo insertamos en el array
    						//para guardar la data por primera vez.
    						$deducciones[] = new Deducciones_orm($fieldset);
    					}

    					$j++;
    				}
    			}

    			if(!empty($deducciones)){
    				/**
    				 * Guardar relacion de colaborador > deducciones
    				 */
    				$colaborador2->deducciones()->saveMany($deducciones);
    			}
    		}

    	} catch(ValidationException $e){

    		// Rollback
    		Capsule::rollback();
    	}

    	// If we reach here, then
    	// data is valid and working.
    	// Commit the queries!
    	Capsule::commit();

    	//Redireccionar
    	redirect(base_url('colaboradores/listar'));
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

    public function ajax_listar_entrega_inventario($grid=NULL)
    {
    	$clause = array();

    	$colaborador_id 	= $this->input->post('colaborador_id', true);
    	$nombre_item 		= $this->input->post('nombre_item', true);
    	$codigo_item 		= $this->input->post('codigo_item', true);
    	$duracion_id 		= $this->input->post('duracion_id', true);
    	$fecha_entrega_desde = $this->input->post('fecha_entrega_desde', true);
    	$fecha_entrega_hasta = $this->input->post('fecha_entrega_hasta', true);
    	$entregado_por 		= $this->input->post('entregado_por', true);
    	$tipo_reemplazo_id 	= $this->input->post('tipo_reemplazo_id', true);

    	//Filtro default evaluaciones relacionadas a colaborador
    	$clause["colaborador_id"] = $colaborador_id;
    	$clause["empresa_id"] = $this->empresa_id;

    	//Filtros de busqueda
    	if(!empty($nombre_item)){
    		$clause["nombre_item"] = array('LIKE', "%$nombre_item%");
    	}
    	if(!empty($codigo_item)){
    		$clause["codigo"] = array('LIKE', "%$codigo_item%");
    	}
    	if( !empty($fecha_entrega_desde)){
    		$fecha_entrega_desde = str_replace('/', '-', $fecha_entrega_desde);
    		$fecha_entrega_desde = date("Y-m-d H:i:s", strtotime($fecha_entrega_desde));
    		$clause["fecha_entrega"] = $fecha_entrega_desde;
    	}
    	if( !empty($fecha_entrega_hasta)){
    		$fecha_entrega_hasta = str_replace('/', '-', $fecha_entrega_hasta);
    		$fecha_entrega_hasta = date("Y-m-d 23:59:59", strtotime($fecha_entrega_hasta));
    		$clause["fecha_entrega@"] = array('<=', $fecha_entrega_hasta);
    	}
    	if(!empty($entregado_por)){
    		$clause["entregado_por"] = $entregado_por;
    	}
    	if(!empty($duracion_id)){
    		$clause["duracion_id"] = $duracion_id;
    	}
    	if(!empty($tipo_reemplazo_id)){
    		$clause["tipo_reemplazo_id"] = $tipo_reemplazo_id;
    	}

    	list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();

    	$count = Entrega_inventario_orm::listar($clause, NULL, NULL, NULL, NULL)->count();

    	list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

    	$rows = Entrega_inventario_orm::listar($clause, $sidx, $sord, $limit, $start);

    	//Constructing a JSON
    	$response = new stdClass();
    	$response->page     = $page;
    	$response->total    = $total_pages;
    	$response->records  = $count;
    	$i=0;

    	if(!empty($rows->toArray())){
    		foreach ($rows->toArray() AS $i => $row){

    			$fecha_entrega = Util::verificar_valor($row['fecha_entrega']);
    			$fecha_entrega = date("d/m/Y", strtotime($fecha_entrega));

    			$fecha_proxima_entrega = Util::verificar_valor($row['proxima_entrega']);
    			$fecha_proxima_entrega = date("d/m/Y", strtotime($fecha_proxima_entrega));

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
    				$hidden_options .= '<a href="#" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success reemplazarInventarioBtn">Reemplazar</a>';
    				//$hidden_options .= '<a href="#" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success editarInventarioBtn">Editar</a>';
    				$hidden_options .= '<a href="#" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success descargarInventarioBtn">Descargar comprobante</a>';

    				$response->rows[$i]["id"] = $row['id'];
    				$response->rows[$i]["cell"] = array(
    					Util::verificar_valor($row['cantidad']),
    					Util::verificar_valor($row["item"]["nombre"]),
    					Util::verificar_valor($row['codigo']),
    					$fecha_entrega,
    					Util::verificar_valor($row['duracion']['etiqueta']),
    					$fecha_proxima_entrega,
    					Util::verificar_valor($row['entregado_por']['nombre']) ." ". Util::verificar_valor($row['entregado_por']['apellido']),
    					Util::verificar_valor($row['reemplazo']['etiqueta']),
    					$link_option,
    					$hidden_options,
    					Util::verificar_valor($row['archivo_ruta']),
    					Util::verificar_valor($row['archivo_nombre']),
    					Util::verificar_valor($row['departamento_id']),
    				);
    			}

    			$i++;
    		}
    	}

    	echo json_encode($response);
    	exit;
    }

    function ajax_eliminar_beneficiario()
    {
    	//Just Allow ajax request
    	if(!$this->input->is_ajax_request()){
    		return false;
    	}

    	$clause = array();
    	$id = $this->input->post('id', true);

    	if(empty($id)){
    		return false;
    	}

    	/**
    	 * Inicializar Transaccion
    	 */
    	Capsule::beginTransaction();

    	try {

    		$response = Beneficiarios_orm::where('id', $id)->delete();

    	} catch(ValidationException $e){

    		// Rollback
    		Capsule::rollback();

    		log_message("error", "MODULO: ". __METHOD__ .", Linea: ". __LINE__ ." --> ". $e->getMessage()."\r\n");

    		echo json_encode(array(
    			"response" => false,
    			"mensaje" => "Hubo un error tratando de eliminar el beneficiario."
    		));
    		exit;
    	}

    	// If we reach here, then
    	// data is valid and working.
    	// Commit the queries!
    	Capsule::commit();

    	echo json_encode(array(
    		"response" => $response,
    		"mensaje" => "Se ha eliminando el beneficiario satisfactoriamente."
    	));
    	exit;
    }

    function ajax_eliminar_dependiente()
    {
    	//Just Allow ajax request
    	if(!$this->input->is_ajax_request()){
    		return false;
    	}

    	$clause = array();
    	$id = $this->input->post('id', true);

    	if(empty($id)){
    		return false;
    	}

    	/**
    	 * Inicializar Transaccion
    	 */
    	Capsule::beginTransaction();

    	try {

    		$response = Dependientes_orm::where('id', $id)->delete();

    	} catch(ValidationException $e){

    		// Rollback
    		Capsule::rollback();

    		log_message("error", "MODULO: ". __METHOD__ .", Linea: ". __LINE__ ." --> ". $e->getMessage()."\r\n");

    		echo json_encode(array(
    			"response" => false,
    			"mensaje" => "Hubo un error tratando de eliminar el dependiente."
    		));
    		exit;
    	}

    	// If we reach here, then
    	// data is valid and working.
    	// Commit the queries!
    	Capsule::commit();

    	echo json_encode(array(
    		"response" => $response,
    		"mensaje" => "Se ha eliminando el dependiente satisfactoriamente."
    	));
    	exit;
    }

    function ajax_eliminar_familia()
    {
    	//Just Allow ajax request
    	if(!$this->input->is_ajax_request()){
    		return false;
    	}

    	$clause = array();
    	$id = $this->input->post('id', true);

    	if(empty($id)){
    		return false;
    	}

    	/**
    	 * Inicializar Transaccion
    	 */
    	Capsule::beginTransaction();

    	try {

    		$response = FamiliaModel::where('id', $id)->delete();

    	} catch(ValidationException $e){

    		// Rollback
    		Capsule::rollback();

    		log_message("error", "MODULO: ". __METHOD__ .", Linea: ". __LINE__ ." --> ". $e->getMessage()."\r\n");

    		echo json_encode(array(
    			"response" => false,
    			"mensaje" => "Hubo un error tratando de eliminar el dependiente."
    		));
    		exit;
    	}

    	// If we reach here, then
    	// data is valid and working.
    	// Commit the queries!
    	Capsule::commit();

    	echo json_encode(array(
    		"response" => $response,
    		"mensaje" => "Se ha eliminando satisfactoriamente."
    	));
    	exit;
    }

    function ajax_eliminar_estudio()
    {
    	//Just Allow ajax request
    	if(!$this->input->is_ajax_request()){
    		return false;
    	}

    	$clause = array();
    	$id = $this->input->post('id', true);

    	if(empty($id)){
    		return false;
    	}

    	/**
    	 * Inicializar Transaccion
    	 */
    	Capsule::beginTransaction();

    	try {

    		$response = Estudios_orm::where('id', $id)->delete();

    	} catch(ValidationException $e){

    		// Rollback
    		Capsule::rollback();

    		log_message("error", "MODULO: ". __METHOD__ .", Linea: ". __LINE__ ." --> ". $e->getMessage()."\r\n");

    		echo json_encode(array(
    			"response" => false,
    			"mensaje" => "Hubo un error tratando de eliminar el estudio."
    		));
    		exit;
    	}

    	// If we reach here, then
    	// data is valid and working.
    	// Commit the queries!
    	Capsule::commit();

    	echo json_encode(array(
    		"response" => $response,
    		"mensaje" => "Se ha eliminando el estudio satisfactoriamente."
    	));
    	exit;
    }

    function ajax_lista_departamentos_asociado_centro()
    {
    	//Just Allow ajax request
    	/*if(!$this->input->is_ajax_request()){
    		return false;
    	}*/

    	$clause = array();
    	$centro_id = $this->input->post('centro_id', true);
    	$empresa_id = !empty($_POST["empresa_id"]) ? $this->input->post('empresa_id', true) : $this->empresa_id;

    	if(empty($centro_id)){
    		return false;
    	}

    	$response = new stdClass();
    	$response->result = Departamentos_orm::departamento_centro($centro_id, $empresa_id);
    	$json = json_encode($response);
    	echo $json;
    	exit;
    }

    public function ajax_eliminar_deduccion()
    {
    	//Just Allow ajax request
    	if(!$this->input->is_ajax_request()){
    		return false;
    	}

    	$clause = array();
    	$id = $this->input->post('id', true);

    	if(empty($id)){
    		return false;
    	}

    	/**
    	 * Inicializar Transaccion
    	 */
    	Capsule::beginTransaction();

    	try {

    		$response = Deducciones_orm::where('id', $id)->delete();

    	} catch(ValidationException $e){

    		// Rollback
    		Capsule::rollback();

    		log_message("error", "MODULO: ". __METHOD__ .", Linea: ". __LINE__ ." --> ". $e->getMessage()."\r\n");

    		echo json_encode(array(
    			"response" => false,
    			"mensaje" => "Hubo un error tratando de eliminar la deducci&oacute;n."
    		));
    		exit;
    	}

    	// If we reach here, then
    	// data is valid and working.
    	// Commit the queries!
    	Capsule::commit();

    	echo json_encode(array(
    		"response" => $response,
    		"mensaje" => "Se ha eliminando la Deducci&oacute;n satisfactoriamente."
    	));
    	exit;
    }

    public function ajax_toggle_colaborador()
    {
    	$colaboradores = $this->input->post('colaboradores', true);
    	$estado_id = $this->input->post('estado_id', true);

    	if(empty($colaboradores)){
    		return false;
    	}

    	/**
    	 * Inicializar Transaccion
    	 */
    	Capsule::beginTransaction();

    	try {

    		foreach($colaboradores AS $colaborador_id)
    		{
    			if(empty($colaborador_id)){
    				continue;
    			}

    			$colaborador = Colaboradores_orm::where('id', '=', $colaborador_id);
    			$colaborador->update(array("estado_id" => $estado_id));
    		}

    	} catch(ValidationException $e){

    		// Rollback
    		Capsule::rollback();

    		log_message("error", "MODULO: ". __METHOD__ .", Linea: ". __LINE__ ." --> ". $e->getMessage()."\r\n");

    		echo json_encode(array(
    			"mensaje" => "Hubo un error tratando de cambiar el estado."
    		));
    		exit;
    	}

    	// If we reach here, then
    	// data is valid and working.
    	// Commit the queries!
    	Capsule::commit();

    	echo json_encode(array(
    		"mensaje" => "Se ha cambiado el estado satisfactoriamente."
    	));
    	exit;
    }

    /**
     * Guardar seleccion del checkbox de formulario
     * de Requisitos.
     */
    public function ajax_guardar_seleccion_requisito()
    {
    	if(!isset($_POST["requisitos"])){
    		return false;
    	}

    	$requisitos = $this->input->post('requisitos', true);
    	$colaborador_id = $this->input->post('colaborador_id', true);

    	/**
    	 * Inicializar Transaccion
    	 */
    	Capsule::beginTransaction();

    	foreach($requisitos AS $id => $checked)
    	{
    		$entregado = $checked == 'false' ? '0' : '1';

    		try {

    			//Verificar si el requisito_id existe
    			//para el colaborador seleccionado.
    			$checkRequisito = Requisitos_orm::where("requisito_id", $id)->where("colaborador_id", $colaborador_id)->get()->toArray();

    			if(empty($checkRequisito))
    			{
    				$fieldset = array(
    					"requisito_id" 		=> $id,
    					"empresa_id"		=> $this->empresa_id,
    					"colaborador_id" 	=> $colaborador_id,
    					"entregado"			=> $entregado,
    					"creado_por" 		=> $this->usuario_id,
    				);
    				$requisito = Requisitos_orm::create($fieldset);

    			}else{

    				$requisito = Requisitos_orm::find($checkRequisito[0]["id"]);
    				$requisito->entregado = $entregado;
    				$requisito->save();
    			}

    		} catch(ValidationException $e){

    			// Rollback
    			Capsule::rollback();

    			log_message("error", "MODULO: ". __METHOD__ .", Linea: ". __LINE__ ." --> ". $e->getMessage()."\r\n");

    			echo json_encode(array(
    				"mensaje" => "Hubo un error tratando de guardar la seleccion del requisito."
    			));
    			exit;
    		}
    	}

    	// If we reach here, then
    	// data is valid and working.
    	// Commit the queries!
    	Capsule::commit();

        echo json_encode(array(
    		"mensaje" => "Se ha seleccionado el requisto como entregado."
    	));
    	exit;
    }

    /**
     * Guardar fecha de expiracion
     * del documento de  Requerido.
     */
    public function ajax_guardar_fecha_requisito()
    {
    	if(!isset($_POST["fecha"])){
    		return false;
    	}

    	$fecha_expiracion = $this->input->post('fecha', true);
    	$fecha_expiracion = date("Y-m-d", strtotime($fecha_expiracion));
    	$colaborador_id = $this->input->post('colaborador_id', true);
    	$requisito_id = $this->input->post('requisito_id', true);

    	//
    	// Inicializar Transaccion
    	//
    	Capsule::beginTransaction();

    	try {

    		//Verificar si el requisito_id existe
    		//para el colaborador seleccionado.
    		$checkRequisito = Requisitos_orm::where("requisito_id", $requisito_id)->where("colaborador_id", $colaborador_id)->get()->toArray();

    		if(empty($checkRequisito))
    		{
    			$fieldset = array(
    				"requisito_id" 		=> $requisito_id,
    				"empresa_id"		=> $this->empresa_id,
    				"colaborador_id" 	=> $colaborador_id,
    				"fecha_expiracion"	=> $fecha_expiracion,
    				"creado_por" 		=> $this->usuario_id,
    			);
    			$requisito = Requisitos_orm::create($fieldset);

    		}else{

    			$requisito = Requisitos_orm::find($checkRequisito[0]["id"]);
    			$requisito->fecha_expiracion = $fecha_expiracion;
    			$requisito->save();
    		}

    	} catch(ValidationException $e){

    		// Rollback
    		Capsule::rollback();

    		log_message("error", "MODULO: ". __METHOD__ .", Linea: ". __LINE__ ." --> ". $e->getMessage()."\r\n");

    		echo json_encode(array(
    			"mensaje" => "Hubo un error tratando de guardar la fecha de expiracion"
    		));
    		exit;
    	}

    	// If we reach here, then
    	// data is valid and working.
    	// Commit the queries!
    	Capsule::commit();

    	echo json_encode(array(
    		"mensaje" => "Se ha seleccionado la fecha de expiracion del requisito."
    	));
    	exit;
    }

    public function ajax_subir_documento_requisito()
    {
    	if(empty($_POST["requisito_id"]) && empty($_POST["colaborador_id"]) || empty($_FILES)){
    		return false;
    	}

    	$colaborador_id = $this->input->post('colaborador_id', true);
    	$requisito_id = $this->input->post('requisito_id', true);

    	$files = $_FILES;
    	$modulo_folder = $this->upload_folder . trim($this->nombre_modulo);
    	$empresa_folder = $modulo_folder ."/". $this->empresa_id;

    	//para guardar en DB
    	$archivo_ruta = "public/uploads/" . trim($this->nombre_modulo) ."/". $this->empresa_id;

    	//Verificar si existe la carpeta
    	//del modulo de colaboradores en uploads
    	if (!file_exists($modulo_folder)) {
    		try{
    			mkdir($modulo_folder, 0777, true);
    		} catch (Exception $e) {
    			log_message("error", "MODULO: Colaboradores --> ". $e->getMessage().".\r\n");
    		}
    	}

    	//Verificar si existe la carpeta
    	//de la empresa existe, dentro
    	//del modulo.
    	if (!file_exists($empresa_folder)) {
    		try{
    			mkdir($empresa_folder, 0777, true);
    		} catch (Exception $e) {
    			log_message("error", "MODULO: Colaboradores --> ". $e->getMessage().".\r\n");
    		}
    	}

    	$config = new \Flow\Config(array(
    		'tempDir' => $modulo_folder
    	));

    	$request = new \Flow\Request();

    	//Armar Nomre de archivo corto.
    	$filename = $this->input->post('flowFilename', true);
    	$extension = pathinfo($filename, PATHINFO_EXTENSION);
    	//$extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
    	$file_name = "req-". rand().time() . "." . $extension;

    	//Subir Archivo
    	if (Flow\Basic::save($empresa_folder . '/' . $file_name, $config, $request))
    	{
    		$mensaje = "Se ha subido el archivo satisfactoriamente.";

    		//
    		// Inicializar Transaccion
    		//
    		Capsule::beginTransaction();

    		try {

    			//Verificar si el requisito_id existe
    			//para el colaborador seleccionado.
    			$checkRequisito = Requisitos_orm::where("requisito_id", $requisito_id)->where("colaborador_id", $colaborador_id)->get()->toArray();

    			if(empty($checkRequisito))
    			{
    				$fieldset = array(
    					"requisito_id" 		=> $requisito_id,
    					"empresa_id"		=> $this->empresa_id,
    					"colaborador_id" 	=> $colaborador_id,
    					"archivo_ruta"		=> $archivo_ruta,
    					"archivo_nombre"	=> $file_name,
    					"creado_por" 		=> $this->usuario_id,
    				);
    				$requisito = Requisitos_orm::create($fieldset);

    			}else{

    				$requisito = Requisitos_orm::find($checkRequisito[0]["id"]);
    				$requisito->archivo_ruta = $archivo_ruta;
    				$requisito->archivo_nombre = $file_name;
    				$requisito->save();
    			}

    		} catch(ValidationException $e){

    			// Rollback
    			Capsule::rollback();

    			log_message("error", "MODULO: ". __METHOD__ .", Linea: ". __LINE__ ." --> ". $e->getMessage()."\r\n");

    			echo json_encode(array(
    				"mensaje" => "Hubo un error tratando de subir el archivo."
    			));
    			exit;
    		}

    		// If we reach here, then
    		// data is valid and working.
    		// Commit the queries!
    		Capsule::commit();

    	}else{
    		$mensaje = "Hubo un error tratando de subir el archivo.";
    	}

    	echo json_encode(array(
    		"mensaje" => $mensaje,
    		"requisitos" => Requisitos_orm::lista_requisitos($colaborador_id)
    	));
    	exit;
    }

    public function ajax_eliminar_adjunto_requisito()
    {
    	if(empty($_POST["requisito_id"]) && empty($_POST["colaborador_id"])){
    		return false;
    	}

    	$colaborador_id = $this->input->post('colaborador_id', true);
    	$requisito_id = $this->input->post('requisito_id', true);
    	$modulo_folder = $this->upload_folder . trim($this->nombre_modulo);
    	$empresa_folder = $modulo_folder ."/". $this->empresa_id;

    	//Verificar si el requisito_id existe
    	//para el colaborador seleccionado.
    	$checkRequisito = Requisitos_orm::where("requisito_id", $requisito_id)->where("colaborador_id", $colaborador_id)->get()->toArray();
    	$archivo_nombre = !empty($checkRequisito[0]["archivo_nombre"]) ? $checkRequisito[0]["archivo_nombre"] : "";

    	if(empty($checkRequisito)){
    		return false;
    	}

    	//Verificar que el archivo exista fisicmente en la carpeta.
	    if(!file_exists($empresa_folder . "/". $archivo_nombre)) {
		    log_message("error", "MODULO: Colaboradores --> El archivo de requisito (". $checkRequisito[0]["archivo_nombre"] ."), no se puede eliminar porque no existe..\r\n");
		    echo json_encode(array(
		    	"mensaje" => "Hubo un error tratando de eliminar el archivo."
		    ));
		    exit;
	    }

	    //Eliminar archivo
	    unlink($empresa_folder . "/". $archivo_nombre);

	    //
	    // Inicializar Transaccion
	    //
	    Capsule::beginTransaction();

	    try {

	    	//Eliminar completamente datos del requisito.
	    	Requisitos_orm::where('id', $checkRequisito[0]["id"])->where("empresa_id", $this->empresa_id)->delete();


    	} catch(ValidationException $e){

    		// Rollback
    		Capsule::rollback();

    		log_message("error", "MODULO: ". __METHOD__ .", Linea: ". __LINE__ ." --> ". $e->getMessage()."\r\n");

    		echo json_encode(array(
    			"mensaje" => "Hubo un error tratando de subir el archivo."
    		));
    		exit;
    	}

    	// If we reach here, then
    	// data is valid and working.
    	// Commit the queries!
    	Capsule::commit();

    	echo json_encode(array(
    		"mensaje" => "Se ha eliminado el archivo adjunto.",
    		"requisitos" => Requisitos_orm::lista_requisitos($colaborador_id)
    	));
    	exit;
    }

    public function ajax_seleccionar_items_segun_categoria()
    {
    	$categoria_id =  $this->input->post('categoria_id', true);
    	$bodega_uuid = $this->input->post('bodega_uuid', true);

    	if(empty($_POST) || empty($categoria_id) || empty($bodega_uuid)){
    		return false;
    	}

    	//Lista items activos (1) por categoria
    	$items = Items_orm::where("estado", 1)->deCategoria($categoria_id)->get(array("id", "nombre", "codigo"))->toArray();

    	if(empty($items)){
    		return false;
    	}

    	$j=0;
    	foreach ($items AS $item)
    	{
    		//Buscar existencia disponible/no disponible del item
    		$existencia = Items_orm::find($item["id"])->enInventario($bodega_uuid);

    		if(empty($existencia)){
    			continue;
    		}

    		$items[$j]["existencia"] = $existencia;

    		$j++;
    	}

    	$response = new stdClass();
    	$response->result = $items;
    	$json = json_encode($response);
    	echo $json;
    	exit;
    }


    /**
     * Guardar entrega de inventario.
     */
    public function ajax_guardar_entrega()
    {
    	/*echo "<pre>";
    	print_r($_POST);
    	echo "</pre>";
    	die();*/

    	/**
    	 * Inicializar Transaccion
    	 */
    	Capsule::beginTransaction();

    	try {

    		$entrega_id			= $this->input->post('entrega_id', true);
    		$colaborador_id 	= $this->input->post('colaborador_id', true);
    		$bodega_uuid     	= hex2bin($this->input->post('bodega_uuid', true));
    		$departamento_id 	= $this->input->post('departamento_id', true);
    		$categoria_id 		= $this->input->post('categoria_id', true);
    		$item_id 			= $this->input->post('item_id', true);
    		$codigo 			= $this->input->post('codigo', true);
    		$cantidad 			= $this->input->post('cantidad', true);
    		$duracion_id 		= $this->input->post('duracion_id', true);
    		$tipo_reemplazo_id 	= $this->input->post('tipo_reemplazo_id', true);
    		$entregado_por 		= $this->input->post('entregado_por', true);
    		$fecha_entrega 		= $this->input->post('fecha_entrega', true);
    		$fecha_entrega		= !empty($fecha_entrega) ? str_replace('/', '-', $fecha_entrega) : "";
    		$fecha_entrega 		= !empty($fecha_entrega) ? date("Y-m-d", strtotime($fecha_entrega)) : "";
    		$proxima_entrega 	= $this->input->post('proxima_entrega', true);
    		$proxima_entrega	= !empty($proxima_entrega) ? str_replace('/', '-', $proxima_entrega) : "";
    		$proxima_entrega 	= !empty($proxima_entrega) ? date("Y-m-d", strtotime($proxima_entrega)) : "";

    		//Verificar si en realidad hay disponibilidad
    		$existencia = Items_orm::find($item_id)->enInventario();
    		$cantidad_disponible = $existencia["cantidadDisponibleBase"];

    		if($cantidad_disponible <= 0){
    			echo json_encode(array(
    				"guardado" => false,
    				"mensaje" => "Ya no hay disponibidad del item seleccionado."
    			));
    			exit;
    		}else if($cantidad_disponible < $cantidad){
    			echo json_encode(array(
    				"guardado" => false,
    				"mensaje" => "La cantidad solicitada es mayor a la cantidad disponible de items $item_id.($cantidad < $cantidad_disponible)."
    			));
    			exit;
    		}

    		//Verificar si existe $evaluacion_id
    		$entrega = Entrega_inventario_orm::find($entrega_id);

    		if(!empty($entrega))
    		{
    			$entrega->empresa_id 		= $this->empresa_id;
    			$entrega->bodega_uuid 		= $bodega_uuid;
    			$entrega->departamento_id 	= $departamento_id;
    			$entrega->categoria_id 		= $categoria_id;
    			$entrega->item_id 			= $item_id;
    			$entrega->codigo 			= $codigo;
    			$entrega->cantidad 			= $cantidad;
    			$entrega->duracion_id 		= $duracion_id;
    			$entrega->tipo_reemplazo_id = $tipo_reemplazo_id;
    			$entrega->fecha_entrega 	= $fecha_entrega;
    			$entrega->proxima_entrega 	= $proxima_entrega;
    			$entrega->entregado_por 	= $entregado_por;
    			$entrega->creado_por 		= $this->usuario_id;
    			$entrega->save();
    		}
    		else
    		{
    			$fieldset = array(
    				"empresa_id" 		=> $this->empresa_id,
    				"colaborador_id" 	=> $colaborador_id,
    				"departamento_id" 	=> $departamento_id,
    				"bodega_uuid" 		=> $bodega_uuid,
    				"categoria_id"		=> $categoria_id,
    				"item_id" 			=> $item_id,
    				"codigo" 			=> $codigo,
    				"cantidad" 			=> $cantidad,
    				"duracion_id"		=> $duracion_id,
    				"tipo_reemplazo_id" => $tipo_reemplazo_id,
    				"fecha_entrega"		=> $fecha_entrega,
    				"proxima_entrega" 	=> $proxima_entrega,
    				"entregado_por"		=> $entregado_por,
    				"creado_por" 		=> $this->usuario_id,
    			);

    			//--------------------
    			// Guardar Entrega
    			//--------------------
    			$entrega = Entrega_inventario_orm::create($fieldset);

    			//--------------------
    			// Selecionar datos del
    			// colaborador
    			//--------------------
    			$colaborador = Colaboradores_orm::distinct()->where("id", $colaborador_id)->get(array("centro_contable_id", "uuid_colaborador"))->toArray();
    			$centro_contable_id = !empty($colaborador[0]["centro_contable_id"]) ? $colaborador[0]["centro_contable_id"] : "";
    			$uuid_colaborador = !empty($colaborador[0]["uuid_colaborador"]) ? $colaborador[0]["uuid_colaborador"] : "";

    			$centro = Centros_orm::distinct()->where("id", $centro_contable_id)->get()->toArray();
    			$uuid_centro = !empty($centro[0]["uuid_centro"]) ? hex2bin($centro[0]["uuid_centro"]) : "";

    			//---------------------------------
    			//
    			//
    			// Registrtar Consumo del Item
    			//
    			//
    			//---------------------------------
    			// Armar fieldset de consumo
    			$fieldset_consumo = array(
    				"uuid_consumo" 		=> Capsule::raw("ORDER_UUID(uuid())"),
    				"referencia" 		=> "Consumo Colaborador",
    				"uuid_centro" 		=> $uuid_centro,
    				"uuid_bodega" 		=> $bodega_uuid,
    				"estado_id" 		=> 1,
    				"uuid_colaborador" 	=> $uuid_colaborador,
    				"created_by" 		=> $this->usuario_id,
    				"empresa_id" 		=> $this->empresa_id,
    			);

    			//---------------------------------
    			// Seleccionar ID de la cuenta
    			//---------------------------------
    			$itemINFO = Items_orm::distinct()->where("id", $item_id)->get()->toArray();
    			$uuid_gasto = !empty($itemINFO[0]["uuid_gasto"]) ? $itemINFO[0]["uuid_gasto"] : "";
    			$cuentaINFO = Cuentas_orm::distinct()->where("uuid_cuenta", $uuid_gasto)->get(array("id"))->toArray();
    			$cuenta_id = !empty($cuentaINFO[0]["id"]) ? $cuentaINFO[0]["id"] : "";

    			//---------------------------------
    			// Seleccionar ID de la unidad Base
    			//---------------------------------
    			$unidadINFO = Items_orm::distinct()->where("id", $item_id)->with(array("item_unidades"))->get()->toArray();
    			$unidad_id = !empty($unidadINFO[0]["item_unidades"][0]["id"]) ? $unidadINFO[0]["item_unidades"][0]["id"] : "";

    			//---------------------------------
    			// Armar fieldset de consumo
    			//---------------------------------
    			$fieldset_items = array(
    				array(
    					"categoria_id" 	=> $categoria_id,
    					"item_id" 		=> $item_id,
    					"cantidad" 		=> $cantidad,
    					"unidad_id" 	=> $unidad_id,
    					"cuenta_id" 	=> $cuenta_id,
    				)
    			);

    			$this->consumos->guardar_consumo($fieldset_consumo, $fieldset_items);
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
    					log_message("error", "MODULO: ". __METHOD__ .", Linea: ". __LINE__ ." --> ". $e->getMessage()."\r\n");
    				}
    			}

    			//Verificar si existe la carpeta
    			//de la empresa existe, dentro
    			//del modulo.
    			if (!file_exists($empresa_folder)) {
    				try{
    					mkdir($empresa_folder, 0777, true);
    				} catch (Exception $e) {
    					log_message("error", "MODULO: ". __METHOD__ .", Linea: ". __LINE__ ." --> ". $e->getMessage()."\r\n");
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
    			$file_name = "entg-". rand().time() . "." . $extension;

    			//Subir Archivo
    			if(Flow\Basic::save($empresa_folder . '/' . $file_name, $config, $request)){

    				$entrega = Entrega_inventario_orm::find($entrega->id);
    				$entrega->archivo_ruta = $archivo_ruta;
    				$entrega->archivo_nombre = $file_name;
    				$entrega->save();

    			}else{
    				log_message("error", "MODULO: ". __METHOD__ .", Linea: ". __LINE__ ." --> No se pudo subir el archivo de evaluacion\r\n");
    			}
    		}

    	} catch(ValidationException $e){

    		// Rollback
    		Capsule::rollback();

    		log_message("error", "MODULO: ". __METHOD__ .", Linea: ". __LINE__ ." --> ". $e->getMessage()."\r\n");

    		echo json_encode(array(
    			"guardado" => false,
    			"mensaje" => "Hubo un error tratando de ". (!empty($entrega_id) ? "actualizar" : "guardar") ." la entrega."
    		));
    		exit;
    	}

    	// If we reach here, then
    	// data is valid and working.
    	// Commit the queries!
    	Capsule::commit();

    	if(empty($entrega_id)){
    		$this->session->set_flashdata('mensaje', "Se ha guardado la entrega satisfactoriamente.");
    		$this->session->set_flashdata('seccion-accordion', "entrega-inventario-seccion");
    	}

    	echo json_encode(array(
    		"guardado" => true,
    		"mensaje" => "Se ha ". (!empty($entrega_id) ? "actualizado" : "guardado") ." ". (!empty($tipo_reemplazo_id) ? "el reemplazo de " : "la") ." entrega de inventario satisfactoriamente."
    	));
    	exit;
    }

    public function ajax_seleccionar_entrega_inventario()
    {
    	$colaborador_id =  $this->input->post('colaborador_id', true);
    	$entrega_id =  $this->input->post('entrega_id', true);

    	if(empty($colaborador_id) || empty($entrega_id)){
    		return false;
    	}

    	$inventario = Entrega_inventario_orm::where("id", $entrega_id)->where("colaborador_id", $colaborador_id)->where("empresa_id", $this->empresa_id)->get()->toArray();

    	if(!empty($inventario)){
    		$inventario[0]["bodega_uuid"] = strtoupper(bin2hex($inventario[0]["bodega_uuid"]));
    		$inventario = $inventario[0];
    	}

    	echo json_encode($inventario);
    	exit;
    }

    public function exportar()
    {
    	if(empty($_POST)){
    		die();
    	}

    	$ids =  $this->input->post('ids', true);
		$id = explode(",", $ids);

		if(empty($id)){
			return false;
		}

		$csv = array();
		$clause = array(
			"colaborador" => $id
		);
		$colaboradores = Colaboradores_orm::listar($clause, NULL, NULL, NULL, NULL)->toArray();

		if(empty($colaboradores)){
			return false;
		}

		$i=0;
		foreach ($colaboradores AS $row)
		{
			$nombre = Util::verificar_valor($row['nombre']);
			$apellido = Util::verificar_valor($row['apellido']);

			$csvdata[$i]['codigo'] = "";
			$csvdata[$i]['nombre'] = $nombre. " ". $apellido;
			$csvdata[$i]["cedula"] = utf8_decode(Util::verificar_valor($row['cedula']));
			$csvdata[$i]["fecha"] = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d/m/Y');
			$csvdata[$i]["centro_contable"] = utf8_decode(Util::verificar_valor($row["centro_contable"]["nombre"]));
			$csvdata[$i]["departamento"] = utf8_decode(Util::verificar_valor($row["departamento"]["nombre"]));
			$csvdata[$i]["cargo"] = utf8_decode(Util::verificar_valor($row["cargo"]["nombre"]));
			$csvdata[$i]["tipo_salario"] = utf8_decode(Util::verificar_valor($row["tipo_salario"]));
			$csvdata[$i]["etiqueta"] = utf8_decode(Util::verificar_valor($row["estado"]["etiqueta"]));
			$i++;
		}

		//we create the CSV into memory
		$csv = Writer::createFromFileObject(new SplTempFileObject());
		$csv->insertOne([
			'No. Colaborador',
			'Nombre',
			'Cedula',
			'Fecha de Contratacion',
			'C. Contable',
			'Area de Negocio',
			'Cargo',
			'Tipo de Salario',
			'Estado'
		]);
		$csv->insertAll($csvdata);
		$csv->output("colaboradores-". date('ymd') .".csv");
		die;
    }

    function ajax_colaborador_info()
    {
    	$colaborador_id =  $this->input->post('colaborador_id', true);

    	if(empty($colaborador_id)){
    		return false;
    	}

    	$colaborador = Colaboradores_orm::where("id", $colaborador_id)->get(array('id', 'fecha_inicio_labores', 'ciclo_id', Capsule::raw("CONCAT_WS(' ', IF(nombre != '', nombre, ''), IF(apellido != '', apellido, '')) AS nombre")))->toArray();

    	if(empty($colaborador)){
    		return false;
    	}

    	$colaborador_id = !empty($colaborador[0]["id"]) ? $colaborador[0]["id"] : "";
    	$ciclo_id 		= !empty($colaborador[0]["ciclo_id"]) ? $colaborador[0]["ciclo_id"] : "";
    	$fecha_inicio_labores = !empty($colaborador[0]["fecha_inicio_labores"]) ? $colaborador[0]["fecha_inicio_labores"] : "";

    	$cantidad_dias_disponibles_vacaciones = 0;
    	if($fecha_inicio_labores != "" && $fecha_inicio_labores != "0000-00-00"){
	    	//
    		// Calcular cantidad de dias Laborados
    		//
	    	$fecha_inicio_labores = new Carbon($fecha_inicio_labores);
	    	$now = Carbon::now();
	    	$cantidad_dias_laborados =  ($fecha_inicio_labores->diffInDays($now))+1;

	    	//
	    	// Calcular dias disponibles de vacaciones
	    	//
	    	$cantidad_dias_disponibles_vacaciones = round(((30*$cantidad_dias_laborados)/360), PHP_ROUND_HALF_UP);
    	}

    	echo json_encode(array(
    		"colaborador_id" => $colaborador_id,
    		"dias_disponibles_vacaciones" => $cantidad_dias_disponibles_vacaciones,
    		"ciclo_id" => $ciclo_id
    	));
    	exit;
    }

    /**
     * Retornar arreglo con los
     * campos que se mostraran
     * en el formulario de subir archivos.
     *
     * @return array
     */
    function documentos_campos(){

    	$colaboradores = Colaboradores_orm::where("empresa_id", $this->empresa_id)->get(array('id', Capsule::raw("CONCAT_WS(' ', IF(nombre != '', nombre, ''), IF(apellido != '', apellido, '')) AS nombre")))->toArray();
    	$tipo_documentos = Estado_orm::where("identificador", "Tipo de Documento")->orderBy("orden", "ASC")->get(array(Capsule::raw("id_cat AS id"), Capsule::raw("etiqueta AS nombre")))->toArray();

    	return array(array(
    		"type"		=> "select",
    		"name" 		=> "colaborador_id",
    		"id" 		=> "colaborador_id",
    		"options" 	=> $colaboradores,
    		"class"		=> "form-control",
    		"disabled"	=> "disabled",
    		"data-rule-required" => "true",
    		"ng-model" 	=> "campos.colaborador_id",
    		"label"		=> "Colaborador"
    	),
    	array(
    		"type"		=> "select",
    		"name" 		=> "tipo_documento_id",
    		"id" 		=> "tipo_documento_id",
    		"options" 	=> $tipo_documentos,
    		"class"		=> "form-control",
    		"data-rule-required" => "true",
    		"ng-model" 	=> "campos.tipo_documento_id",
    		"label"		=> "Tipo de Documento"
    	),
    	array(
    		"type"		=> "text",
    		"name" 		=> "fecha_vencimiento",
    		"id" 		=> "fecha_vencimiento",
    		"model" 	=> "campos.fecha_vencimiento",
    		"class"		=> "form-control fecha-vencimiento",
    		"readonly"	=> "readonly",
    		"ng-model" 	=> "campos.fecha_vencimiento",
    		"label"		=> "Fecha de Expiracion"
    	));
    }

    function ajax_guardar_documentos()
    {
    	if(empty($_POST)){
    		return false;
    	}

    	$colaborador_id = $this->input->post('colaborador_id', true);
    	$modeloInstancia = ColaboradoresModel::find($colaborador_id);

    	$this->documentos->subir($modeloInstancia);
    }

    function ocultoformulariocomentarios() {

        $data = array();

        $this->assets->agregar_js(array(
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/modules/colaboradores/vue.comentario.js',
            'public/assets/js/modules/colaboradores/formulario_comentario.js'
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
        $comentario = ['comentario'=>$comentario,'usuario_id'=>$this->usuario_id];
        $colaboradores = $this->ColaboradoresRepository->agregarComentario($model_id, $comentario);
        $colaboradores->load('comentario_timeline');

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($colaboradores->comentario_timeline->toArray()))->_display();
        exit;
    }
}

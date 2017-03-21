<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Documentos
 *
 * Modulo para administrar documentos de modulo.
 *
 * @package    PensaApp
 * @subpackage Controller
 * @category   Controllers
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  03/03/2016
 */
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Documentos\Repository\DocumentosRepository as DocumentosRepository;
use Flexio\Modulo\Documentos\Models\Documentos as DocumentosModel;
use Flexio\Modulo\Documentos\Repository\TipoDocumentoRepository;
use Flexio\Modulo\CentrosContables\Repository\CentrosContablesRepository;
use Flexio\Modulo\Usuarios\Repository\UsuariosRepository;
use Flexio\Modulo\Catalogos\Repository\CatalogoRepository;
use Flexio\Modulo\FacturasCompras\Repository\FacturaCompraRepository;
use Flexio\Modulo\Documentos\Models\DocumentosHistorial as documentosHistorial;

//utils
use Flexio\Library\Util\FlexioAssets;
use Flexio\Library\Util\FlexioSession;
use Flexio\Library\Toast;

use Carbon\Carbon as Carbon;

class Documentos extends CRM_Controller
{

    //repositories
    protected $TipoDocumentoRepository;
    protected $CentrosContablesRepository;
    protected $UsuariosRepository;
    protected $CatalogoRepository;
    protected $documentosHistorial;

    //utils
    protected $FlexioAssets;
    protected $FlexioSession;
    protected $Toast;
	protected $numeroDocumento;

	/**
	 * @var int
	 */
	protected $empresa_id;

	/**
	 * @var int
	 */
	protected $usuario_id;

	/**
	 * @var string
	 */
	protected $upload_folder = './public/uploads/';

	/**
	 * @var string
	 */
	protected $DocumentosRepository;

	protected $facturaCompraRepository;
	protected $usuario;

	/**
	 * Descripcion
	 *
	 * @access
	 * @param
	 * @return
	 */
	function __construct() {
        parent::__construct();

        //$this->load->model('documentos_orm');
        $this->load->model('modulos/modulos_orm');
        $this->load->model('usuarios/usuario_orm');

        $this->load->library(array('campos', 'user_agent'));

        //Obtener el id_empresa de session
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $this->empresa_id = $empresa->id;

        //Obtener el id de usuario de session
        $uuid_usuario = $this->session->userdata('huuid_usuario');
        $this->usuario = Usuario_orm::findByUuid($uuid_usuario);
        $this->usuario_id = $this->usuario->id;

        $this->nombre_modulo = strtolower(get_class($this));

        //repositories
        $this->DocumentosRepository = new DocumentosRepository();
        $this->TipoDocumentoRepository = new TipoDocumentoRepository;
        $this->CentrosContablesRepository = new CentrosContablesRepository;
        $this->UsuariosRepository = new UsuariosRepository;
        $this->CatalogoRepository = new CatalogoRepository;
        $this->facturaCompraRepository = new FacturaCompraRepository();
        $this->documentosHistorial = new DocumentosHistorial();
        //utils
        $this->FlexioAssets = new FlexioAssets;
        $this->FlexioSession = new FlexioSession;
        $this->Toast = new Toast;
    }

    /**
     * Descripcion
     *
     * @access
     * @param
     * @return
     */
	public function ocultotabla($modulo_id=NULL) {
    	$this->assets->agregar_js(array(
    		'public/assets/js/modules/documentos/listar.js',
    		'public/assets/js/modules/documentos/tabla.js'
    	));

    	//Filtrar seleccion en tabla para modulo de Planilla
    	if(preg_match("/(pedido|ordenes|factura|facturas_compras|ordenes_alquiler)/i", $this->router->fetch_class())){
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
					if(preg_match("/(=)/", $modulo_id)){
						$aux = explode('=', $modulo_id);
            $this->assets->agregar_var_js(array(
                $aux[0] => $aux[1]
            ));
					}else{
						$this->assets->agregar_var_js(array(
	    				"modulo_id" => $modulo_id
	    			));
					}
    		}
    	}
		if(is_array($modulo_id)){
			$this->assets->agregar_var_js(array(
                'campo'=>collect($modulo_id)
            ));
		}

    	$this->load->view('tabla');
    }

    public function ocultotabla_main($campo_array = '')
    {
        if(is_array($campo_array))
        {
            $this->FlexioAssets->add('vars',[
                "campo" => collect($campo_array)
            ]);
        }

        $this->FlexioAssets->add('js', ['public/assets/js/modules/documentos/tabla_main.js']);

		$this->load->view('tabla_main');
	}

	public function ocultotablaV2($sp_string_var = '') {

		$this->assets->agregar_js(array(
			'public/assets/js/modules/documentos/tabla.js'
		));

		$sp_array_var = explode('=', $sp_string_var);
		if (count($sp_array_var) == 2) {

			$this->assets->agregar_var_js(array(
				$sp_array_var[0] => $sp_array_var[1]
			));

		}

		$this->load->view('tabla');
	}

	public function index()
    {

        //permisos
        $acceso = $this->auth->has_permission('acceso', 'documentos/listar');
        $this->Toast->runVerifyPermission($acceso);
        //assets
        $this->FlexioAssets->run();//css y js generales
        $this->FlexioAssets->add('js', ['public/assets/js/modules/documentos/index.js']);
        $this->FlexioAssets->add('vars', [
            "vista" => 'listar',
            "acceso" => $acceso ? 1 : 0,
            "flexio_mensaje" => Flexio\Library\Toast::getStoreFlashdata(),
			'campo' => Collect(['factura_compra'=> $this->input->post('numero_documento')])
        ]);


        //breadcrumb
    	$breadcrumb = [
            "titulo" => '<i class="fa fa-files-o"></i> Documentos',
            //la ruta debe varias segun el menu desde donde fue llamada
            //para la primera version estara quemada la ruta segun el alcance actual
            "ruta" =>[
                ["nombre" => "Compras", "activo" => false],
                ["nombre" => '<b>Documentos</b>',"activo" => true, 'url' => 'documentos/']
            ],
            "menu" => ["nombre" => "Acci&oacute;n", "url" => "#", "opciones" => []]
        ];

         $breadcrumb["menu"]["opciones"]["#DescargarZipBtnDocCompras"] = "Descargar";
      		$breadcrumb["menu"]["opciones"]["#EstadoBtnDocCompras"] = "Cambiar de estado";
      		$breadcrumb["menu"]["opciones"]["#ExportarBtnDocCompras"] = "Exportar";

        $this->DocumentosRepository = new DocumentosRepository();
        $this->TipoDocumentoRepository = new TipoDocumentoRepository;
        $this->CentrosContablesRepository = new CentrosContablesRepository;
        $this->UsuariosRepository = new UsuariosRepository;
        $this->CatalogoRepository = new CatalogoRepository;

        $clause = ['empresa_id' => $this->empresa_id, 'modulo' => 'documentos', 'campo' => ['empresa' => $this->empresa_id], 'transaccionales' => true];
        $catalogos = $this->CatalogoRepository->get($clause);
        //catalogos para los filtros
        //dd($this->TipoDocumentoRepository->get($clause)->toArray());
        $data = [
            "tipos_documento" => $this->TipoDocumentoRepository->get($clause),
            "centros_contables" => $this->CentrosContablesRepository->get($clause),
            "usuarios" => $this->UsuariosRepository->get($clause),
            "estados" => $catalogos->filter(function($row){return $row->tipo == 'estado';})
        ];

        //render
        $this->template->agregar_titulo_header('Documentos');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }


    /**
     * Descripcion
     *
     * @access
     * @param
     * @return
     */
    public function listar() {
    	$data = array();

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


			$breadcrumb = array(
				"titulo" => '<i class="fa fa-users"></i> Documentos',
				"filtro" => false,
				"ruta" => array(
						0 => array(
								"nombre" => "Recursos humanos",
								"activo" => false,
						 ),
						1=> array(
									"nombre" => '<b>Documentos</b>',
									"activo" => true,
 						),

				),
		);
    	$this->template->agregar_titulo_header('Acciones de Personal');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar($breadcrumb);
    }

    public function ajax_listar_main($grid=NULL) {

        Capsule::enableQueryLog();

    	$clause = array(
    		"empresa_id" =>  $this->empresa_id
    	);
        $clause["campo"] = $this->input->post('campo', true);

		list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();

    	$count = $this->DocumentosRepository->count($clause);

    	list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

    	$rows = $this->DocumentosRepository->get($clause, $sidx, $sord, $limit, $start);

    	//Constructing a JSON
    	$response = new stdClass();
    	$response->page     = $page;
    	$response->total    = $total_pages;
    	$response->records  = $count;
    	$response->result 	= array();

    	if($count)
        {
            foreach ($rows AS $i => $row)
            {
                $archivo_ruta = !empty($row->archivo_ruta) ? $row->archivo_ruta : "";
                $archivo_nombre = !empty($row->archivo_nombre) ? $row->archivo_nombre : "";
                $nombre_documento = !empty($row->nombre_documento) ? $row->nombre_documento : "";
				if(count($row->documentos_item) >= 1){
				$nombre_documento = $row->documentos_item->last();
				$nombre_documento = $nombre_documento->archivo_nombre;
				}
				//modal
    			$hidden_options = "";
    			$link_option = '<button data-id="'. $row->id .'" class="viewOptions btn btn-success btn-sm" type="button" ><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

    			//Verificar si la accion personal tiene archivo para descargar
    			if(!empty($archivo_nombre))
                {
                    $hidden_options .= '<a href="#" data-id="'. $row->id .'" class="btn btn-block btn-outline btn-success verAdjuntoBtn">Ver documento</a>';
                   //$hidden_options .= '<a href="#" data-id="'. $row->id .'" class="btn btn-block btn-outline btn-success descargarAdjuntoBtn">Guardar documento</a>'; Comentado por requerimiento de R Boyd
                    $hidden_options .= '<a href="#" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success descargarDocumComprasIndivBtn">Descargar documento</a>';
                    if($this->auth->has_permission('acceso', 'documentos/actualizar-documento')){
					$hidden_options .= '<a href="#" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success actualizarDocumento">Actualizar documento</a>';
					}
					$hidden_options .= '<a href="'.$row->enlace_bitacora.'" class="btn btn-block btn-outline btn-success">Ver bit&aacute;cora</a>';
                    if($row->archivado == 0){
                    $hidden_options .= '<a href="#" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success cambiarEnExpediente">En expediente</a>';
                    }
                    $hidden_options .= '<a href="#" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success cambiarEstadoDocumentos">Cambiar estado</a>';

                    if($this->auth->has_permission('acceso','documentos/borrar'))
                    {
                        $hidden_options .= '<a href="#" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success documentDeleting">Borrar documento</a>';
                    }

    			}

    			$response->rows[$i]["id"] =  $row->id;
    			$response->rows[$i]["cell"] = [
                    $row->present()->relacionado_a,
                    count($row->tipo) ?$row->tipo->nombre : '',
                    '<a href="'. base_url($archivo_ruta .'/'. $archivo_nombre) .'" target="blank" data-id="'. $row->id .'" class="verDetalle" style="color:blue;">'. $nombre_documento .'</a>',
                    count($row->centro_contable) ? $row->centro_contable->nombre : '',
                    $row->created_at->format('d/m/Y'),
                    $row->present()->fecha_documento,
                    $row->usuario->nombre_completo,
                    $row->present()->size,
                    '<a href="#" data-id="'. $row->id .'" class="cambiarEstadoDocumentos" style="color:blue;">'. $row->present()->etapa .'</a>',
                    $link_option,
    				$hidden_options,
    				$archivo_ruta,
    				$archivo_nombre,
                    $nombre_documento,
    				//$row['documentable_id']
    			];

    			$i++;
    		}
    	}
    	echo json_encode($response);
    	exit;
    }

    public function ajax_listar($grid=NULL) {
    	Capsule::enableQueryLog();

    	$clause = array(
    		"empresa_id" =>  $this->empresa_id
    	);

    	$factura_id 		= $this->input->post('factura_id', true);
        $intereses_asegurados_id_persona = $this->input->post('intereses_asegurados_id_persona', true);
        $intereses_asegurados_id_vehiculo = $this->input->post('intereses_asegurados_id_vehiculo', true);
        $intereses_asegurados_id_casco_maritimo = $this->input->post('intereses_asegurados_id_casco_maritimo', true);
        $intereses_asegurados_id_casco_aereo = $this->input->post('intereses_asegurados_id_casco_aereo', true);
        $intereses_asegurados_id_proyecto_actividad = $this->input->post('intereses_asegurados_id_proyecto_actividad', true);
        $intereses_asegurados_id_carga = $this->input->post('intereses_asegurados_id_carga', true);
        $intereses_asegurados_id_articulo = $this->input->post('intereses_asegurados_id_articulo', true);
        $intereses_asegurados_id_ubicacion = $this->input->post('intereses_asegurados_id_ubicacion', true);
        $clientes = $this->input->post('id_cliente', true);
        $cotizaciones = $this->input->post('cotizacion_id', true);
	    	$pedido_id 		= $this->input->post('pedido_id', true);
	    	$facturacompra_id 	= $this->input->post('facturacompra_id', true);
	    	$ordencompra_id 	= $this->input->post('ordencompra_id', true);
	    	$equipo_id 		= $this->input->post('equipo_id', true);
	    	$nombre_colaborador = $this->input->post('nombre_colaborador', true);
	    	$no_colaborador 	= $this->input->post('no_colaborador', true);
	    	$cedula 		= $this->input->post('cedula', true);
	    	$centro_id 		= $this->input->post('centro_id', true);
	    	$cargo_id 		= $this->input->post('cargo_id', true);
	    	$tipo_accion 		= $this->input->post('tipo_accion', true);
	    	$colaborador_id 	= $this->input->post('colaborador_id', true);
	    	$estado			= $this->input->post('estado', true);
	    	$fecha_desde 		= $this->input->post('fecha_desde', true);
	    	$fecha_hasta 		= $this->input->post('fecha_hasta', true);
	    	$contrato_id 		= $this->input->post('contrato_id', true);
	    	$ordenes_ventas_id 	= $this->input->post('ordenes_ventas_id', true);
	    	$proveedores_id 	= $this->input->post('proveedores_id', true);
	    	$items_id        	= $this->input->post('item_id', true);
	    	$caja_id        	= $this->input->post('caja_id', true);
				$orden_alquiler_id = $this->input->post('orden_alquiler_id', true);
                $solicitud_id = $this->input->post('solicitud_id', true);
        $intereses_asegurados = false;
		$retiro_id = $this->input->post('retiro_id', true);

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
        if(!empty($intereses_asegurados_id_persona)){
    		$clause["intereses_asegurados_id_persona"] = $intereses_asegurados_id_persona;
                $intereses_asegurados=true;
    	}
        if(!empty($intereses_asegurados_id_vehiculo)){
    		$clause["intereses_asegurados_id_vehiculo"] = $intereses_asegurados_id_vehiculo;
                $intereses_asegurados=true;
    	}
        if(!empty($intereses_asegurados_id_ubicacion)){
    		$clause["intereses_asegurados_id_ubicacion"] = $intereses_asegurados_id_ubicacion;
                $intereses_asegurados=true;
    	}
        if(!empty($intereses_asegurados_id_articulo)){
    		$clause["intereses_asegurados_id_articulo"] = $intereses_asegurados_id_articulo;
                $intereses_asegurados=true;
    	}
        if(!empty($intereses_asegurados_id_casco_maritimo)){
    		$clause["intereses_asegurados_id_casco_maritimo"] = $intereses_asegurados_id_casco_maritimo;
                $intereses_asegurados=true;
    	}
        if(!empty($intereses_asegurados_id_casco_aereo)){
    		$clause["intereses_asegurados_id_casco_aereo"] = $intereses_asegurados_id_casco_aereo;
                $intereses_asegurados=true;
    	}
        if(!empty($intereses_asegurados_id_proyecto_actividad)){
    		$clause["intereses_asegurados_id_proyecto_actividad"] = $intereses_asegurados_id_proyecto_actividad;
                $intereses_asegurados=true;
    	}
        if(!empty($intereses_asegurados_id_carga)){
    		$clause["intereses_asegurados_id_carga"] = $intereses_asegurados_id_carga;
                $intereses_asegurados=true;
    	}
			if(!empty($orden_alquiler_id)){
				$clause["orden_alquiler_id"] = $orden_alquiler_id;
			}

        if(!empty($clientes)){
    		$clause["id_cliente"] = $clientes;

    	}
        if(!empty($cotizaciones)){
    		$clause["cotizacion_id"] = $cotizaciones;

    	}
    	if(!empty($tipo_accion)){
    		$clause["accionable_type"] = array('LIKE', "%$tipo_accion%");
    	}
    	if(!empty($factura_id)){
    		$clause["factura_id"] = $factura_id;
    	}
    	if(!empty($pedido_id)){
    		$clause["pedido_id"] = $pedido_id;
    	}
    	if(!empty($contrato_id)){
    	    $clause["contrato_id"] = $contrato_id;
    	}
    	if(!empty($facturacompra_id)){
    		$clause["facturacompra_id"] = $facturacompra_id;
    	}
    	if(!empty($ordencompra_id)){
    		$clause["ordencompra_id"] = $ordencompra_id;
    	}
    	if(!empty($equipo_id)){
    		$clause["equipo_id"] = $equipo_id;
    	}
    	if(!empty($colaborador_id)){
    		$clause["colaborador_id"] = $colaborador_id;
    	}
        if(!empty($ordenes_ventas_id)){
    		$clause["ordenes_ventas_id"] = $ordenes_ventas_id;
    	}
        if(!empty($proveedores_id)){
    		$clause["proveedores_id"] = $proveedores_id;
    	}
        if(!empty($items_id)){
    		$clause["items_id"] = $items_id;
    	}
        if(!empty($caja_id)){
    		$clause["caja_id"] = $caja_id;
    	}
        if(!empty($solicitud_id)){
            $clause["documentable_id"] = $solicitud_id;
        }
		if(!empty($retiro_id)){
    		$clause["retiro_id"] = $retiro_id;
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
        $clause["campo"] = $this->input->post('campo', true);

    	list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();

    	$count = $this->DocumentosRepository->listar($clause, NULL, NULL, NULL, NULL)->count();

    	list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

    	$rows = $this->DocumentosRepository->listar($clause, $sidx, $sord, $limit, $start);
            

    	//Constructing a JSON
    	$response = new stdClass();
    	$response->page     = $page;
    	$response->total    = $total_pages;
    	$response->records  = $count;
    	$response->result 	= array();
    	$i=0;

    	if(!empty($rows->toArray())){
    		foreach ($rows->toArray() AS $i => $row){

    			$archivo_ruta = !empty($row['archivo_ruta']) ? $row['archivo_ruta'] : "";
    			$archivo_nombre = !empty($row['archivo_nombre']) ? $row['archivo_nombre'] : "";
                        $nombre_documento = !empty($row['nombre_documento']) ? $row['nombre_documento'] : "";
    			$fecha = !empty($row['created_at']) ? date("d/m/Y h:i a", strtotime($row['created_at'])) : "";
    		    $subido_por = !empty($row['subido_por']) ? $row['subido_por']["nombre"]." ". $row['subido_por']["apellido"] : "";

    			$hidden_options = "";
    			$link_option = '<button data-id="'. $row['id'] .'" class="viewOptions btn btn-success btn-sm" type="button" ><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

    			//Verificar si la accion personal tiene archivo para descargar
    			if(!empty($archivo_nombre)){
    				$hidden_options .= '<a href="#" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success verAdjuntoBtn">Ver documento</a>';
                                if($intereses_asegurados){
                                    $hidden_options .= '<a href="#" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success editnombreBtn">Cambiar nombre</a>';
                                }
                                $hidden_options .= '<a href="#" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success descargarAdjuntoBtn">Guardar documento</a>';
    			}

                if($this->auth->has_permission('acceso','documentos/borrar'))
                {
                    $hidden_options .= '<a href="#" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success documentDeleting">Borrar documento</a>';
                }

    			$response->rows[$i]["id"] =  $row['id'];
    			$response->rows[$i]["cell"] = array(
    				'<a href="'. base_url($archivo_ruta .'/'. $archivo_nombre) .'" target="blank" data-id="'. $row['id'] .'" class="verDetalle" style="color:blue;">'. $row['nombre_documento'] .'</a>',
    				$fecha,
    				$subido_por,
    				$link_option,
    				$hidden_options,
    				$archivo_ruta,
    				$archivo_nombre,
                                $nombre_documento,
    				$row['documentable_id']
    			);

    			$i++;
    		}
    	}
    	echo json_encode($response);
    	exit;
    }

    /**
     * Descripcion
     *
     * @access
     * @param
     * @return
     */
    public function formulario($data=NULL) {

		$this->assets->agregar_js(array(
      		'public/assets/js/modules/documentos/formulario.controller.js'
      	));

      	$this->load->view('formulario', $data);
	}

	function subir($modeloInstancia=NULL) {

		if(empty($_POST) || empty($_FILES)){
			return false;
		}

		$fieldset = array(
			"empresa_id" => $this->empresa_id,
			"subido_por" => $this->usuario_id,
		);

		$extra_datos = array();
		foreach($_POST AS $campo => $valor){
			if(preg_match("/flow/i", $campo)){
				continue;
			}

			$extra_datos[$campo] = $valor;
		}
		/* para debug origen de subida de documento, caso usuario repetido*/
		$extra_datos['environment']=[
			'server_info'=>$_SERVER,
			'request'=>$_REQUEST,
			'cookie'=>$_COOKIE,
		];

		if(!empty($extra_datos)){
			$fieldset["extra_datos"] = json_encode($extra_datos);
		}

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
			} catch (Exception $e) {
				log_message("error", "MODULO: ". __METHOD__ .", Linea: ". __LINE__ ." --> ". $e->getMessage().".\r\n");
			}
		}

		$documentos = array();
		$nombre_docu = array();
        $nombre_doc = !empty($_POST['nombre_documento']) ? $_POST['nombre_documento'] : '';
        if(is_array($nombre_doc)) {
            foreach($nombre_doc AS $value){
                $nombre_docu[] = $value;
            }
        }else {
            $nombre_docu = !empty($_POST['nombre_documento']) ? $_POST['nombre_documento'] : '';
		}

		foreach($_FILES AS $field => $_FILE)
		{
            if(!is_array($_FILE["name"])){
                $_FILE["name"]=[$_FILE["name"]];
                $_FILE["type"]=[$_FILE["type"]];
                $_FILE["tmp_name"]=[$_FILE["tmp_name"]];
                $_FILE["error"]=[$_FILE["error"]];
                $_FILE["size"]=[$_FILE["size"]];
            }
			$j=0;
			$i=0;
			foreach($_FILE AS $file)
			{


				if(empty($_FILE["name"][$j])){
					continue;
				}
				//$secuencial = $this->DocumentosRepository->listar(NULL, NULL, NULL, NULL, NULL)->count();
				$filename 	= $_FILE["name"][$j];

                $not_allowed = ["#"];
                $allowed = [""];
                $filename = str_replace($not_allowed, $allowed, $filename);
 				$type 		= $_FILE["type"][$j];
				$tmp_name 	= $_FILE["tmp_name"][$j];

				$extension = pathinfo($filename, PATHINFO_EXTENSION);
                $time = time();
				$file_name = preg_replace('/[^A-Za-z0-9\-.]/', '', $filename);
                if(move_uploaded_file($tmp_name, $empresa_folder . '/' . $time .$file_name)) {

					$documentos[$i]["archivo_ruta"] = $archivo_ruta;
					$documentos[$i]["archivo_nombre"] =  $time.$file_name;
					$documentos[$i]["nombre_documento"] = !empty($nombre_docu) ? $nombre_docu[$i] : $file_name;

					$documentos[$i] = array_merge($documentos[$i], $fieldset);

				} else{
					log_message("error", "MODULO: ". __METHOD__ .", Linea: ". __LINE__ ." --> No se pudo subir el $field.\r\n");
				}

				$j++;
				$i++;
			}
		}

		Capsule::beginTransaction();
		try{

            $response = $this->DocumentosRepository->create($modeloInstancia, $documentos);
			Capsule::commit();

		}catch(Illuminate\Database\QueryException $e){
			log_message('error', __METHOD__." -> Linea: ". __LINE__ ." --> ". $e->getMessage()."\r\n");
			Capsule::rollback();

			echo json_encode(array(
    			"guardado" => false,
    			"mensaje" => "Hubo un error tratando de guardar los documentos."
    		));
    		exit;
		}
		//Just Allow ajax request
                if(!$this->input->is_ajax_request()){
    		return false;
                }
		echo json_encode(array(
			"guardado" => true,
			"mensaje" => "Se ha guardado los documentos satisfactoriamente."
		));
		exit;
	}

    function ajax_actualizar() {
        if(empty($_POST)){
            return false;
    	}
        $id =  $this->input->post('documen_id', true);
        $nombre = $this->input->post('nombre_document', true);
        $documento = $this->DocumentosRepository->find($id);
        $porciones = explode(".", $documento->archivo_nombre);
        $documento->nombre_documento = $nombre.".".$porciones[1];
        $documento->update();
        $documento = $this->DocumentosRepository->find($id);
        if(!is_null($documento)){
            $mensaje = array('estado' => 200, 'mensaje' =>'<b>¡&Eacute;xito!</b> Se ha guardado correctamente');

        }else{
            $mensaje = array('estado' => 500, 'mensaje' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
        }
        echo json_encode($mensaje);
    }

    public function borrar()
    {
        if(empty($_POST)){
            return false;
    	}

        $document_id = $this->input->post('document_id');
        $document = $this->DocumentosRepository->find($document_id);

        if(count($document) && $document->delete())
        {
            $mensaje = array('estado' => 200, 'mensaje' =>'<b>¡&Eacute;xito!</b> Se ha eliminado el documento');
        }
        else
        {
            $mensaje = array('estado' => 500, 'mensaje' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
        }
        echo json_encode($mensaje);
    }

    function ajax_cambiar_estado() {
        if(empty($_POST)){
            return false;
        }
        $id = $this->input->post('documento_id', true);
        $etapa = $this->input->post('etapa', true);
        $documento = $this->DocumentosRepository->find($id);
        $documento->etapa = $etapa;
        $documento->update();

        echo json_encode($documento);
    	exit;
    }

    function ajax_cambiar_en_expediente() {
        if(empty($_POST)){
            return false;
        }
        $id = $this->input->post('documento_id', true);
        $documento = $this->DocumentosRepository->find($id);
        $documento->archivado = 1;
        $documento->update();

        echo json_encode($documento);
    	exit;
    }

    function info_documento() {

		$nombre_docu = array();
                $nombre_doc = !empty($_POST['nombre_documento']) ? $_POST['nombre_documento'] : '';
                if(is_array($nombre_doc)){
                foreach($nombre_doc AS $value){
                    $nombre_docu = $value;
                }
                }else{
                    $nombre_docu = !empty($_POST['nombre_documento']) ? $_POST['nombre_documento'] : '';
                }

                $archivo_ruta = "public/uploads/" . trim($this->nombre_modulo) ."/". $this->empresa_id;

		foreach($_FILES AS $field => $_FILE)
		{
			$j=0;
			$i=0;
			foreach($_FILE AS $file)
			{
				if(empty($_FILE["name"][$j])){
					continue;
				}
				$secuencial = $this->DocumentosRepository->listar(NULL, NULL, NULL, NULL, NULL)->count();
				$filename 	= $_FILE["name"][$j];

        $not_allowed = ["#"];
        $allowed = [""];
        $filename = str_replace($not_allowed, $allowed, $filename);

				$type 		= $_FILE["type"][$j];
				$tmp_name 	= $_FILE["tmp_name"][$j];

				$extension = pathinfo($filename, PATHINFO_EXTENSION);
				$file_name = preg_replace('/[^A-Za-z0-9\-.]/', '', $filename);

					$documentos[$i]["archivo_ruta"] = $archivo_ruta;
					$documentos[$i]["archivo_nombre"] = $secuencial . "-" . $file_name;
					$documentos[$i]["nombre_documento"] = !empty($nombre_docu) ? $nombre_docu : '';

				$j++;
				$i++;
			}
		}
                $string = '';
                if(isset($documentos)){
                    foreach($documentos as $valores){
                        $string .= '<p>'.$valores['archivo_nombre'].'</p>';
                    }

                }

                return $string;


	}

    function ajax_descargar_zip() {
              if(empty($_POST)){
                      return false;
          }
              $ids =  $this->input->post('ids', true);
              $id = explode(",", $ids);
              if (empty($id)) {
                      return false;
              }
              $csv = array();
              $clause = array("id" => $id);
              $documentos = $this->DocumentosRepository->descargar($clause);
              if (empty($documentos)) {
                      return false;
              }
              $zip = new ZipArchive();
              $filename = "public/uploads/documentos/documentos_compras_".date("Ymd-His").'.zip';

              if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
                      exit("cannot open <$filename>\n");
              }
              $i = 0;
              foreach ($documentos AS $row) {
                          $filepath = strtr($row->archivo_ruta."/".$row->archivo_nombre, '\\', '/');
                          if (file_exists($filepath)) {
                                  $zip->addFile($filepath, $row->archivo_nombre);
                          }
                      $i++;
              }
        $cantidad_documentos =  $zip->numFiles;
        $zip->close();
              if((int) $cantidad_documentos >0){

                  header("Content-type: application/zip");
                  header("Content-Disposition: attachment; filename=$filename");
                  header("Content-length: " . filesize($filename));
                  header("Pragma: no-cache");
                  header("Expires: 0");
                  readfile("$filename");
          unlink($filename);
              }
              else{
           $this->Toast->run("error",array("Usted no cuenta con archivos disponibles."));
                  redirect(base_url('documentos/index'));
              }
          }


	public function subir_documento($uuid_facom = null){

		$data 			= array();
		$mensaje 		= array();
		$breadcrumb 	= array();
		$clause = array();
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
			//'public/assets/js/plugins/bootstrap/daterangepicker.js',
			'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
			'public/assets/js/default/toast.controller.js',
			'public/assets/js/plugins/ckeditor/ckeditor.js',
			'public/assets/js/plugins/ckeditor/adapters/jquery.js',
			'public/assets/js/default/formulario.js',
			'public/assets/js/modules/documentos/plugins.js'

		));
		$titulo 		= '<i class="fa fa-users"></i> Documento: Subir';
		$titulo_header 	= 'Subir Documento';

		$factura =$this->facturaCompraRepository->findByUuid($uuid_facom);

		$data['factura_id'] = $factura->id;
		$data['proveedor'] = array('id'=>$factura->proveedor_id,'nombre'=>$factura->proveedor->nombre);
		$centro = $this->CentrosContablesRepository->find($factura->centro_contable_id);
		$data['centro_contable'] = array('id'=>$centro->id, 'nombre'=>$centro->nombre);
		$data['no_factura'] = $factura->factura_proveedor;
		$data['fecha'] = $factura->fecha_desde;
		$data['mensaje'] = $mensaje;
		$data['usuario'] = array('id'=>$this->usuario->id,'nombre'=>$this->usuario->nombre.' '.$this->usuario->apellido);
		$clause =  ['campo'=>['empresa'=>$this->empresa_id, 'estado'=>19]];
		//$tipo_cat = $this->TipoDocumentoRepository->get($clause);
		$data['tipo_documento'] = $this->TipoDocumentoRepository->get($clause);



		$breadcrumb["titulo"] = $titulo;
		$this->template->agregar_titulo_header($titulo_header);
		$this->template->agregar_breadcrumb($breadcrumb);
		$this->template->agregar_contenido($data);
		$this->template->visualizar();
	}


	public function guardar(){

		if(empty($_POST) || empty($_FILES)){
			return false;
		}

		$extra_datos = array();
		foreach($_POST AS $campo => $valor){
			if(preg_match("/flow/i", $campo)){
				continue;
			}
			$fieldset['fecha_documento'] =  Carbon::createFromFormat('d/m/Y',$valor['fecha_documento']);
			$fieldset['tipo_id'] = $valor['tipo_id'];
			$fieldset['etapa'] = 'por_enviar'; //autot: Cachi
			$extra_datos[$campo] = $valor;
		}
        $factura =$this->facturaCompraRepository->find($extra_datos['campo']['factura_id']);
		$fieldset["centro_contable_id"] = $factura->centro_contable_id;

		$extra_datos['campo']['relacionado_a'] = $factura->codigo.' - '.$factura->proveedor->nombre;

                $fieldset = array(
			"empresa_id" => $this->empresa_id,
			"subido_por" => $this->usuario_id,
                        "documentable_id" => $factura->id,
                        "documentable_type" => 'Flexio\Modulo\FacturasCompras\Models\FacturaCompra',
                        "centro_contable_id" => $factura->centro_contable_id,
                        "tipo_id" => $valor['tipo_id'],
                        "fecha_documento" => Carbon::createFromFormat('d/m/Y',$valor['fecha_documento']),
                        "etapa" => "por_enviar"
		);

		/* para debug origen de subida de documento, caso usuario repetido*/
		$extra_datos['environment']=[
			'server_info'=>$_SERVER,
			'request'=>$_REQUEST,
			'cookie'=>$_COOKIE,
		];

		if(!empty($extra_datos)){
			$fieldset["extra_datos"] = json_encode($extra_datos);
		}

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
		//de la empresa existe, dentro del modulo.
		if (!file_exists($empresa_folder)) {
			try{
				mkdir($empresa_folder, 0777, true);
			} catch (Exception $e) {
				log_message("error", "MODULO: ". __METHOD__ .", Linea: ". __LINE__ ." --> ". $e->getMessage().".\r\n");
			}
		}

		$mimeTypeValidator = new \FileUpload\Validator\MimeTypeValidator([]);

		// Simple path resolver, where uploads will be put
		$pathresolver = new FileUpload\PathResolver\Simple($empresa_folder);

        // The machine's filesystem
		$filesystem = new FileUpload\FileSystem\Simple();

        // FileUploader itself
		$fileupload = new FileUpload\FileUpload($_FILES['documentos'], $_SERVER);

        // Adding it all together. Note that you can use multiple validators or none at all
		$fileupload->setPathResolver($pathresolver);
		$fileupload->setFileSystem($filesystem);
		//$fileupload->addValidator($validator);


        // Doing the deed
		list($files, $headers) = $fileupload->processAll();
		$i=0;
		$documentos = array();
		foreach($files AS $file){

			$filename = $file->name;
      $not_allowed = ["#"];
      $allowed = [""];
      $filename = str_replace($not_allowed, $allowed, $filename);

			$type = $file->type;
			$tmp_name =  $file->path;
			$size  =  $file->size;

			$extra_datos['campo']['size'] = $size;

			if(!empty($extra_datos)){
				$fieldset["extra_datos"] = json_encode($extra_datos);
			}


			$documentos[$i]["archivo_ruta"] = $archivo_ruta;
			$documentos[$i]["archivo_nombre"] = $filename;

			$documentos[$i] = array_merge($documentos[$i], $fieldset);

			Capsule::beginTransaction();
			try{
				$response = $this->DocumentosRepository->createFacturasCompras($documentos);
				Capsule::commit();

			}catch(Illuminate\Database\QueryException $e){
				log_message('error', __METHOD__." -> Linea: ". __LINE__ ." --> ". $e->getMessage()."\r\n");
				Capsule::rollback();
				$mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error!</b> Hubo un error tratando de guardar los documentos.');
				$this->session->set_flashdata('mensaje', $mensaje);
				redirect(base_url('documentos/index'));
				exit;
			}

		}
		$mensaje = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado los documentos correctamente.');
		$this->session->set_flashdata('mensaje', $mensaje);
		redirect(base_url('documentos/index'));
		exit;

	}


        function historial($documento_uuid = NULL){

        $acceso = 1;
        $mensaje =  array();
        $data = array();

        $documentos = $this->DocumentosRepository->findByUuid($documento_uuid);

        if(!$this->auth->has_permission('acceso','documentos/historial') && is_null($documentos)){
            // No, tiene permiso
            $acceso = 0;
            $mensaje = array('estado'=>500, 'mensaje'=>' <b>Usted no cuenta con permiso para esta solicitud</b>','clase'=>'alert-danger');
        }

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
			//'public/assets/js/plugins/bootstrap/daterangepicker.js',
			'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
			'public/assets/js/default/toast.controller.js',
			'public/assets/js/plugins/ckeditor/ckeditor.js',
			'public/assets/js/plugins/ckeditor/adapters/jquery.js',
			'public/assets/js/default/formulario.js',
			'public/assets/js/modules/documentos/plugins.js'

		));
        $this->assets->agregar_js(array(
            'public/assets/js/modules/documentos/vue.componente.timeline.js',
            'public/assets/js/modules/documentos/vue.timeline.js',

        ));


        $breadcrumb = array(
            "titulo" => '<i class="fa fa-car"></i> Bit&aacute;cora: Documento '.$documentos->codigo,
            "ruta" => array(
                0 => array(
                    "nombre" => "Documento",
                    "activo" => false,

                )
            ),
            "filtro"    => false,
            "menu"      => array()
        );


        $documentos->load('historial');

        $this->assets->agregar_var_js(array(
            "timeline" => $documentos,
        ));
        $data['codigo'] = $documentos->codigo;
        $this->template->agregar_titulo_header('Documentos');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    function ocultotimeline(){
        $this->load->view('timeline');
    }

	/** Función para formatear arreglo de archivos**/
	function reArrayFiles($file_post) {

		$file_ary = array();
		$file_count = count($file_post['name']);
		$file_keys = array_keys($file_post);

		for ($i=0; $i<$file_count; $i++) {

			foreach ($file_keys as $key) {

				$file_ary[$i][$key] = $file_post[$key][$i];
			}
		}

		return $file_ary;
	}

    public function actualizar_documento() {

    if(empty($_POST) || empty($_FILES)){
			return false;
		}

        $documento_relacionado = $this->DocumentosRepository->find($_POST['documento_id']);

        $fieldset = array(
            'empresa_id' => $documento_relacionado->empresa_id,
            'extra_datos' => $documento_relacionado->extra_datos,
            'subido_por' => $documento_relacionado->subido_por,
            'documentable_id' => $documento_relacionado->documentable_id,
            'documentable_type' => $documento_relacionado->documentable_type,
            'centro_contable_id' => $documento_relacionado->centro_contable_id,
            'fecha_documento' => $documento_relacionado->fecha_documento,
            'tipo_id' => $documento_relacionado->tipo_id,
            'etapa' => $documento_relacionado->etapa,
            'padre_id' => $_POST['documento_id'],
            'archivado' => $documento_relacionado->archivado

        );


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
		//de la empresa existe, dentro del modulo.
		if (!file_exists($empresa_folder)) {
			try{
				mkdir($empresa_folder, 0777, true);
			} catch (Exception $e) {
				log_message("error", "MODULO: ". __METHOD__ .", Linea: ". __LINE__ ." --> ". $e->getMessage().".\r\n");
			}
		}

		$mimeTypeValidator = new \FileUpload\Validator\MimeTypeValidator([]);

		// Simple path resolver, where uploads will be put
		$pathresolver = new FileUpload\PathResolver\Simple($empresa_folder);

        // The machine's filesystem
		$filesystem = new FileUpload\FileSystem\Simple();

        // FileUploader itself
		$fileupload = new FileUpload\FileUpload($_FILES['documentos'], $_SERVER);

        // Adding it all together. Note that you can use multiple validators or none at all
		$fileupload->setPathResolver($pathresolver);
		$fileupload->setFileSystem($filesystem);
		//$fileupload->addValidator($validator);

        // Doing the deed
		list($files, $headers) = $fileupload->processAll();
		$i=0;
		$documentos = array();
		foreach($files AS $file){
			$filename = $file->name;
      $not_allowed = ["#"];
      $allowed = [""];
      $filename = str_replace($not_allowed, $allowed, $filename);

			$type = $file->type;
			$tmp_name =  $file->path;
			$size  =  $file->size;

			$extra_datos['campo']['size'] = $size;
                        $extra_datos = $documento_relacionado->extra_datos;
			if(!empty($extra_datos)){
				$fieldset["extra_datos"] = $extra_datos;
			}


			$documentos[$i]["archivo_ruta"] = $archivo_ruta;
			$documentos[$i]["archivo_nombre"] = $filename;

			$documentos[$i] = array_merge($documentos[$i], $fieldset);

			Capsule::beginTransaction();
			try{
				$response = $this->DocumentosRepository->createFacturasCompras($documentos);
				Capsule::commit();

			}catch(Illuminate\Database\QueryException $e){
				log_message('error', __METHOD__." -> Linea: ". __LINE__ ." --> ". $e->getMessage()."\r\n");
				Capsule::rollback();
				$mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error!</b> Hubo un error tratando de guardar los documentos.');
				$this->session->set_flashdata('mensaje', $mensaje);
				redirect(base_url('documentos/index'));
				exit;
			}

		}
		$mensaje = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado los documentos correctamente.');
		$this->session->set_flashdata('mensaje', $mensaje);
		redirect(base_url('documentos/index'));
		exit;

    }

    function ajax_descargar_documento() {
    if(empty($_POST)){
    return false;
    }
    $documento_id =  $this->input->post('documento_id', true);
    $documentos = $this->DocumentosRepository->find($documento_id);
    $clause = array(
        'documentable_id' => $documentos->documentable_id
    );
	$last_document = DocumentosModel::where($clause)->get()->last();
	$file_url =  array(
		'file_url' => $last_document->archivo_ruta . "/" . $last_document->archivo_nombre,
		'file_name' => $last_document->archivo_nombre
	);
	echo json_encode($file_url);
    exit;
    }
	function ajax_descargar_documento_detalle() {
		if(empty($_POST)){
			return false;
		}
		$documento_id =  $this->input->post('documento_id', true);
		$documentos = $this->DocumentosRepository->find($documento_id);
		$file_url =  array(
			'file_url' => $documentos->archivo_ruta . "/" . $documentos->archivo_nombre,
			'file_name' => $documentos->archivo_nombre
		);
		echo json_encode($file_url);
		exit;
	}

    public function ocultodetalle(){
		$this->load->view('detalle');
	}

	public function detalle_documentos($uuid = NULL){

		$acceso = 1;
		$mensaje =  array();
		$data = array();

		if(empty($uuid)){
			return false;
		}

		$factura = $this->facturaCompraRepository->findByUuid($uuid);
		$documentos = $this->DocumentosRepository->detalle($factura->id);
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
			//'public/assets/js/plugins/bootstrap/daterangepicker.js',
			'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
			'public/assets/js/default/toast.controller.js',
			'public/assets/js/plugins/ckeditor/ckeditor.js',
			'public/assets/js/plugins/ckeditor/adapters/jquery.js',
			'public/assets/js/default/formulario.js',
			'public/assets/js/modules/documentos/plugins.js'

		));

		$this->assets->agregar_js(array(
			'public/assets/js/modules/documentos/vue.detalle.js',
		));

		$breadcrumb = array(
			"titulo" => '<i class="fa fa-shopping-cart"></i> Facturas de compras: '.$factura->codigo,
			"ruta" => array(
				0 => array(
					"nombre" => "Compras",
					"activo" => false,

				),
				1 => array(
					"nombre" => "Facturas de compras",
					"activo" => false,
					"url" => 'facturas_compras/listar'
				),
				2 => array(
					"nombre" => "Documentos",
					"activo" => true,

				)
			),
			"filtro"    => false,
			"menu"      => array()
		);
		//dd($documentos);
		$detalle = $documentos->map(function ($detalle){
			$form = explode(".",$detalle->archivo_nombre);
			$extension = $this->extension_icono(end($form));
			$ruta = base_url($detalle->archivo_ruta . "/" . $detalle->archivo_nombre);
			return [
				'id' => $detalle->id,
				'nombre' => $detalle->archivo_nombre,
				'usuario' => $detalle->usuario->nombre_completo,
				'estado' => $detalle->present()->etapa,
				'fecha_documento' => $detalle->present()->fecha_documento,
				'fecha_carga' => $detalle->created_at->format('d/m/Y'),
				'tipo' => ($detalle->tipo_id > 0)?$detalle->tipo->nombre:'',
				'extension' => $extension,
				'ruta' => $ruta
			];
		});

		$this->assets->agregar_var_js(array(
			'detalle' => $detalle
		));

		$this->template->agregar_titulo_header('Documentos');
		$this->template->agregar_breadcrumb($breadcrumb);
		$this->template->agregar_contenido($data);
		$this->template->visualizar();
	}

	public function extension_icono($extension){

		switch ($extension){
			case 'jpg':
			case 'png':
				return 'fa fa-file-picture-o';
				break;
			case 'pdf':
				return 'fa fa-file-pdf-o';
			    break;
			case 'xls':
			case 'xlsx':
			case 'csv':
				return 'fa fa-file-excel-o';
				break;
			case 'doc':
			case 'docx':
				return 'fa fa-file-word-o';
				break;
			case 'msg':
				return 'fa fa-envelope-o';
				break;
			default:
				return 'fa fa-file-text-o';
		}
	}


}
?>

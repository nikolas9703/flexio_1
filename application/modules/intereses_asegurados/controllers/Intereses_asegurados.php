<?php

/**
 * Intereses Asegurados
 *
 * Modulo para administrar la creacion, edicion de Intereses Asegurados
 *
 * @package    PensaApp
 * @subpackage Controller
 * @category   Controllers
 * @author     Pensanomica Team
 * @link       http://www.pensanomica.com
 * @copyright  10/29/2015
 */
use Illuminate\Database\Capsule\Manager as Capsule;
use League\Csv\Writer as Writer;
use Flexio\Library\Util\GenerarCodigo as GenerarCodigo;
use Dompdf\Dompdf;
use Carbon\Carbon;
//Repositorios
use Flexio\Modulo\InteresesAsegurados\Repository\InteresesAseguradosRepository as interesesAseguradosRep;
use Flexio\Modulo\InteresesAsegurados\Models\InteresesAsegurados as AseguradosModel;
use Flexio\Modulo\InteresesAsegurados\Models\VehiculoAsegurados as VehiculoModel;
use Flexio\Modulo\InteresesAsegurados\Models\ProyectoAsegurados as ProyectoModel;
use Flexio\Modulo\InteresesAsegurados\Models\MaritimoAsegurados as MaritimoModel;
use Flexio\Modulo\InteresesAsegurados\Models\InteresesAsegurados_cat as InteresesAsegurados_catModel;
use Flexio\Modulo\InteresesAsegurados\Models\InteresesAsegurados_detalles as InteresesAsegurados_detalles;
use Flexio\Modulo\InteresesAsegurados\Models\InteresesAsegurados as ia;
use Flexio\Modulo\SegCatalogo\Models\SegCatalogo as SegCatalogosModel;
use Flexio\Modulo\Acreedores\Repository\AcreedoresRepository as AcreedoresRep;
use Flexio\Modulo\SegCatalogo\Repository\SegCatalogoRepository as SegCatalogoRepository;
use Flexio\Modulo\InteresesAsegurados\Models\InteresesPersonas as PersonasModel;
use Flexio\Modulo\InteresesAsegurados\Models\CargaAsegurados as CargaModel;
use Flexio\Modulo\InteresesAsegurados\Models\AereoAsegurados as AereoModel;
use Flexio\Modulo\SegInteresesAsegurados\Repository\SegInteresesAseguradosRepository as SegInteresesAseguradosRepository;
use Flexio\Modulo\Documentos\Repository\DocumentosRepository as DocumentosRepository;
use Flexio\Modulo\InteresesAsegurados\Models\ArticuloAsegurados as ArticuloModel;
use Flexio\Modulo\Politicas\Repository\PoliticasRepository as PoliticasRepository;
use Flexio\Modulo\InteresesAsegurados\Models\UbicacionAsegurados as UbicacionModel;
use Flexio\Modulo\Polizas\Models\Polizas as PolizasModel;
use Flexio\Modulo\Ramos\Repository\RamoRepository as RamoRepository;
use Flexio\Modulo\Ramos\Models\Ramos as Ramos;
use Flexio\Modulo\Solicitudes\Models\SolicitudesBitacora as bitacoraModel;
use Flexio\Modulo\SegCatalogo\Models\SegCatalogo as SegCatalogo;
use Flexio\Modulo\Proveedores\Models\Proveedores as Proveedores;
use Flexio\Modulo\Modulos\Models\Catalogos;

class Intereses_asegurados extends CRM_Controller {

	private $empresa_id;
	private $id_usuario;
	private $AseguradosModel;
	private $VehiculoModel;
	private $ProyectoModel;
	private $MaritimoModel;
	private $InteresesAsegurados_catModel;
	private $interesesAseguradosRep;
	private $SegCatalogosModel;
	private $AcreedoresRep;
	private $SegCatalogoRepository;
	private $PersonasModel;
	private $CargaModel;
	private $AereoModel;
	private $SegInteresesAseguradosRepository;
	private $DocumentosRepository;
	private $ArticuloModel;
	private $UbicacionModel;
	private $PolizasModel;
	protected $politicas;
	protected $politicas_general;
	protected $PoliticasRepository;
	protected $ramoRepository;
	private $bitacoraModel;
    //flexio
	protected $upload_folder = './public/uploads/';

	public function __construct() {
		parent::__construct();

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

		$this->roles = $this->session->userdata("roles");
        //$roles=implode(",", $this->roles);

		$clause['empresa_id'] = $this->empresa_id;
		$clause['modulo'] = 'intereses_asegurados';
		$clause['usuario_id'] = $this->usuario_id;
		$clause['role_id'] = $this->roles;

        //flexio
		$this->interesesAseguradosRep = new interesesAseguradosRep();
		$this->AseguradosModel = new AseguradosModel();
		$this->VehiculoModel = new VehiculoModel();
		$this->ProyectoModel = new ProyectoModel();
		$this->InteresesAsegurados_catModel = new InteresesAsegurados_catModel();
		$this->SegCatalogoRepository = new SegCatalogoRepository();
		$this->AcreedoresRep = new AcreedoresRep();
		$this->PersonasModel = new PersonasModel();
		$this->MaritimoModel = new MaritimoModel();
		$this->CargaModel = new CargaModel();
		$this->AereoModel = new AereoModel();
		$this->SegInteresesAseguradosRepository = new SegInteresesAseguradosRepository();
		$this->load->module(array('documentos'));
		$this->DocumentosRepository = new DocumentosRepository();
		$this->ArticuloModel = new ArticuloModel();
		$this->PoliticasRepository = new PoliticasRepository();
		$this->UbicacionModel = new UbicacionModel();
		$this->PolizasModel = new PolizasModel();
		$this->ramoRepository = new RamoRepository();
		$this->bitacoraModel = new bitacoraModel();

		$politicas_transaccion = $this->PoliticasRepository->getAllPoliticasRoles($clause);

		$politicas_transaccion_general = count($this->PoliticasRepository->getAllPoliticasRolesModulo($clause));
		$this->politicas_general = $politicas_transaccion_general;

		$estados_politicas = array();
		foreach ($politicas_transaccion as $politica_estado) {
			$estados_politicas[] = $politica_estado->politica_estado;
		}

		$this->politicas = $estados_politicas;
	}

	public function listar() {

		if (!$this->auth->has_permission('acceso', 'intereses_asegurados/listar') == true) {
			$acceso = 0;
			$mensaje = array('tipo' => "error", 'mensaje' => '<b>¡Error!</b> No tiene permisos para ingresar a intereses asegurados', 'titulo' => 'Intereses asegurados ');
			$this->session->set_flashdata('mensaje', $mensaje);

			redirect(base_url(''));
		}

		if ($this->auth->has_permission('acceso', 'intereses_asegurados/eliminar/(:any)') == true) {
			$eliminar = 1;
		} else
		$eliminar = 0;

		$this->assets->agregar_var_js(array(
			"permiso_eliminar" => $eliminar
			));

        //Definir mensaje
		if (!is_null($this->session->flashdata('mensaje'))) {
			$mensaje = $this->session->flashdata('mensaje');
		} else {
			$mensaje = [];
		}
		$this->assets->agregar_var_js(array(
			"flexio_mensaje" => collect($mensaje)
			));

		$data = array();

		$this->_Css();
		$this->_js();

		$breadcrumb = array(
			"titulo" => '<i class="fa fa-archive"></i> Intereses Asegurados',
			"ruta" => array(
				0 => array("nombre" => "Seguros", "url" => "#", "activo" => false),
				1 => array("nombre" => '<b>Intereses Asegurados</b>', "activo" => true)
				),
			"filtro" => false,
			"menu" => array()
			);

		$breadcrumb["menu"] = array(
			"url" => 'javascript:',
			"clase" => 'crearBoton',
			"nombre" => "Crear"
			);

		$menuOpciones = array();

		if ($this->auth->has_permission('acceso', 'intereses_asegurados/listar') == true) {
			$menuOpciones["#cambiarEstadoInteresesLnk"] = "Cambiar Estado";
			$menuOpciones["#exportarBtn"] = "Exportar";
		}

		$breadcrumb["menu"]["opciones"] = $menuOpciones;

		$data["campos"] = array(
			"campos" => array(
				"tipos_intereses_asegurados" => $this->InteresesAsegurados_catModel->get(),
				),
			);
        //$data["usuarios"] = Usuario_orm::get(array('id','nombre','apellido'));

		$this->assets->agregar_js(array(
			'public/assets/js/plugins/jquery/context-menu/jquery.contextMenu.min.js',
			'public/assets/js/modules/intereses_asegurados/listar.js',
			));

		$this->template->agregar_titulo_header('Listado de Intereses Asegurados');
		$this->template->agregar_breadcrumb($breadcrumb);
		$this->template->agregar_contenido($data);
		$this->template->visualizar($breadcrumb);
	}

	public function ajax_listar($grid = NULL) {
		$clause = array(
			"empresa_id" => $this->empresa_id
			);
		$numero = $this->input->post('numero', true);
		$tipo = $this->input->post('tipo', true);
		$identificacion = $this->input->post('identificacion', true);
		$estado = $this->input->post('estado', true);
		$clause['deleted'] = 0;
		if (!empty($numero)) {
			$clause["numero"] = array('LIKE', "%$numero%");
		}
		if (!empty($tipo)) {
			$clause["interesestable_type"] = $tipo;
		}
		if (!empty($identificacion)) {
			$clause["identificacion"] = array('LIKE', "%$identificacion%");
		}
		if (!empty($estado)) {
			$clause["estado"] = $estado;
		}

		list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();

		$count = $this->interesesAseguradosRep->listar_intereses_asegurados($clause, NULL, NULL, NULL, NULL)->count();

		list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

		$rows = $this->interesesAseguradosRep->listar_intereses_asegurados($clause, $sidx, $sord, $limit, $start);

        //Constructing a JSON
		$response = new stdClass();
		$response->page = $page;
		$response->total = $total_pages;
		$response->records = $count;
		$response->result = array();
		$i = 0;

		if (!empty($rows)) {
			foreach ($rows AS $i => $row) {
				$uuid_intereses = bin2hex($row->uuid_intereses);
				$now = Carbon::now();
				$btnClass = $row->estado !== "Activo" ? "successful" : "danger";
				$negativeState = $row->estado != "Activo" ? "Activar" : "Desactivar";
				$politicas = $this->politicas;
				if (in_array(19, $politicas) || in_array(20, $politicas)) {
					if (in_array(19, $politicas)) {
						if ($row->estado === "Activo") {
							$modalstate = '<a href="javascript:" data-id="' . $row['id'] . '" class="btn btn-block btn-outline btn-' . $btnClass . ' massive">' . $negativeState . '</a>';
						} else {
							$modalstate = '<button data-id="alert" id="alert"  style="border: red 1px solid; color: red;">Usted no tiene permisos para cambiar este estado</button>';
						}
					} else if (in_array(20, $politicas)) {
						if ($row->estado !== "Activo") {
							$modalstate = '<a href="javascript:" data-id="' . $row['id'] . '" class="btn btn-block btn-outline btn-' . $btnClass . ' massive">' . $negativeState . '</a>';
						} else {
							$modalstate = '<button data-id="alert" id="alert"  style="border: red 1px solid; color: red;">Usted no tiene permisos para cambiar este estado</button>';
						}
					}
				} else {
					$modalstate = '<a href="javascript:" data-id="' . $row['id'] . '" class="btn btn-block btn-outline btn-' . $btnClass . ' massive">' . $negativeState . '</a>';
				}
				$link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row['id'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
				$estado = $row->estado === "Activo" ? "Activo" : "Inactivo";
				$labelClass = $row->estado === "Activo" ? "successful" : "danger";
				$url = base_url("intereses_asegurados/editar/$uuid_intereses");
				$hidden_options = '<a href="' . $url . '" data-id="' . $row['id'] . '" class="btn btn-block btn-outline btn-success editarInteres" >Ver interés asegurado</a>';
				$hidden_options .= '<a href="javascript:" data-id="' . $row['interesestable_id'] . '" class="btn btn-block btn-outline btn-success subir_archivo_intereses" data-type="' . $row->interesestable_type . '" >Subir Archivo</a>';
				$hidden_options .= '<a href="javascript:" data-id="' . $row['interesestable_id'] . '" class="btn btn-block btn-outline btn-success eliminar_interes" data-type="' . $row->interesestable_type . '" >Eliminar</a>';
				$redirect = "<a style='text-decoration: underline' href=" . $url . ">$row->numero</a>";
				$id = $row->id;
				$response->rows[$i]["cell"] = array(
					"id" => $id,
					"numero" => $redirect,
					"interesestable_type" => $row->tipo->etiqueta,
					"identificacion" => $row->identificacion,
					"estado" => "<label class='label label-$labelClass estadoInteres' data-id='$id' >$estado</label>",
					"options" => $link_option,
					"link" => $hidden_options,
					"modalstate" => $modalstate,
					"massState" => $estado
					);
				$i++;
			}
		}
		echo json_encode($response);
		exit;
	}

	public function ocultotabla($data = array()) {
		$this->assets->agregar_js(array(
			'public/assets/js/modules/intereses_asegurados/tabla.js'
			));

		$this->load->view('tabla', $data);
	}

	public function ocultotablaarticulo($data = array()) {
		$this->assets->agregar_js(array(
			'public/assets/js/modules/intereses_asegurados/tablaarticulo.js'
			));

		$this->load->view('tablaarticulo', $data);
	}

	public function ocultotablacarga($data = array()) {
		$this->assets->agregar_js(array(
			'public/assets/js/modules/intereses_asegurados/tablacarga.js'
			));

		$this->load->view('tablacarga', $data);
	}

	public function ocultotablaaereo($data = array()) {
		$this->assets->agregar_js(array(
			'public/assets/js/modules/intereses_asegurados/tablaaereo.js'
			));

		$this->load->view('tablaaereo', $data);
	}

	public function ocultotablamaritimo($data = array()) {
		$this->assets->agregar_js(array(
			'public/assets/js/modules/intereses_asegurados/tablamaritimo.js'
			));

		$this->load->view('tablamaritimo', $data);
	}

	public function ocultotablapersonas($data = array()) {
		$this->assets->agregar_js(array(
			'public/assets/js/modules/intereses_asegurados/tablapersonas.js'
			));

		$this->load->view('tablapersonas', $data);
	}

	public function ocultotablaproyecto($data = array()) {
		$this->assets->agregar_js(array(
			'public/assets/js/modules/intereses_asegurados/tablaproyecto.js'
			));

		$this->load->view('tablaproyecto', $data);
	}

	public function ocultotablaubicacion($data = array()) {
		$this->assets->agregar_js(array(
			'public/assets/js/modules/intereses_asegurados/tablaubicacion.js'
			));

		$this->load->view('tablaubicacion', $data);
	}

	public function ocultotablavehiculo($data = array()) {
		$this->assets->agregar_js(array(
			'public/assets/js/modules/intereses_asegurados/tablavehiculo.js'
			));

		$this->load->view('tablavehiculo', $data);
	}

	public function crear($formulario = NULL) {
		$acceso = 1;
		$mensaje = array();

		if (!$this->auth->has_permission('acceso', 'intereses_asegurados/crear')) {
            // No, tiene permiso, redireccionarlo.
			$acceso = 0;
			$mensaje = array('tipo' => "error", 'mensaje' => '<b>¡Error!</b> No tiene permisos para crear intereses asegurados', 'titulo' => 'Intereses asegurados ');
			$this->session->set_flashdata('mensaje', $mensaje);

			redirect(base_url('intereses_asegurados/listar'));
		}

		if ($this->auth->has_permission('editar__cambiarEstado', 'intereses_asegurados/editar/(:any)') == true) {
			$cestado = 1;
		} else {
			$cestado = 0;
		}

		$this->_Css();
		$this->_js();

		$this->assets->agregar_js(array(
			'public/assets/js/modules/intereses_asegurados/formulario.js',
                //'public/assets/js/modules/intereses_asegurados/crear.js',
                //'public/assets/js/default/vue-validator.min.js',   
			));

		$this->assets->agregar_var_js(array(
			"permiso_editar" => 1
			));
		$data = array();

		if ($formulario != NULL) {
			$this->assets->agregar_var_js(array(
				"formulario_seleccionado" => $formulario,
				"vista" => "crear",
				"permiso_cambio_estado" => $cestado,
				"desde" => "intereses_asegurados",
				));
		} else {
			$this->assets->agregar_var_js(array(
				"vista" => "crear",
				"permiso_cambio_estado" => $cestado,
				"desde" => "intereses_asegurados",
				));
		}

		if (!empty($_POST)) {
            //Se recibe el parámetro y se usa para buscar el controlador del interés asegurado
			$var = ucfirst($formulario) . "_orm";
			if ($var::create($this->input->post("campo"))) {
				redirect(base_url('intereses_asegurados/listar'));
			} else {
                //Establecer el mensaje a mostrar
				$data["mensaje"]["clase"] = "alert-danger";
				$data["mensaje"]["contenido"] = "Hubo un error al tratar de crear el pedido.";
			}
		}

		$data["campos"] = array(
			"campos" => array(
				"tipos_intereses_asegurados" => $this->InteresesAsegurados_catModel->get(),
				"politicas" => $this->politicas,
				"politicas_general" => $this->politicas_general
				),
			"cambio_estado" => $cestado,
			);


		$breadcrumb = array(
			"titulo" => '<i class="fa fa-archive"></i> Intereses Asegurados: Crear',
			"ruta" => array(
				0 => array("nombre" => "Seguros", "url" => "#", "activo" => false),
				1 => array("nombre" => "Intereses Asegurados", "url" => "intereses_asegurados/listar", "activo" => false),
				2 => array("nombre" => '<b>Crear</b>', "activo" => true)
				),
			"filtro" => false,
			"menu" => array()
			);
		$data['mensaje'] = $mensaje;
		$this->template->agregar_titulo_header('Intereses Asegurados');
		$this->template->agregar_breadcrumb($breadcrumb);
		$this->template->agregar_contenido($data);
		$this->template->visualizar($breadcrumb);
	}

	public function vehiculoformularioparcial($data = array()) {
		$this->assets->agregar_js(array(
			'public/assets/js/modules/intereses_asegurados/crear_vehiculo.js'
			));
		if (empty($data)) {
			$data["campos"] = array();
		}
        //persona
		$data['uso'] = $this->SegCatalogoRepository->listar_catalogo('uso_vehiculo', 'orden');
		$data['condicion'] = $this->SegCatalogoRepository->listar_catalogo('condicion_vehiculo', 'orden');
		$clause['empresa_id'] = $this->empresa_id;
		$clause['tipo_id'] = 1;
		$data['acreedores'] = $this->AcreedoresRep->get($clause);
		$data['estado'] = $this->SegCatalogoRepository->listar_catalogo('estado2', 'orden');

		$this->load->view('formulariovehiculo', $data);
	}

	function guardar_vehiculo() {
		if ($_POST) {
			unset($_POST["campo"]["guardar"]);
			$campo = Util::set_fieldset("campo");
			$campo2 = Util::set_fieldset("campo2");
			$campodesde = Util::set_fieldset("campodesde");
			$campodetalle = Util::set_fieldset("campodetalle");
			if (!isset($campo['uuid'])) {
				$campo['empresa_id'] = $this->empresa_id;
			}
			$uuid = "";
			$vehiculo = null;
			$vehiculoObj = null;
			Capsule::beginTransaction();
			try {
				if (empty($campo['uuid'])) {
					$campo["uuid_vehiculo"] = Capsule::raw("ORDER_UUID(uuid())");
					$campo["empresa_id"] = $this->empresa_id;

                    //$campo["estado"] = "activo";
					$clause['empresa_id'] = $this->empresa_id;
					$total = $this->interesesAseguradosRep->listar_vehiculo($clause);
					$vehiculo = $this->VehiculoModel->create($campo);
					
					$comentario="<b>Interés Vehículo</b><br><br>";
					
					if($vehiculo->chasis!='')
						$comentario.="<b>Campo: N°. Chasis o serie</b><br>Valor: ".$vehiculo->chasis."<br><br>";
					if($vehiculo->unidad!='')
						$comentario.="<b>Campo: N°. Unidad</b><br>Valor: ".$vehiculo->unidad."<br><br>";
					if($vehiculo->marca!='')
						$comentario.="<b>Campo: Marca</b><br>Valor: ".$vehiculo->marca."<br><br>";
					if($vehiculo->modelo!='')
						$comentario.="<b>Campo: Modelo</b><br>Valor: ".$vehiculo->modelo."<br><br>";
					if($vehiculo->placa!='')
						$comentario.="<b>Campo: Placa</b><br>Valor: ".$vehiculo->placa."<br><br>";
					if($vehiculo->ano!='')
						$comentario.="<b>Campo: Año</b><br>Valor: ".$vehiculo->ano."<br><br>";
					if($vehiculo->motor!='')
						$comentario.="<b>Campo: Motor</b><br>Valor: ".$vehiculo->motor."<br><br>";
					if($vehiculo->color!='')
						$comentario.="<b>Campo: Color</b><br>Valor: ".$vehiculo->color."<br><br>";
					if($vehiculo->capacidad!='')
						$comentario.="<b>Campo: Capacidad</b><br>Valor: ".$vehiculo->capacidad."<br><br>";
					if($vehiculo->uso!='')
						$comentario.="<b>Campo: Uso</b><br>Valor: ".$vehiculo->datosUso->etiqueta."<br><br>";
					if($vehiculo->condicion!='')
						$comentario.="<b>Campo: Condición</b><br>Valor: ".$vehiculo->datosCondicion->etiqueta."<br><br>";
					if($vehiculo->operador!='')
						$comentario.="<b>Campo: Operador</b><br>Valor: ".$vehiculo->operador."<br><br>";
					if($vehiculo->extras!='')
						$comentario.="<b>Campo: Extras</b><br>Valor: ".$vehiculo->extras."<br><br>";
					if($vehiculo->valor_extras!='')
						$comentario.="<b>Campo: Valor Extras</b><br>Valor: ".$vehiculo->valor_extras."<br><br>";
					if($vehiculo->nombre!='')
						$comentario.="<b>Campo: Acreedor</b><br>Valor: ".$vehiculo->datosAcreedor->nombre."<br><br>";
					if($vehiculo->porcentaje_acreedor!='')
						$comentario.="<b>Campo: Porcentaje Acreedor</b><br>Valor: ".$vehiculo->porcentaje_acreedor."<br><br>";
					if($vehiculo->observaciones!='')
						$comentario.="<b>Campo: Observaciones</b><br>Valor: ".$vehiculo->observaciones."<br><br>";
					if($campo2['estado']!='')
						$comentario.="<b>Campo: Estado</b><br>Valor: ".$campo2['estado']."<br><br>";

                    //guardar tabla principal
					$codigo = Util::generar_codigo('VEH', $vehiculo->id);
					$fieldset["numero"] = $codigo;
					$fieldset['uuid_intereses'] = $vehiculo->uuid_vehiculo;
					$fieldset['empresa_id'] = $vehiculo->empresa_id;
					$fieldset['interesestable_type'] = 8;
					$fieldset['interesestable_id'] = $vehiculo->id;
					$fieldset['numero'] = $codigo;
					$fieldset['identificacion'] = $vehiculo->motor;
					$fieldset['estado'] = $campo2['estado'];
					$fieldset['updated_at'] = $vehiculo->updated_at;
					$fieldset['created_at'] = $vehiculo->created_at;
					$fieldset['creado_por'] = $this->session->userdata['id_usuario'];
					$interesase = $this->AseguradosModel->create($fieldset);

					if ($campodesde['desde'] == "solicitudes") {
						$u = ia::where('id', $interesase->id)->first()->toArray();
						$uuid = bin2hex($u['uuid_intereses']);

						$detalle = array();
						$detalle['id_intereses'] = $interesase->id;
						$detalle['detalle_relacion'] = isset($campodetalle['relacion']) ? $campodetalle['relacion'] : '';
						$detalle['detalle_prima'] = isset($campodetalle['prima_anual']) ? $campodetalle['prima_anual'] : '';
						$detalle['detalle_beneficio'] = isset($campodetalle['beneficio_vida']) ? $campodetalle['beneficio_vida'] : '';
						$detalle['detalle_monto'] = isset($campodetalle['monto']) ? $campodetalle['monto'] : '';
						$detalle['detalle_int_asociado'] = isset($campodetalle['interes_asociado']) ? $campodetalle['interes_asociado'] : '';
						$detalle['detalle_certificado'] = isset($campodetalle['certificado']) ? $campodetalle['certificado'] : '';
						$detalle['detalle_suma_asegurada'] = isset($campodetalle['suma_asegurada']) ? $campodetalle['suma_asegurada'] : '';
						$detalle['detalle_deducible'] = isset($campodetalle['deducible']) ? $campodetalle['deducible'] : '';
						$detalle['detalle_unico'] = $_POST['detalleunico'];

						$num = InteresesAsegurados_detalles::where('id_intereses', $detalle['id_intereses'])->where('detalle_unico', $detalle['detalle_unico'])->count();
						if ($num > 0) {
							$det = InteresesAsegurados_detalles::where('id_intereses', $detalle['id_intereses'])->where('detalle_unico', $detalle['detalle_unico'])->update($detalle);
							if($det->detalle_certificado!=$detalle['detalle_certificado'])
							{
								$comentario.="<b>Campo: No. Certificado</b><br>Valor Actual:".$det->detalle_certificado."<br>Valor Anterior: ".$detalle['detalle_certificado']."<br><br>";
							}
							if($det->detalle_suma_asegurada!=$detalle['detalle_suma_asegurada'])
							{
								$comentario.="<b>Campo: Suma Asegurada</b><br>Valor Actual:".$det->detalle_suma_asegurada."<br>Valor Anterior: ".$detalle['detalle_suma_asegurada']."<br><br>";
							}
							if($det->detalle_prima!=$detalle['detalle_prima'])
							{
								$comentario.="<b>Campo: Prima neta</b><br>Valor Actual:".$det->detalle_prima."<br>Valor Anterior: ".$detalle['detalle_prima']."<br><br>";
							}
							if($det->detalle_deducible!=$detalle['detalle_deducible'])
							{
								$comentario.="<b>Campo: Deducible</b><br>Valor Actual:".$det->detalle_deducible."<br>Valor Anterior: ".$detalle['detalle_deducible']."<br><br>";
							}
						} else {
							$det = InteresesAsegurados_detalles::create($detalle);

							if($det->detalle_certificado!="")
							{
								$comentario.="<b>Campo: No. Certificado</b><br>Valor: ".$det->detalle_certificado."<br><br>";
							}
							if($det->detalle_suma_asegurada!="")
							{
								$comentario.="<b>Campo: Suma Asegurada</b><br>Valor: ".$det->detalle_suma_asegurada."<br><br>";
							}
							if($det->detalle_prima!="")
							{
								$comentario.="<b>Campo: Prima neta</b><br>Valor: ".$det->detalle_prima."<br><br>";
							}
							if($det->detalle_deducible!="")
							{
								$comentario.="<b>Campo: Deducible</b><br>Valor: ".$det->detalle_deducible."<br><br>";
							}
						}
						
						$usuario_registro = Usuario_orm::find($this->session->userdata['id_usuario']);
						$comentario.="Registrado Por: ".$fieldset['creado_por'] =$usuario_registro->nombre." ".$usuario_registro ->apellido;

						$fieldset["comentario"] = $comentario;
						$fieldset["comentable_id"] = $detalle['detalle_unico'];
						$fieldset["usuario_id"] = $this->session->userdata['id_usuario'];
						$fieldset["empresa_id"] = $this->empresa_id;
						
						$interesase = $this->bitacoraModel->create($fieldset);
					}

					

                    //Subir documentos
					if (!empty($_FILES['file'])) {
						$vehiculo_id = $interesase->id;
						var_dump($interesase->id);
						unset($_POST["campo"]);
						$modeloInstancia = $this->VehiculoModel->find($vehiculo->id);
						$this->documentos->subir($modeloInstancia);
					}
				} else {

					if ($this->auth->has_permission('acceso', 'intereses_asegurados/editar/(:any)')) {
						$intereses_asegurados = $this->interesesAseguradosRep->verInteresAsegurado(hex2bin(strtolower($campo['uuid'])));
						$codigo = $intereses_asegurados->numero;
						$uuid = $campo['uuid'];
						$vehiculoObj = $this->VehiculoModel->find($intereses_asegurados->vehiculo->id);

						$cambio='no';
						if($vehiculoObj->chasis!=$campo['chasis'])
						{
							$comentario.="<b>Campo: N°. Chasis o serie</b><br>Valor Actual: ".$campo['chasis']."<br>Valor Anterior:".$vehiculoObj->chasis."<br><br>";
							$cambio='si';
						}
						if($vehiculoObj->unidad!=$campo['unidad'])
						{
							$cambio='si';
							$comentario.="<b>Campo: N°. Unidad</b><br>Valor Actual:".$campo['unidad']."<br>Valor Anterior:".$vehiculoObj->unidad."<br><br>";
						}
						if($vehiculoObj->marca!=$campo['marca']){
							$comentario.="<b>Campo: Marca</b><br>Valor Actual:".$campo['marca']."<br>Valor Anterior:".$vehiculoObj->marca."<br><br>";
							$cambio='si';
						}
						if($vehiculoObj->modelo!=$campo['modelo']){
							$comentario.="<b>Campo: Modelo</b><br>Valor Actual:".$campo['modelo']."<br>Valor Anterior:".$vehiculoObj->modelo."<br><br>";
							$cambio='si';
						}
						if($vehiculoObj->placa!=$campo['placa']){
							$comentario.="<b>Campo: Placa</b><br>Valor Actual:".$campo['placa']."<br>Valor Anterior:".$vehiculoObj->placa."<br><br>";
							$cambio='si';
						}
						if($vehiculoObj->ano!=$campo['ano']){
							$comentario.="<b>Campo: Año</b><br>Valor Actual:".$campo['ano']."<br>Valor Anterior:".$vehiculoObj->ano."<br><br>";
							$cambio='si';
						}
						if($vehiculoObj->motor!=$campo['motor']){
							$comentario.="<b>Campo: Motor</b><br>Valor Actual:".$campo['motor']."<br>Valor Anterior:".$vehiculoObj->motor."<br><br>";
							$cambio='si';
						}
						if($vehiculoObj->color!=$campo['color']){
							$comentario.="<b>Campo: Color</b><br>Valor Actual:".$campo['color']."<br>Valor Anterior:".$vehiculoObj->color."<br><br>";
							$cambio='si';
						}
						if($vehiculoObj->capacidad!=$campo['capacidad']){
							$comentario.="<b>Campo: Capacidad</b><br>Valor Actual:".$campo['capacidad']."<br>Valor Anterior:".$vehiculoObj->capacidad."<br><br>";
							$cambio='si';
						}
						if($vehiculoObj->uso!=$campo['uso']){
							$uso_anterior=SegCatalogo::find($campo['uso']);
							$comentario.="<b>Campo: Uso</b><br>Valor Actual:".$uso_anterior->etiqueta."<br>Valor Anterior:".$vehiculoObj->datosUso->etiqueta."<br><br>";
							$cambio='si';
						}
						if($vehiculoObj->condicion!=$campo['condicion']){
							$condicion_anterior=SegCatalogo::find($campo['condicion']);
							$comentario.="<b>Campo: Condición</b><br>Valor Actual:".$condicion_anterior->etiqueta."<br>Valor Anterior:".$vehiculoObj->datosCondicion->etiqueta."<br><br>";
							$cambio='si';
						}
						if($vehiculoObj->operador!=$campo['operador']){
							$comentario.="<b>Campo: Operador</b><br>Valor Actual:".$campo['operador']."<br>Valor Anterior:".$vehiculoObj->operador."<br><br>";
							$cambio='si';
						}
						if($vehiculoObj->extras!=$campo['extras']){
							$comentario.="<b>Campo: Extras</b><br>Valor Actual:".$campo['extras']."<br>Valor Anterior:".$vehiculoObj->extras."<br><br>";
							$cambio='si';
						}
						if($vehiculoObj->valor_extras!=$campo['valor_extras']){
							$comentario.="<b>Campo: Valor Extras</b><br>Valor Actual:".$campo['valor_extras']."<br>Valor Anterior:".$vehiculoObj->valor_extras."<br><br>";
							$cambio='si';
						}
						if($vehiculoObj->acreedor!=$campo['acreedor']){
							$acreedor_anterior=Proveedores::find($campo['acreedor']);
							$comentario.="<b>Campo: Acreedor</b><br>Valor Actual:".$acreedor_anterior->nombre."<br>Valor Anterior:".$vehiculoObj->datosAcreedor->nombre."<br><br>";
							$cambio='si';
						}
						if($vehiculoObj->porcentaje_acreedor!=$campo['porcentaje_acreedor']){
							$comentario.="<b>Campo: Porcentaje Acreedor</b><br>Valor Actual:".$campo['porcentaje_acreedor']."<br>Valor Anterior:".$vehiculoObj->porcentaje_acreedor."<br><br>";
							$cambio='si';
						}
						if($vehiculoObj->observaciones!=$campo['observaciones']){
							$comentario.="<b>Campo: Observaciones</b><br>Valor Actual:".$campo['observaciones']."<br>Valor Anterior:".$vehiculoObj->observaciones."<br><br>";
							$cambio='si';
						}
						$vehiculoObj->update($campo);

						$intereses_asegurados->identificacion = $vehiculoObj->chasis;
						if($intereses_asegurados->estado!=$campo2['estado']){
							$comentario.="<b>Campo: Estado</b><br>Valor Actual:".$campo2['estado']."<br>Valor Anterior:".$intereses_asegurados->estado."<br><br>";
							$cambio='si';
						}
						$intereses_asegurados->estado = $campo2['estado'];
						$intereses_asegurados->identificacion = $campo['motor'];
						$intereses_asegurados->save();

						if ($campodesde['desde'] == "solicitudes") {
							$detalle = array();
							$detalle['id_intereses'] = $intereses_asegurados->id;
							$detalle['detalle_relacion'] = isset($campodetalle['relacion']) ? $campodetalle['relacion'] : '';
							$detalle['detalle_prima'] = isset($campodetalle['prima_anual']) ? $campodetalle['prima_anual'] : '';
							$detalle['detalle_beneficio'] = isset($campodetalle['beneficio_vida']) ? $campodetalle['beneficio_vida'] : '';
							$detalle['detalle_monto'] = isset($campodetalle['monto']) ? $campodetalle['monto'] : '';
							$detalle['detalle_int_asociado'] = isset($campodetalle['interes_asociado']) ? $campodetalle['interes_asociado'] : '';
							$detalle['detalle_certificado'] = isset($campodetalle['certificado']) ? $campodetalle['certificado'] : '';
							$detalle['detalle_suma_asegurada'] = isset($campodetalle['suma_asegurada']) ? $campodetalle['suma_asegurada'] : '';
							$detalle['detalle_deducible'] = isset($campodetalle['deducible']) ? $campodetalle['deducible'] : '';
							$detalle['detalle_unico'] = $_POST['detalleunico'];

							$num = InteresesAsegurados_detalles::where('id_intereses', $detalle['id_intereses'])->where('detalle_unico', $detalle['detalle_unico'])->count();

							if ($num > 0) {
								$num1 = InteresesAsegurados_detalles::where('id_intereses', $detalle['id_intereses'])->where('detalle_unico', $detalle['detalle_unico'])->first();

								$det = InteresesAsegurados_detalles::where('id_intereses', $detalle['id_intereses'])->where('detalle_unico', $detalle['detalle_unico'])->update($detalle);

								if($num1->detalle_certificado!=$detalle['detalle_certificado'])
								{
									$comentario.="<b>Campo: No. Certificado</b><br>Valor Actual:".$detalle['detalle_certificado']."<br>Valor Anterior: ".$num1->detalle_certificado."<br><br>";
									$cambio='si';
								}
								if($num1->detalle_suma_asegurada!=$detalle['detalle_suma_asegurada'])
								{
									$comentario.="<b>Campo: Suma Asegurada</b><br>Valor Actual:".$detalle['detalle_suma_asegurada']."<br>Valor Anterior: ".$num1->detalle_suma_asegurada."<br><br>";
									$cambio='si';
								}
								if($num1->detalle_prima!=$detalle['detalle_prima'])
								{
									$comentario.="<b>Campo: Prima neta</b><br>Valor Actual:".$detalle['detalle_prima']."<br>Valor Anterior: ".$num1->detalle_prima."<br><br>";
									$cambio='si';
								}
								if($num1->detalle_deducible!=$detalle['detalle_deducible'])
								{
									$comentario.="<b>Campo: Deducible</b><br>Valor Actual:".$detalle['detalle_deducible']."<br>Valor Anterior: ".$num1->detalle_deducible."<br><br>";
									$cambio='si';
								}

							} else {

								$det = InteresesAsegurados_detalles::create($detalle);

								if($det->detalle_certificado!="")
								{
									$comentario.="<b>Campo: No. Certificado</b><br>Valor Actual:".$det->detalle_certificado."<br><br>";
									$cambio='si';
								}
								if($det->detalle_suma_asegurada!="")
								{
									$comentario.="<b>Campo: Suma Asegurada</b><br>Valor Actual:".$det->detalle_suma_asegurada."<br><br>";
									$cambio='si';
								}
								if($det->detalle_prima!="")
								{
									$comentario.="<b>Campo: Prima neta</b><br>Valor Actual:".$det->detalle_prima."<br><br>";
									$cambio='si';
								}
								if($det->detalle_deducible!="")
								{
									$comentario.="<b>Campo: Deducible</b><br>Valor Actual:".$det->detalle_deducible."<br><br>";
									$cambio='si';
								}
							}

							$usuario_registro = Usuario_orm::find($this->session->userdata['id_usuario']);

							$comentario.="Registrado Por: ".$fieldset['creado_por'] =$usuario_registro->nombre." ".$$usuario_registro ->apellido;

							$comentario2="<b>Interés Vehículo</b><br>Chasis: ".$vehiculoObj->nombre."<br><br>";
							$fieldset["comentario"] = $comentario2."".$comentario;
							$fieldset["comentable_type"] = "Actualizacion_interes_solicitudes";
							if($num1->id_solicitudes==''){
								$solicitud=$_POST['detalleunico'];
							}
							else
								$solicitud=$num1->id_solicitudes;
							$fieldset["comentable_id"] = $solicitud;
							$fieldset["usuario_id"] = $this->session->userdata['id_usuario'];
							$fieldset["empresa_id"] = $this->empresa_id;

							if($cambio=='si')
								$interesase = $this->bitacoraModel->create($fieldset);

						}

						//Subir documentos
						if (!empty($_FILES['file'])) {
							$vehiculo_id = $vehiculoObj->id;
							unset($_POST["campo"]);
							$modeloInstancia = $this->VehiculoModel->find($vehiculoObj->id);
							$this->documentos->subir($modeloInstancia);
						}

					} else {
						$mensaje = array('class' => 'alert-danger', 'contenido' => '<strong>¡Error!</strong>Usted no tiene permisos para editar este registro');
					}
				}
				Capsule::commit();
			} catch (ValidationException $e) {
				log_message('error', $e);
				Capsule::rollback();
			}

			if (!is_null($vehiculo) || !is_null($vehiculoObj)) {
				$mensaje = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente', 'titulo' => 'Interés asegurado ' . $codigo . '');
			} else {
				$mensaje = array('class' => 'alert-danger', 'contenido' => '<strong>¡Error!</strong> Su solicitud no fue procesada');
			}
		} else {
			$mensaje = array('class' => 'alert-warning', 'contenido' => '<strong>¡Error!</strong> Su solicitud no fue procesada');
		}

		$this->session->set_flashdata('mensaje', $mensaje);
		if ($campodesde['desde'] != "solicitudes") {
			redirect(base_url('intereses_asegurados/listar'));
		} else if ($campodesde['desde'] == "solicitudes") {
			print_r($uuid . "&" . $codigo);
			exit;
		}
	}

	function ajax_check_vehiculo() {

		$chasis = $this->input->post("chasis");

		if ($this->input->post("uuid") != "") {
			$uuid = hex2bin(strtolower($this->input->post("uuid")));
			$intereses_asegurados = $this->interesesAseguradosRep->verInteresAsegurado($uuid);

			$vehiculo = $this->VehiculoModel->find($intereses_asegurados->vehiculo->id);

			$chasis_obj = $this->interesesAseguradosRep->identificacionUuid($chasis, $vehiculo->id);

			if (empty($chasis_obj)) {
				echo('USER_AVAILABLE');
			} else {
				echo('USER_EXISTS');
			}
		} else {
			$chasis_obj = $this->interesesAseguradosRep->identificacion($chasis);

			if (empty($chasis_obj)) {
				echo('USER_AVAILABLE');
			} else {
				echo('USER_EXISTS');
			}
		}
	}

	public function cargaformularioparcial($data = array()) {

		$this->assets->agregar_js(array(
			'public/assets/js/modules/intereses_asegurados/crear_carga.js',
			));
		if (empty($data)) {
			$data["campos"] = array();
		}


        //carga
		$clause['empresa_id'] = $this->empresa_id;
		$clause['tipo'] = 2;

		$data['tipo_empaque'] = $this->SegInteresesAseguradosRepository->listar_catalogo('tipo_empaque', 'orden');
		$data['condicion_envio'] = $this->SegInteresesAseguradosRepository->listar_catalogo('condicion_envio', 'orden');
		$data['medio_transporte'] = $this->SegInteresesAseguradosRepository->listar_catalogo('medio_transporte', 'orden');
		$data['tipo_obligacion'] = $this->SegInteresesAseguradosRepository->listar_catalogo('tipo_obligacion', 'orden');

		$data['acreedores'] = $this->AcreedoresRep->get($clause);
		$data['estado'] = $this->SegCatalogoRepository->listar_catalogo('estado2', 'orden');

		$this->load->view('formulariocarga', $data);
	}

	function ajax_check_carga() {

		$no_liquidacion = $this->input->post("no_liquidacion");

		if ($this->input->post("uuid_carga") != "") {
			$uuid = hex2bin(strtolower($this->input->post("uuid_carga")));

			$count = $this->interesesAseguradosRep->validarCarga($uuid, $no_liquidacion);

			if ($count > 0) {
				echo('USER_AVAILABLE');
			} else {
				$liquidacion_obj = $this->interesesAseguradosRep->identificacion_carga($no_liquidacion);
				if (empty($liquidacion_obj)) {
					echo('USER_AVAILABLE');
				} else {
					echo('USER_EXISTS');
				}
			}
		} else {
			$liquidacion_obj = $this->interesesAseguradosRep->identificacion_carga($no_liquidacion);
			if (empty($liquidacion_obj)) {
				echo('USER_AVAILABLE');
			} else {
				echo('USER_EXISTS');
			}
		}
	}

	function guardar_carga() {

		if ($_POST) {
			unset($_POST["campo"]["guardar"]);
			$campo = Util::set_fieldset("campo");
			$campodesde = Util::set_fieldset("campodesde");
			$campodetalle = Util::set_fieldset("campodetalle");
			if (!isset($campo['uuid'])) {
				$campo['empresa_id'] = $this->empresa_id;
			}
			$uuid = "";
			$carga = null;
			$cargaObj = null;
			Capsule::beginTransaction();
			try {
				$campo['acreedor'] = !empty($campo['acreedor']) ? $campo['acreedor'] : '';
				$campo['acreedor_opcional'] = !empty($campo['acreedor_opcional']) ? $campo['acreedor_opcional'] : '';
				$campo['tipo_obligacion'] = !empty($campo['tipo_obligacion']) ? $campo['tipo_obligacion'] : '';
				$campo['tipo_obligacion_opcional'] = !empty($campo['tipo_obligacion_opcional']) ? $campo['tipo_obligacion_opcional'] : '';
				if (empty($campo['uuid'])) {
					$clause['empresa_id'] = $this->empresa_id;
					$total = $this->interesesAseguradosRep->listar_carga($clause);
					$codigo = Util::generar_codigo('CGA', count($total) + 1);
					$campo["numero"] = $codigo;
					$campo["fecha_despacho"] = !empty($campo['fecha_despacho']) ? $campo['fecha_despacho'] : NULL;
					$campo["fecha_arribo"] = !empty($campo['fecha_arribo']) ? $campo['fecha_arribo'] : NULL;
					$carga = $this->CargaModel->create($campo);

					$comentario="<b>Interés Carga</b><br><br>";
					
					if($carga->no_liquidacion!='')
						$comentario.="<b>Campo: N°. liquidación</b><br>Valor: ".$carga->no_liquidacion."<br><br>";
					if($carga->fecha_despacho!='')
						$comentario.="<b>Campo: Fecha de Despacho</b><br>Valor: ".$carga->fecha_despacho."<br><br>";
					if($carga->fecha_arribo!='')
						$comentario.="<b>Campo: Fecha de Arribo</b><br>Valor: ".$carga->fecha_arribo."<br><br>";
					if($carga->detalle!='')
						$comentario.="<b>Campo: Detalle Mercancia</b><br>Valor: ".$carga->detalle."<br><br>";
					if($carga->valor!='')
						$comentario.="<b>Campo: Valor de la Mercancia</b><br>Valor: ".$carga->valor."<br><br>";
					if($carga->tipo_empaque!='')
						$comentario.="<b>Campo: Tipo de Empaque</b><br>Valor: ".$carga->datosTipoEmpaque->etiqueta."<br><br>";
					if($carga->condicion_envio!='')
						$comentario.="<b>Campo: Condición de Envío</b><br>Valor: ".$carga->datosCondicionEnvio->etiqueta."<br><br>";
					if($carga->medio_transporte!='')
						$comentario.="<b>Campo: Medio de Transporte</b><br>Valor: ".$carga->datosMedioTransporte->etiqueta."<br><br>";
					if($carga->origen!='')
						$comentario.="<b>Campo: Origen</b><br>Valor: ".$carga->origen."<br><br>";
					if($carga->destino!='')
						$comentario.="<b>Campo: Destino</b><br>Valor: ".$carga->destino."<br><br>";
					if($carga->acreedor!='')
						$comentario.="<b>Campo: Acreedor</b><br>Valor: ".$carga->datosAcreedor->nombre."<br><br>";
					if($carga->acreedor_opcional!='')
						$comentario.="<b>Campo: Otro Acreedor</b><br>Valor: ".$carga->acreedor_opcional."<br><br>";
					if($carga->tipo_obligacion!='')
						$comentario.="<b>Campo: Tipo Obligación</b><br>Valor: ".$carga->datosTipoObligacion->etiqueta."<br><br>";
					if($carga->tipo_obligacion_opcional!='')
						$comentario.="<b>Campo: Otro Tipo Obligación </b><br>Valor: ".$carga->tipo_obligacion_opcional."<br><br>";
					if($carga->observaciones!='')
						$comentario.="<b>Campo: Observaciones</b><br>Valor: ".$carga->observaciones."<br><br>";
					if($campo['estado']!='')
						$comentario.="<b>Campo: Estado</b><br>Valor: ".$campo['estado']."<br><br>";

                    //guardar tabla principal
					$fieldset['uuid_intereses'] = Capsule::raw("ORDER_UUID(uuid())");
					$fieldset['empresa_id'] = $carga->empresa_id;
					$fieldset['interesestable_type'] = 2;
					$fieldset['interesestable_id'] = $carga->id;
					$fieldset['numero'] = $codigo;
					$fieldset['identificacion'] = $carga->no_liquidacion;
					$fieldset['estado'] = $campo['estado'];
					$fieldset['creado_por'] = $this->session->userdata['id_usuario'];
					$ca = $carga->interesesAsegurados()->create($fieldset);

					$intereses_asegurados = $ca;

					if ($campodesde['desde'] == "solicitudes") {
						$u = ia::where('id', $ca->id)->first()->toArray();
						$uuid = bin2hex($u['uuid_intereses']);

						$detalle = array();
						$detalle['id_intereses'] = $ca->id;
						$detalle['detalle_relacion'] = isset($campodetalle['relacion']) ? $campodetalle['relacion'] : '';
						$detalle['detalle_prima'] = isset($campodetalle['prima_anual']) ? $campodetalle['prima_anual'] : '';
						$detalle['detalle_beneficio'] = isset($campodetalle['beneficio_vida']) ? $campodetalle['beneficio_vida'] : '';
						$detalle['detalle_monto'] = isset($campodetalle['monto']) ? $campodetalle['monto'] : '';
						$detalle['detalle_int_asociado'] = isset($campodetalle['interes_asociado']) ? $campodetalle['interes_asociado'] : '';
						$detalle['detalle_certificado'] = isset($campodetalle['certificado']) ? $campodetalle['certificado'] : '';
						$detalle['detalle_suma_asegurada'] = isset($campodetalle['suma_asegurada']) ? $campodetalle['suma_asegurada'] : '';
						$detalle['detalle_deducible'] = isset($campodetalle['deducible']) ? $campodetalle['deducible'] : '';
						$detalle['detalle_unico'] = $_POST['detalleunico'];

						$num = InteresesAsegurados_detalles::where('id_intereses', $detalle['id_intereses'])->where('detalle_unico', $detalle['detalle_unico'])->count();
						if ($num > 0) {
							$det = InteresesAsegurados_detalles::where('id_intereses', $detalle['id_intereses'])->where('detalle_unico', $detalle['detalle_unico'])->update($detalle);
							if($det->detalle_certificado!=$detalle['detalle_certificado'])
							{
								$comentario.="<b>Campo: No. Certificado</b><br>Valor Actual:".$det->detalle_certificado."<br>Valor Anterior: ".$detalle['detalle_certificado']."<br><br>";
							}
							if($det->detalle_suma_asegurada!=$detalle['detalle_suma_asegurada'])
							{
								$comentario.="<b>Campo: Suma Asegurada</b><br>Valor Actual:".$det->detalle_suma_asegurada."<br>Valor Anterior: ".$detalle['detalle_suma_asegurada']."<br><br>";
							}
							if($det->detalle_prima!=$detalle['detalle_prima'])
							{
								$comentario.="<b>Campo: Prima neta</b><br>Valor Actual:".$det->detalle_prima."<br>Valor Anterior: ".$detalle['detalle_prima']."<br><br>";
							}
							if($det->detalle_deducible!=$detalle['detalle_deducible'])
							{
								$comentario.="<b>Campo: Deducible</b><br>Valor Actual:".$det->detalle_deducible."<br>Valor Anterior: ".$detalle['detalle_deducible']."<br><br>";
							}
						} else {
							$det = InteresesAsegurados_detalles::create($detalle);
							if($det->detalle_certificado!="")
							{
								$comentario.="<b>Campo: No. Certificado</b><br>Valor: ".$det->detalle_certificado."<br><br>";
							}
							if($det->detalle_suma_asegurada!="")
							{
								$comentario.="<b>Campo: Suma Asegurada</b><br>Valor: ".$det->detalle_suma_asegurada."<br><br>";
							}
							if($det->detalle_prima!="")
							{
								$comentario.="<b>Campo: Prima neta</b><br>Valor: ".$det->detalle_prima."<br><br>";
							}
							if($det->detalle_deducible!="")
							{
								$comentario.="<b>Campo: Deducible</b><br>Valor: ".$det->detalle_deducible."<br><br>";
							}
						}
						
						$usuario_registro = Usuario_orm::find($this->session->userdata['id_usuario']);
						$comentario.="Registrado Por: ".$fieldset['creado_por'] =$usuario_registro->nombre." ".$usuario_registro ->apellido;

						$fieldset["comentario"] = $comentario;
						$fieldset["comentable_id"] = $detalle['detalle_unico'];
						$fieldset["usuario_id"] = $this->session->userdata['id_usuario'];
						$fieldset["empresa_id"] = $this->empresa_id;

						$interesase = $this->bitacoraModel->create($fieldset);

					}
					
					
                    //Subir documentos
					if (!empty($_FILES['file'])) {
						$carga_id = $carga->id;
						unset($_POST["campo"]);
						$modeloInstancia = $this->CargaModel->find($carga_id);
						$this->documentos->subir($modeloInstancia);
					}
				} else {

					if ($this->auth->has_permission('acceso', 'intereses_asegurados/editar/(:any)')) {
                        //dd($_POST);
						$cargaInt = AseguradosModel::findByUuid($campo['uuid']);
						$cargaObj = CargaModel::find($cargaInt->interesestable_id);

						$uuid = $campo['uuid'];
						unset($campo['uuid']);
						$campo["fecha_despacho"] = !empty($campo['fecha_despacho']) ? $campo['fecha_despacho'] : NULL;
						$campo["fecha_arribo"] = !empty($campo['fecha_arribo']) ? $campo['fecha_arribo'] : NULL;
                        
						$actCarga = $this->CargaModel->find($cargaInt->interesestable_id);
					
						$cambio='no';
						if($actCarga->no_liquidacion!=$campo['no_liquidacion'])
						{
							$comentario.="<b>Campo: N°. de Liquidación</b><br>Valor Actual: ".$campo['no_liquidacion']."<br>Valor Anterior:".$actCarga->no_liquidacion."<br><br>";
							$cambio='si';
						}
						if($actCarga->fecha_despacho!=$campo['fecha_despacho'])
						{
							$cambio='si';
							$comentario.="<b>Campo: Fecha de Despacho</b><br>Valor Actual:".$campo['fecha_despacho']."<br>Valor Anterior:".$actCarga->fecha_despacho."<br><br>";
						}
						if($actCarga->fecha_arribo!=$campo['fecha_arribo']){
							$comentario.="<b>Campo: Fecha de Arribo</b><br>Valor Actual:".$campo['fecha_arribo']."<br>Valor Anterior:".$actCarga->fecha_arribo."<br><br>";
							$cambio='si';
						}
						if($actCarga->detalle!=$campo['detalle']){
							$comentario.="<b>Campo: Detalle</b><br>Valor Actual:".$campo['detalle']."<br>Valor Anterior:".$actCarga->detalle."<br><br>";
							$cambio='si';
						}
						if($actCarga->valor!=$campo['valor']){
							$comentario.="<b>Campo: Valor Mercancia</b><br>Valor Actual:".$campo['valor']."<br>Valor Anterior:".$actCarga->valor."<br><br>";
							$cambio='si';
						}
						if($actCarga->tipo_empaque!=$campo['tipo_empaque']){
							$ipo_empaque_nuevo=Catalogos::find($campo['tipo_empaque']);
							$comentario.="<b>Campo: Tipo de Empaque</b><br>Valor Actual:".$ipo_empaque_nuevo->etiqueta."<br>Valor Anterior:".$actCarga->datosTipoEmpaque->etiqueta."<br><br>";
							$cambio='si';
						}
						if($actCarga->condicion_envio!=$campo['condicion_envio']){
							$condicion_envio_nuevo=Catalogos::find($campo['condicion_envio']);
							$comentario.="<b>Campo: Condición de Envío</b><br>Valor Actual:".$condicion_envio_nuevo->etiqueta."<br>Valor Anterior:".$actCarga->datosCondicionEnvio->etiqueta."<br><br>";
							$cambio='si';
						}
						if($actCarga->medio_transporte!=$campo['medio_transporte']){
							$medio_transporte_nuevo=Catalogos::find($campo['medio_transporte']);
							$comentario.="<b>Campo: Medio de Transporte</b><br>Valor Actual:".$medio_transporte_nuevo->etiqueta."<br>Valor Anterior:".$actCarga->datosMedioTransporte->etiqueta."<br><br>";
							$cambio='si';
						}
						if($actCarga->origen!=$campo['origen']){
							$comentario.="<b>Campo: Origen</b><br>Valor Actual:".$campo['origen']."<br>Valor Anterior:".$actCarga->origen."<br><br>";
							$cambio='si';
						}
						if($actCarga->destino!=$campo['destino']){
							$comentario.="<b>Campo: Destino</b><br>Valor Actual:".$campo['destino']."<br>Valor Anterior:".$actCarga->destino."<br><br>";
							$cambio='si';
						}
						if($actCarga->acreedor!=$campo['acreedor']){
							if($campo['acreedor']!='otro')
								$acreedor_anterior=Proveedores::find($campo['acreedor'])->nombre;
							else
								$acreedor_anterior='Otro';
							
							if($actCarga->acreedor=='otro')
								$nomb_acreedor='Otro';
							else
								$nomb_acreedor=$actCarga->datosAcreedor->nombre;
							
							$comentario.="<b>Campo: Acreedor</b><br>Valor Actual:".$acreedor_anterior."<br>Valor Anterior:".$nomb_acreedor."<br><br>";
							$cambio='si';
						}
						if($actCarga->acreedor_opcional!=$campo['acreedor_opcional']){
							$comentario.="<b>Campo: Otro Acreedor</b><br>Valor Actual:".$campo['acreedor_opcional']."<br>Valor Anterior:".$actCarga->acreedor_opcional."<br><br>";
							$cambio='si';
						}
						if($actCarga->tipo_obligacion!=$campo['tipo_obligacion']){
							if($campo['tipo_obligacion']!='otro')
								$tipo_obligacion_nuevo=Catalogos::find($campo['tipo_obligacion'])->nombre;
							else
								$tipo_obligacion_nuevo='Otro';
							
							if($actCarga->tipo_obligacion=='otro')
								$tipo='Otro';
							else
								$tipo=$actCarga->datosTipoObligacion->etiqueta;
							$comentario.="<b>Campo: Tipo de Obligación</b><br>Valor Actual:".$tipo_obligacion_nuevo."<br>Valor Anterior:".$tipo."<br><br>";
							$cambio='si';
						}
						if($actCarga->tipo_obligacion_opcional!=$campo['tipo_obligacion_opcional']){
							$comentario.="<b>Campo: Otro Tipo de Obligación</b><br>Valor Actual:".$campo['tipo_obligacion_opcional']."<br>Valor Anterior:".$actCarga->tipo_obligacion_opcional."<br><br>";
							$cambio='si';
						}
						if($actCarga->observaciones!=$campo['observaciones']){
							$comentario.="<b>Campo: Observaciones</b><br>Valor Actual:".$campo['observaciones']."<br>Valor Anterior:".$actCarga->observaciones."<br><br>";
							$cambio='si';
						}
						
						$actCarga = $this->CargaModel->where('id', $cargaInt->interesestable_id)->update($campo);
                        //Tabla principal
						$intereses_asegurados = $this->AseguradosModel->findByInteresesTable($cargaObj->id, $cargaObj->tipo_id);
						$fieldset['identificacion'] = $cargaObj->no_liquidacion;
						if($intereses_asegurados->estado!=$campo['estado']){
							$comentario.="<b>Campo: Estado</b><br>Valor Actual:".$campo['estado']."<br>Valor Anterior:".$intereses_asegurados->estado."<br><br>";
							$cambio='si';
						}
						$fieldset['estado'] = $campo['estado'];
                        //$intereses_asegurados->update($fieldset);
						$intase = $this->AseguradosModel->where('id', $intereses_asegurados->id)->update($fieldset);

						$codigo = $intereses_asegurados->numero;

						if ($campodesde['desde'] == "solicitudes") {
							$detalle = array();
							$detalle['id_intereses'] = $intereses_asegurados->id;
							$detalle['detalle_relacion'] = isset($campodetalle['relacion']) ? $campodetalle['relacion'] : '';
							$detalle['detalle_prima'] = isset($campodetalle['prima_anual']) ? $campodetalle['prima_anual'] : '';
							$detalle['detalle_beneficio'] = isset($campodetalle['beneficio_vida']) ? $campodetalle['beneficio_vida'] : '';
							$detalle['detalle_monto'] = isset($campodetalle['monto']) ? $campodetalle['monto'] : '';
							$detalle['detalle_int_asociado'] = isset($campodetalle['interes_asociado']) ? $campodetalle['interes_asociado'] : '';
							$detalle['detalle_certificado'] = isset($campodetalle['certificado']) ? $campodetalle['certificado'] : '';
							$detalle['detalle_suma_asegurada'] = isset($campodetalle['suma_asegurada']) ? $campodetalle['suma_asegurada'] : '';
							$detalle['detalle_deducible'] = isset($campodetalle['deducible']) ? $campodetalle['deducible'] : '';
							$detalle['detalle_unico'] = $_POST['detalleunico'];

							$num = InteresesAsegurados_detalles::where('id_intereses', $detalle['id_intereses'])->where('detalle_unico', $detalle['detalle_unico'])->count();
							if ($num > 0) {
								$num1 = InteresesAsegurados_detalles::where('id_intereses', $detalle['id_intereses'])->where('detalle_unico', $detalle['detalle_unico'])->first();

								$det = InteresesAsegurados_detalles::where('id_intereses', $detalle['id_intereses'])->where('detalle_unico', $detalle['detalle_unico'])->update($detalle);
							
								if($num1->detalle_certificado!=$detalle['detalle_certificado'])
								{
									$comentario.="<b>Campo: No. Certificado</b><br>Valor Actual:".$detalle['detalle_certificado']."<br>Valor Anterior: ".$num1->detalle_certificado."<br><br>";
									$cambio='si';
								}
								if($num1->detalle_suma_asegurada!=$detalle['detalle_suma_asegurada'])
								{
									$comentario.="<b>Campo: Suma Asegurada</b><br>Valor Actual:".$detalle['detalle_suma_asegurada']."<br>Valor Anterior: ".$num1->detalle_suma_asegurada."<br><br>";
									$cambio='si';
								}
								if($num1->detalle_prima!=$detalle['detalle_prima'])
								{
									$comentario.="<b>Campo: Prima neta</b><br>Valor Actual:".$detalle['detalle_prima']."<br>Valor Anterior: ".$num1->detalle_prima."<br><br>";
									$cambio='si';
								}
								if($num1->detalle_deducible!=$detalle['detalle_deducible'])
								{
									$comentario.="<b>Campo: Deducible</b><br>Valor Actual:".$detalle['detalle_deducible']."<br>Valor Anterior: ".$num1->detalle_deducible."<br><br>";
									$cambio='si';
								}
							
							} else {
								$det = InteresesAsegurados_detalles::create($detalle);
								if($det->detalle_certificado!="")
								{
									$comentario.="<b>Campo: No. Certificado</b><br>Valor Actual:".$det->detalle_certificado."<br><br>";
									$cambio='si';
								}
								if($det->detalle_suma_asegurada!="")
								{
									$comentario.="<b>Campo: Suma Asegurada</b><br>Valor Actual:".$det->detalle_suma_asegurada."<br><br>";
									$cambio='si';
								}
								if($det->detalle_prima!="")
								{
									$comentario.="<b>Campo: Prima neta</b><br>Valor Actual:".$det->detalle_prima."<br><br>";
									$cambio='si';
								}
								if($det->detalle_deducible!="")
								{
									$comentario.="<b>Campo: Deducible</b><br>Valor Actual:".$det->detalle_deducible."<br><br>";
									$cambio='si';
								}
							}
						}
						
						$usuario_registro = Usuario_orm::find($this->session->userdata['id_usuario']);

						$comentario.="Registrado Por: ".$fieldset['creado_por'] =$usuario_registro->nombre." ".$$usuario_registro ->apellido;

						$comentario2="<b>Interés Carga</b><br>N. Liquidación: ".$cargaObj->no_liquidacion."<br><br>";
						$fieldset["comentario"] = $comentario2."".$comentario;
						$fieldset["comentable_type"] = "Actualizacion_interes_solicitudes";
						if($num1->id_solicitudes==''){
							$solicitud=$_POST['detalleunico'];
						}
						else
							$solicitud=$num1->id_solicitudes;
						$fieldset["comentable_id"] = $solicitud;
						$fieldset["usuario_id"] = $this->session->userdata['id_usuario'];
						$fieldset["empresa_id"] = $this->empresa_id;

						if($cambio=='si')
							$interesase = $this->bitacoraModel->create($fieldset);

                        //Subir documentos
						if (!empty($_FILES['file'])) {
							$carga_id = $cargaObj->id;
							unset($_POST["campo"]);
							$modeloInstancia = $this->CargaModel->find($carga_id);
							$this->documentos->subir($modeloInstancia);
						}
					} else {
						$mensaje = array('class' => 'alert-danger', 'contenido' => '<strong>¡Error!</strong>Usted no tiene permisos para editar este registro');
					}
				}
				Capsule::commit();
			} catch (ValidationException $e) {
				log_message('error', $e);
				Capsule::rollback();
			}

			if (!is_null($carga) || !is_null($cargaObj)) {
				$mensaje = array('tipo' => 'success', 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente', 'titulo' => 'Interés asegurado ' . $codigo . '');
			} else {
				$mensaje = array('tipo' => "error", 'mensaje' => '<b>¡&Eacute;rror!</b> Su solicitud no fue procesada', 'titulo' => 'Interés asegurado ' . $codigo . '');
			}
		} else {
			$mensaje = array('tipo' => "error", 'mensaje' => '<b>¡&Eacute;rror!</b> Su solicitud no fue procesada', 'titulo' => 'Interés asegurado ' . $codigo . '');
		}

		$this->session->set_flashdata('mensaje', $mensaje);
		if ($campodesde['desde'] != "solicitudes") {
			redirect(base_url('intereses_asegurados/listar'));
		} else if ($campodesde['desde'] == "solicitudes") {
            //$this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($intereses_asegurados->toArray()))->_display();
			print_r($uuid . "&" . $codigo);
			exit;
		}
	}

	public function proyecto_actividadformularioparcial($data = array()) {

		$this->assets->agregar_js(array(
			'public/assets/js/modules/intereses_asegurados/crear_proyecto.js',
			));
		if (empty($data)) {
			$data["campos"] = array();
		}
        //persona
		$data['uso'] = $this->SegCatalogoRepository->listar_catalogo('uso_vehiculo', 'orden');
		$data['condicion'] = $this->SegCatalogoRepository->listar_catalogo('condicion_vehiculo', 'orden');
		$clause['empresa_id'] = $this->empresa_id;
		$clause['tipo'] = 1;
		$data['tipo_propuesta'] = $this->SegInteresesAseguradosRepository->listar_catalogo('tipo_propuesta', 'orden');
		$data['tipo_fianza'] = $this->SegInteresesAseguradosRepository->listar_catalogo('tipo_propuesta_proyecto', 'orden');
		$data['validez_fianza'] = $this->SegInteresesAseguradosRepository->listar_catalogo('validez_fianza', 'orden');
		$data['acreedores'] = $this->AcreedoresRep->get($clause);
		$data['estado'] = $this->SegCatalogoRepository->listar_catalogo('estado2', 'orden');

		$this->load->view('formularioproyecto', $data);
	}

	function guardar_proyecto() {
		if ($_POST) {
			unset($_POST["campo"]["guardar"]);
			$campo = Util::set_fieldset("campo");
			$campo2 = Util::set_fieldset("campo2");
			$campodesde = Util::set_fieldset("campodesde");
			$campodetalle = Util::set_fieldset("campodetalle");
			if (!isset($campo['uuid'])) {
				$campo['empresa_id'] = $this->empresa_id;
			}
			$uuid = "";
			$proyecto_actividad = null;
			$proyectoObj = null;
			Capsule::beginTransaction();
			try {


				if (empty($campo['uuid'])) {
                    //if(isset($campo['acreedor'])){ $campo['acreedor'] = !empty($campo['acreedor_opcional']) ? $campo['acreedor_opcional'] : $campo['acreedor']; }
					if (isset($campo['tipo_propuesta'])) {
						$campo['tipo_propuesta'] = !empty($campo['tipo_propuesta_opcional']) ? $campo['tipo_propuesta_opcional'] : $campo['tipo_propuesta'];
					}
					if (isset($campo['validez_fianza_pr'])) {
						$campo['validez_fianza_pr'] = !empty($campo['validez_fianza_opcional']) ? $campo['validez_fianza_opcional'] : $campo['validez_fianza_pr'];
					}
					$duplicado = $campo['no_orden'];
					$verificar_proyecto = count($this->interesesAseguradosRep->consultaOrden($duplicado));
					if ($verificar_proyecto == 0) {
						$campo["uuid_proyecto"] = Capsule::raw("ORDER_UUID(uuid())");

						$clause['empresa_id'] = $this->empresa_id;
						$total = $this->interesesAseguradosRep->listar_intereses_asegurados($clause);
						$codigo = Util::generar_codigo('PRO', count($total) + 1);
						$campo["numero"] = $codigo;

						$proyecto_actividad = $this->ProyectoModel->create($campo);
						
						$comentario="<b>Interés Casco Aéreo</b><br><br>";
					
						if($proyecto_actividad->nombre_proyecto!='')
							$comentario.="<b>Campo: Nombre del proyecto o actividad </b><br>Valor: ".$proyecto_actividad->nombre_proyecto."<br><br>";
						if($proyecto_actividad->contratista!='')
							$comentario.="<b>Campo: Contratista</b><br>Valor: ".$proyecto_actividad->contratista."<br><br>";
						if($proyecto_actividad->representante_legal!='')
							$comentario.="<b>Campo: Representante Legal</b><br>Valor: ".$proyecto_actividad->representante_legal."<br><br>";
						if($proyecto_actividad->fecha_consurso!='')
							$comentario.="<b>Campo: Fecha Consurso</b><br>Valor: ".$proyecto_actividad->fecha_consurso."<br><br>";
						if($proyecto_actividad->no_orden!='')
							$comentario.="<b>Campo: No. de orden o contrato</b><br>Valor: ".$proyecto_actividad->no_orden."<br><br>";
						if($proyecto_actividad->duracion!='')
							$comentario.="<b>Campo: Duración del contrato</b><br>Valor: ".$proyecto_actividad->duracion."<br><br>";
						if($proyecto_actividad->fecha!='')
							$comentario.="<b>Campo: Fecha</b><br>Valor: ".$proyecto_actividad->fecha."<br><br>";
						if($proyecto_actividad->tipo_fianza!='')
							$comentario.="<b>Campo: Tipo de Fianza</b><br>Valor: ".$proyecto_actividad->tipodeFianza->etiqueta."<br><br>";
						if($proyecto_actividad->monto!='')
							$comentario.="<b>Campo: Monto del Contrato</b><br>Valor: ".$proyecto_actividad->monto."<br><br>";
						if($proyecto_actividad->monto_afianzado!='')
							$comentario.="<b>Campo: Monto afianzado %</b><br>Valor: ".$proyecto_actividad->monto_afianzado."<br><br>";
						if($proyecto_actividad->tipo_propuesta!='')
						{
							if($proyecto_actividad->tipo_propuesta=='otro')
								$propuesta='Otro';
							else
								$propuesta=$proyecto_actividad->tipodePropuesta->etiqueta;
							$comentario.="<b>Campo: Tipo de propuesta</b><br>Valor: ".$propuesta."<br><br>";
						}
						if($proyecto_actividad->ubicacion!='')
							$comentario.="<b>Campo: Ubicación</b><br>Valor: ".$proyecto_actividad->ubicacion."<br><br>";
						if($proyecto_actividad->acreedor!='')
						{
							if($proyecto_actividad->acreedor=='otro')
								$acree='Otro';
							else
								$acree=$proyecto_actividad->datosAcreedor->nombre;
							$comentario.="<b>Campo: Acreedor</b><br>Valor: ".$acree."<br><br>";
						}
						if($proyecto_actividad->acreedor_opcional!='')
							$comentario.="<b>Campo: Otro Acreedor</b><br>Valor: ".$proyecto_actividad->acreedor_opcional."<br><br>";
						if($proyecto_actividad->validez_fianza_pr!='')
						{
							if($proyecto_actividad->validez_fianza_pr=='otro')
								$validez_fian='Otro';
							else
								$validez_fian=$proyecto_actividad->tipodeFianzapr->etiqueta;
							
							$comentario.="<b>Campo: Validez de la Fianza</b><br>Valor: ".$validez_fian."<br><br>";
						}
						if($proyecto_actividad->validez_fianza_opcional!='')
							$comentario.="<b>Campo: Otra Validez de la Fianza</b><br>Valor: ".$proyecto_actividad->validez_fianza_opcional."<br><br>";
						if($proyecto_actividad->observaciones!='')
							$comentario.="<b>Campo: Observaciones</b><br>Valor: ".$proyecto_actividad->observaciones."<br><br>";
						if($campo2['estado']!='')
							$comentario.="<b>Campo: Estado</b><br>Valor: ".$campo2['estado']."<br><br>";
						
                        //guardar tabla principal
						$fieldset['uuid_intereses'] = $proyecto_actividad->uuid_proyecto;
						$fieldset['empresa_id'] = $proyecto_actividad->empresa_id;
						$fieldset['interesestable_type'] = 6;
						$fieldset['interesestable_id'] = $proyecto_actividad->id;
						$fieldset['numero'] = $codigo;
						$fieldset['identificacion'] = $proyecto_actividad->no_orden."-".$campo['nombre_proyecto'];
						$fieldset["creado_por"] = $this->session->userdata['id_usuario'];
						$fieldset['estado'] = $campo2['estado'];
						$ca = $proyecto_actividad->interesesAsegurados()->create($fieldset);

						if ($campodesde['desde'] == "solicitudes") {
							$u = ia::where('id', $ca->id)->first()->toArray();
							$uuid = bin2hex($u['uuid_intereses']);
							$uuid = $uuid . "&" . $proyecto_actividad->no_orden;

							$detalle = array();
							$detalle['id_intereses'] = $ca->id;
							$detalle['detalle_relacion'] = isset($campodetalle['relacion']) ? $campodetalle['relacion'] : '';
							$detalle['detalle_prima'] = isset($campodetalle['prima_anual']) ? $campodetalle['prima_anual'] : '';
							$detalle['detalle_beneficio'] = isset($campodetalle['beneficio_vida']) ? $campodetalle['beneficio_vida'] : '';
							$detalle['detalle_monto'] = isset($campodetalle['monto']) ? $campodetalle['monto'] : '';
							$detalle['detalle_int_asociado'] = isset($campodetalle['interes_asociado']) ? $campodetalle['interes_asociado'] : '';
							$detalle['detalle_certificado'] = isset($campodetalle['certificado']) ? $campodetalle['certificado'] : '';
							$detalle['detalle_suma_asegurada'] = isset($campodetalle['suma_asegurada']) ? $campodetalle['suma_asegurada'] : '';
							$detalle['detalle_deducible'] = isset($campodetalle['deducible']) ? $campodetalle['deducible'] : '';
							$detalle['detalle_unico'] = $_POST['detalleunico'];

							$num = InteresesAsegurados_detalles::where('id_intereses', $detalle['id_intereses'])->where('detalle_unico', $detalle['detalle_unico'])->count();
							if ($num > 0) {
								$det = InteresesAsegurados_detalles::where('id_intereses', $detalle['id_intereses'])->where('detalle_unico', $detalle['detalle_unico'])->update($detalle);
								if($det->detalle_certificado!=$detalle['detalle_certificado'])
								{
									$comentario.="<b>Campo: No. Certificado</b><br>Valor Actual:".$det->detalle_certificado."<br>Valor Anterior: ".$detalle['detalle_certificado']."<br><br>";
								}
								if($det->detalle_suma_asegurada!=$detalle['detalle_suma_asegurada'])
								{
									$comentario.="<b>Campo: Suma Asegurada</b><br>Valor Actual:".$det->detalle_suma_asegurada."<br>Valor Anterior: ".$detalle['detalle_suma_asegurada']."<br><br>";
								}
								if($det->detalle_prima!=$detalle['detalle_prima'])
								{
									$comentario.="<b>Campo: Prima neta</b><br>Valor Actual:".$det->detalle_prima."<br>Valor Anterior: ".$detalle['detalle_prima']."<br><br>";
								}
								if($det->detalle_deducible!=$detalle['detalle_deducible'])
								{
									$comentario.="<b>Campo: Deducible</b><br>Valor Actual:".$det->detalle_deducible."<br>Valor Anterior: ".$detalle['detalle_deducible']."<br><br>";
								}
							} else {
								$det = InteresesAsegurados_detalles::create($detalle);
								
								if($det->detalle_certificado!="")
								{
									$comentario.="<b>Campo: No. Certificado</b><br>Valor: ".$det->detalle_certificado."<br><br>";
								}
								if($det->detalle_suma_asegurada!="")
								{
									$comentario.="<b>Campo: Suma Asegurada</b><br>Valor: ".$det->detalle_suma_asegurada."<br><br>";
								}
								if($det->detalle_prima!="")
								{
									$comentario.="<b>Campo: Prima neta</b><br>Valor: ".$det->detalle_prima."<br><br>";
								}
								if($det->detalle_deducible!="")
								{
									$comentario.="<b>Campo: Deducible</b><br>Valor: ".$det->detalle_deducible."<br><br>";
								}
							}
							
							$usuario_registro = Usuario_orm::find($this->session->userdata['id_usuario']);
							$comentario.="Registrado Por: ".$fieldset['creado_por'] =$usuario_registro->nombre." ".$usuario_registro ->apellido;

							$fieldset["comentario"] = $comentario;
							$fieldset["comentable_id"] = $detalle['detalle_unico'];
							$fieldset["usuario_id"] = $this->session->userdata['id_usuario'];
							$fieldset["empresa_id"] = $this->empresa_id;

							$interesase = $this->bitacoraModel->create($fieldset);

						}
                        //Subir documentos
						if (!empty($_FILES['file'])) {
							$proyecto_id = $proyecto_actividad->id;
                            //var_dump($proyecto_id;
							unset($_POST["campo"]);
							$modeloInstancia = $this->ProyectoModel->find($proyecto_actividad->id);
							$this->documentos->subir($modeloInstancia);
						}
					} else {
						$proyecto_actividad = "";
						$mensaje = array('tipo' => "error", 'mensaje' => '<b>¡&Eacute;rror!</b> Registro ya existe', 'titulo' => 'Intereses Asegurados ' . $_POST["campo"]["nombre_proyecto"]);
						$this->session->set_flashdata('mensaje', $mensaje);
						redirect(base_url('intereses_asegurados/listar'));
					}
				} else {
					if ($this->auth->has_permission('acceso', 'intereses_asegurados/editar/(:any)')) {
						$duplicado_p2 = $campo2['no_ordenr'];
						$duplicado_p = $campo['no_orden'];
						$uuid = $campo['uuid'];
						$uuid = $uuid . "&" . $campo2['no_ordenr'];
						$valide_f = isset($campo['validez_fianza_pr']) ? $campo['validez_fianza_pr'] : '';
						$acreedor_opc = isset($campo['acreedor_opcional']) ? $campo['acreedor_opcional'] : '';
						$tipo_fianza = isset($campo['tipo_fianza']) ? $campo['tipo_fianza'] : '';
						$acreedor_pro = isset($campo['acreedor']) ? $campo['acreedor'] : '';
						$tipo_prop = isset($campo['tipo_propuesta']) ? $campo['tipo_propuesta'] : '';
						if ($tipo_fianza == "propuesta") {

							$campo['tipo_propuesta'] = $campo['tipo_propuesta'];
							if ($tipo_prop == "otro") {
								$campo['tipo_propuesta_opcional'] = $campo['tipo_propuesta_opcional'];
							} else {
								$campo['tipo_propuesta_opcional'] = "";
							}
							$campo['tipo_propuesta_opcional'] = $campo['tipo_propuesta_opcional'];
						} else {
							$campo['tipo_propuesta'] = "";
						}
						if ($valide_f == "otro") {
							$campo['validez_fianza_opcional'] = $campo['validez_fianza_opcional'];
						} else {
							$campo['validez_fianza_opcional'] = "";
						}

						if ($acreedor_pro == "otro") {
							$campo['acreedor_opcional'] = $campo['acreedor_opcional'];
						} else {
							$campo['acreedor_opcional'] = "";
						}
						if ($duplicado_p != $duplicado_p2) {
							$verificar_proyecto = count($this->interesesAseguradosRep->consultaOrden($duplicado_p));
						} else {
							$verificar_proyecto = 0;
						}
						if ($verificar_proyecto == 0) {
							$intereses_asegurados = $this->interesesAseguradosRep->verInteresAsegurado(hex2bin(strtolower($campo['uuid'])));
							$codigo = $intereses_asegurados->numero;
							$proyectoObj = $this->ProyectoModel->find($intereses_asegurados->proyecto_actividad->id);
							
							$cambio='no';
							if($proyectoObj->nombre_proyecto!=$campo['nombre_proyecto'])
							{
								$comentario.="<b>Campo: Nombre del proyecto o actividad</b><br>Valor Actual: ".$campo['nombre_proyecto']."<br>Valor Anterior:".$proyectoObj->nombre_proyecto."<br><br>";
								$cambio='si';
							}
							if($proyectoObj->contratista!=$campo['contratista']){
								$comentario.="<b>Campo: Contratista</b><br>Valor Actual:".$campo['contratista']."<br>Valor Anterior:".$proyectoObj->contratista."<br><br>";
								$cambio='si';
							}
							if($proyectoObj->representante_legal!=$campo['representante_legal']){
								$comentario.="<b>Campo: Representante Legal</b><br>Valor Actual:".$campo['representante_legal']."<br>Valor Anterior:".$proyectoObj->representante_legal."<br><br>";
								$cambio='si';
							}
							if($proyectoObj->fecha_consurso!=$campo['fecha_consurso']){
								$comentario.="<b>Campo: Fecha Consurso</b><br>Valor Actual:".$campo['fecha_consurso']."<br>Valor Anterior:".$proyectoObj->fecha_consurso."<br><br>";
								$cambio='si';
							}
							if($proyectoObj->no_orden!=$campo['no_orden']){
								$comentario.="<b>Campo: No. de orden o contrato</b><br>Valor Actual:".$campo['no_orden']."<br>Valor Anterior:".$proyectoObj->no_orden."<br><br>";
								$cambio='si';
							}
							if($proyectoObj->duracion!=$campo['duracion']){
								$comentario.="<b>Campo: Duración</b><br>Valor Actual:".$campo['duracion']."<br>Valor Anterior:".$proyectoObj->duracion."<br><br>";
								$cambio='si';
							}
							if($proyectoObj->fecha!=$campo['fecha']){
								$comentario.="<b>Campo: Fecha de Inicio</b><br>Valor Actual:".$campo['fecha']."<br>Valor Anterior:".$proyectoObj->fecha."<br><br>";
								$cambio='si';
							}
							if($proyectoObj->tipo_fianza!=$campo['tipo_fianza']){
								$fianza_nueva=Catalogos::find($campo['tipo_fianza'])->etiqueta;
								$comentario.="<b>Campo: Tipo de Fianza</b><br>Valor Actual:".$fianza_nueva."<br>Valor Anterior:".$proyecto_actividad->tipodeFianza->etiqueta."<br><br>";
								$cambio='si';
							}
							if($proyectoObj->monto!=$campo['monto']){
								$comentario.="<b>Campo: Monto del contrato</b><br>Valor Actual:".$campo['monto']."<br>Valor Anterior:".$proyectoObj->monto."<br><br>";
								$cambio='si';
							}
							if($proyectoObj->monto_afianzado!=$campo['monto_afianzado']){
								$comentario.="<b>Campo: Monto afianzado %</b><br>Valor Actual:".$campo['monto_afianzado']."<br>Valor Anterior:".$proyectoObj->monto_afianzado."<br><br>";
								$cambio='si';
							}
							if($proyectoObj->tipo_propuesta!=$campo['tipo_propuesta']){
								if($proyectoObj->tipo_propuesta=='otro')
									$tip_tipo_propuesta='Otro';
								else
									$tip_tipo_propuesta=$proyecto_actividad->tipodePropuesta->etiqueta;
								
								if($campo['tipo_propuesta']=='otro')
									$tipo_propuesta_nuevo='Otro';
								else
									$tipo_propuesta_nuevo=Catalogos::find($campo['tipo_propuesta'])->etiqueta;
								$comentario.="<b>Campo: Tipo de Propuesta</b><br>Valor Actual:".$tipo_propuesta_nuevo."<br>Valor Anterior:".$tip_tipo_propuesta."<br><br>";
								$cambio='si';
							}
							if($proyectoObj->ubicacion!=$campo['ubicacion']){
								$comentario.="<b>Campo: Ubicación</b><br>Valor Actual:".$campo['ubicacion']."<br>Valor Anterior:".$proyectoObj->ubicacion."<br><br>";
								$cambio='si';
							}
							if($proyectoObj->acreedor!=$campo['acreedor']){
								if($proyectoObj->acreedor=='otro')
									$acreedor_viejo='Otro';
								else
									$acreedor_viejo=$proyectoObj->datosAcreedor->nombre;
								
								if($campo['acreedor']=='otro')
									$acreedor_nuevo='Otro';
								else
									$acreedor_nuevo=Proveedores::find($campo['acreedor'])->nombre;
								
								$comentario.="<b>Campo: Acreedor</b><br>Valor Actual:".$acreedor_nuevo."<br>Valor Anterior:".$acreedor_viejo."<br><br>";
								$cambio='si';
							}
							if($proyectoObj->acreedor_opcional!=$campo['acreedor_opcional']){
								$comentario.="<b>Campo: Otro Acreedor</b><br>Valor Actual:".$campo['acreedor_opcional']."<br>Valor Anterior:".$proyectoObj->acreedor_opcional."<br><br>";
								$cambio='si';
							}
							if($proyectoObj->validez_fianza_pr!=$campo['validez_fianza_pr']){
								if($proyectoObj->validez_fianza_pr=='otro')
									$validez_antigua='Otro';
								else
									$validez_antigua=$proyecto_actividad->tipodeFianzapr->etiqueta;
								
								if($campo['validez_fianza_pr']=='otro')
									$validez_nuevo='Otro';
								else
									$validez_nuevo=Catalogos::find($campo['validez_fianza_pr'])->etiqueta;
								$comentario.="<b>Campo: Validez de la Fianza</b><br>Valor Actual:".$validez_nuevo."<br>Valor Anterior:".$validez_antigua."<br><br>";
								$cambio='si';
							}
							if($proyectoObj->validez_fianza_opcional!=$campo['validez_fianza_opcional']){
								$comentario.="<b>Campo: Otra Validez de la Fianza</b><br>Valor Actual:".$campo['validez_fianza_opcional']."<br>Valor Anterior:".$proyectoObj->validez_fianza_opcional."<br><br>";
								$cambio='si';
							}
							if($proyectoObj->observaciones!=$campo['observaciones']){
								$comentario.="<b>Campo: Observaciones</b><br>Valor Actual:".$campo['observaciones']."<br>Valor Anterior:".$proyectoObj->observaciones."<br><br>";
								$cambio='si';
							}
							$proyectoObj->update($campo);

							$intereses_asegurados->identificacion = $proyectoObj->no_orden;
							if($intereses_asegurados->estado!=$campo2['estado']){
							$comentario.="<b>Campo: Estado</b><br>Valor Actual:".$campo2['estado']."<br>Valor Anterior:".$intereses_asegurados->estado."<br><br>";
							$cambio='si';
						}
							$intereses_asegurados->estado = $campo2['estado'];
							$intereses_asegurados->numero = $proyectoObj->numero;
							$intereses_asegurados->identificacion = $proyectoObj->no_orden."-". $campo['nombre_proyecto'];
							$intereses_asegurados->save();

							if ($campodesde['desde'] == "solicitudes") {
								$detalle = array();
								$detalle['id_intereses'] = $intereses_asegurados->id;
								$detalle['detalle_relacion'] = isset($campodetalle['relacion']) ? $campodetalle['relacion'] : '';
								$detalle['detalle_prima'] = isset($campodetalle['prima_anual']) ? $campodetalle['prima_anual'] : '';
								$detalle['detalle_beneficio'] = isset($campodetalle['beneficio_vida']) ? $campodetalle['beneficio_vida'] : '';
								$detalle['detalle_monto'] = isset($campodetalle['monto']) ? $campodetalle['monto'] : '';
								$detalle['detalle_int_asociado'] = isset($campodetalle['interes_asociado']) ? $campodetalle['interes_asociado'] : '';
								$detalle['detalle_certificado'] = isset($campodetalle['certificado']) ? $campodetalle['certificado'] : '';
								$detalle['detalle_suma_asegurada'] = isset($campodetalle['suma_asegurada']) ? $campodetalle['suma_asegurada'] : '';
								$detalle['detalle_deducible'] = isset($campodetalle['deducible']) ? $campodetalle['deducible'] : '';
								$detalle['detalle_unico'] = $_POST['detalleunico'];

								$num = InteresesAsegurados_detalles::where('id_intereses', $detalle['id_intereses'])->where('detalle_unico', $detalle['detalle_unico'])->count();
								if ($num > 0) {
									$num1 = InteresesAsegurados_detalles::where('id_intereses', $detalle['id_intereses'])->where('detalle_unico', $detalle['detalle_unico'])->first();
									$det = InteresesAsegurados_detalles::where('id_intereses', $detalle['id_intereses'])->where('detalle_unico', $detalle['detalle_unico'])->update($detalle);
									
									if($num1->detalle_certificado!=$detalle['detalle_certificado'])
									{
										$comentario.="<b>Campo: No. Certificado</b><br>Valor Actual:".$detalle['detalle_certificado']."<br>Valor Anterior: ".$num1->detalle_certificado."<br><br>";
										$cambio='si';
									}
									if($num1->detalle_suma_asegurada!=$detalle['detalle_suma_asegurada'])
									{
										$comentario.="<b>Campo: Suma Asegurada</b><br>Valor Actual:".$detalle['detalle_suma_asegurada']."<br>Valor Anterior: ".$num1->detalle_suma_asegurada."<br><br>";
										$cambio='si';
									}
									if($num1->detalle_prima!=$detalle['detalle_prima'])
									{
										$comentario.="<b>Campo: Prima neta</b><br>Valor Actual:".$detalle['detalle_prima']."<br>Valor Anterior: ".$num1->detalle_prima."<br><br>";
										$cambio='si';
									}
									if($num1->detalle_deducible!=$detalle['detalle_deducible'])
									{
										$comentario.="<b>Campo: Deducible</b><br>Valor Actual:".$detalle['detalle_deducible']."<br>Valor Anterior: ".$num1->detalle_deducible."<br><br>";
										$cambio='si';
									}
								} else {
									$det = InteresesAsegurados_detalles::create($detalle);
									
									if($det->detalle_certificado!="")
									{
										$comentario.="<b>Campo: No. Certificado</b><br>Valor Actual:".$det->detalle_certificado."<br><br>";
										$cambio='si';
									}
									if($det->detalle_suma_asegurada!="")
									{
										$comentario.="<b>Campo: Suma Asegurada</b><br>Valor Actual:".$det->detalle_suma_asegurada."<br><br>";
										$cambio='si';
									}
									if($det->detalle_prima!="")
									{
										$comentario.="<b>Campo: Prima neta</b><br>Valor Actual:".$det->detalle_prima."<br><br>";
										$cambio='si';
									}
									if($det->detalle_deducible!="")
									{
										$comentario.="<b>Campo: Deducible</b><br>Valor Actual:".$det->detalle_deducible."<br><br>";
										$cambio='si';
									}
								}
							}
							
							$usuario_registro = Usuario_orm::find($this->session->userdata['id_usuario']);

							$comentario.="Registrado Por: ".$fieldset['creado_por'] =$usuario_registro->nombre." ".$usuario_registro ->apellido;

							$comentario2="<b>Interés Proyecto/Actividad</b><br>Nombre: ".$proyectoObj->nombre_proyecto."<br><br>";
							$fieldset["comentario"] = $comentario2."".$comentario;
							$fieldset["comentable_type"] = "Actualizacion_interes_solicitudes";
							if($num1->id_solicitudes==''){
								$solicitud=$_POST['detalleunico'];
							}
							else
								$solicitud=$num1->id_solicitudes;
							$fieldset["comentable_id"] = $solicitud;
							$fieldset["usuario_id"] = $this->session->userdata['id_usuario'];
							$fieldset["empresa_id"] = $this->empresa_id;

							if($cambio=='si')
								$interesase = $this->bitacoraModel->create($fieldset);

                            //Subir documentos
							if (!empty($_FILES['file'])) {
								$vehiculo_id = $proyectoObj->id;
								unset($_POST["campo"]);
								$modeloInstancia = $this->ProyectoModel->find($vehiculo_id);
								$this->documentos->subir($modeloInstancia);
							}
						} else {
							$mensaje = array('tipo' => "error", 'mensaje' => '<b>¡&Eacute;rror!</b> Registro ya existe', 'titulo' => 'Intereses Asegurados ');
							$this->session->set_flashdata('mensaje', $mensaje);
						}
					} else {
						$mensaje = array('class' => 'alert-danger', 'contenido' => '<strong>¡Error!</strong>Usted no tiene permisos para editar este registro');
					}
				}

				Capsule::commit();
			} catch (ValidationException $e) {
				log_message('error', $e);
				Capsule::rollback();
			}

			if (!is_null($proyecto_actividad) || !is_null($proyectoObj)) {
				$mensaje = array('tipo' => 'success', 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente', 'titulo' => 'Interés asegurado ' . $codigo . '');
			} else {
				$mensaje = array('tipo' => "error", 'mensaje' => '<b>¡&Eacute;rror!</b> Registro ya existe', 'titulo' => 'Intereses Asegurados ' . $campo["nombre_proyecto"]);
			}
		} else {
			$mensaje = array('tipo' => "error", 'mensaje' => '<b>¡&Eacute;rror!</b> Su solicitud no fue procesada', 'titulo' => 'Intereses Asegurados ' . $campo["nombre_proyecto"]);
		}

		$this->session->set_flashdata('mensaje', $mensaje);
		if ($campodesde['desde'] != "solicitudes") {
			redirect(base_url('intereses_asegurados/listar'));
		} else if ($campodesde['desde'] == "solicitudes") {
			print_r($uuid . "&" . $codigo);
			exit;
		}
	}

	function editar($uuid = NULL, $opcion = NULL) {

		if (!is_null($this->session->flashdata('mensaje'))) {
			$mensaje = $this->session->flashdata('mensaje');
		} else {
			$mensaje = [];
		}

        //if (!$this->auth->has_permission('acceso', 'intereses_asegurados/ver/(:any)') && !$this->auth->has_permission('acceso', 'aseguradoras/ver')) {
		if (!$this->auth->has_permission('acceso', 'intereses_asegurados/ver/(:any)') && !$this->auth->has_permission('acceso', 'intereses_asegurados/editar/(:any)')) {
            // No, tiene permiso, redireccionarlo.
			$mensaje = array('tipo' => "error", 'mensaje' => '<b>¡Error!</b> Usted no tiene permisos para ingresar a editar', 'titulo' => 'Intereses Asegurados ');

			$this->session->set_flashdata('mensaje', $mensaje);

			redirect(base_url('intereses_asegurados/listar'));
		}

		if ($this->auth->has_permission('acceso', 'intereses_asegurados/editar/(:any)')) {
			$ceditar = 1;
		} else {
			$ceditar = 0;
		}


		$this->_Css();
		$this->_js();

		$this->assets->agregar_js(array(
			'public/assets/js/modules/intereses_asegurados/formulario.js',
                //'public/assets/js/modules/intereses_asegurados/crear.js',
                //'public/assets/js/default/vue-validator.min.js',   
			));

		$data = array();

		if ($uuid == "")
			$uuid_interes_asegurado = $_POST["campo"]["uuid"];
		else
			$uuid_interes_asegurado = $uuid;

		$intereses_asegurados = $this->interesesAseguradosRep->verInteresAsegurado(hex2bin(strtolower($uuid_interes_asegurado)));

		if (!is_null($intereses_asegurados->persona) && $intereses_asegurados->interesestable_type == 5) {
			$intereses_data = $intereses_asegurados->persona;
			$identificaciones = preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $intereses_asegurados->identificacion) ? "111" : "112";
			$tipo_formulario = "persona";
			$tipo = 5;
		}
		if (!is_null($intereses_asegurados->articulo) && $intereses_asegurados->interesestable_type == 1) {
			$intereses_data = $intereses_asegurados->articulo;
			$tipo_formulario = 'articulo';
			$tipo = 1;
		}
		if (!is_null($intereses_asegurados->ubicacion) && $intereses_asegurados->interesestable_type == 7) {
			$intereses_data = $intereses_asegurados->ubicacion;
			$tipo_formulario = 'ubicacion';
			$tipo = 7;
		}
		if (!is_null($intereses_asegurados->carga) && $intereses_asegurados->interesestable_type == 2) {
			$intereses_data = $intereses_asegurados->carga;
			$tipo_formulario = 'carga';
			$tipo = 2;
		}
		if (!is_null($intereses_asegurados->vehiculo) && $intereses_asegurados->interesestable_type == 8) {
			$intereses_data = $intereses_asegurados->vehiculo;
			$tipo_formulario = 'vehiculo';
			$tipo = 8;
            //$intereses_data->uuid_vehiculo = $uuid;
		}
		if (!is_null($intereses_asegurados->casco_aereo) && $intereses_asegurados->interesestable_type == 3) {
			$intereses_data = $intereses_asegurados->casco_aereo;
			$tipo_formulario = 'casco_aereo';
			$tipo = 3;
		}
		if (!is_null($intereses_asegurados->casco_maritimo) && $intereses_asegurados->interesestable_type == 4) {
			$intereses_data = $intereses_asegurados->casco_maritimo;
			$tipo_formulario = 'casco_maritimo';
			$tipo = 4;
		}
		if (!is_null($intereses_asegurados->proyecto_actividad) && $intereses_asegurados->interesestable_type == 6) {
			$intereses_data = $intereses_asegurados->proyecto_actividad;
			$tipo_formulario = 'proyecto_actividad';
			$tipo = 6;
		}

		$ramos = Ramos::where(['id_tipo_int_asegurado' => $tipo, 'id_tipo_poliza' => 1, 'estado' => 1, 'empresa_id' => $this->empresa_id])->get();
        //Menu para crear
		$clause = array('empresa_id' => $this->empresa_id);
        //catalogo para buscador        
		$menu_crear = $this->ramoRepository->listar_cuentas($clause);

		$breadcrumb = array(
			"titulo" => '<i class="fa fa-archive"></i> Intereses Asegurados: ' . $intereses_asegurados->numero,
			"ruta" => array(
				0 => array("nombre" => "Seguros", "url" => "#", "activo" => false),
				1 => array("nombre" => "Intereses Asegurados", "url" => "intereses_asegurados/listar", "activo" => false),
				2 => array("nombre" => $intereses_asegurados->numero, "activo" => true)
				),
			"filtro" => false,
			"menu" => array()
			);

		if ($this->auth->has_permission('editar__cambiarEstado', 'intereses_asegurados/editar/(:any)') == true) {
			$cestado = 1;
		} else {
			$cestado = 0;
		}

		$this->assets->agregar_var_js(array(
			"formulario_seleccionado" => $intereses_asegurados->tipo->valor,
			"intereses_asegurados_id_" . $intereses_asegurados->tipo->valor => $intereses_data->id,
			"permiso_editar" => $ceditar,
			"desde" => "intereses_asegurados",
			"permiso_cambio_estado" => $cestado,
			"vista" => 'editar',
			"data" => json_encode($intereses_data)
			));

		if ($this->auth->has_permission('acceso', 'intereses_asegurados/editar/(:any)')) {
			$breadcrumb["menu"] = array(
				"url" => 'javascript:',
				"clase" => 'crearAccion',
				"nombre" => "Acción "
				);

			$menuOpciones = array();
			$menuOpciones["#crearSolicitudLnk"] = "Crear Solicitud";
			$menuOpciones["#imprimirLnk"] = "Imprimir";
			$menuOpciones["#subirDocumentoLnk"] = "Subir Documento";
			$menuOpciones["#exportarBtn"] = "Exportar";

			$breadcrumb["menu"]["opciones"] = $menuOpciones;
		}

		$data["subpanels"] = [];

		$data["campos"] = array(
			"campos" => array(
				"tipos_intereses_asegurados" => $this->InteresesAsegurados_catModel->get(),
				"datos" => $intereses_data,
				"tipoformulario" => $tipo_formulario,
				"uuid" => $uuid,
				"tipo" => $tipo,
				"id" => $intereses_data->id,
				"estado" => $intereses_asegurados->estado,
				"politicas" => $this->politicas,
				"politicas_general" => $this->politicas_general,
				"ramos" => $ramos,
				"menu_crear" => $menu_crear
				),
			);


		$this->template->agregar_titulo_header('Aseguradoras');
		$this->template->agregar_breadcrumb($breadcrumb);
		$this->template->agregar_contenido($data);
		$this->template->visualizar();
	}

	public function imprimirFormulario($uuid = null) {
		if ($uuid == null) {
			return false;
		}

		$intereses_asegurados = $this->interesesAseguradosRep->verInteresAsegurado(hex2bin(strtolower($uuid)));
		$nombre = $intereses_asegurados->numero;

		if ($_GET['tipo'] == 8) {
			$formulario = "formularioVehiculo";
		} else if ($_GET['tipo'] == 6) {
			$formulario = "formularioProyecto";
		} else if ($_GET['tipo'] == 2) {
			$formulario = "formularioCarga";
		} else if ($_GET['tipo'] == 4) {
			$formulario = "formularioCascoMaritimo";
		} else if ($_GET['tipo'] == 5) {
			$formulario = "formularioPersona";
		} elseif ($_GET['tipo'] == 1) {
			$formulario = "formularioArticulo";
		} elseif ($_GET['tipo'] == 3) {
			$formulario = "formularioCascoAereo";
		} elseif ($_GET['tipo'] == 7) {
			$formulario = "formularioUbicacion";
		}

		$data = ['datos' => $intereses_asegurados];
		$dompdf = new Dompdf();
		$html = $this->load->view('pdf/' . $formulario, $data, true);
		$dompdf->loadHtml($html);
		$dompdf->setPaper('A4', 'portrait');
		$dompdf->render();
		$dompdf->stream($nombre);
	}

	public function formularioModal($data = NULL) {

		$this->assets->agregar_js(array(
                //'public/assets/js/modules/documentos/formulario.controller.js'
			));

		$this->load->view('formularioModalDocumento', $data);
	}

	public function formularioModalEditar($data = NULL) {

		$numero = $this->AseguradosModel->where('interesestable_id', $data['campos']['datos']->id)->first();
        //$data=$numero->toArray();
		$this->assets->agregar_var_js(array(
			"numero" => $numero->numero,
			'data' => "",
			));

		$this->load->view('formularioModalDocumentoEditar', $data);
	}

	function ajax_guardar_documentos() {
		if (empty($_POST)) {
			return false;
		}

		$intereses_id = $this->input->post('id', true);
		$uuid = $this->input->post('uuid_interes', true);
		$intereses_type = $this->input->post('intereses_type', true);

		if ($intereses_type == 1) {
			$modeloInstancia = $this->ArticuloModel->find($intereses_id);
		}
		if ($intereses_type == 2) {
			$modeloInstancia = $this->CargaModel->find($intereses_id);
		}
		if ($intereses_type == 3) {
			$modeloInstancia = $this->AereoModel->find($intereses_id);
		}
		if ($intereses_type == 4) {
			$modeloInstancia = $this->MaritimoModel->find($intereses_id);
		}
		if ($intereses_type == 5) {
			$modeloInstancia = $this->PersonasModel->find($intereses_id);
		}
		if ($intereses_type == 6) {
			$modeloInstancia = $this->ProyectoModel->find($intereses_id);
		}
		if ($intereses_type == 7) {
			$modeloInstancia = $this->UbicacionModel->find($intereses_id);
		}
		if ($intereses_type == 8) {
			$modeloInstancia = $this->VehiculoModel->find($intereses_id);
		}
		$this->documentos->subir($modeloInstancia);

		redirect(base_url('intereses_asegurados/editar/' . $uuid));
	}

	public function casco_maritimoformularioparcial($data = array()) {
		$this->assets->agregar_js(array(
			'public/assets/js/modules/intereses_asegurados/crear_maritimo.js'
			));
		if (empty($data)) {
			$data["campos"] = array();
		}
		$clause['tipo'] = 1;
        //persona
		$clause['empresa_id'] = $this->empresa_id;
		$data['tipos'] = $this->SegInteresesAseguradosRepository->listar_catalogo('tipo_maritimo', 'orden');
		$data['acreedores'] = $this->AcreedoresRep->get($clause);
		$data['estado'] = $this->SegCatalogoRepository->listar_catalogo('estado2', 'orden');
		$this->load->view('formulariocascomaritimo', $data);
	}

	function guardar_maritimo() {

		if ($_POST) {
			unset($_POST["campo"]["guardar"]);
			$campo = Util::set_fieldset("campo");
			$campo2 = Util::set_fieldset("campo2");
			$campodesde = Util::set_fieldset("campodesde");
			$campodetalle = Util::set_fieldset("campodetalle");
			if (!isset($campo['uuid'])) {
				$campo['empresa_id'] = $this->empresa_id;
			}
			$uuid = "";
			$casco_maritimo = null;
			$maritimoObj = null;
			Capsule::beginTransaction();
			try {
				if (empty($campo['uuid'])) {
					$duplicado_m = $campo['serie'];
					$verificar_maritimo = count($this->interesesAseguradosRep->consultaSerie($duplicado_m));
					if ($verificar_maritimo == 0) {
						$campo["uuid_casco_maritimo"] = Capsule::raw("ORDER_UUID(uuid())");
						$clause['empresa_id'] = $this->empresa_id;
						$total = $this->interesesAseguradosRep->listar_intereses_asegurados($clause);
						$codigo = Util::generar_codigo('CMA', count($total) + 1);
						$campo['acreedor'] = isset($campo['acreedor']) ? $campo['acreedor'] : '0';
						$campo["numero"] = $codigo;
						$casco_maritimo = $this->MaritimoModel->create($campo);
						
						$comentario="<b>Interés Casco Marítimo</b><br><br>";
					
						if($casco_maritimo->serie!='')
							$comentario.="<b>Campo: N°. de serie del casco </b><br>Valor: ".$casco_maritimo->serie."<br><br>";
						if($casco_maritimo->nombre_embarcacion!='')
							$comentario.="<b>Campo: Nombre de la Embarcación</b><br>Valor: ".$casco_maritimo->nombre_embarcacion."<br><br>";
						if($casco_maritimo->tipo!='')
							$comentario.="<b>Campo: Tipo</b><br>Valor: ".$casco_maritimo->tipo->etiqueta."<br><br>";
						if($casco_maritimo->marca!='')
							$comentario.="<b>Campo: Marca</b><br>Valor: ".$casco_maritimo->marca."<br><br>";
						if($casco_maritimo->valor!='')
							$comentario.="<b>Campo: Valor</b><br>Valor: ".$casco_maritimo->valor."<br><br>";
						if($casco_maritimo->pasajeros!='')
							$comentario.="<b>Campo: Pasajeros</b><br>Valor: ".$casco_maritimo->pasajeros."<br><br>";
						if($casco_maritimo->acreedor!='')
							$comentario.="<b>Campo: Acreedor</b><br>Valor: ".$casco_maritimo->datosAcreedor->nombre."<br><br>";
						if($casco_maritimo->porcentaje_acreedor!='')
							$comentario.="<b>Campo: % Asignado al acreedor</b><br>Valor: ".$casco_maritimo->porcentaje_acreedor."<br><br>";
						if($casco_maritimo->observaciones!='')
							$comentario.="<b>Campo: Observaciones</b><br>Valor: ".$casco_maritimo->observaciones."<br><br>";
						if($campo2['estado']!='')
							$comentario.="<b>Campo: Estado</b><br>Valor: ".$campo2['estado']."<br><br>";
						
						
                        //guardar tabla principal
						$fieldset['uuid_intereses'] = $casco_maritimo->uuid_casco_maritimo;
						$fieldset['empresa_id'] = $casco_maritimo->empresa_id;
						$fieldset['interesestable_type'] = 4;
						$fieldset['interesestable_id'] = $casco_maritimo->id;
						$fieldset['numero'] = $codigo;
						$fieldset['identificacion'] = $casco_maritimo->serie;
						$fieldset['estado'] = $campo2['estado'];
						$fieldset['creado_por'] = $this->session->userdata['id_usuario'];
						$ca = $casco_maritimo->interesesAsegurados()->create($fieldset);

						if ($campodesde['desde'] == "solicitudes") {
							$u = ia::where('id', $ca->id)->first()->toArray();
							$uuid = bin2hex($u['uuid_intereses']);
							$uuid = $uuid . "&" . $casco_maritimo->serie;

							$detalle = array();
							$detalle['id_intereses'] = $ca->id;
							$detalle['detalle_relacion'] = isset($campodetalle['relacion']) ? $campodetalle['relacion'] : '';
							$detalle['detalle_prima'] = isset($campodetalle['prima_anual']) ? $campodetalle['prima_anual'] : '';
							$detalle['detalle_beneficio'] = isset($campodetalle['beneficio_vida']) ? $campodetalle['beneficio_vida'] : '';
							$detalle['detalle_monto'] = isset($campodetalle['monto']) ? $campodetalle['monto'] : '';
							$detalle['detalle_int_asociado'] = isset($campodetalle['interes_asociado']) ? $campodetalle['interes_asociado'] : '';
							$detalle['detalle_certificado'] = isset($campodetalle['certificado']) ? $campodetalle['certificado'] : '';
							$detalle['detalle_suma_asegurada'] = isset($campodetalle['suma_asegurada']) ? $campodetalle['suma_asegurada'] : '';
							$detalle['detalle_deducible'] = isset($campodetalle['deducible']) ? $campodetalle['deducible'] : '';
							$detalle['detalle_unico'] = $_POST['detalleunico'];

							$num = InteresesAsegurados_detalles::where('id_intereses', $detalle['id_intereses'])->where('detalle_unico', $detalle['detalle_unico'])->count();
							if ($num > 0) {
								$det = InteresesAsegurados_detalles::where('id_intereses', $detalle['id_intereses'])->where('detalle_unico', $detalle['detalle_unico'])->update($detalle);
								if($det->detalle_certificado!=$detalle['detalle_certificado'])
								{
									$comentario.="<b>Campo: No. Certificado</b><br>Valor Actual:".$det->detalle_certificado."<br>Valor Anterior: ".$detalle['detalle_certificado']."<br><br>";
								}
								if($det->detalle_suma_asegurada!=$detalle['detalle_suma_asegurada'])
								{
									$comentario.="<b>Campo: Suma Asegurada</b><br>Valor Actual:".$det->detalle_suma_asegurada."<br>Valor Anterior: ".$detalle['detalle_suma_asegurada']."<br><br>";
								}
								if($det->detalle_prima!=$detalle['detalle_prima'])
								{
									$comentario.="<b>Campo: Prima neta</b><br>Valor Actual:".$det->detalle_prima."<br>Valor Anterior: ".$detalle['detalle_prima']."<br><br>";
								}
								if($det->detalle_deducible!=$detalle['detalle_deducible'])
								{
									$comentario.="<b>Campo: Deducible</b><br>Valor Actual:".$det->detalle_deducible."<br>Valor Anterior: ".$detalle['detalle_deducible']."<br><br>";
								}
							} else {
								$det = InteresesAsegurados_detalles::create($detalle);
								if($det->detalle_certificado!="")
								{
									$comentario.="<b>Campo: No. Certificado</b><br>Valor: ".$det->detalle_certificado."<br><br>";
								}
								if($det->detalle_suma_asegurada!="")
								{
									$comentario.="<b>Campo: Suma Asegurada</b><br>Valor: ".$det->detalle_suma_asegurada."<br><br>";
								}
								if($det->detalle_prima!="")
								{
									$comentario.="<b>Campo: Prima neta</b><br>Valor: ".$det->detalle_prima."<br><br>";
								}
								if($det->detalle_deducible!="")
								{
									$comentario.="<b>Campo: Deducible</b><br>Valor: ".$det->detalle_deducible."<br><br>";
								}
							}
							$usuario_registro = Usuario_orm::find($this->session->userdata['id_usuario']);
							$comentario.="Registrado Por: ".$fieldset['creado_por'] =$usuario_registro->nombre." ".$usuario_registro ->apellido;

							$fieldset["comentario"] = $comentario;
							$fieldset["comentable_id"] = $detalle['detalle_unico'];
							$fieldset["usuario_id"] = $this->session->userdata['id_usuario'];
							$fieldset["empresa_id"] = $this->empresa_id;

							$interesase = $this->bitacoraModel->create($fieldset);

						}
						
						
                        //Subir documentos
						if (!empty($_FILES['file'])) {
							$aereo_id = $casco_maritimo->id;
							unset($_POST["campo"]);
							$modeloInstancia = $this->MaritimoModel->find($aereo_id);
							$this->documentos->subir($modeloInstancia);
						}
					} else {
						$casco_maritimo = "";
						$mensaje = array('tipo' => "error", 'mensaje' => '<b>¡&Eacute;rror!</b> Registro ya existe', 'titulo' => 'Intereses Asegurados ' . $campo["serie"]);
						$this->session->set_flashdata('mensaje', $mensaje);
						redirect(base_url('intereses_asegurados/listar'));
					}
				} else {
					if ($this->auth->has_permission('acceso', 'intereses_asegurados/editar/(:any)')) {
						$duplicado_m2 = $campo2['serier'];
						$duplicado_m = $campo['serie'];
						$uuid = $campo['uuid'];
						$uuid = $uuid . "&" . $campo2['serier'];
						if ($duplicado_m != $duplicado_m2) {
							$verificar_maritimo = count($this->interesesAseguradosRep->consultaSerie($duplicado_m));
						} else {
							$verificar_maritimo = 0;
						}
						if ($verificar_maritimo == 0) {
							$intereses_asegurados = $this->interesesAseguradosRep->verInteresAsegurado(hex2bin(strtolower($campo['uuid'])));
							$codigo = $intereses_asegurados->numero;
							$campo['acreedor'] = isset($campo['acreedor']) ? $campo['acreedor'] : '0';

							$maritimoObj = $this->MaritimoModel->find($intereses_asegurados->casco_maritimo->id);
							
							$cambio='no';
							if($maritimoObj->serie!=$campo['serie'])
							{
								$comentario.="<b>Campo: N°. de serie del casco</b><br>Valor Actual: ".$campo['serie']."<br>Valor Anterior:".$maritimoObj->serie."<br><br>";
								$cambio='si';
							}
							if($maritimoObj->nombre_embarcacion!=$campo['nombre_embarcacion']){
								$comentario.="<b>Campo: Nombre de la Embarcación</b><br>Valor Actual:".$campo['nombre_embarcacion']."<br>Valor Anterior:".$maritimoObj->nombre_embarcacion."<br><br>";
								$cambio='si';
							}
							if($maritimoObj->tipo!=$campo['tipo']){
								$tipo_nuevo=Catalogos::find($campo['tipo']);
								$comentario.="<b>Campo: Tipo</b><br>Valor Actual:".$tipo_nuevo->etiqueta."<br>Valor Anterior:".$maritimoObj->tipotrans->etiqueta."<br><br>";
								$cambio='si';
							}
							if($maritimoObj->marca!=$campo['marca']){
								$comentario.="<b>Campo: Marca</b><br>Valor Actual:".$campo['marca']."<br>Valor Anterior:".$maritimoObj->marca."<br><br>";
								$cambio='si';
							}
							if($maritimoObj->valor!=$campo['valor']){
								$comentario.="<b>Campo: Valor</b><br>Valor Actual:".$campo['valor']."<br>Valor Anterior:".$maritimoObj->valor."<br><br>";
								$cambio='si';
							}
							if($maritimoObj->pasajeros!=$campo['pasajeros']){
								$comentario.="<b>Campo: Pasajeros</b><br>Valor Actual:".$campo['pasajeros']."<br>Valor Anterior:".$maritimoObj->pasajeros."<br><br>";
								$cambio='si';
							}
							if($maritimoObj->acreedor!=$campo['acreedor']){
								$acreedor_actual=Proveedores::find($campo['acreedor']);
								$comentario.="<b>Campo: Acreedor</b><br>Valor Actual:".$acreedor_actual->nombre."<br>Valor Anterior:".$maritimoObj->datosAcreedor->nombre."<br><br>";
								$cambio='si';
							}
							if($maritimoObj->porcentaje_acreedor!=$campo['porcentaje_acreedor']){
								$comentario.="<b>Campo: % Asignado al acreedor</b><br>Valor Actual:".$acreedor_actual->porcentaje_acreedor."<br>Valor Anterior:".$maritimoObj->porcentaje_acreedor."<br><br>";
								$cambio='si';
							}
							if($maritimoObj->observaciones!=$campo['observaciones']){
								$comentario.="<b>Campo: Observaciones</b><br>Valor Actual:".$campo['observaciones']."<br>Valor Anterior:".$maritimoObj->observaciones."<br><br>";
								$cambio='si';
							}
							$maritimoObj->update($campo);

							$intereses_asegurados->identificacion = $maritimoObj->serie;
							if($intereses_asegurados->estado!=$campo2['estado']){
								$comentario.="<b>Campo: Estado</b><br>Valor Actual:".$campo2['estado']."<br>Valor Anterior:".$intereses_asegurados->estado."<br><br>";
								$cambio='si';
							}
							$intereses_asegurados->estado = $campo2['estado'];

							$intereses_asegurados->save();

							if ($campodesde['desde'] == "solicitudes") {
								$detalle = array();
								$detalle['id_intereses'] = $intereses_asegurados->id;
								$detalle['detalle_relacion'] = isset($campodetalle['relacion']) ? $campodetalle['relacion'] : '';
								$detalle['detalle_prima'] = isset($campodetalle['prima_anual']) ? $campodetalle['prima_anual'] : '';
								$detalle['detalle_beneficio'] = isset($campodetalle['beneficio_vida']) ? $campodetalle['beneficio_vida'] : '';
								$detalle['detalle_monto'] = isset($campodetalle['monto']) ? $campodetalle['monto'] : '';
								$detalle['detalle_int_asociado'] = isset($campodetalle['interes_asociado']) ? $campodetalle['interes_asociado'] : '';
								$detalle['detalle_certificado'] = isset($campodetalle['certificado']) ? $campodetalle['certificado'] : '';
								$detalle['detalle_suma_asegurada'] = isset($campodetalle['suma_asegurada']) ? $campodetalle['suma_asegurada'] : '';
								$detalle['detalle_deducible'] = isset($campodetalle['deducible']) ? $campodetalle['deducible'] : '';
								$detalle['detalle_unico'] = $_POST['detalleunico'];

								$num = InteresesAsegurados_detalles::where('id_intereses', $detalle['id_intereses'])->where('detalle_unico', $detalle['detalle_unico'])->count();
								if ($num > 0) {
									$num1 = InteresesAsegurados_detalles::where('id_intereses', $detalle['id_intereses'])->where('detalle_unico', $detalle['detalle_unico'])->first();

									$det = InteresesAsegurados_detalles::where('id_intereses', $detalle['id_intereses'])->where('detalle_unico', $detalle['detalle_unico'])->update($detalle);
									
									if($num1->detalle_certificado!=$detalle['detalle_certificado'])
									{
										$comentario.="<b>Campo: No. Certificado</b><br>Valor Actual:".$detalle['detalle_certificado']."<br>Valor Anterior: ".$num1->detalle_certificado."<br><br>";
										$cambio='si';
									}
									if($num1->detalle_suma_asegurada!=$detalle['detalle_suma_asegurada'])
									{
										$comentario.="<b>Campo: Suma Asegurada</b><br>Valor Actual:".$detalle['detalle_suma_asegurada']."<br>Valor Anterior: ".$num1->detalle_suma_asegurada."<br><br>";
										$cambio='si';
									}
									if($num1->detalle_prima!=$detalle['detalle_prima'])
									{
										$comentario.="<b>Campo: Prima neta</b><br>Valor Actual:".$detalle['detalle_prima']."<br>Valor Anterior: ".$num1->detalle_prima."<br><br>";
										$cambio='si';
									}
									if($num1->detalle_deducible!=$detalle['detalle_deducible'])
									{
										$comentario.="<b>Campo: Deducible</b><br>Valor Actual:".$detalle['detalle_deducible']."<br>Valor Anterior: ".$num1->detalle_deducible."<br><br>";
										$cambio='si';
									}
								
								} else {
									$det = InteresesAsegurados_detalles::create($detalle);
									
									if($det->detalle_certificado!="")
									{
										$comentario.="<b>Campo: No. Certificado</b><br>Valor Actual:".$det->detalle_certificado."<br><br>";
										$cambio='si';
									}
									if($det->detalle_suma_asegurada!="")
									{
										$comentario.="<b>Campo: Suma Asegurada</b><br>Valor Actual:".$det->detalle_suma_asegurada."<br><br>";
										$cambio='si';
									}
									if($det->detalle_prima!="")
									{
										$comentario.="<b>Campo: Prima neta</b><br>Valor Actual:".$det->detalle_prima."<br><br>";
										$cambio='si';
									}
									if($det->detalle_deducible!="")
									{
										$comentario.="<b>Campo: Deducible</b><br>Valor Actual:".$det->detalle_deducible."<br><br>";
										$cambio='si';
									}
								}
							}
							
							$usuario_registro = Usuario_orm::find($this->session->userdata['id_usuario']);

							$comentario.="Registrado Por: ".$fieldset['creado_por'] =$usuario_registro->nombre." ".$usuario_registro ->apellido;

							$comentario2="<b>Interés Casco Marítimo</b><br>Chasis: ".$maritimoObj->serie."<br><br>";
							$fieldset["comentario"] = $comentario2."".$comentario;
							$fieldset["comentable_type"] = "Actualizacion_interes_solicitudes";
							if($num1->id_solicitudes==''){
								$solicitud=$_POST['detalleunico'];
							}
							else
								$solicitud=$num1->id_solicitudes;
							$fieldset["comentable_id"] = $solicitud;
							$fieldset["usuario_id"] = $this->session->userdata['id_usuario'];
							$fieldset["empresa_id"] = $this->empresa_id;

							if($cambio=='si')
								$interesase = $this->bitacoraModel->create($fieldset);

                            //Subir documentos
							if (!empty($_FILES['file'])) {
//                        $vehiculo_id = $maritimoObj->id;
//                        unset($_POST["campo"]);
//                        $modeloInstancia = $this->MaritimoModel->find($vehiculo_id);
//                        $this->documentos->subir($modeloInstancia);
							}
						} else {
							$mensaje = array('tipo' => "error", 'mensaje' => '<b>¡&Eacute;rror!</b> Registro ya existe', 'titulo' => 'Intereses Asegurados ');
							$this->session->set_flashdata('mensaje', $mensaje);
						}
					} else {
						$mensaje = array('class' => 'alert-danger', 'contenido' => '<strong>¡Error!</strong>Usted no tiene permisos para editar este registro');
					}
				}
				Capsule::commit();
			} catch (ValidationException $e) {
				log_message('error', $e);
				Capsule::rollback();
			}

			if (!is_null($casco_maritimo) || !is_null($maritimoObj)) {
				$mensaje = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente', 'titulo' => 'Interés asegurado ' . $codigo . '');
			} else {
				array('tipo' => "error", 'mensaje' => '<b>¡&Eacute;rror!</b> Registro ya existe', 'titulo' => 'Intereses Asegurados ' . $campo["serie"]);
			}
		} else {
			$mensaje = array('tipo' => "error", 'mensaje' => '<b>¡&Eacute;rror!</b> Su solicitud no fue procesada', 'titulo' => 'Intereses Asegurados ' . $campo["serie"]);
		}

		$this->session->set_flashdata('mensaje', $mensaje);
		if ($campodesde['desde'] != "solicitudes") {
			redirect(base_url('intereses_asegurados/listar'));
		} else if ($campodesde['desde'] == "solicitudes") {
			print_r($uuid . "&" . $codigo);
			exit;
		}
	}

	public function articuloformularioparcial($data = array()) {
		$this->assets->agregar_js(array(
			'public/assets/js/modules/intereses_asegurados/crear_articulo.js'
			));

		if (empty($data)) {
			$data["campos"] = array();
		}

		$clause['empresa_id'] = $this->empresa_id;
		$clause['tipo_id'] = 1;
		$data['condicion'] = $this->SegCatalogoRepository->listar_catalogo('condicion_vehiculo', 'orden');
		$data['estado'] = $this->SegCatalogoRepository->listar_catalogo('estado2', 'orden');
		$this->load->view('formularioarticulo', $data);
	}

	public function guardar_articulo() {
		if ($_POST) {

			unset($_POST["campo"]["guardar"]);
			unset($_POST["campo"]["tipo_id"]);

			$campo = Util::set_fieldset("campo");
			$campo2 = Util::set_fieldset("campo2");
			$campodesde = Util::set_fieldset("campodesde");
			$campodetalle = Util::set_fieldset("campodetalle");

			if (!isset($campo['uuid'])) {
				$campo['empresa_id'] = $this->empresa_id;
			}

			$uuid = "";
			$articulo = null;
			$articuloObj = null;

			Capsule::beginTransaction();
			try {
				if (empty($campo['uuid'])) {

					$campo['uuid_articulo'] = Capsule::raw("ORDER_UUID(uuid())");
					$campo['empresa_id'] = $this->empresa_id;
					$clause['empresa_id'] = $this->empresa_id;
					$articulo = $this->ArticuloModel->create($campo);

					$comentario="<b>Interés Artículo</b><br><br>";

					if($articulo->nombre!='')
						$comentario.="<b>Campo: Nombre</b><br>Valor: ".$articulo->nombre."<br><br>";
					if($articulo->clase_equipo!='')
						$comentario.="<b>Campo: Clase de Equipo</b><br>Valor: ".$articulo->clase_equipo."<br><br>";
					if($articulo->marca!='')
						$comentario.="<b>Campo: Marca</b><br>Valor: ".$articulo->marca."<br><br>";
					if($articulo->modelo!='')
						$comentario.="<b>Campo: Modelo</b><br>Valor: ".$articulo->modelo."<br><br>";
					if($articulo->anio!='')
						$comentario.="<b>Campo: Año</b><br>Valor: ".$articulo->anio."<br><br>";
					if($articulo->numero_serie!='')
						$comentario.="<b>Campo: Número de serie</b><br>Valor: ".$articulo->numero_serie."<br><br>";
					if($articulo->id_condicion!='')
						$comentario.="<b>Campo: Condición</b><br>Valor: ".$articulo->datosCondicion->etiqueta."<br><br>";
					if($articulo->valor!='')
						$comentario.="<b>Campo: Valor</b><br>Valor: ".$articulo->valor."<br><br>";
					if($articulo->observaciones!='')
						$comentario.="<b>Campo: Observaciones</b><br>Valor: ".$articulo->observaciones."<br><br>";
					if($campo2['estado']!='')
						$comentario.="<b>Campo: Estado</b><br>Valor: ".$campo2['estado']."<br><br>";

                    //guardar tabla principal
					$codigo = Util::generar_codigo('ART', $articulo->id);
					$fieldset["empresa_id"] = $articulo->empresa_id;
					$fieldset["interesestable_type"] = 1;
					$fieldset["interesestable_id"] = $articulo->id;
					$fieldset["update_at"] = $articulo->update_at;
					$fieldset["create_at"] = $articulo->create_at;
					$fieldset["uuid_intereses"] = $articulo->uuid_articulo;
					$fieldset["numero"] = $codigo;
					$fieldset["identificacion"] = $articulo->nombre;
					$fieldset["estado"] = $campo2['estado'];
					$fieldset["creado_por"] = $this->session->userdata['id_usuario'];

					$interesase = $this->AseguradosModel->create($fieldset);


					if ($campodesde['desde'] == "solicitudes") {
						$u = ia::where('id', $interesase->id)->first()->toArray();
						$uuid = bin2hex($u['uuid_intereses']);

						$detalle = array();
						$detalle['id_intereses'] = $interesase->id;
						$detalle['detalle_relacion'] = isset($campodetalle['relacion']) ? $campodetalle['relacion'] : '';
						$detalle['detalle_prima'] = isset($campodetalle['prima_anual']) ? $campodetalle['prima_anual'] : '';
						$detalle['detalle_beneficio'] = isset($campodetalle['beneficio_vida']) ? $campodetalle['beneficio_vida'] : '';
						$detalle['detalle_monto'] = isset($campodetalle['monto']) ? $campodetalle['monto'] : '';
						$detalle['detalle_int_asociado'] = isset($campodetalle['interes_asociado']) ? $campodetalle['interes_asociado'] : '';
						$detalle['detalle_certificado'] = isset($campodetalle['certificado']) ? $campodetalle['certificado'] : '';
						$detalle['detalle_suma_asegurada'] = isset($campodetalle['suma_asegurada']) ? $campodetalle['suma_asegurada'] : '';
						$detalle['detalle_deducible'] = isset($campodetalle['deducible']) ? $campodetalle['deducible'] : '';
						$detalle['detalle_unico'] = $_POST['detalleunico'];

						$num = InteresesAsegurados_detalles::where('id_intereses', $detalle['id_intereses'])->where('detalle_unico', $detalle['detalle_unico'])->count();
						if ($num > 0) {
							$det = InteresesAsegurados_detalles::where('id_intereses', $detalle['id_intereses'])->where('detalle_unico', $detalle['detalle_unico'])->update($detalle);
							if($det->detalle_certificado!=$detalle['detalle_certificado'])
							{
								$comentario.="<b>Campo: No. Certificado</b><br>Valor Actual:".$det->detalle_certificado."<br>Valor Anterior: ".$detalle['detalle_certificado']."<br><br>";
							}
							if($det->detalle_suma_asegurada!=$detalle['detalle_suma_asegurada'])
							{
								$comentario.="<b>Campo: Suma Asegurada</b><br>Valor Actual:".$det->detalle_suma_asegurada."<br>Valor Anterior: ".$detalle['detalle_suma_asegurada']."<br><br>";
							}
							if($det->detalle_prima!=$detalle['detalle_prima'])
							{
								$comentario.="<b>Campo: Prima neta</b><br>Valor Actual:".$det->detalle_prima."<br>Valor Anterior: ".$detalle['detalle_prima']."<br><br>";
							}
							if($det->detalle_deducible!=$detalle['detalle_deducible'])
							{
								$comentario.="<b>Campo: Deducible</b><br>Valor Actual:".$det->detalle_deducible."<br>Valor Anterior: ".$detalle['detalle_deducible']."<br><br>";
							}
						} else {
							$det = InteresesAsegurados_detalles::create($detalle);
							if($det->detalle_certificado!="")
							{
								$comentario.="<b>Campo: No. Certificado</b><br>Valor: ".$det->detalle_certificado."<br><br>";
							}
							if($det->detalle_suma_asegurada!="")
							{
								$comentario.="<b>Campo: Suma Asegurada</b><br>Valor: ".$det->detalle_suma_asegurada."<br><br>";
							}
							if($det->detalle_prima!="")
							{
								$comentario.="<b>Campo: Prima neta</b><br>Valor: ".$det->detalle_prima."<br><br>";
							}
							if($det->detalle_deducible!="")
							{
								$comentario.="<b>Campo: Deducible</b><br>Valor: ".$det->detalle_deducible."<br><br>";
							}
						}
						$usuario_registro = Usuario_orm::find($this->session->userdata['id_usuario']);
						$comentario.="Registrado Por: ".$fieldset['creado_por'] =$usuario_registro->nombre." ".$usuario_registro ->apellido;

						$fieldset["comentario"] = $comentario;
						$fieldset["comentable_id"] = $detalle['detalle_unico'];
						$fieldset["usuario_id"] = $this->session->userdata['id_usuario'];
						$fieldset["empresa_id"] = $this->empresa_id;

						$interesase = $this->bitacoraModel->create($fieldset);
						
					}

					//Subir documentos
					if (!empty($_FILES['file'])) {
						$articulo_id = $interesase->id;
                        //var_dump($interesase->id);
						unset($_POST["campo"]);
						$modeloInstancia = $this->ArticuloModel->find($articulo->id);
						$this->documentos->subir($modeloInstancia);
					}
				} else {

					if ($this->auth->has_permission('acceso', 'intereses_asegurados/editar/(:any)')) {
						$intereses_asegurados = $this->interesesAseguradosRep->verInteresAsegurado(hex2bin(strtolower($campo['uuid'])));

						$codigo = $intereses_asegurados->numero;

						$uuid = $campo['uuid'];

						$articuloObj = $this->ArticuloModel->find($intereses_asegurados->articulo->id);

						$cambio='no';
						if($articuloObj->nombre!=$campo['nombre'])
						{
							$comentario.="<b>Campo: Nombre</b><br>Valor Actual: ".$campo['nombre']."<br>Valor Anterior:".$articuloObj->nombre."<br><br>";
							$cambio='si';
						}
						if($articuloObj->clase_equipo!=$campo['clase_equipo'])
						{
							$cambio='si';
							$comentario.="<b>Campo: Clase de Equipo</b><br>Valor Actual:".$campo['clase_equipo']."<br>Valor Anterior:".$articuloObj->clase_equipo."<br><br>";
						}
						if($articuloObj->marca!=$campo['marca']){
							$comentario.="<b>Campo: Marca</b><br>Valor Actual:".$campo['marca']."<br>Valor Anterior:".$articuloObj->marca."<br><br>";
							$cambio='si';
						}
						if($articuloObj->modelo!=$campo['modelo']){
							$comentario.="<b>Campo: Modelo</b><br>Valor Actual:".$campo['modelo']."<br>Valor Anterior:".$articuloObj->modelo."<br><br>";
							$cambio='si';
						}
						if($articuloObj->anio!=$campo['anio']){
							$comentario.="<b>Campo: Año</b><br>Valor Actual:".$campo['ano']."<br>Valor Anterior:".$articuloObj->ano."<br><br>";
							$cambio='si';
						}
						if($articuloObj->numero_serie!=$campo['numero_serie']){
							$comentario.="<b>Campo: Número de serie</b><br>Valor Actual:".$campo['numero_serie']."<br>Valor Anterior:".$articuloObj->numero_serie."<br><br>";
							$cambio='si';
						}
						if($articuloObj->id_condicion!=$campo['id_condicion']){
							$condicion_anterior=SegCatalogo::find($campo['id_condicion']);
							$comentario.="<b>Campo: Condición</b><br>Valor Actual:".$condicion_anterior->etiqueta."<br>Valor Anterior:".$articuloObj->datosCondicion->etiqueta."<br><br>";
							$cambio='si';
						}
						if($articuloObj->valor!=$campo['valor']){
							$comentario.="<b>Campo: Valor</b><br>Valor Actual:".$campo['valor']."<br>Valor Anterior:".$articuloObj->valor."<br><br>";
							$cambio='si';
						}
						if($articuloObj->observaciones!=$campo['observaciones']){
							$comentario.="<b>Campo: Observaciones</b><br>Valor Actual:".$campo['observaciones']."<br>Valor Anterior:".$articuloObj->observaciones."<br><br>";
							$cambio='si';
						}

						$articuloObj->update($campo);

						$intereses_asegurados->identificacion = $articuloObj->nombre;
						if($intereses_asegurados->estado!=$campo2['estado']){
							$comentario.="<b>Campo: Estado</b><br>Valor Actual:".$campo2['estado']."<br>Valor Anterior:".$intereses_asegurados->estado."<br><br>";
							$cambio='si';
						}
						$intereses_asegurados->estado = $campo2['estado'];

						$intereses_asegurados->save();


						if ($campodesde['desde'] == "solicitudes") {
							$detalle = array();
							$detalle['id_intereses'] = $intereses_asegurados->id;
							$detalle['detalle_relacion'] = isset($campodetalle['relacion']) ? $campodetalle['relacion'] : '';
							$detalle['detalle_prima'] = isset($campodetalle['prima_anual']) ? $campodetalle['prima_anual'] : '';
							$detalle['detalle_beneficio'] = isset($campodetalle['beneficio_vida']) ? $campodetalle['beneficio_vida'] : '';
							$detalle['detalle_monto'] = isset($campodetalle['monto']) ? $campodetalle['monto'] : '';
							$detalle['detalle_int_asociado'] = isset($campodetalle['interes_asociado']) ? $campodetalle['interes_asociado'] : '';
							$detalle['detalle_certificado'] = isset($campodetalle['certificado']) ? $campodetalle['certificado'] : '';
							$detalle['detalle_suma_asegurada'] = isset($campodetalle['suma_asegurada']) ? $campodetalle['suma_asegurada'] : '';
							$detalle['detalle_deducible'] = isset($campodetalle['deducible']) ? $campodetalle['deducible'] : '';
							$detalle['detalle_unico'] = $_POST['detalleunico'];

							$num = InteresesAsegurados_detalles::where('id_intereses', $detalle['id_intereses'])->where('detalle_unico', $detalle['detalle_unico'])->count();
							if ($num > 0) {
								$num1 = InteresesAsegurados_detalles::where('id_intereses', $detalle['id_intereses'])->where('detalle_unico', $detalle['detalle_unico'])->first();

								$det = InteresesAsegurados_detalles::where('id_intereses', $detalle['id_intereses'])->where('detalle_unico', $detalle['detalle_unico'])->update($detalle);

								if($num1->detalle_certificado!=$detalle['detalle_certificado'])
								{
									$comentario.="<b>Campo: No. Certificado</b><br>Valor Actual:".$detalle['detalle_certificado']."<br>Valor Anterior: ".$num1->detalle_certificado."<br><br>";
									$cambio='si';
								}
								if($num1->detalle_suma_asegurada!=$detalle['detalle_suma_asegurada'])
								{
									$comentario.="<b>Campo: Suma Asegurada</b><br>Valor Actual:".$detalle['detalle_suma_asegurada']."<br>Valor Anterior: ".$num1->detalle_suma_asegurada."<br><br>";
									$cambio='si';
								}
								if($num1->detalle_prima!=$detalle['detalle_prima'])
								{
									$comentario.="<b>Campo: Prima neta</b><br>Valor Actual:".$detalle['detalle_prima']."<br>Valor Anterior: ".$num1->detalle_prima."<br><br>";
									$cambio='si';
								}
								if($num1->detalle_deducible!=$detalle['detalle_deducible'])
								{
									$comentario.="<b>Campo: Deducible</b><br>Valor Actual:".$detalle['detalle_deducible']."<br>Valor Anterior: ".$num1->detalle_deducible."<br><br>";
									$cambio='si';
								}

							} else {
								$det = InteresesAsegurados_detalles::create($detalle);
								if($det->detalle_certificado!="")
								{
									$comentario.="<b>Campo: No. Certificado</b><br>Valor Actual:".$det->detalle_certificado."<br><br>";
									$cambio='si';
								}
								if($det->detalle_suma_asegurada!="")
								{
									$comentario.="<b>Campo: Suma Asegurada</b><br>Valor Actual:".$det->detalle_suma_asegurada."<br><br>";
									$cambio='si';
								}
								if($det->detalle_prima!="")
								{
									$comentario.="<b>Campo: Prima neta</b><br>Valor Actual:".$det->detalle_prima."<br><br>";
									$cambio='si';
								}
								if($det->detalle_deducible!="")
								{
									$comentario.="<b>Campo: Deducible</b><br>Valor Actual:".$det->detalle_deducible."<br><br>";
									$cambio='si';
								}
							}

							$usuario_registro = Usuario_orm::find($this->session->userdata['id_usuario']);

							$comentario.="Registrado Por: ".$fieldset['creado_por'] =$usuario_registro->nombre." ".$$usuario_registro ->apellido;

							$comentario2="<b>Interés Artículo</b> <br>Nombre: ".$articuloObj->nombre."<br><br>";
							$fieldset["comentario"] = $comentario2."".$comentario;
							$fieldset["comentable_type"] = "Actualizacion_interes_solicitudes";
							if($num1->id_solicitudes==''){
								$solicitud=$_POST['detalleunico'];
							}
							else
								$solicitud=$num1->id_solicitudes;
							$fieldset["comentable_id"] = $solicitud;
							$fieldset["usuario_id"] = $this->session->userdata['id_usuario'];
							$fieldset["empresa_id"] = $this->empresa_id;

							if($cambio=='si')
								$interesase = $this->bitacoraModel->create($fieldset);
						}

						$mensaje = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente');
					} else {
						$mensaje = array('class' => 'alert-danger', 'contenido' => '<strong>¡Error!</strong>Usted no tiene permisos para editar este registro');
					}
				}

				Capsule::commit();
			} catch (ValidationException $e) {
				log_message('error', $e);
				Capsule::rollback();
			}

			if (!empty($articulo) || !empty($articuloObj)) {
				$mensaje = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente', 'titulo' => 'Interés asegurado ' . $codigo . '');
			} else {
				$mensaje = array('class' => 'alert-danger', 'contenido' => '<strong>¡Error!</strong> Su solicitud no fue procesada');
			}
		} else {
			$mensaje = array('class' => 'alert-warning', 'contenido' => '<strong>¡Error!</strong> Su solicitud no fue procesada');
		}

		$this->session->set_flashdata('mensaje', $mensaje);
		if ($campodesde['desde'] != "solicitudes") {
			redirect(base_url('intereses_asegurados/listar'));
		} else if ($campodesde['desde'] == "solicitudes") {
			print_r($uuid . "&" . $codigo);
			exit;
		}
	}

	public function ubicacionformularioparcial($data = array()) {
		$this->assets->agregar_js(array(
			'public/assets/js/modules/intereses_asegurados/crear_ubicacion.js'
			));
		if (empty($data)) {
			$data["campos"] = array();
		}
		$clause['tipo'] = 1;
        //persona
		$clause['empresa_id'] = $this->empresa_id;
		$data['acreedores'] = $this->AcreedoresRep->get($clause);
		$data['estado'] = $this->SegCatalogoRepository->listar_catalogo('estado2', 'orden');
		$this->load->view('formularioubicacion', $data);
	}

	function guardar_ubicacion() {

		if ($_POST) {
			unset($_POST["campo"]["guardar"]);
			$campo = Util::set_fieldset("campo");
			$campo2 = Util::set_fieldset("campo2");
			$campodesde = Util::set_fieldset("campodesde");
			$campodetalle = Util::set_fieldset("campodetalle");
			if (!isset($campo['uuid'])) {
				$campo['empresa_id'] = $this->empresa_id;
			}
			$uuid = "";
			$ubicacion = null;
			$ubicacionObj = null;
			Capsule::beginTransaction();
			try {
				if (empty($campo['uuid'])) {
					$duplicado_u = $campo['direccion'];
					$verificar_ubicacion = count($this->interesesAseguradosRep->consultaUbicacion($duplicado_u));
					if ($verificar_ubicacion == 0) {
						$clause['empresa_id'] = $this->empresa_id;
						$campo["uuid_ubicacion"] = Capsule::raw("ORDER_UUID(uuid())");
						$campo['acreedor'] = isset($campo['acreedor']) ? $campo['acreedor'] : '0';
						$total = $this->interesesAseguradosRep->listar_intereses_asegurados($clause);
						$codigo = Util::generar_codigo('UBI', count($total) + 1);
						$campo["numero"] = $codigo;
						$ubicacion = $this->UbicacionModel->create($campo);
                        //guardar tabla principal
						$fieldset['uuid_intereses'] = $ubicacion->uuid_ubicacion;
						$fieldset['empresa_id'] = $ubicacion->empresa_id;
						$fieldset['interesestable_type'] = 7;
						$fieldset['interesestable_id'] = $ubicacion->id;
						$fieldset['numero'] = $codigo;
						$fieldset['identificacion'] = $ubicacion->direccion;
						$fieldset['estado'] = $campo2['estado'];

						$ca = $ubicacion->interesesAsegurados()->create($fieldset);

						if ($campodesde['desde'] == "solicitudes") {
							$u = ia::where('id', $ca->id)->first()->toArray();
							$uuid = bin2hex($u['uuid_intereses']);
							$uuid = $uuid . "&" . $ubicacion->direccion;

							$detalle = array();
							$detalle['id_intereses'] = $ca->id;
							$detalle['detalle_relacion'] = isset($campodetalle['relacion']) ? $campodetalle['relacion'] : '';
							$detalle['detalle_prima'] = isset($campodetalle['prima_anual']) ? $campodetalle['prima_anual'] : '';
							$detalle['detalle_beneficio'] = isset($campodetalle['beneficio_vida']) ? $campodetalle['beneficio_vida'] : '';
							$detalle['detalle_monto'] = isset($campodetalle['monto']) ? $campodetalle['monto'] : '';
							$detalle['detalle_int_asociado'] = isset($campodetalle['interes_asociado']) ? $campodetalle['interes_asociado'] : '';
							$detalle['detalle_certificado'] = isset($campodetalle['certificado']) ? $campodetalle['certificado'] : '';
							$detalle['detalle_suma_asegurada'] = isset($campodetalle['suma_asegurada']) ? $campodetalle['suma_asegurada'] : '';
							$detalle['detalle_deducible'] = isset($campodetalle['deducible']) ? $campodetalle['deducible'] : '';
							$detalle['detalle_unico'] = $_POST['detalleunico'];

							$num = InteresesAsegurados_detalles::where('id_intereses', $detalle['id_intereses'])->where('detalle_unico', $detalle['detalle_unico'])->count();
							if ($num > 0) {
								$det = InteresesAsegurados_detalles::where('id_intereses', $detalle['id_intereses'])->where('detalle_unico', $detalle['detalle_unico'])->update($detalle);
							} else {
								$det = InteresesAsegurados_detalles::create($detalle);
							}
						}
                        //Subir documentos
						if (!empty($_FILES['file'])) {
							$ubicacion_id = $ubicacion->id;
							unset($_POST["campo"]);
							$modeloInstancia = $this->UbicacionModel->find($ubicacion->id);
							$this->documentos->subir($modeloInstancia);
						}
//                }else{
//                    $mensaje = array('estado' => 500, 'mensaje' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
//                    $this->session->set_flashdata('mensaje', $mensaje);
//                    redirect(base_url('intereses_asegurados/listar'));
//                }
					} else {
						$ubicacion = "";
						$mensaje = array('tipo' => "error", 'mensaje' => '<b>¡&Eacute;rror!</b> Registro ya existe', 'titulo' => 'Intereses Asegurados ' . $campo["direccion"]);
						$this->session->set_flashdata('mensaje', $mensaje);
						redirect(base_url('intereses_asegurados/listar'));
					}
				} else {
					if ($this->auth->has_permission('acceso', 'intereses_asegurados/editar/(:any)')) {
						$duplicado_u2 = $campo2['direccionr'];
						$duplicado_u = $campo['direccion'];
						$uuid = $campo['uuid'];
						$uuid = $uuid . "&" . $campo2['direccionr'];
						if ($duplicado_u != $duplicado_u2) {
							$verificar_ubicacion = count($this->interesesAseguradosRep->consultaUbicacion($duplicado_u));
						} else {
							$verificar_ubicacion = 0;
						}
						if ($verificar_ubicacion == 0) {
							$intereses_asegurados = $this->interesesAseguradosRep->verInteresAsegurado(hex2bin(strtolower($campo['uuid'])));

							$codigo = $intereses_asegurados->numero;
							$ubicacionObj = $this->UbicacionModel->find($intereses_asegurados->ubicacion->id);
							$campo['acreedor'] = isset($campo['acreedor']) ? $campo['acreedor'] : '0';
							$ubicacionObj->update($campo);

							$intereses_asegurados->identificacion = $ubicacionObj->direccion;
							$intereses_asegurados->estado = $campo2['estado'];
							$intereses_asegurados->save();


							if ($campodesde['desde'] == "solicitudes") {
								$detalle = array();
								$detalle['id_intereses'] = $intereses_asegurados->id;
								$detalle['detalle_relacion'] = isset($campodetalle['relacion']) ? $campodetalle['relacion'] : '';
								$detalle['detalle_prima'] = isset($campodetalle['prima_anual']) ? $campodetalle['prima_anual'] : '';
								$detalle['detalle_beneficio'] = isset($campodetalle['beneficio_vida']) ? $campodetalle['beneficio_vida'] : '';
								$detalle['detalle_monto'] = isset($campodetalle['monto']) ? $campodetalle['monto'] : '';
								$detalle['detalle_int_asociado'] = isset($campodetalle['interes_asociado']) ? $campodetalle['interes_asociado'] : '';
								$detalle['detalle_certificado'] = isset($campodetalle['certificado']) ? $campodetalle['certificado'] : '';
								$detalle['detalle_suma_asegurada'] = isset($campodetalle['suma_asegurada']) ? $campodetalle['suma_asegurada'] : '';
								$detalle['detalle_deducible'] = isset($campodetalle['deducible']) ? $campodetalle['deducible'] : '';
								$detalle['detalle_unico'] = $_POST['detalleunico'];

								$num = InteresesAsegurados_detalles::where('id_intereses', $detalle['id_intereses'])->where('detalle_unico', $detalle['detalle_unico'])->count();
								if ($num > 0) {
									$det = InteresesAsegurados_detalles::where('id_intereses', $detalle['id_intereses'])->where('detalle_unico', $detalle['detalle_unico'])->update($detalle);
								} else {
									$det = InteresesAsegurados_detalles::create($detalle);
								}
							}

                            //Subir documentos
//                    if (!empty($_FILES['file'])) {
//                        $vehiculo_id = $ubicacionObj->id;
//                        unset($_POST["campo"]);
//                        $modeloInstancia = $this->UbicacionModel->find($vehiculo_id);
//                        $this->documentos->subir($modeloInstancia);
//                    }
						} else {
							$mensaje = array('tipo' => "error", 'mensaje' => '<b>¡&Eacute;rror!</b> Registro ya existe', 'titulo' => 'Intereses Asegurados ');
							$this->session->set_flashdata('mensaje', $mensaje);
						}
					} else {
						$mensaje = array('class' => 'alert-danger', 'contenido' => '<strong>¡Error!</strong>Usted no tiene permisos para editar este registro');
					}
				}
				Capsule::commit();
			} catch (ValidationException $e) {
				log_message('error', $e);
				Capsule::rollback();
			}

			if (!is_null($ubicacion) || !is_null($ubicacionObj)) {
				$mensaje = array('tipo' => 'success', 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente', 'titulo' => 'Interés asegurado ' . $codigo . '');
			} else {
				$mensaje = array('tipo' => "error", 'mensaje' => '<b>¡&Eacute;rror!</b> Registro ya existe', 'titulo' => 'Intereses Asegurados ' . $campo["nombre"]);
			}
		} else {
			$mensaje = array('tipo' => "error", 'mensaje' => '<b>¡&Eacute;rror!</b> Su solicitud no fue procesada', 'titulo' => 'Intereses Asegurados ' . $campo["nombre"]);
		}

		$this->session->set_flashdata('mensaje', $mensaje);
		if ($campodesde['desde'] != "solicitudes") {
			redirect(base_url('intereses_asegurados/listar'));
		} else if ($campodesde['desde'] == "solicitudes") {
			print_r($uuid . "&" . $codigo);
			exit;
		}
	}

	private function _js() {
		$this->assets->agregar_js(array(
			'public/assets/js/default/jquery-ui.min.js',
			'public/assets/js/plugins/jquery/jquery.sticky.js',
			'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
			'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
			'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
			'public/assets/js/moment-with-locales-290.js',
			'public/assets/js/plugins/jquery/jqgrid/plugins/jQuery.jqGrid.columnToggle.js',
			'public/assets/js/plugins/jquery/switchery.min.js',
			'public/assets/js/plugins/bootstrap/bootstrap-tagsinput.js',
			'public/assets/js/plugins/bootstrap/daterangepicker.js',
			'public/assets/js/plugins/jquery/fileinput/fileinput.js',
			'public/assets/js/plugins/jquery/fileinput/fileinput_locale_es.js',
			'public/assets/js/default/grid.js',
			'public/assets/js/plugins/jquery/fileupload/jquery.fileupload.js',
			'public/assets/js/default/subir_documento_modulo.js',
			'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
			'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
			'public/assets/js/plugins/jquery/chosen.jquery.min.js',
			'public/assets/js/plugins/bootstrap/bootstrap-tabdrop.js',
			'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
			'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
			'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
			'public/assets/js/default/formulario.js',
			'public/assets/js/modules/intereses_asegurados/routes.js'
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
			'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
			'public/assets/css/plugins/jquery/fileinput/fileinput.css',
			'public/assets/css/plugins/jquery/jquery.fileupload.css'
			));
	}

	public function exportar() {
		if (empty($_POST)) {
			exit();
		}
		$ids = $this->input->post('ids', true);
		$id = explode(",", $ids);

		if (empty($id)) {
			return false;
		}
		$csv = array();

		$clause['id'] = $id;

		$contactos = $this->interesesAseguradosRep->listar_intereses_asegurados($clause, NULL, NULL, NULL, NULL);
		if (empty($contactos)) {
			return false;
		}
		$i = 0;
		foreach ($contactos AS $row) {
			$csvdata[$i]['numero'] = $row->numero;
			$csvdata[$i]["interesestable_type"] = utf8_decode(Util::verificar_valor($row->tipo->etiqueta));
			$csvdata[$i]["identificacion"] = utf8_decode(Util::verificar_valor($row->identificacion));
			$csvdata[$i]["estado"] = utf8_decode(Util::verificar_valor($row->estado));
			$i++;
		}
        //we create the CSV into memory
		$csv = Writer::createFromFileObject(new SplTempFileObject());
		$headers = [
		'No.  Interés asegurado',
		'Tipo de interés',
		'Identicación',
		'Estado',
		];
		$decodingHeaders = array_map("utf8_decode", $headers);
		$csv->insertOne($decodingHeaders);
		$csv->insertAll($csvdata);
		$csv->output("InteresesAsegurados-" . date('y-m-d') . ".csv");
		exit();
	}

	public function ajax_cambiar_estado_intereses() {

		$FormRequest = new Flexio\Modulo\InteresesAsegurados\Models\GuardarInteresesAseguradosEstados;

		try {
			$msg = $Agentes = $FormRequest->guardar();
		} catch (\Exception $e) {
			$msg = log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
		}

		print json_encode($msg);
		exit;
	}

	function ajax_eliminar_interes() {
		$id = $this->input->post('id');
		if ($this->auth->has_permission('acceso', 'intereses_asegurados/eliminar/(:any)') == true) {
			$this->AseguradosModel->where('interesestable_id', $id)->first()->update(array('deleted' => 1));
			$resources = "Si";
		} else {
			$resources = "No";
		}

		echo $resources;
		exit;
	}

	function documentos_campos() {

		return array(
			array(
				"type" => "hidden",
				"name" => "cliente_id",
				"id" => "cliente_id",
				"class" => "form-control",
				"readonly" => "readonly",
				));
	}

	public function exportarDocumentos() {
		if (empty($_POST)) {
			exit();
		}
		$ids = $this->input->post('ids', true);
		$id = explode(",", $ids);

		if (empty($id)) {
			return false;
		}
		$csv = array();

		$clause['id'] = $id;

		$documentos = $this->DocumentosRepository->exportar($clause, NULL, NULL, NULL, NULL);
		if (empty($documentos)) {
			return false;
		}
		$i = 0;
		foreach ($documentos AS $row) {

			$usuario = Usuario_orm::find($row->subido_por);

			$csvdata[$i]['nombre'] = $row->archivo_nombre;

			if (!empty($row->archivo_nombre)) {
				$info1 = new SplFileInfo($row->archivo_nombre);
				$info = $info1->getExtension();

				if ($info == "png" || $info == "jpg" || $info == "gif" || $info == "jpeg" || $info == "bmp" || $info == "ai" || $info == "crd" || $info == "dwg" || $info == "svg") {
					$tipo = "Imagen";
				} else if ($info == "doc" || $info == "docx" || $info == "dot" || $info == "rtf") {
					$tipo = "Documento";
				} else if ($info == "xls" || $info == "xlsx") {
					$tipo = "Datos";
				} else if ($info == "ppt" || $info == "pps" || $info == "pptx" || $info == "ppsx") {
					$tipo = "Presentación";
				} else if ($info == "pdf") {
					$tipo = "PDF";
				} else
				$tipo = "";
			}
			else {
				$tipo = "";
			}
			$csvdata[$i]["tipo"] = $tipo;
			$csvdata[$i]["fecha_creacion"] = $row->created_at;
			$csvdata[$i]["subido_por"] = $usuario->nombre . " " . $usuario->apellido;
			$i++;
		}
        //we create the CSV into memory
		$csv = Writer::createFromFileObject(new SplTempFileObject());
		$csv->insertOne([
			'Nombre',
			'Tipo',
			utf8_decode('Fecha Creación'),
			'Usuario'
			]);
		$csv->insertAll($csvdata);
		$csv->output("documentos-" . date('ymd') . ".csv");
		exit();
	}

	public function personaformularioparcial($data = array()) {
		$this->assets->agregar_js(array(
			'public/assets/js/modules/intereses_asegurados/crear.js',
			));
		if (empty($data)) {
			$data["campos"] = array();
		}
        //persona
		$data['tipo_identificacion'] = $this->SegInteresesAseguradosRepository->listar_catalogo('Documento_Identificacion', 'orden');
		$data['info'] = array('letras' => $this->SegInteresesAseguradosRepository->listar_catalogo('Letra', 'orden'),
			'provincias' => $this->SegInteresesAseguradosRepository->listar_catalogo('Provincias', 'orden'));
		$data['estado_civil'] = $this->SegInteresesAseguradosRepository->listar_catalogo('Estado Civil', 'orden');
		$data['sexo'] = $this->SegInteresesAseguradosRepository->listar_catalogo('Sexo', 'orden');
		$clause['empresa_id'] = $this->empresa_id;
		$clause['tipo_id'] = 1;
		$data['acreedores'] = $this->AcreedoresRep->get($clause);
		$data['estado'] = $this->SegCatalogoRepository->listar_catalogo('estado2', 'orden');

		$this->load->view('formulariopersona', $data);
	}

	function ajax_check_persona() {

		$identificacion = $this->input->post("identificacion");
		$identificacion_obj = $this->interesesAseguradosRep->identificacion_persona($identificacion);
		if (empty($identificacion_obj)) {
			echo('USER_AVAILABLE');
		} else {
			echo('USER_EXISTS');
		}
	}

	public function guardar() {

		if ($_POST) {
			unset($_POST["campo"]["guardar"]);
			$campo = Util::set_fieldset("campo");
			$campodesde = Util::set_fieldset("campodesde");
			$campodetalle = Util::set_fieldset("campodetalle");
			$individual=0;
			$interesId ="";
			$validarEditar =$campo['validar_editar'];
			if (!empty($campo['pasaporte']) || $campo['letra'] == 'PAS') {
				$cedula = $campo['pasaporte'];
				$campo['ruc'] = $cedula;
			}if ($campo['identificacion'] == 'cedula') {
				$provincia = $campo['provincia'] =='' ? "" : $campo['provincia'].'-';
				$cedula = $provincia . $campo['letra'] . "-" . $campo['tomo'] . "-" . $campo['asiento'];
				$campo['ruc'] = $cedula;
			}
			if (!isset($campo['uuid'])) {
				$campo['empresa_id'] = $this->empresa_id;
				$campo['fecha_creacion'] = date('Y-m-d H:i:s');
			}
			if(isset($campodetalle['personaInvidual'])){
				if($campodetalle['personaInvidual']==1){
					$individual= InteresesAsegurados_detalles::where('detalle_relacion', 
						'Principal')->where('detalle_unico', $_POST['detalleunico'])->select('id_intereses')->count();
				}
				if($campodetalle['personaInvidual']==2){
					$individual="colectivo";
				}

			}
			if(isset($campodetalle['interes_asociado'])){ 
				$interesId=$this->AseguradosModel
				->where('interesestable_id',$campodetalle['interes_asociado']) 
				->where('interesestable_type',5)->first(); 
			}
			Capsule::beginTransaction();
			try {
				if (empty($campo['uuid'])) {
					$clause['empresa_id'] = $this->empresa_id;
					$total = $this->interesesAseguradosRep->listar_persona($clause);
					$codigo = Util::generar_codigo('PER', count($total) + 1);
					$campo["numero"] = $codigo;
					$campo['identificacion'] = $campo['ruc'];

					$intereses_asegurados = $this->PersonasModel->create($campo);
					
					$comentario="<b>Interés Persona</b><br><br>";
					
					if($intereses_asegurados->nombrePersona!='')
						$comentario.="<b>Campo: Nombre Completo </b><br>Valor: ".$intereses_asegurados->nombrePersona."<br><br>";
					if($intereses_asegurados->identificacion!='')
						$comentario.="<b>Campo: Identificacion</b><br>Valor: ".$intereses_asegurados->identificacion."<br><br>";
					if($intereses_asegurados->fecha_nacimiento!='')
						$comentario.="<b>Campo: Fecha de Nacimiento</b><br>Valor: ".$intereses_asegurados->fecha_nacimiento."<br><br>";
					if($intereses_asegurados->estado_civil!='')
						$comentario.="<b>Campo: Estado Civil</b><br>Valor: ".$intereses_asegurados->estado_catalogo->etiqueta."<br><br>";
					if($intereses_asegurados->nacionalidad!='')
						$comentario.="<b>Campo: Nacionalidad</b><br>Valor: ".$intereses_asegurados->nacionalidad."<br><br>";
					if($intereses_asegurados->sexo!='')
						if($intereses_asegurados->sexo==1)
							$sex='Femenino';
						else
							$sex='Masculino';
						$comentario.="<b>Campo: Sexo</b><br>Valor: ".$sex."<br><br>";
					if($intereses_asegurados->estatura!='')
						$comentario.="<b>Campo: Estatura</b><br>Valor: ".$intereses_asegurados->estatura."<br><br>";
					if($intereses_asegurados->peso!='')
						$comentario.="<b>Campo: Peso</b><br>Valor: ".$intereses_asegurados->peso."<br><br>";
					if($intereses_asegurados->telefono_residencial!='')
						$comentario.="<b>Campo: Teléfono Residencial</b><br>Valor: ".$intereses_asegurados->telefono_residencial."<br><br>";
					if($intereses_asegurados->telefono_oficina!='')
						$comentario.="<b>Campo: Teléfono Oficina</b><br>Valor: ".$intereses_asegurados->telefono_oficina."<br><br>";
					if($intereses_asegurados->dirreccion_residencial!='')
						$comentario.="<b>Campo: Dirección Residencial</b><br>Valor: ".$intereses_asegurados->dirreccion_residencial."<br><br>";
					if($intereses_asegurados->direccion_laboral!='')
						$comentario.="<b>Campo: Direccion Laboral</b><br>Valor: ".$intereses_asegurados->direccion_laboral."<br><br>";
					if($intereses_asegurados->observaciones!='')
						$comentario.="<b>Campo: Observaciones</b><br>Valor: ".$intereses_asegurados->observaciones."<br><br>";
					if($_POST["campo"]["estado"]!='')
						$comentario.="<b>Campo: Estado</b><br>Valor: ".$_POST["campo"]["estado"]."<br><br>";
					
                    //guardar tabla principal
					$fieldset['uuid_intereses'] = Capsule::raw("ORDER_UUID(uuid())");
					$fieldset['empresa_id'] = $this->empresa_id;
					$fieldset['interesestable_type'] = 5;
					$fieldset['interesestable_id'] = $intereses_asegurados->id;
					$fieldset['numero'] = $codigo;
					$fieldset['identificacion'] = $intereses_asegurados->identificacion;
					$fieldset['estado'] = $_POST["campo"]["estado"]=="" ? 'Activo':$_POST["campo"]["estado"];
					$ca = $intereses_asegurados->interesesAsegurados()->create($fieldset);
					if($campodesde['desde']=="solicitudes"){
						$u = ia::where('id',$ca->id)->first()->toArray();
						$uuid = bin2hex($u['uuid_intereses']);

						$detalle = array();
						$detalle['id_intereses'] = $ca->id;
						$detalle['detalle_relacion'] = isset($campodetalle['relacion']) ? $campodetalle['relacion'] : $campodetalle['relacion_benficario'];
						$detalle['tipo_relacion'] =isset($campodetalle['tipo_relacion']) ? $campodetalle['tipo_relacion'] : "";
						$detalle['detalle_prima'] = isset($campodetalle['prima_anual']) ? $campodetalle['prima_anual'] : '';
						$detalle['detalle_beneficio'] = isset($campodetalle['beneficio_vida']) ? $campodetalle['beneficio_vida'] : '';
						$detalle['detalle_monto'] = isset($campodetalle['monto']) ? $campodetalle['monto'] : '';
						$detalle['detalle_int_asociado'] = isset($interesId->id) ?$interesId->id: '';
						$detalle['detalle_certificado'] = isset($campodetalle['certificado']) ? $campodetalle['certificado'] : '';
						$detalle['detalle_suma_asegurada'] = isset($campodetalle['suma_asegurada']) ? $campodetalle['suma_asegurada'] : '';
						$detalle['detalle_deducible'] = isset($campodetalle['deducible']) ? $campodetalle['deducible'] : '';
						$detalle['detalle_participacion'] = isset($campodetalle['participacion']) ? $campodetalle['participacion'] : '';
						$detalle['detalle_unico'] = $_POST['detalleunico'];
						$num = InteresesAsegurados_detalles::where('id_intereses', $detalle['id_intereses'])->where('detalle_unico', $detalle['detalle_unico'])->count();
						if ($num>0 && $validarEditar ==2) {
							$det = InteresesAsegurados_detalles::where('id_intereses', $detalle['id_intereses'])->where('detalle_unico', $detalle['detalle_unico'])->update($detalle);
							
							if($individual==1 || $individual=='colectivo'){
								unset($detalle['detalle_int_asociado']);
								unset($detalle['detalle_relacion']);    
							}
							
							if($det->detalle_relacion!=$detalle['detalle_relacion'])
							{
								$comentario.="<b>Campo: Relación</b><br>Valor Actual:".$det->detalle_relacion."<br>Valor Anterior: ".$detalle['detalle_relacion']."<br><br>";
							}
							if($det->detalle_int_asociado!=$detalle['detalle_int_asociado'])
							{
								//$interes=$this->AseguradosModel->find();
								$comentario.="<b>Campo: Interes asegurado asociado</b><br>Valor Actual:".$det->detalle_int_asociado."<br>Valor Anterior: ".$detalle['detalle_int_asociado']."<br><br>";
							}
							if($det->detalle_participacion!=$detalle['detalle_participacion'])
							{
								$comentario.="<b>Campo: Participación</b><br>Valor Actual:".$det->detalle_participacion."<br>Valor Anterior: ".$detalle['detalle_participacion']."<br><br>";
							}
							if($det->detalle_certificado!=$detalle['detalle_certificado'])
							{
								$comentario.="<b>Campo: No. Certificado</b><br>Valor Actual:".$det->detalle_certificado."<br>Valor Anterior: ".$detalle['detalle_certificado']."<br><br>";
							}
							if($det->detalle_suma_asegurada!=$detalle['detalle_suma_asegurada'])
							{
								$comentario.="<b>Campo: Suma Asegurada</b><br>Valor Actual:".$det->detalle_suma_asegurada."<br>Valor Anterior: ".$detalle['detalle_suma_asegurada']."<br><br>";
							}
							if($det->detalle_prima!=$detalle['detalle_prima'])
							{
								$comentario.="<b>Campo: Prima neta</b><br>Valor Actual:".$det->detalle_prima."<br>Valor Anterior: ".$detalle['detalle_prima']."<br><br>";
							}
							if($det->detalle_deducible!=$detalle['detalle_deducible'])
							{
								$comentario.="<b>Campo: Deducible</b><br>Valor Actual:".$det->detalle_deducible."<br>Valor Anterior: ".$detalle['detalle_deducible']."<br><br>";
							}
						
						}else if(($individual==0 || $detalle['detalle_relacion']=='Dependiente' ||$individual=='colectivo')&& $num<=0) {

							$det = InteresesAsegurados_detalles::create($detalle);
							if($det->detalle_relacion!=$detalle['detalle_relacion'])
							{
								$comentario.="<b>Campo: Relación</b><br>Valor: ".$det->detalle_relacion."<br><br>";
							}
							if($det->detalle_int_asociado!=$detalle['detalle_int_asociado'])
							{
								//$interes=$this->AseguradosModel->find();
								$comentario.="<b>Campo: Interes asegurado asociado</b><br>Valor: ".$det->detalle_int_asociado."<br><br>";
							}
							if($det->detalle_participacion!=$detalle['detalle_participacion'])
							{
								$comentario.="<b>Campo: Participación</b><br>Valor: ".$det->detalle_participacion."<br><br>";
							}
							if($det->detalle_certificado!="")
							{
								$comentario.="<b>Campo: No. Certificado</b><br>Valor: ".$det->detalle_certificado."<br><br>";
							}
							if($det->detalle_suma_asegurada!="")
							{
								$comentario.="<b>Campo: Suma Asegurada</b><br>Valor: ".$det->detalle_suma_asegurada."<br><br>";
							}
							if($det->detalle_prima!="")
							{
								$comentario.="<b>Campo: Prima neta</b><br>Valor: ".$det->detalle_prima."<br><br>";
							}
							if($det->detalle_deducible!="")
							{
								$comentario.="<b>Campo: Deducible</b><br>Valor: ".$det->detalle_deducible."<br><br>";
							}
						}
						$usuario_registro = Usuario_orm::find($this->session->userdata['id_usuario']);
						$comentario.="Registrado Por: ".$fieldset['creado_por'] =$usuario_registro->nombre." ".$usuario_registro ->apellido;

						$fieldset["comentario"] = $comentario;
						$fieldset["comentable_id"] = $detalle['detalle_unico'];
						$fieldset["usuario_id"] = $this->session->userdata['id_usuario'];
						$fieldset["empresa_id"] = $this->empresa_id;

						$interesase = $this->bitacoraModel->create($fieldset);
						
					}
					
					
                    //Subir documentos
					if (!empty($_FILES['file'])) {
						$vehiculo_id = $intereses_asegurados->id;
						unset($_POST["campo"]);
						$modeloInstancia = $this->PersonasModel->find($vehiculo_id);
						$this->documentos->subir($modeloInstancia);
					}
				} else {
					if ($this->auth->has_permission('acceso', 'intereses_asegurados/editar/(:any)')) {
						$personaObj = $this->PersonasModel->find($campo['idPersona']);
						$uuid=$campo['uuid'];
						unset($campo['uuid']);
						unset($campo['ruc']);
						unset($campo['provincia']);
						unset($campo['letra']);
						unset($campo['tomo']);
						unset($campo['asiento']);
						$campo['identificacion'] = $cedula;
						
						$cambio='no';
						if($personaObj->nombrePersona!=$campo['nombrePersona'])
						{
							$comentario.="<b>Campo: Nombre Completo</b><br>Valor Actual: ".$campo['nombrePersona']."<br>Valor Anterior:".$personaObj->nombrePersona."<br><br>";
							$cambio='si';
						}
						if($personaObj->identificacion!=$campo['identificacion']){
							$comentario.="<b>Campo: Identificación</b><br>Valor Actual:".$campo['identificacion']."<br>Valor Anterior:".$personaObj->identificacion."<br><br>";
							$cambio='si';
						}
						if($personaObj->fecha_nacimiento!=$campo['fecha_nacimiento']){
							$comentario.="<b>Campo: Fecha de Nacimiento</b><br>Valor Actual:".$campo['fecha_nacimiento']."<br>Valor Anterior:".$personaObj->fecha_nacimiento."<br><br>";
							$cambio='si';
						}
						if($personaObj->estado_civil!=$campo['estado_civil']){
							$estado_nuevo=Catalogos::find($campo['estado_civil'])->etiqueta;
							$comentario.="<b>Campo: Estado Civil</b><br>Valor Actual:".$estado_nuevo."<br>Valor Anterior:".$personaObj->estado_catalogo->etiqueta."<br><br>";
							$cambio='si';
						}
						if($personaObj->nacionalidad!=$campo['nacionalidad']){
							$comentario.="<b>Campo: Nacionalidad</b><br>Valor Actual:".$campo['nacionalidad']."<br>Valor Anterior:".$personaObj->nacionalidad."<br><br>";
							$cambio='si';
						}
						if($personaObj->sexo!=$campo['sexo']){
							if($intereses_asegurados->sexo==1)
								$sex_ant='Femenino';
							else
								$sex_ant='Masculino';
							
							if($campo['sexo']==1)
								$sex_nue='Femenino';
							else
								$sex_nue='Masculino';
							
							$comentario.="<b>Campo: Sexo</b><br>Valor Actual:".$sex_nue."<br>Valor Anterior:".$sex_ant."<br><br>";
							$cambio='si';
						}
						if($personaObj->estatura!=$campo['estatura']){
							$comentario.="<b>Campo: Estatura</b><br>Valor Actual:".$campo['estatura']."<br>Valor Anterior:".$personaObj->estatura."<br><br>";
							$cambio='si';
						}
						if($personaObj->peso!=$campo['peso']){
							$comentario.="<b>Campo: Peso</b><br>Valor Actual:".$campo['peso']."<br>Valor Anterior:".$personaObj->peso."<br><br>";
							$cambio='si';
						}
						if($personaObj->telefono_residencial!=$campo['telefono_residencialtelefono_residencial']){
							$comentario.="<b>Campo: Teléfono Residencial</b><br>Valor Actual:".$campo['telefono_residencial']."<br>Valor Anterior:".$personaObj->telefono_residencial."<br><br>";
							$cambio='si';
						}
						if($personaObj->telefono_oficina!=$campo['telefono_oficina']){
							$comentario.="<b>Campo: Teléfono Oficina</b><br>Valor Actual:".$campo['telefono_oficina']."<br>Valor Anterior:".$personaObj->telefono_oficina."<br><br>";
							$cambio='si';
						}
						if($personaObj->direccion_residencial!=$campo['direccion_residencial']){
							$comentario.="<b>Campo: Dirección Residencial</b><br>Valor Actual:".$campo['direccion_residencial']."<br>Valor Anterior:".$personaObj->direccion_residencial."<br><br>";
							$cambio='si';
						}
						if($personaObj->direccion_laboral!=$campo['direccion_laboral']){
							$comentario.="<b>Campo: Dirección Laboral</b><br>Valor Actual:".$campo['direccion_laboral']."<br>Valor Anterior:".$personaObj->direccion_laboral."<br><br>";
							$cambio='si';
						}
						if($personaObj->observaciones!=$campo['observaciones']){
							$comentario.="<b>Campo: Observaciones</b><br>Valor Actual:".$campo['observaciones']."<br>Valor Anterior:".$personaObj->observaciones."<br><br>";
							$cambio='si';
						}
						
						$personaObj->update($campo);
                        //Tabla principal
						$intereses_asegurados = $this->AseguradosModel->findByInteresesTable($personaObj->id, 5);
						$codigo = $intereses_asegurados->numero;
						$fieldset['identificacion'] = $personaObj->identificacion;
						if($intereses_asegurados->estado!=$_POST["campo"]["estado"]){
							$comentario.="<b>Campo: Estado</b><br>Valor Actual:".$_POST["campo"]["estado"]."<br>Valor Anterior:".$intereses_asegurados->estado."<br><br>";
							$cambio='si';
						}
						$fieldset['estado'] = $_POST["campo"]["estado"];
						$intereses_asegurados->update($fieldset);

						if($campodesde['desde']=="solicitudes"){
							$detalle = array();
							$detalle['id_intereses'] = $intereses_asegurados->id;
							$detalle['detalle_relacion'] = isset($campodetalle['relacion']) ? $campodetalle['relacion'] : $campodetalle['relacion_benficario'];
							$detalle['tipo_relacion'] =isset($campodetalle['tipo_relacion']) ? $campodetalle['tipo_relacion'] : "";
							$detalle['detalle_prima'] = isset($campodetalle['prima_anual']) ? $campodetalle['prima_anual'] : '';
							$detalle['detalle_beneficio'] = isset($campodetalle['beneficio_vida']) ? $campodetalle['beneficio_vida'] : '';
							$detalle['detalle_monto'] = isset($campodetalle['monto']) ? $campodetalle['monto'] : '';
							$detalle['detalle_int_asociado'] = isset($interesId->id) ?$interesId->id: '';
							$detalle['detalle_certificado'] = isset($campodetalle['certificado']) ? $campodetalle['certificado'] : '';
							$detalle['detalle_suma_asegurada'] = isset($campodetalle['suma_asegurada']) ? $campodetalle['suma_asegurada'] : '';
							$detalle['detalle_deducible'] = isset($campodetalle['deducible']) ? $campodetalle['deducible'] : '';
							$detalle['detalle_participacion'] = isset($campodetalle['participacion']) ? $campodetalle['participacion'] : '';
							$detalle['detalle_unico'] = $_POST['detalleunico'];

							$num = InteresesAsegurados_detalles::where('id_intereses', $detalle['id_intereses'])->where('detalle_unico', $detalle['detalle_unico'])->count();
							if ($num>0 && $validarEditar ==2) {
								if($individual==1 || $individual=='colectivo'){
									unset($detalle['detalle_int_asociado']);
									unset($detalle['detalle_relacion']);    
								}
								$num1 = InteresesAsegurados_detalles::where('id_intereses', $detalle['id_intereses'])->where('detalle_unico', $detalle['detalle_unico'])->first();

								$det = InteresesAsegurados_detalles::where('id_intereses', $detalle['id_intereses'])->where('detalle_unico', $detalle['detalle_unico'])->update($detalle);
								
								if($num1->detalle_relacion!=$detalle['detalle_relacion'])
								{
									$comentario.="<b>Campo: Relación</b><br>Valor Actual:".$detalle['detalle_relacion']."<br>Valor Anterior: ".$num1->detalle_relacion."<br><br>";
								}
								if($num1->detalle_int_asociado!=$detalle['detalle_int_asociado'])
								{
									//$interes=$this->AseguradosModel->find();
									$comentario.="<b>Campo: Interes asegurado asociado</b><br>Valor Actual:".$detalle['detalle_int_asociado']."<br>Valor Anterior: ".$num1->detalle_int_asociado."<br><br>";
								}
								if($num1->detalle_participacion!=$detalle['detalle_participacion'])
								{
									$comentario.="<b>Campo: Participación</b><br>Valor Actual:".$detalle['detalle_participacion']."<br>Valor Anterior: ".$num1->detalle_participacion."<br><br>";
								}
								if($num1->detalle_certificado!=$detalle['detalle_certificado'])
								{
									$comentario.="<b>Campo: No. Certificado</b><br>Valor Actual:".$detalle['detalle_certificado']."<br>Valor Anterior: ".$num1->detalle_certificado."<br><br>";
									$cambio='si';
								}
								if($num1->detalle_suma_asegurada!=$detalle['detalle_suma_asegurada'])
								{
									$comentario.="<b>Campo: Suma Asegurada</b><br>Valor Actual:".$detalle['detalle_suma_asegurada']."<br>Valor Anterior: ".$num1->detalle_suma_asegurada."<br><br>";
									$cambio='si';
								}
								if($num1->detalle_prima!=$detalle['detalle_prima'])
								{
									$comentario.="<b>Campo: Prima neta</b><br>Valor Actual:".$detalle['detalle_prima']."<br>Valor Anterior: ".$num1->detalle_prima."<br><br>";
									$cambio='si';
								}
								if($num1->detalle_deducible!=$detalle['detalle_deducible'])
								{
									$comentario.="<b>Campo: Deducible</b><br>Valor Actual:".$detalle['detalle_deducible']."<br>Valor Anterior: ".$num1->detalle_deducible."<br><br>";
									$cambio='si';
								}
							
							}else if(($individual==0 || $detalle['detalle_relacion']=='Dependiente' ||$individual=='colectivo')&& $num<=0) {

								$det = InteresesAsegurados_detalles::create($detalle);
								
								if($det->detalle_relacion!=$detalle['detalle_relacion'])
								{
									$comentario.="<b>Campo: Relación</b><br>Valor: ".$det->detalle_relacion."<br><br>";
								}
								if($det->detalle_int_asociado!=$detalle['detalle_int_asociado'])
								{
									//$interes=$this->AseguradosModel->find();
									$comentario.="<b>Campo: Interes asegurado asociado</b><br>Valor: ".$det->detalle_int_asociado."<br><br>";
								}
								if($det->detalle_participacion!=$detalle['detalle_participacion'])
								{
									$comentario.="<b>Campo: Participación</b><br>Valor: ".$det->detalle_participacion."<br><br>";
								}
								if($det->detalle_certificado!="")
								{
									$comentario.="<b>Campo: No. Certificado</b><br>Valor Actual:".$det->detalle_certificado."<br><br>";
									$cambio='si';
								}
								if($det->detalle_suma_asegurada!="")
								{
									$comentario.="<b>Campo: Suma Asegurada</b><br>Valor Actual:".$det->detalle_suma_asegurada."<br><br>";
									$cambio='si';
								}
								if($det->detalle_prima!="")
								{
									$comentario.="<b>Campo: Prima neta</b><br>Valor Actual:".$det->detalle_prima."<br><br>";
									$cambio='si';
								}
								if($det->detalle_deducible!="")
								{
									$comentario.="<b>Campo: Deducible</b><br>Valor Actual:".$det->detalle_deducible."<br><br>";
									$cambio='si';
								}
							}
						}
						
						$usuario_registro = Usuario_orm::find($this->session->userdata['id_usuario']);

						$comentario.="Registrado Por: ".$fieldset['creado_por'] =$usuario_registro->nombre." ".$usuario_registro ->apellido;

						$comentario2="<b>Interés Persona</b><br>Nombre: ".$personaObj->nombrePersona."<br><br>";
						$fieldset["comentario"] = $comentario2."".$comentario;
						$fieldset["comentable_type"] = "Actualizacion_interes_solicitudes";
						if($num1->id_solicitudes==''){
							$solicitud=$_POST['detalleunico'];
						}
						else
							$solicitud=$num1->id_solicitudes;
						$fieldset["comentable_id"] = $solicitud;
						$fieldset["usuario_id"] = $this->session->userdata['id_usuario'];
						$fieldset["empresa_id"] = $this->empresa_id;

						if($cambio=='si')
							$interesase = $this->bitacoraModel->create($fieldset);
						
                        //Subir documentos
						if (!empty($_FILES['file'])) {
							$vehiculo_id = $personaObj->id;
							unset($_POST["campo"]);
							$modeloInstancia = $this->PersonasModel->find($vehiculo_id);
							$this->documentos->subir($modeloInstancia);
						}
					} else {
						$mensaje = array('class' => 'alert-danger', 'contenido' => '<strong>¡Error!</strong>Usted no tiene permisos para editar este registro');
					}
				}
				Capsule::commit();
			} catch (ValidationException $e) {
				log_message('error', $e);
				Capsule::rollback();
			}

			if (!is_null($intereses_asegurados)) {
				$mensaje = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente', 'titulo' => 'Interés asegurado '.$codigo.'');
			} else {
				$mensaje = array('class' => 'alert-danger', 'contenido' => '<strong>¡Error!</strong> Su solicitud no fue procesada');
			}
		} else {
			$mensaje = array('class' => 'alert-warning', 'contenido' => '<strong>¡Error!</strong> Su solicitud no fue procesada');
		}

		$this->session->set_flashdata('mensaje', $mensaje);
		if ($campodesde['desde']!="solicitudes") {
			redirect(base_url('intereses_asegurados/listar'));
		}else if($campodesde['desde']=="solicitudes"){
			print_r($uuid."&".$codigo."&".$individual);
			exit;
		}
	}
	public function validatePercent(){
		$unicDetail = $_POST['detail'];
		$fatherId = $_POST['fatherId'];
		$num =0;
		$interest= $this->AseguradosModel->where('interesestable_id',$fatherId)
		->where('interesestable_type',5)->first();
		if(count($interest)){                           
			$num = InteresesAsegurados_detalles::where('detalle_relacion', 'Dependiente')
			->where('detalle_int_asociado', $interest->id)
			->where('detalle_unico', $unicDetail)
			->sum('detalle_participacion');
		}
		print json_encode($num);                    
	}

	public function casco_aereoformularioparcial($data = array()) {

		$this->assets->agregar_js(array(
			'public/assets/js/modules/intereses_asegurados/crear_aereo.js',
			));
		if (empty($data)) {
			$data["campos"] = array();
		}

        //casco aereo
		$clause['empresa_id'] = $this->empresa_id;
		$clause['tipo'] = 1;
		$data['acreedores'] = $this->AcreedoresRep->get($clause);
		$data['estado'] = $this->SegCatalogoRepository->listar_catalogo('estado2', 'orden');

		$this->load->view('formulariocascoaereo', $data);
	}

	function ajax_check_aereo() {

		$chasis = $this->input->post("serie");

		if ($this->input->post("uuid_aereo") != "") {
			$uuid = hex2bin(strtolower($this->input->post("uuid_aereo")));

			$count = $this->interesesAseguradosRep->validarSerieAereo($uuid, $chasis);

			if ($count > 0) {
				echo('USER_AVAILABLE');
			} else {
				$chasis_obj = $this->interesesAseguradosRep->identificacion_aereo($chasis);
				if (empty($chasis_obj)) {
					echo('USER_AVAILABLE');
				} else {
					echo('USER_EXISTS');
				}
			}
		} else {
			$chasis_obj = $this->interesesAseguradosRep->identificacion_aereo($chasis);
			if (empty($chasis_obj)) {
				echo('USER_AVAILABLE');
			} else {
				echo('USER_EXISTS');
			}
		}
	}

	function guardar_aereo() {

		if ($_POST) {
			unset($_POST["campo"]["guardar"]);
			$campo = Util::set_fieldset("campo");
			$campodesde = Util::set_fieldset("campodesde");
			$campodetalle = Util::set_fieldset("campodetalle");
			if (!isset($campo['uuid'])) {
				$campo['empresa_id'] = $this->empresa_id;
			}
			$uuid = "";
			$casco_aereo = null;
			$aereoObj = null;

			Capsule::beginTransaction();
			try {
				if (empty($campo['uuid'])) {
					$clause['empresa_id'] = $this->empresa_id;
					$total = $this->interesesAseguradosRep->listar_aereo($clause);
					$codigo = Util::generar_codigo('CAE', count($total) + 1);
					$campo["numero"] = $codigo;

					$campo['marca'] = isset($campo['marca_aereo']) ? $campo['marca_aereo'] : '';
					$campo['modelo'] = isset($campo['modelo_aereo']) ? $campo['modelo_aereo'] : '';
					$campo['matricula'] = isset($campo['matricula_aereo']) ? $campo['matricula_aereo'] : '';
					$campo['valor'] = isset($campo['valor_aereo']) ? $campo['valor_aereo'] : '';
					$campo['pasajeros'] = isset($campo['pasajeros_aereo']) ? $campo['pasajeros_aereo'] : '';
					$campo['tripulacion'] = isset($campo['tripulacion_aereo']) ? $campo['tripulacion_aereo'] : '';
					$campo['observaciones'] = isset($campo['observaciones']) ? $campo['observaciones'] : '';
					$estadoaereo = $campo['estado'];
					unset($campo['estado']);

					$casco_aereo = $this->AereoModel->create($campo);
					
					$comentario="<b>Interés Casco Aéreo</b><br><br>";
					
					if($casco_aereo->serie!='')
						$comentario.="<b>Campo: N°. de serie del casco </b><br>Valor: ".$casco_aereo->serie."<br><br>";
					if($casco_aereo->marca!='')
						$comentario.="<b>Campo: Marca</b><br>Valor: ".$casco_aereo->marca."<br><br>";
					if($casco_aereo->modelo!='')
						$comentario.="<b>Campo: Modelo</b><br>Valor: ".$casco_aereo->modelo."<br><br>";
					if($casco_aereo->matricula!='')
						$comentario.="<b>Campo: Matrícula</b><br>Valor: ".$casco_aereo->matricula."<br><br>";
					if($casco_aereo->valor!='')
						$comentario.="<b>Campo: Valor</b><br>Valor: ".$casco_aereo->valor."<br><br>";
					if($casco_aereo->pasajeros!='')
						$comentario.="<b>Campo: Pasajeros</b><br>Valor: ".$casco_aereo->pasajeros."<br><br>";
					if($casco_aereo->tripulacion!='')
						$comentario.="<b>Campo: Tripulación</b><br>Valor: ".$casco_aereo->tripulacion."<br><br>";
					if($casco_aereo->observaciones!='')
						$comentario.="<b>Campo: Observaciones</b><br>Valor: ".$casco_aereo->observaciones."<br><br>";
					if($campo['estado']!='')
						$comentario.="<b>Campo: Estado</b><br>Valor: ".$campo['estado']."<br><br>";
					
                    //guardar tabla principal
					$fieldset['uuid_intereses'] = Capsule::raw("ORDER_UUID(uuid())");
					$fieldset['empresa_id'] = $casco_aereo->empresa_id;
					$fieldset['interesestable_type'] = 3;
					$fieldset['interesestable_id'] = $casco_aereo->id;
					$fieldset['numero'] = $codigo;
					$fieldset['identificacion'] = $casco_aereo->serie;
					if ($estadoaereo == "") {
						$fieldset['estado'] = "Activo";
					} else {
						$fieldset['estado'] = $estadoaereo;
					}
					$fieldset['creado_por'] = $this->session->userdata['id_usuario'];
					$ca = $casco_aereo->interesesAsegurados()->create($fieldset);

					if ($campodesde['desde'] == "solicitudes") {
						$u = ia::where('id', $ca->id)->first()->toArray();
						$uuid = bin2hex($u['uuid_intereses']);

						$detalle = array();
						$detalle['id_intereses'] = $ca->id;
						$detalle['detalle_relacion'] = isset($campodetalle['relacion']) ? $campodetalle['relacion'] : '';
						$detalle['detalle_prima'] = isset($campodetalle['prima_anual']) ? $campodetalle['prima_anual'] : '';
						$detalle['detalle_beneficio'] = isset($campodetalle['beneficio_vida']) ? $campodetalle['beneficio_vida'] : '';
						$detalle['detalle_monto'] = isset($campodetalle['monto']) ? $campodetalle['monto'] : '';
						$detalle['detalle_int_asociado'] = isset($campodetalle['interes_asociado']) ? $campodetalle['interes_asociado'] : '';
						$detalle['detalle_certificado'] = isset($campodetalle['certificado']) ? $campodetalle['certificado'] : '';
						$detalle['detalle_suma_asegurada'] = isset($campodetalle['suma_asegurada']) ? $campodetalle['suma_asegurada'] : '';
						$detalle['detalle_deducible'] = isset($campodetalle['deducible']) ? $campodetalle['deducible'] : '';
						$detalle['detalle_unico'] = $_POST['detalleunico'];

						$num = InteresesAsegurados_detalles::where('id_intereses', $detalle['id_intereses'])->where('detalle_unico', $detalle['detalle_unico'])->count();
						if ($num > 0) {
							$det = InteresesAsegurados_detalles::where('id_intereses', $detalle['id_intereses'])->where('detalle_unico', $detalle['detalle_unico'])->update($detalle);
							if($det->detalle_certificado!=$detalle['detalle_certificado'])
							{
								$comentario.="<b>Campo: No. Certificado</b><br>Valor Actual:".$det->detalle_certificado."<br>Valor Anterior: ".$detalle['detalle_certificado']."<br><br>";
							}
							if($det->detalle_suma_asegurada!=$detalle['detalle_suma_asegurada'])
							{
								$comentario.="<b>Campo: Suma Asegurada</b><br>Valor Actual:".$det->detalle_suma_asegurada."<br>Valor Anterior: ".$detalle['detalle_suma_asegurada']."<br><br>";
							}
							if($det->detalle_prima!=$detalle['detalle_prima'])
							{
								$comentario.="<b>Campo: Prima neta</b><br>Valor Actual:".$det->detalle_prima."<br>Valor Anterior: ".$detalle['detalle_prima']."<br><br>";
							}
							if($det->detalle_deducible!=$detalle['detalle_deducible'])
							{
								$comentario.="<b>Campo: Deducible</b><br>Valor Actual:".$det->detalle_deducible."<br>Valor Anterior: ".$detalle['detalle_deducible']."<br><br>";
							}
						} else {
							$det = InteresesAsegurados_detalles::create($detalle);
							if($det->detalle_certificado!="")
							{
								$comentario.="<b>Campo: No. Certificado</b><br>Valor: ".$det->detalle_certificado."<br><br>";
							}
							if($det->detalle_suma_asegurada!="")
							{
								$comentario.="<b>Campo: Suma Asegurada</b><br>Valor: ".$det->detalle_suma_asegurada."<br><br>";
							}
							if($det->detalle_prima!="")
							{
								$comentario.="<b>Campo: Prima neta</b><br>Valor: ".$det->detalle_prima."<br><br>";
							}
							if($det->detalle_deducible!="")
							{
								$comentario.="<b>Campo: Deducible</b><br>Valor: ".$det->detalle_deducible."<br><br>";
							}
						}
						
						$usuario_registro = Usuario_orm::find($this->session->userdata['id_usuario']);
						$comentario.="Registrado Por: ".$fieldset['creado_por'] =$usuario_registro->nombre." ".$usuario_registro ->apellido;

						$fieldset["comentario"] = $comentario;
						$fieldset["comentable_id"] = $detalle['detalle_unico'];
						$fieldset["usuario_id"] = $this->session->userdata['id_usuario'];
						$fieldset["empresa_id"] = $this->empresa_id;

						$interesase = $this->bitacoraModel->create($fieldset);
						
					}
					
					//Subir documentos
					if (!empty($_FILES['file'])) {
						$aereo_id = $casco_aereo->id;
						unset($_POST["campo"]);
						$modeloInstancia = $this->AereoModel->find($aereo_id);
						$this->documentos->subir($modeloInstancia);
					}
				} else {

					if ($this->auth->has_permission('acceso', 'intereses_asegurados/editar/(:any)')) {
                        //$aereoObj = $this->AereoModel->find($campo['uuid']);
						$cargaInt = AseguradosModel::findByUuid($campo['uuid']);
						$codigo = $cargaInt->numero;
						$aereoObj = AereoModel::find($cargaInt->interesestable_id);
						$uuid = $campo['uuid'];
						unset($campo['uuid']);
						$campo['marca'] = isset($campo['marca_aereo']) ? $campo['marca_aereo'] : '';
						$campo['modelo'] = isset($campo['modelo_aereo']) ? $campo['modelo_aereo'] : '';
						$campo['matricula'] = isset($campo['matricula_aereo']) ? $campo['matricula_aereo'] : '';
						$campo['valor'] = isset($campo['valor_aereo']) ? $campo['valor_aereo'] : '';
						$campo['pasajeros'] = isset($campo['pasajeros_aereo']) ? $campo['pasajeros_aereo'] : '';
						$campo['tripulacion'] = isset($campo['tripulacion_aereo']) ? $campo['tripulacion_aereo'] : '';
						$campo['observaciones'] = isset($campo['observaciones']) ? $campo['observaciones'] : '';
						unset($campo['marca_aereo']);
						unset($campo['modelo_aereo']);
						unset($campo['matricula_aereo']);
						unset($campo['valor_aereo']);
						unset($campo['pasajeros_aereo']);
						unset($campo['tripulacion_aereo']);
						unset($campo['observaciones_aereo']);
						$estadoaereo = $campo['estado'];
						unset($campo['estado']);
                        //$aereoObj->update($campo);
						$actAereo = $this->AereoModel->find($cargaInt->interesestable_id);
					
						$cambio='no';
						if($actAereo->serie!=$campo['serie'])
						{
							$comentario.="<b>Campo: N°. de serie del casco</b><br>Valor Actual: ".$campo['serie']."<br>Valor Anterior:".$actAereo->serie."<br><br>";
							$cambio='si';
						}
						if($actAereo->marca!=$campo['marca']){
							$comentario.="<b>Campo: Marca</b><br>Valor Actual:".$campo['marca']."<br>Valor Anterior:".$actAereo->marca."<br><br>";
							$cambio='si';
						}
						if($actAereo->modelo!=$campo['modelo']){
							$comentario.="<b>Campo: Modelo</b><br>Valor Actual:".$campo['modelo']."<br>Valor Anterior:".$actAereo->modelo."<br><br>";
							$cambio='si';
						}
						if($actAereo->matricula!=$campo['matricula']){
							$comentario.="<b>Campo: Matrícula</b><br>Valor Actual:".$campo['matricula']."<br>Valor Anterior:".$actAereo->matricula."<br><br>";
							$cambio='si';
						}
						if($actAereo->valor!=$campo['valor']){
							$comentario.="<b>Campo: Valor</b><br>Valor Actual:".$campo['valor']."<br>Valor Anterior:".$actAereo->valor."<br><br>";
							$cambio='si';
						}
						if($actAereo->pasajeros!=$campo['pasajeros']){
							$comentario.="<b>Campo: Pasajeros</b><br>Valor Actual:".$campo['pasajeros']."<br>Valor Anterior:".$actAereo->pasajeros."<br><br>";
							$cambio='si';
						}
						if($actAereo->tripulacion!=$campo['tripulacion']){
							$comentario.="<b>Campo: Tripulación</b><br>Valor Actual:".$campo['tripulacion']."<br>Valor Anterior:".$actAereo->tripulacion."<br><br>";
							$cambio='si';
						}
						if($actAereo->observaciones!=$campo['observaciones']){
							$comentario.="<b>Campo: Observaciones</b><br>Valor Actual:".$campo['observaciones']."<br>Valor Anterior:".$actAereo->observaciones."<br><br>";
							$cambio='si';
						}
						
						$actAereo = $this->AereoModel->where('id', $cargaInt->interesestable_id)->update($campo);
						
                        //Tabla principal
						$intereses_asegurados = $this->AseguradosModel->findByInteresesTable($aereoObj->id, $aereoObj->tipo_id);
						$fieldset['identificacion'] = $aereoObj->serie;
						if($intereses_asegurados->estado!=$estadoaereo){
							$comentario.="<b>Campo: Estado</b><br>Valor Actual:".$estadoaereo."<br>Valor Anterior:".$intereses_asegurados->estado."<br><br>";
							$cambio='si';
						}
						$fieldset['estado'] = $estadoaereo;
                        //$intereses_asegurados->update($fieldset);
						$intase = $this->AseguradosModel->where('id', $intereses_asegurados->id)->update($fieldset);

						if ($campodesde['desde'] == "solicitudes") {
							$detalle = array();
							$detalle['id_intereses'] = $intereses_asegurados->id;
							$detalle['detalle_relacion'] = isset($campodetalle['relacion']) ? $campodetalle['relacion'] : '';
							$detalle['detalle_prima'] = isset($campodetalle['prima_anual']) ? $campodetalle['prima_anual'] : '';
							$detalle['detalle_beneficio'] = isset($campodetalle['beneficio_vida']) ? $campodetalle['beneficio_vida'] : '';
							$detalle['detalle_monto'] = isset($campodetalle['monto']) ? $campodetalle['monto'] : '';
							$detalle['detalle_int_asociado'] = isset($campodetalle['interes_asociado']) ? $campodetalle['interes_asociado'] : '';
							$detalle['detalle_certificado'] = isset($campodetalle['certificado']) ? $campodetalle['certificado'] : '';
							$detalle['detalle_suma_asegurada'] = isset($campodetalle['suma_asegurada']) ? $campodetalle['suma_asegurada'] : '';
							$detalle['detalle_deducible'] = isset($campodetalle['deducible']) ? $campodetalle['deducible'] : '';
							$detalle['detalle_unico'] = $_POST['detalleunico'];

							$num = InteresesAsegurados_detalles::where('id_intereses', $detalle['id_intereses'])->where('detalle_unico', $detalle['detalle_unico'])->count();
							if ($num > 0) {
								$num1 = InteresesAsegurados_detalles::where('id_intereses', $detalle['id_intereses'])->where('detalle_unico', $detalle['detalle_unico'])->first();

								$det = InteresesAsegurados_detalles::where('id_intereses', $detalle['id_intereses'])->where('detalle_unico', $detalle['detalle_unico'])->update($detalle);
								
								if($num1->detalle_certificado!=$detalle['detalle_certificado'])
								{
									$comentario.="<b>Campo: No. Certificado</b><br>Valor Actual:".$detalle['detalle_certificado']."<br>Valor Anterior: ".$num1->detalle_certificado."<br><br>";
									$cambio='si';
								}
								if($num1->detalle_suma_asegurada!=$detalle['detalle_suma_asegurada'])
								{
									$comentario.="<b>Campo: Suma Asegurada</b><br>Valor Actual:".$detalle['detalle_suma_asegurada']."<br>Valor Anterior: ".$num1->detalle_suma_asegurada."<br><br>";
									$cambio='si';
								}
								if($num1->detalle_prima!=$detalle['detalle_prima'])
								{
									$comentario.="<b>Campo: Prima neta</b><br>Valor Actual:".$detalle['detalle_prima']."<br>Valor Anterior: ".$num1->detalle_prima."<br><br>";
									$cambio='si';
								}
								if($num1->detalle_deducible!=$detalle['detalle_deducible'])
								{
									$comentario.="<b>Campo: Deducible</b><br>Valor Actual:".$detalle['detalle_deducible']."<br>Valor Anterior: ".$num1->detalle_deducible."<br><br>";
									$cambio='si';
								}
							} else {
								$det = InteresesAsegurados_detalles::create($detalle);
								if($det->detalle_certificado!="")
								{
									$comentario.="<b>Campo: No. Certificado</b><br>Valor Actual:".$det->detalle_certificado."<br><br>";
									$cambio='si';
								}
								if($det->detalle_suma_asegurada!="")
								{
									$comentario.="<b>Campo: Suma Asegurada</b><br>Valor Actual:".$det->detalle_suma_asegurada."<br><br>";
									$cambio='si';
								}
								if($det->detalle_prima!="")
								{
									$comentario.="<b>Campo: Prima neta</b><br>Valor Actual:".$det->detalle_prima."<br><br>";
									$cambio='si';
								}
								if($det->detalle_deducible!="")
								{
									$comentario.="<b>Campo: Deducible</b><br>Valor Actual:".$det->detalle_deducible."<br><br>";
									$cambio='si';
								}
							}
						}
						
						$usuario_registro = Usuario_orm::find($this->session->userdata['id_usuario']);

						$comentario.="Registrado Por: ".$fieldset['creado_por'] =$usuario_registro->nombre." ".$usuario_registro ->apellido;

						$comentario2="<b>Interés Casco Aéreo</b><br>Chasis: ".$aereoObj->serie."<br><br>";
						$fieldset["comentario"] = $comentario2."".$comentario;
						$fieldset["comentable_type"] = "Actualizacion_interes_solicitudes";
						if($num1->id_solicitudes==''){
							$solicitud=$_POST['detalleunico'];
						}
						else
							$solicitud=$num1->id_solicitudes;
						$fieldset["comentable_id"] = $solicitud;
						$fieldset["usuario_id"] = $this->session->userdata['id_usuario'];
						$fieldset["empresa_id"] = $this->empresa_id;

						if($cambio=='si')
							$interesase = $this->bitacoraModel->create($fieldset);

                        //Subir documentos
						if (!empty($_FILES['file'])) {
							$vehiculo_id = $aereoObj->id;
							unset($_POST["campo"]);
							$modeloInstancia = $this->AereoModel->find($vehiculo_id);
							$this->documentos->subir($modeloInstancia);
						}
					} else {
						$mensaje = array('class' => 'alert-danger', 'contenido' => '<strong>¡Error!</strong>Usted no tiene permisos para editar este registro');
					}
				}
				Capsule::commit();
			} catch (ValidationException $e) {
				log_message('error', $e);
				Capsule::rollback();
			}

			if (!is_null($casco_aereo) || !is_null($aereoObj)) {
				$mensaje = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente', 'titulo' => 'Interés asegurado ' . $codigo . '');
			} else {
				$mensaje = array('class' => 'alert-danger', 'contenido' => '<strong>¡Error!</strong> Su solicitud no fue procesada');
			}
		} else {
			$mensaje = array('class' => 'alert-warning', 'contenido' => '<strong>¡Error!</strong> Su solicitud no fue procesada');
		}

		$this->session->set_flashdata('mensaje', $mensaje);
		if ($campodesde['desde'] != "solicitudes") {
			redirect(base_url('intereses_asegurados/listar'));
		} else if ($campodesde['desde'] == "solicitudes") {
			print_r($uuid . "&" . $codigo);
			exit;
		}
	}

	public function obtener_politicas() {
		echo json_encode($this->politicas);
		exit;
	}

	public function obtener_politicas_general() {
		echo json_encode($this->politicas_general);
		exit;
	}

	public function ajax_listar_articulo($grid = NULL) {

		$estado = $this->input->post('estado', true);

		$clause = array(
			"numero" => $this->input->post('numero', true),
			"nombre" => $this->input->post('nombre', true),
			"clase_equipo" => $this->input->post('clase_equipo', true),
			"marca" => $this->input->post('marca', true),
			"modelo" => $this->input->post('modelo', true),
			"anio" => $this->input->post('anio', true),
			"numero_serie" => $this->input->post('numero_serie', true),
			"id_condicion" => $this->input->post('id_condicion', true),
			"valor" => $this->input->post('valor', true),
			"fecha" => $this->input->post('fecha', true),
			'empresa_id' => $this->empresa_id,
			"detalle_unico" => $this->input->post('detalle_unico')
			);


		list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
		$count = ArticuloModel::listar_articulo_provicional($clause, NULL, NULL, NULL, NULL)->count();
		list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
		$rows = ArticuloModel::listar_articulo_provicional($clause, $sidx, $sord, $limit, $start);

        //Constructing a JSON
		$response = new stdClass();
		$response->page = $page;
		$response->total = $total_pages;
		$response->records = $count;
		$i = 0;
		if (!empty($rows->toArray())) {
			foreach ($rows->toArray() AS $i => $row) {

				if ($row['estado'] == 'Inactivo')
					$spanStyle = 'label label-danger';
				else if ($row['estado'] == 'Activo')
					$spanStyle = 'label label-successful';
				else
					$spanStyle = 'label label-warning';

				$hidden_options = "<a href='#' class='btn btn-block btn-outline btn-success linkCargaInfoArticulo' data-int-gr='" . $row["id_intereses"] . "' data-int-id='" . $row["interesestable_id"] . "'>Ver Inter&eacute;s</a>";
				$hidden_options .= "<a class='btn btn-block btn-outline btn-success subir_documento_solicitudes_intereses' data-int-id='" . $row["interesestable_id"] . "' data-tipo-interes='articulo' >Subir Documento</a>";
				$hidden_options .= "<a href='#' class='btn btn-block btn-outline btn-success quitarInteres' data-int-gr='" . $row['id_intereses'] . "'>Quitar Inter&eacute;s</a>";
                //$hidden_options .= '<a href="#" id="cambiarEtapaConfirmBtn" class="btn btn-block btn-outline btn-success">Crear Reporte de Comisión</a>';
				$link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row['id'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

				$response->rows[$i]["id"] = $row['id'];
				$response->rows[$i]["cell"] = array(
					$row["numero"],
					$row['nombre'],
					$row['clase_equipo'],
					$row['marca'],
					$row['modelo'],
					$row['anio'],
					$row['numero_serie'],
					$row['id_condicion'],
					$row['valor'],
					$row['fecha_inclusion'],
					$row['fecha_exclusion'],
					"<label class='" . $spanStyle . " cambiarestadoseparado' data-id='" . $row['id'] . "'>" . $row['estado'] . "</label>",
					$link_option,
					$hidden_options
					);
				$i++;
			}
		}
		print_r(json_encode($response));
		exit;
	}

	public function ajax_listar_carga($grid = NULL) {

		$estado = $this->input->post('estado', true);

		$clause = array(
			"numero" => $this->input->post('numero', true),
			"no_liquidacion" => $this->input->post('no_liquidacion', true),
			"fecha_despacho" => $this->input->post('fecha_despacho', true),
			"fecha_arribo" => $this->input->post('fecha_arribo', true),
			"medio_transporte" => $this->input->post('medio_transporte', true),
			"valor" => $this->input->post('valor', true),
			"origen" => $this->input->post('origen', true),
			"destino" => $this->input->post('destino', true),
			"fecha_inclusion" => $this->input->post('fecha_inclusion', true),
			'empresa_id' => $this->empresa_id,
			"detalle_unico" => $this->input->post('detalle_unico')
			);


		list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
		$count = CargaModel::listar_carga_provicional($clause, NULL, NULL, NULL, NULL)->count();
		list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
		$rows = CargaModel::listar_carga_provicional($clause, $sidx, $sord, $limit, $start);

        //Constructing a JSON
		$response = new stdClass();
		$response->page = $page;
		$response->total = $total_pages;
		$response->records = $count;
		$i = 0;

		if (!empty($rows->toArray())) {
			foreach ($rows->toArray() AS $i => $row) {

				if ($row['estado'] == 'Inactivo')
					$spanStyle = 'label label-danger';
				else if ($row['estado'] == 'Activo')
					$spanStyle = 'label label-successful';
				else
					$spanStyle = 'label label-warning';

				$hidden_options = "<a href='#' class='btn btn-block btn-outline btn-success linkCargaInfoCarga' data-int-gr='" . $row["id_intereses"] . "' data-int-id='" . $row["interesestable_id"] . "'>Ver Inter&eacute;s</a>";
				$hidden_options .= "<a class='btn btn-block btn-outline btn-success subir_documento_solicitudes_intereses'  data-int-id='" . $row["interesestable_id"] . "' data-tipo-interes='carga'  >Subir Documento</a>";
				$hidden_options .= "<a href='#' class='btn btn-block btn-outline btn-success quitarInteres' data-int-gr='" . $row['id_intereses'] . "'>Quitar Inter&eacute;s</a>";
                //$hidden_options .= '<a href="#" id="cambiarEtapaConfirmBtn" class="btn btn-block btn-outline btn-success">Crear Reporte de Comisión</a>';
				$link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row['id'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

				$response->rows[$i]["id"] = $row['id'];
				$response->rows[$i]["cell"] = array(
					$row["numero"],
					$row['no_liquidacion'],
					$row['fecha_despacho'],
					$row['fecha_arribo'],
					$row['medio_transporte'],
					$row['valor'],
					$row['origen'],
					$row['destino'],
					$row['fecha_inclusion'],
					$row['fecha_exclusion'],
					"<label class='" . $spanStyle . " cambiarestadoseparado' data-id='" . $row['id'] . "'>" . $row['estado'] . "</label>",
					$link_option,
					$hidden_options
					);
				$i++;
			}
		}
		print_r(json_encode($response));
		exit;
	}

	public function ajax_listar_aereo($grid = NULL) {

		$estado = $this->input->post('estado', true);

		$clause = array(
			"numero" => $this->input->post('numero', true),
			"serie" => $this->input->post('serie', true),
			"marca" => $this->input->post('marca', true),
			"modelo" => $this->input->post('modelo', true),
			"matricula" => $this->input->post('matricula', true),
			"valor" => $this->input->post('valor', true),
			"pasajeros" => $this->input->post('pasajeros', true),
			"tripulacion" => $this->input->post('tripulacion', true),
			"fecha_inclusion" => $this->input->post('fecha_inclusion', true),
			"fecha_exclusion" => $this->input->post('fecha_exclusion', true),
			'empresa_id' => $this->empresa_id,
			"detalle_unico" => $this->input->post('detalle_unico')
			);


		list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
		$count = AereoModel::listar_aereo_provicional($clause, NULL, NULL, NULL, NULL)->count();
		list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
		$rows = AereoModel::listar_aereo_provicional($clause, $sidx, $sord, $limit, $start);

        //Constructing a JSON
		$response = new stdClass();
		$response->page = $page;
		$response->total = $total_pages;
		$response->records = $count;
		$i = 0;

		if (!empty($rows->toArray())) {
			foreach ($rows->toArray() AS $i => $row) {

				if ($row['estado'] == 'Inactivo')
					$spanStyle = 'label label-danger';
				else if ($row['estado'] == 'Activo')
					$spanStyle = 'label label-successful';
				else
					$spanStyle = 'label label-warning';

				$hidden_options = "<a href='#' class='btn btn-block btn-outline btn-success linkCargaInfoAereo' data-int-gr='" . $row["id_intereses"] . "' data-int-id='" . $row["interesestable_id"] . "'>Ver Inter&eacute;s</a>";
				$hidden_options .= "<a class='btn btn-block btn-outline btn-success subir_documento_solicitudes_intereses' data-int-id='" . $row["interesestable_id"] . "' data-tipo-interes='casco_aereo' >Subir Documento</a>";
				$hidden_options .= "<a href='#' class='btn btn-block btn-outline btn-success quitarInteres' data-int-gr='" . $row['id_intereses'] . "'>Quitar Inter&eacute;s</a>";
                //$hidden_options .= '<a href="#" id="cambiarEtapaConfirmBtn" class="btn btn-block btn-outline btn-success">Crear Reporte de Comisión</a>';
				$link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row['id'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

				$response->rows[$i]["id"] = $row['id'];
				$response->rows[$i]["cell"] = array(
					$row["numero"],
					$row['serie'],
					$row['marca'],
					$row['modelo'],
					$row['matricula'],
					$row['valor'],
					$row['pasajeros'],
					$row['tripulacion'],
					$row['fecha_inclusion'],
					$row['fecha_exclusion'],
					"<label class='" . $spanStyle . " cambiarestadoseparado' data-id='" . $row['id'] . "'>" . $row['estado'] . "</label>",
					$link_option,
					$hidden_options
					);
				$i++;
			}
		}
		print_r(json_encode($response));
		exit;
	}

	public function ajax_listar_maritimo($grid = NULL) {

		$estado = $this->input->post('estado', true);

		$clause = array(
			"numero" => $this->input->post('numero', true),
			"serie" => $this->input->post('serie', true),
			"nombre_embarcacion" => $this->input->post('nombre_embarcacion', true),
			"tipo" => $this->input->post('tipo', true),
			"marca" => $this->input->post('marca', true),
			"valor" => $this->input->post('valor', true),
			"acreedor" => $this->input->post('acreedor', true),
			"fecha_inclusion" => $this->input->post('fecha_inclusion', true),
			'empresa_id' => $this->empresa_id,
			"detalle_unico" => $this->input->post('detalle_unico')
			);

		list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
		$count = MaritimoModel::listar_maritimo_provicional($clause, NULL, NULL, NULL, NULL)->count();
		list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
		$rows = MaritimoModel::listar_maritimo_provicional($clause, $sidx, $sord, $limit, $start);

        //Constructing a JSON
		$response = new stdClass();
		$response->page = $page;
		$response->total = $total_pages;
		$response->records = $count;
		$i = 0;

		if (!empty($rows->toArray())) {
			foreach ($rows->toArray() AS $i => $row) {

				if ($row['estado'] == 'Inactivo')
					$spanStyle = 'label label-danger';
				else if ($row['estado'] == 'Activo')
					$spanStyle = 'label label-successful';
				else
					$spanStyle = 'label label-warning';

				$hidden_options = "<a href='#' class='btn btn-block btn-outline btn-success linkCargaInfoMaritimo' data-int-gr='" . $row["id_intereses"] . "' data-int-id='" . $row["interesestable_id"] . "'>Ver Inter&eacute;s</a>";
				$hidden_options .= "<a class='btn btn-block btn-outline btn-success subir_documento_solicitudes_intereses' data-int-id='" . $row["interesestable_id"] . "' data-tipo-interes='casco_maritimo' >Subir Documento</a>";
				$hidden_options .= "<a href='#' class='btn btn-block btn-outline btn-success quitaInteresBtn' data-int-gr='" . $row['id_intereses'] . "'  data-id='" . $row['id_det'] . "' >Quitar Inter&eacute;s</a>";
				$link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row['id_det'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

				$response->rows[$i]["id"] = $row['id_det'];
				$response->rows[$i]["cell"] = array(
					$row["numero"],
					$row['serie'],
					$row['nombre_embarcacion'],
					$row['tipo'],
					$row['marca'],
					$row['valor'],
					$row['nombre'],
					$row['fecha_inclusion'],
					$row['fecha_exclusion'],
					"<label class='" . $spanStyle . " cambiarestadoseparado' data-id='" . $row['id_det'] . "'>" . $row['estado'] . "</label>",
					$link_option,
					$hidden_options
					);
				$i++;
			}
		}
		print_r(json_encode($response));
		exit;
	}

	public function ajax_listar_proyecto($grid = NULL) {

		$estado = $this->input->post('estado', true);

		$clause = array(
			"numero" => $this->input->post('numero', true),
			"no_orden" => $this->input->post('no_orden', true),
			"nombre_proyecto" => $this->input->post('nombre_proyecto', true),
			"ubicacion" => $this->input->post('ubicacion', true),
			"fecha_inclusion" => $this->input->post('fecha_inclusion', true),
			'empresa_id' => $this->empresa_id,
			"detalle_unico" => $this->input->post('detalle_unico')
			);

		list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
		$count = $this->ProyectoModel->listar_proyecto_provicional($clause, NULL, NULL, NULL, NULL)->count();
		list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
		$rows = $this->ProyectoModel->listar_proyecto_provicional($clause, $sidx, $sord, $limit, $start);

        //Constructing a JSON
		$response = new stdClass();
		$response->page = $page;
		$response->total = $total_pages;
		$response->records = $count;
		$i = 0;

		if (!empty($rows->toArray())) {
			foreach ($rows->toArray() AS $i => $row) {

				if ($row['estado'] == 'Inactivo')
					$spanStyle = 'label label-danger';
				else if ($row['estado'] == 'Activo')
					$spanStyle = 'label label-successful';
				else
					$spanStyle = 'label label-warning';

				$hidden_options = "<a href='#' class='btn btn-block btn-outline btn-success linkCargaInfoProyecto' data-int-gr='" . $row["id_intereses"] . "' data-int-id='" . $row["interesestable_id"] . "'>Ver Inter&eacute;s</a>";
				$hidden_options .= "<a class='btn btn-block btn-outline btn-success subir_documento_solicitudes_intereses' data-int-id='" . $row["interesestable_id"] . "' data-tipo-interes='proyecto' >Subir Documento</a>";
				$hidden_options .= "<a href='#' class='btn btn-block btn-outline btn-success quitarInteres' data-int-gr='" . $row['id_intereses'] . "'>Quitar Inter&eacute;s</a>";
                //$hidden_options .= '<a href="#" id="cambiarEtapaConfirmBtn" class="btn btn-block btn-outline btn-success">Crear Reporte de Comisión</a>';
				$link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row['id'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

				$response->rows[$i]["id"] = $row['id'];
				$response->rows[$i]["cell"] = array(
					$row["numero"],
					$row['nombre_proyecto'],
					$row['no_orden'],
					$row['ubicacion'],
					$row['fecha_inclusion'],
					$row['fecha_exclusion'],
					"<label class='" . $spanStyle . " cambiarestadoseparado' data-id='" . $row['id'] . "'>" . $row['estado'] . "</label>",
					$link_option,
					$hidden_options
					);
				$i++;
			}
		}
		print_r(json_encode($response));
		exit;
	}

	public function ajax_listar_ubicacion($grid = NULL) {

		$estado = $this->input->post('estado', true);

		$clause = array(
			"numero" => $this->input->post('numero', true),
			"nombre" => $this->input->post('nombre', true),
			"direccion" => $this->input->post('direccion', true),
			"edif_mejoras" => $this->input->post('edif_mejoras', true),
			"contenido" => $this->input->post('contenido', true),
			"maquinaria" => $this->input->post('maquinaria', true),
			"inventario" => $this->input->post('inventario', true),
			"acreedor" => $this->input->post('acreedor', true),
			'empresa_id' => $this->empresa_id,
			"detalle_unico" => $this->input->post('detalle_unico')
			);

		list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
		$count = UbicacionModel::listar_ubicacion_provicional($clause, NULL, NULL, NULL, NULL)->count();
		list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
		$rows = UbicacionModel::listar_ubicacion_provicional($clause, $sidx, $sord, $limit, $start);

        //Constructing a JSON
		$response = new stdClass();
		$response->page = $page;
		$response->total = $total_pages;
		$response->records = $count;
		$i = 0;

		if (!empty($rows->toArray())) {
			foreach ($rows->toArray() AS $i => $row) {

				if ($row['estado'] == 'Inactivo')
					$spanStyle = 'label label-danger';
				else if ($row['estado'] == 'Activo')
					$spanStyle = 'label label-successful';
				else
					$spanStyle = 'label label-warning';
				if ($row['acreedor'] != "0" ) {
					if ($row['acreedor'] != "otro") {
						$acreedor1 = $this->AcreedoresRep->id($row['acreedor'])->toArray();
						$acreedor=$acreedor1['nombre'];
					} else {
						$acreedor = ucwords($row['acreedor']);
					}

				} else {

					$acreedor ="";
				}

				$hidden_options = "<a href='#' class='btn btn-block btn-outline btn-success linkCargaInfoUbicacion' data-int-gr='" . $row["id_intereses"] . "' data-int-id='" . $row["interesestable_id"] . "'>Ver Inter&eacute;s</a>";
				$hidden_options .= "<a class='btn btn-block btn-outline btn-success subir_documento_solicitudes_intereses' data-int-id='" . $row["interesestable_id"] . "'  data-tipo-interes='ubicacion' >Subir Documento</a>";
				$hidden_options .= "<a href='#' class='btn btn-block btn-outline btn-success quitarInteres' data-int-gr='" . $row['id_intereses'] . "'>Quitar Inter&eacute;s</a>";
                //$hidden_options .= '<a href="#" id="cambiarEtapaConfirmBtn" class="btn btn-block btn-outline btn-success">Crear Reporte de Comisión</a>';
				$link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row['id'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
				$clause['tipo'] = 1;
				$clause['empresa_id'] = $this->empresa_id;
				$row['nombre_acreedor'] = $this->AcreedoresRep->get($clause);

				$response->rows[$i]["id"] = $row['id'];
				$response->rows[$i]["cell"] = array(
					$row["numero"],
					$row['nombre'],
					$row['direccion'],
					$row['edif_mejoras'],
					$row['contenido'],
					$row['maquinaria'],
					$row['inventario'],
					$acreedor,
					"<label class='" . $spanStyle . " cambiarestadoseparado' data-id='" . $row['id'] . "'>" . $row['estado'] . "</label>",
					$link_option,
					$hidden_options
					);
				$i++;
			}
		}
		print_r(json_encode($response));
		exit;
	}

	public function ajax_listar_vehiculo($grid = NULL) {

		$estado = $this->input->post('estado', true);

		$clause = array(
			"numero" => $this->input->post('numero', true),
			"detalle_certificado" => $this->input->post('detalle_certificado', true),
			"chasis" => $this->input->post('chasis', true),
			"unidad" => $this->input->post('unidad', true),
			"marca" => $this->input->post('marca', true),
			"modelo" => $this->input->post('modelo', true),
			"placa" => $this->input->post('placa', true),
			"color" => $this->input->post('color', true),
			"operador" => $this->input->post('operador', true),
			"fecha_inclusion" => $this->input->post('fecha_inclusion', true),
			"prima" => $this->input->post('prima', true),
			'empresa_id' => $this->empresa_id,
			"detalle_unico" => $this->input->post('detalle_unico')
			);

		list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
		$count = VehiculoModel::listar_vehiculo_provicional($clause, NULL, NULL, NULL, NULL)->count();
		list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
		$rows = VehiculoModel::listar_vehiculo_provicional($clause, $sidx, $sord, $limit, $start);

        //Constructing a JSON
		$response = new stdClass();
		$response->page = $page;
		$response->total = $total_pages;
		$response->records = $count;
		$i = 0;

		if (!empty($rows->toArray())) {
			foreach ($rows->toArray() AS $i => $row) {

				if ($row['estado'] == 'Inactivo')
					$spanStyle = 'label label-danger';
				else if ($row['estado'] == 'Activo')
					$spanStyle = 'label label-successful';
				else
					$spanStyle = 'label label-warning';

				$hidden_options = "<a href='#' class='btn btn-block btn-outline btn-success linkCargaInfoVehiculo' data-int-gr='" . $row["id_intereses"] . "' data-int-id='" . $row["interesestable_id"] . "'>Ver Inter&eacute;s</a>";
				$hidden_options .= "<a class='btn btn-block btn-outline btn-success subir_documento_solicitudes_intereses' data-int-id='" . $row["interesestable_id"] . "'  data-tipo-interes='vehiculo' >Subir Documento</a>";
				$hidden_options .= "<a href='#' class='btn btn-block btn-outline btn-success quitarInteres' data-int-gr='" . $row['id_intereses'] . "'>Quitar Inter&eacute;s</a>";
                //$hidden_options .= '<a href="#" id="cambiarEtapaConfirmBtn" class="btn btn-block btn-outline btn-success">Crear Reporte de Comisión</a>';
				$link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row['id'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

				$response->rows[$i]["id"] = $row['id'];
				$response->rows[$i]["cell"] = array(
					$row["numero"],
					$row['detalle_certificado'],
					$row['motor'],
					$row['unidad'],
					$row['marca'],
					$row['modelo'],
					$row['placa'],
					$row['color'],
					$row['operador'],
					$row['fecha_inclusion'],
					$row['fecha_exclusion'],
					$row['detalle_prima'],
					"<label class='" . $spanStyle . " cambiarestadoseparado' data-id='" . $row['id'] . "'>" . $row['estado'] . "</label>",
					$link_option,
					$hidden_options
					);
				$i++;
			}
		}
		print_r(json_encode($response));
		exit;
	}

	public function ajax_listar_personas($grid = NULL) {


		$estado = $this->input->post('estado', true);
		$relacion =empty($this->input->post('relacion')) ? '' : 'Principal'; 	
		$clause = array(
			"numero" => $this->input->post('numero', true),
			"nombrePersona" => $this->input->post('nombrePersona', true),
			"identificacion" => $this->input->post('identificacion', true),
			"edad" => $this->input->post('edad', true),
			"sexo" => $this->input->post('sexo', true),
			"estatura" => $this->input->post('estatura', true),
			"telefono_residencial" => $this->input->post('telefono', true),
			"created_at" => $this->input->post('fecha_inclusion', true),
			"telefono_residencial" => $this->input->post('telefono', true),
			"estado" => $this->input->post('estado', true),
			"prima" => $this->input->post('prima', true),
			'empresa_id' => $this->empresa_id,
			"detalle_unico" => $this->input->post('detalle_unico'),
			"detalle_relacion" => $relacion,
			);

		list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
		$count = PersonasModel::listar_personas_provicional($clause, NULL, NULL, NULL, NULL)->count();
		list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
		$rows = PersonasModel::listar_personas_provicional($clause, $sidx, $sord, $limit, $start);
		$parents = array();
        //Constructing a JSON
		$response = new stdClass();
		$response->page = $page;
		$response->total = $total_pages;
		$response->records = $count;
		$i = 0;
		if (!empty($rows->toArray())) {
			foreach ($rows->toArray() AS   $row) {
				array_push($parents, $row);
				$clause = array('id' => $row['id_intereses'],
					"detalle_unico" => $this->input->post('detalle_unico'));
				$child = PersonasModel::listar_personas_provicional($clause, NULL, NULL, NULL, NULL);

				if (count($child)) {
					
					foreach ($child->toArray() as $key => $value) {
						
						array_push($parents, $value);

					}
				}

			}

			foreach ($parents as $key => $row) {

                # code...	
				if ($row['estado'] == 'Inactivo')
					$spanStyle = 'label label-danger';
				else if ($row['estado'] == 'Activo')
					$spanStyle = 'label label-successful';
				else
					$spanStyle = 'label label-warning';

				$hidden_options = "<a href='#' class='btn btn-block btn-outline btn-success linkCargaInfoPersona' data-int-gr='" . $row["id_intereses"] . "' data-int-id='" . $row["interesestable_id"] . "'>Ver Inter&eacute;s</a>";
				$hidden_options .= "<a href='#' class='btn btn-block btn-outline btn-success setIndividualCoverage' data-int-gr='" . $row['id_intereses'] . "'>Coberturas</a>";
				$hidden_options .= "<a class='btn btn-block btn-outline btn-success subir_documento_solicitudes_intereses' data-int-id='" . $row["interesestable_id"] . "' data-tipo-interes='persona' >Subir Documento</a>";
				$hidden_options .= "<a href='#' class='btn btn-block btn-outline btn-success quitarInteres' data-int-gr='" . $row['id_intereses'] . "'>Quitar Inter&eacute;s</a>";


				$link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row['id_intereses'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
				$age = $row['fecha_nacimiento'];
				$year = "";
				$telefono = $row['telefono_principal'] != 'Laboral' ? $row['telefono_residencial'] : $row['telefono_oficina'];

				if (strpos($age, '-') !== false) {
					$age = explode("-", $row['fecha_nacimiento']);
					$year = Carbon::createFromDate($age[0], $age[1], $age[2])->age;
				}
				$response->rows[$key]["id"] = $row['id_intereses'];
				$response->rows[$key]["cell"] = array(
					'numero' => $row["numero"],
					'certificado' => $row["detalle_certificado"],
					'nombrePersona' => $row['nombrePersona'],
					'identificacion' => $row['identificacion'],
					'fecha_nacimiento' => $row['fecha_nacimiento'],
					'nacionalidad' => $row['nacionalidad'],
					'edad' => $year,
					'sexo' => $row['sexo'] != 1 ? "M" : "F",
					'estatura' => $row['estatura'],
					'peso' => $row['peso'],
					'telefono' => $telefono,
					'relacion' => $row['detalle_relacion'],
					'tipo_relacion' => $row['tipo_relacion'],
					'participacion' => $row['detalle_participacion'],
					'fecha_inclusion' => $row['fecha_inclusion'],
					'fecha_exclusion' => $row['fecha_exclusion'],
					'prima' => $row['detalle_prima'],
					'estado' => "<label class='" . $spanStyle . " cambiarestadoseparado' data-id='" . $row['id'] . "'>" . $row['estado'] . "</label>",
					'options' => $link_option,
					'link' => $hidden_options,
                    "level" => $row["detalle_int_asociado"] != 0 ? "1" : "0", //level
                    'parent' => $row["detalle_int_asociado"] == 0 ? "NULL" : (string) $row["detalle_int_asociado"], //parent
                    'isLeaf' => $row['detalle_int_asociado'] != 0 ? true : false, //isLeaf
                    'expanded' => false, //expended
                    'loaded' => true, //loaded
                    );
			}
		}
		print_r(json_encode($response));
		exit;
	}

	public function get_detalle_asociado() {

		$asociado = $_POST['campo'];

		try {
			$detalle = InteresesAsegurados_detalles::where('id_intereses', $asociado['id_intereses'])->where('detalle_unico', $asociado['detalle_unico'])->first();
			$detalle['Principal'] =$this->AseguradosModel
			->where('id',$detalle->detalle_int_asociado)
			->select('interesestable_id')->first();
		} catch (\Exception $e) {
			$detalle = log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
		}

		$this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
		->set_output(json_encode($detalle))->_display();
		exit;
	}

	public function delete_detalle_asociado() {

		$asociado = $_POST['campo'];
		try {
			$detalle = InteresesAsegurados_detalles::where('id_intereses', $asociado['id_intereses'])->where('detalle_unico', $asociado['detalle_unico'])->delete();
		} catch (\Exception $e) {
			$detalle = log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
		}

		$this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
		->set_output(json_encode($detalle))->_display();
		exit;
	}

	public function ajax_quitar_maritimo() {
		$id = $_POST["id_det"];

		$res = InteresesAsegurados_detalles::where("id", $id)->delete();
		if ($res) {
			$data["msg"] = "Ok";
		} else {
			$data["msg"] = "Error ";
		}
		die(json_encode($data));
	}

	public function get_detalle_prima() {

		$asociado = $_POST['campo'];
		$tipoRelacion = "";
		if (isset($_POST['persona'])) {
			$tipoRelacion = "Principal";
		}
		$clause = array(
//        "detalle_relacion" => $tipoRelacion,
			'detalle_unico' => $asociado
			);
		try {
			$detalle = InteresesAsegurados_detalles::where($clause)->sum("detalle_prima");
		} catch (\Exception $e) {
			$detalle = log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
		}

		$this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
		->set_output(json_encode($detalle))->_display();
		exit;
	}

	public function update_unico() {

		$asociado = $_POST['campos'];

		try {
			$unico = array();
			$unico['detalle_unico'] = $asociado['detalle_unico'];
			$detalle = InteresesAsegurados_detalles::where('id_solicitudes', $asociado['id_solicitudes'])->update($unico);
		} catch (\Exception $e) {
			$detalle = log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
		}

		$this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
		->set_output(json_encode($detalle))->_display();
		exit;
	}

	public function obtenerInteres() {

		$asociado = $_POST['campos'];

		try {
			$detalle = InteresesAsegurados_detalles::join('int_intereses_asegurados', 'int_intereses_asegurados.id', '=', 'int_intereses_asegurados_detalles.id_intereses')->where('int_intereses_asegurados_detalles.id_solicitudes', $asociado['id_solicitudes'])->select('int_intereses_asegurados.interesestable_id')->first();
		} catch (\Exception $e) {
			$detalle = log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
		}

		$this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
		->set_output(json_encode($detalle))->_display();
		exit;
	}

	function tabladetalles($data = array()) {
        /* $this->assets->agregar_var_js(array(
          "modulo_id" => 57,
          )); */
        //$data["campos"] = $data;
          $this->load->view('tabladetalles', $data);
      }

      function ocultotablapolizas($uuid = 0) {

      	$this->assets->agregar_js(array(
      		'public/assets/js/modules/intereses_asegurados/tablapolizas.js'
      		));
        /* $uuid = hex2bin($uuid);
          $inte = $this->AseguradosModel->where("uuid_intereses",$uuid)->first()->toArray();
          $this->assets->agregar_var_js(array(
          'id_interes' => $inte["id"],
          )); */

          $this->load->view('tablapolizas');
      }

      function ajax_listar_polizas($grid = "") {

      	$clause = array(
      		"empresa_id" => $this->empresa_id,
      		"usuario_id" => $this->usuario_id
      		);

      	if (isset($_POST["filters"])) {
      		$filt = (array) json_decode($_POST["filters"]);
      		if (isset($filt["rules"]) AND count($filt["rules"]) > 0) {
      			for ($i = 0; $i < count($filt["rules"]); $i++) {
      				$busq = (array) $filt["rules"][$i];
      				if (isset($busq["field"]) AND $busq["data"] != "") {
      					$clause[$busq["field"]] = array("like", "%" . $busq["data"] . "%");
      				}
      			}
      		}
      	}

      	$uuid = $this->input->post('id_interes', true);

      	$uuid = hex2bin($uuid);
      	$inte = $this->AseguradosModel->where("uuid_intereses", $uuid)->first()->toArray();
      	$id_interes = $inte["id"];

      	$id_solic = $this->AseguradosModel->select("int_intereses_asegurados_detalles.id_solicitudes")->join("int_intereses_asegurados_detalles", "int_intereses_asegurados_detalles.id_intereses", "=", "int_intereses_asegurados.id")->where("int_intereses_asegurados.id", $id_interes)->get()->toArray();

      	$ids = array();
      	foreach ($id_solic as $value) {
      		if ((INT) $value["id_solicitudes"] > 0) {
      			$ids[] = $value["id_solicitudes"];
      		}
      	}

      	if (count($ids) == 0) {
      		exit();
      	}
      	$clause["ids_sol"] = $ids;
      	list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
      	$count = $this->interesesAseguradosRep->listar_polizas($clause, NULL, NULL, NULL, NULL)->count();
      	list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
      	$rows = $this->interesesAseguradosRep->listar_polizas($clause, $sidx, $sord, $limit, $start);

        //Constructing a JSON
      	$response = new stdClass();
      	$response->page = $page;
      	$response->total = $total_pages;
      	$response->records = $count;
      	$i = 0;
      	$rows = (object) $rows;
      	if (!empty($rows)) {
      		foreach ($rows AS $i => $row) {
      			$estado_color = $row->estado == "Por Facturar" ? 'background-color: #F0AD4E' : ($row->estado == "Facturada" ? 'background-color: #5cb85c' : ($row->estado == "Cancelada" ? 'background-color: #222222' : ($row->estado == "Expirada" ? 'background-color: #FC0D1B' : 'background-color: #00BFFF')));

      			$hidden_options = "<a href=" . base_url('polizas/editar/' . strtoupper(bin2hex($row->uuid_polizas))) . " class='viewOptions btn btn-block btn-outline btn-success'>Ver Póliza</a>";


      			$linkEstado = '';
      			if ($row->estado != "Por Facturar") {
      				$linkEstado .= '<a href="#" class="viewOptions btn btn-block btn-warning" data-id="' . $row->id . '" data-estado="Por Facturar">Por Facturar</a>';
      			}
      			if ($row->estado != "Facturada") {
      				$linkEstado .= '<a href="#" class="viewOptions btn btn-block btn-primary" data-id="' . $row->id . '" data-estado="Facturada">Facturada</a>';
      			}
      			if ($row->estado == "Facturada" OR $row->estado == "Expirada") {
      				$linkEstado .= '<a href="#" class="viewOptions btn btn-block btn-info" data-id="' . $row->id . '" data-estado="No Renovada">No Renovar</a>';
      			}

      			$link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row->id . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
      			$response->rows[$i]["id"] = $row->id;
      			$response->rows[$i]["cell"] = array(
      				$row->id,
      				"<a href='" . base_url('polizas/editar/' . strtoupper(bin2hex($row->uuid_polizas))) . "'>" . $row->numero . "</a>",
      				strtoupper($row->nombre_cliente),
      				$row->nombre_aseguradora,
      				$row->ramo,
      				$row->inicio_vigencia,
      				$row->fin_vigencia,
      				'<span class="btn btn-block btn-xs estadoPoliza" style="color:white;' . $estado_color . '; width: 80px;" data-id="' . $row->id . '">' . $row->estado . '</span>',
      				$link_option,
      				$hidden_options,
      				$linkEstado
      				);
      		}
      	}

      	echo json_encode($response);
      	exit;
      }

      function exportarPolizasIntereses() {
      	if (empty($_POST)) {
      		exit();
      	}

      	$ids = $this->input->post('ids', true);

      	$csv = array();
      	$clause = array(
      		"empresa_id" => $this->empresa_id
      		);

      	$id = explode(",", $ids);
      	if (empty($id)) {
      		return false;
      	}

      	$clause['id'] = $id;

      	$polizas = $this->interesesAseguradosRep->exportarPolizasIntereses($clause, NULL, NULL, NULL, NULL);
      	if (empty($polizas)) {
      		return false;
      	}
      	$i = 0;
      	foreach ($polizas AS $row) {

            //$csvdata[$i]['id'] = $row->id;
      		$csvdata[$i]["numero"] = utf8_decode($row->numero);
      		$csvdata[$i]["cliente"] = utf8_decode($row->clientefk->nombre);
      		$csvdata[$i]["aseguradora"] = utf8_decode($row->aseguradorafk->nombre);
      		$csvdata[$i]["ramo"] = utf8_decode($row->ramo);
      		$csvdata[$i]["inicio_vigencia"] = $row->inicio_vigencia;
      		$csvdata[$i]["fin_vigencia"] = $row->fin_vigencia;
      		$csvdata[$i]["estado"] = utf8_decode($row->estado);

      		$i++;
      	}
        //we create the CSV into memory
      	$csv = Writer::createFromFileObject(new SplTempFileObject());
      	$csv->insertOne([
      		utf8_decode('N° Póliza'),
      		'Cliente',
      		'Aseguradora',
      		'Ramo',
      		'Inicio Vigencia',
      		'Fin Vigencia',
      		'Estado',
      		]);
      	$csv->insertAll($csvdata);
      	$csv->output("polizasIntereses-" . date('ymd') . ".csv");
      	exit();
      }

  }

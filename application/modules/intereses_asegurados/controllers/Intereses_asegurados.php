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
 * @link       http://www.pensanomca.com
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
use Flexio\Modulo\InteresesAsegurados\Models\InteresesAsegurados_cat as InteresesAsegurados_catModel;
use Flexio\Modulo\SegCatalogo\Models\SegCatalogo as SegCatalogosModel;
use Flexio\Modulo\Acreedores\Repository\AcreedoresRepository as AcreedoresRep;
use Flexio\Modulo\SegCatalogo\Repository\SegCatalogoRepository as SegCatalogoRepository;
use Flexio\Modulo\InteresesAsegurados\Models\InteresesPersonas as PersonasModel;
use Flexio\Modulo\InteresesAsegurados\Models\CargaAsegurados as CargaModel;
use Flexio\Modulo\SegInteresesAsegurados\Repository\SegInteresesAseguradosRepository as SegInteresesAseguradosRepository;

class Intereses_asegurados extends CRM_Controller {

	private $empresa_id;
	private $id_usuario;
	private $AseguradosModel;
	private $VehiculoModel;
	private $ProyectoModel;
	private $InteresesAsegurados_catModel;
	private $interesesAseguradosRep;
	private $SegCatalogosModel;
	private $AcreedoresRep;
	private $SegCatalogoRepository;
	private $PersonasModel;
	private $CargaModel;
	private $SegInteresesAseguradosRepository;
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

        //flexio
		$this->interesesAseguradosRep = new interesesAseguradosRep();
		$this->AseguradosModel = new AseguradosModel();
		$this->VehiculoModel = new VehiculoModel();
		$this->ProyectoModel = new ProyectoModel();
		$this->InteresesAsegurados_catModel = new InteresesAsegurados_catModel();
		$this->SegCatalogoRepository = new SegCatalogoRepository();
		$this->AcreedoresRep = new AcreedoresRep();
		$this->PersonasModel = new PersonasModel();
		$this->CargaModel = new CargaModel();
		$this->SegInteresesAseguradosRepository = new SegInteresesAseguradosRepository();
		$this->load->module(array('documentos'));
	}

	public function listar() {

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

		if ($this->auth->has_permission('acceso', 'intereses_asegurados/crear')) {
			$breadcrumb["menu"] = array(
				"url" => 'javascript:',
				"clase" => 'crearBoton',
				"nombre" => "Crear"
				);
		}

		$menuOpciones = array();

		if ($this->auth->has_permission('listar__exportarInteresesAsegurados', 'intereses_asegurados/listar')){
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
				$link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row['id'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
				$estado = $row->estado === "Activo" ? "Activo" : "Inactivo";
				$labelClass = $row->estado === "Activo" ? "successful" : "danger";
				$negativeState = $row->estado != "Activo" ? "Activar" : "Desactivar";
				$url=base_url("intereses_asegurados/editar/$uuid_intereses");
				$modalstate = '<a href="javascript:" data-id="' . $row['id'] . '" class="btn btn-block btn-outline btn-success massive">' . $negativeState . '</a>';
				$hidden_options = '<a href="'.$url.'" data-id="' . $row['id'] . '" class="btn btn-block btn-outline btn-success editarInteres" >Ver interés asegurado</a>';
				$hidden_options .= '<a href="javascript:" data-id="' . $row['interesestable_id'] . '" class="btn btn-block btn-outline btn-success subir_archivo_intereses" data-type="'.$row->interesestable_type.'" >Subir Archivo</a>';
				$redirect = "<a style='text-decoration: underline' href=" .$url. ">$row->numero</a>";
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
					"massState"  => $estado
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

	public function crear($formulario=NULL) {		
		$acceso = 1;
		$mensaje = array();

		if(!$this->auth->has_permission('acceso','Intereses_asegurados/crear')){
				// No, tiene permiso, redireccionarlo.
			$acceso = 0;
			$mensaje = array('tipo'=>"error", 'mensaje'=>'<b>¡Error!</b> No tiene permisos para crear intereses asegurados' ,'titulo'=>'Intereses asegurados ');
			$this->session->flashdata('mensaje',$mensaje);
			redirect(base_url('Intereses_asegurados/listar'));
		}

		$this->_Css();   
		$this->_js();
		
		$this->assets->agregar_js(array(       
			'public/assets/js/modules/intereses_asegurados/formulario.js',   
			'public/assets/js/modules/intereses_asegurados/crear.js',
		 //'public/assets/js/default/vue-validator.min.js',	 
			));

		$data=array();      
		
		if($formulario != NULL){
			$this->assets->agregar_var_js(array(
				"formulario_seleccionado" => $formulario,
				"vista" => "crear"
				));
		}
		
		if(!empty($_POST))
		{
            //Se recibe el parámetro y se usa para buscar el controlador del interés asegurado
			$var=ucfirst($formulario)."_orm";
			if($var::create($this->input->post("campo"))){
				redirect(base_url('intereses_asegurados/listar'));
			}else{
                //Establecer el mensaje a mostrar
				$data["mensaje"]["clase"] = "alert-danger";
				$data["mensaje"]["contenido"] = "Hubo un error al tratar de crear el pedido.";
			}
		}
		
		$data["campos"] = array(
			"campos" => array(
				"tipos_intereses_asegurados" => $this->InteresesAsegurados_catModel->get(),
				)
			);

		$breadcrumb = array(
			"titulo" => '<i class="fa fa-archive"></i> Intereses Asegurados: Crear',
			"ruta" => array(
				0 => array("nombre" => "Seguros", "url" => "#",  "activo" => false),
				1 => array("nombre" => "Intereses Asegurados", "url" => "intereses_asegurados/listar",  "activo" => false),
				2 => array("nombre" => '<b>Crear</b>', "activo" => true)
				),
			"filtro"    => false,
			"menu"      => array()
			);
		
		$this->template->agregar_titulo_header('Intereses Asegurados');
		$this->template->agregar_breadcrumb($breadcrumb);
		$this->template->agregar_contenido($data);
		$this->template->visualizar($breadcrumb);
		
	}

	public function vehiculoformularioparcial($data = array()) {
		$this->assets->agregar_js(array(
			'public/assets/js/modules/intereses_asegurados/crear_vehiculo.js'
			));
		if(empty($data))
		{
			$data["campos"] = array();
		}
        //persona
		$data['uso'] = $this->SegCatalogoRepository->listar_catalogo('uso_vehiculo','orden');
		$data['condicion'] = $this->SegCatalogoRepository->listar_catalogo('condicion_vehiculo','orden');
		$clause['empresa_id'] = $this->empresa_id;
		$clause['tipo_id'] = 1;
		$data['acreedores'] = $this->AcreedoresRep->get($clause);
		$data['estado'] = $this->SegCatalogoRepository->listar_catalogo('estado2','orden');
		
		$this->load->view('formulariovehiculo', $data);
	}


	function guardar_vehiculo() {
		if($_POST){
			unset($_POST["campo"]["guardar"]);
			$campo = Util::set_fieldset("campo");
			if(!isset($campo['uuid'])){
				$campo['empresa_id'] = $this->empresa_id;
			}
			Capsule::beginTransaction();
			try {
				if(empty($campo['uuid']))
				{
					$campo["uuid_vehiculo"] = Capsule::raw("ORDER_UUID(uuid())");
					$campo["empresa_id"] = $this->empresa_id;
					
				//$campo["estado"] = "activo";
					$clause['empresa_id'] = $this->empresa_id;
					$total = $this->interesesAseguradosRep->listar_vehiculo($clause);
					$vehiculo = $this->VehiculoModel->create($campo);
					
				//guardar tabla principal
					$codigo = Util::generar_codigo('VEH' ,$vehiculo->id);
					$fieldset["numero"] = $codigo;
					$fieldset['uuid_intereses'] = $vehiculo->uuid_vehiculo;
					$fieldset['empresa_id'] = $vehiculo->empresa_id;
					$fieldset['interesestable_type'] = 8;
					$fieldset['interesestable_id'] = $vehiculo->id;
					$fieldset['numero'] = $codigo;
					$fieldset['identificacion'] = $vehiculo->chasis;
					$fieldset['estado'] = $vehiculo->estado;
					$fieldset['updated_at'] = $vehiculo->updated_at;
					$fieldset['created_at'] = $vehiculo->created_at;
					$fieldset['creado_por']=$this->session->userdata['id_usuario'];
					$interesase=$this->AseguradosModel->create($fieldset);
					
				//Subir documentos
					if(!empty($_FILES['file'])){
						$vehiculo_id = $interesase->id;
					//var_dump($interesase->id);
						unset($_POST["campo"]);
						$modeloInstancia = $this->VehiculoModel->find($vehiculo->id);
						$this->documentos->subir($modeloInstancia);
					}
				}
				else{
					$intereses_asegurados = $this->interesesAseguradosRep->verInteresAsegurado(hex2bin(strtolower($campo['uuid'])));
					
					$vehiculoObj  = $this->VehiculoModel->find($intereses_asegurados->vehiculo->id);
					$vehiculoObj->update($campo);
					
					$intereses_asegurados->identificacion=$vehiculoObj->chasis;
					$intereses_asegurados->estado=$vehiculoObj->estado;
					
					$intereses_asegurados->save();
					
				//Tabla principal
				/*$intereses_asegurados = $this->AseguradosModel->findByInteresesTable($vehiculoObj->id, $vehiculoObj->tipo_id);
				$fieldset['identificacion'] = $intereses_asegurados->chasis;
				$fieldset['estado'] = $intereses_asegurados->estado;
				$intereses_asegurados->update($fieldset);*/
				
				/*//Subir documentos
				if(!empty($_FILES['file'])){
					$vehiculo_id = $vehiculoObj->id;
					unset($_POST["campo"]);
					$modeloInstancia = $this->VehiculoModel->find($vehiculo_id);
					
					$this->documentos->subir($modeloInstancia);
				}*/
			}
			Capsule::commit();
		}catch(ValidationException $e){
			log_message('error', $e);
			Capsule::rollback();
		}

		if(!is_null($vehiculo)){
			$mensaje = array('estado' => 200, 'mensaje' =>'<b>¡&Eacute;xito!</b> Se ha guardado correctamente');

		}
		else{
			$mensaje = array('class' =>'alert-danger', 'contenido' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
		}
	}
	else
	{
		$mensaje = array('class' =>'alert-warning', 'contenido' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
	}

	$this->session->set_flashdata('mensaje', $mensaje);
	redirect(base_url('intereses_asegurados/listar'));
}

function ajax_check_vehiculo() {

	$chasis = $this->input->post("chasis");
	
	if($this->input->post("uuid")!="")
	{
		$uuid = hex2bin(strtolower($this->input->post("uuid")));
		$intereses_asegurados = $this->interesesAseguradosRep->verInteresAsegurado($uuid);
		
		$vehiculo=$this->VehiculoModel->find($intereses_asegurados->vehiculo->id);
		
		$chasis_obj = $this->interesesAseguradosRep->identificacionUuid($chasis,$vehiculo->id);
		
		if (empty($chasis_obj)) {
			echo('USER_AVAILABLE');
		} else {
			echo('USER_EXISTS');
		}
	}
	else
	{
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

	if ($this->auth->has_permission('editar-carga__cambiarEstado', 'intereses_asegurados/editar-carga') ==  true) {
		$data['cambiarEstado'] = 1;
	}else{
		$data['cambiarEstado'] = 0;
	}
	
        //carga
	$clause['empresa_id'] = $this->empresa_id;
	$clause['tipo'] = 1;

	$data['tipo_empaque'] = $this->SegInteresesAseguradosRepository->listar_catalogo('tipo_empaque', 'orden');
	$data['condicion_envio'] = $this->SegInteresesAseguradosRepository->listar_catalogo('condicion_envio', 'orden');
	$data['medio_transporte'] = $this->SegInteresesAseguradosRepository->listar_catalogo('medio_transporte', 'orden');
	$data['tipo_obligacion'] = $this->SegInteresesAseguradosRepository->listar_catalogo('tipo_obligacion', 'orden');

	$data['acreedores'] = $this->AcreedoresRep->get($clause);
	$data['estado'] = $this->SegCatalogoRepository->listar_catalogo('estado2', 'orden');

	$this->load->view('formulariocarga', $data);
}

public function personaformularioparcial($data = array()) {
	$this->assets->agregar_js(array(
		'public/assets/js/modules/intereses_asegurados/crear_vehiculo.js'
		));
	if(empty($data))
	{
		$data["campos"] = array();
	}
        //persona
	$data['tipo_identificacion'] = $this->SegInteresesAseguradosRepository->listar_catalogo('Documento_Identificacion','orden');
	$data['info'] = array('letras' => $this->SegInteresesAseguradosRepository->listar_catalogo('Letra','orden'),
		'provincias' => $this->SegInteresesAseguradosRepository->listar_catalogo('Provincias','orden'));
	$data['estado_civil'] = $this->SegInteresesAseguradosRepository->listar_catalogo('Estado Civil','orden');
	$data['sexo'] = $this->SegInteresesAseguradosRepository->listar_catalogo('Sexo','orden');
	$clause['empresa_id'] = $this->empresa_id;
	$clause['tipo_id'] = 1;
	$data['acreedores'] = $this->AcreedoresRep->get($clause);
	$data['estado'] = $this->SegCatalogoRepository->listar_catalogo('estado2','orden');

	$this->load->view('formulariopersona', $data);
}

function ajax_check_persona() {

	$identificacion   = $this->input->post("identificacion");
	$identificacion_obj = $this->interesesAseguradosRep->identificacion_persona($identificacion);
	if(empty($identificacion_obj)){
		echo('USER_AVAILABLE');
	}else{
		echo('USER_EXISTS');
	}
}

function ajax_check_carga() {

	$no_liquidacion   = $this->input->post("no_liquidacion");
	$liquidacion_obj = $this->interesesAseguradosRep->identificacion_carga($no_liquidacion);
	if(empty($liquidacion_obj)){
		echo('USER_AVAILABLE');
	}else{
		echo('USER_EXISTS');
	}
}

function guardar_carga() {

	if($_POST){
		unset($_POST["campo"]["guardar"]);
		$campo = Util::set_fieldset("campo");
		if(!isset($campo['uuid'])){
			$campo['empresa_id'] = $this->empresa_id;
		}
		Capsule::beginTransaction();
		try {
			$campo['acreedor'] = !empty($campo['acreedor_opcional']) ? $campo['acreedor_opcional'] : $campo['acreedor'];
			$campo['tipo_obligacion'] = !empty($campo['tipo_obligacion_opcional']) ? $campo['tipo_obligacion_opcional'] : $campo['tipo_obligacion'];
			if(empty($campo['uuid'])){
				$clause['empresa_id'] = $this->empresa_id;
				$total = $this->interesesAseguradosRep->listar_carga($clause);
				$codigo = Util::generar_codigo('CGA' , count($total) + 1);
				$campo["numero"] = $codigo;
				$campo["fecha_despacho"] = !empty($campo['fecha_despacho']) ? $campo['fecha_despacho'] : NULL;
				$campo["fecha_arribo"] = !empty($campo['fecha_arribo']) ? $campo['fecha_arribo'] : NULL;
				$carga = $this->CargaModel->create($campo);
    //guardar tabla principal
				$fieldset['uuid_intereses'] = Capsule::raw("ORDER_UUID(uuid())");
				$fieldset['empresa_id'] = $carga->empresa_id;
				$fieldset['interesestable_type'] = 2;
				$fieldset['interesestable_id'] = $carga->id;
				$fieldset['numero'] = $codigo;
				$fieldset['identificacion'] = $carga->no_liquidacion;
				if ($campo['estado']==1) { $est="Activo"; }else{ $est="Inactivo"; }
				$fieldset['estado'] = $est;
				$fieldset['creado_por']=$this->session->userdata['id_usuario'];
				$carga->interesesAsegurados()->create($fieldset);
    //Subir documentos
				if(!empty($_FILES['file'])){
					$carga_id = $carga->id;
					unset($_POST["campo"]);
					$modeloInstancia = $this->CargaModel->find($carga_id);
					$this->documentos->subir($modeloInstancia);
				}
			}else{
 //dd($_POST);
				$cargaInt = AseguradosModel::findByUuid($campo['uuid']);
				$cargaObj  = CargaModel::find($cargaInt->interesestable_id);
				unset($campo['uuid']);
				if(!empty($campo['fecha_despacho']) || !empty($campo['fecha_arribo'])){
					unset($campo['fecha_despacho']);
				}
				$campo["fecha_arribo"] = !empty($campo['fecha_arribo']) ? $campo['fecha_arribo'] : NULL;
                   //$cargaObj->update($campo);
				$actCarga = $this->CargaModel->where('id', $cargaInt->interesestable_id)->update($campo);
    //Tabla principal
				$intereses_asegurados = $this->AseguradosModel->findByInteresesTable($cargaObj->id, $cargaObj->tipo_id);
				$fieldset['identificacion'] = $cargaObj->no_liquidacion;
				if ($campo['estado']==1) { $est="Activo"; }else{ $est="Inactivo"; }
				$fieldset['estado'] = $est;
                   //$intereses_asegurados->update($fieldset);
				$intase = $this->AseguradosModel->where('id', $intereses_asegurados->id)->update($fieldset);
    //Subir documentos
                   /*if(!empty($_FILES['file'])){
                        $vehiculo_id = $cargaObj->id;
                        unset($_POST["campo"]);
                        $modeloInstancia = $this->CargaModel->find($vehiculo_id);
                        $this->documentos->subir($modeloInstancia);
                    }*/
                }
                Capsule::commit();
            }catch(ValidationException $e){
            	log_message('error', $e);
            	Capsule::rollback();
            }

            if(!is_null($carga) || !is_null($cargaObj)){
            	$mensaje = array('tipo' => "success", 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente', 'titulo' => 'Carga ' . $_POST["campo"]["no_liquidacion"]);
            }else{
            	$mensaje = array('tipo' => "error", 'mensaje' => '<b>¡&Eacute;rror!</b> Su solicitud no fue procesada', 'titulo' => 'Carga ' . $_POST["campo"]["no_liquidacion"]);
            }


        }else{
        	$mensaje = array('tipo' => "error", 'mensaje' => '<b>¡&Eacute;rror!</b> Su solicitud no fue procesada', 'titulo' => 'Carga ' . $_POST["campo"]["no_liquidacion"]);
        }

        $this->session->set_flashdata('mensaje', $mensaje);
        redirect(base_url('intereses_asegurados/listar'));

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
    		if (!isset($campo['uuid'])) {
    			$campo['empresa_id'] = $this->empresa_id;
    		}
    		Capsule::beginTransaction();
    		try {


    			if (empty($campo['uuid'])) {
    				$campo['acreedor'] = !empty($campo['acreedor_opcional']) ? $campo['acreedor_opcional'] : $campo['acreedor'];
    				$campo['tipo_propuesta'] = !empty($campo['tipo_propuesta_opcional']) ? $campo['tipo_propuesta_opcional'] : $campo['tipo_propuesta'];
    				$campo['validez_fianza_pr'] = !empty($campo['validez_fianza_opcional']) ? $campo['validez_fianza_opcional'] : $campo['validez_fianza_pr'];
    				$duplicado = $campo['no_orden'];
    				$verificar_proyecto = count($this->interesesAseguradosRep->consultaOrden($duplicado));
    				if ($verificar_proyecto == 0) {
    					$campo["uuid_proyecto"] = Capsule::raw("ORDER_UUID(uuid())");

    					$clause['empresa_id'] = $this->empresa_id;
    					$total = $this->interesesAseguradosRep->listar_intereses_asegurados($clause);
    					$codigo = Util::generar_codigo('PRO', count($total) + 1);
    					$campo["numero"] = $codigo;

    					$proyecto_actividad = $this->ProyectoModel->create($campo);
                        //guardar tabla principal
    					$fieldset['uuid_intereses'] = $proyecto_actividad->uuid_proyecto;
    					$fieldset['empresa_id'] = $proyecto_actividad->empresa_id;
    					$fieldset['interesestable_type'] = 6;
    					$fieldset['interesestable_id'] = $proyecto_actividad->id;
    					$fieldset['numero'] = $codigo;
    					$fieldset['identificacion'] = $proyecto_actividad->no_orden;
    					$fieldset["creado_por"] = $this->session->userdata['id_usuario'];
    					$fieldset['estado'] = $proyecto_actividad->estado;
    					$proyecto_actividad->interesesAsegurados()->create($fieldset);

                        //Subir documentos
    					if (!empty($_FILES['file'])) {
                            //    $proyecto_id = $proyecto_actividad->id;
                            //    unset($_POST["campo"]);
                            //    $modeloInstancia = $this->ProyectoModel->find($proyecto_id);
                            //    $this->documentos->subir($modeloInstancia);
    					}
    				} else {
    					$proyecto_actividad = "";
    					$mensaje = array('tipo' => "error", 'mensaje' => '<b>¡&Eacute;rror!</b> Registro ya existe', 'titulo' => 'Intereses Asegurados ' . $_POST["campo"]["nombre_proyecto"]);
    					$this->session->set_flashdata('mensaje', $mensaje);
    					redirect(base_url('intereses_asegurados/listar'));
    				}
    			} else {
    				$intereses_asegurados = $this->interesesAseguradosRep->verInteresAsegurado(hex2bin(strtolower($campo['uuid'])));

    				$proyectoObj = $this->ProyectoModel->find($intereses_asegurados->proyecto_actividad->id);
    				$proyectoObj->update($campo);

    				$intereses_asegurados->identificacion = $proyectoObj->no_orden;
    				$intereses_asegurados->estado = $proyectoObj->estado;

    				$intereses_asegurados->save();
//                    $proyectoObj = $this->ProyectoModel->find($campo['uuid']);
//                    unset($campo['uuid']);
//                    $proyectoObj->update($campo);
//                    //Tabla principal
//                    $intereses_asegurados = $this->AseguradosModel->findByInteresesTable($proyectoObj->id, $proyectoObj->tipo_id);
//                    $fieldset['identificacion'] = $proyectoObj->nombre_proyecto;
//                    $fieldset['estado'] = $proyectoObj->estado;
//                    $intereses_asegurados->update($fieldset);
                    //Subir documentos
    				if (!empty($_FILES['file'])) {
    					$vehiculo_id = $proyectoObj->id;
    					unset($_POST["campo"]);
    					$modeloInstancia = $this->ProyectoModel->find($vehiculo_id);
    					$this->documentos->subir($modeloInstancia);
    				}
    			}

    			Capsule::commit();
    		} catch (ValidationException $e) {
    			log_message('error', $e);
    			Capsule::rollback();
    		}

    		if (!is_null($proyecto_actividad) || !is_null($proyectoObj)) {
    			$mensaje = array('tipo' => "success", 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente', 'titulo' => 'Intereses Asegurados' . $_POST["campo"]["nombre_proyecto"]);
    		} else {
    			$mensaje = array('tipo' => "error", 'mensaje' => '<b>¡&Eacute;rror!</b> Su solicitud no fue procesada', 'titulo' => 'Intereses Asegurados ' . $_POST["campo"]["nombre_proyecto"]);
    		}
    	} else {
    		$mensaje = array('tipo' => "error", 'mensaje' => '<b>¡&Eacute;rror!</b> Su solicitud no fue procesada', 'titulo' => 'Intereses Asegurados ' . $_POST["campo"]["nombre_proyecto"]);
    	}

    	$this->session->set_flashdata('mensaje', $mensaje);
    	redirect(base_url('intereses_asegurados/listar'));
    }

    function editar($uuid = NULL, $opcion = NULL) {

    	if (!is_null($this->session->flashdata('mensaje'))) {
    		$mensaje = $this->session->flashdata('mensaje');
    	} else {
    		$mensaje = [];
    	}

    	//if (!$this->auth->has_permission('acceso', 'intereses_asegurados/ver/(:any)') && !$this->auth->has_permission('acceso', 'aseguradoras/ver')) {
    	if (!$this->auth->has_permission('acceso', 'intereses_asegurados/ver/(:any)') ) {
            // No, tiene permiso, redireccionarlo.
    		$mensaje = array('tipo' => "error", 'mensaje' => '<b>¡Error!</b> Usted no tiene permisos para ingresar a editar', 'titulo' => 'Intereses Asegurados ');

    		$this->session->set_flashdata('mensaje', $mensaje);

    		redirect(base_url('intereses_asegurados/listar'));
    	}

    	$this->_Css();
    	$this->_js();

    	$data = array();

    	if ($uuid == "")
    		$uuid_interes_asegurado = $_POST["campo"]["uuid"];
    	else
    		$uuid_interes_asegurado = $uuid;

    	$intereses_asegurados = $this->interesesAseguradosRep->verInteresAsegurado(hex2bin(strtolower($uuid_interes_asegurado)));

    	if (!is_null($intereses_asegurados->persona) && $intereses_asegurados->interesestable_type == 5) {
    		$intereses_data = $intereses_asegurados->persona;
    		$identificaciones = preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $intereses_asegurados->identificacion) ? "111" : "112";
    		$tipo_formulario = 5;
    	}
    	if (!is_null($intereses_asegurados->articulo) && $intereses_asegurados->interesestable_type == 1) {
    		$intereses_data = $intereses_asegurados->articulo;
    		$tipo_formulario = 1;
    	}
    	if (!is_null($intereses_asegurados->ubicacion) && $intereses_asegurados->interesestable_type == 7) {
    		$intereses_data = $intereses_asegurados->ubicacion;
    		$tipo_formulario = 7;
    	}
    	if (!is_null($intereses_asegurados->carga) && $intereses_asegurados->interesestable_type == 2) {
    		$intereses_data = $intereses_asegurados->carga;
    		$tipo_formulario = 'carga';
    		$tipo=2;
    	}
    	if (!is_null($intereses_asegurados->vehiculo) && $intereses_asegurados->interesestable_type == 8) {
    		$intereses_data = $intereses_asegurados->vehiculo;
    		$tipo_formulario = 'vehiculo';
    		$tipo=8;
            //$intereses_data->uuid_vehiculo = $uuid;
    	}
    	if (!is_null($intereses_asegurados->casco_aereo) && $intereses_asegurados->interesestable_type == 3) {
    		$intereses_data = $intereses_asegurados->casco_aereo;
    		$tipo_formulario = 3;
    	}
    	if (!is_null($intereses_asegurados->casco_maritimo) && $intereses_asegurados->interesestable_type == 4) {
    		$intereses_data = $intereses_asegurados->casco_maritimo;
    		$tipo_formulario = 4;
    	}
    	if (!is_null($intereses_asegurados->proyecto_actividad) && $intereses_asegurados->interesestable_type == 6) {
    		$intereses_data = $intereses_asegurados->proyecto_actividad;
    		$tipo_formulario = 'proyecto_actividad';
    		$tipo=6;
    	}

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

    	$this->assets->agregar_var_js(array(
    		"formulario_seleccionado" => $intereses_asegurados->tipo->valor,
    		"intereses_asegurados_id_" . $intereses_asegurados->tipo->valor => $intereses_data->id,
    		));

    	$breadcrumb["menu"] = array(
    		"url" => 'javascript:',
    		"clase" => 'crearAccion',
    		"nombre" => "Acción "
    		);

    	$menuOpciones = array();
    	$menuOpciones["#crearSolicitudLnk"] = "Crear Solicitud";
    	$menuOpciones["#imprimirLnk"] = "Imprimir";
    	$menuOpciones["#subirDocumentoLnk"] = "Subir Documento";

    	$breadcrumb["menu"]["opciones"] = $menuOpciones;

    	$data["campos"] = array(
    		"campos" => array(
    			"tipos_intereses_asegurados" => $this->InteresesAsegurados_catModel->get(),
    			"datos" => $intereses_data,
    			"tipoformulario" => $tipo_formulario,
    			"uuid" => $uuid,
    			"tipo"=>$tipo,
    			"id"=>$intereses_data->id,
    			"estado"=>$intereses_asegurados->estado
    			),
    		);

    	$this->template->agregar_titulo_header('Aseguradoras');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar();
    }

    public function imprimirFormulario($uuid=null)
    {
    	if($uuid==null){
    		return false;
    	}

    	$intereses_asegurados = $this->interesesAseguradosRep->verInteresAsegurado(hex2bin(strtolower($uuid)));
    	$nombre= $intereses_asegurados->numero;

    	if($_GET['tipo']==8)
    	{
    		$formulario="formularioVehiculo";
    	}  else if ($_GET['tipo'] == 6) {
    		$formulario = "formularioProyecto";
    	}

    	$data   = ['datos'=>$intereses_asegurados];
    	$dompdf = new Dompdf();
    	$html = $this->load->view('pdf/'.$formulario, $data,true);
    	$dompdf->loadHtml($html);
    	$dompdf->setPaper('A4', 'portrait');
    	$dompdf->render();
    	$dompdf->stream($nombre);
    }

    public function formularioModal($data=NULL) {

    	$this->assets->agregar_js(array(
      		//'public/assets/js/modules/documentos/formulario.controller.js'
    		));

    	$this->load->view('formularioModalDocumento', $data);
    }

    function ajax_guardar_documentos() {
    	if(empty($_POST)){
    		return false;
    	}

    	$intereses_id = $this->input->post('id', true);
    	$uuid = $this->input->post('uuid_interes', true);
    	$intereses_type = $this->input->post('intereses_type', true);

    	if($intereses_type == 1){
    		$modeloInstancia = $this->ArticulomoModel->find($intereses_id);
    	}
    	if($intereses_type == 2){
    		$modeloInstancia = $this->CargaModel->find($intereses_id);
    	}
    	if($intereses_type == 3){
    		$modeloInstancia = $this->AereoModel->find($intereses_id);
    	}
    	if($intereses_type == 4){
    		$modeloInstancia = $this->MaritimoModel->find($intereses_id);
    	}
    	if($intereses_type == 5){
    		$modeloInstancia = $this->PersonasModel->find($intereses_id);
    	}
    	if($intereses_type == 6){
    		$modeloInstancia = $this->ProyectoModel->find($intereses_id);
    	}
    	if($intereses_type == 7){
    		$modeloInstancia = $this->UbicacionmoModel->find($intereses_id);
    	}
    	if($intereses_type == 8){
    		$modeloInstancia = $this->VehiculoModel->find($intereses_id);
    	}
    	$this->documentos->subir($modeloInstancia);

    	redirect(base_url('intereses_asegurados/editar/'.$uuid));

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
    	if(empty($_POST)){
    		exit();
    	}
    	$ids =  $this->input->post('ids', true);
    	$id = explode(",", $ids);

    	if(empty($id)){
    		return false;
    	}
    	$csv = array();

    	$clause['id'] = $id;

    	$contactos = $this->interesesAseguradosRep->listar_intereses_asegurados($clause, NULL, NULL, NULL, NULL);
    	if(empty($contactos)){
    		return false;
    	}
    	$i=0;
    	foreach ($contactos AS $row)
    	{
    		$csvdata[$i]['numero'] = $row->numero;
    		$csvdata[$i]["interesestable_type"] = utf8_decode(Util::verificar_valor($row->tipo->etiqueta));
    		$csvdata[$i]["identificacion"] = utf8_decode(Util::verificar_valor($row->identificacion));
    		$csvdata[$i]["estado"] = utf8_decode(Util::verificar_valor($row->estado));
    		$i++;
    	}
        //we create the CSV into memory
    	$csv = Writer::createFromFileObject(new SplTempFileObject());
    	$csv->insertOne([
    		'No. de interés asegurado',
    		'Tipo de interés',
    		'Identiicación',
    		'Estado',
    		]);

    	$csv->insertAll($csvdata);
    	$csv->output("InteresesAsegurados-". date('y-m-d') .".csv");
    	exit();
    }
    public function ajax_cambiar_estado_intereses(){

    	$FormRequest = new Flexio\Modulo\InteresesAsegurados\Models\GuardarInteresesAseguradosEstados;

    	try {
    		$msg= $Agentes = $FormRequest->guardar(); 

    	} catch (\Exception $e) {
    		$msg= log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
    	}

    	print json_encode($msg);
    	exit;
    }

    function documentos_campos() {

    	return array(
    		array(
    			"type"		=> "hidden",
    			"name" 		=> "cliente_id",
    			"id" 		=> "cliente_id",
    			"class"		=> "form-control",
    			"readonly"	=> "readonly",
    			));
    }

  public  function guardar() {

    	if($_POST){
    		unset($_POST["campo"]["guardar"]);
    		$campo = Util::set_fieldset("campo");
    //formato de identificacion

    		if(!empty($campo['letra']) || $campo['letra'] == 0){
    			$cedula = $campo['provincia']."-".$campo['letra']."-".$campo['tomo']."-".$campo['asiento'];
    			$campo['ruc'] = $cedula;

    			unset($campo['pasaporte']);
    		}else{
    			$campo['ruc'] = $campo['pasaporte'];
    			$cedula = $campo['pasaporte'];
    		}

    		if($campo['identificacion'] == '166'){
    			$cedula = $campo['tomo_ruc']."-".$campo['folio']."-".$campo['asiento_ruc']."-".$campo['digito'];
    			$campo['ruc'] = $cedula;
    		}if(!empty($campo['pasaporte']) || $campo['letra'] == 'PAS'){
    			$cedula = $campo['pasaporte'];
    			$campo['ruc'] = $cedula;
    		}if($campo['identificacion'] == 'RUC'){
    			$cedula = $campo['tomo_ruc']."-".$campo['folio']."-".$campo['asiento_ruc']."-".$campo['digito'];
    			$campo['ruc'] = $cedula;
    		}
    		if(!isset($campo['uuid'])){
    			$campo['empresa_id'] = $this->empresa_id;
    			$campo['fecha_creacion'] = date('Y-m-d H:i:s');
    		}
    		Capsule::beginTransaction();
    		try {
    			if(empty($campo['uuid'])){
    				$clause['empresa_id'] = $this->empresa_id;
    				$total = $this->interesesAseguradosRep->listar_persona($clause);
    				$codigo = Util::generar_codigo('PER' , count($total) + 1);
    				$campo["numero"] = $codigo;
    				$campo['identificacion'] = $campo['ruc'];

    				$intereses_asegurados = $this->PersonasModel->create($campo);
    //guardar tabla principal
    				$fieldset['uuid_intereses'] = Capsule::raw("ORDER_UUID(uuid())");
    				$fieldset['empresa_id'] = $this->empresa_id;
    				$fieldset['interesestable_type'] = 5;
    				$fieldset['interesestable_id'] = $intereses_asegurados->id;
    				$fieldset['numero'] = $codigo;
    				$fieldset['identificacion'] = $intereses_asegurados->identificacion;
    				$fieldset['estado'] = $intereses_asegurados->estado;
    				$intereses_asegurados->interesesAsegurados()->create($fieldset);
    //Subir documentos
    				if(!empty($_FILES['file'])){
    					$vehiculo_id = $intereses_asegurados->id;
    					unset($_POST["campo"]);
    					$modeloInstancia = $this->PersonasModel->find($vehiculo_id);
    					$this->documentos->subir($modeloInstancia);
    				}
    			}else{
    				$personaObj  = $this->PersonasModel->find($campo['uuid']);
    				unset($campo['uuid']);
    				unset($campo['ruc']);
    				unset($campo['provincia']);
    				unset($campo['letra']);
    				unset($campo['tomo']);
    				unset($campo['asiento']);
    				$campo['identificacion'] = $cedula;
    				$personaObj->update($campo);
    //Tabla principal
    				$intereses_asegurados = $this->AseguradosModel->findByInteresesTable($personaObj->id, $personaObj->tipo_id);
    				$fieldset['identificacion'] = $personaObj->identificacion;
    				$fieldset['estado'] = $personaObj->estado;
    				$intereses_asegurados->update($fieldset);
    //Subir documentos
    				if(!empty($_FILES['file'])){
    					$vehiculo_id = $personaObj->id;
    					unset($_POST["campo"]);
    					$modeloInstancia = $this->PersonasModel->find($vehiculo_id);
    					$this->documentos->subir($modeloInstancia);
    				}
    			}
    			Capsule::commit();
    		}catch(ValidationException $e){
    			log_message('error', $e);
    			Capsule::rollback();
    		}

    		if(!is_null($intereses_asegurados)){
    			$mensaje = array('estado' => 200, 'mensaje' =>'<b>¡&Eacute;xito!</b> Se ha guardado correctamente');
    		}else{
    			$mensaje = array('class' =>'alert-danger', 'contenido' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
    		}


    	}else{
    		$mensaje = array('class' =>'alert-warning', 'contenido' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
    	}

    	$this->session->set_flashdata('mensaje', $mensaje);
    	redirect(base_url('intereses_asegurados/listar'));
    }
    
}

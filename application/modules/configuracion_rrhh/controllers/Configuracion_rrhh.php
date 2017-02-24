<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Flexio
 * @subpackage Controller
 * @category   Configuracion RRHH
 * @author     Pensanomica Team
 * @link       http://www.pensanomica.com
 * @copyright  01/03/2016
 */
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
class Configuracion_rrhh extends CRM_Controller
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

		$this->load->model('tiempo_contratacion_orm');
		$this->load->model('cargos_orm');
		$this->load->model('departamentos_orm');
		$this->load->model('liquidaciones_orm');
		$this->load->model('colaboradores/estado_orm');
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

		$this->nombre_modulo = $this->router->fetch_class();
	}

	function listar()
	{
		$data = array();
		$mensaje = array();

		//Verificar permisos de acceso a esta vista
		if(!$this->auth->has_permission('acceso', 'configuracion_rrhh/listar')){
			//Redireccionar
			redirect(base_url('/'));
		}

		//Obtener el id_empresa de session
		$uuid_empresa = $this->session->userdata('uuid_empresa');

		$empresa = Empresa_orm::findByUuid($uuid_empresa);

		$tipo_rata = Cargos_orm::getEnumValues('carg_cargos', 'tipo_rata');

		$clause = array(
			"empresa_id" =>  $this->empresa_id
		);
		$departamentos 	= Departamentos_orm::listar($clause);

		$lista_tiempo_contratacion = Tiempo_contratacion_orm::listar(array(
			"empresa_id" => $empresa->id
		))->toArray();

		$listaDepartamentos = Departamentos_orm::lista($this->empresa_id);

		$centros = Capsule::select(Capsule::raw("SELECT * FROM cen_centros WHERE empresa_id = :empresa_id1 AND estado='Activo' AND id NOT IN (SELECT padre_id FROM cen_centros WHERE empresa_id = :empresa_id2 AND estado='Activo') ORDER BY nombre ASC"), array(
			'empresa_id1' => $empresa->id,
			'empresa_id2' => $empresa->id
		));
		$centros = (!empty($centros) ? array_map(function($centros){ return array("id" => $centros->id, "nombre" => $centros->nombre); }, $centros) : "");

		//Catalogo estado liquidaciones
		$catalogo_estado = Catalogo_orm::where('identificador', '=', 'estado_liquidacion')->get(array('valor', 'etiqueta'));

		//Agregra variables PHP como variables JS
		$this->assets->agregar_var_js(array(
			"departamentos" => json_encode($departamentos),
			"estado_liquidaciones" =>$catalogo_estado,
			"tipo_ratas" => json_encode($tipo_rata),
			"centros" => json_encode($centros),
			"lista_departamentos" => json_encode($listaDepartamentos),
			"lista_tiempo_contratacion" => json_encode($lista_tiempo_contratacion),
			"permiso_crear_cargo" => $this->auth->has_permission('configuracion__crearCargo', 'configuracion_rrhh/listar') ? 'true' : 'false',
			"permiso_editar_cargo" => $this->auth->has_permission('configuracion__editarCargo', 'configuracion_rrhh/listar') ? 'true' : 'false',
			"permiso_crear_departamento" => $this->auth->has_permission('configuracion__crearAreaNegocio', 'configuracion_rrhh/listar') ? 'true' : 'false',
		));

		$this->assets->agregar_css(array(
			'public/assets/css/default/ui/base/jquery-ui.css',
			'public/assets/css/default/ui/base/jquery-ui.theme.css',
			'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
			'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
			'public/assets/css/plugins/jquery/chosen/chosen.min.css',
			'public/assets/css/plugins/jquery/datatables/dataTables.bootstrap.css',
			'public/assets/css/plugins/jquery/datatables/dataTables.responsive.css',
			'public/assets/css/plugins/jquery/datatables/dataTables.tableTools.min.css',
			'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
			'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
		));
		$this->assets->agregar_js(array(
			'public/assets/js/default/jquery-ui.min.js',
			'public/assets/js/plugins/jquery/jquery.sticky.js',
			'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
			'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
			'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
			'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
			'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
			//'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
			//'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
			'public/assets/js/plugins/jquery/chosen.jquery.min.js',
			'public/assets/js/plugins/jquery/combodate/momentjs.js',
			'public/assets/js/moment-with-locales-290.js',
			'public/assets/js/plugins/bootstrap/daterangepicker.js',
			'public/assets/js/plugins/jquery/datatables/jquery.dataTables.js',
			'public/assets/js/plugins/jquery/datatables/dataTables.bootstrap.js',
			'public/assets/js/plugins/jquery/datatables/dataTables.responsive.js',
			'public/assets/js/plugins/jquery/datatables/dataTables.tableTools.min.js',
			'public/assets/js/default/formulario.js',
			'public/assets/js/modules/configuracion_rrhh/configuracion.js',
                        'public/assets/js/default/jquery.inputmask.bundle.min.js',
		));



					$breadcrumb = array(
						"titulo" => '<i class="fa fa-cogs"></i> Configuraci&oacute;n de &aacute;reas de Negocio y cargos',
						"filtro" => false,
						"ruta" => array(
								0 => array(
										"nombre" => "Recursos humanos",
										"activo" => false,
								 ),
								1=> array(
											"nombre" => '<b>Configuración</b>',
											"activo" => true,
		 						),

						),
				);

		$this->template->agregar_titulo_header('Colaboradores');
		$this->template->agregar_breadcrumb($breadcrumb);
		$this->template->agregar_contenido($data);
		$this->template->visualizar();
	}

	/**
	 * Cargar Vista Parcial de Tabla de Cargos
	 *
	 * @return void
	 */
	public function ocultotablacargos()
	{
		$this->assets->agregar_js(array(
			'public/assets/js/modules/configuracion_rrhh/tabla-cargos.js'
		));

		$this->load->view('tabla-cargos');
	}

	public function ocultotablaliquidaciones()
	{
		$this->assets->agregar_js(array(
			'public/assets/js/modules/configuracion_rrhh/tabla-liquidaciones.js'
		));

		$this->load->view('tabla-liquidaciones');
	}

	/**
	 * Retornar array de Cargos
	 * Segun ID de departamento.
	 */
	public function ajax_lista_cargos()
	{
		//Just Allow ajax request
		/*if(!$this->input->is_ajax_request()){
		 return false;
		}*/

		$departamento_id = $this->input->post('departamento_id', true);

                $clause = array(
                   "departamento_id" => $departamento_id,
                   "estado_id" => 1,
                   "empresa_id" => $this->empresa_id
               );

		if($departamento_id==""){
			return false;
		}

		$response = new stdClass();
		$response->result = Cargos_orm::where($clause)->get()->toArray();
		$json = json_encode($response);
		echo $json;
		exit;
	}

	/**
	 * Funcion de Listar Cargos
	 * jQgrid
	 */
	public function ajax_listar_cargos()
	{
		//Just Allow ajax request
		if(!$this->input->is_ajax_request()){
			return false;
		}

		$clause = array(
				"empresa_id" =>  $this->empresa_id //ID de la empresa
		);
		$departamento 	= $this->input->post('departamento', true);
		$cargo 			= $this->input->post('cargo', true);
		$rata 			= $this->input->post('rata', true);
		$codigo 		= $this->input->post('codigo', true);

	/*	if( !empty($departamento)){
			$clause["departamento"] = array('LIKE', "%$departamento%");
		}*/
		if( !empty($cargo)){
			$clause["nombre"] = array('LIKE', "%$cargo%");
		}
		if( !empty($rata)){
			$clause["rata"] = array('LIKE', "%$rata%");
		}
		if( !empty($codigo)){
			$clause["codigo"] = array('LIKE', "%$codigo%");
		}

		list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
		$count = Cargos_orm::listar($clause, NULL, NULL, NULL, NULL)->count();
		list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
		$rows = Cargos_orm::listar($clause, $sidx, $sord, $limit, $start);
		//Constructing a JSON
		//$centrosArr = array();
		//$departamentosArr = array();
		$indiceArr = array();

		$response = new stdClass();
		$response->page     = $page;
		$response->total    = $total_pages;
		$response->records  = $count;
		//dd($response);
		$cargos_listado = Cargos_orm::lista($this->empresa_id);
		//dd($cargos_listado);
		$i = 0;
		foreach ($cargos_listado as $i=>$cargo)
		{	//dd($cargo);
				//**************************** Inicio de botones *******************************************
					//$cargo_id = $cargo["id"]."-$i";
					$cargo_id = $cargo["id"];
					$hidden_options = "";
					//$link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $cargo_id .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

					if($this->auth->has_permission('configuracion__editarCargo', 'colaboradores/configuracion')){
						$hidden_options = '<a href="#" data-id="'. $cargo_id .'" class="btn btn-block btn-outline btn-success editarCargoBtn">Editar Cargo</a>';
					}

					if($this->auth->has_permission('configuracion__duplicarCargo', 'colaboradores/configuracion')){
						$hidden_options .= '<a href="#" data-id="'. $cargo_id .'" class="btn btn-block btn-outline btn-success duplicarCargoBtn">Duplicar</a>';
					}

					//Boton de Activar/Desactivar
					$btn_estado =  $cargo['estado_id'] == 1 ? 0 : 1;
					$btn_class =  $cargo['estado_id'] == 1 ? 'desactivarCargoBtn' : 'activarCargoBtn';
					$btn_texto =  $cargo['estado_id'] == 1 ? 'Desactivar' : 'Activar';

					if($this->auth->has_permission('configuracion__desactivarActivarCargo', 'colaboradores/configuracion')){
						$hidden_options .= '<a href="#" data-id="'. $cargo_id .'" data-estado="'. $btn_estado .'" class="btn btn-block btn-outline btn-success '. $btn_class .'">'. $btn_texto .'</a>';
					}

					$departamento_id = Util::verificar_valor($cargo['departamento_id']);
					$departamento = $cargo['departamentos']['nombre'];
					$departamento_hash = str_replace(" ", "_", strtolower(Util::verificar_valor($departamento)));
					$rata = Util::verificar_valor($cargo['rata']);
					$rata = !empty($rata) ? "$". $rata : "$0.00";
					$estado = !empty($cargo["estado"]["etiqueta"]) ? $cargo["estado"]["etiqueta"] : 'Desactivado';
					//$estado_color = trim($estado) == "Activo" ? 'background-color:#5CB85C' : 'background-color: red';
					$response->rows[$i]["id"] = $cargo_id; //Este es el ID de la fila
					//dd($response->["id"]);
			// *********************************** fin de 3 botones ******************************************
			//$estado = $cargo["estado"]["etiqueta"];
			$estado = $cargo["estado_id"]==1?"Activo":"Desactivo";
			$estado_color = trim($estado) == "Activo" ? 'background-color:#5CB85C' : 'background-color: red';
			$link_option = '<button class="viewOptions btn btn-success btn-sm" type="button"   data-id="'. $cargo_id .'" ><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
			$response->rows[$i]["cell"] = array(
			$response->rows[$i]["id"] = $cargo_id,
			$cargo_nombre = $cargo['nombre'],
			$cargo_descripcion = $cargo['descripcion'],
			$cargo_tipo_rata = $cargo['tipo_rata'],
			$cargo_rata = $cargo['rata'],
			$cargo_codigo = $cargo['codigo'],
			!empty($estado) ? '<label style="color:white; '. $estado_color .'" class="label">'. $estado .'</label>' : "",
		 	$link_option,
			$hidden_options
			);
			$i++;
		}

		/*echo "<pre>";
		 print_r($centroContableCargos);
		print_r($response);
		echo "</pre>";
		die();*/
		//dd($response);
		echo json_encode($response);
		exit;
	}

	/**
	 * Duplicar Cargo
	 *
	 * @return array
	 */
//************************************************************ DATH FUNCION ajax_listar_area_negocio *********************************************************
public function ajax_listar_area_negocio()
{
	//Just Allow ajax request
	if(!$this->input->is_ajax_request()){
		return false;
	}

	$clause = array(
			"empresa_id" =>  $this->empresa_id //ID de la empresa
	);
	list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
	$count = Departamentos_orm::listar($clause);
  //$count = count($count);
	//dd($count);
	list($total_pages, $page, $start) = Jqgrid::paginacion(count($count), $limit, $page);
	$rows = Departamentos_orm::listar($clause, $sidx, $sord, $limit, $start);
	$indiceArr = array();

	$response = new stdClass();
	$response->page     = $page;
	$response->total    = $total_pages;
	$response->records  = count($count);

	$cargos_listado = Departamentos_orm::lista($this->empresa_id);
//++++++++++++++++++++++++++++++++++++++++++++++++
/*list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
$count = Cargos_orm::listar($clause, NULL, NULL, NULL, NULL)->count();
list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
$rows = Cargos_orm::listar($clause, $sidx, $sord, $limit, $start);
$indiceArr = array();
$response = new stdClass();
$response->page     = $page;
$response->total    = $total_pages;
$response->records  = $count;

$cargos_listado = Cargos_orm::lista($this->empresa_id);*/
//++++++++++++++++++++++++++++++++++++++++++++++++

	$i = 0;
	foreach ($cargos_listado as $i=>$cargo)
	{
			//**************************** Inicio de botones *******************************************
				$cargo_id = $cargo["id"];
				$id_estado = $cargo["estado"];
				$hidden_options = "";

				$hidden_options = '<a href="#" data-id="'. $cargo_id .'" class="btn btn-block btn-outline btn-success editarCargoBtn">Editar Cargo</a>';
				//Boton de Activar/Desactivar
				$btn_estado =  $cargo['estado'] == 1 ? 0 : 1;
				$btn_class =  $cargo['estado'] == 1 ? 'desactivarAreaNegBtn' : 'activarAreaNegBtn';
				$btn_texto =  $cargo['estado'] == 1 ? 'Desactivar' : 'Activar';

				//if($this->auth->has_permission('configuracion__desactivarActivarCargo', 'colaboradores/configuracion')){
				$hidden_options .= '<a href="#" data-id="'. $cargo_id .'" data-estado="'. $btn_estado .'" class="btn btn-block btn-outline btn-success '. $btn_class .'">'. $btn_texto .'</a>';
				$departamento_id = Util::verificar_valor($cargo_id);
				$response->rows[$i]["id"] = $cargo_id; //Este es el ID de la fila
				//dd($response->["id"]);
		// *********************************** fin de 3 botones ******************************************
				$link_option = '<button class="viewOptions2 btn btn-success btn-sm" type="button"   data-id="'. $cargo_id .'" ><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
				$estado = ($cargo['estado']) == 1 ? 'Activo':'Inactivo';
				$estado_color = trim($estado) == "Activo" ? 'background-color:#5CB85C' : 'background-color: red';
				//$response->rows[$i]["id"] = $estado;
				$response->rows[$i]["cell"] = array
				(
					$cargo_nombre = $cargo['nombre'],
					!empty($estado) ? '<label style="color:white; '. $estado_color .'" class="label">'. $estado .'</label>' : "",
					$link_option,
					$hidden_options,
					$id_estado
				);
				$i++;
			}

	/*echo "<pre>";
	 print_r($centroContableCargos);
	print_r($response);
	echo "</pre>";
	die();*/
	//dd($response);
	echo json_encode($response);
	exit;
}
//************************************************************ FIN FUNCION 	ajax_listar_area_negocio **********************************************************

//*************************************************************************************************************************************************************
//************************************************************ DARH METODO ocultotablaareanegocio *************************************************************
public function ocultotablaareanegocio()
{

  	$this->assets->agregar_js(array(
		'public/assets/js/modules/configuracion_rrhh/tabla_area_negocio.js'
	));

	$this->load->view('tabla-area-negocio');
}

/**
 * Retornar array de Cargos
 * Segun ID de departamento.
 */
 //******************************************************** FIN METDO ocultotablaareanegocio
	function conf_duplicar_cargo()
	{
		$cargo_id = $this->input->post('cargo_id', true);

		if(empty($cargo_id)){
			return false;
		}

		/**
		 * Inicializar Transaccion
		 */
		Capsule::beginTransaction();

		try {

			$cargo = Cargos_orm::find($cargo_id);

			$fieldset = array(
				"empresa_id" 		=> $cargo->empresa_id,
				"departamento_id" 	=> $cargo->departamento_id,
				"nombre" 			=> $cargo->nombre,
				"descripcion" 		=> $cargo->descripcion,
				"tipo_rata" 		=> $cargo->tipo_rata,
				"rata" 				=> $cargo->rata,
				"estado" 			=> $cargo->estado,
				"creado_por" 		=> $cargo->creado_por
			);
			$nuevo_cargo = Cargos_orm::create($fieldset);

		} catch(ValidationException $e){

			// Rollback
			Capsule::rollback();

			echo json_encode(array(
				"id" => false,
				"mensaje" => "Hubo un error tratando de guardar el cargo."
			));
			exit;
		}

		// If we reach here, then
		// data is valid and working.
		// Commit the queries!
		Capsule::commit();

		echo json_encode(array(
			"id" => $nuevo_cargo->id,
			"estado" => $nuevo_cargo->estado,
			"mensaje" => "Se ha duplicado el cargo ". $nuevo_cargo->nombre .", satisfactoriamente."
		));
		exit;
	}

	/**
	 * Activar/Desactivar Cargo
	 *
	 * @return array
	 */
	function conf_toggle_cargo()
	{
		$cargo_id = $this->input->post('cargo_id', true);
		$estado_id = $this->input->post('estado_id', true);
		$estado = $estado_id == 1 ? 'activado' : 'desactivado';

		if(empty($cargo_id)){
			return false;
		}

		/**
		 * Inicializar Transaccion
		 */
		Capsule::beginTransaction();

		try {

			$cargo = Cargos_orm::where('id', '=', $cargo_id);
			$cargo->update(array("estado_id" => $estado_id));

		} catch(ValidationException $e){

			// Rollback
			Capsule::rollback();

			echo json_encode(array(
					"id" => false,
					"mensaje" => "Hubo un error tratando de $estado el cargo."
			));
			exit;
		}

		// If we reach here, then
		// data is valid and working.
		// Commit the queries!
		Capsule::commit();

		echo json_encode(array(
				"id" => $cargo_id,
				"mensaje" => "Se ha $estado el cargo satisfactoriamente."
		));
		exit;
	}
//listar para liquidacionesForm
	public function ajax_listar_liquidaciones()
  {
 	 //Just Allow ajax request
 	 if(!$this->input->is_ajax_request()){
 		 return false;
 	 }
 	 $clause = array(
 			 "empresa_id" =>  $this->empresa_id
 	 );
 	 list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
 	 $count = Liquidaciones_orm::listar($clause, NULL, NULL, NULL, NULL)->count();
 	 list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
 	 $rows = Liquidaciones_orm::listar($clause, $sidx, $sord, $limit, $start);
 	 //Constructing a JSON
 	 $centrosArr = array();
 	 $departamentosArr = array();
 	 $indiceArr = array();
 	 $response = new stdClass();
 	 $response->page     = $page;
 	 $response->total    = $total_pages;
 	 $response->records  = $count;
 	 if(!empty($rows)){
		 foreach ($rows AS $i => $row){
				 $hidden_options = "";
				 $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row->id .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
				 $hidden_options .= '<a href="#" data-id="'. $row->id .'" class="btn btn-block btn-outline btn-success editar">Editar</a>';
				 $estado = Util::verificar_valor($row->estado);
				 $estado = $estado == 1 ? "Activo" : "Inactivo";
				 $estado_color = trim($estado) == "Activo" ? 'background-color:#5CB85C' : 'background-color: red';
				 $response->rows[$i]["id"] = $row->id;
				 $response->rows[$i]["cell"] = array(
					 Util::verificar_valor($row->nombre),
					 !empty($estado) ? '<span style="color:white; '. $estado_color .'" class="btn btn-xs btn-block">'. $estado .'</span>' : "",
					 $link_option,
					 $hidden_options
				 );
			 $i++;
		 }
 	 }
 	 /*echo "<pre>";
 		print_r($centroContableCargos);
 	 print_r($response);
 	 echo "</pre>";
 	 die();*/
 	 echo json_encode($response);
 	 exit;
  }
	//****************************************************************************************************************************
	function conf_toggle_area_negocio()
	{
		//dd('Llegaaaeeeeee kim');
		/*$cargo_id = $this->input->post('cargo_id', true);
		$estado_id = $this->input->post('estado_id', true);
		$estado = $estado_id == 1 ? 'activado' : 'desactivado';*/

		$departamento_id = $this->input->post('departamento_id', true);
		$estado_id = $this->input->post('estado_id', true);
		$estado = $estado_id == 1 ? 'activado' : 'desactivado';
		//dd('Despues de unas cuantas lineas');
		if(empty($departamento_id))
		{
			//dd('Cargo es EMPTY');
			return false;
		}

		//** * Inicializar Transaccion

		Capsule::beginTransaction();
		//dd($cargo_id. ' '.$estado_id);
		//dd('Antes del TRY');
		try {
		//	dd('Entra al TRY');
			$cargo = Departamentos_orm::where('id', '=', $departamento_id);
			$cargo->update(array("estado" => $estado_id));
			//dd($cargo);
		} catch(ValidationException $e){

			// Rollback
			Capsule::rollback();
			echo json_encode(array(
					"id" => false,
					"mensaje" => "Hubo un error tratando de $estado el cargo."
			));
			exit;
		}
		Capsule::commit();
		echo json_encode(array(
				"id" => $departamento_id,
				"mensaje" => "Se ha $estado el cargo satisfactoriamente.",
				"status"=>true
		));
		exit;
	}
	//****************************************************************************************************************************

	/**
	 * Guardar Formulario de Crear Cargo
	 * Activo/Inactivo
	 *
	 * @return array
	 */
	function conf_guardar_cargo()
	{
		if(empty($_POST["guardarcargoBtn"])){
			return false;
		}

		$cargo_id 			= $this->input->post('id', true);
		$nombre 			= $this->input->post('nombre', true);
		$descripcion 		= $this->input->post('descripcion', true);
		$departamento_id 	= $this->input->post('departamento_id', true);
		$tipo_rata 			= $this->input->post('tipo_rata', true);
		$rata 				= $this->input->post('rata', true);

		/**
		 * Inicializar Transaccion
		*/
		Capsule::beginTransaction();

		try {

			//Verificar si el cargo_id existe
			//Si exite tenemos que actualizar
			//la informacion del cargo.
			$cargo = Cargos_orm::find($cargo_id);

			if(empty($cargo))
			{
				$fieldset = array(
					"empresa_id" 		=> $this->empresa_id,
					"departamento_id" 	=> $departamento_id,
					"nombre" 			=> $nombre,
					"descripcion" 		=> $descripcion,
					"tipo_rata" 		=> $tipo_rata,
					"rata" 				=> $rata,
					"estado" 			=> 1,
					"creado_por" 		=> $this->usuario_id,
				);
				$cargo = Cargos_orm::create($fieldset);

			}else{

				$cargo->departamento_id	= $departamento_id;
				$cargo->nombre 			= $nombre;
				$cargo->descripcion		= $descripcion;
				$cargo->tipo_rata 		= $tipo_rata;
				$cargo->rata 			= $rata;
				$cargo->save();
			}

		} catch(ValidationException $e){

			// Rollback
			Capsule::rollback();

			echo json_encode(array(
					"id" => false,
					"mensaje" => "Hubo un error tratando de guardar el cargo."
			));
			exit;
		}

		// If we reach here, then
		// data is valid and working.
		// Commit the queries!
		Capsule::commit();

		echo json_encode(array(
				"id" => $cargo->id,
				"estado" => $cargo->estado,
				"mensaje" => "Se ha guardado el cargo satisfactoriamente."
		));
		exit;
	}

	/**
	 * Guardar Formulario de crear Departamento
	 * Activo/Inactivo
	 *
	 * @return array
	 */
	function conf_guardar_departamento()
	{
		if(empty($_POST["guardar"]))
		{
			return false;
		}

		$nombre = $this->input->post('nombre', true);
		//dd($nombre);
		/**
		 * Inicializar Transaccion
		*/
		$nomdb = Departamentos_orm::where('nombre','=',$nombre)->get(); //**********
		//dd($nomdb);
		$fila_area = count($nomdb); //****************

		//dd($fila_area);
		Capsule::beginTransaction();
		//dd($fila_area);
		if( $fila_area == 0)
		{
			$fieldset = array
			(
					"empresa_id" 		=> $this->empresa_id,
					"nombre" 			=> $nombre,
					"estado" 			=> 1,
					"creado_por" 		=> $this->usuario_id,
			);
			try
			{
				$departamento = Departamentos_orm::create($fieldset);
			}
			catch(ValidationException $e)
			{
				Capsule::rollback();
				echo json_encode(array
														(
																"id" => false,
																"mensaje" => "Hubo un error tratando de guardar el &aacute;rea de negocio."
														)
												);
				exit;
			}

		}
		else
		{
			echo json_encode(array
													(
															"id" => false,
															"mensaje" => "Hubo un error tratando de guardar el &aacute;rea de negocio. No se puede volver a crear un &aacute;rea de negocio existente."
													)
											);
			exit;
		}
		Capsule::commit();
		echo json_encode(array
												(
														"id" => $departamento->id,
														"estado" => 'Activo', //default se crea con este estado.
														"mensaje" => "Se ha guardado el &aacute;rea de negocio satisfactoriamente."
												));
		exit;
		/*//***********************************************************************************************************************************
		if(count($nomdb) > 0)
		{
			catch(ValidationException $e)
			{
				// Rollback
				Capsule::rollback();
				echo json_encode(array
														(
																"id" => false,
																"mensaje" => "Hubo un error tratando de guardar el &aacute;rea de negocio. No se puede volver a crear un &aacute;rea de negocio existente."
														)
												);
				exit;
			}
		}
		//***************************************************************************************************************************************
		catch(ValidationException $e)
		{
			// Rollback
			Capsule::rollback();
			echo json_encode(array
													(
															"id" => false,
															"mensaje" => "Hubo un error tratando de guardar el &aacute;rea de negocio."
													)
											);
			exit;
		}
		// If we reach here, then
		// data is valid and working.
		// Commit the queries!
		Capsule::commit();
		echo json_encode(array
												(
														"id" => $departamento->id,
														"estado" => 'Activo', //default se crea con este estado.
														"mensaje" => "Se ha guardado el &aacute;rea de negocio satisfactoriamente."
												));
		exit;*/
	}

//guardar liquidaciones
	function conf_guardar_liquidaciones()
 {
	 if(empty($_POST["guardarliquidacionesBtn"])){
		 return false;
	 }
	 $nombre = $this->input->post('nombre', true);
	 $estado_id = $this->input->post('estado', true);
	 $estado = $this->input->post('estado_id', true);
	 $liquidacion_id = $this->input->post('id', true);
	 /**
		* Inicializar Transaccion
	 */
	 Capsule::beginTransaction();
	 try {
		 $liquidaciones = Liquidaciones_orm::find($liquidacion_id);
		 if(empty($liquidaciones))
		 {
		 $fieldset = array(
				 "empresa_id" 		=> $this->empresa_id,
				 "nombre" 			=> $nombre,
				 "estado" 			=> $estado_id,
				 "creado_por" 		=> $this->usuario_id,
		 );
		 $liquidaciones = Liquidaciones_orm::create($fieldset);
	 }else{
				 $liquidaciones->empresa_id = $this->empresa_id;
				 $liquidaciones->nombre		= $nombre;
				 $liquidaciones->estado		= $estado_id;
				 $liquidaciones->creado_por	= $this->usuario_id;

				 $liquidaciones->save();
	 }
	 } catch(ValidationException $e){
		 // Rollback
		 Capsule::rollback();
		 echo json_encode(array(
				 "id" => false,
				 "mensaje" => "Hubo un error tratando de guardar la liquidaci&oacute;n."
		 ));
		 exit;
	 }
	 // If we reach here, then
	 // data is valid and working.
	 // Commit the queries!
	 Capsule::commit();
	 echo json_encode(array(
			 "id" => $liquidaciones->id,
			 "estado" => 'Activo', //default se crea con este estado.
			 "mensaje" => "Se ha guardado satisfactoriamente."
	 ));
	 exit;
 }

	/**
	 * Guardar Estado de Departamento
	 * Activo/Inactivo
	 *
	 * @return array
	 */
	function conf_cambiar_estado_departamento()
	{
 		/*if(!isset($_POST["estado"]) && !is_numeric($_POST["estado"])){
			return false;
		}*/

		$departamento_id = $this->input->post('departamento_id', true);
		$estado_id = $this->input->post('estado_id', true);
		//dd($cargo_id);
		if(!empty($departamento_id))
		{
			foreach($departamentos AS $id => $checked)
			{
				if($checked==false){
					continue;
				}

				$departamento = Departamentos_orm::where('id', '=', $id);
				$departamento->update(array("estado" => $estado));
			}
		}

		$departamentos = Departamentos_orm::get(array('id', 'nombre', Capsule::raw("IF(estado=1, 'Activo', 'Inactivo') AS estado")))->toArray();
		echo json_encode($departamentos);
		exit;
	}

	/**
	 * Relacionar los Centros Contables seleccionados
	 * a los departamento seleccionados.
	 *
	 * @return array
	 */
	function conf_relacionar_departamento_centros()
	{
		if(empty($_POST["centro_contable_id"]) && empty($_POST["departamento_id"])){
			return false;
		}

		$centros_contables = $this->input->post('centro_contable_id', true);
		$departamentos = $this->input->post('departamento_id', true);

		if(!empty($departamentos))
		{
			/**
			 * Inicializar Transaccion
			 */
			Capsule::beginTransaction();

			try {

				//Obtener el id_empresa de session
				$uuid_empresa = $this->session->userdata('uuid_empresa');
				$empresa = Empresa_orm::findByUuid($uuid_empresa);

				foreach($departamentos AS $id => $checked)
				{
					if($checked==false){
						continue;
					}

					$departamento = Departamentos_orm::find($id);
					$centros_contables = (!empty($centros_contables) ? array_map(function($centros_contables){ return $centros_contables; }, $centros_contables) : "");

					//Verificar si existe o no un Centro Contable ya relacionado
					$departamentoINFO = Departamentos_orm::with(array('centros_contables' => function($query) use($centros_contables){
						$query->whereIn('centro_id', $centros_contables);
					}))->where('id', $departamento->id)->get()->toArray();

					$centros = !empty($departamentoINFO[0]["centros_contables"]) ? $departamentoINFO[0]["centros_contables"] : array();
					$centros = !empty($centros) ? array_map(function($centros){ return $centros["id"]; }, $centros) : "";

					if(is_array($centros)){
						//Computes the difference of arrays
						$centros_contables = array_diff($centros_contables, $centros);

						if(empty($centros_contables)){
							continue;
						}
					}

					//Guardar
					$departamento->centros_contables()->attach($centros_contables, array('empresa_id' => $empresa->id));
				}

			} catch(Exception $e){

				// Rollback
				Capsule::rollback();

				echo json_encode(array(
						"estado" => 500,
						"mensaje" => "No se pudo relacionar el &aacute;rea de negocio seleccionado."
				));
				exit;
			}

			Capsule::commit();
		}

		//Seleccionar Listado completo de departamentos
		$listaCompletaDepartamentos = Departamentos_orm::listar();

		//Selecionar Lista de departamentos asociados a centro contable
		$listaDepartamentos = Departamentos_orm::lista($empresa->id);

		echo json_encode(array(
				"estado" => 200,
				"mensaje" => "Se ha relacionado satisfactoriamente el departamento seleccionado con Centro Contables.",
				"listaDepartamentos" => $listaDepartamentos,
				"listaCompletaDepartamentos" => $listaCompletaDepartamentos,
		));
		exit;
	}

	function conf_guardar_tiempo_contratacion()
	{
		if(empty($_POST["guardar"])){
			return false;
		}

		$tiempo = $this->input->post('tiempo', true);

		if(empty($tiempo)){
			return false;
		}

		/**
		 * Inicializar Transaccion
		 */
		Capsule::beginTransaction();

		try {

			$fieldset = array(
					"empresa_id" 		=> $this->empresa_id,
					"tiempo" 			=> $tiempo,
					"estado" 			=> 1,
					"creado_por" 		=> $this->usuario_id,
			);
			$tiempo_contratacion = Tiempo_contratacion_orm::create($fieldset);

		} catch(ValidationException $e){

			// Rollback
			Capsule::rollback();

			echo json_encode(array(
					"estado" => 500,
					"id" => false,
					"mensaje" => "Hubo un error tratando de guardar el cargo."
			));
			exit;
		}

		// If we reach here, then
		// data is valid and working.
		// Commit the queries!
		Capsule::commit();

		echo json_encode(array(
				"estado" => 200,
				"id" => $tiempo_contratacion->id,
				"mensaje" => "Se ha guardado el tiempo satisfactoriamente."
		));
		exit;
	}

	function conf_eliminar_tiempo_contratacion()
	{
		if(empty($_POST["eliminar"])){
			return false;
		}

		$tiempo_contratacion_id = $this->input->post('tiempo_contratacion_id', true);

		if(empty($tiempo_contratacion_id)){
			return false;
		}

		/**
		 * Inicializar Transaccion
		 */
		Capsule::beginTransaction();

		try {

			$response = Tiempo_contratacion_orm::where('id', $tiempo_contratacion_id)->delete();

		} catch(ValidationException $e){

			// Rollback
			Capsule::rollback();

			echo json_encode(array(
					"estado" => 500,
					"mensaje" => "Hubo un error tratando de eliminar el Tiempo de Contratacion."
			));
			exit;
		}

		// If we reach here, then
		// data is valid and working.
		// Commit the queries!
		Capsule::commit();

		//Obtener el id_empresa de session
		$uuid_empresa = $this->session->userdata('uuid_empresa');
		$empresa = Empresa_orm::findByUuid($uuid_empresa);

		$lista_tiempo_contratacion = Tiempo_contratacion_orm::listar(array(
				"empresa_id" => $empresa->id
		))->toArray();

		echo json_encode(array(
				"estado" => 200,
				"mensaje" => "Se ha eliminando el Tiempo de Contratacion satisfactoriamente.",
				"tiempos" => $lista_tiempo_contratacion
		));
		exit;
	}
}

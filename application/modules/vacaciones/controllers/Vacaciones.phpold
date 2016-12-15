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

class Vacaciones extends CRM_Controller
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

        $this->load->model('vacaciones_orm');
        $this->load->model('accion_personal/accion_personal_orm');
       	$this->load->model('colaboradores/colaboradores_orm');
        $this->load->model('configuracion_rrhh/tiempo_contratacion_orm');
        $this->load->model('configuracion_rrhh/cargos_orm');
        $this->load->model('configuracion_rrhh/departamentos_orm');
        $this->load->model('contabilidad/centros_orm');
        
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

    /**
     * Cargar Vista Parcial de Evaluacion
     *
     * @return void
     */
    public function formulario($data=NULL)
    {
    	$this->assets->agregar_js(array(
    		'public/assets/js/modules/vacaciones/formulario.controller.js'
    	));
    	 
    	$this->load->view('formulario', $data);
    }
    
    /**
     * Cargar Vista Parcial de Crear Ausencia
     *
     * @return void
     */
    public function formularioparcial($data=NULL)
    {
    	/*$this->assets->agregar_js(array(
    		'public/assets/js/modules/vacaciones/formulario.controller.js'
    	));*/
    
    	$this->template->vista_parcial(array(
    		'vacaciones',
    		'crear'
    	));
    }
    
    function ajax_seleccionar_vacacion()
    {
    	$vacacion_id =  $this->input->post('id', true);
    	 
    	if(empty($vacacion_id)){
    		return false;
    	}
    	 
    	$vacacion = Vacaciones_orm::where("id", $vacacion_id)->where("empresa_id", $this->empresa_id)->get()->toArray();
    
    	if(!empty($vacacion)){
    		$vacacion = $vacacion[0];
    
    		if(!empty($vacacion["fecha_desde"])){
    			$vacacion["fecha_desde"] = date("d/m/Y", strtotime($vacacion["fecha_desde"]));
    		}
    		if(!empty($vacacion["fecha_hasta"])){
    			$vacacion["fecha_hasta"] = date("d/m/Y", strtotime($vacacion["fecha_hasta"]));
    		}
    		if(!empty($vacacion["fecha_pago"])){
    			$vacacion["fecha_pago"] = date("d/m/Y", strtotime($vacacion["fecha_pago"]));
    		}
    	}
    	 
    	echo json_encode($vacacion);
    	exit;
    }
    
    function ajax_guardar_vacaciones()
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
    		 
    		$vacaciones_id		= $this->input->post('vacaciones_id', true);
    		$colaborador_id 	= $this->input->post('colaborador_id', true);
    		$dias_disponibles 	= $this->input->post('dias_disponibles', true);
    		$fecha_desde 		= $this->input->post('fecha_desde', true);
    		$fecha_desde		= !empty($fecha_desde) ? str_replace('/', '-', $fecha_desde) : "";
    		$fecha_desde 		= !empty($fecha_desde) ? date("Y-m-d", strtotime($fecha_desde)) : "";
    		$fecha_hasta 		= $this->input->post('fecha_hasta', true);
    		$fecha_hasta		= !empty($fecha_hasta) ? str_replace('/', '-', $fecha_hasta) : "";
    		$fecha_hasta 		= !empty($fecha_hasta) ? date("Y-m-d", strtotime($fecha_hasta)) : "";
    		$fecha_pago 		= $this->input->post('fecha_pago', true);
    		$fecha_pago			= !empty($fecha_pago) ? str_replace('/', '-', $fecha_pago) : "";
    		$fecha_pago 		= !empty($fecha_pago) ? date("Y-m-d", strtotime($fecha_pago)) : "";
    		$cantidad_dias 		= $this->input->post('cantidad_dias', true);
			$estado_id 			= $this->input->post('estado_id', true);
			$pago_inmediato_id 	= $this->input->post('pago_inmediato_id', true);
			$cuenta_pasivo_id 	= $this->input->post('cuenta_pasivo_id', true);
			$observaciones 		= $this->input->post('observaciones', true);

    		//Verificar si existe $vacaciones_id
    		$vacaciones = Vacaciones_orm::find($vacaciones_id);
    		$colaborador = Colaboradores_orm::with(['centro_contable', 'departamento', 'cargo'])->where("id", $colaborador_id)->get()->toArray();
    		$colaborador = !empty($colaborador) ? $colaborador[0] : array();
    		
    		if(!empty($vacaciones))
    		{
    			$vacaciones->empresa_id 		= $this->empresa_id;
    			$vacaciones->fecha_desde 		= $fecha_desde;
    			$vacaciones->fecha_hasta 		= $fecha_hasta;
    			$vacaciones->fecha_pago 		= $fecha_pago;
    			$vacaciones->dias_disponibles 	= $dias_disponibles;
    			$vacaciones->cantidad_dias 		= $cantidad_dias;
    			$vacaciones->estado_id 			= $estado_id;
    			$vacaciones->pago_inmediato_id 	= $pago_inmediato_id;
    			$vacaciones->cuenta_pasivo_id 	= $cuenta_pasivo_id;
    			$vacaciones->observaciones 		= $observaciones;
    			$vacaciones->creado_por 		= $this->usuario_id;
    			$vacaciones->save();
    			
    			//Actualizar tabla accion personal
    			$vacaciones->acciones()->where("accionable_id", $vacaciones_id)->update([
    				"colaborador_id" => $colaborador_id,
    				"centro_contable_id" => !empty($colaborador["centro_contable_id"]) ? $colaborador["centro_contable_id"] : "",
    				"departamento_id" => !empty($colaborador["departamento_id"]) ? $colaborador["departamento_id"] : "",
    				"cargo_id" => !empty($colaborador["cargo_id"]) ? $colaborador["cargo_id"] : "",
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
    				"fecha_desde" 		=> $fecha_desde,
    				"fecha_hasta" 		=> $fecha_hasta,
    				"fecha_pago" 		=> $fecha_pago,
    				"dias_disponibles"	=> $dias_disponibles,
    				"cantidad_dias"		=> $cantidad_dias,
    				"estado_id" 		=> $estado_id,
    				"pago_inmediato_id" => $pago_inmediato_id,
    				"cuenta_pasivo_id" 	=> $cuenta_pasivo_id,
    				"observaciones"		=> $observaciones,
    				"creado_por" 		=> $this->usuario_id,
    			);

    			//--------------------
    			// Guardar Vacacion
    			//--------------------
    			$vacaciones = Vacaciones_orm::create($fieldset);
    			$vacaciones->acciones()->saveMany([new Accion_personal_orm([
    				"empresa_id" => $this->empresa_id,
    				"no_accion" => Capsule::raw("NO_ACCION_PERSONAL('AP', ". $this->empresa_id .")"),
    				"colaborador_id" => $colaborador_id,
    				"centro_contable_id" => !empty($colaborador["centro_contable_id"]) ? $colaborador["centro_contable_id"] : "",
    				"departamento_id" => !empty($colaborador["departamento_id"]) ? $colaborador["departamento_id"] : "",
    				"cargo_id" => !empty($colaborador["cargo_id"]) ? $colaborador["cargo_id"] : "",
    				"nombre_completo" => !empty($colaborador["nombre_completo"]) ? $colaborador["nombre_completo"] : "",
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
    				} catch (Exception $e) {
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
    	
    				$vacaciones = Vacaciones_orm::find($vacaciones->id);
    				$vacaciones->archivo_ruta = $archivo_ruta;
    				$vacaciones->archivo_nombre = $file_name;
    				$vacaciones->save();
    	
    			}else{
    				log_message("error", "MODULO: ". __METHOD__ .", Linea: ". __LINE__ ." --> No se pudo subir el archivo de vacacion.\r\n");
    			}
    		}
    		 
    	} catch(ValidationException $e){
    	
    		// Rollback
    		Capsule::rollback();
    	
    		log_message("error", "MODULO: ". __METHOD__ .", Linea: ". __LINE__ ." --> ". $e->getMessage().".\r\n");
    	
    		echo json_encode(array(
    			"guardado" => false,
    			"mensaje" => "Hubo un error tratando de ". (!empty($vacaciones_id) ? "actualizar" : "guardar") ." la vacacion."
    		));
    		exit;
    	}
    	
    	// If we reach here, then
    	// data is valid and working.
    	// Commit the queries!
    	Capsule::commit();
    	
    	$this->session->set_flashdata('mensaje', "Se ha ". (!empty($vacaciones_id) ? "actualizado" : "guardado") ." la vacaci&oacute;n satisfactoriamente.");

    	echo json_encode(array(
    		"guardado" => true,
    		"mensaje" => "Se ha ". (!empty($vacaciones_id) ? "actualizado" : "guardado") ." la vacaci&oacute;n satisfactoriamente."
    	));
    	exit;
    }
    
    function crear($colaborador_uuid=NULL)
    {
    	$data = array();
    	$mensaje = array();
    	$titulo_formulario = "Formulario de Ausencias";
    
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
    
    	$this->template->agregar_titulo_header('Ausencias');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar();
    }
}

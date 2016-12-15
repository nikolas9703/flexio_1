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

class Ausencias extends CRM_Controller
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

        $this->load->model('ausencias_orm');
        $this->load->model('accion_personal/accion_personal_orm');
        $this->load->model('configuracion_rrhh/cargos_orm');
        $this->load->model('configuracion_rrhh/departamentos_orm');
        $this->load->model('evaluaciones/evaluaciones_orm');
        $this->load->model('colaboradores/colaboradores_orm');
        $this->load->model('configuracion_rrhh/tiempo_contratacion_orm');
        $this->load->model('configuracion_rrhh/cargos_orm');
        $this->load->model('configuracion_rrhh/departamentos_orm');
        
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
    		'public/assets/js/modules/ausencias/formulario.controller.js'
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
    		'public/assets/js/modules/ausencias/formulario.controller.js'
    	));*/
    
    	$this->template->vista_parcial(array(
    		'ausencias',
    		'crear'
    	));
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
    
    function ajax_seleccionar_ausencia()
    {
    	$ausencia_id =  $this->input->post('id', true);
    	 
    	if(empty($ausencia_id)){
    		return false;
    	}
    	 
    	$ausencia = Ausencias_orm::where("id", $ausencia_id)->where("empresa_id", $this->empresa_id)->get()->toArray();
    
    	if(!empty($ausencia)){
    		$ausencia = $ausencia[0];
    
    		if(!empty($ausencia["fecha_desde"])){
    			$ausencia["fecha_desde"] = date("d/m/Y", strtotime($ausencia["fecha_desde"]));
    		}
    		if(!empty($ausencia["fecha_hasta"])){
    			$ausencia["fecha_hasta"] = date("d/m/Y", strtotime($ausencia["fecha_hasta"]));
    		}
    	}
    	 
    	echo json_encode($ausencia);
    	exit;
    }
    
    public function ajax_guardar_ausencia()
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
    		 
    		$ausencia_id		= $this->input->post('ausencia_id', true);
    		$colaborador_id 	= $this->input->post('colaborador_id', true);
    		$tipo_ausencia_id 	= $this->input->post('tipo_ausencia_id', true);
    		
    		$fecha_desde 		= $this->input->post('fecha_desde', true);
    		$fecha_desde		= !empty($fecha_desde) ? str_replace('/', '-', $fecha_desde) : "";
    		$fecha_desde		= !empty($fecha_desde) ? date("Y-m-d", strtotime($fecha_desde)) : "";
    		
    		$fecha_hasta 		= $this->input->post('fecha_hasta', true);
    		$fecha_hasta		= !empty($fecha_hasta) ? str_replace('/', '-', $fecha_hasta) : "";
    		$fecha_hasta		= !empty($fecha_hasta) ? date("Y-m-d", strtotime($fecha_desde)) : "";
    	
    		$justificacion_id 	= $this->input->post('justificacion_id', true);
    		$cuenta_pasivo_id 	= $this->input->post('cuenta_pasivo_id', true);
    		$estado_id 			= $this->input->post('estado_id', true);
    		$observaciones 		= $this->input->post('observaciones', true);
    		$creado_por 		= $this->input->post('creado_por', true);
    
    		//Verificar si existe $ausencia_id
    		$ausencia = Ausencias_orm::find($ausencia_id);
    		$colaborador = Colaboradores_orm::where("id", $colaborador_id)->with(['centro_contable', 'departamento', 'cargo'])->get()->toArray();
    		$colaborador = !empty($colaborador) ? $colaborador[0] : array();
    		
    		if(!empty($ausencia))
    		{
    			$ausencia->empresa_id 		= $this->empresa_id;
    			$ausencia->fecha_desde 		= $fecha_desde;
    			$ausencia->fecha_hasta 		= $fecha_hasta;
    			$ausencia->tipo_ausencia_id = $tipo_ausencia_id;
    			$ausencia->justificacion_id = $justificacion_id;
    			$ausencia->cuenta_pasivo_id = $cuenta_pasivo_id;
    			$ausencia->estado_id 		= $estado_id;
    			$ausencia->observaciones 	= $observaciones;
    			$ausencia->creado_por 		= $creado_por;
    			$ausencia->save();
    			
    			//Actualizar tabla accion personal
    			$ausencia->acciones()->where("accionable_id", $ausencia_id)->update([
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
    				"tipo_ausencia_id" 	=> $tipo_ausencia_id,
    				"justificacion_id" 	=> $justificacion_id,
    				"cuenta_pasivo_id"	=> $cuenta_pasivo_id,
					"estado_id"			=> $estado_id,
    				"observaciones"		=> $observaciones,
    				"creado_por" 		=> $creado_por,
    			);
    
    			//--------------------
    			// Guardar Ausencia
    			//--------------------
    			$ausencia = Ausencias_orm::create($fieldset);
    			$ausencia->acciones()->saveMany([new Accion_personal_orm([
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
    
    	} catch(ValidationException $e){
    
    		// Rollback
    		Capsule::rollback();
    
    		log_message("error", "MODULO: ". __METHOD__ .", Linea: ". __LINE__ ." --> ". $e->getMessage().".\r\n");
    
    		echo json_encode(array(
    			"guardado" => false,
    			"mensaje" => "Hubo un error tratando de ". (!empty($ausencia_id) ? "actualizar" : "guardar") ." la ausencia."
    		));
    		exit;
    	}
    
    	// If we reach here, then
    	// data is valid and working.
    	// Commit the queries!
    	Capsule::commit();
    	
    	$this->session->set_flashdata('mensaje', "Se ha ". (!empty($ausencia_id) ? "actualizado" : "guardado") ." la ausencia satisfactoriamente.");
    	
    	echo json_encode(array(
    		"guardado" => true,
    		"mensaje" => "Se ha ". (!empty($ausencia_id) ? "actualizado" : "guardado") ." la ausencia satisfactoriamente."
    	));
    	exit;
    }
    
}

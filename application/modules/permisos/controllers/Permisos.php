<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Permisos
 * 
 * Modulo para administrar las solicitudes de permisos de colaboradores.
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

class Permisos extends CRM_Controller
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

        $this->load->model('Permisos_trabajo_orm');
        $this->load->model('usuarios/usuario_orm');
        $this->load->model('usuarios/empresa_orm');
        $this->load->model('accion_personal/accion_personal_orm');
        $this->load->model('colaboradores/colaboradores_orm');
        $this->load->model('configuracion_rrhh/tiempo_contratacion_orm');
        $this->load->model('configuracion_rrhh/cargos_orm');
        $this->load->model('configuracion_rrhh/departamentos_orm');
        $this->load->model('contabilidad/centros_orm');
        
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
        
        //Obtener el id de modulo
        $controllername = $this->router->fetch_class();
        $modulo = Modulos_orm::where("controlador", $controllername)->get()->toArray();
        $this->modulo_id = $modulo[0]["id"];
        
        $this->nombre_modulo = $this->router->fetch_class();
    }

    /**
     * Cargar Vista Parcial
     *
     * @return void
     */
    public function formulario($data=NULL)
    {
    	$this->assets->agregar_js(array(
    		'public/assets/js/plugins/jquery/fileupload/jquery.fileupload.js',
    		'public/assets/js/modules/permisos/formulario.controller.js'
    	));
    	 
    	$this->load->view('formulario', $data);
    }
    
    /**
     * Cargar Vista Parcial de Crear
     *
     * @return void
     */
    public function formularioparcial($data=NULL)
    {
    	$this->template->vista_parcial(array(
    		'permisos',
    		'crear'
    	));
    }
    
    function ajax_seleccionar_permiso()
    {
    	$permiso_id =  $this->input->post('id', true);
    
    	if(empty($permiso_id)){
    		return false;
    	}
    
    	$permiso = Permisos_trabajo_orm::where("id", $permiso_id)->where("empresa_id", $this->empresa_id)->get()->toArray();
    
    	if(!empty($permiso)){
    		$permiso = $permiso[0];
    
    		if(!empty($permiso["fecha_desde"])){
    			$permiso["fecha_desde"] = date("d/m/Y", strtotime($permiso["fecha_desde"]));
    		}
    		if(!empty($permiso["fecha_hasta"])){
    			$permiso["fecha_hasta"] = date("d/m/Y", strtotime($permiso["fecha_hasta"]));
    		}
    	}
    
    	echo json_encode($permiso);
    	exit;
    }
    
    function ajax_guardar_permiso()
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
    		 
    		$permiso_id		= $this->input->post('permiso_id', true);
    		$colaborador_id 	= $this->input->post('colaborador_id', true);
    		$tipo_permiso_id = $this->input->post('tipo_permiso_id', true);
    		$fecha_desde 		= $this->input->post('fecha_desde', true);
    		$fecha_desde		= !empty($fecha_desde) ? str_replace('/', '-', $fecha_desde) : "";
    		$fecha_desde 		= !empty($fecha_desde) ? date("Y-m-d", strtotime($fecha_desde)) : "";
    		$fecha_hasta 		= $this->input->post('fecha_hasta', true);
    		$fecha_hasta		= !empty($fecha_hasta) ? str_replace('/', '-', $fecha_hasta) : "";
    		$fecha_hasta 		= !empty($fecha_hasta) ? date("Y-m-d", strtotime($fecha_hasta)) : "";
    		$cuenta_pasivo_id 	= $this->input->post('cuenta_pasivo_id', true);
    		$observaciones 		= $this->input->post('observaciones', true);
    		$estado_id 			= $this->input->post('estado_id', true);
			$constancia_permiso = $this->input->post('constancia_permiso', true);
			$constancia_permiso = $constancia_permiso == true ? 1 : 0;
			
    		//Verificar si existe $permiso_id
    		$permiso = Permisos_trabajo_orm::find($permiso_id);
    		$colaborador = Colaboradores_orm::where("id", $colaborador_id)->with(['centro_contable', 'departamento', 'cargo'])->get()->toArray();
    		$colaborador = !empty($colaborador) ? $colaborador[0] : array();
    		
    		if(!empty($permiso))
    		{
    			$permiso->empresa_id 			= $this->empresa_id;
    			$permiso->fecha_desde 			= $fecha_desde;
    			$permiso->fecha_hasta 			= $fecha_hasta;
    			$permiso->tipo_permiso_id 		= $tipo_permiso_id;
    			$permiso->cuenta_pasivo_id 		= $cuenta_pasivo_id;
    			$permiso->estado_id 			= $estado_id;
    			$permiso->observaciones 		= $observaciones;
    			$permiso->constancia_permiso 	= $constancia_permiso;
    			$permiso->creado_por 			= $this->usuario_id;
    			$permiso->save();
    			
    			//Actualizar tabla accion personal
    			$permiso->acciones()->where("accionable_id", $permiso_id)->update([
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
    				"empresa_id" 			=> $this->empresa_id,
    				"colaborador_id" 		=> $colaborador_id,
    				"tipo_permiso_id"		=> $tipo_permiso_id,
    				"fecha_desde" 			=> $fecha_desde,
    				"fecha_hasta" 			=> $fecha_hasta,
    				"cuenta_pasivo_id" 		=> $cuenta_pasivo_id,
    				"observaciones"			=> $observaciones,
    				"estado_id" 			=> $estado_id,
    				"constancia_permiso" 	=> $constancia_permiso,
    				"creado_por" 			=> $this->usuario_id,
    			);
    	
    			//--------------------
    			// Guardar
    			//--------------------
    			$permiso = Permisos_trabajo_orm::create($fieldset);
    			$permiso->acciones()->saveMany([new Accion_personal_orm([
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
    	
    			foreach ($_FILES AS $field => $_FILE)
    			{
    				$filename = $_FILE["name"];
    				$extension = pathinfo($filename, PATHINFO_EXTENSION);
    				$file_name = "perm-". rand().time() . "." . $extension;
    				
    				//Subir Archivo
    				if(move_uploaded_file($_FILE["tmp_name"], $empresa_folder . '/' . $file_name)) {
    					
    					$fieldset = array(
    						"archivo_ruta" => $archivo_ruta,
    						"archivo_nombre" => $file_name,
    					);
    					Permisos_trabajo_orm::where("id", $permiso->id)->update($fieldset);
    				} else{
    					log_message("error", "MODULO: ". __METHOD__ .", Linea: ". __LINE__ ." --> No se pudo subir el $field.\r\n");
    				}
    			}
    		}
    		 
    	} catch(ValidationException $e){
    	
    		// Rollback
    		Capsule::rollback();
    	
    		log_message("error", "MODULO: ". __METHOD__ .", Linea: ". __LINE__ ." --> ". $e->getMessage().".\r\n");
    	
    		echo json_encode(array(
    			"guardado" => false,
    			"mensaje" => "Hubo un error tratando de ". (!empty($permiso_id) ? "actualizar" : "guardar") ." el permiso."
    		));
    		exit;
    	}
    	
    	// If we reach here, then
    	// data is valid and working.
    	// Commit the queries!
    	Capsule::commit();
    	
    	$this->session->set_flashdata('mensaje', "Se ha ". (!empty($permiso_id) ? "actualizado" : "guardado") ." el permiso satisfactoriamente.");

    	echo json_encode(array(
    		"guardado" => true,
    		"mensaje" => "Se ha ". (!empty($permiso_id) ? "actualizado" : "guardado") ." el permiso satisfactoriamente."
    	));
    	exit;
    }

}

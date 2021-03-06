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

class Incapacidades extends CRM_Controller
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

        $this->load->model('incapacidades_orm');
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
     * Cargar Vista Parcial de Evaluacion
     *
     * @return void
     */
    public function formulario($data=NULL)
    {
    	$this->assets->agregar_js(array(
    		'public/assets/js/plugins/jquery/fileupload/jquery.fileupload.js',
    		'public/assets/js/modules/incapacidades/formulario.controller.js'
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
    	$this->template->vista_parcial(array(
    		'incapacidades',
    		'crear'
    	));
    }
    
    function ajax_seleccionar_incapacidad()
    {
    	$incapacidad_id =  $this->input->post('id', true);
    	 
    	if(empty($incapacidad_id)){
    		return false;
    	}
    	 
    	$incapacidad = Incapacidades_orm::where("id", $incapacidad_id)->where("empresa_id", $this->empresa_id)->get()->toArray();
    
    	if(!empty($incapacidad)){
    		$incapacidad = $incapacidad[0];
    
    		if(!empty($incapacidad["fecha_desde"])){
    			$incapacidad["fecha_desde"] = date("d/m/Y", strtotime($incapacidad["fecha_desde"]));
    		}
    		if(!empty($incapacidad["fecha_hasta"])){
    			$incapacidad["fecha_hasta"] = date("d/m/Y", strtotime($incapacidad["fecha_hasta"]));
    		}
    	}
    	 
    	echo json_encode($incapacidad);
    	exit;
    }
    
    function ajax_guardar_incapacidad()
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
    		 
    		$incapacidad_id		= $this->input->post('incapacidad_id', true);
    		$colaborador_id 	= $this->input->post('colaborador_id', true);
    		$tipo_incapacidad_id = $this->input->post('tipo_incapacidad_id', true);
    		$dias_disponibles_id = $this->input->post('dias_disponibles_id', true);
    		$fecha_desde 		= $this->input->post('fecha_desde', true);
    		$fecha_desde		= !empty($fecha_desde) ? str_replace('/', '-', $fecha_desde) : "";
    		$fecha_desde 		= !empty($fecha_desde) ? date("Y-m-d", strtotime($fecha_desde)) : "";
    		$fecha_hasta 		= $this->input->post('fecha_hasta', true);
    		$fecha_hasta		= !empty($fecha_hasta) ? str_replace('/', '-', $fecha_hasta) : "";
    		$fecha_hasta 		= !empty($fecha_hasta) ? date("Y-m-d", strtotime($fecha_hasta)) : "";
    		$cuenta_pasivo_id 	= $this->input->post('cuenta_pasivo_id', true);
    		$observaciones 		= $this->input->post('observaciones', true);
    		$incapacidad_pagada_id = $this->input->post('incapacidad_pagada_id', true);
				$estado_id 			= $this->input->post('estado_id', true);
				$certificado_medico = $this->input->post('certificado_medico', true);
				$certificado_medico = $certificado_medico == true ? 1 : 0;
				$carta_descuento 	= $this->input->post('carta_descuento', true);
				$carta_descuento 	= $carta_descuento == true ? 1 : 0;
				$constancia_institucion_medica 	= $this->input->post('constancia_institucion_medica', true);
				$constancia_institucion_medica 	= $constancia_institucion_medica == true ? 1 : 0;

				//****************************************************************************************************** dath
				$orden_medica_hospitalizacion = $this->input->post('orden_medica_hospitalizacion', true);
				$orden_medica_hospitalizacion = $orden_medica_hospitalizacion == true ? 1: 0;
				$orden_css_pension = $this->input->post('orden_css_pension', true);
				$orden_css_pension = $orden_css_pension == true ? 1:0;
				$desgloce_salario = $this->input->post('desgloce_salario', true);
				$desgloce_salario = $desgloce_salario == true ?1:0;
				$reporte_accion_trabajo = $this->input->post('reporte_accion_trabajo', true);
				$reporte_accion_trabajo = $reporte_accion_trabajo == true?1:0;
				$certificado_incapacidad_accidente_trabajo = $this->input->post('certificado_incapacidad_accidente_trabajo', true);
				$certificado_incapacidad_accidente_trabajo = $certificado_incapacidad_accidente_trabajo == true?1:0;
				//******************************************************************************************************
    		//Verificar si existe $incapacidad_id
    		$incapacidad = Incapacidades_orm::find($incapacidad_id);
    		$colaborador = Colaboradores_orm::where("id", $colaborador_id)->with(['centro_contable', 'departamento', 'cargo'])->get()->toArray();
    		$colaborador = !empty($colaborador) ? $colaborador[0] : array();
    		
    		if(!empty($incapacidad))
    		{
    			$incapacidad->empresa_id 			= $this->empresa_id;
    			$incapacidad->fecha_desde 			= $fecha_desde;
    			$incapacidad->fecha_hasta 			= $fecha_hasta;
    			$incapacidad->colaborador_id 		= $colaborador_id;
    			$incapacidad->tipo_incapacidad_id 	= $tipo_incapacidad_id;
    			$incapacidad->dias_disponibles_id 	= $dias_disponibles_id;
    			$incapacidad->cuenta_pasivo_id 		= $cuenta_pasivo_id;
    			$incapacidad->observaciones 		= $observaciones;
    			$incapacidad->incapacidad_pagada_id = $incapacidad_pagada_id;
    			$incapacidad->estado_id 			= $estado_id;
    			$incapacidad->certificado_medico 	= $certificado_medico;
    			$incapacidad->carta_descuento 		= $carta_descuento;
					$incapacidad->constancia_institucion_medica 		= $constancia_institucion_medica;
					//************************************************************************************************ dath
					$incapacidad->orden_medica_hospitalizacion = $orden_medica_hospitalizacion;
					$incapacidad->orden_css_pension = $orden_css_pension;
					$incapacidad->desgloce_salario = $desgloce_salario;
					$incapacidad->reporte_accion_trabajo = $reporte_accion_trabajo;
					$incapacidad->certificado_incapacidad_accidente_trabajo = $certificado_incapacidad_accidente_trabajo;
					//************************************************************************************************
    			$incapacidad->creado_por 			= $this->usuario_id;
    			$incapacidad->save();
    			
    			//Actualizar tabla accion personal
    			$incapacidad->acciones()->where("accionable_id", $incapacidad_id)->update([
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
    				"tipo_incapacidad_id"	=> $tipo_incapacidad_id,
    				"fecha_desde" 			=> $fecha_desde,
    				"fecha_hasta" 			=> $fecha_hasta,
    				"dias_disponibles_id"	=> $dias_disponibles_id,
    				"cuenta_pasivo_id" 		=> $cuenta_pasivo_id,
    				"observaciones"			=> $observaciones,
    				"incapacidad_pagada_id"	=> $incapacidad_pagada_id,
    				"estado_id" 			=> $estado_id,
    				"certificado_medico" 	=> $certificado_medico,
    				"carta_descuento" 		=> $carta_descuento,
						"constancia_institucion_medica" 		=> $constancia_institucion_medica,
						//********************************************************************************************** dath
						"orden_medica_hospitalizacion"	=> $orden_medica_hospitalizacion,
						"orden_css_pension" => $orden_css_pension,
						"desgloce_salario" => $desgloce_salario,
						"reporte_accion_trabajo" => $reporte_accion_trabajo,
						"certificado_incapacidad_accidente_trabajo" => $certificado_incapacidad_accidente_trabajo,
						//**********************************************************************************************
    				"creado_por" 			=> $this->usuario_id,
    			);
    	
    			//--------------------
    			// Guardar
    			//--------------------
    			$incapacidad = Incapacidades_orm::create($fieldset);
    			$incapacidad->acciones()->saveMany([new Accion_personal_orm([
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
    				$file_name = "incp-". rand().time() . "." . $extension;
    				
    				//Subir Archivo
    				if(move_uploaded_file($_FILE["tmp_name"], $empresa_folder . '/' . $file_name)) {
    					
    					$fieldset = array(
    						$field."_ruta" => $archivo_ruta,
    						$field. "_nombre" => $file_name,
    					);
    					Incapacidades_orm::where("id", $incapacidad->id)->update($fieldset);
    				} else{
    					log_message("error", "MODULO: ". __METHOD__ .", Linea: ". __LINE__ ." --> No se pudo subir el $field.\r\n");
    				}
    				
    				
    				/*if(Flow\Basic::save($empresa_folder . '/' . $file_name, $config, $request)){
    					 
    					$fieldset = array(
    						$field."_ruta" => $archivo_ruta,
    						$field. "_nombre" => $file_name,
    					);
    					 Vacaciones_orm::where("id", $incapacidad->id)->update();
    					 
    				}else{
    					log_message("error", "MODULO: ". __METHOD__ .", Linea: ". __LINE__ ." --> No se pudo subir el $field.\r\n");
    				}*/
    			}
    		}
    		 
    	} catch(ValidationException $e){
    	
    		// Rollback
    		Capsule::rollback();
    	
    		log_message("error", "MODULO: ". __METHOD__ .", Linea: ". __LINE__ ." --> ". $e->getMessage().".\r\n");
    	
    		echo json_encode(array(
    			"guardado" => false,
    			"mensaje" => "Hubo un error tratando de ". (!empty($incapacidad_id) ? "actualizar" : "guardar") ." la incapacidad."
    		));
    		exit;
    	}
    	
    	// If we reach here, then
    	// data is valid and working.
    	// Commit the queries!
    	Capsule::commit();
    	
    	$this->session->set_flashdata('mensaje', "Se ha ". (!empty($incapacidad_id) ? "actualizado" : "guardado") ." la incapacidad satisfactoriamente.");

    	echo json_encode(array(
    		"guardado" => true,
    		"mensaje" => "Se ha ". (!empty($incapacidad_id) ? "actualizado" : "guardado") ." la incapacidad satisfactoriamente."
    	));
    	exit;
    }

}

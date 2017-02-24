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
use Flexio\Modulo\DescuentosDirectos\Repository\DescuentosDirectosRepository as descuentoRep;
use League\Csv\Writer as Writer;
use Carbon\Carbon;

class Liquidaciones extends CRM_Controller
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

	private $descuentoRep;

	/**
	 * @var string
	 */
	protected $upload_folder = './public/uploads/';

	function __construct()
    {
        parent::__construct();

        $this->load->model('liquidaciones_orm');
        $this->load->model('usuarios/usuario_orm');
        $this->load->model('usuarios/empresa_orm');
        $this->load->model('accion_personal/accion_personal_orm');
        $this->load->model('colaboradores/colaboradores_orm');
        $this->load->model('colaboradores/colaboradores_contratos_orm');
        $this->load->model('configuracion_rrhh/tiempo_contratacion_orm');
        $this->load->model('configuracion_rrhh/cargos_orm');
        $this->load->model('configuracion_rrhh/departamentos_orm');
        $this->load->model('contabilidad/centros_orm');
				$this->load->model('descuentos/descuentos_orm');
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
				$this->descuentoRep = new descuentoRep();
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
    		'public/assets/js/modules/liquidaciones/formulario.controller.js'
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
    		'liquidaciones',
    		'crear'
    	));
    }

    function ajax_seleccionar_liquidacion()
    {
    	$liquidacion_id =  $this->input->post('id', true);

    	if(empty($liquidacion_id)){
    		return false;
    	}

    	$liquidacion = Liquidaciones_orm::where("id", $liquidacion_id)->where("empresa_id", $this->empresa_id)->get()->toArray();

    	if(!empty($liquidacion)){
    		$liquidacion = $liquidacion[0];

    		if(!empty($liquidacion["fecha_apartir"])){
    			$liquidacion["fecha_apartir"] = date("d/m/Y", strtotime($liquidacion["fecha_apartir"]));
    		}
    	}

    	echo json_encode($liquidacion);
    	exit;
    }

    function ajax_guardar_liquidacion()
    {
    	/**
    	 * Inicializar Transaccion
    	 */
    	Capsule::beginTransaction();

    	try {

    		$liquidacion_id		= $this->input->post('liquidacion_id', true);
    		$colaborador_id 	= $this->input->post('colaborador_id', true);
    		$tipo_liquidacion_id = $this->input->post('tipo_liquidacion_id', true);
    		$fecha_apartir 		= $this->input->post('fecha_apartir', true);
    		$fecha_apartir		= !empty($fecha_apartir) ? str_replace('/', '-', $fecha_apartir) : "";
    		$fecha_apartir 		= !empty($fecha_apartir) ? date("Y-m-d", strtotime($fecha_apartir)) : "";
    		$firmado_por 		= $this->input->post('firmado_por', true);
    		$cuenta_pasivo_id 	= $this->input->post('cuenta_pasivo_id', true);
    		$motivo 			= $this->input->post('motivo', true);
    		$estado_id 			= $this->input->post('estado_id', true);
			$solicitud 			= $this->input->post('solicitud', true);
			$solicitud 			= $solicitud == true ? 1 : 0;

    		//Verificar si existe $liquidacion_id
    		$liquidaciones = Liquidaciones_orm::find($liquidacion_id);
    		$colaborador = Colaboradores_orm::where("id", $colaborador_id)->with(['centro_contable', 'departamento', 'cargo'])->get()->toArray();
    		$colaborador = !empty($colaborador) ? $colaborador[0] : array();

    		if(!empty($liquidaciones))
    		{
    			$liquidaciones->empresa_id 			= $this->empresa_id;
    			$liquidaciones->fecha_apartir 		= $fecha_apartir;
    			$liquidaciones->tipo_liquidacion_id = $tipo_liquidacion_id;
    			$liquidaciones->firmado_por 		= $firmado_por;
    			$liquidaciones->motivo 				= $motivo;
    			$liquidaciones->estado_id 			= $estado_id;
    			$liquidaciones->cuenta_pasivo_id 	= $cuenta_pasivo_id;
    			$liquidaciones->solicitud 			= $solicitud;
    			$liquidaciones->creado_por 			= $this->usuario_id;
    			$liquidaciones->save();

    			//Actualizar tabla accion personal
    			$liquidaciones->acciones()->where("accionable_id", $liquidacion_id)->update([
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
    				"tipo_liquidacion_id" => $tipo_liquidacion_id,
    				"firmado_por" 		=> $firmado_por,
    				"fecha_apartir" 	=> $fecha_apartir,
    				"motivo"		=> $motivo,
    				"estado_id" 		=> $estado_id,
    				"cuenta_pasivo_id" 	=> $cuenta_pasivo_id,
    				"solicitud"			=> $solicitud,
    				"creado_por" 		=> $this->usuario_id
    			);

    			//--------------------
    			// Guardar
    			//--------------------
    			$liquidaciones = Liquidaciones_orm::create($fieldset);
    			$liquidaciones->acciones()->saveMany([new Accion_personal_orm([
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
                        $liquidaciones->contrato()->where("colaborador_id", $colaborador_id)->update([
                                "fecha_salida" => $fecha_apartir
                        ]);
					// Actualizar los descuentos directos del colaborador requerimiento #1658
					$descuentos_colaborador = Descuentos_orm::where('colaborador_id', '=', $colaborador_id)->get()->toArray();
					if(!empty($descuentos_colaborador)){
					$i = 0;
					foreach($descuentos_colaborador AS $row){
						$descuento_id[$i] = $row['id'];
						$i++;
					};
					$values = array(
						'estado_id' => 7
					);

					$descuento = Descuentos_orm::whereIn('id', $descuento_id)->update($values);
					}
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
    			$file_name = "liq-". rand().time() . "." . $extension;

    			//Subir Archivo
    			if(Flow\Basic::save($empresa_folder . '/' . $file_name, $config, $request)){

    				$liquidaciones = Liquidaciones_orm::find($liquidaciones->id);
    				$liquidaciones->archivo_ruta = $archivo_ruta;
    				$liquidaciones->archivo_nombre = $file_name;
    				$liquidaciones->save();

    			}else{
    				log_message("error", "MODULO: ". __METHOD__ .", Linea: ". __LINE__ ." --> No se pudo subir el archivo de liquidacion.\r\n");
    			}
    		}

    	} catch(ValidationException $e){

    		// Rollback
    		Capsule::rollback();

    		log_message("error", "MODULO: ". __METHOD__ .", Linea: ". __LINE__ ." --> ". $e->getMessage().".\r\n");

    		echo json_encode(array(
    			"guardado" => false,
    			"mensaje" => "Hubo un error tratando de ". (!empty($liquidacion_id) ? "actualizar" : "guardar") ." la liquidacion."
    		));
    		exit;
    	}

    	// If we reach here, then
    	// data is valid and working.
    	// Commit the queries!
    	Capsule::commit();

    	$this->session->set_flashdata('mensaje', "Se ha ". (!empty($liquidacion_id) ? "actualizado" : "guardado") ." la liquidacion satisfactoriamente.");

    	echo json_encode(array(
    		"guardado" => true,
    		"mensaje" => "Se ha ". (!empty($liquidacion_id) ? "actualizado" : "guardado") ." la liquidacion satisfactoriamente."
    	));
    	exit;
    }

}

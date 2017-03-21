<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Beneficiarios
 *
 * Modulo para administrar la creacion, edicion de beneficiarios.
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

class Beneficiarios extends CRM_Controller
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

        $this->load->model('Beneficiarios_orm');
        $this->load->model('usuarios/usuario_orm');
        $this->load->model('usuarios/empresa_orm');

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
            'public/assets/js/modules/beneficiarios/formulario.controller.js'
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
            'beneficiarios',
            'crear'
        ));



    }

    function ajax_guardar_licencia()
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

            $licencia_id		= $this->input->post('licencia_id', true);
            $beneficiario_id 	= $this->input->post('beneficiario_id', true);
            $tipo_licencia_id 	= $this->input->post('tipo_licencia_id', true);
            $fecha_desde 		= $this->input->post('fecha_desde', true);
            $fecha_desde		= !empty($fecha_desde) ? str_replace('/', '-', $fecha_desde) : "";
            $fecha_desde 		= !empty($fecha_desde) ? date("Y-m-d", strtotime($fecha_desde)) : "";
            $fecha_hasta 		= $this->input->post('fecha_hasta', true);
            $fecha_hasta		= !empty($fecha_hasta) ? str_replace('/', '-', $fecha_hasta) : "";
            $fecha_hasta 		= !empty($fecha_hasta) ? date("Y-m-d", strtotime($fecha_hasta)) : "";
            $cuenta_pasivo_id 	= $this->input->post('cuenta_pasivo_id', true);
            $estado_id 			= $this->input->post('estado_id', true);
            $observaciones 		= $this->input->post('observaciones', true);
            $licencia_pagada_id = $this->input->post('licencia_pagada_id', true);
            $carta_sindical 	= $this->input->post('carta_sindical', true);
            $carta_sindical 	= $carta_sindical == true ? 1 : 0;

            //Verificar si existe $licencia_id
            $licencia = Licencias_orm::find($licencia_id);

            if(!empty($licencia))
            {
                $licencia->empresa_id 		= $this->empresa_id;
                $licencia->fecha_desde 		= $fecha_desde;
                $licencia->fecha_hasta 		= $fecha_hasta;
                $licencia->tipo_licencia_id	= $tipo_licencia_id;
                $licencia->cuenta_pasivo_id = $cuenta_pasivo_id;
                $licencia->estado_id 		= $estado_id;
                $licencia->observaciones 	= $observaciones;
                $licencia->licencia_pagada_id = $licencia_pagada_id;
                $licencia->carta_sindical 	= $carta_sindical;
                $licencia->creado_por 		= $this->usuario_id;
                $licencia->save();
            }
            else
            {
                $fieldset = array(
                    "empresa_id" 		=> $this->empresa_id,
                    "beneficiario_id" 	=> $beneficiario_id,
                    "fecha_desde" 		=> $fecha_desde,
                    "fecha_hasta" 		=> $fecha_hasta,
                    "tipo_licencia_id"	=> $tipo_licencia_id,
                    "cuenta_pasivo_id" 	=> $cuenta_pasivo_id,
                    "estado_id" 		=> $estado_id,
                    "observaciones"		=> $observaciones,
                    "licencia_pagada_id" => $licencia_pagada_id,
                    "carta_sindical" 	=> $carta_sindical,
                    "creado_por" 		=> $this->usuario_id,
                );

                //--------------------
                // Guardar Vacacion
                //--------------------
                $licencia = Licencias_orm::create($fieldset);
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
                //del modulo de beneficiarios en uploads
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

                    $licencia = Licencias_orm::find($licencia->id);
                    $licencia->archivo_ruta = $archivo_ruta;
                    $licencia->archivo_nombre = $file_name;
                    $licencia->save();

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
                "mensaje" => "Hubo un error tratando de ". (!empty($licencia_id) ? "actualizar" : "guardar") ." la vacacion."
            ));
            exit;
        }

        // If we reach here, then
        // data is valid and working.
        // Commit the queries!
        Capsule::commit();

        $this->session->set_flashdata('mensaje', "Se ha ". (!empty($licencia_id) ? "actualizado" : "guardado") ." la licencia satisfactoriamente.");

        echo json_encode(array(
            "guardado" => true,
            "mensaje" => "Se ha ". (!empty($licencia_id) ? "actualizado" : "guardado") ." la licencia satisfactoriamente."
        ));
        exit;
    }
}

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

class Documentos extends CRM_Controller
{
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
        $usuario = Usuario_orm::findByUuid($uuid_usuario);
        $this->usuario_id = $usuario->id;

        $this->nombre_modulo = strtolower(get_class($this));

        $this->DocumentosRepository = new DocumentosRepository();
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

    	$this->load->view('tabla');
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
    		"titulo" => '<i class="fa fa-users"></i> Documentos'
    	);

    	$this->template->agregar_titulo_header('Acciones de Personal');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar($breadcrumb);
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
        $intereses_asegurados = false;

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

			//dd($clause);

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
                if(is_array($nombre_doc)){
                foreach($nombre_doc AS $value){
                    $nombre_docu = $value;
                }
                }else{
		$nombre_docu = !empty($_POST['nombre_documento']) ? $_POST['nombre_documento'] : '';
				}

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
				$type 		= $_FILE["type"][$j];
				$tmp_name 	= $_FILE["tmp_name"][$j];

				$extension = pathinfo($filename, PATHINFO_EXTENSION);
				$file_name = preg_replace('/[^A-Za-z0-9\-.]/', '', $filename);
				if(move_uploaded_file($tmp_name, $empresa_folder . '/' . $secuencial . "-" . $file_name)) {

					$documentos[$i]["archivo_ruta"] = $archivo_ruta;
					$documentos[$i]["archivo_nombre"] = $secuencial . "-" . $file_name;
					$documentos[$i]["nombre_documento"] = !empty($nombre_docu) ? $nombre_docu : '';

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
               // dd($string);
                return $string;


	}
}
?>

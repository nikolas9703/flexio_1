<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Flexio
 * @subpackage Controller
 * @category   Ordenes de Trabajo
 * @author     Pensanomica Team
 * @link       http://www.pensanomica.com
 * @copyright  06/13/2016
 */

use Carbon\Carbon as Carbon;
use Dompdf\Dompdf;
use League\Csv\Writer as Writer;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Cliente\Repository\ClienteRepository;
use Flexio\Modulo\OrdenesTrabajo\Repository\OrdenesTrabajoRepository;
use Flexio\Modulo\OrdenesTrabajo\Repository\OrdenesTrabajoCatalogoRepository;
use Flexio\Modulo\Bodegas\Repository\BodegasRepository;
use Flexio\Modulo\Inventarios\Repository\CategoriasRepository AS ItemsCategoriasRepository;
use Flexio\Modulo\Cotizaciones\Repository\LineItemRepository as LineItemRepository;
use Flexio\Modulo\Inventarios\Repository\ItemsRepository;
use Flexio\Modulo\Talleres\Repository\EquipoTrabajoRepository;
use Flexio\Modulo\Inventarios\Repository\PreciosRepository as ItemsPreciosRepository;
use Flexio\Modulo\Contabilidad\Repository\CuentasRepository;
use Flexio\Modulo\Contabilidad\Repository\ImpuestosRepository;
use Flexio\Modulo\FacturasVentas\Repository\FacturaVentaCatalogoRepository;
use Flexio\Modulo\FacturasVentas\Repository\FacturaVentaRepository as FacturaVentaRepository;
use Flexio\Modulo\Usuarios\Repository\UsuariosRepository;
use Flexio\Modulo\CentrosContables\Repository\CentrosContablesRepository;
use Flexio\Modulo\Cotizaciones\Repository\CotizacionRepository as CotizacionRepository;

class Ordenes_trabajo extends CRM_Controller {

	/**
	 * @var int
	 */
	protected $usuario_id;

	/**
	 * @var int
	 */
	protected $empresa_id;

	/**
	 * @var object
	 */
	protected $ClienteRepository;

	/**
	 * @var object
	 */
	protected $BodegasRepository;

	/**
	 * @var object
	 */
	protected $OrdenesTrabajoRepository;

	/**
	 * @var object
	 */
	protected $OrdenesTrabajoCatalogoRepository;

	/**
	 * @var object
	 */
	protected $ItemsRepository;

	/**
	 * @var object
	 */
	protected $EquipoTrabajoRepository;

	/**
	 * @var object
	 */
	protected $ItemsCategoriasRepository;
    protected $ItemsPreciosRepository;
    protected $CuentasRepository;
    protected $ImpuestosRepository;
    protected $cotizacionRepository;
    protected $UsuariosRepository;
    protected $facturaVentaRepository;
    protected $CentrosContablesRepository;
    protected $FacturaVentaCatalogoRepository;
		protected $DocumentosRepository;

	function __construct() {
		parent::__construct ();
		$this->load->model('usuarios/usuario_orm');
		$this->load->model('usuarios/empresa_orm');
		$this->load->model("ajustes/Ajustes_orm");
		$this->load->model("traslados/Traslados_orm");
		$this->load->model("ordenes/Ordenes_orm");
		$this->load->module(array('inventarios/Inventarios', 'documentos'));
		$this->load->model('contabilidad/Cuentas_orm');


		//Obtener el id de usuario de session
		$uuid_usuario 							= $this->session->userdata('huuid_usuario');
		$usuario 								= Usuario_orm::findByUuid($uuid_usuario);
		$this->usuario_id 						= $usuario->id;

		//Obtener el id_empresa de session
		$uuid_empresa 							= $this->session->userdata('uuid_empresa');
		$empresa 								= Empresa_orm::findByUuid($uuid_empresa);
		$this->empresa_id 						= $empresa->id;

		$this->ClienteRepository 				= new ClienteRepository();

		$this->OrdenesTrabajoRepository 		= new OrdenesTrabajoRepository();

		$this->OrdenesTrabajoCatalogoRepository = new OrdenesTrabajoCatalogoRepository();

		$this->BodegasRepository 				= new BodegasRepository();

		$this->ItemsCategoriasRepository 		= new ItemsCategoriasRepository();

		$this->ItemsRepository 					= new ItemsRepository();

		$this->lineItemRepository 				= new LineItemRepository;

		$this->EquipoTrabajoRepository 			= new EquipoTrabajoRepository();
        $this->ItemsPreciosRepository           = new ItemsPreciosRepository();
        $this->CuentasRepository                = new CuentasRepository();
        $this->ImpuestosRepository              = new ImpuestosRepository();
        $this->UsuariosRepository               = new UsuariosRepository();
        $this->facturaVentaRepository           = new FacturaVentaRepository;
        $this->cotizacionRepository             = new CotizacionRepository;
        $this->CentrosContablesRepository       = new CentrosContablesRepository();
        $this->FacturaVentaCatalogoRepository = new FacturaVentaCatalogoRepository();
	}

	public function listar() {
		$data = array();

		$this->assets->agregar_css(array(
			'public/assets/css/default/ui/base/jquery-ui.css',
			'public/assets/css/default/ui/base/jquery-ui.theme.css',
			'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
			'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
			'public/assets/css/plugins/jquery/chosen/chosen.min.css',
			'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
			'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
			'public/assets/css/modules/stylesheets/ordenes_trabajo.css',
			'public/assets/css/plugins/jquery/jquery.fileupload.css',
		));
		$this->assets->agregar_js(array(
			'public/assets/js/default/jquery-ui.min.js',
			'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
			'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
			'public/assets/js/moment-with-locales-290.js',
			'public/assets/js/plugins/jquery/chosen.jquery.min.js',
			'public/assets/js/plugins/bootstrap/daterangepicker.js',
			'public/assets/js/default/toast.controller.js',
			'public/assets/js/default/formulario.js',
			'public/assets/js/plugins/jquery/fileupload/jquery.fileupload.js',
		));

		//------------------------------------------
		// Para mensaje de creacion satisfactoria
		//------------------------------------------
		$mensaje = !empty($this->session->flashdata('mensaje')) ? json_encode(array('estado' => 200, 'mensaje' => $this->session->flashdata('mensaje'))) : '';
		$this->assets->agregar_var_js(array(
			"toast_mensaje" => $mensaje
		));

		$estados = $this->OrdenesTrabajoCatalogoRepository->getEstados()->toArray();
		$estados = (!empty($estados) ? array_map(function($estados) {
			return array(
				"id" 		=> $estados["id"],
				"nombre" 	=> $estados["etiqueta"]
			);
		}, $estados) : "");
		$data["estados"] = $estados;

		//Opcion Default
		$menuOpciones = array();

		//Breadcrum Array
		$breadcrumb = array(
			"titulo" => '<i class="fa fa-wrench"></i> &Oacute;rdenes de trabajo'
		);
		$breadcrumb["menu"] = array(
			"url"	=> "ordenes_trabajo/crear",
			"nombre" => "Crear"
		);
		$menuOpciones["#exportarLnk"] = "Exportar";
		$breadcrumb["menu"]["opciones"] = $menuOpciones;

		$this->template->agregar_titulo_header('Servicios');
		$this->template->agregar_breadcrumb($breadcrumb);
		$this->template->agregar_contenido($data);
		$this->template->visualizar($breadcrumb);
	}

	public function ajax_listar($grid = NULL) {
		$clause = array(
				"empresa_id" => $this->empresa_id
		);
       // dd($this->empresa_id, $this->usuario_id);
		$no_orden 		= $this->input->post('no_orden', true);
		$cliente 		= $this->input->post('cliente', true);
		$estado_id 		= $this->input->post('estado_id', true);
		$fecha_desde 	= $this->input->post('fecha_desde', true);
		$fecha_hasta 	= $this->input->post('fecha_hasta', true);
		$equipo_id 		= $this->input->post('equipo_id', true);

		if(!empty($no_orden)) {
			$clause["numero"] 		= array('LIKE', "%$no_orden%");
		}
		if(!empty($cliente)) {
			$clause["cliente"] 		= array('LIKE', "%$cliente%");
		}
		if(!empty($estado_id)) {
			$clause["estado_id"] 	= $estado_id;
		}
		if(!empty($fecha_desde)) {
    		$fecha_desde = str_replace('/', '-', $fecha_desde);
    		$fecha_inicio = date("Y-m-d H:i:s", strtotime($fecha_desde));
    		$clause["fecha_inicio"] = array('>=', $fecha_inicio);
    	}
    	if(!empty($fecha_hasta)) {
    		$fecha_hasta = str_replace('/', '-', $fecha_hasta);
    		$fecha_fin = date("Y-m-d 23:59:59", strtotime($fecha_hasta));
    		$clause["fecha_inicio@"] = array('<=', $fecha_fin);
    	}
    	if(!empty($equipo_id)){
    		$clause["equipo_id"] = $equipo_id;
    	}

		list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();

		$count = $this->OrdenesTrabajoRepository->listar($clause, NULL, NULL, NULL, NULL)->count();

		list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

		$rows = $this->OrdenesTrabajoRepository->listar($clause, $sidx, $sord, $limit, $start);

		//Constructing a JSON
		$response 			= new stdClass();
		$response->page 	= $page;
		$response->total 	= $total_pages;
		$response->records 	= $count;
		$response->result 	= array();
		$i = 0;

		if (!empty($rows->toArray())) {
			foreach ($rows->toArray() AS $i => $row) {

				$uuid_orden = $row["uuid_orden_trabajo"];
				$uuid_cliente = $row["cliente"]["uuid_cliente"];

				$hidden_options = "";
				$link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row['id'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

				$hidden_options .= '<a href="' . base_url("ordenes_trabajo/ver/$uuid_orden") . '" data-id="' . $row['id'] . '" class="btn btn-block btn-outline btn-success">Ver Detalle</a>';
				$hidden_options .= '<a href="' . base_url("ordenes_trabajo/historial/$uuid_orden") . '" data-id="'. $uuid_orden .'" class="btn btn-block btn-outline btn-success">Ver Bit&aacute;cora</a>';

				//motrar boton facturar si es facturable(si)
				if(preg_match("/por facturar/i", Util::verificar_valor($row["estado"]["etiqueta"]))){
					$hidden_options .= '<a href="' . base_url("cajas/transferir/$uuid_orden") . '" class="btn btn-block btn-outline btn-success m-t-xs" data-id="' . $row['id'] . '">Facturar</a>';
				}
				$hidden_options .= '<a href="#" class="btn btn-block btn-outline btn-success subirArchivoBtn" data-id="'. $row['id'] .'" data-uuid="'. $uuid_orden .'" >Subir archivo</a>';

				$label_estado = str_replace(" ", "-", strtolower($row["estado"]["etiqueta"]));

				$response->rows[$i]["id"] = $row['id'];
				$response->rows[$i]["cell"] = array(
					'<a href="' . base_url("ordenes_trabajo/ver/$uuid_orden") . '" style="color:blue;">' . Util::verificar_valor($row["numero"]) . '</a>',
					'<a href="' . base_url("clientes/ver/$uuid_cliente") . '" style="color:blue;">' . Util::verificar_valor($row["cliente"]["nombre"]). '</a>',
					$row['fecha_inicio'] !="" ? date("d/m/Y", strtotime($row['fecha_inicio'])) : "",
					Util::verificar_valor($row["centro"]["nombre"]),
					'<span class="label label-'. $label_estado .'">' . Util::verificar_valor($row["estado"]["etiqueta"]).'</span>',
					$link_option,
					$hidden_options,
				);
				$i++;
			}
		}

		echo json_encode($response);
		exit;
	}

	public function ocultotabla($modulo_id=NULL) {
		$this->assets->agregar_js(array(
			'public/assets/js/modules/ordenes_trabajo/listar.js',
			'public/assets/js/modules/ordenes_trabajo/tabla.js'
		));

		//Filtrar seleccion en tabla para modulo de Planilla
		if(preg_match("/planilla/i", $this->router->fetch_class())) {
			if(is_array($modulo_id)) {
				$index 		= "";
				$moduloval 	= "";
				foreach ($modulo_id AS $index => $value) {
					$index 		= preg_replace("/(es|s)$/i", "_id", $index);
					$moduloval 	= $value;
				}
				if(!empty($index)) {
					$this->assets->agregar_var_js(array(
						$index => json_encode($moduloval)
					));
				}
			}else{
				$this->assets->agregar_var_js(array(
					"modulo_id" => $modulo_id
				));
			}
		}

		$this->load->view('tabla');
	}

	public function crear($orden_uuid = NULL) {
		$data 				= array();
		$mensaje 			= array();
		$titulo_formulario 	= '<i class="fa fa-wrench"></i> &Oacute;rdenes de trabajo: Crear';

		$this->assets->agregar_css(array(
			'public/assets/css/default/ui/base/jquery-ui.css',
			'public/assets/css/default/ui/base/jquery-ui.theme.css',
			'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
			'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
			'public/assets/css/plugins/bootstrap/awesome-bootstrap-checkbox.css',
			'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
			'public/assets/css/plugins/bootstrap/jquery.bootstrap-touchspin.css',
			'public/assets/css/plugins/jquery/chosen/chosen.min.css',
			'public/assets/css/modules/stylesheets/ordenes_trabajo.css',
      'public/assets/css/plugins/jquery/jquery.webui-popover.css',
      'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
      'public/assets/css/plugins/bootstrap/select2.min.css',
		));
		$this->assets->agregar_js(array(
			'public/assets/js/default/jquery-ui.min.js',
			'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
			'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
			'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
			'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
			'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
			'public/assets/js/default/jquery-ui.min.js',
			'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
			'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
			'public/assets/js/default/lodash.min.js',
			'public/assets/js/default/vue-validator.min.js',
			'public/assets/js/moment-with-locales-290.js',
			'public/assets/js/plugins/jquery/chosen.jquery.min.js',
			'public/assets/js/default/tabla-dinamica.jquery.js',
			'public/assets/js/default/formulario.js',
			'public/assets/js/modules/ordenes_trabajo/vue.servicios.js',
			'public/assets/js/modules/ordenes_trabajo/vue.items.js',
			//'public/assets/js/modules/ordenes_trabajo/vue.crear.js',

      //Se cargan JS de la tabla dinamica de items con type head.
      'public/assets/js/default/vue/directives/inputmask.js',
      'public/assets/js/default/vue/directives/select2.js',
      'public/assets/js/plugins/bootstrap/select2/select2.min.js',
      'public/assets/js/plugins/bootstrap/select2/es.js',
      'public/assets/js/plugins/jquery/jquery.webui-popover.js',
      'public/assets/js/default/vue/directives/pop_over_precio.js',
      'public/assets/js/default/vue/directives/pop_over_cantidad.js',
      'public/resources/compile/modulos/ordenes_trabajo/formulario.js',
		));

		//Crea variables js
		$this->_crear_variables_catalogos($orden_uuid);

		//Si existe UUID
		if ($orden_uuid != NULL) {

			$info = $this->OrdenesTrabajoRepository->findByUuid($orden_uuid);
			$titulo_formulario 	= '<i class="fa fa-wrench"></i> &Oacute;rdenes de trabajo: '. (!empty($info->numero) ? $info->numero : "");

			//subpanels
			$data['ordenes_trabajo_id'] = $info->id;
			$subpanels = [
				'ordenes_trabajo'=>$info->id,
				'clientes'=>$info->id,
			];
			$data['subpanels'] = $subpanels;

			$this->assets->agregar_var_js(array(
					'cliente' => $info->cliente_id
			));
		}

		$breadcrumb = array(
			"titulo" => $titulo_formulario,
		);
                if ($orden_uuid!= NULL) {
                        if ($info->tipo_orden_id==1) {
                            //imprimir solo para servicio
                            $breadcrumb["menu"]["opciones"]["ordenes_trabajo/imprimir_orden_trabajo/" . $info->uuid_orden_trabajo] = "Imprimir";
                            $breadcrumb["menu"]["opciones"]["ordenes_trabajo/historial/" . $info->uuid_orden_trabajo] = "Ver bit&aacute;cora";
                        }
                }
		$this->template->agregar_titulo_header('Nueva Orden de Trabajo');
		$this->template->agregar_breadcrumb($breadcrumb);
		$this->template->agregar_contenido($data);
		$this->template->visualizar();
        }

        public function imprimir_orden_trabajo($uuid) {
            if (!empty($uuid)) {
                $info = $this->OrdenesTrabajoRepository->findByUuid($uuid);
                //mandar informacion
                $history = $this->OrdenesTrabajoRepository->getLastEstadoHistory($info->id);

                $dompdf = new Dompdf();

                $data = ['info' => $info, 'history' => $history];

                $html = $this->load->view('pdf/ordendetrabajo', $data, true);
                //echo '<pre>'. $html . '</pre>'; die;

                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();
                $dompdf->stream($info->codigo . ' - ' . $info->cliente->nombre);
            }
        }


	public function cargar_templates_vue() {
		$this->load->view('componente_servicios');
		$this->load->view('componente_items');
	}

	public function exportar() {

		if(empty($_POST)){
			exit;
		}

		$ids =  $this->input->post('ids', true);
		$id = explode(",", $ids);

		if(empty($id)){
			return false;
		}

		$csv = array();
		$clause = array(
			"empresa_id" => $this->empresa_id,
			"id" => $id
		);
		$ordenes = $this->OrdenesTrabajoRepository->getAll($clause);

		if(empty($ordenes)){
			return false;
		}

		$i=0;
		foreach ($ordenes AS $row)
		{
			$csvdata[$i]['numero'] = Util::verificar_valor($row['numero']);
			$csvdata[$i]['cliente'] = utf8_decode(Util::verificar_valor($row["cliente"]["nombre"]));
			$csvdata[$i]["fecha_inicio"] = $row['fecha_inicio'] !="" ? date("d/m/Y", strtotime($row['fecha_inicio'])) : "";
			$csvdata[$i]["centro_contable"] = utf8_decode(Util::verificar_valor($row["centro"]["nombre"]));
			$csvdata[$i]["estado"] = utf8_decode(Util::verificar_valor($row["estado"]["etiqueta"]));
			$i++;
		}

		//we create the CSV into memory
		$csv = Writer::createFromFileObject(new SplTempFileObject());
		$csv->insertOne([
				'Numero',
				'Cliente',
				'Fecha de Inicio',
				'Centro Contable',
				'Estado'
				]);
		$csv->insertAll($csvdata);
		$csv->output("ordenes-trabajo-". date('ymd') .".csv");
		die;
	}

	public function ajax_seleccionar_cat_orden_de() {
		if(empty($_POST)) {
			return false;
		}

		$orden_de = $this->input->post('orden_de', TRUE);

		if(preg_match("/(cliente)/i", $orden_de)) {

			//Listado de clientes
			$clientes = $this->_seleccionarListadoClientes();
		}

		echo json_encode($response);
		exit;
	}

	public function ajax_guardar_orden() {

		 /*echo "<pre>";
		print_r($_POST);
		echo "</pre>";
		die('HERE YES');*/

		/**
		 * Inicializar Transaccion
		 */
		Capsule::beginTransaction();

		try {
            $input = Illuminate\Http\Request::createFromGlobals();
            $lineitems = $input->input("items");
           // dd($lineitems);
			$id = $this->input->post('id', true);
			$servicios = $this->input->post('servicios', true);

           /// dd($_POST);
			$delete_item = $this->input->post('delete_items', true);
			unset($_POST["servicios"]);
			unset($_POST["credito_favor"]);
			unset($_POST["delete_items"]);
			unset($_POST["saldo_pendiente_acumulado"]);

			//$fieldset = Util::set_fieldset(true);
            $fieldset = array();

            $fieldset["orden_de"] =  !empty($_POST["orden_de"]) ? $_POST["orden_de"] : "0";
            $fieldset["orden_de_id"] =  !empty($_POST["orden_de_id"]) ? $_POST["orden_de_id"] : "0";
            $fieldset["cliente_id"] = $_POST["cliente_id"];
			$fieldset["tipo_orden_id"] = $_POST["tipo_orden_id"];

			$fieldset["equipo_trabajo_id"] = !empty($_POST["equipo_trabajo_id"]) ? $_POST["equipo_trabajo_id"] : "0";
			$fieldset["centro_facturable_id"] = !empty($_POST["centro_facturable_id"]) ? $_POST["centro_facturable_id"] : "0";
           // dd($fieldset);
            //Darle mformato a la fecha
            $fieldset["fecha_inicio"] = Carbon::createFromFormat('d/m/Y', $_POST["fecha_inicio"])->format('Y-m-d');
            if (!empty($_POST["fecha_planificada_fin"])) {
                $fieldset["fecha_planificada_fin"] = Carbon::createFromFormat('d/m/Y', $_POST["fecha_planificada_fin"])->format('Y-m-d');
            }
            if (!empty($_POST["fecha_real_fin"])){
                $fieldset["fecha_real_fin"] = Carbon::createFromFormat('d/m/Y',$_POST["fecha_real_fin"])->format('Y-m-d');
            }
            $fieldset["centro_id"] = $_POST["centro_id"];
            $fieldset["lista_precio_id"] = $_POST["lista_precio_id"];
            $fieldset["facturable_id"] = $_POST["facturable_id"];
            $fieldset["bodega_id"] = $_POST["bodega_id"];
            $fieldset["estado_id"] = $_POST["estado_id"];
            //$fieldset["subtotal"] = $_POST["subtotal"];
           // $fieldset["descuento"] = $_POST["descuento"];
           // $fieldset["impuestos"] = $_POST["impuestos"];
           // $fieldset["total"] = $_POST["total"];
            $fieldset["comentario"] = $_POST["comentario"];
            $fieldset["id"] = $_POST["id"];

           // dd($servicios);
            if (!empty($servicios)) {
                $fieldset["servicios"] = $servicios;
            }else{
                $fieldset["items"] = $lineitems;

            }
            //dd($fieldset);

			//--------------------
			// Elimninar Items
			//--------------------
			if (!empty($delete_item)) {
				$ids = explode(',', $delete_item);
				$this->lineItemRepository->delete($ids);
			}
            $clause = array(
                "empresa_id" => $this->empresa_id
            );
			//--------------------
			// Guardar/Actualizar
			// Orden de TRabajo
			//--------------------
			if(empty($id)) {
                                $fieldset["uuid_orden_trabajo"] = Capsule::raw("ORDER_UUID(uuid())");
                                $total = $this->OrdenesTrabajoRepository->listar($clause, NULL, NULL, NULL, NULL)->count();
                                $year = Carbon::now()->format('y');
                                $codigo = Util::generar_codigo('ODT' . $year, $total + 1, strlen(($total + 1) . "") > 1 ? 6 : 4);
                                $fieldset["numero"] = $codigo;
                              //  $fieldset["numero"] = Capsule::raw("NO_ORDEN_TRABAJO('ODT', " . $this->empresa_id . ")");
                                $fieldset["empresa_id"] = $this->empresa_id;
                                $fieldset["creado_por"] = $this->usuario_id;
                                //dd($fieldset);
				$modelOrdenTrabajo = $this->OrdenesTrabajoRepository->create($fieldset);
			} else {
				$modelOrdenTrabajo = $this->OrdenesTrabajoRepository->update($fieldset);
			}

		} catch (ValidationException $e) {

            // Rollback
            Capsule::rollback();

            log_message("error", "MODULO: " . __METHOD__ . ", Linea: " . __LINE__ . " --> " . $e->getMessage() . ".\r\n");

            echo json_encode(array(
                "guardado" => false,
                "mensaje" => "Hubo un error tratando de " . (!empty($id) ? "actualizar" : "guardar") . " la orden."
            ));
            exit;
        }
        if (!is_null($modelOrdenTrabajo)) {
            Capsule::commit();
            $model = $modelOrdenTrabajo->fresh();
            $mensaje = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente ' . $model->codigo);
            echo json_encode(array(
                "guardado" => true,
                "mensaje" => "Se ha " . (!empty($id) ? "actualizado" : "guardado") . " la orden satisfactoriamente."
            ));
        } else {
            $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error! Su solicitud no fue procesada</b> ');
            echo json_encode(array(
                "guardado" => false,
                "mensaje" => "Hubo un error tratando de " . (!empty($id) ? "actualizar" : "guardar") . " la orden."
            ));
        }
        $this->session->set_flashdata('mensaje', "Se ha " . (!empty($id) ? "actualizado" : "guardado") . " la orden satisfactoriamente.");

        // If we reach here, then
        // data is valid and working.
        // Commit the queries!
       // Capsule::commit();

        /*echo json_encode(array(
            "guardado" => true,
            "mensaje" => "Se ha " . (!empty($id) ? "actualizado" : "guardado") . " la orden satisfactoriamente."
        ));*/
        exit;
	}

	public function postDataProcedure($data) {

    }

	public function ajax_eliminar_item() {

		$item_id = $this->input->post('id', true);
		$response = array();

		try {
			$response = $this->OrdenesTrabajoRepository->deletePieza(array("id" => $item_id));
		} catch (ValidationException $e) {

			// Rollback
			Capsule::rollback();

			log_message("error", "MODULO: " . __METHOD__ . ", Linea: " . __LINE__ . " --> " . $e->getMessage() . ".\r\n");

			echo json_encode(array(
				"eliminado" => false,
				"mensaje" => "Hubo un error tratando de eliminar la pieza."
			));
			exit;
		}

		// If we reach here, then
		// data is valid and working.
		// Commit the queries!
		Capsule::commit();

		echo json_encode(array(
			"eliminado" => $response
		));
		exit;
	}

	public function ajax_eliminar_servicio() {

		$servicio_id = $this->input->post('id', true);
		$response = array();

		try {
			$response = $this->OrdenesTrabajoRepository->deleteServicio(array("id" => $servicio_id));
		} catch (ValidationException $e) {

			// Rollback
			Capsule::rollback();

			log_message("error", "MODULO: " . __METHOD__ . ", Linea: " . __LINE__ . " --> " . $e->getMessage() . ".\r\n");

			echo json_encode(array(
				"eliminado" => false,
				"mensaje" => "Hubo un error tratando de eliminar el item."
			));
			exit;
		}

		// If we reach here, then
		// data is valid and working.
		// Commit the queries!
		Capsule::commit();

		echo json_encode(array(
			"eliminado" => $response
		));
		exit;
	}

	public function ajax_seleccionar_items() {

		$categoria_id = $this->input->post('categoria_id', true);
		$items = $this->_seleccionarListaItems($categoria_id);

		$response = new stdClass();
		$response->items = $items;
		echo json_encode($response);
		exit;
	}

	public function ajax_seleccionar_items_serializados(){
		$categoria_id = $this->input->post('categoria_id', true);

		$items = $this->_seleccionarItemsSerializadosByCategoria($categoria_id);

		$response = new stdClass();
		$response->items = $items;
		echo json_encode($response);
		exit;
	}

	public function ajax_seleccionar_series_de_item() {
		$item_id = $this->input->post('item_id', true);
		$categoria_item_id = $this->input->post('categoria_item_id', true);

		$series = $this->_seleccionarSeries($categoria_item_id, $item_id);

		$response = new stdClass();
		$response->series = $series;
		echo json_encode($response);
		exit;
	}

	public function ajax_seleccionar_items_servicio_por_categoria() {
		$categoria_id = $this->input->post('categoria_id', true);

		$items = $this->_seleccionarItemsByCategoriasServicio($categoria_id);

		$response = new stdClass();
		$response->items_servicios = $items;
		echo json_encode($response);
		exit;
	}

	public function ajax_seleccionar_items_utilizados_por_categoria() {
		$categoria_item_id = $this->input->post('categoria_item_id', true);

		$items = $this->_seleccionarItemsPiezas($categoria_item_id);

		$response = new stdClass();
		$response->items = $items;
		echo json_encode($response);
		exit;
	}

	public function ajax_seleccionar_unidades_item() {
		$item_id 			= $this->input->post('item_id', true);
		$categoria_item_id 	= $this->input->post('categoria_item_id', true);

		$unidades = $this->_seleccionarItemUnidades($item_id, $categoria_item_id);

		$response = new stdClass();
		$response->unidades = $unidades;
		echo json_encode($response);
		exit;
	}

	public function ajax_get_equipotrabajo_info() {
		$equipo_id = $this->input->post('equipo_id', true);

		if(empty($equipo_id)){
			return false;
		}

		$info = $this->EquipoTrabajoRepository->find($equipo_id);

		$response = new stdClass();
		$response->info = $info;
		echo json_encode($response);
		exit;
	}

	public function ajax_seleccionar_orden() {
		$uuid = $this->input->post('uuid', true);
		$item_facturado = [];

		if(empty($uuid)){
			return false;
		}

		$ordenes = $this->OrdenesTrabajoRepository->findByUuid($uuid);
		$ordenes->load('cliente.centro_facturable','facturas', "servicios", 'servicios.piezas', "servicios.piezas.item");

		foreach($ordenes->facturas as $items){
			foreach(explode(",", $items->pivot->items_facturados) as $id){
				$item_facturado[] = (int)$id;
			}
		}
		$j=0;
		$items = [];
		foreach($ordenes->servicios as $item) {
			$items[$j] = array(
				"categoria_id" => $item->categoria_servicio_id,
				"item_id" => $item->item_servicio_id,
				"atributo_id" => "",
				"cantidad" => "",
				"precio_unidad" => "",
				"precio_total" => "",
			);

			if(!empty($item->piezas)){
				foreach($item->piezas as $pieza) {
					$j++;
					$items[$j] = array(
						"categoria_id" => !empty($pieza->categoria_item_id) ? $pieza->categoria_item_id : "",
						"item_id" => !empty($pieza->item_id) ? $pieza->item_id : "",
						"atributo_id" => "",
						"cantidad" => !empty($pieza->cantidad) ? $pieza->cantidad : "",
						"precio_unidad" => "",
						"unidad_id" => !empty($pieza->unidad_id) ? $pieza->unidad_id : "",
						"precio_total" => ""
					);
				}
			}
			$j++;
		}
		unset($ordenes->servicios);
		$ordenes = array_merge($ordenes->toArray(),['facturados'=> $item_facturado], ['items'=> $items]);

		$this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($ordenes))->_display();
		exit;
	}

	private function _crear_variables_catalogos($orden_uuid=NULL) {
		$clause = array('empresa_id' => $this->empresa_id);
		$clause_precios = array_merge($clause, ["estado" => 1]);

		//---------------------
		// Lista Empezar Orden desde
		//---------------------
		$lista_orden_desde = $this->OrdenesTrabajoCatalogoRepository->getOrdenDesde()->toArray();
		$lista_orden_desde = (!empty($lista_orden_desde) ? array_map(function($lista_orden_desde) {
			return array(
				"id" => strtolower($lista_orden_desde["etiqueta"]),
				"nombre" => $lista_orden_desde["etiqueta"]
			);
		}, $lista_orden_desde) : "");
		$lista_orden_desde = collect($lista_orden_desde);

		//---------------------
		// Listado de Clientes
		//---------------------
		$clientes = $this->ClienteRepository->getClientesEstadoActivo($clause)->get(['id', 'nombre', 'credito_favor', 'exonerado_impuesto']);
		$clientes->load('centro_facturable');

		//---------------------
		// Estados de Ordenes
		//---------------------
		$estados = $this->OrdenesTrabajoCatalogoRepository->getEstados()->toArray();
		$estados = (!empty($estados) ? array_map(function($estados) {
			return array(
				"id" => $estados["id"],
				"nombre" => $estados["etiqueta"]
			);
		}, $estados) : "");

		//---------------------
		// Tipo de Ordenes
		//---------------------
		$tipos_orden = $this->OrdenesTrabajoCatalogoRepository->getTiposOrden()->toArray();
		$tipos_orden = (!empty($tipos_orden) ? array_map(function($tipos_orden) {
			return array(
				"id" => $tipos_orden["id"],
				"nombre" => $tipos_orden["etiqueta"]
			);
		}, $tipos_orden) : "");

		//---------------------
		// Lista de precios
		//---------------------
		$lista_tipo_precio = Precios_orm::where($clause_precios)->get(array('id', 'uuid_precio', 'nombre'))->toArray();
		$lista_tipo_precio = (!empty($lista_tipo_precio) ? array_map(function($lista_tipo_precio) {
			return array(
				"id" => $lista_tipo_precio["id"],
				"nombre" => $lista_tipo_precio["nombre"]
			);
		}, $lista_tipo_precio) : "");

		//---------------------
		// Lista de precio Default
		//---------------------
		$found = Util::multiarray_buscar_valor("Regular", "nombre", $lista_tipo_precio);
		$lista_tipo_precioid_default = !empty($lista_tipo_precio[$found]) ? $lista_tipo_precio[$found]["id"] : "";

		//---------------------
		// Lista Facturable
		//---------------------
		$lista_facturable = $this->OrdenesTrabajoCatalogoRepository->getFacturable()->toArray();
		$lista_facturable = (!empty($tipos_orden) ? array_map(function($lista_facturable) {
			return array(
				"id" => $lista_facturable["id"],
				"nombre" => $lista_facturable["etiqueta"]
			);
		}, $lista_facturable) : "");

		//---------------------
		// Lista Centros Contables
		//---------------------
		$cat_centros = Capsule::select(Capsule::raw("SELECT * FROM cen_centros WHERE empresa_id = :empresa_id1 AND id NOT IN (SELECT padre_id FROM cen_centros WHERE empresa_id = :empresa_id2) ORDER BY nombre ASC"), array(
			'empresa_id1' => $this->empresa_id,
			'empresa_id2' => $this->empresa_id
		));
		$cat_centros = (!empty($cat_centros) ? array_map(function($cat_centros) {
			return array("id" => $cat_centros->id, "nombre" => $cat_centros->nombre);
		}, $cat_centros) : "");

		//---------------------
		// Listado de Bodegas
		//---------------------
		$bodegas = $this->BodegasRepository->getAll(array("empresa_id" => $this->empresa_id))->toArray();
		$bodegas = (!empty($bodegas) ? array_map(function($bodegas) {
			return array(
				"id" => $bodegas["id"],
				"nombre" => $bodegas["nombre"]
			);
		}, $bodegas) : "");

		//-------------------------
		// Catalogo Items por Categoria
		//-------------------------
		$categotias = Categorias_orm::where($clause_precios)->get(['id', 'nombre'])->toArray();
		$categotias = (!empty($categotias) ? array_map(function($categotias) {
			return array(
				"id" => $categotias["id"],
				"nombre" => trim($categotias["nombre"])
			);
		}, $categotias) : "");

		//---------------------
		// Lista Impuestos
		//---------------------
		$impuesto = collect($this->_impuestos()->toArray());

		//---------------------
		// Cunetas Transaccionales
		//---------------------
		$cuenta_transaccionales = collect($this->_cuentas()->toArray());

		//---------------------
		// Lisatdo de Equipos de Trabajo
		//---------------------
		$equipos_trabajo = $this->EquipoTrabajoRepository->getAll(array("empresa_id" => $this->empresa_id))->toArray();
		$equipos_trabajo = (!empty($tipos_orden) ? array_map(function($equipos_trabajo) {
			$nombre = !empty($equipos_trabajo["nombre"]) ? $equipos_trabajo["nombre"] : "";
			$codigo = !empty($equipos_trabajo["codigo"]) ? $equipos_trabajo["codigo"] ." - " : "";

			return array(
				"id" => $equipos_trabajo["id"],
				"nombre" => $codigo . $nombre
			);
		}, $equipos_trabajo) : "");
        $acceso = 1;

        if (!$this->auth->has_permission('acceso', 'ordenes_trabajo/ver/(:any)')) {
            $acceso = 0;
            $mensaje = array('estado' => 500, 'mensaje' => ' <b>Usted no cuenta con permiso para esta solicitud</b>', 'clase' => 'alert-danger');
        }
        $editar_precio = 1;
        /*if(!$this->auth->has_permission('ver__editarPrecioOrdenes')){
            $editar_precio= 0;
        }*/

		$objOrdenVentas = new Flexio\Modulo\OrdenesVentas\Repository\RepositoryOrdenVenta;
		if(is_null($orden_uuid)){
			$ordenesVentas = $objOrdenVentas->getOrdenes($this->empresa_id)->porFacturar()->sinOrdenTrabajo()->fetch();
		}else{
			$ordenesVentas = $objOrdenVentas->getOrdenes($this->empresa_id)->porFacturar()->fetch();
		}


		$ordenesVentas->load('items.item.atributos','items.item.unidades');
		$ordenesVentas->each(function($ord){
			$ord->nombre = $ord->codigo ." - ".$ord->cliente_nombre;
			$ord->items->each(function($line){
				//filling awfull
				unset($line->item->uuid_gasto);
				unset($line->item->uuid_variante);
				unset($line->item->uuid_compra);
				unset($line->item->uuid_venta);
				unset($line->item->uuid_ingreso);
				unset($line->item->uuid_activo);

				return $line;
			});
			return $ord;
		});
		$ordenesVentas->load('cliente.centro_facturable');

		//Crear variables JS
		$this->assets->agregar_var_js(array(
			"ordenDesdeArray" => collect([['id'=>'clientes','nombre'=>'Clientes'],['id'=>'orden_venta','nombre'=>'Orden de Venta']]),
			"clientesArray" => $clientes,
			"estadosArray" => json_encode($estados),
			"tiposOrdenArray" => json_encode($tipos_orden),
			"listaTipoPrecioArray" => json_encode($lista_tipo_precio),
			"listaPrecioIdDefault" => !empty($lista_tipo_precioid_default) ? $lista_tipo_precioid_default : "",
			"listaFacturableArray" => json_encode($lista_facturable),
			"listaCentrosArray" => json_encode($cat_centros),
			"listaBodegasArray" => json_encode($bodegas),
			"listaEquiposTrabajoArray" => json_encode($equipos_trabajo),
			"impuestos" => $impuesto,
			"cuentas" => $cuenta_transaccionales,
			"categoriasItems" => collect($categotias), //$categorias,
			"ordenes_ventas" => $ordenesVentas
		));

       //catalogos
        $clause = ["empresa_id" => $this->empresa_id, 'transaccionales' => true, 'conItems' => true, 'vendedor' => true];
        $this->assets->agregar_var_js(array(
            'bodegas' => $this->BodegasRepository->getCollectionBodegas($this->BodegasRepository->get($clause)),
            'cotizaciones' => $this->cotizacionRepository->getCollectionCotizacionesEmpezarDesde($this->cotizacionRepository->getCotizacionOrdenables($clause)),
            'usuario_id' => $this->usuario_id,
            'clientes' => $this->ClienteRepository->getCollectionClientes($this->ClienteRepository->get($clause)),
            'terminos_pago' => $this->FacturaVentaCatalogoRepository->getTerminoPago(),
            'vendedores' => $this->UsuariosRepository->getCollectionUsuarios($this->UsuariosRepository->get($clause)),
            'precios' => $this->ItemsPreciosRepository->get($clause),
            'categoria' => $this->ItemsCategoriasRepository->getCollectionCategorias($this->ItemsCategoriasRepository->get($clause)),
            'cuenta' => $this->CuentasRepository->get($clause),
            'impuesto' => $this->ImpuestosRepository->get($clause),
            'centros_contables' => $this->CentrosContablesRepository->getCollectionCentrosContables($this->CentrosContablesRepository->get($clause)),
            'vista'   => 'crear',
            "acceso" => $acceso,
            "editar_precio" => $editar_precio
        ));

		//----------------------------------------
		// Si existe uuid de orden
		//----------------------------------------
		if ($orden_uuid != NULL) {



			$info = $this->OrdenesTrabajoRepository->findByUuid($orden_uuid);

			$martillazo = $info;

			$martillazo->load('items.item.atributos','items.item.unidades');

				$martillazo->items->each(function($line){
					//filling awfull
					unset($line->item->uuid_gasto);
					unset($line->item->uuid_variante);
					unset($line->item->uuid_compra);
					unset($line->item->uuid_venta);
					unset($line->item->uuid_ingreso);
					unset($line->item->uuid_activo);

					return $line;
				});

			$martillazo->load('cliente.centro_facturable');

			//$titulo_formulario 	= '<i class="fa fa-wrench"></i> &Oacute;rdenes de trabajo: '. (!empty($info->numero) ? $info->numero : "");
           // dd($info->servicios->toArray());
			if(!empty($info)) {
				//----------------------------------------
				// Crear array de servicios y piezas
				// como variable js
				//----------------------------------------
                if(!empty($info->servicios->toArray())){
                   // dd($info->servicios->toArray());
				$i=0;
				$servicios=array();
				foreach ($info->servicios AS $servicio) {

					$items = !empty($servicio["items"]) ? $servicio->items->toArray() : array();
					$items = (!empty($items) ? array_map(function($items) use($impuesto, $cuenta_transaccionales){
						$item_id 		= $items["item_id"];
						$categoria_id 	= $items["categoria_id"];
						$listaitems 	= $this->_seleccionarListaItems($categoria_id);
						$unidades 		= $this->_seleccionarItemUnidades($item_id, $categoria_id);

						$itemseleccionado = !empty($listaitems) ? $listaitems->filter(function($item, $key) use($item_id) {
							return $item["id"] == $item_id;
						})->toArray() : array();

						$atributos = array();
						if(!empty($itemseleccionado[0]["atributos"])){
							$atributoslist = $itemseleccionado[0]["atributos"];
							$atributos = (!empty($atributoslist) ? array_map(function($atributoslist) {
								return array(
									"id" => $atributoslist["id"],
									"nombre" => $atributoslist["nombre"]
								);
							}, $atributoslist) : "");
						}

						return array(
							"id" 			=> !empty($items["id"]) ? $items["id"] : "",
							"item_id" 		=> $items["item_id"],
							"categoria_id" 	=> !empty($items["categoria_id"]) ? $items["categoria_id"] : "",
							"cantidad" 		=> !empty($items["cantidad"]) ? $items["cantidad"] : 1,
							"unidad_id" 	=> !empty($items["unidad_id"]) ? $items["unidad_id"] : "",
							"atributo_id"	=> !empty($items["atributo_id"]) ? $items["atributo_id"] : "",
							"impuesto_uuid" => !empty($items["impuesto"]) ? $items["impuesto"]["uuid_impuesto"] : "",
							"impuesto_porcentaje" => !empty($items["impuesto"]) ? $items["impuesto"]["impuesto"] : "",
							"cuenta_uuid" 	=> !empty($items["cuenta"]) ? $items["cuenta"]["uuid_cuenta"] : "",
							"precio_unidad"	=> !empty($items["precio_unidad"]) ? number_format($items["precio_unidad"], 2, '.', '') : "",
							"precio_total"	=> !empty($items["precio_total"]) ? number_format($items["precio_total"], 2, '.', '') : "",
							"descuento"		=> !empty($items["descuento"]) ? number_format($items["descuento"], 2, '.', '') : "",
							"impuestos" 	=> $impuesto,
							"items" 		=> $listaitems,
							"atributos" 	=> !empty($atributos) ? $atributos : array(),
							"unidades" 		=> $unidades,
							"cuentas" 		=> $cuenta_transaccionales,
						);
					}, $items) : "");

					$categoria_id 	= $servicio["categoria_id"];
					$item_id 		= $servicio["item_id"];
					$listaitems 	= $this->_seleccionarItemsSerializadosByCategoria($categoria_id);
					$series 		= $this->_seleccionarSeries($categoria_id, $item_id);

					$itemseleccionado = collect($listaitems)->filter(function($item, $key) use($item_id) {
						return $item["id"] == $item_id;
					})->toArray();

					$servicios[$i]["id"] 				= $servicio["id"];
					$servicios[$i]["itemseleccionado"] 	= !empty($itemseleccionado[0]["nombre"]) ? $itemseleccionado[0]["nombre"] : "";
					$servicios[$i]["categoria_id"] 		= $categoria_id;
					$servicios[$i]["item_id"] 			= $item_id;
					$servicios[$i]["serie_id"] 			= $servicio["serie_id"];
					$servicios[$i]["equipo_id"] 		= $servicio["equipo_id"];
					$servicios[$i]["itemsservicio"] 	= !empty($listaitems) ? $listaitems : array();
					$servicios[$i]["series"] 			= !empty($series) ? $series : array();
					$servicios[$i]["item_id"] 			= $servicio["item_id"];
					$servicios[$i]["items"] 			= $items;
					$i++;
				}

				$servicios = collect($servicios);

            }else{
               // dd($info->toArray());
                    $info->load('comentario_timeline');
                   $this->assets->agregar_var_js(array(
                        'vista' => 'ver',
                        "coment" =>(isset($info->comentario_timeline)) ? $info->comentario_timeline : "",
                        "orden_id"=> $info->id,
                        "orden_trabajo" => $martillazo,
                        "acceso" => $acceso,
                        //"empezable" => $empezable,
                        "editar_precio" => $editar_precio
                    ));

                }
				$ordenDeIdArray = array();
				if(preg_match("/clientes/i", $info->orden_de)){
					$ordenDeIdArray = $this->_seleccionarListadoClientes();
				}

				$variables = array(
					"id" 				=> Util::verificar_valor($info->id),
					"ordenDeIdArray" 	=> collect($ordenDeIdArray),
					"orden_de" 			=> Util::verificar_valor($info->orden_de),
					"orden_de_id" 		=> Util::verificar_valor($info->orden_de_id),
					"cliente_id" 		=> Util::verificar_valor($info->cliente_id),
					"estado_id" 		=> Util::verificar_valor($info->estado_id),
					"bodega_id" 		=> Util::verificar_valor($info->bodega_id),
					"tipo_orden_id" 	=> Util::verificar_valor($info->tipo_orden_id),
					"lista_precio_id" 	=> Util::verificar_valor($info->lista_precio_id),
					"facturable_id" 	=> Util::verificar_valor($info->facturable_id),
					"centro_id" 		=> Util::verificar_valor($info->centro_id),
					"fecha_inicio" 		=> !empty($info->fecha_inicio) ? date("d/m/Y", strtotime($info->fecha_inicio)) : "",
					"fecha_planificada_fin" => !empty($info->fecha_planificada_fin) ? date("d/m/Y", strtotime($info->fecha_planificada_fin)) : "",
					"fecha_real_fin" 	=> !empty($info->fecha_real_fin) ? date("d/m/Y", strtotime($info->fecha_real_fin)) : "",
					"comentario" 		=> Util::verificar_valor($info->comentarios),
					"serviciosCollection" 	=> !empty($servicios) ? $servicios : "",
					"martillazo" => $martillazo
				);

				//dd($variables);

				//Crear variables JS
				$this->assets->agregar_var_js($variables);
			}
		}
	}

	private function _seleccionarListadoClientes() {
		$clientes = $this->ClienteRepository->getAll(array(
				"empresa_id" => $this->empresa_id
		))->toArray();

		return (!empty($clientes) ? array_map(function($clientes) {
			return array(
					"id" => $clientes["id"],
					"nombre" => $clientes["nombre"]
			);
		}, $clientes) : "");
	}

	private function _seleccionarItemsSerializadosByCategoria($categoria_id="") {

		if($categoria_id=="") {
			return false;
		}

		return $this->ItemsRepository->findSerializadosByCategoria(array(
			"empresa_id" => $this->empresa_id,
			"categoria_id" => array($categoria_id)
		))->toArray();
	}

	private function _seleccionarSeries($categoria_item_id="", $item_id="") {

		if($item_id=="" || $categoria_item_id=="") {
			return false;
		}

		$item = $this->ItemsRepository->findSerializadosByCategoria(array(
			"item_id" => $item_id,
			"empresa_id" => $this->empresa_id,
			"categoria_id" => array($categoria_item_id)
		))->toArray();

		return !empty($item) && !empty($item[0]["seriales"]) ? $item[0]["seriales"] : array();
	}


	private function _seleccionarItemsByCategoriasServicio($categoria_id="") {

		if($categoria_id=="") {
			return false;
		}

		return $this->ItemsRepository->findCategoriasServicio(array(
			"empresa_id" => $this->empresa_id,
			"categoria_id" => array($categoria_id)
		))->toArray();
	}

	private function _seleccionarListaItems($categoria_id="") {

		if($categoria_id=="") {
			return false;
		}

		$clause = array(
			'empresa_id' => $this->empresa_id,
			'id' => $categoria_id
		);

		$categotia = Categorias_orm::with('items', 'items.atributos', 'items.item_unidades', 'items.precios', 'items.unidades', 'items.atributos')->where($clause)->get(['id', 'nombre']);
		foreach($categotia as $cat) {
			if(empty($cat->items)){
				continue;
			}
			foreach ($cat->items as $l) {
				$l->impuesto;
			}
			$cat->items->transform(function($item) {
				$item->uuid_ingreso = strtoupper(bin2hex($item->uuid_ingreso));
				$item->uuid_venta = strtoupper(bin2hex($item->uuid_venta));
				return $item;
			});
		}
		$categoria = $categotia->toArray();
		$items = !empty($categoria[0]["items"]) ? collect($categoria[0]["items"]) : array();
		return $items;
	}

	private function _seleccionarItemUnidades($item_id="", $categoria_item_id="") {

		if($item_id=="" || $categoria_item_id=="") {
			return false;
		}

		$items = $this->ItemsRepository->findByCategoria(array(
			"item_id" => $item_id,
			"empresa_id" => $this->empresa_id,
			"categoria_id" => array($categoria_item_id)
		))->toArray();

		return !empty($items) && !empty($items[0]["unidades"]) ? $items[0]["unidades"] : array();
	}

	private function _impuestos() {
		//---------------------
		// Lista Impuestos
		//---------------------
		$clause = array(
			'empresa_id' => $this->empresa_id,
			"estado" => "Activo"
		);

		return Impuestos_orm::where($clause)->whereHas('cuenta', function ($query) use ($clause) {
			$query->activas();
			$query->where('empresa_id', '=', $clause['empresa_id']);
		})->get(array('id', 'uuid_impuesto', Capsule::raw("HEX(uuid_impuesto) AS uuid"), 'nombre', 'impuesto'));
	}

	private function _cuentas() {
		//---------------------
		// Cunetas Transaccionales
		//---------------------
		return Cuentas_orm::transaccionalesDeEmpresa($this->empresa_id)->deTipoDeCuenta([4])->activas()
    		->get(array('id', 'uuid_cuenta', 'nombre', 'codigo', Capsule::raw("HEX(uuid_cuenta) AS uuid")));
	}

    function ocultoformulariocomentarios() {

        $data = array();

        $this->assets->agregar_js(array(
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/modules/ordenes_trabajo/vue.comentario.js',
            'public/assets/js/modules/ordenes_trabajo/formulario_comentario.js'
        ));

        $this->load->view('formulario_comentarios');
        $this->load->view('comentarios');

    }

    function ajax_guardar_comentario() {

        if(!$this->input->is_ajax_request()){
            return false;
        }
        $model_id   = $this->input->post('modelId');
        $comentario = $this->input->post('comentario');
        $comentario = ['comentario'=>$comentario,'usuario_id'=>$this->usuario_id];
        $orden = $this->OrdenesTrabajoRepository->agregarComentario($model_id, $comentario);
        $orden->load('comentario_timeline');

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($orden->comentario_timeline->toArray()))->_display();
        exit;
    }

		function documentos_campos(){
			return array(
			array(
				"type"		=> "hidden",
				"name" 		=> "ordenes_trabajo_id",
				"id" 		=> "ordenes_trabajo_id",
				"class"		=> "form-control",
				"readonly"	=> "readonly",
			));

    }

    function ajax_guardar_documentos()
    {
    	if(empty($_POST)){
    		return false;
    	}
    	$ordenes_trabajo_id = $this->input->post('ordenes_trabajo_id', true);
    	$modeloInstancia = $this->OrdenesTrabajoRepository->find($ordenes_trabajo_id);
    	$this->documentos->subir($modeloInstancia);
    }

   public function historial($uuid = NULL){

        $acceso = 1;
        $mensaje =  array();
        $data = array();

        $odt = $this->OrdenesTrabajoRepository->findByUuid($uuid);
        if(!$this->auth->has_permission('acceso','ordenes_trabajo/historial') && is_null($odt)){
            // No, tiene permiso
            $acceso = 0;
            $mensaje = array('estado'=>500, 'mensaje'=>' <b>Usted no cuenta con permiso para esta solicitud</b>','clase'=>'alert-danger');
        }

        $this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
            'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
            'public/assets/js/plugins/jquery/sweetalert/sweetalert.css',
            'public/assets/css/modules/stylesheets/cotizaciones.css',
            'public/assets/css/plugins/jquery/jquery.fileupload.css',
            'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
            'public/assets/css/plugins/bootstrap/select2.min.css',
            'public/assets/css/plugins/jquery/jquery.webui-popover.css',
        ));
        $this->assets->agregar_js(array(
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
            'public/assets/js/plugins/jquery/combodate/combodate.js',
            'public/assets/js/plugins/jquery/combodate/momentjs.js',
            'public/assets/js/default/lodash.min.js',
            'public/assets/js/default/accounting.min.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/plugins/bootstrap/select2/select2.min.js',
            'public/assets/js/plugins/bootstrap/select2/es.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/plugins/jquery/sweetalert/sweetalert.min.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/default/vue/directives/datepicker2.js',
            'public/assets/js/default/vue/directives/inputmask.js',
            'public/assets/js/default/vue/directives/select2.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
            'public/assets/js/plugins/jquery/jquery.webui-popover.js',
            'public/assets/js/modules/ordenes_trabajo/vue.componente.timeline.js',
            'public/assets/js/modules/ordenes_trabajo/vue.timeline.js',
        ));



        $breadcrumb = array(
            "titulo" => '<i class="fa fa-car"></i> Bit&aacute;cora: Orden de trabajo '.$odt->codigo,
            "ruta" => array(
                0 => array(
                    "nombre" => "Servicios",
                    "activo" => false,

                ),
                1 => array(
                    "nombre" => "Ordenes de Trabajo",
                    "activo" => false,
                    "url" => 'ordenes_trabajo/listar'
                ),
                2 => array(
                    "nombre" => $odt->codigo,
                    "activo" => false,
                    "url" => 'ordenes_trabajo/ver/'.$uuid
                ),
                3 => array(
                    "nombre" => '<b>Bitácora</b>',
                    "activo" => true
                )
            ),
            "filtro"    => false,
            "menu"      => array()
        );


        $odt->load('historial');

        $this->assets->agregar_var_js(array(
            "timeline" => $odt,
        ));
        $data['codigo'] = $odt->codigo;
        $this->template->agregar_titulo_header('Ordenes de trabajo');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

}

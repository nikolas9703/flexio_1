<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 *
 * @package    Flexio
 * @subpackage Controller
 * @category   Ordenes de Ventas
 * @author     Pensanomica Team
 * @link       http://www.pensanomica.com
 * @copyright  01/15/2016
 */
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\ContratosAlquiler\Repository\ContratosAlquilerRepository;
use Flexio\Modulo\Cotizaciones\Repository\LineItemRepository as LineItemRepository;
use Flexio\Modulo\OrdenesAlquiler\Models\OrdenVentaAlquiler;
use Flexio\Modulo\OrdenesAlquiler\Repository\OrdenVentaAlquilerRepository as OrdenVentaAlquilerRepository;
use Flexio\Modulo\OrdenesAlquiler\Repository\OrdenVentaCatalogoRepository as OrdenVentaCatalogoRepository;
use Flexio\Modulo\FacturasVentas\Repository\FacturaVentaRepository as FacturaVentaRepository;
use Flexio\Modulo\Salidas\Repository\SalidasRepository;
use Flexio\Modulo\Cliente\Repository\ClienteRepository;
use Flexio\Modulo\FacturasVentas\Repository\FacturaVentaCatalogoRepository;
use Flexio\Modulo\Usuarios\Repository\UsuariosRepository;
use Flexio\Modulo\Inventarios\Repository\PreciosRepository as ItemsPreciosRepository;
use Flexio\Modulo\CentrosContables\Repository\CentrosContablesRepository;
use Flexio\Modulo\Inventarios\Repository\CategoriasRepository as ItemsCategoriasRepository;
use Flexio\Modulo\Contabilidad\Repository\CuentasRepository;
use Flexio\Modulo\Contabilidad\Repository\ImpuestosRepository;
use Flexio\Modulo\Bodegas\Repository\BodegasRepository;
use Flexio\Modulo\Inventarios\Repository\PreciosRepository;
//eventos
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\ContratosAlquiler\Events\CambiarEstadoCargosContratoAlquilerEvent;
use Flexio\Modulo\ContratosAlquiler\Listeners\CambiarEstadoCargosContratoAlquilerListener;

class Ordenes_alquiler extends CRM_Controller {

    private $empresa_id;
    private $usuario_id;
    private $empresaObj;
    protected $ContratosAlquilerRepository;
    protected $OrdenVentaAlquilerRepository;
    protected $lineItemRepository;
    protected $ordenVentaCatalogo;
    protected $facturaVentaRepository;
    protected $SalidasRepository;
    protected $ClienteRepository;
    protected $FacturaVentaCatalogoRepository;
    protected $UsuariosRepository;
    protected $ItemsPreciosRepository;
    protected $CentrosContablesRepository;
    protected $ItemsCategoriasRepository;
    protected $CuentasRepository;
    protected $ImpuestosRepository;
    protected $BodegasRepository;
    protected $disparador;
    protected $comentario;
    protected $DocumentosRepository;
    protected $PreciosRepository;
    protected $upload_folder = './public/uploads/';

    function __construct() {
        parent::__construct();
        $this->load->model('usuarios/Usuario_orm');
        $this->load->model('usuarios/Empresa_orm');
        $this->load->model('usuarios/Roles_usuarios_orm');
        $this->load->model('roles/Rol_orm');
        $this->load->model('clientes/Cliente_orm');
        $this->load->module(array('inventarios/Inventarios', 'documentos'));
        $this->load->model('contabilidad/Impuestos_orm');
        $this->load->model('contabilidad/Cuentas_orm');
        $this->load->model('contabilidad/Centros_orm');
        $this->load->model('bodegas/Bodegas_orm');
        $this->load->model('cobros/Cobro_orm');
        $this->load->model('entradas/Entradas_orm');

        $this->load->module("salidas/Salidas");
        Carbon::setLocale('es');
        setlocale(LC_TIME, 'Spanish');
        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();

        //
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $uuid_usuario = $this->session->userdata('huuid_usuario');

        $empresaObj = new Buscar(new Empresa_orm, 'uuid_empresa');
        $usuario = Usuario_orm::findByUuid($uuid_usuario);

        $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);

        $this->empresa_id = $this->empresaObj->id;
        $this->usuario_id = $usuario->id;
        //

        $this->ContratosAlquilerRepository = new ContratosAlquilerRepository;
        $this->OrdenVentaAlquilerRepository = new OrdenVentaAlquilerRepository;
        $this->lineItemRepository = new LineItemRepository;
        $this->ordenVentaCatalogo = new OrdenVentaCatalogoRepository;
        $this->facturaVentaRepository = new FacturaVentaRepository;
        $this->SalidasRepository = new SalidasRepository();
        $this->ClienteRepository = new ClienteRepository();
        $this->FacturaVentaCatalogoRepository = new FacturaVentaCatalogoRepository();
        $this->UsuariosRepository = new UsuariosRepository();
        $this->ItemsPreciosRepository = new ItemsPreciosRepository();
        $this->CentrosContablesRepository = new CentrosContablesRepository();
        $this->ItemsCategoriasRepository = new ItemsCategoriasRepository();
        $this->CuentasRepository = new CuentasRepository();
        $this->ImpuestosRepository = new ImpuestosRepository();
        $this->BodegasRepository = new BodegasRepository();
        $this->PreciosRepository = new PreciosRepository;
        $this->comentario = new Comentario;
    }

    public function ajax_ordenVenta_info() {

        $item_facturado = [];
        $uuid = $this->input->post('uuid');

        $ordenVenta = $this->OrdenVentaAlquilerRepository->findByUuid($uuid);
        $ordenVenta->load('cliente.centro_facturable', 'cotizacion', 'items', 'items.impuesto', 'items.cuenta', 'facturas');
        ///parte para facturas

        foreach ($ordenVenta->facturas as $items) {
            foreach (explode(",", $items->pivot->items_facturados) as $id) {
                $item_facturado[] = (int) $id;
            }
        }
        $ordenVenta = array_merge($ordenVenta->toArray(), ['facturados' => $item_facturado]);

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($ordenVenta))->_display();
        exit;
    }

    function listar() {
        // Verificar si tiene permiso para listar

        if (!$this->auth->has_permission('acceso', 'ordenes_alquiler/listar')) {
            // No, tiene permiso, redireccionarlo.

            redirect('/');
        }

        $data = array();
        $this->_Css();
        $this->_js();
        $this->assets->agregar_js(array(
            'public/assets/js/modules/ordenes_alquiler/listar.js',
            'public/assets/js/default/toast.controller.js',
            'public/assets/js/plugins/jquery/fileupload/jquery.fileupload.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
        ));

        $breadcrumb = array("titulo" => '<i class="fa fa-line-chart"></i> &Oacute;rdenes de Ventas',
            "ruta" => array(
                0 => array(
                    "nombre" => "Ventas",
                    "activo" => false
                ),
                1 => array(
                    "nombre" => '<b>&Oacute;rdenes de Venta</b>',
                    "activo" => true
                )
            ),
            "menu" => array(
                "nombre" => "Crear",
                "url" => "ordenes_alquiler/crear",
                "opciones" => array()
            )
        );
        //dd($this->session->set_flashdata('mensaje'));
        if (!is_null($this->session->flashdata('mensaje'))) {
            $mensaje = json_encode($this->session->flashdata('mensaje'));
        } else {
            $mensaje = '';
        }
        $this->assets->agregar_var_js(array(
            "toast_mensaje" => $mensaje
        ));
        $clause = array('empresa_id' => $this->empresa_id);
        $roles_users = Rol_orm::where('nombre', 'like', '%vendedor%')->get();

        $usuarios = array();
        $vendedores = array();
        foreach ($roles_users as $roles) {
            $usuarios = $roles->usuarios;
            foreach ($usuarios as $user) {
                if ($user->pivot->empresa_id == $clause['empresa_id']) {
                    array_push($vendedores, $user);
                }
            }
        }

        $data['clientes'] = Cliente_orm::where($clause)->get(array('id', 'nombre'));
        $data['etapas'] = $this->ordenVentaCatalogo->getEtapas();
        $data['vendedores'] = $vendedores;
        $breadcrumb["menu"]["opciones"]["#exportarListaCotizaciones"] = "Exportar";
        $this->template->agregar_titulo_header('Listado de &Oacute;rdenes de Ventas');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }

    public function ocultoformulario($cotizacion = array()) {

        $this->assets->agregar_js(array(
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/default/vue/components/empezar_desde.js',
            'public/assets/js/modules/ordenes_alquiler/components/detalle.js',
            'public/assets/js/default/vue/directives/pop_over_precio.js',
            'public/assets/js/default/vue/directives/pop_over_cantidad.js',
            'public/resources/compile/modulos/ordenes_alquiler/formulario.js',
            'public/assets/js/plugins/jquery/switchery.min.js',
        ));

        //catalogos
        $clause = ['empresa_id' => $this->empresa_id, 'transaccionales' => true, 'conItems' => true, 'tipo_cuenta_id' => '4', 'vendedor' => true];
        $this->assets->agregar_var_js(array(
            'bodegas'           => $this->BodegasRepository->getCollectionBodegas($this->BodegasRepository->get($clause)),
            'contratos'         => $this->ContratosAlquilerRepository->getCollectionEmpezarDesde($clause),
            'usuario_id'        => $this->usuario_id,
            'clientes'          => $this->ClienteRepository->getCollectionClientes($this->ClienteRepository->getClientesEstadoActivo($clause)->get()),
            'terminos_pago'     => $this->FacturaVentaCatalogoRepository->getTerminoPago(),
            'vendedores'        => $this->UsuariosRepository->getCollectionUsuarios($this->UsuariosRepository->get($clause)),
            "lista_precio_alquiler" =>  $this->PreciosRepository->get(array('empresa_id' => $this->empresa_id, "estado" => 1, "tipo_precio" => "alquiler")),
            //'vendedores'        => $this->UsuariosRepository->get(array('empresa_id' => $this->empresa_id)),
            'precios'           => $this->PreciosRepository->get(array('empresa_id' => $this->empresa_id, "estado" => 1, "tipo_precio" => "venta")),
            'centros_contables' => $this->CentrosContablesRepository->getCollectionCentrosContables($this->CentrosContablesRepository->get($clause)),
            'estados'           => $this->ordenVentaCatalogo->getEtapas(),
            'categorias'        => $this->ItemsCategoriasRepository->getCollectionCategorias($this->ItemsCategoriasRepository->get($clause)),
            'cuentas'           => $this->CuentasRepository->get($clause),
            'impuestos'         => $this->ImpuestosRepository->get($clause)
        ));

        $this->load->view('formulario');
        $this->load->view('vue/components/empezar_desde');
        $this->load->view('components/detalle');
    }

    public function crear($modulo=NULL, $modulo_id=NULL) {

        if($modulo != NULL && $modulo_id != NULL) {
            $empezable_id = $modulo_id;
            $empezable_type = preg_match('/(contrato_alquiler)/i', $modulo) ? 'contrato' : $modulo;
        }

        $acceso = 1;
        $mensaje = array();

        if (!$this->auth->has_permission('acceso', 'ordenes_alquiler/crear/(:any)/(:num)')) {
            // No, tiene permiso, redireccionarlo.
            $acceso = 0;
            $mensaje = array('estado' => 500, 'mensaje' => ' <b>Usted no cuenta con permiso para esta solicitud</b>', 'clase' => 'alert-danger');
        }

        $this->_Css();
        $this->_js();

        $empezable = collect([
            'id' => isset($empezable_id) ? $empezable_id : '',
            'type' => isset($empezable_type) ? $empezable_type : '',
            'contratos' => []
        ]);

        $this->assets->agregar_var_js(array(
            "vista" => 'crear',
            "acceso" => $acceso,
            "empezable" => $empezable,
            "editar_precio" => $this->auth->has_permission('crear__editarPrecioOrdenAlquiler', 'ordenes_alquiler/crear') == true ? 1 : 0
        ));

        $breadcrumb = array(
            "titulo" => '<i class="fa fa-line-chart"></i> Orden de venta: Crear',
        );

        $data = array();
        $data['mensaje'] = $mensaje;
        $this->template->agregar_titulo_header('Crear Orden de Venta');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    public function ver($uuid = NULL) {

        $acceso = 1;
        $mensaje = array();

        if (!$this->auth->has_permission('acceso', 'ordenes_alquiler/ver/(:any)')) {
            $acceso = 0;
            $mensaje = array('estado' => 500, 'mensaje' => ' <b>Usted no cuenta con permiso para esta solicitud</b>', 'clase' => 'alert-danger');
        }

        $this->_Css();
        $this->_js();

        $ordenVenta = $this->OrdenVentaAlquilerRepository->findByUuid($uuid);
        //dd($ordenVenta->toArray());
        if (is_null($uuid) || is_null($ordenVenta)) {
            $mensaje = array('estado' => 500, 'mensaje' => '<strong>¡Error!</strong> Su solicitud no fue procesada');
            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('ordenes_alquiler/listar'));
        } else {

            $data = array(
                'orden_venta_id' => $ordenVenta->id
            );

            $empezable = !empty($ordenVenta->toArray()) ? collect([
                'id' => !empty($ordenVenta->contrato_id) ? $ordenVenta->contrato_id : '',
                'type' => !empty($ordenVenta->contrato_id) ? 'option' : '',
                'types' => !empty($ordenVenta->contrato_id) ? [0 => ['id' => 'option', 'nombre' => 'Contrato de alquiler']] : [],
                'options' => !empty($ordenVenta->contrato_id) ? [0 => [
                    'id' => $ordenVenta->contrato_id,
                    'nombre' => !empty($ordenVenta->contrato_alquiler) ? "{$ordenVenta->contrato_alquiler->codigo} - {$ordenVenta->contrato_alquiler->cliente->nombre}" : ""
                ]] : []
            ]) : "";

            $ov = $this->OrdenVentaAlquilerRepository->getCollectionOrdenVenta($ordenVenta);
            $ovinfo = $ov->toArray();

            $this->assets->agregar_var_js(array(
                "vista" => 'editar',
                "orden_venta" => $ov,
                "campo" => collect(["cliente" => $ovinfo["cliente_id"]]),
                //"ova_cliente_id" => $ovinfo["cliente_id"],
                "acceso" => $acceso,
                "empezable" => $empezable,
                "uuid_orden" => $uuid,
                "editar_precio" => $this->auth->has_permission('ver__editarPrecioOrdenAlquiler') == true ? 1 : 0
            ));

            $breadcrumb = array(
              "titulo" => '<i class="fa fa-line-chart"></i> Orden de venta: ' . $ordenVenta->codigo,
              "ruta" => array(
                    0 => array(
                        "nombre" => "Ventas",
                        "activo" => false
                    ),
                    1 => array(
                        "nombre" => '&Oacute;rdenes de Venta',
                        "activo" => false
                    ),
                    2 => array(
                        "nombre" => '<b>'. $ordenVenta->codigo .'</b>',
                        "activo" => true
                    )
                )
            );

            ///setear, centro,inicio,periodo
            $this->template->agregar_titulo_header('Editar Orden de Ventas');
            $this->template->agregar_breadcrumb($breadcrumb);
            $this->template->agregar_contenido($data);
            $this->template->visualizar();
        }
    }

    function ajax_listar() {

        if (!$this->input->is_ajax_request()) {
            return false;
        }
        /*
          paramentos de busqueda aqui
         */

        $uuid_cliente = $this->input->post("cliente_id");

        $no_orden = $this->input->post('no_orden', TRUE);
        $no_contrato = $this->input->post('no_contrato', TRUE);
        $cliente = $this->input->post('cliente', TRUE);
        $hasta = $this->input->post('desde', TRUE);
        $desde = $this->input->post('hasta', TRUE);
        $estado = $this->input->post('etapa', TRUE);
        $vendedor = $this->input->post('vendedor', TRUE);
        $clause = array('empresa_id' => $this->empresaObj->id, 'formulario' => 'orden_venta');

        if (!empty($uuid_cliente) && empty($uuid_venta)) {
            $clienteObj = new Buscar(new Cliente_orm, 'uuid_cliente');
            $cliente = $clienteObj->findByUuid($uuid_cliente);
            $clause['cliente_id'] = $cliente->id;
        } elseif (!empty($cliente)) {
            $clause['cliente_id'] = $cliente;
        }

        if (!empty($no_orden)) {
            $clause['codigo'] = $no_orden;
        }
        if (!empty($no_contrato)) {
            $clause['no_contrato'] = $no_contrato;
        }

        if (!empty($this->input->post('cotizacion_id'))) {
            $clause['cotizacion_id'] = $this->input->post('cotizacion_id');
        }


        if (!empty($desde))
            $clause['fecha_desde'] = Carbon::createFromFormat('d/m/Y', $desde, 'America/Panama')->format('Y-m-d');
        if (!empty($hasta))
            $clause['fecha_hasta'] = Carbon::createFromFormat('d/m/Y', $hasta, 'America/Panama')->format('Y-m-d');
        if (!empty($estado))
            $clause['etapa'] = $estado;
        if (!empty($vendedor))
            $clause['creado_por'] = $vendedor;
        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $clause['campo'] = $this->input->post('campo');
        $count = $this->OrdenVentaAlquilerRepository->lista_totales($clause);

        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

        $ordenes = $this->OrdenVentaAlquilerRepository->listar($clause, $sidx, $sord, $limit, $start);

        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;

        if (!empty($ordenes->toArray())&&count($ordenes)>0) {

            $i = 0;
            foreach ($ordenes as $row) {
                $hidden_options = "";
                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row->uuid_venta . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $hidden_options = '<a href="' . base_url('ordenes_alquiler/ver/' . $row->uuid_venta) . '" data-id="' . $row->uuid_venta . '" class="btn btn-block btn-outline btn-success">Ver Orden de Venta</a>';
                if ($row->facturar()) {
                    $hidden_options .= '<a href="' . base_url('ordenes_alquiler/facturar/' . $row->uuid_venta) . '" data-id="' . $row->uuid_venta . '" class="btn btn-block btn-outline btn-success">Facturar</a>';
                }
                $hidden_options .= '<a href="javascript:" data-id="' . $row->uuid_venta . '" class="exportarTablaCliente btn btn-block btn-outline btn-success subirArchivoBtn">Subir Documento</a>';

                $cliente = count($row->cliente) ? $row->cliente->nombre_completo_enlace : '';
                $vendedor = $row->vendedor;
                $etapa = $row->etapa_catalogo;

                $nombre_vendedor = !empty($vendedor->nombre) ? $vendedor->nombre : "";
                $apellido_vendedor = !empty($vendedor->apellido) ? $vendedor->apellido : "";

                $response->rows[$i]["id"] = $row->uuid_venta;
                $response->rows[$i]["cell"] = array(
                    $row->uuid_venta,
                    '<a class="link" href="' . base_url('ordenes_alquiler/ver/' . $row->uuid_venta) . '" >' . $row->codigo . '</a>',
                    $cliente,
                    $row->fecha_desde,
                    !empty($row->contrato_alquiler) ? $row->contrato_alquiler->codigo : "",
                    $nombre_vendedor . ' ' . $apellido_vendedor,
                    '<label class="label label-'. str_replace(" ", "-", strtolower($row->orden_estado)) .'">'. $row->orden_estado .'</label>',
                    $link_option,
                    $hidden_options
                );
                $i++;
            }
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($response))->_display();
        exit;
    }

    public function ocultotabla($uuid = NULL, $modulo = NULL) {

        if(is_array($uuid))
        {
            $this->assets->agregar_var_js([
                "campo" => collect($uuid)
            ]);
        }
        elseif($uuid and count(explode("=", $uuid)) > 1)
        {
            $aux = explode("=", $uuid);
            $this->assets->agregar_var_js([$aux[0]=>$aux[1]]);
        }

        $this->assets->agregar_js(array(
            'public/assets/js/modules/ordenes_alquiler/tabla.js'
        ));

        if (!empty($uuid) && !is_null($modulo)) {
            $this->_planel_variable($modulo, $uuid);
        }

        $this->load->view('tabla');
    }

    public function ocultotablaV2($sp_string_var = []) {

        $this->assets->agregar_js(array(
            'public/assets/js/modules/ordenes_alquiler/tabla.js'
        ));

        $sp_array_var = explode('=', $sp_string_var);
        if (count($sp_array_var) == 2) {

            $this->assets->agregar_var_js(array(
                $sp_array_var[0] => $sp_array_var[1]
            ));

        }

        $this->load->view('tabla');
    }

    public function guardar() {

        if ($_POST) {

            $input = Illuminate\Http\Request::createFromGlobals();

            $orden_alquiler = $input->input("campo");
            $lineitemsarray = $input->input("items");
            $alquileritems  = $input->input("items_alquiler");
            $empezable_id   = $input->input("empezable_id");

            $orden_alquiler['empresa_id'] = $this->empresa_id;
            $orden_alquiler['created_by'] = !empty($orden_alquiler['creado_por']) ? $orden_alquiler['creado_por'] : "";

            //esto es para limpiar los id de las filas cuando se selecciona la opcion empezar desde.
            //estos ids no se limbian en el js porque son necesarios para el comportamiento del precio unitario
            //en los modulos de venta. Francisco Marcano.
            $lineitems = array();
            if ($orden_alquiler['vista'] == 'crear') {
                $orden_alquiler["contrato_id"] = $empezable_id;
                $orden_alquiler["formulario"] = "orden_alquiler";
            }

            if (!empty($lineitemsarray)) {
              $j=0;
              foreach($lineitemsarray AS $litem) {
                  if(empty($litem["item_id"])){
                    continue;
                  }
                  $litem["item_adicional"] = 1;
                  $litem["id_pedido_item"] = '';
                  $litem['empresa_id'] = $this->empresa_id;
                  $lineitems[$j] = $litem;
                  $j++;
              }
            }

            if (!empty($alquileritems)) {
              $alquileritems = array_map(function($alquileritems) {
                  $alquileritems['empresa_id'] = $this->empresa_id;
                  $alquileritems["id_pedido_item"] = '';
                  return $alquileritems;
              }, $alquileritems);
              $lineitems = array_merge($lineitems, $alquileritems);
            }

            Capsule::beginTransaction();
            try {
                if (empty($orden_alquiler['id'])) {
                    /*$total = $this->OrdenVentaAlquilerRepository->lista_totales(['empresa_id' => $this->empresa_id]);
                    $year = Carbon::now()->format('y');
                    $codigo = Util::generar_codigo('SOA' . $year, $total + 1);
                    $orden_alquiler['codigo'] = $codigo;*/
                    $orden_alquiler['codigo'] = $this->getLastCodigo();
                }

                $data = array('ordenalquiler' => $orden_alquiler, 'lineitem' => $lineitems);
                if (empty($orden_alquiler['id'])) {
                    $modelOrdenVenta = $this->OrdenVentaAlquilerRepository->create($data);
                } else {
                    $modelOrdenVenta = $this->OrdenVentaAlquilerRepository->update($data);
                }
            } catch (Illuminate\Database\QueryException $e) {
                Capsule::rollback();
                $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error! Su solicitud no fue procesada</b> ');
                $this->session->set_flashdata('mensaje', $mensaje);
                redirect(base_url('ordenes_alquiler/listar'));
            }
            if (!is_null($modelOrdenVenta)) {
                Capsule::commit();
                if (!empty($_POST['empezable_id'])) {
                    /*$cotizacion = $this->cotizacionRepository->find($_POST['empezable_id']);
                    $cotizacion->estado = 'ganado';
                    $cotizacion->save();*/
                }
                if (empty($orden_alquiler['id'])) {
                  $this->disparador = new \Illuminate\Events\Dispatcher();
                  $this->disparador->listen([CambiarEstadoCargosContratoAlquilerEvent::class], CambiarEstadoCargosContratoAlquilerListener::class);
                  $this->disparador->fire(new CambiarEstadoCargosContratoAlquilerEvent($modelOrdenVenta));
                }

                /*if ($modelOrdenVenta->estado == 'por_facturar') {
                    $this->SalidasRepository->create(array("tipo_id" => $modelOrdenVenta->id, "estado_id" => '1', "tipo" => 'Flexio\\Modulo\\OrdenesVentas\\Models\\OrdenVenta', "empresa_id" => $this->empresa_id));
                } elseif ($modelOrdenVenta->estado == 'anulada') {
                    $salida = Salidas_orm::where(['operacion_type' => 'Flexio\\Modulo\\OrdenesVentas\\Models\\OrdenVenta', 'operacion_id' => $modelOrdenVenta->id])->delete();
                }*/


                //$this->load->library('Repository/ordenes_alquiler/Ordenes_estados', $modelOrdenVenta);
                $mensaje = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente. ' . $modelOrdenVenta->codigo);
            } else {
                $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error! Su solicitud no fue procesada</b> ');
            }
            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('ordenes_alquiler/listar'));
        }
    }

    function getLastCodigo(){
        $clause = ['empresa_id' => $this->empresa_id];
        $year = Carbon::now()->format('y');
        $ovalquiler = OrdenVentaAlquiler::where($clause)->get()->last();
        $codigo_actual = is_null($ovalquiler)? 0: $ovalquiler->codigo;
        $codigo = (int)str_replace('SOA'.$year, "", $codigo_actual);
        return $codigo + 1;
    }

    //desde modal de opciones...
    public function facturar($uuid = null) {

        if (!$this->empresaObj->tieneCuentaCobro()) {
            $mensaje = array('estado' => 500, 'mensaje' => 'No hay cuenta de cobro asociada', 'clase' => 'alert-danger');
            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('ordenes_alquiler/listar'));
        }

        $this->disparador = new \Illuminate\Events\Dispatcher();
        $this->disparador->listen([OrdenVentaFacturableEvent::class], CrearOrdenFacturableListener::class);
        $ordenVenta = $this->OrdenVentaAlquilerRepository->findByUuid($uuid);

        $total = $this->facturaVentaRepository->lista_totales(['empresa_id' => $this->empresa_id]);
        $year = Carbon::now()->format('y');
        $codigo = Util::generar_codigo('INV' . $year, $total + 1);

        Capsule::beginTransaction();
        try {
            $factura = [];
            $factura['codigo'] = $codigo;
            $factura['centro_contable_id'] = $ordenVenta->centro_contable_id;
            $factura['bodega_id'] = $ordenVenta->bodega_id;
            $factura['centro_facturacion_id'] = $ordenVenta->centro_facturacion_id;
            $factura['cliente_id'] = $ordenVenta->cliente_id;
            $factura['created_by'] = $ordenVenta->created_by;
            $factura['empresa_id'] = $ordenVenta->empresa_id;
            $factura['estado'] = 'por_aprobar';
            $factura['fecha_desde'] = $ordenVenta->fecha_desde;
            $factura['fecha_hasta'] = $ordenVenta->fecha_hasta;
            $factura['termino_pago'] = $ordenVenta->termino_pago;
            $factura['item_precio_id'] = $ordenVenta->item_precio_id;
            $factura['subtotal'] = $ordenVenta->subtotal;
            $factura['impuestos'] = $ordenVenta->impuestos;
            $factura['total'] = $ordenVenta->total;
            $factura['formulario'] = 'orden_venta';

            $aux = [];
            $ordenVenta->load('facturas.items');
            foreach ($ordenVenta->facturas as $fac) {
                foreach (explode(",", $fac->pivot->items_facturados) as $id) {
                    $aux[] = $id;
                }
            }

            if (empty($aux)) {
                $orden = $ordenVenta->items;
            } else {
                $orden = $ordenVenta->items()->whereNotIN('item_id', $aux)->get();
            }
            $total = 0;
            $subtotal = 0;
            $impuesto = 0;
            $descuento = 0;
            $sub1 = 0;
            //array para los item de la factura
            $item_factura = [];

            foreach ($orden as $item) {
                $impuesto_actutal = Impuestos_orm::find($item->impuesto_id);
                array_push($item_factura, array(
                    'item_id' => $item->item_id,
                    'categoria_id' => $item->categoria_id,
                    'empresa_id' => $item->empresa_id,
                    'cantidad' => $item->cantidad,
                    'unidad_id' => $item->unidad_id,
                    'precio_unidad' => $item->precio_unidad,
                    'impuesto_id' => $item->impuesto_id,
                    'descuento' => $item->descuento,
                    'cuenta_id' => $item->cuenta_id,
                    'precio_total' => $item->precio_total,
                    'descuento_total' => $item->descuento_total,
                    'impuesto_total' => $item->impuesto_total)
                );
                $subtotal += $item->precio_unidad * $item->cantidad;
                $sub1 = $item->precio_unidad * $item->cantidad;
                $impuesto += $sub1 * ( $impuesto_actutal->impuesto / 100);
                $descuento += $sub1 * ($item->descuento / 100);
                $total += $sub1 + $impuesto - $descuento;
            }
            $data = ['facturaventa' => $factura, 'lineitem' => $item_factura];
            $modelFactura = $this->facturaVentaRepository->create($data);
        } catch (Illuminate\Database\QueryException $e) {
//        log_message('error', __METHOD__." ->". ", Linea: ". __LINE__ ." --> ". $e->getMessage()."\r\n");
            Capsule::rollback();
            $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error! Su solicitud no fue procesada</b> ');
            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('ordenes_alquiler/listar'));
        }
        if (!is_null($modelFactura)) {
            Capsule::commit();
            $this->disparador->fire(new OrdenVentaFacturableEvent($modelFactura, $ordenVenta->fresh()));
            $this->load->library('Events/Orden_venta/Orden_venta_estado');
            $OrdenVentaEstado = new Orden_venta_estado;
            $OrdenVentaEstado->handle($ordenVenta->fresh());
            $modelFactura->subtotal = $subtotal;
            $modelFactura->impuestos = $impuesto;
            $modelFactura->total = $total;
            $modelFactura->save();
            $mensaje = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente ' . $modelFactura->codigo);
        } else {
            $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error! Su solicitud no fue procesada</b> ');
        }
        $this->session->set_flashdata('mensaje', $mensaje);
        redirect(base_url('facturas/listar'));
    }

//    //Comentario en el refactory a vue.js por Francisco Marcano
//    function ajax_get_item_existencia() {
//        $bodega_id = $this->input->post('bodega_id');
//        $id_item = $this->input->post('item_id');
//
//        $bodega = Bodegas_orm::find($bodega_id);
//        $item = Items_orm::find($id_item);
//        $uuid_bodega = count($bodega) ? $bodega->uuid_bodega : NULL;
//        $existencia = $item->enInventario($uuid_bodega);
//        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
//                ->set_output(json_encode($existencia))->_display();
//        exit;
//    }

    private function _Css() {
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
            'public/assets/css/plugins/jquery/jquery.webui-popover.css',
            'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
            'public/assets/css/plugins/bootstrap/select2.min.css',
            'public/assets/css/plugins/jquery/switchery.min.css',
            'public/assets/css/modules/stylesheets/ordenes_alquiler.css',
        ));
    }

    private function _js() {
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
            'public/assets/js/default/accounting.min.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/plugins/jquery/sweetalert/sweetalert.min.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
            'public/assets/js/default/vue/directives/datepicker2.js',
            'public/assets/js/default/vue/directives/inputmask.js',
            'public/assets/js/default/vue/directives/select2.js',
            'public/assets/js/plugins/jquery/jquery.webui-popover.js',
            'public/assets/js/plugins/bootstrap/select2/select2.min.js',
            'public/assets/js/plugins/bootstrap/select2/es.js',
        ));
    }

    private function _planel_variable($modulo, $uuid) {
        $jsVariable = '';

        $jsVariable = $this->assets->agregar_var_js(array(
            "cliente_id" => $uuid
        ));
        return $jsVariable;
    }

    public function documentos_campos() {

        return array(
            array(
                "type" => "hidden",
                "name" => "ordenes_venta_id",
                "id" => "ordenes_venta_id",
                "class" => "form-control",
                "readonly" => "readonly",
        ));
    }

    public function ajax_guardar_documentos() {
        if (empty($_POST)) {
            return false;
        }

        $ordenes_venta_id = $this->input->post('ordenes_ventas_id', true);
        $modeloInstancia = $this->OrdenVentaAlquilerRepository->findByUuid($ordenes_venta_id);
        $this->documentos->subir($modeloInstancia);
    }
}

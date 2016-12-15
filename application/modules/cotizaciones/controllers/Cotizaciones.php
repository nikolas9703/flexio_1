
<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 *
 * @package    Erp
 * @subpackage Controller
 * @category   Cotizaciones
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  10/22/2015
 */
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Support\Collection;
use Carbon\Carbon as Carbon;
use Dompdf\Dompdf;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\Cotizaciones\Repository\CotizacionRepository as CotizacionRepository;
use Flexio\Modulo\Cotizaciones\Repository\LineItemRepository as LineItemRepository;
use Flexio\Modulo\OrdenesVentas\Repository\OrdenVentaRepository as OrdenVentaRepository;
use Flexio\Modulo\Cotizaciones\Repository\CotizacionCatalogoRepository as CotizacionCatalogoRepository;
use Flexio\Modulo\Cliente\Repository\ClienteRepository;
use Flexio\Modulo\Oportunidades\Repository\OportunidadesRepository;
use Flexio\Modulo\OrdenesVentas\Repository\OrdenVentaCatalogoRepository;
use Flexio\Modulo\ClientesPotenciales\Repository\ClientesPotencialesRepository;
use Flexio\Modulo\FacturasVentas\Repository\FacturaVentaCatalogoRepository;
use Flexio\Modulo\Usuarios\Repository\UsuariosRepository;
use Flexio\Modulo\Inventarios\Repository\PreciosRepository as ItemsPreciosRepository;
use Flexio\Modulo\CentrosContables\Repository\CentrosContablesRepository;
use Flexio\Modulo\Inventarios\Repository\CategoriasRepository as ItemsCategoriasRepository;
use Flexio\Modulo\Contabilidad\Repository\CuentasRepository;
use Flexio\Modulo\Contabilidad\Repository\ImpuestosRepository;

class Cotizaciones extends CRM_Controller {

    private $id_empresa;
    private $id_usuario;
    private $empresaObj;
    protected $cotizacionRepository;
    protected $cotizacionCatalogoRepository;
    protected $LineItemRepository;
    protected $ordenVentaRepository;
    protected $clienteRepo;
    protected $comentario;
    protected $OportunidadesRepository;
    protected $OrdenVentaCatalogoRepository;
    protected $DocumentosRepository;
    protected $ClientesPotencialesRepository;
    protected $FacturaVentaCatalogoRepository;
    protected $UsuariosRepository;
    protected $ItemsPreciosRepository;
    protected $CentrosContablesRepository;
    protected $ItemsCategoriasRepository;
    protected $CuentasRepository;
    protected $ImpuestosRepository;
    protected $upload_folder = './public/uploads/';

    function __construct() {
        parent::__construct();
        $this->load->model('usuarios/Usuario_orm');
        $this->load->model('usuarios/Empresa_orm');
        $this->load->model('usuarios/Roles_usuarios_orm');
        $this->load->model('roles/Rol_orm');
        $this->load->model('clientes/Cliente_orm');
        $this->load->model("inventarios/Categorias_orm");
        $this->load->model("inventarios/Items_categorias_orm");
        $this->load->model('inventarios/Items_orm');
        $this->load->model('inventarios/Items_precios_orm');
        $this->load->model('inventarios/Precios_orm');
        $this->load->model('inventarios/Unidades_orm');
        $this->load->module(array("contabilidad/contabilidad", "documentos"));
        $this->load->model('bodegas/Bodegas_orm');
        $this->load->model('facturas/Factura_orm');
        $this->load->model('facturas/Factura_items_orm');
        $this->load->model('cobros/Cobro_orm');


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

        $this->id_empresa = $this->empresaObj->id;
        $this->id_usuario = $usuario->id;
        //

        $this->cotizacionRepository = new CotizacionRepository;
        $this->cotizacionCatalogoRepository = new CotizacionCatalogoRepository;
        $this->LineItemRepository = new LineItemRepository;
        $this->ordenVentaRepository = new OrdenVentaRepository;
        $this->clienteRepo = new ClienteRepository;
        $this->comentario = new Comentario;
        $this->OportunidadesRepository = new OportunidadesRepository();
        $this->OrdenVentaCatalogoRepository = new OrdenVentaCatalogoRepository();
        $this->ClientesPotencialesRepository = new ClientesPotencialesRepository();
        $this->FacturaVentaCatalogoRepository = new FacturaVentaCatalogoRepository();
        $this->UsuariosRepository = new UsuariosRepository();
        $this->ItemsPreciosRepository = new ItemsPreciosRepository();
        $this->CentrosContablesRepository = new CentrosContablesRepository();
        $this->ItemsCategoriasRepository = new ItemsCategoriasRepository();
        $this->CuentasRepository = new CuentasRepository();
        $this->ImpuestosRepository = new ImpuestosRepository();
    }

    public function index() {

    }

    function listar() {
        if (!$this->auth->has_permission('acceso')) {
            // No, tiene permiso, redireccionarlo.
            redirect('/');
        }

        $data = array();

        $this->_Css();
        $this->assets->agregar_js(array(
            //'public/assets/js/default/jquery-ui.min.js',
            //'public/assets/js/plugins/jquery/jquery.sticky.js',
            //'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            //'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            //'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/moment-with-locales-290.js',
            //'public/assets/js/plugins/jquery/jqgrid/plugins/jQuery.jqGrid.columnToggle.js',
            'public/assets/js/default/subir_documento_modulo.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/default/formulario.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            'public/assets/js/plugins/bootstrap/bootstrap-datetimejs.js',
            //'public/assets/js/default/lodash.min.js',
            //'public/assets/js/plugins/jquery/sweetalert/sweetalert.min.js',
            'public/assets/js/modules/cotizaciones/listar.js',
            'public/assets/js/default/toast.controller.js',
            'public/assets/js/plugins/jquery/fileupload/jquery.fileupload.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            //'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
        ));

        $breadcrumb = array("titulo" => '<i class="fa fa-line-chart"></i> Cotizaciones',
            "ruta" => array(
                0 => array(
                    "nombre" => "Ventas",
                    "activo" => false
                ),
                1 => array(
                    "nombre" => '<b>Cotizaciones</b>',
                    "activo" => true
                )
            ),
            "menu" => array(
                "nombre" => "Crear",
                "url" => "cotizaciones/crear",
                "opciones" => array()
            )
        );
        //dd($this->session->set_flashdata('mensaje'));
        if (!is_null($this->session->flashdata('mensaje'))) {
            $mensaje = json_encode($this->session->flashdata('mensaje'));
        } else {
            $mensaje = '';
        }

        $oportunidades = $this->OportunidadesRepository->get(['empresa_id' => $this->id_empresa, 'cotizables' => true]);
        $oportunidades->load('cliente');
        $this->assets->agregar_var_js(array(
            "toast_mensaje" => $mensaje,
            "oportunidades" => $oportunidades
        ));
        $clause = array('empresa_id' => $this->id_empresa);
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
        $data['etapas'] = $this->cotizacionCatalogoRepository->getEtapas();
        $data['vendedores'] = $vendedores;
        $breadcrumb["menu"]["opciones"]["#exportarListaCotizaciones"] = "Exportar";
        $this->template->agregar_titulo_header('Listado de Cotizaciones');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }

    public function ajax_listar() {
        if (!$this->input->is_ajax_request()) {
            return false;
        }
        /*
          paramentos de busqueda aqui
         */
        $no_cotizacion = $this->input->post('no_cotizacion', TRUE);
        $cliente = $this->input->post('cliente', TRUE);
        $uuid_cliente = $this->input->post('cliente_id', TRUE);
        $hasta = $this->input->post('desde', TRUE);
        $desde = $this->input->post('hasta', TRUE);
        $estado = $this->input->post('etapa', TRUE);
        $vendedor = $this->input->post('vendedor', TRUE);
        $uuid_cotizacion = $this->input->post('uuid_cotizacion', TRUE);


        $clause = array('empresa_id' => $this->empresaObj->id);
        $clause["no_cotizacion"] = $no_cotizacion;

        if (!empty($uuid_cliente)) {
            $clienteObj = new Buscar(new Cliente_orm, 'uuid_cliente');
            $cliente = $clienteObj->findByUuid($uuid_cliente);
            $clause['cliente_id'] = $cliente->id;
        } elseif (!empty($cliente)) {
            $clause['cliente_id'] = $cliente;
        }

        if (!empty($this->input->post('sp_orden_venta_id'))) {
            $clause['orden_venta_id'] = $this->input->post('sp_orden_venta_id');
        }

        if (!empty($desde))
            $clause['fecha_desde'] = Carbon::createFromFormat('d/m/Y', $desde, 'America/Panama')->format('Y-m-d');
        if (!empty($hasta))
            $clause['fecha_hasta'] = Carbon::createFromFormat('d/m/Y', $hasta, 'America/Panama')->format('Y-m-d');
        if (!empty($estado))
            $clause['estado'] = $estado;
        if (!empty($vendedor))
            $clause['creado_por'] = $vendedor;


        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = $this->cotizacionRepository->lista_totales($clause);
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $cotizaciones = $this->cotizacionRepository->listar($clause, $sidx, $sord, $limit, $start);


        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;



        if (!empty($cotizaciones->toArray())) {
            $i = 0;
            foreach ($cotizaciones as $row) {
                $modulo = ($row->tipo == 'venta') ? 'cotizaciones/ver' : 'cotizaciones_alquiler/editar';
                $hidden_options = "";
                $orden = $row->ordenes_validas()->count();
                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row->uuid_cotizacion . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $hidden_options = '<a href="' . base_url($modulo . '/' . $row->uuid_cotizacion) . '" data-id="' . $row->uuid_cotizacion . '" class="btn btn-block btn-outline btn-success">Ver Cotizacion</a>';

                //para convertir a cotizacion debe estar aprobada, ser de venta y no estar asociada a un cliente potencial
                if ($orden == 0 && ($row->estado == 'aprobado') && $row->cliente_tipo == "cliente" && $row->tipo == "venta")
                    $hidden_options .= '<a href="' . base_url('ordenes_ventas/crear/cotizacion' . $row->id) . '" class="btn btn-block btn-outline btn-success convertirOrdenVenta">Convertir a Órden de Venta</a>';

                $cliente = $row->cliente;
                $vendedor = $row->vendedor;

                if ($this->auth->has_permission('acceso', 'oportunidades/crear/') && ($row->estado == 'aprobado')) {
                    $hidden_options .= '<a href="#" data-uuid="' . $row->uuid_cotizacion . '" data-id="' . $row->id . '" data-cliente_id="' . $row->cliente_id . '" class="btn btn-block btn-outline btn-success agregar-oportunidad">Agregar a oportunidad</a>';
                }
                $hidden_options .= '<a href="javascript:" data-id="' . $row->uuid_cotizacion . '" class="exportarTablaCliente btn btn-block btn-outline btn-success subirArchivoBtn">Subir Documento</a>';
                if ($this->auth->has_permission('acceso', 'contratos_alquiler/crear/') &&  $row->estado == 'ganado' && $row->tipo == 'alquiler') {
                    $hidden_options .= '<a href="' . base_url('contratos_alquiler/crear/cotizacion' . $row->uuid_cotizacion) . '" data-uuid="' . $row->uuid_cotizacion . '"   class="btn btn-block btn-outline btn-success">Convertir a contrato de alquiler</a>';
                }
                $response->rows[$i]["id"] = $row->uuid_cotizacion;
                $response->rows[$i]["cell"] = array(
                    $row->uuid_cotizacion,
                    '<a class="link" href="' . base_url($modulo . '/' . $row->uuid_cotizacion) . '">' . $row->codigo . '</a>',
                    '<a class="link">' . $row->cliente_nombre . '</a>',
                    $row->fecha_desde,
                    $row->fecha_hasta,
                    $row->formatEstado()->estado_label,
                    '<a class="link">' . $row->vendedor_nombre . '</a>',
                    $link_option,
                    $hidden_options
                );
                $i++;
            }
        }
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))->_display();
        exit;
    }

    public function ajax_listar2() {

        if (!$this->input->is_ajax_request()) {
            return false;
        }



        $clause = $this->input->post();
        $clause['empresa_id'] = $this->empresaObj->id;
        unset($clause['cliente_id']);

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = $this->cotizacionRepository->lista_totales($clause);
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $cotizaciones = $this->cotizacionRepository->listar($clause, $sidx, $sord, $limit, $start);

        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;



        if (!empty($cotizaciones->toArray())) {
            $i = 0;
            foreach ($cotizaciones as $row) {
                $modulo = ($row->tipo == 'venta') ? 'cotizaciones/ver' : 'cotizaciones_alquiler/editar';
                $hidden_options = "";
                $orden = $row->ordenes_validas()->count();
                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row->uuid_cotizacion . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $hidden_options = '<a href="' . base_url($modulo . '/' . $row->uuid_cotizacion) . '" data-id="' . $row->uuid_cotizacion . '" class="btn btn-block btn-outline btn-success">Ver Cotizacion</a>';


                //para convertir a cotizacion debe estar aprobada, ser de venta y no estar asociada a un cliente potencial
                if ($orden == 0 && ($row->estado == 'aprobado') && $row->cliente_tipo == "cliente" && $row->tipo == "venta")
                    $hidden_options .= '<a href="' . base_url('ordenes_ventas/crear/cotizacion' . $row->id) . '" class="btn btn-block btn-outline btn-success convertirOrdenVenta">Convertir a Órden de Venta</a>';

                $cliente = $row->cliente;
                $vendedor = $row->vendedor;


                $response->rows[$i]["id"] = $row->uuid_cotizacion;
                $response->rows[$i]["cell"] = array(
                    $row->uuid_cotizacion,
                    '<a class="link" href="' . base_url($modulo . '/' . $row->uuid_cotizacion) . '">' . $row->codigo . '</a>',
                    $row->fecha_desde,
                    $row->fecha_hasta,
                    $row->formatEstado()->estado_label,
                    '<a class="link">' . $row->vendedor_nombre . '</a>',
                    $link_option,
                    $hidden_options
                );
                $i++;
            }
        }
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))->_display();
        exit;
    }

    public function ocultotabla($uuid = NULL, $modulo = NULL) {

        //If ajax request
        if ($modulo == 'facturas') {

            $this->assets->agregar_var_js(array(
                "factura_id" => $uuid
            ));
        } else {

            if (!empty($uuid) && !is_null($modulo)) {
                $this->_planel_variable($modulo, $uuid);
            }
        }

        $this->assets->agregar_js(array(
            'public/assets/js/modules/cotizaciones/tabla.js'
        ));



        $this->load->view('tabla');
    }

    public function ocultotabla2($key_value = NULL) {

        $this->assets->agregar_js(array(
            'public/assets/js/modules/cotizaciones/tabla2.js'
        ));

        if ($key_value and count(explode('=', $key_value)) > 1) {

            $aux = explode('=', $key_value);
            $this->assets->agregar_var_js(array(
                $aux[0] => $aux[1]
            ));
        }

        $this->load->view('tabla');
    }

    public function crear($foreing_key = '') {
        if (preg_match('/oportunidad/', $foreing_key)) {
            $oportunidad_id = str_replace('oportunidad', '', $foreing_key);
            $oportunidad = $this->OportunidadesRepository->findBy(['oportunidad_id' => $oportunidad_id]);
            $empezable_id = $oportunidad->cliente_id;
            $empezable_type = $oportunidad->cliente_tipo;
        }
        

        if (preg_match('/cliente/', $foreing_key)) {
            $cliente_id = str_replace('cliente', '', $foreing_key);
            $cliente =  $this->clienteRepo->find($cliente_id);
            $empezable_id = $cliente->id;
            $empezable_type = 'cliente';
        }
        
        $acceso = 1;
        $mensaje = array();
        if (!$this->auth->has_permission('acceso')) {
            $acceso = 0;
            $mensaje = array('estado' => 500, 'mensaje' => ' <b>Usted no cuenta con permiso para esta solicitud</b>', 'clase' => 'alert-danger');
        }

        $this->_Css();
        $this->_js();

        $empezable = collect([
            'id' => isset($empezable_id) ? $empezable_id : '',
            'type' => isset($empezable_type) ? $empezable_type : '',
            'clientes' => [],
            'cliente_potencials' => []
        ]);
        $editar_precio = 1;
        if(!$this->auth->has_permission('crear__editarPrecioCotizacion')){
            $editar_precio= 0;
        }

        $this->assets->agregar_var_js(array(
            "vista" => 'crear',
            "acceso" => $acceso,
            "empezable" => $empezable,
            "editar_precio" => $editar_precio
        ));

        $data = array(
            'info' => isset($oportunidad_id) ? ['oportunidad_id' => $oportunidad_id] : []
        );
        
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-line-chart"></i> Crear Cotización',
        );

        $data['mensaje'] = $mensaje;
        $this->template->agregar_titulo_header('Crear Cotizacion');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    function ajax_guardar_comentario() {

        if (!$this->input->is_ajax_request()) {
            return false;
        }

        $model_id = $this->input->post('modelId');
        $comentario = $this->input->post('comentario');
        $uuid_usuario = $this->session->userdata('huuid_usuario');
        $usuario = Usuario_orm::findByUuid($uuid_usuario);
        $comentario = ['comentario' => $comentario, 'usuario_id' => $usuario->id];

        $cotizacion = $this->cotizacionRepository->agregarComentario($model_id, $comentario);

        $cotizacion->load('comentario_timeline', 'items');
        $cotizacion->items->each(function ($item, $key ) use ($cotizacion) {
            if ($item->comentario != '') {
                $fieldset = array(
                    'comentario' => $item->comentario,
                    "usuario_id" => 1,
                    "created_at" => ($item->updated_at != null) ? $item->updated_at : '2016-01-01 00:00:00'
                );

                $comentarios = new Comentario($fieldset);
                $cotizacion->comentario_timeline->push($comentarios);
            }
            return $cotizacion;
        });

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($cotizacion->comentario_timeline->toArray()))->_display();
        exit;
    }

    public function ver($uuid = NULL) {
        $acceso = 1;
        $mensaje = array();
        if (!$this->auth->has_permission('acceso', 'cotizaciones/ver/(:any)')) {
            // No, tiene permiso, redireccionarlo.
            $acceso = 0;
            $mensaje = array('estado' => 500, 'mensaje' => ' <b>Usted no cuenta con permiso para esta solicitud</b>', 'clase' => 'alert-danger');
        }

        $this->_Css();
        $this->_js();

        $cotizacion = $this->cotizacionRepository->findByUuid($uuid);

        if (is_null($uuid) || is_null($cotizacion)) {
            $mensaje = array('estado' => 500, 'mensaje' => '<strong>¡Error!</strong> Su solicitud no fue procesada');
            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('cotizaciones/listar'));
        } else {

            $data = array();
            $data['cotizacion_id'] = $cotizacion->id;
            $data['mensaje'] = $mensaje;

            $empezable = collect([
                "{$cotizacion->cliente_tipo}s" => [0 => ['id' => $cotizacion->cliente_id, 'nombre' => $cotizacion->cliente->nombre]],
                "id" => $cotizacion->cliente_id,
                "type" => $cotizacion->cliente_tipo
            ]);

            $this->assets->agregar_var_js(array(
                "vista" => "editar",
                "acceso" => $acceso,
                "cotizacion" => $this->cotizacionRepository->getCollectionCotizacion($cotizacion),
                "empezable" => $empezable
            ));

            $breadcrumb = array(
                "titulo" => '<i class="fa fa-line-chart"></i> Cotización: ' . $cotizacion->codigo,
            );
            if ($cotizacion->imprimible) {
                $breadcrumb["menu"]["opciones"]["cotizaciones/imprimir/" . $cotizacion->uuid_cotizacion] = "Imprimir";
            }

            $editar_precio = 1;
            if(!$this->auth->has_permission('ver__editarPrecioCotizacion')){
                $editar_precio= 0;
            }
            $this->assets->agregar_var_js(array(
                "editar_precio" => !empty($editar_precio) ? $editar_precio : 1
            ));

            $this->template->agregar_titulo_header('Editar Cotizacion');
            $this->template->agregar_breadcrumb($breadcrumb);
            $this->template->agregar_contenido($data);
            $this->template->visualizar();
        }
    }

    public function imprimir($uuid = null) {
        if ($uuid == null) {
            return false;
        }

        $cotizacion = $this->cotizacionRepository->findByUuid($uuid);
        $cotizacion->load("empresa");
        $dompdf = new Dompdf();
        $data = ['cotizacion' => $cotizacion];

        $html = $this->load->view('cotizacion', $data, true);
        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream($cotizacion->codigo . ' - ' . $cotizacion->cliente->nombre);
    }



    public function ocultoformulario($cotizacion = array()) {

        $this->assets->agregar_js(array(
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/default/vue/components/empezar_desde.js',
            'public/assets/js/modules/cotizaciones/components/detalle.js',
            //'public/assets/js/default/vue/components/articulos.js',
            //'public/assets/js/default/vue/components/articulo.js',
            'public/assets/js/default/vue/directives/pop_over_precio.js',
            'public/assets/js/default/vue/directives/pop_over_cantidad.js',
            'public/resources/compile/modulos/cotizaciones/formulario.js'
        ));

        //catalogos
        $clause = ['empresa_id' => $this->id_empresa, 'transaccionales' => true, 'conItems' => true, 'vendedor' => true, 'tipo_precio' => 'venta'];
        
        $this->assets->agregar_var_js(array(
            'usuario_id' => $this->id_usuario,
            'clientes' => $this->clienteRepo->getCollectionClientes($this->clienteRepo->getClientesEstadoActivo($clause)->get()),
            'cliente_potencials' => $this->ClientesPotencialesRepository->getCollectionClientesPotenciales($this->ClientesPotencialesRepository->get($clause)),
            'terminos_pago' => $this->FacturaVentaCatalogoRepository->getTerminoPago(),
            'vendedores' => $this->UsuariosRepository->getCollectionUsuarios($this->UsuariosRepository->get($clause)),
            'precios' => $this->ItemsPreciosRepository->get($clause),
            'centros_contables' => $this->CentrosContablesRepository->getCollectionCentrosContables($this->CentrosContablesRepository->get($clause)),
            'estados' => $this->cotizacionCatalogoRepository->getEtapas(),
            'categorias' => $this->ItemsCategoriasRepository->getCollectionCategorias($this->ItemsCategoriasRepository->get($clause)),
            'cuentas' => $this->CuentasRepository->get($clause),
            'impuestos' => $this->ImpuestosRepository->get($clause)
        ));

        $this->load->view('formulario');
        $this->load->view('vue/components/empezar_desde');
        $this->load->view('components/detalle');
        //$this->load->view('vue/components/articulos');
        //$this->load->view('vue/components/articulo');
    }





    public function ajax_data_formulario_alquiler() {
        $clause = array('empresa_id' => $this->id_empresa);
        $clientes = $this->clienteRepo->getClientes($clause)->get(['id', 'nombre', 'credito_favor', 'exonerado_impuesto']);
        $clientes->load('centro_facturable');
        $categotiasItems = Categorias_orm::with(array('items' => function($query) {
                        $query->where("item_alquiler", '=', 1);
                    }))->where(array('empresa_id' => $this->id_empresa, 'estado' => 1))->get(['id', 'nombre']);


        foreach ($categotiasItems as $cat) {

            foreach ($cat->items as $l) {
                $l->atributos; //Lista Nueva para los atributos del item
                $l->item_unidades;
                $l->precios;
                $l->unidades;
                $l->impuesto;
                $l->atributos;
            }

            $cat->items->transform(function($item) {
                $item->uuid_ingreso = strtoupper(bin2hex($item->uuid_ingreso));
                $item->uuid_venta = strtoupper(bin2hex($item->uuid_venta));
                return $item;
            });
        }
        $response = array('clientes' => $clientes->toArray(), 'categorias' => $categotiasItems->toArray());

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($response))->_display();
        exit;
    }

    public function guardar() {

        if ($_POST) {

            $input = Illuminate\Http\Request::createFromGlobals();

            $cotizacion = $input->input("campo");
            $lineitems = $input->input("items");

            if (empty($cotizacion['id'])) {
                $cotizacion['empresa_id'] = $this->id_empresa;
                $total = $this->cotizacionRepository->lista_totales(['empresa_id' => $this->id_empresa]);
                $year = Carbon::now()->format('y');
                $codigo = Util::generar_codigo('QT' . $year, $total + 1);
                $cotizacion['codigo'] = $codigo;
            }

            Capsule::beginTransaction();
            try {
                $cotizacion['cliente_tipo'] = !empty($this->input->post('empezable_type')) ? $this->input->post('empezable_type') : 'cliente';
                $data = array('cotizacion' => $cotizacion, 'lineitem' => $lineitems);
                if (empty($cotizacion['id'])) {
                    $modelCotizacion = $this->cotizacionRepository->create($data);
                } else {
                    $modelCotizacion = $this->cotizacionRepository->update($data);
                }
            } catch (Illuminate\Database\QueryException $e) {
                // Rollback
                log_message('error', " __METHOD__  ->  , Linea:  __LINE__  --> " . $e->getMessage() . "\r\n");
                Capsule::rollback();
                $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error! Su solicitud no fue procesada</b> ');
                $this->session->set_flashdata('mensaje', $mensaje);
                redirect(base_url('cotizaciones/listar'));
            }

            if (!is_null($modelCotizacion)) {
                Capsule::commit();
                $model = $modelCotizacion->fresh();
                $mensaje = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente ' . $model->codigo);
            } else {
                $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error! Su solicitud no fue procesada</b> ');
            }
            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('cotizaciones/listar'));
        }
    }

    public function ocultotablaV2($sp_string_var = []) {

        $this->assets->agregar_js(array(
            'public/assets/js/modules/cotizaciones/tabla.js'
        ));

        $sp_array_var = explode('=', $sp_string_var);
        if (count($sp_array_var) == 2) {

            $this->assets->agregar_var_js(array(
                $sp_array_var[0] => $sp_array_var[1]
            ));
        }

        $this->load->view('tabla');
    }

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
            'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
            'public/assets/css/plugins/bootstrap/select2.min.css',
            'public/assets/css/plugins/jquery/jquery.webui-popover.css',
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
            'public/assets/js/plugins/jquery/jquery.webui-popover.js'
        ));
    }

    private function _planel_variable($modulo, $uuid) {
        $jsVariable = '';
        $jsVariable = $this->assets->agregar_var_js(array(
            "cliente_id" => $uuid
        ));
        return $jsVariable;
    }

    function documentos_campos() {

        return array(
            array(
                "type" => "hidden",
                "name" => "cotizacion_id",
                "id" => "cotizacion_id",
                "class" => "form-control",
                "readonly" => "readonly",
        ));
    }

    function ajax_guardar_documentos() {
        if (empty($_POST)) {
            return false;
        }

        $cotizacion_id = $this->input->post('cotizacion_id', true);
        $modeloInstancia = $this->cotizacionRepository->findByUuid($cotizacion_id);
        $this->documentos->subir($modeloInstancia);
    }
}

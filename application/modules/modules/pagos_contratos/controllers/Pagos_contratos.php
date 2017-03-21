<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 *
 * @package    Flexio
 * @subpackage Controller
 * @category   Pagos
 * @author     Pensanomica Team
 * @link       http://www.pensanomica.com
 * @copyright  01/15/2016
 */
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Cajas\Repository\CajasRepository as CajasRepository;
use Carbon\Carbon as Carbon;
use Flexio\Strategy\Transacciones\Transaccion as Transaccion;
use Flexio\Strategy\Transacciones\TransaccionPago as TransaccionPago;
//repositories
use Flexio\Modulo\Pagos\Repository\PagosRepository as pagosRep;
use Flexio\Modulo\Proveedores\Repository\ProveedoresRepository as proveedoresRep;
use Flexio\Modulo\SubContratos\Repository\SubContratoRepository as subcontratosRep;
use Flexio\Modulo\SubContratos\Models\SubContrato as subcontratosVal;
use Flexio\Modulo\Contratos\Repository\PagosRepository as pagosContRep;

class Pagos_contratos extends CRM_Controller {

    private $empresa_id;
    private $id_usuario;
    private $empresaObj;
    protected $pagoGuardar;
    protected $listaCobro;
    protected $cajaRepository;
    //repositories
    private $pagosRep;
    private $proveedoresRep;
    private $subcontratosRep;
    private $subcontratosVal;
    private $usuarioId;
    private $pagosContRep;

    function __construct() {
        parent::__construct();
        $this->load->model('usuarios/Usuario_orm');
        $this->load->model('usuarios/Empresa_orm');
        $this->load->model('usuarios/Roles_usuarios_orm');
        $this->load->model('roles/Rol_orm');

        $this->load->model('proveedores/Proveedores_orm');

        $this->load->model('ordenes/Ordenes_orm');

        $this->load->model('bancos/Bancos_orm');
//    $this->load->model('cotizaciones/Cotizacion_orm');
//    $this->load->model('cotizaciones/Cotizacion_catalogo_orm');
//    $this->load->model('cotizaciones/Cotizacion_item_orm');
//    $this->load->model('inventarios/Items_orm');
//    $this->load->model('inventarios/Items_precios_orm');
//    $this->load->model('inventarios/Precios_orm');
//    $this->load->model('inventarios/Unidades_orm');
//    $this->load->model('contabilidad/Impuestos_orm');
//    $this->load->model('contabilidad/Cuentas_orm');
//    $this->load->model('contabilidad/Centros_orm');
//    $this->load->model('bodegas/Bodegas_orm');
//    $this->load->model('ordenes_ventas/Orden_ventas_orm');
//    $this->load->model('ordenes_ventas/Ordenes_venta_item_orm');
        $this->load->model('facturas_compras/Facturas_compras_orm');
//    $this->load->model('facturas_compras/Factura_items_orm');
//    $this->load->model('facturas_compras/Factura_catalogo_orm');
//
        $this->load->model('pagos_contratos/Pagos_contratos_orm');
        $this->load->model('pagos_contratos/Pago_catalogos_contratos_orm');
        $this->load->model('pagos_contratos/Pago_metodos_pago_contratos_orm');
        $this->load->model('pagos_contratos/Pago_pagables_contratos_orm');
//
//    $this->load->module("salidas/Salidas");

        Carbon::setLocale('es');
        setlocale(LC_TIME, 'Spanish');

        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresaObj = new Buscar(new Empresa_orm, 'uuid_empresa');
        $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
        $this->id_usuario = $this->session->userdata("huuid_usuario");
        $this->empresa_id = $this->empresaObj->id;
        $this->usuarioId =  $this->session->userdata("id_usuario");

        $this->load->library('Repository/Pagos/Guardar_pago');
        $this->load->library('Repository/Pagos/Lista_pago');
        $this->pagoGuardar = new Guardar_pago;
        $this->listaPago = new Lista_pago;
        $this->cajaRepository = new CajasRepository();

        //repositories
        $this->pagosRep = new pagosRep();
        $this->proveedoresRep = new proveedoresRep();
        $this->subcontratosRep = new subcontratosRep();
        $this->subcontratosVal = new subcontratosVal();
        //$this->pagosContRep = new pagosContRep();
    }

    function listar() {

        $data = array();
        if (!$this->auth->has_permission('acceso')) {
            $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud');
            $this->session->set_flashdata('mensaje', $mensaje);
        }

        $this->_Css();
        $this->_js();
        $this->assets->agregar_js(array(
            'public/assets/js/modules/pagos_contratos/listar.js',
            'public/assets/js/default/toast.controller.js'
        ));

        $breadcrumb = array("titulo" => '<i class="fa fa-shopping-cart"></i> Pagos',
            "ruta" => array(
                0 => array(
                    "nombre" => "Compras",
                    "activo" => false
                ),
                1 => array(
                    "nombre" => '<b>Pagos</b>',
                    "activo" => true
                )
            ),
            "menu" => array(
                "nombre" => "Crear",
                "url" => "pagos/crear",
                "opciones" => array()
            )
        );

        if (!is_null($this->session->flashdata('mensaje'))) {
            $mensaje = json_encode($this->session->flashdata('mensaje'));
        } else {
            $mensaje = '';
        }
        $this->assets->agregar_var_js(array(
            "toast_mensaje" => $mensaje
        ));


        $data['proveedores'] = Proveedores_orm::deEmpresa($this->empresa_id)->get(array('id', 'nombre'));
        $data['etapas'] = Pago_catalogos_contratos_orm::where('tipo', 'etapa3')->get(array('etiqueta', 'valor'));
        $data['formas_pago'] = Pago_catalogos_contratos_orm::where('tipo', 'pago')->get(array('id', 'etiqueta', 'valor'));
        $data['bancos'] = Bancos_orm::get(array('id', 'nombre'));

        $breadcrumb["menu"]["opciones"]["#exportarListaPagos"] = "Exportar";
        $this->template->agregar_titulo_header('Listado de Pagos');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }

    private function _filtrarPagos($pagos) {
        /*
        paramentos de busqueda aqui    
        */
        $desde      = $this->input->post('desde',TRUE);
        $hasta      = $this->input->post('hasta',TRUE);
        $proveedor  = $this->input->post('proveedor',TRUE);
        $estado     = $this->input->post('estado',TRUE);
        $montoMin   = $this->input->post('montoMin',TRUE);
        $montoMax   = $this->input->post('montoMax',TRUE);
        $formaPago  = $this->input->post('formaPago',TRUE);
        $tipo       = $this->input->post('tipo',TRUE);
        $banco      = $this->input->post('banco',TRUE);
        $uuid_pedidos = $this->input->post('pedidos_id', true);

        
        //subpanels
        $orden_compra_id    = $this->input->post("orden_compra_id", true);
        if(!empty($orden_compra_id)){$pagos->deOrdenDeCompra($orden_compra_id);}
        
        $factura_compra_id    = $this->input->post("factura_compra_id", true);
        if(!empty($factura_compra_id)){$pagos->deFacturaDeCompra($factura_compra_id);}
        
        if(!empty($desde)) $pagos->deFechaDesde($desde);
        if(!empty($hasta)) $pagos->deFechaHasta($hasta);
        if(!empty($proveedor)) $pagos->deProveedor($proveedor);
        if(!empty($estado)) $pagos->deEstado($estado);
        if(!empty($montoMin)) $pagos->deMontoMin($montoMin);
        if(!empty($montoMax)) $pagos->deMontoMax($montoMax);
        if(!empty($formaPago)) $pagos->deFormaPago($formaPago);
        if(!empty($tipo)) $pagos->deTipo($tipo);
        if(!empty($banco)) $pagos->deBanco($banco);
        if (!empty($uuid_pedidos))$pagos->dePedidos($uuid_pedidos);
    }
    
    public function ajax_exportar()
    {
        $clause                 = [];
        $clause["empresa_id"]   = $this->empresa_id;
        $clause["uuid_pagos"]   = $this->input->post("uuid_pagos", true);

        $csv = \League\Csv\Writer::createFromFileObject(new \SplTempFileObject());
        $csv->insertOne([utf8_decode("Número de pago"), "Fecha", "Proveedor", "No. Documento", "Forma de Pago", "Banco", "Estado", "Monto"]);
        $csv->insertAll($this->pagosRep->getCollectionExportar($this->pagosRep->get($clause)));

        $csv->output('pagos.csv');
        exit;
    }

    function ajax_listar() {
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        $pagos = Pagos_contratos_orm::deEmpresa($this->empresa_id);
        $pagos->deContratos();
        //dd($pagos);
        $this->_filtrarPagos($pagos);

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = $pagos->count();
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

        $pagos->orderBy($sidx, $sord)->skip($start)->take($limit);

        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;


        if ($count) {

            foreach ($pagos->get() as $i => $row) {
                $factura = $row->facturas->last();
                //dd($factura);
                $hidden_options = "";
                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row->uuid_pago . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $hidden_options = '<a href="' . base_url('pagos_contratos/ver/' . $row->uuid_pago) . '" data-id="' . $row->uuid_pago . '" class="btn btn-block btn-outline btn-success">Ver Detalle</a>';
                //$row->total_pagado();
                if ($row->estado != 'anulado' && $row->estado != 'aplicado')
                    $hidden_options .= '<a href="' . base_url('pagos_contratos/registrar_pago_pago/' . $row->uuid_pago) . '" data-id="' . $row->uuid_pago . '" class="btn btn-block btn-outline btn-success">Registrar Pago</a>';

                $proveedor = $row->proveedor;
                $etapa = $row->catalogo_estado;
                $metodo_pago = "ddd";



                $response->rows[$i]["id"] = $row->uuid_pago;
                $response->rows[$i]["cell"] = array(
                    $row->uuid_pago,
                    '<a class="link" href="' . base_url('pagos_contratos/ver/' . $row->uuid_pago) .'" style="color:blue;">'. $row->codigo . '</a>',
                    $row->fecha_pago,
                    '<a class="link">'.$proveedor->nombre.'</a>',
                    count($row->facturas) ? $row->facturas->implode("codigo_enlace", ", ") : '',
                    //!empty($row->operacion_type)? $row->operacion->numero_documento:'',
                    $this->listaPago->metodo_pago($row->metodo_pago),
                    $this->listaPago->banco($row->metodo_pago),
                    $this->listaPago->color_estado($etapa->etiqueta, $etapa->valor),
                    '<label class="' . $this->listaPago->color_monto($row->estado) . '">' . $row->monto_pagado . '</label>',
                    $link_option,
                    $hidden_options
                );
            }
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($response))->_display();
        exit;
    }

    function ocultotabla($uuid_orden_venta = null) {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/pagos_contratos/tabla.js'
        ));

        if (!empty($uuid_orden_venta)) {
            if (preg_match("/pedidos/i", $this->router->fetch_class())) {
                $this->assets->agregar_var_js(array(
                    "pedidos_id" => $uuid_orden_venta
                ));
            } else {
                $this->assets->agregar_var_js(array(
                    "uuid_orden_venta" => $uuid_orden_venta
                ));
            }
        }

        $this->load->view('tabla');
    }

    public function ocultotablaProveedores($uuid_proveedor = null) {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/pagos_contratos/tabla.js'
        ));

        if (!empty($uuid_proveedor)) {
            $this->assets->agregar_var_js(array(
                "uuid_proveedor" => $uuid_proveedor
            ));
        }

        $this->load->view('tabla');
    }
    
    public function ocultotablaOrdenesCompras($orden_compra_id=null){
        $this->assets->agregar_js(array(
            'public/assets/js/modules/pagos_contratos/tabla.js'
        ));

        if (!empty($orden_compra_id)){
            $this->assets->agregar_var_js(array(
                "orden_compra_id" => $orden_compra_id
            ));
        }

        $this->load->view('tabla');
    }
    
    public function ocultotablaFacturasCompras($factura_compra_id=null){
        $this->assets->agregar_js(array(
            'public/assets/js/modules/pagos_contratos/tabla.js'
        ));

        if (!empty($factura_compra_id)){
            $this->assets->agregar_var_js(array(
                "factura_compra_id" => $factura_compra_id
            ));
        }

        $this->load->view('tabla');
    }

    public function crear($foreing_key = ''){
        
        if(preg_match('/proveedor/', $foreing_key))
        {
            $proveedor_id   = str_replace('proveedor', '', $foreing_key);
        }
        elseif(preg_match('/facturacompra/', $foreing_key))
        {
            $factura_compra_uuid = str_replace('facturacompra', '', $foreing_key);
        }
        
        $acceso = 1;
        $mensaje = array();
        if (!$this->auth->has_permission('acceso')) {
            $acceso = 0;
            $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud', 'clase' => 'alert-danger');
        }

        //Obtener el listado de cajas
        $clause = array(
            "empresa_id" => $this->empresa_id
        );
        $cajasList = $this->cajaRepository->getAll($clause)->toArray();
        $cajasList = (!empty($cajasList) ? array_map(function($cajasList){ return array("id" => $cajasList["id"], "nombre" => $cajasList["nombre"]); }, $cajasList) : "");
         
       	$this->_Css();
        $this->assets->agregar_css(array(
            'public/assets/css/modules/stylesheets/animacion.css'
        ));
        $this->_js();
        $this->assets->agregar_js(array(
        	'public/assets/js/modules/pagos_contratos/service.pago.js',
            'public/assets/js/modules/pagos_contratos/crearPago.controller.js',
        ));

        $this->assets->agregar_var_js(array(
            "vista"                 => 'crear',
            "acceso"                => $acceso == 0? $acceso : $acceso,
            "cajasList"             => json_encode($cajasList),
            "proveedor_id"          => (isset($proveedor_id) and !empty($proveedor_id)) ?  $proveedor_id : '',
            "factura_compra_uuid"   => (isset($factura_compra_uuid) and !empty($factura_compra_uuid)) ?  $factura_compra_uuid : ''
        ));

        $data['mensaje'] = $mensaje;
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-shopping-cart"></i> Pago: Crear ',
        );

        $this->template->agregar_titulo_header('Crear Pago');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();

    }

    function ver($uuid = NULL) {
        $mensaje = array();
        $acceso = 1;
        if (!$this->auth->has_permission('acceso', 'pagos_contratos/ver/(:any)')) {
            $acceso = 0;
            $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud', 'clase' => 'alert-danger');
        }

        //Obtener el listado de cajas
        $clause = array(
            "empresa_id" => $this->empresa_id
        );
        $cajasList = $this->cajaRepository->getAll($clause)->toArray();
        $cajasList = (!empty($cajasList) ? array_map(function($cajasList) {
                            return array("id" => $cajasList["id"], "nombre" => $cajasList["nombre"]);
                        }, $cajasList) : "");


        $this->_Css();
        $this->assets->agregar_css(array(
            'public/assets/css/modules/stylesheets/animacion.css'
        ));
        $this->_js();
        $this->assets->agregar_js(array(
            'public/assets/js/modules/pagos_contratos/service.pago.js',
            'public/assets/js/modules/pagos_contratos/crearPago.controller.js',
        ));

        $pagoObj = new Buscar(new Pagos_contratos_orm, 'uuid_pago');
        $pago = $pagoObj->findByUuid($uuid);
        $pagos_cont = $this->pagosRep->findByUuid($uuid);
        $pagos_cont->load('comentario_timeline','pagos_asignados');
        if (is_null($uuid) || is_null($pago)) {
            $mensaje = array('estado' => 500, 'mensaje' => '<strong>¡Error!</strong> Su solicitud no fue procesada');
            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('pago/listar'));
        }
        $contar_pagables = $pago->pagos_pagables()->groupBy('pagable_id')->get();

        if (count($contar_pagables->toArray()) == 1 && $pago->formulario == "factura") {
            $this->assets->agregar_var_js(array(
                "tipo" => 'factura',
                "uuid_factura" => $pago->facturas[0]->uuid_factura,
                "nombre" => $pago->facturas[0]->codigo . ' - ' . $pago->proveedor->nombre
            ));
        } elseif (count($contar_pagables->toArray()) == 1 && $pago->formulario == "planilla") {//opcion no desarrollada
            $this->assets->agregar_var_js(array(
                "tipo" => 'planilla',
                "uuid_planilla" => $pago->planillas[0]->uuid_planilla,
                "nombre" => 'E.D.'//$pago->planillas[0]->codigo.' - '.$pago->proveedor->nombre
            ));
        } elseif ($pago->formulario == "subcontrato") {//opcion no desarrollada
            $facturas = $pago->facturas->filter(function($factura) {
                        return $factura->operacion_type == 'Flexio\Modulo\SubContratos\Models\SubContrato';
                    })->values();

            $subcontrato = $this->subcontratosRep->findBy($facturas[0]->operacion_id);

            $this->assets->agregar_var_js(array(
                "tipo" => 'subcontrato',
                "uuid_subcontrato" => $subcontrato->uuid_subcontrato,
                "nombre" => $subcontrato->numero_documento . ' - ' . $subcontrato->proveedor->nombre
            ));
        } elseif (count($contar_pagables->toArray()) > 1 || $pago->formulario == 'proveedor') {
            $this->assets->agregar_var_js(array(
                "tipo" => 'proveedor',
                "uuid_proveedor" => $pago->proveedor->uuid_proveedor,
                "nombre" => $pago->proveedor->nombre
            ));
        }
        $data = array();
        $clause = array('empresa_id' => $this->empresa_id);
//        $facturas   = Facturas_compras_orm::with('proveedor')->where(function($query) use($clause){
//            $query->where('empresa_id','=',$clause['empresa_id']);
//            $query->whereNotIn('estado',array('anulada'));
//        })->get();
        $this->assets->agregar_var_js(array(
            "vista"                 => 'ver',
            "acceso"                => $acceso == 0? $acceso : $acceso,
            "uuid_pago"             => $pago->uuid_pago,
            "cajasList"             => json_encode($cajasList),
            "proveedor_id"          => '',
            "factura_compra_uuid"   => '',
            "coment"                =>(isset($pagos_cont->comentario_timeline)) ? $pagos_cont->comentario_timeline : "",
            "pagos_cont_id"         => $pagos_cont->id
        ));

        //$data['facturas'] = $facturas->toArray();
        $data['uuid_pago'] = $pago->uuid_pago;
        $data['proveedor_id'] = $pago->proveedor->uuid_proveedor;
        $data['mensaje'] = $mensaje;
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-shopping-cart"></i> Pago: ' . $pago->codigo,
        );

        $this->template->agregar_titulo_header('Ver Pago');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    function registrar_pago($uuid = NULL) {
        //dd($this->pagoGuardar);
        $acceso = 1;
        $mensaje = array();
        if (!$this->auth->has_permission('acceso', 'pagos_contratos/registrar_pago/(:any)')) {
            $acceso = 0;
            $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud', 'clase' => 'alert-danger');
        }

        $this->_Css();
        $this->assets->agregar_css(array(
            'public/assets/css/modules/stylesheets/animacion.css'
        ));
        $this->_js();
        $this->assets->agregar_js(array(
            'public/assets/js/modules/pagos_contratos/service.pago.js',
            'public/assets/js/modules/pagos_contratos/registarCobro.controller.js',
        ));

        $facturaObj = new Buscar(new Factura_orm, 'uuid_factura');
        $factura = $facturaObj->findByUuid($uuid);
        if (is_null($uuid) || is_null($factura)) {
            $mensaje = array('estado' => 500, 'mensaje' => '<strong>¡Error!</strong> Su solicitud no fue procesada');
            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('facturas_compras/listar'));
        }

        $data = array();
        $clause = array('empresa_id' => $this->empresa_id);
        $facturas = Factura_orm::with('proveedor')->where(function($query) use($clause) {
                    $query->where('empresa_id', '=', $clause['empresa_id']);
                    $query->whereNotIn('estado', array('anulada'));
                })->get();
        $this->assets->agregar_var_js(array(
            "vista" => 'registrar_pago',
            "acceso" => $acceso == 0 ? $acceso : $acceso,
            "uuid_factura" => $factura->uuid_factura
        ));

        $data['facturas'] = $facturas->toArray();
        //$data['uuid_factura'] = $factura->uuid_factura;
        $data['mensaje'] = $mensaje;
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-line-chart"></i> Registar Cobro: Factura ' . $factura->codigo,
        );

        $this->template->agregar_titulo_header('Crear Pago');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    function registrar_pago_pago($uuid = NULL) {
        //dd($this->pagoGuardar);
        $acceso = 1;
        $mensaje = array();
        if (!$this->auth->has_permission('acceso', 'pagos_contratos/registrar_pago_pago/(:any)')) {
            $acceso = 0;
            $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud', 'clase' => 'alert-danger');
        }

        $this->_Css();
        $this->assets->agregar_css(array(
            'public/assets/css/modules/stylesheets/animacion.css'
        ));
        $this->_js();
        $this->assets->agregar_js(array(
            'public/assets/js/modules/pagos_contratos/service.pago.js',
            'public/assets/js/modules/pagos_contratos/crearCobro.controller.js',
        ));

        $pagoObj = new Buscar(new Pagos_contratos_orm, 'uuid_pago');
        $pago = $pagoObj->findByUuid($uuid);
        if (is_null($uuid) || is_null($pago)) {
            $mensaje = array('estado' => 500, 'mensaje' => '<strong>¡Error!</strong> Su solicitud no fue procesada');
            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('pago/listar'));
        }
        $contar_facturas = $pago->factura_pagos()->groupBy('factura_id')->get();
        //dd($pago->factura_pagos->toArray());
        if (count($contar_facturas->toArray()) == 1) {
            $this->assets->agregar_var_js(array(
                "tipo" => 'factura',
                "uuid_factura" => $pago->factura_pagos[0]->uuid_factura
            ));
        } elseif (count($contar_facturas->toArray()) > 1) {
            $this->assets->agregar_var_js(array(
                "tipo" => 'proveedor',
                "uuid_proveedor" => $pago->proveedor->uuid_proveedor
            ));
        }
        $data = array();
        $clause = array('empresa_id' => $this->empresa_id);
        $facturas = Facturas_compras_orm::with('proveedor')->where(function($query) use($clause) {
                    $query->where('empresa_id', '=', $clause['empresa_id']);
                    $query->whereNotIn('estado', array('anulada'));
                })->get();
        $this->assets->agregar_var_js(array(
            "vista" => 'registrar_pago_pago',
            "acceso" => $acceso == 0 ? $acceso : $acceso,
            "uuid_pago" => $pago->uuid_pago
        ));

        $data['facturas'] = $facturas->toArray();
        //$data['uuid_factura'] = $factura->uuid_factura;
        $data['mensaje'] = $mensaje;
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-line-chart"></i> Registar Cobro: ' . $pago->codigo,
        );

        $this->template->agregar_titulo_header('Crear Pago');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    function ocultoformulario($facturas = array()) {
        $data = array();
        $clause = array('empresa_id' => $this->empresa_id);

        $data['tipo_pagos'] = Pago_catalogos_orm::where('tipo', 'pago')->get(array('id', 'etiqueta', 'valor'));
        $data['bancos'] = Bancos_orm::get(array('id', 'nombre'));
        $data['cuenta_bancos'] = Cuentas_orm::cuentasBanco($clause);
        $data['proveedores'] = Proveedores_orm::deEmpresa($this->empresa_id)->get(array('id', 'nombre', 'limite_credito'));

        if (isset($facturas['info']))
            $data['info'] = $facturas['info'];

        $this->load->view('formulario', $data);
    }

    function ocultoformulariover($facturas = array()) {
        $data = array();
        $clause = array('empresa_id' => $this->empresa_id);

        $data['tipo_pagos'] = Pago_catalogos_contratos_orm::where('tipo', 'pago')->get(array('id', 'etiqueta', 'valor'));
        $data['bancos'] = Bancos_orm::get(array('id', 'nombre'));
        $data['etapas'] = Pago_catalogos_contratos_orm::where('tipo', 'etapa3')->get(array('etiqueta', 'valor'));
        $data['cuenta_bancos'] = Cuentas_orm::cuentasBanco($clause);
        $data['proveedores'] = Proveedores_orm::deEmpresa($this->empresa_id)->get(array('id', 'nombre', 'limite_credito'));

        if (isset($facturas['info']))
            $data['info'] = $facturas['info'];

        $this->load->view('formulario_ver', $data);
    }

    private function _createPago($pago, $post) {
        $total = Pagos_contratos_orm::deEmpresa($this->empresa_id)->count();
        $year = Carbon::now()->format('y');
        $codigo = Util::generar_codigo('PGO' . $year, $total + 1);

        $pago->codigo = $codigo;
        $pago->empresa_id = $this->empresa_id;
        $pago->fecha_pago = date("Y-m-d", strtotime($post["campo"]["fecha_pago"]));
        $pago->proveedor_id = $post["campo"]["proveedor"];
        $pago->monto_pagado+= $post['campo']['total_pagado'];
        $pago->cuenta_id = $post["campo"]["cuenta_id"];
        $pago->formulario = $post["campo"]["formulario"];
        $pago->estado = 'por_aplicar';
    }

    //en la edicion de pagos solo se puede cambiar el estado
    private function _setPagoFromPost($pago, $post) {
        $pago->estado = isset($post["campo"]["estado"]) ? $post["campo"]["estado"] : 'por_aplicar';
    }

    private function _setPagables($pago, $post) {
        if ($post["campo"]["formulario"] == "factura" || $post["campo"]["formulario"] == "proveedor" || $post["campo"]["formulario"] == "subcontrato") {
            $pago->facturas()->sync($this->_getPagosPagablesFromPost($post, "Facturas_compras_orm"));
        } elseif ($post["campo"]["formulario"] == "planilla") {
            $pago->planillas()->sync($this->_getPagosPagablesFromPost($post, "Planilla_orm"));
        }
    }

    private function _getPagosPagablesFromPost($post, $pagable_type) {
        $aux = [];

        foreach ($post["items"] as $documento) {//factura o planilla
            $aux[$documento["factura_id"]] = [
                "pagable_type" => $pagable_type,
                "monto_pagado" => $documento["monto_pagado"],
                "empresa_id" => $this->empresa_id
            ];
        }

        return $aux;
    }

    private function _setMetodosPagos($pago, $post) {
        foreach ($post["metodo_pago"] as $metodo) {
            $referencia = $this->pagoGuardar->tipo_pago($metodo['tipo_pago'], $metodo);
            $item_pago = new Pago_metodos_pago_orm;

            $item_pago->tipo_pago = $metodo['tipo_pago'];
            $item_pago->total_pagado = $metodo['total_pagado'];
            $item_pago->referencia = $referencia;

            $pago->metodo_pago()->save($item_pago);
        }
    }

    private function _sePuedeAplicarPago($pago) {
        $sePuedeAplicarPago = TRUE;

        $pagables = ($pago->formulario !== "planilla") ? $pago->facturas : $pago->planillas;
        foreach ($pagables as $pagable) {//facturas o planillas
            $total = $pagable->total;
            $pagado = $pagable->pagos_aplicados()->sum("pag_pagos_pagables.monto_pagado");
            $saldo = $total - $pagado;
            $monto_aplicar = $pagable->pivot->monto_pagado;

            if (round($monto_aplicar, 2) > round($saldo, 2)) {
                $sePuedeAplicarPago = FALSE;
                break;
            }
        }

        return $sePuedeAplicarPago;
    }

    private function _actualizarEstadoPagable($pago) {
        $pagables = ($pago->formulario !== "planilla") ? $pago->facturas : $pago->planillas;
        foreach ($pagables as $pagable) {//facturas o planillas
            $total = $pagable->total;
            $pagado = (count($pagable->pagos_aplicados)) ? $pagable->pagos_aplicados()->sum("pag_pagos_pagables.monto_pagado") : 0;
            $saldo = round($total, 2) - round($pagado, 2);

            if ($pagado === 0) {
                $pagable->estado_id = 14; //por pagar
            } elseif ($saldo > 0) {
                $pagable->estado_id = 15; //pagada parcial
            } elseif ($saldo == 0) {
                $pagable->estado_id = 16; //pagada completa
            }

            $pagable->save();
        }
    }

    function guardar() {

        if ($_POST) {
//            echo "<pre>";
//            print_r($_POST);
//            echo "<pre>";
//            die();
            //campos para guardar el pago
            $success = FALSE;
            $post = $this->input->post();
            Capsule::transaction(function() use ($post, &$success) {

                $success = TRUE;
                if (empty($post['campo']["id"])) {//identificador del pago
                    $pago = new Pagos_contratos_orm;
                    $this->_createPago($pago, $post);
                    $pago->save();

                    $this->_setPagables($pago, $post); //model sync();
                    $this->_setMetodosPagos($pago, $post);
                } else {
                    $pago = Pagos_contratos_orm::find($post["campo"]["id"]);

                    //si hay cambio de estado y el nuevo estado es aplicar
                    //se debe comprobar si el saldo de cada una de las facturas relacionadas al
                    //mismo es menor o igual al pago realizado.
                    if ($pago->estado != $post["campo"]["estado"] && $post["campo"]["estado"] == "aplicado") {
                        //se puede aplicar pago verifica que el monto del pago no sea superior a lo que
                        //se requiere pagar
                        if ($this->_sePuedeAplicarPago($pago)) {
                            if ($post["metodo_pago"][0]["tipo_pago"] == "aplicar_credito" and ! $this->proveedoresRep->restar_credito($post["campo"]["proveedor"], $post["campo"]["total_pagado"])) {
                                $success = FALSE;
                            } else {
                                $this->_setPagoFromPost($pago, $post); //solo cambia el estado del pago
                                $pago->save();
                                $transaccion = new Transaccion;
                                $transaccion->hacerTransaccion($pago->fresh(), new TransaccionPago);
                            }
                        } else {
                            $success = FALSE;
                        }
                    } else {
                        if ($post["campo"]["estado"] == "anulado" and $post["metodo_pago"][0]["tipo_pago"] == "aplicar_credito") {
                            $this->proveedoresRep->sumar_credito($post["campo"]["proveedor"], $post["campo"]["total_pagado"]);
                        }
                        $this->_setPagoFromPost($pago, $post); //solo cambia el estado del pago
                        $pago->save();
                    }

                    $this->_actualizarEstadoPagable($pago);
                }


                //$proveedor = Proveedores_orm::find($array_pago['proveedor_id']);
                //$this->pagoGuardar->actualizar_credito_proveedor($proveedor, $_POST["metodo_pago"]);
                //ACTUALIZAR EL ESTADO DE LA FACTURA, PLANILLA, ETC...
                //$this->_actualizarEstadoFacturaPlanilla($factura->operacion_type, $factura->operacion_id);
            });

            if ($success) {


                //$this->load->library('Events/Facturas/Facturas_compras_estados');
                //$facturaEstado = new Facturas_compras_estados;
                //$facturaEstado->manipularEstado($factura_ids);
                //$this->pagoGuardar->actualizar_estados($pago, $factura_ids);
                $mensaje = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente ');
            } else {
                $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error! El pago no puede ser aplicado</b> ');
            }

            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('pagos_contratos/listar'));
        }
    }

    function ajax_factura_info() {
        $uuid = $this->input->post('uuid');
        $facturaObj = new Buscar(new Facturas_compras_orm, 'uuid_factura');

        $factura = $facturaObj->findByUuid($uuid);
        $factura->proveedor;
        $factura->pagos = $factura->pagos_aplicados;
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($factura->toArray()))->_display();
        exit;
    }

    //Obtiene el catalogo de facturas a las cuales se les puede
    //relizar pagos
    function ajax_facturas_pagos() {
        //$vista      = $this->input->post('vista');
        $facturas = Facturas_compras_orm::deEmpresa($this->empresa_id)->paraPagos();
        $resultados = array();

        foreach ($facturas->get() as $factura) {
            $total = $factura->total;
            $pagos = (count($factura->pagos_aplicados)) ? $factura->pagos_aplicados()->sum("pag_pagos_pagables.monto_pagado") : 0;
            $saldo = $total - $pagos;

            if ($saldo > 0) {
                //echo $total."-".$pagos."=".$saldo."\n<br>";
                $resultados[] = array('uuid' => $factura->uuid_factura, 'nombre' => $factura->codigo . ' - ' . $factura->proveedor->nombre);
            }
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($resultados))->_display();
        exit;
    }

    function ajax_proveedores_pagos() {
        //$vista = $this->input->post('vista');
        $proveedores = Proveedores_orm::deEmpresa($this->empresa_id)->conFacturasParaPagos()->orderBy("nombre", "asc");
        $resultados = array();

        foreach ($proveedores->get() as $proveedor) {
            $resultados[] = array('uuid' => $proveedor->uuid_proveedor, 'nombre' => $proveedor->nombre);
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($resultados))->_display();
        exit;
    }
    
    public function ajax_subcontratos_pagos(){
        $vista = $this->input->post("vista");
        
        $clause                 = [];
        $clause["empresa_id"]   = $this->empresa_id;
        $clause["pagables"]     = ($vista == 'ver')?false:true;//con facturas por pagar o facturas pagadas parcial
        $subcontratos           = $this->subcontratosRep->listar($clause);
        
        $resultados= [];
        foreach($subcontratos as $subcontrato){
            $resultados[]= array('uuid'=>$subcontrato->uuid_subcontrato,'nombre'=>$subcontrato->numero_documento.' - '.$subcontrato->proveedor->nombre);
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($resultados))->_display();
        exit;
    }

    function ajax_facturas_proveedor() {

        $uuid = $this->input->post('uuid');
        $vista = $this->input->post('vista');


        $proveedorObj = new Buscar(new Proveedores_orm, 'uuid_proveedor');
        $proveedor = $proveedorObj->findByUuid($uuid);

        if ($vista == 'crear') {
            foreach ($proveedor->facturasCrear as $l) {
                $l->pagos = $l->pagos_aplicados;
            }
        } elseif ($vista == 'ver') {
            foreach ($proveedor->facturasNoAnuladas as $l) {//no esta aun en el modelo
                $l->pagos = $l->pagos_aplicados;
            }
        } elseif ($vista == 'registrar_pago_pago') {
            foreach ($proveedor->facturasHabilitadas as $l) {//no esta aun en el modelo
                $l->pagos = $l->pagos_aplicados;
            }
        }


        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($proveedor->toArray()))->_display();
        exit;
    }

    public function ajax_facturas_subcontrato() {

        $uuid = $this->input->post('uuid'); //uuid_subcontrato
        $vista = $this->input->post('vista');


        $proveedorObj = new Buscar(new Proveedores_orm, 'uuid_proveedor');
        $subcontrato = $this->subcontratosRep->findByUuid($uuid);
        $proveedor = $proveedorObj->findByUuid($subcontrato->proveedor->uuid_proveedor);

        $aux = $proveedor->toArray();
        if ($vista == 'crear') {
            $aux["facturas_crear"] = $proveedor->facturasCrear->filter(function($factura) use ($subcontrato) {
                $factura->pagos = $factura->pagos_aplicados;
                return $factura->operacion_id == $subcontrato->id and $factura->operacion_type == "Flexio\\Modulo\\SubContratos\\Models\\SubContrato";
            });
        } elseif ($vista == 'ver') {
            $aux["facturas_no_anuladas"] = $proveedor->facturasNoAnuladas->filter(function($factura) use ($subcontrato) {
                $factura->pagos = $factura->pagos_aplicados;
                return $factura->operacion_id == $subcontrato->id and $factura->operacion_type == "Flexio\\Modulo\\SubContratos\\Models\\SubContrato";
            });
        } elseif ($vista == 'registrar_pago_pago') {
            $aux["facturas_habilitadas"] = $proveedor->facturasHabilitadas->filter(function($factura) use ($subcontrato) {
                $factura->pagos = $factura->pagos_aplicados;
                return $factura->operacion_id == $subcontrato->id and $factura->operacion_type == "Flexio\\Modulo\\SubContratos\\Models\\SubContrato";
            });
        }

//        if($vista =='crear'){
//            $proveedor->facturasCrear->filter(function($factura) use ($subcontrato){
//                return false;
//                return $factura->operacion_id == $subcontrato->id and $factura->operacion_type == "Flexio\\Modulo\\SubContratos\\Models\\SubContrato";
//            });
//            
//            foreach($proveedor->facturasCrear as $l){
//                $l->pagos = $l->pagos_aplicados;
//            }
//        }elseif($vista =='ver'){
//            $proveedor->facturasNoAnuladas->filter(function($factura) use ($subcontrato){
//                return $factura->operacion_id == $subcontrato->id and $factura->operacion_type == "Flexio\\Modulo\\SubContratos\\Models\\SubContrato";
//            });
//            foreach($proveedor->facturasNoAnuladas as $l){//no esta aun en el modelo
//                $l->pagos = $l->pagos_aplicados;
//            }
//        }elseif($vista =='registrar_pago_pago'){
//            $proveedor->facturasHabilitadas->filter(function($factura) use ($subcontrato){
//                return $factura->operacion_id == $subcontrato->id and $factura->operacion_type == "Flexio\\Modulo\\SubContratos\\Models\\SubContrato";
//            });
//            foreach($proveedor->facturasHabilitadas as $l){//no esta aun en el modelo
//                $l->pagos = $l->pagos_aplicados;
//            }
//        }


        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($aux))->_display();
        exit;
    }

    function ajax_info_pago() {
        $uuid = $this->input->post('uuid');
        $pagoObj = new Buscar(new Pagos_contratos_orm, 'uuid_pago');
        $pago = $pagoObj->findByUuid($uuid);

        $pago->metodo_pago;
        ($pago->formulario != "planilla") ? $l = $pago->facturas : $l = $pago->planillas;
        $pago->pagos_pagables;

        foreach ($l as $row) {
            $row->pagos = $row->pagos_aplicados;
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($pago->toArray()))->_display();
        exit;
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
            'public/assets/css/modules/stylesheets/pagos.css',
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
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/plugins/jquery/sweetalert/sweetalert.min.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
        ));
    }

    function ocultoformulariocomentarios() {

        $data = array();

        $this->assets->agregar_js(array(
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/modules/pagos_contratos/vue.comentario.js',
            'public/assets/js/modules/pagos_contratos/formulario_comentario.js'
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
        $comentario = ['comentario'=>$comentario,'usuario_id'=>$this->usuarioId];
        $pagos = $this->pagosRep->agregarComentario($model_id, $comentario);
        $pagos->load('comentario_timeline');

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($pagos->comentario_timeline->toArray()))->_display();
        exit;
    }

}

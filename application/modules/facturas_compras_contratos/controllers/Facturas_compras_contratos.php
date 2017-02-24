<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 *
 * @package    Flexio
 * @subpackage Controller
 * @category   Facturas de compras
 * @author     Pensanomica Team
 * @link       http://www.pensanomica.com
 * @copyright  02/19/2016
 */
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Strategy\Transacciones\Transaccion as Transaccion;
use Flexio\Strategy\Transacciones\TransaccionFacturaCompra as TransaccionFacturaCompra;
//repositories
use Flexio\Modulo\OrdenesCompra\Repository\OrdenesCompraRepository as ordenesCompraRep;
use Flexio\Modulo\Contabilidad\Repository\ImpuestosRepository as impuestosRep;
use Flexio\Modulo\FacturasCompras\Repository\FacturaCompraRepository as facturasCompraRep;
use Flexio\Modulo\SubContratos\Repository\SubContratoRepository as subcontratosRep;
use Flexio\Modulo\Proveedores\Repository\ProveedoresRepository as proveedoresRep;
use Flexio\Modulo\SubContratos\Models\SubContrato as subcontratosVal;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\FacturasCompras\Models\FacturaCompra as FacturaCompra;
use Flexio\FormularioDocumentos AS FormularioDocumentos;

//utils
use Flexio\Library\Util\FlexioSession;
use Flexio\Library\Util\AuthUser;

class Facturas_compras_contratos extends CRM_Controller {

    protected $facturaCompraRepository;
    private $empresa_id;
    private $id_usuario;
    private $empresaObj;
    private $ordenesCompraRep;
    private $impuestosRep;
    private $facturasCompraRep;
    private $subcontratosRep;
    private $subcontratosVal;
    private $proveedoresRep;
    private $facturaCompra;
    protected $DocumentosRepository;
    protected $upload_folder = './public/uploads/';
    
    //utils
    protected $FlexioSession;

    function __construct() {
        parent::__construct();

        $this->load->model('proveedores/Proveedores_orm');

        $this->load->model('centros/Centros_orm');

        $this->load->model('pagos/Pagos_orm');

        $this->load->model('inventarios/Items_categorias_orm');
        $this->load->model('inventarios/Categorias_orm');

        $this->load->model('usuarios/Usuario_orm');

        $this->load->model('contabilidad/Impuestos_orm');

        $this->load->model('ordenes/Ordenes_orm');

        $this->load->model('facturas_compras/Facturas_compras_orm');
        $this->load->model('pagos/Pago_metodos_pago_orm');
        $this->load->model('pedidos/Pedidos_orm');
        $this->load->model('ordenes/Ordenes_orm');
        $this->load->model('facturas/Factura_catalogo_orm'); //uso el mismo catalogo de la seccion de facturas de ventas
        $this->load->module(array('documentos'));
        $this->load->module("salidas/Salidas");
        Carbon::setLocale('es');
        setlocale(LC_TIME, 'Spanish');
        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();

        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresaObj = new Buscar(new Empresa_orm, 'uuid_empresa');
        $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
        $this->id_usuario = $this->session->userdata("huuid_usuario");
        $this->empresa_id = $this->empresaObj->id;
        $this->ordenesCompraRep = new ordenesCompraRep();
        $this->impuestosRep = new impuestosRep();
        $this->facturasCompraRep = new facturasCompraRep();
        $this->subcontratosRep = new subcontratosRep();
        $this->proveedoresRep = new proveedoresRep();
        $this->subcontratosVal = new subcontratosVal();
        $this->facturaCompra = new FacturaCompra();
        
        //utils
        $this->FlexioSession = new FlexioSession;
    }

    function index() {
        redirect("facturas_compras_contratos/listar");
    }

    function listar() {

        $data = array();

        if (!$this->auth->has_permission('acceso')) {
            $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud');
            $this->session->set_flashdata('mensaje', $mensaje);
        }


        $this->_Css();
        $this->assets->agregar_css(array(
            'public/assets/css/plugins/jquery/fileinput/fileinput.css',
            'public/assets/css/plugins/jquery/jquery.fileupload.css',
        ));
        $this->_js();

        $this->assets->agregar_js(array(
            'public/assets/js/modules/facturas_compras_contratos/listar.js',
            'public/assets/js/default/toast.controller.js',
            'public/assets/js/plugins/jquery/fileupload/jquery.fileupload.js',
        ));


        $breadcrumb = array(
            "titulo" => '<i class="fa fa-shopping-cart"></i> Facturas: contratos',
            "ruta" => array(
                0 => array(
                    "nombre" => "Compras",
                    "activo" => false
                ),
                1 => array(
                    "nombre" => '<b>Facturas de compras</b>',
                    "activo" => true
                )
            ),
            "menu" => array(
                "nombre" => "Crear",
                "url" => "facturas_compras/crear",
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


        $data['proveedores'] = Proveedores_orm::deEmpresa($this->empresa_id)->orderBy("nombre", "asc")->get();
        $data['estados'] = Factura_catalogo_orm::estadosFacturasCompras()->get();
        $data['tipos'] = Factura_catalogo_orm::tiposFacturasCompras()->get();
         $data["centros"] = Centros_orm::deEmpresa($this->empresa_id)
                ->activa()
                ->deMasJuventud($this->empresa_id)
                ->orderBy("nombre", "ASC")
                ->get();

        $breadcrumb["menu"]["opciones"]["#exportarListaFacturasCompras"] = "Exportar";
        // $breadcrumb["menu"]["opciones"]["#refacturar"] = "Refacturar";
        $this->template->agregar_titulo_header('Listado de facturas de compras');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }


    public function ajax_exportar() {

        $clause = [];
        $clause["empresa_id"] = $this->empresa_id;
        $clause["uuid_facturas_compra"] = $this->input->post("uuid_facturas_compra", true);

        $csv = \League\Csv\Writer::createFromFileObject(new \SplTempFileObject());
        $csv->insertOne([utf8_decode("Número"), "Fecha", "Proveedor", "No. contrato", "Centro", "Estado", "Monto", "Saldo"]);
        $csv->insertAll($this->facturasCompraRep->getCollectionExportar($this->facturasCompraRep->get($clause)));

        $csv->output('facturas_compras_contratos.csv');
        exit;
    }

    function ajax_listar() {
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        //Paramentos de busqueda
        $fecha1 = $this->input->post("fecha1", true);
        $fecha2 = $this->input->post('fecha2', true);
        $proveedor = $this->input->post('proveedor', true);
        $estado = $this->input->post('estado', true);
        $monto1 = $this->input->post('monto1', true);
        $monto2 = $this->input->post('monto2', true);
        $centro_contable = $this->input->post('centro_contable', true);
        $tipo = $this->input->post('tipo', true);
        $caja_id = $this->input->post('caja_id', true);
        $item_id = $this->input->post('item_id', true);
        $pedidos_id = $this->input->post('pedidos_id', true);
        $numero_factura = $this->input->post('numero_factura', true);
        //$registros = Facturas_compras_orm::deEmpresa($empresa_id);
        $registros = Facturas_compras_orm::deTipo($tipo);
        $registros->deEmpresa($this->empresa_id);
        //subpanels
        $orden_compra_id = $this->input->post('orden_compra_id', true);
        //dd($registros->get()->toArray());
        if (!empty($fecha1)) {
            $fecha1 = date("Y-m-d H:i:s", strtotime($fecha1));
            $registros->deFechaCreacionMayorIgual($fecha1);
        }

        if (!empty($fecha2)) {
            $fecha2 = date("Y-m-d H:i:s", strtotime($fecha2));
            $registros->deFechaCreacionMenorIgual($fecha2);
        }

        if (!empty($orden_compra_id)) {
            $registros->deOrdenDeCompra($orden_compra_id);
        }

        if (!empty($proveedor)) {
            $registros->deProveedor($proveedor);
        }

        if (!empty($estado)) {
            $registros->deEstado($estado);
        }

        if (!empty($monto1)) {
            $registros->deMontoMayorIgual($monto1);
        }

        if (!empty($monto2)) {
            $registros->deMontoMenorIgual($monto2);
        }
        
        if (!empty($centro_contable)) {
           
            $registros->deCentroContable($centro_contable);
        }

        if (!empty($tipo)) {
            $registros->deTipo($tipo);
        }
        if (!empty($caja_id)) {
            $registros->whereHas('pagos', function($q) use($caja_id) {
                $q->with(['metodo_pago'])->whereHas('metodo_pago', function($r) use($caja_id) {
                    $r->where('tipo_pago', '=', 'caja_chica')
                            ->where(Capsule::raw('CONVERT(referencia USING utf8)'), "like", "%\"caja_id\":\"$caja_id\"%");
                    ;
                });
            });
        }
        if (!empty($item_id)) {
            $registros->deItem($item_id);
        }

        if (!empty($pedidos_id)) {
            $registros = $registros->dePedidos($pedidos_id);
        }
        if (!empty($numero_factura)) {
            $registros = $registros->deFacturaProveedor($numero_factura);
        }
        
        //filtros de centros contables del usuario
        $centros = $this->FlexioSession->usuarioCentrosContables();
        if(!in_array('todos', $centros))
        {
            $registros->whereIn("faccom_facturas.centro_contable_id", $centros);
        }

        // die();
        $count = $registros->count();

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

        if ($sidx != NULL && $sord != NULL) {
            $registros->orderBy($sidx, $sord);
        }
        if ($limit != NULL) {
            $registros->skip($start)->take($limit);
        }

        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
        // dd($registros->get()->operacion);


$retenido = $registros->get()->toArray();
        if ($count > 0)
         {
           $j = 0;

            foreach ($registros->get() as $i => $row)
             {


               $item_factura = $row['items_factura']->toArray();
               $monto_retenido = $item_factura[0]['retenido'];
               $total = $row->total - $monto_retenido;

                $hidden_options = "";
                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row->uuid_factura . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $hidden_options = '<a href="' . base_url('facturas_compras/ver/' . $row->uuid_factura) . '" data-id="' . $row->uuid_factura . '" class="btn btn-block btn-outline btn-success">Ver detalle</a>';

                if ($row->pagable and $this->auth->has_permission('acceso', 'pagos/crear/(:any)')) {
                    $hidden_options .= '<a href="' . base_url('pagos/crear/facturacompra' . $row->uuid_factura) . '" class="btn btn-block btn-outline btn-success">Pagar</a>';
                }
                 //$hidden_options .= '<a href="#" class="btn btn-block btn-outline btn-success subirArchivoBtn" data-id="'. $row->uuid_factura .'" data-uuid="'. $row->uuid_factura .'" >Subir archivo</a>';
                // $hidden_options .= '<a href="javascript:" data-id="' . $row->uuid_factura . '" class="TablaCliente btn btn-block btn-outline btn-success subirArchivoBtn">Subir Documento</a>';
                 $hidden_options .= '<a href="'.base_url('documentos/subir_documento/'. $row->uuid_factura).'" class="btn btn-block btn-outline btn-success">Subir documento</a>';
                 $hidden_options .= '<a  href="'.base_url('facturas_compras/historial/'. $row->uuid_factura).'"   data-id="'.$row->id.'" class="btn btn-block btn-outline btn-success">Ver bit&aacute;cora</a>';
                 $numero_documento = '';
                if(!empty($row->operacion_type) && $row->operacion_type != 'Flexio\\Modulo\\SubContratos\\Models\\SubContrato' ){
                  $numero_documento = $row->operacion->numero_documento;
                }
                $response->rows[$i]["id"] = $row->uuid_factura;
                $response->rows[$i]["cell"] = array(
                    '<a class="link" href="' . base_url('facturas_compras/ver/' . $row->uuid_factura) . '" style="color:blue;">' . $row->codigo . '</a>',
                    $row->created_at,
                    count($row->proveedor) ? '<a class="link" href="' . base_url("proveedores/ver/" . $row->proveedor->uuid_proveedor) . '" style="color:blue;">' . $row->proveedor->nombre . '</a>' : '',
                    $numero_documento,//!empty($row->operacion_type) ? $row->operacion->numero_documento:'',
                    '<label class="totales-success">$' . $row->total . '</label>', //total de la factura
                    '<label class="totales-danger">$' . number_format($row->saldo, 4) . '</label>', //total de la factura
                    count($row->centro_contable) ? $row->centro_contable->nombre : '',
                    count($row->estado) ? $row->estado->valorSpan() : '',
                    $link_option,
                    $hidden_options
                );
                $j++;
            }
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($response))->_display();

        exit;
    }

    public function ajax_listar_de_item() {
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        $clause = $this->input->post();
        $clause["empresa_id"] = $this->empresa_id;

        $count = $this->facturasCompraRep->count($clause);

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

        $facturas = $this->facturasCompraRep->get($clause, $sidx, $sord, $limit);

        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;

        if ($count > 0) {

            foreach ($facturas as $i => $row) {

                $response->rows[$i]["id"] = $row->uuid_factura;
                $response->rows[$i]["cell"] = $this->facturasCompraRep->getCollectionCellDeItem($row, $clause["item_id"]);
            }
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($response))->_display();

        exit;
    }

    function ocultotabla($modulo_id = NULL) {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/facturas_compras_contratos/tabla.js'
        ));

        //Verificar desde donde se esta llamando
        //la tabla de facturas
        if (preg_match("/cajas/i", $this->router->fetch_class())) {
            if (!empty($modulo_id)) {
                $this->assets->agregar_var_js(array(
                    "caja_id" => $modulo_id
                ));
            }
        } elseif (preg_match("/inventarios/i", $this->router->fetch_class()) and ! empty($modulo_id)) {
            $this->assets->agregar_var_js(array(
                "item_id" => $modulo_id
            ));
        } elseif (preg_match("/pedidos/i", $this->router->fetch_class()) and ! empty($modulo_id)) {
            $this->assets->agregar_var_js(array(
                "pedidos_id" => $modulo_id
            ));
        } else {
            if (!empty($modulo_id)) {
                $this->assets->agregar_var_js(array(
                    "cliente_id" => $modulo_id
                ));
            }
        }


        $this->load->view('tabla');
    }

    public function ocultotablaProveedores($modulo_id = NULL) {

        $this->assets->agregar_js(array(
            'public/assets/js/modules/facturas_compras_contratos/tabla.js'
        ));

        if (!empty($modulo_id)) {
            $this->assets->agregar_var_js(array(
                "proveedor_id" => $modulo_id
            ));
        }

        $this->load->view('tabla');
    }

    public function ocultotablaOrdenesCompras($modulo_id = NULL) {

        $this->assets->agregar_js(array(
            'public/assets/js/modules/facturas_compras_contratos/tabla.js'
        ));

        if (!empty($modulo_id)) {
            $this->assets->agregar_var_js(array(
                "orden_compra_id" => $modulo_id
            ));
        }

        $this->load->view('tabla');
    }

    function ocultotabla_de_item($modulo_id = NULL) {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/facturas_compras_contratos/tabla_de_item.js'
        ));

        //Verificar desde donde se esta llamando
        //la tabla de facturas
        if (preg_match("/inventarios/i", $this->router->fetch_class()) and ! empty($modulo_id)) {
            $this->assets->agregar_var_js(array(
                "item_id" => $modulo_id
            ));
        }

        $this->load->view('tabla');
    }

    public function crear($foreing_key = '') {

        if (preg_match('/proveedor/', $foreing_key)) {
            $proveedor_id = str_replace('proveedor', '', $foreing_key);
        } elseif (preg_match('/ordencompra/', $foreing_key)) {
            $orden_compra_id = str_replace('ordencompra', '', $foreing_key);
        }

        $acceso = 1;
        $mensaje = $clause = [];

        $clause["empresa_id"] = $this->empresa_id;
        $clause["facturables"] = true;

        if (!$this->auth->has_permission('acceso')) {
            $acceso = 0;
            $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud', 'clase' => 'alert-danger');
            $this->session->set_flashdata('mensaje', $mensaje);
        }

        $this->_Css();
        $this->assets->agregar_css(array(
            'public/assets/css/modules/stylesheets/animacion.css'
        ));
        $this->_js();
        $this->assets->agregar_js(array(
            'public/assets/js/modules/facturas_compras_contratos/services.facturas.js',
            'public/assets/js/modules/facturas_compras_contratos/crear.controller.js',
        ));


        $data = array();

        $this->assets->agregar_var_js(array(
            "vista" => 'crear',
            "acceso" => $acceso == 0 ? $acceso : $acceso,
            "proveedor_id" => (isset($proveedor_id) and ! empty($proveedor_id)) ? $proveedor_id : '',
            "orden_compra_id" => (isset($orden_compra_id) and ! empty($orden_compra_id)) ? $orden_compra_id : ''
        ));

        $data["info"]['factura_id'] = '';
        //ordenes de compras y contratos que pueden ser facturables -> requieren el metodo "numero_documento"
        $data["operaciones"] = $this->facturasCompraRep->getOperaciones($clause);

        $data['mensaje'] = $mensaje;

        $breadcrumb = array(
            "titulo" => '<i class="fa fa-shopping-cart"></i> Factura de compra: Crear',
        );

        $this->template->agregar_titulo_header('Crear Factura');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    function ver($uuid = null) {
        $acceso = 1;
        $mensaje = array();

        if (!$this->auth->has_permission('acceso', 'facturas_compras_contratos/ver/(:any)')) {
            $acceso = 0;
            $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud', 'clase' => 'alert-danger');
        }

        $this->_Css();
        $this->assets->agregar_css(array(
            'public/assets/css/modules/stylesheets/animacion.css'
        ));
        $this->_js();
        $this->assets->agregar_js(array(
            'public/assets/js/modules/facturas_compras_contratos/services.facturas.js',
            'public/assets/js/modules/facturas_compras_contratos/crear.controller.js',
        ));

        $facturaObj = new Buscar(new Facturas_compras_orm, 'uuid_factura');
        $factura = $facturaObj->findByUuid($uuid);
        $label = '';
        $data = array();


        $this->assets->agregar_var_js(array(
            "vista" => 'editar',
            "acceso" => $acceso == 0 ? $acceso : $acceso,
            "uuid_factura" => $factura->uuid_factura,
            "proveedor_id" => '',
            "orden_compra_id" => ''
        ));

        $clause["empresa_id"] = $this->empresa_id;
        $clause["facturables"] = false;

        $data["info"]['factura_id'] = $factura->id;
        $data['factura_compra_id'] = $factura->id;
        $data["operaciones"] = $this->facturasCompraRep->getOperaciones($clause);
        $data['mensaje'] = $mensaje;

        $breadcrumb = array(
            "titulo" => '<i class="fa fa-shopping-cart"></i> Factura de contrato: Editar ' . $label,
        );
        $breadcrumb["menu"]["opciones"]["facturas_compras_contratos/historial/" . $factura->uuid_factura] = "Ver bit&aacute;cora";
        $this->template->agregar_titulo_header('Editar Factura de Contrato');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    function ocultoformulario($facturas = array()) {

        $data = array();

        $clause_impuesto = array('empresa_id' => $this->empresa_id, 'estado' => 'Activo');

        $data['terminos_pagos'] = Factura_catalogo_orm::where('tipo', 'termino_pago')->get(array('id', 'etiqueta', 'valor'));
        $data['etapas'] = Factura_catalogo_orm::estadosFacturasCompras()->get(array('etiqueta', 'valor'));
        $data['compradores'] = Usuario_orm::deEmpresa($this->empresa_id)->orderBy("nombre", "asc")->get();
        $data['unidades'] = array();
        $data['impuestos'] = Impuestos_orm::where($clause_impuesto)->get(array('id', 'uuid_impuesto', 'nombre', 'impuesto'));
        $data['cuenta_gasto'] = Cuentas_orm::transaccionalesDeEmpresa($this->empresa_id)->deTipoDeCuenta([5])->activas()->get();
        $data['proveedores'] = Proveedores_orm::deEmpresa($this->empresa_id)->orderBy("nombre", "asc")->get(array('id', 'nombre'));
        $data['bodegas'] = Bodegas_orm::deEmpresa($this->empresa_id)->activas()->transaccionales($this->empresa_id)->orderBy("nombre", "asc")->get(array('id', 'nombre'));
        $data["categorias"] = Categorias_orm::deEmpresa($this->empresa_id)->conItems()->get();
        $data['centros_contables'] = Centros_orm::deEmpresa($this->empresa_id)
                ->activa()
                ->deMasJuventud($this->empresa_id)
                ->orderBy("nombre", "ASC")
                ->get();

        if (isset($facturas['info']))
            $data['info'] = $facturas['info'];

        $this->load->view('formulario', $data);
    }

    private function _createFactura($factura, $post) {
        $total = FacturaCompra::deEmpresa($this->empresa_id)->count();

        $factura->codigo = Util::generar_codigo('FT', ($total + 1));
        $factura->empresa_id = $this->empresa_id;
        $factura->operacion_type = ($post["tipo"] == "Subcontratos") ? 'Flexio\\Modulo\\SubContratos\\Models\\SubContrato' : $post["tipo"];
        $factura->operacion_id = $post["uuid_tipo"]; //es el id, no tomar en cuenta la llave
    }

    private function _setFacturaFromPost($factura, $post) {
        $campo = $post['campo'];

        $factura->referencia = ""; //no esta en el disenio del formulario
        $factura->centro_contable_id = $campo["centro_contable_id"];
        $factura->factura_proveedor = $campo["factura_proveedor"];
        $factura->bodega_id = $campo["bodega_id"];
        $factura->proveedor_id = $campo["proveedor_id"];
        $factura->created_by = $campo["creado_por"];
        $factura->estado_id = Factura_catalogo_orm::estadosFacturasCompras()->deEtiqueta($campo["estado"])->first()->id; //es de tipo string
        $factura->fecha_desde = date("Y-m-d", strtotime($campo["fecha_desde"]));
        $factura->comentario = $campo["comentario"];
        $factura->termino_pago = $campo["termino_pago"];
        $factura->fecha_termino_pago = ""; //no esta en el disenio del formulario
        $factura->subtotal = $campo["subtotal"];
        $factura->descuentos = $campo["descuento"];
        $factura->impuestos = $campo["impuestos"];
        $factura->total = $campo["total"];
    }

    private function _getFacturasItemsFromPost($post) {
        $aux = [];
        $facturas_items = $post["items"];

        foreach ($facturas_items as $row) {
            $aux[$row["item_id"]] = [
                "categoria_id" => $row["categoria_id"],
                "cantidad" => $row["cantidad"],
                "unidad_id" => $row["unidad_id"],
                "precio_unidad" => $row["precio_unidad"],
                "impuesto_id" => is_numeric($row["impuesto_id"]) ? $row["impuesto_id"] : $this->impuestosRep->findByUuid($row["impuesto_id"])->id,
                "descuento" => $row["descuento"],
                "cuenta_id" => $row["cuenta_id"],
                "total" => $row["total"],
                "subtotal" => $row["subtotal"],
                "descuentos" => $row["descuentos"],
                "impuestos" => $row["impuestos"],
                "empresa_id" => $this->empresa_id
            ];
        }

        return $aux;
    }

    private function _actualizarEstadoOrdenContrato($operacion_type, $operacion_id) {
        if ($operacion_type == "Ordenes_orm" && $operacion_id) {
            $registro = Ordenes_orm::find($operacion_id);
            $registro->actualizarEstado();
        } elseif ($operacion_type == "Contratos_orm") {
            //...logica para contratos
        }
    }

    private function _factura_contrato_valida($factura) {
        $subcontrato = $this->subcontratosRep->findBy($factura->operacion_id);

        if (round($subcontrato->por_facturar(), 2) >= round($factura->total, 2)) {
            return true;
        }
        return false;
    }

    function guardar() {

        if ($_POST) {
//            echo "<pre>";
//            print_r($_POST);
//            echo "<pre>";
//            die();
            $success = FALSE;
            $campo = $this->input->post("campo");
            $post = $this->input->post();
            Capsule::transaction(function() use ($campo, $post, &$success) {

                if (!$campo["factura_id"]) {
                   // $factura = new Facturas_compras_orm;
                    $factura = new FacturaCompra();
                    $this->_createFactura($factura, $post);
                } else {

                   // $factura = Facturas_compras_orm::find($campo["factura_id"]);
                    $factura = FacturaCompra::find($campo["factura_id"]);
                }
                $this->_setFacturaFromPost($factura, $post);

                if ($factura->estado->etiqueta == 'por_pagar') {

                    if ($factura->operacion_type != "Flexio\\Modulo\\SubContratos\\Models\\SubContrato" || $this->_factura_contrato_valida($factura)) {
                        $transaccion = new Transaccion;
                        $transaccion->hacerTransaccion($factura->fresh(), new TransaccionFacturaCompra);
                        $success = TRUE;
                    } else {
                        $factura->estado_id = '13'; //"por_aprobar";
                        $success = FALSE;
                    }
                } else {
                    $success = TRUE;
                }

                $factura->save();

                $factura->items()->sync($this->_getFacturasItemsFromPost($post));

                //ACTUALIZAR EL ESTADO DE LA ORDEN
                $this->_actualizarEstadoOrdenContrato($factura->operacion_type, $factura->operacion_id);
            });

            if ($success) {
                $mensaje = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente ');
            } else {
                $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error! Su solicitud no fue procesada</b> ');
            }

            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('facturas_compras_contratos/listar'));
        }
    }

    function ajax_factura_info() {
        $uuid = $this->input->post('uuid');
        $facturaObj = new Buscar(new Factura_orm, 'uuid_factura');
        $factura = $facturaObj->findByUuid($uuid);
        $factura->cliente;
        $factura->cotizacion;
        $factura->orden_venta;
        $lista = $factura->items_factura;
        foreach ($lista as $l) {
            $l->impuesto;
            $l->cuenta;
            $l->articulo;
        }


        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($factura->toArray()))->_display();
        exit;
    }

    private function _item($item) {
        return array(
            "id" => $item->id,
            "nombre" => $item->comp_nombre(),
            "unidades" => $item->unidades->toArray(),
            "unidad_id" => $item->unidadBaseModel()->id, //unidad base
            "impuesto_id" => $item->impuestoCompra->id, //impuesto para compra
            "cuenta_id" => $item->cuentaGasto->id //cuenta de gasto
        );
    }

    function ajax_get_items() {

        $registros = array();
        $categoriasConItems = Categorias_orm::deEmpresa($this->empresa_id)->conItems();

        foreach ($categoriasConItems->get() as $i => $row) {
            $itemsDeCategoria = Items_orm::deEmpresa($this->empresa_id)->deCategoria($row->id);
            $registros[$i]["categoria_id"] = $row->id;

            foreach ($itemsDeCategoria->get() as $j => $rowJ) {
                $registros[$i]["items"][$j] = $this->_item($rowJ);
            }
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($registros))->_display();

        exit;
    }

    private function _getImpuesto($impuesto_id) {
        $aux = new stdClass();
        $aux->id = '';
        $aux->uuid_impuesto = '';
        $aux->impuesto = 0;

        return !empty($impuesto_id) ? $this->impuestosRep->find($impuesto_id) : $aux;
    }

    private function _getEmpezarDesde($registro) {
        return [
            "id" => $registro->id,
            "termino_pago" => !empty($registro->termino_pago) ? $registro->termino_pago : '',
            "proveedor" => array(
                "id" => (string) $registro->proveedor->id,
                "nombre" => $registro->proveedor->nombre,
                "saldo_pendiente" => $registro->proveedor->saldo_pendiente,
                "credito_favor" => $registro->proveedor->credito
            ),
            "bodega" => array(
                "id" => isset($registro->bodega->id) ? (string) $registro->bodega->id : '',
                "nombre" => isset($registro->bodega->id) ? $registro->bodega->nombreCompleto() : ''
            ),
            "comprador" => array(
                "id" => (isset($registro->comprador->id)) ? (string) $registro->comprador->id : ''
            ),
            "centro_contable" => array(
                "id" => (string) $registro->centro_contable->id,
                "nombre" => $registro->centro_contable->nombre
            )
        ];
    }

    private function _getEmpezarDesdeItems($registro, $tipo) {
        $aux = [];

        $lista = ($tipo == "Ordenes_orm") ? $registro->items : [];
        foreach ($lista as $item) {
            $impuesto = $this->_getImpuesto($item->pivot->impuesto_id);
            $subtotal = $item->pivot->cantidad * $item->pivot->precio_unidad;
            $descuentos = ($subtotal * $item->pivot->descuento) / 100;
            $subtotal -= $descuentos;
            $impuestos = ($subtotal * $impuesto->impuesto) / 100;

            $aux[] = array(
                "categoria_id" => (string) $item->pivot->categoria_id,
                "item_id" => (string) $item->id,
                "descripcion" => $item->descripcion,
                "cantidad" => $item->pivot->cantidad,
                "unidad_id" => (string) $item->pivot->unidad_id,
                "precio_unidad" => $item->pivot->precio_unidad,
                "impuesto_id" => $impuesto->uuid_impuesto,
                "descuento" => $item->pivot->descuento,
                "cuenta" => (string) $item->pivot->cuenta_id,
                //totalizadores de la fila
                "total" => $subtotal, //no se incluyen impuestos en el total de la fila
                "subtotal" => $subtotal,
                "descuentos" => $descuentos,
                "impuestos" => $impuestos
            );
        }

        return $aux;
    }

    private function _getFactura($registro) {
        return [
            "id" => $registro->id,
            "termino_pago" => $registro->termino_pago,
            "fecha_desde" => $registro->fecha_desde,
            "factura_proveedor" => $registro->factura_proveedor,
            "comentario" => $registro->comentario,
            "estado" => $registro->estado->etiqueta,
            "operacion_type" => $registro->operacion_type,
            "operacion_id" => $registro->operacion_id,
            "proveedor" => array(
                "id" => (string) $registro->proveedor->id,
                "nombre" => $registro->proveedor->nombre,
                "saldo_pendiente" => (string) ($registro->proveedor->total_saldo_pendiente()) ? : "0.00",
                "credito_favor" => $registro->proveedor->credito
            ),
            "bodega" => array(
                "id" => (string) $registro->bodega->id,
                "nombre" => $registro->bodega->nombreCompleto()
            ),
            "comprador" => array(
                "id" => (string) $registro->comprador->id,
                "nombre" => $registro->comprador->nombreCompleto()
            ),
            "centro_contable" => array(
                "id" => (string) $registro->centro_contable->id,
                "nombre" => $registro->centro_contable->nombre
            )
        ];
    }

    private function _getFacturaItems($registro) {
        $aux = [];
        $lista = $registro->facturas_compras_items;
        foreach ($lista as $l) {
            $impuesto = $this->_getImpuesto($l->impuesto_id);
            $subtotal = $l->cantidad * $l->precio_unidad;
            $descuentos = ($subtotal * $l->descuento) / 100;
            $subtotal -= $descuentos;
            $impuestos = ($subtotal * $impuesto->impuesto) / 100;

            $aux[] = array(
                "categoria_id" => (string) $l->categoria_id,
                "item_id" => (string) $l->item->id,
                "descripcion" => $l->item->descripcion,
                "cantidad" => $l->cantidad,
                "unidad_id" => (string) $l->unidad_id,
                "precio_unidad" => $l->precio_unidad,
                "impuesto_id" => $impuesto->uuid_impuesto,
                "descuento" => $l->descuento,
                "cuenta" => (string) $l->cuentaDeGasto->id,
                //totalizadores de la fila
                "total" => $subtotal, //no se incluyen impuestos en el total de la fila
                "subtotal" => $subtotal,
                "descuentos" => $descuentos,
                "impuestos" => $impuestos,
                "factura_item_id" => (string) $l->id,
            );
        }

        return $aux;
    }

    function ajax_get_empezar_desde() {
        $id = $this->input->post('uuid'); //se recibe el id (int:10)
        $tipo = $this->input->post('tipo');

        if ($tipo == "Ordenes_orm") {
            $registro = $this->ordenesCompraRep->find($id);
        } elseif ($tipo == "Subcontratos") {
            $registro = $this->subcontratosRep->findBy($id);
        }

        $aux = $this->_getEmpezarDesde($registro);
        $aux["items"] = $this->_getEmpezarDesdeItems($registro, $tipo);


        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($aux))->_display();

        exit;
    }

    function ajax_get_factura() {
        $factura_id = $this->input->post('factura_id');
        $registro = Facturas_compras_orm::find($factura_id);

        $aux = $this->_getFactura($registro);
        $aux["items"] = $this->_getFacturaItems($registro);


        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($aux))->_display();

        exit;
    }

    function ajax_getFacturadoCompleto() {
        $clause = ['empresa_id' => $this->empresa_id];
        $factura = $this->facturasCompraRep->cobradoCompletoSinNotaDebito($clause);
        $factura->load('proveedor', 'items', 'items.inventario_item', 'items.inventario_item.unidades', 'items.impuesto');

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($factura->toArray()))->_display();
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
            'public/assets/css/modules/stylesheets/facturas_compras.css',
        ));
    }

    private function _js() {
        $this->assets->agregar_js(array(
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/plugins/jquery/jqgrid/plugins/jQuery.jqGrid.columnToggle.js',
            'public/assets/js/plugins/jquery/switchery.min.js',
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
    function ajax_guardar_documentos() {
        if(empty($_POST)){
            return false;
        }

        $factura_id = $this->input->post('factura_id', true);
        $facturaObj = new Buscar(new Facturas_compras_orm, 'uuid_factura');
        $modeloInstancia = $facturaObj->findByUuid($factura_id);
        //$modeloInstancia = $this->facturaCompraRepository->findByUuid($factura_id);
        $this->documentos->subir($modeloInstancia);
    }
    function documentos_campos() {

        return array(
            array(
                "type"		=> "hidden",
                "name" 		=> "factura_id",
                "id" 		=> "factura_id",
                "class"		=> "form-control",
                "readonly"	=> "readonly",
            ));
    }
    function historial($uuid = NULL){

        $acceso = 1;
        $mensaje =  array();
        $data = array();

        // $factura = Facturas_compras_orm::findByUuid($uuid);
        $facturaObj = new Buscar(new Facturas_compras_orm, 'uuid_factura');
        $factura = $facturaObj->findByUuid($uuid);

        if(!$this->auth->has_permission('acceso','facturas_compras/historial') && is_null($factura)) {
            // No, tiene permiso
            $acceso = 0;
            $mensaje = array('estado' => 500, 'mensaje' => ' <b>Usted no cuenta con permiso para esta solicitud</b>', 'clase' => 'alert-danger');
        }
        $this->_Css();
        $this->_js();
        $this->assets->agregar_js(array(
            'public/resources/compile/modulos/facturas_compras/historial.js'
        ));
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-shopping-cart"></i> Bit&aacute;cora: Factura de contrato '.$factura->codigo,
        );

        $factura->load('historial');

        $historial = $factura->historial->map(function($factHist) use ($factura){
            return [
                'titulo' => $factHist->titulo,
                "codigo" =>$factura->codigo,
                "descripcion" =>$factHist->descripcion,
                "antes" => $factHist->antes,
                "despues" => $factHist->despues,
                "tipo" => $factHist->tipo,
                "nombre_usuario" => $factHist->nombre_usuario,
                "hace_tiempo" => $factHist->cuanto_tiempo,
                "fecha_creacion" => $factHist->fecha_creacion,
                "hora" => $factHist->hora
            ];
        });
        $this->assets->agregar_var_js(array(
            'historial' => $historial
        ));
        $this->template->agregar_titulo_header('Facturas de contrato');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();

    }

}

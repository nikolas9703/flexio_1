<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
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
use Dompdf\Dompdf;
use Flexio\Strategy\Transacciones\Transaccion as Transaccion;
use Flexio\Modulo\Cotizaciones\Repository\LineItemRepository as LineItemRepository;
use Flexio\Modulo\Cotizaciones\Models\LineItem;
use Flexio\Strategy\Transacciones\TransaccionFactura as TransaccionFactura;
use Flexio\Modulo\Contratos\Repository\ContratoRepository as ContratoRepository;
use Flexio\Modulo\FacturasSeguros\Repository\FacturaSeguroCatalogoRepository as FacturaSeguroCatalogoRepository;
use Flexio\Modulo\FacturasSeguros\Repository\FacturaSeguroRepository as FacturaSeguroRepository;
use Flexio\Modulo\FacturasCompras\Repository\FacturaCompraRepository;
use Flexio\Modulo\OrdenesVentas\Repository\OrdenVentaRepository as OrdenVentaRepository;
use Flexio\Modulo\OrdenesTrabajo\Repository\OrdenesTrabajoRepository;
use Flexio\Modulo\ContratosAlquiler\Repository\ContratosAlquilerRepository;
use League\Csv\Writer as Writer;
use Flexio\Modulo\Contabilidad\Models\Impuestos as Impuesto;
//eventos
use Flexio\Modulo\FacturasSeguros\Events\OrdenVentaFacturableEvent as OrdenVentaFacturableEvent;
use Flexio\Modulo\FacturasSeguros\Listeners\CrearOrdenFacturableListener as CrearOrdenFacturableListener;
use Flexio\Modulo\Contratos\Events\ContratoFacturableEvent as ContratoFacturableEvent;
use Flexio\Modulo\Contratos\Listeners\CrearContratoFacturableListener as CrearContratoFacturableListener;

use Flexio\Modulo\ContratosAlquiler\Events\ContratoAlquilerFacturableEvent;
use Flexio\Modulo\ContratosAlquiler\Listeners\CrearContratoAlquilerFacturableListener;

use Flexio\Modulo\Refactura\Events\FacturableEvent as FacturableEvent;
use Flexio\Modulo\Refactura\Listeners\FacturableListener as FacturableListener;
use Flexio\FormularioDocumentos AS FormularioDocumentos;
use Flexio\Modulo\FacturasSeguros\Models\FacturaSeguro as FacturaSeguro;
use Flexio\Modulo\Polizas\Models\Polizas as Polizas;
use Flexio\Modulo\Polizas\Models\PolizasParticipacion as PolizasParticipacion;
use Flexio\Modulo\Cliente\Repository\ClienteRepository;
use Flexio\Modulo\Inventarios\Repository\CategoriasRepository as ItemsCategoriasRepository;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\Contabilidad\Repository\ImpuestosRepository;
use Flexio\Modulo\Contabilidad\Repository\CuentasRepository;
use Flexio\Modulo\SegInteresesAsegurados\Repository\SegInteresesAseguradosRepository as SegInteresesAseguradosRepository;
use Flexio\Modulo\CentroFacturable\Models\CentroFacturable as CentroFacturable;
use Flexio\Modulo\Contabilidad\Models\CuentasCobrar;


class Facturas_seguros extends CRM_Controller
{
    private $empresa_id;
    private $id_usuario;
    private $empresaObj;
    protected $contratoRepository;
    protected $ordenVentaRepository;
    protected $facturaVentaRepository;
    protected $lineItemRepository;
    protected $facturaVentaCatalogoRepository;
    protected $ClienteRepository;
    protected $ItemsCategoriasRepository;
    protected $disparador;
    protected $OrdenesTrabajoRepository;
    protected $FacturaCompraRepository;
    protected $ImpuestosRepository;
    protected $CuentasRepository;
    protected $SegInteresesAseguradosRepository;

    function __construct() {
        parent::__construct();
        $this->load->model('usuarios/Usuario_orm');
        $this->load->model('usuarios/Empresa_orm');
        $this->load->model('usuarios/Roles_usuarios_orm');
        $this->load->model('roles/Rol_orm');
        $this->load->model('clientes/Cliente_orm');
        $this->load->module('inventarios/Inventarios');
        $this->load->model('contabilidad/Impuestos_orm');
        $this->load->model('contabilidad/Cuentas_orm');
        $this->load->model('contabilidad/Centros_orm');
        $this->load->model('bodegas/Bodegas_orm');
        $this->load->model('cobros/Cobro_orm');
        $this->load->model('inventarios/Precios_orm');
        $this->load->model('inventarios/Items_precios_orm');
        $this->load->model('facturas_compras/Facturas_compras_orm');
        $this->load->module("salidas/Salidas");
        //HMVC Load Modules
        $this->load->module('documentos');
        Carbon::setLocale('es');
        setlocale(LC_TIME, 'Spanish');
        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresaObj = new Buscar(new Empresa_orm, 'uuid_empresa');
        $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
        $this->id_usuario = $this->session->userdata("huuid_usuario");
        $this->empresa_id = $this->empresaObj->id;
        $this->contratoRepository = new ContratoRepository;
        $this->ordenVentaRepository = new OrdenVentaRepository;
        $this->facturaVentaRepository = new FacturaSeguroRepository;
        $this->lineItemRepository = new LineItemRepository;
        $this->facturaVentaCatalogoRepository = new FacturaSeguroCatalogoRepository;
        $this->ClienteRepository = new ClienteRepository();
        $this->ItemsCategoriasRepository = new ItemsCategoriasRepository();
        $this->OrdenesTrabajoRepository = new OrdenesTrabajoRepository();
        $this->ContratosAlquilerRepository = new ContratosAlquilerRepository();
        $this->FacturaCompraRepository = new FacturaCompraRepository();
        $this->ImpuestosRepository = new ImpuestosRepository();
        $this->CuentasRepository = new CuentasRepository();
        $this->SegInteresesAseguradosRepository = new SegInteresesAseguradosRepository();
    }

    function listar() {

        $data = array();
        if (!$this->auth->has_permission('acceso', 'facturas_seguros/listar') == true) {
            $acceso = 0;
            $mensaje = array('tipo' => "error", 'mensaje' => '<b>¡Error!</b> No tiene permisos para ingresar a facturas', 'titulo' => 'Facturas de seguros ');
            $this->session->set_flashdata('mensaje', $mensaje);

            redirect(base_url(''));
        }

        if ($this->auth->has_permission('cambiarEstado', 'facturas_seguros/listar') == true) {
            $cambiarEstado = 1;
        } else{
            $cambiarEstado = 0;
        }

        $this->assets->agregar_var_js(array(
            "permiso_estado" => $cambiarEstado
        ));

        //Definir mensaje
        if (!is_null($this->session->flashdata('mensaje'))) {
            $mensaje = $this->session->flashdata('mensaje');
        } else {
            $mensaje = [];
        }
        $this->assets->agregar_var_js(array(
            "flexio_mensaje" => collect($mensaje)
        ));

        $this->_Css();
        $this->_js();
        $this->assets->agregar_js(array(
            'public/assets/js/modules/facturas_seguros/listar.js',
            'public/assets/js/default/toast.controller.js'
        ));
        $breadcrumb = array("titulo" => '<i class="fa fa-line-chart"></i> Facturas',
            "ruta" => array(
                0 => array(
                    "nombre" => "Seguros",
                    "activo" => false
                ),
                1 => array(
                    "nombre" => '<b>Facturas</b>',
                    "activo" => true
                )
            ),
            "menu" => array(
                "nombre" => "Acciones",
                "url" => "",
                "opciones" => array()
            )
        );
        
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

        $polizafactura = FacturaSeguro::where("empresa_id", $this->empresa_id)->groupBy("id_poliza")->select("id_poliza")->get();
        $ramopoliza = array();
        foreach ($polizafactura as $value) {
            if (!in_array($value->polizas['ramo'], $ramopoliza)) {
                array_push($ramopoliza, $value->polizas['ramo']);
            }            
        }
        $sitio_pago = $this->SegInteresesAseguradosRepository->listar_catalogo('sitio_pago', 'orden');
        

        $data['sitio_pago'] = $sitio_pago;
        $data['ramo'] = $ramopoliza;
        $data['clientes'] = Cliente_orm::where($clause)->where("estado", "activo")->get(array('id', 'nombre'));
        $data['etapas'] = $this->facturaVentaCatalogoRepository->getEtapas();
        $data['vendedores'] = $vendedores;
        //$breadcrumb["menu"]["opciones"]["#cambiarEstadoFacturas"] = "Cambiar estados";
        $breadcrumb["menu"]["opciones"]["#exportarListaFacturas"] = "Exportar";
        $this->template->agregar_titulo_header('Listado de Facturas de Seguros');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);

    }

function ajax_listar() {
    if (!$this->input->is_ajax_request()) {
        return false;
    }
        /*
         paramentos de busqueda aqui
        */
         $uuid_cliente = $this->input->post("cliente_id");
         $cliente = $this->input->post('cliente', TRUE);
         $no_factura = $this->input->post('no_factura', TRUE);
         $hasta = $this->input->post('hasta', TRUE);
         $desde = $this->input->post('desde', TRUE);
         $estado = $this->input->post('etapa', TRUE);
         $vendedor = $this->input->post('vendedor', TRUE);
         $orden_alquiler_id = $this->input->post('orden_alquiler_id', true);
         $ms_selected = $this->input->post('ms_selected', true);


         $clause = array('empresa_id' => $this->empresaObj->id);
         $clause['contrato_alquiler_id'] = $this->input->post('contrato_alquiler_id', TRUE);
         $clause['contrato_id'] = $this->input->post('contrato_id', TRUE);


         if (!empty($uuid_cliente)) {
            $clienteObj = new Buscar(new Cliente_orm, 'uuid_cliente');
            $cliente = $clienteObj->findByUuid($uuid_cliente);
            $clause['cliente_id'] = $cliente->id;
        } elseif (!empty($cliente)) {
            $clause['cliente_id'] = $cliente;
        }

        if (!empty($desde)) {
            $fecha_inicio = Carbon::createFromFormat('d/m/Y', $desde, 'America/Panama')->format('Y-m-d 00:00:00');
            $clause["fecha_desde"] = array('>=', $fecha_inicio);
        }
        if (!empty($hasta)) {
            $fecha_fin = Carbon::createFromFormat('d/m/Y', $hasta, 'America/Panama')->format('Y-m-d 23:59:59');
            $clause["fecha_desde@"] = array('<=', $fecha_fin);
        }
        if (!empty($no_factura)) {

            $clause['codigo'] = array('LIKE', "%$no_factura%");

        }
        if (!empty($estado)){
            $clause['estado'] = $estado;
        }
        if (!empty($ms_selected) && preg_match("/alquiler/i", $ms_selected)){
            $clause['formulario'] = "contrato_alquiler";
        }

        if(!empty($orden_alquiler_id)){
            $clause["orden_alquiler_id"] = $orden_alquiler_id;
            $clause["formulario"] = "orden_alquiler";
        }

        if (!empty($vendedor)){ $clause['created_by'] = $vendedor;}
        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = $this->facturaVentaRepository->lista_totales($clause);
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $facturas = $this->facturaVentaRepository->listar($clause, $sidx, $sord, $limit, $start);
        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;

        if (!empty($facturas->toArray())) {
            $i = 0;
            foreach ($facturas as $row) {

                $hidden_options = "";
                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row->uuid_factura . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

                $url = base_url('facturas_seguros/editar/' . $row->uuid_factura);
                if ($row->formulario == 'refactura') {
                    $url = base_url('facturas_seguros/refacturar/' . $row->uuid_factura);
                }
                $hidden_options .= '<a href="' . $url . '" data-id="' . $row->uuid_factura . '" class="btn btn-block btn-outline btn-success">Ver Factura</a>';
                $hidden_options .= '<a href="#" class="btn btn-block btn-outline btn-success subirArchivoBtn" data-id="' . $row->id . '" data-codigo="' . $row->codigo. '" >Subir documento</a>';

                if ($row->estado == 'por_cobrar' || $row->estado == 'cobrado_parcial') $hidden_options .= '<a href="' . base_url('cobros/crear?factura=' . $row->uuid_factura) . '" data-id="' . $row->uuid_factura . '" class="btn btn-block btn-outline btn-success">Registrar Pago</a>';
                
                //dd( $row->cliente->uuid_cliente, $row->cliente_nombre);
                $response->rows[$i]["id"] = $row->uuid_factura;
                $response->rows[$i]["cell"] = array(
                    $row->uuid_factura,
                    '<a class="link" href="' . $url . '" >' . $row->codigo . '</a>',
                    '<a class="link" href="'.base_url('clientes/ver/' . $row->cliente->uuid_cliente.'?mod=fact').'">' . $row->cliente_nombre . '</a>',
                    $row->fecha_desde,
                    $row->fecha_hasta,
                    $row->present()->estado_label,
                    $row->present()->total,
                    $row->present()->saldo_pendiente,
                    '<a class="link">' . $row->vendedor_nombre . '</a>',
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

    function ajax_listar_tabla() {
        if (!$this->input->is_ajax_request()) {
            return false;
        }
        /*
         paramentos de busqueda aqui
        */
        $uuid_cliente = $this->input->post("cliente_id");
        $cliente = $this->input->post('cliente', TRUE);
        $no_factura = $this->input->post('no_factura', TRUE);
        $no_poliza = $this->input->post('no_poliza', TRUE);
        $ramop = $this->input->post('ramo', TRUE);
        $hasta = $this->input->post('hasta', TRUE);
        $desde = $this->input->post('desde', TRUE);
        $estado = $this->input->post('etapa', TRUE);
        $vendedor = $this->input->post('vendedor', TRUE);
        $centro_contable = $this->input->post('centro_contable', true);
        $ms_selected = $this->input->post('ms_selected', true);
        $sitiopago1 = $this->input->post('sitio_pago', true);
        $vencimientodesde = $this->input->post('vencimientodesde', true);
        $vencimientohasta = $this->input->post('vencimientohasta', true);


        $clause = array('empresa_id' => $this->empresaObj->id);
        $clause['contrato_alquiler_id'] = $this->input->post('contrato_alquiler_id', TRUE);
        $clause['contrato_id'] = $this->input->post('contrato_id', TRUE);
        if(isset($_POST['campo'])){
            $clause['campo'] = $this->input->post('campo');
        }
        
        if (!empty($no_factura)) {
            $clause['codigo'] = $no_factura;
        }
        if (!empty($no_poliza)) {
            $clause['numero'] = $no_poliza;
        }
        if (!empty($ramop)) {
            $clause['ramo'] = $ramop;
            if (count($clause['ramo'])==1) {
                if($ramop[0]==""){ unset($clause['ramo']); }
            }
        }
        if (!empty($sitiopago1)) {
            $clause['sitiopago'] = $sitiopago1;
        }
        if (!empty($uuid_cliente)) {
            $clienteObj = new Buscar(new Cliente_orm, 'uuid_cliente');
            $cliente = $clienteObj->findByUuid($uuid_cliente);
            $clause['cliente_id'] = $cliente->id;
        } elseif (!empty($cliente)) {
            $clause['cliente_id'] = $cliente;
        }
        if (!empty($desde)) {
            $fecha_inicio = Carbon::createFromFormat('d/m/Y', $desde, 'America/Panama')->format('Y-m-d 00:00:00');
            $clause["fecha_desde"] = $fecha_inicio;
        }
        if (!empty($hasta)) {
            $fecha_fin = Carbon::createFromFormat('d/m/Y', $hasta, 'America/Panama')->format('Y-m-d 23:59:59');
            $clause["fecha_hasta"] = $fecha_fin;
        }  
        if (!empty($vencimientodesde)) {
            $fechainicio = Carbon::createFromFormat('d/m/Y', $vencimientodesde, 'America/Panama')->format('Y-m-d 00:00:00');
            $clause["fecha_vencimiento_desde"] = $fechainicio;
        }
        if (!empty($vencimientohasta)) {
            $fechafin = Carbon::createFromFormat('d/m/Y', $vencimientohasta, 'America/Panama')->format('Y-m-d 23:59:59');
            $clause["fecha_vencimiento_hasta"] = $fechafin;
        }  
        if (!empty($vendedor)){ 
            $clause['created_by'] = $vendedor;
        }
        if (!empty($centro_contable)) {
            $clause['centro_contable'] = $centro_contable;
        }
        if (!empty($estado)){
            $clause['estado'] = $estado;
        }
        $clause['empresa_id'] = $this->empresa_id;
        

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = $this->facturaVentaRepository->lista_totales($clause);
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $facturas = $this->facturaVentaRepository->listar_tabla($clause, $sidx, $sord, $limit, $start);
        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;

        if (!empty($facturas->toArray())) {
            $i = 0;
            foreach ($facturas as $row) {

                $estado_lab = $row->present()->estado_label;
                $e = explode("<label", $estado_lab);
                $estados_tabla = "<label data-id='".$row->id."' ".$e[1]; 

                $hidden_options = "";
                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row->id . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

                $url = base_url('facturas_seguros/editar/' . $row->uuid_factura);
                if ($row->formulario == 'refactura') {
                    $url = base_url('facturas_seguros/refacturar/' . $row->uuid_factura);
                }
                $hidden_options .= '<a href="' . $url . '/?reg=fase" data-id="' . $row->uuid_factura . '" class="btn btn-block btn-outline btn-success">Ver Factura</a>';
                $hidden_options .= '<a href="#" class="btn btn-block btn-outline btn-success subirArchivoBtn" data-id="' . $row->id . '" data-codigo="' . $row->codigo. '" >Subir documento</a>';     

                if ($row->estado == 'por_cobrar' || $row->estado == 'cobrado_parcial') $hidden_options .= '<a href="' . base_url('cobros_seguros/crear?factura=' . $row->uuid_factura) . '&reg=fase" data-id="' . $row->uuid_factura . '" class="btn btn-block btn-outline btn-success">Registrar Pago</a>';
                $hidden_options .= '<a href="' . base_url('facturas_seguros/imprimir/' . $row->uuid_factura) . '" target="_blank" class="btn btn-block btn-outline btn-success imprimirFacturaBtn" data-id="' . $row->id . '" data-codigo="' . $row->codigo. '" >Imprimir</a>';
                //$hidden_options .= '<a href="#" class="btn btn-block btn-outline btn-success verBitacoraBtn" data-id="' . $row->id . '" data-codigo="' . $row->codigo. '" >Ver Bitacora</a>';


                $response->rows[$i]["id"] = $row->id;
                $response->rows[$i]["cell"] = array(
                    $row->uuid_factura,
                    '<a class="link" href="' . $url . '" >' . $row->codigo . '</a>',
                    isset($row->cliente_id)?'<a class="link" href="'.base_url('clientes/ver/' . $row->cliente->uuid_cliente.'?mod=fact').'">' . $row->cliente_nombre . '</a>':'',
                    $row->numero,
                    $row->cliente_nombre,
                    $row->fecha_desde." - ".$row->fecha_hasta,                    
                    $row->ramo,
                    $row->fecha_desde,
                    $row->fecha_hasta,
                    $row->present()->total,
                    $row->present()->saldo_pendiente,
                    ucwords($row->sitio_pago),
                    $row->centro->nombre,
                    $estados_tabla,
                    //$row->estado,
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

    public function exportar() {
        if (empty($_POST)) {
            exit();
        }


        $ids = $this->input->post('ids', true);

        $id = explode(",", $ids);

        if (empty($id)) {
            return false;
        }

        $csv = array();
        $clause = array("uuid_factura" => $id);

        $facturas = $this->facturaVentaRepository->exportar($clause);


        if (empty($facturas)) {
            return false;
        }

        $i = 0;
        foreach ($facturas AS $row) {
            $total_facturado = $row->total_facturado();
            $csvdata[$i]['no_factura'] = utf8_decode(Util::verificar_valor($row->codigo));
            $csvdata[$i]['cliente'] = utf8_decode(Util::verificar_valor($row->cliente->nombre));
            $csvdata[$i]['poliza'] = utf8_decode(Util::verificar_valor($row->polizas->numero));
            $csvdata[$i]['vigencia'] = utf8_decode(Carbon::createFromFormat('m/d/Y', Util::verificar_valor($row->fecha_desde))->format('d/m/Y'))." - ".utf8_decode(Carbon::createFromFormat('m/d/Y', Util::verificar_valor($row->fecha_hasta))->format('d/m/Y'));
            $csvdata[$i]['ramo'] = utf8_decode(Util::verificar_valor($row->polizas->ramo));
            $csvdata[$i]["fecha_desde"] = utf8_decode(Carbon::createFromFormat('m/d/Y', Util::verificar_valor($row->fecha_desde))->format('d/m/Y'));
            $csvdata[$i]["fecha_hasta"] = utf8_decode(Carbon::createFromFormat('m/d/Y', Util::verificar_valor($row->fecha_hasta))->format('d/m/Y'));
            $csvdata[$i]["monto"] = utf8_decode(Util::verificar_valor(number_format(($row->total), 2, '.', ',')));
            $csvdata[$i]["saldo"] = utf8_decode(Util::verificar_valor(number_format(($row->total - $total_facturado), 2, '.', ',')));
            $csvdata[$i]['sitio_pago'] = utf8_decode(Util::verificar_valor(ucwords($row->polizas->primafk->sitio_pago)));
            $csvdata[$i]['centro_contable'] = utf8_decode(Util::verificar_valor($row->centro->nombre));
            
            $csvdata[$i]["estado"] = utf8_decode(Util::verificar_valor($row->etapa_catalogo->valor));
            $i++;
        }

        //we create the CSV into memory
        $csv = Writer::createFromFileObject(new SplTempFileObject());
        $csv->insertOne([
            'No. Factura',
            'Cliente',
            'No. Poliza',
            'Vigencia',
            'Ramo',
            'Fecha de emision',
            'Fecha de vencimiento',            
            'Monto',
            'Saldo',
            'Sitio de pago',
            'Centro Contable',
            'Estado'
        ]);
        $csv->insertAll($csvdata);
        $csv->output("FacturasSeguros-" . date('ymd') . ".csv");
        die;
    }

    function ajax_listar_de_item() {
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        $clause = $this->input->post();
        $clause["empresa_id"] = $this->empresaObj->id;


        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = $this->facturaVentaRepository->lista_totales($clause);
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $facturas = $this->facturaVentaRepository->listar($clause, $sidx, $sord, $limit, $start);

        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
        if (!empty($facturas->toArray())) {
            foreach ($facturas as $i => $row) {
                $response->rows[$i]["id"] = $row->uuid_factura;
                $response->rows[$i]["cell"] = $this->facturaVentaRepository->getCollectionCellDeItem($row, $clause["item_id"]);
            }
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))->_display();
        exit;
    }

    function ocultotabla($modulo_id = null) {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/facturas_seguros/tabla.js'
            ));

        //Verificar desde donde se esta llamando
        //la tabla de facturas
        if (preg_match("/cajas/i", $this->router->fetch_class())) {
            if (!empty($modulo_id)) {
                $this->assets->agregar_var_js(array(
                    "caja_id" => $modulo_id
                    ));
            }
        }
        if (preg_match("/contratos/i", $this->router->fetch_class())) {
            if (!empty($modulo_id)) {
                $this->assets->agregar_var_js(array(
                    "sp_contrato_id" => $modulo_id
                    ));
            }
        }
        elseif(preg_match("/contratos_alquiler/i", $this->router->fetch_class()))
        {
            if(!empty($modulo_id) and is_array(explode("=", $modulo_id))){
                $key_value = explode("=", $modulo_id);
                $this->assets->agregar_var_js([$key_value[0]=>$key_value[1]]);
            }
        }
        elseif(preg_match("/ordenes_alquiler/i", $this->router->fetch_class()))
        {
            if(!empty($modulo_id) and is_array(explode("=", $modulo_id))){
                /*$key_value = explode("=", $modulo_id);
                $this->assets->agregar_var_js([$key_value[0]=>$key_value[1]]);*/
                $this->assets->agregar_var_js(array(
                    "cliente_id" => ""
                    ));
            }
        }
        else {
            if (!empty($modulo_id)) {
                $this->assets->agregar_var_js(array(
                    "cliente_id" => $modulo_id
                    ));
            }
        }

        $this->load->view('tabla');
    }



    function ocultotabla_de_item($sp_string_var = "") {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/facturas_seguros/tabla_de_item.js'
            ));

        $sp_array_var = explode('=', $sp_string_var);
        if (count($sp_array_var) == 2) {

            $this->assets->agregar_var_js(array(
                $sp_array_var[0] => $sp_array_var[1]
                ));

        }

        $this->load->view('tabla');
    }

    //funcion creda: Jose Luis
    function crear2($uuid=NULL) {

    	$data 			= array();
    	$mensaje 		= array();
    	$breadcrumb 	= array();
    	$titulo 		= '<i class="fa fa-line-chart"></i> Factura: Crear';
    	$titulo_header 	= 'Crear Factura';

    	if (!$this->auth->has_permission('acceso')) {
    		$acceso = 0;
    		$mensaje = array('estado' => 500, 'mensaje' => '<b>Error!</b> Usted no cuenta con permiso para esta solicitud', 'clase' => 'alert-danger');
    		$this->session->set_flashdata('mensaje', $mensaje);
    	}
    	if ($uuid == NULL && !$this->empresaObj->tieneCuentaCobro()) {
    		$mensaje = array('estado' => 500, 'mensaje' => 'No hay cuenta de cobro asociada', 'clase' => 'alert-danger');
    		$this->session->set_flashdata('mensaje', $mensaje);
    		redirect(base_url('facturas_seguros/listar'));
    	}

        $editar_precio = 1;
        $usuario = Usuario_orm::findByUuid($this->id_usuario);
        $usuario->load('roles');

        if(!$this->auth->has_permission('crear__editarPrecio') && $usuario->roles->sum('superuser') == 0){
            $editar_precio= 0;
        }

        $this->_Css();
        $this->assets->agregar_css(array(
          'public/assets/css/modules/stylesheets/animacion.css'
          ));
        $this->_js();
        $this->assets->agregar_js(array(
            'public/assets/js/modules/facturas_seguros/componentes/vue.filtro_factura.js',
            'public/assets/js/modules/facturas_seguros/componentes/vue.datos_factura.js',
            'public/resources/compile/modulos/facturas_seguros/items_factura.js',
            'public/assets/js/modules/facturas_seguros/componentes/vue.items_alquiler.js',
            'public/assets/js/modules/facturas_seguros/componentes/vue.contrato_alquiler.js',
            'public/assets/js/modules/facturas_seguros/componentes/vue.items_alquiler_adicionales.js',
            'public/assets/js/modules/facturas_seguros/vue.crear.js',
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/resources/compile/modulos/facturas_seguros/formulario.js'
            ));

    	//Crea variables js
        $this->_crear_variables_catalogos($uuid);

        $clause_precios = array('principal' => "1");
        $precios = Precios_orm::where($clause_precios)->get(array('id','nombre'));
        $precio_id = $precios;

        $usuario_id = $this->id_usuario;
        $vendedor_user = Usuario_orm::findByUuid($usuario_id);

    	// Si existe variable $uuid
    	//
                $breadcrumbName="<script>
        var str =localStorage.getItem('ms-selected');
        var capitalize = str[0].toUpperCase()+str.substring(1);
        document.write(capitalize);
    </script>";
    $breadcrumbUrl =base_url("/");
    $completeBreadCrumb="<a href='$breadcrumbUrl'>$breadcrumbName</a>";
    $breadcrumb = array(

         
         "ruta" => array(
          0 => array(
              "nombre" => " $completeBreadCrumb",
              "activo" => false,
              ),
          1 => array(
            "nombre" => "Facturas",
            "activo" => false,
            "url" => 'facturas_seguros/listar'
            ),
          2=> array(
            "nombre" => '<b>Crear</b>',
            "activo" => true
            )
          ),
         ); 
        if(!empty($uuid)){

          $factura = $this->facturaVentaRepository->findByUuid($uuid);

          if (is_null($uuid) || is_null($factura)) {
           $mensaje = array('estado' => 500, 'mensaje' => '<strong>Error!</strong> Su solicitud no fue procesada');
           $this->session->set_flashdata('mensaje', $mensaje);
           redirect(base_url('facturas_seguros/listar'));
       }

       $titulo = '<i class="fa fa-line-chart"></i> Factura: ' . $factura->codigo;
       $titulo_header = 'Editar Factura: ' . $factura->codigo;
       

       $breadcrumb = array(

         "menu" => array(
           "nombre" => 'Acci&oacute;n',
           "url"	 => '#',
           "opciones" => array()
           ),

         "ruta" => array(
          0 => array(
              "nombre" => " $completeBreadCrumb",
              "activo" => false,
              ),
          1 => array(
            "nombre" => "Facturas",
            "activo" => false,
            "url" => 'facturas_seguros/listar'
            ),
          2=> array(
            "nombre" => '<b>Detalle</b>',
            "activo" => true
            )
          ),
         );

       $breadcrumb["menu"]["opciones"]["facturas_seguros/imprimir/" . $factura->uuid_factura] = "Imprimir";
       $data["uuid"] = $uuid;
       $data['cliente_id'] = $factura->cliente->uuid_cliente;
       $data['uuid_factura'] = $factura->uuid_factura;

       $this->assets->agregar_var_js(array(
           "vista" => "editar"
           ));
   }
   if(!empty($_POST['contrato_alquiler_uuid'])){
    $contrato_alquiler_uuid = $_POST['contrato_alquiler_uuid'];
    $contrato = $this->ContratosAlquilerRepository->findByUuid($contrato_alquiler_uuid);
    $contrato->load("ordenes_alquiler")->first();
        //dd($contrato->ordenes_alquiler[0]);
    $this->assets->agregar_var_js(array(
       "tipo_chosen" => "contrato_alquiler",
       "contrato_alquiler_uuid" => $contrato_alquiler_uuid,
       "termino_pago" => $contrato->ordenes_alquiler[0]->termino_pago,
       "created_by" => $contrato->ordenes_alquiler[0]->created_by,
       "bodega_id" => $contrato->ordenes_alquiler[0]->bodega_id

       ));
}

$this->assets->agregar_var_js(array(
    "precio_id" => $precio_id[0]->id,
    "usuario_id" => $vendedor_user->id,
    "editar_precio" => $editar_precio
    ));

$breadcrumb["titulo"] = $titulo;
$this->template->agregar_titulo_header($titulo_header);
$this->template->agregar_breadcrumb($breadcrumb);
$this->template->agregar_contenido($data);
$this->template->visualizar();
}

function editar($uuid) {


     $data 			= array();
     $mensaje 		= array();
     $breadcrumb 	= array();
     $titulo 		= '<i class="fa fa-line-chart"></i> Factura: Crear';
     $titulo_header 	= 'Crear Factura';

     if (!$this->auth->has_permission('acceso')) {
      $acceso = 0;
      $mensaje = array('estado' => 500, 'mensaje' => '<b>Error!</b> Usted no cuenta con permiso para esta solicitud', 'clase' => 'alert-danger');
      $this->session->set_flashdata('mensaje', $mensaje);
    }
    if ($uuid == NULL && !$this->empresaObj->tieneCuentaCobro()) {
      $mensaje = array('estado' => 500, 'mensaje' => 'No hay cuenta de cobro asociada', 'clase' => 'alert-danger');
      $this->session->set_flashdata('mensaje', $mensaje);
      redirect(base_url('facturas_seguros/listar'));
    }

    $editar_precio = 1;
    $usuario = Usuario_orm::findByUuid($this->id_usuario);
    $usuario->load('roles');

    if(!$this->auth->has_permission('crear__editarPrecio') && $usuario->roles->sum('superuser') == 0){
        $editar_precio= 0;
    }
    $this->_Css();
    $this->assets->agregar_css(array(
      'public/assets/css/modules/stylesheets/animacion.css'
      ));
    $this->_js();
    $this->assets->agregar_js(array(
        'public/assets/js/modules/facturas_seguros/componentes/vue.filtro_factura.js',
        'public/assets/js/modules/facturas_seguros/componentes/vue.datos_factura.js',
        'public/resources/compile/modulos/facturas_seguros/items_factura.js',
        'public/resources/compile/modulos/facturas_seguros/formulario.js',
        'public/assets/js/modules/facturas_seguros/componentes/vue.items_alquiler.js',
        'public/assets/js/modules/facturas_seguros/componentes/vue.contrato_alquiler.js',
        'public/assets/js/modules/facturas_seguros/componentes/vue.items_alquiler_adicionales.js',
        'public/assets/js/modules/facturas_seguros/vue.crear.js',
        'public/assets/js/plugins/ckeditor/ckeditor.js',
        'public/assets/js/plugins/ckeditor/adapters/jquery.js',
    
    ));

    	//Crea variables js
    $this->_crear_variables_catalogos($uuid);

    $clause_precios = array('principal' => "1");
    $precios = Precios_orm::where($clause_precios)->get(array('id','nombre'));
    $precio_id = $precios;

    $usuario_id = $this->id_usuario;
    $vendedor_user = Usuario_orm::findByUuid($usuario_id);

    	// Si existe variable $uuid
    	//
    if(!empty($uuid)){

        $factura = $this->facturaVentaRepository->findByUuid($uuid);

        if (is_null($uuid) || is_null($factura)) {
            $mensaje = array('estado' => 500, 'mensaje' => '<strong>Error!</strong> Su solicitud no fue procesada');
            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('facturas_seguros/listar'));
        }

        $titulo = '<i class="fa fa-line-chart"></i> Factura: ' . $factura->codigo;
        $titulo_header = 'Editar Factura: ' . $factura->codigo;
          $breadcrumbName="<script>
                var str =localStorage.getItem('ms-selected');
                var capitalize = str[0].toUpperCase()+str.substring(1);
                document.write(capitalize);
            </script>";
            $breadcrumbUrl =base_url("/");
            $completeBreadCrumb="<a href='$breadcrumbUrl'>$breadcrumbName</a>";

        $breadcrumb = array(

         "menu" => array(
           "nombre" => 'Acci&oacute;n',
           "url"	 => '#',
           "opciones" => array()
           ),

         "ruta" => array(
          0 => array(
              "nombre" => " $completeBreadCrumb",
              "activo" => false,
              ),
          1 => array(
            "nombre" => "Facturas",
            "activo" => false,
            "url" => 'facturas_seguros/listar'
            ),
          2=> array(
            "nombre" => '<b>Detalle</b>',
            "activo" => true
            )
          ),
         );

        $breadcrumb["menu"]["opciones"]["facturas_seguros/imprimir/" . $factura->uuid_factura] = "Imprimir";
        $breadcrumb["menu"]["opciones"]["#exportarBtn"] = "Exportar";

        $data["uuid"] = $uuid;
        $data['cliente_id'] = $factura->cliente->uuid_cliente;
        $data['uuid_factura'] = $factura->uuid_factura;

        $this->assets->agregar_var_js(array(
            "vista" => "editar"
        ));
    }

    if(!empty($_POST['contrato_alquiler_uuid'])){
        $contrato_alquiler_uuid = $_POST['contrato_alquiler_uuid'];
        $contrato = $this->ContratosAlquilerRepository->findByUuid($contrato_alquiler_uuid);
        $contrato->load("ordenes_alquiler")->first();
            //dd($contrato->ordenes_alquiler[0]);
        $this->assets->agregar_var_js(array(
           "tipo_chosen" => "contrato_alquiler",
           "contrato_alquiler_uuid" => $contrato_alquiler_uuid,
           "termino_pago" => $contrato->ordenes_alquiler[0]->termino_pago,
           "created_by" => $contrato->ordenes_alquiler[0]->created_by,
           "bodega_id" => $contrato->ordenes_alquiler[0]->bodega_id

           ));
    }

    $cuenta_transaccionales = Cuentas_orm::transaccionalesDeEmpresa($this->empresa_id)->activas()
            ->get(array('id', 'nombre', 'codigo', Capsule::raw("HEX(uuid_cuenta) AS uuid")));
    //$cuenta_transaccionales->uuid_cuenta = bin2hex($cuenta_transaccionales->uuid_cuenta);
    $cuenta_transaccionales->toArray();

    $this->assets->agregar_var_js(array(
        "precio_id" => $precio_id[0]->id,
        "usuario_id" => $vendedor_user->id,
        "editar_precio" => $editar_precio,
		"facturas_seguros_id" => $factura->id,
		"data"=> $factura,
        "cuenta_transaccionales" => json_encode($cuenta_transaccionales),
    ));
	
	$data["subpanels"] = [];
	
    $breadcrumb["titulo"] = $titulo;
    $this->template->agregar_titulo_header($titulo_header);
    $this->template->agregar_breadcrumb($breadcrumb);
    $this->template->agregar_contenido($data);
    $this->template->visualizar();
}

    //cambios para factura creada por jaime chung

function guardar_cambios() {

    $reg = !empty($_POST['reg']) ? $_POST['reg'] : '' ;
    $uuid = !empty($_POST['uuid']) ? $_POST['uuid'] : '' ;
    $campo = $_POST['campo'];
    $com = array("comentario" => $campo['comentario']);
    $comentario = FacturaSeguro::where("id", $campo['factura_id'])->update($com);

    $fact = FacturaSeguro::where("id", $campo['factura_id'])->first();
    /*$factura_controller = new FacturaCompraRepository();


    $factura_to_edit = (new FacturaSeguroRepository)->find($_POST['campo']['factura_id']);

    $this->_setFacturaFromPost($factura_to_edit, $_POST);

    $factura_to_edit->save();

    if ($factura_to_edit->estado == 'por_cobrar') {
        $transaccion = new Transaccion;
        $transaccion->hacerTransaccion($factura_to_edit->fresh(), new TransaccionFactura);
    }

    $this->_sync_items($factura_to_edit, $_POST['items']);*/

    $mensaje = array('tipo' => "success", 'mensaje' => '<b>Exito!</b> Se ha guardado la factura correctamente', 'titulo' => 'Factura '.$fact->codigo);
    $this->session->set_flashdata('mensaje', $mensaje);
    if (!empty($reg) && $reg == "fase")// por si en algun momento se necita que valide otra ubicacion
        redirect(base_url('facturas_seguros/listar'));
    else
        redirect(base_url('facturas_seguros/listar'));
}

private function _setFacturaFromPost($factura, $post) {

    $campo = $post['campo'];

    $factura->termino_pago = $campo["termino_pago"];
    $factura->fecha_desde = $campo['fecha_desde'];
    $factura->fecha_hasta = $campo['fecha_hasta'];
    $factura->created_by = $campo["creado_por"];
    $factura->item_precio_id = $campo['item_precio_id'];
    $factura->centro_contable_id = $campo["centro_contable_id"];
    $factura->estado = $campo['estado'];
    $factura->subtotal = $campo["subtotal"];
    $factura->descuento = $campo["descuento"];
    $factura->impuestos = $campo["impuestos"];
    $factura->total = $campo["total"];


}

private function _sync_items($factura, $items){

    $factura->items()->whereNotIn('id',array_pluck($items,'factura_item_id'))->delete();
    $impuesto_id = new ImpuestosRepository();
    $cuenta_id = new CuentasRepository();
    foreach ($items as $row) {

        $factura_item_id = (isset($row['factura_item_id']) and !empty($row['factura_item_id'])) ? $row['factura_item_id'] : '';

        $factura_item = $factura->items()->firstOrNew(['id'=>$factura_item_id]);
        $factura_item->item_id = $row["item_id"];
        $factura_item->categoria_id = $row["categoria_id"];
        $factura_item->cantidad = str_replace(',','',$row["cantidad"]);
        $factura_item->unidad_id = $row["unidad_id"];
        $factura_item->precio_unidad = str_replace(',','',$row["precio_unidad"]);
        $factura_item->impuesto_id = $impuesto_id->findByUuid($row["impuesto_id"])->id;
        $factura_item->descuento = $row["descuento"];
        $factura_item->cuenta_id = $cuenta_id->findByUuid($row["cuenta_id"])->id;
        $factura_item->precio_total = $row['precio_total'];
        $factura_item->descuento = $row["descuento"];


        $factura_item->save();

    }

}

    /**
     * Crea catalogos en variables js
     */
    private function _crear_variables_catalogos($uuid=NULL) {

        $clause = array('empresa_id' => $this->empresa_id);
        $clause_precios = array_merge($clause, ["estado" => 1]);
        $clause_lista_precios = array_merge($clause, ["estado" => 1, "tipo_precio" => 'venta']);
        $clause_impuesto = array_merge($clause, ["estado" => "Activo"]);

        //-------------------------
        // Factura Info
        //-------------------------
        if(!empty($uuid)){

            $factura = $this->facturaVentaRepository->findByUuid($uuid);



            if (is_null($factura)) {
                $mensaje = array('estado' => 500, 'mensaje' => '<strong>Error!</strong> Su solicitud no fue procesada');
                $this->session->set_flashdata('mensaje', $mensaje);
                redirect(base_url('facturas/listar'));
            }

            //$factura->load('comentario_timeline');
            $factura->load('cliente.centro_facturable', 'items', 'items.impuesto', 'items.cuenta','cobros','comentario_timeline');
            $factura->{$factura->formulario};
            //dd($factura->formulario);
            //Comentario de Items
            $factura->items->each(function ($item, $key )  use ($factura) {
                if ($item->comentario!=''){
                    $fieldset = array(
                     'comentario'=>$item->comentario,
                     "usuario_id" => $factura->created_by,
                     "created_at" =>$item->created_at
                     );
                    $comentarios = new Comentario($fieldset);
                    $factura->comentario_timeline->push($comentarios);
                }
                return $factura;
            });

            if ($factura->formulario == 'contrato_venta') $factura->contratos;

            //Filtrar Items de Contrato de Alquiler
            if ($factura->formulario == 'contrato_alquiler'){
               $factura->items = $factura->items->filter(function ($item) {
                return $item->item_adicional == 0;
            });
           }
           
            $factura->formulario = $factura->formulario;

            $fecd = explode("/", $factura->fecha_desde);
            $fech = explode("/", $factura->fecha_hasta);

            $fecha_d = $fecd[2]."-".$fecd[1]."-".$fecd[0];
            $fecha_h = $fech[2]."-".$fech[1]."-".$fech[0];
            $fechas = array("fecha_desde" => $fecha_d, "fecha_hasta" => $fecha_h);

        
           
           $salida = ['id' => $factura->id, 'type' => 'Factura_orm'];
           $this->assets->agregar_var_js(array(
               "uuid_factura" => $factura->uuid_factura,
                //"factura" => json_encode($salida),
               "factura_venta_id"=>  $factura->id,
               "cobros" => $factura->cobros,
               "infofactura" => $factura,
               "fechas" => json_encode($fechas)
               ));
       }

        

        //-------------------------
        // Catalogo Clientes
        //-------------------------
        $clientes = $this->ClienteRepository->getClientesEstadoActivo($clause)->get();
        $clientes->load('centro_facturable');

        //-------------------------
        // Catalogo Terminos Pago
        //-------------------------
       $terminos_pagos = $this->facturaVentaCatalogoRepository->getTerminoPago()->toArray();
       $terminos_pagos = (!empty($terminos_pagos) ? array_map(function($terminos_pagos) {
          return array(
           "id" => $terminos_pagos["etiqueta"],
           "nombre" => $terminos_pagos["valor"]
           );
      }, $terminos_pagos) : "");

        //-------------------------
        // Catalogo Estados
        //-------------------------
       $estados = $this->facturaVentaCatalogoRepository->getEtapas()->toArray();
       $estados = (!empty($estados) ? array_map(function($estados) {
          return array(
           "id" => $estados["etiqueta"],
           "nombre" => $estados["valor"]
           );
      }, $estados) : "");

        //---------------------
        // Catalogo Vendedores
        //---------------------
       $roles_users = Rol_orm::where(function ($query) use ($clause) {
            $query->where('empresa_id', '=', $clause['empresa_id']);
            //$query->where('nombre', 'like', '%vendedor%');
        })->orWhere(function ($query) use ($clause) {
            $query->where('empresa_id', '=', $clause['empresa_id']);
            //$query->where('nombre', 'like', '%venta%');
        })->get();

        $usuarios = array();
        $vendedores = array();
        foreach ($roles_users as $roles) {
            $usuarios = $roles->usuarios;
            foreach ($usuarios as $user) {
                if ($user->pivot->empresa_id == $clause['empresa_id']) {
                    array_push($vendedores, array(
                        "id" => $user->id,
                        "nombre" => Util::verificar_valor($user->nombre) ." ". Util::verificar_valor($user->apellido)
                    ));
                }
            }
        }

        //-------------------------
        // Catalogo Lista Precios
        //-------------------------
        $precios = Precios_orm::where($clause_lista_precios)->get(array('id', 'uuid_precio', 'nombre'))->toArray();
        $precios = (!empty($precios) ? array_map(function($precios) {
          return array(
           "id" => $precios["id"],
           "nombre" => $precios["nombre"]
           );
        }, $precios) : "");

        //-------------------------
        // Catalogo Items por Categoria
        //-------------------------
        //Categorias_orm::with('items')->where($clause_precios)->get(['id', 'nombre'])->toArray();
        /*$categotias = Categorias_orm::where($clause_precios)->get(['id', 'nombre'])->toArray();
        $categotias = (!empty($categotias) ? array_map(function($categotias) {
            return array(
                "id" => $categotias["id"],
                "nombre" => trim($categotias["nombre"])
            );
        }, $categotias) : "");*/

        //el catalogo de items se debe cargar por ajax, limit 20
        $categotiasItems = Categorias_orm::where($clause_precios)->get(['id', 'nombre'])->map(function($categoria){
            return array_merge($categoria->toArray(), ["items" => []]);
        });

        //-------------------------
        // Catalogo Centro Contables
        //-------------------------
        $ids_centros = Centros_orm::where($clause_impuesto)->lists('padre_id');
        //lista de centros contables
        $centros_contables = Centros_orm::whereNotIn('id', $ids_centros->toArray())->where(function ($query) use ($clause_impuesto) {
          $query->where($clause_impuesto);
      })->get(array('id', 'nombre', 'uuid_centro'))->toArray();
        $centros_contables = (!empty($centros_contables) ? array_map(function($centros_contables) {
          return array(
             "id" => $centros_contables["id"],
             "nombre" => $centros_contables["nombre"]
             );
      }, $centros_contables) : "");

        //-------------------------------
        //Centro Facturacion
        //--------------------------------

        $centrofac = CentroFacturable::where("empresa_id", $this->empresa_id)->get();


        //--------------------------------
        // Catalogo Bodegas
        //--------------------------------
        $bodegas = Bodegas_orm::where(array('empresa_id' => $this->empresa_id, 'estado' => 1))->get(array('id', 'nombre'))->toArray();
        $bodegas = (!empty($bodegas) ? array_map(function($bodegas) {
          return array(
             "id" => $bodegas["id"],
             "nombre" => $bodegas["nombre"]
             );
        }, $bodegas) : "");

        //--------------------------------
        // Empezar facturas desde:
        // Catalogo Tipos Factura
        // Ordenes Venta
        //--------------------------------
        $tipos_factura = array();
        $ordenesventas = empty($uuid) ? $this->ordenVentaRepository->ordenesVentasValidas($clause)->load('cliente') : $this->ordenVentaRepository->ordenesVentasValidasVer($clause);
        if(!empty($ordenesventas)){
            foreach ($ordenesventas AS $ordenventa) {
               $tipos_factura["orden_venta"][] = array(
                'uuid' => $ordenventa->uuid_venta,
                'nombre' => $ordenventa->codigo . " - " . $ordenventa->cliente->nombre
                );
            }
        }

        //---------------------
        // Catalogo Contrato de Venta
        //---------------------
        $contratoventas = $this->contratoRepository->getContratos($clause);
        if(!empty($contratoventas)){
            foreach ($contratoventas as $contrato) {
               $tipos_factura["contrato_venta"][] = array(
                'uuid' => $contrato->uuid_contrato,
                'nombre' => $contrato->codigo . " - " .  $contrato->cliente->nombre,
                'contrato' => $contrato
                );
           }
        }

        //---------------------
        // Catalogo Contratos de Alquiler
        //---------------------
        $contratosalquiler = $this->ContratosAlquilerRepository->getContratosValidos($clause)->load('cliente');
        if(!empty($contratosalquiler)){
            foreach($contratosalquiler as $contrato) {
                $tipos_factura["contrato_alquiler"][] = array(
                    'uuid' => $contrato->uuid_contrato_alquiler,
                    'nombre' => $contrato->codigo . " - " . $contrato->cliente->nombre,
                );
            }
        }

        //---------------------
        // Catalogo Ordenes de Trabajo
        //---------------------
        $ordenestrabajo = $this->OrdenesTrabajoRepository->getOrdenesValidas($clause)->load('cliente');
        if(!empty($ordenestrabajo)){
            foreach ($ordenestrabajo as $orden) {
                $tipos_factura["orden_trabajo"][] = array(
                    'uuid' => $orden->uuid_orden_trabajo,
                    'nombre' => $orden->numero . " - " . $orden->cliente->nombre
                );
            }
        }
        $tipos_factura = collect($tipos_factura);

        //---------------------
        // Catalogo Impuestos
        //---------------------
        /*$impuesto = Impuestos_orm::where($clause_impuesto)->whereHas('cuenta', function ($query) use ($clause_impuesto) {
            $query->activas();
            $query->where('empresa_id', '=', $clause_impuesto['empresa_id']);
        })->get(array('id', 'uuid_impuesto', Capsule::raw("HEX(uuid_impuesto) AS uuid"), 'nombre', 'impuesto'))->toArray();*/
        $clause_impuesto = ['empresa_id' => $this->empresa_id, 'transaccionales' => true, 'conItems' => true, 'vendedor' => true];
        $impuesto = $this->ImpuestosRepository->get($clause_impuesto);
        //---------------------
        // Catalogo Cuenta Contable
        //---------------------
        $cuenta_transaccionales = Cuentas_orm::transaccionalesDeEmpresa($this->empresa_id)->activas()
      ->get(array('id', 'uuid_cuenta', 'nombre', 'codigo', Capsule::raw("HEX(uuid_cuenta) AS uuid")))->toArray();

        //---------------------------------------------
        //Metodo de Pago y Sitio Pago
        //---------------------------------------------
        $metodo_pago = $this->SegInteresesAseguradosRepository->listar_catalogo('metodo_pago', 'orden');
        $sitio_pago = $this->SegInteresesAseguradosRepository->listar_catalogo('sitio_pago', 'orden');

        //----------------------------------------------
        //Frecuencia de Pagos
        //----------------------------------------------

        $frecuencia_pagos = $this->SegInteresesAseguradosRepository->listar_catalogo('frecuencia_pagos', 'orden');




        //------------------------------------------
        //Informacion de Poliza
        //-------------------------------------------

        //$poliza = Polizas::where("id","=", $factura->id_poliza)->get(array('id', 'ramo_id', 'ramo', 'aseguradora_id', 'plan_id'));
        $poliza = Polizas::where("id","=", $factura->id_poliza)->select('id', 'ramo_id', 'ramo', 'aseguradora_id', 'plan_id', 'numero', 'fin_vigencia', 'inicio_vigencia')->first();
        $aseguradora = $poliza->aseguradorafk->nombre;
        $pagador = $poliza->vigenciafk->pagador;
        $frecuenciapago = $poliza->primafk->frecuencia_pago;
        $metodopago = $poliza->primafk->metodo_pago;
        $sitiopago = $poliza->primafk->sitio_pago;
        $ramo = $poliza->ramo;
        $idramo = $poliza->ramo_id;
        $numeropoliza = $poliza->numero;
        $iniciovigencia = $poliza->inicio_vigencia;
        $finvigencia = $poliza->fin_vigencia;
        $primaneta = $poliza->primafk->prima_anual;
        $cantidadpagos = $poliza->primafk->cantidad_pagos;
        $primabruta = number_format($primaneta/$cantidadpagos, 2);
        $id_impuesto = $poliza->planesfk->id_impuesto;

        //-------------------------------
        //Agentes de Poliza
        //---------------------------------
        $agentes = PolizasParticipacion::where("id_poliza", $factura->id_poliza)->orderBy("porcentaje", "DESC")->groupBy("agente")->select("agente", Capsule::raw('MAX(porcentaje_participacion) AS porcentaje'))->get()->toArray();



        $imp = Impuesto::where("id", $id_impuesto)->first();

        $polizas_val = array("aseguradora"=>$aseguradora, "pagador" => $pagador, "metodopago" => $metodopago, "sitiopago" => $sitiopago, "frecuenciapago" => $frecuenciapago, "ramo" => $ramo, "ramo_id" => $idramo, "numeropoliza" => $numeropoliza, "iniciovigencia" => $iniciovigencia, "finvigencia" => $finvigencia, "primaneta" => $primaneta, "cantidadpagos" => $cantidadpagos, "primabruta" => $primabruta);
        if($factura->subtotal == 0 && $factura->otros == 0){
            $porcentajedescuento = 0;
        }else{
            $porcentajedescuento = (($factura->descuento)/(($factura->subtotal)+($factura->otros)))*100;
        }
        
        $porcentajedescuento = number_format($porcentajedescuento, 2);

        $fac = FacturaSeguro::where("empresa_id", $this->empresa_id)->where("formulario", "facturas_seguro")->where("id_poliza", $factura->id_poliza)->orderBy("id", "ASC")->get();
        $conta = 1;
        $cuota = 0;
        foreach ($fac as $valor) {
            if ($valor['id']==$factura->id) {
                $cuota = $conta ;
            }
            $conta++;
        }

        if (isset($agentes) && isset($agentes[0])) {
            $agt = $agentes[0]['agente'];
        }else{
            $agt = "";
        }


        //---------------------
        // Agregar variables JS
        //---------------------
        $this->assets->agregar_var_js(array(
          "infopoliza" => json_encode($polizas_val),
          "impuesto_plan" => $id_impuesto,
          "agentemayor" => json_encode($agt),
          "agentesArray" => json_encode($agentes),
          "clientesArray" => $clientes,
          "terminosPagoArray" => json_encode($terminos_pagos),
          "vendedoresArray" => json_encode($vendedores),
          "preciosArray" => json_encode($precios),
          "centrosContablesArray" => json_encode($centros_contables),
          "bodegasArray" => json_encode($bodegas),
          "tiposFacturasArray" => !empty($tipos_factura) ? $tipos_factura : json_encode(array()),
          "estadosArray" => json_encode($estados),
          "impuestos" =>  json_encode($impuesto),
          "porcentaje_descuento" => $porcentajedescuento,
          "cuenta_transaccionales" => json_encode($cuenta_transaccionales),
          "metodo_pago" => json_encode($metodo_pago),
          "sitio_pago" => json_encode($sitio_pago),
          "cuotaactual" => $cuota,
          "frecuenciaArray" => json_encode($frecuencia_pagos),
            "categorias" => $categotiasItems, //$categorias,
            "acceso" => 1
        ));
    }

  public function vue_cargar_templates() {
     $this->load->view('vue/componente_filtro_factura');
     $this->load->view('vue/componente_datos_factura');
     $this->load->view('vue/componente_items_factura');
     $this->load->view('vue/componente_items_alquiler');
     $this->load->view('vue/componente_contrato_alquiler');
     $this->load->view('vue/componente_items_alquiler_adicionales');
 }

    //Seleccionar items por categoria
 public function ajax_seleccionar_items() {
     $categoria_id = $this->input->post('categoria_id', true);

     $items = $this->_seleccionarItemsByCategoria($categoria_id);

     $response = new stdClass();
     $response->items = !empty($items[0]["items"]) ? $items[0]["items"] : array();
     echo json_encode($response);
     exit;
 }

 private function _seleccionarItemsByCategoria($categoria_id="") {

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
return $categotia->toArray();
}

function ver($uuid = null) {
    $acceso = 1;
    $mensaje = array();
    if (!$this->auth->has_permission('acceso', 'facturas_seguros/ver/(:any)')) {
        $acceso = 0;
        $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud', 'clase' => 'alert-danger');
    }

    $this->_Css();
    $this->assets->agregar_css(array(
        'public/assets/css/modules/stylesheets/animacion.css'
        ));
    $this->_js();
    $this->assets->agregar_js(array(
        'public/assets/js/plugins/ckeditor/ckeditor.js',
        'public/assets/js/plugins/ckeditor/adapters/jquery.js',
        'public/assets/js/default/vue-validator.min.js',
        'public/assets/js/default/vue-resource.min.js',
        'public/assets/js/modules/facturas_seguros/services.facturas.js',
        'public/assets/js/modules/cotizaciones/services.itemsData.js',
        'public/assets/js/modules/facturas_seguros/editar.controller.js',


        ));

        //$facturaObj  = new Buscar(new Factura_orm,'uuid_factura');
    $factura = $this->facturaVentaRepository->findByUuid($uuid);
    $factura->load('comentario_timeline');
    if (is_null($uuid) || is_null($factura)) {
        $mensaje = array('estado' => 500, 'mensaje' => '<strong>¡Error!</strong> Su solicitud no fue procesada');
        $this->session->set_flashdata('mensaje', $mensaje);
        redirect(base_url('facturas_seguros/listar'));
    }
    $data = array();
    $salida = ['id' => $factura->id, 'type' => 'Factura_orm'];
    $this->assets->agregar_var_js(array(
        "vista" => 'editar',
        "acceso" => $acceso == 0 ? $acceso : $acceso,
        "uuid_factura" => $factura->uuid_factura,
        "factura" => json_encode($salida),
        "factura_venta_id"=>  $factura->id,
        "cobros" => $factura->cobros,
        "factura" => $factura
        ));
        //$data['ordenes_ventas'] = $ordenesVentas->toArray();
    $data['uuid_factura'] = $factura->uuid_factura;
    $data['cliente_id'] = $factura->cliente->uuid_cliente;
    $data['mensaje'] = $mensaje;
     $breadcrumbName="<script>
        var str =localStorage.getItem('ms-selected');
        var capitalize = str[0].toUpperCase()+str.substring(1);
        document.write(capitalize);
    </script>";
    $breadcrumbUrl =base_url("/");
    $completeBreadCrumb="<a href='$breadcrumbUrl'>$breadcrumbName</a>"; 
    $breadcrumb = array(
        "titulo" => '<i class="fa fa-line-chart"></i> Factura: ' . $factura->codigo,
        );


    $breadcrumb = array(
        "titulo" => '<i class="fa fa-line-chart"></i> $completeBreadCrumb: ' . $factura->codigo,
        "filtro" => false,
        "menu" => array(
            "nombre" => 'Acci&oacute;n',
            "url" => '#',
            "opciones" => array()
            )
        );
        // if($cotizacion->imprimible)
        //   {
    $breadcrumb["menu"]["opciones"]["facturas_seguros/imprimir/" . $factura->uuid_factura] = "Exportar";

        //  }


    $this->template->agregar_titulo_header('Editar Factura');
    $this->template->agregar_breadcrumb($breadcrumb);
    $this->template->agregar_contenido($data);
    $this->template->visualizar();

}

public function imprimir($uuid = null) {
    if ($uuid == null) {
        return false;
    }

    $facturaVenta = $this->facturaVentaRepository->findByUuid($uuid);

    $facturaVenta->load("empresa");
    /*$facturaVenta->load('cliente.centro_facturable', 'items', 'items.impuesto', 'items.cuenta','cobros','comentario_timeline', 'empresa');
    $facturaVenta->{$facturaVenta->formulario};
    //Comentario de Items
    $facturaVenta->items->each(function ($item, $key )  use ($factura) {
        if ($item->comentario!=''){
            $fieldset = array(
            'comentario'=>$item->comentario,
            "usuario_id" => $facturaVenta->created_by,
            "created_at" =>$item->created_at
            );
            $comentarios = new Comentario($fieldset);
            $facturaVenta->comentario_timeline->push($comentarios);
        }
        return $facturaVenta;
    });*/

    $poliza = Polizas::where("id","=", $facturaVenta->id_poliza)->select('id', 'ramo_id', 'ramo', 'aseguradora_id', 'plan_id', 'numero', 'fin_vigencia', 'inicio_vigencia')->first();
    $facturaPol['aseguradora'] = $poliza->aseguradorafk->nombre;
    $facturaPol['pagador'] = $poliza->vigenciafk->pagador;
    $facturaPol['frecuenciapago'] = $poliza->primafk->frecuencia_pago;
    $facturaPol['metodopago'] = $poliza->primafk->metodo_pago;
    $facturaPol['sitiopago'] = $poliza->primafk->sitio_pago;
    $facturaPol['ramo'] = $poliza->ramo;
    $facturaPol['idramo'] = $poliza->ramo_id;
    $facturaPol['numeropoliza'] = $poliza->numero;
    $facturaPol['iniciovigencia'] = $poliza->inicio_vigencia;
    $facturaPol['finvigencia'] = $poliza->fin_vigencia;
    $facturaPol['primaneta'] = $poliza->primafk->prima_anual;
    $facturaPol['cantidadpagos'] = $poliza->primafk->cantidad_pagos;
    $facturaPol['primabruta'] = number_format($facturaPol['primaneta']/$facturaPol['cantidadpagos'], 2);

    $fac = FacturaSeguro::where("empresa_id", $this->empresa_id)->where("formulario", "facturas_seguro")->where("id_poliza", $facturaVenta->id_poliza)->orderBy("id", "ASC")->get();
        $conta = 1;
        $cuota = 0;
        foreach ($fac as $valor) {
            if ($valor['id']==$facturaVenta->id) {
                $facturaPol['cuota'] = $conta ;
            }
            $conta++;
        }

    if ($facturaVenta->formulario == "facturas_seguro") {
        $facturaPol['asociado'] = "Póliza";
    }

    $history = $this->facturaVentaRepository->getLastEstadoHistory($facturaVenta->id);
    $dompdf = new Dompdf();
    $data = ['factura' => $facturaVenta, 'history' => $history, 'factura_poliza' => $facturaPol];

    $html = $this->load->view('factura', $data, true);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream($facturaVenta->codigo . ' - ' . $facturaVenta->cliente->nombre);

}

function ocultoformulario($facturas = array()) {

    $data = array();
    $clause = array('empresa_id' => $this->empresa_id);
    $clause_precios = array('empresa_id' => $this->empresa_id, 'estado' => 1);
    $clause_impuesto = array('empresa_id' => $this->empresa_id, 'estado' => 'Activo');

    $roles_users = Rol_orm::where(function ($query) use ($clause) {
        $query->where('empresa_id', '=', $clause['empresa_id']);
        $query->where('nombre', 'like', '%vendedor%');
    })->orWhere(function ($query) use ($clause) {
        $query->where('empresa_id', '=', $clause['empresa_id']);
        $query->where('nombre', 'like', '%venta%');
    })->get();

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

    $data['terminos_pagos'] = $this->facturaVentaCatalogoRepository->getTerminoPago();
    $data['etapas'] = $this->facturaVentaCatalogoRepository->getEtapas();
    $data['vendedores'] = $vendedores;
    $data['unidades'] = array();
    $data["categorias"] = Categorias_orm::categoriasConItems($this->empresa_id);
    $data['precios'] = Precios_orm::where($clause_precios)->get(array('id', 'uuid_precio', 'nombre'));
    $data['items'] = Items_orm::where($clause_precios)->get(array('id', 'uuid_item', 'uuid_activo', 'nombre', 'codigo'));
    $clause_impuesto_actual = ['empresa_id' => $this->empresa_id, 'transaccionales' => true, 'conItems' => true, 'vendedor' => true];

        /*$impuesto = Impuestos_orm::where($clause_impuesto)->whereHas('cuenta', function ($query) use ($clause_impuesto) {
            $query->activas();
            $query->where('empresa_id', '=', $clause_impuesto['empresa_id']);

        })->get(array('id', 'uuid_impuesto', 'nombre', 'impuesto')); //Estaba dando problemas*/
        $data['impuestos'] =  $this->ImpuestosRepository->get($clause_impuesto_actual);
        $data['cuenta_activo'] = Cuentas_orm::transaccionalesDeEmpresa($this->empresa_id)->deTipoDeCuenta([4])->activas()->get();
        $data['clientes'] = Cliente_orm::where($clause)->get(array('id', 'nombre', 'credito_favor'));
        $data['bodegas'] = Bodegas_orm::where(array('empresa_id' => $this->empresa_id, 'estado' => 1))->get(array('id', 'nombre'));

        $ids_centros = Centros_orm::where($clause_impuesto)->lists('padre_id');
        //lista de centros contables
        $centros_contables = Centros_orm::whereNotIn('id', $ids_centros->toArray())->where(function ($query) use ($clause_impuesto) {
            $query->where($clause_impuesto);
        })->get(array('id', 'nombre', 'uuid_centro'));
        $data['centros_contables'] = $centros_contables;

        if (isset($facturas['info'])) $data['info'] = $facturas['info'];

        $this->load->view('formulario', $data);
    }

    function guardar() {

    	if ($_POST) {

    		$request = Illuminate\Http\Request::createFromGlobals();
    		$venta_uuid= $request->input('venta_uuid');
    		$array_factura = $request->input('campo');
    		$lineitems = $request->input('items');
    		$formulario = $request->input('formulario');
    		$facturable_id = $request->input('fac_facturable_id');
    		$this->disparador = new \Illuminate\Events\Dispatcher();
    		Capsule::beginTransaction();
    		try {
    			$delete_item = $_POST['delete_items'];
    			if (!empty($delete_item)) {
    				$ids = explode(',', $delete_item);
    				$this->lineItemRepository->delete($ids);
    			}
    			$j = 0;
    			$itemFactura = [];
    			foreach ($lineitems as $item) {
    				$item_uuid = $item['item_id'];
    				$impuesto_uuid = $item['impuesto_id'];
    				$cuenta_uuid = $item['cuenta_id'];
    				$impuestoObj = new Buscar(new Impuestos_orm, 'uuid_impuesto');
    				$cuentaObj = new Buscar(new Cuentas_orm, 'uuid_cuenta');
    				$impuestoClase = $impuestoObj->findByUuid($impuesto_uuid);
    				$cuentaClase = $cuentaObj->findByUuid($cuenta_uuid);
    				$item['impuesto_id'] = $impuestoClase->id;
    				$item['cuenta_id'] = $cuentaClase->id;
    				$item['empresa_id'] = $this->empresa_id;
    				$total_impuesto = ($impuestoClase->impuesto / 100) * ($item['cantidad'] * $item['precio_unidad']);
    				$total_descuento = ($item['descuento'] / 100) * ($item['cantidad'] * $item['precio_unidad']);
    				array_push($itemFactura, array(
                        'item_id' => $item['item_id'],
                        'categoria_id' => $item['categoria_id'],
                        'cantidad' => $item['cantidad'],
                        'unidad_id' => $item['unidad_id'],
                        'precio_unidad' => $item['precio_unidad'],
                        'impuesto_id' => $item['impuesto_id'],
                        'atributo_id' => $item['atributo_id'],
                        'descuento' => $item['descuento'],
                        'cuenta_id' => $item['cuenta_id'],
                        'precio_total' => $item['precio_total'],
                        'empresa_id' => $item['empresa_id'],
                        'impuesto_total' => $total_impuesto,
                        'descuento_total' => $total_descuento
                        ));
    				if (!empty($item['factura_item_id'])) {
    					$itemFactura[$j]['lineitem_id'] = $item['factura_item_id'];
    				}
    				$j++;
    			}
    			$array_factura['empresa_id'] = $this->empresa_id;
    			$array_factura['created_by'] = $array_factura['creado_por'];
    			$array_factura['fecha_desde'] = Carbon::createFromFormat('d/m/Y', $array_factura['fecha_desde'], 'America/Panama')->format('m/d/Y');
    			$array_factura['fecha_hasta'] = Carbon::createFromFormat('d/m/Y', $array_factura['fecha_hasta'], 'America/Panama')->format('m/d/Y');
    			// dd($array_factura);
    			if (!empty($formulario)) $array_factura['formulario'] = $formulario;
    			if (empty($array_factura['factura_id'])) {

    				$total = $this->facturaVentaRepository->lista_totales(['empresa_id' => $this->empresa_id]);
    				$year = Carbon::now()->format('y');
    				$codigo = Util::generar_codigo('INV' . $year, $total + 1);
    				$array_factura['codigo'] = $codigo;
    				$data = ['facturaventa' => $array_factura, 'lineitem' => $itemFactura,'venta_uuid'=>$venta_uuid ];
    				$factura = $this->facturaVentaRepository->create($data, $formulario);
    			} else {
    				$data = ['facturaventa' => $array_factura, 'lineitem' => $itemFactura];
    				$factura = $this->facturaVentaRepository->update($data);
    			}
    			if ($formulario == 'orden_venta') {
    				$model = $this->ordenVentaRepository->find($facturable_id);
    				$this->disparador->listen([OrdenVentaFacturableEvent::class], CrearOrdenFacturableListener::class);
    				if (empty($array_factura['factura_id'])) $this->disparador->fire(new OrdenVentaFacturableEvent($factura, $model));
    			} elseif ($formulario == 'contrato_venta') {
    				$model = $this->contratoRepository->findBy($facturable_id);
    				$this->disparador->listen([ContratoFacturableEvent::class], CrearContratoFacturableListener::class);
    				if (empty($array_factura['factura_id'])) $this->disparador->fire(new ContratoFacturableEvent($factura, $model));
    			}
    		} catch (Illuminate\Database\QueryException $e) {
    			log_message('error', __METHOD__ . " ->" . ", Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
    			Capsule::rollback();
    			$mensaje = array('estado' => 500, 'mensaje' => '<b>�Error! Su solicitud no fue procesada</b> ');
    			$this->session->set_flashdata('mensaje', $mensaje);
    			redirect(base_url('facturas_seguros/listar'));
    		}
    		Capsule::commit();
    		if (!is_null($factura)) {
    			if ($formulario == 'orden_venta') {
    				$ordenVenta = $this->ordenVentaRepository->find($facturable_id);
    				if (!is_null($ordenVenta)) {
    					$this->load->library('Events/Orden_venta/Orden_venta_estado');
    					$OrdenVentaEstado = new Orden_venta_estado;
    					$OrdenVentaEstado->handle($ordenVenta);
    				}
    			}
    			if ($factura->estado == 'por_cobrar') {
    				$transaccion = new Transaccion;
    				$transaccion->hacerTransaccion($factura->fresh(), new TransaccionFactura);
    			}
    			//$this->salidas->comp__crearSalida(array("id" => $factura->id, "type" => "Factura_orm"));
    			$mensaje = array('estado' => 200, 'mensaje' => '<b>�&Eacute;xito!</b> Se ha guardado correctamente ' . $factura->codigo);
    		} else {
    			$mensaje = array('estado' => 500, 'mensaje' => '<b>�Error! Su solicitud no fue procesada</b> ');
    		}
    		$this->session->set_flashdata('mensaje', $mensaje);
    		redirect(base_url('facturas_seguros/listar'));
    	}
    }

    function guardar2() {

        if ($_POST) {

            /* echo "<pre>";
              print_r($_POST);
              echo "</pre>";
              die(); */
              $request = Illuminate\Http\Request::createFromGlobals();
              $venta_uuid = $request->input('tipo_factura_id');
              $array_factura = $request->input('campo');
              $lineitems = $request->input('items');

              $items_adicionales = $request->input('items_adicionales');
              $formulario = $request->input('formulario');
              $facturable_id = $request->input('fac_facturable_id');
              $this->disparador = new \Illuminate\Events\Dispatcher();
              Capsule::beginTransaction();

              if (!empty($items_adicionales) && empty($array_factura['factura_id'])) {
                // Agregar identificador de cargo adicional
                // Para diferenciar items normales de items
                // de cargo adicional.
                $itemsadicionales = array();
                foreach ($items_adicionales AS $item) {
                    if (empty($item["categoria_id"]) || empty($item["item_id"])) {
                        continue;
                    }
                    $item["item_adicional"] = 1;
                    $itemsadicionales[] = $item;
                }

                if (!empty($itemsadicionales)) {
                    $lineitems = array_merge($lineitems, $itemsadicionales);
                }
            }

            try {
                $delete_item = $_POST['delete_items'];

                if (!empty($delete_item)) {
                    $ids = explode(',', $delete_item);
                    $this->lineItemRepository->delete($ids);
                }

                $j = 0;
                $itemFactura = [];
                foreach ($lineitems as $item) {
                    $item_uuid = !empty($item['item_id']) ? $item['item_id'] : "";
                    $impuesto_uuid = !empty($item['impuesto_id']) ? $item['impuesto_id'] : "";
                    $cuenta_uuid = !empty($item['cuenta_id']) ? $item['cuenta_id'] : "";
                    $descuento = !empty($item['descuento']) ? $item['descuento'] : 0;
                    $cantidad = !empty($item['cantidad']) ? $item['cantidad'] : 0;
                    $precio_unidad = !empty(    str_replace(',','',$item['precio_unidad'])   ) ? $item['precio_unidad'] : 0;
                  //  $precio_unidad = $item['precio_unidad'];
                    $impuestoObj = new Buscar(new Impuestos_orm, 'uuid_impuesto');
                    $cuentaObj = new Buscar(new Cuentas_orm, 'uuid_cuenta');

                    $impuestoClase = $impuestoObj->findByUuid($impuesto_uuid);
                    $cuentaClase = $cuentaObj->findByUuid($cuenta_uuid);

                    $item['impuesto_id'] = !empty($impuestoClase) ? $impuestoClase->id : "";
                    $item['cuenta_id'] = !empty($cuentaClase) ? $cuentaClase->id : "";
                    $item['empresa_id'] = $this->empresa_id;
                    $total_impuesto = !empty($impuestoClase) ? ($impuestoClase->impuesto / 100) * ($item['cantidad'] * $item['precio_unidad']) : "";
                    $total_descuento = ($descuento / 100) * ($cantidad * $precio_unidad);
                    array_push($itemFactura, array(
                        'item_id' => !empty($item['item_id']) ? $item['item_id'] : "",
                        'categoria_id' => !empty($item['categoria_id']) ? $item['categoria_id'] : "",
                        'cantidad' => $cantidad,
                        'unidad_id' => !empty($item['unidad_id']) ? $item['unidad_id'] : 0,
                        'item_adicional' => !empty($item['item_adicional']) ? $item['item_adicional'] : "",
                        'periodo_tarifario_id' => !empty($item['periodo_tarifario_id']) ? $item['periodo_tarifario_id'] : "",
                        'precio_unidad' => $precio_unidad,
                        'impuesto_id' => !empty($item['impuesto_id']) ? $item['impuesto_id'] : "",
                        'atributo_id' => isset($item['atributo_id']) && !empty($item['atributo_id']) ? $item['atributo_id'] : "",
                        'atributo_text' => isset($item['atributo_text']) && !empty($item['atributo_text']) ? $item['atributo_text'] : "",
                        'descuento' => $descuento,
                        'cuenta_id' => !empty($item['cuenta_id']) ? $item['cuenta_id'] : "",
                        'precio_total' => !empty($item['precio_total']) ? $item['precio_total'] : "",
                        'empresa_id' => !empty($item['empresa_id']) ? $item['empresa_id'] : "",
                        'comentario' => !empty($item['comentario']) ? $item['comentario'] : "",
                        'impuesto_total' => $total_impuesto,
                        'descuento_total' => $total_descuento
                        ));

                    if (!empty($item['factura_item_id']) && !preg_match("/contrato_alquiler/i", $formulario)) {
                        $itemFactura[$j]['lineitem_id'] = $item['factura_item_id'];
                    } else {
                        $itemFactura[$j]['lineitem_id'] = $item['item_id'];
                    }
                    $j++;

                } //end foreach

                $array_factura['empresa_id'] = $this->empresa_id;
                $array_factura['created_by'] = !empty($array_factura['creado_por']) ? $array_factura['creado_por'] : "";

                if (!empty($formulario)){ $array_factura['formulario'] = $formulario; }

                if (empty($array_factura['factura_id'])) {

                    $codigo = 'INV' . date('y') . $this->facturaVentaRepository->getLastCodigo(array('empresa_id' => $this->empresa_id));
                    $array_factura['codigo'] = $codigo;

                    $data = ['facturaventa' => $array_factura, 'lineitem' => $itemFactura, 'venta_uuid' => $venta_uuid];
                    $factura = $this->facturaVentaRepository->create($data, $formulario);

                }
                else {

                    $data = ['facturaventa' => $array_factura, 'lineitem' => $itemFactura];
                    $factura = $this->facturaVentaRepository->update($data);
                }

                if ($formulario === 'orden_venta') {

                    $model = $this->ordenVentaRepository->find($facturable_id);
                    $this->disparador->listen([OrdenVentaFacturableEvent::class], CrearOrdenFacturableListener::class);
                    if (empty($array_factura['factura_id'])) { $this->disparador->fire(new OrdenVentaFacturableEvent($factura, $model));}

                } elseif ($formulario == 'contrato_venta') {

                    $model = $this->contratoRepository->findBy($facturable_id);
                    $this->disparador->listen([ContratoFacturableEvent::class], CrearContratoFacturableListener::class);
                    if (empty($array_factura['factura_id']))
                        $this->disparador->fire(new ContratoFacturableEvent($factura, $model));
                } elseif ($formulario == 'contrato_alquiler') {

                    $model = $this->ContratosAlquilerRepository->find($facturable_id);
                    //dd($model);
                    $this->disparador->listen([ContratoAlquilerFacturableEvent::class], CrearContratoAlquilerFacturableListener::class);
                    if (empty($array_factura['factura_id']))
                        $this->disparador->fire(new ContratoAlquilerFacturableEvent($factura, $model));
                }
            }
            catch (Illuminate\Database\QueryException $e) {
                log_message('error', __METHOD__ . " ->" . ", Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
                Capsule::rollback();
                $mensaje = array('estado' => 500, 'mensaje' => '<b>Error! Su solicitud no fue procesada</b> ');
                $this->session->set_flashdata('mensaje', $mensaje);
                redirect(base_url('facturas_seguros/listar'));
            }
            Capsule::commit();

            if (!is_null($factura)) {
                if ($formulario == 'orden_venta') {
                    $ordenVenta = $this->ordenVentaRepository->find($facturable_id);

                    if (!is_null($ordenVenta)) {
                        $this->load->library('Events/Orden_venta/Orden_venta_estado');
                        $OrdenVentaEstado = new Orden_venta_estado;
                        $OrdenVentaEstado->handle($ordenVenta);
                    }
                }
                if ($factura->estado == 'por_cobrar') {
                    $transaccion = new Transaccion;
                    $transaccion->hacerTransaccion($factura->fresh(), new TransaccionFactura);
                }

                $mensaje = array('estado' => 200, 'mensaje' => '<b>Exito!</b> Se ha guardado correctamente ' . $factura->codigo);
            } else {
                $mensaje = array('estado' => 500, 'mensaje' => '<b>Error! Su solicitud no fue procesada</b> ');
            }
            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('facturas_seguros/listar'));
        }
    }

    function refacturar($uuid = null) {
        $acceso = 1;
        $mensaje = array();
        if (!$this->auth->has_permission('acceso')) {
            $acceso = 0;
            $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud', 'clase' => 'alert-danger');
            $this->session->set_flashdata('mensaje', $mensaje);
        }
        if (!$this->empresaObj->tieneCuentaCobro()) {
            $mensaje = array('estado' => 500, 'mensaje' => 'No hay cuenta de cobro asociada', 'clase' => 'alert-danger');
            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('facturas_seguros/listar'));
        }

        $this->_Css();
        $this->assets->agregar_css(array(
            'public/assets/css/modules/stylesheets/animacion.css',
            'public/assets/css/modules/stylesheets/refactura.css',
            ));
        $this->_js();
        $this->assets->agregar_js(array(
            'public/assets/js/default/vue.min.js',
            'public/assets/js/modules/facturas_seguros/routes.js',
            'public/assets/js/modules/facturas_seguros/fechas.js',
            'public/assets/js/modules/facturas_seguros/componente.js',
            'public/assets/js/modules/facturas_seguros/refacturar.js',


            ));

        $data = array();
        $clause = array('empresa_id' => $this->empresa_id, 'transaccionales' => true);
        $cuentas =  $this->CuentasRepository->get($clause);

        if ($_POST) {

            $request = Illuminate\Http\Request::createFromGlobals();
            $uuid_factura_compras = $request->input('factura_compras');
            $id = explode(",", $uuid_factura_compras[0]);
            $uuid = new Illuminate\Support\Collection($id);
            $uuid->transform(function ($item) {
                return hex2bin($item);
            });

            $factura_compras = $this->FacturaCompraRepository->findByInUuid($uuid);
            $factura_compras->toArray();
            //$factura_compras = Facturas_compras_orm::whereIn('uuid_factura', $uuid)->get();
            //$factura_compras->toArray();

            //dd($cuentas->toArray());
            if (!is_null($factura_compras)) {
                $this->assets->agregar_var_js(array(
                    "factura_compra" => $factura_compras,
                    "vista" => 'refacturar',
                    "cuenta_tran" => $cuentas,
                    "etapas" => $this->facturaVentaCatalogoRepository->getEtapas()
                    ));
            }
        } else {

            $factura = $this->facturaVentaRepository->findByUuid($uuid);

            $factura->load('refactura', 'cliente');
           //dd($factura);
            if (!is_null($factura)) {
                $factura->toArray();
                $this->assets->agregar_var_js(array(
                    "factura" => $factura,
                    "vista" => 'refacturar_ver',
                    "cuenta_tran" => $cuentas,
                    ));
            }
        }

        $this->assets->agregar_var_js(array(
            "acceso" => $acceso == 0 ? $acceso : $acceso
            ));

        $data['mensaje'] = $mensaje;
        /*$breadcrumb = array(
            "titulo" => '<i class="fa fa-line-chart"></i> Factura: Crear',
            );*/
             $breadcrumbName="<script>
        var str =localStorage.getItem('ms-selected');
        var capitalize = str[0].toUpperCase()+str.substring(1);
        document.write(capitalize);
    </script>";
    $breadcrumbUrl =base_url("/");
    $completeBreadCrumb="<a href='$breadcrumbUrl'>$breadcrumbName</a>"; 
            $breadcrumb = array(
                "titulo" => '<i class="fa fa-line-chart"></i> Factura: Crear',
                "menu" => array(
                    "nombre" => 'Acci&oacute;n',
                    "url"	 => '#',
                    "opciones" => array()
                    ),

                "ruta" => array(
                  0 => array(
                      "nombre" => "$completeBreadCrumb",
                      "activo" => false,
                      ),
                  1 => array(
                    "nombre" => "Facturas",
                    "activo" => false,
                    "url" => 'facturas_seguros/listar'
                    ),
                  2=> array(
                    "nombre" => '<b>Detalle</b>',
                    "activo" => true
                    )
                  ),
                );
            $breadcrumb["menu"]["opciones"]["facturas_seguros/imprimir_refactura/" . $factura->uuid_factura] = "Imprimir";

            $this->template->agregar_titulo_header('Crear Factura');
            $this->template->agregar_breadcrumb($breadcrumb);
            $this->template->agregar_contenido($data);
            $this->template->visualizar();

        }

        function imprimir_refactura($uuid) {
            $refactura = $this->facturaVentaRepository->findByUuid($uuid);
            $refactura->load('refactura', 'cliente');
            $history = $this->facturaVentaRepository->getLastEstadoHistory($refactura->id);
            $dompdf = new Dompdf();
            $data = ['factura' => $refactura, 'history' => $history];
            $html = $this->load->view('refactura', $data, true); 
        //echo "<pre>".$html."</pre>"; die;
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $dompdf->stream($refactura->codigo . ' - ' . $refactura->cliente->nombre);



        }

        function ocultorefacturar() {
            $data = array();
            $clause = array('empresa_id' => $this->empresa_id);
            $clause_precios = array('empresa_id' => $this->empresa_id, 'estado' => 1);
            $clause_impuesto = array('empresa_id' => $this->empresa_id, 'estado' => 'Activo');

            $roles_users = Rol_orm::where(function ($query) use ($clause) {
                $query->where('empresa_id', '=', $clause['empresa_id']);
                $query->where('nombre', 'like', '%vendedor%');
            })->orWhere(function ($query) use ($clause) {
                $query->where('empresa_id', '=', $clause['empresa_id']);
                $query->where('nombre', 'like', '%venta%');
            })->get();

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

            $data['terminos_pagos'] = $this->facturaVentaCatalogoRepository->getTerminoPago();
            $data['etapas'] = $this->facturaVentaCatalogoRepository->getEtapas();
            $data['vendedores'] = $vendedores;
            $data['unidades'] = array();
            $data["categorias"] = Categorias_orm::categoriasConItems($this->empresa_id);
            $data['precios'] = Precios_orm::where($clause_precios)->get(array('id', 'uuid_precio', 'nombre'));

            $data['clientes'] = Cliente_orm::where($clause)->get(array('id', 'nombre', 'credito_favor'));
            $ids_centros = Centros_orm::where($clause_impuesto)->lists('padre_id');
        //lista de centros contables
            $centros_contables = Centros_orm::whereNotIn('id', $ids_centros->toArray())->where(function ($query) use ($clause_impuesto) {
                $query->where($clause_impuesto);
            })->get(array('id', 'nombre', 'uuid_centro'));
            $data['centros_contables'] = $centros_contables;

            if (isset($facturas['info'])) $data['info'] = $facturas['info'];

            $this->assets->agregar_var_js(array(
                "etapas" =>   $data['etapas']
                ));

            $this->load->view('formrefactura', $data);
            $this->load->view('componente');

        }

        function guardar_refactura() {
            if ($_POST) {
                $request = Illuminate\Http\Request::createFromGlobals();
                $array_factura = $request->input('campo');
                $ids_facturas_compras = $request->input('factura_compras');
                $this->disparador = new \Illuminate\Events\Dispatcher();

                $array_factura['empresa_id'] = $this->empresa_id;
                $array_factura['created_by'] = $array_factura['creado_por'];
                $array_factura['centro_facturacion_id'] = 0;
                if (empty($array_factura['factura_id'])) {
                    $array_factura['codigo'] = $this->_generar_codigo();
                }
                $ids = [];
                foreach ($ids_facturas_compras as $item) {
                    array_push($ids, $item['id']);
                }

                Capsule::beginTransaction();
                try {
                //dd($array_factura);
                    $factura = $this->facturaVentaRepository->crear($array_factura);
                    if (empty($array_factura['factura_id'])) {
                        $this->disparador->listen([FacturableEvent::class], FacturableListener::class);
                        $models = Facturas_compras_orm::whereIn('id', $ids)->get();

                        if (empty($array_factura['factura_id'])) $this->disparador->fire(new FacturableEvent($factura, $models->all()));
                    }
                } catch (Illuminate\Database\QueryException $e) {
                   log_message('error', __METHOD__ . " ->" . ", Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
                   Capsule::rollback();
                   $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error! Su solicitud no fue procesada</b> ');
                   $this->session->set_flashdata('mensaje', $mensaje);
                   redirect(base_url('facturas_seguros/listar'));
               }

               Capsule::commit();

               if (!is_null($factura)) {
                Capsule::commit();
                $mensaje = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente ' . $factura->codigo);
                if ($factura->estado == 'por_cobrar') {
                    //dd($factura);
                    $transaccion = new Transaccion;
                    $transaccion->hacerTransaccion($factura->fresh(), new TransaccionFactura);
                }
            } else {
                $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error! Su solicitud no fue procesada</b> ');
            }
            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('facturas_seguros/listar'));
        }
    }

    private function _generar_codigo() {
        $clause_empresa = ['empresa_id' => $this->empresa_id];
        $total = $this->facturaVentaRepository->lista_totales($clause_empresa);
        $year = Carbon::now()->format('y');
        $codigo = Util::generar_codigo('INV' . $year, $total + 1);
        return $codigo;
    }

    function ajax_factura_info() {
        $uuid = $this->input->post('uuid');
        $factura = $this->facturaVentaRepository->findByUuid($uuid);
        $factura->load('cliente.centro_facturable', 'items', 'items.impuesto', 'items.cuenta','cobros');
        if ($factura->formulario == 'orden_venta') $factura->ordenes_ventas;
        if ($factura->formulario == 'contrato_venta') $factura->contratos;
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($factura->toArray()))->_display();
        exit;
    }

    function ajax_empezar_factura_desde() {
        $tipo = $this->input->post('tipo');
        $vista = $this->input->post('vista');
        $response = array();
        $resultados = [];
        $clause = array('empresa_id' => $this->empresa_id);
        if ($tipo == 'orden_venta') {
            if ($vista != 'ver') $response = $this->ordenVentaRepository->ordenesVentasValidas($clause);
            if ($vista == 'ver') $response = $this->ordenVentaRepository->ordenesVentasValidasVer($clause);
            $response->load('cliente');
            foreach ($response as $orden_venta) {
                $resultados[] = array('uuid' => $orden_venta->uuid_venta, 'nombre' => $orden_venta->codigo . " - " . $orden_venta->cliente->nombre);
            }
        }
        if ($tipo == 'contrato_venta') {
            $response = $this->contratoRepository->getContratos($clause);
            foreach ($response as $contrato) {
                $resultados[] = array('uuid' => $contrato->uuid_contrato, 'nombre' => $contrato->codigo . " - " . $contrato->cliente->nombre, 'contrato' => $contrato);
            }
        }


        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($resultados))->_display();
        exit;
    }

    function ajax_getAll() {
        if (!$this->input->is_ajax_request()) {
            return false;
        }
        $clause = ['empresa_id' => $this->empresa_id, 'formulario' => ['factura_venta', 'orden_venta', 'contrato_venta'], 'estado' => ['cobrado_completo']];
        $facturas = $this->facturaVentaRepository->sinDevolucion($clause);
        $facturas->load('cliente', 'items', 'items.inventario_item', 'items.inventario_item.unidades', 'items.impuesto');
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($facturas->toArray()))->_display();
        exit;
    }

    function ajax_getFacturadoCompleto() {
        $clause = ['empresa_id' => $this->empresa_id];
        $factura = $this->facturaVentaRepository->cobradoCompletoSinNotaCredito($clause);
        $factura->load('cliente', 'items', 'items.inventario_item', 'items.inventario_item.unidades', 'items.impuesto');
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($factura->toArray()))->_display();
        exit;
    }
    function ajax_getFacturadoValidos() {
        $clause = ['empresa_id' => $this->empresa_id];
        $factura = $this->facturaVentaRepository->estadoValidosSinNotaCredito($clause);
        $factura->load('cliente', 'items', 'items.inventario_item', 'items.inventario_item.unidades', 'items.impuesto');
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
            'public/assets/css/plugins/jquery/switchery.min.css',
            'public/assets/css/plugins/jquery/jquery.fileupload.css',
            'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
            'public/assets/css/plugins/bootstrap/select2.min.css',
            'public/assets/css/modules/stylesheets/facturas.css',
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
            'public/assets/js/plugins/jquery/jqgrid/plugins/jQuery.jqGrid.columnToggle.js',
            'public/assets/js/plugins/jquery/switchery.min.js',
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
            'public/assets/js/plugins/jquery/fileupload/jquery.fileupload.js',
            'public/assets/js/plugins/bootstrap/select2/select2.min.js',
            'public/assets/js/plugins/bootstrap/select2/es.js',
            'public/assets/js/plugins/jquery/jquery.webui-popover.js',
            'public/assets/js/modules/facturas_seguros/directives/item_comentario.js',
            'public/assets/js/default/vue/directives/inputmask.js'
            ));
    }

    /**
     * Retornar arreglo con los
     * campos que se mostraran
     * en el formulario de subir archivos.
     *
     * @return array
     */
    function documentos_campos() {

        return array(array(
            "type" => "text",
            "name" => "numero_factura",
            "id" => "numero_factura",
            "class" => "form-control numero-factura",
            "readonly" => "readonly",
            "ng-model" => "campos.numero_factura",
            "label" => "No. Factura"
            ),
        array(
            "type"		=> "hidden",
            "name" 		=> "factura_id",
            "id" 		=> "factura_id",
            "class"		=> "form-control",
            "readonly"	=> "readonly",
            ));
    }

    function ajax_guardar_documentos() {
        if (empty($_POST)) {
            return false;
        }

        $factura_id = $this->input->post('factura_id', true);

        $modeloInstancia = FacturaSeguro::find($factura_id);
        //dd($modeloInstancia);
        $this->documentos->subir($modeloInstancia);
    }
    function ocultoformulariocomentarios() {

      $this->assets->agregar_js(array(
       'public/assets/js/plugins/ckeditor/ckeditor.js',
       'public/assets/js/plugins/ckeditor/adapters/jquery.js',
       'public/assets/js/modules/facturas_seguros/vue.comentario.js',
       'public/assets/js/modules/facturas_seguros/formulario_comentario.js'
       ));

      $this->load->view('formulario_comentarios');
      $this->load->view('comentarios');
  }
  
    public function formularioModalEditar($data = NULL) {
        $this->load->view('formularioModalDocumentoEditar');
    }
	
  function ajax_guardar_comentario() {

     if(!$this->input->is_ajax_request()){
      return false;
  }

  $model_id   = $this->input->post('modelId');
  $comentario = $this->input->post('comentario');
  $uuid_usuario = $this->session->userdata('huuid_usuario');
  $usuario = Usuario_orm::findByUuid($uuid_usuario);
  $comentario = ['comentario'=>$comentario,'usuario_id'=>$usuario->id];

  $facturas = $this->facturaVentaRepository->agregarComentario($model_id, $comentario);

  $facturas->load('comentario_timeline');

  $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
  ->set_output(json_encode($facturas->comentario_timeline->toArray()))->_display();
  exit;

}
    public function ajax_cliente_info() { //Esta funcion lo habian borrado, se agregó 29sept se usa en refacturar
            /*$clause = array('empresa_id' => $this->empresa_id);
            $clientes = Cliente_orm::where($clause)->get(array('id', 'nombre', 'credito', 'exonerado_impuesto'));
*/

            $clause = ['empresa_id' => $this->empresa_id, 'transaccionales' => true, 'conItems' => true, 'vendedor' => true];
            $clientes =   $this->ClienteRepository->getCollectionCliente(  $this->ClienteRepository->get($clause));

            $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($clientes->toArray()))->_display();



            exit;
        }

        public function ajax_cambiar_estado_facturas() {

        $campos = $_POST['campo'];

        $clause=array();
        $clause['estado'] = $campos['estado'];
        try {
            $factura = FacturaSeguro::whereIn("id", $campos['ids'])->update($clause);
        } catch (\Exception $e) {
            $factura = log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
        }

        print json_encode($factura);
        exit;
    }

    
}

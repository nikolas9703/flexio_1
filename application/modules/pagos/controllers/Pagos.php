<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/*
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
//transacciones
use Flexio\Modulo\Pagos\Transacciones\PagosProveedor as PagosProveedor;
use Flexio\Modulo\Planilla\Transacciones\PagosPlanilla;
use Flexio\Modulo\Comisiones\Transacciones\PagosComisiones;
//repositories
use Flexio\Modulo\Pagos\Repository\PagosRepository as pagosRep;
use Flexio\Modulo\Proveedores\Repository\ProveedoresRepository as proveedoresRep;
use Flexio\Modulo\Agentes\Repository\AgentesRepository as agentesRep;
use Flexio\Modulo\aseguradoras\Repository\AseguradorasRepository as aseguradorasRep;   
use Flexio\Modulo\SubContratos\Repository\SubContratoRepository as subcontratosRep;
use Flexio\Modulo\Cobros\Repository\CatalogoCobroRepository as CatalogoCobroRepository;
use Flexio\Modulo\Pagos\Repository\CatalogoPagoRepository as CatalogoPagoRepository;
use Flexio\Modulo\Planilla\Repository\PagadasRepository as PagadasRepository;
use Flexio\Modulo\Pagos\Models\Pagos as pagosModel;
use Flexio\Modulo\Planilla\Repository\PlanillaRepository;
use Flexio\Modulo\ConfiguracionContabilidad\Repository\CuentaBancoRepository as CuentaBanco;
use Flexio\Modulo\ConfiguracionCompras\Repository\ChequesRepository;
use Flexio\Modulo\Catalogos\Repository\CatalogoRepository;
use Flexio\Modulo\Bancos\Repository\BancosRepository;
use Flexio\Modulo\FacturasCompras\Repository\FacturaCompraRepository;
use Flexio\Modulo\Comisiones\Repository\ColaboradorRepository as ComisionColaboradorRepository;
use Flexio\Modulo\Comisiones\Repository\ComisionesRepository as ComisionesRepository;
use Flexio\Modulo\Contabilidad\Repository\CuentasRepository;
use Flexio\Modulo\Pagos\Validators\PagoValidator;
//utils
use Flexio\Library\Util\FlexioAssets;
use Flexio\Library\Util\FlexioSession;
use Flexio\Library\Toast;
//otros
use Dompdf\Dompdf;

class Pagos extends CRM_Controller
{
    private $empresa_id;
    private $id_usuario;
    private $empresaObj;
    protected $pagoGuardar;
    protected $listaCobro;
    protected $cajaRepository;
    //transacciones
    protected $PagosProveedor;
    //repositories
    private $pagosRep;
    private $pagosModel;
    private $proveedoresRep;
    private $agentesRep;
    private $subcontratosRep;
    protected $CatalogoCobroRepository;
    protected $CatalogoPagoRepository;
    private $usuarioId;
    private $PagadasRepository;
    protected $planillaRepository;
    protected $cuenta_banco;
    protected $chequesRepository;
    protected $CatalogoRepository;
    protected $BancosRepository;
    protected $FacturaCompraRepository;
    protected $PagosPlanilla;
    protected $CuentasRepository;
    protected $ComisionColaboradorRepository;
    protected $ComisionesRepository;
    protected $PagosComisiones;
    protected $PagoValidator;
    //utils
    protected $FlexioAssets;
    protected $FlexioSession;
    protected $Toast;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('usuarios/Usuario_orm');
        $this->load->model('usuarios/Empresa_orm');
        $this->load->model('usuarios/Roles_usuarios_orm');
        $this->load->model('roles/Rol_orm');

        $this->load->model('proveedores/Proveedores_orm');
        $this->load->model('agentes/Agentes_orm');
        $this->load->model('aseguradoras/Aseguradoras_orm');

        $this->load->model('ordenes/Ordenes_orm');

        $this->load->model('bancos/Bancos_orm');

        $this->load->model('facturas_compras/Facturas_compras_orm');
        $this->load->model('proveedores/Proveedores_proveedor_categoria_orm');
        $this->load->model('pagos/Pagos_orm');
        $this->load->model('pagos/Pago_catalogos_orm');
        $this->load->model('pagos/Pago_metodos_pago_orm');
        $this->load->model('pagos/Pago_pagables_orm');

        $this->load->module(array('documentos'));

        Carbon::setLocale('es');
        setlocale(LC_TIME, 'Spanish');

        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresaObj = new Buscar(new Empresa_orm(), 'uuid_empresa');
        $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
        $this->id_usuario = $this->session->userdata('huuid_usuario');
        $this->usuarioId = $this->session->userdata('id_usuario');
        $this->empresa_id = $this->empresaObj->id;

        $this->load->library('Repository/Pagos/Guardar_pago');
        $this->load->library('Repository/Pagos/Lista_pago');
        $this->pagoGuardar = new Guardar_pago();
        $this->listaPago = new Lista_pago();
        $this->cajaRepository = new CajasRepository();
        $this->PagadasRepository = new PagadasRepository();
        $this->ComisionColaboradorRepository = new ComisionColaboradorRepository();
        $this->ComisionesRepository = new ComisionesRepository();

        //transacciones
        $this->PagosProveedor = new PagosProveedor();
        $this->PagosPlanilla = new PagosPlanilla();
        $this->PagosComisiones = new PagosComisiones();

        //repositories
        $this->pagosRep = new pagosRep();
        $this->pagosModel = new pagosModel();
        $this->proveedoresRep = new proveedoresRep();
        $this->subcontratosRep = new subcontratosRep();
        $this->CatalogoCobroRepository = new CatalogoCobroRepository();
        $this->CatalogoPagoRepository = new CatalogoPagoRepository();
        $this->planillaRepository = new PlanillaRepository();
        $this->cuenta_banco = new CuentaBanco();
        $this->chequesRepository = new ChequesRepository();
        $this->CatalogoRepository = new CatalogoRepository();
        $this->BancosRepository = new BancosRepository();
        $this->FacturaCompraRepository = new FacturaCompraRepository();
        $this->PagoValidator = new PagoValidator();
        $this->CuentasRepository = new CuentasRepository();
        //utils
        $this->FlexioAssets = new FlexioAssets();
        $this->FlexioSession = new FlexioSession();
        $this->Toast = new Toast();
    }

    public function getPadreModulo()
    {
        $request = Illuminate\Http\Request::createFromGlobals();
        $moduelo_padre = $this->session->userdata('modulo_padre');
        if($request->has('contrato')){
            $this->modulo_padre = 'ventas';
            return 'ventas';
        }else if($request->has('subcontrato')){
            $this->modulo_padre = 'compras';
            return 'compras';
        }else if($moduelo_padre == 'contratos'){
            $this->modulo_padre = 'contratos';
            return 'contratos';
        }

        $this->modulo_padre = $this->session->userdata('modulo_padre');
        return $this->modulo_padre;
    }

    public function listar()
    {
        $data = array();
        if (!$this->auth->has_permission('acceso')) {
            $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud');
            $this->session->set_flashdata('mensaje', $mensaje);
        }


        $this->_Css();
        $this->_js();
        $this->assets->agregar_js(array(
            'public/assets/js/modules/pagos/listar.js',
        ));

        $breadcrumb = array('titulo' => '<i class="fa fa-shopping-cart"></i> Pagos',
            'ruta' => array(
                0 => array(
                    'nombre' => 'Compras',
                    'activo' => false,
                ),
                1 => array(
                    'nombre' => '<b>Pagos</b>',
                    'activo' => true,
                ),
            ),
            'menu' => array(
                'nombre' => 'Crear',
                'url' => 'pagos/crear',
                'opciones' => array(),
            ),
        );

        if (!is_null($this->session->flashdata('mensaje'))) {
            $mensaje = json_encode($this->session->flashdata('mensaje'));
        } else {
            $mensaje = '';
        }
        $this->assets->agregar_var_js(array(
            'flexio_mensaje' => Flexio\Library\Toast::getStoreFlashdata(),
            'numero_documento' => !empty($_POST['numero_documento']) ? $_POST['numero_documento'] : ''
        ));

        //$data['proveedores'] = Proveedores_orm::deEmpresa($this->empresa_id)->get(array('id', 'nombre'));
        $data['proveedores'] = collect([]);
        $data['info']['categorias'] = Proveedores_categorias_orm
                        ::where('id_empresa', '=', $this->empresa_id)
                        ->where('estado', '=', 19)
                        ->orderBy('nombre', 'ASC')->get();
        $data['etapas'] = Pago_catalogos_orm::where('tipo', 'etapa3')->get(array('etiqueta', 'valor'));
        $data['formas_pago'] = Pago_catalogos_orm::where('tipo', 'pago')->get(array('id', 'etiqueta', 'valor'));
        $data['bancos'] = Bancos_orm::get(array('id', 'nombre'));
        $data['modulo_padre'] = $this->getPadreModulo();

        $breadcrumb['menu']['opciones']['#exportarListaPagos'] = 'Exportar';
        $breadcrumb['menu']['opciones']['#generarMultiplesACH'] = 'Generar ACH';
        $breadcrumb['menu']['opciones']['#generarAplicadoMultiple'] = 'Aplicar pago';
        //$breadcrumb["menu"]["opciones"]["#pagarMultiplesColaboradores"] = "Pagar colaborares";

        $this->template->agregar_titulo_header('Listado de Pagos');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }

    private function _filtrarPagos($pagos)
    {
        /*
          paramentos de busqueda aqui
         */
        $desde = $this->input->post('desde', true);
        $hasta = $this->input->post('hasta', true);
        $proveedor = $this->input->post('proveedor', true);
        $estado = $this->input->post('estado', true);
        $montoMin = $this->input->post('montoMin', true);
        $montoMax = $this->input->post('montoMax', true);
        $formaPago = $this->input->post('formaPago', true);
        $tipo = $this->input->post('tipo', true);
        if($this->getPadreModulo()=="contratos"){
            $tipo= "contratos";
        }
        //$banco      = $this->input->post('banco',TRUE);
        $numeroDocumento = $this->input->post('numeroDocumento', true);
        $pedido_id = $this->input->post('pedido_id', true);
        $caja_id = $this->input->post('caja_id', true);
        $codigo = $this->input->post('codigo', true);

        $campo = $this->input->post('campo', true);
        // $subcontrato_id = $this->input->post('subcontrato_id',TRUE);
        $categoria = $this->input->post('categoria_proveedor', true) != "undefined" ? $this->input->post('categoria_proveedor', true) : '' ;
        //subpanels
        $orden_compra_id = $this->input->post('orden_compra_id', true);
        if (!empty($orden_compra_id)) {
            $pagos->deOrdenDeCompra($orden_compra_id);
        }

        $factura_compra_id = $this->input->post('factura_compra_id', true);
        if (!empty($factura_compra_id)) {
            $pagos->deFacturaDeCompra($factura_compra_id);
        }

        if (!empty($desde)) {
            $pagos->deFechaDesde($desde);
        }
        if (!empty($hasta)) {
            $pagos->deFechaHasta($hasta);
        }
        if (!empty($proveedor)) {
            $proveedor = explode("|", $proveedor);
            $pagos->deProveedor(count($proveedor) == 2 ? $proveedor[1] : $proveedor[0] );
        }
        if (!empty($estado)) {
            $pagos->deEstado($estado);
        }
        if (!empty($montoMin)) {
            $pagos->deMontoMin($montoMin);
        }
        if (!empty($montoMax)) {
            $pagos->deMontoMax($montoMax);
        }
        if (!empty($formaPago)) {
            $pagos->deFormaPago($formaPago);
        }
        if (!empty($tipo)) {
            $pagos->deTipo($tipo);
        }
        if (!empty($caja_id)) {
            $pagos->deCaja($caja_id);
        }
        if (!empty($pedido_id)) {
            $pagos->dePedido($pedido_id);
        }
        if (!empty($numeroDocumento)) {
            $pagos->deDocumentoPago($numeroDocumento);
        }
        if (!empty($campo)) {
            $pagos->deFiltro($campo);
        }
        if (!empty($categoria)) {
            $pagos->deCategoria($categoria);
        }
        if (!empty($codigo)) {
            $pagos->deCodigo($codigo);
        }
        //if(!empty($subcontrato_id))$pagos->deSubContrato($subcontrato_id);
    }

    public function ajax_exportar()
    {
        $clause = [];
        $clause['empresa_id'] = $this->empresa_id;
        $clause['uuid_pagos'] = $this->input->post('uuid_pagos', true);

        $csv = \League\Csv\Writer::createFromFileObject(new \SplTempFileObject());
        $csv->insertOne([utf8_decode('Número de pago'), 'Fecha', 'Proveedor', 'No. Documento', 'Forma de Pago', 'Banco', 'Estado', 'Monto']);
        $csv->insertAll($this->pagosRep->getCollectionExportar($this->pagosRep->get($clause)));

        $csv->output('pagos.csv');
        exit;
    }

    public function ajax_listar()
    {
        if (!$this->input->is_ajax_request()) {
            return false;
        }
        $pagos = Pagos_orm::deEmpresa($this->empresa_id);
        //filtros de centros contables del usuario
        $centros = $this->FlexioSession->usuarioCentrosContables();
        if (!in_array('todos', $centros)) {
            $pagos->whereHas('facturas', function ($factura) use ($centros) {
                $factura->whereIn('faccom_facturas.centro_contable_id', $centros);
            });
        }

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
                $link_planilla = '';
                if ($row->formulario == 'planilla') {
                    $planilla = $row->planillas->first();
                    $codido_planilla = $planilla->codigo;
                    $uuid_colaborador = !empty($row->colaborador) && !empty($row->colaborador->uuid_colaborador) ? $row->colaborador->uuid_colaborador : '';
                    $nombre = !empty($row->colaborador) && !empty($row->colaborador->nombre) ? $row->colaborador->nombre : '';
                    $apellido = !empty($row->colaborador) && !empty($row->colaborador->apellido) ? $row->colaborador->apellido : '';

                    $link_planilla = '<a href="'.base_url('planilla/ver/'.$planilla->uuid_planilla).'" class="link">'.$codido_planilla.'</a>';
                    $link_colaborador = '<a href="'.base_url('colaboradores/ver/'.$uuid_colaborador).'" class="link">'.$nombre.' '.$apellido.'</a>';
                } elseif ($row->formulario == 'pago_extraordinario') {
                    $extraordinario = $row->pagos_extraordinarios->first();
                    $uuid_colaborador = !empty($row->colaborador) && !empty($row->colaborador->uuid_colaborador) ? $row->colaborador->uuid_colaborador : '';
                    $nombre = !empty($row->colaborador) && !empty($row->colaborador->nombre) ? $row->colaborador->nombre : '';
                    $apellido = !empty($row->colaborador) && !empty($row->colaborador->apellido) ? $row->colaborador->apellido : '';
                    $codido_pago_extraordinario = $extraordinario->numero;

                    $link_planilla = '<a href="'.base_url('comisiones/ver/'.$extraordinario->uuid_comision).'" class="link">'.$codido_pago_extraordinario.'</a>';
                    $link_colaborador = '<a href="'.base_url('colaboradores/ver/'.$uuid_colaborador).'" class="link">'.$nombre.' '.$apellido.'</a>';
                } elseif ($row->formulario == 'transferencia') {
                    $transferencia = $row->transferencias->first();
                    $codigo = $transferencia->numero;
                    $link_planilla = '<a href="'.base_url('cajas/transferir_detalle/'.$transferencia->caja->uuid_caja.'/'.$transferencia->id).'" class="link">'.$codigo.'</a>';
                    $link_colaborador = $transferencia->caja->responsable->nombre.' '.$transferencia->caja->responsable->apellido;
                } elseif ($row->empezable_type == 'Flexio\Modulo\Anticipos\Models\Anticipo') {
                    $link_planilla = '<a href="'.$row->empezable->enlace.'" class="link">'.$row->empezable->codigo.'</a>';
                } elseif ($row->formulario == 'movimiento_monetario') {
                    $retiro = $row->retiros->first();
                    $link_planilla = '<a href="'.base_url('movimiento_monetario/ver_retiros/'.bin2hex($retiro->uuid_retiro_dinero)).'" class="link">'.$retiro->codigo.'</a>';
                }
                $hidden_options = '';
                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'.$row->uuid_pago.'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                if ($row->formulario != 'planilla' && $row->formulario != 'pago_extraordinario' /* && $row->formulario != 'movimiento_monetario' */) {
                    if ($row->formulario != 'transferencia' && $row->formulario != 'movimiento_monetario') {
                        $hidden_options .= '<a href="'.base_url('pagos/ver/'.$row->uuid_pago).'" data-id="'.$row->uuid_pago.'" class="btn btn-block btn-outline btn-success">Ver Detalle</a>';
                    }

                    if ($row->estado == 'por_aprobar') {
                        $hidden_options .= '<a href="#" data-id="'.$row->uuid_pago.'" data-pagoid="'.$row->id.'"  class="btn btn-block btn-outline btn-success aprobarPago" >Aprobar pago</a>';
                    } elseif ($row->estado == 'por_aplicar' && trim($this->listaPago->metodo_pago($row->metodo_pago)) != 'Cheque') {
                        $hidden_options .= '<a href="#" data-id="'.$row->uuid_pago.'" data-pagoid="'.$row->id.'" class="btn btn-block btn-outline btn-success aplicarPago" id="#aplicarPago">Aplicar pago</a>';
                    } elseif ($row->estado == 'por_aplicar' and trim($this->listaPago->metodo_pago($row->metodo_pago)) == 'Cheque') {
                        $hidden_options .= '<a href="'.base_url('cheques/crear/pago'.$row->uuid_pago).'" class="btn btn-block btn-outline btn-success">Imprimir Cheque</a>';
                    } elseif ($row->estado == 'cheque_en_transito' and trim($this->listaPago->metodo_pago($row->metodo_pago)) == 'Cheque') {
                        $hidden_options .= '<a href="#" data-id="'.$row->uuid_pago.'" data-pagoid="'.$row->id.'" class="btn btn-block btn-outline btn-success aplicarPago" id="#aplicarPago">Aplicar pago</a>';
                    }

                    if ($row->estado != 'anulado') {
                        $hidden_options .= '<a href="#" data-id="'.$row->uuid_pago.'" data-pagoid="'.$row->id.'" class="btn btn-block btn-outline btn-success anularPago" id="#anularPago">Anular pago</a>';
                    }
                } else {
                    if ($row->estado == 'por_aplicar') {
                        $hidden_options .= '<a href="#" data-tipo="'.$row->formulario.'" data-id="'.$row->uuid_pago.'" class="btn btn-block btn-outline btn-success pagarColaborador" id="#pagarColaborador">Aplicar</a>';
                        $hidden_options .= '<a href="#" data-tipo="'.$row->formulario.'" data-id="'.$row->uuid_pago.'" class="btn btn-block btn-outline btn-success anularColaborador" id="#anularColaborador">Anular</a>';
                    } else {
                        $hidden_options .= 'Este pago ya esta concluido.';
                    }
                }
                if ($row->formulario != 'transferencia' && $row->formulario != 'movimiento_monetario') {
                    $hidden_options .= '<a  href="'.base_url('pagos/historial/'.$row->uuid_pago).'"   data-id="'.$row->id.'" class="btn btn-block btn-outline btn-success">Ver bit&aacute;cora</a>';
                }

                $proveedor = $row->proveedor;
                $etapa = $row->catalogo_estado;

                if ($row->formulario != 'planilla' && count($row->facturas)) {
                    $facturas = $row->facturas->filter(function ($value, $key) {
                        return (int) $value->pivot->monto_pagado > 0;
                    });
                    $no_documento = $facturas->implode('codigo_enlace_v2', ', ');
                } else {
                    $no_documento = $link_planilla;
                }

                $response->rows[$i]['id'] = $row->uuid_pago;
                $response->rows[$i]['cell'] = array(
                    $row->uuid_pago,
                    ($row->formulario != 'transferencia' && $row->formulario != 'planilla' && $row->formulario != 'pago_extraordinario') ? ('<a class="link" href="'.base_url('pagos/ver/'.$row->uuid_pago).'" style="color:blue;">'.$row->codigo.'</a>') : $row->codigo,
                    ($row->formulario != 'transferencia' && $row->formulario != 'planilla' && $row->formulario != 'pago_extraordinario') ? ('<a class="link">'.$proveedor->nombre.'</a>') : $link_colaborador,
                    $row->fecha_pago,
                    '<label class="'.$this->listaPago->color_monto($row->estado).'">$'.number_format($row->monto_pagado, 2).'</label>',
                    $no_documento,
                    $this->listaPago->metodo_pago($row->metodo_pago),
                    //$this->listaPago->banco($row->metodo_pago),
                    $this->listaPago->color_estado($etapa->etiqueta, $etapa->valor),
                    $etapa->etiqueta,
                    $row->id,
                    $link_option,
                    $hidden_options,
                );
            }
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($response))->_display();
        exit;
    }

    public function ocultotabla($uuid_orden_venta = null)
    {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/pagos/funtions.js',
            'public/assets/js/modules/pagos/tabla.js',
        ));
        if (!empty($uuid_orden_venta)) {
            if (preg_match('/pedidos/i', $this->router->fetch_class())) {
                $this->assets->agregar_var_js(array(
                    'pedidos_id' => $uuid_orden_venta,
                ));
            } elseif (is_array($uuid_orden_venta)) {
                $this->assets->agregar_var_js(array(
                    'campo' => collect($uuid_orden_venta),
                ));
            } else {
                $this->assets->agregar_var_js(array(
                    'uuid_orden_venta' => $uuid_orden_venta,
                ));
            }
        }

        $this->load->view('tabla');
    }

    public function ocultotablaV2($sp_string_var = '')
    {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/pagos/funtions.js',
            'public/assets/js/modules/pagos/tabla.js',
        ));

        $sp_array_var = explode('=', $sp_string_var);
        if (count($sp_array_var) == 2) {
            $this->assets->agregar_var_js(array(
                $sp_array_var[0] => $sp_array_var[1],
            ));
        }

        $this->load->view('tabla');
    }

    public function ocultotablaProveedores($uuid_proveedor = null)
    {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/pagos/funtions.js',
            'public/assets/js/modules/pagos/tabla.js',
        ));

        if (!empty($uuid_proveedor)) {
            $this->assets->agregar_var_js(array(
                'uuid_proveedor' => $uuid_proveedor,
            ));
        }

        $this->load->view('tabla');
    }

    public function ocultotablaOrdenesCompras($orden_compra_id = null)
    {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/pagos/funtions.js',
            'public/assets/js/modules/pagos/tabla.js',
        ));

        if (!empty($orden_compra_id)) {
            $this->assets->agregar_var_js(array(
                'orden_compra_id' => $orden_compra_id,
            ));
        }

        $this->load->view('tabla');
    }

    public function ocultotablaFacturasCompras($factura_compra_id = null)
    {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/pagos/funtions.js',
            'public/assets/js/modules/pagos/tabla.js',
        ));

        if (!empty($factura_compra_id)) {
            $this->assets->agregar_var_js(array(
                'factura_compra_id' => $factura_compra_id,
            ));
        }

        $this->load->view('tabla');
    }

    public function crear($foreing_key = '')
    {
        if (preg_match('/proveedor/', $foreing_key)) {
            $empezable_id = str_replace('proveedor', '', $foreing_key);
            $empezable_type = 'proveedor';
        } elseif (preg_match('/facturacompra/', $foreing_key)) {
            $validar_id = str_replace('facturacompra', '', $foreing_key);

            if (is_numeric($validar_id)) {
                $factura = $this->FacturaCompraRepository->find($validar_id);
            } else {
                $factura = $this->FacturaCompraRepository->findByUuid($validar_id);
            }
            $empezable_id = $factura->id;
            $empezable_type = 'factura';
        }

        $empezable = collect([
            'type' => isset($empezable_type) ? $empezable_type : '',
            'id' => isset($empezable_id) ? $empezable_id : '',
        ]);

        //permisos
        $acceso = $this->auth->has_permission('acceso');
        $this->Toast->runVerifyPermission($acceso);

        //assets
        $this->FlexioAssets->run(); //css y js generales
        $this->FlexioAssets->add('vars', [
            'vista' => 'crear',
            'acceso' => $acceso ? 1 : 0,
            'empezable' => $empezable,
            'politica_transaccion' => collect([]),
        ]);

        //breadcrumb
        $breadcrumb = [
            'titulo' => '<i class="fa fa-shopping-cart"></i> Pago: Crear ',
        ];

        //render
        $this->template->agregar_titulo_header('Pagos');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido([]);
        $this->template->visualizar();
    }

    public function ver($uuid = null)
    {
        $data = [];

        //permisos
        $acceso = $this->auth->has_permission('acceso', 'pagos/ver/(:any)');
        $this->Toast->runVerifyPermission($acceso);

        //Cargo el registro
        $registro = $this->pagosRep->findByUuid($uuid);
        $registro->load('comentario_timeline');

        $empezable = collect([
            'type' => $registro->empezable_type,
            "{$registro->empezable_type}s" => ($registro->empezable_type !== false) ? [0 => ['id' => $registro->empezable_id, 'nombre' => (new $registro->empezable_type_model())->find($registro->empezable_id)->codigo]] : [],
            'id' => $registro->empezable_id,
        ]);
        //assets
        $this->FlexioAssets->run(); //css y js generales
        $this->FlexioAssets->add('vars', [
            'vista' => 'editar',
            'acceso' => $acceso ? 1 : 0,
            'pago' => $this->pagosRep->getColletionPago($registro),
            'politica_transaccion' => $registro->politica(),
            'empezable' => $empezable,
        ]);

        //breadcrumb
        $breadcrumb = [
            'titulo' => '<i class="fa fa-shopping-cart"></i> Pago: '.$registro->codigo,
            'menu' => [
                'nombre' => 'Acción',
                'url' => '#',
                'opciones' => array(),
            ],
        ];

        if ($registro->estado == 'aplicado') {
            $breadcrumb['menu']['opciones']['pagos/imprimir/'.$registro->uuid_pago] = 'Imprimir';
        }

        $breadcrumb['menu']['opciones']['pagos/historial/'.$registro->uuid_pago] = 'Ver bit&aacute;cora';
        //render
        $this->template->agregar_titulo_header('Pagos');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    public function imprimir($uuid = null)
    {
        if ($uuid == null) {
            return false;
        }

        $pago = $this->pagosRep->findByUuid($uuid);
        $pago->load('empresa');
        $history = $this->pagosRep->getLastEstadoHistory($pago->id);
        $dompdf = new Dompdf();
        $data = ['pago' => $pago, 'history' => $history];

        $html = $this->load->view('pdf/pago', $data, true);
        //echo '<pre>'. $html . '</pre>'; die;
        //render
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream($pago->codigo);

        exit();
    }

    public function ajax_aplicar_pagos()
    {
        // Just Allow ajax request
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        Capsule::beginTransaction();

        try {
            $pagos = $this->input->post('pago');
            $success_false = $success_true = [];
            foreach ($pagos as $pago) {
                $pago = $this->pagosRep->findByUuid($pago);

                if ($pago->formulario != 'planilla') {
                    if ($this->PagoValidator->_sePuedeAplicarPago($pago)) {
                        if ($pago->metodo_pago[0]->tipo_pago == 'aplicar_credito' and !$this->proveedoresRep->restar_credito($pago->proveedor_id, $pago->monto_pagado, $pago)) {
                            $success_false[] = 1;
                        } else {
                            $pago->estado = 'aplicado';
                            $pago->save();
                            /*
                             * Logica de afectacion contable:
                             *  Compras -> Pagos varios metodos de pago
                             */
                            $this->PagosProveedor->haceTransaccion($pago);
                            $success_true[] = 1;
                        }
                    } else {
                        $success_false[] = 1;
                    }
                } else {
                    $pago->estado = 'aplicado';
                    $pago->save();
                    $success_true[] = 1;

                    $planilla = $this->planillaRepository->find($pago->depositable_id);  //30: Parcial, 31: Completo
                    $planilla->estado_id = ($planilla->pagadas_colaboradores + 1 < $planilla->total_colaboradores) ? 30 : 31;
                    $planilla->pagadas_colaboradores = $planilla->pagadas_colaboradores + 1;
                    $planilla->save();
                }
            }
        } catch (\Exception $e) {

            Capsule::rollback();
            echo json_encode(array(
                'response' => 0,
                'mensaje' => $e->getMessage(),
            ));
            exit;
        }
        Capsule::commit();

        if ((int) count($success_false) > 0 && count($success_true) > 0) {
            echo json_encode(array(
                'response' => 2,
                'mensaje' => 'Algunos pagos no se actualizaron, no cumplen condiciones.',
            ));
            exit;
        }
        if (count($success_true) == 0) {
            Capsule::rollback();
            echo json_encode(array(
                'response' => 0,
                'mensaje' => 'Hubo un error tratando de cambiar el estado al pago.',
            ));
            exit;
        }
        echo json_encode(array(
            'response' => 1,
            'mensaje' => 'Se ha aplicado el estado aplicado satisfactoriamente.',
        ));

        exit;
    }

    public function registrar_pago($uuid = null)
    {
        //dd($this->pagoGuardar);
        $acceso = 1;
        $mensaje = array();
        if (!$this->auth->has_permission('acceso', 'pagos/registrar_pago/(:any)')) {
            $acceso = 0;
            $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud', 'clase' => 'alert-danger');
        }

        $this->_Css();
        $this->assets->agregar_css(array(
            'public/assets/css/modules/stylesheets/animacion.css',
        ));
        $this->_js();
        $this->assets->agregar_js(array(
            'public/assets/js/modules/pagos/service.pago.js',
            'public/assets/js/modules/pagos/registarCobro.controller.js',
        ));

        $facturaObj = new Buscar(new Factura_orm(), 'uuid_factura');
        $factura = $facturaObj->findByUuid($uuid);
        if (is_null($uuid) || is_null($factura)) {
            $mensaje = array('estado' => 500, 'mensaje' => '<strong>¡Error!</strong> Su solicitud no fue procesada');
            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('facturas_compras/listar'));
        }

        $data = array();
        $clause = array('empresa_id' => $this->empresa_id);
        $facturas = Factura_orm::with('proveedor')->where(function ($query) use ($clause) {
            $query->where('empresa_id', '=', $clause['empresa_id']);
            $query->whereNotIn('estado', array('anulada'));
        })->get();
        $this->assets->agregar_var_js(array(
            'vista' => 'registrar_pago',
            'acceso' => $acceso == 0 ? $acceso : $acceso,
            'uuid_factura' => $factura->uuid_factura,
        ));

        $data['facturas'] = $facturas->toArray();
        //$data['uuid_factura'] = $factura->uuid_factura;
        $data['mensaje'] = $mensaje;
        $breadcrumb = array(
            'titulo' => '<i class="fa fa-line-chart"></i> Registar Cobro: Factura '.$factura->codigo,
        );

        $this->template->agregar_titulo_header('Crear Pago');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    public function ajax_get_empezables()
    {
        if(!$this->input->is_ajax_request()){
            return false;
        }

        $response = [];
        $request = array_merge($this->input->post(), $this->input->get(), ['empresa' => $this->empresa_id]);
        if(isset($request['campo']) && !empty($request['campo'])){$request = array_merge($request, $request['campo']);}

        if(!empty($request['empezable_type'])){
            $method = (isset($request['id']) && !empty($request['id'])) ? 'find' : 'get';
            if($request['empezable_type'] == 'factura'){
                $result = \Flexio\Modulo\FacturasCompras\Models\FacturaCompra::where(function($query) use ($request){
                    $query->deFiltro($request);
                })->take(10)->$method($method == 'find' ? $request['id'] : ['*']);
            }elseif ($request['empezable_type'] == 'proveedor') {
                $result = \Flexio\Modulo\Proveedores\Models\Proveedores::where(function($query) use ($request){
                    $query->deFiltro($request);
                })
                ->select('pro_proveedores.*')
                ->join("faccom_facturas", function ($join) {
                    $join->on("faccom_facturas.proveedor_id", "=", "pro_proveedores.id");
                    $join->whereIn("faccom_facturas.estado_id", [14, 15]);
                })
                ->take(10)->$method($method == 'find' ? $request['id'] : ['*']);
            }elseif ($request['empezable_type'] == 'subcontrato') {
                $result = \Flexio\Modulo\SubContratos\Models\SubContrato::where(function($query) use ($request){
                    $query->deFiltro($request);
                })->take(10)->$method($method == 'find' ? $request['id'] : ['*']);
            }elseif ($request['empezable_type'] == 'anticipo') {
                $result = \Flexio\Modulo\Anticipos\Models\Anticipo::where(function($query) use ($request){
                    $query->deFiltro($request);
                })->take(10)->$method($method == 'find' ? $request['id'] : ['*']);
            }

            if($request['empezable_type'] == 'proveedor'){
                $response = $method == 'find' ? ['id' => $result->id, 'nombre' => $result->nombre] : $result->map(function($row){
                    return ['id' => $row->id, 'text' => $row->nombre];
                });
            }else{
                $aux = $method == 'find' && count($result->proveedor) ? $result->proveedor->nombre : '';
                $response = $method == 'find' ? ['id' => $result->id, 'nombre' => $result->codigo.' '.$aux] : $result->map(function($row){
                    $aux = count($row->proveedor) ? $row->proveedor->nombre : '';
                    return ['id' => $row->id, 'text' => $row->codigo.' '.$aux];
                });
            }

        }

        echo json_encode($response);
        exit;
    }

    public function ajax_get_empezable()
    {
        if(!$this->input->is_ajax_request()){
            return false;
        }

        $response = [];
        $request = $this->input->post();

        if(!empty($request['type'])){
            if($request['type'] == 'factura'){
                $result = \Flexio\Modulo\FacturasCompras\Models\FacturaCompra::where(function($query) use ($request){
                    $query->where('id', $request['id']);
                })->get();
                $response = $this->FacturaCompraRepository->getCollectionFacturasPago($result);
            }elseif ($request['type'] == 'proveedor') {
                $result = \Flexio\Modulo\Proveedores\Models\Proveedores::where(function($query) use ($request){
                    $query->where('id', $request['id']);
                })->get();
                $response = $this->proveedoresRep->getCollectionProveedoresPago($result);
            }elseif ($request['type'] == 'subcontrato') {
                $result = \Flexio\Modulo\SubContratos\Models\SubContrato::where(function($query) use ($request){
                    $query->where('id', $request['id']);
                })->get();
                $response = $this->subcontratosRep->getCollectionSubcontratosPago($result);
            }elseif ($request['type'] == 'retenido') {
                $result = \Flexio\Modulo\SubContratos\Models\SubContrato::where(function($query) use ($request){
                    $query->where('id', $request['id']);
                })->get();
                $response = $this->subcontratosRep->getCollectionSubcontratosPagoRetenido($result);
            }elseif ($request['type'] == 'anticipo') {
                $result = \Flexio\Modulo\Anticipos\Models\Anticipo::where(function($query) use ($request){
                    $query->where('id', $request['id']);
                })->get();
                $collectionsAnticipos = new \Flexio\Modulo\Anticipos\Collections\PagoCollection;
                $response = $collectionsAnticipos->anticipos_para_pagos($result);
            }

        }

        echo json_encode(count($response) ? $response[0] : []);
        exit;
    }

    public function ocultoformulario()
    {
        $clause = ['empresa_id' => $this->empresa_id, 'transaccionales' => true, 'conItems' => true, 'modulo' => 'pagos', 'por_pagar' => true];
        $catalogos = $this->CatalogoRepository->get($clause);

        $this->FlexioAssets->add('js', ['public/resources/compile/modulos/pagos/formulario.js']);
        $this->FlexioAssets->add('vars', [
            'metodos_pago' => $catalogos->filter(function ($metodo_pago) {
                return $metodo_pago->tipo == 'metodo_pago';
            }),
            'bancos' => $this->BancosRepository->get(),
            'estados' => $catalogos->filter(function ($estado) {
                return $estado->tipo == 'etapa';
            }),
            'tipos_pago' => $catalogos->filter(function ($tipo_pago) {
                return $tipo_pago->tipo == 'tipo_pago';
            })
        ]);

        $this->load->view('formulario');
    }

    //en la edicion de pagos solo se puede cambiar el estado
    private function _setPagoFromPost($pago, $post)
    {
        $pago->estado = isset($post['campo']['estado']) ? $post['campo']['estado'] : 'por_aplicar';
    }

    public function guardar()
    {
        if (!empty($_POST)) {
            $formGuardar = new Flexio\Modulo\Pagos\FormRequest\GuardarPagos();
            try {
                $post = $this->input->post();
                $post['campo']['empresa_id'] = $this->empresa_id;
                $post['campo']['empezable_type'] = $post['empezable_type'];
                $post['campo']['empezable_id'] = $post['empezable_id'];
                $pago = $formGuardar->save($post);
            } catch (\Exception $e) {
                log_message('error', ' __METHOD__  ->  , Linea:  __LINE__  --> '.$e->getMessage()."\r\n");
                $this->Toast->setUrl('pagos/listar')->run('exception', [$e->getMessage()]);
            }

            if (!is_null($pago)) {
                $this->Toast->run('success', [$pago->codigo]);
            } else {
                $this->Toast->run('error');
            }

            redirect(base_url('pagos/listar'));
        }
    }

    public function ajax_cambiar_estado()
    {
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        $formGuardar = new Flexio\Modulo\Pagos\FormRequest\GuardarPagos();
        try {
            $post = $this->input->post();
             if(!empty($post['pago']) && isset($post['multiple'])){
                  foreach($post['pago'] as $valores){
                    $campos["campo"] = $valores;
                    $pago = $formGuardar->save($campos);
                 }
            }else{
              $pago = $formGuardar->save($post);
            }
        } catch (\Exception $e) {
            log_message('error', ' __METHOD__  ->  , Linea:  __LINE__  --> '.$e->getMessage()."\r\n");
            echo json_encode(array(
                'response' => false,
                'mensaje' => $e->getMessage(),
            ));
            exit;
        }

        echo json_encode(array(
            'response' => true,
            'mensaje' => 'Se ha actualizado el estado satisfactoriamente.',
        ));

        exit;
    }

    public function ajax_pagar_retenido()
    {
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        $formGuardar = new Flexio\Modulo\Pagos\FormRequest\GuardarPagos();
        try {
            $post = $this->input->post();
            $suma = 0;
            if(isset($post['campo']) && !empty($post['campo']) && isset($post['items']) && !empty($post['items'])){
                $total = array_reduce($post['items'], function($suma, $factura){
                    return $suma += str_replace(",", "", $factura['monto_pagado']);
                });
                $post['campo']['monto_pagado'] = $total;
                $post['campo']['total_pagado'] = $total;
                $post['campo']['empresa_id'] = $this->empresa_id;
                if(isset($post['metodo_pago']) && !empty($post['metodo_pago'])){$post['metodo_pago'][0]['total_pagado'] = $total;}
            }
            $pago = $formGuardar->save($post);
        } catch (\Exception $e) {
            log_message('error', ' __METHOD__  ->  , Linea:  __LINE__  --> '.$e->getMessage()."\r\n");
            echo json_encode(array(
                'response' => false,
                'mensaje' => $e->getMessage(),
            ));
            exit;
        }

        echo json_encode(array(
            'response' => true,
            'mensaje' => 'Se ha realizado la solicitud de pago satisfactoriamente.',
        ));

        exit;
    }

    public function ajax_anularpago_colaborador()
    {
        // Just Allow ajax request
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        Capsule::beginTransaction();

        try {
            $uuid_pago = $this->input->post('uuid_pago');
            $tipo_formulario = $this->input->post('tipo_formulario');
            $pagoObj = new Buscar(new Pagos_orm(), 'uuid_pago');
            $pago = $pagoObj->findByUuid($uuid_pago);

            if ($tipo_formulario == 'planilla') {
                $PagadaColaborador = $this->PagadasRepository->findBy(['planilla_id' => $pago->depositable_id, 'colaborador_id' => $pago->proveedor_id]);
                $PagadaColaborador->load('planilla');
                $PagadaColaborador->estado_pago = 'anulado';
                $PagadaColaborador->save();

                $this->PagosPlanilla->deshaceTransaccion($PagadaColaborador->planilla, $pago->proveedor_id);  //En el futuro ya...llegó el futuro
                $planilla = $this->planillaRepository->find($pago->depositable_id);  //30: Parcial, 31: Completo
                $planilla->estado_id = (int) 30;
                $planilla->save();
            } elseif ($tipo_formulario == 'pago_extraordinario') {
                $ComisionColaborador = $this->ComisionColaboradorRepository->findBy(['comision_id' => $pago->depositable_id, 'colaborador_id' => $pago->proveedor_id]);
                $this->PagosComisiones->deshaceTransaccion($ComisionColaborador->pago_extraordinario, $pago->proveedor_id);
                $pago_extraordinario = $this->ComisionesRepository->find($pago->depositable_id);  //30: Parcial
                $pago_extraordinario->estado_id = (int) 30;
                $pago_extraordinario->save();
            }

            $pago->estado = 'anulado';
            $pago->save();
        } catch (ValidationException $e) {
            Capsule::rollback();
            echo json_encode(array(
                'response' => false,
                'mensaje' => 'Hubo un error tratando de cambiar el estado al pago.',
            ));
            exit;
        }
        Capsule::commit();

        echo json_encode(array(
            'response' => true,
            'mensaje' => 'Se ha confirmado el pago al colaborador satisfactoriamente.',
        ));

        exit;
    }

    public function ajax_anular_pago()
    {

        // Just Allow ajax request
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        Capsule::beginTransaction();

        try {
            $uuid_pago = $this->input->post('uuid_pago');
            $pagoObj = new Buscar(new Pagos_orm(), 'uuid_pago');
            $pago = $pagoObj->findByUuid($uuid_pago);

            $this->PagosProveedor->deshaceTransaccion($pago);

            $pago->estado = 'anulado';
            $pago->save();
        } catch (ValidationException $e) {
            Capsule::rollback();
            echo json_encode(array(
                'response' => false,
                'mensaje' => 'Hubo un error tratando de cambiar el estado al pago.',
            ));
            exit;
        }
        Capsule::commit();

        echo json_encode(array(
            'response' => true,
            'mensaje' => 'Se ha actualizado el estado satisfactoriamente.',
        ));

        exit;
    }

    public function ajax_aplicar_pago()
    {

        // Just Allow ajax request
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        Capsule::beginTransaction();

        try {
            $uuid_pago = $this->input->post('uuid_pago');
            $pagoObj = new Buscar(new Pagos_orm(), 'uuid_pago');
            $pago = $pagoObj->findByUuid($uuid_pago);
            $success = false;

            if ($this->PagoValidator->_sePuedeAplicarPago($pago)) {
                if ($pago->metodo_pago[0]->tipo_pago == 'aplicar_credito' and !$this->proveedoresRep->restar_credito($pago->proveedor_id, $pago->monto_pagado, $pago)) {
                    $success = false;
                } else {
                    $pago->estado = 'aplicado';
                    $pago->save();
                    /*
                     * Logica de afectacion contable:
                     *  Compras -> Pagos varios metodos de pago
                     */
                    $this->PagosProveedor->haceTransaccion($pago);
                    $success = true;
                }
            }
            if ($success == false) {
                Capsule::rollback();
                echo json_encode(array(
                    'response' => false,
                    'mensaje' => 'Hubo un error tratando de cambiar el estado al pago.',
                ));
                exit;
            }
        } catch (ValidationException $e) {
            Capsule::rollback();
            echo json_encode(array(
                'response' => false,
                'mensaje' => 'Hubo un error tratando de cambiar el estado al pago.',
            ));
            exit;
        }
        Capsule::commit();

        echo json_encode(array(
            'response' => true,
            'mensaje' => 'Se ha actualizado el estado satisfactoriamente.',
        ));

        exit;
    }

//Solo para pagas que no sean de tipo Planilla
    public function ajax_aprobar_pago()
    {

        // Just Allow ajax request
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        Capsule::beginTransaction();

        try {
            $uuid_pago = $this->input->post('uuid_pago');
            $pagoObj = new Buscar(new Pagos_orm(), 'uuid_pago');
            $pago = $pagoObj->findByUuid($uuid_pago);
            //Si a un pago de tipo cheque se cambia de estado a un estado por aplicar este debe Generar un cheque estado por Imprimir
            if (isset($pago->metodo_pago[0]->tipo_pago) && $pago->metodo_pago[0]->tipo_pago == 'cheque') {
                $array_cheque = [];
                $array_cheque['monto'] = $pago->monto_pagado;
                $array_cheque['empresa_id'] = $this->empresa_id;
                $array_cheque['pago_id'] = $pago->id;
                $array_cheque['fecha_cheque'] = date('Y-m-d H:i:s');
                $array_cheque['estado_id'] = 1;  //se crea el cheque por imprimir
                $this->chequesRepository->crear($array_cheque);
            }

            $pago->estado = 'por_aplicar';
            $pago->save();
        } catch (ValidationException $e) {
            Capsule::rollback();
            echo json_encode(array(
                'response' => false,
                'mensaje' => 'Hubo un error tratando de cambiar el estado al pago.',
            ));
            exit;
        }
        Capsule::commit();

        echo json_encode(array(
            'response' => true,
            'mensaje' => 'Se ha actualizado el estado satisfactoriamente.',
        ));

        exit;
    }

    public function ajax_pagar_colaborador()
    {
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        Capsule::beginTransaction();

        try {
            $tipo_formulario = $this->input->post('tipo_formulario');
            $uuid_pago = $this->input->post('uuid_pago');
            $pagoObj = new Buscar(new Pagos_orm(), 'uuid_pago');
            $pago = $pagoObj->findByUuid($uuid_pago);

            if ($tipo_formulario == 'planilla') {
                $PagadaColaborador = $this->PagadasRepository->findBy(['planilla_id' => $pago->depositable_id, 'colaborador_id' => $pago->proveedor_id]);
                $PagadaColaborador->estado_pago = 'pagado';
                $PagadaColaborador->save();

                $planilla = $this->planillaRepository->find($pago->depositable_id);  //30: Parcial, 31: Completo
                $planilla->estado_id = ($planilla->pagadas_colaboradores + 1 < $planilla->total_colaboradores) ? 30 : 31;
                $planilla->pagadas_colaboradores = $planilla->pagadas_colaboradores + 1;
                $planilla->save();
            } elseif ($tipo_formulario == 'pago_extraordinario') {
                //  *****
                /*  $extraColaborador = $this->ComisionColaboradorRepository->findBy(['comision_id' => $pago->depositable_id, 'colaborador_id' => $pago->proveedor_id ]);
                  $extraColaborador->estado_pago = 'pagado';
                  $extraColaborador->save();
                 */
                $pago_extraordinario = $this->ComisionesRepository->find($pago->depositable_id);  // tabla Comisiones: 30: Parcial, 31: Completo 32:Por_pagar
                $pago_extraordinario->estado_id = ($pago_extraordinario->pagadas_colaboradores + 1 < $pago_extraordinario->total_colaboradores) ? 30 : 31;
                $pago_extraordinario->pagadas_colaboradores = $pago_extraordinario->pagadas_colaboradores + 1;
                $pago_extraordinario->save();
            }

            $pago->estado = 'aplicado';
            $pago->save();
        } catch (ValidationException $e) {
            Capsule::rollback();
            echo json_encode(array(
                'response' => false,
                'mensaje' => 'Hubo un error tratando de cambiar el estado al pago.',
            ));
            exit;
        }
        Capsule::commit();

        echo json_encode(array(
            'response' => true,
            'mensaje' => 'Se ha confirmado el pago al colaborador satisfactoriamente.',
        ));

        exit;
    }

    public function ajax_generar_ach()
    {
        $folder_save = $this->config->item('files_pdf');

        $cadena = '';
        $uuid_pagos = $this->input->post('uuid_pagos', true);
        if (!empty($uuid_pagos)) {
            foreach ($uuid_pagos as $uuid) {
                $cadena_modulo = '';
                $pago = $this->pagosRep->findByUuid($uuid);

                if ($pago->formulario == 'planilla') {
                    $PagadaColaborador = $this->PagadasRepository->findBy(['planilla_id' => $pago->depositable_id, 'colaborador_id' => $pago->proveedor_id]);
                    $cadena_modulo = $this->getTxtAchModulo($pago, $PagadaColaborador);
                } elseif ($pago->formulario == 'pago_extraordinario') {
                    $ComisionColaborador = $this->ComisionColaboradorRepository->findBy(['comision_id' => $pago->depositable_id, 'colaborador_id' => $pago->proveedor_id]);
                    $cadena_modulo = $this->getTxtAchModulo($pago, $ComisionColaborador);
                } else {
                    $cadena_modulo = $this->getTxtAchModulo($pago, array());
                }

                $cadena .= $cadena_modulo;
            }
        }
        $handle = fopen($folder_save.'ach_file.txt', 'w+') or die('Unable to open file!');

        fwrite($handle, $cadena);
        fclose($handle);

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($folder_save.'ach_file.txt'));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: '.filesize($folder_save.'ach_file.txt'));
        readfile($folder_save.'ach_file.txt');
        exit;
    }

    public function ajax_factura_info()
    {
        $uuid = $this->input->post('uuid');
        $facturaObj = new Buscar(new Facturas_compras_orm(), 'uuid_factura');

        $factura = $facturaObj->findByUuid($uuid);
        $factura->proveedor;
        $factura->pagos = $factura->pagos_aplicados;
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($factura->toArray()))->_display();
        exit;
    }

    //Obtiene el catalogo de facturas a las cuales se les puede
    //relizar pagos
    public function ajax_facturas_pagos()
    {
        //$vista      = $this->input->post('vista');
        $facturas = Facturas_compras_orm::deEmpresa($this->empresa_id)->paraPagos()->where('estado_id', '!=', '20');
        $resultados = array();

        foreach ($facturas->get() as $factura) {
            $total = $factura->total;
            $pagos = (count($factura->pagos_aplicados)) ? $factura->pagos_aplicados()->sum('pag_pagos_pagables.monto_pagado') : 0;
            $saldo = $total - $pagos;

            if ($saldo > 0) {
                //echo $total."-".$pagos."=".$saldo."\n<br>";
                $resultados[] = array('uuid' => $factura->uuid_factura, 'nombre' => $factura->factura_proveedor.' - '.$factura->proveedor->nombre);
            }
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($resultados))->_display();
        exit;
    }

    public function ajax_proveedores_pagos()
    {
        //$vista = $this->input->post('vista');
        $proveedores = Proveedores_orm::deEmpresa($this->empresa_id)->conFacturasParaPagos()->orderBy('nombre', 'asc');
        $resultados = array();

        foreach ($proveedores->get() as $proveedor) {
            $resultados[] = array('uuid' => $proveedor->uuid_proveedor, 'nombre' => $proveedor->nombre);
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($resultados))->_display();
        exit;
    }

    public function ajax_subcontratos_pagos()
    {
        $vista = $this->input->post('vista');

        $clause = [];
        $clause['empresa_id'] = $this->empresa_id;
        $clause['pagables'] = ($vista == 'ver') ? false : true; //con facturas por pagar o facturas pagadas parcial
        $subcontratos = $this->subcontratosRep->listar($clause);

        $resultados = [];
        foreach ($subcontratos as $subcontrato) {
            $resultados[] = array('uuid' => $subcontrato->uuid_subcontrato, 'nombre' => $subcontrato->numero_documento.' - '.$subcontrato->proveedor->nombre);
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($resultados))->_display();
        exit;
    }

    public function ajax_catalogo_pagos()
    {
        $Pagos = Cobro_catalogo_orm::where('tipo', 'pago')->get();
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($Pagos->toArray()))->_display();
        exit;
    }

    public function ajax_facturas_proveedor()
    {
        $uuid = $this->input->post('uuid');
        $vista = $this->input->post('vista');

        $proveedorObj = new Buscar(new Proveedores_orm(), 'uuid_proveedor');
        $proveedor = $proveedorObj->findByUuid($uuid);
        //print_r($proveedor->formasDePago->toArray());
        if ($vista == 'crear') {
            foreach ($proveedor->facturasCrear as $l) {
                $l->pagos = $l->pagos_aplicados;
            }
        } elseif ($vista == 'ver') {
            foreach ($proveedor->facturasNoAnuladas as $l) {
                //no esta aun en el modelo
                $l->pagos = $l->pagos_aplicados;
            }
        } elseif ($vista == 'registrar_pago_pago') {
            foreach ($proveedor->facturasHabilitadas as $l) {
                //no esta aun en el modelo
                $l->pagos = $l->pagos_aplicados;
            }
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(array($proveedor->toArray(), $proveedor->formasDePagoCobros->toArray())))->_display();
        exit;
    }

    public function ajax_facturas_subcontrato()
    {
        $uuid = $this->input->post('uuid'); //uuid_subcontrato
        $vista = $this->input->post('vista');

        $proveedorObj = new Buscar(new Proveedores_orm(), 'uuid_proveedor');
        $subcontrato = $this->subcontratosRep->findByUuid($uuid);
        $proveedor = $proveedorObj->findByUuid($subcontrato->proveedor->uuid_proveedor);

        $aux = $proveedor->toArray();
        if ($vista == 'crear') {
            $aux['facturas_crear'] = $proveedor->facturasCrear->filter(function ($factura) use ($subcontrato) {
                $factura->pagos = $factura->pagos_aplicados;

                return $factura->operacion_id == $subcontrato->id and $factura->operacion_type == 'Flexio\\Modulo\\SubContratos\\Models\\SubContrato';
            });
        } elseif ($vista == 'ver') {
            $aux['facturas_no_anuladas'] = $proveedor->facturasNoAnuladas->filter(function ($factura) use ($subcontrato) {
                $factura->pagos = $factura->pagos_aplicados;

                return $factura->operacion_id == $subcontrato->id and $factura->operacion_type == 'Flexio\\Modulo\\SubContratos\\Models\\SubContrato';
            });
        } elseif ($vista == 'registrar_pago_pago') {
            $aux['facturas_habilitadas'] = $proveedor->facturasHabilitadas->filter(function ($factura) use ($subcontrato) {
                $factura->pagos = $factura->pagos_aplicados;

                return $factura->operacion_id == $subcontrato->id and $factura->operacion_type == 'Flexio\\Modulo\\SubContratos\\Models\\SubContrato';
            });
        }

//        if($vista =='crear'){
//            $proveedor->facturasCrear->filter(function($factura) use ($subcontrato){
//                return false;
//                return $factura->operacion_id == $subcontrato->id and $factura->operacion_type == "Flexio\\Modulo\\SubContratos\\Models\\SubContrato";
//            });

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

    public function ajax_info_pago()
    {
        $uuid = $this->input->post('uuid');
        $pagoObj = new Buscar(new Pagos_orm(), 'uuid_pago');
        $pago = $pagoObj->findByUuid($uuid);

        $pago->metodo_pago;
        if ($pago->formulario != 'planilla') {
            $l = $pago->facturas->filter(function ($value, $key) {
                return $value->pivot->monto_pagado > 0;
            })->toArray();
        } elseif ($pago->empezable_type == "Flexio\Modulo\Anticipos\Models\Anticipo") {
            dd($pago->empezable);
        } else {
            $l = $pago->planillas;
        }

        $pago->pagos_pagables;

        foreach ($l as $row) {
            if (empty($row->pagos_aplicados)) {
                continue;
            }
            $row->pagos = $row->pagos_aplicados;
        }

        //Filtrar array de facturas, solo la pagada.
        if ($pago->formulario != 'planilla') {
            $pago = $pago->toArray();
            $pago['facturas'] = collect(array_values(array_filter($l)));
            $pago = collect($pago);
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($pago->toArray()))->_display();
        exit;
    }

    public function ajax_agentes_proovedores()
    {
        //Just Allow ajax request
        if ($this->input->is_ajax_request()) {
            $this->id_empresa = 0;
            //Para aplicar filtros

            $proveedor = new Proveedores_orm;//id_empresa
            $agentes = new Agentes_orm;
            $aseguradoras = new Aseguradoras_orm;//id_empresa

            if (!empty($this->input->get("q",true))) {
                $nombreBuscar = $this->input->get("q",true);
                $proveedor = 
                $proveedor->where("nombre","like","%".$nombreBuscar."%")->where("id_empresa","=",$this->empresa_id);
                $agentes = 
                $agentes->where("nombre","like","%".$nombreBuscar."%");
                $aseguradoras = 
                $aseguradoras->where("nombre","like","%".$nombreBuscar."%")->where("id_empresa","=",$this->empresa_id);

            }

            $parametrosRestriccion = array("estado", "LIKE","activo");
            $camposConsulta = array("nombre","id as proveedor_id");

            $proveedor = 
            $proveedor->where($parametrosRestriccion[0],$parametrosRestriccion[1],$parametrosRestriccion[2])
            ->get($camposConsulta);

            $agentes = 
            $agentes->where($parametrosRestriccion[0],$parametrosRestriccion[1],$parametrosRestriccion[2])
            ->get($camposConsulta);

            $aseguradoras = 
            $aseguradoras->where($parametrosRestriccion[0],$parametrosRestriccion[1],$parametrosRestriccion[2])
            ->get($camposConsulta);

            $arregloFinal = array();
            foreach ($proveedor AS $llave => $valor) {
                $valor['tipo'] = "Proveedores";
                $arregloFinal[] = $valor;
            }
            foreach ($agentes AS $llave => $valor) {
                $valor['tipo'] = "Agentes";
                $arregloFinal[] = $valor;
            }
            foreach ($aseguradoras AS $llave => $valor) {
                $valor['tipo'] = "Aseguradoras";
                $arregloFinal[] = $valor;
            }

            /*$proveedor = $proveedor->orderBy($sidx, $sord)
                ->skip($start)
                ->take($limit)
                ->get();*/

            natcasesort($arregloFinal);
            $arregloFinalOrdenado = array();
            foreach ($arregloFinal AS $llave => $valor) {
                $arregloFinalOrdenado[] = $valor;
            }
            echo json_encode($arregloFinalOrdenado);
            //echo json_encode($proveedor);
            exit;
        }
    }

    private function getTxtAchModulo($pago, $PagadaColaborador)
    {
        $cadena = $identificador = '';

//dd($pago->proveedor->id_tipo_cuenta);

        if ($pago->formulario == 'planilla') {
            $identificador = $pago->colaborador->cedula;
            $nombre_completo = Util::sanear_string($pago->colaborador->apellido.' '.$pago->colaborador->apellido_materno.' '.$pago->colaborador->nombre);
            $ruta_banco = isset($pago->colaborador->banco->ruta_transito) ? $pago->colaborador->banco->ruta_transito : '';
            $numero_cuenta = $pago->colaborador->numero_cuenta;

            if (isset($pago->colaborador->tipo_cuenta_id)) {
                $tipo_cuenta_id = $pago->colaborador->tipo_cuenta_id;
                if ($tipo_cuenta_id == 15) {
                    $tipo_cuenta_id = '03';
                }
                if ($tipo_cuenta_id == 16) {
                    $tipo_cuenta_id = '04';
                }
            }

            //$tipo_cuenta_id = '04'; //$pago->colaborador->tipo_cuenta_id;
            $neto = $pago->monto_pagado;
            $credito = 'C';
            $cierre_planilla = 'REF TXT PAGO '.date('d/m/Y', strtotime($PagadaColaborador->fecha_cierre_planilla)).'\\';
        } elseif ($pago->formulario == 'pago_extraordinario') {
            $identificador = $pago->colaborador->cedula;
            $nombre_completo = Util::sanear_string($pago->colaborador->apellido.' '.$pago->colaborador->apellido_materno.' '.$pago->colaborador->nombre);
            $ruta_banco = isset($pago->colaborador->banco->ruta_transito) ? $pago->colaborador->banco->ruta_transito : '';
            $numero_cuenta = $pago->colaborador->numero_cuenta;

            //$tipo_cuenta_id = '04'; //$pago->colaborador->tipo_cuenta_id;

            if (isset($pago->colaborador->tipo_cuenta_id)) {
                $tipo_cuenta_id = $pago->colaborador->tipo_cuenta_id;
                if ($tipo_cuenta_id == 15) {
                    $tipo_cuenta_id = '03';
                }
                if ($tipo_cuenta_id == 16) {
                    $tipo_cuenta_id = '04';
                }
            }

            $neto = $pago->monto_pagado;
            $credito = 'C';
            $cierre_planilla = 'REF TXT PAGO '.date('d/m/Y', strtotime($PagadaColaborador->pago_extraordinario->fecha_programada_pago)).'\\';
        } else {  //if($pago->depositable_type == 'Flexio\Modulo\Cajas\Models\Cajas')
            if ($pago->proveedor->identificacion == 'juridico') {
                $identificador = $pago->proveedor->tomo_rollo.$pago->proveedor->folio_imagen_doc.$pago->proveedor->asiento_ficha.$pago->proveedor->digito_verificador;
            } elseif ($pago->proveedor->identificacion == 'pasaporte') {
                $identificador = $pago->proveedor->pasaporte;
            } elseif ($pago->proveedor->identificacion == 'natural') {
                $identificador = $pago->proveedor->provincia.'-'.$pago->proveedor->letra.'-'.$pago->proveedor->tomo_rollo.'-'.$pago->proveedor->asiento_ficha;
            }
            $nombre_completo = $pago->proveedor->nombre;
            $ruta_banco = isset($pago->proveedor->banco->ruta_transito) ? $pago->proveedor->banco->ruta_transito : '';
            $pago->load('metodo_pago');

            //$numero_cuenta = !empty($pago->metodo_pago->first()) ? $pago->metodo_pago->first()->referencia['cuenta_proveedor'] : 'no account'; // $pago->proveedor->numero_cuenta;
            $numero_cuenta = !empty($pago->metodo_pago->first()->referencia['cuenta_proveedor'])?$pago->metodo_pago->first()->referencia['cuenta_proveedor']:$pago->proveedor->numero_cuenta; // $pago->proveedor->numero_cuenta;

            if (isset($pago->proveedor->id_tipo_cuenta)) {
                $tipo_cuenta_id = $pago->proveedor->id_tipo_cuenta;
                if ($tipo_cuenta_id == 15) {
                    $tipo_cuenta_id = '03';
                }
                if ($tipo_cuenta_id == 16) {
                    $tipo_cuenta_id = '04';
                }
            }

            //$tipo_cuenta_id = '04';
            $neto = $pago->monto_pagado;
            $credito = 'C';
            $cierre_planilla = 'REF TXT PAGO '.date('d/m/Y', strtotime($pago->fecha_pago)).'\\';
        }

        $cadena = substr(Util::sanear_string_ach($identificador), 0, 15).';'.
                substr(Util::sanear_string_ach($nombre_completo), 0, 22).';'.
                substr($ruta_banco, 0, 9).';'.
                substr($numero_cuenta, 0, 17).';'.
                $tipo_cuenta_id.';'.
                number_format($neto, 2, '.', '').';'.
                $credito.';'.
                substr(Util::sanear_string_ach($cierre_planilla), 0, 80)."\r\n";
        /*  $cadena = substr(str_pad($identificador, 15),0,15).
          substr(str_pad($nombre_completo, 22),0,22).
          substr(str_pad($ruta_banco, 9),0,9).
          substr(str_pad($numero_cuenta, 17),0,17).
          $tipo_cuenta_id.
          substr(str_pad($neto, 11),0,11).
          $credito.
          substr(str_pad($cierre_planilla, 80),0,80)."\r\n"; */

        return $cadena;
    }

    public function historial($uuid = null)
    {
        $acceso = 1;
        $mensaje = array();
        $data = array();

        $registro = $this->pagosRep->findByUuid($uuid);
        if (!$this->auth->has_permission('acceso', 'pagos/historial') && is_null($registro)) {
            // No, tiene permiso
            $acceso = 0;
            $mensaje = array('estado' => 500, 'mensaje' => ' <b>Usted no cuenta con permiso para esta solicitud</b>', 'clase' => 'alert-danger');
        }
        $this->_Css();
        $this->_js();
        $this->assets->agregar_js(array(
            'public/resources/compile/modulos/pagos/historial.js',
        ));
        $breadcrumb = array(
            'titulo' => '<i class="fa fa-shopping-cart"></i> Bit&aacute;cora: Pagos '.$registro->codigo,
        );

        $registro->load('historial');

        $historial = $registro->historial->map(function ($pagotHist) use ($registro) {
            return [
                'id' => $pagotHist->id,
                'titulo' => $pagotHist->titulo,
                'codigo' => $registro->codigo,
                'descripcion' => $pagotHist->descripcion,
                'antes' => $pagotHist->antes,
                'despues' => $pagotHist->despues,
                'tipo' => $pagotHist->tipo,
                'nombre_usuario' => $pagotHist->nombre_usuario,
                'hace_tiempo' => $pagotHist->cuanto_tiempo,
                'fecha_creacion' => $pagotHist->fecha_creacion,
                'hora' => $pagotHist->hora,
            ];
        });
        $this->assets->agregar_var_js(array(
            'historial' => $historial,
        ));
        $this->template->agregar_titulo_header('Pagos');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    private function _Css()
    {
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
            'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
            'public/assets/css/plugins/bootstrap/select2.min.css',
        ));
    }

    private function _js()
    {
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
            // 'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            //'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/plugins/jquery/sweetalert/sweetalert.min.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
            'public/assets/js/default/jquery.inputmask.bundle.min.js',
            'public/assets/js/default/formulario.js',
            'public/assets/js/plugins/jquery/fileupload/jquery.fileupload.js',
            'public/assets/js/plugins/bootstrap/select2/select2.min.js',
            'public/assets/js/plugins/bootstrap/select2/es.js',
        ));
    }
}

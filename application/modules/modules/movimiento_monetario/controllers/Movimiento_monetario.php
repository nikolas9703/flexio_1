<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/*
 *
 * @package    Erp
 * @subpackage Controller
 * @category   Contabilidad
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 */

use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon;

//transacciones
use Flexio\Modulo\MovimientosMonetarios\Transacciones\MovimientosMonetariosRetiro;
use Flexio\Modulo\MovimientosMonetarios\Models\MovimientosRetiros;

//utils
use Flexio\Library\Util\FlexioAssets;
use Flexio\Library\Util\FlexioSession;
use Flexio\Library\Toast;

class Movimiento_monetario extends CRM_Controller
{
    protected $MovimientosMonetariosRetiro;
    protected $pagoGuardar;
    protected $DocumentosRepository;
    protected $upload_folder = './public/uploads/';
    //utils
    protected $FlexioAssets;
    protected $FlexioSession;
    protected $Toast;

    public function setUtils(FlexioAssets $FlexioAssets, FlexioSession $FlexioSession, Toast $Toast)
    {
        $this->FlexioAssets = $FlexioAssets;
        $this->FlexioSession = $FlexioSession;
        $this->Toast = $Toast;
    }

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Movimiento_monetario_orm');
        $this->setUtils(new FlexioAssets, new FlexioSession, new Toast);

        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();
        $this->load->model('Movimiento_retiros_orm');
        $this->load->model('Movimiento_cat_orm');
        $this->load->model('Items_recibos_orm');
        $this->load->model('Items_retiros_orm');
        $this->load->model('Comentario_recibos_orm');
        $this->load->model('Comentario_retiros_orm');
        $this->load->model('entrada_manual/Comentario_orm');
        $this->load->model('centros/Centros_orm');
        $this->load->model('clientes/Cliente_orm');
        $this->load->model('proveedores/Proveedores_orm');
        $this->load->model('facturas_compras/Facturas_compras_orm');
        $this->load->model('pagos/Pagos_orm');
        $this->load->model('clientes/Catalogo_orm');
        $this->load->model('pagos/Pago_metodos_pago_orm');
        $this->load->library('Repository/Pagos/Guardar_pago');
        $this->pagoGuardar = new Guardar_pago();
        //Obtener el id_empresa de session
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);

        //HMVC Load Modules

        $this->load->module(array('documentos'));
        $this->empresa_id = $empresa->id;
        Carbon::setLocale('es');
        setlocale(LC_TIME, 'Spanish');

        //transacciones
        $this->MovimientosMonetariosRetiro = new MovimientosMonetariosRetiro();
    }

    public function listar_recibos()
    {
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
            'public/assets/css/modules/descuentos/estado_cuenta.css',
            'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
            'public/assets/css/plugins/bootstrap/select2.min.css',

        ));
        $this->assets->agregar_js(array(
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
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
            'public/assets/js/plugins/bootstrap/select2/select2.min.js',
            'public/assets/js/plugins/bootstrap/select2/es.js',

        ));

        if (!is_null($this->session->flashdata('mensaje'))) {
            $mensaje = json_encode($this->session->flashdata('mensaje'));
        } else {
            $mensaje = '';
        }
        $this->assets->agregar_var_js(array(
            'toast_mensaje' => $mensaje,
            'flexio_mensaje' => Flexio\Library\Toast::getStoreFlashdata(),
        ));

        $breadcrumb = array(
            'titulo' => '<i class="fa fa-calculator"></i> Recibos de dinero',

        );

        //Verificar permisos para crear
        if ($this->auth->has_permission('acceso', 'movimiento_monetario/crear_recibos')) {
            $breadcrumb['menu'] = array(
                'url' => 'movimiento_monetario/crear_recibos',
                'nombre' => 'Crear',
            );
            $menuOpciones['#exportarReciboLnk'] = 'Exportar';
        }

        $breadcrumb['menu']['opciones'] = !empty($menuOpciones) ? $menuOpciones : array();

        $data['cliente_proveedor'] = Movimiento_cat_orm::lista();

        $this->template->agregar_titulo_header('Recibos de dinero');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }

    public function listar_retiros()
    {
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
            'public/assets/css/modules/descuentos/estado_cuenta.css',
            'public/assets/css/plugins/jquery/jquery.fileupload.css',
            'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
            'public/assets/css/plugins/bootstrap/select2.min.css',

        ));
        $this->assets->agregar_js(array(
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
            'public/assets/js/plugins/jquery/jquery.webui-popover.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/plugins/bootstrap/jquery.bootstrap-touchspin.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            'public/assets/js/plugins/jquery/fileupload/jquery.fileupload.js',
            'public/assets/js/plugins/bootstrap/select2/select2.min.js',
            'public/assets/js/plugins/bootstrap/select2/es.js',
        ));

        //Breadcrum Array
        //$cuentas = Cuentas_orm::find(371);
        //print_r($cuentas->toArray());

        $breadcrumb = array(
            'titulo' => '<i class="fa fa-calculator"></i> Retiros de dinero',

        );

        $this->assets->agregar_var_js(array(
            'flexio_mensaje' => Flexio\Library\Toast::getStoreFlashdata(),
        ));

        //Verificar permisos para crear
        if ($this->auth->has_permission('acceso', 'movimiento_monetario/crear_retiros')) {
            $breadcrumb['menu'] = array(
                'url' => 'movimiento_monetario/crear_retiros',
                'nombre' => 'Crear',
            );
            $menuOpciones['#exportarRetirosLnk'] = 'Exportar';
        }

        $breadcrumb['menu']['opciones'] = !empty($menuOpciones) ? $menuOpciones : array();

        $data['cliente_proveedor'] = Movimiento_cat_orm::lista();

        $this->template->agregar_titulo_header('Retiros de dinero');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }

    public function ajax_listar_recibos($grid = null)
    {

        //$colaboradores = Colaboradores_orm::lista($this->empresa_id);

        $cliente = $this->input->post('cliente', true);
        $nombre = $this->input->post('nombre', true);
        $narracion = $this->input->post('narracion', true);
        $monto_desde = $this->input->post('monto_desde', true);
        $monto_hasta = $this->input->post('monto_hasta', true);
        $fecha_desde = $this->input->post('fecha_desde', true);
        $fecha_hasta = $this->input->post('fecha_hasta', true);

        $clause = array(
            'id_empresa' => $this->empresa_id,
        );

        if (!empty($cliente)) {
            $clause['cliente'] = $cliente;
        }

        if (!empty($nombre)) {
            $clause['nombre'] = $nombre;
        }

        if (!empty($narracion)) {
            $clause['narracion'] = array('LIKE', "%$narracion%");
        }

        if (!empty($monto_desde)) {
            $clause['monto_desde'] = $monto_desde;
        }

        if (!empty($monto_hasta)) {
            $clause['monto_hasta'] = $monto_hasta;
        }
        if (!empty($fecha_desde)) {
            $clause['fecha_desde'] = date('Y-m-d', strtotime($fecha_desde));
        }
        if (!empty($fecha_hasta)) {
            $clause['fecha_hasta'] = date('Y-m-d', strtotime($fecha_hasta));
            /*
             echo '<h2>Consultando Antes ROWS:</h2><pre>';
             print_r($clause["fecha_hasta"]);
             echo '</pre>'; */
        }

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();

        $count = Movimiento_monetario_orm::listar($clause, null, null, null, null)->count();

        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

        $rows = Movimiento_monetario_orm::listar($clause, $sidx, $sord, $limit, $start);

        foreach ($rows as $info) {
            $info->colaborador;
            //  $info->acreedor;
        }
        // print_r(Capsule::getQueryLog());

        //Constructing a JSON
        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
        $response->result = array();
        $i = 0;

        if (!empty($rows->toArray())) {
            foreach ($rows->toArray() as $i => $row) {
                $uuid_recibos = $row['uuid_recibo_dinero'];
                //  $credito = Items_recibos_orm::listar($row['id']);
                /* $j=0;
                 foreach($row['items'] as $item=>$value){

                     $item[] = $value;


                 $j++;
                 } */

                $valores = array_values($row['items']);
                $sum = 0;
                foreach ($valores as $num => $values) {
                    $sum += $values['credito'];
                }

                if (!empty($row['cliente_id'])) {
                    $cliente_proveedor = 'Cliente';
                    $cliente_proveedor_name = $row['cliente']['nombre'];
                    $uuid_cliente_proveedor = $row['cliente']['uuid_cliente'];
                    $base_url = base_url('clientes/ver/'.$uuid_cliente_proveedor);
                } else {
                    $cliente_proveedor = 'Proveedor';
                    $cliente_proveedor_name = $row['proveedor']['nombre'];
                    $uuid_cliente_proveedor = $row['proveedor']['uuid_proveedor'];
                    $base_url = base_url('proveedor/ver/'.$uuid_cliente_proveedor);
                }

                $link_option = '<center><button class="viewOptions btn btn-success btn-sm" type="button" data-id="'.$row['id'].'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button></center>';
                $hidden_options = '';
                $hidden_options .= '<a href="'.base_url('movimiento_monetario/ver/'.$uuid_recibos).'" data-id="'.$row['id'].'" class="btn btn-block btn-outline btn-success">Ver recibo de dinero</a>';
                $hidden_options .= '<a href="#" class="btn btn-block btn-outline btn-success anular">Anular</a>';

                $response->rows[$i]['id'] = $row['id'];
                $response->rows[$i]['cell'] = array(
                    '<a style="color:blue; text-decoration:underline;" href="'.base_url('movimiento_monetario/ver/'.$uuid_recibos).'" data-id="'.$row['id'].'">'.Util::verificar_valor($row['codigo']).'</a>',
                    Util::verificar_valor($cliente_proveedor),
                    '<a style="color:blue; text-decoration:underline;" href="'.$base_url.'">'.Util::verificar_valor($cliente_proveedor_name).'</a>',
                    Util::verificar_valor($row['narracion']),
                    $row['fecha_inicio'],
                    $sum,

                    $link_option,
                    $hidden_options,
                );

                ++$i;
            }
        }
        echo json_encode($response);
        exit;
    }

    public function ajax_listar_retiros($grid = null)
    {

        //$colaboradores = Colaboradores_orm::lista($this->empresa_id);

        $cliente = $this->input->post('cliente', true);
        $nombre = $this->input->post('nombre', true);
        $narracion = $this->input->post('narracion', true);
        $monto_desde = $this->input->post('monto_desde', true);
        $monto_hasta = $this->input->post('monto_hasta', true);
        $fecha_desde = $this->input->post('fecha_desde', true);
        $fecha_hasta = $this->input->post('fecha_hasta', true);

        $clause = array(
            'id_empresa' => $this->empresa_id,
        );

        if (!empty($cliente)) {
            $clause['cliente'] = $cliente;
        }

        if (!empty($nombre)) {
            $clause['nombre'] = $nombre;
        }

        if (!empty($narracion)) {
            $clause['narracion'] = array('LIKE', "%$narracion%");
        }

        if (!empty($monto_desde)) {
            $clause['monto_desde'] = $monto_desde;
        }

        if (!empty($monto_hasta)) {
            $clause['monto_hasta'] = $monto_hasta;
        }
        if (!empty($fecha_desde)) {
            $clause['fecha_desde'] = date('Y-m-d', strtotime($fecha_desde));
        }
        if (!empty($fecha_hasta)) {
            $clause['fecha_hasta'] = date('Y-m-d', strtotime($fecha_hasta));
            /*
             echo '<h2>Consultando Antes ROWS:</h2><pre>';
             print_r($clause["fecha_hasta"]);
             echo '</pre>'; */
        }

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();

        $count = Movimiento_retiros_orm::listar($clause, null, null, null, null)->count();

        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

        $rows = Movimiento_retiros_orm::listar($clause, $sidx, $sord, $limit, $start);

        foreach ($rows as $info) {
            $info->colaborador;
            //  $info->acreedor;
        }
        // print_r(Capsule::getQueryLog());

        //Constructing a JSON
        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
        $response->result = array();
        $i = 0;

        if (!empty($rows->toArray())) {
            foreach ($rows->toArray() as $i => $row) {
                $uuid_retiros = bin2hex($row['uuid_retiro_dinero']);
                $valores = array_values($row['items']);
                $sum = 0;
                foreach ($valores as $num => $values) {
                    $sum += $values['debito'];
                }

                if (!empty($row['cliente_id'])) {
                    $cliente_proveedor = 'Cliente';
                    $cliente_proveedor_name = $row['cliente']['nombre'];
                    $uuid_cliente_proveedor = $row['cliente']['uuid_cliente'];
                    $base_url = base_url('clientes/ver/'.$uuid_cliente_proveedor);
                } else {
                    $cliente_proveedor = 'Proveedor';
                    $cliente_proveedor_name = $row['proveedor']['nombre'];
                    $uuid_cliente_proveedor = $row['proveedor']['uuid_proveedor'];
                    $base_url = base_url('proveedor/ver/'.$uuid_cliente_proveedor);
                }

                $link_option = '<center><button class="viewOptions btn btn-success btn-sm" type="button" data-id="'.$row['id'].'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button></center>';
                $hidden_options = '';
                $hidden_options .= '<a href="'.base_url('movimiento_monetario/ver_retiros/'.$uuid_retiros).'" data-id="'.$row['id'].'" class="btn btn-block btn-outline btn-success">Ver retiro de dinero</a>';
                $hidden_options .= '<a href="#" class="btn btn-block btn-outline btn-success anular">Anular</a>';
                $hidden_options .= '<a href="#" class="btn btn-block btn-outline btn-success subirArchivoBtn" data-id="'.$row['id'].'" data-uuid="'.$uuid_retiros.'" >Subir archivo</a>';

                $response->rows[$i]['id'] = $row['id'];
                $response->rows[$i]['cell'] = array(
                    '<a style="color:blue; text-decoration:underline;" href="'.base_url('movimiento_monetario/ver_retiros/'.$uuid_retiros).'" data-id="'.$row['id'].'">'.Util::verificar_valor($row['codigo']).'</a>',
                    Util::verificar_valor($cliente_proveedor),
                    '<a style="color:blue; text-decoration:underline;" href="'.$base_url.'">'.Util::verificar_valor($cliente_proveedor_name).'</a>',
                    Util::verificar_valor($row['narracion']),
                    $row['fecha_inicio'],
                    $sum,

                    $link_option,
                    $hidden_options,
                );

                ++$i;
            }
        }
        echo json_encode($response);
        exit;
    }

    public function ajax_cliente_proveedor()
    {
        $cliente_proveedor = $this->input->post('cliente_proveedor', true);
        $query = null;
        $clause = ['empresa_id' => $this->empresa_id];

        if ($cliente_proveedor == '1') {
            $query = Proveedores_orm::query();
            $query->where('id_empresa', '=', $clause);
            if (isset($_POST['q'])) {
                $query->where('nombre', 'like', '%'.$_POST['q'].'%');
            }
        } else {
            $query = Cliente_orm::query();
            $query->where('empresa_id', '=', $clause);
            if (isset($_POST['q'])) {
                $query->where('nombre', 'like', '%'.$_POST['q'].'%');
            }
        }

        $query->take(isset($_POST['limit']) ? $_POST['limit'] : 10);

        $json = json_encode($query->get());
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output($json)->_display();
        exit;
    }

    public function get_clientes($cliente)
    {
        $clause = array('empresa_id' => $this->empresa_id);
        $clientes = Cliente_orm::listar($clause)->toArray();

        return $clientes;
    }

    public function get_proveedor($proveedor)
    {
        $clause = array('empresa_id' => $this->empresa_id);
        $proveedores = Proveedores_orm::lista($clause);

        return $proveedores;
    }

    public function ocultotabla()
    {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/movimiento_monetario/tabla_recibos.js',
        ));

        $this->load->view('tabla_recibos');
    }

    public function ocultotabla_retiros()
    {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/movimiento_monetario/tabla_retiros.js',
        ));

        $this->load->view('tabla_retiros');
    }

    public function crear_recibos()
    {
        //permisos
        $acceso = $this->auth->has_permission('acceso', 'movimiento_monetario/crear_recibos');
        $this->Toast->runVerifyPermission($acceso);

        //assets
        $this->FlexioAssets->run(); //css y js generales
        $this->FlexioAssets->add('vars', [
            'vista' => 'crear',
            'modulo' => 'recibo_dinero',
            'acceso' => $acceso ? 1 : 0,
            'usuario_id' => $this->FlexioSession->usuarioId()
        ]);

        //breadcrumb
        $breadcrumb = [
            'titulo' => '<i class="fa fa-calculator"></i> Recibos de dinero: Crear',
        ];

        //render
        $this->template->agregar_titulo_header('Recibos de dinero');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido([]);
        $this->template->visualizar();
    }

    public function crear_retiros()
    {
        //permisos
        $acceso = $this->auth->has_permission('acceso', 'movimiento_monetario/crear_retiros');
        $this->Toast->runVerifyPermission($acceso);

        //assets
        $this->FlexioAssets->run(); //css y js generales
        $this->FlexioAssets->add('vars', [
            'vista' => 'crear',
            'modulo' => 'retiro_dinero',
            'acceso' => $acceso ? 1 : 0,
            'usuario_id' => $this->FlexioSession->usuarioId()
        ]);

        //breadcrumb
        $breadcrumb = [
            'titulo' => '<i class="fa fa-calculator"></i> Retiros de dinero: Crear',
        ];

        //render
        $this->template->agregar_titulo_header('Retiros de dinero');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido([]);
        $this->template->visualizar();
    }

    public function ver_recibos($uuid = NULL)
    {
        //permisos
        $acceso = $this->auth->has_permission('acceso', 'movimiento_monetario/ver/(:any)');
        $this->Toast->runVerifyPermission($acceso);

        //registers
        $recibo_dinero = Flexio\Modulo\MovimientosMonetarios\Models\MovimientoRecibo::where('uuid_recibo_dinero', hex2bin($uuid))->first();

        //assets
        $this->FlexioAssets->run(); //css y js generales
        $this->FlexioAssets->add('vars', [
            'vista' => 'ver',
            'modulo' => 'recibo_dinero',
            'acceso' => $acceso ? 1 : 0,
            'recibo_dinero' => $recibo_dinero->collection()->ver
        ]);

        //breadcrumb
        $breadcrumb = [
            'titulo' => '<i class="fa fa-calculator"></i> Recibos de dinero: '.$recibo_dinero->codigo,
        ];

        //subpanels
        $subpanels = [
            'documento'=>['recibo_dinero'=>$recibo_dinero->id]
        ];

        //render
        $this->template->agregar_titulo_header('Recibos de dinero');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido(['subpanels' => $subpanels]);
        $this->template->visualizar();
    }

    public function ver_retiros($uuid = NULL)
    {
        //permisos
        $acceso = $this->auth->has_permission('acceso', 'movimiento_monetario/ver_retiros/(:any)');
        $this->Toast->runVerifyPermission($acceso);

        //registers
        $retiro_dinero = Flexio\Modulo\MovimientosMonetarios\Models\MovimientosRetiros::where('uuid_retiro_dinero', hex2bin($uuid))->first();

        //assets
        $this->FlexioAssets->run(); //css y js generales
        $this->FlexioAssets->add('vars', [
            'vista' => 'ver',
            'modulo' => 'retiro_dinero',
            'acceso' => $acceso ? 1 : 0,
            'recibo_dinero' => $retiro_dinero->collection()->ver
        ]);

        //breadcrumb
        $breadcrumb = [
            'titulo' => '<i class="fa fa-calculator"></i> Retiros de dinero: '.$retiro_dinero->codigo,
        ];

        //subpanels
        $subpanels = [
            'documento'=>['retiro_dinero'=>$retiro_dinero->id]
        ];

        //render
        $this->template->agregar_titulo_header('Retiros de dinero');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido(['subpanels' => $subpanels]);
        $this->template->visualizar();
    }

    public function guardar()
    {
        if (!empty($this->input->post())) {
            $modulo = $this->input->post('modulo');
            $redirect = $modulo == 'recibo_dinero' ? 'movimiento_monetario/listar_recibos' : 'movimiento_monetario/listar_retiros';
            try {
                $className = "Flexio\Modulo\MovimientosMonetarios\FormRequest\Guardar".studly_case($modulo);
                $Guardar = new $className;
                $movimiento = $Guardar->guardar();
            } catch (\Exception $e) {
                log_message('error', ' __METHOD__  ->  , Linea:  __LINE__  --> '.$e->getMessage()."\r\n");
                $this->Toast->setUrl($redirect)->run('exception', [$e->getMessage()]);
            }

            if (!is_null($movimiento)) {
                $this->Toast->run('success', [$movimiento->fresh()->codigo]);
            } else {
                $this->Toast->run('error');
            }

            redirect(base_url($redirect));
        }
    }

    public function ocultoformulario()
    {
        $this->FlexioAssets->add('js', [
            'public/resources/compile/modulos/movimiento_monetario/formulario.js'
        ]);
        $this->load->view('formulario');
    }

    public function ajax_eliminar_recibos()
    {

        //Just Allow ajax request
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        $clause = array();
        $id = $this->input->post('id', true);

        if (empty($id)) {
            return false;
        }

        /*
         * Inicializar Transaccion
         */
        Capsule::beginTransaction();

        try {
            $response = Movimiento_monetario_orm::find($id);
            $response->estado = '0';
            $response->save();
        } catch (ValidationException $e) {

            // Rollback
            Capsule::rollback();

            log_message('error', 'MODULO: '.__METHOD__.', Linea: '.__LINE__.' --> '.$e->getMessage()."\r\n");

            echo json_encode(array(
                'response' => false,
                'mensaje' => 'Hubo un error tratando de eliminar la deducci&oacute;n.',
            ));
            exit;
        }

        // If we reach here, then
        // data is valid and working.
        // Commit the queries!
        Capsule::commit();

        die;
    }

    public function ajax_eliminar_retiros()
    {

        //Just Allow ajax request
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        $clause = array();
        $id = $this->input->post('id', true);

        if (empty($id)) {
            return false;
        }

        /*
         * Inicializar Transaccion
         */
        Capsule::beginTransaction();

        try {
            $response = Movimiento_retiros_orm::find($id);
            $response->estado = '0';
            $response->save();
        } catch (ValidationException $e) {

            // Rollback
            Capsule::rollback();

            log_message('error', 'MODULO: '.__METHOD__.', Linea: '.__LINE__.' --> '.$e->getMessage()."\r\n");

            echo json_encode(array(
                'response' => false,
                'mensaje' => 'Hubo un error tratando de eliminar la deducci&oacute;n.',
            ));
            exit;
        }

        // If we reach here, then
        // data is valid and working.
        // Commit the queries!
        Capsule::commit();

        die;
    }


    public function documentos_campos()
    {
        return true;
    }

    public function ajax_guardar_documentos()
    {
        if (empty($_POST)) {
            return false;
        }

        $retiro_id = $this->input->post('retiro_id', true);
        $modeloInstancia = MovimientosRetiros::find($retiro_id);

        $this->documentos->subir($modeloInstancia);
    }
}

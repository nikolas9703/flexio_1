<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
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
use Flexio\Modulo\MovimientosMonetarios\Transacciones\MovimientosMonetariosRecibo;
use Flexio\Modulo\MovimientosMonetarios\Transacciones\MovimientosMonetariosRetiro;
use Flexio\Modulo\MovimientosMonetarios\Models\MovimientosRetiros;

class Movimiento_monetario extends CRM_Controller
{

    protected $MovimientosMonetariosRecibo;
    protected $MovimientosMonetariosRetiro;
    protected $pagoGuardar;
    protected $DocumentosRepository;
    protected $upload_folder = './public/uploads/';

    function __construct()
    {
        parent::__construct();
        $this->load->model('movimiento_monetario_orm');

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
        $this->pagoGuardar = new Guardar_pago;
        //Obtener el id_empresa de session
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);

        //HMVC Load Modules

        $this->load->module(array('documentos'));
        $this->empresa_id = $empresa->id;
        Carbon::setLocale('es');
        setlocale(LC_TIME, 'Spanish');

        //transacciones
        $this->MovimientosMonetariosRecibo = new MovimientosMonetariosRecibo();
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
            'public/assets/css/plugins/bootstrap/select2.min.css'

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
            'public/assets/js/plugins/bootstrap/select2/es.js'

        ));

        if (!is_null($this->session->flashdata('mensaje'))) {
            $mensaje = json_encode($this->session->flashdata('mensaje'));
        } else {
            $mensaje = '';
        }
        $this->assets->agregar_var_js(array(
            "toast_mensaje" => $mensaje
        ));

        $breadcrumb = array(
            "titulo" => '<i class="fa fa-calculator"></i> Recibos de dinero',

        );

        //Verificar permisos para crear
        if ($this->auth->has_permission('acceso', 'movimiento_monetario/crear_recibos')) {
            $breadcrumb["menu"] = array(
                "url" => 'movimiento_monetario/crear_recibos',
                "nombre" => "Crear"
            );
            $menuOpciones["#exportarReciboLnk"] = "Exportar";
        }

        $breadcrumb["menu"]["opciones"] = !empty($menuOpciones) ? $menuOpciones : array();


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
            'public/assets/css/plugins/bootstrap/select2.min.css'

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
            'public/assets/js/plugins/bootstrap/select2/es.js'
        ));

        //Breadcrum Array
        //$cuentas = Cuentas_orm::find(371);
        //print_r($cuentas->toArray());

        $breadcrumb = array(
            "titulo" => '<i class="fa fa-calculator"></i> Retiros de dinero',

        );

        //Verificar permisos para crear
        if ($this->auth->has_permission('acceso', 'movimiento_monetario/crear_retiros')) {
            $breadcrumb["menu"] = array(
                "url" => 'movimiento_monetario/crear_retiros',
                "nombre" => "Crear"
            );
            $menuOpciones["#exportarRetirosLnk"] = "Exportar";
        }

        $breadcrumb["menu"]["opciones"] = !empty($menuOpciones) ? $menuOpciones : array();


        $data['cliente_proveedor'] = Movimiento_cat_orm::lista();


        $this->template->agregar_titulo_header('Retiros de dinero');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);

    }


    public function ajax_listar_recibos($grid = NULL)
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
            "id_empresa" => $this->empresa_id
        );

        if (!empty($cliente)) {

            $clause["cliente"] = $cliente;
        }

        if (!empty($nombre)) {

            $clause["nombre"] = $nombre;
        }

        if (!empty($narracion)) {

            $clause["narracion"] = array("LIKE", "%$narracion%");
        }

        if (!empty($monto_desde)) {

            $clause["monto_desde"] = $monto_desde;
        }

        if (!empty($monto_hasta)) {
            $clause["monto_hasta"] = $monto_hasta;
        }
        if (!empty($fecha_desde)) {
            $clause["fecha_desde"] = date('Y-m-d', strtotime($fecha_desde));

        }
        if (!empty($fecha_hasta)) {

            $clause["fecha_hasta"] = date('Y-m-d', strtotime($fecha_hasta));
            /*
             echo '<h2>Consultando Antes ROWS:</h2><pre>';
             print_r($clause["fecha_hasta"]);
             echo '</pre>'; */

        }


        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();

        $count = Movimiento_monetario_orm::listar($clause, NULL, NULL, NULL, NULL)->count();

        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

        $rows = Movimiento_monetario_orm::listar($clause, $sidx, $sord, $limit, $start);

        foreach ($rows AS $info) {

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
            foreach ($rows->toArray() AS $i => $row) {


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

                    $cliente_proveedor = "Cliente";
                    $cliente_proveedor_name = $row['cliente']['nombre'];
                    $uuid_cliente_proveedor = $row['cliente']['uuid_cliente'];
                    $base_url = base_url('clientes/ver/' . $uuid_cliente_proveedor);


                } else {
                    $cliente_proveedor = "Proveedor";
                    $cliente_proveedor_name = $row['proveedor']['nombre'];
                    $uuid_cliente_proveedor = $row['proveedor']['uuid_proveedor'];
                    $base_url = base_url('proveedor/ver/' . $uuid_cliente_proveedor);
                }

                $link_option = '<center><button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row['id'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button></center>';
                $hidden_options = "";
                $hidden_options .= '<a href="' . base_url('movimiento_monetario/ver/' . $uuid_recibos) . '" data-id="' . $row['id'] . '" class="btn btn-block btn-outline btn-success">Ver recibo de dinero</a>';
                $hidden_options .= '<a href="#" class="btn btn-block btn-outline btn-success anular">Anular</a>';


                $response->rows[$i]["id"] = $row['id'];
                $response->rows[$i]["cell"] = array(
                    '<a style="color:blue; text-decoration:underline;" href="' . base_url('movimiento_monetario/ver/' . $uuid_recibos) . '" data-id="' . $row['id'] . '">' . Util::verificar_valor($row['codigo']) . '</a>',
                    Util::verificar_valor($cliente_proveedor),
                    '<a style="color:blue; text-decoration:underline;" href="' . $base_url . '">' . Util::verificar_valor($cliente_proveedor_name) . '</a>',
                    Util::verificar_valor($row['narracion']),
                    $row['fecha_inicio'],
                    $sum,

                    $link_option,
                    $hidden_options,
                );


                $i++;
            }
        }
        echo json_encode($response);
        exit;


    }

    public function ajax_listar_retiros($grid = NULL)
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
            "id_empresa" => $this->empresa_id
        );

        if (!empty($cliente)) {

            $clause["cliente"] = $cliente;
        }

        if (!empty($nombre)) {

            $clause["nombre"] = $nombre;
        }

        if (!empty($narracion)) {

            $clause["narracion"] = array("LIKE", "%$narracion%");
        }

        if (!empty($monto_desde)) {

            $clause["monto_desde"] = $monto_desde;
        }

        if (!empty($monto_hasta)) {
            $clause["monto_hasta"] = $monto_hasta;
        }
        if (!empty($fecha_desde)) {
            $clause["fecha_desde"] = date('Y-m-d', strtotime($fecha_desde));

        }
        if (!empty($fecha_hasta)) {

            $clause["fecha_hasta"] = date('Y-m-d', strtotime($fecha_hasta));
            /*
             echo '<h2>Consultando Antes ROWS:</h2><pre>';
             print_r($clause["fecha_hasta"]);
             echo '</pre>'; */

        }


        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();

        $count = Movimiento_retiros_orm::listar($clause, NULL, NULL, NULL, NULL)->count();

        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

        $rows = Movimiento_retiros_orm::listar($clause, $sidx, $sord, $limit, $start);

        foreach ($rows AS $info) {

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
            foreach ($rows->toArray() AS $i => $row) {

                $uuid_retiros = bin2hex($row['uuid_retiro_dinero']);
                $valores = array_values($row['items']);
                $sum = 0;
                foreach ($valores as $num => $values) {

                    $sum += $values['debito'];

                }

                if (!empty($row['cliente_id'])) {

                    $cliente_proveedor = "Cliente";
                    $cliente_proveedor_name = $row['cliente']['nombre'];
                    $uuid_cliente_proveedor = $row['cliente']['uuid_cliente'];
                    $base_url = base_url('clientes/ver/' . $uuid_cliente_proveedor);

                } else {
                    $cliente_proveedor = "Proveedor";
                    $cliente_proveedor_name = $row['proveedor']['nombre'];
                    $uuid_cliente_proveedor = $row['proveedor']['uuid_proveedor'];
                    $base_url = base_url('proveedor/ver/' . $uuid_cliente_proveedor);
                }


                $link_option = '<center><button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row['id'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button></center>';
                $hidden_options = "";
                $hidden_options .= '<a href="' . base_url('movimiento_monetario/ver_retiros/' . $uuid_retiros) . '" data-id="' . $row['id'] . '" class="btn btn-block btn-outline btn-success">Ver retiro de dinero</a>';
                $hidden_options .= '<a href="#" class="btn btn-block btn-outline btn-success anular">Anular</a>';
                $hidden_options .= '<a href="#" class="btn btn-block btn-outline btn-success subirArchivoBtn" data-id="' . $row['id'] . '" data-uuid="' . $uuid_retiros . '" >Subir archivo</a>';

                $response->rows[$i]["id"] = $row['id'];
                $response->rows[$i]["cell"] = array(
                    '<a style="color:blue; text-decoration:underline;" href="' . base_url('movimiento_monetario/ver_retiros/' . $uuid_retiros) . '" data-id="' . $row['id'] . '">' . Util::verificar_valor($row['codigo']) . '</a>',
                    Util::verificar_valor($cliente_proveedor),
                    '<a style="color:blue; text-decoration:underline;" href="' . $base_url . '">' . Util::verificar_valor($cliente_proveedor_name) . '</a>',
                    Util::verificar_valor($row['narracion']),
                    $row['fecha_inicio'],
                    $sum,

                    $link_option,
                    $hidden_options,
                );


                $i++;
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

        if ($cliente_proveedor == "1") {
            $query = Proveedores_orm::query();
            $query->where('id_empresa', '=' , $clause);
            if (isset($_POST['q'])) {
                $query->where("nombre", "like", "%" . $_POST['q'] . "%");
            }

        } else {
            $query = Cliente_orm::query();
            $query->where('empresa_id', '=' , $clause);
            if (isset($_POST['q'])) {
                $query->where("nombre", "like", "%" . $_POST['q'] . "%");
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
            'public/assets/js/modules/movimiento_monetario/tabla_recibos.js'
        ));

        $this->load->view('tabla_recibos');
    }

    public function ocultotabla_retiros()
    {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/movimiento_monetario/tabla_retiros.js'
        ));

        $this->load->view('tabla_retiros');
    }


    function crear_recibos($recibos_uuid = NULL)
    {


        if (!empty($_POST["campo"])) {

            if (!empty($_POST["campo"]["incluir"])) {
                $check_narracion = "1";
            } else {
                $check_narracion = "0";
            }

            // $dategen = date('Y-m-d H:i:s');
            $recibos_id = $_POST['campo']['id'];
            $narracion = $_POST['campo']['nombre'];
            $incluir_narracion = $check_narracion;
            $cuenta_banco = $_POST['campo']['cuenta_banco'];
            if ($_POST['campo']['id_categoria'] == "1") {
                $id_proveedor = $_POST['campo']['id_cliente_proveedor'];
            } else {
                $id_cliente = $_POST['campo']['id_cliente_proveedor'];
            }
            $fecha_inicio = date('Y-m-d H:i:s');
            $fecha_inicio = !empty($fecha_inicio) ? str_replace('/', '-', $fecha_inicio) : "";
            $fecha_inicio = !empty($fecha_inicio) ? date("Y-m-d", strtotime($fecha_inicio)) : "";
            $j = 0;
            $uuid_recibos = Capsule::raw("ORDER_UUID(uuid())");
            $codigo = Capsule::raw("NO_RECIBO('RC', " . $this->empresa_id . ")");

            /**
             * Inicializar Transaccion
             */
            Capsule::beginTransaction();

            try {


                $recibos = Movimiento_monetario_orm::find($recibos_id);


                if (!empty($recibos)) {
                    $recibos->empresa_id = $this->empresa_id;
                    $recibos->narracion = $narracion;
                    $recibos->incluir_narracion = $incluir_narracion;
                    $recibos->cuenta_id = $cuenta_banco;
                    if ($_POST['campo']['id_categoria'] == "1") {
                        $recibos->proveedor_id = $id_proveedor;
                    } else {
                        $recibos->id_cliente = $id_cliente;
                    }
                    $recibos->fecha_inicio = $fecha_inicio;
                    $recibos->save();
                } else {

                    if ($_POST['campo']['id_categoria'] == "1") {
                        $id_proveedor;
                        $id_cliente = NULL;
                    } else {
                        $id_cliente;
                        $id_proveedor = NULL;
                    }


                    $fieldset = array(
                        "empresa_id" => $this->empresa_id,
                        "narracion" => $narracion,
                        "incluir_narracion" => $incluir_narracion,
                        "cuenta_id" => $cuenta_banco,
                        "proveedor_id" => $id_proveedor,
                        "cliente_id" => $id_cliente,
                        "fecha_inicio" => $fecha_inicio,
                        "codigo" => $codigo,
                        "uuid_recibo_dinero" => $uuid_recibos
                    );

                    //--------------------
                    // Guardar Descuento
                    //--------------------
                    $recibos = Movimiento_monetario_orm::create($fieldset);


                }

                $items = array();
                //Recorrer los dependientes
                $j = 0;
                foreach ($_POST["transacciones"] AS $item) {
                    //$fieldset = array();
                    $fieldset["created_at"] = date('Y-m-d H:i:s');
                    $fieldset["id_recibo"] = $recibos->id;
                    $fieldset["nombre"] = $item['nombre'];
                    $fieldset["cuenta_id"] = $item['cuenta_id'];
                    $fieldset["centro_id"] = $item['centro_id'];
                    $fieldset["credito"] = $item['credito'];

                    $items[] = new Items_recibos_orm($fieldset);
                    $j++;
                }

                $recibos->items()->saveMany($items);


                //Transacciones
                $this->MovimientosMonetariosRecibo->haceTransaccion($recibos);

            } catch (ValidationException $e) {


            }


            // If we reach here, then
            // data is valid and working.
            // Commit the queries!
            Capsule::commit();

            if ($recibos == true) {


                $mensaje = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente');

            } else {
                //Establecer el mensaje a mostrar

                $data["mensaje"]["clase"] = "alert-danger";
                $data["mensaje"]["contenido"] = "Hubo un error al tratar de crear el descuento.";

            }
            redirect(base_url('movimiento_monetario/listar_recibos'));
            $this->session->set_flashdata('mensaje', $mensaje);


        }






        $this->assets->agregar_css(array(
            'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
            'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
            'public/assets/css/modules/stylesheets/entrada_manual_crear.css',
            'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
            'public/assets/css/plugins/bootstrap/select2.min.css'
        ));
        $this->assets->agregar_js(array(
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
            'public/assets/js/plugins/jquery/combodate/combodate.js',
            'public/assets/js/plugins/jquery/combodate/momentjs.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            'public/assets/js/modules/movimiento_monetario/tabla-dinamica.jquery.js',
            'public/assets/js/default/formulario.js',
            'public/assets/js/plugins/bootstrap/select2/select2.min.js',
            'public/assets/js/plugins/bootstrap/select2/es.js',
            'public/assets/js/plugins/jquery/toastr.js',
            //'public/assets/js/modules/entrada_manual/routes.js'
        ));
        $data = array();
        $data['cliente_proveedor'] = Movimiento_cat_orm::lista();
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-calculator"></i> Recibos de dinero: Crear',

        );

        $recibos_info = array();
        if (!empty($recibos_uuid)) {


            $recibos = Movimiento_monetario_orm::findByUuid($recibos_uuid);
            $proveedores = $recibos->proveedor;
            $clientes = $recibos->cliente;
            $transacciones = $recibos->transacciones->toArray();
            $items = Items_recibos_orm::listar($transacciones[0]['id_recibo']);
            $data_recibos = $recibos;
            $data_transacciones = $transacciones;


            $valores = array_values($items);
            $sum = 0;
            foreach ($valores as $num => $values) {

                $sum += $values['credito'];

            }


            $breadcrumb = array("titulo" => '<i class="fa fa-calculator"></i> Recibos de dinero: ' . $data_recibos->codigo,

            );


            //Verificar cargo_id y crear variable js
            $es_cliente = $data_recibos->proveedor_id;
            if (!empty($es_cliente)) {

                $cliente_proveedor = 1;
            } else {

                $cliente_proveedor = 2;
            }

            $data['info'] = $cliente_proveedor;

            $this->assets->agregar_var_js(array(
                // "selected_departamento_id" => $descuento_info[0]["colaborador"]["id"],
                "cliente_proveedor" => $cliente_proveedor,
                "recibos_id" => $data_recibos->id
            ));

            if (!empty($data_recibos->proveedor_id)) {

                $cliente_proveedor = $proveedores->nombre;
                $id_cliente_proveedor = $data_recibos->proveedor_id;

            } else {

                $cliente_proveedor = $clientes->nombre;
                $id_cliente_proveedor = $data_recibos->cliente_id;
            }
            //----------------------------
            // Agregra variables PHP como variables JS
            //----------------------------
            $this->assets->agregar_var_js(array(
                "recibos_uuid" => $recibos_uuid,
                "permiso_editar" => $this->auth->has_permission('ver__editarDescuento', 'descuentos/ver/(:any)') ? 'true' : 'false',
            ));

            //Verificar cliente o proveedor
            if (!empty($cliente_proveedor) && $cliente_proveedor != "") {
                $this->assets->agregar_var_js(array(
                    "id_cliente_proveedor" => $id_cliente_proveedor,
                    "selected_cliente_proveedor" => $cliente_proveedor
                ));
            }

            //Agregar narracion
            if (!empty($data_recibos->narracion) && $data_recibos->narracion != "") {
                $this->assets->agregar_var_js(array(
                    "narracion" => $data_recibos->narracion
                ));
            }

            //agregar cuenta de banco
            if (!empty($data_recibos->cuenta_id) && $data_recibos->cuenta_id != "") {
                $this->assets->agregar_var_js(array(
                    "cuenta_id" => $data_recibos->cuenta_id
                ));
            }

            // agregar incuir narracion

            if (!empty($data_recibos->incluir_narracion) && $data_recibos->incluir_narracion != "") {
                $this->assets->agregar_var_js(array(
                    "incluir_narracion" => $data_recibos->incluir_narracion
                ));
            }


            // transacciones

            if (!empty($data_transacciones[0]) && $data_transacciones[0] != "") {
                $this->assets->agregar_var_js(array(
                    "transacciones" => json_encode($data_transacciones),
                    "cuentas_banco" => json_encode($items),
                    "credito_total" => number_format($sum, 2)
                ));
            }


    	}

    $this->template->agregar_titulo_header('Recibos de dinero');
    $this->template->agregar_breadcrumb($breadcrumb);
    $this->template->agregar_contenido($data);
    $this->template->visualizar();

  }

function crear_retiros($retiros_uuid = NULL){

    if(!empty($_POST["campo"])){

   if(!empty($_POST["campo"]["incluir"])){$check_narracion = "1";}else{$check_narracion = "0";}

        $retiros_id		= $_POST['campo']['id'];
        $narracion		= $_POST['campo']['nombre'];
        $incluir_narracion      = $check_narracion;
    	   $cuenta_banco       	= $_POST['campo']['cuenta_banco'];
    	  $tipo_pago_id       	= $_POST['campo']['tipo_pago_id'];
        $numero_cheque = $_POST['campo']['numero_cheque'];
        $nombre_banco_cheque = $_POST['campo']['nombre_banco_cheque'];
        $numero_tarjeta = $_POST['campo']['numero_tarjeta'];
        $numero_recibo = $_POST['campo']['numero_recibo'];
        $banco_proveedor = $_POST['campo']['banco_proveedor'];
        $numero_cuenta_proveedor = $_POST['campo']['numero_cuenta_proveedor'];  
        if($_POST['campo']['id_categoria'] == "1"){
        $id_proveedor           = $_POST['campo']['id_cliente_proveedor'];
        }else{
        $id_cliente             = $_POST['campo']['id_cliente_proveedor'];     
        }   
            $fecha_inicio = date('Y-m-d H:i:s');
            $fecha_inicio = !empty($fecha_inicio) ? str_replace('/', '-', $fecha_inicio) : "";
            $fecha_inicio = !empty($fecha_inicio) ? date("Y-m-d", strtotime($fecha_inicio)) : "";
            $j = 0;
            $uuid_retiros = Capsule::raw("ORDER_UUID(uuid())");
            $codigo = Capsule::raw("NO_RETIRO('WD', " . $this->empresa_id . ")");

            /**
             * Inicializar Transaccion
             */
            Capsule::beginTransaction();

            try {



                $retiros = Movimiento_retiros_orm::find($retiros_id);

                if (!empty($retiros)) {
                    $retiros->empresa_id = $this->empresa_id;
                    $retiros->narracion = $narracion;
                    $recibos->incluir_narracion = $incluir_narracion;
                    $retiros->cuenta_id = $cuenta_banco;
                    $retiros->tipo_pago_id      	= $tipo_pago_id;
                $retiros->numero_cheque = $numero_cheque;
                $retiros->nombre_banco_cheque = $nombre_banco_cheque;
                $retiros->numero_tarjeta = $numero_tarjeta;
                $retiros->numero_recibo = $numero_recibo;
                $retiros->banco_proveedor = $banco_proveedor;
                $retiros->numero_cuenta_proveedor = $numero_cuenta_proveedor;  
                    if($_POST['campo']['id_categoria'] == "1"){
                        $retiros->proveedor_id          = $id_proveedor;
                    }else{
                        $retiros->id_cliente            = $id_cliente;
                         }
    			$retiros->fecha_inicio 		= $fecha_inicio;
    			$retiros->save();
                }else{

                 if ($_POST['campo']['id_categoria'] == "1") {
                        $id_proveedor=$_POST['campo']['id_cliente_proveedor'];
                        $id_cliente = NULL;
                    } else {
                        $id_cliente=$_POST['campo']['id_cliente_proveedor'];
                        $id_proveedor = NULL;
                    }    
    			$fieldset = array(
    				"empresa_id" 		=> $this->empresa_id,
    				"narracion"             => $narracion,
                    "incluir_narracion"     => $incluir_narracion,
                    "cuenta_id"             => $cuenta_banco,
                    "tipo_pago_id"             => $tipo_pago_id,
                    "numero_cheque" => $numero_cheque,
                    "nombre_banco_cheque" => $nombre_banco_cheque,
                    "numero_tarjeta" => $numero_tarjeta,
                    "numero_recibo" => $numero_recibo,
                    "banco_proveedor" => $banco_proveedor,
                    "numero_cuenta_proveedor" => $numero_cuenta_proveedor,
    				"proveedor_id" 		=> $id_proveedor,
                                "cliente_id"            => $id_cliente,
    				"fecha_inicio" 		=> $fecha_inicio,
                                "codigo"                => $codigo,
                                "uuid_retiro_dinero"    => $uuid_retiros
    			);
                    
                    //--------------------
                    // Guardar Descuento
                    //--------------------
                    $retiros = Movimiento_retiros_orm::create($fieldset);

                }

                $items = array();
                //Recorrer los dependientes
                $j = 0;
                foreach ($_POST["transacciones"] AS $item) {
                    //$fieldset = array();
                    $fieldset["created_at"] = date('Y-m-d H:i:s');
                    $fieldset["id_retiro"] = $retiros->id;
                    $fieldset["nombre"] = $item['nombre'];
                    $fieldset["cuenta_id"] = $item['cuenta_id'];
                    $fieldset["centro_id"] = $item['centro_id'];
                    $fieldset["debito"] = $item['debito'];

                    $items[] = new Items_retiros_orm($fieldset);
                    $j++;
                }

                $retiros->items()->saveMany($items);

                //Transacciones
                //$this->MovimientosMonetariosRetiro->haceTransaccion($retiros); //Las Transacciones deben de contabilizarse en pagos
                //  $this->_createPago( $retiros );
                //$pago = new Pagos_contratos_orm;
                $pago = $this->_createPago($retiros);
                $this->_setPagables($pago, $retiros); //model sync();
                $this->_setMetodosPagos($pago, $retiros);

            } catch (ValidationException $e) {

            }


            // If we reach here, then
            // data is valid and working.
            // Commit the queries!
            Capsule::commit();

            if ($retiros == true) {


                $mensaje = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente');

            } else {
                //Establecer el mensaje a mostrar

                $data["mensaje"]["clase"] = "alert-danger";
                $data["mensaje"]["contenido"] = "Hubo un error al tratar de crear el descuento.";

            }
            redirect(base_url('movimiento_monetario/listar_retiros'));
            $this->session->set_flashdata('mensaje', $mensaje);


        }
        
        $data = array();
        $data['cliente_proveedor'] = Movimiento_cat_orm::lista();
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-calculator"></i> Retiros de dinero: Crear',

    );
    
    $this->assets->agregar_var_js(array(
        'vista' => 'crear'
    ));

        $retiros_info = array();
        if (!empty($retiros_uuid)) {


            $retiros = Movimiento_retiros_orm::findByUuid($retiros_uuid);
            $proveedores = $retiros->proveedor;
            $clientes = $retiros->cliente;
            $transacciones = $retiros->transacciones->toArray();
            $items = Items_retiros_orm::listar($transacciones[0]['id_retiro']);
            $data_retiros = $retiros;
            $data_transacciones = $transacciones;

            $valores = array_values($items);
            $sum = 0;
            foreach ($valores as $num => $values) {

                $sum += $values['debito'];

            }


            $breadcrumb = array("titulo" => '<i class="fa fa-calculator"></i> Retiros de dinero: ' . $data_retiros->codigo,

            );
            $this->assets->agregar_var_js(array(
            'vista' => 'ver'
            ));

            //Verificar cargo_id y crear variable js
            $es_cliente = $data_retiros->proveedor_id;
            if (!empty($es_cliente)) {

                $cliente_proveedor = 1;
            } else {

                $cliente_proveedor = 2;
            }

            $data['info'] = $cliente_proveedor;

            $this->assets->agregar_var_js(array(
                // "selected_departamento_id" => $descuento_info[0]["colaborador"]["id"],
                "cliente_proveedor" => $cliente_proveedor,
                "retiros_id" => $data_retiros->id
            ));

            if (!empty($data_retiros->proveedor_id)) {

                $cliente_proveedor = $proveedores->nombre;
                $id_cliente_proveedor = $data_retiros->proveedor_id;

            } else {

                $cliente_proveedor = $clientes->nombre;
                $id_cliente_proveedor = $data_retiros->cliente_id;
            }
            //----------------------------
            // Agregra variables PHP como variables JS
            //----------------------------
            $this->assets->agregar_var_js(array(
            	"retiros_uuid" => $retiros_uuid,
            	"permiso_editar" => $this->auth->has_permission('ver__editarDescuento', 'descuentos/ver/(:any)') ? 'true' : 'false',
            ));

            $data["retiro_id"] = $data_retiros->id;
            //Verificar cliente o proveedor
            if (!empty($cliente_proveedor) && $cliente_proveedor != "") {
                $this->assets->agregar_var_js(array(
                    "id_cliente_proveedor" => $id_cliente_proveedor,
                    "selected_cliente_proveedor" => $cliente_proveedor
                ));
            }

            //Agregar narracion
            if (!empty($data_retiros->narracion) && $data_retiros->narracion != "") {
                $this->assets->agregar_var_js(array(
                    "narracion" => $data_retiros->narracion
                ));
            }

            //agregar cuenta de banco
            if (!empty($data_retiros->cuenta_id) && $data_retiros->cuenta_id != "") {
                $this->assets->agregar_var_js(array(
                    "cuenta_id" => $data_retiros->cuenta_id
                ));
            }
            //agregar nombre cheque
            if(!empty($data_retiros->numero_cheque) && $data_retiros->numero_cheque != ""){
            	$this->assets->agregar_var_js(array(
            		"numerocheque" => $data_retiros->numero_cheque
            	));
            }
             //agregar nombre banco cheque
            if(!empty($data_retiros->nombre_banco_cheque) && $data_retiros->nombre_banco_cheque != ""){
            	$this->assets->agregar_var_js(array(
            		"nombrebancocheque" => $data_retiros->nombre_banco_cheque
            	));
            }
            //agregar numero tarjeta
            if(!empty($data_retiros->numero_tarjeta) && $data_retiros->numero_tarjeta != ""){
            	$this->assets->agregar_var_js(array(
            		"numerotarjeta" => $data_retiros->numero_tarjeta
            	));
            }
             //agregar numero recibo
            if(!empty($data_retiros->numero_recibo) && $data_retiros->numero_recibo != ""){
            	$this->assets->agregar_var_js(array(
            		"numerorecibo" => $data_retiros->numero_recibo
            	));
            }
            //agregar banco proveedor
            if(!empty($data_retiros->banco_proveedor) && $data_retiros->banco_proveedor != ""){
            	$this->assets->agregar_var_js(array(
            		"bancoproveedor" => $data_retiros->banco_proveedor
            	));
            }
            //agregar numero cuenta proveedor
            if(!empty($data_retiros->numero_cuenta_proveedor) && $data_retiros->numero_cuenta_proveedor != ""){
            	$this->assets->agregar_var_js(array(
            		"numerocuentaproveedor" => $data_retiros->numero_cuenta_proveedor
            	));
            }

            //agregar cuenta de banco
            if(!empty($data_retiros->tipo_pago_id) && $data_retiros->tipo_pago_id != ""){
            	$this->assets->agregar_var_js(array(
            		"tipopagoid" => $data_retiros->tipo_pago_id
            	));
            }

            // agregar incuir narracion

            if (!empty($data_retiros->incluir_narracion) && $data_retiros->incluir_narracion != "") {
                $this->assets->agregar_var_js(array(
                    "incluir_narracion" => $data_retiros->incluir_narracion
                ));
            }


            // transacciones

            if (!empty($data_transacciones[0]) && $data_transacciones[0] != "") {
                $this->assets->agregar_var_js(array(
                    "transacciones" => json_encode($data_transacciones),
                    "cuentas_banco" => json_encode($items),
                    "debito_total" => number_format($sum, 2)
                ));
            }
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
            'public/assets/css/modules/stylesheets/pagos.css',
            'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
            'public/assets/css/plugins/bootstrap/select2.min.css'
        ));
        $this->assets->agregar_js(array(
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
            'public/assets/js/plugins/jquery/combodate/combodate.js',
            'public/assets/js/plugins/jquery/combodate/momentjs.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            'public/assets/js/modules/movimiento_monetario/tabla-dinamica.jquery.js',
            'public/assets/js/default/formulario.js',
            'public/assets/js/plugins/bootstrap/select2/select2.min.js',
            'public/assets/js/plugins/bootstrap/select2/es.js',
            //'public/assets/js/modules/entrada_manual/routes.js'
            'public/assets/js/plugins/jquery/toastr.js',

        ));
        $this->template->agregar_titulo_header('Retiros de dinero');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();

    }


    public function ocultoformulariorecibos($data = NULL)
    {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/movimiento_monetario/crear_recibos.js'
        ));

        $this->load->view('formulario', $data);
    }


public function ocultoformularioretiros($data=NULL)
{
    $this->assets->agregar_js(array(
      'public/assets/js/modules/movimiento_monetario/crear_retiros.js'
    ));
    $this->load->view('formulario_retiros', $data);
}

    function ocultoformcomentariorecibo($data = NULL)
    {
        $this->assets->agregar_js(array(
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/modules/movimiento_monetario/controller_comentario.js'
        ));

        $this->load->view('formulario_comentario_recibos', $data);
    }

    function ocultoformcomentarioretiro($data = NULL)
    {
        $this->assets->agregar_js(array(
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/modules/movimiento_monetario/controller_comentario_retiros.js'
        ));


        $this->load->view('formulario_comentario_retiros', $data);
    }


    function ajax_getComentarioRecibos()
    {
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $recibos_id = $this->input->post('recibos_id');
        $condicion = array('recibos_id' => $recibos_id);
        $comentarios = Comentario_recibos_orm::where($condicion)->orderBy('created_at', 'desc')->get();

        if (!is_null($comentarios)) {
            $response = $comentarios->toArray();
        } else {
            $response = array();
        }


        //print_r($a->usuarios->toArray());
        echo json_encode($response);
        exit;
    }

    function ajax_getComentarioRetiros()
    {
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $retiros_id = $this->input->post('retiros_id');
        $condicion = array('retiros_id' => $retiros_id);
        $comentarios = Comentario_retiros_orm::where($condicion)->orderBy('created_at', 'desc')->get();

        if (!is_null($comentarios)) {
            $response = $comentarios->toArray();
        } else {
            $response = array();
        }


        //print_r($a->usuarios->toArray());
        echo json_encode($response);
        exit;
    }

    function ajax_postComentarioRecibos()
    {
        $datos = array();
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $uuid_usuario = $this->session->userdata('huuid_usuario');
        $usuario = Usuario_orm::findByUuid($uuid_usuario);
        $datos['comentario'] = $this->input->post('comentario');
        $datos['recibos_id'] = $this->input->post('recibos_id');
        $datos['usuario_id'] = $usuario->id;
        $datos['empresa_id'] = $empresa->id;
        $comentario = Comentario_recibos_orm::create($datos);

        $usuario->comentario()->save($comentario->fresh());
        $response = array();
        if (!is_null($comentario)) {
            $condicion = array('recibos_id' => $datos['recibos_id']);
            $response = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente', 'comentario' => $comentario->fresh()->toArray());
        } else {
            $response = array('estado' => 500, 'mensaje' => '<b>¡Error!</b> Su solicitud no fue procesada ');
        }
        echo json_encode($response);
        exit;
    }

    function ajax_postComentarioRetiros()
    {
        $datos = array();
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $uuid_usuario = $this->session->userdata('huuid_usuario');
        $usuario = Usuario_orm::findByUuid($uuid_usuario);
        $datos['comentario'] = $this->input->post('comentario');
        $datos['retiros_id'] = $this->input->post('retiros_id');
        $datos['usuario_id'] = $usuario->id;
        $datos['empresa_id'] = $empresa->id;
        $comentario = Comentario_retiros_orm::create($datos);

        $usuario->comentario()->save($comentario->fresh());
        $response = array();
        if (!is_null($comentario)) {
            $condicion = array('retiros_id' => $datos['retiros_id']);
            $response = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente', 'comentario' => $comentario->fresh()->toArray());
        } else {
            $response = array('estado' => 500, 'mensaje' => '<b>¡Error!</b> Su solicitud no fue procesada ');
        }
        echo json_encode($response);
        exit;
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

        /**
         * Inicializar Transaccion
         */
        Capsule::beginTransaction();

        try {

            $response = Movimiento_monetario_orm::find($id);
            $response->estado = "0";
            $response->save();

        } catch (ValidationException $e) {

            // Rollback
            Capsule::rollback();

            log_message("error", "MODULO: " . __METHOD__ . ", Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");

            echo json_encode(array(
                "response" => false,
                "mensaje" => "Hubo un error tratando de eliminar la deducci&oacute;n."
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

        /**
         * Inicializar Transaccion
         */
        Capsule::beginTransaction();

        try {

            $response = Movimiento_retiros_orm::find($id);
            $response->estado = "0";
            $response->save();

        } catch (ValidationException $e) {

            // Rollback
            Capsule::rollback();

            log_message("error", "MODULO: " . __METHOD__ . ", Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");

            echo json_encode(array(
                "response" => false,
                "mensaje" => "Hubo un error tratando de eliminar la deducci&oacute;n."
            ));
            exit;
        }

        // If we reach here, then
        // data is valid and working.
        // Commit the queries!
        Capsule::commit();

        die;
    }

    private function _setPagables($pago, $retiro)
    {

        $pago->retiros()->sync($this->_getPagosPagablesFromPost($retiro, "Flexio\\Modulo\\MovimientosMonetarios\\Models\\MovimientosRetiros"));

    }


    private function _getPagosPagablesFromPost($movimiento, $pagable_type)
    {
        $aux = [];
        $total = $movimiento->items->sum('debito');
        foreach ($movimiento->items as $documento) {//factura o planilla
            $aux[$movimiento->id] = [
                "pagable_type" => $pagable_type,
                "monto_pagado" => $total,
                "empresa_id" => $this->empresa_id
            ];
        }
        return $aux;
    }


    private function _setMetodosPagos($pago, $retiro) {
      //$forma_pago = $retiro->proveedor->metodo_pagos->first()->catalogo_id;
      $tipo_pago="";
      //Corregir este codigo esta quemado por apuro
        if($retiro->tipo_pago_id == 17){
            $tipo_pago = 'efectivo';
             $referencia = $this->pagoGuardar->tipo_pago(
                'efectivo', array(
                      'efectivo'=>'efectivo'                     
              ));
        }
        if($retiro->tipo_pago_id == 18){
            $tipo_pago = 'credito_favor';
             $referencia = $this->pagoGuardar->tipo_pago(
                'credito_favor', array(
                      'credito_favor'=>'credito_favor'                     
              ));
        }
        if($retiro->tipo_pago_id == 19){
             $tipo_pago = 'cheque';
             $referencia = $this->pagoGuardar->tipo_pago(
                'cheque', array(
                      'nombre_banco_cheque'=>$retiro->numero_cheque,
                      'cuenta_proveedor'=>$retiro->nombre_banco_cheque
              ));       
        }
        if($retiro->tipo_pago_id == 20){
             $tipo_pago = 'tarjeta_credito';
             $referencia = $this->pagoGuardar->tipo_pago(
                'tarjeta_de_credito', array(
                      'numero_tarjeta'=>$retiro->numero_tarjeta,
                      'numero_recibo'=>$retiro->numero_recibo
              ));           
        }
        if($retiro->tipo_pago_id == 21){
             $tipo_pago = 'ach';
             $referencia = $this->pagoGuardar->tipo_pago(
                 'ach', array(
                       'nombre_banco_ach'=>$retiro->banco_proveedor,
                       'cuenta_proveedor'=>$retiro->numero_cuenta_proveedor
               ));
        }
        if($retiro->tipo_pago_id == 22){
            $tipo_pago = 'transferencia_internacional';
             $referencia = $this->pagoGuardar->tipo_pago(
                'transferencia_internacional', array(
                      'transferencia_internacional'=>'transferencia_internacional'                     
              ));
        }
               

        $item_pago = new Pago_metodos_pago_orm;

        $item_pago->tipo_pago = $tipo_pago; //Por apuro quemado
        $item_pago->total_pagado = $retiro->items->sum('debito');       
        $item_pago->referencia = $referencia;
        $pago->metodo_pago()->save($item_pago);
        //  }
    }


    private function _createPago($movimiento_monetario_info)
    {

        //$total = Pagos_contratos_orm::deEmpresa($this->empresa_id)->count();
        $total = Pagos_orm::deEmpresa($movimiento_monetario_info->empresa_id)->count();
        $year = Carbon::now()->format('y');
        $codigo = Util::generar_codigo('PGO' . $year, $total + 1);

        $pago = new Pagos_orm;
        $pago->codigo = $codigo;
        $pago->empresa_id = $this->empresa_id;
        $pago->fecha_pago = date("Y-m-d"); //date("Y-m-d", strtotime($post["campo"]["fecha_pago"]));
        $pago->proveedor_id = $movimiento_monetario_info->proveedor_id;
        $pago->depositable_id = $movimiento_monetario_info->cuenta_id;
        $pago->depositable_type = 'Flexio\Modulo\Contabilidad\Models\Cuentas';
        $pago->empezable_id = $movimiento_monetario_info->id;
        $pago->empezable_type = 'Flexio\Modulo\MovimientosMonetarios\Models\MovimientosRetiros';
        $pago->monto_pagado = $movimiento_monetario_info->items->sum('debito');
        $pago->cuenta_id = $movimiento_monetario_info->cuenta_id;
        $pago->formulario = 'movimiento_monetario';
        $pago->estado = 'por_aprobar';
        $pago->save();

        return $pago;
    }


    /*  private function _createPago( $movimiento_monetario_info ) {

          $total = Pagos_orm::deEmpresa($movimiento_monetario_info->empresa_id)->count();
          $year = Carbon::now()->format('y');
          //$comisionInfo->load('colaboradores.colaborador','cuenta_info');

          $post['campo']['cuenta_id'] = $movimiento_monetario_info->cuenta_id;

          $contador = 1;
           //if(count($comisionInfo->colaboradores)>0){
           //foreach ($comisionInfo->colaboradores as $key => $value) {
             //if($value->monto_neto > 0){
               $aux = [];
               $pago = new Pagos_orm;
               $codigo = Util::generar_codigo('PGO' . $year, $total + $contador);
               $total_pagado_nuevo = (float)str_replace(",","",$value->monto_neto);
               $pago->codigo = $codigo;

               $pago->empresa_id = $comisionInfo->empresa_id;
               $pago->fecha_pago = date("Y-m-d");
               $pago->proveedor_id = $value->colaborador_id;
               $pago->monto_pagado = $total_pagado_nuevo;
               $pago->cuenta_id = $comisionInfo->cuenta_info->id;
               $pago->depositable_id = $comisionInfo->id;
               $pago->depositable_type = 'Movimiento_retiros_orm';
               $pago->formulario = 'movimiento_monetario';
               $pago->estado = 'por_aplicar';

               $pago->save();

               $aux[$comisionInfo->id] = array(
                   "pagable_type" =>'Flexio\\Modulo\\Comisiones\\Models\\Comisiones',
                   "monto_pagado" =>  $total_pagado_nuevo,
                   "empresa_id" => $comisionInfo->empresa_id
               );
                $pago->pagos_extraordinarios()->sync($aux);

               $item_pago = new Pago_metodos_pago_orm;

               $referencia = $this->pagoGuardar->tipo_pago('ach', array(
                 'nombre_banco_ach'=>$value->colaborador->banco_id,
                 'cuenta_proveedor'=>$value->colaborador->numero_cuenta
               ));

               $item_pago->tipo_pago = 'ach';
               $item_pago->total_pagado = $value->monto_total;
               $item_pago->referencia = $referencia;
               $pago->metodo_pago()->save($item_pago);
               ++$contador;
             //}
          //  }
        //  }

      //  }
    }*/
    function documentos_campos() {
       return true;
    }

    function ajax_guardar_documentos() {
        if (empty($_POST)) {
            return false;
        }

        $retiro_id = $this->input->post('retiro_id', true);
        $modeloInstancia = MovimientosRetiros::find($retiro_id);

        $this->documentos->subir($modeloInstancia);
    }
}


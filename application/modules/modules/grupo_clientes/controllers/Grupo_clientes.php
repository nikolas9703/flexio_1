<?php

/**
 * Grupos de Clientes
 *
 * Modulo para administrar la creacion, edicion de grupos de clientes.
 *
 * @package    PensaApp
 * @subpackage Controller
 * @category   Controllers
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  03/15/2016
 */
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\FormularioDocumentos AS FormularioDocumentos;
use League\Csv\Writer as Writer;
use Carbon\Carbon;

class Grupo_clientes extends CRM_Controller {

    private $id_empresa;
    private $id_usuario;
    private $empresaObj;

    function __construct() {
        parent::__construct();

        //MODULOS
        //$this->load->model("usuarios/Empresa_orm");
        $this->load->model("facturas/Factura_orm");
        $this->load->model("cobros/Cobro_orm");
        $this->load->model('clientes/Cliente_orm');
        $this->load->model('Grupo_cliente_orm');
        $this->load->model('clientes/Catalogo_orm');
        $this->load->model('clientes/Catalogo_toma_contacto_orm');
        $this->load->model('Grupo_cliente_agrupador_orm');

        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();
        $uuid_empresa = $this->session->userdata('uuid_empresa');

        //$this->empresaObj  = Empresa_orm::findByUuid($uuid_empresa);
        $empresaObj = new Buscar(new Empresa_orm, 'uuid_empresa');
        $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
        $this->id_usuario = $this->session->userdata("huuid_usuario");
        //    echo $this->id_usuario;
        $this->id_empresa = $this->empresaObj->id;
        //    echo $this->id_empresa;
    }

    public function index() {
        redirect("grupo_clientes/listar");
    }

    public function listar() {
        //echo "<br>listar";
        // Verificar si tiene permiso para crear cliente Natural
        if (!$this->auth->has_permission('acceso')) {
            // No, tiene permiso, redireccionarlo.
            redirect('/');
        }
        //variables
        $data = array();
        $camposGrid = array();

        $this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
            'public/assets/css/plugins/jquery/switchery.min.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
            'public/assets/css/plugins/jquery/fileinput/fileinput.css',
            'public/assets/css/modules/stylesheets/grupo_clientes.css'
        ));
        /* Archivos js para la vista de Crear Actividades */
        $this->assets->agregar_js(array(
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/plugins/jquery/jqgrid/plugins/jQuery.jqGrid.columnToggle.js',
            'public/assets/js/plugins/jquery/switchery.min.js',
            'public/assets/js/default/subir_documento_modulo.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/plugins/bootstrap/bootstrap-tabdrop.js',
            'public/assets/js/default/jqgrid-toggle-resize.js',
            'public/assets/js/default/formulario.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
            /* Archivos js del propio modulo */
            'public/assets/js/modules/grupo_clientes/listar.js',
            'public/assets/js/modules/grupo_clientes/routes.js',
            'public/assets/js/modules/grupo_clientes/tabla.js',
                // 'public/assets/js/modules/grupo_clientes/crear.js',
                //'public/assets/js/modules/contabilidad/crear_cuenta.js',
        ));
        $total_cuenta = 0;
        //$opciones = array('Agrupar', 'Exportar.');
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-line-chart"></i> Grupos de Clientes',
            "ruta" => array(
                0 => array(
                    "nombre" => "Ventas",
                    "activo" => false
                ),
                1 => array(
                    "nombre" => '<b>Grupos de Clientes</b>',
                    "activo" => true
                )
            ),
            "menu" => array(
                "nombre" => "Crear",
                "url" => "javascript:",
                "clase" => "open-modal-crear",
                "opciones" => array()
            )
        );
        $menuOpciones["#exportarClientePotencialBtn"] = "Exportar";
        $breadcrumb["menu"]["opciones"] = $menuOpciones;
        unset($data["mensaje"]);

        $this->template->agregar_titulo_header('Listado de Grupos de Clientes');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }

    public function ajax_listar($grid = NULL) {

        Capsule::enableQueryLog();
        $registros = Grupo_cliente_orm::deEmpresa($this->id_empresa);

        $ids = $this->input->post('ids', true);
         $clause = array();
         if (!empty($ids)){
             $clause['ids'] = $ids;
         }else{
             $clause['empresa_id'] = $this->empresaObj->id;
         }
         
        $page = (int) $this->input->post('page', true);

        /**
         * Get how many rows we want to have into the grid
         * rowNum parameter in the grid.
         * @var int
         */
        $sidx = $this->input->post('sidx', true);

        $sord = $this->input->post('sord', true);

        $limit = (int) $this->input->post('rows', true);

        $count = $registros->count();

        $start = $limit * $page - $limit;

        if ($start < 0)
            $start = 0;


        $registros->orderBy($sidx, $sord)
                ->skip($start)
                ->take($limit);

        /**
         * Calcule total pages if $coutn is higher than zero.
         * @var int
         */
        $total_pages = ($count > 0 ? ceil($count / $limit) : 0);

        // if for some reasons the requested page is greater than the total
        // set the requested page to total page
        if ($page > $total_pages)
            $page = $total_pages;

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = Grupo_cliente_orm::lista_totales($clause);
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $clientes = Grupo_cliente_orm::listar($clause, $sidx, $sord, $limit, $start);
        
        /*  echo '<h2>Consultando Antes ROWS:</h2><pre>';
          print_r($clientes->toArray());
          echo '</pre>'; */

//Constructing a JSON
        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
        //  $response->result = array();
        $i = 0;

        if (!empty($clientes->toArray())) {
            foreach ($clientes->toArray() AS $i => $row) {

                $uuid_grupo = bin2hex($row['uuid_grupo']);
                $id = $row['id'];
                $agrupador = Grupo_cliente_agrupador_orm::getIdsClientes($id);
                $grupoId = $agrupador->toArray();
                //$clause['uuid_cliente'] = $grupoId;
                $clientesInf = Cliente_orm::operations($grupoId, $sidx, $sord, $limit, $start);

                $total_credito = 0;
                $total_saldo = 0;
                if (!empty($clientesInf->toArray())) {
                    $n = 0;
                    foreach ($clientesInf as $rows) {

                        $total_credito += $rows->credito_favor;
                        $total_saldo += $rows->total_saldo_pendiente();
                        $n++;
                    }
                }

                Grupo_cliente_orm::updateCreditSaldo($row['id'], $total_credito, $total_saldo);

                $link_option = '<center><button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row['id'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button></center>';
                $hidden_options = "";


                $hidden_options .= '<a href="#" data-id="' . $row['id'] . '" class="TablaGrupoCliente btn btn-block btn-outline btn-success" id="agregarClienteBtn">Agregar Cliente</a>';
                $hidden_options .= '<a href="#" data-id="' . $row['id'] . '" class="TablaGrupoCliente btn btn-block btn-outline btn-success" id="editarClienteBtn">Editar</a>';
                $hidden_options .= '<a href="#" data-id="' . $row['id'] . '" class="TablaGrupoCliente btn btn-block btn-outline btn-success" id="eliminarClienteBtn">Eliminar</a>';
                $response->rows[$i]["id"] = $row['id'];
                $response->rows[$i]["cell"] = array(
                    Util::verificar_valor($row['nombre']),
                    Util::verificar_valor($row['descripcion']),
                    '<label class="totales-success">' . number_format($total_credito, 2, '.', ',') . '</label>',
                    '<label class="totales-danger">' . number_format($total_saldo, 2, '.', ',') . '</label>',
                    $link_option,
                    $hidden_options
                );
                $i++;
            }
        }

        echo json_encode($response);
        exit;
    }

    public function ocultotabla($uuid = NULL, $modulo = "") {
//If ajax request
//echo "ocultar tabla";
        $this->assets->agregar_js(array(
            'public/assets/js/modules/grupo_clientes/tabla.js'
        ));

        $this->load->view('tabla');
    }

    public function crear() {
        $this->assets->agregar_css(array(
            'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
            'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/css/modules/stylesheets/clientes.css'
        ));
        $this->assets->agregar_js(array(
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
            'public/assets/js/plugins/jquery/combodate/combodate.js',
            'public/assets/js/plugins/jquery/combodate/momentjs.js',
            'public/assets/js/default/lodash.min.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            'public/assets/js/default/formulario.js',
            'public/assets/js/modules/grupo_clientes/routes.js',
            'public/assets/js/modules/grupo_clientes/listar.js',
                //'public/assets/js/modules/grupo_clientes/crear.js',
        ));

        $data = array();
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-line-chart"></i> Crear Grupo de Cliente',
        );
        $this->assets->agregar_var_js(array(
            'tipo_id' => 'null',
            'balance' => 0
        ));
        $total = Cliente_orm::where('empresa_id', '=', $this->id_empresa)->count();

        $data['info']['codigo'] = Util::generar_codigo('CUS', $total + 1);
        // $data['info']['provincias'] = Catalogo_orm::where('tipo', '=', 'provincias')->get(array('id', 'valor'));
        // $data['info']['letras'] = Catalogo_orm::where('tipo', '=', 'letras')->get(array('key', 'valor'));
        $data['info']['toma_contacto'] = Catalogo_toma_contacto_orm::all();

        $this->template->agregar_titulo_header('Crear Grupo de Cliente');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    public function ocultoformulario() {
        // $id_clientes['expression'] = $this->input->post('id_clientes', true);
        // dd($id_clientes);
        $this->assets->agregar_js(array(
            'public/assets/js/modules/grupo_clientes/crear.js'
        ));

        $this->load->view('crear');
    }

    public function ajax_guardar() {

        if (!$this->input->is_ajax_request()) {
            return false;
        }
        $response = new stdClass();
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        //$uuid_grupo = $this->session->userdata('uuid_grupo');
        //$grupo = Grupo_cliente_orm::findByUuid($uuid_grupo);
        $campos = array();
        //Parametros
        $id = $this->input->post('ids', true);
        //dd($id);
        $nombre = $this->input->post('campo[nombre]');
        //dd($nombre);
        $descripcion = $this->input->post('campo[descripcion]');
        $padre_id = $this->input->post('campo[padre_id]');
        $padre_ids = (int) $padre_id;

        Capsule::beginTransaction();

        //Campos
        //$campos['id'] = $$id;
        $campos['nombre'] = $nombre;
        $campos['descripcion'] = $descripcion;
        $campos['empresa_id'] = $empresa->id;
        $campos['id_catalog_agrupador'] = $padre_id;
        //dd($campos);
        try {
            if ($id != '') {
                $nuevo = Grupo_cliente_orm::guardar($id, $nombre, $descripcion, $padre_ids);
                $response = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha actualizado correctamente ' . $nombre);
            } else {
                $nuevo = Grupo_cliente_orm::create($campos);
                $response = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente ' . $nombre);
            }

            // $nuevo = Grupo_cliente_orm::create($campos);
        } catch (Exception $e) {
            log_message('error', $e);
            Capsule::rollback();
            $response = array('estado' => 500, 'mensaje' => '<b>¡Error! Su solicitud no fue procesada</b> ');
        }

        Capsule::commit();
        echo json_encode($response);
        exit;
    }

    public function exportar() {

        $clause = array('empresa_id' => $this->empresaObj->id);
        $clientes = Grupo_cliente_orm::listar($clause, NULL, NULL, NULL, NULL);
        if (empty($clientes)) {
            return false;
        }

        $i = 0;
        foreach ($clientes as $row) {

            $datos_excel[$i]['nombre'] = Util::verificar_valor($row['nombre']);
            $datos_excel[$i]['descripcion'] = utf8_decode(Util::verificar_valor($row['descripcion']));
            $datos_excel[$i]['credito_a_favor'] = utf8_decode(Util::verificar_valor($row['credito_a_favor']));
            $datos_excel[$i]['saldo_acumulado'] = utf8_decode(Util::verificar_valor($row['saldo_acumulado']));
            $i++;
        }

        $csv = Writer::createFromFileObject(new SplTempFileObject());
        $csv->insertOne([
            'Nombre',
            'Descripcion',
            'Credito a Favor',
            'Saldo Acumulado'
        ]);
        $csv->insertAll($datos_excel);
        $csv->output("GruposClientes-" . date('ymd') . ".csv");
        die;
    }

    public function eliminar() {
        $id_clientes = $this->input->post('id_clientes', true);
        $id = $id_clientes[0];
        //dd($id_clientes);
        if (empty($id)) {
            return false;
        }
        if (!$this->input->is_ajax_request()) {
            return false;
        }
        $response = Grupo_cliente_orm::eliminar($id);
        $json = '{"results":[' . json_encode($response) . ']}';
        echo $json;
        die;
    }

    public function ajax_ver() {
        $id_clientes = $this->input->post('id_clientes', true);
        $data = Grupo_cliente_orm::selectGrupoData($id_clientes);
        //  print_r($data->toArray());
        /* if ($data != null) {
          $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
          ->set_output(json_encode($data->toArray()))->_display();
          } */
        $json = json_encode($data);
        // print_r($json);
        echo $json;
        // echo json_encode($data);
        exit;
    }

    public function ajax_listar_clientes() {
        // echo 'ajax listar clientes';
        //die;         
        if (!$this->input->is_ajax_request()) {
            return false;
        }
        /*
          paramentos de busqueda aqui
         */
        $id = $this->input->post('id');
        //dd($id);
        $nombre = $this->input->post('nombre');
        $telefono = $this->input->post('telefono');
        $correo = $this->input->post('correo');

        //dd($nombre);
        $clause = array();

        if (!empty($nombre))
            $clause['nombre'] = $nombre;
        if (!empty($telefono))
            $clause['telefono'] = $telefono;
        if (!empty($correo))
            $clause['correo'] = $correo;

        $agrupador = Grupo_cliente_agrupador_orm::getIdsClientes($id);
        // $clause['id'] = $agrupador->toArray();
        $grupoId = $agrupador->toArray();
        $clause['uuid'] = $grupoId;
        // dd($grupoId);
        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = Cliente_orm::lista_totales_clientes($clause);
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $clientes = Cliente_orm::listar_clientes($clause, $sidx, $sord, $limit, $start);


        // dd($agrupador);
        //Constructing a JSON
        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->record = $count;

        $i = 0;

        if (!empty($clientes->toArray())) {
            $i = 0;
            foreach ($clientes as $row) {
                $hidden_options = "";
                $link_option = '<button class="viewOptionss btn btn-success btn-sm" type="button" data-inf="1" data-id="' . $row->uuid_cliente . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $hidden_options .= '<a href="' . base_url('clientes/ver/' . $row->uuid_cliente) . '" data-id="' . $row->uuid_cliente . '" data-name="' . $row->nombre . '" class="exportarTablaCliente btn btn-block btn-outline btn-success">Ver cliente</a>';
                $hidden_options .= '<a href="#" data-id="' . $row->uuid_cliente . '" data-name="' . $row->nombre . '" class="exportarTablaCliente btn btn-block btn-outline btn-success" id="desagruparClienteBtn">Desagrupar</a>';
                $saldo = empty($row->saldo) ? "0.00" : $row->saldo;
                $response->rows[$i]["id"] = $row->uuid_cliente;
                $response->rows[$i]["cell"] = array(
                    $row->uuid_cliente,
                    $row->codigo,
                    '<a class="link" href="' . base_url('clientes/ver/' . $row->uuid_cliente) . '" class="link">' . $row->nombre . '</a>',
                    $row->telefono,
                    $row->correo,
                    '<label class="totales-success">' . number_format($row->credito_favor, 2, '.', ',') . '</label>',
                    '<label class="totales-danger">' . number_format($row->total_saldo_pendiente(), 2, '.', ',') . '</label>',
                    $link_option,
                    $hidden_options
                );
                // $sald = $row->total_saldo_pendiente();
                //$total_saldo = $total_saldo + $sald;
                $i++;
            }
            // dd($total_saldo);
        }
        echo json_encode($response);
        exit;
    }

    public function desagrupar() {
        $id_clientes = $this->input->post('id_clientes', true);
        $id = $id_clientes[0];
        if (empty($id)) {
            return false;
        }
        if (!$this->input->is_ajax_request()) {
            return false;
        }
        $uuid_clientes = collect($id);
        // dd($uuid_clientes);
        $uuid_clientes->transform(function ($item) {
            return hex2bin($item);
        });
        //dd($id_grup);
        $uuuid = $uuid_clientes->toArray();
        // dd($id);

        $response = Grupo_cliente_agrupador_orm::desagrupar($uuuid);
        $json = '{"results":[' . json_encode($response) . ']}';
        echo $json;
        die;
    }

    public function ajax_buscar() {

        if (!$this->input->is_ajax_request()) {
            return false;
        }
        /*
          paramentos de busqueda aqui
         */
        $nombre = $this->input->post('nombre');
        $telefono = $this->input->post('telefono');
        $correo = $this->input->post('correo');

        //dd($nombre);
        $clause = array();

        if (!empty($nombre))
            $clause['nombre'] = $nombre;
        if (!empty($telefono))
            $clause['telefono'] = $telefono;
        if (!empty($correo))
            $clause['correo'] = $correo;

        $cliente = Cliente_orm::searchClient($clause);
        //dd($cliente->toArray());
        if (!empty($cliente->toArray())) {
            $i = 0;
            foreach ($cliente as $row) {
                $uuid = $row->uuid_cliente;
            }
            $uuid_clientes = collect($uuid);
            $uuid_clientes->transform(function ($item) {
                return hex2bin($item);
            });
            //dd($id_grup);
            $uuuid = $uuid_clientes->toArray();
            $idClienteG = Grupo_cliente_agrupador_orm::getIdsClientesRelations($uuuid);
            // dd($idClienteG->toArray());
            $json = json_encode($idClienteG);
            echo $json;
            die;
        }
    }

}

?>

<?php

/**
 * Clientes
 *
 * Modulo para administrar la creacion, edicion de cliente naturales
 * o juridicos.
 *
 * @package    PensaApp
 * @subpackage Controller
 * @category   Controllers
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  08/10/2015
 */
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\FormularioDocumentos AS FormularioDocumentos;
use League\Csv\Writer as Writer;
use Carbon\Carbon;
use Flexio\Modulo\Cliente\Repository\ClienteRepository;
use Flexio\Modulo\Cliente\HttpRequest\ClienteRequest;
use Flexio\Modulo\CentroFacturable\Repository\CentroFacturableRepository;
use Flexio\Modulo\Cliente\Models\Telefonos;
use Flexio\Modulo\Cliente\Models\Correos;
use Flexio\Modulo\ConfiguracionVentas\Models\TipoClientes as TipoClientes;
use Flexio\Modulo\ConfiguracionVentas\Models\CategoriaClientes as CategoriaClientes;
use Flexio\Modulo\ConfiguracionVentas\Repository\CategoriaClienteRepository as CategoriaClienteRepository;
use Flexio\Modulo\ConfiguracionVentas\Repository\TipoClienteRepository as TipoClienteRepository;
use Flexio\Modulo\ClientesPotenciales\Repository\ClientesPotencialesRepository;
class Clientes extends CRM_Controller {

    private $id_empresa;
    private $id_usuario;
    private $empresaObj;
    protected $clienteRepo;
    protected $centro_facturacion_repository;
    protected $DocumentosRepository;
    protected $upload_folder = './public/uploads/';
    protected $listarCategorias;
    protected $listarTipos;
    protected $categoriaClienteRepository;
    protected  $tipoClienteRepository;
    protected $ClientesPotencialesRepository;

    function __construct() {
        parent::__construct();
        //$this->load->model("usuarios/Empresa_orm");
        //$this->load->model("facturas/Factura_orm");
        $this->load->model('usuarios/Usuario_orm');
        $this->load->model("cobros/Cobro_orm");
        $this->load->model('Cliente_orm');
        $this->load->model('clientes_abonos/Clientes_abonos_orm');
        $this->load->model('Catalogo_orm');
        $this->load->model('Catalogo_toma_contacto_orm');
        $this->load->model('clientes_potenciales/Clientes_potenciales_orm');
        $this->load->model('clientes_potenciales/Catalogo_toma_contacto_orm');
        $this->load->model('grupo_clientes/Grupo_cliente_orm');

        $this->load->model('grupo_clientes/Grupo_cliente_agrupador_orm');
        $this->ClientesPotencialesRepository    = new ClientesPotencialesRepository();

        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();
        //HMVC Load Modules
        $this->load->module(array('documentos'));
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        //$this->empresaObj  = Empresa_orm::findByUuid($uuid_empresa);
        $empresaObj = new Buscar(new Empresa_orm, 'uuid_empresa');
        $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
        $this->id_usuario = $this->session->userdata("id_usuario");
        $this->id_empresa = $this->empresaObj->id;
        $this->clienteRepo = new ClienteRepository;
        $this->centro_facturacion_repository = new CentroFacturableRepository;
        $this->listarTipos = new TipoClientes();
        $this->listarCategorias = new CategoriaClientes();
        $this->categoriaClienteRepository = new CategoriaClienteRepository();
        $this->tipoClienteRepository = new TipoClienteRepository();
    }

    public function listar() {

        // Verificar si tiene permiso para crear cliente Natural
        if (!$this->auth->has_permission('acceso')) {
            // No, tiene permiso, redireccionarlo.
            redirect('/');
        }

        $data = array();
        $camposGrid = array();

        $this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
            'public/assets/css/plugins/jquery/fileinput/fileinput.css',
            'public/assets/css/modules/stylesheets/clientes.css',
            'public/assets/css/plugins/jquery/jquery.fileupload.css',

        ));
        $this->assets->agregar_js(array(
            /*'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',*/
            'public/assets/js/moment-with-locales-290.js',
            /*'public/assets/js/plugins/jquery/jqgrid/plugins/jQuery.jqGrid.columnToggle.js',
            'public/assets/js/plugins/jquery/switchery.min.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',*/
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            //'public/assets/js/plugins/bootstrap/bootstrap-tabdrop.js',
            //'public/assets/js/default/jqgrid-toggle-resize.js',
            'public/assets/js/default/formulario.js',
            'public/assets/js/plugins/jquery/fileupload/jquery.fileupload.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            //'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            //'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
        ));

        $data['info']['categoria'] = $this->categoriaClienteRepository->getCatalogoCategoria($this->id_empresa);
        $data['info']['tipo'] = $this->tipoClienteRepository->getCatalogoTipo($this->id_empresa);
        $data['info']['estado'] = Catalogo_orm::where('tipo', '=', 'estado')->get(array('valor', 'etiqueta'));
        $data['info']['identificacion'] = Catalogo_orm::where('tipo', '=', 'identificacion')->get(array('valor', 'etiqueta'));
       // dd($data);
        $breadcrumb = array("titulo" => '<i class="fa fa-line-chart"></i> Clientes',
            "ruta" => array(
                0 => array(
                    "nombre" => "Ventas",
                    "activo" => false
                ),
                1 => array(
                    "nombre" => '<b>Clientes</b>',
                    "activo" => true
                )
            ),
            "menu" => array(
                "nombre" => "Crear",
                "url" => "clientes/crear",
                "opciones" => array()
            )
        );

        $menuOpciones["#agrupadorClientesBtn"] = "Agrupar";
        $menuOpciones["#exportarClienteBtn"] = "Exportar";
        $breadcrumb["menu"]["opciones"] = $menuOpciones;

        $this->template->agregar_titulo_header('Listado de Clientes');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }

    public function ajax_get_montos() {

        $cliente_id = $this->input->post("cliente_id");
        $cliente = $this->clienteRepo->find($cliente_id);
        $registro = array();

        if (count($cliente)) {
            $centro_facturable = $cliente->centro_facturable;
            $registro['saldo'] = $cliente->saldo_pendiente;
            $registro['credito_favor'] = $cliente->credito_favor;
            $registro['centros_facturacion'] = $centro_facturable;
            $registro['centro_facturacion_id'] = count($centro_facturable) == 1 ? $centro_facturable->first()->id : '';
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($registro))->_display();

        exit;
    }

    public function ajax_listar() {
        if (!$this->input->is_ajax_request()) {
            return false;
        }
        /*
          paramentos de busqueda aqui
         */
        $nombre              = $this->input->post('nombre');
        $telefono            = $this->input->post('telefono');
        $correo              = $this->input->post('correo');
        $tipo                = $this->input->post('tipo');
        $categoria           = $this->input->post('categoria');
        $estado              = $this->input->post('estado');
        $tipo_identificacion = $this->input->post('identificacion');


        $clause = array('empresa_id' => $this->empresaObj->id);
        $var = '';

        if (!empty($nombre))
            $clause['nombre'] = $nombre;
        if (!empty($telefono)){
            $var = Telefonos::where('telefono','=', $telefono)->get()->toArray();
            $clause['id'] = $var[0]["cliente_id"];
        //dd($var[0]["cliente_id"]);
        }
        if (!empty($correo)){
            $var = Correos::where('correo','=', $correo)->get()->toArray();
            $clause['id'] = $var[0]["cliente_id"];
        }
        if (!empty($tipo)) $clause['tipo'] = $tipo;
        if (!empty($categoria)) $clause['categoria'] = $categoria;
        if (!empty($estado)) $clause['estado'] = $estado;
        if (!empty($tipo_identificacion)) $clause['tipo_identificacion'] = $tipo_identificacion;
        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = Cliente_orm::lista_totales($clause);
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $clientes = Cliente_orm::listar($clause, $sidx, $sord, $limit, $start);

        // dd($agrupador);
        //Constructing a JSON
        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;


        //dd($clientes);
        if (!empty($clientes->toArray())) {
            $i = 0;
            foreach ($clientes as $row) {
                //Se agrega primer telefono y correo guardado en la base de datos de cada uno respectivamente.
                //Se agrego asi por solicitud de diseño hasta previo cambio.
                $telefono ="";
                if (count($row->telefonos_asignados) > 0){ $telefono =$row->telefonos_asignados[0]->telefono;}
                $correo ="";
                if(count($row->correos_asignados) > 0){ $correo = $row->correos_asignados[0]->correo;}

                $hidden_options = "";
                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row->uuid_cliente . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $hidden_options = '<a href="' . base_url('clientes/ver/' . $row->uuid_cliente) . '" data-id="' . $row->uuid_cliente . '" class="btn btn-block btn-outline btn-success">Ver Cliente</a>';
                $hidden_options .= '<a href="' . base_url('clientes/ver/' . $row->uuid_cliente . "#contacto") . '" data-id="' . $row->uuid_cliente . '" class="exportarTablaCliente btn btn-block btn-outline btn-success">Agregar Contacto</a>';
                $hidden_options .= '<a href="' . base_url('cotizaciones/crear/cliente' . $row->id) .'" data-id="' . $row->uuid_cliente . '" class="btn btn-block btn-outline btn-success">Nueva Cotizaci&oacute;n</a>';
               // $hidden_options .= '<a href="javascript:" data-id="' . $row->uuid_cliente . '" class="exportarTablaCliente btn btn-block btn-outline btn-success">Registrar Actividad</a>'; //COMENTADO TEMPORALMENTE HASTA NUEVO AVISO
                $hidden_options .= '<a href="javascript:" data-id="' . $row->uuid_cliente . '" class="exportarTablaCliente btn btn-block btn-outline btn-success subirArchivoBtn">Subir Documento</a>';
                //$hidden_options .= '<a href="javascript:" data-id="' . $row->uuid_cliente . '" class="exportarTablaCliente btn btn-block btn-outline btn-success">Agregar Caso</a>'; //COMENTADO TEMPORALMENTE HASTA NUEVO AVISO
                if($row->estado == 'activo'){
                $hidden_options .= '<a href="' . base_url('anticipos/crear/?cliente=' . $row->uuid_cliente) .'" class="btn btn-block btn-outline btn-success">Crear anticipo</a>';
                }
                $saldo = empty($row->saldo) ? "0.00" : $row->saldo;
                $response->rows[$i]["id"] = $row->uuid_cliente;
                $response->rows[$i]["cell"] = array(
                    $row->uuid_cliente,
                    $row->codigo,
                    '<a class="link" href="' . base_url('clientes/ver/' . $row->uuid_cliente) . '" class="link">' . $row->nombre . '</a>',
                    $telefono,
                    $correo,
                    '<label class="totales-success">' . number_format($row->credito_favor, 2, '.', ',') . '</label>',
                    '<label class="totales-danger">' . number_format($row->total_saldo_pendiente(), 2, '.', ',') . '</label>',
                    $row->present()->estado_label,
                    $link_option,
                    $hidden_options
                );
                $i++;
            }
        }
        echo json_encode($response);
        exit;
    }


    public function ocultotabla() {
        //If ajax request
        $this->assets->agregar_js(array(
            'public/assets/js/modules/clientes/tabla.js'
        ));

        $this->load->view('tabla');
    }

    /**
     * Cargar Vista Parcial de Tabla
     * Para Filtrar Clientes desde
     * el Modulo de Contactos.
     *
     * @return void
     */
    public function filtarclientes() {
        //If ajax request
        $this->assets->agregar_js(array(
            'public/assets/js/modules/clientes/filtarclientes.js'
        ));

        $this->load->view('tabla');
    }

    public function crear($uuid_cliente_potencial = NULL) {
        $this->assets->agregar_css(array(
            'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
            'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
            'public/assets/css/modules/stylesheets/clientes.css'
        ));
        $this->_js();
        $this->assets->agregar_js(array(
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
            //'public/assets/js/plugins/jquery/combodate/combodate.js',
            'public/assets/js/plugins/jquery/combodate/momentjs.js',
            //'public/assets/js/default/lodash.min.js',
            //'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            //'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            'public/assets/js/default/formulario.js',
            'public/assets/js/default/jquery.inputmask.bundle.min.js',
            'public/assets/js/default/formatos.js'
          //  'public/assets/js/modules/clientes/vue.telefono-clientes.js',
           // 'public/assets/js/modules/clientes/vue.correo-clientes.js'
        ));

        $data = $cliente_potencial = array();

        $breadcrumb = array(
            "titulo" => '<i class="fa fa-line-chart"></i> Crear Clientes',
            "ruta" => array(
                0 => array(
                    "nombre" => "Ventas",
                    "activo" => false
                ),
                1 => array(
                  "nombre" => "Clientes",
                  "activo" => false,
                  "url" => 'clientes/listar'
                ),
                2=> array(
                    "nombre" => '<b>Crear</b>',
                    "activo" => true
                )
            ),
            "filtro" => false,
            "menu" => array()
        );

        $total = Cliente_orm::where('empresa_id', '=', $this->id_empresa)->count();
        $identificacion = Cliente_orm::where('empresa_id', '=', $this->id_empresa)->get(array('identificacion'));
       // dd($identificacion->toArray());

        $data['info']['codigo'] = Util::generar_codigo('CUS', $total + 1);
        $data['info']['provincias'] = Catalogo_orm::where('tipo', '=', 'provincias')->get(array('id', 'valor'));
        $data['info']['letras'] = Catalogo_orm::where('tipo', '=', 'letras')->get(array('key', 'valor'));
        //$data['info']['toma_contacto'] = Catalogo_toma_contacto_orm::all();
        $data['info']['toma_contacto'] = $this->clienteRepo->getTomaContacto();

        $data['info']['asignados'] = Usuario_orm::where('estado', '=', 'Activo')
        ->leftJoin('usuarios_has_empresas', 'usuarios_has_empresas.id', '=', 'usuarios.id')
        ->where('usuarios_has_empresas.empresa_id', '=', 1)->get(array('usuarios.id', 'usuarios.nombre', 'usuarios.apellido'));

        $data['info']['categoria'] = $this->categoriaClienteRepository->getCatalogoCategoria($this->id_empresa);
        $data['info']['tipo'] = $this->tipoClienteRepository->getCatalogoTipo($this->id_empresa);
        $data['info']['estado'] = Catalogo_orm::where('tipo', '=', 'estado')->get(array('valor', 'etiqueta'));
        $vista = "crear";
        $cliente_potencial_id = 0;
        if($uuid_cliente_potencial!= NULL){ //Viene del cliente potencial
          //$cliente_id = $this->input->post('id', true);
          $client = array('empresa_id'=>$this->id_empresa,'uuid_cliente_potencial' => $uuid_cliente_potencial);
          $cliente_potencial = $this->ClientesPotencialesRepository->findBy($client);
          $cliente_potencial->load("telefonos_asignados","correos_asignados");

          $cliente_potencial_id = $cliente_potencial->id_cliente_potencial;
          $vista = "creando_desde_potencial";

          //$clientePot = Clientes_potenciales_orm::select_cliente_potencial($client);


        }
        $this->assets->agregar_var_js(array(
            'tipo_id' => 'null',
            'balance' => 0,
            "vista"=>$vista,
            "lista_telefonos"=>!empty($cliente_potencial)?$cliente_potencial->telefonos_asignados:'',
            "lista_correo"=>!empty($cliente_potencial)?$cliente_potencial->correos_asignados:'',
            "cliente_potencial_id" => $cliente_potencial_id
        ));
        $this->template->agregar_titulo_header('Crear Cliente');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    public function ocultoformulario($data = NULL) {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/clientes/provider.js',
            'public/assets/js/modules/clientes/crear.js',

        ));
        $this->load->view('formulario', $data);
    }



    public function ocultoformularioagrupador() {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/clientes/agrupador.js'
        ));

        $clause = array('empresa_id' => $this->empresaObj->id);
        $data['agrupador'] = Grupo_cliente_orm::listaNombreAgrupadores($clause)->toArray();

        $this->load->view('agrupador', $data);
    }

    function guardar() {

        if ($_POST){

            //$clause = array('empresa_id' => $this->empresaObj->id);
            $cliente = '';
            $cp_id = $this->input->post('id_cp', true);
            if ($cp_id != NULL) {
                Clientes_potenciales_orm::upDateClientePotencial($cp_id);
            }
                try {
                    $total = Cliente_orm::where('empresa_id', '=', $this->id_empresa)->count();
                    $cliente_request = new ClienteRequest;
                    $codigo = $total + 1;
                    $cliente = $cliente_request->guardar($this->id_empresa, $codigo);
                } catch (\Exception $e) {
                    log_message('error', $e);
                }

                if (!is_null($cliente)) {
                    if ($cliente == 'con_identificacion'){
                        $mensaje = array('clase' => 'alert-warning', 'contenido' => '<strong>¡Error!</strong> Su solicitud no fue procesada. El n&uacute;mero de identificaci&oacute;n ya existe.');
                    }else{
                        $mensaje = array('clase' => 'alert-success', 'contenido' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente');
                    }

                }else{
                    $mensaje = array('clase' => 'alert-danger', 'contenido' => '<strong>¡Error!</strong> Su solicitud no fue procesada');
                }

        }else {
            $mensaje = array('clase' => 'alert-warning', 'contenido' => '<strong>¡Error!</strong> Su solicitud no fue procesada');
        }

        $this->session->set_flashdata('mensaje', $mensaje);
        redirect(base_url('clientes/listar'));
    }

    function ver($uuid = NULL) {
        $data = array();
        $this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/modules/stylesheets/clientes.css'
        ));
        $this->_js();
        $this->assets->agregar_js(array(
        		//'public/assets/js/default/vue.js',
        		'public/assets/js/default/vue-validator.min.js',
        		//'public/assets/js/default/vue-resource.min.js',
            //'public/assets/js/default/jquery-ui.min.js',
            //'public/assets/js/plugins/jquery/jquery.sticky.js',
            //'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            //'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            //'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            //'public/assets/js/plugins/jquery/jqgrid/plugins/jQuery.jqGrid.columnToggle.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
            //'public/assets/js/plugins/jquery/combodate/combodate.js',
            'public/assets/js/plugins/jquery/combodate/momentjs.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            //'public/assets/js/default/lodash.min.js',
            'public/assets/js/default/jquery.inputmask.bundle.min.js',
            'public/assets/js/moment-with-locales-290.js',
            //'public/assets/js/plugins/bootstrap/daterangepicker.js',
            'public/assets/js/default/formulario.js',
            //'public/assets/js/default/jqgrid-toggle-resize.js',
            'public/assets/js/modules/clientes/acciones_ver.js',
            'public/assets/js/modules/contactos/routes.js',
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/resources/compile/modulos/clientes/formulario.js',
            'public/assets/js/default/formatos.js'
        ));
        if (is_null($uuid)) {
            $mensaje = array('clase' => 'alert-warning', 'contenido' => '<strong>¡Error!</strong> Su solicitud no fue procesada');
            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('clientes/listar'));
        } else {

            $cliente = $this->clienteRepo->findByUuid($uuid);
            //dd($cliente);
            $cliente->load('comentario_timeline','clientes_asignados','telefonos_asignados','correos_asignados');
             if (is_null($cliente)) {
                $mensaje = array('clase' => 'alert-warning', 'contenido' => '<strong>¡Error!</strong> Su solicitud no fue procesada');
                $this->session->set_flashdata('mensaje', $mensaje);
                redirect(base_url('clientes/listar'));
            } else {

                $this->assets->agregar_var_js(array(
                    'tipo_id' => (!empty($cliente['tipo_identificacion']))?$cliente['tipo_identificacion']:'',
                    'letra' => isset($cliente['letra']) ? $cliente['letra'] : 'null',
                    'balance' => 1,
                    "vista" => 'ver',
                    'lista_facturacion'=>$cliente->centro_facturable,
                    'lista_asignados'=> isset($cliente->clientes_asignados) ? $cliente->clientes_asignados : '',
                    'lista_telefonos'=> isset($cliente->telefonos_asignados) ? $cliente->telefonos_asignados : '',
                    'lista_correo'=> isset($cliente->correos_asignados) ? $cliente->correos_asignados : '',
                    'cliente'=>$cliente,
                    'clientes_id' => !empty($cliente['id']) ? $cliente['id'] : ''
                ));
                // dd($cliente->toArray());
                 $estado = Catalogo_orm::where('tipo', '=', 'estado')->get(array('valor', 'etiqueta'));
                 $credito = number_format($cliente->credito_favor, 2, '.', ',');
                 $saldo = number_format($cliente->total_saldo_pendiente(), 2, '.', ',');

                 if($cliente['estado'] != 'por_aprobar'){
                     $estado->splice(2, 1);
                 }
                 if($cliente['estado'] != 'inactivo'){
                    // dd($credito, $saldo, count($cliente->estadoFacturaValidate));
                     if(count($cliente->estadoFacturaValidate) > 0 || $credito != '0.00' || $saldo != '0.00' ){
                         $estado->splice(1, 1);

                     }
                 }

                $data['info']['categoria'] = $this->categoriaClienteRepository->getCatalogoCategoria($this->id_empresa);
                $data['info']['tipo'] = $this->tipoClienteRepository->getCatalogoTipo($this->id_empresa);
                $data['info']['estado'] = $estado->all();
                $data['info']['cliente'] = $cliente->toArray();
                //dd($data);
                if ($cliente['tipo_identificacion'] == 'natural') {
                    $identificacion = $cliente['identificacion'];
                    if ($cliente['letra'] == '0') {
                        list($provincia, $tomo, $asiento) = explode("-", $identificacion);
                        $data['info']['cliente']['provincia'] = $provincia;
                        $data['info']['cliente']['tomo'] = $tomo;
                        $data['info']['cliente']['asiento'] = $asiento;
                    } elseif ($cliente['letra'] == 'N' || $cliente['letra'] == 'PE' || $cliente['letra'] == 'E') {
                        list($letra, $tomo, $asiento) = explode("-", $identificacion);
                        $data['info']['cliente']['tomo'] = $tomo;
                        $data['info']['cliente']['asiento'] = $asiento;
                    } elseif ($cliente['letra'] == 'PI') {
                        list($provincia, $tomo, $asiento) = explode("-", $identificacion);
                        $data['info']['cliente']['tomo'] = $tomo;
                        $data['info']['cliente']['asiento'] = $asiento;
                        $provincia = str_replace("PI", "", $provincia);
                        $data['info']['cliente']['provincia'] = $provincia;
                    } elseif ($cliente['letra'] == 'PAS') {
                        $data['info']['cliente']['pasaporte'] = $identificacion;
                    }
                } elseif ($cliente['tipo_identificacion'] == 'juridico') {
                    $identificacion = $cliente['identificacion'];
                    list($tomo, $folio, $asiento, $verificador) = explode("-", $identificacion);
                    $data['info']['cliente']['tomo'] = $tomo;
                    $data['info']['cliente']['folio'] = $folio;
                    $data['info']['cliente']['asiento'] = $asiento;
                    $data['info']['cliente']['verificador'] = $verificador;
                }
            }
        }

        $breadcrumb = array(
            "titulo" => '<i class="fa fa-line-chart"></i> Editar Clientes',
             "ruta" => array(
                0 => array(
                    "nombre" => "Ventas",
                    "activo" => false
                ),
                1 => array(
                  "nombre" => "Clientes",
                  "activo" => false,
                  "url" => 'clientes/listar'
                ),
                2=> array(
                    "nombre" => '<b>Detalle</b>',
                    "activo" => true
                )
            ),
            "filtro" => false,
            "menu" => array(
                'url' => 'javascipt:',
                'nombre' => "Acción",
                "opciones" => array(
                    "#agregarContactoBtn" => "Agregar Contacto",
                  //  "#crearCotizacion" => "Crear Cotización",
                  //  "javascript2:" => "Crear Órden de Venta",
                  //  "javascript3:" => "Crear Factura",
                   // "javascript4:" => "Registrar Cobro",
                  //  "javascript5:" => "Nueva Actividad",
                  // "javascript6:" => "Subir Documentos",
                )
            ),
        );


        $data['uuid_cliente'] = $cliente['uuid_cliente'];
        $data['info']['cliente']['saldo'] = "0.00";
        //credito  a favor
        $data['info']['codigo'] = $cliente['codigo'];
        $data['info']['provincias'] = Catalogo_orm::where('tipo', '=', 'provincias')->get(array('id', 'valor'));
        $data['info']['letras'] = Catalogo_orm::where('tipo', '=', 'letras')->get(array('key', 'valor'));
        $data['info']['toma_contacto'] = Catalogo_toma_contacto_orm::all();
        $data['info']['asignados'] = Usuario_orm::where('estado', '=', 'Activo')
        ->leftJoin('usuarios_has_empresas', 'usuarios_has_empresas.id', '=', 'usuarios.id')
        ->where('usuarios_has_empresas.empresa_id', '=', $this->id_empresa)->get(array('usuarios.id', 'usuarios.nombre', 'usuarios.apellido'));

        $this->template->agregar_titulo_header('Editar Cliente');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    public function ajax_cliente_potencial() {
        //ID cliente potencial.

        $cliente_id = $this->input->post('id', true);
        $clientePot = null;
        $client = array('empresa_id'=>$this->id_empresa,'id_cliente_potencial' => $cliente_id);
        //$clientePot = Clientes_potenciales_orm::select_cliente_potencial($client);
        if ($client['id_cliente_potencial']<>0) {
        $clientePot = $this->ClientesPotencialesRepository->findBy($client);
        //$clientePot->load("telefonos_asignados","correos_asignados");
      //  dd($clientePot);
        }

        if ($clientePot != null) {
            $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode($clientePot->toarray()))->_display();
        }
        exit;
    }

    /**
     * Funcion para exportar los clientes potenciales
     * seleccionados a formato CSV.
     *
     * return void
     */
    public function exportar() {
        if (empty($_POST)) {
            exit();
        }

        $ids = $this->input->post('ids', true);
        //dd($ids);
        $id_clientes = explode(",", $ids);
        //dd($id_clientes);
        if (empty($id_clientes)) {
            return false;
        }

        $uuid_clientes = collect($id_clientes);
        $uuid_clientes->transform(function ($item) {
             return hex2bin($item);
        });

      $uuuid = $uuid_clientes->toArray();
        $clause = array(
            "id_cliente" => $uuid_clientes->toArray()
        );
        // dd($clientesIds);
        $clientes = Cliente_orm::selectExportClient($uuuid);
        //dd($clientes);
        if (empty($clientes)) {
            return false;
        }

        $i = 0;
        foreach ($clientes as $row) {

            $telefono ="";
            if (count($row->telefonos_asignados) > 0){ $telefono =$row->telefonos_asignados[0]->telefono;}
            $correo ="";
            if(count($row->correos_asignados) > 0){ $correo = $row->correos_asignados[0]->correo;}
            $datos[$i]['codigo'] = Util::verificar_valor($row['codigo']);
            $datos[$i]['nombre'] = utf8_decode(Util::verificar_valor($row['nombre']));
            $datos[$i]['telefono'] = Util::verificar_valor($telefono);
            $datos[$i]['correo'] = Util::verificar_valor($correo);
            $datos[$i]['tipo'] = utf8_decode(Util::verificar_valor($row->tipo_cliente[0]->nombre));
            $datos[$i]['categoria'] = utf8_decode(Util::verificar_valor($row->categoria_cliente[0]->nombre));
            $datos[$i]['credito'] = Util::verificar_valor($row['credito_favor']);
            $datos[$i]['saldo'] = Util::verificar_valor($row->total_saldo_pendiente());
            $i++;
        }
        $csv = Writer::createFromFileObject(new SplTempFileObject());
        $csv->insertOne([
            'No. Cliente',
            'Nombre',
            utf8_decode('Teléfono'),
            'E-mail',
            utf8_decode('Tipo de cliente'),
            utf8_decode('Categoría de cliente'),
            utf8_decode('Crédito a favor'),
            'Saldo acumulado'
        ]);
        $csv->insertAll($datos);
        $csv->output("Clientes-" . date('ymd') . ".csv");
        exit();
    }

    public function guardar_agrupador() {
        $id_client = $this->input->post('id_clientes', true);
        $id_grupo = $this->input->post('id_grupo', true);
        // dd($id_grupo);
        //$id_clientes = explode(",", $id_client);
        //$id_clientes = $id_client->toArray();
       // dd($id_clientes);
        if (empty($id_client)) {
            return false;
        }

        $uuid_clientes = collect($id_client[0]);
        // dd($uuid_clientes);
        $uuid_clientes->transform(function ($item) {
             return hex2bin($item);
        });
       $id_grup = (int)$id_grupo[0];
       //dd($id_grup);
      $uuuid = $uuid_clientes->toArray();
      foreach ($uuuid AS $uuid){
           $response = Grupo_cliente_agrupador_orm::guardar($uuid, $id_grup);
      }
     // dd($uuuid);

       // dd($id_grupo);
       $mensaje = array(
            "respuesta" => true,
            "mensaje" => "Se han agrupado los clientes seleccionados satisfactoriamente."
        );
     // $mensaje = array('clase' => 'alert-success', 'contenido' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente ');
       echo json_encode($mensaje);
       exit;
    }

    function ajax_centro_facturable() {
        if (!$this->input->is_ajax_request()) {
            return false;
        }
        $id = $this->input->post('centro_facturacion_id');
        $centro_facturacion = $this->centro_facturacion_repository->find($id);

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($centro_facturacion->tiene_relaciones()))->_display();
        exit;
    }

    private function _js() {
        $this->assets->agregar_js(array(
            'public/assets/js/default/vue.js',
            'public/assets/js/default/vue-resource.min.js',
            'public/assets/js/modules/clientes/vue.centros-facturables.js',
            'public/assets/js/modules/clientes/vue.asignados-clientes.js',
            'public/assets/js/modules/clientes/vue.telefono-cliente.js',
            'public/assets/js/modules/clientes/vue.correo-cliente.js'
        ));
    }

    function ajax_guardar_comentario() {

    	if(!$this->input->is_ajax_request()){
    		return false;
    	}
      	$model_id   = $this->input->post('modelId');
    	$comentario = $this->input->post('comentario');
     	$comentario = ['comentario'=>$comentario,'usuario_id'=>$this->id_usuario];
     	$cliente = $this->clienteRepo->agregarComentario($model_id, $comentario);
     	$cliente->load('comentario_timeline');

     	$this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
    	->set_output(json_encode($cliente->comentario_timeline->toArray()))->_display();
    	exit;
    }

    function ocultoformulariocomentarios() {

    	$data = array();

    	$this->assets->agregar_js(array(
    			'public/assets/js/plugins/ckeditor/ckeditor.js',
    			'public/assets/js/plugins/ckeditor/adapters/jquery.js',
    			'public/assets/js/modules/clientes/vue.comentario.js',
    			'public/assets/js/modules/clientes/formulario_comentario.js'
    	));

    	$this->load->view('formulario_comentarios');
    	$this->load->view('comentarios');
    }

    function documentos_campos() {

    	return array(
    	array(
    		"type"		=> "hidden",
    		"name" 		=> "cliente_id",
    		"id" 		=> "cliente_id",
    		"class"		=> "form-control",
    		"readonly"	=> "readonly",
    	));
    }

    function ajax_guardar_documentos() {
    	if(empty($_POST)){
    		return false;
    	}

    	$clientes_id = $this->input->post('cliente_id', true);
        $modeloInstancia = $this->clienteRepo->findByUuid($clientes_id);
    	$this->documentos->subir($modeloInstancia);
    }

    /*function ajax_verificar_identificacion(){

        $response = [];
        $clause = array('empresa_id' => $this->empresaObj->id);
        $cliente_request = new ClienteRequest;
        $verificar = $cliente_request->getIdentificacionClientes($clause)->count();
        if ($verificar > 0){
            $response["tipo"] == "success";
        }else{
            $response["tipo"] == "danger";
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response))->_display();

              exit;
    }*/

}

?>

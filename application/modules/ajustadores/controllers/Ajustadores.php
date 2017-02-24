<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Colaboradores
 * 
 * Modulo para administrar la creacion, edicion de solicitudes.
 *
 * @package    PensaApp
 * @subpackage Controller
 * @category   Controllers
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  05/22/2015
 */
use Illuminate\Database\Capsule\Manager as Capsule;
use League\Csv\Writer as Writer;
use Dompdf\Dompdf;
use Carbon\Carbon;
use Flexio\Modulo\Ajustadores\Repository\AjustadoresRepository as AjustadoresRepository;
use Flexio\Modulo\Ajustadores\Models\Ajustadores as AjustadoresModel;
use Flexio\Modulo\SegCatalogo\Repository\SegCatalogoRepository as SegCatalogoRepository;
use Flexio\Modulo\SegAjustadoresContacto\Models\SegAjustadoresContacto as SegAjustadoresContactoModel;
use Flexio\Modulo\SegAjustadoresContacto\Repository\SegAjustadoresContactoRepository as SegAjustadoresContactoRepository;
use Flexio\Modulo\Politicas\Repository\PoliticasRepository as PoliticasRepository;

class Ajustadores extends CRM_Controller {

    /**
     * @var int
     */
    protected $usuario_id;

    /**
     * @var int
     */
    protected $empresa_id;

    /**
     * @var int
     */
    protected $modulo_id;

    /**
     * @var string
     */
    protected $nombre_modulo;
    protected $AjustadoresRepository;
    protected $AjustadoresModel;
    protected $SegCatalogoRepository;
    protected $SegAjustadoresContactoRepository;
    protected $SegAjustadoresContactoModel;
    protected $politicas;
    protected $roles;

    /**
     * @var string
     */
    protected $upload_folder = './public/uploads/';

    function __construct() {
        parent::__construct();

        //$this->load->model('Planes_orm');
        //$this->load->model('Coberturas_orm');
        $this->load->model('usuarios/usuario_orm');

        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();

        //Obtener el id de usuario de session
        $uuid_usuario = $this->session->userdata('huuid_usuario');
        $usuario = Usuario_orm::findByUuid($uuid_usuario);

        $this->usuario_id = $usuario->id;

        //Obtener el id_empresa de session
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $this->empresa_id = $empresa->id;

        $this->AjustadoresRepository = new AjustadoresRepository();
        $this->AjustadoresModel = new AjustadoresModel();
        $this->SegCatalogoRepository = new SegCatalogoRepository();
        $this->SegAjustadoresContactoModel = new SegAjustadoresContactoModel();
        $this->SegAjustadoresContactoRepository = new SegAjustadoresContactoRepository();
        $this->PoliticasRepository = new PoliticasRepository();


        $this->roles = $this->session->userdata("roles");
        //$roles=implode(",", $this->roles);

        $clause['empresa_id'] = $this->empresa_id;
        $clause['modulo'] = 'ajustadores';
        $clause['usuario_id'] = $this->usuario_id;
        $clause['role_id'] = $this->roles;

        $politicas_transaccion = $this->PoliticasRepository->getAllPoliticasRoles($clause);

        $politicas_transaccion_general = count($this->PoliticasRepository->getAllPoliticasRolesModulo($clause));
        $this->politicas_general = $politicas_transaccion_general;
        $politicas_transaccion_general2 = $this->PoliticasRepository->getAllPoliticasRolesModulo($clause);

        $estados_politicas = array();
        foreach ($politicas_transaccion as $politica_estado) {
            $estados_politicas[] = $politica_estado->politica_estado;
        }
        $estados_politicasgenerales = array();
        foreach ($politicas_transaccion_general2 as $politica_estado_generales) {
            $estados_politicasgenerales[] = $politica_estado_generales->politica_estado;
        }

        $this->politicas = $estados_politicas;
        $this->politicas_generales = $estados_politicasgenerales;
    }

    public function listar() {
        //Definir mensaje
        if (!is_null($this->session->flashdata('mensaje'))) {
            $mensaje = $this->session->flashdata('mensaje');
        } else {
            $mensaje = [];
        }
        //$mensaje = array('tipo' => "error", 'mensaje' => '<b>¡Error!</b> No tiene permisos para ingresar al módulo', 'titulo' => 'Ajustadores ');

        $this->assets->agregar_var_js(array(
            "flexio_mensaje" => collect($mensaje)
        ));

        if (!$this->auth->has_permission('acceso', 'ajustadores/listar')) {
            // No, tiene permiso, redireccionarlo.
            $acceso = 0;
            $mensaje = array('tipo' => "error", 'mensaje' => '<b>¡Error!</b> No tiene permisos para ingresar al módulo', 'titulo' => 'Ajustadores ');

            redirect(base_url(''));
        }

        $data = array();

        $this->_Css();
        $this->_js();

        $this->assets->agregar_js(array(
            'public/assets/js/plugins/jquery/context-menu/jquery.contextMenu.min.js',
            'public/assets/js/modules/ajustadores/listar.js',
            'public/assets/js/modules/ajustadores/routes.js',
        ));




        //Verificar permisos para crear
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-archive"></i> Ajustadores',
            "ruta" => array(
                0 => array("nombre" => "Seguros", "url" => "#", "activo" => false),
                1 => array("nombre" => '<b>Ajustadores</b>', "activo" => true)
            ),
            "filtro" => false,
            "menu" => array()
        );

        $breadcrumb["menu"] = array(
            "url" => 'ajustadores/crear',
            "nombre" => "Crear"
        );
        $menuOpciones["#cambiarEstadoLnk"] = "Cambiar Estado";
        $menuOpciones["#exportarAjustadoresLnk"] = "Exportar";
        $breadcrumb["menu"]["opciones"] = $menuOpciones;

        //Menu para crear
        $clause = array('empresa_id' => $this->empresa_id);

        $this->template->agregar_titulo_header('Listado de Ajustadores');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }

    function ajax_cambiar_estados() {
        /* if (!$this->input->is_ajax_request()) {
          return false;
          } */
        $mensaje = [];
        $FormRequest = new Flexio\Modulo\Ajustadores\FormRequest\GuardarAjustadoresEstados;
        try {
            $ajustadores = $FormRequest->guardar();
            //formatear el response
            $res = $ajustadores->map(function($ant) {
                return[
                    'id' => $ant->id, 'estado' => $ant->present()->estado_label
                ];
            });
        } catch (\Exception $e) {
            log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output($res)->_display();
        exit;
    }

    public function crear() {
        //Definir mensaje
        $acceso = 1;
        $mensaje = array();
        if (!is_null($this->session->flashdata('mensaje'))) {
            $mensaje = $this->session->flashdata('mensaje');
        } else {
            $mensaje = [];
        }

        $this->assets->agregar_var_js(array(
            "flexio_mensaje" => collect($mensaje)
        ));

        if (!$this->auth->has_permission('acceso', 'ajustadores/crear')) {
            // No, tiene permiso, redireccionarlo.
            $acceso = 0;

            $mensaje = array('tipo' => "error", 'mensaje' => '<b>¡Error!</b> No tiene permisos para crear ajustadores', 'titulo' => 'Ajustadores ');
            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('ajustadores/listar'));
        }


        $this->_Css();
        $this->_js();
        $this->assets->agregar_js(array(
            'public/assets/js/modules/ajustadores/formulario.js',
            'public/assets/js/modules/ajustadores/crear.js',
            'public/assets/js/default/vue-validator.min.js',
        ));

        $data = array();
        $this->assets->agregar_var_js(array(
            "vista" => 'crear',
            "acceso" => $acceso,
        ));

        $data["campos"] = array(
            "campos" => array(
                "created_at" => "",
                "uuid_ajustadores" => "",
                "nombre" => "",
                "identificacion" => "",
                "tomo_j" => "",
                "folio" => "",
                "asiento_j" => "",
                "provincia" => "",
                "letras" => "",
                "digverificador" => "",
                "tomo" => "",
                "asiento" => "",
                "pasaporte" => "",
                "telefono" => "",
                "email" => "",
                "direccion" => "",
                "estado" => "",
                "guardar" => 1,
                'politicas' => "",
                'politicas_general' => ""
            ),
        );
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-archive"></i> Ajustadores: Crear',
            "ruta" => array(
                0 => array("nombre" => "Seguros", "url" => "#", "activo" => false),
                1 => array("nombre" => 'Ajustadores', "url" => "ajustadores/listar", "activo" => false),
                2 => array("nombre" => '<b>Crear</b>', "activo" => true)
            ),
            "filtro" => false,
            "menu" => array()
        );
        $data['mensaje'] = $mensaje;
        $this->template->agregar_titulo_header('Ajustadores: Crear');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    function ocultoformulario($data = array()) {
        $data['info']['provincias'] = $this->SegCatalogoRepository->listar_catalogo('provincias');
        $data['info']['letras'] = $this->SegCatalogoRepository->listar_catalogo('letras');
        $data['info']['estado'] = $this->SegCatalogoRepository->listar_catalogo_excepcion('estado', 'orden', 'Bloqueado');
        $data['info']['estado2'] = $this->SegCatalogoRepository->listar_catalogo_excepcion2('estado', 'orden', 'Bloqueado', 'Por aprobar');
        $data['info']['identificacion'] = $this->SegCatalogoRepository->listar_catalogo('identificacion');
        $clause = array('empresa_id' => $this->empresa_id);
        $this->assets->agregar_var_js(array(
        ));

        $this->load->view('formulario', $data);
    }

    function guardar() {
        if ($_POST) {
            unset($_POST["campo"]["guardar"]);
            $campo = Util::set_fieldset("campo");
            Capsule::beginTransaction();
            try {
                if (empty($campo['uuid'])) {
                    $ruc = "";
                    if ($_POST["campo"]["identificacion"] == "Juridico") {
                        if ($_POST["campo"]["tomo_j"] != "") {
                            $ruc = $_POST["campo"]["tomo_j"];
                        }
                        if ($_POST["campo"]["folio"] != "") {
                            $ruc .= "-" . $_POST["campo"]["folio"];
                        }
                        if ($_POST["campo"]["asiento_j"] != "") {
                            $ruc .= "-" . $_POST["campo"]["asiento_j"];
                        }
                        if ($_POST["campo"]["digverificador"] != "") {
                            $ruc .= " DV " . $_POST["campo"]["digverificador"];
                        }
                    } else if ($_POST["campo"]["identificacion"] == "Natural") {
                        if ($_POST["campo"]["pasaporte"] != "") {
                            $ruc = $_POST["campo"]["pasaporte"];
                        }
                        if (isset($_POST["campo"]["provincia"]) && ($_POST["campo"]["provincia"] != "")) {
                            $ruc .= $_POST["campo"]["provincia"];
                        }
                        if ($_POST["campo"]["letras"] != "") {
                            $ruc .= "-" . $_POST["campo"]["letras"];
                        }
                        if ($_POST["campo"]["tomo"] != "") {
                            $ruc .= "-" . $_POST["campo"]["tomo"];
                        }
                        if ($_POST["campo"]["asiento"] != "") {
                            $ruc .= "-" . $_POST["campo"]["asiento"];
                        }
                    }

                    $campo['ruc'] = $ruc;
                    $verificar_ruc = count($this->AjustadoresRepository->consultaRucEmp($ruc, $this->empresa_id));
                    $campo["uuid_ajustadores"] = Capsule::raw("ORDER_UUID(uuid())");
                    $clause['empresa_id'] = $this->empresa_id;
                    $total = $this->AjustadoresRepository->listar($clause);
                    $year = Carbon::now()->format('y');
                    $campo["creado_por"] = $this->session->userdata['id_usuario'];
                    $campo["empresa_id"] = $this->empresa_id;
                    $date = Carbon::now();
                    $date = $date->format('Y-m-d');
                    $campo['fecha_creacion'] = $date;
                    $campo['estado'] = "Por aprobar";
                    if ($verificar_ruc == 0) {
                        $ajustadores = $this->AjustadoresModel->create($campo);
                    } else {
                        $ajustadores = "";
                        $mensaje = array('tipo' => "error", 'mensaje' => '<b>¡&Eacute;rror!</b> Registro ya existe para esta empresa.', 'titulo' => 'Ajustadores ' . $_POST["campo"]["nombre"]);
                        $this->session->set_flashdata('mensaje', $mensaje);
                        redirect(base_url('ajustadores/crear'));
                    }
                } else {
                    echo "hola mundo";
                    var_dump("Hola mundo");
                }
                Capsule::commit();
            } catch (ValidationException $e) {
                log_message('error', $e);
                Capsule::rollback();
            }
            if (!is_null($ajustadores)) {
                $mensaje = array('tipo' => "success", 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente', 'titulo' => 'Ajustadores' . $_POST["campo"]["nombre"]);
            } else {
                $mensaje = array('tipo' => "error", 'mensaje' => '<b>¡&Eacute;rror!</b> Registro ya existe', 'titulo' => 'Ajustadores ' . $_POST["campo"]["nombre"]);
            }
        } else {
            $mensaje = array('tipo' => "error", 'mensaje' => '<b>¡&Eacute;rror!</b> Su solicitud no fue procesada', 'titulo' => 'Ajustadores ' . $_POST["campo"]["nombre"]);
        }

        $this->session->set_flashdata('mensaje', $mensaje);
        redirect(base_url('ajustadores/listar'));
    }

    public function ocultotabla() {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/ajustadores/tabla.js'
        ));

        $this->load->view('tabla');
    }

    function editar($uuid = NULL, $opcion = NULL) {
        //Definir mensaje
        if (!is_null($this->session->flashdata('mensaje'))) {
            $mensaje = $this->session->flashdata('mensaje');
        } else {
            $mensaje = [];
        }
        $this->assets->agregar_var_js(array(
            "flexio_mensaje" => collect($mensaje)
        ));


        if (!$this->auth->has_permission('acceso', 'ajustadores/editar') && !$this->auth->has_permission('acceso', 'ajustadores/ver')) {
            // No, tiene permiso, redireccionarlo.
            $acceso = 0;
            $mensaje = array('tipo' => "error", 'mensaje' => '<b>¡Error!</b> No tiene permisos para ingresar a editar ajustadores', 'titulo' => 'Ajustadores ');
            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('ajustadores/listar'));
        }


        $this->_Css();
        $this->_js();

        $this->assets->agregar_js(array(
            'public/assets/js/modules/ajustadores/formulario.js',
            'public/assets/js/modules/ajustadores/editar.js',
            'public/assets/js/default/vue-validator.min.js',
        ));

        $data = array();
        $mensaje = array();

        $ajustadores = $this->AjustadoresRepository->verAjustadores(hex2bin(strtolower($uuid)));


        if (!empty($_POST)) {
            $ajustadores = $this->AjustadoresRepository->verAjustadores(hex2bin(strtolower($_POST["campo"]["uuid"])));
            $campo = $this->input->post("campo");

            $ruc = "";
            if ($_POST["campo"]["identificacion"] == "Juridico") {
                if ($_POST["campo"]["tomo_j"] != "") {
                    $ruc = $_POST["campo"]["tomo_j"];
                }
                if ($_POST["campo"]["folio"] != "") {
                    $ruc .= "-" . $_POST["campo"]["folio"];
                }
                if ($_POST["campo"]["asiento_j"] != "") {
                    $ruc .= "-" . $_POST["campo"]["asiento_j"];
                }
                if ($_POST["campo"]["digverificador"] != "") {
                    $ruc .= " DV " . $_POST["campo"]["digverificador"];
                }
            } else if ($_POST["campo"]["identificacion"] == "Natural") {
                if ($_POST["campo"]["pasaporte"] != "") {
                    $ruc = $_POST["campo"]["pasaporte"];
                }
                if (isset($_POST["campo"]["provincia"]) && ($_POST["campo"]["provincia"] != "")) {
                    $ruc .= $_POST["campo"]["provincia"];
                }
                if ($_POST["campo"]["letras"] != "") {
                    $ruc .= "-" . $_POST["campo"]["letras"];
                }
                if ($_POST["campo"]["tomo"] != "") {
                    $ruc .= "-" . $_POST["campo"]["tomo"];
                }
                if ($_POST["campo"]["asiento"] != "") {
                    $ruc .= "-" . $_POST["campo"]["asiento"];
                }
            }

            $ajustadores->nombre = $campo["nombre"];
            $ajustadores->ruc = $ruc;
            $ajustadores->identificacion = $campo["identificacion"];
            $ajustadores->tomo_j = $campo["tomo_j"];
            $ajustadores->folio = $campo["folio"];
            $ajustadores->asiento_j = $campo["asiento_j"];
            $ajustadores->digverificador = $campo["digverificador"];
            $ajustadores->provincia = $campo["provincia"];
            $ajustadores->letras = $campo["letras"];
            $ajustadores->tomo = $campo["tomo"];
            $ajustadores->asiento = $campo["asiento"];
            $ajustadores->pasaporte = $campo["pasaporte"];
            $ajustadores->telefono = $campo["telefono"];
            $ajustadores->email = $campo["email"];
            $ajustadores->direccion = $campo["direccion"];
            $ajustadores->estado = $campo["estado"];

            if ($ruc != $campo["ruc"]){
                $verificar_ruc = count($this->AjustadoresRepository->consultaRucEmp($ruc, $this->empresa_id));
            } else {
                $verificar_ruc=0;
            }

            if ($verificar_ruc == 0) {
                if ($ajustadores->save()) {
                    $mensaje = array('tipo' => "success", 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente', 'titulo' => 'Ajustadores ' . $_POST["campo"]["nombre"]);
                } else {
                    $mensaje = array('tipo' => "error", 'mensaje' => '<b>¡&Eacute;xito!</b> Su solicitud no fue procesada', 'titulo' => 'Ajustadores ' . $_POST["campo"]["nombre"]);
                }
            } else {
                $mensaje = array('tipo' => "error", 'mensaje' => '<b>¡&Eacute;rror!</b> Registro ya existe', 'titulo' => 'Ajustadores ' . $_POST["campo"]["nombre"]);
                $this->session->set_flashdata('mensaje', $mensaje);
            }

            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('ajustadores/listar'));
        }

        $breadcrumb = array(
            "titulo" => '<i class="fa fa-archive"></i> Ajustadores ' . $ajustadores->nombre,
            "filtro" => false, //sin vista grid
            "menu" => array(
                'url' => 'javascipt:',
                'nombre' => "Acción",
                "opciones" => array(
                    "#datosAjustadoresBtn" => "Datos Ajustador",
                    "#agregarContactoBtn" => "Nuevo Contacto",
//                    "#agregarPlanBtn" => "Nuevo Plan",
                    "#exportarBtn" => "Exportar",
                )
            ),
            "ruta" => array(
                0 => array("nombre" => "Seguros", "url" => "#", "activo" => false),
                1 => array("nombre" => "Ajustadores", "url" => "ajustadores/listar", "activo" => false),
                2 => array("nombre" => $ajustadores->nombre, "activo" => true)
            )
        );

        if ($this->auth->has_permission('acceso', 'ajustadores/editar')) {
            $guardar = 1;
        } else {
            $guardar = 0;
        }

        $data["opcion"] = $opcion;

        $data["campos"] = array(
            "campos" => array(
                "created_at" => $ajustadores->created_at,
                "uuid_ajustadores" => $uuid,
                "ruc" => $ajustadores->ruc,
                "nombre" => $ajustadores->nombre,
                "identificacion" => $ajustadores->identificacion,
                "tomo_j" => $ajustadores->tomo_j,
                "folio" => $ajustadores->folio,
                "asiento_j" => $ajustadores->asiento_j,
                "provincia" => $ajustadores->provincia,
                "letras" => $ajustadores->letras,
                "digverificador" => $ajustadores->digverificador,
                "tomo" => $ajustadores->tomo,
                "asiento" => $ajustadores->asiento,
                "pasaporte" => $ajustadores->pasaporte,
                "telefono" => $ajustadores->telefono,
                "email" => $ajustadores->email,
                "direccion" => $ajustadores->direccion,
                "estado" => $ajustadores->estado,
                "guardar" => $guardar,
                'politicas' => $this->politicas,
                'politicas_general' => $this->politicas_general,
                'politicas_generales' =>$this->politicas_generales
            ),
        );
        $this->template->agregar_titulo_header('ajustadores');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    public function ajax_listar($grid = NULL) {
        $clause = array(
            "empresa_id" => $this->empresa_id
        );
        $nombre = $this->input->post('nombre', true);
        $ruc = $this->input->post('ruc', true);
        $telefono = $this->input->post('telefono', true);
        $email = $this->input->post('email', true);
        $direccion = $this->input->post('direccion', true);
        $estado = $this->input->post('estado', true);

        if (!empty($nombre)) {
            $clause["nombre"] = array('LIKE', "%$nombre%");
        }
        if (!empty($ruc)) {
            $clause["ruc"] = array('LIKE', "%$ruc%");
        }
        if (!empty($telefono)) {
            $clause["telefono"] = array('LIKE', "%$telefono%");
        }
        if (!empty($email)) {
            $clause["email"] = array('LIKE', "%$email%");
        }
        if (!empty($direccion)) {
            $clause["direccion"] = array('LIKE', "%$direccion%");
        }

        if (!empty($estado)) {
            $clause["estado"] = $estado;
        }

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();

        $count = $this->AjustadoresRepository->listar_ajustadores($clause, NULL, NULL, NULL, NULL)->count();

        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

        $rows = $this->AjustadoresRepository->listar_ajustadores($clause, $sidx, $sord, $limit, $start);


        //var_dump($rows);
        //Constructing a JSON
        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
        $response->result = array();
        $i = 0;

        if (!empty($rows)) {
            foreach ($rows AS $i => $row) {
                $uuid_ajustadores = bin2hex($row->uuid_ajustadores);
                $now = Carbon::now();
                $hidden_options = "";
                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row->id . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                //var_dump($row->uuid_ajustadores);
                if ($this->auth->has_permission('acceso', 'ajustadores/editar') || $this->auth->has_permission('acceso', 'ajustadores/ver')) {
                    $hidden_options .= '<a href="' . base_url('ajustadores/editar/' . $uuid_ajustadores) . '" data-id="' . $row->id . '" class="btn btn-block btn-outline btn-success">Ver detalle</a>';
                }
                $hidden_options .= '<a href="' . base_url('ajustadores/agregarcontacto/' . $uuid_ajustadores . '?opt=1') . '" data-id="' . $row->id . '" class="btn btn-block btn-outline btn-success">Agregar Contacto</a>';

                $response->rows[$i]["id"] = $row->id;
                $response->rows[$i]["cell"] = array(
                    $row->id,
                    '<a href="' . base_url('ajustadores/editar/' . $uuid_ajustadores) . '" style="color:blue;">' . $row->nombre . '</a>',
                    $row->ruc,
                    $row->telefono,
                    $row->email,
                    $row->direccion,
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

    function tabladetalles($data = array()) {
        /* $clause = array('empresa_id' => $this->empresa_id);        
          $this->assets->agregar_var_js(array(
          ));
         */
        $this->load->view('tabladetalles', $data);
    }

    function editoformulario($data = array()) {
        $data['info']['provincias'] = $this->SegCatalogoRepository->listar_catalogo('provincias');
        $data['info']['letras'] = $this->SegCatalogoRepository->listar_catalogo('letras');
        $data['info']['estado'] = $this->SegCatalogoRepository->listar_catalogo_excepcion('estado', 'orden', 'Bloqueado');
        $data['info']['estado2'] = $this->SegCatalogoRepository->listar_catalogo_excepcion2('estado', 'orden', 'Bloqueado', 'Por aprobar');
        $data['info']['identificacion'] = $this->SegCatalogoRepository->listar_catalogo('identificacion');
        $clause = array('empresa_id' => $this->empresa_id);
        $this->assets->agregar_var_js(array(
        ));

        $this->load->view('formulario', $data);
    }

    function agregarcontacto($uuid = NULL, $opcion = NULL) {
        if (!is_null($this->session->flashdata('mensaje'))) {
            $mensaje = $this->session->flashdata('mensaje');
        } else {
            $mensaje = [];
        }
        $this->_Css();
        $this->_js();

        $this->assets->agregar_js(array(
            'public/assets/js/modules/ajustadores/formulario.js',
            'public/assets/js/modules/ajustadores/crearcontacto.js',
            'public/assets/js/default/vue-validator.min.js',
        ));

        $data = array();

        if (!empty($_POST)) {
            if ($_POST["campo"]["uuid"] != "") {
                $contacto = $this->SegAjustadoresContactoRepository->verContactoUiid(hex2bin(strtolower($_POST["campo"]["uuid"])));

                $contacto->nombre = $_POST["campo"]["nombre"];
                $contacto->cargo = $_POST["campo"]["cargo"];
                $contacto->telefono = $_POST["campo"]["telefono"];
                $contacto->celular = $_POST["campo"]["celular"];
                $contacto->email = $_POST["campo"]["email"];
                $contacto->estado = "Activo";
                $date = Carbon::now();
                $date = $date->format('Y-m-d');
                $contacto->updated_at = $date;

                if ($contacto->save()) {
                    $mensaje = array('tipo' => "success", 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente', 'titulo' => 'Contacto ' . $_POST["campo"]["nombre"]);
                } else {
                    $mensaje = array('tipo' => "error", 'mensaje' => '<b>¡&Eacute;rror!</b> Su solicitud no fue procesada', 'titulo' => 'Contacto ' . $_POST["campo"]["nombre"]);
                }
                $this->session->set_flashdata('mensaje', $mensaje);
                $url = 'ajustadores/editar/' . $_POST["campo"]["uuid_ajustadores"];
                redirect(base_url($url));
            } else {
                $ajustadores = $this->AjustadoresRepository->verAjustadores(hex2bin(strtolower($_POST["campo"]["uuid_ajustadores"])));


                $campo = $this->input->post("campo");
                $verificar_email = count($this->SegAjustadoresContactoRepository->consultaEmail($campo["email"], $ajustadores->id));
                $campo["uuid_contacto"] = Capsule::raw("ORDER_UUID(uuid())");
                $campo["estado"] = "Activo";
                $campo['ajustador_id'] = $ajustadores->id;
                $year = Carbon::now()->format('y');
                $campo["creado_por"] = $this->session->userdata['id_usuario'];
                $date = Carbon::now();
                $date = $date->format('Y-m-d');
                $campo['created_at'] = $date;
                if ($verificar_email == 0) {
                    $ajustadores = $this->SegAjustadoresContactoModel->create($campo);
                } else {
                    $ajustadores = "";
                    $mensaje = array('tipo' => "error", 'mensaje' => '<b>¡&Eacute;rror!</b> Usuario ya existe ', 'titulo' => 'Contacto ' . $_POST["campo"]["nombre"]);
//                    var_dump($this->session->set_flashdata('mensaje', $mensaje));
                    $this->session->set_flashdata('mensaje', $mensaje);
                    $url = 'ajustadores/editar/' . $_POST["campo"]["uuid_ajustadores"];
                    redirect(base_url($url));
                }

                if ((!is_null($ajustadores)) && ($_POST["campo"]["opt"] == 1)) {
                    $mensaje = array('tipo' => "success", 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente', 'titulo' => 'Ajustadores: Contacto ' . $_POST["campo"]["nombre"]);
                    $this->session->set_flashdata('mensaje', $mensaje);
                    $url = 'ajustadores/editar/' . $_POST["campo"]["uuid_ajustadores"];
                    redirect(base_url($url));
                } else if ((!is_null($ajustadores)) && ($_POST["campo"]["opt"] == 2)) {
                    $mensaje = array('tipo' => "success", 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente', 'titulo' => 'Ajustadores: Contacto ' . $_POST["campo"]["nombre"]);
                    $this->session->set_flashdata('mensaje', $mensaje);
                    redirect(base_url('ajustadores/editar/' . $_POST["campo"]["uuid_ajustadores"]));
                } else {
                    $mensaje = array('tipo' => "error", 'mensaje' => '<b>¡Error!</b> Su solicitud no fue procesada', 'titulo' => 'Ajustadores: Contacto ' . $_POST["campo"]["nombre"]);
                    $this->session->set_flashdata('mensaje', $mensaje);
                    $url = 'ajustadores/editar/' . $_POST["campo"]["uuid_ajustadores"];
                    redirect(base_url($url));
                }
            }
        }
        $ajustadores = $this->AjustadoresRepository->verAjustadores(hex2bin(strtolower($uuid)));

        $breadcrumb = array(
            "titulo" => '<i class="fa fa-archive"></i> Ajustadores: ' . $ajustadores->nombre . ' Crear Contacto ',
            "filtro" => false, //sin vista grid
            "ruta" => array(
                0 => array("nombre" => "Seguros", "url" => "#", "activo" => false),
                1 => array("nombre" => "Ajustadores", "url" => "ajustadores/listar", "activo" => false),
                2 => array("nombre" => $ajustadores->nombre, "url" => "ajustadores/editar/" . $uuid . "", "activo" => false),
                3 => array("nombre" => "Crear Contacto", "activo" => false)
            )
        );

        $data["opcion"] = $opcion;
        $data["campos"] = array(
            "campos" => array(
                "uuid_ajustadores" => $uuid,
                "nombre" => '',
                "email" => '',
                "cargo" => '',
                "telefono" => '',
                "celular" => '',
                "uuid_contacto" => '',
                "opt" => $_GET['opt']
            ),
        );

        $this->template->agregar_titulo_header('Crear Contacto');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    function ocultoformulariocontacto($data = array()) {
        $clause = array('empresa_id' => $this->empresa_id);
        $this->assets->agregar_js(array(
            'public/assets/js/modules/ajustadores/crearcontacto.js',
        ));

        $this->load->view('formulariocontacto', $data);
    }

    function tabladetallescontactos($data = array()) {

        $this->assets->agregar_js(array(
            'public/assets/js/modules/ajustadores/tablacontacto.js',
        ));

        $this->load->view('tabladetallescontactos', $data);
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
        $clause = array(
            "empresa_id" => $this->empresa_id
        );
        $clause['ajustadores'] = $id;

        $ajustadores = $this->AjustadoresRepository->listar($clause, NULL, NULL, NULL, NULL);
        if (empty($ajustadores)) {
            return false;
        }
        $i = 0;
        foreach ($ajustadores AS $row) {
            $csvdata[$i]['nombre'] = $row->nombre;
            $csvdata[$i]["ruc"] = utf8_decode(Util::verificar_valor($row->ruc));
            $csvdata[$i]["telefono"] = utf8_decode(Util::verificar_valor($row->telefono));
            $csvdata[$i]["email"] = utf8_decode(Util::verificar_valor($row->email));
            $csvdata[$i]["direccion"] = utf8_decode(Util::verificar_valor($row->direccion));
            $i++;
        }
        //we create the CSV into memory
        $csv = Writer::createFromFileObject(new SplTempFileObject());
        $csv->insertOne([
            'Nombre',
            'Ruc',
            'Telefono',
            'Email',
            'Direccion'
        ]);
        $csv->insertAll($csvdata);
        $csv->output("ajustadores-" . date('ymd') . ".csv");
        exit();
    }

    public function exportarContactos() {
        if (empty($_POST)) {
            exit();
        }

        $ids = $this->input->post('ids', true);
        $id = explode(",", $ids);

        if (empty($id)) {
            return false;
        }

        $csv = array();

        $clause['id'] = $id;
        $contactos = $this->SegAjustadoresContactoRepository->listar_contactos($clause, NULL, NULL, NULL, NULL);
        if (empty($contactos)) {
            return false;
        }

        $i = 0;
        foreach ($contactos AS $row) {
            $csvdata[$i]['nombre'] = $row->nombre;
            $csvdata[$i]["cargo"] = utf8_decode(Util::verificar_valor($row->cargo));
            $csvdata[$i]["email"] = utf8_decode(Util::verificar_valor($row->email));
            $csvdata[$i]["celular"] = utf8_decode(Util::verificar_valor($row->celular));
            $csvdata[$i]["telefono"] = utf8_decode(Util::verificar_valor($row->telefono));
            $csvdata[$i]["estado"] = $row->estado;
            $i++;
        }
        //we create the CSV into memory
        $csv = Writer::createFromFileObject(new SplTempFileObject());
        $csv->insertOne([
            'Nombre',
            'Cargo',
            'Email',
            'Celular',
            'Telefono',
            'Estado'
        ]);
        $csv->insertAll($csvdata);
        $csv->output("contactos-" . date('ymd') . ".csv");
        exit();
    }

    public function ajax_listar_contacto($grid = NULL) {


        //print_r("uuid=".$this->ajustador_id);
        $ajustadores = $this->AjustadoresRepository->verAjustadores(hex2bin(strtolower($this->input->post('uuid_ajustadores'))));

        $id_ajustador = $ajustadores->id;

        $nombre = $this->input->post('nombre', true);
        $cargo = $this->input->post('cargo', true);
        $email = $this->input->post('email', true);
        $celular = $this->input->post('celular', true);
        $telefono = $this->input->post('telefono', true);
        $estado = $this->input->post('estado', true);

        if ($nombre != "")
            $clause['nombre'] = array('LIKE', '%' . $nombre . '%');
        if ($cargo != "")
            $clause['cargo'] = array('LIKE', '%' . $cargo . '%');
        if ($email != "")
            $clause['email'] = array('LIKE', '%' . $email . '%');
        if ($celular != "")
            $clause['celular'] = array('LIKE', '%' . $celular . '%');
        if ($telefono != "")
            $clause['telefono'] = array('LIKE', '%' . $telefono . '%');
        if ($estado == "Activo" || $estado == "Inactivo" || $estado == "Por aprobar") {
            $clause['estado'] = array('=', $estado);
        }


        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();

        $clause['ajustador_id'] = $id_ajustador;

        $count = $this->SegAjustadoresContactoRepository->listar_contactos($clause, NULL, NULL, NULL, NULL)->count();

        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

        $contactos = $this->SegAjustadoresContactoRepository->listar_contactos($clause, $sidx, $sord, $limit, $start);

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();


        list($total_pages, $page, $start) = Jqgrid::paginacion($count, 10, 2);

        //Constructing a JSON
        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->record = $count;
        $i = 0;

        if (!empty($contactos)) {
            foreach ($contactos as $row) {
                $uuid_ajustadorescontacto = bin2hex($row->uuid_contacto);
                $now = Carbon::now();
                $tituloBoton = ($row['estado'] != 1) ? 'Habilitar' : 'Deshabilitar';
                $estado = ($row['estado'] == 1) ? 0 : 1;
                $hidden_options = "";
                $link_option = '<button class="ajustadoresopciones btn btn-success btn-sm" type="button" data-id="' . $row['id'] . '"><i class="fa fa-cog" id="ajustadoresopciones"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $hidden_options .= '<a href="" data-id="' . $row['id'] . '" class="btn btn-block btn-outline btn-success detallecontacto">Ver detalle</a>';

                if ($row['estado'] == 'Activo')
                    $datoestado = 'Inactivar';
                else
                    $datoestado = 'Activar';


                $hidden_options .= '<a href="" data-id="' . $row['id'] . '" class="btn btn-block btn-outline btn-success verdetalleestado cambiarestadoseparado">' . $datoestado . '</a>';

                $level = substr_count($row['nombre'], ".");
                $spanStyle = "";

                if ($row['estado'] == 'Inactivo')
                    $spanStyle = 'label label-danger';
                else if ($row['estado'] == 'Activo')
                    $spanStyle = 'label label-successful';
                else
                    $spanStyle = 'label label-warning';

                if ($row['contacto_principal'] == 1)
                    $principal = '<label class="label label-warning">Principal</label>';
                else
                    $principal = '';

                $response->rows[$i] = array("id" => $row['id'], 'cell' => array(
                        'id' => $row['id'],
                        'nombre' => "<a href='' class='verdetallenombre' data-id='" . $row['id'] . "'><span style='" . $spanStyle . "'>" . $row['nombre'] . "</span></a> " . $principal,
                        'cargo' => $row['cargo'],
                        'cargo' => $row['cargo'],
                        'email' => $row['email'],
                        'celular' => $row['celular'],
                        'telefono' => $row['telefono'],
                        'estado' => "<label class='" . $spanStyle . " verdetalleestado cambiarestadoseparado' data-id='" . $row['id'] . "'>" . $row['estado'] . "</label>",
                        'estadoestado' => $row['estado'],
                        'principal' => $row['contacto_principal'],
                        'options' => $hidden_options,
                        'link' => $link_option,
                ));
                $i++;
            }
        }

        echo json_encode($response);
        exit;
    }

    public function obtener_politicas() {
        echo json_encode($this->politicas);
        exit;
    }

    public function obtener_politicas_general() {
        echo json_encode($this->politicas_general);
        exit;
    }
    public function obtener_politicasgenerales() {
        echo json_encode($this->politicas_generales);
        exit;
    }

    function ajax_cargar_contacto() {
        $id = $this->input->post('id');

        $contacto = $this->SegAjustadoresContactoRepository->verContacto($id);
        $nombre_ajustadores = $contacto->nombreAjustadores->nombre;
        $contacto = $contacto->toArray();
        $resources['datos'] = $contacto;
        $resources['datos']['uuid_contacto'] = bin2hex($contacto['uuid_contacto']);
        $resources['datos']['nombre_ajustadores'] = $nombre_ajustadores;

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($resources))->_display();
        exit;
    }

    function ajax_cambiar_contacto_principal() {
        $id = $this->input->post('id');

        $contacto = $this->SegAjustadoresContactoRepository->verContacto($id);
        $cambiarprincipal = $this->SegAjustadoresContactoRepository->cambiarPrincipal($contacto->ajustador_id);
        $nombre_ajustadores = $contacto->nombreAjustadores->nombre;

        $contacto->contacto_principal = 1;
        $contacto->save();
        $contacto = $contacto->toArray();
        $resources['datos'] = $contacto;
        $resources['datos']['uuid_contacto'] = bin2hex($contacto['uuid_contacto']);
        $resources['datos']['nombre_ajustadores'] = $nombre_ajustadores;
        $resources['datos']['principal'] = 1;

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($resources))->_display();
        exit;
    }

    function ajax_cambiar_estado_contacto() {
        $id = $this->input->post('id');

        $contacto = $this->SegAjustadoresContactoRepository->verContacto($id);
        $nombre_ajustadores = $contacto->nombreAjustadores->nombre;

        if ($contacto->estado == 'Activo') {
            $spanStyle = 'label label-danger';
            $nuevoestado = 'Inactivo';
        } else {
            $nuevoestado = 'Activo';
            $spanStyle = 'label label-successful';
        }


        $contacto->estado = $nuevoestado;
        $contacto->save();
        $estadoestado = $contacto->estado;
        $contacto = $contacto->toArray();
        $resources['datos'] = $contacto;
        $resources['datos']['uuid_contacto'] = bin2hex($contacto['uuid_contacto']);
        $resources['datos']['nombre_ajustadores'] = $nombre_ajustadores;
        $resources['datos']['estadoestado'] = $estadoestado;
        $resources['datos']['labelestado'] = $spanStyle;

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($resources))->_display();
        exit;
    }

    public function imprimirContacto($uuid = null) {
        if ($uuid == null) {
            return false;
        }
        //$uuid=$this->input->post('id');

        $contacto = $this->SegAjustadoresContactoRepository->verContactoUiid(hex2bin(strtolower($uuid)));
        $data = ['contacto' => $contacto];
        $dompdf = new Dompdf();
        $html = $this->load->view('pdf/formulariocontacto', $data, true);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream($contacto->nombre);
    }

    private function _js() {
        $this->assets->agregar_js(array(
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
            'public/assets/js/default/jquery.inputmask.bundle.min.js',
            'public/assets/js/plugins/jquery/jquery.webui-popover.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/plugins/jquery/switchery.min.js',
            'public/assets/js/plugins/bootstrap/jquery.bootstrap-touchspin.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            'public/assets/js/default/formulario.js',
            'public/assets/js/plugins/jquery/fileupload/jquery.fileupload.js',
            'public/assets/js/plugins/bootstrap/select2/select2.min.js',
            'public/assets/js/plugins/bootstrap/select2/es.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/default/vue/directives/inputmask.js',
        ));
    }

    private function _css() {
        $this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/plugins/jquery/switchery.min.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
            'public/assets/css/plugins/bootstrap/bootstrap-tagsinput.css',
            'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
            'public/assets/css/plugins/jquery/fileinput/fileinput.css',
            'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
            'public/assets/css/plugins/bootstrap/awesome-bootstrap-checkbox.css',
            'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
            'public/assets/css/plugins/bootstrap/select2.min.css',
        ));
    }

}

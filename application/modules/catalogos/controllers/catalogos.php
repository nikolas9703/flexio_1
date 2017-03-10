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
 * @link       http://www.pensanomica.com
 * @copyright  05/22/2015
 */
use Illuminate\Database\Capsule\Manager as Capsule;
use League\Csv\Writer as Writer;
use Carbon\Carbon;
use Flexio\Modulo\Planes\Repository\PlanesRepository as PlanesRepository;
use Flexio\Modulo\Planes\Models\Planes as PlanesModel;
use Flexio\Modulo\Catalogos\Repository\CatalogosRepository as CatalogosRepository;
use Flexio\Modulo\Catalogos\Models\Catalogos as CatalogosModel;
use Flexio\Modulo\Catalogos\Models\RamosDocumentos;
use Flexio\Modulo\Planes\Models\Planes_orm as PlanesFormModel;
use Flexio\Modulo\aseguradoras\Models\Aseguradoras as AseguradorasModel;
use Flexio\Modulo\Roles\Models\Roles as Roles;
use Flexio\Modulo\Usuarios\Models\Usuarios as Usuarios;
use Flexio\Modulo\Ramos\Models\CatalogoTipoPoliza as CatalogoTipoPoliza;
use Flexio\Modulo\Ramos\Models\CatalogoTipoIntereses as CatalogoTipoIntereses;
use Flexio\Modulo\Politicas\Repository\PoliticasRepository as PoliticasRepository;
use Flexio\Modulo\Ramos\Models\RamosRoles;
use Flexio\Modulo\Ramos\Models\RamosUsuarios;
use Flexio\Modulo\Ramos\Repository\RamoRepository;
use Flexio\Modulo\Geo\Models\Provincia as provincias;
use Flexio\Modulo\Geo\Models\Corregimiento as corregimientos;
use Flexio\Modulo\Geo\Models\Distrito as distritos;
use Flexio\Modulo\Rutas\Models\Rutas as rutas;
//use Flexio\Modulo\Solicitudes\Models\SolicitudesDocumentacionCategoria as DocumentacionCategoria;
use Flexio\Modulo\Usuarios\Models\RolesUsuario;
use Flexio\Modulo\Rutas\Repository\RutasRepository as rutasRepository;

//use Flexio\Modulo\aseguradoras\Models\Aseguradoras_orm as AseguradorasFormModel;

class Catalogos extends CRM_Controller {

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
    protected $SegurosPlanesRepository;
    protected $politicas;
    protected $politicas_general;
    protected $PoliticasRepository;
	protected $provincias;
	protected $corregimientos;
	protected $distritos;
	protected $rutas;
	protected $rutasRepository;
//    protected $DocumentacionCategoria;

    /**
     * @var string
     */
    protected $upload_folder = './public/uploads/';

    function __construct() {
        parent::__construct();

        //$this->load->model('Planes_orm');
        //$this->load->model('Coberturas_orm');

        $this->load->helper(array('file', 'string', 'util'));
        $this->load->model('catalogos/Aseguradoras_orm');
        $this->load->model('configuracion_seguros/Comisiones_orm');
        $this->load->model('Contabilidad/tipo_cuentas_orm');
        $this->load->model('Contabilidad/Cuentas_orm');
        $this->load->model('catalogos/Ramos_orm');
        $this->load->model('contactos/Contacto_orm');
        //$this->load->model('aseguradoras/Planes_orm');
        //$this->load->model('aseguradoras/Coberturas_orm');
        $this->load->model('usuarios/usuario_orm');
        $this->load->model('usuarios/Roles_usuarios_orm');
        $this->load->model('usuarios/organizacion_orm');
        $this->load->model('usuarios/organizacion_orm');
        //$this->load->model('aseguradoras/Catalogo_tipo_poliza_orm');
        //$this->load->model('aseguradoras/Catalogo_tipo_intereses_orm');
        $this->load->model('contabilidad/Impuestos_orm');

        $this->load->dbutil();
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresaObj = new Buscar(new Empresa_orm, 'uuid_empresa');
        $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
        $this->id_usuario = $this->session->userdata("huuid_usuario");
        $this->id_empresa = $this->empresaObj->id;
		
		//cargar las provincias, distritos y corregimientos
		
		$this->provincias=new provincias();
		$this->corregimientos=new corregimientos();
		$this->distritos=new distritos();
		$this->rutasRepository= new rutasRepository();


        //Obtener el id de usuario de session
        $uuid_usuario = $this->session->userdata('huuid_usuario');
        $usuario = Usuario_orm::findByUuid($uuid_usuario);

        $this->usuario_id = $usuario->id;
		$this->rutas=new rutas();

        //Obtener el id_empresa de session
        //$uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $this->empresa_id = $empresa->id;
        $this->PoliticasRepository = new PoliticasRepository();
        $this->PlanesRepository = new PlanesRepository();
//        $this->DocumentacionCategoria = new DocumentacionCategoria();

        $this->roles = $this->session->userdata("roles");
        //$roles=implode(",", $this->roles);

        $clause['empresa_id'] = $this->empresa_id;
        $clause['modulo'] = 'ramos';
        $clause['usuario_id'] = $this->usuario_id;
        $clause['role_id'] = $this->roles;

        $politicas_transaccion = $this->PoliticasRepository->getAllPoliticasRoles($clause);

        $politicas_transaccion_general = count($this->PoliticasRepository->getAllPoliticasRolesModulo($clause));
        $this->politicas_general = $politicas_transaccion_general;

        $estados_politicas = array();
        foreach ($politicas_transaccion as $politica_estado) {
            $estados_politicas[] = $politica_estado->politica_estado;
        }

        $this->politicas = $estados_politicas;
    }

    public function listar() {

        $data = array();

        $this->_Css();
        $this->_js();

        $this->assets->agregar_js(array(
            'public/assets/js/modules/planes/listar.js'
        ));


        //defino mi mensaje
        if (!is_null($this->session->flashdata('mensaje'))) {
            $mensaje = json_encode($this->session->flashdata('mensaje'));
        } else {
            $mensaje = '';
        }
        $this->assets->agregar_var_js(array(
            "toast_mensaje" => $mensaje
        ));

        //Verificar permisos para crear
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-archive"></i> Catalogos',
            "ruta" => array(
                0 => array("nombre" => "Seguros", "url" => "#", "activo" => false),
                1 => array("nombre" => '<b>Catalogos</b>', "activo" => true)
            ),
            "filtro" => false,
            "menu" => array()
        );

        if ($this->auth->has_permission('acceso', 'catalogos/ver')) {
            $breadcrumb["menu"] = array(
                "url" => 'catalogos/ver',
                "nombre" => "Crear"
            );
            $menuOpciones["#cambiarEstadoLnk"] = "Cambiar Estado";
            $menuOpciones["#exportarBtn"] = "Exportar";
            $breadcrumb["menu"]["opciones"] = $menuOpciones;
        }

        //Menu para crear
        $clause = array('empresa_id' => $this->empresa_id);

        //$data['menu_crear'] = array('nombre'=>1); 
        /* //catalogo para buscador        
          $data['planes'] = planes_orm::where($clause)->get();
          $data['tipo'] = Catalogo_tipo_poliza_orm::get();
          $data['usuarios'] = usuario_orm::where('estado', 'Activo')->get();
          /*$clause2['empresa_id'] = $this->empresa_id; */

        $this->template->agregar_titulo_header('Listado de Catalogos');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }

    public function ver($vista = null, $id = null) {
        $acceso = 1;
        $mensaje = array();
        $mensaje2 = array();
        $data = array();
        $planes = array();
        $cobertura = array();



        /* $clause_acceso= array('empresa_id' => $this->empresa_id,'usuario_id' => $this->usuario_id);
          $accesos = Roles_Usuarios_orm::accesos($clause_acceso, null, null, null, null);

          $urls=array();
          foreach ($accesos as $value) {
          array_push($urls, $value['nombrerecurso']);
          }

          if (in_array('crear Planes', $urls)) { $plancrear =1;  }else{ $plancrear=0; }
          if (in_array('listar Planes', $urls)) { $planlistar =1;  }else{ $planlistar=0; }
          if (in_array('editar Planes', $urls)) { $planeditar =1;  }else{ $planeditar=0; }
          if (in_array('ver Planes', $urls)) { $planver =1;  }else{ $planver=0; } */
        $ramosPermission = array(
            'crear' => $this->auth->has_permission('acceso', 'crear Ramos'),
            'listar' => $this->auth->has_permission('acceso', 'listar Ramos'),
            'editar' => $this->auth->has_permission('acceso', 'editar Ramos'),
        );

        $planesPermission = array(
            'crear' => $this->auth->has_permission('acceso', 'crear Planes'),
            'listar' => $this->auth->has_permission('acceso', 'listar Planes'),
            'editar' => $this->auth->has_permission('acceso', 'editar Planes'),
        );

        $data['ramosPermission'] = $ramosPermission;
        $data['planesPermission'] = $planesPermission;
        if ($this->auth->has_permission('acceso', 'crear Planes')) {
            $plancrear = 1;
        } else {
            $plancrear = 0;
        }
        if ($this->auth->has_permission('acceso', 'listar Planes')) {
            $planlistar = 1;
        } else {
            $planlistar = 0;
        }
        if ($this->auth->has_permission('acceso', 'editar Planes')) {
            $planeditar = 1;
        } else {
            $planeditar = 0;
        }
        if ($this->auth->has_permission('acceso', 'ver Planes')) {
            $planver = 1;
        } else {
            $planver = 0;
        }
        // if($this->auth->has_permission('acceso','ver Catálogos')){ print 1;  }else{ redirect(base_url('')); }
        if (!$this->auth->has_permission('acceso')) {
            // No, tiene permiso, redireccionarlo.
            $acceso = 0;
            $mensaje = array('estado' => 500, 'mensaje' => ' <b>Usted no cuenta con permiso para esta solicitud</b>', 'clase' => 'alert-danger');
            // redirect(base_url(''));
        }


        if ($vista == "exitoso") {
            $data["mensaje"]["clase"] = "alert-success";
            $data["mensaje"]["contenido"] = '<b>¡&Eacute;xito!</b> Se ha guardado correctamente.';
            $mensaje = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente.', 'clase' => 'alert-success');
            //echo "si";
        } else if ($vista == "fallo") {
            $data["mensaje"]["clase"] = "alert-danger";
            $data["mensaje"]["contenido"] = '<strong>¡Error!</strong> Su solicitud no fue procesada';
            $mensaje = array('estado' => 500, 'mensaje' => '<strong>¡Error!</strong> Su solicitud no fue procesada', 'clase' => 'alert-danger');
            //echo "no";
        } else if ($vista == "planes") {
            $mensaje2 = array('estado' => 0);
        }

        $a = Aseguradoras_orm::findByUuid($this->input->get('a'));

        if ($a != NULL) {
            $data['uuida'] = array('id' => $a->id, 'ua' => $this->input->get('a'));
        } else {
            $data['uuida'] = array('id' => '', 'ua' => '');
        }


        $this->_Css();
        // $this->_js();

        $this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/plugins/jquery/jstree/default/style.min.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
            'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
            'public/assets/css/modules/stylesheets/aseguradoras.css',
            'public/assets/css/plugins/jquery/switchery.min.css'
        ));
        $this->assets->agregar_js(array(
			
			'public/assets/js/modules/rutas/configuracion.js',
			'public/assets/js/modules/rutas/routes.js',
            'public/assets/js/modules/planes/formulario.js',
            'public/assets/js/modules/planes/crear.js',
            'public/assets/js/modules/planes/crear.vue.js',
            'public/assets/js/modules/planes/component.vue.js',
            'public/assets/js/modules/planes/plugins.js',
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/default/lodash.min.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/plugins/jquery/jquery.progresstimer.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/jstree.min.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.numeric.extensions.js',
            'public/assets/js/default/vue.js',
            'public/assets/js/modules/ramos/routes.js',
            'public/assets/js/modules/planes/configuracion.js',
            'public/assets/js/modules/ramos/configuracion.js',
            'public/assets/js/plugins/jquery/switchery.min.js',
            'public/assets/js/default/formulario.js',
        ));


        $this->assets->agregar_var_js(array(
            "vista" => 'crear',
            "acceso" => $acceso,
            "data_planes" => (!empty($planes)) ? $planes->toJson() : '',
                //"id_planes" => ($id==null) ? '' : $id,
                //"id_aseguradora" => (!isset($aseguradora)) ? '' : $aseguradora->id
        ));
        $menuOpciones = array(
            "#activarLnk" => "Habilitar",
            "#inactivarLnk" => "Deshabilitar",
            "#exportarLnk" => "Exportar",
        );

        /* $data=array();      
          $this->assets->agregar_var_js(array(
          "vista" => 'crear',
          "acceso" => $acceso,
          )); */

        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);

        $breadcrumb = array(
            "titulo" => '<i class="fa fa-archive"></i> Catálogos: Crear  ',
            "ruta" => array(
                0 => array("nombre" => "Seguros", "url" => "#", "activo" => false),
                1 => array("nombre" => 'Catálogos', "url" => "catalogos/ver", "activo" => false),
                2 => array("nombre" => '<b>Crear</b>', "activo" => true)
            ),
            "filtro" => false,
            "menu" => array()
        );
        if ($this->auth->has_permission('acceso', 'catalogos/ver')) {
            $breadcrumb["menu"] = array(
                "url" => '#',
                "nombre" => "Acción"
            );
            $menuOpciones2["#exportarBtn"] = "Exportar";
            $breadcrumb["menu"]["opciones"] = $menuOpciones2;
        }
        $clause = array('empresa_id' => $this->id_empresa, 'estado' => 1);
        $clauseasegura = array('empresa_id' => $this->id_empresa, 'estado' => 'Activo');
        $data['mensaje'] = $mensaje;
        $data['mensaje2'] = $mensaje2;
        $data['accesoplan'] = array('plancrear' => $plancrear, 'planeditar' => $planeditar, 'planver' => $planver, 'planlistar' => $planlistar);
        $data['usuarios'] = Usuarios::join('usuarios_has_empresas', 'usuario_id', '=', 'usuarios.id')
                ->where('usuarios_has_empresas.empresa_id', '=', $this->id_empresa)
                ->where('usuarios.estado', '=', 'Activo')
                ->select('usuarios.id', 'nombre', 'apellido')
                ->get();
        $data['roles'] = Roles::where($clause)->get();
//        $data['documentacion_n'] = DocumentacionCategoria::all();

        $data['aseguradoras'] = AseguradorasModel::where($clauseasegura)->get();
		$data['provincias'] = $this->provincias->get();
        $data['tipo_intereses'] = CatalogoTipoIntereses::all();
        $data['tipo_poliza'] = CatalogoTipoPoliza::all();
        $data['impuestos'] = Impuestos_orm::impuesto_select(array('empresa_id' => $empresa->id, 'estado' => 'Activo'));
        $this->template->agregar_titulo_header('Planes: Crear');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    public function editar($vista = null, $id = null) {
        $acceso = 1;
        $mensaje = array();
        $mensaje2 = array();
        $data = array();
        $planes = array();
        $cobertura = array();


        //if($this->auth->has_permission('acceso','crear Planes')){ $plancrear =1;  }else{ $plancrear=0; }
        //if($this->auth->has_permission('acceso','listar Planes')){ $planlistar =1;  }else{ $planlistar=0; }
        if ($this->auth->has_permission('acceso', 'editar Planes')) {
            $planeditar = 1;
        } else {
            $planeditar = 0;
        }
        if ($this->auth->has_permission('acceso', 'ver Planes')) {
            $planver = 1;
        } else {
            $planver = 0;
        }

        if (!$this->auth->has_permission('acceso')) {
            // No, tiene permiso, redireccionarlo.
            $acceso = 0;
            $mensaje = array('estado' => 500, 'mensaje' => ' <b>Usted no cuenta con permiso para esta solicitud</b>', 'clase' => 'alert-danger');
            redirect(base_url(''));
        }





        $this->_Css();
        // $this->_js();

        $this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/plugins/jquery/jstree/default/style.min.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
            'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
            'public/assets/css/modules/stylesheets/aseguradoras.css',
            'public/assets/css/plugins/jquery/switchery.min.css'
        ));
        $this->assets->agregar_js(array(
            'public/assets/js/modules/planes/formulario.js',
            'public/assets/js/modules/planes/crear.js',
            'public/assets/js/modules/planes/crear.vue.js',
            'public/assets/js/modules/planes/component.vue.js',
            'public/assets/js/modules/planes/plugins.js',
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/default/lodash.min.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/plugins/jquery/jquery.progresstimer.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/jstree.min.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.numeric.extensions.js',
            'public/assets/js/default/vue.js',
            'public/assets/js/modules/ramos/routes.js',
            'public/assets/js/modules/planes/configuracion.js',
            'public/assets/js/modules/ramos/configuracion.js',
            'public/assets/js/plugins/jquery/switchery.min.js',
            'public/assets/js/default/formulario.js',
        ));


        $this->assets->agregar_var_js(array(
            "vista" => 'editar',
            "acceso" => $acceso,
            "data_planes" => (!empty($planes)) ? $planes->toJson() : '',
                //"id_planes" => ($id==null) ? '' : $id,
                //"id_aseguradora" => (!isset($aseguradora)) ? '' : $aseguradora->id
        ));
        $menuOpciones = array(
            "#activarLnk" => "Habilitar",
            "#inactivarLnk" => "Deshabilitar",
            "#exportarLnk" => "Exportar",
        );


        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);

        $breadcrumb = array(
            "titulo" => '<i class="fa fa-archive"></i> Catálogos: Editar  ',
            "ruta" => array(
                0 => array("nombre" => "Seguros", "url" => "#", "activo" => false),
                1 => array("nombre" => 'Catálogos', "url" => "catalogos/ver", "activo" => false),
                2 => array("nombre" => '<b>Editar</b>', "activo" => true)
            ),
            "filtro" => false,
            "menu" => array()
        );
        if ($this->auth->has_permission('acceso', 'catalogos/editar')) {
            $breadcrumb["menu"] = array(
                "url" => '#',
                "nombre" => "Acción"
            );
            $menuOpciones2["#cambiarEstadoLnk"] = "Cambiar Estado";
            $menuOpciones2["#exportarBtn"] = "Exportar";
            $breadcrumb["menu"]["opciones"] = $menuOpciones2;
        }
        $clause = array('empresa_id' => $this->id_empresa, 'estado' => 1);
        $clauseasegura = array('empresa_id' => $this->id_empresa, 'estado' => 'Activo');
        $data['mensaje'] = $mensaje;
        $data['mensaje2'] = $mensaje2;
        //$data['accesoplan'] = array('plancrear' => $plancrear, 'planeditar' => $planeditar, 'planver' => $planver, 'planlistar' => $planlistar);

        $data['roles'] = Roles::where($clause)->get();

        $data['aseguradoras'] = AseguradorasModel::where($clauseasegura)->get();
        $data['tipo_intereses'] = CatalogoTipoIntereses::all();
        $data['tipo_poliza'] = CatalogoTipoPoliza::all();
        $data['impuestos'] = Impuestos_orm::impuesto_select(array('empresa_id' => $empresa->id, 'estado' => 'Activo'));
        $this->template->agregar_titulo_header('Planes: Crear');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    public function ajax_listar_ramos_tree() {

        if (!$this->input->is_ajax_request()) {
            return false;
        }

        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $tipo = $this->input->post('tipo');
        $clause = array('empresa_id' => $empresa->id);
        if (!empty($tipo))
            $clause['tipo_cuenta_id'] = $tipo;
        $cuentas = Ramos_orm::listar_cuentas($clause);
        //Constructing a JSON
        $response = new stdClass();
        $response->plugins = ["contextmenu"];
        $response->core->check_callback[0] = true;

        $i = 0;
        if (!empty($cuentas)) {
            foreach ($cuentas as $row) {
                $spanStyle = ($row['estado'] == 1) ? '' : 'color:red;';
                $response->core->data[$i] = array(
                    'id' => (string) $row['id'],
                    'parent' => $row["padre_id"] == 0 ? "#" : (string) $row["padre_id"],
                    'text' => "<span id='labelramo' style='" . $spanStyle . "'>" . $row["nombre"] . "</span>",
                    'icon' => 'fa fa-folder',
                    'codigo' => $row["id"]
                        //'state' =>array('opened' => true)
                );

                $i++;
            }
        }

        echo json_encode($response);
        exit;
    }

    public function ajax_listar_ramos() {
        //Just Allow ajax request

        if (!$this->input->is_ajax_request()) {
            return false;
        }

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $tipo = $this->input->post('tipo');
        $nombre = (string) $this->input->post('nombre');
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        //fix count
        $count = Ramos_orm::where('empresa_id', $empresa->id)->count();

        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

        $clause = array('empresa_id' => $empresa->id);
        if (!empty($tipo))
            $clause['tipo_cuenta_id'] = $tipo;
        //if(!empty($nombre)) $clause['nombre'] = array('like',"%$nombre%");

        $cuentas = Ramos_orm::listar($clause, $nombre, $sidx, $sord, $limit, $start);

        //Constructing a JSON
        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->record = $count;
        $i = 0;

        if (!empty($cuentas)) {
            foreach ($cuentas as $row) {

                $tituloBoton = ($row['estado'] != 1) ? 'Habilitar' : 'Deshabilitar';
                $estado = ($row['estado'] == 1) ? 0 : 1;
                $hidden_options = "";
                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row['id'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $hidden_options .= '<a href="javascript:" data-id="' . $row['id'] . '" class="btn btn-block btn-outline btn-success editarRamoBtn">Editar Ramo</a>';
                $hidden_options .= '<a href="javascript:" data-id="' . $row['id'] . '" data-estado="' . $estado . '" class="btn btn-block btn-outline btn-success cambiarEstadoRamoBtn">' . $tituloBoton . ' Ramo</a>';
                $level = substr_count($row['nombre'], ".");
                $spanStyle = ($row['estado'] == 1) ? '' : 'color:red;';
                $response->rows[$i] = array("id" => $row['id'], 'cell' => array(
                        'id' => $row['id'],
                        'nombre' => "<span style='" . $spanStyle . "'>" . $row['nombre'] . "</span>",
                        'descripcion' => "<span style='" . $spanStyle . "'>" . $row['descripcion'] . "</span>",
                        'codigo' => $row['codigo_ramo'],
                        'tipo_interes' => $row['interes_asegurado']["nombre"],
                        'tipo_poliza' => $row['tipo_poliza']["nombre"],
                        'estado' => "<span style='" . $spanStyle . "'>" . (($row['estado'] == 1) ? 'Habilitado' : 'Deshabilitado') . "</span>",
                        'opciones' => $link_option,
                        'link' => $hidden_options,
                        "level" => isset($row["level"]) ? $row["level"] : "0", //level
                        'parent' => $row["padre_id"] == 0 ? "NULL" : (string) $row["padre_id"], //parent
                        'isLeaf' => (Ramos_orm::is_parent($row['id']) == true) ? false : true, //isLeaf
                        'expanded' => false, //expended
                        'loaded' => true, //loaded
                ));
                $i++;
            }
        }

        echo json_encode($response);
        exit;
    }

    function ajax_guardar_ramos() {
        if (!$this->input->is_ajax_request()) {
            return false;
        }


        $response = new stdClass();
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $id = $this->input->post('id');
        $nombre = $this->input->post('nombre');
        $descripcion = $this->input->post('descripcion');
        $codigo_ramo = $this->input->post('codigo_ramo');
        $tipo_interes_ramo = $this->input->post('tipo_interes_ramo');
        $tipo_poliza_ramo = $this->input->post('tipo_poliza_ramo');
        $form_solicitud = $this->input->post('form_solicitud');
        $padre_id = $this->input->post('codigo');
        $cuenta_id = $this->input->post('cuenta_id');


        if (!isset($id)) {
            $clause = array(
                "codigo_ramo" => strtoupper($codigo_ramo),
                "empresa_id" => $empresa->id
            );
            $existe = Ramos_orm::findCodigo($clause);
            if ($existe && $codigo_ramo != '') {
                $response->clase = "danger";
                $response->estado = 200;
                $response->mensaje = '<b>Error</b> Codigo ya existe.';
                echo json_encode($response);
                exit;
            } else {
                $datos = array();
                $datos['nombre'] = $nombre;
                $datos['descripcion'] = $descripcion;
                $datos['codigo_ramo'] = strtoupper($codigo_ramo);
                $datos['id_tipo_int_asegurado'] = $tipo_interes_ramo;
                $datos['id_tipo_poliza'] = $tipo_poliza_ramo;
                $datos['empresa_id'] = $empresa->id;
                $datos['padre_id'] = $padre_id;
                $impuesto_save = Ramos_orm::create($datos);
                $response->clase = "success";
                $response->estado = 200;
                $response->mensaje = '<b>¡&Eacute;xito!</b> Se ha guardado correctamente  ' . $impuesto_save->nombre;
            }
        } else {
            $impuesto_save = Ramos_orm::find($id);

            if ($impuesto_save->codigo_ramo != strtoupper($codigo_ramo)) {
                $clause = array(
                    "codigo_ramo" => strtoupper($codigo_ramo),
                    "empresa_id" => $empresa->id
                );
                $existe = Ramos_orm::findCodigo($clause);
                if ($existe) {
                    $response->clase = "danger";
                    $response->estado = 200;
                    $response->mensaje = '<b>Error</b> Codigo ya existe.';
                    echo json_encode($response);
                    exit;
                }
            }
            $impuesto_save->nombre = $nombre;
            $impuesto_save->descripcion = $descripcion;
            $impuesto_save->codigo_ramo = strtoupper($codigo_ramo);
            $impuesto_save->id_tipo_int_asegurado = $tipo_interes_ramo;
            $impuesto_save->id_tipo_poliza = $tipo_poliza_ramo;
            $impuesto_save->padre_id = $padre_id;
            $impuesto_save->save();
            $response->clase = "success";
            $response->estado = 200;
            $response->mensaje = '<b>¡&Eacute;xito!</b> Se ha actualizado correctamente  ' . $impuesto_save->nombre;
        }

        echo json_encode($response);
        exit;
    }

    function ajax_guardar_documentos() {
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        $documentos['id_ramo'] = $this->input->post('id_ramo');
        $documentos['nombre'] = $this->input->post('nombre');
        $documentos['categoria'] = $this->input->post('categoria');
        $documentos['modulo'] = $this->input->post('modulo');
        $documentos['estado'] = "Activo";
        $response = new stdClass();
        $documetos = RamosDocumentos::create($documentos);
        if (count($documetos) > 0) {
            $response->clase = "success";
            $response->estado = 200;
            $response->mensaje = '<b>¡&Eacute;xito!</b> Se ha gurdado correctamente  ' . $documentos['nombre'];
        } else {
            $response->clase = "danger";
            $response->estado = 200;
            $response->mensaje = '<b>Error</b> al guardar.';
        }
        echo json_encode($response);
        exit;
    }

    function ocultoformulario() {
        $clause = array('empresa_id' => $this->empresa_id);
        $this->assets->agregar_var_js(array(
        ));

        $this->load->view('formulario');
    }

    function guardar() {
        if ($_POST) {
            unset($_POST["campo"]["guardar"]);
            $campo = Util::set_fieldset("campo");
            Capsule::beginTransaction();
            try {
                if (empty($campo['uuid'])) {
                    $campo["uuid_planes"] = Capsule::raw("ORDER_UUID(uuid())");
                    $clause['empresa_id'] = $this->empresa_id;
                    $total = $this->solicitudesRepository->listar($clause);
                    $year = Carbon::now()->format('y');
                    $codigo = Util::generar_codigo($_POST['codigo_ramo'] . "-" . $year, count($total) + 1);
                    $campo["numero"] = $codigo;
                    $campo["usuario_id"] = $this->session->userdata['id_usuario'];
                    $campo["empresa_id"] = $this->empresa_id;
                    $date = Carbon::now();
                    $date = $date->format('Y-m-d');
                    $campo['fecha_creacion'] = $date;
                    $solicitudes = $this->solicitudesModel->create($campo);
                } else {
                    echo "hola mundo";
                }
                Capsule::commit();
            } catch (ValidationException $e) {
                log_message('error', $e);
                Capsule::rollback();
            }
            if (!is_null($solicitudes)) {
                $mensaje = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente');
            } else {
                $mensaje = array('class' => 'alert-danger', 'contenido' => '<strong>¡Error!</strong> Su solicitud no fue procesada');
            }
        } else {
            $mensaje = array('class' => 'alert-warning', 'contenido' => '<strong>¡Error!</strong> Su solicitud no fue procesada');
        }

        $this->session->set_flashdata('mensaje', $mensaje);
        redirect(base_url('solicitudes/listar'));
    }

    public function ocultotabla() {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/planes/tabla.js'
        ));

        $this->load->view('tabla');
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
        $aseguradora = $this->input->post('planes', true);

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

        if (!empty($aseguradora)) {
            $clause["creado_por"] = $aseguradora;
        }

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();

        $count = $this->PlanesRepository->listar_planes($clause, NULL, NULL, NULL, NULL)->count();

        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

        $rows = $this->PlanesRepository->listar_planes($clause, $sidx, $sord, $limit, $start);

        //Constructing a JSON
        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
        $response->result = array();
        $i = 0;

        if (!empty($rows)) {
            foreach ($rows AS $i => $row) {
                $uuid_aseguradora = bin2hex($row->uuid_aseguradora);
                $now = Carbon::now();
                $hidden_options = "";
                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row->id . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

                $estado = "Pendiente";
                $estado_color = trim($estado) == "Pendiente" ? 'background-color:#F8AD46' : 'background-color: red';

                $response->rows[$i]["id"] = $row->id;
                $response->rows[$i]["cell"] = array(
                    '<a href="' . base_url('planes/ver/' . $uuid_aseguradora) . '" style="color:blue;">' . $row->nombre . '</a>',
                    $row->ruc,
                    $row->telefono,
                    $row->email,
                    $row->direccion,
                    $link_option,
                    $hidden_options
                );
                $i++;
            }
        }
        echo json_encode($response);
        exit;
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
            'public/assets/js/default/toast.controller.js',
            'public/assets/js/plugins/bootstrap/select2/select2.min.js',
            'public/assets/js/plugins/bootstrap/select2/es.js'
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
            'public/assets/css/plugins/jquery/toastr.min.css',
            'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
            'public/assets/css/plugins/bootstrap/select2.min.css',
        ));
    }

    function ajax_cambiar_estado_ramo() {
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        $response = array();
        $estado = $this->input->post('estado');
        $id = $this->input->post('id');


        $total = Ramos_orm::cambiar_estado($id, $estado);

        if ($total > 0) {
            $response = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> La actualizaci&oacute;n de estado');
        } else {
            $response = array('estado' => 500, 'mensaje' => '<b>¡Error!</b> Su solicitud no fue Procesada');
        }
        echo json_encode($response);
        exit;
    }

    function ajax_buscar_ramo() {
        if (!$this->input->is_ajax_request()) {
            return false;
        }
        $id = $this->input->post('id');
        $cuenta = Ramos_orm::find($id);
        $response = array();


        $response['id'] = $cuenta->id;
        $response['codigo'] = $cuenta->id;
        $response['nombre'] = $cuenta->nombre;
        $response['descripcion'] = $cuenta->descripcion;
        $response['padre_id'] = $cuenta->padre_id;
        $response['codigo_ramo'] = $cuenta->codigo_ramo;
        $response['interes_asegurado'] = $cuenta->id_tipo_int_asegurado;
        $response['tipo_poliza'] = $cuenta->id_tipo_poliza;

        echo json_encode($response);
        exit;
    }

   public function exportar() {
        /* if(empty($_POST)){
          exit();
          }

          $ids =  $this->input->post('ids', true);
          $id ['ids']= explode(",", $ids);

          if(empty($id)){
          return false;
      } */
      $csv = array();
      $csvdata = array();
      $clause = array(
        "empresa_id" => $this->empresa_id
        );
        // $clause['ramo'] = $id;

      $aseguradoras = RamoRepository::listar_cuentas($clause);

      if (empty($aseguradoras)) {

        return false;
    }
    $i = 0;
    foreach ($aseguradoras AS $row) {
        $csvdata[$i]['nombre'] = $row['nombre'];
        $csvdata[$i]["descripcion"] = $row['descripcion'];
        $csvdata[$i]["codigo_ramo"] = $row['codigo_ramo'];
        
        if(isset($row['id_tipo_int_asegurado'])){
            $interes = CatalogoTipoIntereses::where('id',$row['id_tipo_int_asegurado'])
            ->select('nombre')
            ->first();
            if(count($interes))
                $csvdata[$i]["id_tipo_int_asegurado"] = $interes->nombre;
        }
        if(isset($row['id_tipo_poliza'])){
            $poliza = CatalogoTipoPoliza::where('id',$row['id_tipo_poliza'])
            ->select('nombre')
            ->first();
            if(count($poliza))
                $csvdata[$i]["id_tipo_Poliza"] = $poliza->nombre;

        }
        $csvdata[$i]["estado"] = $row['estado']==1 ? "Habilitado":"Deshabilitado";
        $roles=RamosRoles::join('roles','roles.id','=','seg_ramos_roles.id_rol')
        ->select('roles.*')        
        ->where('id_ramo',$row['id'])->get();
        $rolesString ="";
        if(count($roles))
            $isline = count ($roles);
            foreach ($roles as $key => $user) {
             $char = $key == ($isline-1) ? "":" - ";   
             $rolesString .= $user->nombre.$char;
         }
         $csvdata[$i]['roles'] = $rolesString;
        $usuarios=RamosUsuarios::join('usuarios','usuarios.id','=','seg_ramos_usuarios.id_usuario')
        ->select('usuarios.*')        
        ->where('id_ramo',$row['id'])->get();
        $usuariosString ="";
        if(count($usuarios))
            $isline2 = count($usuarios);
            foreach ($usuarios as $key => $user) {
             $char = $key == ($isline2-1) ? "":" - ";  
             $usuariosString .= $user->nombre." ".$user->apellido.$char;
         }
         $csvdata[$i]['usuarios'] = $usuariosString;

         $i++;
     }
        //we create the CSV into memory
     $csv = Writer::createFromFileObject(new SplTempFileObject());
     $csv->insertOne([
        'Nombre',
       'Descripción',
        'Codigo Ramo',
        'interes',
        'Tipo Poliza',
        'Estado',
        'Roles',
        'Usuarios'
        ]);
     $csv->insertAll($csvdata);
     $csv->output("Ramos-" . date('Y/m/d') . ".csv");
     exit();
 }
  
    function ajax_buscar_ramo_usuario() {
        if (!$this->input->is_ajax_request()) {
            return false;
        }
        if ($this->input->post('idRol')!== null ) {
            $roles = $this->input->post('idRol');
        } else {
            $roles = array();
        }
        if ($this->input->post('idUsuario')!== null ) {
            $id_usuario = $this->input->post('idUsuario');
        } else {
            $id_usuario = array();
        }
        $user = new Usuarios();

        $response = $user->getActiveUsersByRol2($this->empresa_id, $roles, $id_usuario);
        echo json_encode($response);
        exit;
    }

    public function getActiveUsersByRol() {

        if ($this->input->post('idRol')!== null ) {
            $roles = $this->input->post('idRol');
        } else {
            $roles = array();
        }

        $user = new Usuarios();
        if (in_array("todos", $roles)) {
            $roles = Roles::where("empresa_id", "=", $this->empresa_id)
                            ->select("id")
                            ->get()->toArray();
            $roles = array_values($roles);
        }
        $response = $user->getActiveUsersByRol($this->empresa_id, $roles);
        print json_encode($response);
    }

    public function obtener_politicas() {
        echo json_encode($this->politicas);
        exit;
    }

    public function obtener_politicas_general() {
        echo json_encode($this->politicas_general);
        exit;
    }

    public function ocultotabladocumentos() {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/configuracion_seguros/tablasdocumentos.js'
        ));

        $this->load->view('tablasdocumentos');
    }

    public function ajax_listar_documentos($grid = NULL) {


        $clause = array(
            "nombre" => $this->input->post('nombre', true),
            "categoria" => $this->input->post('categoria', true),
            "modulo" => $this->input->post('modulo', true),
            "estado" => $this->input->post('estado', true),
            "id_ramo" => $this->input->post('id_ramo', true) == '' ? 0 : $this->input->post('id_ramo', true),
        );

        //var_dump($clause);

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = RamosDocumentos::listar_documentos_provicional($clause, NULL, NULL, NULL, NULL)->count();  //where(['id_ramo' => 1])->count();//ArticuloModel::listar_articulo_provicional
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $rows = RamosDocumentos::listar_documentos_provicional($clause, $sidx, $sord, $limit, $start);      // where(['id_ramo' => 1])->get();//ArticuloModel::listar_articulo_provicional($clause, $sidx, $sord, $limit, $start);
        //Constructing a JSON
        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
        $i = 0;
        if (!empty($rows->toArray())) {
            foreach ($rows->toArray() AS $i => $row) {

                if ($row['estado'] == 'Inactivo')
                    $spanStyle = 'label label-danger';
                else if ($row['estado'] == 'Activo')
                    $spanStyle = 'label label-successful';
                else
                    $spanStyle = 'label label-warning';

                $hidden_options = "<a class='btn btn-block btn-outline btn-success linkCargaInfo' data-id='" . $row["id"] . "' >Editar </a>";
                $hidden_options .= "<a class='btn btn-block btn-outline btn-success cambiar_estado' data-id='" . $row["id"] . "' >Cambiar estado</a>";
                //$hidden_options .= "<a href='#' class='btn btn-block btn-outline btn-success quitarInteres' data-int-gr='" . $row['id_intereses'] . "'>Quitar Inter&eacute;s</a>";
                //$hidden_options .= '<a href="#" id="cambiarEtapaConfirmBtn" class="btn btn-block btn-outline btn-success">Crear Reporte de Comisión</a>';
                $link_option = '<button class="viewOptions btn btn-success btn-sm opcionesDocumentos" type="button" data-id="' . $row['id'] . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

                $response->rows[$i]["id"] = $row['id'];
                $response->rows[$i]["cell"] = array(
                    '',
                    $row['nombre'],
                    $row['categoria'],
                    $row['modulo'],
                    "<label class='" . $spanStyle . " cambiarestadoseparado' data-id='" . $row['id'] . "'>" . $row['estado'] . "</label>",
                    $link_option,
                    $hidden_options
                );
                $i++;
            }
        }
        print_r(json_encode($response));
        exit;
    }

    public function ajax_buscar_documentos() {

        if (!$this->input->is_ajax_request()) {
            return false;
        }

        $id = $this->input->post('id');

        $documentos = RamosDocumentos::find($id);
        //var_dump($documentos);
        $response = array();

        $response['id'] = $documentos->id;
        $response['nombre'] = $documentos->nombre;
        $response['categoria'] = $documentos->categoria;
        $response['modulo'] = $documentos->modulo;

        echo json_encode($response);
        exit;
    }

    public function ajax_editar_documentos() {
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        $id = $this->input->post('id');

        $documentos['id_ramo'] = $this->input->post('id_ramo');
        $documentos['nombre'] = $this->input->post('nombre');
        $documentos['categoria'] = $this->input->post('categoria');
        $documentos['modulo'] = $this->input->post('modulo');

        $documentos = RamosDocumentos::find($id)->update($documentos);
        $response = new stdClass();
        if (count($documentos) > 0) {
            $response->clase = "success";
            $response->estado = 200;
            $response->mensaje = '<b>¡&Eacute;xito!</b> Se ha gurdado correctamente  ' . $documentos['nombre'];
        } else {
            $response->clase = "danger";
            $response->estado = 100;
            $response->mensaje = '<b>Error</b> al actualizar.';
        }

        echo json_encode($response);
        exit;
    }

    public function ajax_cambiarestado_documentos() {
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        $id = $this->input->post('id');
        $documentos['estado'] = $this->input->post('estado');
        $documentos = RamosDocumentos::find($id)->update($documentos);
        $response = new stdClass();
        if (count($documentos) > 0) {
            $response->clase = "success";
            $response->estado = 200;
            $response->mensaje = '<b>¡&Eacute;xito!</b> Se ha cambiado es estado correctamente  ' . $documentos['nombre'];
        } else {
            $response->clase = "danger";
            $response->estado = 100;
            $response->mensaje = '<b>Error</b> al cambiar estado.';
        }
        echo json_encode($response);
        exit;
    }
	
	function ajax_listar_distritos()
	{
		if (!$this->input->is_ajax_request()) {
            return false;
        }

        $id = $this->input->post('provincia_id');
		
		$distritos=$this->distritos->where('provincia_id',$id)->get(array('id','nombre'));

		$response = new stdClass();
		/*foreach ($distritos as $key => $value) {
			$response->distritos = array(
				"id" => $value->id,
				"nombre" => $value->nombre
				);
		}*/
		$response->distritos = $distritos;
		$this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
		->set_output(json_encode($response))->_display();
		exit;
	}
	
	function ajax_listar_corregimientos()
	{
		if (!$this->input->is_ajax_request()) {
            return false;
        }

        $id = $this->input->post('distrito_id');
		
		$corregimientos=$this->corregimientos->where('distrito_id',$id)->get(array('id','nombre'));

		$response = new stdClass();
		/*foreach ($distritos as $key => $value) {
			$response->distritos = array(
				"id" => $value->id,
				"nombre" => $value->nombre
				);
		}*/
		$response->corregimientos = $corregimientos;
		$this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
		->set_output(json_encode($response))->_display();
		exit;
	}
	
	function ajax_guardar_rutas()
	{
       /* if (!$this->input->is_ajax_request()) {
            return false;
        }*/
        
        $response = new stdClass();
        $id_ruta = $this->input->post('id_ruta');

		 if (empty($id_ruta)) {
			$datos = array();
			$datos['uiid_ruta']=Capsule::raw("ORDER_UUID(uuid())");
			$datos['nombre_ruta'] = $this->input->post('nombre1_ruta');
			$datos['provincia_id'] = $this->input->post('provincia_ruta');
			$datos['distrito_id'] = $this->input->post('distrito_ruta');
			$datos['corregimiento_id'] = $this->input->post('corregimiento_ruta');
			$datos['nombre_mensajero'] = $this->input->post('nombremensajero_ruta');
			$datos['empresa_id'] = $this->empresa_id;
			$datos['usuario_id'] = $this->usuario_id;
			$datos['estado']='Activo';
			$datos['created_at']=date('Y-m-d H:i:s');
			$datos['updated_at']= date('Y-m-d H:i:s');
			//var_dump($this->input->post());
			$ruta_save = $this->rutas->create($datos);

			$response->clase = "success";
			$response->estado = 200;
			$response->mensaje = '<b>¡&Eacute;xito!</b> Se ha guardado correctamente  ' . $ruta_save->nombre;
		}
		else{
			$datos = array();
			$datos['nombre_ruta'] = $this->input->post('nombre1_ruta');
			$datos['provincia_id'] = $this->input->post('provincia_ruta');
			$datos['distrito_id'] = $this->input->post('distrito_ruta');
			$datos['corregimiento_id'] = $this->input->post('corregimiento_ruta');
			$datos['nombre_mensajero'] = $this->input->post('nombremensajero_ruta');
			$datos['empresa_id'] = $this->empresa_id;
			$datos['usuario_id'] = $this->usuario_id;
			$datos['updated_at']= date('Y-m-d H:i:s');
			$ruta_save = $this->rutas->find($id_ruta)->update($datos);

			$response->clase = "success";
			$response->estado = 200;
			$response->mensaje = '<b>¡&Eacute;xito!</b> Se ha actualizado correctamente  ' .$datos['nombre_ruta'];
		}
		echo json_encode($response);
		exit;
	}
	
	function ajax_cambiar_estados_rutas()
	{
		if (!$this->input->is_ajax_request()) {
            return false;
        }
        $response = array();
        $estado = $this->input->post('estado');
        $id = $this->input->post('id');
		$campos['estado']=$estado;

        $total = $this->rutas->find($id)->update($campos);

        if ($total > 0) {
            $response = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> La actualizaci&oacute;n de estado');
        } else {
            $response = array('estado' => 500, 'mensaje' => '<b>¡Error!</b> Su solicitud no fue Procesada');
        }
        echo json_encode($response);
        exit;
	}
	
	public function exportar_rutas() {
    	if(empty($_POST)){
    		exit();
    	}
		
    	$ids =  $this->input->post('id_rutas', true);
		$id = explode(",", $ids);

		if(empty($id)){
			return false;
		}
		$csv = array();

        $clause['id'] = $id;
                
		$rutas = $this->rutasRepository->exportar($clause);
		if(empty($rutas)){
			return false;
		}
		
		$i=0;
		foreach ($rutas AS $row)
		{
			$csvdata[$i]['nombre'] = utf8_decode($row->nombre_ruta);
			$csvdata[$i]["provincia"] = utf8_decode($row->datosProvincia->nombre);
			$csvdata[$i]["distrito"] = utf8_decode($row->datosDistrito->nombre);
			$csvdata[$i]["corregimiento"] = utf8_decode($row->datosCorregimiento->nombre);
			$csvdata[$i]["mensajero"] = utf8_decode($row->nombre_mensajero);
			$csvdata[$i]["estado"] = $row->estado;
			$i++;
		}
		//we create the CSV into memory
		$csv = Writer::createFromFileObject(new SplTempFileObject());
		$csv->insertOne([
			'Nombre de ruta',
			'Provincia',
			'Distrito',
			'Corregimiento',
			'Mensajero',
			'Estado'
		]);
		$csv->insertAll($csvdata);
		$csv->output("rutas-". date('ymd') .".csv");
		exit();
    }
}

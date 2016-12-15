<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
use Carbon\Carbon;
use Flexio\Modulo\ConfiguracionSeguro\Repository\ConfiguracionSeguroRepository as ConfiguracionSeguroRepository;
use Flexio\Modulo\Configuracionseguro\Models\ConfiguracionSeguro as ConfiguracionSeguro;
use Flexio\Modulo\TipoIntereses\Models\TipoIntereses as TipoIntereses;
use Flexio\Modulo\TipoPoliza\Models\TipoPoliza as TipoPoliza;
use Flexio\Modulo\Empresa\Models\Empresa as Empresa;

class Configuracion_seguros extends CRM_Controller
{
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
    
    protected $SegurosAseguradorasRepository;    

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
        $this->load->model('aseguradoras/aseguradoras_orm');
        $this->load->model('Comisiones_orm');
        $this->load->model('Contabilidad/tipo_cuentas_orm');
        $this->load->model('Contabilidad/Cuentas_orm');
        $this->load->model('aseguradoras/Ramos_orm');
        $this->load->model('contactos/Contacto_orm');
        $this->load->model('aseguradoras/Planes_orm');
        $this->load->model('aseguradoras/Coberturas_orm');
        $this->load->model('usuarios/usuario_orm');
        $this->load->model('usuarios/organizacion_orm');
        $this->load->model('usuarios/organizacion_orm');
        $this->load->model('aseguradoras/Catalogo_tipo_poliza_orm');
        $this->load->model('aseguradoras/Catalogo_tipo_intereses_orm');
        $this->load->model('contabilidad/Impuestos_orm');

        //Obtener el id_empresa de session
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $this->empresa_id = $empresa->id;
        $this->ConfiguracionSeguroRepository = new ConfiguracionSeguroRepository();
    }                                                  
    public function listar() {

        $data = array();
        
        $this->_Css();   
        $this->_js();
        
        $this->assets->agregar_js(array(
            'public/assets/js/modules/configuracion_seguros/listar.js'
            ));
        
        
        //defino mi mensaje
        if(!is_null($this->session->flashdata('mensaje'))){
            $mensaje = json_encode($this->session->flashdata('mensaje'));
        }else{
            $mensaje = '';
        }
        $this->assets->agregar_var_js(array(
            "toast_mensaje" => $mensaje
            ));
        
        //Verificar permisos para crear
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-cog"></i> Configuración',
            "filtro" => true,
            "menu" => array(
                "nombre" => "Crear",
                "url" => "configuracion_seguros/listar",
                ),
            "ruta" => array(
                0 => array("nombre" => "Seguros", "url" => "#",  "activo" => false),               
                1 => array("nombre" => "<b>Configuraci&oacute;n</b>", "activo" => true)
                ),
            );        

        $menuOpciones["#exportarAseguradoraLnk"] = "Exportar";
        $breadcrumb["menu"]["opciones"] = $menuOpciones;
        $breadcrumb["botones"]["Aseguradoras"] = '<i class="fa fa-tasks"></i> Pipeline';
        $breadcrumb["botones"]["Aseguradoras"] = '<i class="fa fa-star"></i> Score';
        
        if ($this->auth->has_permission('acceso', 'aseguradoras/crear')){
            $breadcrumb["menu"] = array(
                "url"   => 'aseguradoras/crear',
                "nombre" => "Crear"
                );
            $menuOpciones["#cambiarEstadoLnk"] = "Cambiar Estado";
            $breadcrumb["menu"]["opciones"] = $menuOpciones;
        }
        
        //Menu para crear
        $clause = array('empresa_id' => $this->empresa_id);
        
        //$data['menu_crear'] = array('nombre'=>1); 
        /*//catalogo para buscador        
        $data['aseguradoras'] = Aseguradoras_orm::where($clause)->get();
        $data['tipo'] = Catalogo_tipo_poliza_orm::get();
        $data['usuarios'] = usuario_orm::where('estado', 'Activo')->get();
        /*$clause2['empresa_id'] = $this->empresa_id;*/
        $intereses = new TipoIntereses();
        $poliza = new TipoPoliza();
        $empresa = new Empresa();
        $data['tipo_intereses']= $intereses::all();
        $data['tipo_poliza'] = $poliza::all();
        $uuid_empresa = $this->session->userdata('id_empresa');
        $data1 = $empresa::where('id', $this->empresa_id)->get();
        $data['test'] = $data1;
        $this->template->agregar_titulo_header('Listado de Aseguradoras');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);

        
        
    }

    public function crear() { 
        $acceso = 1;
        $mensaje = array();

        if(!$this->auth->has_permission('acceso')){
      // No, tiene permiso, redireccionarlo.
          $acceso = 0;
          $mensaje = array('estado'=>500, 'mensaje'=>' <b>Usted no cuenta con permiso para esta solicitud</b>','clase'=>'alert-danger');
      }

      $this->_Css();   
      $this->_js();
      $this->assets->agregar_js(array(       
         'public/assets/js/modules/seguros_aseguradoras/formulario.js',   
         'public/assets/js/modules/seguros_aseguradoras/crear.vue.js',
         'public/assets/js/modules/seguros_aseguradoras/component.vue.js',  
         'public/assets/js/modules/seguros_aseguradoras/plugins.js'   
         ));

      $data=array();      
      $this->assets->agregar_var_js(array(
        "vista" => 'crear',
        "acceso" => $acceso,
        ));
      

      $breadcrumb = array(
        "titulo" => '<i class="fa fa-book"></i> Aseguradoras',
        "filtro" => true,
        "menu" => array(
            "nombre" => "Crear",
            "url" => "aseguradoras/crear",
            ),
        "ruta" => array(
            0 => array("nombre" => "Seguros", "url" => "#",  "activo" => false),               
            1 => array("nombre" => "<b>Configuraci&oacute;n</b>", "activo" => true)
            ),
        );        

      $menuOpciones["#exportarAseguradoraLnk"] = "Exportar";
      $breadcrumb["menu"]["opciones"] = $menuOpciones;
      $breadcrumb["botones"]["Aseguradoras"] = '<i class="fa fa-tasks"></i> Pipeline';
      $breadcrumb["botones"]["Aseguradoras"] = '<i class="fa fa-star"></i> Score';

      $data['mensaje'] = $mensaje;
      $this->template->agregar_titulo_header('Aseguradoras: Crear');
      $this->template->agregar_breadcrumb($breadcrumb);
      $this->template->agregar_contenido($data);
      $this->template->visualizar();    

  }

  function ocultoformulario() {
    $clause = array('empresa_id' => $this->empresa_id);        
    $this->assets->agregar_var_js(array(
        ));

    $this->load->view('formulario');
}

function guardar() {
    if($_POST){
        unset($_POST["campo"]["guardar"]);
        $campo = Util::set_fieldset("campo");    
        Capsule::beginTransaction();
        try {
            if(empty($campo['uuid'])){ 
                $campo["uuid_aseguradoras"] = Capsule::raw("ORDER_UUID(uuid())");
                $clause['empresa_id'] = $this->empresa_id;
                $total = $this->solicitudesRepository->listar($clause);
                $year = Carbon::now()->format('y');
                $codigo = Util::generar_codigo($_POST['codigo_ramo'] . "-" . $year , count($total) + 1);
                $campo["numero"] = $codigo;
                $campo["usuario_id"] = $this->session->userdata['id_usuario'];
                $campo["empresa_id"] = $this->empresa_id;    
                $date = Carbon::now();
                $date = $date->format('Y-m-d');
                $campo['fecha_creacion'] = $date;   
                $solicitudes = $this->solicitudesModel->create($campo); 
            }else{
                echo "hola mundo";
            }
            Capsule::commit();
        }catch(ValidationException $e){
            log_message('error', $e);
            Capsule::rollback();
        }
        if(!is_null($solicitudes)){    
            $mensaje = array('estado' => 200, 'mensaje' =>'<b>¡&Eacute;xito!</b> Se ha guardado correctamente'); 

        }else{
            $mensaje = array('class' =>'alert-danger', 'contenido' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
        }


    }else{
        $mensaje = array('class' =>'alert-warning', 'contenido' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
    }
    
    $this->session->set_flashdata('mensaje', $mensaje);
    redirect(base_url('solicitudes/listar'));
}

public function ocultotabla() {
    $this->assets->agregar_js(array(
        'public/assets/js/modules/configuracion_seguros/tabla_ramos.js'
        ));

    $this->load->view('tabla');
}

function ajax_listar() {


        if (!$this->input->is_ajax_request()) {
            return false;
        }

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $tipo = $this->input->post('tipo');
        $nombre = (string)$this->input->post('nombre');
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        //fix count
        $count = Ramos_orm::where('empresa_id', $empresa->id)->count();

        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

        $clause = array('empresa_id' => $empresa->id);
        if (!empty($tipo)) $clause['tipo_cuenta_id'] = $tipo;
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
                    'nombre' => "<span style='".$spanStyle."'>".$row['nombre']."</span>",
                    'descripcion' => "<span style='".$spanStyle."'>".$row['descripcion']."</span>",
                    'codigo' => $row['codigo_ramo'],
                    'tipo_interes' => $row['interes_asegurado']["nombre"],
                    'tipo_poliza' => $row['tipo_poliza']["nombre"],
                    'estado' => "<span style='".$spanStyle."'>".(($row['estado'] == 1) ? 'Habilitado' : 'Deshabilitado')."</span>",
                    'opciones' => $link_option,
                    'link' => $hidden_options,
                    "level" => isset($row["level"]) ? $row["level"] : "0", //level
                    'parent' => $row["padre_id"] == 0 ? "NULL" : (string)$row["padre_id"], //parent
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


public function ajax_listar_ramos_tree() {
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $tipo = $this->input->post('tipo');
        $clause = array('empresa_id' => $empresa->id);
        if (!empty($tipo)) $clause['tipo_cuenta_id'] = $tipo;
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
                    'id' => (string)$row['id'],
                    'parent' => $row["padre_id"] == 0 ? "#" : (string)$row["padre_id"],
                    'text' => "<span id='labelramo' style='".$spanStyle."'>".$row["nombre"]."</span>",
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
        'public/assets/js/modules/ramos/configuracion.js',
        'public/assets/js/modules/configuracion_seguros/routes.js',
        'public/assets/js/plugins/bootstrap/select2/select2.min.js',
        'public/assets/js/plugins/bootstrap/select2/es.js',
        'public/assets/js/plugins/jquery/jstree.min.js'

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
        'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
        'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
        'public/assets/css/plugins/jquery/chosen/chosen.min.css',
        'public/assets/js/plugins/jquery/sweetalert/sweetalert.css',
        'public/assets/css/modules/stylesheets/remesas.css',
        'public/assets/css/plugins/jquery/jstree/default/style.min.css'
        ));
}

public function ajax_listar_ramos() {
        //Just Allow ajax request

        if (!$this->input->is_ajax_request()) {
            return false;
        }

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $tipo = $this->input->post('tipo');
        $nombre = (string)$this->input->post('nombre');
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        //fix count
        $count = Ramos_orm::where('empresa_id', $empresa->id)->count();

        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

        $clause = array('empresa_id' => $empresa->id);
        if (!empty($tipo)) $clause['tipo_cuenta_id'] = $tipo;
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
                    'nombre' => "<span style='".$spanStyle."'>".$row['nombre']."</span>",
                    'descripcion' => "<span style='".$spanStyle."'>".$row['descripcion']."</span>",
                    'codigo' => $row['codigo_ramo'],
                    'tipo_interes' => $row['interes_asegurado']["nombre"],
                    'tipo_poliza' => $row['tipo_poliza']["nombre"],
                    'estado' => "<span style='".$spanStyle."'>".(($row['estado'] == 1) ? 'Habilitado' : 'Deshabilitado')."</span>",
                    'opciones' => $link_option,
                    'link' => $hidden_options,
                    "level" => isset($row["level"]) ? $row["level"] : "0", //level
                    'parent' => $row["padre_id"] == 0 ? "NULL" : (string)$row["padre_id"], //parent
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
            if($existe && $codigo_ramo != ''){
                $response->clase = "danger";
                $response->estado = 200;
                $response->mensaje = '<b>Error</b> Codigo ya existe.';
                echo json_encode($response);
                exit;
            }else{
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
            
            if($impuesto_save->codigo_ramo != strtoupper($codigo_ramo)){
                $clause = array(
                    "codigo_ramo" => strtoupper($codigo_ramo),
                    "empresa_id" => $empresa->id
                );
                $existe = Ramos_orm::findCodigo($clause);
                if($existe){
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

}
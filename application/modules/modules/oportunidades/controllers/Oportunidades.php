<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Erp
 * @subpackage Controller
 * @category   Contratos de alquiler
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  10/22/2015
 */

use Carbon\Carbon;
use League\Csv\Writer;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Oportunidades\Repository\OportunidadesRepository;
use Flexio\Modulo\Oportunidades\Repository\OportunidadesCatalogosRepository;
use Flexio\Modulo\Cliente\Repository\ClienteRepository;
use Flexio\Modulo\Usuarios\Repository\UsuariosRepository;
use Flexio\Modulo\ClientesPotenciales\Repository\ClientesPotencialesRepository;
use Flexio\Modulo\Cotizaciones\Repository\CotizacionRepository;
use Flexio\Library\Util\AuthUser;

class Oportunidades extends CRM_Controller
{
    /**
     * Atributos
     */
    private $usuario_id;
    private $empresa_id;
    private $empresaObj;
    protected $OportunidadesRepository;
    protected $OportunidadesCatalogosRepository;
    protected $ClienteRepository;
    protected $UsuariosRepository;
    protected $ClientesPotencialesRepositoy;
    protected $CotizacionRepository;

    /**
     * Método constructor
     */
    public function __construct()
    {
        parent::__construct();
        //cargar los modelos
        $this->load->model('usuarios/Empresa_orm');
        $this->load->model('usuarios/Usuario_orm');

        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $uuid_usuario = $this->session->userdata('huuid_usuario');

        $empresaObj = new Buscar(new Empresa_orm,'uuid_empresa');
        $usuario = Usuario_orm::findByUuid($uuid_usuario);

        $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
        $this->empresa_id = $this->empresaObj->id;


        $this->usuario_id = $usuario->id;

        $this->OportunidadesRepository = new OportunidadesRepository();
        $this->OportunidadesCatalogosRepository = new OportunidadesCatalogosRepository();
        $this->ClienteRepository = new ClienteRepository();
        $this->UsuariosRepository = new UsuariosRepository();
        $this->ClientesPotencialesRepositoy = new ClientesPotencialesRepository();
        $this->CotizacionRepository = new CotizacionRepository();
    }

    public function listar()
    {
        $data = array();
        $mensaje ='';
        if(!$this->auth->has_permission('acceso', 'oportunidades/listar'))
        {
            redirect ( '/' );
        }
        if(!empty($this->session->flashdata('mensaje')))
        {
            $mensaje = json_encode($this->session->flashdata('mensaje'));
        }

        $this->_css();$this->_js();

        $breadcrumb = array( "titulo" => '<i class="fa fa-line-chart"></i> Oportunidades',
            "ruta" => array(
                0 => ["nombre" => "Ventas", "activo" => false],
                1 => ["nombre" => '<b>Oportunidades</b>', "activo" => true]
            ),
            "menu" => ["nombre" => "Crear", "url" => "oportunidades/crear","opciones" => array()]
        );
        $breadcrumb["menu"]["opciones"]["#exportarOportunidades"] = "Exportar";
        $breadcrumb["menu"]["opciones"]["#cambiarEstadoGrupal"] = ' Cambiar Estado';

        $cotizaciones = $this->CotizacionRepository->getCotizacionAbierta(['empresa_id'=>$this->empresa_id]);
        $cotizaciones->load('cliente');
        $this->assets->agregar_var_js(array(
            "toast_mensaje" => $mensaje,
            "cotizaciones" => $cotizaciones
        ));
        $clause = ['empresa_id' => $this->empresa_id];
        $usuarios = $this->UsuariosRepository->get($clause);

        if(!AuthUser::is_owner() ){
        $usuarios = $usuarios->filter(function($v) {
            return $v->id == $this->usuario_id;
        });
        }


        $data['usuarios'] = $usuarios;
        $data['estados'] = $this->OportunidadesCatalogosRepository->get(['tipo'=>'estado']);

        $this->template->agregar_titulo_header('Listado de Oportunidades');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }


    public function ocultotabla($campo_array = [])
    {
        if(is_array($campo_array))
        {
            $this->assets->agregar_var_js([
                "campo" => collect($campo_array)
            ]);
        }

        $this->assets->agregar_js([
            'public/assets/js/modules/oportunidades/tabla.js'
        ]);

        $this->load->view('tabla');

    }



    /**
     * Método listar los registros de los subcontratos en ocultotabla()
     */
    public function ajax_listar()
    {
        if(!$this->input->is_ajax_request()){return false;}
        $cliente_id             = $this->input->post('cliente_id');
        $clause                 = $this->input->post();
        $clause['empresa_id']   = $this->empresa_id;
        /**
         * Si el usuario no es de tipo dueño o si el rol del usuario actual no tiene el permiso listar todos la lista
         * mostrara solo las oportunidades asignadas a el usuario actual.
         */
        if( !AuthUser::is_owner() && !$this->auth->has_permission('listar_todos', 'oportunidades/listar') )$clause['asignado_a_id'] = $this->usuario_id;

        if ($cliente_id <> ''){
            $clause['cliente_id'] = $this->ClienteRepository->findByUuid($cliente_id)->id;
        }
        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = $this->OportunidadesRepository->count($clause);

        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $oportunidades = $this->OportunidadesRepository->get($clause ,$sidx, $sord, $limit, $start);

        $response          = new stdClass();
        $response->page    = $page;
        $response->total   = $total_pages;
        $response->records = $count;

        if($count > 0){
            foreach ($oportunidades as $i => $oportunidad)
            {
                $response->rows[$i]["id"]   = $oportunidad->uuid_oportunidad;
                $response->rows[$i]["cell"] = $this->OportunidadesRepository->getCollectionCell($oportunidad, $this->auth);
            }
        }
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))->_display();
        exit;
    }





    /**
     * Método para generar código del subcontrato
     */
    private function _generar_codigo()
    {
        $clause_empresa = ['empresa_id' => $this->empresa_id];
        $total = $this->OportunidadesRepository->count($clause_empresa);
        $year = Carbon::now()->format('y');
        $codigo = Util::generar_codigo('OPV'.$year,$total + 1);
        return $codigo;
    }


    private function _css()
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
            'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
            'public/assets/css/plugins/bootstrap/select2.min.css',
            //'public/assets/css/modules/stylesheets/oportunidades.css',
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
            'public/assets/js/default/lodash.min.js',
            'public/assets/js/default/accounting.min.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/plugins/jquery/sweetalert/sweetalert.min.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/plugins/bootstrap/select2/select2.min.js',
            'public/assets/js/plugins/bootstrap/select2/es.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
            'public/assets/js/default/toast.controller.js',
            'public/assets/js/default/vue/directives/datepicker2.js',
            'public/assets/js/default/vue/directives/inputmask.js',
            'public/assets/js/default/vue/directives/select2.js',
            'public/assets/js/modules/oportunidades/plugins.js',
        ));
    }

    public function ajax_exportar()
    {
        $post = $this->input->post();
    	if(empty($post)){exit();}

    	$oportunidades = $this->OportunidadesRepository->get(['campo' => ['uuid_oportunidad' => $post['ids']]]);

        $csv = Writer::createFromFileObject(new SplTempFileObject());
        $csv->insertOne(['No. Oportunidad','Nombre de la oportunidad','Monto',utf8_decode('Fecha de creación'),'Asignado a','Etapa']);
        $csv->insertAll($this->OportunidadesRepository->getCollectionExportar($oportunidades));
        $csv->output("Oportunidad-". date('ymd') .".csv");
        exit();
    }

    public function ajax_change_status()
    {
        $post = $this->input->post();
        if (empty($post) || !isset($post['ids'])) {
            echo json_encode(['status' => "fail", 'message' => "No ha seleccionado nunguna oportunidad"]);
            return;
        }
        Capsule::beginTransaction();
        try {
            $oportunidades = $this->OportunidadesRepository->get(['campo' => ['uuid_oportunidad' => $post['ids']]]);
            foreach ($oportunidades as $oportunidad) {
                $oportunidad->update(['etapa_id' => $this->input->post('status', TRUE)]);
            }
            Capsule::commit();
            echo json_encode(['status' => 200, 'message' => "Cambios realizados"]);
        } catch (Illuminate\Database\QueryException $e) {
            log_message('error', __METHOD__ . " ->" . ", Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
            Capsule::rollback();
            echo json_encode(['status' => $e->getCode(), 'message' => "No ha seleccionado nunguna oportunidad", 'cause' => $e->getMessage()]);

        }


    }


    public function crear(){

        $acceso = 1;
        $mensaje = array();
        if (!$this->auth->has_permission('acceso', 'oportunidades/lista')) {
            $acceso = 0;
            $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud', 'clase' => 'alert-danger');
        }

        $clause = array(
            "empresa_id" => $this->empresa_id
        );

       	$this->_css();
        $this->_js();


        $this->assets->agregar_var_js(array(
            "vista"                 => 'crear',
            "acceso"                => $acceso == 0 ? $acceso : $acceso
        ));

        $data['mensaje'] = $mensaje;
      /*  $breadcrumb = array(
            "titulo" => '<i class="fa fa-line-chart"></i> Oportunidades: Crear ',
        );*/

        $breadcrumb = array(
          "titulo" => '<i class="fa fa-line-chart"></i> Oportunidades: Crear ',
        "ruta" => array(
          0 => array(
            "nombre" => "Ventas",
            "activo" => false,
          ),
          1 => array(
            "nombre" => "Oportunidades",
            "activo" => false,
            "url" => 'oportunidades/listar'
          ),
          2=> array(
            "nombre" => '<b>Crear</b>',
            "activo" => true
          )
        ),
        );

        $this->template->agregar_titulo_header('Oportunidades: Crear ');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();

    }

    public function editar($uuid){

        $acceso = 1;
        $mensaje = array();
        if (!$this->auth->has_permission('acceso', 'oportunidades/editar/(:any)')) {
            $acceso = 0;
            $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud', 'clase' => 'alert-danger');
        }

        $this->_css();
        $this->_js();


        $oportunidad = $this->OportunidadesRepository->findBy(['uuid_oportunidad'=>$uuid]);
        $this->assets->agregar_var_js(array(
            "vista" => 'editar',
            "acceso" => $acceso == 0 ? $acceso : $acceso,
            "oportunidad" => $this->OportunidadesRepository->getCollectionCampo($oportunidad)
        ));

        $data['mensaje'] = $mensaje;
        $data['oportunidad'] = $oportunidad;

        $breadcrumb = array(
        "titulo" => '<i class="fa fa-line-chart"></i> Oportunidades: '.$oportunidad->codigo,
        "menu" => ["nombre" => "Acci&oacute;n", "url" => "#","opciones" => array()],
        "ruta" => array(
          0 => array(
            "nombre" => "Ventas",
            "activo" => false,
          ),
          1 => array(
            "nombre" => "Oportunidades",
            "activo" => false,
            "url" => 'oportunidades/listar'
          ),
          2=> array(
            "nombre" => '<b>Detalle</b>',
            "activo" => true
          )
        ),
        );
        $breadcrumb["menu"]["opciones"]["cotizaciones/crear/oportunidad".$oportunidad->id] = "Nueva cotizaci&oacute;n";

        $this->template->agregar_titulo_header('Oportunidades: '.$oportunidad->codigo);
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();

    }

    public function ocultoformulario($oportunidad = array()) {
        $data = array();
        $clause = array('empresa_id' => $this->empresa_id, 'vendedor' => $this->empresa_id);

        $this->assets->agregar_js(array(
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/resources/compile/modulos/oportunidades/formulario.js'
        ));

        if (isset($oportunidad['info'])){$data['info'] = $oportunidad['info'];}
        $clientes = $this->ClienteRepository->getClientesEstadoActivo($clause)->get();

        $aa = $this->UsuariosRepository->get($clause);

        $this->assets->agregar_var_js(array(
            "clientes" => $clientes,
            'clientes_potenciales' => $this->ClientesPotencialesRepositoy->get($clause),
            "vendedores" => $this->UsuariosRepository->get($clause),
            "estados" => $this->OportunidadesCatalogosRepository->get(['tipo'=>'estado']),
            "codigo" => $this->_generar_codigo(),
            "usuario_id" => $this->usuario_id
        ));

        $this->load->view('formulario', $data);
    }



    public function ajax_asociar_cotizacion() {

        if (!$this->input->is_ajax_request()) {
            return false;
        }

        $relacionable_id = $this->input->post('cotizacion_id');
        $relacionable_type = 'Flexio\\Modulo\\Cotizaciones\\Models\\Cotizacion';
        $oportunidad_id = $this->input->post('oportunidad_id');
        $oportunidad = $this->OportunidadesRepository->findBy(['oportunidad_id'=>$oportunidad_id]);
        $success = $this->OportunidadesRepository->asociarCotizacion($oportunidad, ['relacionable_id'=>$relacionable_id,'relacionable_type'=>$relacionable_type]);

        $response = [
            'estado' => ($success) ? 200 : 500,
            'mensaje' => ($success) ? '<b>¡&Eacute;xito!</b> Se ha guardado correctamente la cotizaci&oacute;n a la oportunidad' : '<b>¡Error!</b> No se ha guardado correctamente la cotizaci&oacute;n a la oportunidad'
        ];

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($response))->_display();
        exit;
    }

    public function guardar()
    {

        $post = $this->input->post();

        if (!empty($post)) {

            Capsule::beginTransaction();
            try {
                $campo = $post['campo'];
                if(empty($campo['id']))
                {
                    $post['campo']['codigo']        = $this->_generar_codigo();
                    $post['campo']['empresa_id']    = $this->empresa_id;
                    $oportunidad = $this->OportunidadesRepository->create($post);
                } else {
                    $oportunidad = $this->OportunidadesRepository->save($post);
                }

            } catch (Illuminate\Database\QueryException $e) {
                log_message('error', __METHOD__ . " ->" . ", Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
                Capsule::rollback();
                $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error! Su solicitud no fue procesada</b> ');
                $this->session->set_flashdata('mensaje', $mensaje);
                redirect(base_url('oportunidades/listar'));
                //echo $e->getMessage();
            }
            Capsule::commit();

            if (!is_null($oportunidad)) {
                $mensaje = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente ' . $oportunidad->codigo);
            } else {
                $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error! Su solicitud no fue procesada</b> ');
            }
            $this->session->set_flashdata('mensaje', $mensaje);
            if($post['campo']['crear_cotizacion'] == '1'){redirect(base_url('cotizaciones/crear/oportunidad'.$oportunidad->id));}
            redirect(base_url('oportunidades/listar'));
        }


    }
}

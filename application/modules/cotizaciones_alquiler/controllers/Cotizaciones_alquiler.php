<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Erp
 * @subpackage Controller
 * @category   Cotizaciones de alquiler
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  10/22/2015
 */

use Carbon\Carbon;
use League\Csv\Writer;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\CotizacionesAlquiler\Repository\CotizacionesAlquilerRepository;
use Flexio\Modulo\Cotizaciones\Repository\CotizacionCatalogoRepository as CotizacionesAlquilerCatalogosRepository;
use Flexio\Modulo\Cliente\Repository\ClienteRepository;
use Flexio\Modulo\ClientesPotenciales\Repository\ClientesPotencialesRepository;
use Flexio\Modulo\CentrosContables\Repository\CentrosContablesRepository;
use Flexio\Modulo\Usuarios\Repository\UsuariosRepository;
use Flexio\Modulo\Inventarios\Repository\CategoriasRepository as ItemsCategoriasRepository;
use Flexio\Modulo\ContratosAlquiler\Repository\ContratosAlquilerCatalogosRepository;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\Inventarios\Repository\PreciosRepository;

class Cotizaciones_alquiler extends CRM_Controller
{
    /**
     * Atributos
     */
    private $empresa_id;
    private $empresaObj;
    protected $CotizacionesAlquilerRepository;
    protected $CotizacionesAlquilerCatalogosRepository;
    protected $ClienteRepository;
    protected $ClientesPotencialesRepository;
    protected $CentrosContablesRepository;
//    protected $OrdenesCompraRepository;
    protected $UsuariosRepository;
    protected $ItemsCategoriasRepository;
    protected $ContratosAlquilerCatalogosRepository;
    protected $PreciosRepository;

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
        $empresaObj = new Buscar(new Empresa_orm,'uuid_empresa');
        $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
        $this->empresa_id = $this->empresaObj->id;

        $this->CotizacionesAlquilerRepository = new CotizacionesAlquilerRepository();
        $this->CotizacionesAlquilerCatalogosRepository = new CotizacionesAlquilerCatalogosRepository();
        $this->ClienteRepository = new ClienteRepository();
        $this->ClientesPotencialesRepository = new ClientesPotencialesRepository();
        $this->CentrosContablesRepository = new CentrosContablesRepository();
//        $this->OrdenesCompraRepository              = new OrdenesCompraRepository();
        $this->UsuariosRepository = new UsuariosRepository();
        $this->ItemsCategoriasRepository = new ItemsCategoriasRepository();
        $this->ContratosAlquilerCatalogosRepository = new ContratosAlquilerCatalogosRepository();
        $this->PreciosRepository = new PreciosRepository;
    }

    public function listar()
    {
        $data = array();
        $mensaje ='';
        if(!$this->auth->has_permission('acceso'))
        {
            redirect ( '/' );
        }
        if(!empty($this->session->flashdata('mensaje')))
        {
            $mensaje = json_encode($this->session->flashdata('mensaje'));
        }

        $this->_css();$this->_js();

        $breadcrumb = array( "titulo" => '<i class="fa fa-line-chart"></i> Cotizaciones ',
            "ruta" => array(
                0 => ["nombre" => "Alquileres", "activo" => false],
                1 => ["nombre" => '<b>Cotizaciones</b>', "activo" => true]
            ),
            "menu" => ["nombre" => "Crear", "url" => "cotizaciones_alquiler/crear","opciones" => array()]
        );
        $breadcrumb["menu"]["opciones"]["#exportarCotizacionesAlquiler"] = "Exportar";

        $this->assets->agregar_var_js(array(
            "toast_mensaje" => $mensaje
        ));

        $clause = ['empresa_id' => $this->empresa_id];
        $data['clientes']   = $this->ClienteRepository->get($clause);
        $data['estados']    = $this->CotizacionesAlquilerCatalogosRepository->getEtapas();
        $this->template->agregar_titulo_header('Cotizaciones ');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }






    /**
     * Método listar los registros de los subcotizaciones en ocultotabla()
     */
//    public function ajax_listar()
//    {
//        if(!$this->input->is_ajax_request()){return false;}
//
//        $clause                 = $this->input->post();
//        $clause['empresa_id']   = $this->empresa_id;
//
//        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
//        $count = $this->CotizacionesAlquilerRepository->count($clause);
//
//        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
//        $cotizaciones_alquiler = $this->CotizacionesAlquilerRepository->get($clause ,$sidx, $sord, $limit, $start);
//
//        $response          = new stdClass();
//        $response->page    = $page;
//        $response->total   = $total_pages;
//        $response->records = $count;
//
//        if($count > 0){
//            foreach ($cotizaciones_alquiler as $i => $cotizacion_alquiler)
//            {
//                $response->rows[$i]["id"]   = $cotizacion_alquiler->uuid_cotizacion_alquiler;
//                $response->rows[$i]["cell"] = $this->CotizacionesAlquilerRepository->getCollectionCell($cotizacion_alquiler, $this->auth);
//            }
//        }
//        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))->_display();
//        exit;
//    }



    /**
     * Método para generar código del subcotizacion
     */
    private function _generar_codigo()
    {
        $clause_empresa = ['empresa_id' => $this->empresa_id];
        $total = $this->CotizacionesAlquilerRepository->count($clause_empresa);
        $year = Carbon::now()->format('y');
        $codigo = Util::generar_codigo('QTA'.$year,$total + 1);
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
            'public/assets/css/plugins/jquery/jquery.webui-popover.css',
            'public/assets/css/default/ladda.min.css',
            'public/assets/css/modules/stylesheets/cotizaciones_alquiler.css',
        ));
    }


    private function _js()
    {
        $this->assets->agregar_js(array(

            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/plugins/bootstrap/select2/select2.min.js',
            'public/assets/js/plugins/bootstrap/select2/es.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
            'public/assets/js/default/toast.controller.js',
            'public/assets/js/default/vue/directives/datepicker2.js',
            'public/assets/js/default/vue/directives/new-select2.js',
            'public/assets/js/default/vue/directives/item-comentario.js',
            'public/assets/js/default/vue/directives/porcentaje.js',
            'public/assets/js/default/vue/directives/inputmask3.js',
        ));
    }

//    public function ajax_exportar()
//    {
//        $post = $this->input->post();
//    	if(empty($post)){exit();}
//
//    	$cotizaciones_alquiler = $this->CotizacionesAlquilerRepository->get(['ids', $post['ids']]);
//
//        $csv = Writer::createFromFileObject(new SplTempFileObject());
//        $csv->insertOne(['No. Cotizacion','Cliente','Fecha de inicio','Saldo por facturar','Total facturado',utf8_decode('Días transcurridos'),'Estado']);
//        $csv->insertAll($this->CotizacionesAlquilerRepository->getCollectionExportar($cotizaciones_alquiler));
//        $csv->output("CotizacionAlquiler-". date('ymd') .".csv");
//        exit();
//    }

    public function crear(){

        $acceso = 1;
        $mensaje = array();
        if (!$this->auth->has_permission('acceso')) {
            $acceso = 0;
            $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud', 'clase' => 'alert-danger');
        }

        $clause = array(
            "empresa_id" => $this->empresa_id
        );

       	$this->_css();
        $this->_js();
        $this->assets->agregar_js(array(
            'public/resources/compile/modulos/cotizaciones_alquiler/crear-alquiler-cotizacion.js',
        ));

        $this->assets->agregar_var_js(array(
            "vista"                 => 'crear',
            "acceso"                => $acceso == 0 ? $acceso : $acceso,
            "usuario_id"            => $this->session->userdata("id_usuario"),
            "lista_precio_alquiler" => $this->PreciosRepository->get(array('empresa_id' => $this->empresa_id, "estado" => 1, "tipo_precio" => "alquiler")),
        ));

        $data['mensaje'] = $mensaje;
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-line-chart"></i> Cotizaciones: Crear ',
        );

        $this->template->agregar_titulo_header('Cotizaciones: Crear ');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();

    }

    public function editar($uuid) {

        $acceso = 1;
        $mensaje = array();
        $cotizacion_alquiler = $this->CotizacionesAlquilerRepository->findBy(['empresa_id'=>$this->empresa_id,'uuid_cotizacion'=>$uuid]);
        if (!$this->auth->has_permission('acceso') || is_null($cotizacion_alquiler)) {
            $acceso = 0;
            $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud', 'clase' => 'alert-danger');
        }

        $this->_css();
        $this->_js();
        $this->assets->agregar_js(array(
            'public/resources/compile/modulos/cotizaciones_alquiler/crear-alquiler-cotizacion.js',
        ));

        if(!is_null($cotizacion_alquiler))$cotizacion_alquiler->load('items');

        $this->assets->agregar_var_js(array(
            "vista"                     => 'editar',
            "acceso"                    => $acceso == 0 ? $acceso : $acceso,
            "cotizacion_alquiler"         => $cotizacion_alquiler,
            "uuid_cotizacion"   => $uuid,
              "lista_precio_alquiler" => $this->PreciosRepository->get(array('empresa_id' => $this->empresa_id, "estado" => 1, "tipo_precio" => "alquiler")),
        ));

        $data['mensaje'] = $mensaje;
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-line-chart"></i> Cotizaciones: '.$cotizacion_alquiler->codigo,
        );

        $this->template->agregar_titulo_header('Cotizaciones: '.$cotizacion_alquiler->codigo);
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();

    }

    public function ocultoformulario() {
        $this->load->view('formulario');
    }

    //agregada funcion "ocultotablaV2" por jose luis
    public function ocultotablaV2($sp_string_var = []) {

        /*$this->assets->agregar_js(array(
            'public/assets/js/modules/cotizaciones/tabla.js'
        ));*/

        $sp_array_var = explode('=', $sp_string_var);
        if (count($sp_array_var) == 2) {

            $this->assets->agregar_var_js(array(
                $sp_array_var[0] => $sp_array_var[1]
            ));
        }

        $this->load->view('tabla');
    }

    public function ocultoformulario_items_cotizados($cotizacion_alquiler = array()) {
        $data = array();
        $clause = array('empresa_id' => $this->empresa_id);

        if (isset($cotizacion_alquiler['info'])){$data['info'] = $cotizacion_alquiler['info'];}

        $categorias = $this->ItemsCategoriasRepository->get(['empresa_id'=>$this->empresa_id,'conItems'=>true]);
        $categorias->load('items_contratos_alquiler');
        $this->assets->agregar_var_js(array(
            "categorias"        => $categorias,
            "ciclos_tarifarios" => $this->ContratosAlquilerCatalogosRepository->get(['tipo'=>'tarifa'])
        ));

        $this->load->view('formulario_items_cotizados', $data);
        $this->load->view('templates/cotizacion_items');
    }

    public function guardar()
    {

        $post = $this->input->post();

        if (!empty($post)) {
        $formGuardar = new Flexio\Modulo\CotizacionesAlquiler\FormRequest\GuardarCotizacionAlquiler;

            try {

            $cotizacion_alquiler = $formGuardar->guardar();
            }catch (\Exception $e) {
                log_message('error', __METHOD__ . " ->" . ", Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
                $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error! Su solicitud no fue procesada</b> ');
                $this->session->set_flashdata('mensaje', $mensaje);
                redirect(base_url('cotizaciones_alquiler/listar'));
            }


            if (!is_null($cotizacion_alquiler)) {
                $mensaje = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente ' . $cotizacion_alquiler->codigo);
            } else {
                $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error! Su solicitud no fue procesada</b> ');
            }
            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('cotizaciones_alquiler/listar'));
        }
    }
    function ajax_get_cotizacion(){
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        $uuid = $this->input->post('uuid', TRUE);
        $cotizacion_alquiler = $this->CotizacionesAlquilerRepository->findBy(['empresa_id'=>$this->empresa_id,'uuid_cotizacion'=>$uuid]);
        $cotizacion_alquiler->load('items.item.atributos','landing_comments');

        $cotizacion_alquiler->items->each(function($item) use($cotizacion_alquiler) {
            if ($item->comentario!=''){
                $fieldset = array(
                    'comentario'=>$item->comentario,
                    "usuario_id" => $cotizacion_alquiler->creado_por,
                    "created_at" =>$cotizacion_alquiler->created_at
                );
                $comentarios = new Comentario($fieldset);
                $cotizacion_alquiler->landing_comments->push($comentarios);
            }
            return $cotizacion_alquiler;
        });

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($cotizacion_alquiler))->_display();
        exit();
    }
}

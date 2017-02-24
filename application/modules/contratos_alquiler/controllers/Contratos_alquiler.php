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
use Flexio\Modulo\ContratosAlquiler\Repository\ContratosAlquilerRepository;
use Flexio\Modulo\ContratosAlquiler\Repository\ContratosAlquilerCatalogosRepository;
use Flexio\Modulo\Cliente\Repository\ClienteRepository;
use Flexio\Modulo\OrdenesCompra\Repository\OrdenesCompraRepository;
use Flexio\Modulo\Usuarios\Repository\UsuariosRepository;
use Flexio\Modulo\Inventarios\Repository\CategoriasRepository as ItemsCategoriasRepository;
use Flexio\Modulo\Inventarios\Repository\PreciosRepository;
use Flexio\Modulo\CentrosContables\Repository\CentrosContablesRepository;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Library\Util\FormRequest;
use Flexio\Modulo\CotizacionesAlquiler\Repository\CotizacionesAlquilerRepository as CotizacionesAlquilerRepository;
use Flexio\Modulo\Inventarios\Repository\ItemsRepository as itemsRep;
use Flexio\Modulo\EntregasAlquiler\Models\EntregasAlquiler;
use Flexio\Library\Util\AuthUser;
use Dompdf\Dompdf;
use Dompdf\Options;
class Contratos_alquiler extends CRM_Controller
{
    /**
     * Atributos
     */
    private $empresa_id;
    private $empresaObj;
    private $usuario_id;
    private $itemsRep;

    protected $ContratosAlquilerRepository;
    protected $ContratosAlquilerCatalogosRepository;
    protected $ClienteRepository;
    protected $OrdenesCompraRepository;
    protected $UsuariosRepository;
    protected $ItemsCategoriasRepository;
    protected $CentrosContablesRepository;
    protected $CotizacionesAlquilerRepository;
    protected $PreciosRepository;

    /**
     * Método constructor
     */
    public function __construct() {
        parent::__construct();
        //cargar los modelos
        $this->load->model('usuarios/Empresa_orm');
        $this->load->model('usuarios/Usuario_orm');
        $this->load->model('contabilidad/Impuestos_orm');
        $this->load->model('contabilidad/Cuentas_orm');
        $this->load->model('contabilidad/Centros_orm');
        $this->load->model('roles/Rol_orm');
        $this->load->module('documentos');

        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresaObj = new Buscar(new Empresa_orm,'uuid_empresa');
        $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
       // $this->empresaObj  = Empresa_orm::findByUuid($uuid_empresa);
        $this->empresa_id = $this->empresaObj->id;
        $this->usuario_id   = $this->session->userdata("id_usuario");
        $this->ContratosAlquilerRepository          = new ContratosAlquilerRepository();
        $this->ContratosAlquilerCatalogosRepository = new ContratosAlquilerCatalogosRepository();
        $this->ClienteRepository                    = new ClienteRepository();
        $this->OrdenesCompraRepository              = new OrdenesCompraRepository();
        $this->UsuariosRepository                   = new UsuariosRepository();
        $this->ItemsCategoriasRepository            = new ItemsCategoriasRepository();
        $this->CentrosContablesRepository            = new CentrosContablesRepository();
        $this->CotizacionesAlquilerRepository = new CotizacionesAlquilerRepository;
        $this->PreciosRepository = new PreciosRepository;
        $this->itemsRep = new itemsRep();
        Carbon::setLocale('es');
        setlocale(LC_TIME, 'es_ES.utf8');
    }

    public function listar() {
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

        $this->assets->agregar_css(array(
            'public/assets/css/plugins/jquery/jquery.fileupload.css',
        ));
        $this->assets->agregar_js(array(
            'public/assets/js/plugins/jquery/fileupload/jquery.fileupload.js',
        ));


        $breadcrumb = array( "titulo" => '<i class="fa fa-car"></i> Contratos de alquiler',
            "ruta" => array(
                0 => ["nombre" => "Alquileres", "activo" => false],
                1 => ["nombre" => '<b>Contratos de alquiler</b>', "activo" => true]
            ),
            "menu" => ["nombre" => "Crear", "url" => "contratos_alquiler/crear","opciones" => array()]
        );
        $breadcrumb["menu"]["opciones"]["#exportarContratosAlquiler"] = "Exportar";

        $this->assets->agregar_var_js(array(
            "toast_mensaje" => $mensaje
        ));

        $clause = ['empresa_id' => $this->empresa_id];
        $data['clientes']   = $this->ClienteRepository->get($clause);
        $data['estados']    = $this->ContratosAlquilerCatalogosRepository->get(['tipo'=>'estado']);

        $this->template->agregar_titulo_header('Listado de Contratos de alquiler');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }


    public function ocultotabla() {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/contratos_alquiler/tabla.js'
        ));

        $this->load->view('tabla');
    }

    function ocultotabla2($modulo_id = null) {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/contratos_alquiler/tabla2.js'
        ));

        if(!empty($modulo_id) and is_array(explode("=", $modulo_id))){
            $key_value = explode("=", $modulo_id);
            $this->assets->agregar_var_js([$key_value[0]=>$key_value[1]]);
        }

        $this->load->view('tabla');
    }

    function ocultoformulariocomentarios() {

        $data = array();

        $this->assets->agregar_js(array(
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/modules/contratos_alquiler/vue.comentario.js',
            'public/assets/js/modules/contratos_alquiler/formulario_comentario.js'
        ));

        $this->load->view('formulario_comentarios');
        $this->load->view('comentarios');
    }

    /**
     * Método listar los registros de los subcontratos en ocultotabla()
     */
    public function ajax_listar() {
        if(!$this->input->is_ajax_request()){return false;}

        $clause                 = $this->input->post();
        $clause['empresa_id']   = $this->empresa_id;

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = $this->ContratosAlquilerRepository->count($clause);

        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $contratos_alquiler = $this->ContratosAlquilerRepository->get($clause ,$sidx, $sord, $limit, $start);

        $response          = new stdClass();
        $response->page    = $page;
        $response->total   = $total_pages;
        $response->records = $count;

        if($count > 0){
            foreach ($contratos_alquiler as $i => $contrato_alquiler)
            {
                $response->rows[$i]["id"]   = $contrato_alquiler->uuid_contrato_alquiler;
                $response->rows[$i]["cell"] = $this->ContratosAlquilerRepository->getCollectionCell($contrato_alquiler, $this->auth);
            }
        }
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))->_display();
        exit;
    }

    public function ajax_listar2() {
        if(!$this->input->is_ajax_request()){return false;}

        $clause                 = $this->input->post();
        $clause['empresa_id']   = $this->empresa_id;

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = $this->ContratosAlquilerRepository->count($clause);

        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $contratos_alquiler = $this->ContratosAlquilerRepository->get($clause ,$sidx, $sord, $limit, $start);

        $response          = new stdClass();
        $response->page    = $page;
        $response->total   = $total_pages;
        $response->records = $count;

        if($count > 0){
            foreach ($contratos_alquiler as $i => $contrato_alquiler)
            {
                $response->rows[$i]["id"]   = $contrato_alquiler->uuid_contrato_alquiler;
                $response->rows[$i]["cell"] = $this->ContratosAlquilerRepository->getCollectionCell2($contrato_alquiler, $this->auth);
            }
        }
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))->_display();
        exit;
    }
    /**
     * Retornar arreglo con los
     * campos que se mostraran
     * en el formulario de subir archivos.
     *
     * @return array
     */
    function documentos_campos() {

        return array(array(
            "type"		=> "hidden",
            "name" 		=> "contrato_id",
            "id" 		=> "contrato_id",
            "model" 	=> "campos.contrato_id",
            "class"		=> "",
            "readonly"	=> "",
            "ng-model" 	=> "campos.contrato_id",
            "label"		=> ""
        ));
    }
    function ajax_guardar_documentos() {
        if(empty($_POST)){
            return false;
        }

         $id = $this->input->post('contrato_id', true);
         $modeloInstancia = $this->ContratosAlquilerRepository->find($id);


         $info_documento = $this->documentos->info_documento();

         $comentario = ['comentario'=>$info_documento, 'tipo'=>'documento'];
         $this->ContratosAlquilerRepository->addHistorial( $modeloInstancia,  $comentario );
         $this->documentos->subir($modeloInstancia);
    }
    function ajax_guardar_comentario() {

        if(!$this->input->is_ajax_request()){
            return false;
        }

        $model_id   = $this->input->post('modelId');
        $comentario = $this->input->post('comentario');
        $comentario = ['comentario'=>$comentario,'usuario_id'=>$this->usuario_id, 'tipo'=>'comentario'];
        $contrato_alquiler = $this->ContratosAlquilerRepository->agregarComentario($model_id, $comentario);
        $contrato_alquiler->load('comentario_timeline');
        $this->ContratosAlquilerRepository->addHistorial( $contrato_alquiler,  $comentario );

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($contrato_alquiler->comentario_timeline->toArray()))->_display();
        exit;
    }


    /**
     * Método para generar código del subcontrato
     */
    private function _generar_codigo() {
        $clause_empresa = ['empresa_id' => $this->empresa_id];
        $total = $this->ContratosAlquilerRepository->count($clause_empresa);
        $year = Carbon::now()->format('y');
        $codigo = Util::generar_codigo('ALQ'.$year,$total + 1);
        return $codigo;
    }


    private function _css() {
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
            'public/assets/css/modules/stylesheets/contratos_alquiler.css',
        ));
    }


    private function _js() {
        $this->assets->agregar_js(array(
            //'public/assets/js/default/jquery-ui.min.js',
            //'public/assets/js/plugins/jquery/jquery.sticky.js',
            //'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            //'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            //'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            //'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
            //'public/assets/js/default/accounting.min.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            //'public/assets/js/plugins/jquery/sweetalert/sweetalert.min.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/plugins/bootstrap/select2/select2.min.js',
            'public/assets/js/plugins/bootstrap/select2/es.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
            'public/assets/js/default/toast.controller.js',
            'public/assets/js/default/vue/directives/select2.js',
            'public/assets/js/default/vue/directives/datepicker2.js',
            'public/assets/js/default/vue/directives/datepicker-range.js',
            'public/assets/js/default/vue/directives/new-select2.js',
            'public/assets/js/default/vue/directives/item-comentario.js',
            'public/assets/js/default/vue/directives/porcentaje.js',
            'public/assets/js/default/vue/directives/inputmask3.js',
           // 'public/assets/js/modules/contratos_alquiler/plugins.js',
        ));
    }

    public function ajax_exportar() {
        $post = $this->input->post();
    	if(empty($post)){exit();}

    	$contratos_alquiler = $this->ContratosAlquilerRepository->get(['ids', $post['ids']]);

        $csv = Writer::createFromFileObject(new SplTempFileObject());
        $csv->insertOne(['No. Contrato','Cliente','Fecha de inicio','Saldo por facturar','Total facturado',utf8_decode('Días transcurridos'),'Estado']);
        $csv->insertAll($this->ContratosAlquilerRepository->getCollectionExportar($contratos_alquiler));
        $csv->output("ContratoAlquiler-". date('ymd') .".csv");
        exit();
    }


    public function crear($modulo=NULL, $modulo_uuid=NULL) {

        $acceso = 1;
        $mensaje = array();
        if (!$this->auth->has_permission('acceso', 'contratos_alquiler/crear')) {
            $acceso = 0;
            $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud', 'clase' => 'alert-danger');
        }
        
        //#case 1682.1 si el usuario tiene role de vendedor, se deshabilita el campo vendedor y se pone como default el usuario logeado
        $vendedor=1;
        $user_id=0;
        foreach(AuthUser::roles_empresa() as $role){
            if($role->nombre=='Vendedor'){
                $vendedor = 0;
                $user_id = AuthUser::getId();
            }
        }
        
        $disableEmpezarDesde = 0;
        $empezable = collect([
            'id' =>'',
            'type' =>'',
            'clientes' => [],
            'cotizacions' => [],
            'types' => [
                    
                    1 => ['id' => 'cotizacion', 'nombre' => 'Cotizaciones']]
        ]);

        if (preg_match('/cotizacion/', $modulo)) {

            //$uuid_contrato_alquiler = str_replace('cotizacion', '', $foreing_key);

            $cotizaciones = $this->CotizacionesAlquilerRepository->findBy(['uuid_cotizacion' => $modulo_uuid]);
            //dd(collect($cotizaciones->load('articulos'))->toArray());
            $empezable['cotizacions'] = $cotizaciones;
            $empezable['type'] = 'cotizacion';
            $empezable['id'] = $cotizaciones->id;
            //$empezable['types'] = [ 0 => ['id' => 'cotizacion', 'nombre' => 'Cotizaciones']];
            $disableEmpezarDesde = 1;
        } 
        $clause = array(
            "empresa_id" => $this->empresa_id
        );

       	$this->_css();
        $this->_js();
        $this->assets->agregar_js(array(
            'public/assets/js/modules/contratos_alquiler/components/contrato_items.js',
            'public/assets/js/modules/contratos_alquiler/components/item_extrainfo.js',

    	  ));

        $this->assets->agregar_var_js(array(
            "vista" => 'crear',
            "disableEmpezarDesde" => $disableEmpezarDesde,
            "acceso" => $acceso,
            "vendedor" => $vendedor,
            "userId" => $user_id,
            "empezable" => $empezable
        ));

        $data['mensaje'] = $mensaje;
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-car"></i> Contratos de alquiler: Crear ',
            "ruta" => [
                ["nombre" => "Alquileres", "activo" => false],
                ["nombre" => "Contratos de alquiler", "activo" => false, 'url'=>'contratos_alquiler/listar'],
                ["nombre" => "<b>Crear</b>","activo" => true]
            ]
        );

        $this->template->agregar_titulo_header('Contratos de alquiler: Crear ');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();

    }

    public function editar($uuid) {


        $acceso = 1;
        $mensaje = array();
        if (!$this->auth->has_permission('acceso', 'contratos_alquiler/editar/(:any)')) {
            $acceso = 0;
            $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud', 'clase' => 'alert-danger');
        }

        $this->_css();
        $this->_js();

        $contrato_alquiler = $this->ContratosAlquilerRepository->findBy(['uuid_contrato_alquiler'=>$uuid]);
        $contrato_alquiler->load('comentario_timeline','contratos_items');
        $anular = 1;
        if(count($contrato_alquiler->entregas_activas)>0){
            $anular = 0;
        }
        if($contrato_alquiler->tipo == 'cliente') //edicion de alquiler tipo cliente
        {
            $clientes = $this->ClienteRepository->get( array('empresa_id' => $this->empresa_id) );
            $clientes->load('centro_facturable');
            $empezable_lista = $this->ClienteRepository->getCollectionCliente( $clientes );
            $empezable_types = [0 => ['id' => 'cliente', 'nombre' => 'Clientes']];
            $id_empezable = $contrato_alquiler->cliente_id;

         }else{
            $id_empezable = (int) preg_replace("/cotizacion/", "", $contrato_alquiler->tipo);
            $cotizaciones = $this->CotizacionesAlquilerRepository->getCotizacionGandas( array('empresa_id' => $this->empresa_id, 'cotizacion_id' => $id_empezable) );
            $cotizaciones->load("cliente");
            $empezable_lista = $this->CotizacionesAlquilerRepository->getCollectionCotizacionEnContrato($cotizaciones);
            $empezable_types = [0 => ['id' => 'cotizacion', 'nombre' => 'Cotizaciones']];
	        $contrato_alquiler->tipo = 'cotizacion';

          }

          $empezable = collect([
            'type' =>($contrato_alquiler->tipo=='cliente')?'cliente':'cotizacion',
            "{$contrato_alquiler->tipo}s" => $empezable_lista,
            'types' =>$empezable_types,
            'id' => $id_empezable
        ]);

         $this->assets->agregar_js(array(
               'public/assets/js/modules/contratos_alquiler/ver_historial.js'
        ));
         //#case 1682.1 si el usuario tiene role de vendedor, se deshabilita el campo vendedor y se pone como default el usuario logeado
        $vendedor=1;
        $user_id=0;
        foreach(AuthUser::roles_empresa() as $role){
            if($role->nombre=='Vendedor'){
                $vendedor = 0;
                $user_id = AuthUser::getId();
            }
        }
          $this->assets->agregar_var_js(array(
            "vista"                     => 'editar',
            "acceso"                    => $acceso == 0 ? $acceso : $acceso,
            "contrato_alquiler"         => collect($this->ContratosAlquilerRepository->getCollectionCampo($contrato_alquiler)),
            "contrato_alquiler_items"   => $contrato_alquiler->items,
            "objectModel"               => $contrato_alquiler,
            "contrato_id"               => $contrato_alquiler->id,
            "empezable"               => $empezable,
              "vendedor" => $vendedor,
              "userId" => $user_id,
            "disableAnular"           => $anular
        ));

        $data['mensaje']            = $mensaje;
        $data['contrato_alquiler']  = $contrato_alquiler;
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-car"></i> Contratos de alquiler: '.$contrato_alquiler->codigo,
             "historial" => true,
             "ruta" => [
                 ["nombre" => "Alquileres", "activo" => false],
                 ["nombre" => "Contratos de alquiler", "activo" => false,'url'=>'contratos_alquiler/listar'],
                 ["nombre" => "<b>{$contrato_alquiler->codigo}</b>","activo" => true]
             ],
                 "menu" => ["nombre" => "Acci&oacute;n", "url" => "#","opciones" => array("contratos_alquiler/imprimir/{$uuid}" => "Imprimir")]

        );
        $cotizacion_id = 'x';
        
        if($contrato_alquiler->tipoid!=''){
            $cotizacion_id = $contrato_alquiler->tipoid;
        }
        $data['subpanels'] = [
            
            'cotizaciones_alquiler' => ['contrato_alquiler' => $contrato_alquiler->id],
            'entregas' => ['contrato_alquiler' => $contrato_alquiler->id],
            'devoluciones' => ['contrato_alquiler' => $contrato_alquiler->id],
            'ordenes_alquiler' => ['contrato_alquiler' => $contrato_alquiler->id],
            'facturas2' => ['contrato_alquiler' => $contrato_alquiler->id],
            'items' => ['contrato_alquiler' => $contrato_alquiler->id],
            'series' => ['contrato_alquiler' => $contrato_alquiler->id],
            'documentos' => ['contrato_alquiler' => $contrato_alquiler->id]
            
        ];
        
       
        
        $this->template->agregar_titulo_header('Contratos de alquiler: '.$contrato_alquiler->codigo);
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();

    }

    public function imprimir($uuid=null)
    {
        if($uuid==null){
            return false;
        }

        $contrato = $this->ContratosAlquilerRepository->findByUuid($uuid);
        $variable = collect($this->ContratosAlquilerRepository->getCollectionCampo($contrato));
        $contrato->load('centro_facturacion', 'corte_facturacion', 'items', 'contratos_items');
        $centro = $this->CentrosContablesRepository->find($contrato->centro_contable_id);
        $empresa = $this->empresaObj->find($contrato->empresa_id);
        $cliente = $this->ClienteRepository->find($contrato->cliente_id);
        $cliente->load('telefonos_asignados');
        $usuario = $this->UsuariosRepository->find($contrato->created_by);
        $creador = $this->UsuariosRepository->find($this->usuario_id);
        $items = $contrato->load('contratos_items', 'entregas', 'contratos_items.item', 'contratos_items.ciclo', 'contratos_items.contratos_items_detalles');

        if(!empty($contrato->entregas)){
        $i=0;
        foreach($contrato->entregas as $row){
          //$ResultEntregas[$i] = EntregasAlquiler::with(array("contrato_alquiler.contratos_items.item"))->where('uuid_entrega_alquiler', hex2bin($row->uuid_entrega_alquiler))->get();
          $ResultEntregas[$i] = EntregasAlquiler::with(array("contrato_alquiler.corte_facturacion","contrato_alquiler.facturar_contra_entrega","contrato_alquiler.calculo_costo_retorno","contrato_alquiler.contratos_items", "contrato_alquiler.contratos_items.ciclo", "contrato_alquiler.contratos_items.item", "contrato_alquiler.contratos_items.item.precios_alquiler"))->where('uuid_entrega_alquiler', hex2bin($row->uuid_entrega_alquiler))->get();
          $i++;
        }
        }else{
            $ResultEntregas = '';
        }
        $options = new Options();
        $options->set('isRemoteEnabled', TRUE);
        $dompdf = new Dompdf($options);
        $data   = ['contrato_info'=>$contrato, 'empresa' => $empresa, 'usuario' => $usuario, 'centro_contable' => $centro->nombre, 'cliente' => $cliente, 'items' => $items, 'items_entregados' => !empty($ResultEntregas)?$ResultEntregas:'', 'creador' => $creador, 'atributos' => $variable['articulos']];
        $html = $this->load->view('pdf/contrato_alquiler', $data, true);

        //render
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream($contrato->codigo);

        exit();
    }

    public function ocultoformulario($contrato_alquiler = array()) {

        $data = $rango = array();

        $this->assets->agregar_js(array(

            'public/assets/js/modules/contratos_alquiler/components/contrato_items.js',
            'public/assets/js/modules/contratos_alquiler/components/item_extrainfo.js',
            'public/assets/js/default/vue/components/empezar_desde.js',
            'public/resources/compile/modulos/contratos_alquiler/formulario.js',

    	));

        $clause = array('empresa_id' => $this->empresa_id);

        if (isset($contrato_alquiler['info'])){$data['info'] = $contrato_alquiler['info'];}
        $clientes = $this->ClienteRepository->getClientesEstadoActivo($clause)->get();
        $clientes->load('centro_facturable');
        $clientes = $this->ClienteRepository->getCollectionCliente( $clientes );


        $cotizaciones = $this->CotizacionesAlquilerRepository->getCotizacionGandas( $clause );
        $cotizaciones = $this->CotizacionesAlquilerRepository->getCollectionCotizacionEnContrato($cotizaciones);

        $condicion = array('empresa_id'=>$this->empresaObj->id,'estado'=>'Activo','transaccionales'=>true);

        foreach (range(1, 28) as $number) {
          $rango[] = array(
                "id"=>$number,
                "nombre"=>$number
            );
        }

        //---------------------
        // Catalogo Impuestos
        //---------------------
        $clause_impuesto = array('empresa_id' => $this->empresa_id, "estado" => "Activo");
        $impuestos = Impuestos_orm::where($clause_impuesto)->whereHas('cuenta', function ($query) use ($clause_impuesto) {
        	$query->activas();
        	$query->where('empresa_id', '=', $clause_impuesto['empresa_id']);
        })->get(array('id', 'uuid_impuesto', Capsule::raw("HEX(uuid_impuesto) AS uuid"), 'nombre', 'impuesto'));

        //---------------------
        // Catalogo Cuenta Contable
        //---------------------
        $cuentas = Cuentas_orm::transaccionalesDeEmpresa($this->empresa_id)->activas()
        	->get(array('id', 'uuid_cuenta', 'nombre', 'codigo', Capsule::raw("HEX(uuid_cuenta) AS uuid")));

        $preguntas_cerrada = $this->ContratosAlquilerCatalogosRepository->get(['tipo'=>'pregunta_cerrada']);
        $pregunta_cerrada_default = $preguntas_cerrada->filter(function ($value, $key) {
            return $value["valor"]=="no";
        })->first();

        $this->assets->agregar_var_js(array(
            "fecha_hoy"             =>date("d/m/Y"),
            "clientes"              => $clientes,
            "cotizacions"          => $cotizaciones,
            "vendedores"            => $this->UsuariosRepository->get(array_merge($clause, ['vendedor' => true])),
            "estados"               => $this->ContratosAlquilerCatalogosRepository->get(['tipo'=>'estado']),
            "cortes_facturacion"    => $this->ContratosAlquilerCatalogosRepository->get(['tipo'=>'corte_facturacion']),
            "costos_retorno"        => $this->ContratosAlquilerCatalogosRepository->get(['tipo'=>'calculo_costo_retorno']),
            "lista_precio_alquiler" => $this->PreciosRepository->get(array('empresa_id' => $this->empresa_id, "estado" => 1, "tipo_precio" => "alquiler")),
            "preguntas_cerrada"     => $preguntas_cerrada,
            "pregunta_cerrada_default" => $pregunta_cerrada_default->id,
            "cuentas"               => $cuentas,
            "impuestos"             => $impuestos,
            "codigo"                => $this->_generar_codigo(),
            "centros_contables"     => $this->CentrosContablesRepository->get($condicion),
            "dia_corte"             => collect($rango)
        ));

        $this->load->view('formulario', $data);
        $this->load->view('vue/components/empezar_desde');

    }

    public function ocultoformulario_items_contratados($contrato_alquiler = array()) {
        $data = array();
        $clause = array('empresa_id' => $this->empresa_id);

        if (isset($contrato_alquiler['info'])){$data['info'] = $contrato_alquiler['info'];}

        $categorias = $this->ItemsCategoriasRepository->getCategoriasAlquiler(['empresa_id'=>$this->empresa_id,'conItems'=>true])->map(function($categoria){
            return array_merge($categoria->toArray(), ["items" => []]);
        });
        $this->assets->agregar_var_js(array(
            "categorias" => $categorias,
            "ciclos_tarifarios" => $this->ContratosAlquilerCatalogosRepository->get(['tipo'=>'tarifa'])->sortBy('orden')
        ));

        $this->load->view('formulario_items_contratados', $data);
        $this->load->view('templates/contrato_items');
        $this->load->view('templates/item_extrainfo');

    }

    public function guardar() {

        $post = $this->input->post();
        if (!empty($post)) {

            
            $contrato_alquiler=null;

            Capsule::beginTransaction();
            try {
                $campo = $post['campo'];
                if(empty($campo['id']))
                {
                    
                    $post['campo']['codigo']        = $this->_generar_codigo();
                    $post['campo']['empresa_id']    = $this->empresa_id;
                    $contrato_alquiler = $this->ContratosAlquilerRepository->create($post);
                } else {
                    $contrato_alquiler = $this->ContratosAlquilerRepository->save($post);
                }

            } catch (Illuminate\Database\QueryException $e) {
                log_message('error', __METHOD__ . " ->" . ", Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
                Capsule::rollback();
                $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error! Su solicitud no fue procesada</b> ');
                $this->session->set_flashdata('mensaje', $mensaje);
                redirect(base_url('contratos_alquiler/listar'));
                //echo $e->getMessage();
            }
            Capsule::commit();

            if (!is_null($contrato_alquiler)) {
                $mensaje = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente ' . $contrato_alquiler->codigo);
            } else {
                $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error! Su solicitud no fue procesada</b> ');
            }
            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('contratos_alquiler/listar'));
        }


    }

    function ajax_contrato_info() {
    	$uuid = $tipo = $this->input->post('uuid');
    	$id = $this->input->post('contrato_id');
    	$item_facturado = [];
    	$clause = array(
    		"empresa_id" => $this->empresa_id
    	);

      if(!empty($uuid)){
        $clause["uuid_contrato_alquiler"] = $uuid;
      }
      if(!empty($id)){
        $clause["id"] = $id;
      }

    	$ciclos_tarifarios = $this->ContratosAlquilerCatalogosRepository->get(['tipo'=>'tarifa']);

    	$contrato = $this->ContratosAlquilerRepository->findBy($clause);
    	$contrato->load('cliente', 'contratos_items', 'contratos_items.item', 'contratos_items.impuestoinfo', 'contratos_items.cuenta');
		foreach($contrato->contratos_items as $item){
    		$item["periodo_tarifario"] = $ciclos_tarifarios;
    	}

    	/*foreach($contrato->facturas as $items){
    		foreach(explode(",", $items->pivot->items_facturados) as $id){
    			$item_facturado[] = (int)$id;
    		}
    	}*/


    	$contrato = array_merge($contrato->toArray(),[/*"facturados" => $item_facturado*/]);

    	$this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
    	->set_output(json_encode($contrato))->_display();
    	exit;
    }

    function historial($contrato_uuid = NULL){

      $acceso = 1;
      $mensaje =  array();
      $data = array();

      $contrato_alquiler = $this->ContratosAlquilerRepository->findBy(['uuid_contrato_alquiler'=>$contrato_uuid]);
       if(!$this->auth->has_permission('acceso','contratos_alquiler/historial') && is_null($contrato_alquiler)){
        // No, tiene permiso
          $acceso = 0;
          $mensaje = array('estado'=>500, 'mensaje'=>' <b>Usted no cuenta con permiso para esta solicitud</b>','clase'=>'alert-danger');
      }

      $this->_css();
      $this->_js();
      $this->assets->agregar_js(array(
          'public/assets/js/modules/contratos_alquiler/vue.componente.timeline.js',
          'public/assets/js/modules/contratos_alquiler/vue.timeline.js',

      ));


        $breadcrumb = array(
            "titulo" => '<i class="fa fa-car"></i> Bit&aacute;cora: Contrato de alquiler '.$contrato_alquiler->codigo,
            "ruta" => array(
                0 => array(
                    "nombre" => "Alquileres",
                    "activo" => false,

                ),
                1 => array(
                    "nombre" => "Contratos de alquiler",
                    "activo" => false,
                    "url" => 'contratos_alquiler/listar'
                ),
                2 => array(
                    "nombre" => $contrato_alquiler->codigo,
                    "activo" => false,
                    "url" => 'contratos_alquiler/editar/'.$contrato_uuid
                ),
                3 => array(
                    "nombre" => '<b>Bitácora</b>',
                    "activo" => true
                )
            ),
            "filtro"    => false,
            "menu"      => array()
        );


      $contrato_alquiler->load('historial');
          $this->assets->agregar_var_js(array(
            "timeline" => $contrato_alquiler,
      ));
        $data['codigo'] = $contrato_alquiler->codigo;
       $this->template->agregar_titulo_header('Contratos de alquiler');
      $this->template->agregar_breadcrumb($breadcrumb);
      $this->template->agregar_contenido($data);
      $this->template->visualizar();
  }
    function ocultotimeline(){
        $this->load->view('timeline');
  }

}

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Flexio
 * @subpackage Controllers
 * @category   Depreciacion Activo Fijo
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  04/03/2016
 */

use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\DepreciacionActivosFijos\Repository\DepreciacionRepository as DepreciacionRepository;
use Flexio\Modulo\Inventarios\Repository\ItemActivoFijoRepository as ItemActivoFijo;
use Flexio\Modulo\Inventarios\Transform\ItemTransform as ItemTransform;
use Flexio\Library\Util\FormRequest;
use Flexio\Modulo\Inventarios\Models\Items as Items;
use Flexio\Modulo\Inventarios\Models\ItemsCat;
use Flexio\Modulo\Contabilidad\Models\Cuentas;


//transacciones
use Flexio\Modulo\DepreciacionActivosFijos\Transacciones\DepreciacionActivosFijosTransacciones;

class Depreciacion_activos_fijos extends CRM_Controller{

  private $empresa_id;
  private $empresaObj;
  protected $depreciacionRepositorio;
  protected $depItemRepository;
  protected $activoFijo;
  protected $disparador;
    //transacciones
    protected $DepreciacionActivosFijosTransacciones;
              function __construct(){
    parent::__construct();

    $this->load->model('usuarios/Empresa_orm');
    $this->load->model('usuarios/Usuario_orm');
    $this->load->model('clientes/Cliente_orm');
    $this->load->model('contabilidad/Cuentas_orm');
    $this->load->model('contabilidad/Centros_orm');
    $this->load->model("inventarios/Categorias_orm");
    $this->load->model("inventarios/Items_categorias_orm");


    $uuid_empresa = $this->session->userdata('uuid_empresa');
    $empresaObj  = new Buscar(new Empresa_orm,'uuid_empresa');
	  $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
	  $this->empresa_id = $this->empresaObj->id;
    $this->depreciacionRepositorio =  new DepreciacionRepository;
    $this->activoFijo = new ItemActivoFijo;
  //$this->depItemRepository =  new DepreciacionItemRepository;
    //$this->disparador = new \Illuminate\Events\Dispatcher();

        //transacciones
        $this->DepreciacionActivosFijosTransacciones = new DepreciacionActivosFijosTransacciones;
  }


  function listar(){
    $data = array();
    $mensaje ='';
    if(!$this->auth->has_permission('acceso'))
    {
      redirect ( '/' );
    }

    if(!empty($this->session->flashdata('mensaje'))){
      $mensaje = json_encode($this->session->flashdata('mensaje'));
    }


    $this->_Css();
    $this->_js();

    $breadcrumb = array( "titulo" => '<i class="fa fa-calculator"></i>Depreciaci&oacute;n de activos fijos',
        "ruta" => array(
          0 => [
            "nombre" => "contabilidad",
            "activo" => false
          ],
          1 => ["nombre" => '<b>Depreciaci&oacute;n de activos fijos</b>', "activo" => true]
        ),
        "menu" => [
          "nombre" => "Crear","url"	 => "depreciacion_activos_fijos/crear",
          "opciones" => array()
        ]
   );
   $this->assets->agregar_var_js(array(
     "toast_mensaje" => $mensaje
   ));
   $clause = array('empresa_id' => $this->empresa_id,'estado' => 'Activo');

   $ids_centros = Centros_orm::where($clause)->lists('padre_id');
   $centros_contables = Centros_orm::whereNotIn('id', $ids_centros->toArray())->where(function($query) use($clause){
     $query->where($clause);
   })->get(array('id','nombre','uuid_centro'));

   $data['centros_contables']= $centros_contables;
   $breadcrumb["menu"]["opciones"]["#exportarListaDepreciacion"] = "Exportar";
   $this->template->agregar_titulo_header('Listado de Contratos');
   $this->template->agregar_breadcrumb($breadcrumb);
   $this->template->agregar_contenido($data);
   $this->template->visualizar($breadcrumb);

  }


  public function ocultotabla($uuid=NULL,$modulo=NULL){

    $this->assets->agregar_js(array(
      'public/assets/js/modules/depreciacion/tabla.js'
    ));
    $this->load->view('tabla');
  }

  public function ajax_listar(){
    if(!$this->input->is_ajax_request()){
      return false;
    }

    $centro_contable_id = $this->input->post('centro_contable_id',TRUE);
    $referencia = $this->input->post('referencia',TRUE);
    $desde = $this->input->post('desde',TRUE);
    $hasta = $this->input->post('hasta',TRUE);

    $clause = array('empresa_id' => $this->empresa_id);


    if(!empty($centro_contable_id)) $clause['centro_contable_id'] = $centro_contable_id;
    if(!empty($referencia)) $clause['referencia'] = $referencia;
    if(!empty($desde)) $clause['fecha_desde'] = Carbon::createFromFormat('m/d/Y',$desde,'America/Panama')->format('Y-m-d 00:00:00');
    if(!empty($hasta)) $clause['fecha_hasta'] = Carbon::createFromFormat('m/d/Y',$hasta,'America/Panama')->format('Y-m-d 23:59:59');

    list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
    $count = $this->depreciacionRepositorio->lista_totales($clause); // funcion del repositorio
    list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
    $depreciaciones = $this->depreciacionRepositorio->listar($clause ,$sidx, $sord, $limit, $start); //funcion del repositorio
    $response = new stdClass();
    $response->page     = $page;
    $response->total    = $total_pages;
    $response->records  = $count;
    if(!empty($depreciaciones->toArray())){
      $i=0;

      foreach ($depreciaciones as $row) {
        $hidden_options = "";
        $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row->uuid_depreciacion .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
        $hidden_options = '<a href="'. base_url('depreciacion_activos_fijos/ver/'. $row->uuid_depreciacion) .'" data-id="'. $row->uuid_depreciacion .'" class="btn btn-block btn-outline btn-success">Ver Depreciaci&oacute;n</a>';
        $response->rows[$i]["id"] = $row->uuid_depreciacion;
        $response->rows[$i]["cell"] = array(
           $row->uuid_depreciacion,
           '<a class="link" href="'. base_url('depreciacion_activos_fijos/ver/'. $row->uuid_depreciacion) .'">'.$row->codigo.'</a>',
           $row->centro_contable->nombre,
           $row->referencia,
           $row->created_at,
           "$".number_format($row->total(), 2, '.', ','),
           $link_option,
           $hidden_options
        );
       $i++;
      }
    }

    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))->_display();
    exit;
  }

  public function crear(){
    $acceso = 1;
    $mensaje = array();
    if(!$this->auth->has_permission('acceso','depreciacion_activos_fijos/crear')){
      // No, tiene permiso
        $acceso = 0;
        $mensaje = array('estado'=>500, 'mensaje'=>' <b>Usted no cuenta con permiso para esta solicitud</b>','clase'=>'alert-danger');
    }

    $this->_Css();
    //$this->assets->agregar_css(array('public/assets/css/modules/stylesheets/animacion.css'));
    $this->_js();
    $this->assets->agregar_js(array(
      //'public/assets/js/default/vue.js',
      'public/assets/js/default/vue-validator.min.js',
      //'public/assets/js/default/vue-resource.min.js',
      'public/assets/js/modules/depreciacion/vue.funcion.guardar.js',
      'public/assets/js/modules/depreciacion/componente.vue.js',
      'public/assets/js/modules/depreciacion/vue.crear.formulario.js',
      'public/assets/js/modules/depreciacion/vue.select2.js',

    ));
    $itemcat = new ItemsCat;
    $catalogodetipos = $itemcat->DeValor('tipo')
                                   ->where('etiqueta', 'Inventariado con serie')
                                   ->orWhere('etiqueta', 'Activos fijos con serie')
                                   ->get(array('id_cat', 'etiqueta'));
    
    $cuenta_transaccionales = Cuentas::transaccionalesDeEmpresa($this->empresa_id)->activas() ->orderBy("codigo")
    		->get(array('id', 'uuid_cuenta', 'nombre', 'codigo', Capsule::raw("HEX(uuid_cuenta) AS uuid")))->toArray();
    
    $cta = array();
    $i=0;
    foreach ($cuenta_transaccionales as $ctatran) {
        $cta[$i] = array(
            'id' => $ctatran['id'],
            'nombre' => $ctatran['codigo'].' - '.$ctatran['nombre']
        );
        $i++;
    }
    $cuenta_transaccionales = $cta;
    $this->assets->agregar_var_js(array(
      "vista" => 'crear',
      "acceso" => $acceso,
      "tiposdeitem" => $catalogodetipos,
      "catalogo_cuentas_transaccionales" => collect($cuenta_transaccionales)
    ));

    $data=array();
    $breadcrumb = array(
      "titulo" => '<i class="fa fa-calculator"></i> Depreciaci&oacute;n de activos fijos: Crear
      ',
    );
    $this->template->agregar_titulo_header('Crear Depreciaci&oacute;n de activos fijos');
    $this->template->agregar_breadcrumb($breadcrumb);
    $this->template->agregar_contenido($data);
    $this->template->visualizar($breadcrumb);
  }

  public function ocultoformulario($info=[]){
    $data=[];
    $clause_empresa = ['empresa_id'=>$this->empresa_id];
    $clause_empresas = ['empresa_id'=>$this->empresa_id,'estado'=>'Activo'];

    $data['clientes'] = Cliente_orm::where($clause_empresa)->get(array('id','nombre'));
    $categorias_catalogo = Categorias_orm::categoriasConItems($this->empresa_id);
    $data['categorias'] = $categorias_catalogo;
    
    
    //lista de centros contables
    $ids_centros = Centros_orm::where($clause_empresas)->lists('padre_id');
    $centros_contables = Centros_orm::whereNotIn('id', $ids_centros->toArray())->where(function($query) use($clause_empresas){
      $query->where($clause_empresas);      
    })->get(array('id','nombre','uuid_centro'));
    
    	
    $data['info'] = $info;
    $data['centros_contables']= $centros_contables;
    $this->load->view('formulario',$data);
    $this->load->view('componenteItems');
  }


  function ver($uuid=null){
    $acceso = 1;
    $mensaje = array();
    $data=array();
    $depreciacion = $this->depreciacionRepositorio->findByUuid($uuid);
    if(!$this->auth->has_permission('acceso','depreciacion_activos_fijos/ver/(:any)') && !is_null($depreciacion)){
      // No, tiene permiso
        $acceso = 0;
        $mensaje = array('estado'=>500, 'mensaje'=>' <b>Usted no cuenta con permiso para esta solicitud</b>','clase'=>'alert-danger');
    }

    $this->_Css();
    $this->_js();
    $this->assets->agregar_js(array(
      'public/assets/js/default/vue.js',
      'public/assets/js/default/vue-validator.min.js',
      'public/assets/js/default/vue-resource.min.js',
      'public/assets/js/modules/depreciacion/vue.funcion.guardar.js',
      'public/assets/js/modules/depreciacion/componente.vue.js',
      'public/assets/js/modules/depreciacion/vue.crear.formulario.js',
      'public/assets/js/modules/depreciacion/vue.select2.js',
    ));
    $itemcat = new ItemsCat;
    $catalogodetipos = $itemcat->DeValor('tipo')
                                   ->where('etiqueta', 'Inventariado con serie')
                                   ->orWhere('etiqueta', 'Activos fijos con serie')
                                   ->get(array('id_cat', 'etiqueta'));
    
    $cuenta_transaccionales = Cuentas::transaccionalesDeEmpresa($this->empresa_id)->activas() ->orderBy("codigo")
    		->get(array('id', 'uuid_cuenta', 'nombre', 'codigo', Capsule::raw("HEX(uuid_cuenta) AS uuid")))->toArray();
    
    $cta = array();
    $i=0;
    foreach ($cuenta_transaccionales as $ctatran) {
        $cta[$i] = array(
            'id' => $ctatran['id'],
            'nombre' => $ctatran['codigo'].' - '.$ctatran['nombre']
        );
        $i++;
    }
    $cuenta_transaccionales = $cta;
    
    $depreciacion->load('categoria_item', 'items','items.items_activo_fijo');
    $depreciacion->toArray();

    $this->assets->agregar_var_js(array(
      "vista" => 'ver',
      "acceso" => $acceso,
      "depreciacion" => $depreciacion,
      "depreciacion_id" => $depreciacion->id,
      "tiposdeitem" => $catalogodetipos,
      "catalogo_cuentas_transaccionales" => collect($cuenta_transaccionales)
    ));

    $breadcrumb = array(
      "titulo" => '<i class="fa fa-line-chart"></i> Depreciación de activos fijos: Ver' .$depreciacion->codigo,
      "ruta" => array(
        0 => [
          "nombre" => "Ventas",
          "activo" => false
        ],
        1 => ["nombre" => '<b> Depreciación de activos fijos</b>',"activo" => true]
      ),
      "menu" => []
    );

    $this->template->agregar_titulo_header(' Depreciación de activos fijos');
    $this->template->agregar_breadcrumb($breadcrumb);
    $this->template->agregar_contenido($data);
    $this->template->visualizar($breadcrumb);
  }



  private function _generar_codigo(){
    $clause_empresa = ['empresa_id'=>$this->empresa_id];
    $numero = $this->depreciacionRepositorio->lista_totales($clause_empresa);//cambiar al valor maximo
    return $numero + 1;
  }

    public function guardar(){
        if($_POST){
            
            $request = Illuminate\Http\Request::createFromGlobals();
            $array_activo = $request->input('campo');
            $datos_activos = FormRequest::data_formulario($array_activo);
            $items = $request->input('items');
            $datos_activos_items = FormRequest::array_filter_dos_dimenciones($items);

            if(!isset($datos_activos['id'])){
                $datos_activos['empresa_id'] = $this->empresa_id;
                $datos_activos['codigo'] = $this->_generar_codigo();
            }

            foreach ($datos_activos_items as $key => $activos_items) {
                if(!isset($activos_items[$key]['id'])){
                    $datos_activos_items[$key]['empresa_id'] = $this->empresa_id;
                }
            }
           
            Capsule::beginTransaction();
            try{
                $data = array('depreciacion'=>$datos_activos,'items'=> $datos_activos_items);
                $depreciacion = $this->depreciacionRepositorio->crear($data);
                
                $this->DepreciacionActivosFijosTransacciones->haceTransaccion($depreciacion);
            }catch(Illuminate\Database\QueryException $e){
                log_message('error', __METHOD__." ->". ", Linea: ". __LINE__ ." --> ". $e->getMessage()."\r\n");
                Capsule::rollback();
                $mensaje = array('estado'=>500, 'mensaje'=>'<b>¡Error! Su solicitud no fue procesada</b> ');
                $this->session->set_flashdata('mensaje', $mensaje);
                redirect(base_url('depreciacion_activos_fijos/listar'));
            }


            if(!is_null($depreciacion)){
                Capsule::commit();
                $mensaje = array('estado' => 200, 'mensaje' =>'<b>¡&Eacute;xito!</b> Se ha guardado correctamente '.$depreciacion->codigo);
            }else{
                $mensaje = array('estado'=>500, 'mensaje'=>'<b>¡Error! Su solicitud no fue procesada</b> ');
            }
            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('depreciacion_activos_fijos/listar'));
        }
    }


  function ajax_items_activos_fijos(){

    if(!$this->input->is_ajax_request()){
      return false;
    }
    $clause = ['empresa_id' => $this->empresa_id];
    if(isset($_POST['categoria_id'])){
        $clause['categoria_id'] = $this->input->post('categoria_id');
    }
    if(isset($_POST['cuenta_transaccional_id'])){
        $clause['cuenta_transaccional_id'] = $this->input->post('cuenta_transaccional_id');
    }
    if(isset($_POST['tipo_item'])){
        $clause['tipo_item'] = $this->input->post('tipo_item');
    }
    if(isset($_POST['categoria'])){
        $clause['categoria'] = $this->input->post('categoria');
    }

    $items_activo_fijo = $this->activoFijo->activo_fijo($clause);
    
    @$data = ItemTransform::transform($items_activo_fijo);
    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($data['data']))->_display();
    exit;
  }

  private function _Css(){
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
      'public/assets/css/modules/stylesheets/depreciacion.css'
    ));
  }

  private function _js(){
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
      'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
      'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
      'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.numeric.extensions.js',
      'public/assets/js/plugins/jquery/sweetalert/sweetalert.min.js',
      'public/assets/js/moment-with-locales-290.js',
      'public/assets/js/plugins/bootstrap/select2/select2.min.js',
      'public/assets/js/plugins/bootstrap/select2/es.js',
      'public/assets/js/plugins/bootstrap/daterangepicker.js',
      'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
      'public/assets/js/default/toast.controller.js',
      'public/assets/js/modules/depreciacion/plugins.js',
    ));
  }


}

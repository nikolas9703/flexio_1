<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Flexio
 * @subpackage Controller
 * @category   Notas de Credito
 * @author     Pensanomica Team
 * @link       http://www.pensanomica.com
 * @copyright  04/18/2016
 */

use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\NotaCredito\Repository\NotaCreditoRepository;
use Flexio\Modulo\NotaCredito\Repository\CatalogoNotaCreditoRepository as CatalogoNotaCredito;
use Flexio\Library\Util\FormRequest;
use Flexio\Strategy\Transacciones\Transaccion;
use Flexio\Modulo\NotaCredito\Transaccion\TransaccionNotaCredito;
use Flexio\Modulo\Cliente\Repository\ClienteRepository as ClienteRepository;

class Notas_creditos extends CRM_Controller
{
  protected $catalogo;
  protected $notaCreditoRepository;
  protected $ClienteRepository;

  function __construct(){
    parent::__construct();
    $this->load->model('usuarios/Usuario_orm');
    $this->load->model('usuarios/Empresa_orm');
    $this->load->model('usuarios/Roles_usuarios_orm');
    $this->load->model('roles/Rol_orm');
    $this->load->model('clientes/Cliente_orm');
    $this->load->model('contabilidad/Impuestos_orm');
    $this->load->model('contabilidad/Cuentas_orm');
    $this->load->model('contabilidad/Centros_orm');
    $this->load->model('contabilidad/Centros_orm');
    Carbon::setLocale('es');
    setlocale(LC_TIME, 'Spanish');
    $uuid_empresa = $this->session->userdata('uuid_empresa');
    $empresaObj  = new Buscar(new Empresa_orm,'uuid_empresa');
    $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
    $this->empresa_id   = $this->empresaObj->id;
    $this->notaCreditoRepository = new NotaCreditoRepository;
    $this->catalogo = new CatalogoNotaCredito;
    $this->ClienteRepository = new ClienteRepository();

  }

  public function listar(){
    if (! $this->auth->has_permission ( 'acceso' )) {
      // No, tiene permiso, redireccionarlo.
      redirect ( '/' );
    }

      $data = array();
      $this->_css();
      $this->_js();
      $this->assets->agregar_js(array(
        'public/assets/js/modules/nota_credito/listar.js',
        'public/assets/js/default/toast.controller.js'
      ));

      $breadcrumb = array( "titulo" => '<i class="fa fa-line-chart"></i> Nota de Cr&eacute;dito',
          "ruta" => array(
            0 => array(
              "nombre" => "Ventas",
              "activo" => false
            ),
            1 => array(
              "nombre" => '<b>Nota de Cr&eacute;dito</b>',
              "activo" => true
            )
          ),
          "menu" => array(
            "nombre" => "Crear",
            "url"	 => "notas_creditos/crear",
            "opciones" => array()
          )
     );

     if(!is_null($this->session->flashdata('mensaje'))){
       $mensaje = json_encode($this->session->flashdata('mensaje'));
     }else{
       $mensaje = '';
     }
     $this->assets->agregar_var_js(array(
       "toast_mensaje" => $mensaje
     ));
     $clause = array('empresa_id'=> $this->empresa_id);

      $clientes = new Cliente_orm;
      $data['clientes']= $clientes->clientesConNotaCredito($clause);
      $data['etapas'] = $this->catalogo->getEtapas();
      $data['vendedores'] = Usuario_orm::rolVendedor($clause);
      $breadcrumb["menu"]["opciones"]["#exportarNotaCredito"] = "Exportar";
      $this->template->agregar_titulo_header('Listado de Nota de Cr&eacute;dito');
      $this->template->agregar_breadcrumb($breadcrumb);
      $this->template->agregar_contenido($data);
      $this->template->visualizar($breadcrumb);
  }

  public function ocultotabla($uuid = NULL, $modulo = NULL){

    $this->assets->agregar_js(array(
      'public/assets/js/modules/nota_credito/tabla.js'
    ));
    $this->load->view('tabla');
  }


  public function ajax_listar(){
    if(!$this->input->is_ajax_request()){
      return false;
    }

    $uuid_cliente = $this->input->post("cliente_id");
    $cliente = $this->input->post('cliente',TRUE);
    $hasta = $this->input->post('hasta',TRUE);
    $desde = $this->input->post('desde',TRUE);
    $estado = $this->input->post('etapa',TRUE);
    $vendedor = $this->input->post('vendedor',TRUE);
    $clause = array('empresa_id' => $this->empresaObj->id);

    if(!empty($uuid_cliente)){
       $clienteObj  = new Buscar(new Cliente_orm,'uuid_cliente');
       $cliente = $clienteObj->findByUuid($uuid_cliente);
       $clause['cliente_id'] = $cliente->id;
    }elseif(!empty($cliente)){
      $clause['cliente_id'] = $cliente;
    }

    if(!empty($desde)) $clause['fecha_desde'] = Carbon::createFromFormat('d/m/Y',$desde,'America/Panama')->format('Y-m-d 00:00:00');
    if(!empty($hasta)) $clause['fecha_hasta'] = Carbon::createFromFormat('d/m/Y',$hasta,'America/Panama')->format('Y-m-d 23:59:59');;
    if(!empty($estado)) $clause['estado'] = $estado;
    if(!empty($vendedor)) $clause['creado_por'] = $vendedor;
    list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();

    $count = $this->notaCreditoRepository->lista_totales($clause);
    list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

    $notas_creditos = $this->notaCreditoRepository->listar($clause ,$sidx, $sord, $limit, $start);

    $response = new stdClass();
    $response->page     = $page;
    $response->total    = $total_pages;
    $response->records  = $count;

    if(!is_null($notas_creditos)){
      $i=0;
      foreach($notas_creditos as $row){
        $hidden_options = "";
        $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row->uuid_nota_credito .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
        $hidden_options = '<a href="'. base_url('notas_creditos/ver/'. $row->uuid_nota_credito) .'" data-id="'. $row->uuid_nota_credito .'" class="btn btn-block btn-outline btn-success">Ver Notas Creditos</a>';

        $etapa = $row->etapa_catalogo;
        $response->rows[$i]["id"] = $row->uuid_nota_credito;
        $response->rows[$i]["cell"] = array
          (
             $row->uuid_venta,
             '<a class="link" href="'. base_url('notas_creditos/ver/'. $row->uuid_nota_credito) .'" >'.$row->codigo.'</a>',
             '<a class="link">'.$row->cliente_nombre.'</a>',
              Carbon::createFromFormat('d/m/Y',$row->fecha,'America/Panama')->format('d/m/Y'),
             $row->total,
             '<a class="link">'.$row->nombre_vendedor.'</a>',
             $row->present()->estado_label,
             $link_option,
             $hidden_options
          );
       $i++;
      }
    }

    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))->_display();
    exit;


  }

  function crear(){
    $acceso = 1;
    $mensaje = array();

    if(!$this->auth->has_permission('acceso')){
      // No, tiene permiso, redireccionarlo.
      $acceso = 0;
      $mensaje = array('estado'=>500, 'mensaje'=>' <b>Usted no cuenta con permiso para esta solicitud</b>','clase'=>'alert-danger');
    }

    $this->_Css();
    $this->assets->agregar_css(array(
      'public/assets/css/modules/stylesheets/animacion.css'
    ));
    $this->_js();
    $this->assets->agregar_js(array(
        'public/assets/js/default/vue-validator.min.js',
        'public/assets/js/modules/nota_credito/vue.funcion.guardar.js',
        'public/assets/js/modules/nota_credito/componente.vue.js',
        'public/assets/js/modules/nota_credito/vue.comentario.js',
        'public/assets/js/modules/nota_credito/vue.crear.formulario.js',
    ));

      $data=array();
      $clause = array('empresa_id'=> $this->empresa_id);
      $this->assets->agregar_var_js(array(
        "vista" => 'crear',
        "acceso" => $acceso
      ));
    $breadcrumb = array(
      "titulo" => '<i class="fa fa-line-chart"></i> Notas de crédito: Crear',
    );
    $data['mensaje'] = $mensaje;
    $this->template->agregar_titulo_header('Notas de crédito: Crear');
    $this->template->agregar_breadcrumb($breadcrumb);
    $this->template->agregar_contenido($data);
    $this->template->visualizar();
  }

  function ver($uuid=null){
    $acceso = 1;
    $mensaje = array();

    $nota_credito = $this->notaCreditoRepository->findByUuid($uuid);
    if(!$this->auth->has_permission('acceso','notas_creditos/ver/(:any)') && !is_null($nota_credito)){
      // No, tiene permiso
        $acceso = 0;
        $mensaje = array('estado'=>500, 'mensaje'=>' <b>Usted no cuenta con permiso para esta solicitud</b>','clase'=>'alert-danger');
    }
    $nota_credito->load('items.inventario_item','items.impuesto','factura.items','cliente');
    $nota_credito->load(['comentario' =>function($query){
      $query->orderBy('created_at','desc');
    }]);
    $nota_credito->toArray();
    $this->_Css();
    $this->assets->agregar_css(array(
      'public/assets/css/modules/stylesheets/animacion.css'
    ));
    $this->_js();
    $this->assets->agregar_js(array(
        'public/assets/js/default/vue-validator.min.js',
        //'public/assets/js/default/vue-resource.min.js',
        'public/assets/js/modules/nota_credito/vue.funcion.guardar.js',
        'public/assets/js/modules/nota_credito/componente.vue.js',
        'public/assets/js/modules/nota_credito/vue.comentario.js',
        'public/assets/js/modules/nota_credito/vue.crear.formulario.js',
    ));

      $data=array();
      $clause = array('empresa_id'=> $this->empresa_id);
      $this->assets->agregar_var_js(array(
        "vista" => 'ver',
        "acceso" => $acceso,
        "nota_credito" => $nota_credito
      ));
    $breadcrumb = array(
      "titulo" => '<i class="fa fa-line-chart"></i> Notas de crédito: Ver '.$nota_credito->codigo,
    );
    $data['mensaje'] = $mensaje;
    $this->template->agregar_titulo_header('Notas de crédito: Ver');
    $this->template->agregar_breadcrumb($breadcrumb);
    $this->template->agregar_contenido($data);
    $this->template->visualizar();
  }

  function ocultoformulario(){
    $data = array();
    $clause = array('empresa_id'=> $this->empresa_id);
    $data['vendedores'] = Usuario_orm::rolVendedor($clause);
    $data['centros_contables'] = Centros_orm::transaccionalesDeEmpresa($this->empresa_id)->activos()->get();
    $data['clientes']= $this->ClienteRepository->getClientesEstadoIP($clause)->get(array('id','nombre','credito_favor'));
    //$data['clientes']= Cliente_orm::where($clause)->get(array('id','nombre','credito'));
    $data['cuentas'] = Cuentas_orm::transaccionalesDeEmpresa($this->empresa_id)->activas()->get();
    $data['etapas'] = $this->catalogo->getEtapas();
    $this->load->view('formulario', $data);
    $this->load->view('componente',$data['cuentas']);
    $this->load->view('comentarios');

  }


  function guardar(){
    //dd($_POST);
    if($_POST){
      $request = Illuminate\Http\Request::createFromGlobals();
      $array_campo = $request->input('campo');
      $datos_activos = FormRequest::data_formulario($array_campo);
      $items = $request->input('items');
      $datos_items = FormRequest::array_filter_dos_dimenciones($items);
      $comentario=[];
      if(!isset($datos_activos['id'])){
        $datos_activos['empresa_id'] = $this->empresa_id;
        $datos_activos['codigo'] = $this->_generar_codigo();
        $uuid_usuario = $this->session->userdata('huuid_usuario');
        $usuario = Usuario_orm::findByUuid($uuid_usuario);
        $comentario = ['comentario'=>$datos_activos['comentario'],'usuario_id'=>$usuario->id];
      }


      foreach ($datos_items as $key => $activos_items) {
        if(!isset($datos_items[$key]['id'])){
          $datos_items[$key]['empresa_id'] = $this->empresa_id;
        }
      }

      Capsule::beginTransaction();
      try{
        $data = array('nota_credito'=>$datos_activos,'items'=> $datos_items);
        $nota_credito = $this->notaCreditoRepository->crear($data, $comentario);
        Capsule::commit();
      }catch(Illuminate\Database\QueryException $e){
        log_message('error', __METHOD__." ->". ", Linea: ". __LINE__ ." --> ". $e->getMessage()."\r\n");
        Capsule::rollback();
        $mensaje = array('estado'=>500, 'mensaje'=>'<b>¡Error! Su solicitud no fue procesada</b> ');
        $this->session->set_flashdata('mensaje', $mensaje);
        redirect(base_url('notas_creditos/listar'));
      }

      if(!is_null($nota_credito)){
        $mensaje = array('estado' => 200, 'mensaje' =>'<b>¡&Eacute;xito!</b> Se ha guardado correctamente '.$nota_credito->codigo);
        if($nota_credito->estado == 'aprobado'){
          $transaccion = new Transaccion;
          $transaccion->hacerTransaccion($nota_credito->fresh(), new TransaccionNotaCredito);
        }

      }else{
        $mensaje = array('estado'=>500, 'mensaje'=>'<b>¡Error! Su solicitud no fue procesada</b> ');
      }
        $this->session->set_flashdata('mensaje', $mensaje);
        redirect(base_url('notas_creditos/listar'));

    }
  }

  function ajax_guardar_comentario(){
    if(!$this->input->is_ajax_request()){
      return false;
    }

    $model_id   = $this->input->post('modelId');
    $comentario = $this->input->post('comentario');
    $uuid_usuario = $this->session->userdata('huuid_usuario');
    $usuario = Usuario_orm::findByUuid($uuid_usuario);
    $comentario = ['comentario'=>$comentario,'usuario_id'=>$usuario->id];

    $nota_credito = $this->notaCreditoRepository->agregarComentario($model_id, $comentario);
    $nota_credito->load('comentario');
    $lista_comentario = $nota_credito->comentario()->orderBy('created_at','desc')->get();
    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($lista_comentario->toArray()))->_display();
    exit;
  }
  private function _generar_codigo(){
    $clause_empresa = ['empresa_id'=>$this->empresa_id];
    $numero = $this->notaCreditoRepository->lista_totales($clause_empresa);
    return $numero + 1;
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
        'public/assets/js/plugins/ckeditor/ckeditor.js',
        'public/assets/js/plugins/ckeditor/adapters/jquery.js',
        'public/assets/js/default/toast.controller.js',
        'public/assets/js/modules/nota_credito/plugins.js',
        'public/assets/js/default/operaciones.js',
        'public/assets/js/default/vue/filters/numeros.js',
  ));
  }

  private function _css(){
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
      'public/assets/css/modules/stylesheets/nota_credito.css',
    ));
  }



}

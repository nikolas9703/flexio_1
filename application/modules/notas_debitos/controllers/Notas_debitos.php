<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Flexio
 * @subpackage Controller
 * @category   Notas de Debito
 * @author     Pensanomica Team
 * @link       http://www.pensanomica.com
 * @copyright  04/18/2016
 */

use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\NotaDebito\Repository\NotaDebitoRepository;
use Flexio\Modulo\NotaDebito\Repository\CatalogoNotaDebitoRepository as CatalogoNotaDebito;
use Flexio\Library\Util\FormRequest;
use Flexio\Modulo\Proveedores\Repository\ProveedoresRepository;
use Flexio\Modulo\Contabilidad\Repository\ImpuestosRepository;
use Flexio\Modulo\FacturasCompras\Repository\FacturaCompraRepository;

//transacciones
use Flexio\Modulo\NotaDebito\Transacciones\NotasDebitosFacturas;

//utils
use Flexio\Library\Util\FlexioSession;
use Flexio\Library\Util\FlexioAssets;
use Flexio\Library\Toast;

class Notas_debitos extends CRM_Controller
{
  protected $catalogo;
  protected $notaDebitoRepository;

  //transacciones
  protected $NotasDebitosFacturas;
  protected $ProveedoresRepository;
  protected $ImpuestosRepository;
  protected $FacturaCompraRepository;

  //utils
  protected $FlexioSession;
  protected $FlexioAssets;
  protected $Toast;

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
    $this->load->model('facturas_compras/Facturas_compras_orm');
    $this->load->model('pagos/Pagos_orm');
    Carbon::setLocale('es');
    setlocale(LC_TIME, 'Spanish');
    $uuid_empresa = $this->session->userdata('uuid_empresa');
    $empresaObj  = new Buscar(new Empresa_orm,'uuid_empresa');
    $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
    $this->empresa_id   = $this->empresaObj->id;
    $this->notaDebitoRepository = new NotaDebitoRepository;
    $this->catalogo = new CatalogoNotaDebito;
    $this->ProveedoresRepository = new ProveedoresRepository();
    $this->ImpuestosRepository = new ImpuestosRepository;
    $this->FacturaCompraRepository = new FacturaCompraRepository;
    $empresaObj = new Buscar(new Empresa_orm,'uuid_empresa');
    //$usuario = Usuario_orm::findByUuid($uuid_usuario);
    //$this->id_usuario = $usuario->id;

        //trasancciones
        $this->NotasDebitosFacturas = new NotasDebitosFacturas();

        //utils
        $this->FlexioSession = new FlexioSession;
        $this->FlexioAssets = new FlexioAssets;
        $this->Toast = new Toast;
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
        'public/assets/js/modules/nota_debito/listar.js',
        'public/assets/js/default/toast.controller.js'
      ));

      $breadcrumb = array( "titulo" => '<i class="fa fa-shopping-cart"></i> Notas de d&eacute;bito',
          "ruta" => array(
            0 => array(
              "nombre" => "Compras",
              "activo" => false
            ),
            1 => array(
              "nombre" => '<b>Notas de d&eacute;bito</b>',
              "activo" => true
            )
          ),
          "menu" => array(
            "nombre" => "Crear",
            "url"	 => "notas_debitos/crear",
            "opciones" => array()
          )
     );

     $this->assets->agregar_var_js(array(
       "flexio_mensaje" => Toast::getStoreFlashdata()
     ));
     $clause = array('empresa_id'=> $this->empresa_id);

      $proveedores = new Proveedores_orm;

      $data['proveedores'] = $proveedores->proveedoresConNotaDebito($clause);
      $data['etapas'] = $this->catalogo->getEtapas();
      $data['vendedores'] = Usuario_orm::rolVendedor($clause);
      $breadcrumb["menu"]["opciones"]["#exportarNotaDebito"] = "Exportar";
      $this->template->agregar_titulo_header('Listado de Nota de D&eacute;bito');
      $this->template->agregar_breadcrumb($breadcrumb);
      $this->template->agregar_contenido($data);
      $this->template->visualizar($breadcrumb);
  }

  public function ocultotabla($sp_string_var = '')
  {
      $sp_array_var = explode('=', $sp_string_var);
      if (count($sp_array_var) == 2) {

          $this->assets->agregar_var_js(array(
              $sp_array_var[0] => $sp_array_var[1]
          ));

      }

      //heredado de estrutura vieja
      if (preg_match("/proveedores/i", $this->router->fetch_class())) {
        $this->assets->agregar_var_js(array(
          "sp_proveedor_uuid" => $sp_string_var
        ));
      }

      $this->FlexioAssets->add('js', ['public/assets/js/modules/nota_debito/tabla.js']);
      $this->load->view('tabla');
  }


  public function ajax_listar(){
    if(!$this->input->is_ajax_request()){
      return false;
    }

    $uuid_cliente = $this->input->post("cliente_id");
    $proveedor_uuid = $this->input->post('proveedor_uuid',TRUE);
    $hasta = $this->input->post('hasta',TRUE);
    $desde = $this->input->post('desde',TRUE);
    $estado = $this->input->post('etapa',TRUE);
    $vendedor = $this->input->post('vendedor',TRUE);
    $codigo = $this->input->post('codigo',TRUE);
    $no_nota_credito = $this->input->post('no_nota_credito',TRUE);
    $clause = array('empresa_id' => $this->empresaObj->id);
    $montos_de  = $this->input->post('monto1', true);
    $montos_a   = $this->input->post('monto2', true);

    if(!empty($proveedor_uuid)){
       $proveedor = $this->ProveedoresRepository->findByUuid($proveedor_uuid);
       $clause['proveedor_id'] = $proveedor->id;
    }

    //filtros de centros contables del usuario
    $centros = $this->FlexioSession->usuarioCentrosContables();
    if(!in_array('todos', $centros))
    {
        $clause['centros_contables'] = $centros;
    }

    if(!empty($desde)) $clause['fecha_desde'] = Carbon::createFromFormat('d/m/Y',$desde,'America/Panama')->format('Y-m-d 00:00:00');
    if(!empty($hasta)) $clause['fecha_hasta'] = Carbon::createFromFormat('d/m/Y',$hasta,'America/Panama')->format('Y-m-d 23:59:59');;
    if(!empty($estado)) $clause['estado'] = $estado;
    if(!empty($codigo)) $clause['codigo'] = $codigo;
    if(!empty($no_nota_credito)) $clause['no_nota_credito'] = $no_nota_credito;
    if(!empty($vendedor)) $clause['creado_por'] = $vendedor;

    if(!empty($montos_de)){
       $clause['montos_de'] = $montos_de;
    }
    if(!empty($montos_a)){
        $clause['montos_a'] = $montos_a;
    }

    list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();

    $count = $this->notaDebitoRepository->lista_totales($clause);
    list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

    $notas_debitos = $this->notaDebitoRepository->listar($clause ,$sidx, $sord, $limit, $start);

    $response = new stdClass();
    $response->page     = $page;
    $response->total    = $total_pages;
    $response->records  = $count;

    if(!is_null($notas_debitos)){
      $i=0;
      foreach($notas_debitos as $row){
         $hidden_options = "";
        $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row->uuid_nota_debito .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
        $hidden_options = '<a href="'. base_url('notas_debitos/ver/'. $row->uuid_nota_debito) .'" data-id="'. $row->uuid_nota_debito .'" class="btn btn-block btn-outline btn-success">Ver Notas Debitos</a>';
        $proveedor = $row->proveedor;
        $vendedor = $row->vendedor;
        $etapa = $row->etapa_catalogo;

         $response->rows[$i]["id"] = $row->uuid_nota_debito;
        $response->rows[$i]["cell"] = array(
           $row->uuid_venta,
           '<a class="link" href="'. base_url('notas_debitos/ver/'. $row->uuid_nota_debito) .'" >'.$row->codigo.'</a>',
           $row->no_nota_credito,
           isset($proveedor->nombre)?'<a class="link">'.$proveedor->nombre.'</a>':'',
            $row->fecha,
           '<label class="totales-success">$' . number_format($row->total, 2) .'</label>',
           isset($vendedor->nombre) ? '<a class="link">'.$vendedor->nombre.' '.$vendedor->apellido.'</a>' : '',
           '<label class="label label-'.$etapa->color_label.'">'.$etapa->valor.'</label>',
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

  public function crear()
  {
      //permisos
      $acceso = $this->auth->has_permission('acceso');
      $this->Toast->runVerifyPermission($acceso);

      //assets
      $this->FlexioAssets->run();//css y js generales
      $this->FlexioAssets->add('css', ['public/assets/css/modules/stylesheets/nota_debito.css']);
      $this->FlexioAssets->add('js', ['public/assets/js/default/operaciones.js']);
      $this->FlexioAssets->add('vars', [
          "vista" => 'crear',
          "acceso" => $acceso ? 1 : 0
      ]);

      //breadcrumb
      $breadcrumb = [
          "titulo" => '<i class="fa fa-shopping-cart"></i> Notas de d&eacute;bito: Crear ',
          "ruta" => [
              ["nombre" => "Compras", "activo" => false],
              ["nombre" => "Notas de débito", "activo" => false, "url" => 'notas_debitos/listar'],
              ["nombre" => "<b>Crear</b>", "activo" => true]
          ],
      ];

      //render
      $this->template->agregar_titulo_header('Notas de d&eacute;bito: Crear');
      $this->template->agregar_breadcrumb($breadcrumb);
      $this->template->agregar_contenido([]);
      $this->template->visualizar();
  }

  public function ver($uuid=null)
  {
      //permisos
      $acceso = $this->auth->has_permission('acceso');
      $this->Toast->runVerifyPermission($acceso);

      //registros
      $nota_debito = $this->notaDebitoRepository->findByUuid($uuid);

      //falta empezable
      $empezable = collect([
          "type" => $nota_debito->tipo,
          "facturas" => !empty($nota_debito->factura_id) ? [0=>['id'=>$nota_debito->factura_id,'nombre'=>$nota_debito->proveedor->nombre ." - ". $nota_debito->factura->codigo]] : [],
          "id" => !empty($nota_debito->factura_id) ? $nota_debito->factura_id : ''
      ]);

      //assets
      $this->FlexioAssets->run();//css y js generales
      $this->FlexioAssets->add('css', ['public/assets/css/modules/stylesheets/nota_debito.css']);
      $this->FlexioAssets->add('js', ['public/assets/js/default/operaciones.js']);
      $this->FlexioAssets->add('vars', [
          "vista" => 'ver',
          "acceso" => $acceso ? 1 : 0,
          "nota_debito" => $this->notaDebitoRepository->getCollectionNotaDebito($nota_debito),
          "empezable" => $empezable
      ]);
      //breadcrumb
      $breadcrumb = [
          "titulo" => '<i class="fa fa-shopping-cart"></i> Notas de d&eacute;bito: '.$nota_debito->codigo,
          "ruta" => [
              ["nombre" => "Compras", "activo" => false],
              ["nombre" => "Notas de débito", "activo" => false, "url" => 'notas_debitos/listar'],
              ["nombre" => "<b>Detalle</b>", "activo" => true]
          ],
      ];

      //render
      $this->template->agregar_titulo_header('Notas de d&eacute;bito: Detalle');
      $this->template->agregar_breadcrumb($breadcrumb);
      $this->template->agregar_contenido([]);
      $this->template->visualizar();
  }

  public function ocultoformulario()
  {
    $data = array();
    $clause = array('empresa_id'=> $this->empresa_id);
    $clause2 = ['empresa_id'=>$this->empresa_id,'ordenables'=>true];

    $this->FlexioAssets->add('js', ['public/resources/compile/modulos/notas_debitos/formulario.js']);
    $this->FlexioAssets->add('vars',[
      'proveedores' => $this->ProveedoresRepository->getCollectionProveedores($this->ProveedoresRepository->get($clause2)),
      'centros_contables' => Centros_orm::transaccionalesDeEmpresa($this->empresa_id)->activos()->get(),
      'usuarios' => Usuario_orm::listar($this->empresaObj->uuid_empresa),
      'estados' => $this->catalogo->getEtapas(),
      'cuentas' => Cuentas_orm::transaccionalesDeEmpresa($this->empresa_id)->activas()->get(),
      'impuestos' => $this->ImpuestosRepository->get($clause),
      'usuario_id' => $this->FlexioSession->usuarioId(),
      'facturas' => $this->FacturaCompraRepository->getCollectionFacturasNotaDebito($this->FacturaCompraRepository->cobradoCompletoSinNotaDebito($clause))
    ]);

    $this->load->view('formulario', $data);

  }


  public function guardar(){

    if($_POST){

      $request = Illuminate\Http\Request::createFromGlobals();
      $array_campo = $request->input('campo');
      $array_campo['tipo'] = $request['empezable_type'];
      $array_campo['factura_id'] = $request['empezable_id'];

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
        $data = array('nota_debito'=>$datos_activos,'items'=> $datos_items);

        $nota_debito = $this->notaDebitoRepository->crear($data, $comentario);

        if($nota_debito->estado == "aprobado")
        {
            $this->NotasDebitosFacturas->haceTransaccion($nota_debito);
        }

      }catch(\Exception $e){
        log_message('error', __METHOD__." ->". ", Linea: ". __LINE__ ." --> ". $e->getMessage()."\r\n");
        Capsule::rollback();
        $this->Toast->setUrl('notas_debitos/listar')->run("exception",[$e->getMessage()]);
      }

      if(!is_null($nota_debito)){
        Capsule::commit();
        $this->Toast->run("success",[$nota_debito->codigo]);
      }else{
        $this->Toast->run("error");
      }

      redirect(base_url('notas_debitos/listar'));

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

    $nota_debito = $this->notaDebitoRepository->agregarComentario($model_id, $comentario);
    $nota_debito->load('comentario');
    $lista_comentario = $nota_debito->comentario()->orderBy('created_at','desc')->get();
    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($lista_comentario->toArray()))->_display();
    exit;
  }
  private function _generar_codigo(){
    $clause_empresa = ['empresa_id'=>$this->empresa_id];
    $numero = $this->notaDebitoRepository->lista_totales($clause_empresa);
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
        'public/assets/js/plugins/ckeditor/ckeditor.js',
        'public/assets/js/plugins/ckeditor/adapters/jquery.js',
        'public/assets/js/default/toast.controller.js',
        'public/assets/js/default/vue/filters/numeros.js',
        'public/assets/js/default/operaciones.js',
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
        'public/assets/css/modules/stylesheets/nota_debito.css',
    ));
  }



}

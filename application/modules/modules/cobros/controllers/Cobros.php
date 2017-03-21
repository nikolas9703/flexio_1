<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Flexio
 * @subpackage Controller
 * @category   Ordenes de Ventas
 * @author     Pensanomica Team
 * @link       http://www.pensanomica.com
 * @copyright  01/15/2016
 */

use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Strategy\Transacciones\Transaccion;
use Flexio\Modulo\FacturasVentas\Repository\FacturaVentaRepository as FacturaVentaRepository;
use Flexio\Modulo\Contratos\Repository\ContratoRepository as ContratoRepository;
use Flexio\Modulo\Cobros\Repository\CobroRepository as CobroRepository;
use Flexio\Modulo\Cobros\Repository\MetodoCobroRepository as MetodoCobroRepository;
use Flexio\Modulo\Cobros\Models\CatalogoCobro;
use Flexio\Modulo\Cobros\Transaccion\TransaccionCobro;
use Flexio\Modulo\Cajas\Repository\CajasRepository;
use Flexio\Modulo\Cobros\HttpRequest\FormGuardar;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\Cliente\Repository\ClienteRepository as ClienteRepository;

class Cobros extends CRM_Controller
{
  private $empresa_id;
  private $id_usuario;
  private $empresaObj;
  protected $facturaVentaRepository;
  protected $contratoRepository;
  protected $cobroRepository;
  protected $caja;
  private $usuario_id;
  protected $ClienteRepository;


  function __construct() {
    parent::__construct();

    Carbon::setLocale('es');
    setlocale(LC_TIME, 'Spanish');
    $uuid_empresa = $this->session->userdata('uuid_empresa');
    $empresaObj  = new Buscar(new Empresa_orm,'uuid_empresa');
    $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
	$this->id_usuario   = $this->session->userdata("huuid_usuario");
    $this->usuario_id = $this->session->userdata("id_usuario");
	$this->empresa_id   = $this->empresaObj->id;

    $this->facturaVentaRepository = new FacturaVentaRepository;
    $this->contratoRepository = new ContratoRepository;
    $this->cobroRepository = new CobroRepository;
    $this->caja = new CajasRepository;
    $this->ClienteRepository = new ClienteRepository();
  }
  function index(){}
  function listar() {

    $data = array();
    if (!$this->auth->has_permission('acceso')) {
      $mensaje = array('estado'=>500, 'mensaje'=>'<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud');
      $this->session->set_flashdata('mensaje', $mensaje);
      //redirect('/');
    }


    $this->_Css();
    $this->_js();
    $this->assets->agregar_js(array(
      'public/assets/js/modules/cobros/listar.js',
      'public/assets/js/default/toast.controller.js'
    ));
    $breadcrumb = array( "titulo" => '<i class="fa fa-line-chart"></i> Cobros',
        "ruta" => array(
          0 => array(
            "nombre" => "Ventas",
            "activo" => false
          ),
          1 => array(
            "nombre" => '<b>Cobros</b>',
            "activo" => true
          )
        ),
        "menu" => array(
          "nombre" => "Crear",
          "url"	 => "cobros/crear",
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
    $data['clientes'] = $this->ClienteRepository->getClientesEstadoIP($clause)->get(array('id', 'nombre'));
    $data['etapas'] = CatalogoCobro::estados();
    $breadcrumb["menu"]["opciones"]["#exportarListaCobros"] = "Exportar";
    $this->template->agregar_titulo_header('Listado de Cobros');
    $this->template->agregar_breadcrumb($breadcrumb);
    $this->template->agregar_contenido($data);
    $this->template->visualizar($breadcrumb);
  }

  function ajax_listar() {
    if(!$this->input->is_ajax_request()){
      return false;
    }
    $clause = [];
    $jqgrid = new Flexio\Modulo\Cobros\Services\CobroJqgrid;
    $clause['empresa'] = $this->empresa_id;

    if ($this->input->post("cliente_id") <> '') {
        $client_id = (new ClienteRepository)->findByUuid($this->input->post("cliente_id"))->id;
        $clause['cliente'] = $client_id;
    }

    if (!empty($this->input->post('factura_id'))) {
        $factura = (new FacturaVentaRepository)->findByUuid($_POST['factura_id']);

        $clause['factura'] = $factura->id;
    }


    $response = $jqgrid->listar($clause);

    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
    ->set_output(json_encode($response))->_display();
    exit;

  }


  function ocultotabla($uuid_orden_venta=null) {
    $this->assets->agregar_js(array(
      'public/assets/js/modules/cobros/tabla.js'
    ));

    if (!empty($uuid_orden_venta)) {
      if(preg_match("/(=)/", $uuid_orden_venta)){
        $aux = explode('=', $uuid_orden_venta);
        $this->assets->agregar_var_js(array(
            $aux[0] => $aux[1]
        ));
      }else{
        $this->assets->agregar_var_js(array(
        "uuid_orden_venta" => $uuid_orden_venta
        ));
      }
    }

    $this->load->view('tabla');
  }

  function crear() {
    $acceso = 1;
    $mensaje = array();
    if(!$this->auth->has_permission('acceso')){
            $acceso = 0;
            $mensaje = array('estado'=>500, 'mensaje'=>'<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud','clase'=>'alert-danger');
    }

    $this->_Css();
    $this->assets->agregar_css(array(
      'public/assets/css/modules/stylesheets/animacion.css'
    ));
    $this->_js();
    $this->assets->agregar_js(array(
      'public/assets/js/default/formatos.js',
      'public/assets/js/default/vue/directives/new-select2.js',
      'public/resources/compile/modulos/cobros/formulario.js',
    ));

    $this->assets->agregar_var_js(array(
      "vista" => 'crear',
      "acceso" => $acceso == 0? $acceso : $acceso,
    ));
    $this->desde_empezables();
    $data['mensaje'] = $mensaje;
    $breadcrumb = array(
      "titulo" => '<i class="fa fa-line-chart"></i> Cobro: Crear ',
      "ruta" => array(
          0 => array(
              "nombre" => "Ventas",
              "activo" => true
          ),
          1 => array(
              "nombre" => '<b>Cobros</b>',
              "activo" => true,
              "url" => "cobros/listar"

          ),
          2 => array(
              "nombre" => 'Crear',
              "activo" => false
          )
      )
    );

    $this->template->agregar_titulo_header('Crear Cobro');
    $this->template->agregar_breadcrumb($breadcrumb);
    $this->template->agregar_contenido($data);
    $this->template->visualizar();

  }

  function ver($uuid=NULL) {
    $mensaje = array();
    $acceso = 1;
    if(!$this->auth->has_permission('acceso','cobros/ver/(:any)')){
            $acceso = 0;
            $mensaje = array('estado'=>500, 'mensaje'=>'<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud','clase'=>'alert-danger');
    }

    $this->_Css();
    $this->assets->agregar_css(array(
      'public/assets/css/modules/stylesheets/animacion.css'
    ));
    $this->_js();
    $this->assets->agregar_js(array(
      'public/assets/js/default/vue/directives/new-select2.js',
      'public/resources/compile/modulos/cobros/formulario.js',
    ));

    $cobro = $this->cobroRepository->findByUuid($uuid);

    if(is_null($uuid) || is_null($cobro)){
      $mensaje = array('estado'=>500, 'mensaje'=>'<strong>¡Error!</strong> Su solicitud no fue procesada');
      $this->session->set_flashdata('mensaje', $mensaje);
      redirect(base_url('cobro/listar'));
    }

    $this->assets->agregar_var_js(array(
        "vista"     => 'ver',
        "acceso"    => $acceso == 0? $acceso : $acceso,
        "hex_cobro" => $cobro->uuid_cobro
    ));

    $data['mensaje'] = $mensaje;
    $breadcrumb = array(
      "titulo" => '<i class="fa fa-line-chart"></i> Cobro: '.$cobro->codigo,
      "ruta" => array(
          0 => array(
              "nombre" => "Ventas",
              "activo" => false
          ),
          1 => array(
              "nombre" => 'Cobros',
              "activo" => false,
              "url" => "cobros/listar"

          ),
          2 => array(
              "nombre" => "<b>Detalle</b>",
              "activo" => true
          )
      )
    );

    $this->template->agregar_titulo_header('Ver Cobro');
    $this->template->agregar_breadcrumb($breadcrumb);
    $this->template->agregar_contenido($data);
    $this->template->visualizar();

  }
    public function guardar(){

        if ($_POST) {
            $accion = new FormGuardar();
            try {
                $cobro = $accion->guardar();
                $mensaje = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente ' . $cobro->codigo);
            } catch (\Exception $e) {
                log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
                $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error! Su solicitud no fue procesada</b> ');
            }
            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('cobros/listar'));
        }

    }

  function ajax_formulario_catalogos(){
    if(!$this->input->is_ajax_request()){
      return false;
    }
    $empresa = ['empresa_id' => $this->empresa_id];
    $bancos = new Flexio\Modulo\Bancos\Repository\BancosRepository;
    $config_cuentas_bancos = new Flexio\Modulo\ConfiguracionContabilidad\Repository\CuentaBancoRepository;
    $cajas = $this->caja->getAll(array_merge($empresa,['estado_id'=>1]));

    $catalogos = [];
    $catalogos['bancos'] = $bancos->get();
    $catalogos['estados'] = CatalogoCobro::estados();
    $catalogos['tipo_cobro'] = CatalogoCobro::tipoCobro();
    $catalogos["cuenta_bancos"] = [];
    $catalogos['cajas'] = $cajas;
    $catalogos['metodo_cobros'] = CatalogoCobro::metodoCobro();

    if($config_cuentas_bancos->tieneCuenta($empresa)) {
      $cuenta_banco = $config_cuentas_bancos->cuentasConfigBancos($empresa);
      $catalogos["cuenta_bancos"] = $cuenta_banco;
     }

    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
      ->set_output(collect($catalogos))->_display();
    exit;

  }

  function ajax_get_cobro(){
      if(!$this->input->is_ajax_request()){
        return false;
      }
      $deposito = ["Flexio\Modulo\Contabilidad\Models\Cuentas"=>'banco','Flexio\Modulo\Cajas\Models\Cajas'=>'caja'];
      $empezar = ['cliente'=>'Flexio\Modulo\Cliente\Models\Cliente','contrato_venta'=>'Flexio\Modulo\Contratos\Models\Contrato','factura'=>'Flexio\Modulo\FacturasVentas\Models\FacturaVenta','orden_trabajo' => 'Flexio\Modulo\OrdenesTrabajo\Models\OrdenTrabajo'];
      $empz = array_flip($empezar);
      $uuid = $this->input->post('uuid');
      $cobro = $this->cobroRepository->findByUuid($uuid);
      $cobro->load('metodo_cobro','cliente','cobros_facturas','empezable','landing_comments');
      $cobro->load(['factura_cobros.cobros'=>function($cob){
          $cob->where('estado','aplicado');
      }]);


      //$cobro->depositable_type = $deposito[$cobro->depositable_type];
      //$cobro->empezable_type = $empz[$cobro->empezable_type];

      $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
          ->set_output($cobro)->_display();
      exit;
  }

  function ajax_info_cobro() {
      $uuid = $this->input->post('uuid');
      $cobro = $this->cobroRepository->findByUuid($uuid);
      $cobro->load('metodo_cobro','factura_cobros','cobros_facturas.cobros');
      $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
          ->set_output(json_encode($cobro->toArray()))->_display();
      exit;
  }

  /**
   * catalagos de cobros
   */
   function facturas(){
       if(!$this->input->is_ajax_request()){
           return false;
       }
       $this->empresa_id;
       $clause = [];
       $facturasObj = new Flexio\Modulo\FacturasVentas\Repository\FacturaVentaRepositorio;
       $id = $this->input->post('id');
       if(empty($id)){
           $faturas = $facturasObj->getFacturas($this->empresa_id)->paraCobrar()->conClienteActivo()->fetch();
       }else{
           $faturas = $facturasObj->getFacturas($this->empresa_id)->conId($id)->fetch();
       }

       $faturas->load('cliente','ordenes_ventas.anticipos');
        $faturas->load(['cobros'=>function($cob){
            $cob->where('estado','aplicado');
            $cob->with('metodo_cobro');
        }]);
       $response =  $faturas->map(function($fac){
           return collect([
               'id'=> $fac->id,
               'nombre'=> $fac->codigo ." ".$fac->cliente_nombre,
               'codigo'=> $fac->codigo,
               'fecha_desde' => $fac->fecha_desde,
               'fecha_hasta' => $fac->fecha_hasta,
               'total' => $fac->total,
               'cobros'=> $fac->cobros,
               'cliente'=> $fac->cliente,
               'ordenes_ventas' => $fac->ordenes_ventas->where('estado','facturado_completo')
           ]);
       });

       $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response))->_display();
       exit();
   }

   function catalogo_contratos_ventas(){

       if(!$this->input->is_ajax_request()){
           return false;
       }
       $clause = [];
       $contratoVentas = new Flexio\Modulo\Contratos\Repository\RepositorioContrato;
       $clause['empresa_id'] = $this->empresa_id;

       $id = $this->input->post('id');
       if(empty($id)){
         $contratoVentas = $contratoVentas->getContratos($this->empresa_id)
                                          ->conClienteActivo()
                                          ->conFacturas()
                                          ->ParaCobrar()
                                          ->fetch();
      }else{
         $contratoVentas = $contratoVentas->getContratos($this->empresa_id)->conId($id)->fetch();
      }

       $contratoVentas->load('cliente','anticipos');

       $response = $contratoVentas->map(function($sub){
         return collect([
           'id'=> $sub->id,
           'nombre' => $sub->codigo .' - '.$sub->cliente_nombre,
           'cliente'  => $sub->cliente,
           'facturas' => $sub->facturas->load(['cobros' =>function($cob){$cob->where('estado','aplicado');$cob->with('metodo_cobro');}]),
           'anticipos'=> $sub->anticipos
         ]);
       });
       $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response))->_display();
       exit();
   }

   function catalogo_clientes_activo(){
       if(!$this->input->is_ajax_request()){
           return false;
       }

       $clause = ['empresa_id'=>$this->empresa_id];
       $clientesObj = new Flexio\Modulo\Cliente\Repository\ClienteRepositorio;
       $id = $this->input->post('id');
       if(empty($id)){
         $clientes = $clientesObj->getClientes($this->empresa_id)
                                 ->activos()
                                 ->conFacturas()
                                 ->paraCrearCobros()->fetch();
        }else{
         $clientes = $clientesObj->getClientes($this->empresa_id)->conId($id)->fetch();
        }
       $clientes->load('anticipo_cliente','anticipos');

       $clientes = $clientes->map(function($cliente){
           return collect([
               'id'=> $cliente->id,
               'nombre' => $cliente->codigo . ' - '.$cliente->nombre,
               'saldo_pendiente' => $cliente->saldo_pendiente,
               'anticipos' => $cliente->anticipos,
               'anticipos_cliente' => $cliente->anticipo_cliente,
               'credito_favor' => $cliente->credito_favor,
               'facturas'=> $this->loadFacturaInfo($cliente)
             ]);
       });

       $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
           ->set_output($clientes)->_display();
       exit;
   }

   protected function  loadFacturaInfo($cliente){
     $cliente->facturas->load(['cobros' =>
             function($cob){
                $cob->where('estado','aplicado');
                $cob->with('metodo_cobro');
            }]);
     $cliente->facturas->load('contratos.anticipos','ordenes_ventas.anticipos');
     return $cliente->facturas;
   }

   function orden_trabajo(){
       if(!$this->input->is_ajax_request()){
           return false;
       }

       $ordenTrabajoObj = new Flexio\Modulo\OrdenesTrabajo\Repository\OrdenTrabajoRepositorio;
       $id = $this->input->post('id');
       if(empty($id)){
         $orden_trabajo = $ordenTrabajoObj->getOrdenesTrabajos($this->empresa_id)
         ->debeTenerFacturasParaCobrar()
         ->conFacturasParaCobrar()
         ->facturado()
         ->fetch();
        }else{
         $orden_trabajo = $ordenTrabajoObj->getOrdenesTrabajos($this->empresa_id)->conId($id)->fetch();
        }
        $orden_trabajo = $orden_trabajo->map(function($orden){
            return collect([
                'id'=> $orden->id,
                'nombre' => $orden->codigo . ' - '.$orden->cliente_nombre,
                'cliente'=> $orden->cliente,
                'facturas' => $orden->facturas->load('cobros')
            ]);
        });
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output($orden_trabajo)->_display();
        exit;
   }

   private function desde_empezables(){
       $request = Illuminate\Http\Request::capture();
       if($request->has('factura')){

          $facturasObj = new Flexio\Modulo\FacturasVentas\Repository\FacturaVentaRepositorio;
          $factura = $facturasObj->getFacturas($this->empresa_id)->conUUID($request->input('factura'))->fetch()->first();

         if(!is_null($factura)){
           $this->assets->agregar_var_js(array(
             'referenciaUrl' =>collect(['factura'=>$factura->id])
           ));
         }
       }
   }

    private function _Css() {
      $this->assets->agregar_css(array(
        'public/assets/css/default/ui/base/jquery-ui.css',
        'public/assets/css/default/ui/base/jquery-ui.theme.css',
        'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
        'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
        'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
        'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
        'public/assets/css/plugins/bootstrap/select2.min.css',
        'public/assets/js/plugins/jquery/sweetalert/sweetalert.css',
        'public/assets/css/modules/stylesheets/cobros.css',
      ));
    }

    private function _js() {
      $this->assets->agregar_js(array(

        'public/assets/js/plugins/ckeditor/ckeditor.js',
        'public/assets/js/plugins/ckeditor/adapters/jquery.js',
        'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
        'public/assets/js/default/jquery.inputmask.bundle.min.js',
        'public/assets/js/moment-with-locales-290.js',
        'public/assets/js/plugins/bootstrap/select2/select2.min.js',
        'public/assets/js/plugins/bootstrap/select2/es.js'
      ));
    }

}

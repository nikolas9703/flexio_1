<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Flexio
 * @subpackage Controller
 * @category   Anticipos
 * @author     Pensanomica Team
 * @link       http://www.pensanomica.com
 * @copyright  10/15/2016
 */

use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

use Flexio\Modulo\ConfiguracionContabilidad\Repository\CuentaBancoRepository as CuentaBanco;
use Flexio\Modulo\Proveedores\Repository\ProveedoresRepository;
use Flexio\Modulo\Usuarios\Repository\UsuariosRepository;

class Anticipos extends CRM_Controller
{
  private $empresa_id;
  private $usuario_id;
  private $empresaObj;

  protected $cuenta_banco;
  protected $modulo_padre;
  private $proveedoresRep;
  public $tipo;
  protected $UsuariosRepository;

    function __construct(){
        parent::__construct();

        Carbon::setLocale('es');
        setlocale(LC_TIME, 'Spanish');
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresaObj  = new Buscar(new Empresa_orm,'uuid_empresa');
        $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
	    $this->empresa_id   = $this->empresaObj->id;
        $this->cuenta_banco = new CuentaBanco;
        $this->proveedoresRep = new ProveedoresRepository();
        $this->setPadreModulo();
        $this->load->module(array('documentos'));
        $this->UsuariosRepository = new UsuariosRepository;
        $this->usuario_id = $this->session->userdata("id_usuario");
    }

    function listar(){

        $data = array();
        if (!$this->auth->has_permission('acceso')) {
            $mensaje = array('tipo'=>"error", 'mensaje'=>'<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud');
            $this->session->set_flashdata('mensaje', $mensaje);
        }

        $this->_Css();
        $this->assets->agregar_css(array(
          'public/assets/js/plugins/jquery/context-menu/jquery.contextMenu.min.css'
        ));
        $this->_js();
        $this->assets->agregar_js(array(
            'public/assets/js/plugins/jquery/context-menu/jquery.contextMenu.min.js',
            'public/assets/js/modules/anticipos/routes.js',
            'public/assets/js/modules/anticipos/listar.js',
            'public/assets/js/modules/anticipos/menuEstado.js',
        ));
        $empresa = ['empresa_id' => $this->empresa_id];
        $catalogo_anticipo = new Flexio\Modulo\Anticipos\Repository\CatalogoAnticipo;
        $catalogo_proveedores = new Flexio\Modulo\Proveedores\Repository\ProveedoresRepository;
        $catalogo_clientes = new Flexio\Modulo\Cliente\Repository\ClienteRepository;

        if($this->modulo_padre == 'compras'){
            $anticipable = $catalogo_proveedores->get($empresa);
            $this->tipo = $this->modulo_padre;
        }else{
            $clientesActivos = $catalogo_clientes->getClientesEstadoActivo($empresa)->get();
            $anticipable = $catalogo_clientes->clienteCatalogo($clientesActivos);
            $this->tipo = $this->modulo_padre;
        }



        $breadcrumb = array( "titulo" => '<i class="fa '.$this->icono().'"></i> Anticipos',
            "ruta" => array(
                0 => array(
                    "nombre" => ucfirst($this->modulo_padre),
                    "activo" => true
                ),
                1 => array(
                    "nombre" => '<b>Anticipos</b>',
                    "activo" => true

                )
            ),
            "menu" => array(
                "nombre"    => "Crear",
                "url"       => "anticipos/crear",
                "opciones" => array()
            )
        );

        if(!is_null($this->session->flashdata('mensaje'))){
            $mensaje = $this->session->flashdata('mensaje');
        }else{
            $mensaje = [];
        }
        $this->assets->agregar_var_js(array(
            "flexio_mensaje" =>  collect($mensaje)
        ));

        $data['relacion_anticipo'] = [1=>['etiqueta'=>'cliente','valor'=>'A Cliente'],
                                      0=>['etiqueta'=>'proveedor','valor'=>'A Proveedor']
                                     ];
        $data['anticipables']    = $anticipable;
        $data['etapas']         = $catalogo_anticipo->getEstados();
        $data['metodos']    = $catalogo_anticipo->getMetodoAnticipo();
        $data['anticipable_type'] = $this->owner();

        $breadcrumb["menu"]["opciones"]["#cambiarEstadoAnticipo"] = "Cambiar estado";
        $breadcrumb["menu"]["opciones"]["#exportarListaAnticipo"] = "Exportar";
        $this->template->agregar_titulo_header('Listado de Anticipo');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }


    function ajax_listar(){
        if(!$this->input->is_ajax_request()){
            return false;
        }
        $clause = [];
        $jqgrid = new Flexio\Modulo\Anticipos\Services\AnticipoJqgrid;
        $clause['empresa'] = $this->empresa_id;
        $modulo_id 	= $this->input->post('modulo_id', true);
        $anticipable_type = $this->input->post('anticipable_type');

        if(empty($modulo_id)){
        if(empty($anticipable_type))
            $clause['anticipable_type'] = $this->anticipable_type();
        }

        $response = $jqgrid->listar($clause);

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))->_display();
        exit;
    }

    function ocultotabla($modulo_id = null){

        $this->assets->agregar_js(array(
            'public/assets/js/modules/anticipos/tabla.js'
        ));
        if (!empty($modulo_id)) {
            if(is_array($modulo_id))
            {
                $this->assets->agregar_var_js([
                    "campo" => collect($modulo_id)
                ]);
            }
            elseif (preg_match("/(ordenes)/i", $this->router->fetch_class())) {
                //dd($modulo_id);
                $this->assets->agregar_var_js(array(
                    "orden_id" => $modulo_id
                ));
            }else{
                $provedor = $this->proveedoresRep->findByUuid($modulo_id);
                $this->assets->agregar_var_js(array(
                    "proveedor_id" => $provedor->id
                ));
            }
        }
        $this->load->view('tabla');
    }

    public function crear(){
        $acceso = 1;
        $mensaje = array();
        $request = Illuminate\Http\Request::capture();
        if(!$this->auth->has_permission('acceso')){
            $acceso = 0;
            $mensaje = array('estado'=>500, 'mensaje'=>'<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud','clase'=>'alert-danger');
        }

        if($this->modulo_padre == "compras"){
            $this->desde_empezables_compras($request);
            $this->tipo = $this->modulo_padre;
        }else{
            $this->desde_empezables_ventas($request);
            $this->tipo = $this->modulo_padre;
        }

        $this->_Css();
        $this->_js();
        $this->assets->agregar_js(array(
          'public/assets/js/default/vue/directives/new-select2.js',
          'public/resources/compile/modulos/anticipos/formulario.js'
        ));

        $this->assets->agregar_var_js(array(
            "vista"             => 'crear',
            "acceso"            => $acceso == 0? $acceso : $acceso,
            'usuario_id' => $this->usuario_id,
        ));

        $data['mensaje'] = $mensaje;
        $breadcrumb = array(
            "titulo" => '<i class="fa '.$this->icono().'"></i> Anticipo: Crear ',
            "ruta" => array(
                0 => array(
                    "nombre" => ucfirst($this->modulo_padre),
                    "activo" => false
                ),
                1 => array(
                    "nombre" => 'Anticipos',
                    "activo" => false,
                    "url" => "anticipos/listar"
                ),
                2 => array(
                    "nombre" => '<b>Crear</b>',
                    "activo" => true
                )
            )
        );

        $this->template->agregar_titulo_header('Crear Anticipo');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();

    }

    function ver($uuid=NULL){
        $mensaje = array();
        $acceso = 1;
        $subpanels = [];
        if(!$this->auth->has_permission('acceso','anticipos/ver/(:any)')){
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
            'public/resources/compile/modulos/anticipos/formulario.js'
        ));

        $anticipoObj    = new Flexio\Modulo\Anticipos\Repository\AnticipoRepository;
        $anticipo       = $anticipoObj->findByUuid($uuid);
        if(is_null($uuid) || is_null($anticipo)){
            $mensaje = array('estado'=>500, 'mensaje'=>'<strong>¡Error!</strong> Su solicitud no fue procesada');
            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('anticipos/listar'));
        }

        if($this->modulo_padre =="compras"){
          $subpanels = [
            'pago'=>['anticipo'=>$anticipo->id],
            'documento'=>['anticipo'=>$anticipo->id]
          ];
          $this->tipo = $this->modulo_padre;
        }

        $clause  = array('empresa_id'=> $this->empresa_id);

        $this->assets->agregar_var_js(array(
            "vista"     => 'ver',
            "acceso"    => $acceso == 0? $acceso : $acceso,
            "hex_anticipo" => $anticipo->uuid_anticipo,
            'usuario_id' => $this->usuario_id,
        ));
        $data['modulo'] = $this->modulo_padre;
        $data['subpanels'] = $subpanels;
        $data['mensaje']        = $mensaje;
        $breadcrumb = array(
            "titulo" => '<i class="fa '.$this->icono().'"></i> Anticipo: '.$anticipo->codigo,
            "ruta" => array(
                0 => array(
                    "nombre" =>  ucfirst($this->modulo_padre) ,
                    "activo" => false
                ),
                1 => array(
                    "nombre" => 'Anticipos',
                    "activo" => false,
                    "url" => "anticipos/listar"
                ),
                2 => array(
                    "nombre" =>'<b>Detalle</b>',
                    "activo" => true
                )
            )
        );

        $this->template->agregar_titulo_header('Ver Anticipo');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();

    }

    function ocultoformulario(){
        $this->load->view('formulario');
    }

    function ajax_catalogo_formulario_anticipo(){
      if (!$this->input->is_ajax_request()) {
        return false;
      }
      $clause = ['empresa_id'=>$this->empresa_id];
      $modulo = $this->input->post('modulo');
      $empresa = ['empresa_id' => $this->empresa_id];
      $catalogo_anticipo = new Flexio\Modulo\Anticipos\Repository\CatalogoAnticipo;

      $bancos = new Flexio\Modulo\Bancos\Repository\BancosRepository;
      $cajas = new Flexio\Modulo\Cajas\Repository\CajasRepository;
      //repositorio catalogo
      //repositorio proveedores
      $catalogo = [];
      $catalogo['estados'] = $catalogo_anticipo->getEstados();
      //$catalogo['metodo_anticipo'] = $catalogo_anticipo->getMetodoAnticipo();
      //$catalogo['bancos'] = $bancos->get();
      //$catalogo['caja'] = $cajas->getAll(array_merge($empresa,['estado_id'=>1]));
      //$catalogo["depositable"] = [];
      // hacer dinamico por el tipo se refiere banco o caja
      if($this->modulo_padre =="compras"){
          $tipoable = [
            ['etiqueta' => 'banco', 'valor'=>'Pagar de cuenta de banco']
          ];
      }else{
           $tipoable = [
               ['etiqueta' => 'banco', 'valor'=>'Depositar en cuenta de banco']
        ];
      }
      //$catalogo['tipoable'] = $tipoable;

      if($this->cuenta_banco->tieneCuenta($empresa)) {
        $cuenta_banco = $this->cuenta_banco->cuentasConfigBancos($empresa);
        //$catalogo["depositable"] = $cuenta_banco;
       }
       //hacer dinamico se refiere proveedores o clientes
       $catalogo['anticipables'] = $this->catalogo_anticipable($modulo);
       $catalogo['compradores'] = $this->UsuariosRepository->getCollectionUsuarios($this->UsuariosRepository->get($clause));


      $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($catalogo))->_display();
      exit;
    }

   function guardar(){

       $FormRequest = new Flexio\Modulo\Anticipos\FormRequest\GuardarAnticipo;
       try{
          $anticipo = $FormRequest->guardar();
          $mensaje = array('tipo'=>"success", 'mensaje'=>'<b>¡&Eacute;xito!</b> Se ha guardado correctamente' ,'titulo'=>'Anticipo '.$anticipo->codigo);
       }catch(\Exception $e){
           log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
           $mensaje = array('tipo' => 'error', 'mensaje' => '<b>¡Error! Su solicitud no fue procesada</b>', 'titulo' => "Anticipo");
       }

       $this->session->set_flashdata('mensaje', $mensaje);
       redirect(base_url('anticipos/listar'));
   }

   function ajax_cambiar_estado(){

       if (!$this->input->is_ajax_request()) {
        return false;
       }
        $mensaje=[];
        $FormRequest = new Flexio\Modulo\Anticipos\FormRequest\GuardarAnticipo;
       try{
          $anticipo = $FormRequest->guardar();
          $mensaje = array('estado'=>$anticipo->present()->estado_label,'monto' =>$anticipo->present()->monto);
       }catch(\Exception $e){
           log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");

       }

       $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
       ->set_output(json_encode($mensaje))->_display();
       exit;
   }

   function ajax_cambiar_estados(){
     if (!$this->input->is_ajax_request()) {
      return false;
     }
      $mensaje=[];
      $FormRequest = new Flexio\Modulo\Anticipos\FormRequest\GuardarAnticiposEstados;
     try{
        $anticipo = $FormRequest->guardar();
        //formatear el response
        $res = $anticipo->map(function($ant){
          return[
            'id'=>$ant->id,'estado'=>$ant->present()->estado_label,'monto' =>$ant->present()->monto
          ];
        });
     }catch(\Exception $e){
         log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");

     }

     $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
     ->set_output($res)->_display();
     exit;
   }



   public function exportar()
   {
       if(empty($_POST)){
           return false;
       }
       $request = Illuminate\Http\Request::capture();
       $ids =  $request->input('ids', []);
       $clause = ['empresa_id' => $this->empresa_id];

       if(!empty($ids))  {
           $id = explode(",", $ids);
           $clause['id'] = $id;
       }

       $anticipoCsv = new Flexio\Modulo\Anticipos\Exportar\Csv\AnticipoCsv;
       $csv = $anticipoCsv->crearCsv($clause);

       if(!is_null($csv)){
           $csv->output("anticipos-". date('ymd') .".csv");
           die;
       }
       return false;
   }

   public function ajax_get_anticipo(){

       if (!$this->input->is_ajax_request()) {
        return false;
       }


       $tipo_deposito = ['Flexio\Modulo\Contabilidad\Models\Cuentas' => 'banco', 'Flexio\Modulo\Cajas\Models\Cajas'=> 'caja'];
       $tipo_anticipable = ['Flexio\Modulo\Proveedores\Models\Proveedores' => 'proveedor','Flexio\Modulo\Cliente\Models\Cliente'=>'cliente'];
       $empezable = ['orden_compra' => 'Flexio\Modulo\OrdenesCompra\Models\OrdenesCompra',
       'subcontrato'=>'Flexio\Modulo\SubContratos\Models\SubContrato'];

       $uuid =$this->input->post('uuid');
       $anticipoObj    = new Flexio\Modulo\Anticipos\Repository\AnticipoRepository;
       $anticipo       = $anticipoObj->findByUuid($uuid);
       $anticipo->tipo_deposito= !empty($tipo_deposito[$anticipo->depositable_type])?$tipo_deposito[$anticipo->depositable_type]:'';
       $anticipo->tipo_anticipable =  $tipo_anticipable[$anticipo->anticipable_type];

       $anticipo = $this->cargar_relaciones($anticipo);
       $anticipo->anticipable;
       if($anticipo->tipo_anticipable =="proveedor"){
           $proveedor = collect([
           'id'=> $anticipo->anticipable->uuid_proveedor,
           'credito' => $anticipo->anticipable->credito,
           'saldo_pendiente' => $anticipo->anticipable->saldo_pendiente,
           'nombre' => $anticipo->anticipable->nombre,
           'retiene_impuesto' => $anticipo->anticipable->retiene_impuesto,
           'proveedor_id' => $anticipo->anticipable->id
           ]);
           $anticipo->{'proveedor'} = $proveedor;
       }
       
       $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
       ->set_output($anticipo)->_display();
       exit;
   }

   protected function cargar_relaciones($anticipo){

     if($this->modulo_padre =="compras"){
       $anticipo->load('landing_comments','orden_compra','subcontrato','pagos_no_anulados', 'pagos_anulados');
       $anticipo->politica = $anticipo->politica();
       return $anticipo;
     }

     $anticipo->load('landing_comments','contrato','orden_venta','anticipable');
     
     $anticipo->politica = [];
     return $anticipo;
   }

   private function owner(){

      if($this->modulo_padre =="compras"){
          return 'proveedor';
      }else if($this->modulo_padre =="ventas"){
        return 'cliente';
      }

   }

   private function anticipable_type(){

       if($this->owner() =='proveedor'){
           return 'Flexio\Modulo\Proveedores\Models\Proveedores';
       }else if($this->owner() =='cliente'){
            return 'Flexio\Modulo\Cliente\Models\Cliente';
       }

   }

   private function catalogo_anticipable($modulo){

     $empresa = ['empresa_id' => $this->empresa_id];
     
     if($modulo == 'compras'){
       //$catalogo_proveedores = new Flexio\Modulo\Proveedores\Repository\ProveedoresRepository;
       //$proveedores = $catalogo_proveedores->get($empresa);
       return [];
     }
     $catalogo_clientes = new Flexio\Modulo\Cliente\Repository\ClienteRepository;
     $clientes = $catalogo_clientes->getClientesEstadoActivo($empresa)->get();
     $formatoClientes = $catalogo_clientes->clienteCatalogo($clientes);
     return $formatoClientes;

   }

   private function desde_empezables_compras($request){

       if($request->has('proveedor')){
         $proveedorObj = new Flexio\Modulo\Proveedores\Repository\ProveedoresRepository;
          $proveedor = $proveedorObj->findByUuid($request->input('proveedor'));
         if(!is_null($proveedor)){
           $this->assets->agregar_var_js(array(
             'referenciaUrl' =>collect(['proveedor'=>$proveedor->id])
           ));
         }
       }

       if($request->has('orden_compra')){
         $ordenCompraObj = new Flexio\Modulo\OrdenesCompra\Repository\OrdenesCompraRepository;
          $orden_compra = $ordenCompraObj->findByUuid($request->input('orden_compra'));
         if(!is_null($orden_compra)){
           $this->assets->agregar_var_js(array(
             'referenciaUrl' =>collect(['orden_compra'=>$orden_compra->id])
           ));
         }
       }

       if($request->has('subcontrato')){
         $subcontratoObj = new Flexio\Modulo\SubContratos\Repository\SubContratoRepository;
          $subcontrato = $subcontratoObj->findByUuid($request->input('subcontrato'));
         if(!is_null($subcontrato)){
           $this->assets->agregar_var_js(array(
             'referenciaUrl' =>collect(['subcontrato'=>$subcontrato->id])
           ));
         }
       }
   }

   private function desde_empezables_ventas($request){
     if($request->has('cliente')){
       $clienteObj = new Flexio\Modulo\Cliente\Repository\ClienteRepository;
        $cliente = $clienteObj::findByUuid($request->input('cliente'));
       if(!is_null($cliente)){
         $this->assets->agregar_var_js(array(
           'referenciaUrl' =>collect(['cliente'=>$cliente->id])
         ));
       }
     }

     if($request->has('orden_venta')){
       $ordenVentaObj = new Flexio\Modulo\OrdenesVentas\Repository\OrdenVentaRepository;
        $orden_venta = $ordenVentaObj->findByUuid($request->input('orden_venta'));
       if(!is_null($orden_venta)){
         $this->assets->agregar_var_js(array(
           'referenciaUrl' =>collect(['orden_venta'=>$orden_venta->id])
         ));
       }
     }

     if($request->has('contrato')){
       $contratoObj = new Flexio\Modulo\Contratos\Repository\ContratoRepository;
        $contrato = $contratoObj->findByUuid($request->input('contrato'));
       if(!is_null($contrato)){
         $this->assets->agregar_var_js(array(
           'referenciaUrl' =>collect(['contrato'=>$contrato->id])
         ));
       }
     }
   }


   private function icono(){
     if($this->owner() =='proveedor'){
       return 'fa-shopping-cart';
     }
     return 'fa-line-chart';
   }

   function setPadreModulo(){
      $request = Illuminate\Http\Request::createFromGlobals();

      if($request->has('contrato')){
        $this->modulo_padre = 'ventas';
        return 'ventas';
      }else if($request->has('subcontrato')){
        $this->modulo_padre = 'compras';
        return 'compras';
      }


      return $this->modulo_padre = $this->session->userdata('modulo_padre');
   }

   public function documentos_campos() {

       return array(
           array(
               "type" => "hidden",
               "name" => "anticipo_id",
               "id" => "anticipo_id",
               "class" => "form-control",
               "readonly" => "readonly",
       ));
   }

   public function ajax_guardar_documentos() {
       if (empty($_POST)) {
           return false;
       }

       $anticipo_id = $this->input->post('anticipo_id', true);
       $anticipoObj = new Flexio\Modulo\Anticipos\Repository\AnticipoRepository;
       $anticipo        = $anticipoObj->findByUuid($anticipo_id);
       $this->documentos->subir($anticipo);
   }


    private function _Css(){
      $this->assets->agregar_css(array(
        'public/assets/css/default/ui/base/jquery-ui.css',
        'public/assets/css/default/ui/base/jquery-ui.theme.css',
        'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
        'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
        'public/assets/css/plugins/bootstrap/select2.min.css',
        'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
        'public/assets/css/plugins/jquery/jquery.fileupload.css',
      ));
    }

    private function _js(){
      $this->assets->agregar_js(array(
        'public/assets/js/plugins/ckeditor/ckeditor.js',
        'public/assets/js/plugins/ckeditor/adapters/jquery.js',
        'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
        'public/assets/js/plugins/jquery/fileupload/jquery.fileupload.js',
        'public/assets/js/default/jquery.inputmask.bundle.min.js',
        'public/assets/js/moment-with-locales-290.js',
        'public/assets/js/plugins/bootstrap/select2/select2.min.js',
        'public/assets/js/plugins/bootstrap/select2/es.js',
        'public/assets/js/moment-with-locales-290.js',
        'public/assets/js/plugins/bootstrap/daterangepicker.js',
        'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
        'public/assets/js/modules/anticipos/plugins.js'
      ));
    }



}

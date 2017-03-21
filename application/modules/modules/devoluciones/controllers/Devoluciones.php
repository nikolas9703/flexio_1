<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Flexio
 * @subpackage Controller
 * @category   Devoluciones
 * @author     Pensanomica Team
 * @link       http://www.pensanomica.com
 * @copyright  01/15/2016
 */
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\Devoluciones\Repository\DevolucionCatalogoRepository as DevolucionCatalogoRepository;
use Flexio\Modulo\Devoluciones\Repository\DevolucionRepository as DevolucionRepository;
use Flexio\Modulo\FacturasVentas\Repository\FacturaVentaRepository as FacturaVentaRepository;
use Flexio\Modulo\Cotizaciones\Repository\LineItemRepository as LineItemRepository;
use Flexio\Modulo\Cliente\Models\Cliente;

class Devoluciones extends CRM_Controller {
    private $empresa_id;
    private $id_usuario;
    private $empresaObj;
    protected $devolucionRepository;
    protected $facturaVentaRepository;
    protected $lineItemRepository;
    protected $devolucionCatalogoRepository;
    protected $disparador;

    function __construct(){
      parent::__construct();
      $this->load->model('usuarios/Usuario_orm');
      $this->load->model('usuarios/Empresa_orm');
      $this->load->model('usuarios/Roles_usuarios_orm');
      $this->load->model('roles/Rol_orm');
      $this->load->model('clientes/Cliente_orm');
      $this->load->module('inventarios/Inventarios');
      $this->load->model('contabilidad/Impuestos_orm');
      $this->load->model('contabilidad/Cuentas_orm');
      $this->load->model('contabilidad/Centros_orm');
      $this->load->model('bodegas/Bodegas_orm');
      $this->load->model('cobros/Cobro_orm');
      Carbon::setLocale('es');
      setlocale(LC_TIME, 'Spanish');
      //Cargar Clase Util de Base de Datos
      //$this->load->dbutil();
      $uuid_empresa = $this->session->userdata('uuid_empresa');
      $empresaObj  = new Buscar(new Empresa_orm,'uuid_empresa');
  	  $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
  	  $this->id_usuario   = $this->session->userdata("huuid_usuario");
  	  $this->empresa_id   = $this->empresaObj->id;
      $this->devolucionRepository = new DevolucionRepository;
      $this->facturaVentaRepository = new FacturaVentaRepository;
      $this->lineItemRepository = new LineItemRepository;
      $this->devolucionCatalogoRepository = new DevolucionCatalogoRepository;
    }

    public function listar() {
      $data = array();
      if (!$this->auth->has_permission('acceso')){
        $mensaje = array('estado'=>500, 'mensaje'=>'<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud');
        $this->session->set_flashdata('mensaje', $mensaje);
        //redirect('/');
      }


      $this->_Css();
      $this->_js();
      $this->assets->agregar_js(array(
        'public/assets/js/modules/devoluciones/listar.js',
        'public/assets/js/default/toast.controller.js'
      ));
      $breadcrumb = array( "titulo" => '<i class="fa fa-line-chart"></i> Devoluciones',
          "ruta" => array(
            0 => array(
              "nombre" => "Ventas",
              "activo" => false
            ),
            1 => array(
              "nombre" => '<b>Devoluciones</b>',
              "activo" => true
            )
          ),
          "menu" => array(
            "nombre" => "Crear",
            "url"	 => "devoluciones/crear",
            "opciones" => array()
          )
     );
     //dd($this->session->set_flashdata('mensaje'));
     if(!is_null($this->session->flashdata('mensaje'))){
       $mensaje = json_encode($this->session->flashdata('mensaje'));
     }else{
       $mensaje = '';
     }
     $this->assets->agregar_var_js(array(
       "toast_mensaje" => $mensaje
     ));
     $clause = array('empresa_id'=> $this->empresa_id);
     $roles_users = Rol_orm::where('nombre','like','%vendedor%')->get();

     $usuarios = array();
     $vendedores = array();
     foreach($roles_users as $roles){
      $usuarios = $roles->usuarios;
      foreach ($usuarios as  $user) {
        if($user->pivot->empresa_id == $clause['empresa_id']){
          array_push($vendedores,$user);
        }
      }
     }

      $data['clientes'] = Cliente_orm::where($clause)->get(array('id','nombre'));
      $data['etapas'] = $this->devolucionCatalogoRepository->getEtapas();
      $data['vendedores'] = $vendedores;
      $breadcrumb["menu"]["opciones"]["#exportarListaFacturas"] = "Exportar";
      $this->template->agregar_titulo_header('Listado  de Devoluciones');
      $this->template->agregar_breadcrumb($breadcrumb);
      $this->template->agregar_contenido($data);
      $this->template->visualizar($breadcrumb);

    }

    function ajax_listar() {
      if(!$this->input->is_ajax_request()){
        return false;
      }
      /*
       paramentos de busqueda aqui
      */
      $uuid_cliente = $this->input->post("cliente_id");
      $cliente = $this->input->post('cliente',TRUE);
      $hasta = $this->input->post('desde',TRUE);
      $desde = $this->input->post('hasta',TRUE);
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
      if(!empty($desde)) $clause['fecha_desde'] = Carbon::createFromFormat('d/m/Y',$desde,'America/Panama')->format('Y-m-d 23:59:59');
      if(!empty($hasta)) $clause['fecha_hasta'] = Carbon::createFromFormat('d/m/Y',$hasta,'America/Panama')->format('Y-m-d 00:00:00');
      if(!empty($estado)) $clause['etapa'] = $estado;
      if(!empty($vendedor)) $clause['creado_por'] = $vendedor;
      list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
      $count = $this->devolucionRepository->lista_totales($clause);
      list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
      $devoluciones = $this->devolucionRepository->listar($clause ,$sidx, $sord, $limit, $start);

      $response = new stdClass();
      $response->page     = $page;
      $response->total    = $total_pages;
      $response->records  = $count;



      if(!empty($devoluciones->toArray())){
        $i=0;
        foreach($devoluciones as $row){
          $hidden_options = "";
          $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row->uuid_devolucion .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

          $hidden_options = '<a href="'. base_url('devoluciones/ver/'. $row->uuid_devolucion) .'" data-id="'. $row->uuid_devolucion .'" class="btn btn-block btn-outline btn-success">Ver Devolucion</a>';

          $razon =  $row->razon_catalogo;
          $response->rows[$i]["id"] = $row->uuid_devolucion;
          $response->rows[$i]["cell"] = array(
             $row->uuid_devolucion,
             '<a class="link" href="'. base_url('devoluciones/ver/'. $row->uuid_devolucion) .'" >'.$row->codigo.'</a>',
             '<a class="link">'.$row->cliente_nombre.'</a>',
             $row->fecha_devolucion,
            $razon->valor,
            '<a class="link">'.$row->vendedor_nombre.'</a>',
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

    function ocultotabla($cliente_id=null) {
      $this->assets->agregar_js(array(
        'public/assets/js/modules/devoluciones/tabla.js'
      ));

      if (!empty($cliente_id)) {

       $this->assets->agregar_var_js(array(
              "cliente_id" => $cliente_id
          ));

      }

      $this->load->view('tabla');
    }

    function crear() {
      $acceso = 1;
      $mensaje = array();
      if(!$this->auth->has_permission('acceso')){
              $acceso = 0;
              $mensaje = array('estado'=>500, 'mensaje'=>'<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud','clase'=>'alert-danger');
              $this->session->set_flashdata('mensaje', $mensaje);
      }
      if(!$this->empresaObj->tieneCuentaCobro()){
        $mensaje = array('estado'=>500, 'mensaje'=>'No hay cuenta de cobro asociada','clase'=>'alert-danger');
        $this->session->set_flashdata('mensaje', $mensaje);
        redirect(base_url('facturas/listar'));
      }

      $this->_Css();
      $this->assets->agregar_css(array(
        'public/assets/css/modules/stylesheets/animacion.css'
      ));
      $this->_js();
      $this->assets->agregar_js(array(
        'public/assets/js/default/vue.min.js',
        'public/assets/js/modules/devoluciones/listar.js',
        'public/assets/js/modules/devoluciones/vue.componente.tabla.js',
        'public/assets/js/modules/devoluciones/vue.funciones.js',
        'public/assets/js/modules/devoluciones/vue.crear.js',
        'public/assets/js/modules/devoluciones/helpers.js',

      ));

        $data=array();
        $clause = array('empresa_id'=> $this->empresa_id);

        $this->assets->agregar_var_js(array(
          "vista" => 'crear',
          "acceso" => $acceso == 0? $acceso : $acceso
        ));

      $data['mensaje'] = $mensaje;

      $breadcrumb = array(
          "titulo" => '<i class="fa fa-line-chart"></i> Devoluciones: Crear',
          "ruta" => array(
              0 => array(
                  "nombre" =>  'Ventas',
                  "activo" => false
              ),
              1 => array(
                  "nombre" => 'Devoluciones',
                  "activo" => false,
                  "url" => "devoluciones/listar"
              ),
              2 => array(
                  "nombre" =>'<b>Crear</b>',
                  "activo" => true
              )
          )
      );
      $this->template->agregar_titulo_header('Crear Devoluciones');
      $this->template->agregar_breadcrumb($breadcrumb);
      $this->template->agregar_contenido($data);
      $this->template->visualizar();

    }

    function ver($uuid = null) {
      $acceso = 1;
      $mensaje = array();

      if(!$this->auth->has_permission('acceso')){
              $acceso = 0;
              $mensaje = array('estado'=>500, 'mensaje'=>'<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud','clase'=>'alert-danger');
              $this->session->set_flashdata('mensaje', $mensaje);
      }
      if(!$this->empresaObj->tieneCuentaCobro()){
        $mensaje = array('estado'=>500, 'mensaje'=>'No hay cuenta de cobro asociada','clase'=>'alert-danger');
        $this->session->set_flashdata('mensaje', $mensaje);
        redirect(base_url('facturas/listar'));
      }

      $this->_Css();
      $this->assets->agregar_css(array(
        'public/assets/css/modules/stylesheets/animacion.css'
      ));
      $this->_js();
      $this->assets->agregar_js(array(
        //'public/assets/js/default/vue.min.js',
          'public/assets/js/plugins/ckeditor/ckeditor.js',
          'public/assets/js/plugins/ckeditor/adapters/jquery.js',
        'public/assets/js/modules/devoluciones/listar.js',
        'public/assets/js/modules/devoluciones/vue.funciones.js',
        'public/assets/js/modules/devoluciones/vue.componente.tabla.js',
        'public/assets/js/modules/devoluciones/vue.crear.js',
        'public/assets/js/modules/devoluciones/funciones.vista.ver.js',
        'public/resources/compile/modulos/devoluciones/comentario-devoluciones.js'
      ));

      $devolucion = $this->devolucionRepository->findByUuid($uuid);
       if(is_null($uuid) || is_null($devolucion)){
         $mensaje = array('estado'=>500, 'mensaje'=>'<strong>¡Error!</strong> Su solicitud no fue procesada');
         $this->session->set_flashdata('mensaje', $mensaje);
         redirect(base_url('devoluciones/listar'));
       }

        $data=array();
        $clause = array('empresa_id'=> $this->empresa_id);

        $devolucion->load('items','facturas','facturas.cliente','items.inventario_item','items.inventario_item.unidades','items.impuesto','comentario_timeline');
       // $devo_coment = $devolucion;
        $devolucion->toArray();
        $this->assets->agregar_var_js(array(
          "vista" => 'ver',
          "acceso" => $acceso == 0? $acceso : $acceso,
          "devolucion" => $devolucion,
          "coment_devoluciones" => (isset($devolucion->comentario_timeline)) ? $devolucion->comentario_timeline : "",
          "devoluciones_id" => $devolucion->id
        ));

      $data['mensaje'] = $mensaje;

      $breadcrumb = array(
          "titulo" => '<i class="fa fa-line-chart"></i> Devoluciones: ver '.$devolucion->codigo,
          "ruta" => array(
              0 => array(
                  "nombre" =>  'Ventas',
                  "activo" => false
              ),
              1 => array(
                  "nombre" => 'Devoluciones',
                  "activo" => false,
                  "url" => "devoluciones/listar"
              ),
              2 => array(
                  "nombre" =>'<b>Detalle</b>',
                  "activo" => true
              )
          )
      );
      $this->template->agregar_titulo_header('Ver Devoluciones');
      $this->template->agregar_breadcrumb($breadcrumb);
      $this->template->agregar_contenido($data);
      $this->template->visualizar();
    }

    function ocultoformulario($facturas = array()) {

      $data = array();
      $clause = array('empresa_id'=> $this->empresa_id);
      $clause_precios = array('empresa_id'=>$this->empresa_id,'estado'=>1);
      $clause_impuesto = array('empresa_id'=>$this->empresa_id,'estado'=>'Activo');

        $roles_users = Rol_orm::where(function($query) use($clause){
          $query->where('empresa_id','=',$clause['empresa_id']);
          $query->where('nombre','like','%vendedor%');
        })->orWhere(function($query) use($clause){
          $query->where('empresa_id','=',$clause['empresa_id']);
          $query->where('nombre','like','%venta%');
        })->get();

        $usuarios = array();
        $vendedores = array();
        foreach($roles_users as $roles){
        $usuarios = $roles->usuarios;
          foreach ($usuarios as  $user) {
              if($user->pivot->empresa_id == $clause['empresa_id']){
                array_push($vendedores,$user);
              }
          }
        }

      $data['razones'] = $this->devolucionCatalogoRepository->getRazon();
      $data['etapas'] = $this->devolucionCatalogoRepository->getEtapas();
      $data['vendedores'] = $vendedores;
      $data['unidades'] = array();
      $data["categorias"] = Categorias_orm::categoriasConItems($this->empresa_id);
      $data['precios'] = Precios_orm::where($clause_precios)->get(array('id','uuid_precio','nombre'));
      $data['items'] = Items_orm::where($clause_precios)->get(array('id','uuid_item','uuid_activo','nombre','codigo'));
      $impuesto = Impuestos_orm::where($clause_impuesto)->whereHas('cuenta',function($query) use($clause_impuesto){
           $query->activas();
           $query->where('empresa_id','=',$clause_impuesto['empresa_id']);
       })->get(array('id','uuid_impuesto','nombre','impuesto'));
      $data['impuestos'] = $impuesto;
      $data['cuenta_activo'] = Cuentas_orm::transaccionalesDeEmpresa($this->empresa_id)->deTipoDeCuenta([4])->activas()->get();
      //$data['clientes'] = Cliente_orm::where($clause)->get(array('id','nombre','credito'));
      $data['clientes'] = Cliente::where($clause)->get(array('id','nombre','credito_limite'));
      $data['bodegas'] = Bodegas_orm::where(array('empresa_id'=>$this->empresa_id,'estado'=>1))->get(array('id','nombre'));

      $ids_centros = Centros_orm::where($clause_impuesto)->lists('padre_id');
      //lista de centros contables
      $centros_contables = Centros_orm::whereNotIn('id', $ids_centros->toArray())->where(function($query) use($clause_impuesto){
        $query->where($clause_impuesto);
      })->get(array('id','nombre','uuid_centro'));
      $data['centros_contables']= $centros_contables;

      if(isset($facturas['info']))$data['info'] = $facturas['info'];

      $this->load->view('formulario', $data);
      $this->load->view('componente_tabla', $data);
    }

    function guardar() {
       if($_POST){
           $request = Illuminate\Http\Request::createFromGlobals();
           $array_devolucion = $request->input('campo');
           $lineitems = $request->input('items');
           //dd($request->all());
           $j=0;
           $itemDevolucion = [];
         foreach($lineitems as $item){
             $impuestoClase  = Impuestos_orm::find($item['impuesto_id']);
             $total_impuesto = ($impuestoClase->impuesto / 100) * ($item['cantidad_devolucion'] * $item['precio_unidad']);
             $total_descuento = ($item['descuento'] / 100) * ($item['cantidad_devolucion'] * $item['precio_unidad']);
             array_push($itemDevolucion,array(
               'item_id'=> $item['item_id'],
               'categoria_id' => $item['categoria_id'],
               'cantidad' => $item['cantidad'],
               'cantidad_devolucion' => $item['cantidad_devolucion'],
               'unidad_id' => $item['unidad_id'],
               'precio_unidad' => $item['precio_unidad'],
               'impuesto_id' => $item['impuesto_id'],
               'cuenta_id' => $item['cuenta_id'],
               'precio_total' => $item['precio_total'],
               'empresa_id' => $this->empresa_id,
               'impuesto_total' => $total_impuesto,
               'descuento_total' => $total_descuento,
               'descuento'=> $item['descuento'],
             ));
             if(!empty($item['devolucion_item_id'])){
               $itemDevolucion[$j]['lineitem_id']=$item['devolucion_item_id'];
             }
             $j++;
         }

         $array_devolucion['empresa_id'] = $this->empresa_id;
         Capsule::beginTransaction();
         try{
                 if(empty($array_devolucion['id'])){
                   $total = $this->devolucionRepository->lista_totales(['empresa_id'=>$this->empresa_id]);
                   $year = Carbon::now()->format('y');
                   $codigo = Util::generar_codigo('DEV'.$year,$total + 1);
                   $array_devolucion['codigo'] = $codigo;
                   $data = ['devolucion'=>$array_devolucion,'lineitem'=>$itemDevolucion];
                   $devolucion = $this->devolucionRepository->create($data);
                 }else{
                   $data = ['devolucion'=>$array_devolucion,'lineitem'=>$itemDevolucion];
                   $devolucion = $this->devolucionRepository->update($data);
                 }
             }catch(Illuminate\Database\QueryException $e){
               log_message('error', __METHOD__." ->". ", Linea: ". __LINE__ ." --> ". $e->getMessage()."\r\n");
               Capsule::rollback();
               $mensaje = array('estado'=>500, 'mensaje'=>'<b>¡Error! Su solicitud no fue procesada</b> ');
               $this->session->set_flashdata('mensaje', $mensaje);
               redirect(base_url('devoluciones/listar'));
             }

             if(!is_null($devolucion)){
                 Capsule::commit();
                 $mensaje = array('estado' => 200, 'mensaje' =>'<b>¡&Eacute;xito!</b> Se ha guardado correctamente '.$devolucion->codigo);
               }else{
                 $mensaje = array('estado'=>500, 'mensaje'=>'<b>¡Error! Su solicitud no fue procesada null</b> ');
               }
                 $this->session->set_flashdata('mensaje', $mensaje);
                 redirect(base_url('devoluciones/listar'));

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
        'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
        'public/assets/css/plugins/bootstrap/select2.min.css',
        'public/assets/js/plugins/jquery/sweetalert/sweetalert.css',
        'public/assets/css/modules/stylesheets/devoluciones.css',
      ));
    }

    private function _js() {
      $this->assets->agregar_js(array(
        'public/assets/js/default/jquery-ui.min.js',
        'public/assets/js/plugins/jquery/jquery.sticky.js',
        'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
        'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
        'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
        'public/assets/js/plugins/jquery/jqgrid/plugins/jQuery.jqGrid.columnToggle.js',
        'public/assets/js/plugins/jquery/switchery.min.js',
        'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
        'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
        'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
        'public/assets/js/plugins/jquery/combodate/combodate.js',
        'public/assets/js/plugins/jquery/combodate/momentjs.js',
        'public/assets/js/default/lodash.min.js',
        'public/assets/js/default/accounting.min.js',
        'public/assets/js/plugins/bootstrap/select2/select2.min.js',
        'public/assets/js/plugins/bootstrap/select2/es.js',
        'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
        'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
        'public/assets/js/plugins/jquery/sweetalert/sweetalert.min.js',
        'public/assets/js/moment-with-locales-290.js',
        'public/assets/js/plugins/bootstrap/daterangepicker.js',
        'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
      ));
    }



}

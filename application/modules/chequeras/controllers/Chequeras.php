<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Flexio
 * @subpackage Controller
 * @category   Chequeras
 * @author     Pensanomica Team
 * @link       http://www.pensanomica.com
 * @copyright  01/15/2016
 */

use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

use Flexio\Modulo\ConfiguracionCompras\Repository\ChequerasRepository as ChequerasRepository;



class Chequeras extends CRM_Controller
{
  private $empresa_id;
  private $id_usuario;
  private $empresaObj;
  protected $contratoRepository;
  protected $ordenVentaRepository;
  protected $chequerasRepository;
  protected $lineItemRepository;
  protected $chequeVentaCatalogoRepository;
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
    $this->load->module("salidas/Salidas");
      $this->load->model('chequeras/Chequeras_orm');
      $this->load->model('pagos/Pagos_orm');
      $this->load->model('chequeras/Chequeras_orm');

      Carbon::setLocale('es');
    setlocale(LC_TIME, 'Spanish');
    //Cargar Clase Util de Base de Datos
    $this->load->dbutil();
    $uuid_empresa = $this->session->userdata('uuid_empresa');
    $empresaObj  = new Buscar(new Empresa_orm,'uuid_empresa');
		$this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
	  $this->id_usuario   = $this->session->userdata("huuid_usuario");
	  $this->empresa_id   = $this->empresaObj->id;
      $this->chequerasRepository = new ChequerasRepository;
  }

  function listar(){
    $data = array();
    if (!$this->auth->has_permission ('acceso')) {
      $mensaje = array('estado'=>500, 'mensaje'=>'<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud');
      $this->session->set_flashdata('mensaje', $mensaje);
      //redirect('/');
    }


    $this->_Css();
    $this->_js();
    $this->assets->agregar_js(array(
      'public/assets/js/modules/chequeras/listar.js',
      'public/assets/js/default/toast.controller.js'
    ));
    $breadcrumb = array( "titulo" => '<i class="fa fa-shopping-cart"></i> Chequeras',
        "ruta" => array(
          0 => array(
            "nombre" => "Ventas",
            "activo" => false
          ),
          1 => array(
            "nombre" => '<b>Chequeras</b>',
            "activo" => true
          )
        ),
        "menu" => array(
          "nombre" => "Crear",
          "url"	 => "chequeras/crear",
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
    $data['vendedores'] = $vendedores;
    $breadcrumb["menu"]["opciones"]["#exportarListaChequeras"] = "Exportar";
    $this->template->agregar_titulo_header('Listado de Ordenes de Ventas');
    $this->template->agregar_breadcrumb($breadcrumb);
    $this->template->agregar_contenido($data);
    $this->template->visualizar($breadcrumb);

  }

  function ajax_listar(){
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
    if(!empty($desde)) $clause['fecha_desde'] = Carbon::createFromFormat('m/d/Y',$desde,'America/Panama')->format('Y-m-d');
    if(!empty($hasta)) $clause['fecha_hasta'] = Carbon::createFromFormat('m/d/Y',$hasta,'America/Panama')->format('Y-m-d');
    if(!empty($estado)) $clause['etapa'] = $estado;
    if(!empty($vendedor)) $clause['creado_por'] = $vendedor;
    list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
    $count = $this->chequerasRepository->lista_totales($clause);
    list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
    $chequeras = $this->chequerasRepository->listar($clause ,$sidx, $sord, $limit, $start);

    $response = new stdClass();
    $response->page     = $page;
    $response->total    = $total_pages;
    $response->records  = $count;



      if(!empty($chequeras->toArray())){

      $i=0;
      foreach($chequeras as $i => $row){

        $hidden_options = "";
        $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row->uuid_cheque .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

          $url = base_url('chequeras/ver/'. $row->uuid_cheque);
        if($row->formulario == 'recheque'){
          $url = base_url('chequeras/rechequer/'. $row->uuid_cheque);
        }
        $hidden_options = '<a href="'. $url .'" data-id="'. $row->uuid_cheque .'" class="btn btn-block btn-outline btn-success">Ver Cheque</a>';

        if($row->estado =='por_cobrar')$hidden_options .= '<a href="'. base_url('cobros/registrar_pago/'. $row->uuid_cheque) .'" data-id="'. $row->uuid_cheque .'" class="btn btn-block btn-outline btn-success">Registrar Pago</a>';

        $cliente = $row->cliente;
        $vendedor = $row->vendedor;
        $etapa = $row->etapa_catalogo;
        $response->rows[$i]["id"] = $row->uuid_cheque;
        $response->rows[$i]["cell"] = array(
           '<a class="link" href="'. $url .'" >'.$row->numero.'</a>',
           '<a class="link">'.$row->fecha_cheque.'</a>',
            $row->pago->numero,
            $row->chequera->nombre,
            $row->monto_cheque,
            $row->estado,
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
  
  function ajax_listar_de_item(){
        if(!$this->input->is_ajax_request()){
            return false;
        }
    
        $clause                 = $this->input->post();
        $clause["empresa_id"]   = $this->empresaObj->id;

    
        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = $this->chequerasRepository->lista_totales($clause);
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $chequeras = $this->chequeVentaRepository->listar($clause ,$sidx, $sord, $limit, $start);

        $response = new stdClass();
        $response->page     = $page;
        $response->total    = $total_pages;
        $response->records  = $count;

        if(!empty($chequeras->toArray())){
            foreach($chequeras as $i => $row){
                $response->rows[$i]["id"]   = $row->uuid_cheque;
                $response->rows[$i]["cell"] = $this->chequeVentaRepository->getCollectionCellDeItem($row, $clause["item_id"]);
            }
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))->_display();
        exit;
    }

  function ocultotabla(){
    $this->assets->agregar_js(array(
      'public/assets/js/modules/chequeras/tabla.js'
    ));

    $this->load->view('tabla');
  }

  function crear($tipo=null,$uuid=null){
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
      redirect(base_url('chequeras/listar'));
    }

    $this->_Css();
    $this->assets->agregar_css(array(
      'public/assets/css/modules/stylesheets/animacion.css'
    ));
    $this->_js();
    $this->assets->agregar_js(array(
      'public/assets/js/modules/chequeras/services.chequeras.js',
      'public/assets/js/modules/cotizaciones/services.itemsData.js',
      'public/assets/js/modules/chequeras/crear.controller.js',
    ));

      $data=array();
      $clause = array('empresa_id'=> $this->empresa_id);

      $this->assets->agregar_var_js(array(
        "vista" => 'crear',
        "acceso" => $acceso == 0? $acceso : $acceso
      ));
    if($tipo=='contrato'){
        $contrato = $this->contratoRepository->findByUuid($uuid);
        if(!is_null($contrato)){
            $this->assets->agregar_var_js(array(
              "tipo" => 'contrato_venta',
              "uuid" => $contrato->uuid_contrato
            ));
        }
    }
    $data['mensaje'] = $mensaje;
    $breadcrumb = array(
      "titulo" => '<i class="fa fa-shopping-cart"></i> Cheque: Crear',
    );

    $this->template->agregar_titulo_header('Crear Cheque');
    $this->template->agregar_breadcrumb($breadcrumb);
    $this->template->agregar_contenido($data);
    $this->template->visualizar();

  }

  function ver($uuid = null){
    $acceso = 1;
    $mensaje = array();
    if(!$this->auth->has_permission('acceso','chequeras/ver/(:any)')){
            $acceso = 0;
            $mensaje = array('estado'=>500, 'mensaje'=>'<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud','clase'=>'alert-danger');
    }

    $this->_Css();
    $this->assets->agregar_css(array(
      'public/assets/css/modules/stylesheets/animacion.css'
    ));
    $this->_js();
    $this->assets->agregar_js(array(
      'public/assets/js/modules/chequeras/services.chequeras.js',
      'public/assets/js/modules/cotizaciones/services.itemsData.js',
      'public/assets/js/modules/chequeras/editar.controller.js',
    ));

    //$chequeObj  = new Buscar(new Cheque_orm,'uuid_cheque');
    $cheque = $this->chequeVentaRepository->findByUuid($uuid);
    if(is_null($uuid) || is_null($cheque)){
      $mensaje = array('estado'=>500, 'mensaje'=>'<strong>¡Error!</strong> Su solicitud no fue procesada');
      $this->session->set_flashdata('mensaje', $mensaje);
      redirect(base_url('chequeras/listar'));
    }
      $data=array();
      $salida = ['id'=>$cheque->id,'type'=>'Cheque_orm'];
      $this->assets->agregar_var_js(array(
        "vista" => 'editar',
        "acceso" => $acceso == 0? $acceso : $acceso,
        "uuid_cheque" => $cheque->uuid_cheque,
        "cheque"=> json_encode($salida)
      ));
    //$data['ordenes_ventas'] = $ordenesVentas->toArray();
    $data['uuid_cheque'] = $cheque->uuid_cheque;
    $data['cliente_id'] = $cheque->cliente->uuid_cliente;
    $data['mensaje'] = $mensaje;
    $breadcrumb = array(
      "titulo" => '<i class="fa fa-shopping-cart"></i> Cheque: '.$cheque->codigo,
    );

    $this->template->agregar_titulo_header('Editar Cheque');
    $this->template->agregar_breadcrumb($breadcrumb);
    $this->template->agregar_contenido($data);
    $this->template->visualizar();

  }

  function ocultoformulario($chequeras = array()){

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

    $data['terminos_pagos'] = $this->chequeVentaCatalogoRepository->getTerminoPago();
    $data['vendedores'] = $vendedores;
    $data['unidades'] = array();
    $data['precios'] = Precios_orm::where($clause_precios)->get(array('id','uuid_precio','nombre'));
    $data['items'] = Items_orm::where($clause_precios)->get(array('id','uuid_item','uuid_activo','nombre','codigo'));
    $impuesto = Impuestos_orm::where($clause_impuesto)->whereHas('cuenta',function($query) use($clause_impuesto){
         $query->activas();
         $query->where('empresa_id','=',$clause_impuesto['empresa_id']);
     })->get(array('id','uuid_impuesto','nombre','impuesto'));
    $data['impuestos'] = $impuesto;
    $data['cuenta_activo'] = Cuentas_orm::transaccionalesDeEmpresa($this->empresa_id)->deTipoDeCuenta([4])->activas()->get();
    $data['clientes'] = Cliente_orm::where($clause)->get(array('id','nombre','credito'));
    $data['bodegas'] = Bodegas_orm::where(array('empresa_id'=>$this->empresa_id,'estado'=>1))->get(array('id','nombre'));

    $ids_centros = Centros_orm::where($clause_impuesto)->lists('padre_id');
    //lista de centros contables
    $centros_contables = Centros_orm::whereNotIn('id', $ids_centros->toArray())->where(function($query) use($clause_impuesto){
      $query->where($clause_impuesto);
    })->get(array('id','nombre','uuid_centro'));
    $data['centros_contables']= $centros_contables;

    if(isset($chequeras['info']))$data['info'] = $chequeras['info'];

    $this->load->view('formulario', $data);
  }

  function guardar(){

    if($_POST){
      $request = Illuminate\Http\Request::createFromGlobals();
      $array_cheque = $request->input('campo');
      $lineitems = $request->input('items');
      $formulario = $request->input('formulario');
      $chequeble_id = $request->input('fac_chequeble_id');
      $this->disparador = new \Illuminate\Events\Dispatcher();
      Capsule::beginTransaction();
      try{
          $delete_item = $_POST['delete_items'];
          if(!empty($delete_item)){
          $ids=  explode( ',', $delete_item);
            $this->lineItemRepository->delete($ids);
          }
        $j=0;
        $itemCheque = [];
      foreach ($lineitems as $item){

        $item_uuid = $item['item_id'];
        $impuesto_uuid = $item['impuesto_id'];
        $cuenta_uuid = $item['cuenta_id'];

        $impuestoObj  = new Buscar(new Impuestos_orm,'uuid_impuesto');
        $cuentaObj  = new Buscar(new Cuentas_orm,'uuid_cuenta');

        $impuestoClase = $impuestoObj->findByUuid($impuesto_uuid);
        $cuentaClase = $cuentaObj->findByUuid($cuenta_uuid);

        $item['impuesto_id'] = $impuestoClase->id;
        $item['cuenta_id'] = $cuentaClase->id;
        $item['empresa_id'] = $this->empresa_id;
        $total_impuesto = ($impuestoClase->impuesto / 100) * ($item['cantidad'] * $item['precio_unidad']);
        $total_descuento = ($item['descuento'] / 100) * ($item['cantidad'] * $item['precio_unidad']);

          array_push($itemCheque,array(
            'item_id'=> $item['item_id'],
            'categoria_id' => $item['categoria_id'],
            'cantidad' => $item['cantidad'],
            'unidad_id' => $item['unidad_id'],
            'precio_unidad' => $item['precio_unidad'],
            'impuesto_id' => $item['impuesto_id'],
            'descuento' => $item['descuento'],
            'cuenta_id' => $item['cuenta_id'],
            'precio_total' => $item['precio_total'],
            'empresa_id' => $item['empresa_id'],
            'impuesto_total' => $total_impuesto,
            'descuento_total' => $total_descuento
          ));
          if(!empty($item['cheque_item_id'])){
            $itemCheque[$j]['lineitem_id']=$item['cheque_item_id'];
          }
          $j++;

      }

      $array_cheque['empresa_id'] = $this->empresa_id;
      $array_cheque['created_by'] = $array_cheque['creado_por'];
      if(!empty($formulario))$array_cheque['formulario'] = $formulario;
      if(empty($array_cheque['cheque_id'])){
        $total = $this->chequerasRepository->lista_totales(['empresa_id'=>$this->empresa_id]);
        $year = Carbon::now()->format('y');
        $codigo = Util::generar_codigo('INV'.$year,$total + 1);
        $array_cheque['codigo'] = $codigo;
        $data = ['chequeventa'=>$array_cheque,'lineitem'=>$itemCheque];
        $cheque = $this->chequeVentaRepository->create($data, $formulario);
      }else{
        $data = ['chequeventa'=>$array_cheque,'lineitem'=>$itemCheque];
        $cheque = $this->chequeVentaRepository->update($data);
      }

      if($formulario =='orden_venta'){
        $model = $this->ordenVentaRepository->find($chequeble_id);
        $this->disparador->listen([OrdenVentaChequebleEvent::class], CrearOrdenChequebleListener::class);
        if(empty($array_cheque['cheque_id']))$this->disparador->fire(new OrdenVentaChequebleEvent($cheque,$model));
      }elseif($formulario =='contrato_venta'){
        $model = $this->contratoRepository->findBy($chequeble_id);
        $this->disparador->listen([ContratoChequebleEvent::class], CrearContratoChequebleListener::class);
        if(empty($array_cheque['cheque_id']))$this->disparador->fire(new ContratoChequebleEvent($cheque,$model));
      }

      }catch(Illuminate\Database\QueryException $e){
        log_message('error', __METHOD__." ->". ", Linea: ". __LINE__ ." --> ". $e->getMessage()."\r\n");
        Capsule::rollback();
        $mensaje = array('estado'=>500, 'mensaje'=>'<b>¡Error! Su solicitud no fue procesada</b> ');
        $this->session->set_flashdata('mensaje', $mensaje);
        redirect(base_url('chequeras/listar'));
      }
      Capsule::commit();

      if(!is_null($cheque)){
        if($formulario =='orden_venta'){
        $ordenVenta = $this->ordenVentaRepository->find($chequeble_id);

        if(!is_null($ordenVenta)){
          $this->load->library('Events/Orden_venta/Orden_venta_estado');
          $OrdenVentaEstado = new Orden_venta_estado;
          $OrdenVentaEstado->handle($ordenVenta);
        }
        }
        if($cheque->estado =='por_cobrar'){
          $transaccion = new Transaccion;
          $transaccion->hacerTransaccion($cheque->fresh(), new TransaccionCheque);
        }

        //$this->salidas->comp__crearSalida(array("id" => $cheque->id, "type" => "Cheque_orm"));
        $mensaje = array('estado' => 200, 'mensaje' =>'<b>¡&Eacute;xito!</b> Se ha guardado correctamente '.$cheque->codigo);
      }else{
        $mensaje = array('estado'=>500, 'mensaje'=>'<b>¡Error! Su solicitud no fue procesada</b> ');
      }
        $this->session->set_flashdata('mensaje', $mensaje);
        redirect(base_url('chequeras/listar'));
    }


  }

  private function _generar_codigo(){
    $clause_empresa = ['empresa_id'=>$this->empresa_id];
    $total = $this->chequerasRepository->lista_totales($clause_empresa);
    $year = Carbon::now()->format('y');
    $codigo = Util::generar_codigo('INV'.$year,$total + 1);
    return $codigo;
  }

  function ajax_cheque_info (){
    $uuid = $this->input->post('uuid');
    $cheque = $this->chequeVentaRepository->findByUuid($uuid);
    $cheque->load('cliente','items','items.impuesto','items.cuenta');
    if($cheque->formulario=='orden_venta')$cheque->ordenes_ventas;
    if($cheque->formulario=='contrato_venta')$cheque->contratos;
    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($cheque->toArray()))->_display();
    exit;
  }

  function ajax_getAll(){
    if(!$this->input->is_ajax_request()){
      return false;
    }
    $clause = ['empresa_id' => $this->empresa_id,'formulario' =>['cheque_venta','orden_venta','contrato_venta'],'estado'=>['cobrado_completo']];
    $chequeras = $this->chequeVentaRepository->sinDevolucion($clause);
    $chequeras->load('cliente','items','items.inventario_item','items.inventario_item.unidades','items.impuesto');
    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($chequeras->toArray()))->_display();
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
      'public/assets/css/modules/stylesheets/chequeras.css',
    ));
  }

  private function _js(){
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
      'public/assets/js/plugins/jquery/chosen.jquery.min.js',
      'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
      'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
      'public/assets/js/plugins/jquery/sweetalert/sweetalert.min.js',
      'public/assets/js/moment-with-locales-290.js',
      'public/assets/js/plugins/bootstrap/daterangepicker.js',
      'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
    ));
  }

}

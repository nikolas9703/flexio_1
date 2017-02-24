<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Flexio
 * @subpackage Controller
 * @category   Cheques
 * @author     Pensanomica Team
 * @link       http://www.pensanomica.com
 * @copyright  01/15/2016
 */

use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Dompdf\Dompdf;

use Flexio\Modulo\ConfiguracionCompras\Repository\ChequesRepository as ChequesRepository;
use Flexio\Modulo\Pagos\Repository\PagosRepository as pagosRep;



class Cheques extends CRM_Controller
{
  private $empresa_id;
  private $id_usuario;
  private $empresaObj;
  protected $contratoRepository;
  protected $ordenVentaRepository;
  protected $chequesRepository;
  protected $lineItemRepository;
  protected $chequeVentaCatalogoRepository;
  protected $disparador;
    private $usuarioId;

    //repositories
    private $pagosRep;

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
      $this->load->model('facturas_compras/Facturas_compras_orm');
      $this->load->model('proveedores/Proveedores_orm');

      $this->load->module("salidas/Salidas");
      $this->load->model('cheques/Cheques_orm');
      $this->load->model('pagos/Pagos_orm');
      $this->load->model('pagos/Pago_metodos_pago_orm');
      $this->load->model('chequeras/Chequeras_orm');
      $this->load->model('cheques/Cheques_orm');
      $this->load->model('pagos/Pago_catalogos_orm');

      Carbon::setLocale('es');
    setlocale(LC_TIME, 'Spanish');
    //Cargar Clase Util de Base de Datos
    $this->load->dbutil();
    $uuid_empresa = $this->session->userdata('uuid_empresa');
    $empresaObj  = new Buscar(new Empresa_orm,'uuid_empresa');
		$this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
	  $this->id_usuario   = $this->session->userdata("huuid_usuario");
	  $this->empresa_id   = $this->empresaObj->id;
      $this->chequesRepository = new ChequesRepository;
      $this->chequeraRepository = new \Flexio\Modulo\ConfiguracionCompras\Repository\ChequerasRepository();
      $this->usuarioId = $this->session->userdata("id_usuario");
      //repositories
      $this->pagosRep   = new pagosRep();
  }

  function listar(){
    $data = array();
    if (!$this->auth->has_permission ('acceso', 'cheques/listar')) {
      $mensaje = array('estado'=>500, 'mensaje'=>'<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud');
      $this->session->set_flashdata('mensaje', $mensaje);
      //redirect('/');
    }


    $this->_Css();
    $this->_js();
    $this->assets->agregar_js(array(
      'public/assets/js/modules/cheques/listar.js',
      'public/assets/js/default/toast.controller.js'
    ));
    $breadcrumb = array( "titulo" => '<i class="fa fa-shopping-cart"></i> Cheques',
        "ruta" => array(
          0 => array(
            "nombre" => "Ventas",
            "activo" => false
          ),
          1 => array(
            "nombre" => '<b>Cheques</b>',
            "activo" => true
          )
        ),
        "menu" => array(
          "nombre" => "Crear",
          "url"	 => "cheques/crear",
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
      $clause_pro = array('id_empresa'=> $this->empresa_id);
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

    $data['chequeras'] = Chequeras_orm::where($clause)->get(array('id','nombre','uuid_chequera'));
      $data['estados'] = \Flexio\Modulo\ConfiguracionCompras\Models\ChequesCatalogo::get(array('id','etiqueta'));
      $data['proveedores'] = Proveedores_orm::where($clause_pro)->get(array('id','nombre','uuid_proveedor'));

    $breadcrumb["menu"]["opciones"]["#exportarListaCheques"] = "Exportar";
    $this->template->agregar_titulo_header('Listado de Cheques');
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
    $estado = $this->input->post("estado",TRUE);
   $uuid_proveedor = $this->input->post('proveedor',TRUE);
    $hasta = $this->input->post('desde',TRUE);
    $desde = $this->input->post('hasta',TRUE);
    $numero = $this->input->post('numero',TRUE);
    $uuid_chequera = $this->input->post('chequera',TRUE);
    $clause = array('empresa_id' => $this->empresaObj->id);

    if(!empty($uuid_chequera)){
       $chequeraObj  = new Buscar(new Chequeras_orm,'uuid_chequera');
       $chequera = $chequeraObj->findByUuid($uuid_chequera);
       $clause['chequera_id'] = $chequera->id;
    }
      if(!empty($uuid_proveedor)){
          $proveedorObj  = new Buscar(new Proveedores_orm,'uuid_proveedor');
          $proveedor = $proveedorObj->findByUuid($uuid_proveedor);
          $clause['proveedor'] = $proveedor->id;
      }

    if(!empty($desde)) $clause['fecha_desde'] = Carbon::createFromFormat('d-m-Y',$desde,'America/Panama')->format('Y-m-d');
    if(!empty($hasta)) $clause['fecha_hasta'] = Carbon::createFromFormat('d-m-Y',$hasta,'America/Panama')->format('Y-m-d');
    if(!empty($estado)) $clause['estado'] = $estado;
    if(!empty($numero)) $clause['numero'] = $numero;
    list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
    $count = $this->chequesRepository->lista_totales($clause);
    list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
    $cheques = $this->chequesRepository->listar($clause ,$sidx, $sord, $limit, $start);

    $response = new stdClass();
    $response->page     = $page;
    $response->total    = $total_pages;
    $response->records  = $count;



      if(!empty($cheques->toArray())){

      $i=0;
      foreach($cheques as $i => $row){

        $hidden_options = "";
        $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row->uuid_cheque .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
          $hidden_options .= '<a href="'. $row->getEnlaceAttribute() .'" data-id="'. $row->uuid_cheque .'" class="btn btn-block btn-outline btn-success">Ver Cheque</a>';

            if($row->imprimible && $row->chequera_id!=0){
                $hidden_options .= '<a    id="imprimir_cheque" href="'. base_url("cheques/imprimir/".$row->uuid_cheque) .'" data-id="'. $row->uuid_cheque .'" class="btn btn-block btn-outline btn-success ">Imprimir Cheque</a>';
            }
            if($row->anulable){
                $hidden_options .= '<a href="#" id="anular_cheque" data-monto="'.$row->monto.'" data-proveedor_nombre="'.$row->pago->proveedor->nombre.'" data-id="'.$row->id.'" data-numero="'.$row->numero.'" class="btn btn-block btn-outline btn-success">Anular</a>';
            }
        $response->rows[$i]["id"] = $row->uuid_cheque;
        $response->rows[$i]["cell"] = array(
           ($row->chequera_id!=0)?'<a class="link" href="'.$row->getEnlaceAttribute().'" >'.$row->numero.'</a>':'<a class="link" href="'.$row->getEnlaceAttribute().'" >Incompleto</a>',
           '<a class="link" href="'.base_url("pagos/ver/".$row->pago->uuid_pago).'" >'.$row->pago->codigo.'</a>',
           $row->fecha_cheque,
           $row->pago->proveedor->nombre,
           isset($row->chequera->nombre)?$row->chequera->nombre:'',
           '$' . number_format($row->monto, 2),
           '<p class="tag '.$row->estado_cheque->valor.'">'.$row->estado_cheque->etiqueta.'</p>',
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
        $count = $this->chequesRepository->lista_totales($clause);
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $cheques = $this->chequeVentaRepository->listar($clause ,$sidx, $sord, $limit, $start);

        $response = new stdClass();
        $response->page     = $page;
        $response->total    = $total_pages;
        $response->records  = $count;

        if(!empty($cheques->toArray())){
            foreach($cheques as $i => $row){
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
      'public/assets/js/modules/cheques/tabla.js'
    ));

    $this->load->view('tabla');
  }

    public function crear($foreign_key = '', $tipo=null,$uuid=null){
        if(preg_match('/pago/', $foreign_key))
        {
            $pago_uuid = str_replace('pago', '', $foreign_key);
        }

        $acceso     = 1;
        $mensaje    = array();
        if(!$this->auth->has_permission('acceso','cheques/crear')){
            $acceso = 0;
            $mensaje = array('estado'=>500, 'mensaje'=>'<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud','clase'=>'alert-danger');
            $this->session->set_flashdata('mensaje', $mensaje);
        }

        $this->_Css();
        $this->assets->agregar_css(array(
            'public/assets/css/modules/stylesheets/animacion.css'
        ));
        $this->_js();
        $this->assets->agregar_js(array(
            'public/assets/js/modules/cheques/service.cheque.js',
            'public/assets/js/modules/cheques/crearCheque.controller.js',
        ));


        $this->assets->agregar_var_js(array(
            "habilitar_formulario"  => 'si', //Solo se usa en edicion
            "vista"     => 'crear',
            "acceso"    => $acceso == 0? $acceso : $acceso,
            "pago_uuid" => (isset($pago_uuid) and !empty($pago_uuid)) ? $pago_uuid : ''
        ));

        $data               = array();
        $data['mensaje']    = $mensaje;

        $breadcrumb = array(
            "titulo" => '<i class="fa fa-shopping-cart"></i> Cheque: Crear',
            "menu" => array(
              "opciones" => []
          ),
          "ruta" => array(
              0 => array(
                "nombre" => "Ventas",
                "activo" => false
              ),
              1 => array(
                "nombre" => 'Cheques',
                'url'=>'cheques/listar',
                "activo" => false
              ),
              2 => array(
                "nombre" => '<b>Crear</b>',
                "activo" => true
              )
            )
        );


        $this->template->agregar_titulo_header('Crear Cheque');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();

    }

    public function ver($uuid_cheque){
        $acceso = 1;
        $mensaje = array();
        if(!$this->auth->has_permission('acceso', "cheques/ver/(:any)")){
            $acceso = 0;
            $mensaje = array('estado'=>500, 'mensaje'=>'<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud','clase'=>'alert-danger');
            $this->session->set_flashdata('mensaje', $mensaje);
        }

        $this->_Css();
        $this->assets->agregar_css(array(
            'public/assets/css/modules/stylesheets/animacion.css'
        ));
        $this->_js();
        $this->assets->agregar_js(array(
            'public/assets/js/modules/cheques/service.cheque.js',
            'public/assets/js/modules/cheques/crearCheque.controller.js',
            'public/assets/js/modules/cheques/crear.js',
        ));

        $cheque = $this->chequesRepository->findByUuid($uuid_cheque);
        if($cheque->pago->formulario != 'transferencia'){
          $cheque->load('pago', 'pago.proveedor', 'chequera','comentario_timeline','cheques_asignados');
        }
        else{
          $cheque->load('pago',  'pago.proveedor','pago.transferencias.caja.responsable', 'chequera','comentario_timeline','cheques_asignados');
         }
        if($cheque->chequera == null){
            $habilitar_formulario = 'si';
        }else{
            $habilitar_formulario =  'no';
        }
         $this->assets->agregar_var_js(array(
            "habilitar_formulario"  => $habilitar_formulario,
            "vista"     => 'ver',
            "cheque"    => $cheque,
            "acceso"    => $acceso == 0? $acceso : $acceso,
            "pago_uuid" => '',
            "coment" =>(isset($cheque->comentario_timeline)) ? $cheque->comentario_timeline : "",
            "cheque_id"=>$cheque->id
        ));
        $data['mensaje'] = $mensaje;


        $breadcrumb = array(
          "titulo" => '<i class="fa fa-shopping-cart"></i> Cheque: Ver',
          "menu" => array(
              "opciones" => []
          ),
            "ruta" => array(
              0 => array(
                "nombre" => "Ventas",
                "activo" => false
              ),
              1 => array(
                "nombre" => 'Cheques',
                'url'=>'cheques/listar',
                "activo" => false
              ),
              2 => array(
                "nombre" => '<b>Detalle</b>',
                "activo" => true
              )
            ),
            "menu" => array(
              "nombre" => "Crear",
              "url"	 => "cheques/crear",
              "opciones" => array()
            )
       );

        if($cheque->imprimible)
        {
            $breadcrumb["menu"]["opciones"]["cheques/imprimir/".$cheque->uuid_cheque] = "Imprimir";
        }

        if($cheque->anulable)
        {
            $breadcrumb["menu"]["opciones"]["#anular_cheque"] = "Anular";
        }


        $this->template->agregar_titulo_header('Ver Cheque');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();

    }

  function ocultoformulario($cheques = array()){

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

    $data['vendedores'] = $vendedores;
    $data['unidades'] = array();
    $data["categorias"] = Categorias_orm::categoriasConItems($this->empresa_id);
    $data['precios'] = Precios_orm::where($clause_precios)->get(array('id','uuid_precio','nombre'));
    //$data['items'] = Items_orm::where($clause_precios)->get(array('id','uuid_item','uuid_activo','nombre','codigo'));
    $impuesto = Impuestos_orm::where($clause_impuesto)->whereHas('cuenta',function($query) use($clause_impuesto){
         $query->activas();
         $query->where('empresa_id','=',$clause_impuesto['empresa_id']);
     })->get(array('id','uuid_impuesto','nombre','impuesto'));
    $data['impuestos'] = $impuesto;
    $data['cuenta_activo'] = Cuentas_orm::transaccionalesDeEmpresa($this->empresa_id)->deTipoDeCuenta([4])->activas()->get();
    $data['clientes'] = Cliente_orm::where($clause)->get(array('id','nombre','credito_favor'));
    $data['bodegas'] = Bodegas_orm::where(array('empresa_id'=>$this->empresa_id,'estado'=>1))->get(array('id','nombre'));
      $data['tipo_pagos']     = Pago_catalogos_orm::where(array('tipo'=>'pago','valor'=>'cheque'))->get(array('id','etiqueta','valor'));
      $data['proveedores']    = Proveedores_orm::deEmpresa($this->empresa_id)->get(array('id','nombre', 'limite_credito'));
      $data["chequeras"] = Chequeras_orm::where($clause)->get(array('uuid_chequera','nombre'));


      $ids_centros = Centros_orm::where($clause_impuesto)->lists('padre_id');
    //lista de centros contables
    $centros_contables = Centros_orm::whereNotIn('id', $ids_centros->toArray())->where(function($query) use($clause_impuesto){
      $query->where($clause_impuesto);
    })->get(array('id','nombre','uuid_centro'));
    $data['centros_contables']= $centros_contables;

    if(isset($cheques['info']))$data['info'] = $cheques['info'];

    $this->load->view('formulario', $data);
  }

    public function guardar(){



        if($_POST){


            $request = Illuminate\Http\Request::createFromGlobals();
            $array_cheque = $request->input('campo');

            $this->disparador = new \Illuminate\Events\Dispatcher();
            Capsule::beginTransaction();

            try{
                $chequera   = $this->chequeraRepository->findByUuid($array_cheque['chequera_id']);

                if(!empty($array_cheque['id'])){ //edicion

                  $array_update['id']    = $array_cheque['id'];
                  $array_update['numero']    = $array_cheque['numero'];
                  $array_update['chequera_id']    = $chequera->id;
                  $array_update['fecha_cheque']   = date("Y-m-d H:i:s", strtotime($array_cheque["fecha_pago"]));

                  $cheque = $this->chequesRepository->editar($array_update);
                  $this->chequeraRepository->incrementa_secuencial($chequera->id);

                }else{ //Creando

                  $pago       = $this->pagosRep->findBy(["uuid_pago" => $request->input("crear_desde"), "empresa_id" => $this->empresa_id]);
                  $pago->metodo_pago[0]->referencia = ['numero_cheque' => $array_cheque['numero'], 'nombre_banco_cheque' => $chequera->cuenta->nombre];
                  $pago->metodo_pago[0]->save();

                  $array_cheque['empresa_id']     = $this->empresa_id;
                  $array_cheque['chequera_id']    = $chequera->id;
                  $array_cheque['pago_id']        = $pago->id;
                  $array_cheque['fecha_cheque']   = date("Y-m-d H:i:s", strtotime($array_cheque["fecha_pago"]));
                  $array_cheque['estado_id'] = 1; //se crea el cheque por imprimir

                  $cheque = $this->chequesRepository->crear($array_cheque);
                  $this->chequeraRepository->incrementa_secuencial($chequera->id);
                }

            }catch(Illuminate\Database\QueryException $e){
                log_message('error', __METHOD__." ->". ", Linea: ". __LINE__ ." --> ". $e->getMessage()."\r\n");
                Capsule::rollback();
                $mensaje = array('estado'=>500, 'mensaje'=>'<b>¡Error! Su solicitud no fue procesada</b> ');
                $this->session->set_flashdata('mensaje', $mensaje);
                redirect(base_url('cheques/listar'));
            }
            Capsule::commit();

            if(!is_null($cheque)){
                $mensaje = array('estado' => 200, 'mensaje' =>'<b>¡&Eacute;xito!</b> Se ha guardado correctamente '.$cheque->codigo);
            }else{
                $mensaje = array('estado'=>500, 'mensaje'=>'<b>¡Error! Su solicitud no fue procesada</b> ');
            }
            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('cheques/listar'));
        }

    }

  private function _generar_codigo(){
    $clause_empresa = ['empresa_id'=>$this->empresa_id];
    $total = $this->chequesRepository->lista_totales($clause_empresa);
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

    function ajax_info_chequera (){
        $uuid = $this->input->post('chequera_uuid');
        $chequera = $this->chequeraRepository->findByUuid($uuid);
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($chequera->toArray()))->_display();
        exit;
    }

    function ajax_info_cheque (){
        $uuid = $this->input->post('uuid');
        $cheques= $this->chequesRepository->findByUuid($uuid);
        $chequera=Chequeras_orm::where(array('id'=>$cheques->chequera_id))->get(array('uuid_chequera','nombre'));
        $attributes = $chequera->toArray();
        $cheques->chequera_uuid=bin2hex($attributes[0]["uuid_chequera"]);
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($cheques->toArray()))->_display();
        exit;
    }

    function ajax_cheque_pago (){
        $uuid = $this->input->post('uuid');
        $cheque = $this->chequesRepository->findByUuid($uuid);
        $pago = Pagos_orm::find(array("id"=>$cheque->pago_id));
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($pago->toArray()))->_display();
        exit;
    }

    public function ajax_anular_cheque (){
        $cheque_id  = $this->input->post("cheque_id");
        $data       = [
            "success"   => $this->chequesRepository->anular_cheque($cheque_id)
        ];

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($data))->_display();
        exit;
    }

  function ajax_getAll(){
    if(!$this->input->is_ajax_request()){
      return false;
    }
    $clause = ['empresa_id' => $this->empresa_id,'formulario' =>['cheque_venta','orden_venta','contrato_venta'],'estado'=>['cobrado_completo']];
    $cheques = $this->chequeVentaRepository->sinDevolucion($clause);
    $cheques->load('cliente','items','items.inventario_item','items.inventario_item.unidades','items.impuesto');
    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($cheques->toArray()))->_display();
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
      'public/assets/css/modules/stylesheets/cheques.css',
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

    public function ajax_pagos_cheques(){
        $vista = $this->input->post('vista');

        $pagos      = Pagos_orm::deEmpresa($this->empresa_id);
        $resultados = array();

        if($vista == 'crear')
        {
            $pagos->deFormaPago('cheque');
            $pagos->deEstado('por_aplicar');
        }

        foreach($pagos->get() as $pago){
            $resultados[]= array('uuid'=>$pago->uuid_pago,'codigo'=>$pago->codigo.' - '.$pago->proveedor->nombre);
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($resultados))->_display();
        exit;
    }

    function ajax_pago_info()
    {
        $uuid       = $this->input->post('uuid');
        $pagoObj = new Buscar(new Pagos_orm,'uuid_pago');
        $pago = $pagoObj->findByUuid($uuid);

        $proveedorObj2   = new Buscar(new Proveedores_orm,'uuid_proveedor');
        $proveedorObj = Proveedores_orm::where(array('id'=>$pago->proveedor_id))->get();
        $arr_proveedor=$proveedorObj->toArray();
        $proveedor= $proveedorObj2->findByUuid($arr_proveedor[0]["uuid_proveedor"]);

        foreach($proveedor->facturasCrear as $l){
                $l->pagos = $l->pagos_aplicados;
            }
        $pago->proveedor_cheque=$proveedor;
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($pago->toArray()))->_display();
        exit;
    }

    function ajax_pago_proveedor(){

        $uuid   = $this->input->post('uuid');
        $vista  = $this->input->post('vista');


        $pagoObj   = new Buscar(new Pagos_orm,'uuid_pago');
        $pago      = $pagoObj->findByUuid($uuid);

        $proveedorObj   = new Buscar(new Proveedores_orm,'id');
        $proveedor      = $proveedorObj->findById($pago->proveedor_id);



        if($vista =='crear'){
            foreach($proveedor->facturasCrear as $l){
                $l->pagos = $l->pagos_aplicados;
            }
        }elseif($vista =='ver'){
            foreach($proveedor->facturasNoAnuladas as $l){//no esta aun en el modelo
                $l->pagos = $l->pagos_aplicados;
            }
        }elseif($vista =='registrar_pago_pago'){
            foreach($proveedor->facturasHabilitadas as $l){//no esta aun en el modelo
                $l->pagos = $l->pagos_aplicados;
            }
        }


        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($proveedor->toArray()))->_display();
        exit;
    }

    public function ajax_cambiando_estado(){

      // Just Allow ajax request
      if (! $this->input->is_ajax_request ()) {
       return false;
      }

      Capsule::beginTransaction();

      try {
        $data_uuid   = $this->input->post('data_uuid');
        $cheque =$this->chequesRepository->findByUuid($data_uuid);
        $cheque->pago->chequeEnTransito();

        $this->chequesRepository->update($data_uuid,array("estado_id"=>2));
      } catch(ValidationException $e){
        Capsule::rollback();
          echo json_encode(array(
            "response" => false,
            "mensaje" => "Hubo un error tratando de actualizar el registro."
          ));
       exit;
      }
      Capsule::commit();

      echo json_encode(array(
         "response" => true,
         "mensaje" => "Se ha actualizado el registro satisfactoriamente."
      ));
      exit;
     }
    public function imprimir($uuid=null)
    {
        if($uuid==null){
            return false;
        }

        $cheque =$this->chequesRepository->findByUuid($uuid);

       $cheque->estado_id = 2;
       $cheque->save();

        $cheque->pago->estado = 'cheque_en_transito'; //Se cambia el flujo
        $cheque->pago->save();

        $dompdf = new Dompdf();

        $data   = array(
            "cheque"=>$cheque
        );


      //  Capsule::beginTransaction();
      //  $this->chequesRepository->update($uuid,array("estado_id"=>2));
    //    $cheque->pago->aplicar();
    //    Capsule::commit();

        $html = $this->load->view('cheque', $data, true);


        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream($cheque->chequera->nombre.$cheque->numero);
    }

    function ocultoformulariocomentarios() {

        $data = array();

        $this->assets->agregar_js(array(
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/modules/cheques/vue.comentario.js',
            'public/assets/js/modules/cheques/formulario_comentario.js'
        ));

        $this->load->view('formulario_comentarios');
        $this->load->view('comentarios');

    }
    function ajax_guardar_comentario() {

        if(!$this->input->is_ajax_request()){
            return false;
        }
        $model_id   = $this->input->post('modelId');
        $comentario = $this->input->post('comentario');
        $comentario = ['comentario'=>$comentario,'usuario_id'=>$this->usuarioId];
        $cheques = $this->chequesRepository->agregarComentario($model_id, $comentario);
        $cheques->load('comentario_timeline');

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($cheques->comentario_timeline->toArray()))->_display();
        exit;
    }
}

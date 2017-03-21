<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Erp
 * @subpackage Controller
 * @category   Contratos
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  10/22/2015
 */

use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\Contratos\Repository\ContratoRepository as ContratoRepository;
use Flexio\Modulo\Contratos\Repository\AdendaRepository as AdendaRepository;
use Flexio\Modulo\Contratos\Events\ActualizarContratoMontoEvent as ActualizarContratoMontoEvent;
use Flexio\Modulo\Contratos\Listeners\ActualizarContratoListener as ActualizarContratoListener;
use Flexio\Modulo\FacturasVentas\Models\FacturaVenta as FacturaVenta; //dath
use Flexio\Modulo\OrdenesVentas\Models\OrdenVenta as OrdenVenta; //datth
use  Flexio\Modulo\Cliente\Repository\ClienteRepository as ClienteRepository;

class Contratos extends CRM_Controller{

  private $empresa_id;
  private $empresaObj;
  protected $contratosRepositorio;
  protected $adendaRepository;
  protected $disparador;
  protected $facturaVenta; //dath
  protected $ClienteRepository;
            function __construct(){
    parent::__construct();

    $this->load->model('usuarios/Empresa_orm');
    $this->load->model('usuarios/Usuario_orm');
    $this->load->model('clientes/Cliente_orm');
    $this->load->model('contabilidad/Cuentas_orm');
    $this->load->model('contabilidad/Centros_orm');
    $this->load->model('facturas/Factura_orm');
    $this->load->model('cobros/Cobro_orm');

    $uuid_empresa = $this->session->userdata('uuid_empresa');
    $empresaObj  = new Buscar(new Empresa_orm,'uuid_empresa');
		$this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
	  $this->empresa_id = $this->empresaObj->id;
    $this->contratosRepositorio =  new ContratoRepository;
    $this->adendaRepository =  new AdendaRepository;
    $this->disparador = new \Illuminate\Events\Dispatcher();
    $this->disparador->listen([ActualizarContratoMontoEvent::class], ActualizarContratoListener::class);

    $this->facturaVenta = new FacturaVenta; //dath
    $this->ordenVenta = new OrdenVenta; //dath
    $this->ClienteRepository = new ClienteRepository();
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

    $breadcrumb = array( "titulo" => '<i class="fa fa-line-chart"></i> Contratos de Ventas',
        "ruta" => array(
          0 => [
            "nombre" => "Ventas",
            "activo" => false
          ],
          1 => ["nombre" => '<b>Contratos de venta</b>',"activo" => true]
        ),
        "menu" => [
          "nombre" => "Crear","url"	 => "contratos/crear",
          "opciones" => array()
        ]
   );
   $this->assets->agregar_var_js(array(
     "toast_mensaje" => $mensaje
   ));
   $clause = array('empresa_id' => $this->empresa_id);

   $clientes = new Cliente_orm;
   $centros = new Centros_orm;
   //dd($clientes);
   $data['clientes']= $clientes->clientesConContratos($clause);
   $data['centros_contables']= $centros->centrosConContratos($clause);
   $breadcrumb["menu"]["opciones"]["#exportarListaContratos"] = "Exportar";
   $this->template->agregar_titulo_header('Listado de Contratos');
   $this->template->agregar_breadcrumb($breadcrumb);
   $this->template->agregar_contenido($data);
   $this->template->visualizar($breadcrumb);

  }


  public function ocultotabla($uuid=NULL,$modulo=NULL){

    $this->assets->agregar_js(array(
      'public/assets/js/modules/contratos/tabla.js'
    ));
    $this->load->view('tabla');
  }

  public function ajax_listar(){
    if(!$this->input->is_ajax_request()){
      return false;
    }

    $cliente = $this->input->post('cliente',TRUE);
    $uuid_cliente = $this->input->post('cliente_id',TRUE);
    $monto = $this->input->post('monto_original',TRUE);
    $codigo = $this->input->post('numero_contrato',TRUE);
    $centro = $this->input->post('centro',TRUE);

    $clause = array('empresa_id' => $this->empresa_id);

    if(!empty($uuid_cliente)){
      $clienteObj  = new Buscar(new Cliente_orm,'uuid_cliente');
      $cliente = $clienteObj->findByUuid($uuid_cliente);
      $clause['cliente_id'] = $cliente->id;
    }elseif(!empty($cliente)){
      $clause['cliente_id'] = $cliente;
    }

    if(!empty($monto)) $clause['monto_original'] = $monto;
    if(!empty($codigo)) $clause['codigo'] = $codigo;
    if(!empty($centro)) $clause['centro_id'] = $centro;

    list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
    $count = $this->contratosRepositorio->lista_totales($clause); // funcion del repositorio
    list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
    $contratos = $this->contratosRepositorio->listar($clause ,$sidx, $sord, $limit, $start); //funcion del repositorio
    $response = new stdClass();
    $response->page     = $page;
    $response->total    = $total_pages;
    $response->records  = $count;
    if(!empty($contratos->toArray())){
      $i=0;

      foreach ($contratos as $row) {
        $sumatoria = $row->facturas->sum('total');
        $hidden_options = "";
        $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row->uuid_contrato .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
        $hidden_options = '<a href="'. base_url('contratos/ver/'. $row->uuid_contrato) .'" data-id="'. $row->uuid_contrato .'" class="btn btn-block btn-outline btn-success">Ver Contrato</a>';
        $hidden_options .= '<a href="'. base_url('anticipos/crear?contrato='.$row->uuid_contrato) .'" data-id="'. $row->uuid_contrato .'" class="btn btn-block btn-outline btn-success">Crear anticipo</a>'; //COMENTADO TEMPORALMENTE HASTA NUEVO AVISO
        $hidden_options .= '<a href="'. base_url('contratos/agregar_adenda/'. $row->uuid_contrato) .'" data-id="'. $row->uuid_contrato .'" class="btn btn-block btn-outline btn-success">Crear adenda</a>';
        $response->rows[$i]["id"] = $row->uuid_contrato;
        $response->rows[$i]["cell"] = array(
           $row->uuid_contrato,
           '<a class="link" href="'. base_url('contratos/ver/'. $row->uuid_contrato) .'">'.$row->codigo.'</a>',
           '<a class="link">'.$row->cliente->nombre.' '.$row->cliente->apellido.'</a>',
           $row->present()->monto_original,
           $row->present()->monto_adenda,
           $row->present()->monto_contrato,
           $row->present()->facturado,
           $row->present()->por_facturar,
           $row->centro_contable->nombre,
           $row->referencia,
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

  public function ajax_listar_adendas(){
    if(!$this->input->is_ajax_request()){
      return false;
    }

    $contrato_id = $this->input->post('contrato_id',TRUE);
    $clause = array('empresa_id' => $this->empresa_id);

    if(!empty($contrato_id)) $clause['contrato_id'] = $contrato_id;

    list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
    $count = $this->adendaRepository->lista_totales($clause); // funcion del repositorio
    list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
    $adendas = $this->adendaRepository->listar($clause ,$sidx, $sord, $limit, $start); //funcion del repositorio
    $response = new stdClass();
    $response->page     = $page;
    $response->total    = $total_pages;
    $response->records  = $count;
    if(!empty($adendas->toArray())){
      $i=0;
      foreach ($adendas as $row) {
        $hidden_options = "";
        $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row->uuid_adenda .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';


        $hidden_options = '<a href="'. base_url('contratos/editar_adenda/'. $row->uuid_adenda) .'" data-id="'. $row->uuid_adenda .'" class="btn btn-block btn-outline btn-success">Ver Adenda</a>';
        $response->rows[$i]["id"] = $row->uuid_adenda;
        $response->rows[$i]["cell"] = array(
           $row->uuid_adenda,
           '<a style="color:blue;" class="link" href="'. base_url('contratos/editar_adenda/'. $row->uuid_adenda) .'">'.$row->codigo.'</a>',
           $row->fecha,
           "$".number_format($row->monto_adenda, 2, '.', ','),
           "$".number_format($row->monto_acumulado, 2, '.', ','),
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

    public function editar_adenda($uuid = null)
    {
        $acceso         = 1;
        $mensaje        = array();
        $data           = array();

        $clause         = [];
        $clause["empresa_id"]   = $this->empresa_id;
        $clause["uuid_adenda"]  = $uuid;
        $adenda                 = $this->adendaRepository->findBy($clause);
        $contrato               = $this->contratosRepositorio->findByUuid($adenda->contrato->uuid_contrato);

        //dd($subcontrato);
        if(!$this->auth->has_permission('acceso','contratos/editar_adenda/(:any)') && !is_null($contrato))
        {
            // No, tiene permiso
            $acceso = 0;
            $mensaje = array(
                'estado'  =>500,
                'mensaje' => ' <b>Usted no cuenta con permiso para esta solicitud</b>',
                'clase'   => 'alert-danger'
            );
        }
        $this->_Css();
        $this->_js();
        $this->assets->agregar_js(array(
            'public/assets/js/default/vue.js',
            'public/assets/js/default/vue-validator.min.js',
            'public/assets/js/default/vue-resource.min.js',
            'public/assets/js/modules/contratos/formulario_ver.js',
            'public/assets/js/modules/contratos/eventos.js',
        ));
        $contrato->load('contrato_montos', 'tipo_abono', 'tipo_retenido', 'cliente', 'adenda');
        $adenda->load('adenda_montos','comentario');
        $data['adenda']     = $adenda->toArray();
        $data['contrato']   = $contrato->toArray();
        $this->assets->agregar_var_js(array(
            "vista"         => 'editar',
            "acceso"        => $acceso,
            "contrato"      => $contrato,
            "adenda"        => $adenda,
            "cliente_id"    => $contrato['cliente']['uuid_cliente'],
        ));
        $breadcrumb = array(
            //"titulo" => '<i class="fa fa-file-text"></i> Adenda: ' .$subcontrato->codigo. ' / Crear',
            "titulo" => '<i class="fa fa-file-text-o"></i> Adenda: '.$adenda->codigo,
            "ruta" => array(
                0 => ["nombre" => "Ventas", "activo" => false], // VENTAS ??????????????????
                1 => ["nombre" => 'Contratos', "activo" => false, 'url'=>'contratos/listar'],
                2 => ["nombre" =>  $contrato->codigo, "activo" => false, 'url'=>'contratos/ver/'.$adenda->contrato->uuid_contrato],
                3 => ["nombre" => '<b>Adenda detalle</b>', "activo" => true],
                 ),
            "menu" => []
        );

        $this->template->agregar_titulo_header('Contrato');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }

  public function crear(){
    $acceso = 1;
    $mensaje = array();
    if(!$this->auth->has_permission('acceso','contratos/crear')){
      // No, tiene permiso
        $acceso = 0;
        $mensaje = array('estado'=>500, 'mensaje'=>' <b>Usted no cuenta con permiso para esta solicitud</b>','clase'=>'alert-danger');
    }

    $this->_Css();
    //$this->assets->agregar_css(array('public/assets/css/modules/stylesheets/animacion.css'));
    $this->_js();
    $this->assets->agregar_js(array(
      'public/assets/js/default/vue.js',
      'public/assets/js/modules/contratos/formulario_crear.js',
    ));

    $this->assets->agregar_var_js(array(
      "vista" => 'crear',
      "acceso" => $acceso
    ));

    $data=array();
    /*$breadcrumb = array(

    );*/
    $breadcrumb = array(
    "titulo" => '<i class="fa fa-line-chart"></i> Crear contrato',
    "ruta" => array(
      0 => array(
        "nombre" => "Ventas",
        "activo" => false,
      ),
      1 => array(
        "nombre" => "Contratos de venta",
        "activo" => false,
        "url" => 'contratos/listar'
      ),
      2=> array(
        "nombre" => '<b>Crear</b>',
        "activo" => true
      )
    ),
    );
    $this->template->agregar_titulo_header('Crear Contratos');
    $this->template->agregar_breadcrumb($breadcrumb);
    $this->template->agregar_contenido($data);
    $this->template->visualizar($breadcrumb);
  }

  public function ocultoformulario($info=[]){
    $data=[];
    $clause = ['empresa_id'=>$this->empresa_id];
    $clause_empresas = ['empresa_id'=>$this->empresa_id,'estado'=>'Activo'];
    $data['codigo'] = $this->_generar_codigo();
    $data['clientes'] = $this->ClienteRepository->getClientesEstadoActivo($clause)->get();
    //$data['clientes'] = Cliente_orm::where($clause_empresa)->get(array('id','nombre'));
    $ids_centros = Centros_orm::where($clause_empresas)->lists('padre_id');
    //lista de centros contables
    $centros_contables = Centros_orm::whereNotIn('id', $ids_centros->toArray())->where(function($query) use($clause_empresas){
      $query->where($clause_empresas);
    })->get(array('id','nombre','uuid_centro'));
    $data['info'] = $info;
    $data['cuenta_ingreso'] = Cuentas_orm::transaccionalesDeEmpresa($this->empresa_id)->deTipoDeCuenta([4])->activas()->get();
    $data['cuentas'] = Cuentas_orm::transaccionalesDeEmpresa($this->empresa_id)->activas()->get();
    $data['centros_contables']= $centros_contables;
    $this->load->view('formulario',$data);
  }

  function ocultoformularioAdenda($info=[]){
    $this->assets->agregar_js(array(
        'public/assets/js/plugins/ckeditor/ckeditor.js',
        'public/assets/js/plugins/ckeditor/adapters/jquery.js',
        'public/assets/js/modules/contratos/tabla-componente.js',
        'public/assets/js/modules/contratos/vue.comentario.js',
        'public/assets/js/modules/contratos/formulario_adenda.js'
    ));
    $data['codigo'] = $this->_generar_codigo_adenda();
    $data['info'] = $info;
    $data['cuenta_ingreso'] = Cuentas_orm::transaccionalesDeEmpresa($this->empresa_id)->deTipoDeCuenta([4])->activas()->get();
    $this->load->view('formulario_adenda',$data);
    $this->load->view('componente',$data);
    $this->load->view('notas_creditos/comentarios');
  }

  function ver($uuid=null){
    $acceso = 1;
    $mensaje = array();
    $data=array();
    $contrato = $this->contratosRepositorio->findByUuid($uuid);
      $contrato->load('comentario_timeline');
    if(!$this->auth->has_permission('acceso','contratos/ver/(:any)') && !is_null($contrato)){
      // No, tiene permiso
        $acceso = 0;
        $mensaje = array('estado'=>500, 'mensaje'=>' <b>Usted no cuenta con permiso para esta solicitud</b>','clase'=>'alert-danger');
    }

    $this->_Css();
    $this->_js();
    $this->assets->agregar_js(array(
      'public/assets/js/default/vue.js',
        'public/assets/js/plugins/ckeditor/ckeditor.js',
        'public/assets/js/plugins/ckeditor/adapters/jquery.js',
      'public/assets/js/modules/contratos/formulario_ver.js',
      'public/assets/js/modules/contratos/eventos.js',
        'public/resources/compile/modulos/contratos/comentario-contratos.js'
    ));
    $contrato->load('contrato_montos','tipo_abono','tipo_retenido','cliente','adenda');
    $data['contrato']=$contrato->toArray();
    $this->assets->agregar_var_js(array(
      "vista" => 'ver',
      "acceso" => $acceso,
      "contrato" => $contrato,
      "cliente_id" => $contrato['cliente']['uuid_cliente'],
      "coment_contrato" => (isset($contrato->comentario_timeline)) ? $contrato->comentario_timeline : "",
      "contrato_coment_id" => $contrato->id
    ));

    $data['contrato_id'] = $contrato->id;
    $breadcrumb = array(
      "titulo" => '<i class="fa fa-line-chart"></i> Contrato de venta:' .$contrato->codigo,
      "ruta" => array(
        0 => [
          "nombre" => "Ventas",
          "activo" => false
        ],
        //1 => ["nombre" => '<b>Contratos de venta</b>',"activo" => true],
        1 => array(
							"nombre" => "Contratos de venta",
							"activo" => false,
							"url" => 'contratos/listar'
						),

        2=> array(
          "nombre" => '<b>Detalle</b>',
          "activo" => true
        )
      ),
      "menu" => [
        "nombre" => "Acci&oacute;n","url"	 => "#",
        "opciones" => array('/contratos/agregar_adenda/'.$contrato->uuid_contrato =>'Crear Adenda',
        /*'#exportar_adenda'=>'Exportar Adenda'*/) //COMENTADO TEMPORALMENTE HASTA NUEVO AVISO
      ]
    );



    $this->template->agregar_titulo_header('Contrato de venta');
    $this->template->agregar_breadcrumb($breadcrumb);
    $this->template->agregar_contenido($data);
    $this->template->visualizar($breadcrumb);
  }

  function agregar_adenda($uuid=null){

    $acceso = 1;
    $mensaje = array();
    $data=array();
    $contrato = $this->contratosRepositorio->findByUuid($uuid);
    if(!$this->auth->has_permission('acceso','contratos/agregar_adenda/(:any)') && !is_null($contrato)){
      // No, tiene permiso
        $acceso = 0;
        $mensaje = array('estado'=>500, 'mensaje'=>' <b>Usted no cuenta con permiso para esta solicitud</b>','clase'=>'alert-danger');
    }

    $this->_Css();
    $this->_js();
    $this->assets->agregar_js(array(
      'public/assets/js/default/vue.js',
      'public/assets/js/modules/contratos/formulario_ver.js',
      'public/assets/js/modules/contratos/eventos.js',
    ));
    $contrato->load('contrato_montos','tipo_abono','tipo_retenido','cliente','adenda');
    $data['contrato']=$contrato->toArray();
    $this->assets->agregar_var_js(array(
      "vista" => 'ver',
      "acceso" => $acceso,
      "contrato" => $contrato,
      "cliente_id" => $contrato['cliente']['uuid_cliente']
    ));
    $breadcrumb = array(
        //"titulo" => '<i class="fa fa-file-text"></i> Adenda: ' .$subcontrato->codigo. ' / Crear',
        "titulo" => '<i class="fa fa-file-text-o"></i> Contratos: adendas',
        "ruta" => array(
            0 => ["nombre" => "Ventas", "activo" => false], // VENTAS ??????????????????
            1 => ["nombre" => 'Contratos', "activo" => false, 'url'=>'contratos/listar'],
            2 => ["nombre" =>  $contrato->codigo, "activo" => false, 'url'=>'contratos/ver/'.$uuid],
            3 => ["nombre" => '<b>Adenda crear</b>', "activo" => true],
             ),
        "menu" => []
    );

  /*  $breadcrumb = array(
      "titulo" => '<i class="fa fa-line-chart"></i> Adenda: ' .$contrato->codigo. ' / Crear',
      "ruta" => array(
        0 => ["nombre" => "Ventas", "activo" => false],
        1 => ["nombre" => '<b>Contrato de venta</b>',"activo" => false],
        2 => ["nombre" => '<b>'.$contrato->codigo.' / Adenda/ Crear</b>',"activo" => false]
      ),
      "menu" => []
    );*/

    $this->template->agregar_titulo_header('Contrato de venta');
    $this->template->agregar_breadcrumb($breadcrumb);
    $this->template->agregar_contenido($data);
    $this->template->visualizar($breadcrumb);
  }

  function ocultoTablaAdendas($id=null,$modulo=NULL){

    $this->assets->agregar_js(array(
      'public/assets/js/modules/contratos/tabla_adendas.js'
    ));
    if(!is_null($id)){
      $this->assets->agregar_var_js(array(
           "contrato_id" => $id
       ));
    }
    $this->load->view('tabla_adendas');
  }

  private function _generar_codigo(){
    $clause_empresa = ['empresa_id'=>$this->empresa_id];
    $total = $this->contratosRepositorio->lista_totales($clause_empresa);
    $year = Carbon::now()->format('y');
    $codigo = Util::generar_codigo('CA'.$year,$total + 1);
    return $codigo;
  }

  private function _generar_codigo_adenda(){
    $clause_empresa = ['empresa_id'=>$this->empresa_id];
    $total = $this->adendaRepository->lista_totales($clause_empresa);
    $year = Carbon::now()->format('y');
    $codigo = Util::generar_codigo('AD'.$year,$total + 1);
    return $codigo;
  }

  function guardar(){
      if($_POST){
        $array_contrato = Util::set_fieldset("campo");
        $array_contrato['fecha_inicio'] = $_POST['campo']['fecha_inicio'];
        $array_contrato['fecha_final'] = $_POST['campo']['fecha_final'];
        $array_contrato['codigo'] = $this->_generar_codigo();
        //dd($_POST['campo']['fecha_inicio']);
        $array_contrato['fecha_inicio'] = Carbon::createFromFormat('m/d/Y',$array_contrato['fecha_inicio'],'America/Panama');
        $array_contrato['fecha_final'] = Carbon::createFromFormat('m/d/Y',$array_contrato['fecha_final'],'America/Panama');
        $array_contrato['empresa_id'] = $this->empresa_id;
        $array_contrato_abono = Util::set_fieldset("abono");
        $array_contrato_abono['tipo']="abono";
        $array_contrato_abono['empresa_id'] = $this->empresa_id;
        $array_contrato_retenido = Util::set_fieldset("retenido");
        $array_contrato_retenido['empresa_id'] = $this->empresa_id;
        $array_contrato_retenido['tipo']='retenido';
        $fieldset_item = [];
        $j=0;
        foreach ($_POST["items"] as $item){
          $fieldset_item[$j]= Util::set_fieldset("items", $j);
          $fieldset_item[$j]['empresa_id'] = $this->empresa_id;
          $j++;
        }

      $create=array('contrato'=>$array_contrato,'abono'=>$array_contrato_abono,'retenido'=>$array_contrato_retenido, 'montos'=>$fieldset_item);
      $contrato = Capsule::transaction(function() use($create){
        try{
          return $this->contratosRepositorio->create($create);
        }catch(Illuminate\Database\QueryException $e){
          log_message('error', __METHOD__." ->". ", Linea: ". __LINE__ ." --> ". $e->getMessage()."\r\n");
        }
      });
      if(!is_null($contrato)){
        $mensaje = array('estado' => 200, 'mensaje' =>'<b>¡&Eacute;xito!</b> Se ha guardado correctamente '.$contrato->codigo);
      }else{
        $mensaje = array('estado'=>500, 'mensaje'=>'<b>¡Error! Su solicitud no fue procesada</b> ');
      }
        $this->session->set_flashdata('mensaje', $mensaje);
        redirect(base_url('contratos/listar'));
      }
  }

  function guardar_adenda(){
    if($_POST){

      $usuario = Usuario_orm::findByUuid($this->uuid_usuario);

      $array_adenda = Util::set_fieldset("campo");
      $contrato = $this->contratosRepositorio->findBy($array_adenda['contrato_id']);
      $array_adenda['fecha'] = $_POST['campo']['fecha'];
      $array_adenda['codigo']          = (isset($array_adenda["codigo"]) and !empty($array_adenda["codigo"])) ? $array_adenda["codigo"] : $this->_generar_codigo_adenda();
      $array_adenda['fecha'] = Carbon::createFromFormat('m/d/Y',$array_adenda['fecha'],'America/Panama');
      $array_adenda['empresa_id'] = $this->empresa_id;
      $array_adenda['monto_acumulado'] = $contrato->monto_contrato + $array_adenda['monto_adenda'];
      $array_adenda['usuario_id'] = $usuario->id;
      $fieldset_item = [];
      $j=0;
      foreach ($_POST["components"] as $item){
        $fieldset_item[$j]= Util::set_fieldset("components", $j);
        $fieldset_item[$j]['empresa_id'] = $this->empresa_id;
        $j++;
      }
      $create = array('adenda'=>$array_adenda, 'montos'=>$fieldset_item);
      $adenda = Capsule::transaction(function() use($create){
                    try{
                      return $this->adendaRepository->create($create);
                    }catch(Illuminate\Database\QueryException $e){
                      log_message('error', __METHOD__." ->". ", Linea: ". __LINE__ ." --> ". $e->getMessage()."\r\n");
                    }
                });
      if(!is_null($adenda)){
        $this->disparador->fire(new ActualizarContratoMontoEvent($adenda,$contrato));
        $mensaje = array('estado' => 200, 'mensaje' =>'<b>¡&Eacute;xito!</b> Se ha creado la adenda '.$adenda->codigo);
      }else{
        $mensaje = array('estado'=>500, 'mensaje'=>'<b>¡Error! Su solicitud no fue procesada</b> ');
      }
        $this->session->set_flashdata('mensaje', $mensaje);
        redirect(base_url('contratos/listar'));
    }
  }

    public function ajax_guardar_comentario(){
        if(!$this->input->is_ajax_request()){
            return false;
        }

        $model_id       = $this->input->post('modelId');
        $comentario     = $this->input->post('comentario');
        $uuid_usuario   = $this->session->userdata('huuid_usuario');
        $usuario        = Usuario_orm::findByUuid($uuid_usuario);
        $comentarioArr  = ['comentario'=>$comentario,'usuario_id'=>$usuario->id];

        $adenda         = $this->adendaRepository->agregarComentario($model_id, $comentarioArr);
        $adenda->load('comentario');

        $lista_comentario = $adenda->comentario()->orderBy('created_at','desc')->get();
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($lista_comentario->toArray()))->_display();
        exit;
    }

  function ajax_contrato_info(){
    $uuid = $tipo = $this->input->post('uuid');
    $contrato = $this->contratosRepositorio->findByUuid($uuid);
    $contrato->load('cliente');
    $contrato->toArray();

    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($contrato))->_display();
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
      'public/assets/css/modules/stylesheets/contratos.css',
      //se usa para el subpanel de facturas de venta
      'public/assets/css/modules/stylesheets/facturas.css',
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
      'public/assets/js/plugins/jquery/chosen.jquery.min.js',
      'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
      'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
      'public/assets/js/plugins/jquery/sweetalert/sweetalert.min.js',
      'public/assets/js/moment-with-locales-290.js',
      'public/assets/js/plugins/bootstrap/select2/select2.min.js',
      'public/assets/js/plugins/bootstrap/select2/es.js',
      'public/assets/js/plugins/bootstrap/daterangepicker.js',
      'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
      'public/assets/js/default/toast.controller.js',
      'public/assets/js/modules/contratos/plugins.js',
    ));
  }


}

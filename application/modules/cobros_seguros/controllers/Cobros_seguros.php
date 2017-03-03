<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
use Flexio\Modulo\Cobros_seguros\Repository\CobroRepository as CobroRepository;
use Flexio\Modulo\Cobros_seguros\Repository\MetodoCobroRepository as MetodoCobroRepository;
use Flexio\Modulo\Cobros_seguros\Models\CatalogoCobro;
use Flexio\Modulo\Cobros_seguros\Transaccion\TransaccionCobro;
use Flexio\Modulo\Cajas\Repository\CajasRepository;
use Flexio\Modulo\Cobros_seguros\HttpRequest\FormGuardar;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\Cliente\Repository\ClienteRepository as ClienteRepository;
use Flexio\Modulo\Cobros_seguros\Models\Cobros_seguros as Cobros_seg;
use Flexio\Modulo\Polizas\Models\Polizas as Polizas;
use Flexio\Modulo\Cobros_seguros\Models\CobroFactura;
use Flexio\Modulo\ComisionesSeguros\Models\ComisionesSeguros;


class Cobros_seguros extends CRM_Controller {
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
			'public/assets/js/modules/cobros_seguros/listar.js',
			'public/assets/js/default/toast.controller.js'
		));
		$breadcrumb = array( 
			"titulo" => '<i class="fa fa-line-chart"></i> Cobros',
			"ruta" => array(
				0 => array(
					"nombre" => "Seguros",
					"activo" => false
				),
				1 => array(
					"nombre" => '<b>Cobros</b>',
					"activo" => true
				)
			),
			"menu" => array(
				"nombre" => "Crear",
				"url"	 => "cobros_seguros/crear",
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

	function ajax_listar(){
		if(!$this->input->is_ajax_request()){
			return false;
		}
		$clause = [];
		$jqgrid = new Flexio\Modulo\Cobros_seguros\Services\CobroJqgrid;
		$clause['empresa'] = $this->empresa_id;
		$clause['formulario'] = 'seguros';
		
		if($this->input->post("uuid_poliza")!=""){
			$poliza=Polizas::where('uuid_polizas',hex2bin($this->input->post("uuid_poliza")))->first();
			if($poliza->id!="")
				$clause['empezable_id'] = $poliza->id;
		}
		
		if($this->input->post("codigo")!=""){
			$clause['codigo'] = $this->input->post("codigo");
		}
		
		if($this->input->post("cliente")!=""){
			$clause['clientenombre'] = $this->input->post("cliente");
		}
		
		if($this->input->post("estado")!=""){
			if($this->input->post("estado")!="0")
			{
				$clause['estado'] = $this->input->post("estado");
			}
		}
		
		if($this->input->post("metodo_pago")!=""){
			if($this->input->post("metodo_pago")!="0")
			{
				$clause['metodoPago'] = $this->input->post("metodo_pago");
			}
		}
		
		if ($this->input->post("cliente_id") <> ''){
			$client_id = (new ClienteRepository)->findByUuid($this->input->post("cliente_id"))->id;
			$clause['cliente'] = $client_id;
		}

		if (!empty($this->input->post('factura_id'))){
			$factura = (new FacturaVentaRepository)->findByUuid($_POST['factura_id']);
			$clause['factura'] = $factura->id;
		}
		//var_dump($clause);
		$response = $jqgrid->listar($clause);

		$this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response))->_display();
		exit;
	}


	function ocultotabla($uuid_orden_venta=null) {
		$this->assets->agregar_js(array(
			'public/assets/js/modules/cobros_seguros/tabla.js'
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
	
	function ocultotablatab($uuid_orden_venta=null) {
		$this->assets->agregar_js(array(
			'public/assets/js/modules/cobros_seguros/tablatab.js'
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

	function crear(){
		$acceso = 1;
		$mensaje = array();
		if(!$this->auth->has_permission('acceso','cobros_seguros/crear')){
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
			'public/resources/compile/modulos/cobros_seguros/formulario.js',
		));

		if(isset($_GET['mod']) && $_GET['mod'] == 'poliza'){
			$aplica_cobro = 'polizas';
			$id_cliente = $_GET['idPoliza'];
			$ids_polizas = '';
		}elseif(isset($_GET['mod']) && $_GET['mod'] == 'polizas'){
			$aplica_cobro = 'cliente';
			$id_cliente = $_GET['idPoliza'];
			$ids_polizas = str_replace(",", "_", $_GET['ids']);
		}else{
			$aplica_cobro = '';
			$id_cliente = '';
			$ids_polizas = '';
		}

		$this->assets->agregar_var_js(array(
			"vista" => 'crear',
			"acceso" => $acceso == 0? $acceso : $acceso,
			"regreso" => isset($_GET['mod']) ? $_GET['mod'] : '',
			"aplica_cobro" => $aplica_cobro,
			"id_cliente" => $id_cliente,
			"ids_polizas" => $ids_polizas,
		));

		$this->desde_empezables();
		$data['mensaje'] = $mensaje;
		$breadcrumb = array(
			"titulo" => '<i class="fa fa-line-chart"></i> Cobro: Crear ',
			"ruta" => array(
				0 => array(
					"nombre" => "Seguros",
					"activo" => true
				),
				1 => array(
					"nombre" => '<b>Cobros</b>',
					"activo" => true,
					"url" => "cobros_seguros/listar"
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
		if(!$this->auth->has_permission('acceso','cobros_seguros/ver/(:any)')){
			$acceso = 0;
			$mensaje = array('estado'=>500, 'mensaje'=>'<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud','clase'=>'alert-danger');
		}
		
		$regreso='';
		
		if(isset($_GET['reg'])){

			if($_GET['reg']=='com'){
				$regreso='com';
			}else{
				$regreso='';
			}	
		}elseif (isset($_GET['mod'])) {
			if($_GET['mod']=='polizas'){
				$regreso = 'polizas';
			}else{
				$regreso='';
			}
		}
		elseif($_GET['reg']=='fase')
		{
			$regreso='fase';
		}

		$this->_Css();
		$this->assets->agregar_css(array(
			'public/assets/css/modules/stylesheets/animacion.css'
		));
		$this->_js();
		$this->assets->agregar_js(array(
			'public/assets/js/default/vue/directives/new-select2.js',
			'public/resources/compile/modulos/cobros_seguros/formulario.js',
		));

		$cobro = $this->cobroRepository->findByUuid($uuid);
		
		if(is_null($uuid) || is_null($cobro)){
			$mensaje = array('estado'=>500, 'mensaje'=>'<strong>¡Error!</strong> Su solicitud no fue procesada');
			$this->session->set_flashdata('mensaje', $mensaje);
			redirect(base_url('cobros_seguros/listar'));
		}
			
		$this->assets->agregar_var_js(array(
			"vista"     => 'ver',
			"acceso"    => $acceso == 0? $acceso : $acceso,
			"hex_cobro" => $cobro->uuid_cobro,
			"regreso" =>$regreso,
			'ids_polizas' => '',
		));
		//$this->desde_empezables();
		$data['mensaje'] = $mensaje;
		$breadcrumb = array(
			"titulo" => '<i class="fa fa-line-chart"></i> Cobro: '.$cobro->codigo,
			"ruta" => array(
				0 => array(
					"nombre" => "Seguros",
					"activo" => false
				),
				1 => array(
					"nombre" => 'Cobros',
					"activo" => false,
					"url" => "cobros_seguros/listar"
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
				
				$cobrosfacturas=CobroFactura::where('cobro_id',$cobro->id)->get();
		
				foreach($cobrosfacturas as $key => $value)
				{
					if($value->facturas->formulario=='facturas_seguro')
					{
						//Generar la comision por cada factura del cobro
						$comision['uuid_comision']=Capsule::raw("ORDER_UUID(uuid())");
						$countcomision = ComisionesSeguros::where('id_empresa',$this->empresa_id)->count();
						$codigo = Util::generar_codigo('COM'.$this->empresa_id, ($countcomision+1) );
						
						$comision['no_comision']=$codigo;
						$comision['id_cobro']=$cobro->id;
						$comision['fecha']=date('Y-m-d');
						$comision['monto_recibo']=$value->monto_pagado;
						$comision['id_factura']=$value->cobrable_id;
						$comision['id_aseguradora']=$value->facturas->polizas->aseguradora_id;
						$comision['id_poliza']=$value->facturas->polizas->id;
						$comision['id_cliente']=$value->facturas->cliente->id;
						$comision['id_ramo']=$value->facturas->polizas->ramo_id;
						$comision['comision']=$value->facturas->polizas->comision;
						$comision['impuesto']=$value->facturas->porcentaje_impuesto;
						$comision['impuesto_pago']=($value->monto_pagado*($value->facturas->porcentaje_impuesto/100));
						$comision['pago_sobre_prima']=$comision['monto_recibo']-$comision['impuesto_pago'];
						$comision['monto_comision']=($comision['pago_sobre_prima']*($value->facturas->polizas->comision/100));
						$comision['sobre_comision']=$value->facturas->polizas->porcentaje_sobre_comision;
						$comision['monto_scomision']=($comision['pago_sobre_prima']*($value->facturas->polizas->porcentaje_sobre_comision/100));
						//$comision['comision_pendiente']=0;
						//$comision['id_remesa']=$id_remesa;
						$comision['lugar_pago']=$value->facturas->polizas->primafk->sitio_pago;
						$comision['estado']='por_liquidar';
						$comision['created_at']=date('Y-m-d H:i:s');
						$comision['updated_at']=date('Y-m-d H:i:s');
						$comision['id_empresa']=$this->empresa_id;
						
						if($value->facturas->polizas->des_comision=='si')
						{
							$comision['comision_descontada']=($valor_real*($value->facturas->polizas->comision/100));
							$comision['scomision_descontada']=($valor_real*($value->facturas->polizas->porcentaje_sobre_comision/100));
						}
						else
						{
							$comision['comision_descontada']=0;
							$comision['scomision_descontada']=0;
						}
						
						$comision['comision_pagada']=($comision['monto_comision']-$comision['comision_descontada'])+($comision['monto_scomision']-$comision['scomision_descontada']);
						
						$comision['comision_pendiente']=($comision['monto_comision']-$comision['comision_descontada'])+($comision['monto_scomision']-$comision['scomision_descontada']);
						
						$comision_creada=ComisionesSeguros::create($comision);
					}
				}
				
				$mensaje = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente ' . $cobro->codigo);
			} catch (\Exception $e) {
				log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
				$mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error! Su solicitud no fue procesada...</b> ');
			}
			
			
			if( isset($_POST['mod']) && $_POST['mod'] == 'polizas'  ){
				$this->session->set_flashdata('mensaje', $mensaje);
				redirect(base_url('polizas/listar'));
			}else{
				$this->session->set_flashdata('mensaje', $mensaje);
				redirect(base_url('cobros_seguros/listar'));
			}
			
			
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

		$this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(collect($catalogos))->_display();
		
		exit;
	}

	function ajax_get_cobro(){
		if(!$this->input->is_ajax_request()){
			return false;
		}
		$deposito = ["Flexio\Modulo\Contabilidad\Models\Cuentas"=>'banco','Flexio\Modulo\Cajas\Models\Cajas'=>'caja'];
		$empezar = ['cliente'=>'Flexio\Modulo\Cliente\Models\Cliente','polizas'=>'Flexio\Modulo\Polizas\Models\Polizas','factura'=>'Flexio\Modulo\FacturasSeguros\Models\FacturaSeguro' ];
		$empz = array_flip($empezar);
		$uuid = $this->input->post('uuid');
		//$cobro = $this->cobroRepository->select("cob_corbos.")->findByUuid($uuid);
		$cobro = $this->cobroRepository->findByUuid($uuid);
		
		//$cobro->load('metodo_cobro','cliente','cobros_facturas','empezable','landing_comments');
		$cobro->load('metodo_cobro','cliente','cobros_facturas','landing_comments');
		$cobro->load(['factura_cobros.cobros'=>function($cob){
			$cob->where('cob_cobros.estado','aplicado');
		}]);
		
		$this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output($cobro)->_display();
		exit;
	}

	function ajax_info_cobro() {
		$uuid = $this->input->post('uuid');
		$cobro = $this->cobroRepository->findByUuid($uuid);
		$cobro->load('metodo_cobro','factura_cobros','cobros_facturas.cobros');
		$this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($cobro->toArray()))->_display();
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
		$facturasObj = new Flexio\Modulo\FacturasSeguros\Repository\FacturaSeguroRepositorio;
		$id = $this->input->post('id');
		
		if(empty($id)){
			$faturas = $facturasObj->getFacturas($this->empresa_id)->conClienteActivo()->fetch();
		}else{
			$faturas = $facturasObj->getFacturas($this->empresa_id)->conId($id)->fetch();
		}

		$faturas->load('cliente','ordenes_ventas.anticipos');
		$faturas->load(['cobros'=>function($cob){
			$cob->with('metodo_cobro');
		}]);
		$response =  $faturas->map(function($fac){
			return collect([
				'id'=> $fac->id,
				'nombre'=> $fac->codigo ." ".$fac->cliente_nombre,
				'codigo'=> $fac->codigo,
				'numero_poliza' => $fac->numero_poliza,
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

	function catalogo_polizas(){

		if(!$this->input->is_ajax_request()){
			return false;
		}
		$clause = [];
		$this->empresa_id;
		$facturasObj = new Flexio\Modulo\FacturasSeguros\Repository\FacturaSeguroRepositorio;
		$id = $this->input->post('id');
		if(empty($id)){
			$faturas = $facturasObj->getPolizasFacturas($this->empresa_id)->fetch();
		}else{
			$faturas = $facturasObj->getPolizasFacturas($this->empresa_id)->conId($id)->fetch();
		}
		/*
		$faturas->load(['cobros'=>function($cob){
			$cob->where('estado','aplicado');
			$cob->with('metodo_cobro');
		}]);*/
		
		$response =  $faturas->map(function($fac){
			return collect([
				'id'=> $fac->id,
				'nombre'=> $fac->cliente_nombre." - ".$fac->ramo_aso." - No. ".$fac->numero_poliza,
				'codigo'=> $fac->codigo,
				'numero_poliza' => $fac->numero_poliza,
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

	function catalogo_clientes_activo($ids_polizas = null){
		if(!$this->input->is_ajax_request()){
			return false;
		}

		$clause = ['empresa_id'=>$this->empresa_id];
		$clientesObj = new Flexio\Modulo\Cliente\Repository\ClienteRepositorio;
		$id = $this->input->post('id');
		if(empty($id)){
			$clientes = $clientesObj->getClientes($this->empresa_id)->activos()->conFacturas()->paraCrearCobros()->fetch();			
		}else{

			$clientes = $clientesObj->getClientes($this->empresa_id)->conId($id)->fetch();
		}
		$clientes->load('anticipo_cliente','anticipos');

		if(!empty($ids_polizas)){
			$ids_polizas = explode("_", $ids_polizas);
		}else{
			$ids_polizas = '';
		}

		//$clientes = $clientes->map(function($cliente){
			foreach ($clientes as $key => $cliente) {
				$clientes[$key] = collect([
					'id'=> $cliente->id,
					'nombre' => $cliente->codigo . ' - '.$cliente->nombre,
					'saldo_pendiente' => $cliente->saldo_pendiente,
					'anticipos' => $cliente->anticipos,
					'anticipos_cliente' => $cliente->anticipo_cliente,
					'credito_favor' => $cliente->credito_favor,
					'facturas'=> $this->loadFacturaInfo($cliente,$ids_polizas)
				]);
			}
		//});

		$this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output($clientes)->_display();
		exit;
	}
	
	protected function loadFacturaInfo($cliente,$ids_polizas){
		
		$facturasObj = new Flexio\Modulo\FacturasSeguros\Repository\FacturaSeguroRepositorio;
		$id = $cliente->id;
		if(empty($id)){
			$faturas = $facturasObj->getFacturasCliente($this->empresa_id)->fetch();
		}else{
			if($ids_polizas != ''){
				$faturas = $facturasObj->getFacturasCliente($this->empresa_id,$id,$ids_polizas)->fetch();
			}else{
				$faturas = $facturasObj->getFacturasCliente($this->empresa_id,$id)->fetch();
			}
			
		}
		
		$response =  $faturas->map(function($fac){
			return collect([
				'id'=> $fac->id,
				'nombre'=> $fac->cliente_nombre." - ".$fac->ramo_aso." - No. ".$fac->numero_poliza,
				'codigo'=> $fac->codigo,
				'numero_poliza' => $fac->numero_poliza,
				'fecha_desde' => $fac->fecha_desde,
				'fecha_hasta' => $fac->fecha_hasta,
				'total' => $fac->saldo,
				'cobros'=> $fac->cobros,
				'cliente'=> $fac->cliente,
				'ordenes_ventas' => $fac->ordenes_ventas->where('estado','facturado_completo')
			]);
		});
		
		
		return $response;
	}

	/*protected function  loadFacturaInfo($cliente){
		$cliente->facturas->load(['cobros' =>
			function($cob){
				$cob->where('estado','aplicado');
				$cob->with('metodo_cobro');
				
			}
		]);
		$cliente->facturas->load('contratos.anticipos','ordenes_ventas.anticipos');
		return $cliente->facturas;
	}*/

	function orden_trabajo(){
		if(!$this->input->is_ajax_request()){
			return false;
		}

		$ordenTrabajoObj = new Flexio\Modulo\OrdenesTrabajo\Repository\OrdenTrabajoRepositorio;
		$id = $this->input->post('id');
		if(empty($id)){
			$orden_trabajo = $ordenTrabajoObj->getOrdenesTrabajos($this->empresa_id)->debeTenerFacturasParaCobrar()->conFacturasParaCobrar()->facturado()->fetch();
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
		$this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output($orden_trabajo)->_display();
		exit;
	}

	private function desde_empezables(){
		$request = Illuminate\Http\Request::capture();
		if($request->has('factura')){
			$facturasObj = new Flexio\Modulo\FacturasSeguros\Repository\FacturaSeguroRepositorio;
			$factura = $facturasObj->getFacturas($this->empresa_id)->conUUID($request->input('factura'))->fetch()->first();

			if(!is_null($factura)){
				$this->assets->agregar_var_js(array(
					'referenciaUrl' =>collect(['factura'=>$factura->id])
				));
			}
		}
	}
	
	function ocultoformulario($data = array())
    {
        $this->assets->agregar_js(array('public/assets/js/modules/cobros_seguros/formulario.js'));
		$usuario_id = $usuario->id;
		/*if(!empty($data["uuid"])){
			$obj
		}*/
		
		$data["campos"] = array(
			'usuario'=>$usuario_id,
			'creado_por'=>$usuario_id,
			'empresa_id'=>$this->id_empresa,
		);
        $this->load->view('formulario',$data);
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

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Flexio
 * @subpackage Controller
 * @category   Abonos
 * @author     Pensanomica Team
 * @link       http://www.pensanomica.com
 * @copyright  01/15/2016
 */

use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

//transacciones
use Flexio\Modulo\Abonos\Transacciones\AbonosProveedor;
use Flexio\Modulo\ConfiguracionContabilidad\Repository\CuentaBancoRepository as CuentaBanco;

class Abonos extends CRM_Controller
{
  private $empresa_id;
  private $id_usuario;
  private $empresaObj;
  
  //transacciones
  protected $AbonosProveedor;
  
  protected $abonoGuardar;
  protected $listaCobro;
  protected $cuenta_banco;

    function __construct(){
        parent::__construct();
        $this->load->model('usuarios/Usuario_orm');
        $this->load->model('usuarios/Empresa_orm');
        $this->load->model('usuarios/Roles_usuarios_orm');
        $this->load->model('roles/Rol_orm');
    
        $this->load->model('proveedores/Proveedores_orm');
        
        $this->load->model('bancos/Bancos_orm');
//    $this->load->model('cotizaciones/Cotizacion_orm');
//    $this->load->model('cotizaciones/Cotizacion_catalogo_orm');
//    $this->load->model('cotizaciones/Cotizacion_item_orm');
//    $this->load->model('inventarios/Items_orm');
//    $this->load->model('inventarios/Items_precios_orm');
//    $this->load->model('inventarios/Precios_orm');
//    $this->load->model('inventarios/Unidades_orm');
//    $this->load->model('contabilidad/Impuestos_orm');
//    $this->load->model('contabilidad/Cuentas_orm');
//    $this->load->model('contabilidad/Centros_orm');
//    $this->load->model('bodegas/Bodegas_orm');
//    $this->load->model('ordenes_ventas/Orden_ventas_orm');
//    $this->load->model('ordenes_ventas/Ordenes_venta_item_orm');
        $this->load->model('facturas_compras/Facturas_compras_orm');
//    $this->load->model('facturas_compras/Factura_items_orm');
//    $this->load->model('facturas_compras/Factura_catalogo_orm');
//
        $this->load->model('pagos/Pagos_orm');
        
        $this->load->model('abonos/Abonos_orm');
        $this->load->model('abonos/Abono_catalogos_orm');
        $this->load->model('abonos/Abono_metodos_abono_orm');
//
//    $this->load->module("salidas/Salidas");
    
        Carbon::setLocale('es');
        setlocale(LC_TIME, 'Spanish');
        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresaObj  = new Buscar(new Empresa_orm,'uuid_empresa');
        $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
	$this->id_usuario   = $this->session->userdata("huuid_usuario");
	$this->empresa_id   = $this->empresaObj->id;
        
        $this->load->library('Repository/Abonos/Guardar_abono');
        $this->load->library('Repository/Abonos/Lista_abono');
        $this->abonoGuardar = new Guardar_abono;
        $this->listaAbono = new Lista_abono;
        
        //transacciones
        $this->AbonosProveedor  = new AbonosProveedor();

        $this->cuenta_banco = new CuentaBanco;

    }

    function listar(){

        redirect(base_url('proveedores/listar'));
        //este metodo no se ha solicitado para desarrollo
        $data = array();
        if (!$this->auth->has_permission('acceso')) {
            $mensaje = array('estado'=>500, 'mensaje'=>'<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud');
            $this->session->set_flashdata('mensaje', $mensaje);
        }

        $this->_Css();
        $this->_js();
        $this->assets->agregar_js(array(
            'public/assets/js/modules/abonos/listar.js',
            'public/assets/js/default/toast.controller.js'
        ));
    
        $breadcrumb = array( "titulo" => '<i class="fa fa-shopping-cart"></i> Abonos',
            "ruta" => array(
                0 => array(
                    "nombre" => "Compras",
                    "activo" => false
                ),
                1 => array(
                    "nombre" => '<b>Abonos</b>',
                    "activo" => true
                )
            ),
            "menu" => array(
                "nombre"    => "Crear",
                "url"       => "abonos/crear",
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
        
        
        $data['proveedores']    = Proveedores_orm::deEmpresa($this->empresa_id)->get(array('id','nombre'));
        $data['etapas']         = Abono_catalogos_orm::where('tipo','etapa3')->get(array('etiqueta','valor'));
        $data['formas_abono']    = Abono_catalogos_orm::where('tipo','abono')->get(array('id','etiqueta','valor'));
        $data['bancos']         = Bancos_orm::get(array('id','nombre'));
        
        $breadcrumb["menu"]["opciones"]["#exportarListaAbonos"] = "Exportar";
        $this->template->agregar_titulo_header('Listado de Abonos');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }
    
    private function _filtrarAbonos($abonos)
    {
        /*
        paramentos de busqueda aqui    
        */
        $desde      = $this->input->post('desde',TRUE);
        $hasta      = $this->input->post('hasta',TRUE);
        $proveedor  = $this->input->post('proveedor',TRUE);
        $estado     = $this->input->post('estado',TRUE);
        $montoMin   = $this->input->post('montoMin',TRUE);
        $montoMax   = $this->input->post('montoMax',TRUE);
        $formaAbono  = $this->input->post('formaAbono',TRUE);
        $tipo       = $this->input->post('tipo',TRUE);
        $banco      = $this->input->post('banco',TRUE);
        
        if(!empty($desde)) $abonos->deFechaDesde($desde);
        if(!empty($hasta)) $abonos->deFechaHasta($hasta);
        if(!empty($proveedor)) $abonos->deProveedor($proveedor);
        if(!empty($estado)) $abonos->deEstado($estado);
        if(!empty($montoMin)) $abonos->deMontoMin($montoMin);
        if(!empty($montoMax)) $abonos->deMontoMax($montoMax);
        if(!empty($formaAbono)) $abonos->deFormaAbono($formaAbono);
        if(!empty($tipo)) $abonos->deTipo($tipo);
        if(!empty($banco)) $abonos->deBanco($banco);
    }

    function ajax_listar(){
        if(!$this->input->is_ajax_request()){
            return false;
        }
        
        $abonos = Abonos_orm::deEmpresa($this->empresa_id);
        $this->_filtrarAbonos($abonos);
        
        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = $abonos->count();
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        
        $abonos->orderBy($sidx, $sord)->skip($start)->take($limit);
    

        $response = new stdClass();
        $response->page     = $page;
        $response->total    = $total_pages;
        $response->records  = $count;


        if($count){
      
            foreach($abonos->get() as $i => $row){
                $factura = $row->facturas->last();
        
                $hidden_options = "";
                $link_option    = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row->uuid_abono .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $hidden_options = '<a href="'. base_url('abonos/ver/'. $row->uuid_abono) .'" data-id="'. $row->uuid_abono .'" class="btn btn-block btn-outline btn-success">Ver Detalle</a>';
                if($row->estado !='anulado' && $factura->total != $row->total_pagado())$hidden_options .= '<a href="'. base_url('abonos/registrar_abono_abono/'. $row->uuid_abono) .'" data-id="'. $row->uuid_abono .'" class="btn btn-block btn-outline btn-success">Registrar Abono</a>';

                $proveedor      = $row->proveedor;
                $etapa          = $row->catalogo_estado;
                $metodo_abono    = "ddd";
                
                $response->rows[$i]["id"] = $row->uuid_abono;
                $response->rows[$i]["cell"] = array(
                    $row->uuid_abono,
                    '<a class="link" href="'. base_url('abonos/ver/'. $row->uuid_abono) .'" >'.$row->codigo.'</a>',
                    $row->fecha_abono,
                    '<a class="link">'.$proveedor->nombre.'</a>',
                    'Compras',
                    $this->listaAbono->metodo_abono($row->metodo_abono),
                    $this->listaAbono->banco($row->metodo_abono),
                    $this->listaAbono->color_estado($etapa->etiqueta, $etapa->valor),
                    '<label class="'.$this->listaAbono->color_monto($row->estado).'">'.$row->monto_pagado.'</label>',
                    $link_option,
                    $hidden_options
                );
            }
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))->_display();
        exit;
    }

    function ocultotabla($uuid_orden_venta=null){
        $this->assets->agregar_js(array(
            'public/assets/js/modules/abonos/tabla.js'
        ));

        if (!empty($uuid_orden_venta)) {

            $this->assets->agregar_var_js(array(
                "uuid_orden_venta" => $uuid_orden_venta
            ));

        }

        $this->load->view('tabla');
    }

    public function crear($uuid_proveedor = NULL){
        $acceso = 1;
        $mensaje = array();
        if(!$this->auth->has_permission('acceso') or !$uuid_proveedor){
            $acceso = 0;
            $mensaje = array('estado'=>500, 'mensaje'=>'<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud','clase'=>'alert-danger');
        }

        $this->_Css();
        $this->assets->agregar_css(array(
            'public/assets/css/modules/stylesheets/animacion.css'
        ));
        $this->_js();
        $this->assets->agregar_js(array(
            'public/assets/js/modules/abonos/service.abono.js',
            'public/assets/js/modules/abonos/crearAbono.controller.js',
        ));

        $this->assets->agregar_var_js(array(
            "vista"             => 'crear',
            "acceso"            => $acceso == 0? $acceso : $acceso,
            "uuid_proveedor"    => $uuid_proveedor
        ));

        $data['mensaje'] = $mensaje;
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-shopping-cart"></i> Abono: Crear ',
        );

        $this->template->agregar_titulo_header('Crear Abono');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();

    }

    function ver($uuid=NULL){
        $mensaje = array();
        $acceso = 1;
        if(!$this->auth->has_permission('acceso','abonos/ver/(:any)')){
            $acceso = 0;
            $mensaje = array('estado'=>500, 'mensaje'=>'<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud','clase'=>'alert-danger');
        }

        $this->_Css();
        $this->assets->agregar_css(array(
            'public/assets/css/modules/stylesheets/animacion.css'
        ));
        $this->_js();
        $this->assets->agregar_js(array(
            'public/assets/js/modules/abonos/service.abono.js',
            'public/assets/js/modules/abonos/crearAbono.controller.js',
        ));

        $abonoObj    = new Buscar(new Abonos_orm,'uuid_abono');
        $abono       = $abonoObj->findByUuid($uuid);
        if(is_null($uuid) || is_null($abono)){
            $mensaje = array('estado'=>500, 'mensaje'=>'<strong>¡Error!</strong> Su solicitud no fue procesada');
            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('abono/listar'));
        }
        $contar_pagables = $abono->abonos_pagables()->groupBy('pagable_id')->get();
    
        if(count($contar_pagables->toArray()) == 1 && $abono->formulario == "factura"){
            $this->assets->agregar_var_js(array(
                "tipo"          => 'factura',
                "uuid_factura"  => $abono->facturas[0]->uuid_factura,
                "nombre"        => $abono->facturas[0]->codigo.' - '.$abono->proveedor->nombre
            ));
        }elseif(count($contar_pagables->toArray()) == 1 && $abono->formulario == "planilla"){//opcion no desarrollada
            $this->assets->agregar_var_js(array(
                "tipo"          => 'planilla',
                "uuid_planilla" => $abono->planillas[0]->uuid_planilla,
                "nombre"        => 'E.D.'//$abono->planillas[0]->codigo.' - '.$abono->proveedor->nombre
            ));
        }elseif(count($contar_pagables->toArray()) > 1){
            $this->assets->agregar_var_js(array(
                "tipo"              => 'proveedor',
                "uuid_proveedor"    => $abono->proveedor->uuid_proveedor,
                "nombre"            => $abono->proveedor->nombre
            ));
        }
        $data       = array();
        $clause     = array('empresa_id'=> $this->empresa_id);
//        $facturas   = Facturas_compras_orm::with('proveedor')->where(function($query) use($clause){
//            $query->where('empresa_id','=',$clause['empresa_id']);
//            $query->whereNotIn('estado',array('anulada'));
//        })->get();
        $this->assets->agregar_var_js(array(
            "vista"     => 'ver',
            "acceso"    => $acceso == 0? $acceso : $acceso,
            "uuid_abono" => $abono->uuid_abono
        ));

        //$data['facturas'] = $facturas->toArray();
        $data['uuid_abono']      = $abono->uuid_abono;
        $data['proveedor_id']   = $abono->proveedor->uuid_proveedor;
        $data['mensaje']        = $mensaje;
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-shopping-cart"></i> Abono: '.$abono->codigo,
        );

        $this->template->agregar_titulo_header('Ver Abono');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();

    }

  function registrar_abono($uuid=NULL){
    //dd($this->abonoGuardar);
    $acceso = 1;
    $mensaje = array();
    if(!$this->auth->has_permission('acceso','abonos/registrar_abono/(:any)')){
            $acceso = 0;
            $mensaje = array('estado'=>500, 'mensaje'=>'<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud','clase'=>'alert-danger');
    }

    $this->_Css();
    $this->assets->agregar_css(array(
      'public/assets/css/modules/stylesheets/animacion.css'
    ));
    $this->_js();
    $this->assets->agregar_js(array(
      'public/assets/js/modules/abonos/service.abono.js',
      'public/assets/js/modules/abonos/registarCobro.controller.js',
    ));

    $facturaObj  = new Buscar(new Factura_orm,'uuid_factura');
    $factura = $facturaObj->findByUuid($uuid);
    if(is_null($uuid) || is_null($factura)){
      $mensaje = array('estado'=>500, 'mensaje'=>'<strong>¡Error!</strong> Su solicitud no fue procesada');
      $this->session->set_flashdata('mensaje', $mensaje);
      redirect(base_url('facturas_compras/listar'));
    }

    $data=array();
    $clause = array('empresa_id'=> $this->empresa_id);
    $facturas = Factura_orm::with('proveedor')->where(function($query) use($clause){
      $query->where('empresa_id','=',$clause['empresa_id']);
      $query->whereNotIn('estado',array('anulada'));
    })->get();
    $this->assets->agregar_var_js(array(
      "vista" => 'registrar_abono',
      "acceso" => $acceso == 0? $acceso : $acceso,
      "uuid_factura" => $factura->uuid_factura
    ));

    $data['facturas'] = $facturas->toArray();
    //$data['uuid_factura'] = $factura->uuid_factura;
    $data['mensaje'] = $mensaje;
    $breadcrumb = array(
      "titulo" => '<i class="fa fa-line-chart"></i> Registar Cobro: Factura '.$factura->codigo,
    );

    $this->template->agregar_titulo_header('Crear Abono');
    $this->template->agregar_breadcrumb($breadcrumb);
    $this->template->agregar_contenido($data);
    $this->template->visualizar();
  }

  function registrar_abono_abono($uuid=NULL){
    //dd($this->abonoGuardar);
    $acceso = 1;
    $mensaje = array();
    if(!$this->auth->has_permission('acceso','abonos/registrar_abono_abono/(:any)')){
            $acceso = 0;
            $mensaje = array('estado'=>500, 'mensaje'=>'<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud','clase'=>'alert-danger');
    }

    $this->_Css();
    $this->assets->agregar_css(array(
      'public/assets/css/modules/stylesheets/animacion.css'
    ));
    $this->_js();
    $this->assets->agregar_js(array(
      'public/assets/js/modules/abonos/service.abono.js',
      'public/assets/js/modules/abonos/crearCobro.controller.js',
    ));

    $abonoObj  = new Buscar(new Abonos_orm,'uuid_abono');
    $abono = $abonoObj->findByUuid($uuid);
    if(is_null($uuid) || is_null($abono)){
      $mensaje = array('estado'=>500, 'mensaje'=>'<strong>¡Error!</strong> Su solicitud no fue procesada');
      $this->session->set_flashdata('mensaje', $mensaje);
      redirect(base_url('abono/listar'));
    }
    $contar_facturas = $abono->factura_abonos()->groupBy('factura_id')->get();
    //dd($abono->factura_abonos->toArray());
    if(count($contar_facturas->toArray()) == 1){
      $this->assets->agregar_var_js(array(
        "tipo" => 'factura',
        "uuid_factura" => $abono->factura_abonos[0]->uuid_factura
      ));
    }elseif(count($contar_facturas->toArray()) > 1){
      $this->assets->agregar_var_js(array(
        "tipo" => 'proveedor',
        "uuid_proveedor" => $abono->proveedor->uuid_proveedor
      ));
    }
    $data=array();
    $clause = array('empresa_id'=> $this->empresa_id);
    $facturas = Facturas_compras_orm::with('proveedor')->where(function($query) use($clause){
      $query->where('empresa_id','=',$clause['empresa_id']);
      $query->whereNotIn('estado',array('anulada'));
    })->get();
    $this->assets->agregar_var_js(array(
      "vista" => 'registrar_abono_abono',
      "acceso" => $acceso == 0? $acceso : $acceso,
      "uuid_abono" => $abono->uuid_abono
    ));

    $data['facturas'] = $facturas->toArray();
    //$data['uuid_factura'] = $factura->uuid_factura;
    $data['mensaje'] = $mensaje;
    $breadcrumb = array(
      "titulo" => '<i class="fa fa-line-chart"></i> Registar Cobro: '.$abono->codigo,
    );

    $this->template->agregar_titulo_header('Crear Abono');
    $this->template->agregar_breadcrumb($breadcrumb);
    $this->template->agregar_contenido($data);
    $this->template->visualizar();
  }


    function ocultoformulario($facturas = array()){
        $data   = array();
        $clause = array('empresa_id'=> $this->empresa_id);
        
        $data['tipo_abonos']     = Abono_catalogos_orm::where('tipo','pago')->get(array('id','etiqueta','valor'));
        $data['bancos']         = Bancos_orm::get(array('id','nombre'));
        //$data['cuenta_bancos']  = Cuentas_orm::cuentasBanco($clause);

        $empresa = ['empresa_id' => $this->empresa_id];

        $data["cuenta_bancos"] ="";
        if($this->cuenta_banco->tieneCuenta($empresa)) {
          $data["cuenta_bancos"] = $this->cuenta_banco->getAll($empresa);
          $data["cuenta_bancos"]->load("cuenta");

         }

        $data['proveedores']    = Proveedores_orm::deEmpresa($this->empresa_id)->get(array('id','nombre', 'limite_credito'));

        if(isset($facturas['info']))$data['info'] = $facturas['info'];

        $this->load->view('formulario', $data);
    }

    function ocultoformulariover($facturas = array()){
        $data = array();
        $clause = array('empresa_id'=> $this->empresa_id);
        
        $data['tipo_abonos']     = Abono_catalogos_orm::where('tipo','abono')->get(array('id','etiqueta','valor'));
        $data['bancos']         = Bancos_orm::get(array('id','nombre'));
        $data['etapas']         = Abono_catalogos_orm::where('tipo','etapa3')->get(array('etiqueta','valor'));
        //$data['cuenta_bancos']  = Cuentas_orm::cuentasBanco($clause);
        $empresa = ['empresa_id' => $this->empresa_id];
        $data["cuenta_bancos"] ="";
        if($this->cuenta_banco->tieneCuenta($empresa)) {
            $data["cuenta_bancos"] = $this->cuenta_banco->getAll($empresa);
            $data["cuenta_bancos"]->load("cuenta");
         }


        $data['proveedores']    = Proveedores_orm::deEmpresa($this->empresa_id)->get(array('id','nombre', 'limite_credito'));

        if(isset($facturas['info']))$data['info'] = $facturas['info'];

        $this->load->view('formulario_ver', $data);
    }

    private function _createAbono($abono, $post)
    {
        $total  = Abonos_orm::deEmpresa($this->empresa_id)->count();
        $year   = Carbon::now()->format('y');
        $codigo = Util::generar_codigo('ABO'.$year,$total + 1);
        
        $abono->codigo          = $codigo;
        $abono->empresa_id      = $this->empresa_id;
        $abono->fecha_abono     = date("Y-m-d", strtotime($post["campo"]["fecha_abono"]));
        $abono->proveedor_id    = $post["campo"]["proveedor"];
        $abono->monto_abonado   = $post['campo']['total_abonado'];
        $abono->cuenta_id       = $post["campo"]["cuenta_id"];
        //$abono->formulario      = $post["campo"]["formulario"];
        $abono->estado          = 'aplicado';
    }
    
    //en la edicion de abonos solo se puede cambiar el estado
    private function _setAbonoFromPost($abono, $post)
    {
        $abono->estado   = isset($post["campo"]["estado"]) ? $post["campo"]["estado"] : 'por_aplicar';
    }
    
    
    
    private function _setMetodosAbonos($abono, $post)
    {
        foreach($post["metodo_abono"] as $metodo){
            $referencia   = $this->abonoGuardar->tipo_abono($metodo['tipo_abono'], $metodo);
            $item_abono    = new Abono_metodos_abono_orm;
          
            $item_abono->tipo_abono     = $metodo['tipo_abono'];
            $item_abono->total_abonado  = $metodo['total_abonado'];
            $item_abono->referencia     = $referencia;

            $abono->metodo_abono()->save($item_abono);
        }
    }
    
    
    
    private function _actualizarCreditoProveedor($abono)
    {
        $abono->proveedor->credito += $abono->monto_abonado;
        $abono->proveedor->save();
    }

    function guardar()
    {

        if($_POST)
        {
//            echo "<pre>";
//            print_r($_POST);
//            echo "<pre>";
//            die();
            //campos para guardar el abono
            $success    = FALSE;
            $post       = $this->input->post();
            Capsule::transaction(function() use ($post, &$success){
                
                $success = TRUE;
                if(!isset($post['campo']["id"]))//identificador del abono
                {
                    $abono = new Abonos_orm;
                    $this->_createAbono($abono, $post);
                    $abono->save();
                
                    $this->_setMetodosAbonos($abono, $post);
                    $this->_actualizarCreditoProveedor($abono);
                }
                else
                {
                    $abono = Abonos_orm::find($post["campo"]["id"]);
                    $this->_setAbonoFromPost($abono, $post);//solo cambia el estado del abono
                    $abono->save();
                }

                $this->AbonosProveedor->haceTransaccion($abono);
            });
            
            if($success)
            {
                //$this->load->library('Events/Facturas/Facturas_compras_estados');
                //$facturaEstado = new Facturas_compras_estados;
                //$facturaEstado->manipularEstado($factura_ids);
                //$this->abonoGuardar->actualizar_estados($abono, $factura_ids);
                $mensaje = array('estado' => 200, 'mensaje' =>'<b>¡&Eacute;xito!</b> Se ha guardado correctamente ');
            }else{
                $mensaje = array('estado'=>500, 'mensaje'=>'<b>¡Error! El abono no puede ser aplicado</b> ');
            }
            
            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('proveedores/listar'));
        }
    }

    function ajax_factura_info()
    {
        $uuid = $this->input->post('uuid');
        $facturaObj  = new Buscar(new Facturas_compras_orm,'uuid_factura');
        $factura = $facturaObj->findByUuid($uuid);
        $factura->proveedor;
        $factura->abonos;

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($factura->toArray()))->_display();
        exit;
    }

    //Obtiene el catalogo de facturas a las cuales se les puede
    //relizar abonos
    function ajax_facturas_abonos(){
        //$vista      = $this->input->post('vista');
        $facturas   = Facturas_compras_orm::deEmpresa($this->empresa_id)->paraAbonos();
        $resultados = array();
        
        foreach($facturas->get() as $factura){
            $total = $factura->total;
            $abonos = (count($factura->abonos)) ? $factura->abonos()->sum("pag_abonos_pagables.monto_pagado") : 0;
            $saldo = $total - $abonos;
            
            if($saldo > 0)
            {
                //echo $total."-".$abonos."=".$saldo."\n<br>";
                $resultados[]= array('uuid'=>$factura->uuid_factura,'nombre'=>$factura->codigo.' - '.$factura->proveedor->nombre);
            }
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($resultados))->_display();
        exit;
    }

    public function ajax_proveedores(){
        $proveedores    = Proveedores_orm::deEmpresa($this->empresa_id)->activos()->orderBy("nombre", "asc");
        $resultados     = array();
        
        foreach($proveedores->get() as $proveedor){
            $resultados[] = array('uuid'=>$proveedor->uuid_proveedor,'nombre'=>$proveedor->nombre);
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($resultados))->_display();
        exit;
    }

    public function ajax_proveedor_info(){

        $uuid   = $this->input->post('uuid');

        $proveedorObj   = new Buscar(new Proveedores_orm,'uuid_proveedor');
        $proveedor      = $proveedorObj->findByUuid($uuid);


        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($proveedor->toArray()))->_display();
        exit;
    }


    function ajax_info_abono(){
        $uuid       = $this->input->post('uuid');
        $abonoObj    = new Buscar(new Abonos_orm,'uuid_abono');
        $abono       = $abonoObj->findByUuid($uuid);
        
        $abono->metodo_abono;
        ($abono->formulario != "planilla") ? $l = $abono->facturas : $l = $abono->planillas;
        $abono->abonos_pagables;
        
        foreach ($l as $row)
        {
            $row->abonos;
        }
        
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
          ->set_output(json_encode($abono->toArray()))->_display();
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
        'public/assets/css/modules/stylesheets/abonos.css',
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

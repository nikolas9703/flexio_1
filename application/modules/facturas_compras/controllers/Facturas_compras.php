<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 *
 * @package    Flexio
 * @subpackage Controller
 * @category   Facturas de compras
 * @author     Pensanomica Team
 * @link       http://www.pensanomica.com
 * @copyright  02/19/2016
 */
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Strategy\Transacciones\Transaccion as Transaccion;
use Flexio\Strategy\Transacciones\TransaccionFacturaCompra as TransaccionFacturaCompra;
use Flexio\Modulo\FacturasCompras\Models\FacturaCompra as FaccomModel;

//transacciones
use Flexio\Modulo\FacturasCompras\Transacciones\FacturasComprasTransacciones;

//repositories
use Flexio\Modulo\OrdenesCompra\Repository\OrdenesCompraRepository as ordenesCompraRep;
use Flexio\Modulo\Contabilidad\Repository\ImpuestosRepository as impuestosRep;
use Flexio\Modulo\FacturasCompras\Repository\FacturaCompraRepository;
use Flexio\Modulo\SubContratos\Repository\SubContratoRepository as subcontratosRep;
use Flexio\Modulo\Proveedores\Repository\ProveedoresRepository as proveedoresRep;
use Flexio\Modulo\FacturasVentas\Repository\FacturaVentaCatalogoRepository;
use Flexio\Modulo\Usuarios\Repository\UsuariosRepository;
use Flexio\Modulo\CentrosContables\Repository\CentrosContablesRepository;
use Flexio\Modulo\Bodegas\Repository\BodegasRepository;
use Flexio\Modulo\Inventarios\Repository\CategoriasRepository as ItemsCategoriasRepository;
use Flexio\Modulo\Contabilidad\Repository\CuentasRepository;
use Flexio\Modulo\FacturasCompras\Repository\FacturaCompraCatalogoRepository;
use Flexio\Modulo\FacturasCompras\Models\FacturaCompra as FacturaCompra;

use Carbon\Carbon as Carbon;

//utils
use Flexio\Library\Util\FlexioSession;
use Flexio\Library\Util\AuthUser;

//services
use Flexio\Modulo\FacturasCompras\Services\FacturaCompraEmpezable;

class Facturas_compras extends CRM_Controller {

    protected $FacturaCompraRepository;
    private $empresa_id;
    private $id_usuario;
    private $empresaObj;
    private $ordenesCompraRep;
    private $impuestosRep;
    private $facturasCompraRep;
    private $subcontratosRep;
    private $proveedoresRep;

    //se usa de forma temporal para definir en valor en la columna operacion_type
    private $operaciones = [
        'orden_compra' => 'Ordenes_orm',
        'subcontrato' => 'Flexio\\Modulo\\SubContratos\\Models\\SubContrato'
    ];

    private $operaciones_nombres = [
        '&Oacute;rdenes de compras' => 'Ordenes_orm',
        'Subcontrato' => 'Flexio\\Modulo\\SubContratos\\Models\\SubContrato'
    ];

    protected $FacturaVentaCatalogoRepository;
    protected $UsuariosRepository;
    protected $CentrosContablesRepository;
    protected $BodegasRepository;
    protected $ItemsCategoriasRepository;
    protected $CuentasRepository;
    protected $FacturaCompraCatalogoRepository;

    //transacciones
    protected $FacturasComprasTransacciones;

    //utils
    protected $FlexioSession;

    function __construct() {
        parent::__construct();

        $this->load->model('proveedores/Proveedores_orm');

        $this->load->model('centros/Centros_orm');

        $this->load->model('pagos/Pagos_orm');

        $this->load->model('inventarios/Items_categorias_orm');
        $this->load->model('inventarios/Categorias_orm');

        $this->load->model('usuarios/Usuario_orm');

        $this->load->model('contabilidad/Impuestos_orm');

        $this->load->model('ordenes/Ordenes_orm');

        $this->load->model('facturas_compras/Facturas_compras_orm');
        $this->load->model('pagos/Pago_metodos_pago_orm');
        $this->load->model('pedidos/Pedidos_orm');
        $this->load->model('ordenes/Ordenes_orm');
        $this->load->model('facturas/Factura_catalogo_orm'); //uso el mismo catalogo de la seccion de facturas de ventas

        $this->load->module(array("salidas/Salidas", "documentos"));
        Carbon::setLocale('es');
        setlocale(LC_TIME, 'Spanish');
        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();

    $uuid_empresa       = $this->session->userdata('uuid_empresa');
    $uuid_usuario = $this->session->userdata('huuid_usuario');

    $empresaObj         = new Buscar(new Empresa_orm,'uuid_empresa');
    $usuario = Usuario_orm::findByUuid($uuid_usuario);

    $this->empresaObj   = $empresaObj->findByUuid($uuid_empresa);

    $this->empresa_id   = $this->empresaObj->id;
    $this->id_usuario   = $usuario->id;

    $this->ordenesCompraRep     = new ordenesCompraRep();
    $this->impuestosRep         = new impuestosRep();
    $this->facturasCompraRep    = new FacturaCompraRepository();
    $this->subcontratosRep      = new subcontratosRep();
    $this->proveedoresRep       = new proveedoresRep();

        $this->FacturasComprasTransacciones = new FacturasComprasTransacciones();
        $this->FacturaCompraRepository = new FacturaCompraRepository();
        $this->FacturaVentaCatalogoRepository = new FacturaVentaCatalogoRepository();
        $this->UsuariosRepository = new UsuariosRepository();
        $this->CentrosContablesRepository = new CentrosContablesRepository();
        $this->BodegasRepository = new BodegasRepository();
        $this->ItemsCategoriasRepository = new ItemsCategoriasRepository();
        $this->CuentasRepository = new CuentasRepository();
        $this->FacturaCompraCatalogoRepository = new FacturaCompraCatalogoRepository();

        //utils
        $this->FlexioSession = new FlexioSession;
    }

    public function ajax_get_empezable()
    {
        if(!$this->input->is_ajax_request()){
    		return false;
    	}

        $post = array_filter($this->input->post());
        $response = [];

        if(isset($post['type']))
        {
            $empezable = new FacturaCompraEmpezable;
            $response = $empezable->getResponse($post);
        }

    	$this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
    	->set_output(Collect($response))->_display();
    	exit;
    }

    function index() {
        redirect("facturas_compras/listar");
    }

    function listar() {

        $data = array();
        $toast = new Flexio\Library\Toast;
        //Verificar permisos de acceso -> sin no los tiene retorna al landing page.
        $toast->runVerifyPermission($this->auth->has_permission('acceso'));

        $this->_Css();
        $this->_js();
        $this->assets->agregar_js(array(
            'public/assets/js/modules/facturas_compras/listar.js',
            'public/assets/js/default/toast.controller.js'
        ));

        $breadcrumb = array(
            "titulo" => '<i class="fa fa-shopping-cart"></i> Facturas de compras',
            "ruta" => array(
                0 => array(
                    "nombre" => "Compras",
                    "activo" => false
                ),
                1 => array(
                    "nombre" => '<b>Facturas de compras</b>',
                    "activo" => true
                )
            ),
            "menu" => array(
                "nombre" => "Crear",
                "url" => "facturas_compras/crear",
                "opciones" => array()
            )
        );

        $this->assets->agregar_var_js(array(
            "flexio_mensaje" => Flexio\Library\Toast::getStoreFlashdata()
        ));


        $clause2 = ['empresa_id'=>$this->empresa_id,'ordenables'=>true,'transaccionales'=>true,'conItems'=>true, 'estado != por_aprobar'];
        $clause = ['empresa_id'=>$this->empresa_id,'facturables'=>true,'transaccionales'=>true,'conItems'=>true];
        $data['vendedores'] = $this->UsuariosRepository->get($clause)->map(function($usuario){return ['id'=>$usuario->id,'nombre'=>$usuario->nombre_completo];});
         
        /// filtro de categoria
        $categorias_items = AuthUser::usuarioCategoriaItems();

        $columns = ['id', 'nombre'];
        $categorias = $this->ItemsCategoriasRepository->getAll(['empresa_id'=>$this->empresa_id], $columns);
    
        $categoria = $categorias->filter(function($categoria) use($categorias_items){
            if(!in_array('todos', $categorias_items)){
               return in_array($categoria->id, $categorias_items);
            }
            return $categoria;
        });


        $data["categorias"]  = $categoria;
        //$data['proveedores'] = Proveedores_orm::deEmpresa($this->empresa_id)->orderBy("nombre", "asc")->skip(0)->take(200)->get();
        $data['proveedores'] = collect([]);
        $data['estados'] = Factura_catalogo_orm::estadosFacturasCompras()->get();
        $data['tipos'] = Factura_catalogo_orm::tiposFacturasCompras()->get();
        $data["centros"] = $this->CentrosContablesRepository->getCollectionCentrosContables($this->CentrosContablesRepository->get($clause));
        /*$data["centros"] = Centros_orm::deEmpresa($this->empresa_id)
                ->activa()
                ->deMasJuventud($this->empresa_id)
                ->orderBy("nombre", "ASC")
                ->get();*/
        $breadcrumb["menu"]["opciones"]["#exportarListaFacturasCompras"] = "Exportar";
        $breadcrumb["menu"]["opciones"]["#refacturar"] = "Refacturar";
        $this->template->agregar_titulo_header('Listado de facturas de compras');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }

    public function ajax_exportar() {
        $clause = [];
        $clause["empresa_id"] = $this->empresa_id;
        $clause["uuid_facturas_compra"] = $this->input->post("uuid_facturas_compra", true);

        $csv = \League\Csv\Writer::createFromFileObject(new \SplTempFileObject());
        $csv->insertOne([utf8_decode("Número"), "Fecha", "Proveedor", "Referencia", "Centro", "Estado", "Monto", "Saldo"]);
        $csv->insertAll($this->facturasCompraRep->getCollectionExportar($this->facturasCompraRep->get($clause)));

        $csv->output('facturas_compras.csv');
        exit;
    }

    function ajax_listar() {
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        //Paramentos de busqueda
        $numero_factura = $this->input->post("numero_factura");
        $fecha1 = $this->input->post("fecha1", true);
        $fecha2 = $this->input->post('fecha2', true);
        $proveedor = $this->input->post('proveedor', true);
        $estado = $this->input->post('estado', true);
        $monto1 = $this->input->post('monto1', true);
        $monto2 = $this->input->post('monto2', true);
        $centro_contable = $this->input->post('centro_contable', true);
        //$tipo = $this->input->post('tipo', true);
        $creado_por = $this->input->post('creado_por', true);
        $caja_id = $this->input->post('caja_id', true);
        $item_id = $this->input->post('item_id', true);
        $pedido_id = $this->input->post('pedido_id', true);
        $registros = Facturas_compras_orm::deEmpresa($this->empresa_id);

        $categoria_id     = $this->input->post('categoria_id', true);
        $campo = $this->input->post('campo', true);
        //subpanels
        $orden_compra_id = $this->input->post('orden_compra_id', true);
       //$subcontrato_id = $this->input->post('subcontrato_id', true); Esta linea fue comentada porque esta enviando datos malos al listar y hace que se rompa

        if(!empty($numero_factura)){
            $clause_numero['factura_proveedor'] = array('LIKE', "%$numero_factura%");
            $registros->deNumeroFactura($clause_numero);
        }

        if (!empty($fecha1)) {
            $fecha1 = date("Y-m-d H:i:s", strtotime($fecha1));
            $registros->deFechaCreacionMayorIgual($fecha1);
        }

        if (!empty($fecha2)) {
            $fecha2 = date("Y-m-d H:i:s", strtotime($fecha2));
            $registros->deFechaCreacionMenorIgual($fecha2);
        }

        if (!empty($orden_compra_id)) {
            $registros->deOrdenDeCompra($orden_compra_id);
        }

        if (!empty($subcontrato_id)) {
            $registros->deSubcontrato($subcontrato_id);
        }

        if (!empty($creado_por)) {
            $registros->deComprador($creado_por);
        }

        if (!empty($proveedor)) {
            $registros->deProveedor($proveedor);
        }

        if (!empty($estado)) {
            $registros->deEstado($estado);
        }

        if (!empty($monto1)) {
            $registros->deMontoMayorIgual($monto1);
        }

        if (!empty($monto2)) {
            $registros->deMontoMenorIgual($monto2);
        }

        if (!empty($centro_contable)) {
            $registros->deCentroContable($centro_contable);
        }
        if(!empty($campo)){
            $registros->deFiltro($campo);
        }


        //filtros de centros contables del usuario
        $centros = $this->FlexioSession->usuarioCentrosContables();
        if(!in_array('todos', $centros))
        {
            $registros->whereIn("faccom_facturas.centro_contable_id", $centros);
        }

        //filtro de facturas por la categoria del usuario
        $categorias_items = AuthUser::usuarioCategoriaItems();

        if(!in_array('todos', $categorias_items)){
            $registros->deCategoria($categorias_items);
        }

        if (!empty($tipo)) {
            $registros->deTipo($tipo);
        }
         if (!empty($caja_id)) {
            $registros->whereHas('pagos', function($q) use($caja_id) {
                $q->with(['metodo_pago'])->whereHas('metodo_pago', function($r) use($caja_id) {
                    $r->where('tipo_pago', '=', 'caja_chica')
                            ->where(Capsule::raw('CONVERT(referencia USING utf8)'), "like", "%\"caja_id\":\"$caja_id\"%");
                    ;
                });
            });
        }
        if (!empty($item_id)) {
            $registros->deItem($item_id);
        }

        if (!empty($pedido_id)) {
            $registros = $registros->dePedido($pedido_id);
        }
        if(!empty($categoria_id)){

          $registros          = $registros->deCategoria($categoria_id);
        }

        $count = $registros->count();

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

        if ($sidx != NULL && $sord != NULL) {
            $registros->orderBy($sidx, $sord);
        }
        if ($limit != NULL) {
            $registros->skip($start)->take($limit);
        }

        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
          if ($count > 0) {
            foreach ($registros->get() as $i => $row) {

                $comprador =  $row->comprador;
                $hidden_options = "";
                $nombre_completo = $comprador['nombre'].' '.$comprador['apellido'];
                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $row->uuid_factura . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $hidden_options = '<a href="' . base_url('facturas_compras/ver/' . $row->uuid_factura) . '" data-id="' . $row->uuid_factura . '" class="btn btn-block btn-outline btn-success">Ver detalle</a>';

                if($row->pagable and $this->auth->has_permission('acceso', 'pagos/crear/(:any)') and $row->estado_id != 20){
                    $hidden_options .= '<a href="'.base_url('pagos/crear/facturacompra'. $row->id).'" class="btn btn-block btn-outline btn-success">Pagar</a>';
                }

                if($this->auth->has_permission('acceso', 'facturas_compras/ver/(:any)')){
                        $hidden_options .= '<a href="'.base_url('documentos/subir_documento/'. $row->uuid_factura).'" class="btn btn-block btn-outline btn-success">Subir documento</a>';
                    }
                $hidden_options .= '<a  href="'.base_url('facturas_compras/historial/'. $row->uuid_factura).'"   data-id="'.$row->id.'" class="btn btn-block btn-outline btn-success">Ver bit&aacute;cora</a>';
                $response->rows[$i]["id"] = $row->uuid_factura;
                $response->rows[$i]["cell"] = array(
                    '<a class="link" href="' . base_url('facturas_compras/ver/' . $row->uuid_factura) .'" style="color:blue;">'. $row->codigo .'</a>',
                    $row->fecha_desde,
                    count($row->proveedor) ? '<a class="link" href="' . base_url("proveedores/ver/" . $row->proveedor->uuid_proveedor) . '" style="color:blue;">' . $row->proveedor->nombre . '</a>' : '',
                    $row->present()->total,
                    $row->present()->saldo,
                    count($row->centro_contable) ? $row->centro_contable->nombre : '',
                    $nombre_completo,
                    $row->present()->estado_label,
                    $link_option,
                    $hidden_options
                );
            }
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($response))->_display();

        exit;
    }

    public function ajax_listar_de_item() {
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        $clause = $this->input->post();
        $clause["empresa_id"] = $this->empresa_id;

        $count = $this->facturasCompraRep->count($clause);

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

        $facturas = $this->facturasCompraRep->get($clause, $sidx, $sord, $limit);

        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;

        if ($count > 0) {

            foreach ($facturas as $i => $row) {

                $response->rows[$i]["id"] = $row->uuid_factura;
                $response->rows[$i]["cell"] = $this->facturasCompraRep->getCollectionCellDeItem($row, $clause["item_id"]);
            }
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($response))->_display();

        exit;
    }

    function ocultotabla($modulo_id = NULL) {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/facturas_compras/tabla.js'
        ));

        //Verificar desde donde se esta llamando
        //la tabla de facturas
        if (preg_match("/cajas/i", $this->router->fetch_class())) {
            if (!empty($modulo_id)) {
                $this->assets->agregar_var_js(array(
                    "caja_id" => $modulo_id
                ));
            }
        } elseif (preg_match("/inventarios/i", $this->router->fetch_class()) and ! empty($modulo_id)) {
            $this->assets->agregar_var_js(array(
                "item_id" => $modulo_id
            ));
        } elseif (preg_match("/pedidos/i", $this->router->fetch_class()) and ! empty($modulo_id)) {
            $this->assets->agregar_var_js(array(
                "pedidos_id" => $modulo_id
            ));
        } else {
            if (!empty($modulo_id)) {
                $this->assets->agregar_var_js(array(
                    "cliente_id" => $modulo_id
                ));
            }
        }


        $this->load->view('tabla');
    }

    public function ocultotablaV2($sp_string_var = '') {

        $this->assets->agregar_js(array(
            'public/assets/js/modules/facturas_compras/tabla.js'
        ));

        $sp_array_var = explode('=', $sp_string_var);
        if (count($sp_array_var) == 2) {

            $this->assets->agregar_var_js(array(
                $sp_array_var[0] => $sp_array_var[1]
            ));

        }

        $this->load->view('tabla');
    }

    public function ocultotablaProveedores($modulo_id=NULL){

        $this->assets->agregar_js(array(
            'public/assets/js/modules/facturas_compras/tabla.js'
        ));

        if(!empty($modulo_id)) {
            $this->assets->agregar_var_js(array(
                "proveedor_id" => $modulo_id
            ));
        }

        $this->load->view('tabla');
    }

    public function ocultotablaOrdenesCompras($modulo_id=NULL){
        $this->assets->agregar_js(array(
            'public/assets/js/modules/facturas_compras/tabla.js'
        ));

        if(!empty($modulo_id)) {
            $this->assets->agregar_var_js(array(
                "orden_compra_id" => $modulo_id
            ));
        }

        $this->load->view('tabla');

    }

    public function ocultotablaSubcontratos($modulo_id=NULL){

        $this->assets->agregar_js(array(
            'public/assets/js/modules/facturas_compras/tabla.js'
        ));
        if(is_array($modulo_id))
        {
            $this->assets->agregar_var_js([
                "campo" => collect($modulo_id)
            ]);
        }else if(!is_array($modulo_id) && !empty($modulo_id)) {
            $this->assets->agregar_var_js(array(
                "subcontrato_id" => $modulo_id
            ));
        }

        $this->load->view('tabla');
    }

    function ocultotabla_de_item($sp_string_var = ""){
        $this->assets->agregar_js(array(
            'public/assets/js/modules/facturas_compras/tabla_de_item.js'
        ));

        $sp_array_var = explode('=', $sp_string_var);
        if (count($sp_array_var) == 2) {

            $this->assets->agregar_var_js(array(
                $sp_array_var[0] => $sp_array_var[1]
            ));

        }

        $this->load->view('tabla');
    }

    public function crear($foreing_key = '') {

        if(preg_match('/proveedor/', $foreing_key))
        {
            //$proveedor_id   = str_replace('proveedor', '', $foreing_key);
            //queda pendiente integracion
        }
        elseif (preg_match('/ordencompra/', $foreing_key))
        {
            $empezable_id = str_replace('ordencompra', '', $foreing_key);
            $empezable_type = 'orden_compra';
        }
        elseif (preg_match('/subcontrato/', $foreing_key))
        {
            $empezable_id = str_replace('subcontrato', '', $foreing_key);
            $empezable_type = 'subcontrato';
        }

        $acceso = 1;
        $permiso_editar_retenido =  $this->auth->has_permission('ver__editarSubcontrato', 'subcontratos/ver/(:any)')?1:0;



        $mensaje = $clause = $data = [];

        if (!$this->auth->has_permission('acceso')) {
            $acceso = 0;
            $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud', 'clase' => 'alert-danger');
            $this->session->set_flashdata('mensaje', $mensaje);
        }

        $this->_Css();
        $this->_js();

        $empezable = collect([
            'id' => isset($empezable_id) ? $empezable_id : '',
            'type' => isset($empezable_type) ? $empezable_type : '',
            'orden_compras' => [],
            'subcontratos' => []
        ]);
        $is_admin = $this->UsuariosRepository->findByUuid($this->session->userdata('huuid_usuario'));
        $is_admin = $is_admin->roles_reales->pluck('superuser')->last();
        $this->assets->agregar_var_js(array(
            "vista" => 'crear',
            "acceso" => $acceso == 0 ? $acceso : $acceso,
            "empezable" => $empezable,
            'politica_transaccion' => collect([]),
            'permiso_editar_retenido' => $permiso_editar_retenido,
            'super_user' => 0
        ));

        $data['mensaje'] = $mensaje;

        $breadcrumb = array(
            "titulo" => '<i class="fa fa-shopping-cart"></i> Factura de compra: Crear',
            "ruta" => array(
                0 => array(
                    "nombre" => "Compras",
                    "activo" => false
                ),
                1 => array(
                    "nombre" => 'Facturas de compras',
                    "activo" => true,
                    "url" => "facturas_compras/listar",
                ),

                2 => array(
                    "nombre" => '<b>Crear</b>',
                    "activo" => true
                )
            ),
            "menu" => array(
                "nombre" => "Crear",
                "url" => "facturas_compras/crear",
                "opciones" => array()
            )
        );


        $this->template->agregar_titulo_header('Crear Factura');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    public function ver($uuid = null) {




        $acceso = 1;
        $mensaje = array();

        if (!$this->auth->has_permission('acceso', 'facturas_compras/ver/(:any)')) {
            $acceso = 0;
            $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud', 'clase' => 'alert-danger');
        }

        $this->_Css();
        $this->_js();
        $permiso_editar_retenido =  $this->auth->has_permission('ver__editarSubcontrato', 'subcontratos/ver/(:any)')?1:0;

        $factura_compra = $this->FacturaCompraRepository->findByUuid($uuid);

        $empezable = collect([
            'id' => !empty($factura_compra->operacion_type) && count($factura_compra->operacion) ? $factura_compra->operacion->id : '',
            'type' => !empty($factura_compra->operacion_type) && count($factura_compra->operacion) ? 'option' : '',
            'types' => !empty($factura_compra->operacion_type) && count($factura_compra->operacion) ? [0=>['id'=>'option','nombre'=>array_search($factura_compra->operacion_type, $this->operaciones_nombres)]] : [],
            'options' => !empty($factura_compra->operacion_type) && count($factura_compra->operacion) ? [0=>['id'=>$factura_compra->operacion->id,'nombre'=>$factura_compra->proveedor->nombre." - ".$factura_compra->operacion->numero_documento]] : []
        ]);
         $is_admin = $this->UsuariosRepository->findByUuid($this->session->userdata('huuid_usuario'));
         $is_admin = $is_admin->roles_reales->pluck('superuser')->last();
        $this->assets->agregar_var_js(array(
            "vista" => 'editar',
            "acceso" => $acceso == 0 ? $acceso : $acceso,
            "factura" => $this->FacturaCompraRepository->getCollectionCampos($factura_compra),//falta el metodo get collect
            "empezable" => $empezable,
            'politica_transaccion' => $factura_compra->politica(),
            'permiso_editar_retenido' => $permiso_editar_retenido,
            'super_user' => !empty($is_admin) ? $is_admin : 0
        ));

        $data = array();
        $data['factura_compra_id'] = $factura_compra->id;
        $data['mensaje'] = $mensaje;

        $breadcrumb = array(
            "titulo" => '<i class="fa fa-shopping-cart"></i> Factura de compra: ' . $factura_compra->factura_proveedor,
            "menu" => array(
                "nombre" => 'Acci&oacute;n',
                "url"	 => '#',
                "opciones" => array()
            ),
            "ruta" => array(
              0 => array(
                  "nombre" => "Compras",
                  "activo" => false,
                ),
               1 => array(
                   "nombre" => "Facturas de compras",
                   "activo" => false,
                     "url" => 'facturas_compras/listar'
                ),
                2 => array(
                    "nombre" => "<b>Detalle</b>",
                    "activo" => true,

                )

            ),
        );
        $breadcrumb["menu"]["opciones"]["facturas_compras/historial/" . $factura_compra->uuid_factura] = "Ver bit&aacute;cora";
        $this->template->agregar_titulo_header('Editar Factura de Compra');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    public function ocultoformulario(){

        $this->assets->agregar_js(array(
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/default/vue/components/empezar_desde.js',
            'public/assets/js/modules/facturas_compras/components/detalle.js',
            //'public/assets/js/default/vue/components/articulos.js',
            //'public/assets/js/default/vue/components/articulo.js',
            'public/assets/js/default/vue/directives/pop_over_precio.js',
            'public/assets/js/default/vue/directives/pop_over_cantidad.js',
            'public/resources/compile/modulos/facturas_compras/formulario.js'
    	));

        $clause = ['empresa_id'=>$this->empresa_id,'facturables'=>true,'transaccionales'=>true,'conItems'=>true];
        $this->assets->agregar_var_js(array(
            'orden_compras' => $this->ordenesCompraRep->getCollectionOrdenesCompraAjax($this->ordenesCompraRep->get($clause)),
            'subcontratos' => $this->subcontratosRep->getCollectionSubcontratosAjax($this->subcontratosRep->listar($clause)),
            //'proveedores' => $this->proveedoresRep->getCollectionProveedores($this->proveedoresRep->get($clause)),
            'proveedores' => collect([]),
            'terminos_pago' => $this->FacturaVentaCatalogoRepository->getTerminoPago(),
            'usuarios' => $this->UsuariosRepository->get($clause)->map(function($usuario){return ['id'=>$usuario->id,'nombre'=>$usuario->nombre_completo];}),
            'centros_contables' => $this->CentrosContablesRepository->getCollectionCentrosContables($this->CentrosContablesRepository->get($clause)),
            'bodegas' => $this->BodegasRepository->getCollectionBodegas($this->BodegasRepository->get($clause)),
            'estados' => $this->FacturaCompraCatalogoRepository->get(['tipo'=>'estado_factura_compra']),
            'categorias' => $this->ItemsCategoriasRepository->getCollectionCategorias($this->ItemsCategoriasRepository->get($clause)),
            'cuentas' => $this->CuentasRepository->get($clause),
            'impuestos' => $this->impuestosRep->get($clause),
            'empresa' => $this->empresaObj,
            'usuario_id' => $this->id_usuario
        ));

        $this->load->view('formulario');
        $this->load->view('vue/components/empezar_desde');
        $this->load->view('components/detalle');
        //$this->load->view('vue/components/articulos');
        //$this->load->view('vue/components/articulo');

    }




    private function _createFactura($factura, $post){

        $total = FacturaCompra::deEmpresa($this->empresa_id)->count();
        $factura->codigo = Util::generar_codigo('FT', ($total + 1));
        $factura->empresa_id = $this->empresa_id;
        $factura->operacion_type = array_key_exists($post['empezable_type'], $this->operaciones) ? $this->operaciones[$post['empezable_type']] : '';
        $factura->operacion_id = $post["empezable_id"];

    }

    private function _setFacturaFromPost($factura, $post) {

        $campo = $post['campo'];

        $factura->centro_contable_id = $campo["centro"];
        $factura->factura_proveedor = $campo["factura_proveedor"];
        $factura->bodega_id = $campo["lugar"];
        $factura->proveedor_id = $campo["proveedor"];
        $factura->created_by = $campo["creado_por"];
        $factura->estado_id = $campo["estado"];
        $factura->fecha_desde = Carbon::createFromFormat('d/m/Y', $campo['fecha_desde']);
        $factura->comentario = $campo["observaciones"];
        $factura->referencia = $campo["referencia"];
        $factura->termino_pago = $campo["termino_pago"];
        $factura->subtotal = $campo["subtotal"];
        $factura->descuentos = $campo["descuento"];
        $factura->impuestos = $campo["impuesto"];
        $factura->total = $campo["total"];
        $factura->retencion = isset($campo["retencion"])?$campo["retencion"]:0;
        $factura->porcentaje_retencion = isset($campo["porcentaje_retencion"])?$campo["porcentaje_retencion"]:0;


    }

    private function _getFacturasItemsFromPost($post) {
        $aux = [];
        $facturas_items = $post["items"];

        foreach ($facturas_items as $row) {
            $aux[$row["item_id"]] = [
                "categoria_id" => $row["categoria_id"],
                "cantidad" => $row["cantidad"],
                "unidad_id" => $row["unidad_id"],
                "precio_unidad" => $row["precio_unidad"],
                "impuesto_id" => is_numeric($row["impuesto_id"]) ? $row["impuesto_id"] : $this->impuestosRep->findByUuid($row["impuesto_id"])->id,
                "descuento" => $row["descuento"],
                "cuenta_id" => $row["cuenta_id"],
                "total" => $row["total"],
                "subtotal" => $row["subtotal"],
                "descuentos" => $row["descuentos"],
                "impuestos" => $row["impuestos"],
                "empresa_id" => $this->empresa_id,
                "retenido" => $row["retenido"]
            ];
        }

        return $aux;
    }

    private function _actualizarEstadoOrdenContrato($operacion_type, $operacion_id) {
        if ($operacion_type == "Ordenes_orm" && $operacion_id) {
            $registro = Ordenes_orm::find($operacion_id);
            $registro->actualizarEstado();
        } elseif ($operacion_type == "Contratos_orm") {
            //...logica para contratos
        }
    }

    private function _factura_contrato_valida($factura) {
        $subcontrato = $this->subcontratosRep->findBy($factura->operacion_id);

        if (round($subcontrato->por_facturar(), 2) >= round($factura->total, 2)) {
            return true;
        }
        return false;
    }

    public function guardar() {


        if ($_POST) {
            $success = FALSE;
            $campo = $this->input->post("campo");
            $post = $this->input->post();
            $desde_subcontratos = false;

            $toast = new Flexio\Library\Toast();
            try {
            Capsule::transaction(function() use ($campo, $post, &$success, &$desde_subcontratos) {

                $aux = $this->FacturaCompraRepository->get(['factura_proveedor' => $campo['factura_proveedor'],'empresa_id'=>$this->empresa_id,'proveedor_id'=>$campo['proveedor']]);

                if(count($aux)){

                    if(!$campo["id"] || ($aux[0]->id != $campo["id"])){
                        $success = false;
                        return;
                    }

                }

                if (!$campo["id"]){
                   // $factura = new FacturaCompra();
                    $factura = new Facturas_compras_orm;
                    $this->_createFactura($factura, $post, $campo);

                } else {

                     $factura = Facturas_compras_orm::find($campo["id"]);
                    //$factura = FacturaCompra::find($campo["id"]);
                }

                $this->_setFacturaFromPost($factura, $post, $campo);


                if($factura->operacion_type == "Flexio\\Modulo\\SubContratos\\Models\\SubContrato")
                {
                    $desde_subcontratos = true;
                }

                if ($factura->estado->etiqueta == 'por_pagar') {

                   // if ($this->_factura_contrato_valida($factura)) {
                        //$transaccion = new Transaccion;
                        //$transaccion->hacerTransaccion($factura->fresh(), new TransaccionFacturaCompra);
                        //nueva version de transacciones
                        $this->FacturasComprasTransacciones->haceTransaccion($factura);
                        $success = TRUE;
                    //} else {
                      //  $factura->estado_id = '13'; //"por_aprobar";
                      //  $success = FALSE;
                  // }
                } else {
                    $success = TRUE;
                }


                $factura->save();


                $this->FacturaCompraRepository->_sync_items($factura, $post['items']);


                //ACTUALIZAR EL ESTADO DE LA ORDEN
                if(!empty($factura->operacion_type) && count($factura->operacion) > 0 && $factura->estado->etiqueta == 'por_pagar')
                $this->_actualizarEstadoOrdenContrato($factura->operacion_type, $factura->operacion_id);
            });
            } catch (Exception $e) {
                log_message('error', $e);
                $toast->setUrl('facturas_compras/listar')->run("exception",[$e->getMessage()]);
            }



            if ($success) {
                $toast->run("success");
            } else {
                $toast->run("error");
            }

            if($desde_subcontratos)
            {
                redirect(base_url('facturas_compras_contratos/listar'));
            }
            redirect(base_url('facturas_compras/listar'));
        }
    }

    function ajax_factura_info() {
        $uuid = $this->input->post('uuid');
        $facturaObj = new Buscar(new Factura_orm, 'uuid_factura');
        $factura = $facturaObj->findByUuid($uuid);
        $factura->cliente;
        $factura->cotizacion;
        $factura->orden_venta;
        $lista = $factura->items_factura;
        foreach ($lista as $l) {
            $l->impuesto;
            $l->cuenta;
            $l->articulo;
        }


        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($factura->toArray()))->_display();
        exit;
    }

    private function _item($item) {
        return array(
            "id" => (string) $item->id,
            "nombre" => $item->comp_nombre(),
            "unidades" => $item->unidades->toArray(),
            "unidad_id" => (string) $item->unidadBaseModel()->id, //unidad base
            "impuesto_id" => (string) $item->impuestoCompra->id, //impuesto para compra
            "cuenta_id" => (string) $item->cuentaGasto->id, //cuenta de gasto,
            "impuesto" => $item->impuestoCompra->toArray()
        );
    }

    function ajax_get_items() {

        $registros = array();
        $categoriasConItems = Categorias_orm::deEmpresa($this->empresa_id)->conItems();

        foreach ($categoriasConItems->get() as $i => $row) {
            $itemsDeCategoria = Items_orm::deEmpresa($this->empresa_id)->deCategoria($row->id);
            $registros[$i]["categoria_id"] = (string) $row->id;

            foreach ($itemsDeCategoria->get() as $j => $rowJ) {

                $registros[$i]["items"][$j] = $this->_item($rowJ);
            }
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($registros))->_display();

        exit;
    }

    private function _getImpuesto($impuesto_id) {
        $aux = new stdClass();
        $aux->id = '';
        $aux->uuid_impuesto = '';
        $aux->impuesto = 0;

        return !empty($impuesto_id) ? $this->impuestosRep->find($impuesto_id) : $aux;
    }

    private function _getEmpezarDesde($registro) {
        return [
            "id" => $registro->id,
            "termino_pago" => !empty($registro->termino_pago) ? $registro->termino_pago : '',
            "proveedor" => array(
                "id" => (string) $registro->proveedor->id,
                "nombre" => $registro->proveedor->nombre,
                "saldo_pendiente" => $registro->proveedor->saldo_pendiente,
                "credito_favor" => $registro->proveedor->credito,
                "retiene_impuesto" => $registro->proveedor->retiene_impuesto
            ),
            "bodega" => array(
                "id" => isset($registro->bodega->id) ? (string) $registro->bodega->id : '',
                "nombre" => isset($registro->bodega->id) ? $registro->bodega->nombreCompleto() : ''

            ),
            "comprador" => array(
                "id" => (isset($registro->comprador->id)) ? (string) $registro->comprador->id : ''
            ),
            "referencia" => !empty($registro->referencia) ? $registro->referencia : '',
            "centro_contable" => array(
                "id" => (string) $registro->centro_contable->id,
                "nombre" => $registro->centro_contable->nombre
            )
        ];
    }

    private function _getEmpezarDesdeItems($registro, $tipo) {
        $aux = [];

        $lista = ($tipo == "Ordenes_orm") ? $registro->items : [];
        foreach ($lista as $item) {
            $impuesto = $this->_getImpuesto($item->pivot->impuesto_id);
            $subtotal = $item->pivot->cantidad * $item->pivot->precio_unidad;
            $descuentos = ($subtotal * $item->pivot->descuento) / 100;
            $subtotal -= $descuentos;
            $impuestos = ($subtotal * $impuesto->impuesto) / 100;

            $aux[] = array(
                "categoria_id"  => (string) $item->pivot->categoria_id,
                "item_id"       => (string) $item->id,
                "descripcion"   => $item->descripcion,
                "cantidad"      => $item->pivot->cantidad,
                "unidad_id"     => (string) $item->pivot->unidad_id,
                "precio_unidad" => $item->pivot->precio_unidad,
                "impuesto_id"   => (string) $impuesto->id,
                "descuento"     => $item->pivot->descuento,
                "cuenta"        => (string) $item->pivot->cuenta_id,
                //totalizadores de la fila
                "total" => $subtotal, //no se incluyen impuestos en el total de la fila
                "subtotal" => $subtotal,
                "descuentos" => $descuentos,
                "impuestos" => $impuestos
            );
        }

        return $aux;
    }

    private function _getFactura($registro) {
        return [
            "id" => $registro->id,
            "termino_pago" => $registro->termino_pago,
            "fecha_desde" => $registro->fecha_desde,
            "factura_proveedor" => $registro->factura_proveedor,
            "comentario" => $registro->comentario,
            "estado" => $registro->estado->etiqueta,
            "operacion_type" => $registro->operacion_type,
            "operacion_id" => $registro->operacion_id,
            "proveedor" => array(
                "id" => (string) $registro->proveedor->id,
                "nombre" => $registro->proveedor->nombre,
                "saldo_pendiente" => (string) ($registro->proveedor->total_saldo_pendiente()) ? : "0.00",
                "credito_favor" => $registro->proveedor->credito,
                "retiene_impuesto" => $registro->proveedor->retiene_impuesto
            ),
            "bodega" => array(
                "id" => (string) $registro->bodega->id,
                "nombre" => $registro->bodega->nombreCompleto()
            ),
            "comprador" => array(
                "id" => (string) $registro->comprador->id,
                "nombre" => $registro->comprador->nombreCompleto()
            ),
            "referencia" => $registro->referencia,
            "centro_contable" => array(
                "id" => (string) $registro->centro_contable->id,
                "nombre" => $registro->centro_contable->nombre
            ),
            "pagos" => $registro->pagos
        ];
    }

    private function _getFacturaItems($registro) {
        $aux = [];
        $lista = $registro->facturas_compras_items;
        foreach ($lista as $l) {
            $impuesto = $this->_getImpuesto($l->impuesto_id);
            $subtotal = $l->cantidad * $l->precio_unidad;
            $descuentos = ($subtotal * $l->descuento) / 100;
            $subtotal -= $descuentos;
            $impuestos = ($subtotal * $impuesto->impuesto) / 100;

            $aux[] = array(
                "categoria_id"  => (string) $l->categoria_id,
                "item_id"       => (string) $l->item->id,
                "descripcion"   => $l->item->descripcion,
                "cantidad"      => $l->cantidad,
                "unidad_id"     => (string) $l->unidad_id,
                "precio_unidad" => $l->precio_unidad,
                "impuesto_id"   => (string) $impuesto->id,
                "descuento" => $l->descuento,
                "cuenta" => (string) $l->cuentaDeGasto->id,
                //totalizadores de la fila

                "total"         => $subtotal,//no se incluyen impuestos en el total de la fila
                "subtotal"      => $subtotal,
                "descuentos"    => $descuentos,
                "impuestos"     => $impuestos,
                "factura_item_id" => (string) $l->id,
            );
        }

        return $aux;
    }

    function ajax_get_empezar_desde() {
        $id = $this->input->post('uuid'); //se recibe el id (int:10)
        $tipo = $this->input->post('tipo');

        if ($tipo == "Ordenes_orm") {
            $registro = $this->ordenesCompraRep->find($id);
        } elseif ($tipo == "Subcontratos") {
            $registro = $this->subcontratosRep->findBy($id);
        }

        $aux = $this->_getEmpezarDesde($registro);
        $aux["items"] = $this->_getEmpezarDesdeItems($registro, $tipo);


        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($aux))->_display();

        exit;
    }

    function ajax_get_factura() {
        $factura_id = $this->input->post('factura_id');
        $registro = Facturas_compras_orm::find($factura_id);

        $aux = $this->_getFactura($registro);
        $aux["items"] = $this->_getFacturaItems($registro);


        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($aux))->_display();

        exit;
    }

    function ajax_getFacturadoCompleto() {
        $clause = ['empresa_id' => $this->empresa_id];
        $factura = $this->facturasCompraRep->cobradoCompletoSinNotaDebito($clause);
        $factura->load('proveedor', 'facturas_compras_items.inventario_item','facturas_compras_items.impuesto');

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($factura->toArray()))->_display();
        exit();
    }

    private function _Css() {
        $this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
            'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
            'public/assets/css/plugins/jquery/jquery.webui-popover.css',
            'public/assets/js/plugins/jquery/sweetalert/sweetalert.css',
            'public/assets/css/modules/stylesheets/facturas_compras.css',
            'public/assets/css/plugins/jquery/jquery.fileupload.css',
            'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
            'public/assets/css/plugins/bootstrap/select2.min.css'
        ));
    }

    private function _js() {
        $this->assets->agregar_js(array(
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
            'public/assets/js/plugins/jquery/combodate/combodate.js',
            'public/assets/js/plugins/jquery/combodate/momentjs.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
            'public/assets/js/plugins/jquery/fileupload/jquery.fileupload.js',
            'public/assets/js/plugins/bootstrap/select2/select2.min.js',
            'public/assets/js/plugins/bootstrap/select2/es.js',
            'public/assets/js/default/vue/directives/datepicker2.js',
            'public/assets/js/default/vue/directives/inputmask.js',
            'public/assets/js/default/vue/directives/select2.js',
        ));
    }

    function documentos_campos(){

    	return array(
    	array(
    		"type"		=> "hidden",
    		"name" 		=> "factura_compra_id",
    		"id" 		=> "factura_Compra_id",
    		"class"		=> "form-control",
    		"readonly"	=> "readonly",
    	));
    }

    function ajax_guardar_documentos()
    {
    	if(empty($_POST)){
    		return false;
    	}

    	$factura_id = $this->input->post('factura_id', true);
        $modeloInstancia = FaccomModel::find($factura_id);

    	$this->documentos->subir($modeloInstancia);
    }
    function historial($uuid = NULL){

        $acceso = 1;
        $mensaje =  array();
        $data = array();

       // $factura = Facturas_compras_orm::findByUuid($uuid);
        $facturaObj = new Buscar(new Facturas_compras_orm, 'uuid_factura');
        $factura = $facturaObj->findByUuid($uuid);

        if(!$this->auth->has_permission('acceso','facturas_compras/historial') && is_null($factura)) {
            // No, tiene permiso
            $acceso = 0;
            $mensaje = array('estado' => 500, 'mensaje' => ' <b>Usted no cuenta con permiso para esta solicitud</b>', 'clase' => 'alert-danger');
        }
            $this->_Css();
            $this->_js();
            $this->assets->agregar_js(array(
                'public/resources/compile/modulos/facturas_compras/historial.js'
            ));
            $breadcrumb = array(
                "titulo" => '<i class="fa fa-shopping-cart"></i> Bit&aacute;cora de Facturas: '.$factura->codigo,
            );

            $factura->load('historial');

                $historial = $factura->historial->map(function($factHist) use ($factura){
                    return [
                        'id'  => $factHist->id,
                        'titulo' => $factHist->titulo,
                        "codigo" =>$factura->codigo,
                        "descripcion" =>$factHist->descripcion,
                        "antes" => $factHist->antes,
                        "despues" => $factHist->despues,
                        "tipo" => $factHist->tipo,
                        "nombre_usuario" => $factHist->nombre_usuario,
                        "hace_tiempo" => $factHist->cuanto_tiempo,
                        "fecha_creacion" => $factHist->fecha_creacion,
                        "hora" => $factHist->hora
                    ];
               });
        $this->assets->agregar_var_js(array(
            'historial' => $historial
        ));
            $this->template->agregar_titulo_header('Facturas de compras');
            $this->template->agregar_breadcrumb($breadcrumb);
            $this->template->agregar_contenido($data);
            $this->template->visualizar();

    }
}

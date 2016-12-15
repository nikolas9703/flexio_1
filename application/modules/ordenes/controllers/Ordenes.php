<?php
/**
 * Pedidos
 *
 * Modulo para administrar la creacion, edicion de ordenes
 *
 * @package    PensaApp
 * @subpackage Controller
 * @category   Controllers
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  10/16/2015
 */

use Illuminate\Database\Capsule\Manager as Capsule;
use League\Csv\Writer as Writer;
use Dompdf\Dompdf;
use Carbon\Carbon;

//repositorios
use Flexio\Modulo\Bodegas\Repository\BodegasRepository as bodegasRep;
use Flexio\Modulo\Inventarios\Repository\ItemsRepository as itemsRep;
use Flexio\Modulo\OrdenesCompra\Repository\OrdenesCompraRepository as ordenesCompraRep;
use Flexio\Modulo\OrdenesCompra\Models\OrdenesCompra as OrdenesModel;
use Flexio\Modulo\Pedidos\Repository\PedidoRepository;
use Flexio\Modulo\Proveedores\Repository\ProveedoresRepository;
use Flexio\Modulo\CentrosContables\Repository\CentrosContablesRepository;
use Flexio\Modulo\Pedidos\Repository\PedidosCatRepository;
use Flexio\Modulo\FacturasVentas\Repository\FacturaVentaCatalogoRepository;
use Flexio\Modulo\Inventarios\Repository\CategoriasRepository as ItemsCategoriasRepository;
use Flexio\Modulo\Contabilidad\Repository\CuentasRepository;
use Flexio\Modulo\Contabilidad\Repository\ImpuestosRepository;
use Flexio\Modulo\OrdenesCompra\Repository\OrdenesCompraCatRepository;
use Flexio\Modulo\Usuarios\Repository\UsuariosRepository;

//utils
use Flexio\Library\Util\FlexioSession;

class Ordenes extends CRM_Controller
{
    protected $id_empresa;
    protected $prefijo;
    protected $id_usuario;
    protected $PedidosRepository;
    protected $ProveedoresRepository;
    protected $CentrosContablesRepository;
    protected $PedidosCatRepository;
    protected $FacturaVentaCatalogoRepository;
    protected $ItemsCategoriasRepository;
    protected $CuentasRepository;
    protected $ImpuestosRepository;
    protected $OrdenesCompraCatRepository;
    protected $misPoliticas;
    protected $UsuariosRepository;
    protected $RolesUsuario;
    //protected

    //repositorios
    private $bodegasRep;
    private $itemsRep;
    private $ordenesCompraRep;

    //utils
    protected $FlexioSession;

    public function __construct()
    {
        parent::__construct();

        //MODULOS
        $this->load->model('usuarios/Empresa_orm');
        $this->load->model('usuarios/Usuario_orm');
        $this->load->model('usuarios/Roles_usuarios_orm');
        $this->load->module("entradas/Entradas");

        $this->load->model("Ordenes_orm");
        $this->load->model("Ordenes_estados_orm");
        $this->load->model("Ordenes_items_orm");

        $this->load->model("entradas/Entradas_orm");
        $this->load->model("entradas/Entradas_items_orm");

        $this->load->model("bodegas/Bodegas_orm");

        $this->load->model("inventarios/Items_orm");
        $this->load->model("inventarios/Unidades_orm");

        $this->load->model("salidas/Salidas_orm");
        $this->load->module(array('documentos'));
        $this->load->model("consumos/Consumos_orm");
        $this->load->model("consumos/Consumos_items_orm");

        $this->load->model("ordenes_ventas/Orden_ventas_orm");
        $this->load->model("ordenes_ventas/Ordenes_venta_item_orm");

        $this->load->model("contabilidad/Cuentas_orm");
        $this->load->model("contabilidad/Impuestos_orm");

        $this->load->model("pedidos/Pedidos_orm");
        $this->load->model("pagos/Pagos_orm");
        //$this->load->model("pedidos/Pedidos_estados_orm");
        $this->load->model("pedidos/Pedidos_items_orm");

        $this->load->model("facturas_compras/Facturas_compras_orm");

        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();

        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $uuid_usuario = $this->session->userdata('huuid_usuario');

        $empresaObj = new Buscar(new Empresa_orm,'uuid_empresa');
        $usuario = Usuario_orm::findByUuid($uuid_usuario);

        $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);

        $this->id_empresa = $this->empresaObj->id;
        $this->id_usuario = $usuario->id;

        //PREFIJO DE NOMEMCLATURA DE PEDIDO
        $this->prefijo = "OC";

        $this->bodegasRep = new bodegasRep();
        $this->itemsRep = new itemsRep();
        $this->ordenesCompraRep = new ordenesCompraRep();
        $this->PedidosRepository = new PedidoRepository();
        $this->ProveedoresRepository = new ProveedoresRepository();
        $this->CentrosContablesRepository = new CentrosContablesRepository();
        $this->PedidosCatRepository = new PedidosCatRepository();
        $this->FacturaVentaCatalogoRepository = new FacturaVentaCatalogoRepository();
        $this->ItemsCategoriasRepository = new ItemsCategoriasRepository();
        $this->CuentasRepository = new CuentasRepository();
        $this->ImpuestosRepository = new ImpuestosRepository();
        $this->OrdenesCompraCatRepository = new OrdenesCompraCatRepository();
        $this->UsuariosRepository = new UsuariosRepository;

        //utils
        $this->FlexioSession = new FlexioSession;

        $config = Array(
            'mailtype' => 'html',
            'charset' => 'utf-8',
            'wordwrap' => TRUE
        );
          $this->load->library('email', $config);
     }



    public function index()
    {
        redirect("ordenes/listar");
    }


    public function listar()
    {
        $data = array();
        $mensaje ='';
    	$this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/plugins/jquery/switchery.min.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
            'public/assets/css/plugins/bootstrap/bootstrap-tagsinput.css',
            'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
            'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
            'public/assets/css/plugins/jquery/fileinput/fileinput.css',
            'public/assets/css/plugins/jquery/jquery.fileupload.css',
            'public/assets/css/modules/stylesheets/ordenes_compras.css',
            'public/assets/js/plugins/jquery/sweetalert/sweetalert.css'
        ));

        $this->assets->agregar_js(array(
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/plugins/jquery/jqgrid/plugins/jQuery.jqGrid.columnToggle.js',
            'public/assets/js/plugins/jquery/switchery.min.js',
            'public/assets/js/plugins/bootstrap/bootstrap-tagsinput.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            'public/assets/js/plugins/jquery/fileinput/fileinput.js',
            'public/assets/js/plugins/jquery/fileinput/fileinput_locale_es.js',
            'public/assets/js/default/grid.js',
            'public/assets/js/default/subir_documento_modulo.js',
            'public/assets/js/plugins/jquery/sweetalert/sweetalert.min.js',

            /* Archivos js para la vista de Crear Actividades */
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/plugins/bootstrap/bootstrap-tabdrop.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
            'public/assets/js/default/toast.controller.js',
            'public/assets/js/default/formulario.js',
            'public/assets/js/plugins/jquery/fileupload/jquery.fileupload.js',
            /* Archivos js del propio modulo*/
            'public/assets/js/modules/ordenes/listar.js',
        ));
        if(!empty($this->session->flashdata('mensaje')))
        {
            $mensaje = json_encode($this->session->flashdata('mensaje'));
        }



        $clause = array();

        $sidx       = 'id';
        $sord       = 'desc';
        $limit      = '1000';

        $orden      = new Ordenes_orm;
        $orden      = $orden->where("id_empresa", "=", $this->id_empresa);
        $orden      = $orden->orderBy($sidx, $sord)
                    ->take($limit)
                    ->get();

        //Verificar si hay datos.
        if($orden->count() > 0)
        {
            $i=0;
            foreach($orden as $row)
            {
                $hidden_options = "";

                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-nombre="'.$row->numero.'" data-orden="'. $row->uuid_orden .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

                //IMPORTANTE A MODO DE DESARROLLO ANADI LA CONDICION OR 1
                //PARA QUE TODAS LAS CONDICIONES DIERAN TRUE

                if(1 OR $this->auth->has_permission('acceso', 'orden/ver/(:any)')){
                    $hidden_options .= '<a href="'. base_url('orden/ver/'. $row->uuid_orden) .'" class="btn btn-block btn-outline btn-success">Ver Orden</a>';
                }


                $camposGrid[$i]["uuid"]                 = isset($row->uuid_orden) ? $row->uuid_orden : NULL;

                $camposGrid[$i]["titulo"]["name"]       = "N&uacute;mero";
                $camposGrid[$i]["titulo"]["value"]      = $this->prefijo.$row->numero;

                $camposGrid[$i]["subtitulo"]["name"]    = "Fecha";
                $camposGrid[$i]["subtitulo"]["value"]   = $row->fecha_creacion;

                $camposGrid[$i]["info"][0]["name"]      = "Proveedor";
                $camposGrid[$i]["info"][0]["value"]     = $row->proveedor_nombre;

                $camposGrid[$i]["info"][1]["name"]      = "Referencia";
                $camposGrid[$i]["info"][1]["value"]     = isset($row->referencia) ? $row->referencia : "";

                $camposGrid[$i]["info"][2]["name"]      = "Centro Contable";
                $camposGrid[$i]["info"][2]["value"]     = isset($row->centro->nombre)?$row->centro->nombre:'';

                $camposGrid[$i]["info"][3]["name"]      = "Estado";
                $camposGrid[$i]["info"][3]["value"]     = $row->estado->etiqueta;

                $camposGrid[$i]["info"][4]["name"]      = "Monto";
                $camposGrid[$i]["info"][4]["value"]     = "\$".$row->monto;

                $camposGrid[$i]["id"]                   = $row->id;
                $camposGrid[$i]["opcion"]               =  $hidden_options;

                $i++;
            }

            $data["estados"]    = Ordenes_estados_orm
                                ::where("id_campo", "=", "7")
                                ->orderBy("id_cat", "ASC")
                                ->get();

            $data["centros"]    = Centros_orm::deEmpresa($this->id_empresa)
                                ->activa()
                                ->deMasJuventud($this->id_empresa)
                                ->orderBy("nombre", "ASC")
                                ->get();

            $data["proveedores"]    = Proveedores_orm
                                    ::where("id_empresa", "=", $this->id_empresa)
                                    ->where("estado", 'activo')
                                    ->orderBy("nombre", "ASC")
                                    ->get();
            $clause = ['empresa_id' => $this->id_empresa];
            $data["usuarios"]    =  $this->UsuariosRepository->get($clause, 'nombre', 'ASC');

            $clause = ['empresa_id'=>$this->id_empresa,'ordenables'=>true,'transaccionales'=>true,'conItems'=>true, 'estado != por_aprobar'];
            $data["categorias"]  = $this->ItemsCategoriasRepository->getCollectionCategorias($this->ItemsCategoriasRepository->get($clause));




            $data           = array_merge($data,$camposGrid);
        }


    	//Breadcrum Array
        $breadcrumb = array(
            "titulo"    => '<i class="fa fa-shopping-cart"></i> &Oacute;rdenes de compras',
            "ruta" => array(
                0 => array(
                    "nombre" => "Compras",
                    "activo" => false
                ),
                1 => array(
                    "nombre" => '<b>&Oacute;rdenes de compras</b>',
                    "activo" => true
                )
            ),
           // "filtro"    => true,
            "menu"      => array()
        );

        //Verificar si tiene permiso a la seccion de Crear
        if (1 or $this->auth->has_permission('acceso', 'ordenes/crear')){
            $breadcrumb["menu"]["nombre"] = "Crear";
            $breadcrumb["menu"]["url"] = "ordenes/crear";
        }

        //Verificar si tiene permiso de Exportar
        if (1 or $this->auth->has_permission('listar__exportar', 'ordenes/listar')){
            $breadcrumb["menu"]["opciones"]["#exportarBtn"] = "Exportar";
        }

        //Agregra variables PHP como variables JS
        $this->assets->agregar_var_js(array(
            "mensaje_clase"     => isset($data["mensaje"]["clase"]) ? $data["mensaje"]["clase"] : "0",
            "mensaje_contenido" => isset($data["mensaje"]["contenido"]) ? $data["mensaje"]["contenido"] : "0",
            "toast_mensaje" => $mensaje
        ));

        unset($data["mensaje"]);

    	$this->template->agregar_titulo_header('Listado de &Oacute;rdenes de compras');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar($breadcrumb);

    }

    function crearsubpanel()
    {




        $this->template->vista_parcial(array(
            'ordenes',
            'crear'
        ));
    }

    public function editarsubpanel($uuid = NULL)
    {



        $this->template->vista_parcial(array(
            'ordenes',
            'editar'
        ));
    }

    public function ajax_obtener_item()
    {
    	//Just Allow ajax request
    	if($this->input->is_ajax_request())
        {
            $uuid           = $this->input->post("uuid", true);
            $uuid_bodega    = $this->input->post("uuid_bodega", true) ? : NULL;
            $item           = $this->itemsRep->findByUuid($uuid);

            $response               = array();
            $response["success"]    = false;
            $response["registro"]   = array(
                "unidades"  => array()
            );

            if(count($item))
            {
                $response["success"]    = true;
                $response["registro"]   = $this->itemsRep->getColletionRegistro($item, $uuid_bodega);
            }

            echo json_encode($response);
            exit();
        }

    }



    public function ajax_obtener_pedido()
    {
    	//Just Allow ajax request
    	if($this->input->is_ajax_request())
        {
            $uuid_pedido    = $this->input->post("uuid_pedido");
            $registro       = Pedidos_orm::findByUuid($uuid_pedido);

            $aux            = array();
            if(count($registro))
            {
                //dd($registro->toArray());
                $aux = array(
                    "referencia"    => $registro->referencia,
                    "uuid_centro"   => strtoupper(bin2hex($registro->uuid_centro)),
                    "uuid_lugar"    => strtoupper(bin2hex($registro->uuid_lugar)),//bodega de recepcion del pedido
                );
            }

            $response               = array();
            $response["success"]    = count($registro) ? true : false;
            $response["registro"]   = $aux;

            echo json_encode($response);
            exit();
        }

    }

    public function ajax_obtener_impuesto()
    {
    	//Just Allow ajax request
    	if($this->input->is_ajax_request())
        {
            $response               = array();
            $response["success"]    = false;
            $response["impuesto"]   = array();

            if($this->input->post("uuid"))
            {
                $registro               = Impuestos_orm::findByUuid($this->input->post("uuid"));
                $response["success"]    = true;
                $response["impuesto"]   = array(
                    "valor"     => count($registro) ? $registro->impuesto : "0"
                );
            }

            echo json_encode($response);
            exit();
        }

    }

    public function ajax_obtener_orden_item()
    {

    	if($this->input->is_ajax_request())
        {

            $id_orden_item  = $this->input->post("id_orden_item", true);
            $registro       = new Ordenes_items_orm;

            $registro       = $registro->find($id_orden_item);


            $response               = array();
            $response["success"]    = true;
            $response["registro"]   = array(
                "id"            => $registro->id,
                "id_item"       => $registro->id_item,
                "cantidad"      => $registro->cantidad,
                "unidad"        => $registro->unidad,
                "precio_unidad" => $registro->precio_unidad,
                "uuid_impuesto" => strtoupper(bin2hex($registro->uuid_impuesto)),
                "descuento"     => $registro->descuento,
                "cuenta"        => $registro->cuenta
            );

            echo json_encode($response);
            exit();
        }

    }

    public function ajax_enviar_correo(){


      if(!$this->input->is_ajax_request()){
        return false;
      }


              $orden_id = $this->input->post("orden_id", true);
              $correo = $this->input->post("correo", true);

              $result = $this->enviar_correo_proveedor($this->ordenesCompraRep->find($orden_id), $correo);
                if($result){
                  echo json_encode(array(
                      "response" => true,
                      "mensaje" => "&Eacute;xito! Se ha enviado correctamente el correo."
                  ));

                }else{
                  echo json_encode(array(
                    "response" => false,
                    "mensaje" => "Hubo un error tratando de enviar el correo."
                  ));
                }

        exit();
    }

    public function ajax_obtener_resto_items()
    {
    	//Just Allow ajax request
    	if($this->input->is_ajax_request())
        {

            $id_pedido_item = $this->input->post("id_pedido_item", true);
            $uuid_pedido    = $this->input->post("uuid_pedido", true);

            $registro       = Pedidos_items_orm::find($id_pedido_item);

            $ordenes        = new Ordenes_orm;
            $ordenes        = $ordenes
                            ->where("uuid_pedido", "=", hex2bin(strtolower($uuid_pedido)))
                            ->where("id_estado", "<", "5")//Que no esten anuladas
                            ->get();

            $id_ordenes     = array();
            foreach($ordenes as $orden)
            {
                $id_ordenes[] = $orden->id;
            }

            //RECIBE LOS IDS DE LA ORDENES ASOCIADAS AL PEDIDO
            if(!empty($id_ordenes))
            {
                $cantidad   = new Ordenes_items_orm;
                $cantidad   = $cantidad
                            ->whereIn("id_orden", $id_ordenes)
                            ->where("id_item", "=", $registro->id_item)
                            ->sum("cantidad");

                //verifica si esta en el historial antes de relaizar la resta
                $registro->cantidad -= $cantidad;

                if($registro->cantidad < 0)
                {
                    $registro->cantidad = 0;
                }
            }


            $response               = array();
            $response["success"]    = false;
            $response["registro"]   = $registro->toArray();

            if(!empty($response["registro"]))
            {
                $response["success"]    = true;
            }

            echo json_encode($response);
            exit();
        }

    }

    public function ajax_listar()
    {
    	//Just Allow ajax request
    	if($this->input->is_ajax_request())
    	{
            /**
             * Get the requested page.
             * @var int
             */
            $page = (int)$this->input->post('page', true);

            /**
             * Get how many rows we want to have into the grid
             * rowNum parameter in the grid.
             * @var int
            */
            $limit = (int)$this->input->post('rows', true);

            /**
             * Get index row - i.e. user click to sort
             * at first time sortname parameter - after that the index from colModel.
             * @var int
            */
            $sidx = $this->input->post('sidx', true);

            /**
             * Sorting order - at first time sortorder
             * @var string
            */
            $sord = $this->input->post('sord', true);

            //Para aplicar filtros
            $registros = new Ordenes_orm;
            $registros = $registros->where("id_empresa", "=", $this->id_empresa);

            $registros_count = new Ordenes_orm;
            $registros_count = $registros_count->where("id_empresa", "=", $this->id_empresa);

            /**
             * Verificar si existe algun $_POST
             * de los campos de busqueda
            */
            $fecha_desde     = $this->input->post('fecha_desde', true);
            $fecha_hasta     = $this->input->post('fecha_hasta', true);
            $centro     = $this->input->post('centro', true);
            $estado     = $this->input->post('estado', true);
            $creado_por = $this->input->post('creado_por', true);
            $numero     = $this->input->post('numero', true);
            $proveedor  = $this->input->post('proveedor', true);
            $montos_de  = $this->input->post('montos_de', true);
            $montos_a   = $this->input->post('montos_a', true);

            $pedido_id     = $this->input->post('pedido_id', true);
            $categoria_id     = $this->input->post('categoria_id', true);


            //subpanels
            $factura_compra_id = $this->input->post('factura_compra_id', true);

            if(!empty($factura_compra_id)){
                $registros          = $registros->deFacturaDeCompra($factura_compra_id);

                $registros_count    = $registros_count->deFacturaDeCompra($factura_compra_id);
            }

            if(!empty($pedido_id)){
                $pedido = $this->PedidosRepository->find($pedido_id);
                $registros          = $registros
                                    ->where("uuid_pedido", "=", hex2bin(strtolower($pedido->uuid_pedido)));

                $registros_count    = $registros_count
                                    ->where("uuid_pedido", "=", hex2bin(strtolower($pedido->uuid_pedido)));
            }

          /*  if(!empty($fechas)){
                $fechas_array       = explode(" hasta ", $fechas);

                $registros          = $registros
                                    ->where("fecha_creacion", ">=", date('Y-m-d', strtotime($fechas_array[0])))
                                    ->where("fecha_creacion", "<=", date('Y-m-d', strtotime($fechas_array[1])));

                $registros_count    = $registros_count
                                    ->where("fecha_creacion", ">=", date('Y-m-d', strtotime($fechas_array[0])))
                                    ->where("fecha_creacion", "<=", date('Y-m-d', strtotime($fechas_array[1])));
            } */

           if(!empty($fecha_desde)){

                $registros          = $registros
                                    ->where("fecha_creacion", ">=", date('Y-m-d', strtotime($fecha_desde)));
                $registros_count    = $registros_count
                                    ->where("fecha_creacion", ">=", date('Y-m-d', strtotime($fecha_desde)));

           }

           if(!empty($fecha_hasta)){

                $registros          = $registros
                                    ->where("fecha_creacion", "<=", date('Y-m-d', strtotime($fecha_hasta)));
                $registros_count    = $registros_count
                                    ->where("fecha_creacion", "<=", date('Y-m-d', strtotime($fecha_hasta)));

           }

            if(!empty($montos_de)){
                $registros          = $registros
                                    ->where("monto", ">=", $montos_de);

                $registros_count    = $registros_count
                                    ->where("monto", ">=", $montos_de);
            }
            if(!empty($montos_a)){
                $registros          = $registros
                                    ->where("monto", "<=", $montos_a);

                $registros_count    = $registros_count
                                    ->where("monto", "<=", $montos_a);
            }

            //filtros de centros contables del usuario
            $centros = $this->FlexioSession->usuarioCentrosContablesHex();
            if(!in_array('todos', $centros))
            {
                $registros = $registros->whereIn("uuid_centro", array_map(function($centro1){return hex2bin($centro1);}, $centros));
                $registros = $registros_count->whereIn("uuid_centro", array_map(function($centro1){return hex2bin($centro1);}, $centros));
            }

            if(!empty($centro)){
                $registros          = $registros->where("uuid_centro", "=", hex2bin(strtolower($centro)));
                $registros_count    = $registros_count->where("uuid_centro", "=", hex2bin(strtolower($centro)));
            }
            if(!empty($proveedor)){
                $registros          = $registros->where("uuid_proveedor", "=", hex2bin(strtolower($proveedor)));
                $registros_count    = $registros_count->where("uuid_proveedor", "=", hex2bin(strtolower($proveedor)));
            }
            if(!empty($estado)){
                $registros          = $registros->where("id_estado", "=", $estado);
                $registros_count    = $registros_count->where("id_estado", "=", $estado);
            }
          /*  if(!empty($referencia)){
                $registros          = $registros->where("referencia", "like", "%$referencia%");
                $registros_count    = $registros_count->where("referencia", "like", "%$referencia%");
            }*/
            if(!empty($creado_por)){
                $registros          = $registros->where("creado_por", "=",  $creado_por);
                $registros_count    = $registros_count->where("creado_por", "=",  $creado_por);
            }
            if(!empty($numero)){
                $numero             = str_replace($this->prefijo,"",$numero);

                $registros          = $registros->where("numero", "like", "%$numero%");
                $registros_count    = $registros_count->where("numero", "like", "%$numero%");
            }
            if(!empty($categoria_id)){

              $registros          = $registros->deCategoria($categoria_id);

              $registros_count    = $registros_count->deCategoria($categoria_id);

            }



            /**
             * Total rows found in the query.
             * @var int
            */
            $count          = $registros_count->get()->count();

            /**
             * Calcule total pages if $coutn is higher than zero.
             * @var int
            */
            $total_pages = ($count > 0 ? ceil($count/$limit) : 0);

            // if for some reasons the requested page is greater than the total
            // set the requested page to total page
            if ($page > $total_pages) $page = $total_pages;

            /**
             * calculate the starting position of the rows
             * do not put $limit*($page - 1).
             * @var int
             */
            $start = $limit * $page - $limit; // do not put $limit*($page - 1)

            // if for some reasons start position is negative set it to 0
            // typical case is that the user type 0 for the requested page
            if($start < 0) $start = 0;


            $registros = $registros->orderBy($sidx, $sord)
                    ->skip($start)
                    ->take($limit)
                    ->get();

            //Constructing a JSON
            $response   = new stdClass();
            $response->page     = $page;
            $response->total    = $total_pages;
            $response->records  = $count;
            $i = 0;



            if(!empty($registros) )
            {
                foreach ($registros AS $i => $row)
                {
                    $hidden_options = "";
                    $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-nombre="'.$row->numero.'" data-orden="'. $row->id .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

                    //IMPORTANTE A MODO DE DESARROLLO ANADI LA CONDICION OR 1
                    //PARA QUE TODAS LAS CONDICIONES DIERAN TRUE

                    $enlace = $row->numero;
                    if($this->auth->has_permission('acceso', 'ordenes/ver/(:any)')){
                        //
                        $hidden_options .= '<a href="'.base_url('ordenes/ver/'. $row->uuid_orden).'" class="btn btn-block btn-outline btn-success">Ver detalle</a>';

                        $enlace = '<a href="'. base_url('ordenes/ver/'. $row->uuid_orden) .'" style="color:blue;">'.$enlace.'</a>';
                    }

                    if($row->facturable and $this->auth->has_permission('acceso', 'facturas_compras/crear/(:any)')){
                        $hidden_options .= '<a href="'.base_url('facturas_compras/crear/ordencompra'. $row->id).'" class="btn btn-block btn-outline btn-success">Agregar Factura</a>';
                    }


                    if($this->auth->has_permission('acceso', 'ordenes/ver/(:any)')){
                        $hidden_options .= '<a href="#" data-id="'.$row->id.'" class="btn btn-block btn-outline btn-success subirDocumento">Subir documento</a>';
                        $hidden_options .= '<a  href="'.base_url('ordenes/historial/'. $row->uuid_orden).'"   data-id="'.$row->id.'" class="btn btn-block btn-outline btn-success">Ver bit&aacute;cora</a>';
                    }



                    if($row->estado->id_cat == 2 && count($row->proveedor)){

                             $hidden_options .= '<a href="#" data-id="'.$row->id.'" data-correo="'.$row->proveedor->email.'" data-nombre="'.$row->proveedor_nombre.'"  data-codigo="'.$row->numero.'"  class="btn btn-block btn-outline btn-success enviar_correo_proveedor">Enviar a proveedor</a>';

                             $hidden_options .= '<a href="' . base_url('anticipos/crear/?orden_compra=' . $row->uuid_orden) .'" class="btn btn-block btn-outline btn-success">Crear anticipo</a>';

                    }


//dd($row);
                    //Si no tiene acceso a ninguna opcion
                    //ocultarle el boton de opciones
                    if($hidden_options == ""){
                            $link_option = "&nbsp;";
                    }
                    $enlace_proveedor =   is_null($row->proveedor)?'':$row->proveedor->uuid_proveedor;
                    $response->rows[$i]["id"]   = $row->id;
                    $response->rows[$i]["cell"] = array(
                        $enlace,
                        $row->fecha_creacion,
                        '<a href="'. base_url('proveedores/ver/' . $enlace_proveedor) .'" style="color:blue;">'. $row->proveedor_nombre .'</a>',
                        $row->present()->monto,
                        isset($row->centro->nombre)?$row->centro->nombre:'',
                        $row->comprador->nombre.' '.$row->comprador->apellido,
                        $row->present()->estado_label,
                        $link_option,
                        $hidden_options,
                    );
                    $i++;
                }
            }
            echo json_encode($response);
            exit;
    	}
    }

    function ajax_anular()
    {
        $response = array();
        $response["success"]    = false;
        $response["mensaje"]    = "Error de sistema. Comuniquelo con el administrador de sistema";
        $response["clase"]      = "alert-danger";

        $uuid   = $this->input->post("uuid", true);
        if(!empty($uuid))
        {
            $registro   = Pedidos_orm
                        ::where("uuid_pedido", "=", hex2bin(strtolower($uuid)))
                        ->first();

            //DEFINO EL ESTADO COMO ANULADO = 6
            $registro->id_estado = "6";
            if($registro->save())
            {
                $response["success"]    = true;
                $response["mensaje"]    = "Su solicitud fue procesada satifastoriamente.";
                $response["clase"]      = "alert-success";
            }

        }

        echo json_encode($response);
        exit();
    }

    function ajax_eliminar_orden_item()
    {
    	//Just Allow ajax request
    	if(!$this->input->is_ajax_request()){
    		return false;
    	}

        $id_registro    = $this->input->post("id_registro", true);
    	$registro       = Ordenes_items_orm::find($id_registro);

        if(0 and $registro)
        {
            $response   = array(
                "respuesta" => $registro->delete(),
                "mensaje"   => "Se ha eliminado el registro satisfactoriamente"
            );
        }
        else
        {
            $response   = array(
                "respuesta" => true,
                "mensaje"   => "Se ha eliminado el registro satisfactoriamente"
            );
        }




        $json       = '{"results":['.json_encode($response).']}';
    	echo $json;
    	exit;
    }

    function ajax_reabrir()
    {
        $response = array();
        $response["success"]    = false;
        $response["mensaje"]    = "Error de sistema. Comuniquelo con el administrador de sistema";
        $response["clase"]      = "alert-danger";

        $uuid   = $this->input->post("uuid", true);
        if(!empty($uuid))
        {
            $registro   = Pedidos_orm
                        ::where("uuid_pedido", "=", hex2bin(strtolower($uuid)))
                        ->first();

            //DEFINO EL ESTADO COMO ABIERTO = 1
            $registro->id_estado = "1";
            if($registro->save())
            {
                $response["success"]    = true;
                $response["mensaje"]    = "Su solicitud fue procesada satifastoriamente.";
                $response["clase"]      = "alert-success";
            }

        }

        echo json_encode($response);
        exit();
    }

    public function exportar()
    {
    	if(empty($_POST)){
    		die();
    	}

    	$ids =  $this->input->post('ids', true);
		$id = explode(",", $ids);

		if(empty($id)){
			return false;
		}

		$csv = array();
		$clause = array(
			"id" => $id
		);
		//$ordenes = Ordenes_orm::listar($clause, NULL, NULL, NULL, NULL);
                     //
                $aux = Ordenes_orm::whereIn("id", $id)->get();

              /*  {
                    echo $aux[0]->proveedor->nombre."<br>";
                } */

            //print_r($ordenes->toArray());

		if(empty($aux)){
			return false;
		}

		$i=0;
		foreach ($aux as $row)
		{

			//$nombre = Util::verificar_valor($row['nombre']);
			//$apellido = Util::verificar_valor($row['apellido']);

			$csvdata[$i]['numero'] = "OC" . $row['numero'];
			$csvdata[$i]["fecha"] = $row['fecha_creacion'];
			$csvdata[$i]["proveedor"] = utf8_decode(Util::verificar_valor($row->proveedor->nombre));
			$csvdata[$i]["referencia"] = utf8_decode(Util::verificar_valor($row["referencia"]));
			$csvdata[$i]["centro_contable"] = utf8_decode(Util::verificar_valor($row->centro->nombre));
			$csvdata[$i]["estado"] = utf8_decode(Util::verificar_valor($row["estado"]["etiqueta"]));
			$csvdata[$i]["monto"] = utf8_decode(Util::verificar_valor($row["monto"]));
			$i++;
		}


		//we create the CSV into memory
		$csv = Writer::createFromFileObject(new SplTempFileObject());
		$csv->insertOne([
			'Numero',
			'Fecha',
			'Proveedor',
			'Referencia',
			'Centro Contable',
			'Estado',
			'Monto'

		]);
		$csv->insertAll($csvdata);
		$csv->output("Orden-". date('ymd') .".csv");
		die;
    }


    function ajax_exportar()
    {
        $id_registros = $this->input->post("id_registros", true);

    	if(!$id_registros){
            return false;
    	}

    	$id_registros = explode("-", $id_registros);

        //EN CASO DE QUE SEAN UUID LOS CAMBIO AL
        //FORMATO QUE ESTA EN LA BASE DE DATOS
        foreach ($id_registros as &$row)
        {
            $row = hex2bin(strtolower($row));
        }

    	$registros  = Ordenes_orm
                    ::whereIn("uuid_orden", $id_registros)
                    ->get();

        $items = array();
        $i = 0;
        foreach($registros as $registro)
        {
            $items[$i]["Fecha"]             = $registro->fecha_creacion;
            $items[$i]["Numero"]            = $this->prefijo.$registro->numero;
            $items[$i]["Proveedor"]         = utf8_decode($registro->proveedor->nombre);
            $items[$i]["Referencia"]        = isset($registro->referencia) ? $registro->referencia : "";
            $items[$i]["Centro Contable"]   = utf8_decode($registro->centro->nombre);
            $items[$i]["Estado"]            = $registro->estado->etiqueta;
            $items[$i]["Monto"]             = "\$".$registro->monto;

            $i += 1;
        }

        if(empty($items)){
            return false;
    	}
        /*
        $objecto        = new stdClass();
        $objecto->count = count($items);
        $objecto->items = $items; */

        $csv = Writer::createFromFileObject(new SplTempFileObject());
		$csv->insertOne([
			'Fecha',
			'Numero',
			'Proveedor',
			'Referencia',
			'C. Contable',
			'Estado',
                        'Monto'
		]);
		$csv->insertAll($items);
		$csv->output("ordenes_compra-". date('ymd') .".csv");
		die;
    }

    /**
     * Cargar Vista Parcial de Tabla
     *
     * @return void
     */
    public function ocultotabla($uuid = NULL, $modulo = "")
    {
        if (!empty($uuid)) {

            // Agregra variables PHP como variables JS
            $this->assets->agregar_var_js(array(
                "uuid_proveedor"    => ($modulo == "proveedores") ? $uuid : "",
                "uuid_pedido"       => ($modulo == "pedidos") ? $uuid : ""
            ));

        }

    	//If ajax request
    	$this->assets->agregar_js(array(
    		'public/assets/js/modules/ordenes/tabla.js'
    	));

    	$this->load->view('tabla');
    }

    public function ocultotablaV2($sp_string_var = '') {

        $this->assets->agregar_js(array(
            'public/assets/js/modules/ordenes/tabla.js'
        ));

        $sp_array_var = explode('=', $sp_string_var);
        if (count($sp_array_var) == 2) {

            $this->assets->agregar_var_js(array(
                $sp_array_var[0] => $sp_array_var[1]
            ));

        }

        $this->load->view('tabla');
    }

    public function ocultotablaProveedores($uuid = NULL, $modulo = "")
    {
        if (!empty($uuid)) {
            $this->assets->agregar_var_js(array(
                "uuid_proveedor"    => $uuid
            ));
        }

    	$this->assets->agregar_js(array(
    		'public/assets/js/modules/ordenes/tabla.js'
    	));

    	$this->load->view('tabla');
    }

    function ocultoformulariocomentarios() {

    	$this->assets->agregar_js(array(
    			'public/assets/js/plugins/ckeditor/ckeditor.js',
    			'public/assets/js/plugins/ckeditor/adapters/jquery.js',
    			'public/assets/js/modules/ordenes/vue.comentario.js',
    			'public/assets/js/modules/ordenes/formulario_comentario.js'
    	));

    	$this->load->view('formulario_comentarios');
    	$this->load->view('comentarios');
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

     	$orden = $this->ordenesCompraRep->agregarComentario($model_id, $comentario);
    	$orden->load('comentario');
        $this->ordenesCompraRep->addHistorial( $orden,  $comentario );
    	$lista_comentario = $orden->comentario()->orderBy('created_at','desc')->get();
    	$this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
    	->set_output(json_encode($lista_comentario->toArray()))->_display();
    	exit;
    }


    public function ocultotablaFacturasCompras($factura_compra_id = NULL)
    {
        if (!empty($factura_compra_id)) {
            $this->assets->agregar_var_js(array(
                "factura_compra_id"    => $factura_compra_id
            ));
        }

    	$this->assets->agregar_js(array(
    		'public/assets/js/modules/ordenes/tabla.js'
    	));

    	$this->load->view('tabla');
    }

    public function ocultocabecera($data = array())
    {
        $this->load->view('cabecera', $data);
    }

    /**
     * Cargar Vista Parcial de Formulario
     *
     * @return void
     */
    public function ocultoformulario($data = array())
    {
        $rolesUsuario = Roles_usuarios_orm::where('usuario_id',"=",$this->id_usuario)->where('empresa_id',"=",$this->id_empresa)->get();
        $this->assets->agregar_js(array(
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/default/vue/components/empezar_desde.js',
            'public/assets/js/modules/ordenes/components/detalle.js',
            'public/assets/js/default/vue/directives/pop_over_precio.js',
            'public/assets/js/default/vue/directives/pop_over_cantidad.js',
            'public/resources/compile/modulos/ordenes/formulario.js'
    	));

        //catalogos
        $clause = ['empresa_id'=>$this->id_empresa,'ordenables'=>true,'transaccionales'=>true,'conItems'=>true, 'estado != por_aprobar'];
        $this->assets->agregar_var_js(array(
            'pedidos' => $this->PedidosRepository->getCollectionPedidos($this->PedidosRepository->get($clause)->filter(function($pedido){
                return $pedido->comprable == true;
            })),
            'proveedores' => $this->ProveedoresRepository->getCollectionProveedores($this->ProveedoresRepository->get($clause)),
            'bodegas' => $this->bodegasRep->getCollectionBodegas($this->bodegasRep->get($clause)),
            'centros_contables' => $this->CentrosContablesRepository->getCollectionCentrosContables($this->CentrosContablesRepository->get($clause)),
            'estados' => $this->OrdenesCompraCatRepository->get(['campo_id'=>7]),
            'terminos_pago' => $this->FacturaVentaCatalogoRepository->getTerminoPago(),
            'categorias' => $this->ItemsCategoriasRepository->getCollectionCategorias($this->ItemsCategoriasRepository->get($clause)),
            'cuentas' => $this->CuentasRepository->get($clause),
            'impuestos' => $this->ImpuestosRepository->get($clause),
            'usuarios' => $this->UsuariosRepository->get($clause)->map(function($usuario){return ['id'=>$usuario->id,'nombre'=>$usuario->nombre_completo];}),
            'usuario_id' => $this->id_usuario,
            //'politica_transaccion' => $this->ordenesCompraRep->gePoliticasTransaccciones($this->_buscar_rol_usuario($rolesUsuario))
            //'politica_transaccion' => []
        ));

        $this->load->view('formulario', $data);
        $this->load->view('vue/components/empezar_desde');
        $this->load->view('components/detalle');
        //$this->load->view('components/correo');
        //$this->load->view('vue/components/articulos');
        //$this->load->view('vue/components/articulo');
    }

    private function _css()
    {
        $this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/plugins/jquery/switchery.min.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
            'public/assets/css/plugins/jquery/jquery.webui-popover.css',
            'public/assets/css/plugins/bootstrap/bootstrap-tagsinput.css',
            'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
            'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
            'public/assets/css/plugins/jquery/fileinput/fileinput.css',
            'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
            'public/assets/css/plugins/bootstrap/select2.min.css',
             'public/assets/css/modules/stylesheets/ordenes_compras.css',
        ));
    }

    private function _js()
    {
        $this->assets->agregar_js(array(
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/plugins/jquery/jqgrid/plugins/jQuery.jqGrid.columnToggle.js',
            'public/assets/js/plugins/jquery/switchery.min.js',
            'public/assets/js/plugins/jquery/jquery.webui-popover.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/combodate/combodate.js',
            'public/assets/js/plugins/jquery/combodate/momentjs.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/plugins/bootstrap/select2/select2.min.js',
            'public/assets/js/plugins/bootstrap/select2/es.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/default/tabla-dinamica.jquery.js',
            'public/assets/js/default/vue/directives/datepicker2.js',
            'public/assets/js/default/vue/directives/inputmask.js',
            'public/assets/js/default/vue/directives/select2.js',
            //'public/assets/js/default/formulario.js'
    	));
    }

    function enviar_correo_proveedor($orden_compra, $correo_proveedor) {

         if (!empty($orden_compra)) {

            $filepath = realpath('./public/templates/email/ordenes/correo_proveedor.html');

             if (!file_exists($filepath)) {
                log_message("error", "MODULO: Login --> No se encontro la plantilla de crear cuenta");
                return false;
            }
             list($file_saved, $html) = $this->imprimir_solo_test($orden_compra->uuid_orden);


             $htmlmail = read_file($filepath);


            $htmlmail = str_replace("__SITE_URL__", base_url('/'), $htmlmail);
            $htmlmail = str_replace("__BODY__", $html, $htmlmail);
            $htmlmail = str_replace("__YEAR__", date('Y'), $htmlmail);

            $this->email->from('no-reply@pensanomica.com', 'Flexio');
            $this->email->to($correo_proveedor);

            $this->email->subject('Orden de Compra No. '.$orden_compra->codigo.'<<'.$orden_compra->empresa->nombre.'>>');
            $this->email->message($htmlmail);
            $this->email->attach($file_saved);
            $resultado_envio = $this->email->send();
            if($resultado_envio == 1){
                $comentario = ['comentario'=>'Se envió exitosamente el correo al proveedor','usuario_id'=>$this->id_usuario];
            }else{
                $comentario = ['comentario'=>'No se envi el correo al proveedor','usuario_id'=>$this->id_usuario];
            }
            $this->ordenesCompraRep->addHistorial( $orden_compra,  $comentario );
            return $resultado_envio;
        } else {
            return false;
        }
    }

     public function guardar()
    {

         $post = $this->input->post();
         if (!empty($post)) {

            Capsule::beginTransaction();
            try {
                $campo = $post['campo'];
                $post['campo']['empresa_id'] = $this->id_empresa;
                $post['campo']['usuario_id'] = $this->id_usuario;
                if(empty($campo['id']))
                {
                    $post['campo']['codigo'] = $this->_generar_codigo();
                    $orden_compra = $this->ordenesCompraRep->create($post);
                } else {

                    $orden_compra = $this->ordenesCompraRep->save($post);


                    if($post['campo']['estado'] ==2 && !empty($post['campo']['correo_proveedor'])) //Enviando Correo al proveedor, esto se tiene que mejorar, no trabajar con los id's
                    {

                       $correo_resultado = $this->enviar_correo_proveedor($this->ordenesCompraRep->find($post['campo']['id']), $post['campo']['correo_proveedor']);
                       $mensaje_correo = ($correo_resultado == 1)?' Enviado el correo al proveedor.':'No se envió el correo.';
                    }
                }
                 //ACTUALIZO EL ESTADO DEL PEDIDO -> pendiente refactory
                $uuid_pedido = count($orden_compra->pedido) ? $orden_compra->pedido->uuid_pedido : '';
                $pedido = Pedidos_orm::findByUuid($uuid_pedido);
                if(count($pedido)){
                     $pedido->comp_actualizarEstado();//ACTUALIZO EL ESTADO DEL PEDIDO
                 }
             } catch (Illuminate\Database\QueryException $e) {
                log_message('error', __METHOD__ . " ->" . ", Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
                Capsule::rollback();
                $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error! Su solicitud no fue procesada</b> ');
                $this->session->set_flashdata('mensaje', $mensaje);
                redirect(base_url('ordenes/listar'));
                //echo $e->getMessage();
            }
            Capsule::commit();

            if (!is_null($orden_compra)) {
                $mensaje = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente ' . $orden_compra->codigo.' '.$mensaje_correo);
            } else {
                $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error! Su solicitud no fue procesada</b> ');
            }
            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('ordenes/listar'));

        }


    }

    private function _generar_codigo()
    {
        $clause_empresa = ['empresa_id' => $this->id_empresa];
        $total = $this->ordenesCompraRep->count($clause_empresa);
        $year = Carbon::now()->format('y');
        $codigo = Util::generar_codigo('OC'.$year,$total + 1);
        return $codigo;
    }

    public function crear($uuid_pedido = NULL)
    {

        if(preg_match('/proveedor/', $uuid_pedido))
        {
            $uuid_proveedor = str_replace('proveedor', '', $uuid_pedido);
            $uuid_pedido    = NULL;
        }

        $data = $mensaje = [];

        $data["message"] = $mensaje;

        $this->_css();
        $this->_js();

    	$breadcrumb = array(
            "titulo" => '<i class="fa fa-shopping-cart"></i> Crear orden de compra'
    	);

        $pedido = $uuid_pedido ? $this->PedidosRepository->findByUuid($uuid_pedido) : [];
        $empezable = collect([
            'id' => count($pedido) ? $pedido->id : '',
            'type' => count($pedido) ? 'pedido' : '',
            'pedidos' => count($pedido) ? [0=>['id'=>$pedido->id,'nombre'=>'PD'.$pedido->numero]] : []
        ]);

        $this->assets->agregar_var_js(array(
            'vista' => 'crear',
            'codigo' => $this->_generar_codigo(),
            'empezable' => $empezable,
            'politica_transaccion' => collect([])
         ));

        $this->template->agregar_titulo_header('Ordenes');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar();
    }

    private function _js_editar(){

        $this->assets->agregar_js(array(
            'public/assets/js/default/vue-validator.min.js',
            'public/assets/js/default/vue-resource.min.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/plugins/jquery/jqgrid/plugins/jQuery.jqGrid.columnToggle.js',
            'public/assets/js/plugins/jquery/switchery.min.js',
            'public/assets/js/plugins/jquery/jquery.webui-popover.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/combodate/combodate.js',
            'public/assets/js/plugins/jquery/combodate/momentjs.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/default/tabla-dinamica.jquery.js',
            'public/assets/js/default/formulario.js',
        ));

    }

    private function _css_editar(){

        $this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/plugins/jquery/switchery.min.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
            'public/assets/css/plugins/jquery/jquery.webui-popover.css',
            'public/assets/css/plugins/bootstrap/bootstrap-tagsinput.css',
            'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
            'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
            'public/assets/css/plugins/jquery/fileinput/fileinput.css'
        ));

    }



    public function _buscar_rol_usuario($roles=array()){

        //$unico = array_unique($roles->toArray());
        $b = array_filter($roles->toArray() ,
                   function($fila)
                   {

                            return($fila['role_id']>3);
                   }
                   );
                  // dd($b['role_id']);
         $retrna = reset($b);
         return $retrna['role_id'];
    }

    public function editar($uuid=NULL){
          $data = $mensaje = [];
      	  $orden = $this->ordenesCompraRep->findByUuid($uuid);
        //dd($orden->id);
           //dd($orden->politica());
          $this->_css();$this->_js();
          $this->assets->agregar_js(array(
                'public/assets/js/modules/ordenes/enviar_correo.js',
                'public/assets/js/modules/ordenes/ver_historial.js'
          ));

          $this->assets->agregar_css(array(
              'public/assets/js/plugins/jquery/sweetalert/sweetalert.css'
          ));

          $rolesUsuario = Roles_usuarios_orm::where('usuario_id',"=",$this->id_usuario)->where('empresa_id',"=",$this->id_empresa)->get();

          $data["message"] = $mensaje;
          $data["id"] = $orden->id;
          $data["ordencompra_id"] = $orden->id;
          $data["uuid_orden"] = $orden->uuid_orden;

          $breadcrumb = array(
              "titulo" => '<i class="fa fa-shopping-cart"></i> Orden de compra '.$orden->numero,
              "historial" => true,


              "ruta" => array(
                0 => array(
                    "nombre" => "Compras",
                    "activo" => false,
                  ),
                 1 => array(
                     "nombre" => "Órdenes de compras",
                     "activo" => false,
                       "url" => 'ordenes/listar'
                  ),
                  2 => array(
                      "nombre" => "<b>Detalle</b>",
                      "activo" => true,

                  )

              ),
          );

          if($orden->imprimible)
          {
              $breadcrumb["menu"]["opciones"]["ordenes/imprimir/".$orden->uuid_orden] = "Imprimir";
          }
          if($orden->id_estado == 2){
               $breadcrumb["menu"]["opciones"]["#EnviarAProveedor"] = "Enviar a proveedor";
          }


          $empezable = collect([
              'type' => count($orden->pedido) ? 'pedido' : '',
              'pedidos' => count($orden->pedido) ? [0=>['id'=>$orden->pedido->id,'nombre'=>'PD'.$orden->pedido->numero]] : [],
              'id' => count($orden->pedido) ? $orden->pedido->id : ''
          ]);
          ///dd($this->ordenesCompraRep->gePoliticasTransaccciones($this->_buscar_rol_usuario($rolesUsuario)));
          $this->assets->agregar_var_js(array(
              'vista' => 'editar',
              'orden' => $this->ordenesCompraRep->getColletionCampos($orden),
              'empezable' => $empezable,
              'politica_transaccion' => $orden->politica()
              //'politica_transaccion' => $this->ordenesCompraRep->gePoliticasTransaccciones($this->_buscar_rol_usuario($rolesUsuario))
          ));


          $this->template->agregar_titulo_header('Ordenes');
    	    $this->template->agregar_breadcrumb($breadcrumb);
    	    $this->template->agregar_contenido($data);
    	    $this->template->visualizar();
    }

    public function imprimir_solo_test($uuid=null)
    {
        if($uuid==null){
            return false;
        }
        $folder_save = $this->config->item('files_pdf');

        $orden_compra = $this->ordenesCompraRep->findByUuid($uuid);
        $orden_compra = $orden_compra->load("empresa");
        $centro_contable = $this->CentrosContablesRepository->findByUuid($orden_compra->uuid_centro);
        $coleccion = $this->ordenesCompraRep->getColletionCampos($orden_compra);

        $dompdf = new Dompdf();
        $data   = ['orden_compra'=>$orden_compra, 'centro_contable'=>$centro_contable, 'coleccion'=>$coleccion];
        $html = $this->load->view('pdf/orden_compra', $data, true);
        $html_correo = $this->load->view('html/orden_compra', $data, true);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $output = $dompdf->output();
        $documento = $orden_compra->numero_documento.' - '.$orden_compra->proveedor->nombre.'.pdf';
        file_put_contents($folder_save.$documento, $output);

        return array($folder_save.$documento, $html_correo);
     }

    public function imprimir($uuid=null)
    {
        if($uuid==null){
            return false;
        }

        $orden_compra = $this->ordenesCompraRep->findByUuid($uuid);
        $orden_compra = $orden_compra->load("empresa");
        //dd($orden_compra->items);
        $centro_contable = $this->CentrosContablesRepository->findByUuid($orden_compra->uuid_centro);
		$coleccion = $this->ordenesCompraRep->getColletionCampos($orden_compra);
        //$count = count($orden_compra->pedido);
        //if($count > 0)
        //dd($orden_compra->pedido->numero);
        //$pedidos = $this->PedidosRepository->findByUuid();
        $dompdf = new Dompdf();
        $data   = ['orden_compra'=>$orden_compra, 'centro_contable'=>$centro_contable, 'coleccion'=>$coleccion];
        $html = $this->load->view('pdf/orden_compra', $data, true);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream($orden_compra->numero_documento.' - '.$orden_compra->proveedor->nombre);

    }

    function documentos_campos(){

    	return array(
    	array(
    		"type"		=> "hidden",
    		"name" 		=> "orden_id",
    		"id" 		=> "orden_id",
    		"class"		=> "form-control",
    		"readonly"	=> "readonly",
    	));
    }

    function ajax_guardar_documentos()
    {
    	if(empty($_POST)){
    		return false;
    	}

    	$orden_id = $this->input->post('orden_id', true);
    	$modeloInstancia = OrdenesModel::find($orden_id);

    	$this->documentos->subir($modeloInstancia);
    }

     function ocultotimeline(){
        $this->load->view('timeline');
  }
  function historial($orden_uuid = NULL){

      $acceso = 1;
      $mensaje =  array();
      $data = array();

      $orden = $this->ordenesCompraRep->findByUuid($orden_uuid);

      if(!$this->auth->has_permission('acceso','ordenes/historial') && is_null($orden)){
        // No, tiene permiso
          $acceso = 0;
          $mensaje = array('estado'=>500, 'mensaje'=>' <b>Usted no cuenta con permiso para esta solicitud</b>','clase'=>'alert-danger');
      }

      $this->_css();
      $this->_js();
      $this->assets->agregar_js(array(
          'public/assets/js/modules/ordenes/vue.componente.timeline.js',
          'public/assets/js/modules/ordenes/vue.timeline.js',

      ));

      $breadcrumb = array(
        "titulo" => '<i class="fa fa-shopping-cart"></i> Bit&aacute;cora: Orden de compra '.$orden->numero,
      );

      $orden->load('historial','comentario');
      //dd($orden->toArray());
        $this->assets->agregar_var_js(array(
        "timeline_orden" => Collect(array_merge(
            $orden->toArray(),
            [
                'historial' => array_merge(
                    $orden->historial->toArray(),
                    $orden->comentario->map(function($comentario) use ($orden){
                        if($orden->historial->where('descripcion',$comentario->comentario)->count())return;
                        return [
                            "id" => "",
                            "uuid_historial" => "",
                            "codigo" => "",
                            "descripcion" => strip_tags($comentario->comentario),
                            "codigo_cuenta" => "",
                            "antes" => "",
                            "despues" => "",
                            "tipo" => "comentario",
                            "created_at" => $comentario->created_at,
                            "updated_at" => $comentario->updated_at,
                            "nombre_usuario" => $comentario->nombre_usuario,
                            "hace_tiempo" => $comentario->cuanto_tiempo,
                            "fecha_creacion" => $comentario->fecha_creacion,
                            "hora" => $comentario->hora,
                            "usuario" => $comentario->usuarios
                        ];
                    })->filter(function($comentario){return count($comentario) ? true : false;})->toArray()
                )
            ]
        )),
      ));

       $this->template->agregar_titulo_header('&Oacute;rdenes de compras');
      $this->template->agregar_breadcrumb($breadcrumb);
      $this->template->agregar_contenido($data);
      $this->template->visualizar();
  }

}

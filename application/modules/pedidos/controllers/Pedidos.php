<?php
/**
 * Pedidos
 *
 * Modulo para administrar la creacion, edicion de pedidos
 *
 * @package    PensaApp
 * @subpackage Controller
 * @category   Controllers
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  10/16/2015
 */

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Pedidos\Repository\PedidoRepository as PedidoRepository;
use Flexio\Modulo\Pedidos\Models\Pedidos as PedidosModel;
use Flexio\Modulo\Bodegas\Repository\BodegasRepository;
use Flexio\Modulo\Usuarios\Repository\UsuariosRepository;
use Flexio\Modulo\CentrosContables\Repository\CentrosContablesRepository;
use Flexio\Modulo\Pedidos\Repository\PedidosCatRepository;
use Flexio\Modulo\Inventarios\Repository\CategoriasRepository as ItemsCategoriasRepository;
use Flexio\Modulo\Contabilidad\Repository\CuentasRepository;
use Flexio\Modulo\Contabilidad\Repository\ImpuestosRepository;
use Flexio\Modulo\Inventarios\Repository\UnidadesRepository;
use Flexio\Modulo\Inventarios\Repository\ItemsCatRepository;
use Flexio\Modulo\OrdenesCompra\Repository\OrdenesCompraRepository;
use Flexio\Library\Util\AuthUser;
use Flexio\Modulo\Contabilidad\Models\CuentasCentro;
use Flexio\Modulo\Busqueda\Repository\BusquedaRepository;
use Flexio\Modulo\Politicas\Repository\PoliticasRepository;
use Flexio\Modulo\Politicas\Models\PoliticasCatalogo;

//utils
use Flexio\Library\Util\FlexioSession;
use Carbon\Carbon;


class Pedidos extends CRM_Controller
{
    protected $id_empresa;
    protected $prefijo;
    protected $id_usuario;

    protected $PedidoRepository;
    protected $BodegasRepository;
    protected $UsuariosRepository;
    protected $CentrosContablesRepository;
    protected $PedidosCatRepository;
    protected $ItemsCategoriasRepository;
    protected $CuentasRepository;
    protected $ImpuestosRepository;
    protected $UnidadesRepository;
    protected $ItemsCatRepository;
    protected $OrdenesCompraRepo;
    protected $BusquedaRepository;
    protected $PoliticasRepository;
    //utils
    protected $FlexioSession;


    public function __construct()
    {
        parent::__construct();
        $this->load->model("Pedidos_orm");
        $this->load->model("Pedidos_items_orm");
        $this->load->model("Pedidos_estados_orm");

        $this->load->module(array('documentos'));
        $this->load->model('facturas_compras/Facturas_compras_orm');

        $this->load->model("contabilidad/Cuentas_orm");

        $this->load->model("inventarios/Items_orm");
        $this->load->model("inventarios/Unidades_orm");
        $this->load->model("colaboradores/Estado_orm");

        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();

        //Esto se debe definir con los muchacos
        $uuid_empresa       = $this->session->userdata('uuid_empresa');
        $this->empresa      = Empresa_orm::findByUuid($uuid_empresa);
        $this->id_usuario   = $this->session->userdata("id_usuario");
        $this->id_empresa   = $this->empresa->id;

        //PREFIJO DE NOMEMCLATURA DE PEDIDO
        $this->prefijo = "PD";

        $this->PedidoRepository = new PedidoRepository;
        $this->BodegasRepository = new BodegasRepository;
        $this->UsuariosRepository = new UsuariosRepository;
        $this->CentrosContablesRepository = new CentrosContablesRepository;
        $this->PedidosCatRepository = new PedidosCatRepository;
        $this->ItemsCategoriasRepository = new ItemsCategoriasRepository;
        $this->CuentasRepository = new CuentasRepository;
        $this->ImpuestosRepository = new ImpuestosRepository;
        $this->UnidadesRepository = new UnidadesRepository;
        $this->ItemsCatRepository = new ItemsCatRepository;
        $this->OrdenesCompraRepo = new OrdenesCompraRepository;
        $this->BusquedaRepository = new BusquedaRepository;
        $this->PoliticasRepository = new PoliticasRepository;
        //utils
        $this->FlexioSession = new FlexioSession;

    }



    public function index()
    {
        redirect("pedidos/listar");
    }


    public function listar()
    {
        //Verificar permisos de acceso a esta vista
    	if(!$this->auth->has_permission('acceso', 'pedidos/listar')){
    		//Redireccionar
    		redirect(base_url('/'));
    	}


    	$data = array();

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
            'public/assets/css/plugins/jquery/toastr.min.css',
            'public/assets/css/plugins/jquery/jquery.fileupload.css',
        ));

        $this->assets->agregar_js(array(
            //'public/assets/js/default/jquery-ui.min.js',
            //'public/assets/js/plugins/jquery/jquery.sticky.js',
            //'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/plugins/jquery/jqgrid/plugins/jQuery.jqGrid.columnToggle.js',
            //'public/assets/js/plugins/jquery/switchery.min.js',
            'public/assets/js/plugins/bootstrap/bootstrap-tagsinput.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            'public/assets/js/plugins/jquery/fileinput/fileinput.js',
            'public/assets/js/plugins/jquery/fileinput/fileinput_locale_es.js',
            'public/assets/js/default/grid.js',
            'public/assets/js/default/subir_documento_modulo.js',
            'public/assets/js/default/lodash.min.js',
            /* Archivos js para la vista de Crear Actividades */
            //'public/assets/js/plugins/ckeditor/ckeditor.js',
            //'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/plugins/bootstrap/bootstrap-tabdrop.js',
            //'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
            'public/assets/js/plugins/toastr.min.js',
            'public/assets/js/default/formulario.js',
            'public/assets/js/plugins/jquery/fileupload/jquery.fileupload.js',

            /* Archivos js del propio modulo*/
            'public/assets/js/modules/pedidos/listar.js',
            'public/assets/js/default/parametros_busqueda.js'
        ));

    	/*
    	 * Verificar si existe alguna variable de session
    	 * proveniente de algun formulario de crear/editar
    	 */
    	if($this->session->userdata('idPedido')){
    		//Borrar la variable de session
    		$this->session->unset_userdata('idPedido');

    		//Establecer el mensaje a mostrar
    		$data["mensaje"]["clase"] = "alert-success";
    		$data["mensaje"]["contenido"] = "Se ha creado el Pedido satisfactoriamente.";
    	}
    	else if($this->session->userdata('updatedPedido')){
    		//Borrar la variable de session
    		$this->session->unset_userdata('updatedPedido');

    		//Establecer el mensaje a mostrar
    		$data["mensaje"]["clase"] = "alert-success";
    		$data["mensaje"]["contenido"] = "Se ha actualizado el Pedido satisfactoriamente.";
    	}


        $clause = array();

        $sidx       = 'id';
        $sord       = 'desc';
        $limit      = '1000';

        $pedidos    = new Pedidos_orm;
        $pedidos    = $pedidos->where("id_empresa", "=", $this->id_empresa);
        $pedidos    = $pedidos->orderBy($sidx, $sord)
                    ->take($limit)
                    ->get();

        //Verificar si hay datos.
        if($pedidos->count() > 0)
        {
            $i=0;
            foreach($pedidos as $row)
            {
                $hidden_options = "";

                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-nombre="'.$row->numero.'" data-pedido="'. $row->uuid_pedido .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

                //IMPORTANTE A MODO DE DESARROLLO ANADI LA CONDICION OR 1
                //PARA QUE TODAS LAS CONDICIONES DIERAN TRUE

                if($this->auth->has_permission('acceso', 'pedidos/ver/(:any)')){
                    $hidden_options .= '<a href="'. base_url('pedidos/ver/'. $row->uuid_pedido) .'" class="btn btn-block btn-outline btn-success">Ver Pedido</a>';
                }

                //Esta boton depende de los estados
                //1.- Pendiente
                //2.- Abierto
                //3.- Parcial
                //4.- En orden
                //5.- Completado
                //6.- Anulado
                if(($row->id_estado > 1 and $row->id_estado < 4) and $this->auth->has_permission('acceso', 'ordenes_compra/crear-orden-compra/(:any)')){
                    $hidden_options .= '<a href="'. base_url('ordenes_compra/crear-orden-compra/pedido='. $row->uuid_pedido) .'" class="btn btn-block btn-outline btn-success">Convertir a Orden de Compra</a>';
                }

                //Esta boton depende de los estados
                //1.- Pendiente
                //2.- Abierto
                //3.- Parcial
                //4.- En orden
                //5.- Completado
                //6.- Anulado
                if(($row->id_estado > 1 and $row->id_estado < 4) and $this->auth->has_permission('acceso', 'traslados/crear-traslado/(:any)')){
                    $hidden_options .= '<a href="'. base_url('traslados/crear-traslado/pedido='. $row->uuid_pedido) .'" class="btn btn-block btn-outline btn-success">Convertir a Traslado</a>';
                }

                //Esta boton depende de los estados
                //1.- Pendiente
                //2.- Abierto
                //3.- Parcial
                //4.- En orden
                //5.- Completado
                //6.- Anulado
                if(1 OR ($row->id_estado < 4) and $this->auth->has_permission('acceso', 'pedidos/ajax-anular')){
                    $hidden_options .= '<a href="#" data-uuid="'.$row->uuid_pedido.'" class="btn btn-block btn-outline btn-success anular">Anular Pedido</a>';
                }
                elseif(1 OR ($row->id_estado == "6") and $this->auth->has_permission('acceso', 'pedidos/ajax-reabrir'))
                {
                    $hidden_options .= '<a href="#" data-uuid="'.$row->uuid_pedido.'" class="btn btn-block btn-outline btn-success reabrir">Reabrir Pedido</a>';
                }


                $camposGrid[$i]["uuid"]                 = isset($row->uuid_pedido) ? $row->uuid_pedido : NULL;

                $camposGrid[$i]["titulo"]["name"]       = "N&uacute;mero";
                $camposGrid[$i]["titulo"]["value"]      = $this->prefijo.$row->numero;

                $camposGrid[$i]["subtitulo"]["name"]    = "Fecha";
                $camposGrid[$i]["subtitulo"]["value"]   = $row->fecha_creacion;

                $camposGrid[$i]["info"][0]["name"]      = "Referencia";
                $camposGrid[$i]["info"][0]["value"]     = isset($row->referencia) ? $row->referencia : "";

                $camposGrid[$i]["info"][1]["name"]      = "Centro Contable";
                $camposGrid[$i]["info"][1]["value"]     = $row->centro;

                $camposGrid[$i]["info"][2]["name"]      = "Estado";
                $camposGrid[$i]["info"][2]["value"]     = "Revisar";//isset($row->estado->etiqueta);

                $camposGrid[$i]["id"]                   = $row->id;
                $camposGrid[$i]["opcion"]               =  $hidden_options;

                $i++;
            }

            $data["estados"]    = Pedidos_estados_orm
                                ::where("id_campo", "=", "7")
                                ->orderBy("id_cat", "ASC")
                                ->get();
            $clause = ['empresa_id' => $this->id_empresa, 'transaccionales' => true];
            $data["centros"] = $this->CentrosContablesRepository->getCollectionCentrosContables($this->CentrosContablesRepository->get($clause));
           /* $data["centros"]    = Centros_orm::deEmpresa($this->id_empresa)
                                ->activa()
                                ->deMasJuventud($this->id_empresa)
                                ->orderBy("nombre", "ASC")
                                ->get();*/

            $data           = array_merge($data,$camposGrid);
        }


    	//Breadcrum Array
        $breadcrumb = array(
            "titulo"    => '<i class="fa fa-shopping-cart"></i> Pedidos',
            "ruta" => array(
                0 => array(
                    "nombre" => "Compras",
                    "activo" => false
                ),
                1 => array(
                    "nombre" => '<b>Pedidos</b>',
                    "activo" => true
                )
            ),
            "filtro"    => false,
            "menu"      => array()
        );

        //Verificar si tiene permiso a la seccion de Crear
        if ($this->auth->has_permission('acceso', 'pedidos/crear')){
            $breadcrumb["menu"]["nombre"] = "Crear";
            $breadcrumb["menu"]["url"] = "pedidos/crear/";
        }

        $breadcrumb["menu"]["opciones"]["#convertirOrdenBtn"] = "Convertir a orden de compra";

        //Verificar si tiene permiso de Exportar
        if ($this->auth->has_permission('listar__exportarPedidos', 'pedidos/listar')){
            $breadcrumb["menu"]["opciones"]["#exportarBtn"] = "Exportar";
            $breadcrumb["menu"]["opciones"]["#cambiarEstado"] = "Cambiar de estado";
        }

       $data["modulo"] = $this->FlexioSession->uri()->segment(1);
       $data['menu_busqueda'] = $this->BusquedaRepository->get([
         'busquedable_type' =>  $this->FlexioSession->uri()->segment(1)
      ]);
      //politicas para cambio de estado en modal
      $politicas = PoliticasCatalogo::where('tipo', 'pedido')->pluck('estado2')->toArray();      
      $estados = Pedidos_estados_orm::whereIn('id_cat', $politicas)->orderBy("id_cat", "ASC")->get();
      $labelEstado = $estados[0]->labelEstado;      
      $this->assets->agregar_var_js(array(
        "estados" => $estados,
        "label_estados" => json_encode($labelEstado),
      ));
    	$this->template->agregar_titulo_header('Listado de Pedidos');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar($breadcrumb);

    }

    public function ajax_get_cuentas_contables(){
               if(!$this->input->is_ajax_request()){
                  return false;
              }
              $centro = $this->CentrosContablesRepository->findByUuid($this->input->post('uuid_centro'));
              $response = CuentasCentro::where("centro_id",$centro->id)->where('empresa_id',$this->id_empresa)->get()->load('cuentas_info');
              $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response))->_display();
              exit();
      }

    public function ajax_obtener_item()
    {
    	//Just Allow ajax request
    	if($this->input->is_ajax_request())
        {
            $this->load->model("inventarios/Items_orm");
            $uuid = $this->input->post("uuid", true);

            if(empty($uuid))return false;

            $item       = Items_orm::findByUuid($uuid);
            $registro   = array();

            $registro   = array(
                "descripcion"   => $item->descripcion,
                "uuid_gasto"    => strtoupper(bin2hex($item->uuid_gasto))
            );

            foreach ($item->item_unidades as $item_unidad)
            {
                $registro["unidades"][] = array(
                    "id"                => $item_unidad->unidad->id,
                    "uuid_unidad"       => $item_unidad->unidad->uuid_unidad,
                    "nombre"            => $item_unidad->unidad->nombre,
                    "base"              => $item_unidad->base,
                    "factor_conversion" => $item_unidad->factor_conversion
                );
            }

            $response               = array();
            $response["success"]    = true;
            $response["registro"]   = $registro;

            echo json_encode($response);
            exit();
        }

    }

    public function ajax_obtener_pedido_item()
    {
    	//Just Allow ajax request
    	if($this->input->is_ajax_request())
        {
            $this->load->model("pedidos/Pedidos_items_orm");

            $id_pedido_item = $this->input->post("id_pedido_item", true);
            $registro       = Pedidos_items_orm::find($id_pedido_item)->toArray();


            $response               = array();
            $response["success"]    = false;
            $response["registro"]   = $registro;

            if(!empty($response["registro"]))
            {
                $response["success"]    = true;
            }

            echo json_encode($response);
            exit();
        }

    }


    public function ajax_validar_pedido() {
      // Just Allow ajax request
      if (! $this->input->is_ajax_request ()) {
       return false;
      }

      Capsule::beginTransaction();
      try {
       $pedido_id = $this->input->post ('pedido_id', true);

       $pedido = $this->PedidoRepository->find($pedido_id);
       $pedido->validado = 'si';
       $pedido->save();

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
            $registros = new Pedidos_orm;
            $registros = $registros->where("id_empresa", "=", $this->id_empresa);

            $registros_count = new Pedidos_orm;
            $registros_count = $registros_count->where("id_empresa", "=", $this->id_empresa);

            /**
             * Verificar si existe algun $_POST
             * de los campos de busqueda
            */
            $fecha1     = $this->input->post('fecha1', true);
            $fecha2     = $this->input->post('fecha2', true);
            $centro     = $this->input->post('centro', true);
            $estado     = $this->input->post('estado', true);
            $estado     = !empty($estado) ? (is_array($estado) ? $estado : array($estado)) : array();
            $referencia = $this->input->post('referencia', true);
            $numero     = $this->input->post('numero', true);

            //subpanels
            $orden_compra_id    = $this->input->post('orden_compra_id', true);
            $factura_compra_id  = $this->input->post('factura_compra_id', true);
            $item_id    = $this->input->post('item_id', true);

            //filtros de centros contables del usuario
            $centros = $this->FlexioSession->usuarioCentrosContablesHex();
            if(!in_array('todos', $centros))
            {
                $registros = $registros->whereIn("ped_pedidos.uuid_centro", array_map(function($centro1){return hex2bin($centro1);}, $centros));
                $registros = $registros_count->whereIn("ped_pedidos.uuid_centro", array_map(function($centro1){return hex2bin($centro1);}, $centros));
            }

            if(!empty($orden_compra_id)){
                $registros          = $registros->deOrdenDeCompra($orden_compra_id);
                $registros_count    = $registros_count->deOrdenDeCompra($orden_compra_id);
            }

            if(!empty($factura_compra_id)){
                $registros          = $registros->deFacturaDeCompra($factura_compra_id);
                $registros_count    = $registros_count->deFacturaDeCompra($factura_compra_id);
            }
            if(!empty($item_id)){
                $registros          = $registros->deItem($item_id);
                $registros_count    = $registros_count->deItem($item_id);
            }

            if(!empty($fecha1)||!empty($fecha2)){
                $fechas_array       = array($fecha1,$fecha2);

                $registros          = $registros
                                    ->where("ped_pedidos.fecha_creacion", ">=", date('Y-m-d', strtotime($fechas_array[0])))
                                    ->where("ped_pedidos.fecha_creacion", "<=", date('Y-m-d', strtotime($fechas_array[1])));

                $registros_count    = $registros_count
                                    ->where("ped_pedidos.fecha_creacion", ">=", date('Y-m-d', strtotime($fechas_array[0])))
                                    ->where("ped_pedidos.fecha_creacion", "<=", date('Y-m-d', strtotime($fechas_array[1])));
            }
            if(!empty($centro)){
                $registros          = $registros->where("ped_pedidos.uuid_centro", "=", hex2bin(strtolower($centro)));
                $registros_count    = $registros_count->where("ped_pedidos.uuid_centro", "=", hex2bin(strtolower($centro)));
            }
            if(!empty(array_filter($estado))){
                $registros          = $registros->whereIn("ped_pedidos.id_estado", array_filter(array_unique($estado)));
                $registros_count    = $registros_count->whereIn("ped_pedidos.id_estado", array_filter(array_unique($estado)));
            }
            if(!empty($referencia)){
                $registros          = $registros->where("ped_pedidos.referencia", "like", "%$referencia%");
                $registros_count    = $registros_count->where("ped_pedidos.referencia", "like", "%$referencia%");
            }
            if(!empty($numero)){
                $numero             = str_replace($this->prefijo,"",$numero);

                $registros          = $registros->where("ped_pedidos.numero", "like", "%$numero%");
                $registros_count    = $registros_count->where("ped_pedidos.numero", "like", "%$numero%");
            }
            //filtro de pedidos por la categoria del usuario
            $categorias_items = AuthUser::usuarioCategoriaItems();
            if(!in_array('todos', $categorias_items)){
                  $registros=$registros->deCategoria($categorias_items);
                  $registros_count=$registros_count->deCategoria($categorias_items);
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
               // dd($registros->toArray());
                foreach ($registros AS $i => $row)
                {
                    $hidden_options = "";
                    $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-nombre="'.$row->numero.'" data-pedido="'. $row->uuid_pedido .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

                    //IMPORTANTE A MODO DE DESARROLLO ANADI LA CONDICION OR 1
                    //PARA QUE TODAS LAS CONDICIONES DIERAN TRUE
                    $var = substr($row->numero,0,2);
                    if($var == "00"){
                        $enlace = $this->prefijo.$row->numero;
                    }else{
                        $enlace = $row->numero;
                    }
                   // dd($enlace);
                    if($this->auth->has_permission('acceso', 'pedidos/ver/(:any)')){
                        $hidden_options .= '<a href="'. base_url('pedidos/ver/'. $row->uuid_pedido) .'" class="btn btn-block btn-outline btn-success">Ver pedido</a>';

                        $enlace = '<a href="'. base_url('pedidos/ver/'. $row->uuid_pedido) .'" style="color:blue;">'.$enlace.'</a>';
                    }

                    //Pedido en cotizacion => estado_id:2 || Pedido parcial => estado_id:3
                    if($row->comprable and $this->auth->has_permission('acceso', 'ordenes/crear/(:any)')){
                        $hidden_options .= '<a href="'. base_url('ordenes/crear/'. $row->uuid_pedido) .'" class="btn btn-block btn-outline btn-success">Convertir a &oacute;rden de compra</a>';
                    }

                    if($row->comprable and $this->auth->has_permission('acceso', 'traslados/crear')){
                        $hidden_options .= '<a href="'. base_url('traslados/crear/?pedido_id='. $row->id) .'" class="btn btn-block btn-outline btn-success">Convertir a traslado</a>';
                    }

                    if(($row->id_estado < 3) and $this->auth->has_permission('acceso', 'pedidos/ajax-anular')){
                        $hidden_options .= '<a href="#" data-uuid="'.$row->uuid_pedido.'" class="btn btn-block btn-outline btn-success anular">Anular pedido</a>';
                    }

                    if(1 or $this->auth->has_permission('acceso', 'pedidos/historial/(:any)')){
                        $hidden_options .= '<a href="'. base_url('pedidos/historial/'. $row->uuid_pedido) .'" data-id="'.$row->id.'" class="btn btn-block btn-outline btn-success ">ver bit&aacute;cora</a>';
                    }

                    if($this->auth->has_permission('acceso', 'pedidos/ver/(:any)')){
                        $hidden_options .= '<a href="#" data-id="'.$row->id.'" class="btn btn-block btn-outline btn-success subirDocumento">Subir documento</a>';
                    }

                    if($row->estado->id_cat == 4){
                        $enlace_estado = '<span class="label label-information cambiarEstado" data-id="'.$row->id.'" style="background-color:#5cb85c; color:#ffffff;">'. $row->estado->etiqueta .'</span>';
                    }
                    elseif($row->estado->id_cat == 5){
                        $enlace_estado = '<span class="label label-information cambiarEstado" data-id="'.$row->id.'" style="background-color:red; color:#ffffff;">'. $row->estado->etiqueta .'</span>';
                    }
                    else{
                        $enlace_estado = '<span class="label label-information cambiarEstado" data-id="'.$row->id.'" style="background-color:#1C84C6; color:#ffffff;">'. $row->estado->etiqueta .'</span>';
                    }
                    $validado = $row->validado == 'si'?'  <span class="btn btn-info btn-circle"><i class="fa fa-check"></i></span>':'';

                    if( $row->validado == 'no' )
                        $hidden_options .= '<a href="#" data-id="' . $row->id . '" data-pedido-id="' . $row->id . '"  class="btn btn-block btn-outline btn-success validarPedido" >Validar</a>';                        
                    if($this->auth->has_permission('acceso', 'pedidos/ver/(:any)')){
                        $hidden_options .= '<a href="#" data-id="' . $row->id . '" data-pedido-id="' . $row->id . '"  class="btn btn-block btn-outline btn-success cambiarEstado" >Cambiar estado</a>';    
                    }
                    //Si no tiene acceso a ninguna opcion
                    //ocultarle el boton de opciones
                    if($hidden_options == ""){
                            $link_option = "&nbsp;";
                    }

                    $response->rows[$i]["id"]   = $row->uuid_pedido;
                    $response->rows[$i]["cell"] = array(
                        $row->fecha_creacion,
                        $enlace.$validado,
                        isset($row->referencia) ? $row->referencia : "",
                        count($row->centro) ? $row->centro->nombre : '',
                        $enlace_estado,
                        $link_option,
                        $hidden_options,
                        count($row->centro) ? $row->centro->id : '',
                        count($row->bodega) ? $row->bodega->id : ''
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

            //DEFINO EL ESTADO COMO ANULADO = 5
            $registro->id_estado = "5";
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

    function ajax_eliminar_pedido_item()
    {
    	//Just Allow ajax request
    	if(!$this->input->is_ajax_request()){
    		return false;
    	}

        $this->load->model("pedidos/Pedidos_items_orm");

        $id_registro    = $this->input->post("id_registro", true);
    	$registro       = Pedidos_items_orm::find($id_registro);

    	$response   = array(
            "respuesta" => $registro->delete(),
            "mensaje"   => "Se ha eliminado el registro satisfactoriamente"
        );


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

    	$registros  = Pedidos_orm
                    ::whereIn("uuid_pedido", $id_registros)
                    ->get();

        $items = array();
        $i = 0;
        foreach($registros as $registro)
        {
            $items[$i]["Fecha"]             = $registro->fecha_creacion;
            $items[$i]["Numero"]            = $this->prefijo.$registro->numero;
            $items[$i]["Referencia"]        = isset($registro->referencia) ? $registro->referencia : "";
            $items[$i]["Centro Contable"]   = $registro->centro->nombre;
            $items[$i]["Estado"]            = $registro->estado->etiqueta;

            $i += 1;
        }

        if(empty($items)){
            return false;
    	}

        $objecto        = new stdClass();
        $objecto->count = count($items);
        $objecto->items = $items;

    	echo json_encode($objecto);
        exit();
    }

    /**
     * Cargar Vista Parcial de Tabla
     *
     * @return void
     */
    public function ocultotabla()
    {
    	//If ajax request
    	$this->assets->agregar_js(array(
    		'public/assets/js/modules/pedidos/tabla.js'
    	));

    	$this->load->view('tabla');
    }

    public function ocultotablaOrdenesCompras($orden_compra_id=null){
        $this->assets->agregar_js(array(
            'public/assets/js/modules/pedidos/tabla.js'
        ));

        if (!empty($orden_compra_id)) {
            $this->assets->agregar_var_js(array(
                "orden_compra_id" => $orden_compra_id
            ));
        }

        $this->load->view('tabla');
    }

    public function ocultotablaFacturasCompras($factura_compra_id=null){
        $this->assets->agregar_js(array(
            'public/assets/js/modules/pedidos/tabla.js'
        ));

        if (!empty($factura_compra_id)) {
            $this->assets->agregar_var_js(array(
                "factura_compra_id" => $factura_compra_id
            ));
        }

        $this->load->view('tabla');
    }


    /**
     * Cargar Vista Parcial de Formulario
     *
     * @return void
     */
    public function ocultoformulario($data = array()){

        $this->assets->agregar_js(array(
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/modules/pedidos/components/detalle.js',
            //'public/assets/js/default/vue/components/articulos.js',
            //'public/assets/js/default/vue/components/articulo.js',
            'public/assets/js/default/vue/directives/pop_over_precio.js',
            'public/assets/js/default/vue/directives/pop_over_cantidad.js',
            'public/resources/compile/modulos/pedidos/formulario.js'
        ));

        if(!empty($data)){
                   $pedido = $this->PedidoRepository->find($data->id);
                   $centro = $this->CentrosContablesRepository->findByUuid($pedido->uuid_centro);
        }

        $clause = ['empresa_id'=>$this->id_empresa,'ordenables'=>true,'transaccionales'=>true,'conItems'=>true, 'estado != por_aprobar']; // Requerimiento: listar todas las cuentas
        $usuario_id=$this->id_usuario;
        $compradores = collect([]);
        if ($data == null || empty($data)) {
            $compradores = Usuario_orm::findById($this->FlexioSession->usuarioId())->get();
        } else {
            $compradores = Usuario_orm::where('id', $data->creado_por)->get();
            $usuario_id=$data->creado_por;
        }
        $this->assets->agregar_var_js(array(
            'bodegas' => $this->BodegasRepository->getCollectionBodegas($this->BodegasRepository->get($clause)),
            'usuario_id' => $usuario_id,
            'compradores' => $this->UsuariosRepository->getCollectionUsuarios($this->UsuariosRepository->get($clause)),
            'centros_contables' => $this->CentrosContablesRepository->getCollectionCentrosContables($this->CentrosContablesRepository->get($clause)),
            'estados' => $this->PedidosCatRepository->get(['campo_id'=>'7']),
            'tipos_item' => $this->ItemsCatRepository->get(['valor'=>'tipo']),
            'unidades' => $this->UnidadesRepository->get($clause),
            'categorias' => $this->ItemsCategoriasRepository->getCollectionCategorias($this->ItemsCategoriasRepository->get($clause)),
            'cuentas' => Collect([]),
            'impuestos' => $this->ImpuestosRepository->get($clause)
        ));

        $this->load->view('formulario');
        $this->load->view('components/detalle');
    }

    public function guardar() {

        $post = $this->input->post();

        if (!empty($post)){

            Capsule::beginTransaction();

            try {
                if (empty($post['campo']['id'])) {
                    $total = $this->PedidoRepository->count(['empresa_id'=>$this->id_empresa]);
                    $year = Carbon::now()->format('y');
                    $codigo = Util::generar_codigo('PD' . $year, $total + 1);
                    $post['campo']['numero'] = $codigo;
                }

                $post['campo']['id_empresa'] = $this->id_empresa;
                if (empty($post['campo']['id'])) {
                    $pedido = $this->PedidoRepository->create($post);
                } else {
                    $pedido = $this->PedidoRepository->update($post);
                }
            } catch (Illuminate\Database\QueryException $e) {
                log_message('error', __METHOD__ . " ->" . ", Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
                Capsule::rollback();
                $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error! Su solicitud no fue procesada</b> ');
                $this->session->set_flashdata('mensaje', $mensaje);
                redirect(base_url('pedidos/listar'));
            }

            if (!is_null($pedido)) {
                Capsule::commit();
                $mensaje = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente ' . $pedido->codigo);
            } else {
                $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error! Su solicitud no fue procesada</b> ');
            }

            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('pedidos/listar'));
        }
    }

    private function _css(){

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
            //refactory to vue.js
            'public/assets/css/plugins/jquery/jquery.webui-popover.css',
            'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
            'public/assets/css/plugins/bootstrap/select2.min.css',
        ));

    }

    private function _js(){

        $this->assets->agregar_js(array(
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/combodate/combodate.js',
            'public/assets/js/plugins/jquery/combodate/momentjs.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/default/tabla-dinamica.jquery.js',
            'public/assets/js/default/formulario.js',
            //listar y subpanels (ver/editar)
            // 'public/assets/js/plugins/jquery/switchery.min.js',
            // 'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            // 'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            // 'public/assets/js/plugins/jquery/jqgrid/plugins/jQuery.jqGrid.columnToggle.js',
            //refactory to vue.js
            // 'public/assets/js/default/jquery-ui.min.js',
            // 'public/assets/js/plugins/jquery/jquery.webui-popover.js',
            'public/assets/js/plugins/bootstrap/select2/select2.min.js',
            'public/assets/js/plugins/bootstrap/select2/es.js',
            'public/assets/js/plugins/jquery/combodate/momentjs.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/default/vue/directives/datepicker2.js',
            'public/assets/js/default/vue/directives/inputmask.js',
            'public/assets/js/default/vue/directives/select2.js',

    	));

    }



    function ajax_guardar_comentario(){

    	if(!$this->input->is_ajax_request()){
    		return false;
    	}
    	$PedidoRepository = new PedidoRepository;
    	$model_id   = $this->input->post('modelId');
    	$comentario = $this->input->post('comentario');
    	$uuid_usuario = $this->session->userdata('huuid_usuario');
    	$usuario = Usuario_orm::findByUuid($uuid_usuario);
    	$comentario = ['comentario'=>$comentario,'usuario_id'=>$usuario->id];

     	$pedido 	  = $PedidoRepository->agregarComentario($model_id, $comentario);

    	$pedido->load('comentario');
     	$lista_comentario = $pedido->comentario()->orderBy('created_at','desc')->get();
    	$this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
    	->set_output(json_encode($lista_comentario->toArray()))->_display();
    	exit;
    }

    public function crear() {

        $acceso = 1;
        $data = $mensaje = [];

        if (!$this->auth->has_permission('acceso')) {
            $acceso = 0;
            $mensaje = array('estado' => 500, 'mensaje' => ' <b>Usted no cuenta con permiso para esta solicitud</b>', 'clase' => 'alert-danger');
        }

        $this->_css();
        $this->_js();

        $this->assets->agregar_var_js(array(
            "vista" => 'crear',
            "acceso" => $acceso,
            'politica_transaccion' => collect([])
        ));

        $breadcrumb = array(
                "titulo" => '<i class="fa fa-shopping-cart"></i> Crear pedido',
      	         "ruta" => array(
                        0 => array(
                          "nombre" => "Compras",
                          "activo" => false,
                        ),
                         1 => array(
                           "nombre" => "Pedidos",
                             "activo" => false,
                               "url" => 'pedidos/listar'
                          ),
                          2 => array(
                              "nombre" => "<b>Crear</b>",
                              "activo" => true,
                           )
                 ),
            );

        $data['mensaje'] = $mensaje;
        $this->template->agregar_titulo_header('Pedidos');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar();

    }

    public function editar($uuid=NULL){

        $acceso = 1;
        $data = $mensaje = [];

        if (!$this->auth->has_permission('acceso')) {
            $acceso = 0;
            $mensaje = array('estado' => 500, 'mensaje' => ' <b>Usted no cuenta con permiso para esta solicitud</b>', 'clase' => 'alert-danger');
        }

        $this->_css();
        $this->_js();

        $pedido = $this->PedidoRepository->findByUuid($uuid);

        $ordenes = $this->OrdenesCompraRepo->getOrdenesPedidoWithItems($uuid);

        $this->assets->agregar_var_js(array(
            "vista" => 'editar',
            "acceso" => $acceso,
            "pedido" => $this->PedidoRepository->getCollectionPedido($pedido),
            'ordenesPedido' => collect($ordenes->toarray()),
            'politica_transaccion' => $pedido->politica()
        ));

        //Introducir mensaje de error al arreglo
    	//para mostrarlo en caso de haber error
    	$data["message"] = $mensaje;


      $breadcrumb = array(
          "titulo" => '<i class="fa fa-shopping-cart"></i> Pedido '.$pedido->numero,
          "ruta" => array(
            0 => array(
                "nombre" => "Compras",
                "activo" => false,
              ),
             1 => array(
                 "nombre" => "Pedidos",
                 "activo" => false,
                   "url" => 'pedidos/listar'
              ),
              2 => array(
                  "nombre" => "<b>Detalle</b>",
                  "activo" => true,

              )

          ),
      );

        $breadcrumb["menu"]["opciones"]["pedidos/historial/" . $pedido->uuid_pedido] = "Ver bit&aacute;cora";
        //Importante -> para subpanel -> cambiar por id...
        $data["pedido_id"] = $pedido->id;
        $data["pedido_obj"] = $pedido;
        $this->template->agregar_titulo_header('Pedidos');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar();

    }

    function documentos_campos(){

    	return array(
    	array(
    		"type"		=> "hidden",
    		"name" 		=> "pedido_id",
    		"id" 		=> "pedido_id",
    		"class"		=> "form-control",
    		"readonly"	=> "readonly",
    	));
    }

    function ajax_guardar_documentos()
    {
    	if(empty($_POST)){
    		return false;
    	}

    	$pedido_id = $this->input->post('pedido_id', true);
    	$modeloInstancia = PedidosModel::find($pedido_id);

    	$this->documentos->subir($modeloInstancia);
    }

   public function historial($uuid = null){
       $acceso = 1;
       $mensaje =  array();
       $data = array();

       $pedido = $this->PedidoRepository->findByUuid($uuid);

       if(!$this->auth->has_permission('acceso','pedidos/historial/(:any)') && is_null($pedido)){
          $acceso = 0;
          $mensaje = array('estado'=>500, 'mensaje'=>' <b>Usted no cuenta con permiso para esta solicitud</b>','clase'=>'alert-danger');
      }

      $this->_css();
      $this->_js();
      $this->assets->agregar_js(array(
           'public/resources/compile/modulos/pedidos/historial.js'
      ));

      $pedido->load('historial');

      $historial =  $pedido->historial->map(function($historial)use($pedido){
           return [
               'titulo' => $historial->titulo,
               'descripcion' => $historial->descripcion,
               'antes' => $historial->antes,
               'codigo' => $pedido->numero,
               'despues' => $historial->despues,
               'tipo' => $historial->tipo,
               'nombre_usuario' => $historial->nombre_usuario,
               'hora' => $historial->hora,
               'fecha_creacion' => $historial->fecha_creacion
           ];
      });
      $this->assets->agregar_var_js(array(
          'historial' => $historial
      ));
      $breadcrumb = array(
        "titulo" => '<i class="fa fa-shopping-cart"></i> Bit&aacute;cora: Pedido '.$pedido->numero,
      );

      $this->template->agregar_titulo_header('&Oacute;rdenes de compras');
      $this->template->agregar_breadcrumb($breadcrumb);
      $this->template->agregar_contenido($data);
      $this->template->visualizar();

    }
    function ajax_cambiar_estado() {
        if(empty($_POST)){
            return false;
        }
          
        $id = $this->input->post('pedido_id', true);        
        $estado_id = $this->input->post('estado_id', true);  
        $empresa_id = $this->id_empresa;
        $id = explode(',', $id);
        if (count($id) > 1){
        foreach ($id as &$row)
        {
            $row = hex2bin(strtolower($row));
        }             
        $id  = Pedidos_orm::whereIn("uuid_pedido", $id)->get()->pluck('id')->toArray(); 
        foreach($id as $id_pedido){
	                try {
	                    $pedido = $this->PedidoRepository->guardarEstado($id_pedido, $estado_id, $empresa_id);
	                } catch (\Exception $e) {
	            echo json_encode(array(
                'response' => false,
                'mensaje' => $e->getMessage(),
            ));
            exit;
	                }   
        }                
        }else{
        $id = $id[0];    
        
        try { 
        $pedido = $this->PedidoRepository->guardarEstado($id, $estado_id, $empresa_id);
        } catch (\Exception $e) {
            echo json_encode(array(
                'response' => false,
                'mensaje' => $e->getMessage(),
            ));
            exit;
        }
        }
        echo json_encode(array(
            'response' => true,
            'mensaje' => 'Se actualiz&oacute; el estado correctamente.',
        ));
        exit;       

    }

    public function ajax_convetir_a_orden() {

      if(!$this->input->is_ajax_request()){
        return false;
      }

      $pedidos = $this->input->post("pedido_id", true);

      //verificar si existe $pedido_id
      if(empty($pedidos)){
        return false;
      }

      $pedido = $this->PedidoRepository->convetir_a_orden($pedidos);
    }

}

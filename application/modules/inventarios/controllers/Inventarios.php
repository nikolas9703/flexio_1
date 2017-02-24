<?php
/**
 * Inventarios
 *
 * Modulo para administrar la creacion, edicion de inventarios
 *
 * @package    PensaApp
 * @subpackage Controller
 * @category   Controllers
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  10/16/2015
 */

use Illuminate\Database\Capsule\Manager as Capsule;

//repositorios
use Flexio\Modulo\Inventarios\Repository\ItemsRepository as itemsRep;
use Flexio\Modulo\Inventarios\Repository\SerialesRepository as seriesRep;
use Flexio\Modulo\Inventarios\Repository\CategoriasRepository as ItemsCategoriasRepository;
use Flexio\Modulo\Inventarios\Repository\ItemsCatRepository;
use Flexio\Modulo\Inventarios\Repository\UnidadesRepository as ItemsUnidadesRepository;
use Flexio\Modulo\Inventarios\Repository\PreciosRepository as ItemsPreciosRepository;
use Flexio\Modulo\Contabilidad\Repository\CuentasRepository;
use Flexio\Modulo\Contabilidad\Repository\ImpuestosRepository;


//models
use Flexio\Modulo\Atributos\Models\Atributos;

//utilities
use Carbon\Carbon as Carbon;
use Flexio\Library\HTML\HtmlRender;

class Inventarios extends CRM_Controller
{
    protected $empresa;
    protected $id_empresa;
    protected $prefijo;
    protected $id_usuario;
    protected $DocumentosRepository;
    protected $upload_folder = './public/uploads/';

    //repositorios
    private $itemsRep;
    private $seriesRep;
    protected $ItemsCategoriasRepository;
    protected $ItemsCatRepository;
    protected $ItemsUnidadesRepository;
    protected $ItemsPreciosRepository;
    protected $CuentasRepository;
    protected $ImpuestosRepository;
    protected $HtmlRender;

    protected $StateColorsAjustes = ['3' => '#F0AD4E', '4' => '#5CB85C', '5' => '#D9534F'];//por aprobar / aprobado / rechazado //

    public function __construct()
    {
        parent::__construct();

        //seteando hora en espanol
        Carbon::setLocale("es");
        setlocale(LC_ALL, 'es_ES.utf8');//pendiente instalar formato en servidor de desarrollos

        $this->load->model("usuarios/Empresa_orm");
        $this->load->model("inventarios/Items_orm");
        $this->load->model("inventarios/Items_estados_orm");

        $this->load->model("inventarios/Categorias_orm");
        $this->load->model("inventarios/Items_categorias_orm");


        $this->load->model("inventarios/Precios_orm");
        $this->load->model("inventarios/Items_precios_orm");

        $this->load->model("inventarios/Unidades_orm");
        $this->load->model("inventarios/Items_unidades_orm");

        $this->load->model("pedidos/Pedidos_items_orm");

        $this->load->model("entradas/Entradas_orm");
        $this->load->model("entradas/Entradas_items_orm");

        $this->load->model("salidas/Salidas_orm");

        $this->load->model("consumos/Consumos_orm");
        $this->load->model("consumos/Consumos_items_orm");

        $this->load->model("ordenes/Ordenes_orm");
        $this->load->model("ordenes/Ordenes_items_orm");

        $this->load->model("ordenes_ventas/Orden_ventas_orm");
        $this->load->model("ordenes_ventas/Ordenes_venta_item_orm");

        $this->load->model("traslados/Traslados_orm");
        $this->load->model("traslados/Traslados_items_orm");
        $this->load->model("traslados/Traslados_cat_orm");

        $this->load->model("bodegas/Bodegas_orm");

        $this->load->model("ajustes/Ajustes_orm");
        $this->load->model("ajustes/Ajustes_items_orm");
        $this->load->model("ajustes/Ajustes_cat_orm");
        $this->load->model("contabilidad/Impuestos_orm");
        $this->load->module(array('documentos'));
        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();

        $uuid_empresa       = $this->session->userdata('uuid_empresa');
        $this->empresa      = Empresa_orm::findByUuid($uuid_empresa);
        $this->id_usuario   = $this->session->userdata("id_usuario");
        $this->id_empresa   = $this->empresa->id;

        //PREFIJO DE NOMEMCLATURA DE PEDIDO
        $this->prefijo = "INV";

        //repositorios
        $this->itemsRep     = new itemsRep();
        $this->seriesRep    = new seriesRep();
        $this->ItemsCategoriasRepository = new ItemsCategoriasRepository();
        $this->ItemsCatRepository = new ItemsCatRepository();
        $this->ItemsUnidadesRepository = new ItemsUnidadesRepository();
        $this->ItemsPreciosRepository = new ItemsPreciosRepository();
        $this->CuentasRepository = new CuentasRepository();
        $this->ImpuestosRepository = new ImpuestosRepository();

        $this->HtmlRender = new HtmlRender;
    }



    public function index()
    {
        redirect("inventarios/listar");
    }

    public function ajax_get_cantidad(){

        if(!$this->input->is_ajax_request()){
            return false;
        }

        $response = [];
        $post = $this->input->post();
        $item = $this->itemsRep->find($post['item_id']);
        if(count($item)){
            $response = $item->comp_enInventario($post['uuid_bodega']);
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response))->_display();
        exit();

    }

    public function ajax_get_precios(){

        if(!$this->input->is_ajax_request()){
            return false;
        }

        $response = [];
        $post = $this->input->post();
        $item = $this->itemsRep->find($post['item_id']);
        if(count($item)){
            //$response = $item->comp_ultimosTresPrecios();
            $response = $this->itemsRep->getUltimosPrecios($item, 3);
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response))->_display();
        exit();

    }


    public function listar()
    {
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
            'public/assets/css/plugins/jquery/jquery.fileupload.css',
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
            'public/assets/js/plugins/jquery/fileupload/jquery.fileupload.js',

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
            'public/assets/js/default/formulario.js',

            /* Archivos js del propio modulo*/
            'public/assets/js/modules/inventarios/listar.js',
        ));

    	/*
    	 * Verificar si existe alguna variable de session
    	 * proveniente de algun formulario de crear/editar
    	 */
    	if($this->session->userdata('idItem')){
    		//Borrar la variable de session
    		$this->session->unset_userdata('idItem');

    		//Establecer el mensaje a mostrar
    		$data["mensaje"]["clase"] = "alert-success";
    		$data["mensaje"]["contenido"] = "Se ha creado el Item satisfactoriamente.";
    	}
    	else if($this->session->userdata('updatedItem')){
    		//Borrar la variable de session
    		$this->session->unset_userdata('updatedItem');

    		//Establecer el mensaje a mostrar
    		$data["mensaje"]["clase"] = "alert-success";
    		$data["mensaje"]["contenido"] = "Se ha actualizado el Item satisfactoriamente.";
    	}


        //Breadcrum Array
        $breadcrumb = array(
            "titulo"    => '<i class="fa fa-cubes"></i> Inventario: Items',
            "ruta" => array(
                0 => array(
                    "nombre" => "Inventarios",
                    "activo" => false
                ),
                1 => array(
                    "nombre" => '<b>Items</b>',
                    "activo" => true
                )
            ),
            "filtro"    => false, //sin vista grid
            "menu"      => array()
        );

        //Verificar si tiene permiso a la seccion de Crear
        if (1 or $this->auth->has_permission('acceso', 'inventarios/crear')){
            $breadcrumb["menu"]["nombre"] = "Crear";
            $breadcrumb["menu"]["url"] = "inventarios/crear";
        }

        //Verificar si tiene permiso de Exportar
        if (1 or $this->auth->has_permission('listar__exportar', 'inventarios/listar')){
            $breadcrumb["menu"]["opciones"]["#exportarBtn"] = "Exportar";
        }

        //Agregra variables PHP como variables JS
        $this->assets->agregar_var_js(array(
            "flexio_mensaje" => Flexio\Library\Toast::getStoreFlashdata()
        ));

        unset($data["mensaje"]);

        //CATALOGOS - ESTADO = 0 DE FORMA TERMPORAL
        $data["estados"]    = Items_estados_orm
                            ::where("id_campo", "=", "0")
                            ->orderBy("id_cat", "ASC")
                            ->get();

        $data["categorias"] = Categorias_orm
                            ::where("empresa_id", "=", $this->id_empresa)
                            ->where("estado", "=", "1")
                            ->orderBy("nombre", "ASC")
                            ->get();

    	$this->template->agregar_titulo_header('Listado de Items');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar($breadcrumb);

    }


    public function ajax_get_item_unidad()
    {

    	if($this->input->is_ajax_request())
        {

            $id_item_unidad = $this->input->post("id_item_unidad", true);
            $registro       = Items_unidades_orm::find($id_item_unidad);


            $response               = array();
            $response["success"]    = true;
            $response["registro"]   = $registro->toArray();

            echo json_encode($response);
            exit();
        }

    }



    public function ajax_listar()
    {
        if(!$this->input->is_ajax_request()){
            return false;
        }

        $clause                 = $this->input->post();
        $clause["empresa_id"]   = $this->id_empresa;
        $clause['campo']        = $this->input->post('campo');
        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();

        $count = $this->itemsRep->count($clause);
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

        $items = $this->itemsRep->get($clause, $sidx, $sord, $limit, $start);

        $response           = new stdClass();
        $response->page     = $page;
        $response->total    = $total_pages;
        $response->records  = $count;


        if($count){

            foreach($items as $i => $row){

                $response->rows[$i]["id"]   = $row->uuid_item;
                $response->rows[$i]["cell"] = $this->itemsRep->getColletionCell($row, $this->auth);
            }
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))->_display();
        exit;
    }

    public function ajax_get_codigo_validez() {

        $clause = $this->input->post();
        $clause["empresa_id"] = $this->id_empresa;

        $items = $this->itemsRep->get($clause, NULL, NULL, 1, 0);

        $registro = ["codigo_valido" => false];
        if (count($items) == 0) {
            $registro['codigo_valido'] = true;
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($registro))->_display();

        exit;
    }

    public function ajax_listar_series()
    {
        if(!$this->input->is_ajax_request()){
            return false;
        }
        $clause                 = $this->input->post();
        $clause["empresa_id"]   = $this->id_empresa;
        $clause['campo']        = $this->input->post('campo');
        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();

        $count = $this->seriesRep->count($clause);
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

        $series = $this->seriesRep->get($clause, $sidx, $sord, $limit, $start);

        $response           = new stdClass();
        $response->page     = $page;
        $response->total    = $total_pages;
        $response->records  = $count;


        if($count){
            foreach($series as $i => $row){
                $response->rows[$i]["id"]   = $row->id;
                $response->rows[$i]["cell"] = $this->seriesRep->getCollectionCellSeries($row, $this->auth);
            }
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))->_display();
        exit;
    }

    public function ajax_get_existencia()
    {
        if($this->input->is_ajax_request())
    	{
            $response               = array();
            $response["success"]    = false;
            $response["registro"]   = array();

            $item_iden      = $this->input->post("item_iden", true);//numeric|hex
            $unidad_iden    = $this->input->post("unidad_iden", true);//numeric|hex
            $bodega_iden    = $this->input->post("bodega_iden", true);//numeric|hex

            if(!empty($item_iden))
            {
                $item   = is_numeric($item_iden) ? Items_orm::find($item_iden) : Items_orm::findByUuid($item_iden);

                if(!empty($bodega_iden))
                {
                    //Retorna las entradas de la bodega indicada
                    $bodega         = is_numeric($bodega_iden) ? Bodegas_orm::find($bodega_iden) : Bodegas_orm::findByUuid($bodega_iden);
                    $entradas_items = Entradas_items_orm::withExistencia($item, $bodega->uuid_bodega)->get();
                }
                else
                {
                    //Retorna las entradas de todas las bodegas
                    $entradas_items = Entradas_items_orm::withExistenciaAllBodegas($item)->get();
                }

                if(count($entradas_items))
                {
                    $response["success"]    = true;


                    foreach($entradas_items as $ei)
                    {

                    }

                    $response["registro"]  = $entradas_items->toArray();
                    echo json_encode($response);
                    exit;
                    // IMPORTANTE --- tomar en cuenta la unidad...
                }
            }
        }
    }

    public function ajax_listar_en_inventario()
    {
    	//Just Allow ajax request
    	if($this->input->is_ajax_request())
    	{
            //Para aplicar filtros
            $item_id  = $this->input->post("item_id", true);
            $item = Flexio\Modulo\Inventarios\Models\Items::find($item_id);
            $registros  = Bodegas_orm::deEmpresa($this->id_empresa)->activas();

            //jqgrid
            list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
            $count = $registros->count();
            list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

            $registros->orderBy($sidx, $sord)
                    ->skip($start)
                    ->take($limit);


            //Constructing a JSON
            $response = new stdClass();
            $response->page = $page;
            $response->total = $total_pages;
            $response->records = $count;

            if($count)
            {
                foreach ($registros->get() AS $i => $row)
                {

                    $hidden_options = "";
                    $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-uuid="'. $row->uuid_bodega .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';


                    $hidden_options .= '<a href="'.base_url('bodegas/ver/'. $row->uuid_bodega).'" class="btn btn-block btn-outline btn-success">Ver Bodega</a>';


                    //Si no tiene acceso a ninguna opcion
                    //ocultarle el boton de opciones
                    if($hidden_options == ""){
                            $link_option = "&nbsp;";
                    }


                    $enInventario = $item->comp_enInventario($row->uuid_bodega);

                    $response->rows[$i]["id"]   = $row->uuid_bodega;
                    $response->rows[$i]["cell"] = array(
                        $row->nombre,
                        $enInventario["cantidadDisponibleBase"],
                        $enInventario["cantidadDisponibleBase"],
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

    public function ajax_listar_historial_ajustes()
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
            $item_id = $this->input->post("item_id", true);
            $item = Items_orm::find($item_id);

            $registros  = Ajustes_orm::deEmpresa($this->id_empresa)
                        ->conItem($this->input->post("item_id", true));


            /**
             * Total rows found in the query.
             * @var int
            */
            $count          = $registros->count();

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

            $registros  = $registros->orderBy($sidx, $sord)
                        ->skip($start)
                        ->take($limit)
                        ->get();


            //Constructing a JSON
            $response   = new stdClass();
            $response->page     = $page;
            $response->total    = $total_pages;
            $response->records  = $count;
            $i = 0;



            if($count)
            {
                foreach ($registros AS $i => $row)
                {

                    $hidden_options = "";
                    $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-uuid="'. $row->uuid_ajuste .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';


                    $hidden_options .= '<a href="'.base_url('ajustes/ver/'. $row->uuid_ajuste).'" class="btn btn-block btn-outline btn-success">Ver Ajuste</a>';


                    //Si no tiene acceso a ninguna opcion
                    //ocultarle el boton de opciones
                    if($hidden_options == ""){
                            $link_option = "&nbsp;";
                    }



                    $response->rows[$i]["id"]   = $row->uuid_ajuste;
                    $response->rows[$i]["cell"] = array(
                        $row->created_at,
                        "AJ".$row->numero,
                        $row->descripcion,
                        $this->HtmlRender->setBackgroundColor($this->StateColorsAjustes[$row->estado->id_cat])->setContent($row->estado->etiqueta)->label(),
                        $row->tipo_ajuste->comp__tipoWithSpan(),
                        $row->cantidadAjustadaItem($item->id)." ".$item->unidadBase(),
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


    public function ajax_listar_bitacora_traslados()
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
            $item_id  = $this->input->post("item_id", true);
            $item       = Items_orm::find($item_id);

            $registros  = Traslados_orm::deEmpresa($this->id_empresa)
                        ->conItem($item->id);

            /**
             * Total rows found in the query.
             * @var int
            */
            $count          = $registros->count();

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

            $registros->orderBy($sidx, $sord)
                        ->skip($start)
                        ->take($limit);


            //Constructing a JSON
            $response   = new stdClass();
            $response->page     = $page;
            $response->total    = $total_pages;
            $response->records  = $count;
            $i = 0;



            if($count)
            {
                foreach ($registros->get() AS $i => $row)
                {

                    $hidden_options = "";
                    $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-uuid="'. $row->uuid_traslado .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';


                    $hidden_options .= '<a href="'.base_url('traslados/ver/'. $row->uuid_traslado).'" class="btn btn-block btn-outline btn-success">Ver Traslado</a>';


                    //Si no tiene acceso a ninguna opcion
                    //ocultarle el boton de opciones
                    if($hidden_options == ""){
                            $link_option = "&nbsp;";
                    }



                    $response->rows[$i]["id"]   = $row->uuid_traslado;
                    $response->rows[$i]["cell"] = array(
                        "TRAS".$row->numero,
                        $row->fecha_creacion,
                        $row->deBodega->nombre,
                        $row->bodega->nombre,
                        $row->estado->comp__etiquetaWithSpan(),
                        $row->cantidadTrasladadaItem($item->id),
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

    function ajax_delete_item_unidad()
    {
    	//Just Allow ajax request
    	if(!$this->input->is_ajax_request()){
            return false;
    	}

        $id_registro    = $this->input->post("id_registro", true);
    	$registro       = Items_unidades_orm::find($id_registro);

        if($registro->first())
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
            $items[$i]["Proveedor"]         = $registro->proveedor->nombre;
            $items[$i]["Referencia"]        = isset($registro->referencia) ? $registro->referencia : "";
            $items[$i]["Centro Contable"]   = $registro->centro->nombre;
            $items[$i]["Estado"]            = $registro->estado->etiqueta;
            $items[$i]["Monto"]             = "\$".$registro->monto;

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
    public function ocultotabla($uuid = NULL, $modulo = "")
    {

        if(is_array($uuid))
        {
            $this->assets->agregar_var_js([
                "campo" => collect($uuid)
            ]);
        }
        //If ajax request
    	$this->assets->agregar_js(array(
            'public/assets/js/modules/inventarios/tabla.js'
    	));

    	$this->load->view('tabla');
    }

    public function ocultotabla_series($sp_string_var = "")
    {



        /*$sp_array_var = explode('=', $sp_string_var);
        if (count($sp_array_var) == 2) {

            $this->assets->agregar_var_js(array(
                $sp_array_var[0] => $sp_array_var[1]
            ));

        }*/ //modelo anterior

        if(is_array($sp_string_var))
        {
            $this->assets->agregar_var_js([
                "campo" => collect($sp_string_var)
            ]);
        }
        elseif($sp_string_var and count(explode("=", $sp_string_var)) > 1)
        {
            $aux = explode("=", $sp_string_var);
            $this->assets->agregar_var_js([$aux[0]=>$aux[1]]);
        }

        $this->assets->agregar_js(array(
            'public/assets/js/modules/inventarios/tabla_series.js'
    	));

    	$this->load->view('tabla_series');
    }

    public function ocultotablaEnInventario($sp_string_var = '') {

        $this->assets->agregar_js(array(
            'public/assets/js/modules/inventarios/tablaEnInventario.js'
        ));

        $sp_array_var = explode('=', $sp_string_var);
        if (count($sp_array_var) == 2) {

            $this->assets->agregar_var_js(array(
                $sp_array_var[0] => $sp_array_var[1]
            ));

        }

        $this->load->view('tablaEnInventario');
    }

    public function ocultotablaHistorialAjustes($sp_string_var = "")
    {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/inventarios/tablaHistorialAjustes.js'
    	));

        $sp_array_var = explode('=', $sp_string_var);
        if (count($sp_array_var) == 2) {

            $this->assets->agregar_var_js(array(
                $sp_array_var[0] => $sp_array_var[1]
            ));

        }

    	$this->load->view('tablaHistorialAjustes');
    }

    public function ocultotablaBitacoraTraslados($sp_string_var = '')
    {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/inventarios/tablaBitacoraTraslados.js'
    	));

        $sp_array_var = explode('=', $sp_string_var);
        if (count($sp_array_var) == 2) {

            $this->assets->agregar_var_js(array(
                $sp_array_var[0] => $sp_array_var[1]
            ));

        }

    	$this->load->view('tablaBitacoraTraslados');
    }

    public function ocultoarbolactivo()
    {
		$this->assets->agregar_js(array(
	           'public/assets/js/modules/inventarios/arbol_activo.js'
		));
		$this->load->view('arbol_activo');
	}

    public function ocultoarbolingreso()
    {
		$this->assets->agregar_js(array(
	           'public/assets/js/modules/inventarios/arbol_ingreso.js'
		));
		$this->load->view('arbol_ingreso');
	}

    public function ocultoarbolcosto()
    {
		$this->assets->agregar_js(array(
	           'public/assets/js/modules/inventarios/arbol_costo.js'
		));
		$this->load->view('arbol_costo');
	}

    public function ocultoarbolvariante()
    {
		$this->assets->agregar_js(array(
	           'public/assets/js/modules/inventarios/arbol_variante.js'
		));
		$this->load->view('arbol_variante');
	}

    /**
     * Cargar Vista Parcial de Formulario
     *
     * @return void
     */

    public function ocultoformulario()
    {
    	$this->assets->agregar_js(array(
            'public/assets/js/default/vue/directives/inputmask.js',
            'public/assets/js/default/vue/directives/select2.js',
            'public/resources/compile/modulos/inventarios/formulario.js',
            //se usa para los servicios asociados al segmento de cuentas
            'public/assets/js/modules/configuracion_contabilidad/routes.js',
    	));

        $clause = ['empresa_id'=>$this->id_empresa];
        $cuentas = $this->CuentasRepository->get(array_merge($clause,['transaccionales'=>true]));

        $inventarios_cat = $this->ItemsCatRepository->get();
        $this->assets->agregar_var_js(array(
            "tipos" => $inventarios_cat->filter(function($inventario_cat){return $inventario_cat->valor == "tipo";}),
            "categorias" => $this->ItemsCategoriasRepository->get($clause),
            "estados" => $inventarios_cat->filter(function($inventario_cat){return $inventario_cat->valor == "estado";}),
            "unidades" => $this->ItemsUnidadesRepository->get($clause),
            "precios_venta" => $this->ItemsPreciosRepository->get(array_merge($clause,['tipo_precio'=>'venta', 'estado'=>1])),
            "precios_alquiler" => $this->ItemsPreciosRepository->get(array_merge($clause,['tipo_precio'=>'alquiler', 'estado'=>1])),
            "impuestos" => $this->ImpuestosRepository->get($clause)
        ));

        $this->load->view('formulario');
    }

    public function ocultoformulario_trazabilidad($data = array())
    {
        $this->load->view('formulario_trazabilidad', $data);
    }

    public function guardar()
    {
        $post = $this->input->post();
//dd($post);
        if (!empty($post))
        {
            $accion = new Flexio\Modulo\Inventarios\HttpRequest\FormGuardar;
            $toast = new Flexio\Library\Toast;
            try {
                $item = $accion->guardar();
            } catch (\Exception $e) {
                log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
                $toast->setUrl('inventarios/listar')->run("exception",[$e->getMessage()]);
            }
            if (isset($item)) {
                $toast->run("success",["{$item->codigo} - {$item->nombre}"]);
            } else {
                $toast->run("error");
            }
            redirect(base_url('inventarios/listar'));
        }

    }

    public function ajax_quick_add()
    {
        $post = $this->input->post();
        $error = '<b>¡Error!</b> No se ha guardado correctamente el item</b>';

        if (!empty($post))
        {
            $accion = new Flexio\Modulo\Inventarios\HttpRequest\FormGuardar;
            $toast = new Flexio\Library\Toast;
            try {
                $item = $accion->guardar();
            } catch (\Exception $e) {
                log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
                $error = $e->getMessage();
            }

            $response = [
                'estado' => isset($item) ? 200 : 500,
                'mensaje' => isset($item) ? '<b>¡&Eacute;xito!</b> Se ha guardado correctamente el item. Ya puede a&ntilde;adirlo a su pedido' : $error
            ];

            $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode($response))->_display();
            exit;
        }

    }


    public function crear()
    {
        $data = [];

        $toast = new Flexio\Library\Toast;
        //Verificar permisos de acceso -> sin no los tiene retorna al landing page.
        $toast->runVerifyPermission($this->auth->has_permission('acceso', "inventarios/crear"));

        //styles, scripts and javascript vars required
        $this->_css();
        $this->_js();

        $this->assets->agregar_var_js(array(
            "vista" => 'crear'
        ));

        //breadcrumb





      $breadcrumb = array(
         "titulo" => '<i class="fa fa-cubes"></i> Crear item',
          "filtro" => false,
          "menu" => array(
              "nombre" => 'Acci&oacute;n',
              "url"	 => '#',
              "opciones" => array()
          ),

          "ruta" => array(
            0 => array(
                "nombre" => "Inventarios",
                "activo" => false,
            ),
              1 => array(
                  "nombre" => "Items",
                  "activo" => false,
                  "url" => 'inventarios/listar'
              ),
              2=> array(
                  "nombre" => '<b>Crear</b>',
                  "activo" => true
              )
          ),
      );

        //output
        $this->template->agregar_titulo_header('Items');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar();
    }

    public function editar($uuid=NULL)
    {

        $data = [];

        $toast = new Flexio\Library\Toast;
        //Verificar permisos de acceso -> sin no los tiene retorna al landing page.
        $toast->runVerifyPermission($this->auth->has_permission('acceso', "inventarios/ver/(:any)"));

        //styles, scripts and javascript vars required
        $this->_css();
        $this->_js();
        $this->assets->agregar_js(array(
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
        ));

        $item = $this->itemsRep->findByUuid($uuid);
        $item->load('comentario_timeline' );
        $this->assets->agregar_var_js(array(
            "vista" => 'editar',
            "item"  => $this->itemsRep->getCollectionCampo($item)
        ));



      $breadcrumb = array(
         "titulo" => '<i class="fa fa-cubes"></i> Item '.$item->codigo,
          "filtro" => false,
          "menu" => array(
              "nombre" => 'Acci&oacute;n',
              "url"	 => '#',
              "opciones" => array()
          ),

          "ruta" => array(
            0 => array(
                "nombre" => "Inventarios",
                "activo" => false,
            ),
              1 => array(
                  "nombre" => "Items",
                  "activo" => false,
                  "url" => 'inventarios/listar'
              ),
              2=> array(
                  "nombre" => '<b>Detalle</b>',
                  "activo" => true
              )
          ),
      );


        //se usa para los metodos ocultotabla
        $data["item_id"]    = $item->id;

    	$this->template->agregar_titulo_header('Items');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar();
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
            'public/assets/css/plugins/bootstrap/bootstrap-tagsinput.css',
            'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
            'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
            'public/assets/css/plugins/jquery/fileinput/fileinput.css',
            'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
            'public/assets/css/plugins/bootstrap/select2.min.css'
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
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/plugins/jquery/jqgrid/plugins/jQuery.jqGrid.columnToggle.js',
            'public/assets/js/plugins/jquery/switchery.min.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/combodate/combodate.js',
            'public/assets/js/plugins/jquery/combodate/momentjs.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/plugins/bootstrap/select2/select2.min.js',
            'public/assets/js/plugins/bootstrap/select2/es.js',
            'public/assets/js/default/tabla-dinamica.jquery.js',
            'public/assets/js/default/formulario.js',
    	));
    }

    public function trazabilidad($uuid_serial=NULL)
    {
        if(!$uuid_serial)
        {
            echo "hace falta el identificador del serial.";
            die();
        }

    	$data       = array();

        //Cargo el registro
        $clause                 = [];
        $clause["uuid_serial"]  = $uuid_serial;
        $data["serial"]         = $this->seriesRep->findBy($clause);

        $this->_css();
        $this->_js();

    	$breadcrumb = array(
            "titulo" => '<i class="fa fa-cubes"></i> Detalle del item: Trazabilidad',
            "ruta" => [
                ["nombre" => "Inventario", "activo" => false],
                ["nombre" => '<b>Series</b>', "activo" => true, 'url' => 'series/listar']
            ]
    	);

    	$this->template->agregar_titulo_header('Items');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar();
    }




    public function ajax_get_items()
    {

    /*	if($this->input->is_ajax_request())
        {*/

            $clause                 = $this->input->post();
            $clause["empresa_id"]   = $this->id_empresa;
            $items                  = $this->itemsRep->get($clause);

            $response               = array();
            $response["success"]    = true;
            $response["registros"]  = array();

            if(count($items))
            {
                $response["registros"] = $this->itemsRep->getColletionRegistros($items, isset($clause["uuid_bodega"]) ? $clause["uuid_bodega"] : NULL);
            }

            echo json_encode($response);
            exit();
        //}

    }

    public function ajax_get_items_categoria()
    {
        if(!$this->input->is_ajax_request()){
            return false;
        }


        $clause = $this->input->post();
        $items = $this->itemsRep->newCatItems(['categoria_id'=>$clause['id'],'empresa_id'=>$this->id_empresa, "item_id" => $clause["item_id"], 'activo' => true]);
        $response = [
            'items' => $clause['ventas'] ? $this->itemsRep->getCollectionItemsVentas($items) : $this->itemsRep->getCollectionItems($items)
        ];

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response))->_display();
        exit();
    }


    public function ajax_get_typehead_items(){
        if(!$this->input->is_ajax_request()){
            return false;
        }

        $nombre = $this->input->get('search');
        $ventas = $this->input->get('ventas');
        $categoria_id = $this->input->get('categoria_id');
        $items = $this->itemsRep->getItemsConCategoriasChunk(['nombre'=>$nombre,'empresa_id'=>$this->id_empresa, "categoria_id" => $categoria_id, 'activo' => true]);

        $response = $ventas ? $this->itemsRep->getCollectionItemsVentas($items) : $this->itemsRep->getCollectionItems($items);

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response))->_display();
        exit();

    }

    function ajax_getnuevo_typehead_items(){
        if(!$this->input->is_ajax_request()){
            return false;
        }

        $nombre = $this->input->get('params[search]');
        if(empty($nombre))$nombre = $this->input->get('search');
        $ventas = str_replace("/", "",$this->input->get('ventas'));
        $categoria_id = $this->input->get('categoria_id');
        $collection = new \Flexio\Modulo\Inventarios\Collections\ItemsVentas;

        $items = $this->itemsRep->getItemsChunk(['nombre'=>$nombre,'empresa_id'=>$this->id_empresa, "categoria_id" => $categoria_id,'estado' => 1]);

        $response = $collection::getCollectionVentas($items);

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response))->_display();
        exit();
    }

    function documentos_campos() {

    	return array(
    	array(
    		"type"		=> "hidden",
    		"name" 		=> "items_id",
    		"id" 		=> "items_id",
    		"class"		=> "form-control",
    		"readonly"	=> "readonly",
    	));
    }

    function ajax_guardar_documentos() {
    	if(empty($_POST)){
    		return false;
    	}

    	$items_id = $this->input->post('items_id', true);
        $modeloInstancia = $this->itemsRep->findByUuid($items_id);
    	$this->documentos->subir($modeloInstancia);
    }

    function ajax_categoria(){
        if(!$this->input->is_ajax_request()){
            return false;
        }
         $clause = ['empresa_id' => $this->id_empresa];
         $columns = ['id','nombre'];
         $categorias = $this->ItemsCategoriasRepository->getAll($clause, $columns);
          $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output($categorias)->_display();
        exit();

    }

}

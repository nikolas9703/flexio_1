<?php
/**
 * Bodegas
 *
 * Modulo para administrar la creacion, edicion de bodegas
 *
 * @package    PensaApp
 * @subpackage Controller
 * @category   Controllers
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  10/16/2015
 */

use Illuminate\Database\Capsule\Manager as Capsule;

//Repositorios
use Flexio\Modulo\Bodegas\Repository\BodegasRepository as bodegasRep;
use Flexio\Modulo\Bodegas\Repository\BodegasCambiarEstadoRepository as bodegasCambiarEstadoRepository;
use Flexio\Modulo\Inventarios\Repository\ItemsRepository as itemsRep;

class Bodegas extends CRM_Controller
{
    protected $empresa;
    protected $id_empresa;
    protected $prefijo;
    protected $id_usuario;

    //repositorios
    protected $bodegasRep;
    protected $itemsRep;


    public function __construct()
    {
        parent::__construct();
        $this->load->model("usuarios/Empresa_orm");

        $this->load->model("bodegas/Bodegas_orm");
        $this->load->model("bodegas/Bodegas_cat_orm");

        $this->load->model("entradas/Entradas_orm");
        $this->load->model("entradas/Entradas_items_orm");

        $this->load->model("salidas/Salidas_orm");

        $this->load->model("ordenes_ventas/Orden_ventas_orm");
        $this->load->model("ordenes_ventas/Ordenes_venta_item_orm");

        $this->load->model("consumos/Consumos_orm");
        $this->load->model("consumos/Consumos_items_orm");

        $this->load->model("ordenes/Ordenes_orm");
        $this->load->model("ordenes/Ordenes_items_orm");

        $this->load->model("traslados/Traslados_orm");
        $this->load->model("traslados/Traslados_items_orm");

        $this->load->model("inventarios/Unidades_orm");
        $this->load->model("inventarios/Categorias_orm");
        $this->load->model("inventarios/Items_orm");
        $this->load->model("inventarios/Items_estados_orm");

        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();

        $uuid_empresa       = $this->session->userdata('uuid_empresa');
        $this->empresa      = Empresa_orm::findByUuid($uuid_empresa);
        $this->id_usuario   = $this->session->userdata("id_usuario");
        $this->id_empresa   = $this->empresa->id;

        //PREFIJO DE NOMEMCLATURA
        $this->prefijo = "BOD";

        //repositorios
        $this->bodegasRep                       = new bodegasRep();
        $this->bodegasCambiarEstadoRepository   = new bodegasCambiarEstadoRepository();
        $this->itemsRep                         = new itemsRep();
    }



    public function index()
    {
        redirect("bodegas/listar");
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
            'public/assets/css/plugins/jquery/toastr.min.css',
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
            'public/assets/js/plugins/toastr.min.js',
            'public/assets/js/default/formulario.js',

            /* Archivos js del propio modulo*/
            'public/assets/js/modules/bodegas/listar.js',
        ));

    	/*
    	 * Verificar si existe alguna variable de session
    	 * proveniente de algun formulario de crear/editar
    	 */
    	if($this->session->userdata('idBodega')){
    		//Borrar la variable de session
    		$this->session->unset_userdata('idBodega');

    		//Establecer el mensaje a mostrar
    		$data["mensaje"]["clase"] = "alert-success";
    		$data["mensaje"]["contenido"] = "Se ha creado la Bodega satisfactoriamente.";
    	}
    	else if($this->session->userdata('updatedBodega')){
    		//Borrar la variable de session
    		$this->session->unset_userdata('updatedBodega');

    		//Establecer el mensaje a mostrar
    		$data["mensaje"]["clase"] = "alert-success";
    		$data["mensaje"]["contenido"] = "Se ha actualizado la Bodega satisfactoriamente.";
    	}


        //Breadcrum Array
        $breadcrumb = array(
            "titulo"    => '<i class="fa fa-cubes"></i> Inventario: Bodegas',
            "ruta" => array(
                0 => array(
                    "nombre" => "Inventarios",
                    "activo" => false
                ),
                1 => array(
                    "nombre" => '<b>Bodegas</b>',
                    "activo" => true
                )
            ),
            "filtro"    => false, //sin vista grid
            "menu"      => array()
        );

        //Verificar si tiene permiso a la seccion de Crear
        if ($this->auth->has_permission('acceso', 'bodegas/crear')){
            $breadcrumb["menu"]["nombre"] = "Crear";
            $breadcrumb["menu"]["url"] = "bodegas/crear";
        }

        //Verificar si tiene permiso de Exportar
        if ($this->auth->has_permission('listar__exportar', 'bodegas/listar')){
            $breadcrumb["menu"]["opciones"]["#exportarBtn"] = "Exportar";
        }

        //Agregra variables PHP como variables JS
        $this->assets->agregar_var_js(array(
            "mensaje_clase"     => isset($data["mensaje"]["clase"]) ? $data["mensaje"]["clase"] : "0",
            "mensaje_contenido" => isset($data["mensaje"]["contenido"]) ? $data["mensaje"]["contenido"] : "0"
        ));

        unset($data["mensaje"]);

        //CATALOGOS - ESTADO = 0 DE FORMA TERMPORAL
//        $data["estados"]    = Items_estados_orm
//                            ::where("id_campo", "=", "0")
//                            ->orderBy("id_cat", "ASC")
//                            ->get();

//        $data["categorias"] = Categorias_orm
//                            ::where("empresa_id", "=", $this->id_empresa)
//                            ->where("estado", "=", "1")
//                            ->orderBy("nombre", "ASC")
//                            ->get();

    	$this->template->agregar_titulo_header('Listado de Bodegas');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar($breadcrumb);

    }

    function ajax_cambiar_estado()
    {
        $response = array();
        $response["success"]    = false;

        $id             = (int) $this->input->post("id", true);
        $estado_actual  = (int) $this->input->post("estado_actual", true);

        if(!empty($id) && !empty($estado_actual))
        {
            //llamar en mi repositorio a mi funcion que cambia el estado
            $response["success"] = $this->bodegasCambiarEstadoRepository->cambiarEstado($id, $estado_actual);
        }

        echo json_encode($response);
        exit();
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


            //Capsule::connection()->enableQueryLog();
            //Para aplicar filtros
            $registros = Bodegas_orm::deEmpresa($this->id_empresa)->padres();

            /**
             * Verificar si existe algun $_POST
             * de los campos de busqueda
            */
            $codigo             = $this->input->post('codigo', true);
            $nombre             = $this->input->post('nombre', true);
            $contacto_principal = $this->input->post('contacto_principal', true);
            $direccion          = $this->input->post('direccion', true);
            $telefono           = $this->input->post('telefono', true);


            if(!empty($codigo)){
                $registros->deCodigo($codigo);
            }

            if(!empty($nombre)){
                $registros->deNombre($nombre);
            }

            if(!empty($contacto_principal)){
                $registros->deContactoPrincipal($contacto_principal);
            }

            if(!empty($direccion)){
                $registros->deDireccion($direccion);
            }

            if(!empty($telefono)){
                $registros->deTelefono($telefono);
            }



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



            if($count > 0)
            {
                foreach (Bodegas_orm::listar($registros->get()) AS $i => $row)
                {

                    $hidden_options = "";
                    $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-nombre="'.$row["nombre"].'" data-uuid="'. $row["id"] .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

                    //IMPORTANTE A MODO DE DESARROLLO ANADI LA CONDICION OR 1
                    //PARA QUE TODAS LAS CONDICIONES DIERAN TRUE

                    $enlace = $row["codigo"];
                    if($this->auth->has_permission('acceso', 'bodegas/ver/(:any)')){
                        //
                        $hidden_options .= '<a href="'.base_url('bodegas/ver/'. $row["uuid_bodega"]).'" class="btn btn-block btn-outline btn-success">Ver Bodega</a>';

                        $enlace = '<a href="'. base_url('bodegas/ver/'. $row["uuid_bodega"]) .'" style="color:blue;">'.$enlace.'</a>';
                    }

                    //activar/desactivar bodega
                    if((!$row["entradas"]) and $this->auth->has_permission('acceso', 'bodegas/ajax-cambiar-estado')){
                        $hidden_options .= '<a href="#" class="btn btn-block btn-outline btn-success btn-cambiar-estado" data-estado_id="'.$row["estado"].'" data-id="'.$row["id"].'">'.(($row["estado"] != "1")?"Activar":"Inactivar").'</a>';
                    }


                    //Si no tiene acceso a ninguna opcion
                    //ocultarle el boton de opciones
                    if($hidden_options == ""){
                        $link_option = "&nbsp;";
                    }

                    $estado = $row["estado"] == 1 ? "Activo" : "Inactivo";
                    $color_label = $row["estado"] == 1 ? "label-successful" : "label-danger";
                    $label_estado = '<label class="label '.$color_label.'">'.$estado.'</label>';
                    $response->rows[$i]["id"]   = (string) $row["id"];
                    $response->rows[$i]["cell"] = array(
                        "id"                    => (string) $row["id"],
                        'Codigo'                => $enlace,//codigo
                        'Nombre'                => $row["nombre"],
                        'Contacto principal'    => $row["padre_id"] == 0 ? $row["contacto_principal"] : "",
                        'Telefono'              => $row["padre_id"] == 0 ? $row["telefono"] : "",
                        'Direccion'             => $row["padre_id"] == 0 ? $row["direccion"] : "",
                        'Estado'                => $label_estado,
                        'link'                  => $link_option,
                        'options'               => $hidden_options,
                        'level'                 => $row["padre_id"] == 0? "0" : "1", //level
                        'parent'                => $row["padre_id"] == 0? "NULL": (string)$row["padre_id"], //parent
                        'isLeaf'                => $row["hijos"], //isLeaf
                        'expanded'              => false, //expended
                        'loaded'                => true, //loaded
                        "uuid"                  => $row["uuid_bodega"]
                    );
                    $i++;
                }
            }
            echo json_encode($response);
            exit;
    	}
    }

    public function ajax_listar_bodegas(){
        if(!$this->input->is_ajax_request()){
            return false;
    	}

        $registros  = Bodegas_orm::deEmpresa($this->id_empresa)->padres();
	$bodegas    = Bodegas_orm::listar($registros->get());

        //opcional
        $nodo_id    = $this->input->post("nodo_id", true);

        //Constructing a JSON
        $response = new stdClass();
        $response->plugins = [ "contextmenu" ];
        $response->core->check_callback[0] = true;

	if(count($bodegas)){
            foreach ($bodegas as  $i => $row){
                $response->core->data[$i] = array(
                    'id'        => (string)$row['id'],
                    'parent'    => $row["padre_id"] == 0 ? "#" : (string)$row["padre_id"],
                    'text'      => $row["codigo"]." ".$row["nombre"],
                    'icon'      => 'fa fa-folder',
                    'codigo'    => $row["codigo"],
                    'state'     => [
                        "disabled"  => ($row["entradas"]) ? true : false,
                        "selected"  => (!empty($nodo_id) and $nodo_id == $row["id"]) ? true : false
                    ]
                );
            }
        }

        echo json_encode($response);
        exit;

    }

    public function ajax_listar_items()
    {
    	//Just Allow ajax request
    	if(!$this->input->is_ajax_request()){return false;}

        $clause                 = $this->input->post();
        $clause["empresa_id"]   = $this->id_empresa;

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = $this->itemsRep->count($clause);
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $items = $this->itemsRep->get($clause, $sidx, $sord, $limit, $start);

        //Constructing a JSON
        $response   = new stdClass();
        $response->page     = $page;
        $response->total    = $total_pages;
        $response->records  = $count;

        if($count > 0)
        {
            foreach ($items AS $i => $row)
            {
                $response->rows[$i]["id"]   = $row->id;
                $response->rows[$i]["cell"] = $this->itemsRep->getColletionCell($row, $this->auth, $clause["uuid_bodega"], "Cell2");
            }
        }
        echo json_encode($response);exit;
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
        //If ajax request
    	$this->assets->agregar_js(array(
            'public/assets/js/modules/bodegas/tabla.js'
    	));

    	$this->load->view('tabla');
    }

    public function ocultotablaItems()
    {
        //If ajax request
    	$this->assets->agregar_js(array(
            'public/assets/js/modules/bodegas/tablaItems.js'
    	));

    	$this->load->view('tablaItems');
    }

    /**
     * Cargar Vista Parcial de Formulario
     *
     * @return void
     */
    public function ocultoformulario($data = array("campos" => array()))
    {
    	$this->assets->agregar_js(array(
            'public/assets/js/modules/bodegas/formulario.js'
    	));

        //Catalogos y otros
        $data["tipos_bodegas"]          = Bodegas_cat_orm::tipos()->get(array("id_cat", "etiqueta"));
        $data["estados_items_bodegas"]  = Bodegas_cat_orm::estadosItems()->get(array("id_cat", "etiqueta"));
        $data["bodegas_sin_entradas"]   = Bodegas_orm::deEmpresa($this->id_empresa)->sinEntradas()->get(array("id", "nombre", "codigo"));

        $this->load->view('formulario', $data);
    }

    function crear($segmento3 = "")
    {
        $data       = array();
    	$mensaje    = array();


        if(!empty($_POST))
    	{
            $response = false;
            $response = Capsule::transaction(
                function()
                {
                    $campo = $this->input->post("campo");
                    //DATOS GENERALES DEL ITEM
                    $bodega                 = new Bodegas_orm;
                    $bodega->uuid_bodega    = Capsule::raw("ORDER_UUID(uuid())");
                    $bodega->fecha_creacion = date("Y-m-d", time());
                    $bodega->estado         = 1;
                    $bodega->creado_por     = $this->id_usuario;
                    $bodega->empresa_id     = $this->id_empresa;

                    $bodega->codigo             = $campo["codigo"];
                    $bodega->nombre             = $campo["nombre"];
                    $bodega->contacto_principal = !empty($campo["contacto_principal"]) ? $campo["contacto_principal"] : "";
                    $bodega->direccion          = !empty($campo["direccion"]) ? $campo["direccion"] : "";
                    $bodega->telefono           = !empty($campo["telefono"]) ? $campo["telefono"] : "";
                    $bodega->entrada_id         = !empty($campo["entrada"]) ? $campo["entrada"] : "";
                    $bodega->estado_items       = !empty($campo["estado_items_bodega"]) ? $campo["estado_items_bodega"] : "0";
                    $bodega->padre_id           = !empty($campo["padre"]) ? $campo["padre"] : "0";

                    //GUARDO EL REGISTRO
                    $bodega->save();


                    $this->session->set_userdata('idBodega', $bodega->id);
                    return true;
                }
            );


            if($response == "1"){

                redirect(base_url('bodegas/listar'));

            }else{
                //Establecer el mensaje a mostrar
                $data["mensaje"]["clase"] = "alert-danger";
                $data["mensaje"]["contenido"] = "Hubo un error al tratar de crear el item.";
            }
    	}

    	//Introducir mensaje de error al arreglo
    	//para mostrarlo en caso de haber error
    	$data["message"] = $mensaje;

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
            'public/assets/css/plugins/jquery/jstree/default/style.min.css',
        ));

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
            'public/assets/js/plugins/jquery/jstree.min.js',
            'public/assets/js/default/lodash.min.js'
    	));

    	$breadcrumb = array(
            "titulo" => '<i class="fa fa-cubes"></i> Crear bodega'
    	);


        $data["campos"]["campos"] = array();


    	$this->template->agregar_titulo_header('Bodegas');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar();
    }

    public function exportar_existencia($uuid_bodega)
    {
        $csv = \League\Csv\Writer::createFromFileObject(new \SplTempFileObject());

        $clause                 = [];
        $clause["empresa_id"]   = $this->id_empresa;
        $clause["uuid_bodega"]  = $uuid_bodega;
        $bodega                 = $this->bodegasRep->findBy($clause);

        $csv->insertOne([utf8_decode("Número"),"Nombre","Contacto Principal",utf8_decode("Teléfono"),utf8_decode("Dirección")]);

        $csv->insertOne([$bodega->codigo,utf8_decode($bodega->nombre),utf8_decode($bodega->contacto_principal),$bodega->telefono,utf8_decode($bodega->direccion)]);
        $csv->insertOne([""]);
        $csv->insertOne([
            //"Ubicación",->en revision
            utf8_decode("Categoría"),"Item","Nombre","Serie","Unidad","Notas"
        ]);

        $csv->insertAll($this->itemsRep->getColletionRegistrosExportar($this->itemsRep->get($clause), $uuid_bodega));

        $csv->output('bodegas.csv');
        exit;
    }

    function editar($uuid=NULL)
    {
        if(!$uuid)
        {
            echo "Error.";
            die();
        }

    	$data       = array();
    	$mensaje    = array();

        //Cargo el registro
       // $registro   = Bodegas_orm::where("uuid_bodega", "=", hex2bin(strtolower($uuid)))->first();
        $registro = $this->bodegasRep->findByUuid($uuid);

    	if(!empty($_POST))
    	{
//            echo "<pre>";
//            print_r($_POST);
//            echo "<pre>";
//            die();
            $response = false;
            $response = Capsule::transaction(
                function() use ($registro)
                {
                    $campo = $this->input->post("campo");

                    $registro->codigo               = $campo["codigo"];
                    $registro->nombre               = $campo["nombre"];
                    $registro->contacto_principal   = !empty($campo["contacto_principal"]) ? $campo["contacto_principal"] : "";
                    $registro->direccion            = !empty($campo["direccion"]) ? $campo["direccion"] : "";
                    $registro->telefono             = !empty($campo["telefono"]) ? $campo["telefono"] : "";
                    $registro->entrada_id           = !empty($campo["entrada"]) ? $campo["entrada"] : "";
                    $registro->estado_items         = !empty($campo["estado_items_bodega"]) ? $campo["estado_items_bodega"] : "0";
                    $registro->padre_id             = !empty($campo["padre"]) ? $campo["padre"] : "0";

                    //GUARDO EL REGISTRO
                    $registro->save();

                    return true;
                }
            );


            if($response){
                $this->session->set_userdata('updatedBodega', $registro->id);
                redirect(base_url('bodegas/listar'));
            }else{
                //Establecer el mensaje a mostrar
                $data["mensaje"]["clase"] = "alert-danger";
                $data["mensaje"]["contenido"] = "Hubo un error al tratar de editar el pedido.";
            }
    	}
        $bodega_coment = $this->bodegasRep->findByUuid($uuid);
        $bodega_coment->load('comentario_timeline');
    	//Introducir mensaje de error al arreglo
    	//para mostrarlo en caso de haber error
    	$data["message"] = $mensaje;

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
            'public/assets/css/plugins/jquery/jstree/default/style.min.css',
            'public/assets/css/modules/stylesheets/bodegas.css'
        ));

    	$this->assets->agregar_js(array(
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
            'public/assets/js/default/tabla-dinamica.jquery.js',
            'public/assets/js/default/formulario.js',
            'public/assets/js/plugins/jquery/jstree.min.js',
            'public/assets/js/default/lodash.min.js',
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/resources/compile/modulos/bodegas/comentario-bodegas.js'
    	));

        //Agregra variables PHP como variables JS
        $this->assets->agregar_var_js(array(
            "uuid_bodega" => $uuid,
            "bodega_id" => $bodega_coment->id,
            "bodega_coment" => (isset($bodega_coment->comentario_timeline)) ? $bodega_coment->comentario_timeline : ""
        ));


    	$breadcrumb = array(
            "titulo"    => '<i class="fa fa-cubes"></i> Bodega '.$registro->codigo,
            "menu"      => []
    	);

        //Verificar si tiene permiso a la seccion de Crear
        if ($this->auth->has_permission('acceso', 'bodegas/editar')){
            $breadcrumb["menu"]["nombre"]   = "Acci&oacute;n";
            $breadcrumb["menu"]["url"]      = "#";
            $breadcrumb["menu"]["opciones"]["#exportarBtn"] = "Exportar";
        }


        $data["campos"] = array(
            "campos"    => array(
                "codigo"                => $registro->codigo,
                "nombre"                => $registro->nombre,
                "contacto_principal"    => $registro->contacto_principal,
                "direccion"             => $registro->direccion,
                "telefono"              => $registro->telefono,
                "entrada"               => $registro->entrada_id,
                "padre"                 => $registro->padre_id,
                "estado_items_bodega"   => $registro->estado_items,
                "uuid_bodega"           => $registro->uuid_bodega
            ),

        );

    	$this->template->agregar_titulo_header('Bodegas');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar();
    }

}

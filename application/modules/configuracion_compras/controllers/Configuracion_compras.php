<?php
/**
 * Configuración de compras
 *
 * Modulo para administrar la creacion, edicion de configuracion_compras
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
use Carbon\Carbon;
use Flexio\Modulo\Contabilidad\Repository\CuentasRepository;
use Flexio\Modulo\ConfiguracionContabilidad\Repository\CuentaBancoRepository as CuentaBanco;

class Configuracion_compras extends CRM_Controller
{
    protected $empresa;
    protected $id_empresa;
    protected $prefijo;
    protected $id_usuario;
    protected $CuentasRepository;
    protected $cuenta_banco;

    public function __construct() {
        parent::__construct();
        $this->load->module("entradas/Entradas");
        $this->load->model("usuarios/Empresa_orm");

        $this->load->model("configuracion_compras/Chequeras_orm");

        $this->load->model("contabilidad/Cuentas_orm");
        $this->load->model("configuracion_compras/Categorias_proveedores_orm");
        $this->load->model("configuracion_compras/Tipos_proveedores_orm");
        $this->load->model("configuracion_compras/Items_estados_orm");

        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();

        $uuid_empresa       = $this->session->userdata('uuid_empresa');
        $this->empresa      = Empresa_orm::findByUuid($uuid_empresa);
        $this->id_usuario   = $this->session->userdata("id_usuario");
        $this->id_empresa   = $this->empresa->id;

        //PREFIJO DE NOMEMCLATURA DE PEDIDO
        $this->prefijo = "NA"; //No aplica

          $this->CuentasRepository  = new CuentasRepository();
          $this->cuenta_banco = new CuentaBanco;
    }



    public function index() {
        redirect("configuracion_compras/listar");
    }


    public function listar() {
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
            'public/assets/js/modules/configuracion_compras/listar.js',
        ));

    	/*
    	 * Verificar si existe alguna variable de session
    	 * proveniente de algun formulario de crear/editar
    	 */
    	if($this->session->userdata('idTraslado')){
    		//Borrar la variable de session
    		$this->session->unset_userdata('idTraslado');

    		//Establecer el mensaje a mostrar
    		$data["mensaje"]["clase"] = "alert-success";
    		$data["mensaje"]["contenido"] = "Se ha creado el Traslado satisfactoriamente.";
    	}
    	else if($this->session->userdata('updatedTraslado')){
    		//Borrar la variable de session
    		$this->session->unset_userdata('updatedTraslado');

    		//Establecer el mensaje a mostrar
    		$data["mensaje"]["clase"] = "alert-success";
    		$data["mensaje"]["contenido"] = "Se ha actualizado el Traslado satisfactoriamente.";
    	}


        //Breadcrum Array
        $breadcrumb = array(
            "titulo"    => '<i class="fa fa-shopping-cart"></i> Configuraci&oacute;n Compras',
            "ruta" => array(
                0 => array(
                    "nombre" => "Compras",
                    "activo" => false
                ),
                1 => array(
                    "nombre" => '<b>Cat&aacute;logos</b>',
                    "activo" => true
                )
            ),
            "filtro"    => false, //sin vista grid
            "menu"      => array()
        );

        $breadcrumb["menu"]["nombre"] = "Acción";
        $breadcrumb["menu"]["url"] = "";

        //Verificar si tiene permiso de Exportar
        $breadcrumb["menu"]["opciones"]["#exportarBtn"] = "Exportar";
        //Agregra variables PHP como variables JS
        $this->assets->agregar_var_js(array(
            "mensaje_clase"     => isset($data["mensaje"]["clase"]) ? $data["mensaje"]["clase"] : "0",
            "mensaje_contenido" => isset($data["mensaje"]["contenido"]) ? $data["mensaje"]["contenido"] : "0"
        ));

        unset($data["mensaje"]);

	$empresa = ['empresa_id' => $this->id_empresa];

        if($this->cuenta_banco->tieneCuenta($empresa)) {
            $data["cuentas_bancos"] = $this->cuenta_banco->getAll($empresa);
            $data["cuentas_bancos"]->load("cuenta");
	}
         /*$data["cuentas_bancos"]    = Cuentas_orm::cuentasBanco(array("empresa_id"=>$this->id_empresa));*/
         //Estado

     	$this->template->agregar_titulo_header('Cat&aacute;logos de Inventario');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar($breadcrumb);

    }


    public function ajax_cambiar_estado_chequera() {

    	if($this->input->is_ajax_request())
        {

            $uuid       = $this->input->post("uuid", true);
            $estado     = $this->input->post("estado", true);
            $registro   = Chequeras_orm::findByUuid($uuid);

            $response               = array();
            $response["success"]    = false;

            if(count($registro))
            {
                $registro->estado       = $estado;
                $response["success"]    = $registro->save();
            }

            echo json_encode($response);
            exit();
        }

    }

    public function ajax_guardar() {

    	if($this->input->is_ajax_request())
        {

            $uuid       = $this->input->post("uuid", true);

            if($uuid)
            {
                $registro   = Chequeras_orm::findByUuid($uuid);
            }
            else
            {
                $registro                   = new Chequeras_orm;
                $registro->empresa_id       = $this->id_empresa;
                $registro->created_by       = $this->id_usuario;
                $registro->uuid_chequera   = Capsule::raw("ORDER_UUID(uuid())");
            }

            //otros campos
            $registro->nombre          = $this->input->post("nombre", true);
            $registro->cuenta_banco_id          = $this->input->post("cuenta_banco", true);
            $registro->cheque_inicial          = $this->input->post("cheque_inicial", true);
            $registro->cheque_final          = $this->input->post("cheque_final", true);
            $registro->proximo_cheque          = $this->input->post("proximo_cheque", true);
            $registro->ancho          = $this->input->post("ancho", true);
            $registro->alto          = $this->input->post("alto", true);
            $registro->izquierda          = $this->input->post("izquierda", true);
            $registro->derecha          = $this->input->post("derecha", true);
            $registro->arriba          = $this->input->post("arriba", true);
            $registro->abajo          = $this->input->post("abajo", true);
            $registro->posicion          = $this->input->post("posicion", true);

            $response["success"]        = $registro->save();


            echo json_encode($response);
            exit();
        }

    }

    public function ajax_get_chequera() {

    	if($this->input->is_ajax_request())
        {

            $uuid                   = $this->input->post("uuid", true);
            $registro               = Chequeras_orm::findByUuid($uuid);
            $response["success"]    = false;

            if(count($registro))
            {
                $response["success"]    = true;
                $response["registro"]   = $registro;
            }


            echo json_encode($response);
            exit();
        }

    }

    public function ajax_listar_chequeras() {
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
            $registros = Chequeras_orm::deEmpresa($this->id_empresa);


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
                    $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-uuid="'. $row->uuid_chequera .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

                    $hidden_options.= '<a href="#" class="btn btn-block btn-outline btn-success editarChequera" data-uuid="'. $row->uuid_chequera .'">Ver Detalle</a>';


                    if($row->estado == "1")//activo
                    {
                        $hidden_options.= '<a href="#" class="btn btn-block btn-outline btn-success desactivarChequera" data-uuid="'. $row->uuid_chequera .'">Inactivar</a>';
                        $estado="Activa";
                    }
                    else
                    {
                        $hidden_options.= '<a href="#" class="btn btn-block btn-outline btn-success activarChequera" data-uuid="'. $row->uuid_chequera .'">Activar</a>';
                        $estado="Inactiva";
                    }

                    //Si no tiene acceso a ninguna opcion
                    //ocultarle el boton de opciones
                    if($hidden_options == ""){
                            $link_option = "&nbsp;";
                    }



                    $response->rows[$i]["id"]   = $row->uuid_chequera;
                    $response->rows[$i]["cell"] = array(
                        $row->nombre,
                        $row->cheque_inicial,
                        $row->cheque_final,
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
    public function ajax_listar_categorias() {
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
            $registros = Categorias_proveedores_orm::deEmpresa($this->id_empresa);


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
                    $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-uuid="'. $row->uuid_categoria .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

                    $hidden_options.= '<a href="#" class="btn btn-block btn-outline btn-success editarCategoria" data-uuid="'. $row->uuid_categoria .'">Editar</a>';


                    if($row->estado == "19")
                    {
                       // $estado = "Activo";
                        $hidden_options.= '<a href="#" class="btn btn-block btn-outline btn-success desactivarCategoria" data-uuid="'. $row->uuid_categoria .'">Desactivar</a>';
                    }
                    else
                    {
                        //$estado = "Inactivo";
                        $hidden_options.= '<a href="#" class="btn btn-block btn-outline btn-success activarCategoria" data-uuid="'. $row->uuid_categoria .'">Activar</a>';
                    }

                    //Si no tiene acceso a ninguna opcion
                    //ocultarle el boton de opciones
                    if($hidden_options == ""){
                        $link_option = "&nbsp;";
                    }



                    $response->rows[$i]["id"]   = $row->uuid_categoria;
                    $response->rows[$i]["cell"] = array(
                        $row->nombre,
                        $row->descripcion,
                        $row->estadoReferencia->etiqueta,
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
    public function ajax_listar_tipos() {
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
            $registros = Tipos_proveedores_orm::deEmpresa($this->id_empresa);


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
                    //dd($registros->get());
                    $uuid = bin2hex($row->uuid_tipo);
                    $hidden_options = "";
                    $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-uuid="'. $uuid .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

                    $hidden_options.= '<a href="#" class="btn btn-block btn-outline btn-success editarTipos" data-uuid="'. $uuid .'">Editar</a>';


                    if($row->estado == "19")
                    {
                        // $estado = "Activo";
                        $hidden_options.= '<a href="#" class="btn btn-block btn-outline btn-success desactivarTipos" data-uuid="'. $uuid .'">Desactivar</a>';
                    }
                    else
                    {
                        //$estado = "Inactivo";
                        $hidden_options.= '<a href="#" class="btn btn-block btn-outline btn-success activarTipos" data-uuid="'. $uuid .'">Activar</a>';
                    }

                    //Si no tiene acceso a ninguna opcion
                    //ocultarle el boton de opciones
                    if($hidden_options == ""){
                        $link_option = "&nbsp;";
                    }



                    $response->rows[$i]["id"]   = $uuid;
                    $response->rows[$i]["cell"] = array(
                        $row->nombre,
                        $row->descripcion,
                        $row->estadoReferencia->etiqueta,
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
    public function ajax_guardar_categorias() {

        if($this->input->is_ajax_request())
        {

            $uuid       = $this->input->post("uuid", true);

            if($uuid)
            {
                $registro   = Categorias_proveedores_orm::findByUuid($uuid);
            }
            else
            {

                $registro                   = new Categorias_proveedores_orm;
                $registro->id_empresa       = $this->id_empresa;
                $registro->creado_por       = $this->id_usuario;
                $registro->uuid_categoria   = Capsule::raw("ORDER_UUID(uuid())");

            }

            $nombre         = $this->input->post("categoria", true);
            $descripcion    = $this->input->post("descripcion", true);
           // dd($nombre.$descripcion);

            $registro->nombre           = $nombre;
            $registro->descripcion      = $descripcion;

            $response["success"]        = $registro->save();

            echo json_encode($response);
            exit();
        }
    }
    public function  ajax_guardar_tipos() {
        if($this->input->is_ajax_request())
        {

            $uuid       = $this->input->post("uuid", true);

            if($uuid)
            {
                $registro   = Tipos_proveedores_orm::findByUuid($uuid);
            }
            else
            {

                $registro                   = new Tipos_proveedores_orm;
                $registro->id_empresa       = $this->id_empresa;
                $registro->creado_por       = $this->id_usuario;
                $registro->uuid_tipo   = Capsule::raw("ORDER_UUID(uuid())");

            }

            $nombre         = $this->input->post("tipo", true);
            $descripcion    = $this->input->post("descripcion", true);
            // dd($nombre.$descripcion);

            $registro->nombre           = $nombre;
            $registro->descripcion      = $descripcion;

            $response["success"]        = $registro->save();

            echo json_encode($response);
            exit();
        }
    }

    public function ajax_get_categoria() {

        if($this->input->is_ajax_request())
        {

            $uuid                   = $this->input->post("uuid", true);
            $registro               = Categorias_proveedores_orm::findByUuid($uuid);
            $response["success"]    = false;

            if(count($registro))
            {
                $response["success"]    = true;
                $response["registro"]   = $registro;
            }


            echo json_encode($response);
            exit();
        }

    }
    public function ajax_get_tipos() {

        if($this->input->is_ajax_request())
        {

            $uuid                   = $this->input->post("uuid", true);
            $registro               = Tipos_proveedores_orm::findByUuid($uuid);
           // dd($registro->nombre);
            $response["success"]    = false;

            if(count($registro))
            {
                $response["success"]    = true;
               // $response["registro"] = $registro;
                $response["nombre"]   = $registro->nombre;
                $response["descripcion"]   = $registro->descripcion;
                $response["uuid"]   = bin2hex($registro->uuid_tipo);
            }


            echo json_encode($response);
            exit();
        }

    }
    public function ajax_cambiar_estado_categoria() {

        if($this->input->is_ajax_request())
        {

            $uuid       = $this->input->post("uuid", true);
            $estado     = $this->input->post("estado", true);
            $registro   = Categorias_proveedores_orm::findByUuid($uuid);

            $response               = array();
            $response["success"]    = false;

            if(count($registro))
            {
                $registro->estado       = $estado;
                $response["success"]    = $registro->save();
            }

            echo json_encode($response);
            exit();
        }

    }
    public function ajax_cambiar_estado_tipos() {

        if($this->input->is_ajax_request())
        {

            $uuid       = $this->input->post("uuid", true);
            $estado     = $this->input->post("estado", true);
            $registro   = Tipos_proveedores_orm::findByUuid($uuid);

            $response               = array();
            $response["success"]    = false;

            if(count($registro))
            {
                $registro->estado       = $estado;
                $response["success"]    = $registro->save();
            }

            echo json_encode($response);
            exit();
        }

    }
    /**
     * Cargar Vista Parcial de Tabla
     *
     * @return void
     */
    public function ocultotablaChequeras() {
        //If ajax request
    	$this->assets->agregar_js(array(
            'public/assets/js/modules/configuracion_compras/tablaChequeras.js',
    	));

    	$this->load->view('tablaChequeras');
    }

    /**
     * Cargar Vista Parcial de Tabla categorias
     *
     * @return void
     */
    public function ocultotablaCategoria() {
        //If ajax request
        $this->assets->agregar_js(array(
            'public/assets/js/modules/configuracion_compras/tablaCategorias.js',
        ));

        $this->load->view('tablaCategorias');
    }
    /**
     * Cargar Vista Parcial de Tabla de tipos
     *
     * @return void
     */
    public function ocultotablaTipo() {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/configuracion_compras/tablaTipos.js',
        ));

        $this->load->view('tablaTipos');

    }

    public function exportar() {
        if (empty($_POST)) {
            exit();
        }
        $id = $this->input->post('ids', true);
        $ids = explode(",", $id);
        $tabla = $this->input->post('tabla', true);

        if (empty($ids)) {
            return false;
        }

        $uuid_tabla = collect($ids);
       // dd($uuid_tabla);
        $uuid_tabla->transform(function ($item) {
            return hex2bin($item);
        });
        $uuuid = $uuid_tabla->toArray();

        if($tabla == 'categoria'){
            $categotias = Categorias_proveedores_orm::exportar($uuuid);
           // dd($categotias);
            if (empty($categotias)) {
                return false;
            }
            $i = 0;
            foreach ($categotias as $row) {
                $datos[$i]['nombre'] = utf8_decode(Util::verificar_valor( utf8_decode($row['nombre'])));
                $datos[$i]['descripcion'] = Util::verificar_valor( utf8_decode($row['descripcion']));
                $datos[$i]['estado'] = Util::verificar_valor($row['estadoReferencia']['etiqueta']);
                $i++;
            }
            $csv = Writer::createFromFileObject(new SplTempFileObject());
            $csv->insertOne([
                'Nombre',
                utf8_decode('Descripción'),
                'Estatus'
            ]);
            $csv->insertAll($datos);
            $csv->output("Categorias_Proveedores-" . date('ymd') . ".csv");
            exit();
        }elseif ($tabla == 'chequera'){
            $chequeras = Chequeras_orm::exportar($uuuid);
            if (empty($chequeras)) {
                return false;
            }
            $j = 0;
            foreach ($chequeras as $row) {
                $datos[$j]['nombre'] = utf8_decode(Util::verificar_valor( utf8_decode($row['nombre'])));
                $datos[$j]['cheque_inicial'] = Util::verificar_valor($row['cheque_inicial']);
                $datos[$j]['cheque_final'] = Util::verificar_valor($row['cheque_final']);
                if($row['estado']=='1') {$estatus = 'Activo';}else {$estatus = 'Inactivo';}
                $datos[$j]['estado'] = $estatus;
                $j++;
            }
            $csv = Writer::createFromFileObject(new SplTempFileObject());
            $csv->insertOne([
                'Nombre de Chequera',
                utf8_decode('Número de cheque inicial'),
                utf8_decode('Número de cheque final'),
                'Estatus'
            ]);
            $csv->insertAll($datos);
            $csv->output("Compras_Chequeras-" . date('ymd') . ".csv");
            exit();
        }else{
            $tipos = Tipos_proveedores_orm::exportar($uuuid);
            // dd($categotias);
            if (empty($tipos)) {
                return false;
            }
            $i = 0;
            foreach ($tipos as $row) {
                $datos[$i]['nombre'] = utf8_decode(Util::verificar_valor( utf8_decode($row['nombre'])));
                $datos[$i]['descripcion'] = Util::verificar_valor( utf8_decode($row['descripcion']));
                $datos[$i]['estado'] = Util::verificar_valor($row['estadoReferencia']['etiqueta']);
                $i++;
            }
            $csv = Writer::createFromFileObject(new SplTempFileObject());
            $csv->insertOne([
                'Nombre',
                utf8_decode('Descripción'),
                'Estatus'
            ]);
            $csv->insertAll($datos);
            $csv->output("Tipos_Proveedor-" . date('ymd') . ".csv");
            exit();
        }

    }


}

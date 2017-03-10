<?php
/**
 * Configuración de compras
 *
 * Modulo para administrar la creacion, edicion de configuracion de clientes.
 *
 * @package    PensaApp
 * @subpackage Controller
 * @category   Controllers
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  10/06/2016
 */
use Illuminate\Database\Capsule\Manager as Capsule;
use League\Csv\Writer as Writer;
use Carbon\Carbon;
use Flexio\Modulo\ConfiguracionVentas\Repository\CategoriaClienteRepository as CategoriaClienteRepository;
use Flexio\Modulo\ConfiguracionVentas\Repository\TipoClienteRepository as TipoClienteRepository;
use Flexio\Modulo\ConfiguracionVentas\Models\TipoClientes as TipoClientes;
use Flexio\Modulo\ConfiguracionVentas\Models\CategoriaClientes as CategoriaClientes;

class Configuracion_ventas extends CRM_Controller
{
    protected $empresa;
    protected $id_empresa;
    protected $id_usuario;
    protected $categoriaClienteRepository;
    protected  $tipoClienteRepository;
    protected $listarCategorias;
    protected $listarTipos;
    public function __construct()
    {
        parent::__construct();
        $this->load->model("usuarios/Empresa_orm");
        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();
        $uuid_empresa       = $this->session->userdata('uuid_empresa');
        $this->empresa      = Empresa_orm::findByUuid($uuid_empresa);
        $this->id_usuario   = $this->session->userdata("id_usuario");
        $this->id_empresa   = $this->empresa->id;

        $this->categoriaClienteRepository = new CategoriaClienteRepository();
        $this->tipoClienteRepository = new TipoClienteRepository();
    }
    public function index() {
        redirect("configuracion_ventas/listar");
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
            'public/assets/css/plugins/jquery/fileinput/fileinput.css',
            //select2
            'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
            'public/assets/css/plugins/bootstrap/select2.min.css',
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

            /* Archivos js para la vista de Crear Actividades */
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/plugins/bootstrap/bootstrap-tabdrop.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/toastr.min.js',
            //select2
            'public/assets/js/plugins/bootstrap/select2/select2.min.js',
            'public/assets/js/plugins/bootstrap/select2/es.js',
            'public/assets/js/default/formulario.js',

            /* Archivos js del propio modulo*/
            'public/assets/js/modules/configuracion_ventas/listar.js',
        ));
        //Breadcrum Array
        $breadcrumb = array(
            "titulo"    => '<i class="fa fa-cogs"></i> Configuraci&oacute;n Ventas',
            "ruta" => array(
                0 => array(
                    "nombre" => "Ventas",
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

        $this->template->agregar_titulo_header('Cat&aacute;logos de Ventas');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }

    /**
     * Cargar Vista Parcial de Tabla categorias
     *
     * @return void
     */
    public function ocultotablaCategoria() {
        //If ajax request
        $this->assets->agregar_js(array(
            'public/assets/js/modules/configuracion_ventas/tablaCategorias.js',
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
            'public/assets/js/modules/configuracion_ventas/tablaTipos.js',
        ));

        $this->load->view('tablaTipos');

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
            $this->listarCategorias = new CategoriaClientes();

            $registros = $this->listarCategorias->deEmpresa($this->id_empresa);


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


                    if($row->estado == "activo")
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
                        $row->estadoReferencia->valor,
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
            $this->listarTipos = new TipoClientes();
            $registros =$this->listarTipos->deEmpresa($this->id_empresa);


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
                    $uuid = $row->uuid_tipo;
                    $hidden_options = "";
                    $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-uuid="'. $uuid .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

                    $hidden_options.= '<a href="#" class="btn btn-block btn-outline btn-success editarTipos" data-uuid="'. $uuid .'">Editar</a>';


                    if($row->estado == "activo")
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
                        $row->estadoReferencia->valor,
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
            $nombre         = $this->input->post("categoria", true);
            $descripcion    = $this->input->post("descripcion", true);
            $uuid       = $this->input->post("uuid", true);
           // dd($uuid);
            if($uuid)
            {
                $registro   = $this->categoriaClienteRepository->findByUuid($uuid);
               // dd($registro);
            }
            else
            {
                //dd("Nuevo");
                $registro                   = new CategoriaClientes();
                $registro->id_empresa       = $this->id_empresa;
                $registro->creado_por       = $this->id_usuario;
                $registro->uuid_categoria   = Capsule::raw("ORDER_UUID(uuid())");
                //dd($registro);
            }

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
            $nombre         = $this->input->post("tipo", true);
            $descripcion    = $this->input->post("descripcion", true);

            if($uuid)
            {
                $registro   = $this->tipoClienteRepository->findByUuid($uuid);
            }
            else
            {

                $registro                   = new TipoClientes();
                $registro->id_empresa       = $this->id_empresa;
                $registro->creado_por       = $this->id_usuario;
                $registro->uuid_tipo   = Capsule::raw("ORDER_UUID(uuid())");

            }

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
            $registro               = $this->categoriaClienteRepository->findByUuid($uuid);
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
            $registro               = $this->tipoClienteRepository->findByUuid($uuid);
            // dd($registro->nombre);
            $response["success"]    = false;

            if(count($registro))
            {
                $response["success"]    = true;
                // $response["registro"] = $registro;
                $response["nombre"]   = $registro->nombre;
                $response["descripcion"]   = $registro->descripcion;
                $response["uuid"]   = $registro->uuid_tipo;
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
            $registro   = $this->categoriaClienteRepository->findByUuid($uuid);

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
           // dd($uuid);
            $registro   = $this->tipoClienteRepository->findByUuid($uuid);

            //dd($registro);
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
            $csv->output("Categorias_Clientes-" . date('ymd') . ".csv");
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
            $csv->output("Tipos_Clientes-" . date('ymd') . ".csv");
            exit();
        }

    }
}

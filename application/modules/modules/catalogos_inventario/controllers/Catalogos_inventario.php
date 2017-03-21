<?php
/**
 * Ajustes
 *
 * Modulo para administrar la creacion, edicion de catalogos_inventario
 *
 * @package    PensaApp
 * @subpackage Controller
 * @category   Controllers
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  10/16/2015
 */

use Illuminate\Database\Capsule\Manager as Capsule;

//repositories
use Flexio\Modulo\Ajustes\Repository\AjustesRazonesRepository as ajustesRazonesRep;

//utils
use Flexio\Library\Util\FlexioAssets;
use Flexio\Library\Util\FlexioSession;
use Flexio\Library\Toast;

class Catalogos_inventario extends CRM_Controller
{
    protected $empresa;
    protected $id_empresa;
    protected $prefijo;
    protected $id_usuario;

    //repositories
    private $ajustesRazonesRep;

    //utils
    protected $FlexioAssets;
    protected $FlexioSession;
    protected $Toast;

    public function __construct() {
        parent::__construct();
        $this->load->module("entradas/Entradas");
        $this->load->model("usuarios/Empresa_orm");

        $this->load->model("inventarios/Categorias_orm");
        $this->load->model("inventarios/Precios_orm");
        $this->load->model("inventarios/Unidades_orm");
        $this->load->model("inventarios/Items_estados_orm");

        $this->load->model("contabilidad/Cuentas_orm");

        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();

        $uuid_empresa       = $this->session->userdata('uuid_empresa');
        $this->empresa      = Empresa_orm::findByUuid($uuid_empresa);
        $this->id_usuario   = $this->session->userdata("id_usuario");
        $this->id_empresa   = $this->empresa->id;

        //PREFIJO DE NOMEMCLATURA DE PEDIDO
        $this->prefijo = "NA"; //No aplica

        //repositories
        $this->ajustesRazonesRep = new ajustesRazonesRep();

        //utils
        $this->FlexioAssets = new FlexioAssets();
        $this->FlexioSession = new FlexioSession();
        $this->Toast = new Toast();
    }



    public function index() {
        redirect("catalogos_inventario/listar");
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
            //'public/assets/css/plugins/jquery/toastr.min.css',
            'public/assets/css/modules/stylesheets/catalogo_inventario.css'
        ));

        $this->assets->agregar_js(array(
            //'public/assets/js/default/jquery-ui.min.js',
            //'public/assets/js/plugins/jquery/jquery.sticky.js',
           // 'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            //'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            //'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/moment-with-locales-290.js',
            //'public/assets/js/plugins/jquery/jqgrid/plugins/jQuery.jqGrid.columnToggle.js',
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
            'public/assets/js/default/jquery.inputmask.bundle.min.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/plugins/bootstrap/bootstrap-tabdrop.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
            //'public/assets/js/plugins/toastr.min.js',
            'public/assets/js/default/formulario.js',

            /* Archivos js del propio modulo*/

            'public/assets/js/modules/catalogos_inventario/listar.js',
            'public/assets/js/modules/catalogos_inventario/formulario_categoria_items.js',
            'public/assets/js/modules/catalogos_inventario/routes.js',
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
            "titulo"    => '<i class="fa fa-cubes"></i> Configuraci&oacute;n de inventario',
            "ruta" => array(
                0 => array(
                    "nombre" => "Inventarios",
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


        //Agregra variables PHP como variables JS
        $this->assets->agregar_var_js(array(
            "mensaje_clase"     => isset($data["mensaje"]["clase"]) ? $data["mensaje"]["clase"] : "0",
            "mensaje_contenido" => isset($data["mensaje"]["contenido"]) ? $data["mensaje"]["contenido"] : "0"
        ));

        unset($data["mensaje"]);

        //Activos
        $data["activos"]    = Cuentas_orm::transaccionalesDeEmpresa($this->id_empresa)
                            ->activas()
                            ->deTipoDeCuenta(array(1))
                            ->get();
        //Ingresos
        $data["ingresos"]    = Cuentas_orm::transaccionalesDeEmpresa($this->id_empresa)
                            ->activas()
                            ->deTipoDeCuenta(array(4))
                            ->get();
        //Gasto
        $data["gastos"]     = Cuentas_orm::transaccionalesDeEmpresa($this->id_empresa)
                            ->activas()
                            ->deTipoDeCuenta(array(5))
                            ->get();
        //Variante
        $data["variantes"]  = Cuentas_orm::transaccionalesDeEmpresa($this->id_empresa)
                            ->activas()
                            ->deTipoDeCuenta(array(5))
                            ->get();

        //Estado
        $data["estados"]    = Items_estados_orm::estados()->get();

        $data['categoria_cuentas'] = $this->catalogo_cuentas();


    	$this->template->agregar_titulo_header('Cat&aacute;logos de Inventario');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar($breadcrumb);

    }


    public function ajax_cambiar_estado_categoria() {

    	if($this->input->is_ajax_request())
        {

            $uuid       = $this->input->post("uuid", true);
            $estado     = $this->input->post("estado", true);
            $registro   = Categorias_orm::findByUuid($uuid);

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

    public function ajax_cambiar_estado_precio() {

    	if($this->input->is_ajax_request())
        {

            $uuid       = $this->input->post("uuid", true);
            $estado     = $this->input->post("estado", true);
            $registro   = Precios_orm::findByUuid($uuid);

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

    public function ajax_cambiar_estado_unidad() {

    	if($this->input->is_ajax_request())
        {

            $uuid       = $this->input->post("uuid", true);
            $estado     = $this->input->post("estado", true);
            $registro   = Unidades_orm::findByUuid($uuid);

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

    public function ajax_cambiar_estado_razon() {

    	if(!$this->input->is_ajax_request())
        {
            return false;
        }

        $post                   = $this->input->post();
        $response               = array();
        $response["success"]    = $this->ajustesRazonesRep->cambiar_estado($post);

        echo json_encode($response);
        exit();
    }

    public function ajax_guardar() {

    	if($this->input->is_ajax_request())
        {

            $FormRequest = new Flexio\Modulo\Inventarios\HttpRequest\FormGuardarCategoria;

           try{
             $categoria = $FormRequest->guardar();
             $mensaje = array('tipo'=>"success", 'mensaje'=>'<b>¡&Eacute;xito!</b> Se ha guardado correctamente' ,'titulo'=>'Categoria '.$categoria->nombre);
           }catch(\Exception $e){
             log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
             $mensaje = array('tipo' => 'error', 'mensaje' => '<b>¡Error! Su solicitud no fue procesada</b>', 'titulo' => "Categoria");
          }

          $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
       ->set_output(json_encode($mensaje))->_display();
       exit;

        }

    }

    public function ajax_guardar_precio() {

    	if($this->input->is_ajax_request())
        {

            $uuid       = $this->input->post("uuid", true);

            if($uuid)
            {
                $registro   = Precios_orm::findByUuid($uuid);
            }
            else
            {
              $registro                   = new Precios_orm;
              $registro->empresa_id       = $this->id_empresa;
              $registro->created_by       = $this->id_usuario;
              $registro->uuid_precio      = Capsule::raw("ORDER_UUID(uuid())");
            }

            //otros campos
            $nombre         = $this->input->post("nombre", true);
            $descripcion    = $this->input->post("descripcion", true);
            $estado         = $this->input->post("estado", true);
            $tipo          = $this->input->post("tipo", true);

            $registro->nombre           = $nombre;
            $registro->descripcion      = $descripcion;
            $registro->estado           = $estado;
            $registro->tipo_precio      = $tipo;

            $response["success"]        = $registro->save();


            echo json_encode($response);
            exit();
        }

    }

    public function ajax_guardar_unidad() {

    	if($this->input->is_ajax_request())
        {

            $uuid       = $this->input->post("uuid", true);

            if($uuid)
            {
                $registro   = Unidades_orm::findByUuid($uuid);
            }
            else
            {
                $registro                   = new Unidades_orm;
                $registro->empresa_id       = $this->id_empresa;
                $registro->created_by       = $this->id_usuario;
                $registro->uuid_unidad      = Capsule::raw("ORDER_UUID(uuid())");
            }

            //otros campos
            $nombre         = $this->input->post("nombre", true);
            $descripcion    = $this->input->post("descripcion", true);
            $estado         = $this->input->post("estado", true);

            $registro->nombre           = $nombre;
            $registro->descripcion      = $descripcion;
            $registro->estado           = $estado;

            $response["success"]        = $registro->save();


            echo json_encode($response);
            exit();
        }

    }

    public function ajax_guardar_razon() {

    	if(!$this->input->is_ajax_request())
        {
            return false;
        }

        $post                   = $this->input->post();
        $post["empresa_id"]     = $this->id_empresa;
        $post["usuario_id"]     = $this->id_usuario;

        $response["success"]    = $this->ajustesRazonesRep->save($post);

        echo json_encode($response);
        exit();
    }

    public function ajax_get_categoria() {

    	if($this->input->is_ajax_request())
        {

            $uuid                   = $this->input->post("uuid", true);
            $registro               = Categorias_orm::findByUuid($uuid);
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

    public function ajax_get_precio() {

    	if($this->input->is_ajax_request())
        {

            $uuid                   = $this->input->post("uuid", true);
            $registro               = Precios_orm::findByUuid($uuid);
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

    public function ajax_get_unidad() {

    	if($this->input->is_ajax_request())
        {

            $uuid                   = $this->input->post("uuid", true);
            $registro               = Unidades_orm::findByUuid($uuid);
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

    public function ajax_get_razon() {

    	if(!$this->input->is_ajax_request())
        {
            return false;
        }

        $uuid                   = $this->input->post("uuid", true);
        $registro               = $this->ajustesRazonesRep->findByUuid($uuid);
        $response["success"]    = false;

        if(count($registro))
        {
            $response["success"]    = true;
            $response["registro"]   = $registro;
        }

        echo json_encode($response);
        exit();
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
            $registros = Categorias_orm::deEmpresa($this->id_empresa);


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
                    $hidden_options.= '<a href="'.base_url("catalogos_inventario/datos_adicionales/".$row->uuid_categoria).'" class="btn btn-block btn-outline btn-success">Datos adicionales</a>';


                    if($row->estado == "1")//activo
                    {
                        $hidden_options.= '<a href="#" class="btn btn-block btn-outline btn-success desactivarCategoria" data-uuid="'. $row->uuid_categoria .'">Desactivar</a>';
                    }
                    else
                    {
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
                        $row->depreciacion_meses=="0"?'N/A':$row->depreciacion_meses,
                        $row->porcentaje_depreciacion =="0"?'N/A':$row->porcentaje_depreciacion,
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

    public function ajax_listar_datos_adicionales()
    {
        if(!$this->input->is_ajax_request())return false;

        $clause = array('empresa' => $this->id_empresa);
        $datos_adicional = new \Flexio\Modulo\Inventarios\Models\DatoAdicional;
        $jqgrid = new Flexio\Modulo\Inventarios\Services\DatoAdicionalJqgrid($datos_adicional);
        $response = $jqgrid->listar($clause);

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))->_display();
        exit;
    }

    public function ajax_get_dato_adicional()
    {
        if(!$this->input->is_ajax_request())return false;

        $dato_adicional = \Flexio\Modulo\Inventarios\Models\DatoAdicional::find($this->input->post('id'));
        $response = [
            'success' => count($dato_adicional) ? true : false,
            'data' => $dato_adicional
        ];

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))->_display();
        exit;
    }

    public function ajax_get_datos_adicionales()
    {
        if(!$this->input->is_ajax_request())return false;
        $clause = ['categoria' => $this->input->post('categoria_id'), 'estado' => 'activo'];
        $datos_adicionales = \Flexio\Modulo\Inventarios\Models\DatoAdicional::deFiltro($clause)->get()->map(function($dato_adicional){
            return ['llave' => $dato_adicional->nombre, 'valor' => ''];
        });
        $response = [
            'success' => count($datos_adicionales) ? true : false,
            'data' => $datos_adicionales
        ];
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))->_display();
        exit;
    }

    public function ajax_guardar_dato_adicional()
    {
        $errors = "";
        try {
            $GuardarDatoAdicional = new \Flexio\Modulo\Inventarios\FormRequest\GuardarDatoAdicional();
            $dato_adicional = $GuardarDatoAdicional->guardar();
        } catch (\Exception $e) {
            $errors .= $e->getMessage()."<br>";
        }

        echo json_encode(array(
            'response' => strlen($errors) ? false : true,
            'mensaje' => strlen($errors) ? $errors : 'Se actualiz&oacute; el elemento correctamente.'
        ));
        exit;
    }

    public function ajax_exportar_datos_adicionales()
    {
        $clause = [];
        $clause['ids'] = $this->input->post('ids', true);
        $datos_adicionales = \Flexio\Modulo\Inventarios\Models\DatoAdicional::whereIn('id', $clause['ids'])->get()
        ->map(function($dato_adicional){
            return [
                'nombre' => $dato_adicional->nombre,
                'requerido' => $dato_adicional->requerido,
                'en_busqueda_avanzada' => $dato_adicional->en_busqueda_avanzada,
                'estado' => $dato_adicional->estado
            ];
        });

        $csv = \League\Csv\Writer::createFromFileObject(new \SplTempFileObject());
        $csv->insertOne(['Nombre del campo', 'Requerido', utf8_decode('En búsqueda avanzada'), 'Estado']);
        $csv->insertAll($datos_adicionales);

        $csv->output('datos_adicionales.csv');
        exit;
    }

    public function ajax_get_states_segment()
    {
        $id = $this->input->post('id');
        $dato_adicional = \Flexio\Modulo\Inventarios\Models\DatoAdicional::find($id);
        $data = ['dato_adicional' => $dato_adicional, 'id' => $id];
        $response = ['data' => $this->load->view('segments/states', $data, true)];
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))->_display();
        exit;
    }

    public function ajax_update_state()
    {
        $id = $this->input->post('id');
        $ids = is_array($id) ? $id : [$id];
        $errors = "";

        foreach($ids as $dato_id){
            try {
                $GuardarDatoAdicional = new \Flexio\Modulo\Inventarios\FormRequest\GuardarDatoAdicional();
                $GuardarDatoAdicional->guardar(['estado' => $this->input->post('estado'), 'id' => $dato_id]);
            } catch (\Exception $e) {
                $errors .= $e->getMessage()."<br>";
            }
        }
        echo json_encode(array(
            'response' => strlen($errors) ? false : true,
            'mensaje' => strlen($errors) ? $errors : 'Se actualiz&oacute; el estado correctamente.'
        ));
        exit;
    }

    public function datos_adicionales($uuid = null)
    {
        //permisos
        $acceso = $this->auth->has_permission('acceso', 'catalogos_inventario/datos_adicionales/(:any)');
        $this->Toast->runVerifyPermission($acceso);

        //Cargo el registro y repository
        $categoria = \Flexio\Modulo\Inventarios\Models\Categoria::where('uuid_categoria', hex2bin($uuid))->first();
        $categoriaRepository = new \Flexio\Modulo\Inventarios\Repository\CategoriasRepository;

        //assets
        $this->FlexioAssets->run(); //css y js generales
        $this->FlexioAssets->add('js',['public/resources/compile/modulos/catalogos_inventario/datos_adicionales.js']);
        $this->FlexioAssets->add('vars', [
            'vista' => 'main',
            'acceso' => $acceso ? 1 : 0,
            'categoria' => $categoriaRepository->getCollectionCategoria($categoria)
        ]);

        //breadcrumb
        $breadcrumb = [
            'titulo' => '<i class="fa fa-cubes"></i> Inventario: Datos adicionales',
            'menu' => [
                'nombre' => 'Acción',
                'url' => '#',
                'opciones' => [
                    '#cambiar-estado-btn' => '<i class="fa fa-compass"></i> Cambiar estados</a>',
                    '#toCSV' => '<i class="fa fa-download"></i> Exportar'
                ],
            ],
        ];

        //render
        $this->template->agregar_titulo_header('Inventario: Datos adicionales');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido([]);
        $this->template->visualizar();
    }

    public function ajax_listar_precios() {
    	//Just Allow ajax request
    	if($this->input->is_ajax_request())
    	{
            /**
             * Get the requested page.
             * @var int
             */
            $tipo =  $this->input->post('tipo', true);

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
            if($tipo == 'venta')
              $registros = Precios_orm::deEmpresa($this->id_empresa)->deTipoVenta();
            else if($tipo == 'alquiler')
              $registros = Precios_orm::deEmpresa($this->id_empresa)->deTipoAlquiler();


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
                    $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-uuid="'. $row->uuid_precio .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

                    if($row->tipo_precio == 'venta')
                      $hidden_options.= '<a href="#" class="btn btn-block btn-outline btn-success editarPrecio" data-uuid="'. $row->uuid_precio .'">Editar</a>';
                    else {
                      $hidden_options.= '<a href="#" class="btn btn-block btn-outline btn-success editarPrecioAlquiler" data-uuid="'. $row->uuid_precio .'">Editar</a>';
                    }
                    $label_precio_principal = $row->principal == 1 ? '<span class="label label-warning">Default</span>' : "";

                    if($row->estado == "1")//activo
                    {
                        $hidden_options.= '<a href="#" class="btn btn-block btn-outline btn-success desactivarPrecio" data-uuid="'. $row->uuid_precio .'">Desactivar</a>';
                    }
                    else
                    {
                        $hidden_options.= '<a href="#" class="btn btn-block btn-outline btn-success activarPrecio" data-uuid="'. $row->uuid_precio .'">Activar</a>';
                    }
                    //Si no tiene acceso a ninguna opcion
                    //ocultarle el boton de opciones
                    if($hidden_options == ""){
                            $link_option = "&nbsp;";
                    }
                    $response->rows[$i]["id"]   = $row->uuid_precio;
                    $response->rows[$i]["cell"] = array(
                        $row->principal,
                        $row->nombre.' '.$label_precio_principal,
                        $row->descripcion,
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

    public function ajax_listar_razones_ajustes() {
        if(!$this->input->is_ajax_request()){
            exit;
        }

        $clause                 = $this->input->post();
        $clause["empresa_id"]   = $this->id_empresa;

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = $this->ajustesRazonesRep->count($clause);
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $entradas = $this->ajustesRazonesRep->get($clause, $sidx, $sord, $limit, $start);

        $response           = new stdClass();
        $response->page     = $page;
        $response->total    = $total_pages;
        $response->records  = $count;


        if($count){
            foreach($entradas as $i => $row){

                $hidden_options = "";
                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-uuid="'. $row->uuid_razon .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

                $hidden_options.= '<a href="#" class="btn btn-block btn-outline btn-success editarRazon" data-uuid="'. $row->uuid_razon .'">Editar</a>';


                if($row->estado_id == "6")//activo
                {
                    $hidden_options.= '<a href="#" class="btn btn-block btn-outline btn-success desactivarRazon" data-uuid="'. $row->uuid_razon .'">Inactivar</a>';
                }
                else
                {
                    $hidden_options.= '<a href="#" class="btn btn-block btn-outline btn-success activarRazon" data-uuid="'. $row->uuid_razon .'">Activar</a>';
                }

                //Si no tiene acceso a ninguna opcion
                //ocultarle el boton de opciones
                if($hidden_options == ""){
                        $link_option = "&nbsp;";
                }

                $response->rows[$i]["id"]   = $row->uuid_razon;
                $response->rows[$i]["cell"] = [
                    $row->nombre,
                    $row->descripcion,
                    $row->present()->estado_ajuste,
                    $link_option,
                    $hidden_options
                ];
            }
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))->_display();
        exit;
    }

    public function ajax_listar_unidades() {
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
            $registros = Unidades_orm::deEmpresa($this->id_empresa);


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
                    $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-uuid="'. $row->uuid_unidad .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

                    $hidden_options.= '<a href="#" class="btn btn-block btn-outline btn-success editarUnidad" data-uuid="'. $row->uuid_unidad .'">Editar</a>';


                    if($row->estado == "1")//activo
                    {
                        $hidden_options.= '<a href="#" class="btn btn-block btn-outline btn-success desactivarUnidad" data-uuid="'. $row->uuid_unidad .'">Desactivar</a>';
                    }
                    else
                    {
                        $hidden_options.= '<a href="#" class="btn btn-block btn-outline btn-success activarUnidad" data-uuid="'. $row->uuid_unidad .'">Activar</a>';
                    }

                    //Si no tiene acceso a ninguna opcion
                    //ocultarle el boton de opciones
                    if($hidden_options == ""){
                            $link_option = "&nbsp;";
                    }



                    $response->rows[$i]["id"]   = $row->uuid_unidad;
                    $response->rows[$i]["cell"] = array(
                        $row->nombre,
                        $row->descripcion,
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
    public function ajax_select_precio() {
        if(!$this->input->is_ajax_request()){
            return false;
        }
        $clause = array();
        $uuid_precio = $this->input->post('uuid_precio', true);
        $tipo_precio = $this->input->post('tipo', true);
        $precio = Precios_orm::findByUuid($uuid_precio);
        $clause['id'] = $precio->id;

        $response = Precios_orm::asignar_precio_principal($clause,$tipo_precio );
        $json = json_encode($response);
        echo $json;
        exit;
    }


    /**
     * Cargar Vista Parcial de Tabla
     *
     * @return void
     */
    public function ocultotablaCategorias() {
        //If ajax request
    	$this->assets->agregar_js(array(
            'public/assets/js/modules/catalogos_inventario/tablaCategorias.js',
    	));

    	$this->load->view('tablaCategorias');
    }

    public function ocultotablaPrecios() {
        //If ajax request
    	$this->assets->agregar_js(array(
            'public/assets/js/modules/catalogos_inventario/tablaPrecios.js',
    	));

    	$this->load->view('tablaPrecios');
    }
    public function ocultotablaPreciosAlquiler() {
        //If ajax request
    	$this->assets->agregar_js(array(
            'public/assets/js/modules/catalogos_inventario/tablaPreciosAlquiler.js',
    	));

    	$this->load->view('tablaPreciosAlquiler');
    }
    public function ocultotablaUnidades() {
        //If ajax request
    	$this->assets->agregar_js(array(
            'public/assets/js/modules/catalogos_inventario/tablaUnidades.js',
    	));

    	$this->load->view('tablaUnidades');
    }

    public function ocultotablaRazonesAjustes() {
        //If ajax request
    	$this->assets->agregar_js(array(
            'public/assets/js/modules/catalogos_inventario/tablaRazonesAjustes.js',
    	));

    	$this->load->view('tablaRazonesAjustes');
    }

    public function catalogo_cuentas(){

        $repCuentas = new  Flexio\Modulo\Contabilidad\Repository\CuentasRepository;
        $clause = ['empresa_id'=>$this->id_empresa, 'transaccionales'=> true];

        $cuentas =  $repCuentas->get($clause);

        $cuentas =  $repCuentas->catalagos_transacciones($cuentas);

        return $cuentas->sortBy('nombre');
    }


}

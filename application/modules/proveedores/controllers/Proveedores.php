
<?php
/**
 * Pedidos
 *
 * Modulo para administrar la creacion, edicion de proveedores
 *
 * @package    PensaApp
 * @subpackage Controller
 * @category   Controllers
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  10/29/2015
 **/

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Comentario\Models\Comentario;
//repositories
use Flexio\Modulo\Proveedores\Repository\ProveedoresRepository;
use Flexio\Modulo\Proveedores\Models\Proveedores as proveedoresModel;

use Flexio\Library\HTML\HtmlRender;

class Proveedores extends CRM_Controller
{
    protected $id_empresa;
    protected $id_usuario;
    protected $empresa;
    protected $DocumentosRepository;
    protected $upload_folder = './public/uploads/';
    private  $proveedoresModel;

    protected $HtmlRender;
    protected $color_states = ['19' => '#5CB85C', '20' => '#D9534F', '21' => '#F0AD4E' ];


    //repositories
    private $proveedoresRep;

    public function __construct() {
        parent::__construct();
        $this->load->model("ordenes/Ordenes_orm");

        $this->load->model("pagos/Pagos_orm");
        $this->load->model('pagos/Pago_catalogos_orm');
        $this->load->model("Proveedores_orm");
        $this->load->model("Proveedores_cat_orm");
        $this->load->model("Proveedores_categorias_orm");
        $this->load->model("Proveedores_proveedor_categoria_orm");
        $this->load->model("usuarios/Empresa_orm");
        $this->load->model('Clientes/Catalogo_orm');
        $this->load->model('Bancos/bancos_orm');
        $this->load->model('Models/Catalogos_orm');
        $this->load->model("configuracion_compras/Items_estados_orm");
        $this->load->model("configuracion_compras/Tipos_proveedores_orm");
        $this->load->module(array('ordenes', 'documentos'));

        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();

        //Esto se debe definir con los muchacos
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $this->empresa = Empresa_orm::findByUuid($uuid_empresa);
        $this->id_usuario = $this->session->userdata("id_usuario");
        $this->id_empresa = $this->empresa->id;

        //repositories
        $this->proveedoresRep = new ProveedoresRepository();

         $this->HtmlRender = new HtmlRender;

    }


    /* public function index()
     {
         redirect("proveedores/listar");
     }*/


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
            'public/assets/css/modules/stylesheets/proveedores.css',
            'public/assets/css/plugins/jquery/jquery.fileupload.css',
        ));

        $this->assets->agregar_js(array(
            //'public/assets/js/default/jquery-ui.min.js',
            //'public/assets/js/plugins/jquery/jquery.sticky.js',
            //'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
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
            //'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            //'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/default/jquery.inputmask.bundle.min.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/plugins/bootstrap/bootstrap-tabdrop.js',
            //'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
            'public/assets/js/plugins/toastr.min.js',
            'public/assets/js/default/formulario.js',
            'public/assets/js/plugins/jquery/fileupload/jquery.fileupload.js',
            /* Archivos js del propio modulo*/
            'public/assets/js/modules/proveedores/listar.js',
        ));

        /*
         * Verificar si existe alguna variable de session
         * proveniente de algun formulario de crear/editar
         */
        if ($this->session->userdata('idProveedor')) {
            //Borrar la variable de session
            $this->session->unset_userdata('idProveedor');

            //Establecer el mensaje a mostrar
            $data["mensaje"]["clase"] = "alert-success";
            $data["mensaje"]["contenido"] = "Se ha creado el Proveedor satisfactoriamente.";
        } else if ($this->session->userdata('updatedProveedor')) {
            //Borrar la variable de session
            $this->session->unset_userdata('updatedProveedor');

            //Establecer el mensaje a mostrar
            $data["mensaje"]["clase"] = "alert-success";
            $data["mensaje"]["contenido"] = "Se ha actualizado el Proveedor satisfactoriamente.";
        }

        //Breadcrum Array
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-shopping-cart"></i> Proveedores',
            "ruta" => array(
                0 => array(
                    "nombre" => "Compras",
                    "activo" => false
                ),
                1 => array(
                    "nombre" => '<b>Proveedores</b>',
                    "activo" => true
                )
            ),
            "filtro" => false,
            "menu" => array()
        );

        //Verificar si tiene permiso a la seccion de Crear
        if ($this->auth->has_permission('acceso', 'proveedores/crear')) {
            $breadcrumb["menu"]["nombre"] = "Crear";
            $breadcrumb["menu"]["url"] = "proveedores/crear/";
        }

        //Verificar si tiene permiso de Exportar
        if ($this->auth->has_permission('listar__exportar', 'proveedores/listar')) {
            $breadcrumb["menu"]["opciones"]["#exportarBtn"] = "Exportar";
        }


        //Agregra variables PHP como variables JS
        $this->assets->agregar_var_js(array(
            "mensaje_clase" => isset($data["mensaje"]["clase"]) ? $data["mensaje"]["clase"] : "0",
            "mensaje_contenido" => isset($data["mensaje"]["contenido"]) ? $data["mensaje"]["contenido"] : "0"
        ));

        unset($data["mensaje"]);

        //catalogos
        $tipos_proveedores = Tipos_proveedores_orm::where("id_empresa", "=", $this->id_empresa)->estadoActivo()->get(array("id","nombre"))->toArray();
        $data["tipos"] = $tipos_proveedores;

        $data['info']["categorias"] = Proveedores_categorias_orm
            ::where("id_empresa", "=", $this->id_empresa)
            ->where("estado", "=", 19)
            ->orderBy("nombre", "ASC")->get();
        $data['estados'] = Proveedores_cat_orm::where('id_campo', '=', '1')->get()->toArray();

        $this->template->agregar_titulo_header('Listado de Proveedores');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);

    }

    public function ajax_obtener_item() {
        //Just Allow ajax request
        if ($this->input->is_ajax_request()) {
            $this->load->model("inventarios/Items_orm");
            $uuid = $this->input->post("uuid", true);

            $registro = Items_orm
                ::where("uuid_item", "=", hex2bin(strtolower($uuid)))
                ->get();

            $item = array();
            $i = 0;
            foreach ($registro as $row) {
                $item[$i] = array(
                    "descripcion" => $row->descripcion,
                    "unidades" => $row->unidades
                );
                $i += 1;
            }

            $response = array();
            $response["success"] = false;
            $response["item"] = $item;

            if (!empty($response["item"])) {
                $response["success"] = true;
            }

            echo json_encode($response);
            exit();
        }

    }

    public function ajax_obtener_pedido_item() {
        //Just Allow ajax request
        if ($this->input->is_ajax_request()) {
            $this->load->model("pedidos/Pedidos_items_orm");

            $id_pedido_item = $this->input->post("id_pedido_item", true);
            $registro = Pedidos_items_orm::find($id_pedido_item)->toArray();


            $response = array();
            $response["success"] = false;
            $response["registro"] = $registro;

            if (!empty($response["registro"])) {
                $response["success"] = true;
            }

            echo json_encode($response);
            exit();
        }

    }

    public function ajax_listar() {
        //Just Allow ajax request
        if ($this->input->is_ajax_request()) {
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
            $registros = new Proveedores_orm;
            $registros = $registros->where("pro_proveedores.id_empresa", "=", $this->id_empresa);

            $registros_count = new Proveedores_orm;
            $registros_count = $registros_count->where("pro_proveedores.id_empresa", "=", $this->id_empresa);

            /**
             * Verificar si existe algun $_POST
             * de los campos de busqueda
             */
            $nombre = $this->input->post('nombre', true);
            $aux = !empty($this->input->post('categoria', true)) ? $this->input->post('categoria', true) : array();
            $categoria = array_filter($aux, function ($value) {
                return !empty($value);
            });
            $tipo = $this->input->post('tipo', true);
            $estados = $this->input->post('estados', true);


            if (!empty($nombre)) {
                $registros = $registros->where("pro_proveedores.nombre", "like", "%$nombre%");
                $registros_count = $registros_count->where("pro_proveedores.nombre", "like", "%$nombre%");
            }
            if (count($categoria)) {
                //usar join
                $registros = $registros->join('pro_proveedor_categoria', 'pro_proveedor_categoria.id_proveedor', '=', 'pro_proveedores.id');
                $registros = $registros->join('pro_categorias', 'pro_proveedor_categoria.id_categoria', '=', 'pro_categorias.id');
                $registros = $registros->select('pro_proveedores.*');
                $registros = $registros->whereIn("pro_categorias.id", $categoria);

                $registros_count = $registros_count->join('pro_proveedor_categoria', 'pro_proveedor_categoria.id_proveedor', '=', 'pro_proveedores.id');
                $registros_count = $registros_count->join('pro_categorias', 'pro_proveedor_categoria.id_categoria', '=', 'pro_categorias.id');
                $registros_count = $registros_count->select('pro_proveedores.*');
                $registros_count = $registros_count->whereIn("pro_categorias.id", $categoria);
            }
            if (!empty($tipo)) {
                $registros = $registros->where("pro_proveedores.tipo_id", $tipo);
                $registros_count = $registros_count->where("pro_proveedores.tipo_id", $tipo);
            }
            //************************************************************************************
            if (!empty($estados))
            {
                //$data['estados'] = Proveedores_orm::where('estado', '=', $estados)->get()->toArray();
                 $registros = $registros->where("pro_proveedores.estado", "=", $estados);
            }

            /**
             * Total rows found in the query.
             * @var int
             */
            $count = $registros_count->get()->count();

            /**
             * Calcule total pages if $coutn is higher than zero.
             * @var int
             */
            $total_pages = ($count > 0 ? ceil($count / $limit) : 0);

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
            if ($start < 0) $start = 0;


            $registros = $registros->orderBy($sidx, $sord)
                ->skip($start)
                ->take($limit)
                ->get();

            //Constructing a JSON
            $response = new stdClass();
            $response->page = $page;
            $response->total = $total_pages;
            $response->records = $count;
            $i = 0;


            if (!empty($registros)) {
                foreach ($registros AS $i => $row) {
                    $hidden_options = "";
                    $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-nombre="' . $row->nombre . '" data-proveedor="' . $row->uuid_proveedor . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

                    //IMPORTANTE A MODO DE DESARROLLO ANADI LA CONDICION OR 1
                    //PARA QUE TODAS LAS CONDICIONES DIERAN TRUE

                    $enlace = '<a class="link">' . $row->nombre . '</a>';
                    if ($this->auth->has_permission('acceso', 'proveedores/ver/(:any)')) {
                        $hidden_options .= '<a href="' . base_url('proveedores/ver/' . $row->uuid_proveedor) . '" class="btn btn-block btn-outline btn-success">Ver Proveedor</a>';

                        $enlace = '<a href="' . base_url('proveedores/ver/' . $row->uuid_proveedor) . '" style="color:blue;">' . $enlace . '</a>';
                    }

                    if ($this->auth->has_permission('acceso', 'ordenes/crear/(:any)')) {
                        $hidden_options .= '<a href="' . base_url('ordenes/crear/proveedor' . $row->uuid_proveedor) . '" class="btn btn-block btn-outline btn-success">Nueva orden de compra</a>';
                    }

                    if ($this->auth->has_permission('acceso', 'facturas_compras/crear/(:any)')) {
                        $hidden_options .= '<a href="' . base_url('facturas_compras/crear/proveedor' . $row->id) . '" class="btn btn-block btn-outline btn-success">Agregar factura</a>';
                    }

                    if ($this->auth->has_permission('acceso', 'pagos/crear/(:any)')) {
                        $hidden_options .= '<a href="' . base_url('pagos/crear/proveedor' . $row->id) . '" class="btn btn-block btn-outline btn-success">Realizar pago</a>';
                    }

                    if ($this->auth->has_permission('acceso', 'anticipos/crear/(:any)')) {
                        $hidden_options .= '<a href="' . base_url('anticipos/crear/?proveedor=' . $row->uuid_proveedor) . '" class="btn btn-block btn-outline btn-success">Crear anticipo</a>';
                    }

                    if ($this->auth->has_permission('acceso', 'reportes_financieros/reporte/estado_cuenta_proveedor/(:any)')) {
                        $hidden_options .= '<a href="#" class="btn btn-block btn-outline btn-success estadoProveedor" data-id="' . $row['id'] . '" data-uuid="' . $row->id . '" >Ver estado de cuenta</a>';
                    }
                    $hidden_options .= '<a href="javascript:" data-id="' . $row->uuid_proveedor . '" class="exportarTablaCliente btn btn-block btn-outline btn-success subirArchivoBtn">Subir Documento</a>';

                    //Categorias
                    $categorias = array();
                    $aux = $row->categorias;
                    foreach ($aux as $categoria) {
                        $categorias[] = $categoria->nombre;
                    }

                    //Si no tiene acceso a ninguna opcion
                    //ocultarle el boton de opciones
                    if ($hidden_options == "") {
                        $link_option = "&nbsp;";
                    }

                    //dd($row);
                    $cat_estado = new Proveedores_cat_orm();
                    $estados = $cat_estado->where('valor','=',$row->estado)->get();
                   // dd($estados[0]->valor);
                    $response->rows[$i]["id"] = $row->uuid_proveedor;
                    $response->rows[$i]["cell"] = array(
                        '<a class="link" href="' . base_url('proveedores/ver/' . $row->uuid_proveedor) . '" class="link">' . $row->nombre . '</a>',
                        $row->telefono,
                        $row->email,
                        (empty($categorias)) ? "No tiene" : implode(", ", $categorias),
                        // count($row->tipo) ? $row->tipo->nombre : 'No tiene tipo',
                        $row->ordenesAbiertas(),
                        '<label class="totales-danger">$' . number_format($row->total_saldo_pendiente(), 2, '.', ',') . '</label>',
                        $this->HtmlRender->setContent($estados[0]->etiqueta)->setBackgroundColor($this->color_states[$estados[0]->id_cat])->label($estados[0]->valor),
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

    function ajax_anular() {
        $response = array();
        $response["success"] = false;
        $response["mensaje"] = "Error de sistema. Comuniquelo con el administrador de sistema";
        $response["clase"] = "alert-danger";

        $uuid = $this->input->post("uuid", true);
        if (!empty($uuid)) {
            $registro = Pedidos_orm
                ::where("uuid_pedido", "=", hex2bin(strtolower($uuid)))
                ->first();

            //DEFINO EL ESTADO COMO ANULADO = 6
            $registro->id_estado = "6";
            if ($registro->save()) {
                $response["success"] = true;
                $response["mensaje"] = "Su solicitud fue procesada satifastoriamente.";
                $response["clase"] = "alert-success";
            }

        }

        echo json_encode($response);
        exit();
    }

    function ajax_eliminar_pedido_item() {
        //Just Allow ajax request
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        $this->load->model("pedidos/Pedidos_items_orm");

        $id_registro = $this->input->post("id_registro", true);
        $registro = Pedidos_items_orm::find($id_registro);

        $response = array(
            "respuesta" => $registro->delete(),
            "mensaje" => "Se ha eliminado el registro satisfactoriamente"
        );


        $json = '{"results":[' . json_encode($response) . ']}';
        echo $json;
        exit;
    }

    function ajax_reabrir() {
        $response = array();
        $response["success"] = false;
        $response["mensaje"] = "Error de sistema. Comuniquelo con el administrador de sistema";
        $response["clase"] = "alert-danger";

        $uuid = $this->input->post("uuid", true);
        if (!empty($uuid)) {
            $registro = Pedidos_orm
                ::where("uuid_pedido", "=", hex2bin(strtolower($uuid)))
                ->first();

            //DEFINO EL ESTADO COMO ABIERTO = 1
            $registro->id_estado = "1";
            if ($registro->save()) {
                $response["success"] = true;
                $response["mensaje"] = "Su solicitud fue procesada satifastoriamente.";
                $response["clase"] = "alert-success";
            }

        }

        echo json_encode($response);
        exit();
    }

    private function _getProveedor($proveedor) {
        return [
            "id" => $proveedor->id,
            "nombre" => $proveedor->nombre,
            "credito" => $proveedor->credito, //Por desarrollar -> depende de abonos
            "saldo" => (string)($proveedor->total_saldo_pendiente()) ?: "0.00",
            "retiene_impuesto" => $proveedor->retiene_impuesto
        ];
    }

    function ajax_get_proveedor() {

        $proveedor_id = $this->input->post("proveedor_id");
        $proveedor = Proveedores_orm::find($proveedor_id);
        $registro = array();

        if (count($proveedor)) {
            $registro = $this->_getProveedor($proveedor);
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($registro))->_display();

        exit;
    }

    public function ajax_get_montos() {

        $proveedor_id = $this->input->post("proveedor_id");
        $proveedor = is_numeric($proveedor_id) ? $this->proveedoresRep->find($proveedor_id) : $this->proveedoresRep->findByUuid($proveedor_id);//cuando es desde ordenes de compra se manda el uuid
        $registro = array();

        if (count($proveedor)) {
            $registro['saldo'] = $proveedor->saldo_pendiente;
            $registro['credito'] = $proveedor->credito;
            $registro['termino_pago'] = count($proveedor->termino_pago) ? $proveedor->termino_pago->valor : '';
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($registro))->_display();

        exit;
    }

    function ajax_exportar() {
        $clause = [];
        $clause["empresa_id"] = $this->id_empresa;
        $clause["uuid_proveedores"] = $this->input->post("uuid_proveedor", true);

        $csv = \League\Csv\Writer::createFromFileObject(new \SplTempFileObject());
        $csv->insertOne(["Nombre", utf8_decode("Teléfono"), "E-mail", utf8_decode("Categorías"), "Tipo", "O/C Abiertas", "Total a pagar"]);
        $csv->insertAll($this->proveedoresRep->getCollectionExportar($this->proveedoresRep->get($clause)));

        $csv->output('proveedores.csv');
        exit;
    }

    /**
     * Cargar Vista Parcial de Tabla
     *
     * @return void
     */
    public function ocultotabla() {
        //If ajax request
        $this->assets->agregar_js(array(
            'public/assets/js/modules/proveedores/tabla.js'
        ));

        $this->load->view('tabla');
    }

    /**
     * Cargar Vista Parcial de Formulario
     *
     * @return void
     */
    public function ocultoformulario($data = array())
    {
        //$this->assets->agregar_js(array(
        //    'public/assets/js/modules/proveedores/formulario.js',
        //));

        $this->load->view('formularios', $data);
    }
    /**
     * Cargar Vista Parcial de Formulario
     *
     * @return void
     */
    public function ocultoformulariover($data = array()) {
        $this->assets->agregar_js(array(
            //'public/assets/js/modules/proveedores/crear.js',
            //'public/assets/js/modules/proveedores/formulario.js'
        ));

        /*if(empty($data))
        {
            $data["campos"] = array();
        }*/

        $this->load->view('formulario_ver', $data);
    }
    function ocultoformulariocomentarios() {

        $data = array();

        $this->assets->agregar_js(array(
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/modules/proveedores/vue.comentario.js',
            'public/assets/js/modules/proveedores/formulario_comentario.js'
        ));

        $this->load->view('formulario_comentarios');
        $this->load->view('comentarios');

    }

    function ajax_guardar_comentario() {

        if(!$this->input->is_ajax_request()){
            return false;
        }
        $model_id   = $this->input->post('modelId');
        $comentario = $this->input->post('comentario');
        $comentario = ['comentario'=>$comentario,'usuario_id'=>$this->id_usuario];
        $proveedor = $this->proveedoresRep->agregarComentario($model_id, $comentario);
        $proveedor->load('comentario_timeline');

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($proveedor->comentario_timeline->toArray()))->_display();
        exit;
    }

    function crear() {
        $data = array();
        $mensaje = array();
        $acceso = "";
        if ($this->auth->has_permission('acceso', 'proveedores/ver/(:any)')) {
            $acceso = "acceso";
        }
        //Agregra variables PHP como variables JS
        $this->assets->agregar_var_js(array(
            'retiene_impuesto' => 'no',
            'acceso' => $acceso
        ));
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
            'public/assets/css/plugins/jquery/fileinput/fileinput.css'
        ));

        $this->assets->agregar_js(array(
            //'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            //'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/default/jquery.inputmask.bundle.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/combodate/combodate.js',
            'public/assets/js/plugins/jquery/combodate/momentjs.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/default/tabla-dinamica.jquery.js',
            'public/assets/js/default/formulario.js',
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/modules/proveedores/formulario.js',
            'public/resources/compile/modulos/proveedores/formulario.js'
        ));

        $breadcrumb = array(
            "titulo" => '<i class="fa fa-shopping-cart"></i> Proveedores: Crear'
        );
        $data['info']['provincias'] = Catalogo_orm::where('tipo', '=', 'provincias')->get(array('id', 'valor'));
        $data['info']['letras'] = Catalogo_orm::where('tipo', '=', 'letras')->get(array('key', 'valor'));
        //catalogos
        /*$data["tipos"] = Proveedores_cat_orm
            ::where("valor", "tipo_acreedor")
            ->orderBy("etiqueta", "ASC")
            ->get()->toArray();*/

        $data['info']["categorias"] = Proveedores_categorias_orm
            ::where("id_empresa", "=", $this->id_empresa)
            ->where("estado", "=", 19)
            ->orderBy("nombre", "ASC")->get();

        //Tipo de proveedores
        $tipos_proveedores = Tipos_proveedores_orm::where("id_empresa", "=", $this->id_empresa)->estadoActivo()->get(array("id","nombre"))->toArray();
        $data['info']['tipos'] = $tipos_proveedores;
        //dd($data['tipos_proveedores']);

        $data['info']['bancos'] = Bancos_orm::all();
        $data['info']['terminoPago'] = Proveedores_cat_orm::where('id_campo', '=', '29')->get();
        $data['info']['identificacion'] = Proveedores_cat_orm::where('id_campo', '=', '21')->get(array('valor', 'etiqueta'));
        $data['info']['tipoCuenta'] = Catalogos_orm::where('identificador', '=', 'Tipo de Cuenta')->get();
        //$data['info']['formaPago'] = Catalogos_orm::where('identificador', '=', 'Forma de Pago')->get();
        $data['info']['formaPago'] = Pago_catalogos_orm::where('tipo', 'pago')->get(array('id', 'etiqueta', 'valor'));
        $data['info']['estados'] = Proveedores_cat_orm::where('id_campo', '=', '1')->get();
        $this->assets->agregar_var_js(array(
            'tipo_id' => 'null',
            'balance' => 0,
            "vista" => "crear"
        ));
        // dd($data);
        $this->template->agregar_titulo_header('Proveedores');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }


    function editar($uuid = NULL) {
        if (!$uuid) {
            echo "Error.";
            exit;
        }

        $data = array();
        $mensaje = array();

        $acceso = "";
        if ($this->auth->has_permission('acceso', 'proveedores/ver/(:any)')) {
            $acceso = "acceso";
        }
        //Cargo el registro
        $proveedor = new Proveedores_orm;
        $proveedor = $proveedor
            ->where("uuid_proveedor", "=", hex2bin(strtolower($uuid)))
            ->first();
        $proveedores = $this->proveedoresRep->findByUuid($uuid);
        //dd($proveedores->comentario_timeline);
        $proveedores->load('comentario_timeline','proveedores_asignados');
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
            'public/assets/css/modules/stylesheets/proveedores.css',
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
            //'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            //'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/default/jquery.inputmask.bundle.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/combodate/combodate.js',
            'public/assets/js/plugins/jquery/combodate/momentjs.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/default/tabla-dinamica.jquery.js',
            'public/assets/js/default/formulario.js',
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/modules/proveedores/formulario.js',
            'public/resources/compile/modulos/proveedores/formulario.js'
        ));

        //dd($proveedores->toArray());
        //Agregra variables PHP como variables JS
        $this->assets->agregar_var_js(array(
            "uuid_anterior" => (isset($proveedor->proveedor_anterior()->id)) ? $proveedor->proveedor_anterior()->uuid_proveedor : "",
            "uuid_siguiente" => (isset($proveedor->proveedor_siguiente()->id)) ? $proveedor->proveedor_siguiente()->uuid_proveedor : "",
            'retiene_impuesto' => $proveedor->retiene_impuesto,
            'vista' => 'ver',
            "proveedores_id" => $proveedor->id,
            "proveedor" => $proveedores,
            "pro_coment" =>(isset($proveedores->comentario_timeline)) ? $proveedores->comentario_timeline : [],
            "lista_asignados" => $proveedor->proveedores_asignados,
            'acceso' => $acceso
        ));

        //Arreglo de modulo de subpabeles que estan activos
        $menuOpciones = array();
        $opcionesModulos = array();
        $opcionesModulos["ordenes"] = array(
            "url" => "#crearContactoLnk",
            "nombre" => "Ordenes",
        );
        $modulo_subpaneles = Subpanel::lista_modulos_activos_relacionados();

        //Recorer el arreglo e introducirlo en
        //el menu de opciones si existe
        if (!empty($modulo_subpaneles)) {
            foreach ($modulo_subpaneles AS $nombre_modulo) {
                if (!empty($opcionesModulos[$nombre_modulo])) {
                    $menuOpciones[$opcionesModulos[$nombre_modulo]["url"]] = $opcionesModulos[$nombre_modulo]["nombre"];
                }
            }
        }


        $breadcrumb = array(
            "titulo" => '<i class="fa fa-shopping-cart"></i> Proveedor: ' . $proveedor->nombre,
            "filtro" => false,
            "menu" => array(
                "opciones" => $menuOpciones
            )
        );

        $data['info']['provincias'] = Catalogo_orm::where('tipo', '=', 'provincias')->get(array('id', 'valor'));
        $data['info']['letras'] = Catalogo_orm::where('tipo', '=', 'letras')->get(array('key', 'valor'));
        //catalogos
        /*$data["tipos"] = Proveedores_cat_orm
            ::where("valor", "tipo_acreedor")
            ->orderBy("etiqueta", "ASC")
            ->get()->toArray();*/

        //Tipo de proveedores
        $tipos_proveedores = Tipos_proveedores_orm::where("id_empresa", "=", $this->id_empresa)->estadoActivo()->get(array("id","nombre"))->toArray();
      //dd($tipos_proveedores);
        $data['info']['tipos'] = $tipos_proveedores;

        $data['info']["categorias"] = Proveedores_categorias_orm
            ::where("id_empresa", "=", $this->id_empresa)
            ->where("estado", "=", 19)
            ->orderBy("nombre", "ASC")->get();

        $data['info']['bancos'] = Bancos_orm::all();
        $data['info']['terminoPago'] = Proveedores_cat_orm::where('id_campo', '=', '29')->get();
        $data['info']['identificacion'] = Proveedores_cat_orm::where('id_campo', '=', '21')->get(array('valor', 'etiqueta'));
        $data['info']['tipoCuenta'] = Catalogos_orm::where('identificador', '=', 'Tipo de Cuenta')->get();
        //$data['info']['formaPago'] = Catalogos_orm::where('identificador', '=', 'Forma de Pago')->get();
        $data['info']['formaPago'] = Pago_catalogos_orm::where('tipo', 'pago')->get(array('id', 'etiqueta', 'valor'));
        $data['info']['estados'] = Proveedores_cat_orm::where('id_campo', '=', '1')->get();


        // dd($proveedor);
        $this->assets->agregar_var_js(array(
            'proveedor'=> $proveedor
        ));

        $data["info"]["credito"] = number_format($proveedor->credito, 2, '.', ',') ?: "0.00";
        $data["info"]["saldo"] =  number_format($proveedor->total_saldo_pendiente(), 2, '.', ',') ?: "0.00";
        $data["info"]["acreedor"] = $proveedor->acreedor;
        $data["info"]["tipoCuentaSelect"] = $proveedor->id_tipo_cuenta;
        $data["info"]["terminoPagoSelect"] = $proveedor->termino_pago_id;
        $data["info"]["bancoSelect"] = $proveedor->id_banco;
          $data["info"]["tipo_id_selected"] = $proveedor->tipo_id;
        //Identificacion
        $data["info"]["identificacionSelect"] = $proveedor->identificacion;
        //Letra de la identificación
        $data["info"]["letraSelect"] = $proveedor->letra;
        $data["info"]["estadoSelect"] = $proveedor->estado;

        //formas de pago
        $j=0;
        foreach ($proveedor->formasDePago as $row) {
            $data["info"]["pagosSelect"][$j] = $row->id_cat;
            $j += 1;
        }

        //categorias
        $i=0;
        foreach ($proveedor->categorias as $row) {
            $data["info"]["catSelect"][$i] = $row->id;
            $i += 1;
        }
        //dd($data);


        //IDENTIFICADOR DEL PROVEEDOR
        $data["info"]["uuid_proveedor"] = $uuid;
        $data["uuid_proveedor"] = $uuid;
        $this->template->agregar_titulo_header('Proveedores');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    function guardar() {

        $data       = array();
        $mensaje    = array();
        $proveedor = "";

        //dd($_POST);

        if (!empty($_POST)) {
            $response = false;
            $response = Capsule::transaction(
                function () {
                    $campo = $this->input->post("campo");
                    //DATOS GENERALES
                    if (empty($campo["uuid"])){
                        $proveedor = new proveedoresModel();
                    }else{
                        $proveedor = $this->proveedoresRep->findByUuid($campo["uuid"]);
                    }


                    //DATOS GENERALES DEL PROVEEDOR
                    $proveedor->nombre = $campo["nombre"];
                    $proveedor->telefono = $campo["telefono"];
                    $proveedor->email = $campo["email"];
                    $proveedor->estado = $campo["estado"];
                    $proveedor->id_banco = $campo["banco"];
                    $proveedor->id_tipo_cuenta = $campo["tipo_cuenta"];
                    $proveedor->numero_cuenta = $campo["numero_cuenta"];
                    $proveedor->limite_credito = str_replace(',','',$campo["limite_credito"]);
                    $proveedor->direccion = $campo["direccion"];
                    $proveedor->termino_pago_id = $campo["termino_pago_id"];
                    $proveedor->retiene_impuesto = !empty($campo["retiene_impuesto"]) ? $campo["retiene_impuesto"] : "";
                    $proveedor->acreedor = $campo["acrededor"];
                    $identificacion = $campo["tipo_identificacion"];
                    $proveedor->identificacion = $identificacion;
                    $proveedor->tipo_id = $campo["tipo_id"];

                    if ($identificacion == 'natural') {
                        $natural = $this->input->post("natural");
                        $proveedor->provincia = $natural['provincia'];
                        $letra = $natural['letra'];
                        $proveedor->letra = $letra;
                        if ($letra == 'PAS') {
                            $proveedor->pasaporte = $natural['pasaporte'];
                        } else {
                            $proveedor->tomo_rollo = $natural['tomo'];
                            $proveedor->asiento_ficha = $natural['asiento'];
                        }
                    } elseif ($identificacion == 'juridico') {
                        $juridico = $this->input->post('juridico');
                        $proveedor->digito_verificador = $juridico['verificador'];
                        $proveedor->asiento_ficha = $juridico["asiento"];
                        $proveedor->folio_imagen_doc = $juridico["folio"];
                        $proveedor->tomo_rollo = $juridico['tomo'];
                    } elseif ($identificacion == 'pasaporte') {
                        $proveedor->pasaporte = $campo['pasaporte'];
                    }

                    if (empty($campo["uuid"])){
                        //Guarda el registro.
                        $proveedor->uuid_proveedor = Capsule::raw("ORDER_UUID(uuid())");
                        $proveedor->fecha_creacion = date("Y-m-d", time());
                        $proveedor->creado_por = $this->id_usuario;
                        $proveedor->id_empresa = $this->id_empresa;
                        $proveedor->save();

                    }else{
                        //Actualiza el registro.
                        $proveedor->save();
                    }

                    //Pasa a la session el id del proveedor.
                    $this->session->set_userdata('updatedProveedor', $proveedor->id);
                    //Tipos de Pago
                    $proveedor->formasDePago()->sync($campo["forma_pago"]);

                    //Categorias
                    $registro = array();
                    $proveedor_categoria = new Proveedores_proveedor_categoria_orm();

                    if (!empty($campo["categorias"])) {
                        $i = 0;
                        $count = 0;
                        foreach ($campo["categorias"] as $row) {
                            if (!empty($row)) {
                                if ($proveedor_categoria->countRegistro($proveedor->id, $row)== 0){
                                    $registro[$i]["id_proveedor"] = $proveedor->id;
                                    $registro[$i]["id_categoria"] = $row;
                                    $count += 1;
                                    $i += 1;
                                }
                                $i += 1;
                            }
                        }
                        if($count>0){
                            $proveedor_categoria::insert($registro);
                        }

                    }
                    return true;
                }
            );
            if ($response == "1") {
                redirect(base_url('proveedores/listar'));
            } else {
                //Establecer el mensaje a mostrar
                $data["mensaje"]["clase"] = "alert-danger";
                $data["mensaje"]["contenido"] = "Hubo un error al tratar de crear el pedido.";
            }
        }

    }
    function documentos_campos() {

        return array(
            array(
                "type"		=> "hidden",
                "name" 		=> "proveedores_id",
                "id" 		=> "proveedores_id",
                "class"		=> "form-control",
                "readonly"	=> "readonly",
            ));
    }

    function ajax_guardar_documentos() {
        if(empty($_POST)){
            return false;
        }

        $proveedores_id = $this->input->post('proveedores_id', true);
        $modeloInstancia = $this->proveedoresRep->findByUuid($proveedores_id);
        $this->documentos->subir($modeloInstancia);
    }
}

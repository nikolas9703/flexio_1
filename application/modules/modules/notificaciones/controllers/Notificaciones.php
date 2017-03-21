<?php
/**
 * Notificaciones
 *
 * Modulo para administrar la creacion, edicion de Notificaciones.
 *
 * @package    PensaApp
 * @subpackage Controller
 * @category   Controllers
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  10/20/2016
 */

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Modulos\Repository\ModulosRepository as ModulosRepository;
use Flexio\Modulo\Pedidos\Repository\PedidoRepository as PedidoRepository;
use Flexio\Modulo\Pedidos\Models\Pedidos as PedidosModel;
use Flexio\Modulo\Pedidos\Repository\PedidosCatRepository;
use Flexio\Modulo\Usuarios\Repository\UsuariosRepository;
use Flexio\Library\Util\Utiles as Util;
use Flexio\Modulo\Empresa\Repository\EmpresaRepository;
use Flexio\Modulo\Inventarios\Repository\CategoriasRepository as ItemsCategoriasRepository;
use Flexio\Modulo\Notificaciones\Repository\NotificacionesCatalogRepository as NotificacionesCatalogRepository;
use Flexio\Modulo\Notificaciones\Repository\NotificacionesRepository as NotificacionesRepository;
use Flexio\Modulo\FacturasCompras\Repository\FacturaCompraCatalogoRepository;
use Flexio\Modulo\FacturasCompras\Models\FacturaCompraCatalogo;

class Notificaciones extends CRM_Controller
{
    protected $modulosRepository;
    protected $PedidoRepository;
    protected $UsuariosRepository;
    protected $PedidosCatRepository;
    protected $FacturaCompraCatalogoRepository;
    protected $empresaRepo;
    protected $id_usuario;
    protected $ItemsCategoriasRepository;
    protected $NotificacionesCatalogRepository;
    protected $NotificacionesRepository;
  //  protected $empresa;

    public function __construct()
    {
        parent::__construct();

        $this->load->model("roles/Rol_orm");
        $this->load->model("usuarios/Empresa_orm");
        $this->load->model('usuarios/Usuario_orm');
        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();

      //  $uuid_empresa = $this->session->userdata('uuid_empresa');
       // $this->empresa = Empresa_orm::findByUuid($uuid_empresa);
        $this->id_usuario = $this->session->userdata("id_usuario");
      //  $this->id_empresa = $this->empresa->id;

        $this->modulosRepository = new ModulosRepository();
        $this->PedidoRepository = new PedidoRepository;
        $this->UsuariosRepository = new UsuariosRepository;
        $this->PedidosCatRepository = new PedidosCatRepository;
        $this->FacturaCompraCatalogoRepository = new FacturaCompraCatalogoRepository;
        $this->empresaRepo = new EmpresaRepository();
        $this->ItemsCategoriasRepository = new ItemsCategoriasRepository();
        $this->NotificacionesCatalogRepository = new NotificacionesCatalogRepository();
        $this->NotificacionesRepository = new NotificacionesRepository();

    }

    public function listar()
    {
        $data = array();

        //Breadcrum Array
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-cogs"></i>Notificaciones',
            "ruta" => array(/*0 => array(
                    "nombre" => "Compras",
                    "activo" => false
                ),
                1 => array(
                    "nombre" => '<b>Proveedores</b>',
                    "activo" => true
                )*/
            ),
            "filtro" => false,
            "menu" => array()
        );

        $this->template->agregar_titulo_header('Listado de Proveedores');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }

    public function ajax_listar()
    {
        if($this->input->is_ajax_request())
        {
            $empresa_id = $this->input->post('empresa_id');
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

            //dd($registros->toArray());
            /**
             * Total rows found in the query.
             * @var int
             */
            $count = $this->NotificacionesRepository->listar($empresa_id,null,null,null,null)->count();

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

            $registros = $this->NotificacionesRepository->listar($empresa_id,$sidx, $sord, $limit, $start);
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
            if(!empty($registros) ) {
                foreach ($registros AS $i => $row) {
                    $hidden_options = "";
                    $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-nombre="'.$row->id.'" data-orden="'. $row->id .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

                    $aux = $this->UsuariosRepository->findIds($row->usuarios);
                    $usuarios = array();
                    foreach ($aux as $usuario){
                        $usuarios[] = $usuario->nombrecompleto;
                    }
                    $rol = new Rol_orm;
                    $aux2 =  $rol->whereIn("id", $row->roles)->get(array('nombre'));
                    $roles = array();
                    foreach ($aux2 as $r){
                        $roles[] = $r->nombre;
                    }
                    $aux3 = $this->NotificacionesCatalogRepository->findIds($row->tipo_notificacion);
                    $tipos = array();
                    foreach ($aux3 as $tipo){
                        $tipos [] = $tipo->etiqueta;
                    }                                 
                    if($row->modulos->id == '36'){                    
                    $etiqueta_factura = FacturaCompraCatalogo::where('id', $row->transaccion)->get();
                        $etiqueta = $etiqueta_factura[0]->valor;
                    }else{
                        $etiqueta = $row->transacciones->etiqueta;
                    }
                    $response->rows[$i]["id"]   = $row->id;
                    $response->rows[$i]["cell"] = array(
                        'Compras\\'.$row->modulos->nombre,
                        $etiqueta,
                        (empty($roles)) ? "" : implode(", ", $roles),
                        (empty($usuarios)) ? "" : implode(", ", $usuarios),
                        (empty($row->categorias->nombre)) ? "" :$row->categorias->nombre,
                         $row->present()->montos,
                        (empty($row->sin_transaccion)) ? "" :$row->sin_transaccion,
                        (empty($tipos)) ? "" : implode(", ", $tipos),
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

    public function crear($empresa_uuid = NULL)
    {
        $data = array();
        $mensaje = array();
        $empresa = $this->empresaRepo->findByUuid($empresa_uuid);
        $this->_CSS();
        $this->_JS();
        //Se filtra solo por Compras/Pedidos.
        //Para modulos futuros hay que restructurar como se van a
        //presentar la lista de Modulos.
       // $modulos = $this->modulosRepository->getModulos();
        $modulos = $this->modulosRepository->find();
        // dd($modulos);

        $dia_count = date("t");
        $dias = array();
        for ($i = 1; $i < $dia_count; $i++) {
            $dias[$i] = $i;
        }

        $reponse = null;

        //Se filtra solo por Compras/Pedidos.
        //Para modulos futuros hay que restructurar como se van a
        //presentar la lista de Modulos.
      /*  foreach ($modulos as $modulo) {
           if ($modulo->id == '20') {
               $reponse = $modulo;
              /*$reponse =[
                   'id' => $modulo->id,
                   'nombre' => 'Compras/' .$modulo->nombre
               ];
               $modulo->map
           }

            }*/
           // dd($reponse);
        $data['info']['dias'] = $dias;
        //$rol = new Rol_orm;
        //$data['info']['roles'] = $rol->where("roles.empresa_id", "=", $empresa->id)->get(array('id', 'nombre'));
        $clause = ['empresa_id' => $empresa->id];
        $usuarios =  $this->UsuariosRepository->get($clause, 'nombre', 'ASC');
        $usuarios->load('roles');
        $rol = new Rol_orm;
        $this->assets->agregar_var_js(array(
            'modulos'             => $modulos,
            'empresa_id'          => $empresa->id,
            'usuarios'            => $usuarios,
            //'dias'                => $dias,
            "categorias"          => $this->ItemsCategoriasRepository->get($clause),
            "operadores"          => $this->NotificacionesCatalogRepository->getOperador(),
            "estados"             => $this->NotificacionesCatalogRepository->getEstados(),
            "notificaciones_tipo" => $this->NotificacionesCatalogRepository->getNotificaciones(),
            //"transaccion"         => $this->PedidosCatRepository->get(['campo_id' => '7']),
            "roles"               => $rol->where("roles.empresa_id", "=", $empresa->id)->get(array('id', 'nombre'))
        ));
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-cogs"></i> Administraci&oacute;n: Notificaciones',
        );
        $this->template->agregar_titulo_header('Crear Notificaciones');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    public function ajax_transaccion()
    {
        $empresa_id = $this->input->post('empresa_id', true);
        $modulo_id = $this->input->post('modulo_id', true);
       // $id = $this->input->post('id', true);/
        //if ($id == '20') {           
        $clause = ['empresa_id' => $empresa_id];
        $catalogo = [];
        $rol = new Rol_orm;
        if($modulo_id == '36'){
        $clause = ['tipo' => 'estado_factura_compra'];
        $catalogo['estados'] = $this->FacturaCompraCatalogoRepository->get($clause);        
        }else{
        //estados transaccionales de pedidos        
        $catalogo['estados'] = $this->PedidosCatRepository->get(['tipo' => 'estado_factura_compra']);
        }
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($catalogo))->_display();
        exit;
    }

    /**
     * Cargar Vista Parcial de Formulario
     *
     * @return void
     */
    public function ocultoformulario($data = array())
    {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/notificaciones/routes.js',
            'public/assets/js/modules/notificaciones/formulario.js',
        ));

        $this->load->view('formulario', $data);
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
            'public/assets/js/modules/notificaciones/tabla_notificaciones.js'
        ));

        $this->load->view('tabla');
    }
    /**
     * Guarda los campos Formulario
     *
     * @return void
     */
    public function guardar()
    {
        $post = $this->input->post();
        //dd($post);
        //dd($post);
        if (!empty($post))
        {
            $notificacion = new Flexio\Modulo\Notificaciones\HttpRequest\FormGuardar;
            //$toast = new Flexio\Library\Toast;
            try {
                $item = $notificacion->guardar();
            } catch (\Exception $e) {
                log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
                //$toast->setUrl('notificaciones/listar')->run("exception",[$e->getMessage()]);
            }
        }

    }
    public function ajax_guardar(){
        $post = $this->input->is_ajax_request();
        if (!$post)
        {
            return false;
        }
            $response = new stdClass();
            $notificacion = new Flexio\Modulo\Notificaciones\HttpRequest\FormGuardar;
            try {
                $guardar = $notificacion->guardar();
            } catch (\Exception $e) {
                log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
                $response->clase = "danger";
                $response->estado = 500;
                $response->mensaje = '<b>Error</b> No fue posible guardar la notificación.';
                echo json_encode($response);
                exit;
            }
            if ($guardar != null){
                $response->clase = "success";
                $response->estado = 200;
                $response->mensaje = '<b>¡&Eacute;xito!</b> Se ha guardado correctamente.';
            }else{
                $response->clase = "danger";
                $response->estado = 500;
                $response->mensaje = '<b>Error</b> No fue posible guardar la notificación.';
            }
            echo json_encode($response);
            exit;

    }
    public function _CSS()
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
            'public/assets/css/plugins/bootstrap/select2.min.css',
        ));
    }

    public function _JS()
    {
        $this->assets->agregar_js(array(
            'public/assets/js/default/vue.js',
            'public/assets/js/default/vue-validator.min.js',
            'public/assets/js/default/vue-resource.min.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
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
            'public/assets/js/modules/notificaciones/ckeditor.config.js',
            'public/assets/js/plugins/bootstrap/select2/select2.min.js',
            'public/assets/js/plugins/bootstrap/select2/es.js',
            'public/assets/js/default/vue/directives/select2.js',
        ));
    }
}


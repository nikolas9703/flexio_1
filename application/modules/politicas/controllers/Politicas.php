<?php
/**
 * Administrador Modulos
 *
 * Administra los modulos adicionales que pueden ser instalados o
 * desintalados en el sistema.
 *
 * @package    PensaApp
 * @subpackage Controller
 * @category   Controllers
 * @author     Pensanomica Team
 * @link       http://www.pensanomica.com
 *
 */

use Carbon\Carbon;
use League\Csv\Writer;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Politicas\Repository\PoliticasRepository;
use Flexio\Modulo\Inventarios\Repository\CategoriasRepository as ItemsCategoriasRepository;
use Flexio\Modulo\Politicas\Repository\PoliticasCatalogosRepository;
use Flexio\Modulo\Usuarios\Models\RolesUsuario;

use Flexio\Modulo\Roles\Models\Roles;

class Politicas extends CRM_Controller
{
	/**
	 * @var
	 */
	private $cache;
	private $empresa_id;
    private $empresaObj;
    private $usuario_id;
    private $empresa_uuid ;

    protected $PoliticasRepository;
    protected $ItemsCategoriasRepository;
    protected $PoliticasCatalogosRepository;
    /*
        cuando cuando exista el instalador de modulos validar que la
        empresas tenga estos modulos y que esten activos,
        por mientras van quemados
     */
    protected $modulos = [
        ['id' => 'orden_compra', 'nombre'=>'Ordenes de Compras'],
        ['id' => 'pedido', 'nombre'=>'Pedidos'],
        ['id' => 'pago', 'nombre'=>'Pagos'],
        ['id' => 'factura_compra', 'nombre'=>'Facturas de Compras'],
		['id'=> 'anticipo','nombre' => 'Anticipos']
    ];

	function __construct()
    {
        parent::__construct();


        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresaObj = new Buscar(new Empresa_orm,'uuid_empresa');
        $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
        $this->empresa_id = $this->empresaObj->id;
        $this->empresa_uuid = $uuid_empresa;
        $this->usuario_id   = $this->session->userdata("id_usuario");

        $this->PoliticasRepository          = new PoliticasRepository();
        $this->ItemsCategoriasRepository    = new ItemsCategoriasRepository();
        $this->PoliticasCatalogosRepository = new PoliticasCatalogosRepository();

        $this->load->model('roles/rol_orm');
        $this->load->model('modulos/modulos_model');
        $this->load->model('modulos/modulos_orm');

         $this->cache = Cache::inicializar();
    }


    public function ajax_guardar_politica(){
        if (! $this->input->is_ajax_request ()) {
    		return false;
    	}

         $store = new Flexio\Modulo\Politicas\FormRequest\GuardarPolitica;
        try{
					
            $politica = $store->guardar();
            $response = [
                "response" => true,
                "mensaje" => "<b>&Eacute;xito!</b> Se ha guardado correctamente la Politica"
            ];
        }catch(\Exception $e){
            log_message('error', __METHOD__." ->". ", Linea: ". __LINE__ ." --> ". $e->getMessage()."\r\n");
            $response = [
                "response" => false,
                "mensaje" => "Hubo un error tratando de actualizar  las pol&iacute;ticas de transaccio&oacute;."
            ];
        }

        echo json_encode($response);
    	exit;

    }
    public function ocultoformulario( $info = array())
    {

     	  $this->assets->agregar_js(array(
              'public/assets/js/modules/politicas/components/directiva.js',
             'public/assets/js/modules/politicas/vue.formulario.js',
         ));


        $clause = ['empresa_id' => $this->empresa_id, 'transaccionales' => true, 'conItems' => true, 'tipo_cuenta_id' => '4', 'vendedor' => true];

        //$roles = RolesUsuario::where("empresa_id", $this->empresa_id)->get();
        $lista_roles =  Roles::where(["empresa_id"=>$this->empresa_id])->get();

        //$lista_roles = $roles->load('roles');
        $modulos = collect($this->modulos);
        $this->assets->agregar_var_js(array(
            'empresa_id' =>$this->empresa_id,
            'roles' =>$lista_roles,
            'modulos' => $modulos,
            'categorias' => $this->ItemsCategoriasRepository->getCollectionCategorias($this->ItemsCategoriasRepository->get($clause)),
            'transacciones' =>  $this->PoliticasCatalogosRepository->getTransacciones()
        ));

        $this->load->view('formulario');
     }
     //ptr_transacciones_catalogo
    public function listar($uuid_empresa){

         $data=array();

        $this->_css();$this->_js();

        if(!is_null($this->session->flashdata('mensaje'))){
            $mensaje = json_encode($this->session->flashdata('mensaje'));
        }else{
            $mensaje = '';
        }

         $empresaObj = new Buscar(new Empresa_orm,'uuid_empresa');
         $empresaObj = $empresaObj->findByUuid($uuid_empresa);


				 if($empresaObj->id != $this->empresa_id){
					 	redirect(base_url('politicas/listar/'.$this->empresa_uuid));
 				 }
         $this->assets->agregar_var_js(array(
           "toast_mensaje" => $mensaje,
           "empresa_id_pol" => $empresaObj->id

         ));

    	$breadcrumb = array(
    		"titulo" => '<i class="fa fa-gears "></i> Administraci&oacute;n: Pol&iacute;ticas de transacciones',
    	);



    	$breadcrumb["menu"]["opciones"] = !empty($menuOpciones) ? $menuOpciones : array();



        $data['info']['empresa_id'] = $empresaObj->id;
     	$this->template->agregar_titulo_header('Administraci&oacute;n: Pol&iacute;ticas de transacciones');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar($breadcrumb);
    }

        public function ajax_get_politica() {
            if(!$this->input->is_ajax_request()){
                    return false;
            }


            $id = $this->input->post('id', true);
            $info = $this->PoliticasRepository->find($id);
            $info->load("categorias");

    	echo json_encode($info);
    	exit;

        }
        public function ocultotabla() {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/politicas/tabla.js'
        ));

        $this->load->view('tabla');
    }

    public function ajax_listar() {
        if(!$this->input->is_ajax_request()){return false;}

        $empresa_id = $this->input->post('empresa_id');
        $clause                 = $this->input->post();
        $clause['empresa_id']   = $empresa_id;
        //$clause['usuario_id']   = $this->usuario_id;

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = $this->PoliticasRepository->count($clause);

        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $politicas_alquileres = $this->PoliticasRepository->get($clause ,$sidx, $sord, $limit, $start);

        $response          = new stdClass();
        $response->page    = $page;
        $response->total   = $total_pages;
        $response->records = $count;
         if($count > 0){
            foreach ($politicas_alquileres as $i => $politica_alquiler)
            {
                 $response->rows[$i]["id"]   = $politica_alquiler->id;
                $response->rows[$i]["cell"] = $this->PoliticasRepository->getCollectionCell($politica_alquiler, $this->auth);
            }
        }
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))->_display();
        exit;
    }


     private function _css() {
        $this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
            'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
            'public/assets/js/plugins/jquery/sweetalert/sweetalert.css',
            'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
            'public/assets/css/plugins/bootstrap/select2.min.css',
            'public/assets/css/modules/stylesheets/politicas.css',
        ));
    }


    private function _js() {
        $this->assets->agregar_js(array(
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/plugins/bootstrap/select2/select2.min.js',
            'public/assets/js/plugins/bootstrap/select2/es.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
            'public/assets/js/default/toast.controller.js',
            'public/assets/js/modules/politicas/plugins.js',
            'public/assets/js/default/jquery.inputmask.bundle.min.js'
        ));
    }
}
?>

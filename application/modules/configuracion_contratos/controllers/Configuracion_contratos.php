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
use Flexio\Library\Util\FormRequest;
use League\Csv\Writer as Writer;
use Carbon\Carbon;
use Flexio\Modulo\Catalogos\Repository\CatalogoRepository;
use Flexio\Modulo\ConfiguracionContratos\Repository\TipoSubContratoCatalogoRepository;

class Configuracion_contratos extends CRM_Controller
{
    protected $empresa;
    protected $empresa_id;
    protected $usuario_id;
    protected $CatalogoRepository;
    protected $TipoSubContratoCatalogoRepository;

    const PREFIJO_FUNC_GUARDAR_CONFIGURACION = 'guardar_configuracion_';

    public function __construct()
    {
        parent::__construct();
        $this->load->model("usuarios/Empresa_orm");
        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();
        $uuid_empresa       = $this->session->userdata('uuid_empresa');
        $this->empresa      = Empresa_orm::findByUuid($uuid_empresa);
        $this->usuario_id   = $this->session->userdata("id_usuario");
        $this->empresa_id   = $this->empresa->id;

        $this->CatalogoRepository = new CatalogoRepository;
        $this->TipoSubContratoCatalogoRepository = new TipoSubContratoCatalogoRepository;
    }
    public function index() {
        redirect("configuracion_contratos/listar");
    }

    public function configuracion()
    {
      if(!$this->auth->has_permission('acceso')){
        redirect ( '/' );
      }

      $data = array();
      $breadcrumb = array();

      $this->_Css();
    	$this->assets->agregar_css(array(
    		'public/assets/css/plugins/jquery/switchery.min.css',
        'public/assets/css/plugins/jquery/awesome-bootstrap-checkbox.css',
    		'public/assets/css/modules/stylesheets/animacion.css'
    	));
    	$this->_js();
    	$this->assets->agregar_js(array(
        'public/assets/js/default/vue-validator.min.js',
        'public/resources/compile/modulos/configuracion_contratos/configuracion.js'
    	));

      $this->template->agregar_titulo_header('Configuracion Subcontratos');
      $this->template->agregar_breadcrumb($breadcrumb);
      $this->template->agregar_contenido($data);
      $this->template->visualizar($breadcrumb);
    }

    /**
     * Guardar formularios de configuracion de subcontrato
     * @return json
     */
    public function ajax_guardar_configuracion() {

      $request = Illuminate\Http\Request::createFromGlobals();
      $fieldset = FormRequest::data_formulario($request->all());

      //
      // El $_POST indice "formulario" sera usado para ejcutar
      // una funcion que empieze por el prefijo guardar_configuracion_,
      // quiere decir que cada formulario en configuracion debe tener su funcion de guardar.
      //
      $response = [];
      if (!empty($fieldset['formulario']) && method_exists($this, static::PREFIJO_FUNC_GUARDAR_CONFIGURACION . $fieldset['formulario'])) {
        $response = $this->{static::PREFIJO_FUNC_GUARDAR_CONFIGURACION . $fieldset['formulario']}();
      }

      $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
          ->set_output(json_encode($response))->_display();
      exit;
    }

    public function ajax_listar_catalogo()
    {
        if(!$this->input->is_ajax_request()){
            return false;
        }

        $clause = array(
          'empresa_id' => $this->empresa_id
        );
       /* $tipo = $this->input->post('tipo');
        if(!empty($tipo)){
            $clause['tipo'] = $tipo;
        }*/

        $jqgrid = new Flexio\Modulo\ConfiguracionContratos\Services\TipoSubContratoCatalogoJqgrid();
        $response = $jqgrid->listar($clause);
        $this->output->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    public function ocultotablaCatalogoTipoSubcontratos($id=null, $modulo = null)
    {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/configuracion_contratos/tabla_tipos_subcontrato.js'
        ));
        $this->load->view('tabla_tipos_subcontrato');
    }

    /**
     * Guardar catalogo de Tipo de subcontrato
     *
     * @return array
     */
    public function guardar_configuracion_tipo_subcontrato(){

      $request = \Illuminate\Http\Request::createFromGlobals();
      $fieldset = FormRequest::data_formulario($request->all());

      if(empty($fieldset) || empty($fieldset["valor"])){
        return false;
      }

      unset($fieldset["formulario"]);
      unset($fieldset["guardarBtn"]);
      unset($fieldset["tipo"]);
      unset($fieldset["modulo"]);

      //$cat_subcontrato = $this->CatalogoRepository->get(['modulo' => 'subcontratos']);
     // $tipos_subcontratos = $this->CatalogoRepository->get(['modulo' => 'subcontratos', 'tipo' => 'tipo_subcontrato']);
      $fieldset["empresa_id"] = $this->empresa_id;
      $fieldset["created_by"] = $this->usuario_id;

      $fieldset["acceso"] = !empty($fieldset["con_acceso"]) ? $fieldset["con_acceso"] : 0;
        unset($fieldset["con_acceso"]);
      $fieldset["estado"] = !empty($fieldset["activo"]) ? $fieldset["activo"] : 0;
        unset($fieldset["activo"]);
       $fieldset["nombre"] =  $fieldset["valor"];
        unset($fieldset["valor"]);

      //$fieldset["orden"] = !empty($tipos_subcontratos) ? $tipos_subcontratos->max('orden')+1 : 0;
      //$fieldset["key"] = !empty($tipos_subcontratos) ? $cat_subcontrato->max('key')+1 : 0;

      try{
         if(!empty($fieldset['id'])){
             $this->TipoSubContratoCatalogoRepository->actualizar($fieldset);
         }else{
           //$this->CatalogoRepository->crear($fieldset);
             //Se crea un catalogo para el tipo de SubContratos
             $this->TipoSubContratoCatalogoRepository->crear($fieldset);
         }

         $mensaje = array('tipo'=>"success", 'mensaje'=>'<b>¡&Eacute;xito!</b> Se ha guardado correctamente');
      }catch(\Exception $e){
          log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
          $mensaje = array('tipo' => 'error', 'mensaje' => '<b>¡Error! Su solicitud no fue procesada</b>');
      }
      return $mensaje;
    }

    /**
     * Método para cargar los Js
     * @return array
     */
    private function _Css()
    {
        $this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
            'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
            'public/assets/css/plugins/bootstrap/select2.min.css'
        ));
    }

    /**
     * Método para cargar los Js
     * @return array
     */
    private function _js()
    {
        $this->assets->agregar_js(array(
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
            'public/assets/js/default/lodash.min.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/plugins/bootstrap/select2/select2.min.js',
            'public/assets/js/plugins/bootstrap/select2/es.js',
            'public/assets/js/default/toast.controller.js',
            'public/assets/js/modules/subcontratos/plugins.js',
            'public/assets/js/default/vue/directives/select2.js',
            'public/assets/js/plugins/jquery/fileupload/jquery.fileupload.js'
        ));
    }
}

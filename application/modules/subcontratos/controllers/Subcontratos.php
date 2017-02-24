<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Erp
 * @subpackage Controller
 * @category   Subcontratos
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  10/22/2015
 */
use Flexio\Library\Util\FormRequest;
use Carbon\Carbon                                                      as Carbon;
use Illuminate\Database\Capsule\Manager                                as Capsule;
use Flexio\Modulo\SubContratos\Repository\SubContratoRepository        as SubContratoRepository;
use Flexio\Modulo\SubContratos\Repository\AdendaRepository             as AdendaRepository;
use Flexio\Modulo\SubContratos\Events\ActualizarSubContratoMontoEvent  as ActualizarSubContratoMontoEvent;
use Flexio\Modulo\SubContratos\Listeners\ActualizarSubContratoListener as ActualizarSubContratoListener;
use Flexio\Modulo\Catalogos\Repository\CatalogoRepository;
use Flexio\Modulo\Proveedores\Repository\ProveedoresRepository;
use Flexio\Modulo\CentrosContables\Repository\CentrosContablesRepository;
use Flexio\Modulo\Contabilidad\Repository\CuentasRepository;
use Flexio\Modulo\SubContratos\Models\SubContrato      as SubContrato;
use Flexio\Modulo\ConfiguracionContabilidad\Models\CuentaContrato;


//utils
use Flexio\Library\Util\FlexioAssets;
use Flexio\Library\Toast;

class Subcontratos extends CRM_Controller
{
    /**
     * Atributos
     */
    private $empresa_id;
    private $empresaObj;
    protected $subcontratosRepositorio;
    protected $adendaRepository;
    protected $disparador;
    private  $id_usuario;
    protected $CatalogoRepository;
    protected $ProveedoresRepository;
    protected $CentrosContablesRepository;
    protected $CuentasRepository;
    protected $DocumentosRepository;
    protected $FlexioAssets;
    protected $Toast;

    /**
     * Método constructor
     */
    public function __construct()
    {
        parent::__construct();
        //cargar los modelos
        $this->load->model('usuarios/Empresa_orm');
        $this->load->model('usuarios/Usuario_orm');
        $this->load->model('proveedores/Proveedores_orm');
        $this->load->model('contabilidad/Cuentas_orm');
        $this->load->model('contabilidad/Centros_orm');
        $this->load->model('facturas/Factura_orm');
        $this->load->model('cobros/Cobro_orm');
        //HMVC Load Modules
        $this->load->module(array('documentos'));
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresaObj = new Buscar(new Empresa_orm,'uuid_empresa');
        $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
        $this->empresa_id = $this->empresaObj->id;
        $this->id_usuario = $this->session->userdata('id_usuario');
        $this->subcontratosRepositorio = new SubContratoRepository;
        $this->adendaRepository = new AdendaRepository;
        $this->disparador = new \Illuminate\Events\Dispatcher();
        $this->disparador->listen([ActualizarSubContratoMontoEvent::class], ActualizarSubContratoListener::class);
        $this->CatalogoRepository = new CatalogoRepository;
        $this->ProveedoresRepository = new ProveedoresRepository;
        $this->CentrosContablesRepository = new CentrosContablesRepository;
        $this->CuentasRepository = new CuentasRepository;

        $this->FlexioAssets = new FlexioAssets;
        $this->Toast = new Toast;
    }

    /*public function configuracion()
    {
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
        'public/resources/compile/modulos/subcontratos/configuracion.js'
    	));

      $this->template->agregar_titulo_header('Configuracion Subcontratos');
      $this->template->agregar_breadcrumb($breadcrumb);
      $this->template->agregar_contenido($data);
      $this->template->visualizar($breadcrumb);
    }*/


    /**
     * Método de la vista de los subcontratos
     */
    public function listar()
    {
        $data = array();
        $mensaje ='';
        if(!$this->auth->has_permission('acceso'))
        {
            redirect ( '/' );
        }
        if(!empty($this->session->flashdata('mensaje')))
        {
            $mensaje = json_encode($this->session->flashdata('mensaje'));
        }
        $this->_Css();
        $this->_js();

        //breadcrumb
    	$breadcrumb = [
            "titulo" => '<i class="fa fa-file-text"></i> Subcontratos',
            "ruta" =>[
                ["nombre" => "Contratos", "activo" => false],
                ["nombre" => '<b>Subcontratos</b>',"activo" => true, 'url' => 'subcontratos/listar']
            ],
            "menu" => ["nombre" => "Crear", "url" => "subcontratos/crear", "opciones" => []]
        ];

        $this->assets->agregar_var_js(array(
            "flexio_mensaje" => Flexio\Library\Toast::getStoreFlashdata()
            ));
        $clause = array('empresa_id' => $this->empresa_id);

        //catalogo tipos de Subcontratos q no necesitan acceso
        $tipos_subcontratos = $this->CatalogoRepository->get(['modulo' => 'subcontratos', 'tipo' => 'tipo_subcontrato', 'con_acceso' => 0]);

        //Obtener los tipos de suncontrato al que el usuario tiene acceso
        $tipos_subcontrato_acceso_restringido = $this->listaTiposSubcontratosRestringidosDelUsuario();
        if(!empty($tipos_subcontrato_acceso_restringido)) {

          //si tiene acceso poner el tipo en el array del catalogo
          $tipos_subcontratos_restringidos = $this->CatalogoRepository->get(['id' => $tipos_subcontrato_acceso_restringido, 'modulo' => 'subcontratos', 'tipo' => 'tipo_subcontrato', 'con_acceso' => 1]);
          $tipos_subcontratos = $tipos_subcontratos->merge($tipos_subcontratos_restringidos);
        }

        $proveedores = new Proveedores_orm;
        $centros = new Centros_orm;
        $data['proveedores'] = $proveedores->proveedoresConSubcontratos($clause);
        $data['centros_contables']= $centros->centrosConSubcontratos($clause);
        $data['tipos_subcontratos']= $tipos_subcontratos;
        $breadcrumb["menu"]["opciones"]["#exportarListaSubContratos"] = "Exportar";

        $this->template->agregar_titulo_header('Listado de Subcontratos');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }

    /**
     * Método para mostrar la tabla de los subcontratos
     */
    public function ocultotabla($uuid = null, $modulo = null)
    {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/subcontratos/tabla.js'
            ));
        $this->load->view('tabla');
    }

    private function listaTiposSubcontratosRestringidosDelUsuario() {
      //Obtener lista de tipo subcontrato de acceso
      //restringido que el usuario puede ver.
      return Capsule::table('usuarios_tipos_subcontratos')
              ->where("usuario_id", $this->id_usuario)
              ->where("empresa_id", $this->empresa_id)
              ->pluck("tipo_subcontrato_id");
    }

    /**
     * Método listar los registros de los subcontratos en ocultotabla()
     */
    public function ajax_listar()
    {
        if(!$this->input->is_ajax_request())
        {
            return false;
        }

        $clause = array('empresa' => $this->empresa_id);

        $catalogos = $this->CatalogoRepository->get(['modulo' => 'subcontratos']);
        $tipos_subcontrato_acceso_libre = $catalogos->filter(function($option){return $option->tipo == 'tipo_subcontrato' && $option->con_acceso == 0;})->pluck("id");
        $clause["tipo_subcontrato_acceso"] = $tipos_subcontrato_acceso_libre->toArray();

        $tipos_subcontrato_acceso_restringido = $this->listaTiposSubcontratosRestringidosDelUsuario();
        if(!empty($tipos_subcontrato_acceso_restringido)) {
          $clause["tipo_subcontrato_acceso"] = array_merge($clause["tipo_subcontrato_acceso"], $tipos_subcontrato_acceso_restringido);
        }

        //se pasa el objecto para poder validar las rutas
        $jqgrid = new Flexio\Modulo\SubContratos\Services\SubContratoJqgrid($this->auth);
        $response = $jqgrid->listar($clause);

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))->_display();
        exit;
    }

    public function ajax_listar_adendas()
    {
        if(!$this->input->is_ajax_request())
        {
            return false;
        }

        $clause = array('empresa' => $this->empresa_id);
        $jqgrid = new Flexio\Modulo\SubContratos\Services\SubContratoAdendaJqgrid();
        $response = $jqgrid->listar($clause);

        $this->output->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    /**
     * Método para crear un nuevo subcontrato
     */
    public function crear()
    {
         //permisos
        $acceso = $this->auth->has_permission('acceso');
        $this->Toast->runVerifyPermission($acceso);

        //assets
        $this->FlexioAssets->run();//css y js generales
        $this->FlexioAssets->add('vars', [
            "vista" => 'crear',
            "acceso" => $acceso ? 1 : 0,
            "permiso_adenda" =>  $acceso ? 1 : 0,
        ]);

        //breadcrumb
    	$breadcrumb = [
            "titulo" => '<i class="fa fa-file-text"></i> Crear Subcontrato',
            "ruta" =>[
                ["nombre" => "Contratos", "activo" => false],
                ["nombre" => 'Subcontratos',"activo" => false, 'url' => 'subcontratos/listar'],
                ["nombre" => '<b>Crear</b>',"activo" => true]
            ]
        ];

        //render
    	$this->template->agregar_titulo_header('Crear Subcontrato');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido([]);
    	$this->template->visualizar();
    }

    public function ocultoformulario($info = [])
    {
        $clause = ['empresa_id'=>$this->empresa_id,'transaccionales'=>true];
        $catalogos = $this->CatalogoRepository->get(['modulo' => 'subcontratos']);
        $cuentas_contratos = new CuentaContrato;
        $this->FlexioAssets->add('js',['public/resources/compile/modulos/subcontratos/formulario.js']);
        $this->FlexioAssets->add('vars',[
            //'proveedores' => $this->ProveedoresRepository->getCollectionProveedores($this->ProveedoresRepository->get($clause)),
            'proveedores' => collect([]),
            'centros_contables' => $this->CentrosContablesRepository->getCollectionCentrosContables($this->CentrosContablesRepository->get($clause)),
            'estados' => $catalogos->filter(function($option){return $option->tipo == 'estado';}),
            'tipos_subcontratos' => $catalogos->filter(function($option){return $option->tipo == 'tipo_subcontrato';}),
            'cuentas' => $this->CuentasRepository->get($clause),
            'cuentas_contrato' => collect($cuentas_contratos->get()->toarray()),
        ]);

        $this->load->view('formulario');
    }

    public function ocultoformularioExportarCuenta(){
        $this->load->view('exportar_estado_cuenta');
    }

    public function ocultoformularioAdenda($info = [])
    {
        $this->assets->agregar_js(array(
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/modules/subcontratos/tabla-componente.js',
            'public/assets/js/modules/subcontratos/vue.comentario.js',
            'public/assets/js/modules/subcontratos/formulario_adenda.js'
        ));
        $data['codigo'] = $this->_generar_codigo_adenda();
        $data['info'] = $info;
        $data['cuenta_ingreso'] = Cuentas_orm::transaccionalesDeEmpresa($this->empresa_id)->activas()->get();
        $this->load->view('formulario_adenda',$data);
        $this->load->view('componente',$data);
        $this->load->view('notas_creditos/comentarios');
    }

    /**
     * Método para mostrar el subcontrato
     */
    public function ver($uuid = null)
    {
        $data = [];

        //permisos
        $acceso = $this->auth->has_permission('acceso','subcontratos/ver/(:any)');
          $permiso_adenda = 0;


        $this->Toast->runVerifyPermission($acceso);

        //Cargo el registro
        $registro = $this->subcontratosRepositorio->findByUuid($uuid);
        $registro->load('comentario_timeline', 'adenda.adenda_montos');
        //$subcontrato->load('subcontrato_montos', 'tipo_abono', 'tipo_retenido', 'proveedor', 'adenda','comentario_timeline','subcontratos_asignados');

        //assets
        $this->FlexioAssets->run();//css y js generales

        $this->assets->agregar_css(array(
            'public/assets/css/plugins/jquery/jquery.fileupload.css'
        ));
        $this->assets->agregar_js(array(
            'public/assets/js/plugins/jquery/fileupload/jquery.fileupload.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            'public/assets/js/modules/subcontratos/detalle.js',
            'public/assets/js/modules/subcontratos/exportar_estado_cuenta.js'
        ));

        if($acceso == 1 && $registro->estado == 'por_aprobar' )
          $permiso_adenda = true;

        $subcontrato = $this->subcontratosRepositorio->getCollectionSubcontrato($registro);

        $this->FlexioAssets->add('vars', [
            "vista" => 'ver',
            "acceso" => $acceso ? 1 : 0,
            "permiso_adenda" => $permiso_adenda,
            "subcontrato" => $subcontrato,
        ]);

        if($registro->estado == 'vigente'){
            $breadcrumb = [
                "titulo" => '<i class="fa fa-file-text"></i> Detalle del Contrato: ' .$registro->codigo,
                "ruta" =>[
                    ["nombre" => "Contratos", "activo" => false],
                    ["nombre" => 'Subcontratos',"activo" => false, 'url' => 'subcontratos/listar'],
                    ["nombre" => '<b>Detalle</b>',"activo" => true]
                ],
                "menu" => [
                    "nombre" => "Acci&oacute;n",
                    "url" => "#",
                    "opciones" => array(
                        '/subcontratos/agregar_adenda/'.$registro->uuid_subcontrato => 'Crear adenda'
                     )
                ]
            ];

            if( $registro->subcontrato_montos()->sum('monto') > $registro->anticipos_no_anulados->sum('monto')){
               $breadcrumb['menu']['opciones']['/anticipos/crear/?subcontrato='.$registro->uuid_subcontrato] ='Crear anticipo';  //Nuevo;
            }
            $breadcrumb['menu']['opciones']['#exportar_adenda'] ='Exportar adenda';
            $breadcrumb['menu']['opciones']['#subirArchivoBtn'] ='Subir documento';
            $breadcrumb['menu']['opciones']['#exportarEstadoCuenta'] ='Imprimir estado de subcontrato';
            //$breadcrumb['menu']['opciones']['subcontratos/historial/'.$registro->uuid_factura] = 'Ver bit&aacute;cora';
         }else{
        $breadcrumb = [
            "titulo" => '<i class="fa fa-file-text"></i> Detalle del Contrato: ' .$registro->codigo,
            "ruta" =>[
                ["nombre" => "Contratos", "activo" => false],
                ["nombre" => 'Subcontratos',"activo" => false, 'url' => 'subcontratos/listar'],
                ["nombre" => '<b>Detalle</b>',"activo" => true]
            ],
            "menu" => [
                "nombre" => "Acci&oacute;n",
                "url" => "#",
                "opciones" => array(
                    '#exportar_adenda'=>'Exportar Adenda',
                    '#subirArchivoBtn' => 'Subir Documento'
                )
            ]
        ];
        }
        $breadcrumb['menu']['opciones']['subcontratos/historial/'.$registro->uuid_subcontrato] = 'Ver bit&aacute;cora';
        //subpanels
        $data['subcontrato_id'] = $registro->id;
        $subpanels = [
          'adendas'=>['subcontrato'=>$registro->id],
          'facturas_compras'=>['subcontrato'=>$registro->id],
          'anticipos' => ['subcontrato'=>$registro->id],
          'pagos' => ['subcontrato'=>$registro->id],
          'documentos' => ['subcontrato' => $registro->id]
        ];
        $data['subpanels'] = $subpanels;
        //render
        $this->template->agregar_titulo_header('Subcontrato');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    public function agregar_adenda($uuid = null)
    {
        $acceso = 1;
        $mensaje = array();
        $data = array();
        $subcontrato = $this->subcontratosRepositorio->findByUuid($uuid);

        if(!$this->auth->has_permission('acceso','subcontratos/agregar_adenda/(:any)') && !is_null($subcontrato))
        {
            // No, tiene permiso
            $acceso = 0;
            $mensaje = array(
                'estado'  =>500,
                'mensaje' => ' <b>Usted no cuenta con permiso para esta solicitud</b>',
                'clase'   => 'alert-danger'
            );
        }
        $this->_Css();
        $this->_js();
        $this->assets->agregar_js(array(
            'public/assets/js/default/vue.js',
            'public/assets/js/modules/subcontratos/formulario_ver.js',
            'public/assets/js/modules/subcontratos/eventos.js',
        ));
        $subcontrato->load('subcontrato_montos', 'tipo_abono', 'tipo_retenido', 'proveedor', 'adenda', 'comentario_timeline');
        $data['subcontrato'] = $subcontrato->toArray();
        $this->assets->agregar_var_js(array(
            "vista"        => 'ver',
            "acceso"       => $acceso,
            "subcontrato"  => $subcontrato,
            "proveedor_id" => $subcontrato['proveedor']['uuid_proveedor']
        ));
        $breadcrumb = array(
            //"titulo" => '<i class="fa fa-file-text"></i> Adenda: ' .$subcontrato->codigo. ' / Crear',
            "titulo" => '<i class="fa fa-file-text-o"></i> Contratos: adendas',
            "ruta" => array(
                0 => ["nombre" => "Subcontrato", "activo" => false],
                1 => ["nombre" => '<b>'.$subcontrato->codigo.'</b>',
                      "activo" => true,
                      "url" => 'subcontratos/ver/'.$uuid],
                2 => ["nombre" => '<b>'.$this->_generar_codigo_adenda().'</b>', "activo" => false]
                ),
            "menu" => []
        );

        $this->template->agregar_titulo_header('Subcontrato');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }

    public function ajax_guardar_comentario(){
        if(!$this->input->is_ajax_request()){
            return false;
        }

        $model_id       = $this->input->post('modelId');
        $comentario     = $this->input->post('comentario');
        $uuid_usuario   = $this->session->userdata('huuid_usuario');
        $usuario        = Usuario_orm::findByUuid($uuid_usuario);
        $comentarioArr  = ['comentario'=>$comentario,'usuario_id'=>$usuario->id];

        $adenda         = $this->adendaRepository->agregarComentario($model_id, $comentarioArr);
        $adenda->load('comentario');

        $lista_comentario = $adenda->comentario()->orderBy('created_at','desc')->get();
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($lista_comentario->toArray()))->_display();
        exit;
    }

    public function editar_adenda($uuid = null)
    {
        $acceso         = 1;
        $mensaje        = array();
        $data           = array();

        $clause         = [];
        $clause["empresa_id"]   = $this->empresa_id;
        $clause["uuid_adenda"]  = $uuid;
        $adenda                 = $this->adendaRepository->findBy($clause);
        $subcontrato            = $this->subcontratosRepositorio->findByUuid($adenda->subcontrato->uuid_subcontrato);

        if(!$this->auth->has_permission('acceso','subcontratos/editar_adenda/(:any)') && !is_null($subcontrato))
        {
            // No, tiene permiso
            $acceso = 0;
            $mensaje = array(
                'estado'  =>500,
                'mensaje' => ' <b>Usted no cuenta con permiso para esta solicitud</b>',
                'clase'   => 'alert-danger'
            );
        }
        $this->_Css();
        $this->_js();
        $this->assets->agregar_js(array(
            'public/assets/js/default/vue.js',
            'public/assets/js/default/vue-validator.min.js',
            'public/assets/js/default/vue-resource.min.js',
            'public/assets/js/modules/subcontratos/formulario_ver.js',
            'public/assets/js/modules/subcontratos/eventos.js',
        ));
        $subcontrato->load('subcontrato_montos', 'tipo_abono', 'tipo_retenido', 'proveedor', 'adenda');
        $adenda->load('adenda_montos','comentario');
        $data['adenda']         = $adenda->toArray();
        $data['subcontrato']    = $subcontrato->toArray();
        $this->assets->agregar_var_js(array(
            "vista"         => 'editar',
            "acceso"        => $acceso,
            "subcontrato"   => $subcontrato,
            "adenda"        => $adenda,
            "proveedor_id"  => $subcontrato['proveedor']['uuid_proveedor']
        ));
        $breadcrumb = array(
            //"titulo" => '<i class="fa fa-file-text"></i> Adenda: ' .$subcontrato->codigo. ' / Crear',
            "titulo" => '<i class="fa fa-file-text-o"></i> Contratos: adendas',
            "ruta" => array(
                0 => ["nombre" => "Subcontrato", "activo" => false],
                1 => ["nombre" => '<b>'.$subcontrato->codigo.'</b>',
                      "activo" => true,
                      "url" => 'subcontratos/ver/'.$adenda->subcontrato->uuid_subcontrato],
                2 => ["nombre" => '<b>'.$subcontrato->adenda[0]->codigo.'</b>', "activo" => false]
                ),
            "menu" => []
        );

        $this->template->agregar_titulo_header('Subcontrato');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }

    public function ocultoTablaAdendas($id = null, $modulo = null)
    {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/subcontratos/tabla_adendas.js'
        ));
        if(is_array($id))
        {
            $this->assets->agregar_var_js([
                "campo" => collect($id)
            ]);
        }else if(!is_array($id) && !is_null($id))
        {
            $this->assets->agregar_var_js(array(
                "subcontrato_id" => $id
                ));
        }
        $this->load->view('tabla_adendas');
    }

    /**
     * Método para generar código del subcontrato
     */
    private function _generar_codigo()
    {
        $clause_empresa = ['empresa_id' => $this->empresa_id];
        $total = $this->subcontratosRepositorio->lista_totales($clause_empresa);
        $year = Carbon::now()->format('y');
        $codigo = Util::generar_codigo('CT'.$year,$total + 1);
        return $codigo;
    }

    /**
     * Método para generar código de la adenda
     */
    private function _generar_codigo_adenda()
    {
        $clause_empresa = ['empresa_id' => $this->empresa_id];
        $total = $this->adendaRepository->lista_totales($clause_empresa);
        $year = Carbon::now()->format('y');
        //$codigo = Util::generar_codigo('AD'.$year,$total + 1);
        $codigo = Util::generar_codigo('AD'.$year,$total + 2);//Cambio de JA
        return $codigo;
    }

    /**
     * Método para guardar subcontratos
     */
    public function guardar()
    {
        if($_POST)
        {
            $post = $this->input->post();
             if(empty($post['campo']['id']))
            {
                $post['campo']['codigo'] = $this->_generar_codigo();
                $post["campo"]["empresa_id"] = $this->empresa_id;
                $post["campo"]["creado_por"] = $this->session->userdata('id_usuario');
            }

            Capsule::beginTransaction();
            try {

                $registro = $this->subcontratosRepositorio->create($post);

            } catch (\Exception $e) {
                log_message('error', " __METHOD__  ->  , Linea:  __LINE__  --> " . $e->getMessage() . "\r\n");
                Capsule::rollback();
                $this->Toast->setUrl('subcontratos/listar')->run("exception",[$e->getMessage()]);
            }

            if(count($registro)){
                Capsule::commit();
                $this->Toast->run("success",[$registro->codigo]);
            }else{
                $this->Toast->run("error");
            }

            redirect(base_url('subcontratos/listar'));
        }
    }

    /**
     * Método para guardar adendas
     */
    public function guardar_adenda()
    {
        if($_POST)
        {

//            echo "<pre>";
//            print_r($_POST);
//            echo "<pre>";
//            die();
            $usuario                         = Usuario_orm::findByUuid($this->uuid_usuario);
            $array_adenda                    = Util::set_fieldset("campo");
            $subcontrato                     = $this->subcontratosRepositorio->findBy($array_adenda['subcontrato_id']);
            $array_adenda['fecha']           = $_POST['campo']['fecha'];
            $array_adenda['codigo']          = (isset($array_adenda["codigo"]) and !empty($array_adenda["codigo"])) ? $array_adenda["codigo"] : $this->_generar_codigo_adenda();
            //$array_adenda['fecha']           = Carbon::createFromFormat('d/m/Y',$array_adenda['fecha'],'America/Panama');
            $array_adenda['empresa_id']      = $this->empresa_id;
            $array_adenda['monto_acumulado'] = $subcontrato->monto_subcontrato + $array_adenda['monto_adenda'];
            $array_adenda['usuario_id']      = $usuario->id;

            $fieldset_item = [];
            $j = 0;
            foreach ($_POST["components"] as $item)
            {
                $fieldset_item[$j]= Util::set_fieldset("components", $j);
                $fieldset_item[$j]['empresa_id'] = $this->empresa_id;
                $j++;
            }
            $create = array('adenda' => $array_adenda, 'montos' => $fieldset_item);

            $adenda = Capsule::transaction(function() use ($create){
                try{
                    return $this->adendaRepository->create($create);
                }catch(Illuminate\Database\QueryException $e){
                    log_message('error', __METHOD__." ->". ", Linea: ". __LINE__ ." --> ". $e->getMessage()."\r\n");
                }
            });

            if(!is_null($adenda))
            {
                $this->disparador->fire(new ActualizarSubContratoMontoEvent($adenda, $subcontrato));
                $mensaje = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha creado la adenda '.$adenda->codigo);
            }else{
                $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error! Su solicitud no fue procesada</b> ');
            }
            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('subcontratos/ver/'.$subcontrato->uuid_subcontrato));
        }
    }

    public function ajax_subcontrato_info()
    {
        $uuid = $tipo = $this->input->post('uuid');
        $subcontrato = $this->subcontratosRepositorio->findByUuid($uuid);
        $subcontrato->load('proveedor');
        $subcontrato->toArray();

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($subcontrato))->_display();
        exit;
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
            'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
            'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
            'public/assets/js/plugins/jquery/sweetalert/sweetalert.css',
            'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
            'public/assets/css/plugins/bootstrap/select2.min.css',
            'public/assets/css/modules/stylesheets/subcontratos.css',
            'public/assets/css/plugins/jquery/jquery.fileupload.css'
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
            'public/assets/js/default/accounting.min.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/plugins/jquery/sweetalert/sweetalert.min.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/plugins/bootstrap/select2/select2.min.js',
            'public/assets/js/plugins/bootstrap/select2/es.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
            'public/assets/js/default/toast.controller.js',
            'public/assets/js/modules/subcontratos/plugins.js',
            'public/assets/js/default/vue/directives/select2.js',
            'public/assets/js/plugins/jquery/fileupload/jquery.fileupload.js'
        ));
    }

    function ocultoformulariocomentarios() {

        $data = array();

        $this->assets->agregar_js(array(
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/modules/subcontratos/vue.comentariosub.js',
            'public/assets/js/modules/subcontratos/formulario_comentariosub.js'
        ));

        $this->load->view('formulario_comentarios');
        $this->load->view('comentarios');

    }

    function ajax_guardar_comentario_subcontrato() {

        if(!$this->input->is_ajax_request()){
            return false;
        }
        $model_id   = $this->input->post('modelId');
        $comentario = $this->input->post('comentario');
        $comentario = ['comentario'=>$comentario,'usuario_id'=>$this->id_usuario];
        $subcontrato = $this->subcontratosRepositorio->agregarComentario($model_id, $comentario);
        $subcontrato->load('comentario_timeline');

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($subcontrato->comentario_timeline->toArray()))->_display();
        exit;
    }

    function documentos_campos(){
    return array(
            array(
                "type" => "hidden",
                "name" => "subcontrato_id",
                "id" => "subcontrato_id",
                "class" => "form-control",
                "readonly" => "readonly",
            ));
    }

    function ajax_guardar_documentos()
    {
    	if(empty($_POST)){
    		return false;
    	}

    	$subcontrato_id = $this->input->post('subcontrato_id', true);
    	$modeloInstancia = SubContrato::find($subcontrato_id);

    	$this->documentos->subir($modeloInstancia);
    }

    function exportar_subcontrato_estado_cuenta(){

         if (empty($_POST)) {
            exit();
        }

        $id = $this->input->post('subcontrato_id', true);

        if (empty($id)) {
            exit();
        }
        $modeloInstancia = SubContrato::find($id);
        //$nombre_archivo = "reporte_de_subcontrato_".$modeloInstancia->proveedor->nombre."xlsx";
        $nombre_archivo = "reporte_de_subcontrato.xlsx";
        header('Content-Type: application/vnd.openxmlformatsofficedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$nombre_archivo.'"');
        header('Cache-Control: max-age=0');
        try{
          $excell = new Flexio\Modulo\SubContratos\Exportar\Excell\EstadoCuentaProveedor();
          $formulario = $excell->generarExcell($modeloInstancia);
          $objWriter = \PHPExcel_IOFactory::createWriter($formulario, 'Excel2007');
          $objWriter->save('php://output');
        }catch(\Exception $e) {
          log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
       }

    }
    public function historial($uuid = null)
    {
        $acceso = 1;
        $mensaje = array();
        $data = array();

        $registro = $this->subcontratosRepositorio->findByUuid($uuid);
        //assets
        $this->FlexioAssets->run();//css y js generales

        $this->assets->agregar_js(array(
            'public/resources/compile/modulos/subcontratos/historial.js',
        ));

        $breadcrumb = array(
            'titulo' => '<i class="fa fa-shopping-cart"></i> Bit&aacute;cora de Subcontrato: '.$registro->codigo,
        );
        $registro->load('historial');
        $historial = $registro->historial->map(function ($factHist) use ($registro) {
            return [
                'id' => $factHist->id,
                'titulo' => $factHist->titulo,
                'codigo' => $registro->codigo,
                'descripcion' => $factHist->descripcion,
                'antes' => $factHist->antes,
                'despues' => $factHist->despues,
                'tipo' => $factHist->tipo,
                'nombre_usuario' => $factHist->nombre_usuario,
                'hace_tiempo' => $factHist->cuanto_tiempo,
                'fecha_creacion' => $factHist->fecha_creacion,
                'hora' => $factHist->hora,
            ];
        });
        $this->assets->agregar_var_js(array(
            'historial' => $historial,
        ));
        $this->template->agregar_titulo_header('Facturas de compras');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }
}

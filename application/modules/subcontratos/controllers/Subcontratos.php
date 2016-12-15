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

        $proveedores = new Proveedores_orm;
        $centros = new Centros_orm;
        $data['proveedores'] = $proveedores->proveedoresConSubcontratos($clause);
        $data['centros_contables']= $centros->centrosConSubcontratos($clause);
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

    /**
     * Método listar los registros de los subcontratos en ocultotabla()
     */
    public function ajax_listar()
    {
        if(!$this->input->is_ajax_request())
        {
            return false;
        }

        $proveedor      = $this->input->post('proveedor', true);
        $uuid_proveedor = $this->input->post('proveedor_id', true);
        $monto1          = $this->input->post('monto1', true);
        $monto2          = $this->input->post('monto2', true);
        $codigo         = $this->input->post('numero_subcontrato', true);
        $centro         = $this->input->post('centro', true);
        $estado         = $this->input->post('estado', true);
        $clause         = array('empresa_id' => $this->empresa_id);

        if(!empty($uuid_proveedor))
        {
            $proveedorObj = new Buscar(new Proveedores_orm, 'uuid_proveedor');
            $proveedor = $proveedorObj->findByUuid($uuid_proveedor);
            $clause['proveedor_id'] = $proveedor->id;
        } elseif(!empty($proveedor)){
            $clause['proveedor_id'] = $proveedor;
        }

        if(!empty($monto1))  $clause['monto1'] = $monto1;
        if(!empty($monto2))  $clause['monto2'] = $monto2;
        if(!empty($codigo)) $clause['codigo'] = $codigo;
        if(!empty($centro)) $clause['centro_id'] = $centro;
        if(!empty($estado)) $clause['estado'] = $estado;

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = $this->subcontratosRepositorio->lista_totales($clause);

        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $subcontratos = $this->subcontratosRepositorio->listar($clause ,$sidx, $sord, $limit, $start);

        $response          = new stdClass();
        $response->page    = $page;
        $response->total   = $total_pages;
        $response->records = $count;

        if(!empty($subcontratos->toArray()))
        {
            $i = 0;
            foreach ($subcontratos as $row)
            {
                $hidden_options = "";
                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row->uuid_subcontrato .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $hidden_options = '<a href="'. base_url('subcontratos/ver/'. $row->uuid_subcontrato) .'" data-id="'. $row->uuid_subcontrato .'" class="btn btn-block btn-outline btn-success">Ver Contrato</a>';

                if($row->facturable and $this->auth->has_permission('acceso', 'facturas_compras/crear/(:any)')){
                    $hidden_options .= '<a href="'.base_url('facturas_compras/crear/subcontrato'. $row->id).'" class="btn btn-block btn-outline btn-success">Agregar Factura</a>';
                }

                $hidden_options .= '<a href="'. base_url('subcontratos/agregar_adenda/'. $row->uuid_subcontrato) .'" data-id="'. $row->uuid_subcontrato .'" class="btn btn-block btn-outline btn-success">Crear adenda</a>';
                $hidden_options .= '<a href="' . base_url('anticipos/crear/?subcontrato=' . $row->uuid_subcontrato) . '" data-id="'. $row->uuid_subcontrato .'" class="btn btn-block btn-outline btn-success">Crear anticipo</a>';
                $response->rows[$i]["id"] = $row->uuid_subcontrato;
                $response->rows[$i]["cell"] = array(
                    $row->uuid_subcontrato,
                    '<a style="color:blue;" class="link" href="'. base_url('subcontratos/ver/'. $row->uuid_subcontrato) .'">'.$row->codigo.'</a>',
                    '<a class="link">'.$row->proveedor->nombre.' '.$row->proveedor->apellido.'</a>',
                    $row->present()->monto_original,
                    $row->present()->monto_adenda,
                    $row->present()->monto_subcontrato,
                    $row->present()->facturado,
                    $row->present()->por_facturar,
                    $row->centro_contable->nombre,
                    $row->present()->estado,
                    $link_option,
                    $hidden_options
                    );
                $i++;
            }
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    public function ajax_listar_adendas()
    {
        if(!$this->input->is_ajax_request())
        {
            return false;
        }
        $subcontrato_id = $this->input->post('subcontrato_id', true);
        $clause = array('empresa_id' => $this->empresa_id);

        if(!empty($subcontrato_id)) $clause['subcontrato_id'] = $subcontrato_id;

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = $this->adendaRepository->lista_totales($clause);

        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $adendas = $this->adendaRepository->listar($clause ,$sidx, $sord, $limit, $start);

        $response          = new stdClass();
        $response->page    = $page;
        $response->total   = $total_pages;
        $response->records = $count;

        if(!empty($adendas->toArray()))
        {
            $i = 0;
            foreach ($adendas as $row) {
                $hidden_options = "";
                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row->uuid_adenda .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $hidden_options = '<a href="'. base_url('subcontratos/editar_adenda/'. $row->uuid_adenda) .'" data-id="'. $row->uuid_adenda .'" class="btn btn-block btn-outline btn-success">Ver Adenda</a>';
                $response->rows[$i]["id"] = $row->uuid_adenda;
                $response->rows[$i]["cell"] = array(
                    $row->uuid_adenda,
                    '<a style="color:blue;" class="link" href="'. base_url('subcontratos/editar_adenda/'. $row->uuid_adenda) .'">'.$row->codigo.'</a>',
                    $row->fecha,
                    "$".number_format($row->monto_adenda, 2, '.', ','),
                    "$".number_format($row->monto_acumulado, 2, '.', ','),
                    $link_option,
                    $hidden_options
                    );
                $i++;
            }
        }
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
            "acceso" => $acceso ? 1 : 0
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
        $this->FlexioAssets->add('js',['public/resources/compile/modulos/subcontratos/formulario.js']);
        $this->FlexioAssets->add('vars',[
            'proveedores' => $this->ProveedoresRepository->getCollectionProveedores($this->ProveedoresRepository->get($clause)),
            'centros_contables' => $this->CentrosContablesRepository->getCollectionCentrosContables($this->CentrosContablesRepository->get($clause)),
            'estados' => $catalogos->filter(function($option){return $option->tipo == 'estado';}),
            'cuentas' => $this->CuentasRepository->get($clause),
        ]);

        $this->load->view('formulario');
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
        $acceso = $this->auth->has_permission('acceso');
        $this->Toast->runVerifyPermission($acceso);

        //Cargo el registro
        $registro = $this->subcontratosRepositorio->findByUuid($uuid);
        $registro->load('comentario_timeline');
        //$subcontrato->load('subcontrato_montos', 'tipo_abono', 'tipo_retenido', 'proveedor', 'adenda','comentario_timeline','subcontratos_asignados');

        //assets
        $this->FlexioAssets->run();//css y js generales
        $this->FlexioAssets->add('vars', [
            "vista" => 'ver',
            "acceso" => $acceso ? 1 : 0,
            "subcontrato" => $this->subcontratosRepositorio->getCollectionSubcontrato($registro),
        ]);

        //breadcrumb
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
                "opciones" => array('/subcontratos/agregar_adenda/'.$registro->uuid_subcontrato => 'Crear Adenda',
                    '#exportar_adenda'=>'Exportar Adenda')
            ]
        ];

        //subpanels
        $data['subcontrato_id'] = $registro->id;

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
        //dd($subcontrato);
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
        $subcontrato->load('subcontrato_montos', 'tipo_abono', 'tipo_retenido', 'proveedor', 'adenda');
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
        //dd($subcontrato);
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
        if(!is_null($id))
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
        $codigo = Util::generar_codigo('AD'.$year,$total + 1);
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
            //dd($create);
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
            'public/assets/js/default/vue/directives/select2.js'
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
}

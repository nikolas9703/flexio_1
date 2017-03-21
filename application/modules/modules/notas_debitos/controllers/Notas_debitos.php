<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Flexio
 * @subpackage Controller
 * @category   Notas de Debito
 * @author     Pensanomica Team
 * @link       http://www.pensanomica.com
 * @copyright  04/18/2016
 */

use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\NotaDebito\Repository\NotaDebitoRepository;
use Flexio\Modulo\NotaDebito\Repository\CatalogoNotaDebitoRepository as CatalogoNotaDebito;
use Flexio\Library\Util\FormRequest;
use Flexio\Modulo\Proveedores\Repository\ProveedoresRepository;
use Flexio\Modulo\Contabilidad\Repository\ImpuestosRepository;
use Flexio\Modulo\FacturasCompras\Repository\FacturaCompraRepository;
use Flexio\Modulo\CentrosContables\Repository\CentrosContablesRepository;

//transacciones
use Flexio\Modulo\NotaDebito\Transacciones\NotasDebitosFacturas;

//utils
use Flexio\Library\Util\FlexioSession;
use Flexio\Library\Util\FlexioAssets;
use Flexio\Library\Toast;

class Notas_debitos extends CRM_Controller
{
    protected $catalogo;
    protected $notaDebitoRepository;

    //transacciones
    protected $NotasDebitosFacturas;
    protected $ProveedoresRepository;
    protected $ImpuestosRepository;
    protected $FacturaCompraRepository;
    protected $CentrosContablesRepository;

    //utils
    protected $FlexioSession;
    protected $FlexioAssets;
    protected $Toast;

    function __construct(){
        parent::__construct();
        $this->load->model('usuarios/Usuario_orm');
        $this->load->model('usuarios/Empresa_orm');
        $this->load->model('usuarios/Roles_usuarios_orm');
        $this->load->model('roles/Rol_orm');
        $this->load->model('clientes/Cliente_orm');
        $this->load->model('contabilidad/Impuestos_orm');
        $this->load->model('contabilidad/Cuentas_orm');
        $this->load->model('contabilidad/Centros_orm');
        $this->load->model('contabilidad/Centros_orm');
        $this->load->model('facturas_compras/Facturas_compras_orm');
        $this->load->model('pagos/Pagos_orm');
        Carbon::setLocale('es');
        setlocale(LC_TIME, 'Spanish');
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresaObj  = new Buscar(new Empresa_orm,'uuid_empresa');
        $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
        $this->empresa_id   = $this->empresaObj->id;
        $this->notaDebitoRepository = new NotaDebitoRepository;
        $this->catalogo = new CatalogoNotaDebito;
        $this->ProveedoresRepository = new ProveedoresRepository();
        $this->ImpuestosRepository = new ImpuestosRepository;
        $this->FacturaCompraRepository = new FacturaCompraRepository;
        $empresaObj = new Buscar(new Empresa_orm,'uuid_empresa');
        //$usuario = Usuario_orm::findByUuid($uuid_usuario);
        //$this->id_usuario = $usuario->id;
        $this->load->module(array('documentos'));
        //trasancciones
        $this->NotasDebitosFacturas = new NotasDebitosFacturas();
        $this->CentrosContablesRepository = new CentrosContablesRepository;

        //utils
        $this->FlexioSession = new FlexioSession;
        $this->FlexioAssets = new FlexioAssets;
        $this->Toast = new Toast;
    }

    public function listar(){
        if (! $this->auth->has_permission ( 'acceso' )) {
            // No, tiene permiso, redireccionarlo.
            redirect ( '/' );
        }

        $data = array();
        $this->_css();
        $this->_js();
        $this->assets->agregar_js(array(
            'public/assets/js/modules/nota_debito/listar.js',
            'public/assets/js/default/toast.controller.js'
        ));

        $breadcrumb = array( "titulo" => '<i class="fa fa-shopping-cart"></i> Notas de cr&eacute;dito de proveedor',
            "ruta" => array(
                0 => array(
                    "nombre" => "Compras",
                    "activo" => false
                ),
                1 => array(
                    "nombre" => '<b>Notas de cr&eacute;dito de proveedor</b>',
                    "activo" => true
                )
            ),
            "menu" => array(
                "nombre" => "Crear",
                "url"	 => "notas_debitos/crear",
                "opciones" => array()
            )
        );

        $this->assets->agregar_var_js(array(
            "flexio_mensaje" => Toast::getStoreFlashdata()
        ));
        $clause = array('empresa_id'=> $this->empresa_id);

        $proveedores = new Proveedores_orm;

        $usuarios = Usuario_orm::findById($this->FlexioSession->usuarioId())->get();

        $data['proveedores'] = $proveedores->proveedoresConNotaDebito($clause);
        $data['etapas'] = $this->catalogo->getEtapas();
        $data['vendedores'] = $usuarios;
        $breadcrumb["menu"]["opciones"]["#exportarNotaDebito"] = "Exportar";
        $this->template->agregar_titulo_header('Listado de Notas de cr&eacute;dito de proveedor');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }

    public function ocultotabla($sp_string_var = '')
    {
        $sp_array_var = explode('=', $sp_string_var);
        if (count($sp_array_var) == 2) {

            $this->assets->agregar_var_js(array(
                $sp_array_var[0] => $sp_array_var[1]
            ));

        }

        //heredado de estrutura vieja
        if (preg_match("/proveedores/i", $this->router->fetch_class())) {
            $this->assets->agregar_var_js(array(
                "sp_proveedor_uuid" => $sp_string_var
            ));
        }

        $this->FlexioAssets->add('js', ['public/assets/js/modules/nota_debito/tabla.js']);
        $this->load->view('tabla');
    }


    public function ajax_listar(){
        if(!$this->input->is_ajax_request()){
            return false;
        }

        $uuid_cliente = $this->input->post("cliente_id");
        $proveedor_id = $this->input->post('proveedor_id',TRUE);
        $hasta = $this->input->post('hasta',TRUE);
        $desde = $this->input->post('desde',TRUE);
        $estado = $this->input->post('etapa',TRUE);
        $vendedor = $this->input->post('vendedor',TRUE);
        $codigo = $this->input->post('codigo',TRUE);
        $no_nota_credito = $this->input->post('no_nota_credito',TRUE);
        $clause = array('empresa_id' => $this->empresaObj->id);
        $montos_de  = $this->input->post('monto1', true);
        $montos_a   = $this->input->post('monto2', true);

        if(!empty($proveedor_id)){
            $clause['proveedor_id'] = $proveedor_id;
        }

        //filtros de centros contables del usuario
        $centros = $this->FlexioSession->usuarioCentrosContables();
        if(!in_array('todos', $centros))
        {
            $clause['centros_contables'] = $centros;
        }

        if(!empty($desde)) $clause['fecha_desde'] = Carbon::createFromFormat('d/m/Y',$desde,'America/Panama')->format('Y-m-d 00:00:00');
        if(!empty($hasta)) $clause['fecha_hasta'] = Carbon::createFromFormat('d/m/Y',$hasta,'America/Panama')->format('Y-m-d 23:59:59');;
        if(!empty($estado)) $clause['estado'] = $estado;
        if(!empty($codigo)) $clause['codigo'] = $codigo;
        if(!empty($no_nota_credito)) $clause['no_nota_credito'] = $no_nota_credito;
        if(!empty($vendedor)) $clause['creado_por'] = $vendedor;

        if(!empty($montos_de)){
            $clause['montos_de'] = $montos_de;
        }
        if(!empty($montos_a)){
            $clause['montos_a'] = $montos_a;
        }

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();

        $count = $this->notaDebitoRepository->lista_totales($clause);
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

        $notas_debitos = $this->notaDebitoRepository->listar($clause ,$sidx, $sord, $limit, $start);

        $response = new stdClass();
        $response->page     = $page;
        $response->total    = $total_pages;
        $response->records  = $count;

        if(!is_null($notas_debitos)){
            $i=0;
            foreach($notas_debitos as $row){
                $hidden_options = "";
                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row->uuid_nota_debito .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $hidden_options = '<a href="'. base_url('notas_debitos/ver/'. $row->uuid_nota_debito) .'" data-id="'. $row->uuid_nota_debito .'" class="btn btn-block btn-outline btn-success">Ver Notas Debitos</a>';

                $hidden_options .= '<a href="javascript:" data-id="'. $row->uuid_nota_debito .'" class="btn btn-block btn-outline btn-success subirArchivoBtn">Subir Documentos</a>';

                $proveedor = $row->proveedor;
                $vendedor = $row->vendedor;
                $etapa = $row->etapa_catalogo;

                $response->rows[$i]["id"] = $row->uuid_nota_debito;
                $response->rows[$i]["cell"] = array(
                    $row->uuid_venta,
                    '<a class="link" href="'. base_url('notas_debitos/ver/'. $row->uuid_nota_debito) .'" >'.$row->codigo.'</a>',
                    $row->no_nota_credito,
                    isset($proveedor->nombre)?'<a class="link">'.$proveedor->nombre.'</a>':'',
                    $row->fecha,
                    '<label class="totales-success">$' . number_format($row->total, 2) .'</label>',
                    isset($vendedor->nombre) ? '<a class="link">'.$vendedor->nombre.' '.$vendedor->apellido.'</a>' : '',
                    '<label class="label label-'.$etapa->color_label.'">'.$etapa->valor.'</label>',
                    $link_option,
                    $hidden_options
                );
                $i++;
            }
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response))->_display();
        exit;


    }

    public function crear()
    {
        //permisos
        $acceso = $this->auth->has_permission('acceso');
        $this->Toast->runVerifyPermission($acceso);

        //assets
        $this->FlexioAssets->run();//css y js generales
        $this->FlexioAssets->add('css', ['public/assets/css/modules/stylesheets/nota_debito.css']);
        $this->FlexioAssets->add('js', ['public/assets/js/default/operaciones.js']);
        $this->FlexioAssets->add('vars', [
            "vista" => 'crear',
            "acceso" => $acceso ? 1 : 0
        ]);

        //breadcrumb
        $breadcrumb = [
            "titulo" => '<i class="fa fa-shopping-cart"></i> Notas de cr&eacute;dito de proveedor: Crear ',
            "ruta" => [
                ["nombre" => "Compras", "activo" => false],
                ["nombre" => "Notas de cr&eacute;dito de proveedor", "activo" => false, "url" => 'notas_debitos/listar'],
                ["nombre" => "<b>Crear</b>", "activo" => true]
            ],
        ];

        //render
        $this->template->agregar_titulo_header('Notas de cr&eacute;dito de proveedor: Crear');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido([]);
        $this->template->visualizar();
    }

    public function ajax_get_nota_by_id(){

        if (!$this->input->is_ajax_request()) {
            return false;
        }

        if (!isset($_POST['id'])) {
            return false;
        }

        $facturaCompraRepository = new Flexio\Modulo\FacturasCompras\Repository\FacturaCompraRepository;

        $response =$facturaCompraRepository->getCollectionFacturasNotaDebito($facturaCompraRepository->findById($_POST['id'])->get())->first();

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response))->_display();
        exit();
    }


    public function ver($uuid=null)
    {
        $acceso = $this->auth->has_permission('acceso');
        if(!$this->auth->has_permission('acceso', 'notas_debitos/ver/(:any)')){
            redirect(base_url('/'));
        }

        //$this->Toast->runVerifyPermission($acceso);

        //registros
        $nota_debito = $this->notaDebitoRepository->findByUuid($uuid);

        //falta empezable
        @$empezable = collect([
            "type" => $nota_debito->tipo,
            "facturas" => !empty($nota_debito->factura_id) ? [0=>['id'=>$nota_debito->factura_id,'nombre'=>$nota_debito->proveedor->nombre ." - ". $nota_debito->factura->codigo]] : [],
            "id" => !empty($nota_debito->factura_id) ? $nota_debito->factura_id : ''
        ]);

        //assets
        $this->FlexioAssets->run();//css y js generales
        $this->FlexioAssets->add('css', ['public/assets/css/modules/stylesheets/nota_debito.css']);
        $this->FlexioAssets->add('js', ['public/assets/js/default/operaciones.js']);
        $this->FlexioAssets->add('vars', [
            "vista" => 'ver',
            "acceso" => $acceso ? 1 : 0,
            "nota_debito" =>$this->notaDebitoRepository->getCollectionNotaDebito($nota_debito),
            "empezable" => $empezable
        ]);
        //breadcrumb
        $breadcrumb = [
            "titulo" => '<i class="fa fa-shopping-cart"></i> Notas de cr&eacute;dito de proveedor: '.$nota_debito->codigo,
            "ruta" => [
                ["nombre" => "Compras", "activo" => false],
                ["nombre" => "Notas de cr&eacute;dito de proveedor", "activo" => false, "url" => 'notas_debitos/listar'],
                ["nombre" => "<b>Detalle</b>", "activo" => true]
            ],
        ];

        $subpanels = [
                'documento'=>['nota_debito'=>$nota_debito->id]
            ];
        $data['subpanels'] = $subpanels;
        $data['nota_debito'] = $nota_debito;
        //render
        $this->template->agregar_titulo_header('Notas de cr&eacute;dito de proveedor: Detalle');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    public function ocultoformulario($nota_debito)
    {
        //dd($info->toArray());
        $limit = 0;
        $data = array();
        $clause = array('empresa_id' => $this->empresa_id);
        $clause2 = ['empresa_id' => $this->empresa_id, 'ordenables' => true];
        $clause3 = ['empresa_id' => $this->empresa_id, 'transaccionales' => true];
        $this->FlexioAssets->add('js', ['public/resources/compile/modulos/notas_debitos/formulario.js']);

        $usuarios = $centros_contables = $proveedores = collect([]);
        if ($nota_debito != null) {
            $usuarios = collect([$nota_debito->vendedor]);
        } else {
            $usuarios = Usuario_orm::findById($this->FlexioSession->usuarioId())->get();
        }


        $this->FlexioAssets->add('vars', [
            'proveedores' => $proveedores,
            'centros_contables' => $this->CentrosContablesRepository->getCollectionCentrosContables($this->CentrosContablesRepository->get($clause3)),
            'usuarios' => $usuarios,
            'estados' => $this->catalogo->getEtapas(),
            'cuentas' => Cuentas_orm::transaccionalesDeEmpresa($this->empresa_id)->activas()->get(),
            'impuestos' => $this->ImpuestosRepository->get($clause),
            'usuario_id' => $this->FlexioSession->usuarioId(),
            'facturas' => collect([]),
            'empresa' => $this->empresaObj
        ]);

        $this->load->view('formulario', $data);

    }


    public function guardar(){

        if($_POST){


            $formRequest =  new Flexio\Modulo\NotaDebito\FormRequest\FormGuardarNotaDebito;

            try{
                $nota_debito = $formRequest->guardar();
                $this->Toast->run("success",[$nota_debito->codigo]);


            }catch(\Exception $e){
                log_message('error', __METHOD__." ->". ", Linea: ". __LINE__ ." --> ". $e->getMessage()."\r\n");
                $this->Toast->setUrl('notas_debitos/listar')->run("exception",[$e->getMessage()]);
            }

            if(is_null($nota_debito)){
                $this->Toast->run("error");
            }

            redirect(base_url('notas_debitos/listar'));

        }
    }

     public function documentos_campos() {

       return array(
           array(
               "type" => "hidden",
               "name" => "nota_debito_id",
               "id" => "nota_debito_id",
               "class" => "form-control",
               "readonly" => "readonly",
       ));
   }

   public function ajax_guardar_documentos() {
       if (empty($_POST)) {
           return false;
       }

       $nota_debito_id = $this->input->post('nota_debito_id', true);
       $notaDebitoObj = new Flexio\Modulo\NotaDebito\Repository\NotaDebitoRepository;
       $notaDebito        = $notaDebitoObj->findByUuid($nota_debito_id);
       $this->documentos->subir($notaDebito);
   }

    function ajax_guardar_comentario(){
        if(!$this->input->is_ajax_request()){
            return false;
        }

        $model_id   = $this->input->post('modelId');
        $comentario = $this->input->post('comentario');
        $uuid_usuario = $this->session->userdata('huuid_usuario');
        $usuario = Usuario_orm::findByUuid($uuid_usuario);
        $comentario = ['comentario'=>$comentario,'usuario_id'=>$usuario->id];

        $nota_debito = $this->notaDebitoRepository->agregarComentario($model_id, $comentario);
        $nota_debito->load('comentario');
        $lista_comentario = $nota_debito->comentario()->orderBy('created_at','desc')->get();
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($lista_comentario->toArray()))->_display();
        exit;
    }
    private function _generar_codigo(){
        $clause_empresa = ['empresa_id'=>$this->empresa_id];
        $numero = $this->notaDebitoRepository->lista_totales($clause_empresa);
        return $numero + 1;
    }
    private function _js(){
        $this->assets->agregar_js(array(
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.numeric.extensions.js',
            'public/assets/js/plugins/jquery/sweetalert/sweetalert.min.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/plugins/bootstrap/select2/select2.min.js',
            'public/assets/js/plugins/bootstrap/select2/es.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/default/toast.controller.js',
            'public/assets/js/default/vue/filters/numeros.js',
            'public/assets/js/plugins/jquery/fileupload/jquery.fileupload.js',
            'public/assets/js/default/operaciones.js',
        ));
    }

    private function _css(){
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
            'public/assets/css/modules/stylesheets/nota_debito.css',
            'public/assets/css/plugins/jquery/jquery.fileupload.css',
        ));
    }



}

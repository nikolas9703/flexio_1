<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Erp
 * @subpackage Controller
 * @category   Entregas de alquiler
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  10/22/2015
 */

use Carbon\Carbon;
use League\Csv\Writer;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\EntregasAlquiler\Repository\EntregasAlquilerRepository;
use Flexio\Modulo\EntregasAlquiler\Repository\EntregasAlquilerCatalogosRepository;
use Flexio\Modulo\Cliente\Repository\ClienteRepository;
use Flexio\Modulo\Usuarios\Repository\UsuariosRepository;
use Flexio\Modulo\ContratosAlquiler\Repository\ContratosAlquilerRepository;
use Flexio\Modulo\Inventarios\Repository\CategoriasRepository as ItemsCategoriasRepository;
use Flexio\Modulo\ContratosAlquiler\Repository\ContratosAlquilerCatalogosRepository;
use Flexio\Modulo\Bodegas\Repository\BodegasRepository;
use Flexio\Modulo\CentroFacturable\Repository\CentroFacturableRepository;
use Flexio\Modulo\Inventarios\Repository\SerialesRepository;
use Flexio\Modulo\EntregasAlquiler\Models\EntregasAlquilerItems;
use Flexio\Jobs\ContratosAlquiler\CronCargosAlquiler;
use Flexio\Modulo\EntregasAlquiler\Models\EntregasAlquiler;
use Flexio\Modulo\CentrosContables\Repository\CentrosContablesRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
class Entregas_alquiler extends CRM_Controller
{
    /**
     * Atributos
     */
    private $empresa_id;
    private $empresaObj;
    private $usuario_id;
    protected $EntregasAlquilerRepository;
    protected $EntregasAlquilerCatalogosRepository;
    protected $ClienteRepository;
    protected $UsuariosRepository;
    protected $CentrosContablesRepository;
    protected $ContratosAlquilerRepository;
    protected $ItemsCategoriasRepository;
    protected $ContratosAlquilerCatalogosRepository;
    protected $BodegasRepository;
    protected $CentroFacturableRepository;
    protected $SerialesRepository;
    protected $EntregasAlquilerItems;
    protected $CronCargosAlquiler;

    /**
     * Método constructor
     */
    public function __construct()
    {
        parent::__construct();
        //cargar los modelos
        $this->load->model('usuarios/Empresa_orm');
        $this->load->model('usuarios/Usuario_orm');
        $this->load->model('roles/Rol_orm');
        $this->load->model('contabilidad/Impuestos_orm');

        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresaObj = new Buscar(new Empresa_orm,'uuid_empresa');
        $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
        $this->empresa_id = $this->empresaObj->id;
        $this->usuario_id = $this->session->userdata('id_usuario');
        $this->EntregasAlquilerRepository = new EntregasAlquilerRepository();
        $this->EntregasAlquilerCatalogosRepository = new EntregasAlquilerCatalogosRepository();
        $this->ClienteRepository = new ClienteRepository();
        $this->UsuariosRepository = new UsuariosRepository();
        $this->ContratosAlquilerRepository = new ContratosAlquilerRepository();
        $this->ItemsCategoriasRepository = new ItemsCategoriasRepository();
        $this->ContratosAlquilerCatalogosRepository = new ContratosAlquilerCatalogosRepository();
        $this->BodegasRepository = new BodegasRepository();
        $this->CentroFacturableRepository = new CentroFacturableRepository();
        $this->SerialesRepository = new SerialesRepository();
        $this->EntregasAlquilerItems = new EntregasAlquilerItems();
        $this->CronCargosAlquiler = new CronCargosAlquiler();
        $this->CentrosContablesRepository = new CentrosContablesRepository();
    }



    public function ocultotabla($key_valor = NULL)
    {
        if(is_array($key_valor))
        {
            $this->assets->agregar_var_js([
                "campo" => collect($key_valor)
            ]);
        }
        elseif($key_valor and count(explode("=", $key_valor)) > 1)
        {
            $aux = explode("=", $key_valor);
            $this->assets->agregar_var_js([$aux[0]=>$aux[1]]);
        }
        
        $this->assets->agregar_js(array(
            'public/assets/js/modules/entregas_alquiler/tabla.js'
        ));

        $this->load->view('tabla');
    }

    public function ocultoformulario($entrega_alquiler = array()) {

        $entrega_uuid = $this->uri->segment(3);

        $data = array();
        $clause = array('empresa_id' => $this->empresa_id, 'empezable'=>true);

        $this->assets->agregar_js(array(
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/resources/compile/modulos/entregas_alquiler/formulario.js'
        ));

        //---------------------
        // Catalogo Vendedores
        //---------------------
        $roles_users = Rol_orm::where(function ($query) use ($clause) {
                $query->where('empresa_id', '=', $clause['empresa_id']);
                $query->where('nombre', 'like', '%vendedor%');
        })->orWhere(function ($query) use ($clause) {
               $query->where('empresa_id', '=', $clause['empresa_id']);
               $query->where('nombre', 'like', '%venta%');
        })->with(array('usuarios'))->get();

        $vendedores = array();
        foreach ($roles_users as $roles) {
            $usuarios = $roles->usuarios;
            foreach ($usuarios as $user) {
                if ($user->pivot->empresa_id == $clause['empresa_id']) {
                    array_push($vendedores, array(
                        "id" => $user->id,
                        "nombre" => Util::verificar_valor($user->nombre) ." ". Util::verificar_valor($user->apellido)
                    ));
                }
            }
        }

        if (isset($entrega_alquiler['info'])){$data['info'] = $entrega_alquiler['info'];}

        $empezables = $this->ContratosAlquilerRepository->get($clause);
        $empezables->load('cliente', 'items', 'contratos_items','contratos_items.item','contratos_items.item.seriales','contratos_items.item.atributos');

        $this->assets->agregar_var_js(array(
            "empezables"    => $empezables->filter(function($empezable) use ($entrega_uuid){
              if(empty($entrega_uuid)){
                return $empezable->contratos_items->sum('cantidad') - $empezable->contratos_items->sum('entregado') > 0; //por entregar + aun no asociadas a una entrega
              }else{
                return $empezable;
              }
            })->map(function($empezable){
                $data = $empezable->toArray();
                if($empezable->fecha_inicio != "0000-00-00 00:00:00" && $empezable->fecha_inicio != ""){
                  $data = array_merge($data, ['fecha_inicio' => $empezable->fecha_inicio->format('d/m/Y')]);
                }
                if($empezable->fecha_fin != "0000-00-00 00:00:00" && $empezable->fecha_fin != ""){
                  $data = array_merge($data, ['fecha_fin' => $empezable->fecha_fin->format('d/m/Y')]);
                }
                return $data;
            }),
            "clientes"      => $this->ClienteRepository->get($clause),
            "vendedores"    => collect($vendedores),
            "usuarios"      => $this->UsuariosRepository->get(array('empresa_id' => $this->empresa_id)),
            "estados"       => $this->EntregasAlquilerCatalogosRepository->get(['tipo'=>'estado']),
            "codigo"        => $this->_generar_codigo(),
        ));

        $this->load->view('formulario', $data);
    }

    public function ocultoformulario_items_entregados($entrega_alquiler = array()) {
        $data = array();
        $clause = array('empresa_id' => $this->empresa_id);

        if (isset($entrega_alquiler['info'])){$data['info'] = $entrega_alquiler['info'];}

        $categorias = $this->ItemsCategoriasRepository->get(['empresa_id'=>$this->empresa_id,'conItems'=>true]);
        $categorias->load('items_contratos_alquiler');

        $aux = array_map(function($row){
            return [
                'id' => $row['id'],
                'nombre' => $row['nombre']
            ];
        },$this->BodegasRepository->get(['empresa_id'=>$this->empresa_id,'transaccionales'=>true])->toArray());

        $this->assets->agregar_var_js(array(
            "categorias"        => $categorias,
            "bodegas"           => json_encode($aux),
            "ciclos_tarifarios" => $this->ContratosAlquilerCatalogosRepository->get(['tipo'=>'tarifa'])
        ));
        //dd($this->BodegasRepository->get(['empresa_id'=>1,'transaccionales'=>true])->toArray());
        $this->load->view('formulario_items_entregados', $data);
        $this->load->view('templates/entrega_items');
        $this->load->view('templates/entrega_item');
        $this->load->view('templates/lista-seriales');
    }

    /**
     * Método listar los registros de los subentregas en ocultotabla()
     */
    public function ajax_listar()
    {   
        
        if(!$this->input->is_ajax_request()){return false;}
        
        $clause                 = $this->input->post();
        $clause['campo']        = $this->input->post("campo");
        $clause['empresa_id']   = $this->empresa_id;

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = $this->EntregasAlquilerRepository->count($clause);
        
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $entregas_alquiler = $this->EntregasAlquilerRepository->get($clause ,$sidx, $sord, $limit, $start);

        $response          = new stdClass();
        $response->page    = $page;
        $response->total   = $total_pages;
        $response->records = $count;

        if($count > 0){
            foreach ($entregas_alquiler as $i => $entrega_alquiler)
            {
                $response->rows[$i]["id"]   = $entrega_alquiler->uuid_entrega_alquiler;
                $response->rows[$i]["cell"] = $this->EntregasAlquilerRepository->getCollectionCell($entrega_alquiler, $this->auth);
            }
        }
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))->_display();
        exit;
    }

    public function ajax_get_serie_ubicacion()
    {
        if(!$this->input->is_ajax_request()){return false;}

        $clause = $this->input->post();
        $serial = $this->SerialesRepository->findBy($clause);

        $this->output
                ->set_status_header(200)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(['ubicacion_id'=>$serial->ubicacion_id], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
        exit;
    }

    public function ajax_exportar()
    {
        $post = $this->input->post();
    	if(empty($post)){exit();}

    	$entregas_alquiler = $this->EntregasAlquilerRepository->get(['ids', $post['ids']]);

        $csv = Writer::createFromFileObject(new SplTempFileObject());
        $csv->insertOne(['No. Entrega','Fecha de entrega','No. Contrato','Cliente',utf8_decode('Centro de Facturación'),'Estado']);
        $csv->insertAll($this->EntregasAlquilerRepository->getCollectionExportar($entregas_alquiler));
        $csv->output("EntregaAlquiler-". date('ymd') .".csv");
        exit();
    }

    public function guardar()
    {
        $post = $this->input->post();

        if (!empty($post)) {
            Capsule::beginTransaction();
            try {
                $campo = $post['campo'];
                if(empty($campo['id']))
                {
                    $post['campo']['codigo']        = $this->_generar_codigo();
                    $post['campo']['empresa_id']    = $this->empresa_id;
                    $entrega_alquiler = $this->EntregasAlquilerRepository->create($post);
                } else {
                    $entrega_alquiler = $this->EntregasAlquilerRepository->save($post);
                }

            } catch (Illuminate\Database\QueryException $e) {
                log_message('error', __METHOD__ . " ->" . ", Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
                Capsule::rollback();
                $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error! Su solicitud no fue procesada</b> ');
                $this->session->set_flashdata('mensaje', $mensaje);
                redirect(base_url('entregas_alquiler/listar'));
                //echo $e->getMessage();
            }

            Capsule::commit();

            $entrega_alquiler->load('estado','contrato_alquiler','contrato_alquiler.facturar_contra_entrega');

            $estado = !empty($entrega_alquiler["estado"]) ? $entrega_alquiler["estado"]["valor"] : "";
            $facturar_contra_entrega = !empty($entrega_alquiler["contrato_alquiler"]["facturar_contra_entrega"]) ? $entrega_alquiler["contrato_alquiler"]["facturar_contra_entrega"]["valor"] : "";

            //Verificar estado
            if(preg_match("/entregado/i", $estado)){
              //Verificar si hay contrato es contra entrega.
              if(preg_match("/si/i", $facturar_contra_entrega)){
                //Ejecutar cargos por adelantado.
                $this->CronCargosAlquiler->ejecutar();
              }
            }

            if (!is_null($entrega_alquiler)) {
                $mensaje = array('estado' => 200, 'mensaje' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente ' . $entrega_alquiler->codigo);
            } else {
                $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error! Su solicitud no fue procesada</b> ');
            }
            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('entregas_alquiler/listar'));
        }


    }

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

        $this->_css();$this->_js();

        $breadcrumb = array( "titulo" => '<i class="fa fa-car"></i> Entregas',
            "ruta" => array(
                0 => ["nombre" => "Alquileres", "activo" => false],
                1 => ["nombre" => '<b>Entregas</b>', "activo" => true]
            ),
            "menu" => ["nombre" => "Crear", "url" => "entregas_alquiler/crear","opciones" => array()]
        );
        $breadcrumb["menu"]["opciones"]["#exportarEntregasAlquiler"] = "Exportar";

        $this->assets->agregar_var_js(array(
            "toast_mensaje" => $mensaje
        ));

        $clause = ['empresa_id' => $this->empresa_id];
        $data['clientes']   = $this->ClienteRepository->get($clause);
        $data['estados']    = $this->EntregasAlquilerCatalogosRepository->get(['tipo'=>'estado']);
        $data['centros_facturables'] = $this->CentroFacturableRepository->get(['empresa_id'=>$this->empresa_id]);

        $this->template->agregar_titulo_header('Listado de Entregas');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }

    public function crear(){

        $acceso = 1;
        $mensaje = array();
        if (!$this->auth->has_permission('acceso', 'entregas_alquiler/crear')) {
            $acceso = 0;
            $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud', 'clase' => 'alert-danger');
        }

        $clause = array(
            "empresa_id" => $this->empresa_id
        );

       	$this->_css();
        $this->_js();
        $this->assets->agregar_js(array(
            'public/assets/js/default/vue/directives/select2.js',
            'public/assets/js/modules/entregas_alquiler/components/entrega_items.js',
            'public/assets/js/modules/entregas_alquiler/components/entrega_item.js',
            'public/assets/js/modules/entregas_alquiler/components/lista-seriales.js',
            'public/assets/js/modules/entregas_alquiler/formulario.js',
        ));
        $items_disponibles = EntregasAlquilerItems::where($clause)->get(array('item_id', 'serie'));
        
        $this->assets->agregar_var_js(array(
            "vista"                 => 'crear',
            "acceso"                => $acceso == 0 ? $acceso : $acceso,
            "items_disponibles"     => !empty($items_disponibles) ? $items_disponibles : ''
        ));

        $data['mensaje'] = $mensaje;
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-car"></i> Entregas: Crear ',
            "ruta" => [
                ["nombre" => "Alquileres", "activo" => false],
                ["nombre" => "Entregas", "activo" => false, "url" => "entregas_alquiler/listar"],
                ["nombre" => "<b>Crear</b>","activo" => true]
            ]
        );

        $this->template->agregar_titulo_header('Entregas: Crear ');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();

    }

    public function editar($uuid){

        $acceso = 1;
        $mensaje = array();
        if (!$this->auth->has_permission('acceso', 'entregas_alquiler/editar/(:any)')) {
            $acceso = 0;
            $mensaje = array('estado' => 500, 'mensaje' => '<b>¡Error!</b> Usted no cuenta con permiso para esta solicitud', 'clase' => 'alert-danger');
        }

        $this->_css();
        $this->_js();
        $this->assets->agregar_js(array(
            'public/assets/js/default/vue/directives/select2.js',
            'public/assets/js/modules/entregas_alquiler/components/entrega_items.js',
            'public/assets/js/modules/entregas_alquiler/components/entrega_item.js',
            'public/assets/js/modules/entregas_alquiler/components/lista-seriales.js',
            'public/assets/js/modules/entregas_alquiler/formulario.js',
        ));

        $entrega_alquiler = $this->EntregasAlquilerRepository->findBy(['uuid_entrega_alquiler'=>$uuid]);
        $entrega_alquiler->load('comentario_timeline');
        //dd($entrega_alquiler);
        $this->assets->agregar_var_js(array(
            "vista"                     => 'editar',
            "acceso"                    => $acceso == 0 ? $acceso : $acceso,
            "entrega_alquiler"          => json_encode($this->EntregasAlquilerRepository->getCollectionCampo($entrega_alquiler)),
            "entrega_alquiler_items"    => $entrega_alquiler->items,//ver relacion para comprender como va esto -> aplicar filtro
            "coment" =>(isset($entrega_alquiler->comentario_timeline)) ? $entrega_alquiler->comentario_timeline : Collect([]),
            "entrega_alquiler_id"       => $entrega_alquiler->id
        ));

        $data['mensaje']            = $mensaje;
        $data['entrega_alquiler']   = $entrega_alquiler;
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-car"></i> Entregas de alquiler: '. $entrega_alquiler->codigo,
            "ruta" => [
                ["nombre" => "Alquileres", "activo" => false],
                ["nombre" => "Entregas", "activo" => false, "url" => "entregas_alquiler/listar"],
                ["nombre" => "<b>{$entrega_alquiler->codigo}</b>","activo" => true]
            ],
            "menu" => ["nombre" => "Acci&oacute;n", "url" => "#","opciones" => array("entregas_alquiler/imprimir/{$uuid}" => "Imprimir")]

        );

        $this->template->agregar_titulo_header('Entregas de alquiler: Editar ');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();

    }

    private function _generar_codigo()
    {
        $clause_empresa = ['empresa_id' => $this->empresa_id];
        $total = $this->EntregasAlquilerRepository->count($clause_empresa);
        $year = Carbon::now()->format('y');
        $codigo = Util::generar_codigo('ENT'.$year,$total + 1);
        return $codigo;
    }


    private function _css()
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
            'public/assets/css/modules/stylesheets/entregas_alquiler.css',
            //saldo y credio styles
            'public/assets/css/modules/stylesheets/cotizaciones.css',
        ));
    }


    private function _js()
    {
        $this->assets->agregar_js(array(
            //'public/assets/js/default/jquery-ui.min.js',
            //'public/assets/js/plugins/jquery/jquery.sticky.js',
            //'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            //'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            //'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
            //'public/assets/js/default/lodash.min.js',
            //'public/assets/js/default/accounting.min.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            //'public/assets/js/plugins/jquery/sweetalert/sweetalert.min.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/plugins/bootstrap/select2/select2.min.js',
            'public/assets/js/plugins/bootstrap/select2/es.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
            'public/assets/js/default/toast.controller.js',
            //'public/assets/js/default/lodash.min.js',
            'public/assets/js/modules/entregas_alquiler/plugins.js',
        ));
    }

    function ajax_guardar_comentario() {

        if(!$this->input->is_ajax_request()){
            return false;
        }
        $model_id   = $this->input->post('modelId');
        $comentario = $this->input->post('comentario');
        $comentario = ['comentario'=>$comentario,'usuario_id'=>$this->usuario_id];
        $entregas = $this->EntregasAlquilerRepository->agregarComentario($model_id, $comentario);
        $entregas->load('comentario_timeline');

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($entregas->comentario_timeline->toArray()))->_display();
        exit;
    }

    public function imprimir($uuid=null)
    {
        if($uuid==null){
            return false;
        }

        $entrega_alquiler = $this->EntregasAlquilerRepository->findBy(['uuid_entrega_alquiler'=>$uuid]);
        $entrega_alquiler->load('contrato_alquiler');
        $contrato = $this->ContratosAlquilerRepository->findByUuid($entrega_alquiler->contrato_alquiler->uuid_contrato_alquiler);
        $variable = collect($this->ContratosAlquilerRepository->getCollectionCampo($contrato));
        $centro_facturable = !empty($entrega_alquiler->centro_facturacion_id) ? $this->CentroFacturableRepository->find($entrega_alquiler->centro_facturacion_id) : '';
        $ResultEntregas = EntregasAlquiler::with(array("contrato_alquiler.contratos_items.item", "items_entregados2"))->where('uuid_entrega_alquiler', hex2bin($uuid))->get();
        $empresa = $this->empresaObj->find($entrega_alquiler->empresa_id);
        $cliente = $this->ClienteRepository->find($entrega_alquiler->cliente_id);
        $cliente->load('telefonos_asignados');
        $centro = $this->CentrosContablesRepository->find($entrega_alquiler->contrato_alquiler->centro_contable_id);
        $usuario = $this->UsuariosRepository->find($entrega_alquiler->created_by);
        $creador = $this->UsuariosRepository->find($entrega_alquiler->contrato_alquiler->created_by);
        $options = new Options();
        $options->set('isRemoteEnabled', TRUE);
        $dompdf = new Dompdf($options);
        $data   = ['entrega_info'=>$entrega_alquiler, 'empresa' => $empresa, 'usuario' => $usuario, 'centro_contable' => $centro->nombre, 'cliente' => $cliente, 'items_entregados' => $ResultEntregas, 'creador' => $creador, 'centro_facturacion' => $centro_facturable, 'atributos' => $variable['articulos']];

        $html = $this->load->view('pdf/entrega_alquiler', $data, true);

        //render
        //echo '<pre>'.$html.'</pre>'; die;
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream($entrega_alquiler->codigo);

        exit();
    }


}

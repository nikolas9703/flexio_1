<?php
/**
 * Ajustes
 *
 * Modulo para administrar la creacion, edicion de traslados
 *
 * @package    PensaApp
 * @subpackage Controller
 * @category   Controllers
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  10/16/2015
 */

use Illuminate\Database\Capsule\Manager as Capsule;

//repositorios
use Flexio\Modulo\Bodegas\Repository\BodegasRepository as bodegasRep;
use Flexio\Modulo\Entradas\Repository\EntradasRepository as entradasRep;
use Flexio\Modulo\Traslados\Repository\TrasladosRepository as trasladosRep;
use Flexio\Modulo\Inventarios\Repository\CategoriasRepository as ItemsCategoriasRepository;
use Flexio\Modulo\Traslados\Repository\TrasladosCatRepository;
use Flexio\Modulo\Pedidos\Repository\PedidoRepository;

//utils
use Flexio\Library\Util\FlexioAssets;
use Flexio\Library\Util\FlexioSession;
use Flexio\Library\Toast;

class Traslados extends CRM_Controller
{
    protected $empresa;
    protected $id_empresa;
    protected $prefijo;
    protected $id_usuario;

    //repositorios
    private $bodegasRep;
    private $entradasRep;
    private $trasladosRep;
    protected $ItemsCategoriasRepository;
    protected $TrasladosCatRepository;
    protected $PedidoRepository;

    //utils
    protected $FlexioAssets;
    protected $FlexioSession;
    protected $Toast;

    public function __construct()
    {
        parent::__construct();

        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();

        //repositorios
        $this->bodegasRep = new bodegasRep();
        $this->entradasRep = new entradasRep();
        $this->trasladosRep = new trasladosRep();
        $this->ItemsCategoriasRepository = new ItemsCategoriasRepository;
        $this->TrasladosCatRepository = new TrasladosCatRepository;
        $this->PedidoRepository = new PedidoRepository;

        //utils
        $this->FlexioAssets = new FlexioAssets;
        $this->FlexioSession = new FlexioSession;
        $this->Toast = new Toast;
    }



    public function index()
    {
        redirect("traslados/listar");
    }


    public function listar()
    {

        //permissions
        $acceso = $this->auth->has_permission('acceso', 'traslados/listar');
        $this->Toast->runVerifyPermission($acceso);

        //assets
        $this->FlexioAssets->run();//css y js generales
        $this->FlexioAssets->add('js',['public/assets/js/modules/traslados/listar.js']);
        $this->FlexioAssets->add('vars', [
            "flexio_mensaje" => Flexio\Library\Toast::getStoreFlashdata()
        ]);

        //breadcrumb
    	$breadcrumb = [
            "titulo" => '<i class="fa fa-cubes"></i> Traslado: Tabla Principal ',
            "ruta" => [
                ["nombre" => "Inventarios", "activo" => false],
                ["nombre" => '<b>Traslados</b>',"activo" => true]
            ],
            "menu" => [
                "nombre" => "Crear",
                "url" => "traslados/crear",
                "opciones" => ["#exportarBtn" => "Exportar"]
            ]
        ];

        //Search filter catalogs
        $clause = ['empresa_id'=>$this->FlexioSession->empresaId(),'ordenables'=>true,'transaccionales'=>true,'conItems'=>true];
        $data = [
            "bodegas" => $this->bodegasRep->getCollectionBodegas($this->bodegasRep->get($clause, 'nombre', 'asc')),
            "estados" => $this->TrasladosCatRepository->get($clause, 'etiqueta', 'asc')
        ];

        //render
    	$this->template->agregar_titulo_header('Traslados');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar();

    }


    public function ajax_listar()
    {
        if(!$this->input->is_ajax_request()){
            return false;
        }

        $jqgrid = new Flexio\Modulo\Traslados\Services\TrasladoJqgrid;

        $clause = $this->input->post();
        $clause['empresa'] = $this->FlexioSession->empresaId();

        $response = $jqgrid->listar($clause);

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))->_display();
        exit;
    }


    public function ocultotabla($campo_array = [])
    {
        if(is_array($campo_array))
        {
            $this->assets->agregar_var_js([
                "campo" => collect($campo_array)
            ]);

        }
        //If ajax request
    	$this->assets->agregar_js(array(
            'public/assets/js/modules/traslados/tabla.js'
    	));

    	$this->load->view('tabla');
    }

    public function ocultoformulario()
    {
        $this->FlexioAssets->add('js', ['public/resources/compile/modulos/traslados/formulario.js']);

        $clause = ['empresa_id'=>$this->FlexioSession->empresaId(),'ordenables'=>true,'transaccionales'=>true,'conItems'=>true];

        $this->FlexioAssets->add('vars',[
            'bodegas' => $this->bodegasRep->getCollectionBodegas($this->bodegasRep->get($clause)),
            'estados' => $this->TrasladosCatRepository->get($clause),
            'categorias' => $this->ItemsCategoriasRepository->getCollectionCategorias($this->ItemsCategoriasRepository->get($clause)),
            'pedidos' => $this->PedidoRepository->getCollectionPedidos($this->PedidoRepository->get($clause)->filter(function($pedido){
                return $pedido->comprable == true;
            }))
        ]);

        $this->load->view('formulario');
    }

    public function crear()
    {

        //permisos
        $acceso = $this->auth->has_permission('acceso', 'traslados/crear');
        $this->Toast->runVerifyPermission($acceso);

        //from get
        $get = $this->input->get();

        //empezable -> falta empezar desde pedido
        $empezable = collect([
            'id' => isset($get['pedido_id']) ? $get['pedido_id'] : '',
            'type' => isset($get['pedido_id']) ? 'pedido' : '',
            'pedidos' => []
        ]);

        //assets
        $this->FlexioAssets->run();//css y js generales
        $this->FlexioAssets->add('vars', [
            "vista" => 'crear',
            "acceso" => $acceso ? 1 : 0,
            "empezable" => $empezable
        ]);

        //breadcrumb
    	$breadcrumb = [
            "titulo" => '<i class="fa fa-cubes"></i> Traslado: Crear '
        ];

        //render
    	$this->template->agregar_titulo_header('Traslados');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido([]);
    	$this->template->visualizar();

    }

    public function guardar()
    {
        $post = $this->input->post();

        if(!empty($post))
        {
            $formGuardar = new  Flexio\Modulo\Traslados\FormRequest\GuardarTraslados;
            try {
                $pedido = $this->PedidoRepository->find($post['empezable_id']);
                $post["campo"]["id_empresa"] = $this->FlexioSession->empresaId();
                $post["campo"]["uuid_pedido"] = count($pedido) ? $pedido->uuid_pedido : '';
                $traslado = $formGuardar->save($post);
            } catch (\Exception $e) {
                dd($e->getMessage());
                log_message('error', " __METHOD__  ->  , Linea:  __LINE__  --> " . $e->getMessage() . "\r\n");
                $this->Toast->setUrl('traslados/listar')->run("exception",[$e->getMessage()]);
            }

            if(!is_null($traslado)){
                $this->Toast->run("success",[$traslado->codigo]);
            }else{
                $this->Toast->run("error");
            }

            redirect(base_url('traslados/listar'));
        }
    }

    public function editar($uuid=NULL)
    {
        if(!$uuid)return;

        //permisos
        $acceso = $this->auth->has_permission('acceso','traslados/ver/(:any)');
        $this->Toast->runVerifyPermission($acceso);

        //Cargo el registro
        $registro = $this->trasladosRep->findByUuid($uuid);
        $registro->load('comentario_timeline');

        $empezable = collect([
            "type" => count($registro->pedido) ? 'pedido' : '',
            "pedidos" => count($registro->pedido) ? [0=>['id'=>$registro->pedido->id,'nombre'=>$registro->pedido->codigo]] : [],
            "id" => count($registro->pedido) ? $registro->pedido->id : ''
        ]);

        //assets
        $this->FlexioAssets->run();//css y js generales
        $this->FlexioAssets->add('vars', [
            "vista" => 'editar',
            "acceso" => $acceso ? 1 : 0,
            "traslado" => $this->trasladosRep->getColletionTraslado($registro),
            "empezable" => $empezable
        ]);

        //breadcrumb
        $breadcrumb = [
            "titulo" => '<i class="fa fa-cubes"></i> Traslado: ' . $registro->codigo

        ];

        //render
        $this->template->agregar_titulo_header('Traslados');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido([]);
        $this->template->visualizar();
    }

}

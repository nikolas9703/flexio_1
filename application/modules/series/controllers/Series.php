<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * @package    Flexio
 * @subpackage Controller
 * @category   Series
 * @author     Pensanomica Team
 * @link       http://www.pensanomica.com
 * @copyright  01/15/2016
 */



//repositories
use Flexio\Modulo\Inventarios\Repository\SerialesRepository;
use Flexio\Modulo\Inventarios\Repository\CategoriasRepository as ItemsCategoriasRepository;
use Flexio\Modulo\Catalogos\Repository\CatalogoRepository;
use Flexio\Modulo\Bodegas\Repository\BodegasRepository;
use Flexio\Modulo\Cliente\Repository\ClienteRepository;
use Flexio\Modulo\Inventarios\Repository\ItemsCatRepository;
use Flexio\Modulo\Inventarios\Repository\UnidadesRepository as ItemsUnidadesRepository;

//libs
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

//utils
use Flexio\Library\Util\FlexioAssets;
use Flexio\Library\Util\FlexioSession;
use Flexio\Library\Toast;


class Series extends CRM_Controller{

    //repositories
    protected $SerialesRepository;
    protected $ItemsCategoriasRepository;
    protected $CatalogoRepository;
    protected $BodegasRepository;
    protected $ClienteRepository;
    protected $ItemsCatRepository;
    protected $ItemsUnidadesRepository;


    //utils
    protected $FlexioAssets;
    protected $FlexioSession;
    protected $Toast;


    public function __construct()
    {
        parent::__construct();

        Carbon::setLocale('es');
        setlocale(LC_TIME, 'Spanish');


        //repositories
        $this->SerialesRepository = new SerialesRepository;
        $this->ItemsCategoriasRepository = new ItemsCategoriasRepository;
        $this->CatalogoRepository = new CatalogoRepository;
        $this->BodegasRepository = new BodegasRepository;
        $this->ClienteRepository = new ClienteRepository;
        $this->ItemsCatRepository = new ItemsCatRepository;
        $this->ItemsUnidadesRepository = new ItemsUnidadesRepository;

        //utils
        $this->FlexioAssets = new FlexioAssets;
        $this->FlexioSession = new FlexioSession;
        $this->Toast = new Toast;
    }


    public function listar() {

        //permisos
        $acceso = $this->auth->has_permission('acceso');
        $this->Toast->runVerifyPermission($acceso);

        //assets
        $this->FlexioAssets->run();//css y js generales
        $this->FlexioAssets->add('vars', [
            "vista" => 'lisar',
            "acceso" => $acceso ? 1 : 0,
            "flexio_mensaje" => Flexio\Library\Toast::getStoreFlashdata()
        ]);

        //breadcrumb
        $breadcrumb = [
            "titulo" => '<i class="fa fa-cubes"></i> Inventario: Series',
            "ruta" => [
                ["nombre" => "Inventario", "activo" => false],
                ["nombre" => '<b>Series</b>', "activo" => true, 'url' => 'series/listar']
            ]
        ];

        //render
        $this->template->agregar_titulo_header('Series');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido([]);
        $this->template->visualizar();//

    }


    public function ocultotabla($sp_string_var = '')
    {

        $clause = ["empresa_id" => $this->FlexioSession->empresaId(), 'conItems' => true, 'modulo' => 'series', 'transaccionales' => true];
        $catalogos = $this->CatalogoRepository->get($clause);

        //js
        $this->FlexioAssets->add('js', ['public/resources/compile/modulos/series/listar.js']);

        //vars
        $this->FlexioAssets->add('vars',[
            'categorias' => $this->ItemsCategoriasRepository->getCollectionCategorias($this->ItemsCategoriasRepository->get($clause)),
            'estados' => $catalogos->filter(function($estado){return $estado->tipo == 'estado';}),
            'bodegas' => $this->BodegasRepository->getCollectionBodegas($this->BodegasRepository->get($clause)),
            'clientes' => $this->ClienteRepository->getCollectionClientes($this->ClienteRepository->get($clause))
        ]);

        //vars subpanels
        $sp_array_var = explode('=', $sp_string_var);
        if (count($sp_array_var) == 2) { $this->FlexioAssets->add('vars',[$sp_array_var[0] => $sp_array_var[1]]);}

        //render
        $this->load->view('tabla');
    }


    public function ajax_listar()
    {
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        $clause = array_merge(['empresa_id' => $this->FlexioSession->empresaId()], $this->input->post());
        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = $this->SerialesRepository->count($clause);
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $series = $this->SerialesRepository->get($clause, $sidx, $sord, $limit, $start);

        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;

        if ($count) { $response->rows = $this->SerialesRepository->getResponseRows($series);}

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($response))->_display();
        exit;
    }



    public function ver($uuid = NULL) {

        //permisos
        $acceso = $this->auth->has_permission('acceso');
        $this->Toast->runVerifyPermission($acceso);

        //Cargo el registro
        $registro = $this->SerialesRepository->findBy(['uuid_serial' => $uuid]);
        $registro->load('comentario_timeline');

        //assets
        $this->FlexioAssets->run();//css y js generales
        $this->FlexioAssets->add('vars', [
            "vista" => 'ver',
            "acceso" => $acceso ? 1 : 0,
            "serie" => $this->SerialesRepository->getCollectionSerie($registro)
        ]);

        //breadcrumb
        $breadcrumb = [
            "titulo" => '<i class="fa fa-cubes"></i> Inventarios/Series: ' . $registro->nombre,
            "historial" => true,
            "ruta" => [
                ["nombre" => "Inventario", "activo" => false],
                ["nombre" => 'Series', "activo" => false, 'url' => 'series/listar'],
                ["nombre" => "<b>{$registro->nombre}</b>", "activo" => true]
            ]
        ];

        //render
        $this->template->agregar_titulo_header('Series');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido(['serie_id' => $registro->id]);
        $this->template->visualizar();

    }


    public function ocultoformulario()
    {
        $clause = ["empresa_id" => $this->FlexioSession->empresaId(), 'conItems' => true];

        $this->FlexioAssets->add('js', [
            'public/assets/js/default/vue/directives/inputmask.js',
            'public/resources/compile/modulos/series/formulario.js'
        ]);

        $inventarios_cat = $this->ItemsCatRepository->get();
        $this->FlexioAssets->add('vars',[
            "tipos" => $inventarios_cat->filter(function($inventario_cat){return $inventario_cat->valor == "tipo";}),
            "categorias" => $this->ItemsCategoriasRepository->get($clause),
            "estados" => $inventarios_cat->filter(function($inventario_cat){return $inventario_cat->valor == "estado";}),
            "unidades" => $this->ItemsUnidadesRepository->get($clause),
        ]);

        $this->load->view('formulario');
    }





}

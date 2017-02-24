<?php
/**
 * Ajustes
 *
 * Modulo para administrar la creacion, edicion de ajustes
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
use Flexio\Modulo\Ajustes\Repository\AjustesRepository as ajustesRep;
use Flexio\Modulo\CentrosContables\Repository\CentrosContablesRepository as centrosContablesRep;
use Flexio\Modulo\Bodegas\Repository\BodegasRepository as bodegasRep;
use Flexio\Modulo\Ajustes\Repository\AjustesCatRepository as ajustesCatRep;
use Flexio\Modulo\Inventarios\Repository\CategoriasRepository as categoriasRep;
use Flexio\Modulo\Contabilidad\Repository\CuentasRepository as cuentasRep;
use Flexio\Modulo\Ajustes\Repository\AjustesRazonesRepository as ajustesRazonesRep;
use Flexio\Modulo\Ajustes\Transacciones\AjustesTransacciones as transaccionAjuste;

//utils
use Flexio\Library\Util\FlexioAssets;
use Flexio\Library\Toast;

class Ajustes extends CRM_Controller
{
    protected $empresa;
    protected $id_empresa;
    protected $prefijo;
    protected $id_usuario;
    protected $FlexioAssets;
    protected $Toast;

    //repositorios
    private $ajustesRep;
    private $centrosContablesRep;
    private $bodegasRep;
    private $ajustesCatRep;
    private $categoriasRep;
    private $cuentasRep;
    private $ajustesRazonesRep;

    public function __construct()
    {
        parent::__construct();

        //MODULOS
        $this->load->module("entradas/Entradas");
        $this->load->module("salidas/Salidas");

        $this->load->model("usuarios/Empresa_orm");

        $this->load->model("ajustes/Ajustes_orm");
        $this->load->model("ajustes/Ajustes_items_orm");
        $this->load->model("ajustes/Ajustes_cat_orm");

        $this->load->model("usuarios/Usuario_orm");

        $this->load->model("entradas/Entradas_orm");
        $this->load->model("entradas/Entradas_items_orm");

        $this->load->model("salidas/Salidas_orm");

        $this->load->model("ordenes/Ordenes_orm");
        $this->load->model("ordenes/Ordenes_items_orm");

        $this->load->model("ordenes_ventas/Orden_ventas_orm");
        $this->load->model("ordenes_ventas/Ordenes_venta_item_orm");

        $this->load->model("consumos/Consumos_orm");
        $this->load->model("consumos/Consumos_items_orm");

        $this->load->model("bodegas/Bodegas_orm");

        $this->load->model("centros/Centros_orm");

        $this->load->model("traslados/Traslados_orm");
        $this->load->model("traslados/Traslados_items_orm");

        $this->load->model("inventarios/Items_orm");
        $this->load->model("inventarios/Items_estados_orm");
        $this->load->model("inventarios/Items_unidades_orm");
        $this->load->model("inventarios/Items_categorias_orm");
        $this->load->model("inventarios/Unidades_orm");
        $this->load->model("inventarios/Categorias_orm");

        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();

        $uuid_empresa       = $this->session->userdata('uuid_empresa');
        $this->empresa      = Empresa_orm::findByUuid($uuid_empresa);
        $this->id_usuario   = $this->session->userdata("id_usuario");
        $this->id_empresa   = $this->empresa->id;




        //PREFIJO DE NOMEMCLATURA DE PEDIDO
        $this->prefijo = "AJS";

        //repositorios
        $this->ajustesRep           = new ajustesRep();
        $this->centrosContablesRep  = new centrosContablesRep();
        $this->bodegasRep           = new bodegasRep();
        $this->ajustesCatRep        = new ajustesCatRep();
        $this->categoriasRep        = new categoriasRep();
        $this->cuentasRep           = new cuentasRep();
        $this->ajustesRazonesRep    = new ajustesRazonesRep;
        $this->transaccionAjuste    = new transaccionAjuste;

        //utils
        $this->FlexioAssets = new FlexioAssets;
        $this->Toast = new Toast;
    }



    public function index()
    {
        redirect("ajustes/listar");
    }


    public function listar()
    {
        $data = array();
        $toast = new Flexio\Library\Toast;

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
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/plugins/jquery/jqgrid/plugins/jQuery.jqGrid.columnToggle.js',
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
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/plugins/bootstrap/bootstrap-tabdrop.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
            'public/assets/js/default/formulario.js',

            /* Archivos js del propio modulo*/
            'public/assets/js/modules/ajustes/listar.js',
        ));

    	/*
    	 * Verificar si existe alguna variable de session
    	 * proveniente de algun formulario de crear/editar
    	 */
    	if($this->session->userdata('idAjuste')){
    		//Borrar la variable de session
    		$this->session->unset_userdata('idAjuste');

    		//Establecer el mensaje a mostrar
    		$data["mensaje"]["clase"] = "alert-success";
    		$data["mensaje"]["contenido"] = "Se ha creado el Ajuste satisfactoriamente.";
    	}
    	else if($this->session->userdata('updatedAjuste')){
    		//Borrar la variable de session
    		$this->session->unset_userdata('updatedAjuste');

    		//Establecer el mensaje a mostrar
    		$data["mensaje"]["clase"] = "alert-success";
    		$data["mensaje"]["contenido"] = "Se ha actualizado el Ajuste satisfactoriamente.";
    	}


        //Breadcrum Array
        $breadcrumb = array(
            "titulo"    => '<i class="fa fa-cubes"></i> Inventario: Ajustes',
            "ruta" => array(
                0 => array(
                    "nombre" => "Inventarios",
                    "activo" => false
                ),
                1 => array(
                    "nombre" => '<b>Ajustes</b>',
                    "activo" => true
                )
            ),
            "filtro"    => false, //sin vista grid
            "menu"      => array()
        );

        //Verificar si tiene permiso a la seccion de Crear
        if (1 or $this->auth->has_permission('acceso', 'ajustes/crear')){
            $breadcrumb["menu"]["nombre"] = "Crear";
            $breadcrumb["menu"]["url"] = "ajustes/crear";
        }

        //Verificar si tiene permiso de Exportar
        if (1 or $this->auth->has_permission('listar__exportar', 'ajustes/listar')){
            $breadcrumb["menu"]["opciones"]["#exportarBtn"] = "Exportar";
        }

        //Agregra variables PHP como variables JS
        $this->assets->agregar_var_js(array(
            "flexio_mensaje" => Flexio\Library\Toast::getStoreFlashdata()
        ));

        unset($data["mensaje"]);

//        //CATALOGOS - ESTADO = 0 DE FORMA TERMPORAL


        $data["categorias"]     = Categorias_orm::deEmpresa($this->id_empresa)
                                ->where("estado", "=", "1")
                                ->orderBy("nombre", "ASC")
                                ->get();

        $data["bodegas"]        = Bodegas_orm::deEmpresa($this->id_empresa)
                                ->transaccionales($this->id_empresa)
                                ->activas()
                                ->orderBy("nombre", "ASC")
                                ->get();

       $data["centros"]        = Centros_orm::deEmpresa($this->id_empresa)
                                ->orderBy("nombre", "ASC")
                                ->get();


        $data["tipo_ajustes"]   = Ajustes_cat_orm::tipos()
                                ->orderBy("etiqueta", "asc")
                                ->get();

        $data["estados"]        = Ajustes_cat_orm::estados()
                                ->orderBy("etiqueta", "asc")
                                ->get();

    	$this->template->agregar_titulo_header('Listado de Ajustes');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar($breadcrumb);

    }


    public function ajax_get_item()
    {

    	if($this->input->is_ajax_request())
        {

            //uuid_item...
            $uuid           = $this->input->post("uuid", true);
            $uuid_bodega    = $this->input->post("uuid_bodega", true);
            $registro       = Items_orm::findByUuid($uuid);



            $response               = array();
            $response["success"]    = false;

            if(count($registro))
            {
                $enInventario           = $registro->enInventario($uuid_bodega);
                $response["success"]    = true;
                $response["registro"]   = array(
                    "cantidad_disponible"   => $enInventario["cantidadDisponibleBase"],
                    "precio"                => $registro->precioBase()
                );
            }

            echo json_encode($response);
            exit();
        }

    }

    public function ajax_get_articulos()
    {

    	if($this->input->is_ajax_request())
        {

            $ajuste_id  = $this->input->post("ajuste_id", true);
            $ajuste     = $this->ajustesRep->find($ajuste_id);

            $response               = array();
            $response["success"]    = false;

            if(count($ajuste))
            {
                $response["success"]    = true;
                $response["articulos"]  = $this->ajustesRep->getCollectionArticulos($ajuste->items, $ajuste->empresa_id);
            }

            $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response))->_display();
            exit();
        }

    }



    public function ajax_listar()
    {
    	//Just Allow ajax request
    	if($this->input->is_ajax_request())
    	{
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
            $registros = Ajustes_orm::deEmpresa($this->id_empresa);



            /**
             * Verificar si existe algun $_POST
             * de los campos de busqueda
            */
            $fecha          = $this->input->post('fecha', true);
            $centro         = $this->input->post('centro', true);
            $bodega         = $this->input->post('bodega', true);
            $tipo_ajuste    = $this->input->post('tipo_ajuste', true);
            $numero_ajuste  = $this->input->post('numero_ajuste', true);
            $categorias     = $this->input->post('categorias', true);
            $numero_item    = $this->input->post('numero_item', true);
            $estado         = $this->input->post('estado', true);
            $campo = $this->input->post('campo', true);


            if(!empty($campo)){
                $registros->deFiltro($campo);
            }

            if(!empty($fecha)){
                $registros->deFecha(date("Y-m-d", strtotime($fecha)));
            }

            if(!empty($centro)){
                $registros->deCentro($centro);
            }

            if(!empty($bodega)){
                $registros->deBodega($bodega);
            }

            if(!empty($tipo_ajuste)){
                $registros->deTipoAjuste($tipo_ajuste);
            }

            if(!empty($numero_ajuste)){
                $registros->deNumeroAjuste(str_replace($this->prefijo, "", $numero_ajuste));
            }

            if(!empty($estado)){
                $registros->deEstado($estado);
            }

            if(!empty($categorias) and count($categorias) > 1){
                $registros->deItemsCategorias($categorias);
            }

            if(!empty($numero_item)){
                $registros->deItemsNumero($numero_item);
            }


            /**
             * Total rows found in the query.
             * @var int
            */
            $count          = $registros->count();

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


            $registros->orderBy($sidx, $sord)
                    ->skip($start)
                    ->take($limit);

            //Constructing a JSON
            $response   = new stdClass();
            $response->page     = $page;
            $response->total    = $total_pages;
            $response->records  = $count;
            $i = 0;



            if($count)
            {
                foreach ($registros->get() AS $i => $row)
                {

                    $centro = Centros_orm::centros($this->id_empresa, $row->centro_id);

                    foreach($centro AS $info){

                        $nombre_centro = $info['nombre'];

                    }

                    $hidden_options = "";
                    $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-uuid="'. $row->uuid_ajuste .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

                    //IMPORTANTE A MODO DE DESARROLLO ANADI LA CONDICION OR 1
                    //PARA QUE TODAS LAS CONDICIONES DIERAN TRUE

                    $enlace = $this->prefijo.$row->numero;
                    if($this->auth->has_permission('acceso', 'ajustes/ver/(:any)')){
                        //
                        $hidden_options .= '<a href="'.base_url('ajustes/ver/'. $row->uuid_ajuste).'" class="btn btn-block btn-outline btn-success">Ver Ajuste</a>';

                        $enlace = '<a href="'. base_url('ajustes/ver/'. $row->uuid_ajuste) .'" style="color:blue;">'.$enlace.'</a>';
                    }


                    //Si no tiene acceso a ninguna opcion
                    //ocultarle el boton de opciones
                    if($hidden_options == ""){
                            $link_option = "&nbsp;";
                    }

            /*echo '<h2>Consultando Antes centros:</h2><pre>';
                print_r($row['centros']['nombre']);
            echo '</pre>';
             */
                    $response->rows[$i]["id"]   = $row->uuid_ajuste;
                    $response->rows[$i]["cell"] = array(
                        $enlace,
                        $row->created_at,
                        $nombre_centro,
                        count($row->bodega) ? $row->bodega->nombre : '',
                        $row->tipo_ajuste->comp__tipoWithSpan(),
                        $row->estado->comp__estadoWithSpan(),
                        $row->usuario->nombreCompleto(),
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







    function ajax_exportar()
    {
        $id_registros = $this->input->post("id_registros", true);

    	if(!$id_registros){
            return false;
    	}

    	$id_registros = explode("-", $id_registros);

        //EN CASO DE QUE SEAN UUID LOS CAMBIO AL
        //FORMATO QUE ESTA EN LA BASE DE DATOS
        foreach ($id_registros as &$row)
        {
            $row = hex2bin(strtolower($row));
        }

    	$registros  = Ordenes_orm
                    ::whereIn("uuid_orden", $id_registros)
                    ->get();

        $items = array();
        $i = 0;
        foreach($registros as $registro)
        {
            $items[$i]["Fecha"]             = $registro->fecha_creacion;
            $items[$i]["Numero"]            = $this->prefijo.$registro->numero;
            $items[$i]["Proveedor"]         = $registro->proveedor->nombre;
            $items[$i]["Referencia"]        = isset($registro->referencia) ? $registro->referencia : "";
            $items[$i]["Centro Contable"]   = $registro->centro->nombre;
            $items[$i]["Estado"]            = $registro->estado->etiqueta;
            $items[$i]["Monto"]             = "\$".$registro->monto;

            $i += 1;
        }

        if(empty($items)){
            return false;
    	}

        $objecto        = new stdClass();
        $objecto->count = count($items);
        $objecto->items = $items;

    	echo json_encode($objecto);
        exit();
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
            'public/assets/js/modules/ajustes/tabla.js'
    	));

    	$this->load->view('tabla');
    }



    public function guardar()
    {
        if(!empty($_POST))
    	{
            Capsule::beginTransaction();
            try {

                $post = $this->input->post();
                $post["campo"]["usuario_id"] = $this->id_usuario;
                $post["campo"]["empresa_id"] = $this->id_empresa;

                if(!empty($post["campo"]["id"]))
                {
                    $registro = $this->ajustesRep->find($post["campo"]["id"]);

                    if(count($registro))
                    {
                       	$this->ajustesRep->save($registro, $post);
                        if($registro->fresh()->estado_id == 4)//aprobado
                        {
                            $this->transaccionAjuste->hacerTransaccion($registro->fresh());
                        }
                    }
                }
                else
                {
                    $registro = $this->ajustesRep->create($post);
                }

            } catch (\Exception $e) {
                log_message('error', " __METHOD__  ->  , Linea:  __LINE__  --> " . $e->getMessage() . "\r\n");
                Capsule::rollback();
                $this->Toast->setUrl('ajustes/listar')->run("exception",[$e->getMessage()]);
            }

            if(count($registro)){
                Capsule::commit();
                $this->Toast->run("success",["AJS{$registro->numero}"]);
            }else{
                $this->Toast->run("error");
            }

            redirect(base_url('ajustes/listar'));
    	}
    }

    public function ocultoformulario()
    {
        //falta filtro para que muestre la razon inactiva en caso de que
        //se haya inactivado luego de ser asociada a un registro
        $clause = ["empresa_id" => $this->id_empresa, "transaccionales" => true, 'conItems' => true];

        $this->FlexioAssets->add('js', ['public/resources/compile/modulos/ajustes/formulario.js']);
        $this->FlexioAssets->add('vars',[
            'centros_contables' => $this->centrosContablesRep->get($clause, 'nombre', 'asc'),
            'bodegas' => $this->bodegasRep->getCollectionBodegas($this->bodegasRep->get($clause)),
            'tipos_ajustes' => $this->ajustesCatRep->get(["valor" => "tipo"]),
            'estados' => $this->ajustesCatRep->get(["valor" => "estado"]),
            'categorias' => $this->categoriasRep->get($clause),
            'cuentas' => $this->cuentasRep->get($clause, 'nombre', 'asc'),
            'razones' => $this->ajustesRazonesRep->get($clause, 'nombre', 'asc')
        ]);

        $this->load->view('formulario');
    }

    public function crear()
    {
        $data = [];

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
            "titulo" => '<i class="fa fa-cubes"></i> Crear ajuste'
        ];

        //render
    	$this->template->agregar_titulo_header('Ajustes');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar();
    }



    public function editar($uuid=NULL)
    {
        $data = [];

        //permisos
        $acceso = $this->auth->has_permission('acceso');
        $this->Toast->runVerifyPermission($acceso);

        //Cargo el registro
        $registro = $this->ajustesRep->findByUuid($uuid);
        $registro->load('comentario_timeline');

        //assets
        //dd($this->ajustesRep->getColletionAjuste($registro));
        $this->FlexioAssets->run();//css y js generales
        $this->FlexioAssets->add('vars', [
            "vista" => 'editar',
            "acceso" => $acceso ? 1 : 0,
            "ajuste" => $this->ajustesRep->getColletionAjuste($registro)
        ]);

        //breadcrumb
        $breadcrumb = [
            "titulo" => '<i class="fa fa-cubes"></i> Ajuste '.$this->prefijo.$registro->numero
    	];

        //render
    	$this->template->agregar_titulo_header('Ajustes');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar();
    }

}

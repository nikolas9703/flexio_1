<?php
/**
 * Consumos
 *
 * Modulo para administrar la creacion, edicion de consumos
 *
 * @package    PensaApp
 * @subpackage Controller
 * @category   Controllers
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  10/16/2015
 */

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Bodegas\Repository\BodegasRepository as bodegasRep;
use Flexio\Modulo\Consumos\Repository\ConsumosRepository as consumosRep;
use Flexio\Modulo\Inventarios\Repository\ItemsRepository as itemsRep;
use Flexio\Modulo\Inventarios\Repository\UnidadesRepository as unidadesRep;
use Flexio\Modulo\Colaboradores\Repository\ColaboradoresRepository as colaboradoresRep;

class Consumos extends CRM_Controller
{
    protected $empresa;
    protected $id_empresa;
    protected $prefijo;
    protected $id_usuario;
    
    //repositories
    private $bodegasRep;
    private $consumosRep;
    private $itemsRep;
    private $unidadesRep;
    private $colaboradoresRep;

    public function __construct()
    {
        parent::__construct();
        //MODULOS
        $this->load->module("entradas/Entradas");
        $this->load->module("salidas/Salidas");

        //MODELOS
        $this->load->model("usuarios/Empresa_orm");

        $this->load->model("consumos/Consumos_orm");
        $this->load->model("consumos/Consumos_items_orm");
        $this->load->model("consumos/Consumos_cat_orm");
        $this->load->model("configuracion_rrhh/Departamentos_orm");

        $this->load->model("inventarios/Categorias_orm");
        $this->load->model("inventarios/Unidades_orm");
        $this->load->model("inventarios/Items_categorias_orm");
        $this->load->model("inventarios/Items_unidades_orm");

        $this->load->model("contabilidad/Cuentas_orm");

        $this->load->model("centros/Centros_orm");

        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();

        $uuid_empresa       = $this->session->userdata('uuid_empresa');
        $this->empresa      = Empresa_orm::findByUuid($uuid_empresa);
        $this->id_usuario   = $this->session->userdata("id_usuario");
        $this->id_empresa   = $this->empresa->id;

        //PREFIJO DE NOMEMCLATURA DE PEDIDO
        $this->prefijo = "CONS";
        
        $this->bodegasRep   = new bodegasRep();
        $this->consumosRep  = new consumosRep();
        $this->itemsRep     = new itemsRep();
        $this->unidadesRep  = new unidadesRep();
        $this->colaboradoresRep = new colaboradoresRep();
    }



    public function index()
    {
        redirect("consumos/listar");
    }


    public function listar()
    {
        $data = array();

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
            'public/assets/css/plugins/jquery/toastr.min.css',
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
            'public/assets/js/plugins/toastr.min.js',
            'public/assets/js/default/formulario.js',

            /* Archivos js del propio modulo*/
            'public/assets/js/modules/consumos/listar.js',
        ));

    	/*
    	 * Verificar si existe alguna variable de session
    	 * proveniente de algun formulario de crear/editar
    	 */
    	if($this->session->userdata('idConsumo')){
    		//Borrar la variable de session
    		$this->session->unset_userdata('idConsumo');

    		//Establecer el mensaje a mostrar
    		$data["mensaje"]["clase"] = "alert-success";
    		$data["mensaje"]["contenido"] = "Se ha creado el Consumo satisfactoriamente.";
    	}
    	else if($this->session->userdata('updatedConsumo')){
    		//Borrar la variable de session
    		$this->session->unset_userdata('updatedConsumo');

    		//Establecer el mensaje a mostrar
    		$data["mensaje"]["clase"] = "alert-success";
    		$data["mensaje"]["contenido"] = "Se ha actualizado el Consumo satisfactoriamente.";
    	}


        //Breadcrum Array
        $breadcrumb = array(
            "titulo"    => '<i class="fa fa-cubes"></i> Inventario: Consumos',
            "ruta" => array(
                0 => array(
                    "nombre" => "Inventarios",
                    "activo" => false
                ),
                1 => array(
                    "nombre" => '<b>Consumos</b>',
                    "activo" => true
                )
            ),
            "filtro"    => false, //sin vista grid
            "menu"      => array()
        );

        //Verificar si tiene permiso a la seccion de Crear
        if (1 or $this->auth->has_permission('acceso', 'consumos/crear')){
            $breadcrumb["menu"]["nombre"] = "Crear";
            $breadcrumb["menu"]["url"] = "consumos/crear";
        }

        //Verificar si tiene permiso de Exportar
        if (1 or $this->auth->has_permission('listar__exportar', 'consumos/listar')){
            $breadcrumb["menu"]["opciones"]["#exportarBtn"] = "Exportar";
        }

        //Agregra variables PHP como variables JS
        $this->assets->agregar_var_js(array(
            "mensaje_clase"     => isset($data["mensaje"]["clase"]) ? $data["mensaje"]["clase"] : "0",
            "mensaje_contenido" => isset($data["mensaje"]["contenido"]) ? $data["mensaje"]["contenido"] : "0"
        ));

        unset($data["mensaje"]);

        $data["colaboradores"]  = Colaboradores_orm::deEmpresa($this->id_empresa)
                                ->orderBy("nombre", "ASC")
                                ->get();

        $data["centros"]        = Centros_orm::deEmpresa($this->id_empresa)
                                ->orderBy("nombre", "ASC")
                                ->get();

        $data["estados"]        = Consumos_cat_orm::estados()
                                ->orderBy("id_cat", "ASC")
                                ->get();

    	$this->template->agregar_titulo_header('Listado de Consumos');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar($breadcrumb);

    }


    public function ajax_get_item()
    {

    	if($this->input->is_ajax_request())
        {

            //uuid_item...
            $id             = $this->input->post("id", true);
            $uuid_bodega    = $this->input->post("uuid_bodega", true);
            $registro       = (is_numeric($id)) ? $this->itemsRep->find($id) : $this->itemsRep->findByUuid($id);

            $response               = array();
            $response["success"]    = false;

            if(count($registro))
            {
                $response["success"]    = true;
                $response["registro"]   = $this->itemsRep->getColletionRegistro($registro, $uuid_bodega);
            }

            echo json_encode($response);
            exit();
        }

    }

    /**
     * En este metodo solo me interesa obtener el factor de conversion, en un futuro esas peticiones pasaran al contolador de items
     */
    public function ajax_get_unidad()
    {

    	if($this->input->is_ajax_request())
        {

            //uuid_item...
            $uuid_item      = $this->input->post("uuid_item", true);
            $uuid_unidad    = $this->input->post("uuid_unidad", true);

            $item           = $this->itemsRep->findByUuid($uuid_item);
            
            $response               = array();
            $response["success"]    = false;

            if(count($item))
            {
                $unidad = $this->unidadesRep->findByUuid($uuid_unidad);
                
                $response["success"]    = true;
                $response["registro"]   = array(
                    "factor_conversion" => $item->factor_conversion($unidad->id)
                );
            }

            echo json_encode($response);
            exit();
        }

    }

    public function ajax_delete_item()
    {

    	if($this->input->is_ajax_request())
        {

            $id_consumo_item        = $this->input->post("id_registro", true);
            $response               = array();
            $response["success"]    = false;

            if(!empty($id_consumo_item))
            {
                $response["success"]    = Consumos_items_orm::destroy($id_consumo_item);
            }

            echo json_encode($response);
            exit();
        }

    }

    function ajax_lista_departamentos_asociado_centro()
    {
    	//Just Allow ajax request
    	/*if(!$this->input->is_ajax_request()){
    		return false;
    	}*/
    	
    	$clause = array();
    	$uuid_centro = hex2bin(strtolower($this->input->post('uuid_centro', true)));
    	/*echo '<h2>Consultando Antes colaboradores:</h2><pre>';
            print_r($uuid_centro);
            echo '</pre>'; */
    	if(empty($uuid_centro)){
    		return false;
    	}

    	$response = new stdClass();
    	$response->result = Departamentos_orm::departamento_centro2($uuid_centro);
    	$json = json_encode($response);
    	echo $json;
    	exit;
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
            $registros = Consumos_orm::deEmpresa($this->id_empresa);

            /**
             * Verificar si existe algun $_POST
             * de los campos de busqueda
            */
            $fecha_desde        = $this->input->post('fecha_desde', true);
            $fecha_hasta        = $this->input->post('fecha_hasta', true);
            $colaborador        = $this->input->post('colaborador', true);
            $estado             = $this->input->post('estado', true);
            $referencia         = $this->input->post('referencia', true);
            $numero             = $this->input->post('numero', true);
            $centro             = $this->input->post('centro', true);



            if(!empty($fecha_desde)){
                $registros->deFechaDesde(date("Y-m-d", strtotime($fecha_desde)));
            }

            if(!empty($fecha_hasta)){
                $registros->deFechaHasta(date("Y-m-d", strtotime($fecha_hasta)));
            }

            if(!empty($colaborador)){
                $registros->deColaborador($colaborador);
            }

            if(!empty($estado)){
                $registros->deEstado($estado);
            }

            if(!empty($referencia)){
                $registros->deReferencia($referencia);
            }
            if(!empty($numero)){
                $numero = str_replace($this->prefijo, "", $numero);
                $registros->deNumero($numero);
            }

            if(!empty($centro)){
                $registros->deCentro($centro);
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
                    $hidden_options = "";
                    $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-uuid="'. $row->uuid_consumo .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

                    //IMPORTANTE A MODO DE DESARROLLO ANADI LA CONDICION OR 1
                    //PARA QUE TODAS LAS CONDICIONES DIERAN TRUE

                    $enlace = $row->comp_numeroDocumento();
                    if(1 OR $this->auth->has_permission('acceso', 'consumos/ver/(:any)')){
                        //
                        $hidden_options .= '<a href="'.base_url('consumos/ver/'. $row->uuid_consumo).'" class="btn btn-block btn-outline btn-success">Ver Consumo</a>';

                        $enlace = '<a href="'. base_url('consumos/ver/'. $row->uuid_consumo) .'" style="color:blue;">'.$enlace.'</a>';
                    }


                    //Si no tiene acceso a ninguna opcion
                    //ocultarle el boton de opciones
                    if($hidden_options == ""){
                            $link_option = "&nbsp;";
                    }



                    $response->rows[$i]["id"]   = $row->uuid_consumo;
                    $response->rows[$i]["cell"] = array(
                        $enlace,
                        $row->created_at,
                        count($row->colaborador) ? $row->colaborador->comp_colaboradorEnlace() : "",
                        $row->referencia,
                        $row->centro->nombre,
                        $row->estado->comp__etiquetaSpan(),
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

    /**
     * Cargar Vista Parcial de Tabla
     *
     * @return void
     */
    public function ocultotabla($uuid = NULL, $modulo = "")
    {
        //If ajax request
    	$this->assets->agregar_js(array(
            'public/assets/js/modules/consumos/tabla.js'
    	));

    	$this->load->view('tabla');
    }

    /**
     * Cargar Vista Parcial de Formulario
     *
     * @return void
     */
    public function ocultoformulario($data = array("campos" => array()))
    {
    	$this->assets->agregar_js(array(
            'public/assets/js/modules/consumos/formulario.js'
    	));

        $this->load->view('formulario', $data);
    }

    function crear($segmento3 = "")
    {
        $data       = array();
    	$mensaje    = array();


        $post = $this->input->post();
        if(!empty($post) and isset($post["colaborador_id"]) and isset($post["colaborador_uuid"]))
    	{
            $colaborador = $this->colaboradoresRep->find($post["colaborador_id"]);
            
            $data["campos"]["campos"]["colaborador"]        = $colaborador->id;
            $data["campos"]["campos"]["centro_contable"]    = $colaborador->centro_contable->uuid_centro;
            $data["campos"]["campos"]["areaNegocio"]        = '';
            //cachi agrega aca el valor para el area de negocio
        }elseif(!empty($post))
        {
            $response = false;

            /**
             * Guardar Consumo
             * de Items
             */
            $response = $this->guardar_consumo();

            if($response == "1"){

                redirect(base_url('consumos/listar'));

            }else{
                //Establecer el mensaje a mostrar
                $data["mensaje"]["clase"] = "alert-danger";
                $data["mensaje"]["contenido"] = "Hubo un error al tratar de crear el consumo.";
            }
    	}

    	//Introducir mensaje de error al arreglo
    	//para mostrarlo en caso de haber error
    	$data["message"] = $mensaje;

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
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/combodate/combodate.js',
            'public/assets/js/plugins/jquery/combodate/momentjs.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/default/tabla-dinamica.jquery.js',
            'public/assets/js/default/formulario.js',
    	));

    	$breadcrumb = array(
            "titulo" => '<i class="fa fa-cubes"></i> Crear consumo'
    	);

        $data["campos"]["campos"]["numero"] = $this->prefijo . $this->genera_numero_consumo();
        $data["campos"]["campos"]["fecha"]  = date('d-m-Y', time());

    	$this->template->agregar_titulo_header('Consumos');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar();
    }

    /**
     * Generar # consecutivo de Consumo.
     */
    private function genera_numero_consumo()
    {
    	$countConsumos = Consumos_orm::deEmpresa($this->id_empresa)->count();

    	return sprintf("%08d", ($countConsumos + 1));
    }

    /**
     * Guardar datos de consumo
     */
    public function guardar_consumo($fieldset_consumo=NULL, $fieldset_items=NULL)
    {
    	return Capsule::transaction(function() use ($fieldset_consumo, $fieldset_items){

                $post               = $this->input->post();
                $post["empresa_id"] = $this->id_empresa;
                $post["usuario_id"] = $this->id_usuario;
                
	    	$registro   = $this->consumosRep->save($post, $fieldset_consumo, $fieldset_items);

    		//Si el consumo se esta guardando desde mismo
    		//modulo de consumo, crear variable de session.
    		if($fieldset_consumo==NULL && $fieldset_items==NULL){
    			$this->session->set_userdata('idConsumo', $registro->id);
    		}

    		return true;
    	});
    }

    function editar($uuid=NULL)
    {
        if(!$uuid)
        {
            echo "Error.";
            die();
        }

    	$data       = array();
    	$mensaje    = array();

        //Cargo el registro
        $registro   = $this->consumosRep->findByUuid($uuid);
        $registro->load('comentario_timeline');
    	if(!empty($_POST))
    	{
            $response = false;
            $response = Capsule::transaction(
                function() use ($uuid)
                {
//                    echo "<pre>";
//                    print_r($_POST);
//                    echo "<pre>";
//                    die();
                    $post                           = $this->input->post();
                    $post["empresa_id"]             = $this->id_empresa;
                    $post["usuario_id"]             = $this->id_usuario;
                    $post["campo"]["uuid_consumo"]  = $uuid;
                    
                    $this->consumosRep->save($post);

                    $this->session->set_userdata('updatedConsumo', 1);
                    return true;
                }
            );


            if($response == "1"){

                redirect(base_url('consumos/listar'));

            }else{
                //Establecer el mensaje a mostrar
                $data["mensaje"]["clase"] = "alert-danger";
                $data["mensaje"]["contenido"] = "Hubo un error al tratar de crear el consumo.";
            }
    	}

    	//Introducir mensaje de error al arreglo
    	//para mostrarlo en caso de haber error
    	$data["message"] = $mensaje;

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
            'public/assets/css/modules/stylesheets/consumos.css'
        ));

    	$this->assets->agregar_js(array(
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/combodate/combodate.js',
            'public/assets/js/plugins/jquery/combodate/momentjs.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/default/tabla-dinamica.jquery.js',
            'public/assets/js/default/formulario.js',
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/resources/compile/modulos/consumos/comentario-consumos.js'
    	));

        $this->assets->agregar_var_js(array(

            "consumos_id" => $registro->id,
            "coment_consumos" => (isset($registro->comentario_timeline)) ? $registro->comentario_timeline : ""
        ));
    	$breadcrumb = array(
            "titulo" => '<i class="fa fa-cubes"></i> Consumo '.$registro->numero_documento
    	);


        $data["campos"]["campos"]           = $this->consumosRep->getColletionCampos($registro);
        $data["campos"]["campos"]["items"]  = $this->consumosRep->getColletionCamposItems($registro->items);

    	$this->template->agregar_titulo_header('Consumos');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar();
    }

}

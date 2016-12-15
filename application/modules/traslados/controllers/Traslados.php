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
    
    public function __construct()
    {
        parent::__construct();
        //MODULOS
        $this->load->module("entradas/Entradas");
        $this->load->module("salidas/Salidas");
        
        //MODELOS
        $this->load->model("usuarios/Empresa_orm");
        
        $this->load->model("pedidos/Pedidos_orm");
        
        $this->load->model("traslados/Traslados_orm");
        $this->load->model("traslados/Traslados_items_orm");
        $this->load->model("traslados/Traslados_cat_orm");
        
        $this->load->model("ordenes/Ordenes_orm");
        $this->load->model("ordenes/Ordenes_items_orm");
        
        $this->load->model("inventarios/Items_orm");
        $this->load->model("inventarios/Items_unidades_orm");
        $this->load->model("inventarios/Unidades_orm");
        
        $this->load->model("entradas/Entradas_orm");
        $this->load->model("entradas/Entradas_items_orm");
        
        $this->load->model("bodegas/Bodegas_orm");
        
        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();
        
        $uuid_empresa       = $this->session->userdata('uuid_empresa');
        $this->empresa      = Empresa_orm::findByUuid($uuid_empresa);
        $this->id_usuario   = $this->session->userdata("id_usuario");
        $this->id_empresa   = $this->empresa->id;
        
        //PREFIJO DE NOMEMCLATURA DE PEDIDO
        $this->prefijo = "TRAS";
        
        //repositorios
        $this->bodegasRep       = new bodegasRep();
        $this->entradasRep      = new entradasRep();
        $this->trasladosRep     = new trasladosRep();
    }
    
    

    public function index()
    {
        redirect("traslados/listar");
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
            'public/assets/js/modules/traslados/listar.js',
        ));
    	
    	/*
    	 * Verificar si existe alguna variable de session
    	 * proveniente de algun formulario de crear/editar
    	 */
    	if($this->session->userdata('idTraslado')){
    		//Borrar la variable de session
    		$this->session->unset_userdata('idTraslado');
    	
    		//Establecer el mensaje a mostrar
    		$data["mensaje"]["clase"] = "alert-success";
    		$data["mensaje"]["contenido"] = "Se ha creado el Traslado satisfactoriamente.";
    	}
    	else if($this->session->userdata('updatedTraslado')){
    		//Borrar la variable de session
    		$this->session->unset_userdata('updatedTraslado');
    	
    		//Establecer el mensaje a mostrar
    		$data["mensaje"]["clase"] = "alert-success";
    		$data["mensaje"]["contenido"] = "Se ha actualizado el Traslado satisfactoriamente.";
    	}
        
        
        //Breadcrum Array
        $breadcrumb = array(
            "titulo"    => '<i class="fa fa-cubes"></i> Inventario: Traslados',
            "ruta" => array(
                0 => array(
                    "nombre" => "Inventarios",
                    "activo" => false
                ),
                1 => array(
                    "nombre" => '<b>Traslados</b>',
                    "activo" => true
                )
            ),
            "filtro"    => false, //sin vista grid
            "menu"      => array()
        );
        
        //Verificar si tiene permiso a la seccion de Crear
        if (1 or $this->auth->has_permission('acceso', 'traslados/crear')){
            $breadcrumb["menu"]["nombre"] = "Crear";
            $breadcrumb["menu"]["url"] = "traslados/crear";
        }
        
        //Verificar si tiene permiso de Exportar
        if (1 or $this->auth->has_permission('listar__exportar', 'traslados/listar')){
            $breadcrumb["menu"]["opciones"]["#exportarBtn"] = "Exportar";
        }
        
        //Agregra variables PHP como variables JS
        $this->assets->agregar_var_js(array(
            "mensaje_clase"     => isset($data["mensaje"]["clase"]) ? $data["mensaje"]["clase"] : "0",
            "mensaje_contenido" => isset($data["mensaje"]["contenido"]) ? $data["mensaje"]["contenido"] : "0"
        ));
        
        unset($data["mensaje"]);
        

        $data["bodegas"]    = Bodegas_orm::deEmpresa($this->id_empresa)
                            ->activas()
                            ->transaccionales($this->id_empresa)
                            ->orderBy("nombre", "ASC")
                            ->get();
        
        $data["estados"]    = Traslados_cat_orm::estados()
                            ->orderBy("id_cat", "ASC")
                            ->get();
        
    	$this->template->agregar_titulo_header('Listado de Traslados');
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
            $uuid_de_bodega = $this->input->post("uuid_de_bodega", true);
            $item           = Items_orm::findByUuid($uuid);
            
            if(empty($uuid))
            {
                return false;
            }
            
            $cantidad_disonible = 0;
            $precio             = 0;//queda pendiente el calculo de precio
            $enInventario       = $item->enInventario($uuid_de_bodega);
            $precioBase         = $item->precioBase();
            
            $response               = array();
            $response["success"]    = true;
            $response["registro"]  = array(
                "cantidad_disponible"   => $enInventario["cantidadDisponibleBase"],
                "precio_unidad"         => $precioBase,
                "descripcion"           => $item->descripcion,
                "descuento"             => $precioBase//lo uso como auxiliar para no perder el precio_unidad en la vista
            );

            foreach ($item->item_unidades as $item_unidad)
            {
                $response["registro"]["unidades"][] = array(
                    "id"                => $item_unidad->unidad->id,
                    "unidad_id"         => $item_unidad->unidad->id,
                    "nombre"            => $item_unidad->unidad->nombre,
                    "base"              => $item_unidad->base,
                    "factor_conversion" => $item_unidad->factor_conversion
                );
            }
            
            echo json_encode($response);
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
            $registros = Traslados_orm::deEmpresa($this->id_empresa);
            
            /**
             * Verificar si existe algun $_POST
             * de los campos de busqueda
            */
            $numero_traslado    = $this->input->post('numero_traslado', true);
            $de_bodega          = $this->input->post('de_bodega', true);
            $a_bodega           = $this->input->post('a_bodega', true);
            $fecha_solicitud    = $this->input->post('fecha_solicitud', true);
            $fecha_entrega      = $this->input->post('fecha_entrega', true);
            $estado             = $this->input->post('estado', true);
            
            if(!empty($numero_traslado)){
                $numero_traslado = str_replace($this->prefijo, "", $numero_traslado);
                $registros->deTraslado($numero_traslado);
            }
            
            if(!empty($de_bodega)){
                $registros->deProcedencia($de_bodega);
            }
            
            if(!empty($a_bodega)){
                $registros->deDestino($a_bodega);
            }
            
            if(!empty($fecha_solicitud)){
                $registros->deFechaDeSolicitud(date("Y-m-d", strtotime($fecha_solicitud)));
            }
            
            if(!empty($fecha_entrega)){
                $registros->deFechaDeEntrega(date("Y-m-d", strtotime($fecha_entrega)));
            }
            
            if(!empty($estado)){
                $registros->deEstado($estado);
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
                    $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-uuid="'. $row->uuid_traslado .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                
                    //IMPORTANTE A MODO DE DESARROLLO ANADI LA CONDICION OR 1
                    //PARA QUE TODAS LAS CONDICIONES DIERAN TRUE

                    $enlace = $this->prefijo.$row->numero;
                    if(1 OR $this->auth->has_permission('acceso', 'traslados/ver/(:any)')){
                        //
                        $hidden_options .= '<a href="'.base_url('traslados/ver/'. $row->uuid_traslado).'" class="btn btn-block btn-outline btn-success">Ver Traslado</a>';
                        
                        $enlace = '<a href="'. base_url('traslados/ver/'. $row->uuid_traslado) .'" style="color:blue;">'.$enlace.'</a>';
                    }

                    
                    //Si no tiene acceso a ninguna opcion
                    //ocultarle el boton de opciones
                    if($hidden_options == ""){
                            $link_option = "&nbsp;";
                    }
                    
                   

                    $response->rows[$i]["id"]   = $row->uuid_traslado;
                    $response->rows[$i]["cell"] = array(
                        $enlace,
                        $row->fecha_creacion,
                        (strtotime($row->fecha_creacion) <= strtotime($row->fecha_entrega)) ? $row->fecha_entrega : "",
                        $row->deBodega->nombre,//procedencia
                        $row->bodega->nombre,//destino
                        $row->estado->comp__etiquetaWithSpan(),
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
            'public/assets/js/modules/traslados/tabla.js'
    	));
    	
    	$this->load->view('tabla');
    }
    
    public function ocultocabecera($data = array())
    {
        $this->load->view('cabecera', $data);
    }
    
    /**
     * Cargar Vista Parcial de Formulario
     *
     * @return void
     */
    public function ocultoformulario($data = array("campos" => array()))
    {
    	$this->assets->agregar_js(array(
            'public/assets/js/modules/traslados/formulario.js'
    	));
        
        $this->load->view('formulario', $data);
    }
    
    function crear($uuid_pedido = NULL)
    {
        $data       = array();
    	$mensaje    = array();
        
        
        if(!empty($_POST))
    	{

            $response = false;
            $response = Capsule::transaction(
                function()
                {
                    $post                           = $this->input->post();
                    $post["campo"]["empresa_id"]    = $this->id_empresa;
                    $post["campo"]["usuario_id"]    = $this->id_usuario;
                    
                    $traslado   = $this->trasladosRep->create($post);
                    $this->session->set_userdata('idTraslado', $traslado->id);
                    
                    return true;
                }
            );
            
            
            if($response == "1"){
                
                redirect(base_url('traslados/listar'));
                
            }else{
                //Establecer el mensaje a mostrar
                $data["mensaje"]["clase"] = "alert-danger";
                $data["mensaje"]["contenido"] = "Hubo un error al tratar de crear el item.";
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
            "titulo" => '<i class="fa fa-cubes"></i> Crear traslado'
    	);
        
        
        $registroMax    = Traslados_orm::deEmpresa($this->id_empresa)
                        ->groupBy("id_empresa")
                        ->max("numero");
        
        $data["campos"] = array(
            "campos"    => array(
                "fecha"     => date('d-m-Y', time()),
                "numero"    => $this->prefijo.sprintf("%08d", ($registroMax + 1))
            )
        );
        
        
        
        //datos para empezardesde
        if($uuid_pedido)
        {
            $pedido     = Pedidos_orm::findByUuid($uuid_pedido);
            
            $data["campos"]["campos"]["lugar"]  = strtoupper(bin2hex($pedido->uuid_lugar));
            $data["campos"]["campos"]["centro"] = strtoupper(bin2hex($pedido->uuid_centro));
            
            foreach ($pedido->items as $item)
            {
                $data["campos"]["campos"]["items"][] = array(
                    "item"              => $item->item->uuid_item,
                    "descripcion"       => $item->item->descripcion,
                    "cantidad"          => $item->cantidad,
                    "unidad"            => $item->unidadReferencia->uuid_unidad,
                    "cuenta"            => $item->cuentaDeGasto->uuid_cuenta,
                    "precio_total"      => "",//se calcula
                    "id_traslado_item"  => $item->id
                );
            }
        }else
        {
            //traslado_items
            $data["campos"]["campos"]["items"][] = array(
                "descuento"             => "descuento",
                "precio_unidad"         => "precio_unidad",
                "id_traslado_item"      => "id_traslado_item"
            );
        }
        //Lista de Pedidos en Cotizacion
        $data["pedidos"]        = Pedidos_orm::deEmpresa($this->id_empresa)->enCotizacionOParcial()->get();
        //Cabecera del formulario - Metodo empezar desde...
        $data["empezar_tipo"]   = $uuid_pedido ? "pedido" : "";
        $data["empezar_uuid"]   = $uuid_pedido ? : "";
        
        
    	$this->template->agregar_titulo_header('Traslados');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar();
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
        $registro   = Traslados_orm::findByUuid($uuid);
        $traslado_coment = $this->trasladosRep->findByUuid($uuid);
        $traslado_coment->load('comentario_timeline');
    
    	if(!empty($_POST))
    	{

            $response = false;
            $response = Capsule::transaction(
                function() use ($traslado_coment)
                {
                    $campo                  = $this->input->post("campo");

                    $traslado_coment->id_estado    = $campo["estado"];
                    
                    //GUARDO EL REGISTRO
                    $traslado_coment->save();
                    
                    return true;
                }
            );
                
            
            if($response){
                $this->session->set_userdata('updatedTraslado', $registro->id);
                redirect(base_url('traslados/listar'));
            }else{
                //Establecer el mensaje a mostrar
                $data["mensaje"]["clase"] = "alert-danger";
                $data["mensaje"]["contenido"] = "Hubo un error al tratar de editar el pedido.";
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
            'public/assets/css/modules/stylesheets/traslados.css'
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
            'public/resources/compile/modulos/traslados/comentario-traslados.js'
    	));

        $this->assets->agregar_var_js(array(
            "traslados_id" => $traslado_coment->id,
            "coment_traslados" => (isset($traslado_coment->comentario_timeline)) ? $traslado_coment->comentario_timeline : ""
        ));
    	$breadcrumb = array(
            "titulo" => '<i class="fa fa-cubes"></i> Traslado '.$this->prefijo.$registro->numero
    	);
        
        
        $data["campos"] = array(
            "campos"    => array(
                "numero"        => $this->prefijo.$registro->numero,
                "a_bodega"      => strtoupper(bin2hex($registro->uuid_lugar)),
                "de_bodega"     => strtoupper(bin2hex($registro->uuid_lugar_anterior)),
                "fecha"         => $registro->fecha_creacion,
                "fecha_entrega" => (strtotime($registro->fecha_creacion) <= strtotime($registro->fecha_entrega)) ? $registro->fecha_entrega : "",
                "estado"        => $registro->id_estado
            )
        );
        
        //ajuste_items
        foreach($registro->traslados_items as $ti)
        {
            $data["campos"]["campos"]["items"][] = array(
                "item"              => $ti->item->uuid_item,
                "descripcion"       => $ti->item->descripcion,
                "observacion"       => $ti->observacion,
                "cantidad_enviada"  => $ti->cantidad,
                "unidad"            => $ti->unidadReferencia->uuid_unidad,
                "id_traslado_item"  => $ti->id
            );
        }
        
        //Lista de Pedidos -> en la vista de editar los pedidos no son seleccionables
        $data["pedidos"]    = Pedidos_orm::deEmpresa($this->id_empresa)->get();
        //Cabecera del formulario - Metodo empezar desde...
        $data["empezar_tipo"]   = count($registro->pedido) ? "pedido" : "";
        $data["empezar_uuid"]   = count($registro->pedido) ? strtoupper(bin2hex($registro->uuid_pedido)) : "";
        
    	$this->template->agregar_titulo_header('Traslados');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar();
    }
    
}

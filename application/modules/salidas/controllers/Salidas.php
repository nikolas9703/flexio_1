<?php
/**
 * Pedidos
 *
 * Modulo para administrar la creacion, edicion de salidas de inventario
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
use Flexio\Modulo\Salidas\Repository\SalidasRepository as salidasRep;
use Flexio\Modulo\Bodegas\Repository\BodegasRepository as bodegasRep;
use Flexio\Modulo\Salidas\Repository\SalidasCatRepository as salidasCatRep;
use Flexio\Modulo\Contabilidad\Repository\CuentasRepository as cuentasRep;
use Flexio\Modulo\Inventarios\Repository\ItemsRepository as itemsRep;
use Flexio\Modulo\Salidas\Transacciones\SalidasTransacciones as transaccionSalida;
use Flexio\Modulo\Cliente\Repository\ClienteRepository as clientesRep;


class Salidas extends CRM_Controller
{
    protected $id_empresa;
    protected $prefijo;
    protected $id_usuario;

    //repositorios
    private $salidasRep;
    private $bodegasRep;
    private $salidasCatRep;
    private $cuentasRep;
    private $itemsRep;
    private $clienteRep;

    public function __construct()
    {
        parent::__construct();

        $this->load->model("Salidas_orm");
        $this->load->model("Salidas_cat_orm");

        $this->load->model("inventarios/Items_orm");
        $this->load->model("inventarios/Unidades_orm");

        $this->load->model("bodegas/Bodegas_orm");
        $this->load->model("bodegas/Bodegas_cat_orm");

        $this->load->model("clientes/Cliente_orm");

        $this->load->model("colaboradores/Colaboradores_orm");

        $this->load->model("contabilidad/Cuentas_orm");

        $this->load->model("proveedores/Proveedores_orm");

        $this->load->model("traslados/Traslados_orm");
        $this->load->model("traslados/Traslados_items_orm");

        $this->load->model("ordenes_ventas/Orden_ventas_orm");
        $this->load->model("ordenes/Ordenes_orm");
        $this->load->model("ordenes_ventas/Ordenes_venta_item_orm");

        $this->load->model("facturas/Factura_orm");

        $this->load->model("consumos/Consumos_orm");
        $this->load->model("consumos/Consumos_items_orm");

        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();

        $uuid_empresa       = $this->session->userdata('uuid_empresa');
        $this->empresa      = Empresa_orm::findByUuid($uuid_empresa);
        $this->id_usuario   = $this->session->userdata("id_usuario");
        $this->id_empresa   = $this->empresa->id;

        //PREFIJO DE NOMEMCLATURA DE PEDIDO
        $this->prefijo = "SAL";

        //repositorios
        $this->salidasRep       = new salidasRep();
        $this->bodegasRep       = new bodegasRep();
        $this->salidasCatRep    = new salidasCatRep();
        $this->cuentasRep       = new cuentasRep();
        $this->itemsRep         = new itemsRep();
        $this->transaccionSalida= new transaccionSalida();
        $this->clienteRep = new clientesRep();
    }



    public function index()
    {
        redirect("salidas/listar");
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
            'public/assets/js/modules/salidas/listar.js',
        ));

    	/*
    	 * Verificar si existe alguna variable de session
    	 * proveniente de algun formulario de crear/editar
    	 */
    	if($this->session->userdata('idSalida')){
            //Borrar la variable de session
            $this->session->unset_userdata('idSalida');

            //Establecer el mensaje a mostrar
            $data["mensaje"]["clase"] = "alert-success";
            $data["mensaje"]["contenido"] = "Se ha creado la Salida satisfactoriamente.";
    	}
    	else if($this->session->userdata('updatedSalida')){
    		//Borrar la variable de session
    		$this->session->unset_userdata('updatedSalida');

    		//Establecer el mensaje a mostrar
    		$data["mensaje"]["clase"] = "alert-success";
    		$data["mensaje"]["contenido"] = "Se ha actualizado la Salida satisfactoriamente.";
    	}




        $data["estados"]        = Salidas_cat_orm::estados()
                                ->orderBy("id_cat", "ASC")
                                ->get();
        

        $data["bodegas"]        = Bodegas_orm::deEmpresa($this->id_empresa)
                                ->activas()
                                ->transaccionales($this->id_empresa)
                                ->orderBy("nombre", "ASC")
                                ->get();

        
        $data["clientes"] = $this->clienteRep->getAll(['empresa_id'=>$this->id_empresa],['uuid_cliente','nombre']);        

        
        $data["colaboradores"]  = Colaboradores_orm::deEmpresa($this->id_empresa)->get();

       // $data["clientes"] = [];
         //$data["colaboradores"] =[];
        //LLenando el catalogo de destinos
        $destinos   = array();
        foreach ($data["bodegas"] as $bodega)
        {
            $destinos[] = array(
                "uuid"      => $bodega->uuid_bodega,
                "nombre"    => $bodega->nombre
            );
        }

        foreach ($data["clientes"] as $cliente)
        {
            $destinos[] = array(
                "uuid"      => $cliente->uuid_cliente,
                "nombre"    => $cliente->nombre
            );
        }

        foreach ($data["colaboradores"] as $colaborador)
        {
            $destinos[] = array(
                "uuid"      => $colaborador->comp_uuidColaborador(),
                "nombre"    => $colaborador->comp_nombreCompleto()
            );
        }

        $data["destinos"]   = $destinos;


    	//Breadcrum Array
        $breadcrumb = array(
            "titulo"    => '<i class="fa fa-cubes"></i> Inventario: Salidas',
            "ruta" => array(
                0 => array(
                    "nombre" => "Inventarios",
                    "activo" => false
                ),
                1 => array(
                    "nombre" => '<b>Salidas</b>',
                    "activo" => true
                )
            ),
            "filtro"    => false,//sin vista grid
            "menu"      => array()
        );


        //Verificar si tiene permiso de Exportar
        if (1 or $this->auth->has_permission('listar__exportar', 'salidas/listar')){
            $breadcrumb["menu"]["opciones"]["#exportarBtn"] = "Exportar";
        }

        //Agregra variables PHP como variables JS
        $this->assets->agregar_var_js(array(
            "mensaje_clase"     => isset($data["mensaje"]["clase"]) ? $data["mensaje"]["clase"] : "0",
            "mensaje_contenido" => isset($data["mensaje"]["contenido"]) ? $data["mensaje"]["contenido"] : "0"
        ));

        unset($data["mensaje"]);

    	$this->template->agregar_titulo_header('Listado de Salidas de Inventario');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar($breadcrumb);

    }

    public function ajax_get_destinos()
    {

    	if($this->input->is_ajax_request())
        {
            $bodegas            = Bodegas_orm::deEmpresa($this->id_empresa)
                                ->activas()
                                ->transaccionales($this->id_empresa)
                                ->orderBy("nombre", "ASC")
                                ->get();

            $clientes           = Cliente_orm::listar(array("empresa_id" => $this->id_empresa));

            $colaboradores      = Colaboradores_orm::deEmpresa($this->id_empresa)
                                ->get();



            //LLenando el catalogo de destinos
            $destinos   = array();
            foreach ($bodegas as $bodega)
            {
                $destinos[] = array(
                    "uuid"      => $bodega->uuid_bodega,
                    "nombre"    => $bodega->nombre
                );
            }

            foreach ($clientes as $cliente)
            {
                $destinos[] = array(
                    "uuid"      => $cliente->uuid_cliente,
                    "nombre"    => $cliente->nombre
                );
            }

            foreach ($colaboradores as $colaborador)
            {
                $destinos[] = array(
                    "uuid"      => $colaborador->comp_uuidColaborador(),
                    "nombre"    => $colaborador->comp_nombreCompleto()
                );
            }


            $response               = array();
            $response["success"]    = false;

            if(count($destinos))
            {
                $response["success"]    = true;
                $response["registros"]  = $destinos;
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
            
            $clause                     = $this->input->post();
            $clause["empresa_id"]       = $this->id_empresa;
            $clause["usuario_id"]       = $this->id_usuario;
            $clause["estados_validos"]  = true;
             
            if ($this->input->post("cliente_id")<> ''){
                $clause["destino"] = (new clientesRep)->findByUuid($this->input->post("cliente_id"))->id;
                
            }
            

            list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
            $count = $this->salidasRep->count($clause);
            //dd($count);
            list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

            //Constructing a JSON
            $response   = new stdClass();
            $response->page     = $page;
            $response->total    = $total_pages;
            $response->records  = $count;

            if($count > 0)
            {
                $salidas = $this->salidasRep->get($clause, $sidx, $sord, $limit, $start);
                
                foreach ($salidas AS $i => $row)
                {
                    $response->rows[$i]["id"]   = $row->uuid_salida;
                    $response->rows[$i]["cell"] = $this->salidasRep->getColletionCell($row, $this->auth);
                }
                
            }

            echo json_encode($response);
            exit;
    	}
    }

    public function ajax_listar_historial_item()
    {
    	//Just Allow ajax request
    	if($this->input->is_ajax_request())
    	{
            $clause                     = $this->input->post();
            $clause["empresa_id"]       = $this->id_empresa;
            $clause["usuario_id"]       = $this->id_usuario;
            $clause["estados_validos"]  = true;

            list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
            $count = $this->salidasRep->count($clause);
            list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

            //Constructing a JSON
            $response   = new stdClass();
            $response->page     = $page;
            $response->total    = $total_pages;
            $response->records  = $count;

            if($count > 0)
            {
                $salidas = $this->salidasRep->get($clause, $sidx, $sord, $limit, $start);
                foreach ($salidas AS $i => $row)
                {
                    $response->rows[$i]["id"]   = $row->uuid_salida;
                    $response->rows[$i]["cell"] = $this->salidasRep->getColletionCellHistorialItem($row, $this->auth, $clause);
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
    public function ocultotabla()
    {
        //If ajax request
    	$this->assets->agregar_js(array(
    		'public/assets/js/modules/salidas/tabla.js'
    	));

    	$this->load->view('tabla');
    }

    public function ocultotablaV2($sp_string_var = '')
    {

        //If ajax request
    	$this->assets->agregar_js(array(
    		'public/assets/js/modules/salidas/tabla_item.js'
    	));

        $sp_array_var = explode('=', $sp_string_var);
        if (count($sp_array_var) == 2) {

            $this->assets->agregar_var_js(array(
                $sp_array_var[0] => $sp_array_var[1]
            ));

        }

    	$this->load->view('tabla');

    }

    /**
     * Cargar Vista Parcial de Formulario
     *
     * @return void
     */
    public function ocultoformulario($data = array())
    {
    	$this->assets->agregar_js(array(
            'public/assets/js/modules/salidas/formulario.js'
    	));

        //catalogos
        $clause             = ["empresa_id" => $this->id_empresa];
        $data["bodegas"]    = $this->bodegasRep->get($clause);
        $data["estados"]    = $this->salidasCatRep->get(["valor" => "estado"]);
        $data["cuentas"]    = $this->cuentasRep->get($clause);
        $data["items"]      = $this->itemsRep->get($clause);

        $this->load->view('formulario', $data);
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
        $registro = $this->salidasRep->findByUuid($uuid);

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
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/default/tabla-dinamica.jquery.js',
            'public/assets/js/default/formulario.js',
    	));

        $this->assets->agregar_var_js(array(
            "uuid_destino"   => (count($registro->operacion) and count($registro->operacion->destino)) ? $registro->operacion->destino->uuid : "null"
        ));

    	$breadcrumb = array(
            "titulo" => '<i class="fa fa-cubes"></i> Inventario: Salida '.$registro->numero_salida
    	);

        $data["campos"]["campos"]           = $this->salidasRep->getColletionCampos($registro);
        $data["campos"]["campos"]["items"]  = $this->salidasRep->getColletionCamposItems($registro->items);

//        echo "<pre>";
//        print_r($data);
//        echo "<pre>";
//        die();

    	$this->template->agregar_titulo_header('Salidas');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar();
    }

    public function guardar()
    {
        if(!empty($_POST))
    	{
            $response   = false;
            $post       = $this->input->post();
            Capsule::transaction(
                function() use (&$response, $post)
                {

                    $registro   = $this->salidasRep->find($post["campo"]["salida_id"]);

                   if($registro->operacion_type == 'Flexio\Modulo\Ajustes\Models\Ajustes' || $registro->operacion_type== 'Flexio\Modulo\Consumos\Models\Consumos'){
                    		$this->transaccionSalida->hacerTransaccion($registro);
                   }

                    $response   = $this->salidasRep->save($registro, $post);
                }
            );


            if($response){
                $this->session->set_userdata('updatedSalida', $post["campo"]["salida_id"]);
                redirect(base_url('salidas/listar'));
            }else{
                //Establecer el mensaje a mostrar
                $data["mensaje"]["clase"] = "alert-danger";
                $data["mensaje"]["contenido"] = "Hubo un error al tratar de editar.";
            }
    	}
    }

    public static function comp__crearSalida($salida)
    {
        $CI                     = get_instance();
        $Controller             = $CI->salidas;
        $registro               = Salidas_orm::deOperacionType($salida["type"])->deOperacionId($salida["id"])->first();
        $salida["estado_id"]    = isset($salida["estado_id"]) ? $salida["estado_id"] : "1";

        if(!count($registro))
        {
            $numero                     = Salidas_orm::deEmpresa($Controller->id_empresa)->count();
            $registro                   = new Salidas_orm;
            $registro->uuid_salida      = Capsule::raw("ORDER_UUID(uuid())");
            $registro->prefijo          = $Controller->prefijo;
            $registro->numero           = $numero + 1;
            $registro->created_by       = $Controller->id_usuario;
            $registro->empresa_id       = $Controller->id_empresa;
            $registro->operacion_id     = $salida["id"];
            $registro->operacion_type   = $salida["type"];
        }

        $registro->estado_id        = $salida["estado_id"];

        $registro->save();

        //Verifico si no tiene registros para mostrar para proceder a borrar
        if(count($registro->comp__salidasItemsModel()) == 0)
        {
            $registro->delete();
        }
    }
}

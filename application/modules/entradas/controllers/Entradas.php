<?php
/**
 * Pedidos
 *
 * Modulo para administrar la creacion, edicion de entradas de inventario
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
use Flexio\Modulo\Entradas\Repository\EntradasRepository as entradasRep;
use Flexio\Modulo\Entradas\Repository\EntradasCatRepository as entradasCatRep;
use Flexio\Modulo\Bodegas\Repository\BodegasRepository as bodegasRep;
use Flexio\Modulo\Inventarios\Repository\ItemsRepository as itemsRep;
use Flexio\Modulo\ConfiguracionContabilidad\Repository\CuentaInventarioRepository as CuentaInventario;
use Flexio\Modulo\Entradas\Transaccion\TransaccionFactura as transaccionFactura;
use Flexio\Modulo\FacturasCompras\Models\FacturaCompra as FacturaCompra;

class Entradas extends CRM_Controller
{
    protected $id_empresa;
    protected $empresa_id;
    protected $prefijo;
    protected $id_usuario;
    protected $cuenta_inventario;
    //repositorios
    private $entradasRep;
    private $entradasCatRep;
    private $bodegasRep;
    private $itemsRep;
    private $transaccionFactura;

    public function __construct()
    {
        parent::__construct();
        $this->load->model("Entradas_orm");
        $this->load->model("Entradas_items_orm");
        $this->load->model("Entradas_cat_orm");

        $this->load->model("inventarios/Items_orm");
        $this->load->model("inventarios/Unidades_orm");

        $this->load->model("ordenes/Ordenes_orm");
        $this->load->model("ordenes/Ordenes_items_orm");

        $this->load->model("bodegas/Bodegas_orm");
        $this->load->model("bodegas/Bodegas_cat_orm");
        $this->load->model('contabilidad/Cuentas_orm');
        $this->load->model("proveedores/Proveedores_orm");

        $this->load->model("traslados/Traslados_orm");
        $this->load->model("traslados/Traslados_items_orm");


//        $this->load->model("inventarios/Items_orm");
//        $this->load->model("pedidos/Pedidos_orm");
//        $this->load->model("pedidos/Pedidos_estados_orm");
//        $this->load->model("pedidos/Pedidos_items_orm");

        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();

        $uuid_empresa       = $this->session->userdata('uuid_empresa');
        $this->empresa      = Empresa_orm::findByUuid($uuid_empresa);
        $this->id_usuario   = $this->session->userdata("id_usuario");
        $this->id_empresa   = $this->empresa->id;
        $this->empresa_id   = $this->empresa->id;

        //PREFIJO DE NOMEMCLATURA DE PEDIDO
        $this->prefijo = "ENT";

        //repositorios
        $this->entradasRep = new entradasRep();
        $this->entradasCatRep = new entradasCatRep();
        $this->bodegasRep = new bodegasRep();
        $this->itemsRep = new itemsRep();
        $this->transaccionFactura = new transaccionFactura();
        $this->cuenta_inventario = new CuentaInventario;
    }



    public function index()
    {
        redirect("entradas/listar");
    }


    public function listar()
    {
        $data = array();
        $toast = new Flexio\Library\Toast;

    	$this->_addMainCss();

        $this->_addMainJS();
        $this->assets->agregar_js(array(
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/plugins/jquery/jqgrid/plugins/jQuery.jqGrid.columnToggle.js',
            'public/assets/js/plugins/jquery/switchery.min.js',
            'public/assets/js/plugins/bootstrap/bootstrap-tagsinput.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            'public/assets/js/plugins/jquery/fileinput/fileinput.js',
            'public/assets/js/plugins/jquery/fileinput/fileinput_locale_es.js',
            'public/assets/js/default/grid.js',
            'public/assets/js/default/subir_documento_modulo.js',
            'public/assets/js/modules/entradas/listar.js',
        ));

    	/*
    	 * Verificar si existe alguna variable de session
    	 * proveniente de algun formulario de crear/editar
    	 */
    	if($this->session->userdata('idEntrada')){
    		//Borrar la variable de session
    		$this->session->unset_userdata('idEntrada');

    		//Establecer el mensaje a mostrar
    		$data["mensaje"]["clase"] = "alert-success";
    		$data["mensaje"]["contenido"] = "Se ha creado la Entrada satisfactoriamente.";
    	}
    	else if($this->session->userdata('updatedEntrada')){
    		//Borrar la variable de session
    		$this->session->unset_userdata('updatedEntrada');

    		//Establecer el mensaje a mostrar
    		$data["mensaje"]["clase"] = "alert-success";
    		$data["mensaje"]["contenido"] = "Se ha actualizado la Entrada satisfactoriamente.";
    	}




        $data["estados"]    = Entradas_cat_orm::estados()
                            ->orderBy("id_cat", "ASC")
                            ->get();

        $data["bodegas"]    = Bodegas_orm::deEmpresa($this->id_empresa)
                            ->activas()
                            ->transaccionales($this->id_empresa)
                            ->orderBy("nombre", "ASC")
                            ->get();

        $data["proveedores"]    = Proveedores_orm::deEmpresa($this->id_empresa)
                                ->activos()
                                ->orderBy("nombre", "ASC")
                                ->get();

        //LLenando el catalogo de origenes
        $origenes   = array();
        foreach ($data["proveedores"] as $proveedor)
        {
            $origenes[] = array(
                "uuid"      => $proveedor->uuid_proveedor,
                "nombre"    => $proveedor->nombre
            );
        }

        foreach ($data["bodegas"] as $bodega)
        {
            $origenes[] = array(
                "uuid"      => $bodega->uuid_bodega,
                "nombre"    => $bodega->nombre
            );
        }

        $data["origenes"]   = $origenes;
    	//Breadcrum Array
        $breadcrumb = array(
            "titulo"    => '<i class="fa fa-cubes"></i> Inventario: Entradas',
            "ruta" => array(
                0 => array(
                    "nombre" => "Inventarios",
                    "activo" => false
                ),
                1 => array(
                    "nombre" => '<b>Entradas</b>',
                    "activo" => true
                )
            ),
            "filtro"    => false,//sin vista grid
            "menu"      => array()
        );


        //Verificar si tiene permiso de Exportar
        if (1 or $this->auth->has_permission('listar__exportar', 'entradas/listar')){
            $breadcrumb["menu"]["opciones"]["#exportarBtn"] = "Exportar";
        }

        //Agregra variables PHP como variables JS
        $this->assets->agregar_var_js(array(
            "flexio_mensaje" => Flexio\Library\Toast::getStoreFlashdata()
        ));

        unset($data["mensaje"]);

    	$this->template->agregar_titulo_header('Listado de Entradas de Inventario');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar($breadcrumb);

    }

    function crearsubpanel()
    {


        $this->assets->agregar_js(array(
            'public/assets/js/modules/entradas/formulario.js'
        ));

        $this->template->vista_parcial(array(
            'entradas',
            'crear'
        ));
    }

    public function editarsubpanel($uuid = NULL)
    {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/entradas/formulario.js'
        ));


        $this->template->vista_parcial(array(
            'entradas',
            'editar'
        ));
    }



    public function ajax_listar()
    {
        if(!$this->input->is_ajax_request()){
            exit;
        }

        $clause                 = $this->input->post();
        $clause["empresa_id"]   = $this->empresa_id;

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = $this->entradasRep->count($clause);
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $entradas = $this->entradasRep->get($clause, $sidx, $sord, $limit, $start);

        $response           = new stdClass();
        $response->page     = $page;
        $response->total    = $total_pages;
        $response->records  = $count;


        if($count){
            foreach($entradas as $i => $row){
                $response->rows[$i]["id"]   = $row->uuid_entrada;
                $response->rows[$i]["cell"] = $this->entradasRep->getColletionCell($row, $this->auth);
            }
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))->_display();
        exit;
    }

    public function ajax_listar_historial_item()
    {
        if(!$this->input->is_ajax_request()){
            exit;
        }

        $clause                 = $this->input->post();
        $clause["empresa_id"]   = $this->empresa_id;

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = $this->entradasRep->count($clause);
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $entradas = $this->entradasRep->get($clause, $sidx, $sord, $limit, $start);

        //dd($entradas->toArray());

        $response           = new stdClass();
        $response->page     = $page;
        $response->total    = $total_pages;
        $response->records  = $count;


        if($count){
            foreach($entradas as $i => $row){
                $response->rows[$i]["id"]   = $row->uuid_entrada;
                $response->rows[$i]["cell"] = $this->entradasRep->getColletionCellHistorialItem($row, $this->auth, $clause);
            }
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))->_display();
        exit;
    }

    public function ajax_get_origenes()
    {

    	if($this->input->is_ajax_request())
        {
            $bodegas        = Bodegas_orm::deEmpresa($this->id_empresa)
                            ->activas()
                            ->transaccionales($this->id_empresa)
                            ->orderBy("nombre", "ASC")
                            ->get();

            $proveedores    = Proveedores_orm::deEmpresa($this->id_empresa)
                            ->activos()
                            ->orderBy("nombre", "ASC")
                            ->get();

            //LLenando el catalogo de origenes
            $origenes   = array();
            foreach ($proveedores as $proveedor)
            {
                $origenes[] = array(
                    "uuid"      => $proveedor->uuid_proveedor,
                    "nombre"    => $proveedor->nombre
                );
            }

            foreach ($bodegas as $bodega)
            {
                $origenes[] = array(
                    "uuid"      => $bodega->uuid_bodega,
                    "nombre"    => $bodega->nombre
                );
            }




            $response               = array();
            $response["success"]    = false;

            if(count($origenes))
            {
                $response["success"]    = true;
                $response["registros"]  = $origenes;
            }

            echo json_encode($response);
            exit();
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


    public function ocultotabla($sp_string_var = '')
    {

        //If ajax request
    	$this->assets->agregar_js(array(
    		'public/assets/js/modules/entradas/tabla.js'
    	));

        $sp_array_var = explode('=', $sp_string_var);
        if (count($sp_array_var) == 2) {

            $this->assets->agregar_var_js(array(
                $sp_array_var[0] => $sp_array_var[1]
            ));

        }

    	$this->load->view('tabla');

    }

    public function ocultotablaV2($sp_string_var = '')
    {

        //If ajax request
    	$this->assets->agregar_js(array(
    		'public/assets/js/modules/entradas/tabla_item.js'
    	));

        if(is_array($sp_string_var))
        {
            $this->assets->agregar_var_js([
                "campo" => collect($sp_string_var)
            ]);

        }else{
            $sp_array_var = explode('=', $sp_string_var);
            if (count($sp_array_var) == 2) {

                $this->assets->agregar_var_js(array(
                    $sp_array_var[0] => $sp_array_var[1]
                ));

            }
        }
    	$this->load->view('tabla');

    }

    public function ocultotablaFacturasCompras($factura_compra_id=null){
        $this->assets->agregar_js(array(
            'public/assets/js/modules/entradas/tabla.js'
        ));

        if (!empty($factura_compra_id)) {
            $this->assets->agregar_var_js(array(
                "factura_compra_id" => $factura_compra_id
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

    //  ini_set('memory_limit','256M');
    	$this->assets->agregar_js(array(
            'public/assets/js/modules/entradas/formulario.js'
    	));


      //$arreglo_de_items = "ids de los items de la operacion relacionadada a la o/c";

        $entrada = Flexio\Modulo\Entradas\Models\Entradas::where("uuid_entrada",hex2bin($data["uuid_orden"]))->first();
        $arreglo_de_items = $entrada->operacion->items->pluck("id");
        //catalogos
        $data["bodegas"]    = $this->bodegasRep->get(["empresa_id" => $this->empresa_id, "transaccionales" => true]);
        $data["estados"]    = $this->entradasCatRep->get(["valor" => "estado"]);
        $data["items"]      = $this->itemsRep->get(["empresa_id" => $this->empresa_id,"item_ids" => $arreglo_de_items]);

        $this->load->view('formulario', $data);
    }


    private function _addMainCss()
    {
        //main
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
    }

    private function _addMainJS()
    {
        $this->assets->agregar_js(array(
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/plugins/bootstrap/bootstrap-tabdrop.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/combodate/momentjs.js',//required by datetimepicker - moment.js
            'public/assets/js/moment-with-locales-290.js',//required by datetimepicker - moment.locale.js
            'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
            'public/assets/js/default/formulario.js',
        ));
    }

    public function guardar()
    {

        if(!empty($_POST))
    	{
            $response = false;
            $toast = new Flexio\Library\Toast();
            try {
                Capsule::transaction(
                    function() use (&$response)
                    {
                        $post       = $this->input->post();
                        $registro   = $this->entradasRep->find($post["campo"]["entrada_id"]);

          //Para parciales
          foreach ($post["items"] as $item)
          {

            $cantidad_recibida_aux = 0;
            if(isset($item["seriales"]))
            {
                foreach ($item["seriales"] as $serial)
                {
                    $cantidad_recibida_aux += (!empty($serial)) ? 1 : 0;
                }
                $cantidad_recibida = $cantidad_recibida_aux;
            }
              if($cantidad_recibida < $item["cantidad_esperada"]){
                $completo = 0;
              }else{
                $completo = 1;
              }
          }

          if($completo == 0){
            echo "esto es parcial";
            $facturas_count = count($registro->operacion->facturas);
            if($facturas_count == 0){
               $tipo = "sin_factura_activo";
            }else{
               $tipo = "facturado_activo";
            }
        		$empresa = [
        				'empresa_id' => $this->empresa_id,
        				'tipo'=> $tipo
        		];



            if($item["cantidad_esperada"] != $cantidad_recibida){

                $tipo = "facturado_activo";
                $empresa = [
        				'empresa_id' => $this->empresa_id,
        				'tipo'=> $tipo
        		];
                if(!empty($this->cuenta_inventario->tieneCuenta($empresa))) {
              $cuenta = $this->cuenta_inventario->getAll($empresa);
              $cuenta = $cuenta[0]['cuenta_id'];
        		}

                $cantidad_restante = $item["cantidad_esperada"] - $cantidad_recibida;
                echo "vamo a entrar1";

            }else{
                if(!empty($this->cuenta_inventario->tieneCuenta($empresa))) {
              $cuenta = $this->cuenta_inventario->getAll($empresa);
              $cuenta = $cuenta[0]['cuenta_id'];
        		}
                $cantidad_restante = $item["cantidad_esperada"] - $cantidad_recibida;
            }

              echo "Entrando completo";



            $this->transaccionFactura->hacerTransaccionParcial($registro, $cuenta, $cantidad_recibida, $cantidad_restante);
           /* $cantidad_restante = 1;

            $this->transaccionFactura->hacerTransaccion($registro, $cantidad_restante); */

          }elseif($completo == 1){
            $facturas_count = count($registro->operacion->facturas);
            if($facturas_count == 0){
               $tipo = "sin_factura_pasivo";
            }else{
               $tipo = "facturado_activo";
            }
        		$empresa = [
        				'empresa_id' => $this->empresa_id,
        				'tipo'=> $tipo
        		];

            if($item["cantidad_esperada"] == $cantidad_recibida && $facturas_count == 0){

               $cantidad_restante = $cantidad_recibida - $item["cantidad_recibida"];
               $tipo = "sin_factura_activo";
               $empresa = [
        				'empresa_id' => $this->empresa_id,
        				'tipo'=> $tipo
        		];
               if(!empty($this->cuenta_inventario->tieneCuenta($empresa))) {
              $cuenta = $this->cuenta_inventario->getAll($empresa);
              $cuenta = $cuenta[0]['cuenta_id'];
        		}

            }else{
              if(!empty($this->cuenta_inventario->tieneCuenta($empresa))) {
              $cuenta = $this->cuenta_inventario->getAll($empresa);
              $cuenta = $cuenta[0]['cuenta_id'];
        		}
               $cantidad_restante = $cantidad_recibida - $item["cantidad_recibida"];
            }

           $this->transaccionFactura->hacerTransaccionParcial($registro, $cuenta, $cantidad_recibida, $cantidad_restante);

          }else{
            $this->transaccionFactura->hacerTransaccion($registro);
          }

                        $response = $this->entradasRep->save($registro, $post);

                    }
                );
            } catch (Exception $e) {
                $toast->setUrl('entradas/listar')->run("exception",[$e->getMessage()]);
            }

            $response ? $toast->run("success", "ENT{$registro->codigo}") : $toast->run("error");
            redirect(base_url('entradas/listar'));

    	}
    }



    public function editar($uuid=NULL, $data = [])
    {
        //Cargo el registro
        $entrada = $this->entradasRep->findByUuid($uuid);
        $entrada->load('comentario_timeline');
        //obtengo los CSS y JavaScript necesarios para la vista
        $this->_addMainCSS();
        $this->_addMainJS();
        $this->assets->agregar_js(array(
            'public/assets/js/plugins/jquery/combodate/combodate.js',
            'public/assets/js/default/tabla-dinamica.jquery.js',
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/resources/compile/modulos/entradas/comentario-entradas.js'
    	));
    	//dd($entrada->comentario_timeline);
        //Uso este valor para ejecutar cierta logica en la vista porque
        //el catelogo de origenes se arma una ves de carga el formulario
        $this->assets->agregar_var_js(array(
            "uuid_origen"   => $entrada->uuid_origen,
            "entrada_id"    => $entrada->id,
            "coment_entrada" => (isset($entrada->comentario_timeline)) ? $entrada->comentario_timeline : ""
        ));

        $breadcrumb = array(
            "titulo" => '<i class="fa fa-cubes"></i> Inventario: Entrada '.$this->prefijo.$entrada->codigo
    	);

        $data["campos"]["campos"]           = $this->entradasRep->getColletionCampos($entrada);
        $data["campos"]["campos"]["items"]  = $this->entradasRep->getColletionCamposItems($entrada->items);
        $data["campos"]['uuid_orden'] = $uuid;
        $this->template->agregar_titulo_header('Entradas');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar();
    }


    public static function realizarAjuste($item, $bodega, $cantidad, $tipo_ajuste = 1)
    {
        $entradas_items = Entradas_items_orm::withExistencia($item, $bodega);
        $j
                = 0;
        foreach ($entradas_items->get() as $entrada_item)
        {
            $operacion          = $entrada_item->operacion;
            $items_unidades     = $operacion->unidadReferencia->item_unidades->toArray();
            $factor_conversion  = 1;

            //DETERMINO EL FACTOR DE CONVERSION QUE SE APLICARA AL
            //ELEMENTO DE LA ENTRADA
            foreach($items_unidades as $item_unidad)
            {
                if($item_unidad["id_item"] == $operacion->id_item and $item_unidad["id_unidad"] == $operacion->unidad)
                {
                    $factor_conversion = $item_unidad["factor_conversion"];
                }
            }


            if(($cantidad_ajuste/$factor_conversion) > 0 and $tipo_ajuste == 1)//negativo
            {
                if($entrada_item->cantidad_saliente < $entrada_item->cantidad_recibida)
                {
                    //la cantidad disponible es la que puedo tomar
                    //para realizar el ajuste
                    $cantidad_disponible    = $entrada_item->cantidad_recibida - $entrada_item->cantidad_saliente;
                    $aux                    = ($cantidad_ajuste/$factor_conversion) - $cantidad_disponible;
                    if($cantidad_disponible > 0)
                    {

                        if($aux >= 0)
                        {
                            $entrada_item->cantidad_saliente+= $cantidad_disponible;
                            $entrada_item->save();

                            $cantidad_ajuste = $aux;
                        }
                        else
                        {
                            if($j==0)
                            {
                                $entrada_item->cantidad_saliente+= $cantidad_ajuste/$factor_conversion;
                            }
                            else
                            {
                                $entrada_item->cantidad_saliente+= $cantidad_ajuste;
                            }

                            $entrada_item->save();

                            $cantidad_ajuste = 0;
                        }
                    }
                }
            }
            elseif($cantidad_ajuste > 0)
            {
                if($j==0)
                {
                    $entrada_item->cantidad_recibida+= $cantidad_ajuste/$factor_conversion;
                    $entrada_item->save();
                }
            }
            $j += 1;
        }
    }

    public static function comp__crearEntrada($entrada)
    {
        $CI         = get_instance();
        $Controller = $CI->entradas;
        $registro   = Entradas_orm::deTipo($entrada["type"])->deTipoId($entrada["id"])->first();

        if(!count($registro))
        {
            $numero                     = Entradas_orm::deEmpresa($Controller->id_empresa)->count();

            $registro                   = new Entradas_orm;
            $registro->uuid_entrada     = Capsule::raw("ORDER_UUID(uuid())");
            $registro->codigo           = $numero + 1;
            $registro->empresa_id       = $Controller->id_empresa;
            $registro->operacion_id     = $entrada["id"];
            $registro->operacion_type   = $entrada["type"];
            $registro->comentarios      = "";
        }

        $registro->estado_id    = $entrada["estado_id"];
        $registro->save();

        //Verifico si no tiene registros para mostrar para proceder a borrar
        if(count($registro->comp__entradasItemsModel()) == 0)
        {
            $registro->delete();
        }
    }
}

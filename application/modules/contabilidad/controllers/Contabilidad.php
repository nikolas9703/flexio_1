<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
*
* @package    Erp
* @subpackage Controller
* @category   Contabilidad
* @author     Pensanomica Team
* @link       http://www.pensanomca.com
* @copyright  10/22/2015
*/

use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Contabilidad\Repository\ListarCuentas;
use Flexio\Modulo\Contabilidad\Models\Cuentas as Cuenta;
use Flexio\Modulo\CentrosContables\Models\CentrosContables as CentrosContables;
//use Carbon\Carbon;
@include_once ('HistorialCuenta.php'); //similacion de trait porque CI no permite hacerlo de otra manera
class Contabilidad extends CRM_Controller
{
    use HistorialCuenta;
    protected $empresa_id;
    protected $listar_cuentas;
    protected $impuestoFormRequest;

    public function __construct() {

        parent::__construct();

        $this->load->model('movimiento_monetario/Movimiento_monetario_orm');
        $this->load->model('abonos/Abonos_orm');
        $this->load->model('clientes_abonos/Clientes_abonos_orm');
        $this->load->model('cuentas_orm');
        $this->load->model('tipo_cuentas_orm');
        $this->load->model('Centros_orm');
        $this->load->model('Impuestos_orm');
        $this->load->model('usuarios/empresa_orm');
        $this->load->model('entrada_manual/Transaccion_orm');
        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();

        $uuid_empresa       = $this->session->userdata('uuid_empresa');
        $this->empresa_id   = Empresa_orm::findByUuid($uuid_empresa)->id;
    }


    //lista el plan contable
    public function listar() {
        $data=array();
        $this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/plugins/jquery/jstree/default/style.min.css',
            'public/assets/css/modules/stylesheets/contabilidad.css',

        ));
        $this->assets->agregar_js(array(
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/default/lodash.min.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/plugins/jquery/jquery.progresstimer.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/jstree.min.js',
            'public/assets/js/modules/contabilidad/routes.js',
            'public/assets/js/modules/contabilidad/listar.js',
            'public/assets/js/modules/contabilidad/crear_cuenta.js',
        ));

        //Breadcrum Array

        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $total_cuenta = $empresa->cuentas->count();
        $opciones = array('entrada_manual/crear'=> 'Registrar Entrada Manual');
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-calculator"></i> Plan Contable',
            "filtro" => false,
            "menu" => array(
                "nombre" => $total_cuenta==0? "Crear Plan Contable" :"Crear",
                "url"	 => 'javascript:',
                "clase" => $total_cuenta==0?"opcion-crear-plan":"opcion-agregar-cuenta",
                "opciones" => $opciones
            )
        );
        $data['impuestos'] = Impuestos_orm::impuesto_select(array('empresa_id'=>$empresa->id,'estado'=>'Activo'));
        $this->template->agregar_titulo_header('Plan Contable');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }

    public function ajax_listar() {
        //Just Allow ajax request

        if(!$this->input->is_ajax_request()){
            return false;
        }
        $this->listar_cuentas = new ListarCuentas;
        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $tipo = $this->input->post('tipo');
        $nombre = (string)$this->input->post('nombre');
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        //fix count
        $count = Cuentas_orm::where('empresa_id',$empresa->id)->count();
        //dd($count);
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

        $clause = array('empresa_id' => $empresa->id, 'padre_id' => $this->input->post('nodeid'));
        if(!empty($tipo)) $clause['tipo_cuenta_id'] = $tipo;
        if(!empty($nombre)){
      		$clause["nombre"] = array('LIKE', "%$nombre%");
      	}

        $cuentas = $this->listar_cuentas->listar_cuentas($clause, $nombre ,$sidx, $sord, $limit, $start);

        //dd($cuentas);
        //Constructing a JSON
        $response = new stdClass();
        $response->page     = $page;
        $response->total    = $total_pages;
        $response->record  = $count;
        $i=0;

        if(!empty($cuentas)){
            foreach ($cuentas as  $row){

                $tituloBoton = ($row['estado']!=1)?'Habilitar':'Deshabilitar';
                $estado = ($row['estado']==1)?0:1;
                $hidden_options = "";
                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row['id'] .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $hidden_options = '<a href="'.base_url('contabilidad/historial_transacciones/'.$row['uuid_cuenta']).'" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success Nofunciona">Ver Historial</a>';
                $hidden_options .= '<a href="'.base_url('entrada_manual/crear').'" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success">Registrar Entrada Manual</a>';
                $hidden_options .= '<a href="javascript:" data-id="'. $row['id'] .'" data-estado="'.$estado.'" class="btn btn-block btn-outline btn-success cambiarEstadoCuentaBtn">'.$tituloBoton.' Cuenta</a>';
                $hidden_options .= '<a href="javascript:" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success editarCuentaBtn">Editar Cuenta</a>';
                $level = substr_count($row['codigo'],".");
                $response->rows[$i] = array("id" => $row['id'], 'cell' => array(
                    'id' => $row['id'],
                    'codigo'=> $row['codigo'],
                    'cuenta' => $row['nombre'],
                    'tipo' =>	Cuentas_orm::tipo($row['id']),
                    'detalle' => $row['detalle'],
                    'balance' =>$row['balance'],
                    'estado' =>($row['estado']==1)?'Habilitado':'Deshabilitado',
                    'opciones' =>$link_option,
                    'link' => $hidden_options,
                    'level' =>(integer)$level-1, //level
                    'parent' => $row["padre_id"]==0? "NULL": (string)$row["padre_id"], //parent
                    'isLeaf' =>(Cuentas_orm::is_parent($row['id']) == true)? false: true, //isLeaf
                    'expanded' =>  false, //expended
                    'loaded' => false, //loaded
                ));
                $i++;
            }
        }

        echo json_encode($response);
        exit;
    }

    /**
    * Cargar Vista Parcial de Tabla
    *
    * @return void
    */
    public function ocultotabla() {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/contabilidad/tabla.js'
        ));

        $this->load->view('tabla');
    }

    /**
    * carga el plan contable
    */

    public function ajax_cargar_plan_contable() {

        if(!$this->input->is_ajax_request()){
            return false;
        }

        $filepath = realpath('./application/modules/contabilidad/plan_contable.sql');
        if(!file_exists($filepath)){
            //No se encontro el archivo ...
            log_message("error", "MODULO: contabilidad --> No se encontro el archivo");
            return false;
        }
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $sql = read_file($filepath);
        $sql = str_replace("__EMP01__", $empresa->id, $sql);
        Capsule::beginTransaction();
        try{
            $result = 	Capsule::unprepared($sql);
        }catch(Exception $e){
            $result = array('status'=>500, 'error' => 'duplicidad de data');
            Capsule::rollback();
            http_response_code(406);
        }
        Capsule::commit();
        echo json_encode($result);
        exit;
    }

    public function ajax_listar_cuentas() {
        if(!$this->input->is_ajax_request()){
            return false;
        }

        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $tipo = $this->input->post('tipo');
        $clause= array('empresa_id' => $empresa->id);
        if(!empty($tipo)) $clause['tipo_cuenta_id'] = $tipo;

        $cuentas = Cuentas_orm::listar_cuentas($clause);
        //Constructing a JSON
        $response = new stdClass();
        $response->plugins = [ "contextmenu" ];
        $response->core->check_callback[0] = true;

        $i = 0;
        if(!empty($cuentas)){
            foreach ($cuentas as  $row){
                $response->core->data[$i] = array(
                    'id' => (string)$row['id'],
                    'parent'=> $row["padre_id"]==0? "#": (string)$row["padre_id"],
                    'text' => $row["nombre"],
                    'icon' => 'fa fa-folder',
                    'codigo' => $row["codigo"]
                    //'state' =>array('opened' => true)
                );

                $i++;
            }

        }

        echo json_encode($response);
        exit;

    }
    //codigo para la creacion de cuenta
    public function ajax_codigo() {
        if(!$this->input->is_ajax_request()){
            return false;
        }

        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $padre_id = $this->input->post('node');
        $clause= array('empresa_id' => $empresa->id,'padre_id' => $padre_id);
        $cuentas = Cuentas_orm::where($clause)->orderBy('id', 'desc')->first();
        $codigo = Cuentas_orm::codigo($cuentas->codigo);
        $response = new stdClass();
        $response->codigo = $codigo;
        echo json_encode($response);
        exit;

    }
    //guarda la cuenta del plan contable
    function ajax_guardarCuenta() {
        if(!$this->input->is_ajax_request()){
            return false;
        }
        $response = new stdClass();
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $campos = array();
        $id = $this->input->post('id');
        $nombre = $this->input->post('nombre');
        $codigo = $this->input->post('codigo');
        $descripcion = $this->input->post('descripcion');
        $padre_id = $this->input->post('padre_id');
        $cuenta = Cuentas_orm::find($padre_id);
        $tipo_cuenta_id = $cuenta->tipo_cuenta_id;
        $impuesto = $this->input->post('impuesto');
        Capsule::beginTransaction();
        $campos['codigo']= $codigo;
        $campos['nombre']= $nombre;
        $campos['detalle']= $descripcion;
        $campos['padre_id']= $padre_id;
        $campos['tipo_cuenta_id']= $tipo_cuenta_id;
        $campos['empresa_id']= $empresa->id;
        //$campos['impuesto_id']= $impuesto;
        try {
            if(!isset($id))
            {
                $nuevo = Cuentas_orm::create($campos);
                $response->mensaje = '<b>¡&Eacute;xito!</b> Se ha guardado correctamente '. $nuevo->nombre;
                $response->estado = 200;
                $ids_centros = $empresa->centros_contables->map(function($item){
                    return $item->id;
                });
                if(!empty($ids_centros->toArray())){
                    $nuevo->centros_contables()->attach($ids_centros->toArray(),array('empresa_id'=>$empresa->id));
                }

            }elseif(isset($id)){
                $cuenta_data = Cuenta::find($id);
                ///$nuevo = Cuentas_orm::find($id);
                //$nuevo->nombre = $nombre;
                $cuenta_data->nombre = $nombre;
                //$nuevo->impuesto_id = $impuesto;
                if(isset($descripcion)){
                   // $nuevo->detalle = $descripcion;
                    $cuenta_data->detalle = $descripcion;
                }
                $cuenta_data->save();
               // $nuevo->save();
               //
               $response->estado = 200;
                $response->mensaje = '<b>¡&Eacute;xito!</b> Se ha actualizado correctamente '. $cuenta_data->nombre;
            }
            Capsule::commit();
        }catch(Exception $e){
            $response->mensaje = $e;
            $response->estado = 500;
            $response->mensaje = '<b>¡Error!</b> Su solicitud no fue procesada';
            log_message('error',$e);
            Capsule::rollback();
        }


        /*if($cuenta_data){
            $response->estado = 200;

        }else{
            $response->estado = 500;
            $response->mensaje = '<b>¡Error!</b> Su solicitud no fue procesada';
        }*/

        echo json_encode($response);
        exit;

    }

    // lista centros contable

    function listar_centros_contables() {

        $data = array();
        $this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/plugins/jquery/jstree/default/style.min.css',
            //'public/assets/css/modules/stylesheets/contabilidad.css',

        ));
        $this->assets->agregar_js(array(
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/default/lodash.min.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/plugins/jquery/jquery.progresstimer.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/jstree.min.js',
            'public/assets/js/modules/contabilidad/routes.js',

        ));
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-calculator"></i> Centro Contable',
            "filtro" => false,
            "menu" => array(
                "nombre" => "Crear",
                "url"	 => 'javascript:',
                "clase" => "open-modal-centro_contable",
                "opciones" => array()
            )
        );
        $this->template->agregar_titulo_header('Centro Contable');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);

    }

    function ocultotablacentro() {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/contabilidad/tabla_centro.js'
        ));

        $this->load->view('tabla_centro');
    }

    function ajax_listar_centros_contable() {
        if(!$this->input->is_ajax_request()) {
            return false;
        }

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        //empresa en session
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $clause= array('empresa_id' => $empresa->id);

        $nombre = $this->input->post('nombre');
        $descripcion = $this->input->post('descripcion');
        $estado = $this->input->post('estado');
        if(!empty($nombre) ) $clause['nombre'] = $nombre;
        if(!empty($descripcion)) $clause['descripcion'] = $descripcion;
        if(!empty($estado)) $clause['estado'] = $estado;

        //dd(get_class_methods(new Centros_orm));
        $centros = Centros_orm::listar($clause);
        $count = count($centros);
        //Constructing a JSON
        $response = new stdClass();
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $response->page     = $page;
        $response->total    = $total_pages;
        $response->record  = $count;

        $i = 0;

        if(!empty($centros)){
            foreach ($centros as  $row){

                $hidden_options = "";
                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row['id'] .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $hidden_options = '<a href="javascript:" data-uuid="'. $row['uuid_centro'] .'" class="btn btn-block btn-outline btn-success editarCentroBtn">Editar Centro Contable</a>';
                $tituloBoton = ($row['estado']!='Activo')?'Activar':'Inactivar';
                $estado_valor = $row['estado']=='Activo'?'Inactivo':'Activo';
                $hidden_options .= '<a href="javascript:" data-uuid="'. $row['uuid_centro'] .'" data-estado="'. $estado_valor .'" class="btn btn-block btn-outline btn-success estadoCentroBtn">'.$tituloBoton.' Centro Contable</a>';
                $response->rows[$i] = array("id" => $row['id'], 'cell' => array(
                    'id' => (string)$row['id'],
                    'nombre' => $row['nombre'],
                    'descripcion' => $row['descripcion'],
                    'estado' =>$row['estado'],
                    'opciones' =>$link_option,
                    'link' => $hidden_options,
                    'level' =>$row["padre_id"]==0? 0 : 1, //level
                    'parent' => $row["padre_id"]==0? "NULL": (string)$row["padre_id"], //parent
                    //'isLeaf' =>($row["padre_id"]==0)? false: true, //isLeaf
                    'isLeaf' =>$row["hijos"], //isLeaf
                    'expanded' =>  false, //expended
                    'loaded' => true, //loaded
                ) );
                $i++;
            }

        }

        echo json_encode($response);
        exit;

    }


    public function ocultoformulario() {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/contabilidad/crear_centro_contable.js'
        ));

        $this->load->view('modal_formulario_crear_centro');
    }

    public function ajax_guardarCentro() {
        if(!$this->input->is_ajax_request()){
            return false;
        }
        $response = array();
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $fieldset = Util::set_fieldset("campo");
        if(!isset($fieldset['padre_id']))$fieldset['padre_id']=0;

        Capsule::beginTransaction();
        try{
            if(!isset($fieldset['id'])){
                $fieldset['uuid_centro'] = Capsule::raw("ORDER_UUID(uuid())");
                $fieldset['empresa_id'] = $empresa->id;

                $centro = Centros_orm::create($fieldset);
                if($centro){
                    $cuentas = Cuentas_orm::where('empresa_id',$empresa->id)->lists('id');

                    $centro->cuentas_contables()->attach($cuentas->toArray(),array('empresa_id'=>$empresa->id));
                    $response = array('estado'=>200, 'mensaje'=>'<b>¡&Eacute;xito!</b> Se ha guardado correctamente el '.$centro->nombre);
                }
            }else{
                $centro = CentrosContables::find($fieldset['id']);
               // $centro = Centros_orm::find($fieldset['id']);
                $centro->nombre = $fieldset['nombre'];
                $centro->padre_id = $fieldset['padre_id'];
                if(isset($fieldset['descripcion']))$centro->descripcion = $fieldset['descripcion'];
                $centro->save();
                $response = array('estado'=>200, 'mensaje'=>'<b>¡&Eacute;xito!</b> Se ha actualizado '.$centro->nombre);
            }


        }catch(Exception $e){
            log_message('error', $e);
            Capsule::rollback();
            $response = array('estado'=>500, 'mensaje'=>'<b>¡Error! Su solicitud no fue procesada</b> ');
        }

        Capsule::commit();
        echo json_encode($response);
        exit;

    }

    function ajax_buscar_centro() {
        if(!$this->input->is_ajax_request()){
            return false;
        }
        $uuid_centro = $this->input->post('uuid_centro');
        $centro = Centros_orm::findByUuid($uuid_centro);
        $response = array();


        $response['id'] = $centro->id;
        $response['uuid'] = $centro->uuid_centro;
        $response['nombre'] =$centro->nombre;
        $response['descripcion'] = $centro->descripcion;
        $response['padre_id'] = $centro->padre_id;

        echo json_encode($response);
        exit;

    }

    function ajax_cambiar_estado_centro_contable() {
        if(!$this->input->is_ajax_request()){
            return false;
        }
        $response=array();
        $estado = $this->input->post('estado');
        $uuid_centro = $this->input->post('uuid_centro');
        $centro = Centros_orm::findByUuid($uuid_centro);
        $response = $centro->cambiarEstado($centro, $estado);
        echo json_encode($response);
        exit;


    }

    function ajax_buscar_cuenta() {
        if(!$this->input->is_ajax_request()){
            return false;
        }
        $id = $this->input->post('id');
        $cuenta = Cuentas_orm::find($id);
        $response = array();


        $response['id'] = $cuenta->id;
        $response['codigo'] = $cuenta->codigo;
        $response['nombre'] = $cuenta->nombre;
        $response['detalle'] =$cuenta->detalle;
        $response['padre_id'] = $cuenta->padre_id;
        //$response['impuesto_id'] = $cuenta->impuesto->pluck('id');

        echo json_encode($response);
        exit;

    }

    function ajax_cambiar_estado_cuenta_contable() {
        if(!$this->input->is_ajax_request()){
            return false;
        }

        $response=array();
        $estado = $this->input->post('estado');
        $id = $this->input->post('id');


        $total =  Cuentas_orm::cambiar_estado($id,$estado);

        if($total > 0){
            $response= array('estado'=>200,'mensaje' => '<b>¡&Eacute;xito!</b> La actualizaci&oacute;n de estado');
        }else{
            $response= array('estado'=>500,'mensaje' => '<b>¡Error!</b> Su solicitud no fue Procesada');
        }
        echo json_encode($response);
        exit;
    }

    function configuracion() {
        $data = array();
        $this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',

        ));
        $this->assets->agregar_js(array(
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/default/lodash.min.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/plugins/jquery/jquery.progresstimer.min.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/modules/contabilidad/routes.js',
            'public/assets/js/modules/contabilidad/impuesto_controller.js',
            'public/assets/js/default/formulario.js',
            //'public/assets/js/modules/contabilidad/lista_impuesto.js',
        ));

        //Breadcrum Array
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $condicion = array('empresa_id'=>$empresa->id, 'tipo_cuenta_id'=>2);
        // $cuentas_pasivo =
        $ids_pasivo = Cuentas_orm::where($condicion)->lists('padre_id');

        $cuentas = Cuentas_orm::whereNotIn('id', $ids_pasivo->toArray())->where(function($query) use($condicion){
            $query->where($condicion);
        })->get(array('id','nombre','codigo'));

        $data['pasivos'] = $cuentas->toArray();
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-calculator"></i> Contabilidad: Configuraci&oacute;n',
            "filtro" => false,
            "menu" => array(
            )
        );
        $this->template->agregar_titulo_header('Contabilidad Configuracion');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }

    function ocultotablaimpuesto() {

        $this->load->view('tabla_impuesto');
    }

    function  ajax_listar_impuestos() {

        if(!$this->input->is_ajax_request()){
            return false;
        }
        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        //empresa en session
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $clause= array('empresa_id' => $empresa->id);

        $impuestos = Impuestos_orm::listar_grid($clause);

        $count = $impuestos->count();
        $impuestos->load('cuenta');
        //Constructing a JSON
        $response = new stdClass();
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $response->page     = $page;
        $response->total    = $total_pages;
        $response->records  = $count;
        $i=0;
        if(!empty($impuestos)){

            foreach($impuestos as $row){
                $hidden_options = "";
                $boton_estado = ($row->estado == 'Activo'? 'Inactivar':'Activar' );
                $opcion_estado = ($row->estado == 'Activo'? 'Inactivo':'Activo' );
                $color_label = ($row->estado == 'Activo')? 'label-info':'label-dark' ;
                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row->id .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $hidden_options = '<a href="javascript:" data-uuid="'. $row->uuid_impuesto .'" class="btn btn-block btn-outline btn-success editarImpuestoBtn">Editar Impuesto</a>';
                $hidden_options .= '<a href="javascript:" data-uuid="'. $row->uuid_impuesto .'" data-estado="'.$opcion_estado.'" class="btn btn-block btn-outline btn-success cambiarEstadoImpuestoBtn"> '.$boton_estado.' Impuesto</a>';
                //$response->rows[$i]["id"] = $row['id'];
                //$response->rows[$i]["cell"] = array(
                $response->rows[$i] = [
                    'id_impuesto' => $row->id,
                    'nombre' =>	$row->nombre,
                    'descripcion' => $row->descripcion,
                    'impuesto' => $row->impuesto,
                    'nombre_cuenta' => $row->cuenta->nombre,
                    'estado' =>	'<label class="label '.$color_label.'">'.$row->estado.'</label>',
                    'opciones' =>	$link_option,
                    'link' =>	$hidden_options,
                    'retiene_impuesto' => $row->retiene_impuesto,
                    'porcentaje_retenido' => $row->porcentaje_retenido,
                    'cuenta_retenida_id'=> $row->cuenta_retenida_id
                ];
                $i++;
            }
        }

        echo json_encode($response);
        exit;
    }

    function ajax_guardar_impuesto() {
        if(!$this->input->is_ajax_request()){
            return false;
        }
        $response = new stdClass();
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $datos = ['empresa_id'=>$empresa->id];
        $this->impuestoFormRequest = new Flexio\Modulo\ConfiguracionContabilidad\FormRequest\CrearImpuestoFormRequest();
        try{
            $impuesto = $this->impuestoFormRequest->guardar($datos);
        }catch(\Exception $e){
            $impuesto= null;
            $response->estado = 500;
            $response->mensaje = '<b>¡&Error!</b> Se ha guardado correctamente producido un error ';
        }

        if(!is_null($impuesto)){
            $response->estado = 200;
            $response->mensaje = '<b>¡&Eacute;xito!</b> Se ha guardado correctamente  '.$impuesto->nombre;
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($response))->_display();
		exit();
    }

    function ajax_cambiar_estado_impuesto() {
        if(!$this->input->is_ajax_request()){
            return false;
        }
        $response = new stdClass();
        $uuid = $this->input->post('uuid_impuesto');
        $estado = $this->input->post('estado');

        $impuesto = Impuestos_orm::findByUuid($uuid);
        $impuesto->estado = $estado;
        if($impuesto->save()){
            $response->estado = 200;
            $response->mensaje ='<b>¡&Eacute;xito!</b> La actualizaci&oacute;n de estado';
        }else{
            $response->estado = 500;
            $response->mensaje ='<b>¡Error!</b> Su solicitud no fue Procesada';

        }

        echo json_encode($response);
        exit;

    }

    function ajax_lista_centros_contables() {

        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $clause = array('empresa_id'=>$empresa->id,'estado'=>'Activo','padre_id'=>0);
        $centros = Centros_orm::where($clause)->get(array('id', 'nombre'));

        echo json_encode($centros);
        exit;
    }

    function ajax_get_impuesto_exonerado() {
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $clause = array('empresa_id'=>$empresa->id,'estado'=>'Activo','impuesto'=>'0.00');

        //$impuesto = Impuestos_orm::where($clause)->get(array('id', 'uuid_impuesto','nombre'));
        $impuesto = Impuestos_orm::where($clause)->first(['id', 'uuid_impuesto','nombre','impuesto']);
        $array_impuesto = isset($impuesto) ? $impuesto->toArray() : '';
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($array_impuesto))->_display();
        exit;
    }


    function exportar_historial() {

        if(!empty($_POST)){
            $uuid =  $this->input->post('historial_exportar');

            $presupuestoObj = new Buscar(new Presupuesto_orm,'uuid_presupuesto');
            $presupuestos = $presupuestoObj->findByUuid($uuid);

            $datos_presupuesto = $presupuestos->toArray();

            $inicio = $datos_presupuesto['inicio']; //mes de inicio
            $perido =  $datos_presupuesto['cantidad_meses']; //cantidad de meses
            list($mes1, $year) = explode("-",$inicio);

            $listados = $presupuestos->lista_presupuesto()->get();
            $datos_excel= array();
            $i=0;
            foreach($listados as $lista){
                $datos_excel[$i]['codigo'] = $lista->cuentas->codigo;
                $datos_excel[$i]['cuenta'] = utf8_decode($lista->cuentas->nombre);

                $meses = json_decode($lista->info_presupuesto);
                foreach($meses->meses as $k=> $mes){
                    $datos_excel[$i] = array_merge($datos_excel[$i], array($k=>floatval($mes)));
                }
                $datos_excel[$i] = array_merge($datos_excel[$i],array('totales'=> $lista->montos));
                $i++;
            }

            //columnas dinamicas de los meses del año
            $colNames = array('Codigo','Cuentas');
            for($j=0;$j<=($perido - 1);$j++){
                $fecha_nueva =  Carbon::createFromDate($year, $mes1, 1, 'America/Panama');

                $fechaObj = $fecha_nueva->addMonths($j);
                $nombre_columna = str_replace(".","",$fechaObj->formatLocalized('%b-%y'));
                array_push($colNames, ucfirst($nombre_columna));
            }

            array_push($colNames,'Totales');
            //header("Content-Type: binary/octet-stream");
            //we create the CSV into memory
            $csv = Writer::createFromFileObject(new SplTempFileObject());

            $csv->insertOne($colNames);
            $csv->insertAll($datos_excel);

            $csv->output('centro_presupuesto.csv');
            die;
        }else{
            die;
        }
    }

function _js(){
    $this->assets->agregar_js(array(
        'public/assets/js/moment-with-locales-290.js',
        'public/assets/js/plugins/bootstrap/daterangepicker.js',
        'public/assets/js/plugins/jquery/jquery.sticky.js',
        'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
        'public/assets/js/plugins/jquery/jquery.progresstimer.min.js',
        'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
        'public/assets/js/plugins/jquery/jstree.min.js',
        'public/assets/js/modules/contabilidad/routes.js',
        'public/assets/js/modules/contabilidad/listar.js',
        'public/assets/js/modules/contabilidad/crear_cuenta.js',
        'public/assets/js/plugins/bootstrap/select2/select2.min.js',
        'public/assets/js/plugins/bootstrap/select2/es.js',
        'public/assets/js/plugins/jquery/chosen.jquery.min.js'
    ));
}
function _Css(){
    $this->assets->agregar_css(array(
        'public/assets/css/default/ui/base/jquery-ui.css',
        'public/assets/css/default/ui/base/jquery-ui.theme.css',
        'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
        'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
        'public/assets/css/plugins/jquery/jstree/default/style.min.css',
        'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
        'public/assets/css/modules/stylesheets/contabilidad.css',
        'public/assets/css/plugins/bootstrap/select2.min.css',
        'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
        'public/assets/css/plugins/jquery/chosen/chosen.min.css'
    ));
}

}

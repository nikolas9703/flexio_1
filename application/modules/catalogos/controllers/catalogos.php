<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Colaboradores
 * 
 * Modulo para administrar la creacion, edicion de solicitudes.
 *
 * @package    PensaApp
 * @subpackage Controller
 * @category   Controllers
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  05/22/2015
 */

use Illuminate\Database\Capsule\Manager as Capsule;
use League\Csv\Writer as Writer;
use Carbon\Carbon;
use Flexio\Modulo\Planes\Repository\PlanesRepository as PlanesRepository;
use Flexio\Modulo\Planes\Models\Planes as PlanesModel;
use Flexio\Modulo\Catalogos\Repository\CatalogosRepository as CatalogosRepository;
use Flexio\Modulo\Catalogos\Models\Catalogos as CatalogosModel;
//use Flexio\Modulo\Planes\Models\Planes_orm as PlanesFormModel;
use Flexio\Modulo\aseguradoras\Repository\AseguradorasRepository as AseguradorasRepository;
use Flexio\Modulo\aseguradoras\Models\Aseguradoras as AseguradorasModel;
//use Flexio\Modulo\aseguradoras\Models\Aseguradoras_orm as AseguradorasFormModel;

class catalogos extends CRM_Controller
{
	/**
	 * @var int
	 */
	protected $usuario_id;
	
	/**
	 * @var int
	 */
	protected $empresa_id;
	
	/**
	 * @var int
	 */
	protected $modulo_id;
	
	/**
	 * @var string
	 */
	protected $nombre_modulo;
	
    protected $SegurosPlanesRepository;    

	/**
	 * @var string
	 */
	protected $upload_folder = './public/uploads/';
	
	function __construct() {
        parent::__construct();
		
        //$this->load->model('Planes_orm');
        //$this->load->model('Coberturas_orm');

        $this->load->helper(array('file', 'string', 'util'));
        $this->load->model('aseguradoras/aseguradoras_orm');
        $this->load->model('configuracion_seguros/Comisiones_orm');
        $this->load->model('Contabilidad/tipo_cuentas_orm');
        $this->load->model('Contabilidad/Cuentas_orm');
        $this->load->model('aseguradoras/Ramos_orm');
        $this->load->model('contactos/Contacto_orm');
        $this->load->model('aseguradoras/Planes_orm');
        $this->load->model('aseguradoras/Coberturas_orm');
        $this->load->model('usuarios/usuario_orm');
        $this->load->model('usuarios/organizacion_orm');
        $this->load->model('usuarios/organizacion_orm');
        $this->load->model('aseguradoras/Catalogo_tipo_poliza_orm');
        $this->load->model('aseguradoras/Catalogo_tipo_intereses_orm');
        $this->load->model('contabilidad/Impuestos_orm');

        $this->load->dbutil();
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresaObj = new Buscar(new Empresa_orm, 'uuid_empresa');
        $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
        $this->id_usuario = $this->session->userdata("huuid_usuario");
        $this->id_empresa = $this->empresaObj->id;

        
        //Obtener el id de usuario de session
        $uuid_usuario = $this->session->userdata('huuid_usuario');
        $usuario = Usuario_orm::findByUuid($uuid_usuario);
        
        $this->usuario_id = $usuario->id;
         
        //Obtener el id_empresa de session
        //$uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $this->empresa_id = $empresa->id;
		
		$this->PlanesRepository = new PlanesRepository();
    }


    public function listar() {
	
		$data = array();
    	
        $this->_Css();   
        $this->_js();
        
    	$this->assets->agregar_js(array(
        'public/assets/js/modules/planes/listar.js'
      ));
    	
    	
    	//defino mi mensaje
        if(!is_null($this->session->flashdata('mensaje'))){
        $mensaje = json_encode($this->session->flashdata('mensaje'));
        }else{
        $mensaje = '';
        }
        $this->assets->agregar_var_js(array(
        "toast_mensaje" => $mensaje
        ));
    	
    	//Verificar permisos para crear
    	$breadcrumb = array(
            "titulo" => '<i class="fa fa-archive"></i> Catalogos',
            "ruta" => array(
                0 => array("nombre" => "Seguros", "url" => "#",  "activo" => false),
                1 => array("nombre" => '<b>Catalogos</b>', "activo" => true)
            ),
            "filtro"    => false,
            "menu"      => array()
        );
        
        if ($this->auth->has_permission('acceso', 'catalogos/crear')){
            $breadcrumb["menu"] = array(
    		"url"	=> 'catalogos/crear',
    		"nombre" => "Crear"
    	);
			$menuOpciones["#cambiarEstadoLnk"] = "Cambiar Estado";
            $menuOpciones["#exportarSolicitudesLnk"] = "Exportar";
            $breadcrumb["menu"]["opciones"] = $menuOpciones;
        }
        
        //Menu para crear
        $clause = array('empresa_id' => $this->empresa_id);
		
        //$data['menu_crear'] = array('nombre'=>1); 
        /*//catalogo para buscador        
        $data['planes'] = planes_orm::where($clause)->get();
        $data['tipo'] = Catalogo_tipo_poliza_orm::get();
        $data['usuarios'] = usuario_orm::where('estado', 'Activo')->get();
        /*$clause2['empresa_id'] = $this->empresa_id;*/        
        
    	$this->template->agregar_titulo_header('Listado de Catalogos');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar($breadcrumb);
		
    }

    public function crear($vista = null,$id = null) { 
        $acceso = 1;
        $mensaje = array();
        $data = array();
        $planes = array();
        $cobertura = array();

        if(!$this->auth->has_permission('acceso')){
          // No, tiene permiso, redireccionarlo.
          $acceso = 0;
          $mensaje = array('estado'=>500, 'mensaje'=>' <b>Usted no cuenta con permiso para esta solicitud</b>','clase'=>'alert-danger');
        }


        if($this->input->post('id_planes')){
            //echo "Aqui";
            Capsule::beginTransaction();
            try {
                $Uuid = $this->input->post('id_planes');
                $planes = Planes_orm::findByUuid($Uuid);
                $planes->nombre = $this->input->post('nombre_plan');
                $planes->id_aseguradora = $this->input->post('idAseguradora');
                $planes->id_ramo = $this->input->post('codigo');
                $planes->id_impuesto = $this->input->post('impuesto');
                if($this->input->post('ch_comision') == 'on'){ $planes->desc_comision = 'Si'; }else{ $planes->desc_comision=''; }
                $planes->save();
                Coberturas_orm::where('id_planes', $planes->id)->delete();
                $fieldset = array();
                foreach ($this->input->post('coberturas') as $value) {
                    $fieldset['nombre'] = $value;
                    $fieldset["id_planes"] = $planes->id;
                    $fieldset["created_at"] = date('Y-m-d H:i:s');
                    Coberturas_orm::create($fieldset);
                }
                Comisiones_orm::where('id_planes', $planes->id)->delete();
                $fieldset = array();
                                //guardar comisiones del plan
                if($this->input->post('anio_inicio')!=NULL){
                    $anio_inicio = $this->input->post('anio_inicio');
                    $anio_fin = $this->input->post('anio_fin');
                    $comision = $this->input->post('p_comision');
                    $sobre_comision = $this->input->post('p_sobre_comision');
                    foreach ($anio_inicio as $key => $value) {
                        $fieldset['inicio'] = $value;
                        $fieldset['fin'] = $anio_fin[$key];
                        $fieldset['comision'] = $comision[$key];
                        $fieldset['sobre_comision'] = $sobre_comision[$key];
                        $fieldset["id_planes"] = $planes->id;
                        $fieldset["created_at"] = date('Y-m-d H:i:s');
                        Comisiones_orm::create($fieldset);
                    }
                }

                if(!is_null($planes)){    
                    $mensaje = array('estado' => 200, 'mensaje' =>'<b>¡&Eacute;xito!</b> Se ha guardado correctamente'); 

                }else{
                    $mensaje = array('estado' => 500, 'mensaje' =>'<strong>¡Error!</strong> Su solicitud no fue procesada'); 
                }

            } catch (ValidationException $e) {
                Capsule::rollback();
            }
            Capsule::commit();
            $aseguradora = Aseguradoras_orm::find($planes->id_aseguradora);
            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('aseguradoras/editar/'.$aseguradora->uuid_aseguradora.'/ver_aseguradora'));
        }else{
            //echo "Aqui 2";
            if(!empty($_POST)){
                Capsule::beginTransaction();
                try {
                    $fieldset['nombre'] = $this->input->post('nombre_plan');
                    $fieldset["id_aseguradora"] = $this->input->post('idAseguradora');
                    $fieldset["id_ramo"] = $this->input->post('codigo');
                    $fieldset["id_impuesto"] = $this->input->post('impuesto');
                    $fieldset["created_at"] = date('Y-m-d H:i:s');
                                //dd($this->input->post('ch_comision'));
                    if($this->input->post('ch_comision') == 'on')$fieldset["desc_comision"] = 'Si';
                    $res = Planes_orm::create($fieldset);
                    $fieldset = array();
                                //guardar coberturas del plan
                    if($this->input->post('coberturas')!=NULL){
                        foreach ($this->input->post('coberturas') as $value) {
                            $fieldset['nombre'] = $value;
                            $fieldset["id_planes"] = $res->id;
                            $fieldset["created_at"] = date('Y-m-d H:i:s');
                            Coberturas_orm::create($fieldset);
                        }
                    }
                    $fieldset = array();
                                //guardar comisiones del plan
                    if($this->input->post('anio_inicio')!=NULL){
                        $anio_inicio = $this->input->post('anio_inicio');
                        $anio_fin = $this->input->post('anio_fin');
                        $comision = $this->input->post('p_comision');
                        $sobre_comision = $this->input->post('p_sobre_comision');
                        foreach ($anio_inicio as $key => $value) {
                            $fieldset['inicio'] = $value;
                            $fieldset['fin'] = $anio_fin[$key];
                            $fieldset['comision'] = $comision[$key];
                            $fieldset['sobre_comision'] = $sobre_comision[$key];
                            $fieldset["id_planes"] = $res->id;
                            $fieldset["created_at"] = date('Y-m-d H:i:s');
                            Comisiones_orm::create($fieldset);
                        }
                    }
                } catch (ValidationException $e) {
                    Capsule::rollback();
                }
                Capsule::commit();
                if (!is_null($res)) {
                    $data["mensaje"]["clase"] = "alert-success";
                    $data["mensaje"]["contenido"] = '<b>¡&Eacute;xito!</b> Se ha guardado correctamente ' . $res->nombre;
                    $mensaje = array('estado' => 200, 'mensaje' =>'<b>¡&Eacute;xito!</b> Se ha guardado correctamente '. $res->nombre);
                } else {
                    $data["mensaje"]["clase"] = "alert-danger";
                    $data["mensaje"]["contenido"] = '<strong>¡Error!</strong> Su solicitud no fue procesada';
                    $mensaje = array('estado' => 500, 'mensaje' =>'<strong>¡Error!</strong> Su solicitud no fue procesada'); 
                }if($this->input->post('vista')=="planes-crear"){
                    $aseguradora = Aseguradoras_orm::find($res->id_aseguradora);
                    $this->session->set_flashdata('mensaje', $mensaje);
                    redirect(base_url('aseguradoras/editar/'.$aseguradora->uuid_aseguradora.'/ver_aseguradora'));
                }
            }
        }


        if($id != null){
            if($vista == 'planes-editar' || $vista == 'planes-ver'){
                $planes = Planes_orm::findByUuid($id);
                $data['coberturas_data'] = Coberturas_orm::where('id_planes',$planes->id)->get();
                $data['comisiones_data'] = Comisiones_orm::where('id_planes',$planes->id)->get();
            }else if($vista == 'planes-crear'){
                $aseguradora = Aseguradoras_orm::findByUuid($id);
            }


        }



        $this->_Css();   
        $this->_js();

        $this->assets->agregar_css(array(

                'public/assets/css/default/ui/base/jquery-ui.css',
                'public/assets/css/default/ui/base/jquery-ui.theme.css',
                'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
                'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
                'public/assets/css/plugins/jquery/jstree/default/style.min.css',
                'public/assets/css/plugins/jquery/chosen/chosen.min.css',
                'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
                'public/assets/css/modules/stylesheets/aseguradoras.css',
                'public/assets/css/plugins/jquery/switchery.min.css'

            ));
        $this->assets->agregar_js(array(       
            'public/assets/js/modules/planes/formulario.js',  
            'public/assets/js/modules/planes/crear.js', 
            'public/assets/js/modules/planes/crear.vue.js',
            'public/assets/js/modules/planes/component.vue.js',  
            'public/assets/js/modules/planes/plugins.js',
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
                'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
                'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
                'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.numeric.extensions.js',
                'public/assets/js/default/vue.js',
                'public/assets/js/modules/configuracion_seguros/routes.js',
                //'public/assets/js/modules/configuracion_seguros/configuracion.js',
                'public/assets/js/modules/planes/configuracion.js',
                'public/assets/js/plugins/jquery/switchery.min.js',
                'public/assets/js/default/formulario.js', 
        ));

        
        $this->assets->agregar_var_js(array(
                "vista" => 'crear',
                "acceso" => $acceso,
                "data_planes" => (!empty($planes)) ? $planes->toJson() : '',
                //"id_planes" => ($id==null) ? '' : $id,
                //"id_aseguradora" => (!isset($aseguradora)) ? '' : $aseguradora->id
            ));
            $menuOpciones = array(
                "#activarLnk" => "Habilitar",
                "#inactivarLnk" => "Deshabilitar",
                "#exportarLnk" => "Exportar",
            );

          /*$data=array();      
          $this->assets->agregar_var_js(array(
            "vista" => 'crear',
            "acceso" => $acceso,
          ));*/
        
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
         
        $breadcrumb = array(
          "titulo" => '<i class="fa fa-archive"></i> Catalogos: Crear / ',
          "ruta" => array(
                0 => array("nombre" => "Seguros", "url" => "#",  "activo" => false),
                1 => array("nombre" => 'Catalogos',"url" => "catalogos/crear", "activo" => false),
                2 => array("nombre" => '<b>Crear</b>', "activo" => true)
            ),
          "filtro"    => false,
          "menu"      => array()
        );
        $data['mensaje'] = $mensaje;
        $clause = array('empresa_id' => $this->id_empresa);
        $data['aseguradoras'] = Aseguradoras_orm::where($clause)->get();
        $data['tipo_intereses'] = Catalogo_tipo_intereses_orm::all();
        $data['tipo_poliza'] = Catalogo_tipo_poliza_orm::all();
        $data['impuestos'] = Impuestos_orm::impuesto_select(array('empresa_id'=>$empresa->id,'estado'=>'Activo'));
        $this->template->agregar_titulo_header('Planes: Crear');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();    
        
    }
    

    

    public function ajax_listar_ramos_tree() {

        echo "AQUIIIII";

        if (!$this->input->is_ajax_request()) {
            return false;
        }

        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $tipo = $this->input->post('tipo');
        $clause = array('empresa_id' => $empresa->id);
        if (!empty($tipo)) $clause['tipo_cuenta_id'] = $tipo;
        $cuentas = Ramos_orm::listar_cuentas($clause); 
        //Constructing a JSON
        $response = new stdClass();
        $response->plugins = ["contextmenu"];
        $response->core->check_callback[0] = true;
        
        $i = 0;
        if (!empty($cuentas)) {
            foreach ($cuentas as $row) {
                $spanStyle = ($row['estado'] == 1) ? '' : 'color:red;';
                $response->core->data[$i] = array(
                    'id' => (string)$row['id'],
                    'parent' => $row["padre_id"] == 0 ? "#" : (string)$row["padre_id"],
                    'text' => "<span id='labelramo' style='".$spanStyle."'>".$row["nombre"]."</span>",
                    'icon' => 'fa fa-folder',
                    'codigo' => $row["id"]
                    //'state' =>array('opened' => true)
                );

                $i++;
            }

        }

        echo json_encode($response);
        exit;

    } 

    function ajax_guardar_ramos() {
        if (!$this->input->is_ajax_request()) {
            return false;
        }
        
        
        $response = new stdClass();
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $id = $this->input->post('id');
        $nombre = $this->input->post('nombre');
        $descripcion = $this->input->post('descripcion');
        $codigo_ramo = $this->input->post('codigo_ramo');
        $tipo_interes_ramo = $this->input->post('tipo_interes_ramo');
        $tipo_poliza_ramo = $this->input->post('tipo_poliza_ramo');
        $form_solicitud = $this->input->post('form_solicitud');
        $padre_id = $this->input->post('codigo');
        $cuenta_id = $this->input->post('cuenta_id');
        
        
        if (!isset($id)) {
            $clause = array(
                "codigo_ramo" => strtoupper($codigo_ramo),
                "empresa_id" => $empresa->id
            );
            $existe = Ramos_orm::findCodigo($clause);
            if($existe && $codigo_ramo != ''){
                $response->clase = "danger";
                $response->estado = 200;
                $response->mensaje = '<b>Error</b> Codigo ya existe.';
                echo json_encode($response);
                exit;
            }else{
                $datos = array();
                $datos['nombre'] = $nombre;
                $datos['descripcion'] = $descripcion;
                $datos['codigo_ramo'] = strtoupper($codigo_ramo);
                $datos['id_tipo_int_asegurado'] = $tipo_interes_ramo;
                $datos['id_tipo_poliza'] = $tipo_poliza_ramo;
                $datos['empresa_id'] = $empresa->id;
                $datos['padre_id'] = $padre_id;
                $impuesto_save = Ramos_orm::create($datos);
                $response->clase = "success";
                $response->estado = 200;
                $response->mensaje = '<b>¡&Eacute;xito!</b> Se ha guardado correctamente  ' . $impuesto_save->nombre;
            }
            
        } else {
            $impuesto_save = Ramos_orm::find($id);
            
            if($impuesto_save->codigo_ramo != strtoupper($codigo_ramo)){
                $clause = array(
                    "codigo_ramo" => strtoupper($codigo_ramo),
                    "empresa_id" => $empresa->id
                );
                $existe = Ramos_orm::findCodigo($clause);
                if($existe){
                    $response->clase = "danger";
                    $response->estado = 200;
                    $response->mensaje = '<b>Error</b> Codigo ya existe.';
                    echo json_encode($response);
                    exit;
                }
            }
            $impuesto_save->nombre = $nombre;
            $impuesto_save->descripcion = $descripcion;
            $impuesto_save->codigo_ramo = strtoupper($codigo_ramo);
            $impuesto_save->id_tipo_int_asegurado = $tipo_interes_ramo;
            $impuesto_save->id_tipo_poliza = $tipo_poliza_ramo;
            $impuesto_save->padre_id = $padre_id;
            $impuesto_save->save();
            $response->clase = "success";
            $response->estado = 200;
            $response->mensaje = '<b>¡&Eacute;xito!</b> Se ha actualizado correctamente  ' . $impuesto_save->nombre;
        }

        echo json_encode($response);
        exit;
    }

function ocultoformulario() {
        $clause = array('empresa_id' => $this->empresa_id);        
        $this->assets->agregar_var_js(array(
        ));
        
        $this->load->view('formulario');
}

function guardar() {
    if($_POST){
    unset($_POST["campo"]["guardar"]);
    $campo = Util::set_fieldset("campo");    
    Capsule::beginTransaction();
    try {
    if(empty($campo['uuid'])){ 
    $campo["uuid_planes"] = Capsule::raw("ORDER_UUID(uuid())");
    $clause['empresa_id'] = $this->empresa_id;
    $total = $this->solicitudesRepository->listar($clause);
    $year = Carbon::now()->format('y');
    $codigo = Util::generar_codigo($_POST['codigo_ramo'] . "-" . $year , count($total) + 1);
    $campo["numero"] = $codigo;
    $campo["usuario_id"] = $this->session->userdata['id_usuario'];
    $campo["empresa_id"] = $this->empresa_id;    
    $date = Carbon::now();
    $date = $date->format('Y-m-d');
    $campo['fecha_creacion'] = $date;   
    $solicitudes = $this->solicitudesModel->create($campo); 
    }else{
    echo "hola mundo";
    }
    Capsule::commit();
    }catch(ValidationException $e){
    log_message('error', $e);
    Capsule::rollback();
    }
    if(!is_null($solicitudes)){    
        $mensaje = array('estado' => 200, 'mensaje' =>'<b>¡&Eacute;xito!</b> Se ha guardado correctamente'); 
  
    }else{
        $mensaje = array('class' =>'alert-danger', 'contenido' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
    }


    }else{
            $mensaje = array('class' =>'alert-warning', 'contenido' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
    }
    
    $this->session->set_flashdata('mensaje', $mensaje);
    redirect(base_url('solicitudes/listar'));
}

	public function ocultotabla() {
    	$this->assets->agregar_js(array(
    		'public/assets/js/modules/planes/tabla.js'
    	));
    	
    	$this->load->view('tabla');
    }

	public function ajax_listar($grid=NULL) {    	
    	$clause = array(
    		"empresa_id" =>  $this->empresa_id
    	);
    	$nombre 	= $this->input->post('nombre', true);
    	$ruc 	= $this->input->post('ruc', true);
    	$telefono 	= $this->input->post('telefono', true);
    	$email 		= $this->input->post('email', true);
    	$direccion    	= $this->input->post('direccion', true);
		$aseguradora 	= $this->input->post('planes', true);
    	
    	if(!empty($nombre)){
    		$clause["nombre"] = array('LIKE', "%$nombre%");
    	}
    	if(!empty($ruc)){
    		$clause["ruc"] = array('LIKE', "%$ruc%");
    	}
    	if(!empty($telefono)){
    		$clause["telefono"] = array('LIKE', "%$telefono%");
    	}
    	if(!empty($email)){
    		$clause["email"] = array('LIKE', "%$email%");
    	}
    	if(!empty($direccion)){
    		$clause["direccion"] = array('LIKE', "%$direccion%");
    	}
		
		if(!empty($aseguradora)){
    		$clause["creado_por"] = $aseguradora;
    	}
       
    	list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
		
    	$count = $this->PlanesRepository->listar_planes($clause, NULL, NULL, NULL, NULL)->count();
    		
    	list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
    		 
    	$rows = $this->PlanesRepository->listar_planes($clause, $sidx, $sord, $limit, $start);
    	
    	//Constructing a JSON
    	$response = new stdClass();
    	$response->page     = $page;
    	$response->total    = $total_pages;
    	$response->records  = $count;
    	$response->result 	= array();
    	$i=0;
    
    	if(!empty($rows)){
    		foreach ($rows AS $i => $row){
            $uuid_aseguradora = bin2hex($row->uuid_aseguradora);
            $now = Carbon::now();
            $hidden_options = ""; 
            $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row->id .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

            $estado = "Pendiente";
            $estado_color = trim($estado) == "Pendiente" ? 'background-color:#F8AD46' : 'background-color: red';
            
            $response->rows[$i]["id"] = $row->id;
            $response->rows[$i]["cell"] = array(
                    '<a href="'. base_url('planes/ver/'. $uuid_aseguradora) .'" style="color:blue;">'. $row->nombre.'</a>',  
					$row->ruc,
					$row->telefono,
					$row->email,
					$row->direccion,
                    $link_option,
                    $hidden_options                   
            );
    $i++;
    		}
    	}
    	echo json_encode($response);
    	exit;
    } 

private function _js() {
    $this->assets->agregar_js(array(
        'public/assets/js/default/jquery-ui.min.js',
        'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
        'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
        'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
        'public/assets/js/default/jquery.inputmask.bundle.min.js',
        'public/assets/js/plugins/jquery/jquery.webui-popover.js',
        'public/assets/js/plugins/jquery/jquery.sticky.js',
        'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
        'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
        'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
        'public/assets/js/plugins/jquery/chosen.jquery.min.js',
        'public/assets/js/moment-with-locales-290.js',
        'public/assets/js/plugins/jquery/switchery.min.js',
        'public/assets/js/plugins/bootstrap/jquery.bootstrap-touchspin.js',
        'public/assets/js/plugins/bootstrap/daterangepicker.js',
        'public/assets/js/default/formulario.js',
        'public/assets/js/plugins/jquery/fileupload/jquery.fileupload.js',
        'public/assets/js/default/toast.controller.js',
        'public/assets/js/plugins/bootstrap/select2/select2.min.js',
        'public/assets/js/plugins/bootstrap/select2/es.js'
  ));
  }

  private function _css() {
    $this->assets->agregar_css(array(
        'public/assets/css/default/ui/base/jquery-ui.css',
        'public/assets/css/default/ui/base/jquery-ui.theme.css',
        'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
        'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
        'public/assets/css/plugins/jquery/switchery.min.css',
        'public/assets/css/plugins/jquery/chosen/chosen.min.css',
        'public/assets/css/plugins/bootstrap/bootstrap-tagsinput.css',
        'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
        'public/assets/css/plugins/jquery/fileinput/fileinput.css',
        'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
        'public/assets/css/plugins/bootstrap/awesome-bootstrap-checkbox.css',
        'public/assets/css/plugins/jquery/toastr.min.css',
        'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
        'public/assets/css/plugins/bootstrap/select2.min.css',
    ));
  }    
}
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
use Flexio\Modulo\Planes\Models\Planes_orm as Planes_orm;
use Flexio\Modulo\Usuarios\Models\Usuarios as Usuarios;
use Flexio\Modulo\Roles\Models\Roles as Roles;
use Flexio\Modulo\aseguradoras\Repository\AseguradorasRepository as AseguradorasRepository;
use Flexio\Modulo\aseguradoras\Models\Aseguradoras as AseguradorasModel;
use Flexio\Modulo\Ramos\Models\CatalogoTipoPoliza as CatalogoTipoPoliza;
use Flexio\Modulo\Ramos\Models\CatalogoTipoIntereses as CatalogoTipoIntereses;
//use Flexio\Modulo\aseguradoras\Models\Aseguradoras_orm as AseguradorasFormModel;

class Planes extends CRM_Controller
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
     * @var int
     */
    protected $aseguradora_id;
	
	/**
	 * @var string
	 */
	protected $nombre_modulo;
	
    protected $SegurosPlanesRepository;  
    protected $PlanesRepository;  

	/**
	 * @var string
	 */
	protected $upload_folder = './public/uploads/';
	
	function __construct() {
        parent::__construct();
		
        //$this->load->model('Planes_orm');
        //$this->load->model('Coberturas_orm');

        $this->load->helper(array('file', 'string', 'util'));
        //$this->load->model('aseguradoras/aseguradoras_orm');
        $this->load->model('configuracion_seguros/Comisiones_orm');
        $this->load->model('Contabilidad/tipo_cuentas_orm');
        $this->load->model('Contabilidad/Cuentas_orm');
        $this->load->model('catalogos/Ramos_orm');
        $this->load->model('contactos/Contacto_orm');
        $this->load->model('catalogos/Aseguradoras_orm');
        //$this->load->model('catalogos/Planes_orm');
        $this->load->model('catalogos/Coberturas_orm');
        $this->load->model('catalogos/Deducibles_orm');
        $this->load->model('usuarios/usuario_orm');
        $this->load->model('usuarios/organizacion_orm');
        $this->load->model('usuarios/organizacion_orm');
        $this->load->model('catalogos/Catalogo_tipo_poliza_orm');
        $this->load->model('catalogos/Catalogo_tipo_intereses_orm');
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
            "titulo" => '<i class="fa fa-archive"></i> Planes',
            "ruta" => array(
                0 => array("nombre" => "Seguros", "url" => "#",  "activo" => false),
                1 => array("nombre" => '<b>Planes</b>', "activo" => true)
            ),
            "filtro"    => false,
            "menu"      => array()
        );
        
        if ($this->auth->has_permission('acceso', 'planes/crear')){
            $breadcrumb["menu"] = array(
    		"url"	=> 'planes/crear',
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
        
    	$this->template->agregar_titulo_header('Listado de Planes');
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
        //Llegan variables de formulario y se procesa
        if ($vista == "planes") {

            if($this->input->post('id_planes')){
                //echo "Aqui";
                //die();
                Capsule::beginTransaction();
                try {
                    $Uuid = $this->input->post('id_planes');
                    $planes = Planes_orm::findByUuid($Uuid);
                    $planes->nombre = $this->input->post('nombre_plan');
                    $planes->id_aseguradora = $this->input->post('idAseguradora');
                    $planes->id_ramo = $this->input->post('codigo');
                    $planes->id_impuesto = $this->input->post('impuesto');
                    $planes->prima_neta = $this->input->post('primaneta');

                    if($this->input->post('ch_comision') == 'on'){ $planes->desc_comision = 'Si'; }else{ $planes->desc_comision='no'; }
                    $planes->save();
                    Coberturas_orm::where('id_planes', $planes->id)->delete();
                    $fieldset = array();
                    if($this->input->post('coberturas')!=NULL){
                        $cobermonetario = $this->input->post('coberturasmonet');
                        foreach ($this->input->post('coberturas') as $key => $value) {
                            if ($value!="" AND $value!=NULL) {
                                $fieldset['nombre'] = $value;
                                $fieldset["id_planes"] = $planes->id;
                                $fieldset["created_at"] = date('Y-m-d H:i:s');
                                $fieldset["cobertura_monetario"] = $cobermonetario[$key];
                                Coberturas_orm::create($fieldset);
                            }                                
                        }
                    }
                    Deducibles_orm::where('id_planes', $planes->id)->delete();
                    $fieldset = array();
                    //guardar deducibles del plan
                    if($this->input->post('deducibles')!=NULL){
                        $deducmonetario = $this->input->post('deduciblesmonet');
                        foreach ($this->input->post('deducibles') as $key => $value) {
                            if ($value!="" AND $value!=NULL) {
                                $fieldset['nombre'] = $value;
                                $fieldset["id_planes"] = $planes->id;
                                $fieldset["created_at"] = date('Y-m-d H:i:s');
                                $fieldset["deducible_monetario"] = $deducmonetario[$key];
                                Deducibles_orm::create($fieldset);
                            }                                
                        }
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
				
				if($this->input->post('regreso')=='aseg')
				{
					redirect(base_url('aseguradoras/editar/'.$aseguradora->uuid_aseguradora.''));
				}
				else
				{
					redirect(base_url('catalogos/ver/planes'));
				}
					
            }else{
                //echo "Aqui 2";
                //die();
                if(!empty($_POST)){
                    Capsule::beginTransaction();
                    try {
                        $fieldset['nombre'] = $this->input->post('nombre_plan');
                        $fieldset["id_aseguradora"] = $this->input->post('idAseguradora');
                        $fieldset["id_ramo"] = $this->input->post('codigo');
                        $fieldset["id_impuesto"] = $this->input->post('impuesto');
                        $fieldset["prima_neta"] = $this->input->post('primaneta');
                        $fieldset["created_at"] = date('Y-m-d H:i:s');
                                    //dd($this->input->post('ch_comision'));
                        if($this->input->post('ch_comision') == 'on')$fieldset["desc_comision"] = 'Si';
                        $res = Planes_orm::create($fieldset);
                        $fieldset = array();
                                    //guardar coberturas del plan
                        if($this->input->post('coberturas')!=NULL){
                            $cobermonetario = $this->input->post('coberturasmonet');
                            foreach ($this->input->post('coberturas') as $key => $value) {
                                if ($value!="" AND $value!=NULL) {
                                    $fieldset['nombre'] = $value;
                                    $fieldset["id_planes"] = $res->id;
                                    $fieldset["created_at"] = date('Y-m-d H:i:s');
                                    $fieldset["cobertura_monetario"] = $cobermonetario[$key];
                                    Coberturas_orm::create($fieldset);
                                }                                
                            }
                        }

                        $fieldset = array();
                                    //guardar coberturas del plan
                        if($this->input->post('deducibles')!=NULL){
                            $deducmonetario = $this->input->post('deduciblesmonet');
                            foreach ($this->input->post('deducibles') as $key => $value) {
                                if ($value!="" AND $value!=NULL) {
                                    $fieldset['nombre'] = $value;
                                    $fieldset["id_planes"] = $res->id;
                                    $fieldset["created_at"] = date('Y-m-d H:i:s');
                                    $fieldset["deducible_monetario"] = $deducmonetario[$key];
                                    Deducibles_orm::create($fieldset);
                                }                                
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
                        $exito=1;
                    } else {
                        $data["mensaje"]["clase"] = "alert-danger";
                        $data["mensaje"]["contenido"] = '<strong>¡Error!</strong> Su solicitud no fue procesada';
                        $mensaje = array('estado' => 500, 'mensaje' =>'<strong>¡Error!</strong> Su solicitud no fue procesada'); 
                        $exito=0;
                    }
                    if($this->input->post('vista')=="planes-crear"){
                        $aseguradora = Aseguradoras_orm::find($res->id_aseguradora);
                        $this->session->set_flashdata('mensaje', $mensaje);
						
						if($this->input->post('regreso')=='aseg')
						{
							redirect(base_url('aseguradoras/editar/'.$aseguradora->uuid_aseguradora.''));
						}
						else
						{
							redirect(base_url('catalogos/ver/planes'));
						}
                   }
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


        if ($exito==1) {
            //Redireccionar
            redirect(base_url('catalogos/ver/exitoso'));
        }else{
            //Redireccionar
            redirect(base_url('catalogos/ver/fallo'));
        }      
                
    }




    public function editar($uuid_plan = null) { 

        $acceso = 1;
        $mensaje = array();
        $mensaje2 = array();
        $data = array();
        $planes = array();
        $cobertura = array();

        if($this->auth->has_permission('acceso','editar Planes')){ $planeditar =1;  }else{ $planeditar=0; }

        if(!$this->auth->has_permission('acceso','editar Planes')){ redirect(base_url('')); }

        if(!$this->auth->has_permission('acceso')){
        // No, tiene permiso, redireccionarlo.
            $acceso = 0;
            $mensaje = array('estado'=>500, 'mensaje'=>' <b>Usted no cuenta con permiso para esta solicitud</b>','clase'=>'alert-danger');
            redirect(base_url(''));
        }


        $this->_Css();   
   // $this->_js();

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
            'public/assets/js/modules/ramos/routes.js',
            'public/assets/js/modules/planes/configuracion.js',
            'public/assets/js/modules/ramos/configuracion.js',        
            'public/assets/js/plugins/jquery/switchery.min.js',
            'public/assets/js/default/formulario.js', 
            ));

        $plan = PlanesModel::where("uuid_planes", hex2bin($uuid_plan))->first();


        $this->assets->agregar_var_js(array(
            "vista" => 'editar',
            "acceso" => $acceso,
            "data_planes" => (!empty($planes)) ? $planes->toJson() : '',
            "id_ramo_plan" => $plan->id_ramo,
                //"id_planes" => ($id==null) ? '' : $id,
                //"id_aseguradora" => (!isset($aseguradora)) ? '' : $aseguradora->id
            ));
        $menuOpciones = array(
            "#activarLnk" => "Habilitar",
            "#inactivarLnk" => "Deshabilitar",
            "#exportarLnk" => "Exportar",
            );


        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);

        $breadcrumb = array(
            "titulo" => '<i class="fa fa-archive"></i> Planes: Editar  ',
          "ruta" => array(
            0 => array("nombre" => "Seguros", "url" => "#",  "activo" => false),
            1 => array("nombre" => 'Catálogos',"url" => "catalogos/ver", "activo" => false),
            2 => array("nombre" => 'Planes',"url" => "catalogos/ver/planes", "activo" => false),
            3 => array("nombre" => '<b>Editar</b>', "activo" => true)
            ),
          "filtro"    => false,
          "menu"      => array()
          );
        /*if ($this->auth->has_permission('acceso', 'ver Editar')){
            $breadcrumb["menu"] = array(
                "url"   => '#',
                "nombre" => "Acción"
                );
            $menuOpciones2["#cambiarEstadoLnk"] = "Cambiar Estado";
            $menuOpciones2["#exportarRamosLnk"] = "Exportar";
            $breadcrumb["menu"]["opciones"] = $menuOpciones2;
        } */



        $clauseplanes = array('uuid_planes' => $uuid_plan);
        $data['planes'] = Planes_orm::findByUuid($uuid_plan);
        $data['coberturas'] = Coberturas_orm::findByIdP($data['planes']->id);
        $data['deducibles'] = Deducibles_orm::findByIdP($data['planes']->id);
        $data['comisiones'] = Comisiones_orm::findByIdP($data['planes']->id);
        $ase = Aseguradoras_orm::findByIdAseguradora($data['planes']->id_aseguradora);
        $data['uuid'] = array('uuid_a'=>$ase->uuid_aseguradora);

        $c=0;
        $ramos="";
        $idramo = $data['planes']->id_ramo;
        while ($c==0) {
            $ramo = Ramos_orm::getRamoById($idramo);
            if (($ramo->padre_id)!=0) { $idramo = $ramo->padre_id; }else{ $c=1; }
            if ($ramos=="") {$ramos = $ramo->nombre;}else{ $ramos = $ramo->nombre."/".$ramos; }
        }
        $data['ramos'] = array('ramo'=> $ramos, 'id_ramo' => $idramo);

        $clause = array('empresa_id' => $this->id_empresa,'estado'=>1);
        $clauseasegura = array('empresa_id' => $this->id_empresa, 'estado' => 'Activo');
        $data['mensaje'] = $mensaje;
        $data['mensaje2'] = $mensaje2;
        //$data['accesoplan'] = array('plancrear' => $plancrear, 'planeditar' => $planeditar, 'planver' => $planver, 'planlistar' => $planlistar);
        $data['aseguradoras'] = AseguradorasModel::where($clauseasegura)->get();
        $data['impuestos'] = Impuestos_orm::impuesto_select(array('empresa_id'=>$empresa->id,'estado'=>'Activo'));
        $this->template->agregar_titulo_header('Planes: Editar');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar(); 

    }



    public function ver($uuid_plan = null) { 

        $acceso = 1;
        $mensaje = array();
        $mensaje2 = array();
        $data = array();
        $planes = array();
        $cobertura = array();

        if(!$this->auth->has_permission('acceso','ver Planes')){ redirect(base_url('')); }
        
/*
        if(!$this->auth->has_permission('acceso')){
        // No, tiene permiso, redireccionarlo.
            $acceso = 0;
            $mensaje = array('estado'=>500, 'mensaje'=>' <b>Usted no cuenta con permiso para esta solicitud</b>','clase'=>'alert-danger');
            //redirect(base_url(''));
        }*/





        $this->_Css();   
   // $this->_js();

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
            'public/assets/js/modules/ramos/routes.js',
            'public/assets/js/modules/planes/configuracion.js',
            'public/assets/js/modules/ramos/configuracion.js',        
            'public/assets/js/plugins/jquery/switchery.min.js',
            'public/assets/js/default/formulario.js', 
            ));


        $this->assets->agregar_var_js(array(
            "vista" => 'editar',
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


        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);

        $breadcrumb = array(
            "titulo" => '<i class="fa fa-archive"></i> Planes: Ver  ',
          "ruta" => array(
            0 => array("nombre" => "Seguros", "url" => "#",  "activo" => false),
            1 => array("nombre" => 'Catálogos',"url" => "catalogos/ver", "activo" => false),
            2 => array("nombre" => 'Planes',"url" => "catalogos/ver/planes", "activo" => false),
            3 => array("nombre" => '<b>Ver</b>', "activo" => true)
            ),
          "filtro"    => false,
          "menu"      => array()
          );
        /*if ($this->auth->has_permission('acceso', 'ver Editar')){
            $breadcrumb["menu"] = array(
                "url"   => '#',
                "nombre" => "Acción"
                );
            $menuOpciones2["#cambiarEstadoLnk"] = "Cambiar Estado";
            $menuOpciones2["#exportarRamosLnk"] = "Exportar";
            $breadcrumb["menu"]["opciones"] = $menuOpciones2;
        } */

        $clauseplanes = array('uuid_planes' => $uuid_plan);
        $data['planes'] = Planes_orm::findByUuid($uuid_plan);
        $data['coberturas'] = Coberturas_orm::findByIdP($data['planes']->id);
        $data['deducibles'] = Deducibles_orm::findByIdP($data['planes']->id);
        $data['comisiones'] = Comisiones_orm::findByIdP($data['planes']->id);
        $ase = Aseguradoras_orm::findByIdAseguradora($data['planes']->id_aseguradora);
        $data['uuid'] = array('uuid_a'=>$ase->uuid_aseguradora);

        $c=0;
        $ramos="";
        $idramo = $data['planes']->id_ramo;
        while ($c==0) {
            $ramo = Ramos_orm::getRamoById($idramo);
            if (($ramo->padre_id)!=0) { $idramo = $ramo->padre_id; }else{ $c=1; }
            if ($ramos=="") {$ramos = $ramo->nombre;}else{ $ramos = $ramo->nombre."/".$ramos; }
        }
        $data['ramos'] = array('ramo'=> $ramos, 'id_ramo' => $idramo);

        $clause = array('empresa_id' => $this->id_empresa,'estado'=>1);
        $clauseasegura = array('empresa_id' => $this->id_empresa, 'estado' => 'Activo');
        $data['mensaje'] = $mensaje;
        $data['mensaje2'] = $mensaje2;
        //$data['accesoplan'] = array('plancrear' => $plancrear, 'planeditar' => $planeditar, 'planver' => $planver, 'planlistar' => $planlistar);
        $data['aseguradoras'] = AseguradorasModel::where($clauseasegura)->get();
        $data['impuestos'] = Impuestos_orm::impuesto_select(array('empresa_id'=>$empresa->id,'estado'=>'Activo'));
        $this->template->agregar_titulo_header('Planes: Crear');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar(); 

    }
    

    

    public function ajax_listar_ramos_tree() {

        if (!$this->input->is_ajax_request()) {
            return false;
        }

        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $tipo = $this->input->post('tipo');
        $clause = array('empresa_id' => $empresa->id, 'estado' => 1);
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
	
	public function ocultotablatab() {
    	$this->assets->agregar_js(array(
    		'public/assets/js/modules/planes/tabla_planes.js'
    	));
    	
    	$this->load->view('tabla_planes');
    }
	
	public function ocultotablaprincipal() {
    	$this->assets->agregar_js(array(
    		'public/assets/js/modules/planes/tabla_planes_principal.js'
    	));
    	
    	$this->load->view('tabla_planes_principal');
    }

	
    public function ajax_listar_planes($uuid_aseguradora = null) {
        //Just Allow ajax request   
   
        
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        //print_r("post=".$this->input->post('uuid'));   
        if($this->input->post('uuid')){
            $ase = Aseguradoras_orm::findByUuid($this->input->post('uuid'));
            $id_aseguradora = $ase->id;
        }

        $nombreplan = $this->input->post('plan', true);
        $productoplan = $this->input->post('producto', true);
        $ramoplan = $this->input->post('ramo', true);
        $comisionplan = $this->input->post('comision', true);
        $sobrecomisionplan = $this->input->post('sobre_comision', true);
        $desccomisionplan = $this->input->post('desc_comision', true);
        

        if($nombreplan!="")
            $clause['seg_planes.nombre'] = array('LIKE', '%'.$nombreplan.'%');
        if($productoplan!="")
            $clause['producto.nombre'] = array('LIKE', '%'.$productoplan.'%');
        if($ramoplan!="")
            $clause['seg_ramos.nombre'] = array('LIKE', '%'.$ramoplan.'%');
        if($comisionplan!="")
            $clause['comi.comision'] = array('LIKE', '%'.$comisionplan.'%');
        if($sobrecomisionplan!="")
            $clause['comi.sobre_comision'] = array('LIKE', '%'.$sobrecomisionplan.'%');
        if($desccomisionplan!="")
            $clause['seg_planes.desc_comision'] = array('LIKE', '%'.$desccomisionplan.'%');
        
        
        //$clause = array('id_aseguradora' => $id_aseguradora);
        $clause['id_aseguradora'] = $id_aseguradora;
		
		if($this->input->post('modulo')=="Aseguradoras")
		{
			$regr="aseg";
		}
		else
		{
			$regr='';
		}

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar(); 
        $count = Planes_orm::listar($clause, null, null, null, null)->count();
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $planes = Planes_orm::listar($clause, $sidx, $sord, $limit, $start);

        //Constructing a JSON
        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->record = $count;
        $i = 0;

        if (!empty($planes)) {
            foreach ($planes as $row) {
                //$tituloBoton = ($row['estado'] != 1) ? 'Habilitar' : 'Deshabilitar';
                //$estado = ($row['estado'] == 1) ? 0 : 1;

                if ($row['comision']=="") { $comision = 0; }else{ $comision = $row['comision']; }
                if ($row['sobre_comision']=="") { $sobrecomision = 0; }else{ $sobrecomision = $row['sobre_comision']; }
				
                $hidden_options = "";
                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row->id .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $hidden_options .= '<a href="'. base_url('planes/ver/'. $row->uuid_planes.'?regr='.$regr) .'" data-id="'. $row->id .'" class="btn btn-block btn-outline btn-success">Ver detalle</a>';
                $hidden_options .= '<a href="'. base_url('planes/editar/'. $row->uuid_planes.'?regr='.$regr) .'" data-id="'. $row->id .'" class="btn btn-block btn-outline btn-success">Editar</a>';
                $level = substr_count($row['nombre'], ".");
                $spanStyle = ($row['estado'] == 1) ? 'success' : 'danger';
                $response->rows[$i] = array("id" => $row['id'], 'cell' => array(
                    'id' => $row['id'],
                    'plan' => "<a href='". base_url('planes/ver/'. $row->uuid_planes.'?regr='.$regr) ."'><span style='".$spanStyle."'>".$row['plan']."</span></a>",
                    'producto' => $row['producto'],
                    'ramo' => $row['ramo'],
                    'comision' => $comision." %",
                    'sobre_comision' => $sobrecomision. " %",
                    'desc_comision' => $row['desc_comision'],
                    'link' => $link_option,
                    'options' => $hidden_options,
                    ));
                $i++;
            }
        }

        echo json_encode($response);
        exit;
    }
	
	public function ajax_listar_planes_principal($uuid_aseguradora = null) {
        //Just Allow ajax request   
   
        if (!$this->input->is_ajax_request()) {
            return false;
        }
		$clause=array();
		
		$nombreplan = $this->input->post('seg_planes_nombre', true);
        $aseguradoranombre = $this->input->post('aseg_nombre', true);
        $productoplan = $this->input->post('producto_nombre', true);
        $comisionplan = $this->input->post('comi_comision', true);
        $sobrecomisionplan = $this->input->post('comi_sobre_comision', true);
        $desccomisionplan = $this->input->post('seg_planes_desc_comision', true);
		
		if($nombreplan!="")
            $clause['seg_planes.nombre'] = array('LIKE', '%'.$nombreplan.'%');
        if($productoplan!="")
            $clause['producto.nombre'] = array('LIKE', '%'.$productoplan.'%');
        if($aseguradoranombre!="")
            $clause['aseg.nombre'] = array('LIKE', '%'.$aseguradoranombre.'%');
        if($comisionplan!="")
            $clause['comi.comision'] = array('LIKE', '%'.$comisionplan.'%');
        if($sobrecomisionplan!="")
            $clause['comi.sobre_comision'] = array('LIKE', '%'.$sobrecomisionplan.'%');
        if($desccomisionplan!="")
            $clause['seg_planes.desc_comision'] = array('LIKE', '%'.$desccomisionplan.'%');
		
		$clause['aseg.empresa_id'] = $this->empresa_id;

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar(); 
        $count = Planes_orm::listartodo($clause, null, null, null, null)->count();
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $planes = Planes_orm::listartodo($clause, $sidx, $sord, $limit, $start);

        //Constructing a JSON
        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->record = $count;
        $i = 0;

        if (!empty($planes)) {
            foreach ($planes as $row) {
                //$tituloBoton = ($row['estado'] != 1) ? 'Habilitar' : 'Deshabilitar';
                //$estado = ($row['estado'] == 1) ? 0 : 1;

                if ($row['comision']=="") { $comision = 0; }else{ $comision = $row['comision']; }
                if ($row['sobre_comision']=="") { $sobrecomision = 0; }else{ $sobrecomision = $row['sobre_comision']; }
				
                $hidden_options = "";
                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row->id .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $hidden_options .= '<a href="'. base_url('planes/ver/'. $row->uuid_planes) .'" data-id="'. $row->id .'" class="btn btn-block btn-outline btn-success">Ver detalle</a>';
                $hidden_options .= '<a href="'. base_url('planes/editar/'. $row->uuid_planes) .'" data-id="'. $row->id .'" class="btn btn-block btn-outline btn-success">Editar</a>';
                $level = substr_count($row['nombre'], ".");
                $spanStyle = ($row['estado'] == 1) ? 'success' : 'danger';
                $response->rows[$i] = array("id" => $row['id'], 'cell' => array(
                    'id' => $row['id'],
                    'seg_planes.nombre' => "<a href='". base_url('planes/ver/'. $row->uuid_planes) ."'><span style='".$spanStyle."'>".$row['plan']."</span></a>",
                    'aseg.nombre' => "<a href='". base_url('aseguradoras/editar/'. bin2hex($row->uuid_aseguradora)) ."'><span style='".$spanStyle."'>".$row->nombre_aseguradora."</span></a>",
				    'producto.nombre' => "<span style='".$spanStyle."'>".$row['producto']."</span>",
                    'comi.comision' => $comision." %",
                    'comi.sobre_comision' => $sobrecomision. " %",
                    'seg_planes.desc_comision' => $row['desc_comision'],
                    'link' => $link_option,
                    'options' => $hidden_options,
                    ));
                $i++;
            }
        }

        echo json_encode($response);
        exit;
    }


    public function detalles_planes($data = array()) {
        //If ajax request
        
      $this->assets->agregar_js(array(
            'public/assets/js/modules/planes/tabla_planes.js'
        ));//'public/assets/js/modules/aseguradoras/tabla_ramos.js'
        
      //$this->aseguradora_id = $data['uuid_aseguradora'];

        $this->load->view('tabla_planes', $data);
    }

    public function exportar() {
        if(empty($_POST)){
            exit();
        }
        
        $ids =  $this->input->post('ids', true);
        $id = explode(",", $ids);
    
        if(empty($id)){
            return false;
        }

        $csv = array();
        $csvdata = array();
        //$clause = array('id_aseguradora' => $id_aseguradora);
        $clause['id'] = $id;
                
        $planes = Planes_orm::listarplanesexportar($clause, null, null, null, null);
        if(empty($planes)){
            return false;
        }
        $i=0;
        foreach ($planes AS $row)
        {
            if ($row->comision == "") { $comi = "0"; }else{ $comi = $row->comision; }
            if ($row->sobre_comision == "") { $sobrecomi = "0"; }else{ $sobrecomi = $row->sobre_comision; }

            $csvdata[$i]['nombre'] = $row->plan;
			$csvdata[$i]["nombre_aseguradora"] = utf8_decode(Util::verificar_valor($row->nombre_aseguradora));
            $csvdata[$i]["producto"] = utf8_decode(Util::verificar_valor($row->producto));
            $csvdata[$i]["ramo"] = utf8_decode(Util::verificar_valor($row->ramo));
            $csvdata[$i]["inicio_comision"] = utf8_decode(Util::verificar_valor($row->inicio_comision));
            $csvdata[$i]["fin_comision"] = utf8_decode(Util::verificar_valor($row->fin_comision));
            $csvdata[$i]["comision"] = $comi;
            $csvdata[$i]["sobrecomision"] = $sobrecomi;
            $csvdata[$i]["desccomision"] = utf8_decode(Util::verificar_valor($row->desc_comision));
            $i++;
        }
        //we create the CSV into memory
        $csv = Writer::createFromFileObject(new SplTempFileObject());
        $csv->insertOne([
            'Nombres',
			'Aseguradora',
            'Producto',
            'Ramo',
            'Inicio',
            'Fin',
            'Comision',
            'Sobre Comision',
            'Desc Comision'
        ]);
        $csv->insertAll($csvdata);
        $csv->output("planes-". date('ymd') .".csv");
        exit();
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
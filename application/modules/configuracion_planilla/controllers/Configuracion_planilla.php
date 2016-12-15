<?php
/**
 * Planilla
 * 
 * Modulo para administrar Planilla
 * o juridicos.
 *
 * @package    PensaApp
 * @subpackage Controller
 * @category   Controllers
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  15/10/2015
 */
 
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon;
use Flexio\Modulo\ConfiguracionPlanilla\Models\ConfiguracionPlanillaLiquidacion;
use Flexio\Modulo\ConfiguracionPlanilla\Repository\ConfiguracionPlanillaLiquidacionRepository;


class Configuracion_planilla extends CRM_Controller
{
	
	protected $empresa_id;
	protected $configuracionPlanillaLiquidacionRepository;
	
	function __construct()
    {
        parent::__construct();
         
        $this->load->model('Acumulados_contructores_orm');
        $this->load->model('Deducciones_contructores_orm');
        $this->load->model('Acumulados_orm');
        $this->load->model('Deducciones_orm');
        $this->load->model('Diasferiados_orm');
        $this->load->model('Diasferiados_acumulados_orm');
        $this->load->model('Diasferiados_deducciones_orm');
        $this->load->model('Beneficios_orm');
        $this->load->model('Beneficios_acumulados_orm');
        $this->load->model('Beneficios_deducciones_orm');
        $this->load->model('Recargos_orm');
        $this->load->model('usuarios/usuario_orm');
        $this->load->model('usuarios/empresa_orm');
        $this->load->model('contabilidad/cuentas_orm');
        $this->load->model('modulos/Catalogos_orm');
         
        $this->load->dbutil();
        
        //Obtener el id_empresa de session
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        
        $this->empresa_id = $empresa->id;
        $this->configuracionPlanillaLiquidacionRepository = new ConfiguracionPlanillaLiquidacionRepository;
        
     }
     
      
     public function configuracion()
    {
       	//Verificar permisos de acceso a esta vista
    	if(!$this->auth->has_permission('acceso', 'configuracion_planilla/configuracion')){
    		//Redireccionar
    		redirect(base_url('/'));
    	}
      	
    	//Incializacion de Arrays
    	$data = $lista=  array();
    	
     	$data['permiso']['permiso_deducciones'] =  $this->auth->has_permission('configuracion__tabDeducciones', 'planilla/configuracion')?1:0;
    	$data['permiso']['permiso_beneficios']=  $this->auth->has_permission('configuracion__tabBeneficios', 'planilla/configuracion')?1:0;
    	$data['permiso']['permiso_acumulados'] =  $this->auth->has_permission('configuracion__tabAcumulados', 'planilla/configuracion')?1:0;
    	$data['permiso']['permiso_feriados'] =  $this->auth->has_permission('configuracion__tabDiasFeriados', 'planilla/configuracion')?1:0;
    	$data['permiso']['permiso_recargos']=  $this->auth->has_permission('configuracion__tabRecargos', 'planilla/configuracion')?1:0;
    	 
     	
    	$cuentas_pasivos = Capsule::select(Capsule::raw(
    			"SELECT id, CONCAT_WS(' ', IF(codigo != '', codigo, ''), IF(nombre != '', nombre, '')) AS nombre  FROM contab_cuentas
    		 		 WHERE empresa_id = :empresa_id1
    		 		 AND codigo <> ''
    		 		 AND nombre <> ''
    		 		 AND estado = 1
    		 		 AND id NOT IN (SELECT padre_id FROM cen_centros WHERE empresa_id = :empresa_id2) AND tipo_cuenta_id= :tipo_cuenta_id ORDER BY nombre ASC"), array(
    	    		 		 		'empresa_id1' => $this->empresa_id,
    	    		 		 		'empresa_id2' => $this->empresa_id,
    	    		 		 		'tipo_cuenta_id' => 2
    	    		 		 ));
    	$data['cuentas_pasivos'] = $cuentas_pasivos;
    	
     	$lista_deducciones =  Deducciones_orm::where('estado_id','=', 1 )->where('empresa_id','=', $this->empresa_id)->get();
    	$lista_deducciones = $lista_deducciones->toArray();
    	if(!empty($lista_deducciones)){
    		foreach($lista_deducciones as $valores){
    			$data['deducciones'][] = array('id'=>$valores['id'], 'nombre'=>$valores['nombre']);
    		}
    	} 
    	//Listado de Acumulados
    	$lista_acumulados =  Acumulados_orm::where('estado_id','=', 1 )->where('empresa_id','=', $this->empresa_id)->get();
    	$lista_acumulados = $lista_acumulados->toArray();
    	if(!empty($lista_acumulados)){
    		foreach($lista_acumulados as $valores){
    			$data['acumulados'][] = array('id'=>$valores['id'], 'nombre'=>$valores['nombre']);
    		}
    	} 
    	
       	//Variable para las Liquidaciones
    	$data['tipo_liquidaciones']     	= Catalogos_orm::where(array('identificador'=>'tipo_liquidaciones','activo'=>'1'))->get(array('id_cat','etiqueta'));
    	$data['pagos_aplicables_normales']  = Catalogos_orm::where(array('identificador'=>'pagos_aplicables_liquidaciones','activo'=>'1'))->get(array('id_cat','etiqueta'));
    	$data['pagos_aplicables_acumulados']= Catalogos_orm::where(array('identificador'=>'pagos_aplicables_liquidaciones_acumulado','activo'=>'1'))->get(array('id_cat','etiqueta'));
      	
       	 $this->assets->agregar_css(array(
     			'public/assets/css/default/ui/base/jquery-ui.css',
     			'public/assets/css/default/ui/base/jquery-ui.theme.css',
     			'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
     			'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
       			'public/assets/css/plugins/bootstrap/bootstrap-tagsinput.css',
     			'public/assets/css/plugins/jquery/fileinput/fileinput.css',
      	 		'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
      	 		'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
      	 		'public/assets/css/plugins/jquery/chosen/chosen.min.css'
     	));
     	 $this->assets->agregar_js(array(
    			'public/assets/js/default/jquery-ui.min.js',
    			'public/assets/js/plugins/jquery/jquery.sticky.js',
    			'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
    			'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
    			'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
    			'public/assets/js/plugins/jquery/jqgrid/plugins/jQuery.jqGrid.columnToggle.js',
    			'public/assets/js/plugins/jquery/switchery.min.js',
    			'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
    			'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
				'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
    			'public/assets/js/moment-with-locales-290.js',
    			'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
     	 		'public/assets/js/plugins/bootstrap/daterangepicker.js',
    			'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
    			'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
     	 		'public/assets/js/plugins/jquery/chosen.jquery.min.js',
     	 		'public/assets/js/default/formulario.js',
    			'public/assets/js/default/js.cookie.js',
     	 		'public/assets/js/default/tabla-dinamica.jquery.js',
     			'public/assets/js/modules/configuracion_planilla/configuracion.js', 
     	 		'public/assets/js/default/toast.controller.js'
     	)); 
      	   
      	 if(!is_null($this->session->flashdata('mensaje'))){
     	 	$mensaje = json_encode($this->session->flashdata('mensaje'));
     	 	 
     	 }else{
     	 	$mensaje = '';
     	 }
      	 if(!empty($this->session->flashdata('tab'))){
     	 	$tab =$this->session->flashdata('tab');
      	 }else{
      	 	$tabs_especial = explode ("?",$_SERVER['REQUEST_URI']);
       	 	$tab = isset($tabs_especial[1])?$tabs_especial[1]:'tab-1';
      	 }
     	 $this->assets->agregar_var_js(array(
     	 		"toast_mensaje" => $mensaje,
     	 		"tab" =>$tab
     	 ));
    	$breadcrumb = array(
    			"titulo" => '<i class="fa fa-gear"></i> Configuraci&oacute;n',
    			"ruta" => array(
    					0 => array(
    							"nombre" => "Planilla",
    							"activo" => false
    					),
    					1 => array(
    							"nombre" => '<b>Configuracion</b>',
    							"activo" => true
    					)
    			),
    			"filtro" => false,
    			"menu" =>  array()
     	);
     	
     	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar($breadcrumb);
    }
    
    
    public function acumulado_constructor($acumulado_uuid = NULL){
    	$data = array();

    	
    	if(!empty($_POST)){
    		 
    		$fieldset = $_POST;
    		$this->guardar_acumulado_constructor($fieldset);
    	}
    	 
    	
    	if($acumulado_uuid!=NULL){
    		//Cuando el codigo entra a la edicion
    		$acumulado_info = Acumulados_orm::with(array('cuenta_pasivo','contructores'))
    		->where(Capsule::raw("HEX(uuid_acumulado)"), "=", $acumulado_uuid)->get()->toArray();
     		$data["info"] = $acumulado_info[0];

     		$data["info"]['maximo_acumulable'] = number_format($acumulado_info[0]['maximo_acumulable'], 2);
     		$data['operadores'] =  Catalogos_orm::where('identificador','=', 'Operador' )->where('activo','=', 1)
    		->get();
    		
     		$data['tipo_calculo'] = field_enums('pln_config_acumulados_constructores', 'tipo_calculo_uno');
     		 
    	}	
    	
    	 
    	$data['cuentas_pasivos'] = Cuentas_orm::transaccionalesDeEmpresa($this->empresa_id)->deTipoDeCuenta([2])->activas()->get();
 	  
    	$this->assets->agregar_css(array(
    			'public/assets/css/default/ui/base/jquery-ui.css',
    			'public/assets/css/default/ui/base/jquery-ui.theme.css',
    			'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
    			'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
    			'public/assets/css/plugins/bootstrap/bootstrap-tagsinput.css',
    			'public/assets/css/plugins/jquery/fileinput/fileinput.css',
    			'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
    			'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
    			'public/assets/css/plugins/jquery/chosen/chosen.min.css'
    	));
    	$this->assets->agregar_js(array(
    			'public/assets/js/default/jquery-ui.min.js',
    			'public/assets/js/plugins/jquery/jquery.sticky.js',
    			'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
    			'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
    			'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
    			'public/assets/js/plugins/jquery/jqgrid/plugins/jQuery.jqGrid.columnToggle.js',
    			'public/assets/js/plugins/jquery/switchery.min.js',
    			'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
    			'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
    			'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
    			'public/assets/js/moment-with-locales-290.js',
    			'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
    			'public/assets/js/plugins/bootstrap/daterangepicker.js',
    			'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
    			'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
    			'public/assets/js/plugins/jquery/chosen.jquery.min.js',
    			'public/assets/js/default/formulario.js',
    			'public/assets/js/default/js.cookie.js',
    			'public/assets/js/default/tabla-dinamica.jquery.js',
    			'public/assets/js/modules/configuracion_planilla/acumulado_constructor.js' 
    	));
      	$this->assets->agregar_var_js(array(
    			"fecha_corte" => date("d/m/Y", strtotime($data["info"]['fecha_corte']))
    	));
      	$nombre_acumulado = isset($data["info"]['nombre'])?$data["info"]['nombre']:'';
       	$breadcrumb = array(
    			"titulo" => '<i class="fa fa-gear"></i> Configuraci&oacute;n: '.$nombre_acumulado,
    			"filtro" => false,
    			"menu" => array()
    	);
     
    	
     	$this->template->agregar_titulo_header('Colaboradores');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar();
    }
    public function deduccion_constructor($deduccion_uuid = NULL){
    	$data = array();
    
    	 
    	if(!empty($_POST)){
    		 
    		$fieldset = $_POST;
    		$this->guardar_deduccion_constructor($fieldset);
    	}
    
    	 
    	if($deduccion_uuid!=NULL){
    		//Cuando el codigo entra a la edicion
    		$deduccion_info = Deducciones_orm::with(array('cuenta_pasivo','contructores'))
    		->where(Capsule::raw("HEX(uuid_deduccion)"), "=", $deduccion_uuid)->get()->toArray();
    		$data["info"] = $deduccion_info[0];
    
     		 $data['cuando'] =  Catalogos_orm::where('identificador','=', 'Operador' )
    		->get();
    
    	 	$data['operadores'] = field_enums('pln_config_deducciones_constructores', 'operador');
    		$data['aplicar'] = field_enums('pln_config_deducciones_constructores', 'aplicar');
    
    		
    	}
    	 
    
    	 $data['cuentas_pasivos'] = Cuentas_orm::transaccionalesDeEmpresa($this->empresa_id)->deTipoDeCuenta([2])->activas()->get();
     	 
    	$this->assets->agregar_css(array(
    			'public/assets/css/default/ui/base/jquery-ui.css',
    			'public/assets/css/default/ui/base/jquery-ui.theme.css',
    			'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
    			'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
    			'public/assets/css/plugins/bootstrap/bootstrap-tagsinput.css',
    			'public/assets/css/plugins/jquery/fileinput/fileinput.css',
    			'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
    			'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
    			'public/assets/css/plugins/jquery/chosen/chosen.min.css'
    	));
    	$this->assets->agregar_js(array(
    			'public/assets/js/default/jquery-ui.min.js',
    			'public/assets/js/plugins/jquery/jquery.sticky.js',
    			'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
    			'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
    			'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
    			'public/assets/js/plugins/jquery/jqgrid/plugins/jQuery.jqGrid.columnToggle.js',
    			'public/assets/js/plugins/jquery/switchery.min.js',
    			'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
    			'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
    			'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
    			'public/assets/js/moment-with-locales-290.js',
    			'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
    			'public/assets/js/plugins/bootstrap/daterangepicker.js',
    			'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
    			'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
    			'public/assets/js/plugins/jquery/chosen.jquery.min.js',
    			'public/assets/js/default/formulario.js',
    			'public/assets/js/default/js.cookie.js',
//    			'public/assets/js/default/tabla-dinamica.jquery.js',
    			'public/assets/js/modules/configuracion_planilla/tabla-dinamica.jquery.js',
    			'public/assets/js/modules/configuracion_planilla/deduccion_constructor.js'
    	));
    	
    	 //echo '<pre>'; print_r($data); echo '</pre>';
    	 
    	
    	 $this->assets->agregar_var_js(array(
    			"colaborador_rata" => $data["info"]['rata_colaborador'],
    			"colaborador_simbolo" => $data["info"]['rata_colaborador_tipo'], 
    			"patrono_rata" => $data["info"]['rata_patrono'], 
    			"patrono_simbolo" =>  $data["info"]['rata_patrono_tipo']
    	));
    	 $nombre_deduccion = isset($data["info"]['nombre'])?$data["info"]['nombre']:'';
    	$breadcrumb = array(
    			"titulo" => '<i class="fa fa-gear"></i> Configuraci&oacute;n: '.$nombre_deduccion,
    			"filtro" => false,
    			"menu" => array()
    	);
    	 
    	 
    	$this->template->agregar_titulo_header('Colaboradores');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar();
    }
    
    private function guardar_deduccion_constructor($campos=NULL)
    {
    	 
    	/**
    	 * Inicializar Transaccion
    	 */
    	Capsule::beginTransaction();
    
    	try {
     		
     				$update_lista['nombre'] 		= $campos['nombre'];
    				$update_lista['cuenta_pasivo_id'] = $campos['cuenta_pasivo_id'];
    				
    				if(!empty($campos['rata_colaborador_tipo']))
    					$update_lista['rata_colaborador_tipo'] = $campos['rata_colaborador_tipo'];
    				
    				$update_lista['rata_colaborador'] = $campos['rata_colaborador'];
    				if(!empty($campos['rata_patrono_tipo']))
    					$update_lista['rata_patrono_tipo'] = $campos['rata_patrono_tipo'];
    				
    				$update_lista['rata_patrono'] =   $campos['rata_patrono'];
    				$update_lista['descripcion'] =   $campos['descripcion'];
    				$update_lista['estado_id'] =   $campos['estado_id'];
    				
     		Deducciones_orm::where('id',  $campos['deduccion_id'])->update($update_lista);
    		$deducciones  = Deducciones_orm::find($campos['deduccion_id']);
    
     		/**
    		 * Guardar Contructores
    		*/
    		$contructores = array();
    
    		/**
    		 * Verificar si existe datos en arreglo
    		*/
    		if(!empty( $campos['constructor']))
    		{
    			if(Util::is_array_empty($campos['constructor']) == false){
    				//Recorrer los dependientes
    
    				 
    
    				foreach ($campos['constructor'] AS $construct){
    					
     					$fieldset = array(
    							"cuando"=>$construct['cuando_id'],
    							"operador"=>$construct['operador_id'],
    							"monto"=>$construct['monto'],
    							"aplicar"=>$construct['aplicar'],
     							"deduccion_id"=> $campos['deduccion_id']
    					);
    					
    						
    					
    					 
    					if($construct['id']==0 || $construct['id']=='' )	{
    						$contructores[] = new Deducciones_contructores_orm($fieldset);
    					}
    					else{
    							
    							
    						Deducciones_contructores_orm::where("id", "=", $construct['id'])->update($fieldset);
    					}
    
    				}	 
    			}
    			if(!empty($contructores)){
    				$deducciones->contructores()->saveMany($contructores);
    			}
    		}
    		 
    	} catch(ValidationException $e){
    		 
    		// Rollback " <<Horas/Planilla>>";
    		Capsule::rollback();
    		$mensaje = array('tab'=>'tab-1','estado'=>500, 'mensaje'=>'<b>�Error! Su solicitud no fue procesada <<datos/configuraci&oacute;n>>.</b> ');
    		$this->session->set_flashdata('mensaje', $mensaje);
    		echo json_encode(array(
    				"response" => false,
    		));
    		exit;
    	}
    
     
    	Capsule::commit();
    	$mensaje = array('estado'=>200, 'tab'=>'tab-1','mensaje'=>'<b>&Eacute;xito!</b> Se ha actualizado correctamente los datos/configuraci&oacute;n.');
    	$this->session->set_flashdata('mensaje', $mensaje);
    	 
    	redirect(base_url('configuracion_planilla/configuracion'));
    	 
    }
    
    private function guardar_acumulado_constructor($campos=NULL)
    {
    	
    	 
    	/**
    	 * Inicializar Transaccion
    	*/
    	Capsule::beginTransaction();
    
    	try {
    		
    		$fecha_corte  = explode("/", $campos['fecha_corte']);
     		$fecha_corte = $fecha_corte[2].'-'.$fecha_corte[1].'-'.$fecha_corte[0];
    		
    		
     			Acumulados_orm::where('id',  $campos['acumulado_id'])->update([
    			'nombre' => $campos['nombre'],
    			'descripcion' => $campos['descripcion'],
    			//'tipo_acumulado' => $campos['tipo_acumulado'],
    			'cuenta_pasivo_id' => $campos['cuenta_pasivo_id'],
    			//'maximo_acumulable' => $campos['maximo_acumulable'],
    			//'fecha_corte' =>   $fecha_corte,
     			]);
     			$acumulados  = Acumulados_orm::find($campos['acumulado_id']);
    
    		/**
    		 * Guardar Contructores
    		 */
    		$contructores = array();
    
    		/**
    		 * Verificar si existe datos en arreglo
    		*/
    		if(!empty( $campos['constructor']))
    		{
    			if(Util::is_array_empty($campos['constructor']) == false){
    				
    				foreach ($campos['constructor'] AS $construct){
     					$fieldset = array(
    							//"operador_id"=>$construct['operador_id'],
    							"operador_valor"=>$construct['operador_valor'],
    							"tipo_calculo_uno"=>$construct['tipo_calculo_uno'],
    							"valor_calculo_uno"=>$construct['valor_calculo_uno'],
    							"tipo_calculo_dos"=>$construct['tipo_calculo_dos'],
    							"valor_calculo_dos"=>$construct['valor_calculo_dos'],
    							"acumulado_id"=> $campos['acumulado_id']
    					);
     					
     					
     					if($construct['id']==0 || $construct['id']=='' )	{
     						
       						$contructores[] = new Acumulados_contructores_orm($fieldset);
      					}
     					else{
     						
      						Acumulados_contructores_orm::where("id", "=", $construct['id'])->update($fieldset);
      					}
     					
    				}
    			}
      			
    			if(!empty($contructores)){
      				$acumulados->contructores()->saveMany($contructores);
    			}
    		}
     
     	} catch(ValidationException $e){
    		 
    		// Rollback " <<Horas/Planilla>>";
     		Capsule::rollback();
     		$mensaje = array('estado'=>500, 'mensaje'=>'<b>�Error! Su solicitud no fue procesada <<datos/configuraci&oacute;n>>.</b> ');
     		$this->session->set_flashdata('mensaje', $mensaje);
     		$this->session->set_flashdata('tab', 'tab-2');
     		echo json_encode(array(
     				"response" => false,
      		));
     		exit;
    	}
     	// If we reach here, then
    	// data is valid and working.
    	// Commit the queries!
    	//Capsule::commit();
    
    	//Redireccionar
    	//redirect(base_url('configuracion_planilla/acumulado-constructor/11e5efa0034c9cac839ebc5ff4a18b92'));
     	Capsule::commit();
     	$mensaje = array('estado'=>200,  'mensaje'=>'<b>&Eacute;xito!</b> Se ha actualizado correctamente los datos/configuraci&oacute;n.');
    	$this->session->set_flashdata('mensaje', $mensaje);
    	$this->session->set_flashdata('tab', 'tab-2');
    	
    	redirect(base_url('configuracion_planilla/configuracion'));
    	/*echo json_encode(array(
    			"response" => true,
    			//"mensaje" => "Se ha creado satisfactoriamente."
    	));
    	exit;*/
    }
    
    
    
    public function ajax_eliminar_constructor_acumulado() {
    	// Just Allow ajax request
    	if (! $this->input->is_ajax_request ()) {
    		return false;
    	}
    
    	/**
    	 * Inicializar Transaccion
    	 */
    	Capsule::beginTransaction();
    
    	try {
    		$id = $this->input->post ('id', true);
    		Acumulados_contructores_orm::where('id', $id)->delete();
    
    	} catch(ValidationException $e){
    		Capsule::rollback();
    		echo json_encode(array(
    				"response" => false,
    				"mensaje" => "Hubo un error tratando de eliminar el registro."
    		));
    		exit;
    	}
    	Capsule::commit();
    
    	echo json_encode(array(
    			"response" => true,
    			"mensaje" => "<b>&Eacute;xito!</b> Se ha eliminado el registro satisfactoriamente."
    	));
    	exit;
    }
    
    public function ajax_eliminar_pago_acumulado() {
    	// Just Allow ajax request
    	if (! $this->input->is_ajax_request ()) {
    		return false;
    	}
    
    	/**
    	 * Inicializar Transaccion
    	 */
    	Capsule::beginTransaction();
    
    	try {
    		$id = $this->input->post ('id', true);
    		
    		$pago = $this->configuracionPlanillaLiquidacionRepository->eliminarRegistroPago($id);
    
    	} catch(ValidationException $e){
    		Capsule::rollback();
    		echo json_encode(array(
    				"response" => false,
    				"mensaje" => "Hubo un error tratando de eliminar el registro."
    		));
    		exit;
    	}
    	Capsule::commit();
    
    	echo json_encode(array(
    			"response" => true,
    			"mensaje" => "<b>&Eacute;xito!</b> Se ha eliminado el registro satisfactoriamente."
    	));
    	exit;
    }
    
    
    public function ajax_eliminar_liquidacion() {
    	// Just Allow ajax request
    	if (! $this->input->is_ajax_request ()) {
    		return false;
    	}
    
    	/**
    	 * Inicializar Transaccion
    	 */
    	Capsule::beginTransaction();
    
    	try {
    		$id = $this->input->post ('id', true);
    
    		$pago = $this->configuracionPlanillaLiquidacionRepository->eliminarLiquidacion($id);
    
    	} catch(ValidationException $e){
    		Capsule::rollback();
    		echo json_encode(array(
    				"response" => false,
    				"mensaje" => "Hubo un error tratando de eliminar el registro."
    		));
    		exit;
    	}
    	Capsule::commit();
    
    	echo json_encode(array(
    			"response" => true,
    			"mensaje" => "<b>&Eacute;xito!</b> Se ha eliminado el registro satisfactoriamente."
    	));
    	exit;
    }
    
    
    public function ajax_listar_acumulados(){
    	//Just Allow ajax request
    	if(!$this->input->is_ajax_request()){
    		return false;
    	}
    	$clause = array();
    	 
    	 $clause["empresa_id"] = $this->empresa_id;
    	list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
    
    	$count = Acumulados_orm::listar($clause, NULL, NULL, NULL, NULL)->count();
    
    	list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
    
    	$rows = Acumulados_orm::listar($clause, $sidx, $sord, $limit, $start);
    	//Constructing a JSON
    	$response = new stdClass();
    	$response->page     = $page;
    	$response->total    = $total_pages;
    	$response->records  = $count;
    	$i=0;
    	if(!empty($rows->toArray())){
    		foreach ($rows->toArray() AS $i => $row){
    			
    			$uuid_acumulado = bin2hex($row['uuid_acumulado']);
    			$hidden_options =  "";
    			$hidden_options .= '<a href="#"  data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success editarAcumuladoBtn">Ver detalle</a>';
    			//$hidden_options .= '<a href="#"  data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success editarAcumuladoBtn">Constructor de expresiones</a>';
    			$hidden_options .= '<a href="'. base_url('configuracion_planilla/acumulado-constructor/'. $uuid_acumulado) .'" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success">Constructor de expresiones</a>';
    			 
    			$link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row['id'] .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
    			if ($row['estado_id'] == 1) {
    				$estado = '<span class="label" style="color:#fff; background-color:#5BB85C">Activo</span>';
    			} else {
    				$estado = '<span class="label" style="color:#fff; background-color:#F0AD4E">Inactivo</span>';
    			}
    			$nombre = Util::verificar_valor($row['nombre']);
    
    			$response->rows[$i]["id"] = $row['id'];
    			$response->rows[$i]["cell"] = array(
    					'1',
    					$nombre,
    					$row['cuenta_pasivo']['nombre'],
    					//'QUEMADO VALOR ACUMULADO ',
    					//'QUEMADO RATA ACTUAL',
    					$row['descripcion'],
    					$row['cuenta_pasivo_id'],
    					
     					
    					$estado,
    					$row['estado_id'],
    					date("d/m/Y", strtotime($row['fecha_corte'])),
      					$link_option,
    					$hidden_options,
    					number_format($row['maximo_acumulable'],2),
    					$row['tipo_acumulado']
    			);
    			$i++;
    		}
    	}
    
    	echo json_encode($response);
    	exit;
    }
     public function ajax_listar_deducciones(){
     	//Just Allow ajax request
     	if(!$this->input->is_ajax_request()){
     		return false;
     	}
     	$clause = array();
     	 
     	$clause["empresa_id"] = $this->empresa_id;
     	list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
     	
     	$count = Deducciones_orm::listar($clause, NULL, NULL, NULL, NULL)->count();
     	
     	list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
     	
     	$rows = Deducciones_orm::listar($clause, $sidx, $sord, $limit, $start);
      	//Constructing a JSON
     	$response = new stdClass();
     	$response->page     = $page;
     	$response->total    = $total_pages;
     	$response->records  = $count;
     	$i=0;
     	if(!empty($rows->toArray())){
     		foreach ($rows->toArray() AS $i => $row){
     		//	echo '<pre>'; print_r($row); echo '</pre>';
     			$uuid_deduccion = bin2hex($row['uuid_deduccion']);
     			$hidden_options =  "";
     			$hidden_options .= '<a href="#"  data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success editarDeduccionBtn">Ver detalle</a>';
     			//$hidden_options .= '<a href="'. base_url('configuracion_planilla/deduccion-constructor/'. $uuid_deduccion) .'" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success">Constructor de expresiones</a>';
     			
     			$link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row['id'] .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
     			if ($row['estado_id'] == 1) {
     				$estado = '<span class="label" style="color:#fff; background-color:#5BB85C">Activo</span>';
     			} else {
     				$estado = '<span class="label" style="color:#fff; background-color:#F0AD4E">Inactivo</span>';
     			}
     			
     			$rata_colaborador = ($row['rata_colaborador_tipo'] == 'Porcentual')?$row['rata_colaborador'].'%':' $'.$row['rata_colaborador'];
     			$rata_patono 	  = ($row['rata_patrono_tipo'] == 'Porcentual')?$row['rata_patrono'].'%':' $'.$row['rata_patrono'];
     			$nombre = Util::verificar_valor($row['nombre']);
     	
     			$response->rows[$i]["id"] = $row['id'];
     			$response->rows[$i]["cell"] = array(
     					$row['id'],
     					$nombre,
      					$row['cuenta_pasivo']['nombre'],
     					$rata_colaborador,
     					$rata_patono,
     					$estado,
     					$row['estado_id'],
     					$row['descripcion'],
     					$row['cuenta_pasivo_id'],
     					$link_option,
     					$hidden_options,
     					$row['rata_colaborador_tipo'],
     					$row['rata_colaborador'],
     					$row['rata_patrono'],
     					$row['rata_patrono_tipo']
     			);
     			$i++;
     		}
     	}
     	
     	echo json_encode($response);
     	exit;
     }
     public function ajax_listar_recargos(){
 
    	//Just Allow ajax request
    	if(!$this->input->is_ajax_request()){
    		return false;
    	}
    	$clause = array();
        $clause["empresa_id"] = $this->empresa_id;
        
     	list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
    	 
    	$count = Recargos_orm::listar($clause, NULL, NULL, NULL, NULL)->count();
    	 
    	list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
    
    	$rows = Recargos_orm::listar($clause, $sidx, $sord, $limit, $start);
    
    	//Constructing a JSON
    	$response = new stdClass();
    	$response->page     = $page;
    	$response->total    = $total_pages;
    	$response->records  = $count;
    	$i=0;
    	if(!empty($rows->toArray())){
    		foreach ($rows->toArray() AS $i => $row){
    			
    			$hidden_options =  "";
     		    $hidden_options .= '<a href="#"  data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success editarRecargoBtn">Ver detalle</a>';
    			
      			$link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-nombre="'. $row['nombre'] .'" data-id="'. $row['id'] .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
     			if ($row['estado_id'] == 1) {
     				$estado = '<span class="label" style="color:#fff; background-color:#5BB85C">Activo</span>';
     			} else {
     				$estado = '<span class="label" style="color:#fff; background-color:#F0AD4E">Inactivo</span>';
     			}
    			$nombre = Util::verificar_valor($row['nombre']);
    			 
    			$response->rows[$i]["id"] = $row['id'];
    			$response->rows[$i]["cell"] = array(
    					$row['id'],
    					$nombre,
    					$row['porcentaje_hora'],
    					$row['descripcion'],
    					$estado,
    					$row['estado_id'],
      					$link_option,
    					$hidden_options
    			);
    			$i++;
    		}
    	}
    
    	echo json_encode($response);
    	exit;
    	 
    }
     
      
    
      public function ajax_listar_diasferiados()
    {
    	//Just Allow ajax request
    	if(!$this->input->is_ajax_request()){
    		return false;
    	}
    	$uuid_empresa = $this->session->userdata('uuid_empresa');
    	$empresa = Empresa_orm::findByUuid($uuid_empresa);
    	//$empresa_id= $this->empresa_id;
    	list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
    	$count = Diasferiados_orm::listar($this->empresa_id)->count();
    	 
    	list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
    	$rows = Diasferiados_orm::listar($this->empresa_id, $sidx, $sord, $limit, $start);
     	$response = new stdClass();
    	$response->page     = $page;
    	$response->total    = $total_pages;
    	$response->records  = $count;
    	$i=0;
     	if(!empty($rows->toArray())){
    		foreach ($rows->toArray() AS $i => $row){
    			$data_deduccion = $data_acumulada = array();
    			
    			$data_acumulada = Diasferiados_acumulados_orm::DiasFeriados_Acumulados($row ['id']);
    			$data_deduccion = Diasferiados_deducciones_orm::DiasFeriados_Deducciones($row ['id']);
    			
     			$hidden_options = "";
     			$hidden_options .= '<a href="#"  data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success editarFechaBtn">Ver detalle</a>';
    			
     			if ($row['estado_id'] == 1) {
     				$estado = '<span class="label" style="color:#fff; background-color:#5BB85C">Activo</span>';
     			} else {
     				$estado = '<span class="label" style="color:#fff; background-color:#F0AD4E">Inactivo</span>';
     			} 
   
     			$link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row['id'] .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
    			$response->rows[$i]["id"] = $row['id'];
    			$response->rows[$i]["cell"] = array(
    					$row['nombre'],
      					date("d/m/Y", strtotime($row['fecha_oficial'])),
     					$row['cuenta_pasivo']['nombre'],
     					$row['cuenta_pasivo_id'],
    					$row['horas_no_laboradas'], 
    					$estado,
    					$row['estado_id'],
     					$link_option,
    					$hidden_options,
    					$row['descripcion'],
    					serialize($data_acumulada['acumulados']),
    					serialize($data_deduccion['deducciones'])
    					
    			);
    			$i++;
    		}
    	}
    	echo json_encode($response);
    	exit;
    }
     
    public function ajax_listar_beneficios()
    {
    	//Just Allow ajax request
    	if(!$this->input->is_ajax_request()){
    		return false;
    	}
        
        $clause = array();
    	 
    	 $clause["empresa_id"] = $this->empresa_id;
         
    	list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
    	$count = Beneficios_orm::listar($clause)->count();
    	 
    	list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
    	$rows = Beneficios_orm::listar($clause, $sidx, $sord, $limit, $start);
    
    	$response = new stdClass();
    	$response->page     = $page;
    	$response->total    = $total_pages;
    	$response->records  = $count;
    	$i=0;
    	
    	if(!empty($rows->toArray())){
    		foreach ($rows->toArray() AS $i => $row){
    			$data_deduccion = $data_acumulada = array();
    			
    			$data_acumulada = Beneficios_acumulados_orm::Beneficios_Acumulados($row ['id']);
    			$data_deduccion = Beneficios_Deducciones_orm::Beneficios_Deducciones($row ['id']);
    			
    			$hidden_options = "";
    			//if( Auth::has_permission("acceso","oportunidades/crear-oportunidad/(:any)") ){
    			$hidden_options = '<a href="#"  data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success editarBeneficioBtn">Ver detalle</a>';
    			//}
    			if ($row['estado_id'] == 1) {
    				$estado = '<span class="label" style="color:#fff; background-color:#5BB85C">Activo</span>';
    			} else {
    				$estado = '<span class="label" style="color:#fff; background-color:#F0AD4E">Inactivo</span>';
    			}
    			
    			$link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row['id'] .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
    			$response->rows[$i]["id"] = $row['id'];
    			$response->rows[$i]["cell"] = array(
    					$row['nombre'],
    					$row['descripcion'],
    					$row['cuenta_pasivo']['nombre'],
    					$row['cuenta_pasivo_id'],
    					$row['modificador_actual'],
    					$estado,
    					$row['estado_id'],
    					$link_option,
    					$hidden_options,
    					serialize($data_acumulada['acumulados']),
    					serialize($data_deduccion['deducciones'])
    			);
    			$i++;
    		}
    	}
    	echo json_encode($response);
    	exit;
    }
    
    public function ajax_listar_liquidaciones()
    {
     	//Just Allow ajax request
    	if(!$this->input->is_ajax_request()){
    		return false;
    	}

     	$clause["empresa_id"] = $this->empresa_id;
    	
    	list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
    	$count = $this->configuracionPlanillaLiquidacionRepository->lista_totales($clause);
    	
     	list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
      	$liquidaciones = $this->configuracionPlanillaLiquidacionRepository->listar($clause, $sidx, $sord, $limit, $start);
     	$response = new stdClass();
    	$response->page     = $page;
    	$response->total    = $total_pages;
    	$response->records  = $count;
    	$i=0;
    	
     	 
    	if(!empty($liquidaciones->toArray())){
    		foreach ($liquidaciones->toArray() AS $i => $row){
    			
    			
    			$pagos_aplicables = $this->configuracionPlanillaLiquidacionRepository->separando_array($row['pagos_aplicables']);
    			$acumulados_aplicables = $this->configuracionPlanillaLiquidacionRepository->separando_array($row['pagos_acumulados']);
     			
    			if ($row['estado_id'] == 1) {
    				//$estado = '<h1><span class="label " style="color:#fff; background-color:#5BB85C">Activo</span></h1>';
    				$estado = '<span style="color:white; background-color:#5CB85C" class="btn btn-xs btn-block">Activo</span>';
    			} else {
    				$estado = '<span style="color:white; background-color:#F0AD4E" class="btn btn-xs btn-block">Inactivo</span>';
    				//$estado = '<h1><span class="label" style="color:#fff; background-color:#F0AD4E">Inactivo</span></h1>';
    			}
     			/*$hidden_options = "";
    			//if( Auth::has_permission("acceso","oportunidades/crear-oportunidad/(:any)") ){*/
    				$hidden_options = '<a href="#"  data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success editarLiquidacionBtn">Ver detalle</a>';
    			//}
    			$hidden_options .= '<a href="#"    data-id="' . $row ['id'] . '" class="btn btn-block btn-outline btn-success" id="confirmarEliminar" type="button">Eliminar</a>';
     				
    			 
     			$link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row['id'] .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
    			$response->rows[$i]["id"] = $row['id'];
    			$response->rows[$i]["cell"] = array(
    					$row['id'],
    					$row['tipo_liquidacion']['etiqueta'],
    					$pagos_aplicables,
    					$acumulados_aplicables,
    					$estado,
    					$link_option,
    					$hidden_options,
    			);
    			$i++;
    		}
    	}
    	echo json_encode($response);
    	exit;
    }
     function ajax_duplicar_diasferiados() {
    	if (! $this->input->is_ajax_request ()) {
    		return false;
    	}
    	 
    	$uuid_empresa = $this->session->userdata('uuid_empresa');
    	$empresa = Empresa_orm::findByUuid($uuid_empresa);
    	$uuid_usuario = $this->session->userdata('huuid_usuario');
    	$usuario = Usuario_orm::findByUuid($uuid_usuario);
    	 
    	/**
    	 * Inicializar Transaccion
    	*/
    	Capsule::beginTransaction();
    	 
    	try {
    		$actualAnio = date("Y") ;
    		$lastYear = date("Y") - 1 ;
    			
    		$desde =  $actualAnio.'-01-01';
    		$hasta =  $actualAnio.'-12-31';
    		
    		$resultado = Diasferiados_orm::where('fecha_oficial','>=', $desde )
    		->where("fecha_oficial", "<=", $hasta)
    		->where("empresa_id", "=", $empresa->id)
    		->where("semilla", "=", 0)
    		->delete();
    		
    		
    		
 	    	$lista_dias_feriados =  Diasferiados_orm::where('semilla','=', 1 )
	    	->get();
	    	$dias_feriados = $lista_dias_feriados->toArray();
	    	if(!empty($dias_feriados)){
	    		foreach($dias_feriados as $valores){
	    			 
	    			
	    			$dia_bd = explode("-",$valores["fecha_oficial"]);
	    			$fecha_nueva = $actualAnio."-".$dia_bd[1]."-".$dia_bd[2];
	    			
	    			$fieldset["nombre"] = $valores['nombre'];
	    			$fieldset["descripcion"] = $valores['descripcion'];
	    			$fieldset["fecha_oficial"] = $fecha_nueva;
 	    			$fieldset["horas_no_laboradas"] = $valores['horas_no_laboradas'];
 	    			$fieldset["empresa_id"] = $empresa->id;
	    			$fieldset["estado_id"] = 1;
	    			$fieldset["creado_por"] = $usuario->id;
	    			$fieldset["semilla"] = 0;
	    			
	    			$diasFeriadosCreados = Diasferiados_orm::create($fieldset); 
 	    		}
	    		
	    	}
 	    	 
    	} catch(ValidationException $e){
    		 
    		// Rollback
    		Capsule::rollback();
    	
    		echo json_encode(array(
    				"result" => false,
    				"mensaje" => "Hubo un error tratando de crear los dias feriados."
    		));
    		exit;
    	
    	}
    	
    	// If we reach here, then
    	// data is valid and working.
    	// Commit the queries!
    	Capsule::commit();
    	
    	echo json_encode(array(
    			"result" => true,
    			"mensaje" => 'Se ha creado exitosamente los dias feriados.'
    	));
    	exit;
     }
     
     
    public function ajax_crear_deduccion() {
    	if (! $this->input->is_ajax_request ()) {
    		return false;
    	}
    	/**
    	 * Inicializar Transaccion
    	 */
    	Capsule::beginTransaction();
    	 
    	try {
    		
    		 
    		//Obtener el id de usuario de session
    		$uuid_usuario = $this->session->userdata('huuid_usuario');
    		$usuario = Usuario_orm::findByUuid($uuid_usuario);
    		 
    		//Obtener el id_empresa de session
    		$uuid_empresa = $this->session->userdata('uuid_empresa');
    		$empresa = Empresa_orm::findByUuid($uuid_empresa);
    		$cuenta_pasivo_id = $this->input->post('cuenta_pasivo_id', true);
    		 
    		if( $this->input->post ('id_deduccion', true)  == 0)  //Si se esta creando uno nuevo
    		{
    			$fieldset = array(
    					"nombre" 			=>  $this->input->post ('nombre', true),
    					"cuenta_pasivo_id" 	=>   isset($cuenta_pasivo_id)?$cuenta_pasivo_id:0, 
    					"rata_colaborador" 	=>  $this->input->post ('rata_colaborador', true),
    					"rata_colaborador_tipo"	=>  $this->input->post ('rata_colaborador_tipo', true),
    					"rata_patrono" 		=>  $this->input->post ('rata_patrono', true),
    					"rata_patrono_tipo" =>  $this->input->post ('rata_patrono_tipo', true),
    					"descripcion" 		=>  $this->input->post ('descripcion', true),
     					"estado_id" 		=>  $this->input->post ('estado_id', true),
    					"empresa_id" 		=>  $this->empresa_id,
    					"creado_por" 		=>  $usuario->id,
    					"uuid_deduccion" 	=> 	Capsule::raw("ORDER_UUID(uuid())")
     			);
    			Deducciones_orm::create($fieldset);
    			$mensaje = "Se ha creado exitosamente la deducci&oacute;n";
    		}
    		else if( $this->input->post ('id_deduccion', true)  > 0 ) //Si se esta editando}
    		{
    			$id	 			= $this->input->post('id_deduccion', true); //Nuevo o Edicion
    			
    			$nombre	 		= $this->input->post('nombre', true); //Nuevo o Edicion
     			$descripcion	= $this->input->post('descripcion', true); //Nuevo o Edicion
     			$estado_id= $this->input->post('estado_id', true); //Nuevo o Edicion
    	
     			$rata_colaborador 	= $this->input->post ('rata_colaborador', true);
     			$rata_colaborador_tipo	=  $this->input->post ('rata_colaborador_tipo', true);
     			$rata_patrono 		=  $this->input->post ('rata_patrono', true);
     			$rata_patrono_tipo =  $this->input->post ('rata_patrono_tipo', true);
     			
    			Deducciones_orm::where('id', $id)->update([
    			'nombre' => $nombre,
    			'cuenta_pasivo_id' =>  isset($cuenta_pasivo_id)?$cuenta_pasivo_id:0, 
    			'rata_colaborador' => $rata_colaborador,
    			'rata_colaborador_tipo' => $rata_colaborador_tipo,
    			'rata_patrono' => $rata_patrono,
    			'rata_patrono_tipo' => $rata_patrono_tipo,
    			'descripcion' => $descripcion,
     			'estado_id' => $estado_id
    			]);
    			 
    			$mensaje = "Se ha editado exitosamente la deducci&oacute;n.";
    		}
    		 
    	} catch(ValidationException $e){
    		 
     		Capsule::rollback();
    		 
    		echo json_encode(array(
    				"response" => false,
    				"mensaje" => "Hubo un error tratando de actualizar la deducci&oacute;n."
    		));
    		exit;
    	}
    	
    	Capsule::commit();
    	 
    	echo json_encode(array(
    			"response" => true,
    			"mensaje" => $mensaje
    	));
    	exit;
    }
    
    public function ajax_crear_acumulado() {
    	if (! $this->input->is_ajax_request ()) {
    		return false;
    	}
    	/**
    	 * Inicializar Transaccion
    	 */
    	Capsule::beginTransaction();
    
    	try {
    		//Obtener el id de usuario de session
    		$uuid_usuario = $this->session->userdata('huuid_usuario');
    		$usuario = Usuario_orm::findByUuid($uuid_usuario);
    		 
    		//Obtener el id_empresa de session
    		$uuid_empresa = $this->session->userdata('uuid_empresa');
    		$empresa = Empresa_orm::findByUuid($uuid_empresa);
    		
    		//$fecha_corte = explode("/",$this->input->post ('fecha_corte', true));
    		//$fecha_corte = $fecha_corte[2]."-".$fecha_corte[1]."-".$fecha_corte[0];
    		 
    		if( $this->input->post ('id_acumulado', true)  == 0)  //Si se esta creando uno nuevo
    		{
     			$fieldset = array(
    					"nombre" 		=>  $this->input->post ('nombre', true),
    					"descripcion" 	=>  $this->input->post ('descripcion', true),
    					//"tipo_acumulado" 	=>  $this->input->post ('tipo_acumulado', true),
    					"cuenta_pasivo_id" 	=>  $this->input->post ('cuenta_pasivo_id', true),
    					//"maximo_acumulable" 		=>  $this->input->post ('maximo_acumulable', true),
    					//"fecha_corte" 		=> $fecha_corte,
    					"estado_id" 		=>  $this->input->post ('estado_id', true),
    					"empresa_id" 		=>  $this->empresa_id,
     					"uuid_acumulado" 	=> 	Capsule::raw("ORDER_UUID(uuid())"),
    					"creado_por" 		=>  $usuario->id,
    			);
    			Acumulados_orm::create($fieldset);
    			$mensaje = "Se ha creado exitosamente el acumulado";
    		}
    		else if( $this->input->post ('id_acumulado', true)  > 0 ) //Si se esta editando}
    		{
    			$id	 				= $this->input->post('id_acumulado', true); //Nuevo o Edicion
    			 
    			$nombre	 			= $this->input->post('nombre', true); //Nuevo o Edicion
    			$descripcion		= $this->input->post('descripcion', true); //Nuevo o Edicion
     			//$tipo_acumulado	 			= $this->input->post('tipo_acumulado', true); //Nuevo o Edicion
    			$cuenta_pasivo_id	= $this->input->post('cuenta_pasivo_id', true); //Nuevo o Edicion
    			//$maximo_acumulable	= $this->input->post('maximo_acumulable', true); //Nuevo o Edicion
    			//$fecha_corte		= $fecha_corte; //Nuevo o Edicion
    			$estado_id			= $this->input->post('estado_id', true); //Nuevo o Edicion
    			 
    			Acumulados_orm::where('id', $id)->update([
    			'nombre' => $nombre,
    			'descripcion' => $descripcion,
    			//'tipo_acumulado' => $tipo_acumulado,
    			'cuenta_pasivo_id' => $cuenta_pasivo_id,
    			//'maximo_acumulable' => $maximo_acumulable,
    			//'fecha_corte' => $fecha_corte,
     			'estado_id' => $estado_id
    			]);
    
    			$mensaje = "Se ha editado exitosamente el acumulado";
    		}
    		 
    	} catch(ValidationException $e){
    		 
    		// Rollback
    		Capsule::rollback();
    		 
    		echo json_encode(array(
    				"response" => false,
    				"mensaje" => "Hubo un error tratando de actualizar el acumulado"
    		));
    		exit;
    	}
    	 
    	Capsule::commit();
    
    	echo json_encode(array(
    			"response" => true,
    			"mensaje" => $mensaje
    	));
    	exit;
    }
    public function ajax_crear_recargo() {
    	
    	if (! $this->input->is_ajax_request ()) {
    		return false;
    	}
    	/**
    	 * Inicializar Transaccion
    	 */
    	Capsule::beginTransaction();
    	
    	try {
    		//Obtener el id de usuario de session
    		$uuid_usuario = $this->session->userdata('huuid_usuario');
    		$usuario = Usuario_orm::findByUuid($uuid_usuario);
    	
    		//Obtener el id_empresa de session
    		$uuid_empresa = $this->session->userdata('uuid_empresa');
    		$empresa = Empresa_orm::findByUuid($uuid_empresa);
    	
     	 
     		if( $this->input->post ('id_recargo', true)  == 0)  //Si se esta creando uno nuevo
    		{
    			$fieldset = array(
    					"nombre" 		=>  $this->input->post ('nombre', true),
    					"descripcion" 	=>  $this->input->post ('descripcion', true),
    					"porcentaje_hora" 	=>  $this->input->post ('porcentaje_hora', true),
    					"estado_id" 		=>  $this->input->post ('estado_id', true),
    					"empresa_id" 		=>  $this->empresa_id,
     					"creado_por" 	=>  $usuario->id,
    	
    			);
    			Recargos_orm::create($fieldset);
    			$mensaje = "Se ha creado exitosamente el dia recargo";
    		}
    		else if( $this->input->post ('id_recargo', true)  > 0 ) //Si se esta editando}
    		{
    			  	$id	 			= $this->input->post('id_recargo', true); //Nuevo o Edicion
			    	$nombre	 		= $this->input->post('nombre', true); //Nuevo o Edicion
			    	$descripcion	= $this->input->post('descripcion', true); //Nuevo o Edicion
			    	$porcentaje_hora= $this->input->post('porcentaje_hora', true); //Nuevo o Edicion
			    	$estado_id= $this->input->post('estado_id', true); //Nuevo o Edicion
 			    	 
			    	Recargos_orm::where('id', $id)->update([
			    			'nombre' => $nombre,
			    			'descripcion' => $descripcion,
			    			'porcentaje_hora' => $porcentaje_hora,
			    			'estado_id' => $estado_id
			    		]);
			    		
    			$mensaje = "Se ha editado exitosamente el recargo.";
    		}
    		 
    	} catch(ValidationException $e){
    	
    		// Rollback
    			Capsule::rollback();
    			
    			echo json_encode(array(
    					"response" => false,
    					"mensaje" => "Hubo un error tratando de actualizar el recargo."
    			));
    			exit;
    	}
     	
    	Capsule::commit();
    	
    	echo json_encode(array(
    			"response" => true,
    			"mensaje" => $mensaje
    	));
    	exit;
    	 
    }
    
    public function ajax_crear_liquidacion() {
    	
     
     	if (! $this->input->is_ajax_request ()) {
    		return false;
    	}
    	 
     	 
    	 
    	/**
    	 * Inicializar Transaccion
    	 */
    	Capsule::beginTransaction();
    	 
    	try {
    		
    	 
     		$uuid_usuario = $this->session->userdata('huuid_usuario');
    		$usuario = Usuario_orm::findByUuid($uuid_usuario);
    		
     		$uuid_empresa = $this->session->userdata('uuid_empresa');
    		$empresa = Empresa_orm::findByUuid($uuid_empresa);
    		
    		$input = Illuminate\Http\Request::createFromGlobals();
    		//$liquidacion = $input->input("liquidacion");
    		
    		$general = $input->input("liquidacion");
    		$pagos_normales= $input->input("liquidacion_pago");
    		$pagos_acumulados  = $input->input("liquidacion_acumulado");
    		
     		 
    		 
      		if( $general['id']  == 0)  //Si se esta creando uno nuevo
    		{
    			 
     			if(  empty($pagos_normales)   && empty($pagos_acumulados) ){
    				echo json_encode(array(
    						"response" => false,
    						"mensaje" => "Hubo un error tratando de crear la liquidaci&oacute;n. Informacion incompleta."
    				));
    				exit;
    			}else{
      				 $data = array(
    				 		'general'	=>  array_merge( $general, array('empresa_id'=>$this->empresa_id,'creado_por'=>$usuario->id)),
    				 		'normales'	=> $pagos_normales,
    				 		'acumulados'=> $pagos_acumulados
    				 );
       				 
     				$modelLiquidacion = $this->configuracionPlanillaLiquidacionRepository->create($data);
    				$mensaje = "<b>&Eacute;xito!</b> Se ha creado correctamente los datos/Liquidaci&oacute;n";
    			}
     			
    		}
    		else if(  $general['id']  > 0 ) //Si se esta editando}
    		{
    			
       			$data = array(
      					'general'	=>  array_merge( $general, array('empresa_id'=>$this->empresa_id,'creado_por'=>$usuario->id)),
      					'normales'	=> $pagos_normales,
      					'acumulados'=> $pagos_acumulados
      			);
      			 $modelLiquidacion = $this->configuracionPlanillaLiquidacionRepository->update($data);
    			 
     			$mensaje = "<b>&Eacute;xito!</b> Se ha actualizado correctamente los datos/Liquidaci&oacute;n";
    		}
    		 
    	} catch(ValidationException $e){
    		 
    		// Rollback
    		log_message('error', __METHOD__." ->". ", Linea: ". __LINE__ ." --> ". $e->getMessage()."\r\n");
    		Capsule::rollback();
    		 
    		echo json_encode(array(
    				"response" => false,
    				"mensaje" => "Hubo un error tratando de actualizar  la liquidaci&oacute;n."
    		));
    		exit;
    	}
    
    	Capsule::commit();
    	 
    	echo json_encode(array(
    			"response" => true,
    			"mensaje" => $mensaje
    	));
    	exit;
    
    }
    public function ajax_crear_beneficio() {
	    	// Just Allow ajax request
	    	if (! $this->input->is_ajax_request ()) {
	    		return false;
	    	}
	 
	    	$uuid_empresa = $this->session->userdata('uuid_empresa');
	    	$empresa = Empresa_orm::findByUuid($uuid_empresa);
 	    	
	    	/**
	    	 * Inicializar Transaccion
	    	 */
	    	Capsule::beginTransaction();
	    	
	    	try {
	    		 
	    		//Obtener el id de usuario de session
	    		$uuid_usuario = $this->session->userdata('huuid_usuario');
	    		
 	    		$usuario = Usuario_orm::findByUuid($uuid_usuario);
	    		 
	    		//Obtener el id_empresa de session
	    		$uuid_empresa = $this->session->userdata('uuid_empresa');
	    		$empresa = Empresa_orm::findByUuid($uuid_empresa);
	    		$id = $this->input->post ('id_beneficio', true);
	    		$acumulados = $this->input->post ('acumulados', true);
	    		$deducciones = $this->input->post ('deducciones', true);
	    		$cuenta_pasivo_id = $this->input->post ('cuenta_pasivo_id', true);
	    		
	    		if( $id == 0)  //Si se esta creando uno nuevo
	    		{
	    			$fieldset = array(
	    					"nombre" 		=>  $this->input->post ('nombre', true),
	    					"descripcion" 	=>  $this->input->post ('descripcion', true),
	    					"empresa_id" 		=>  $this->empresa_id,
	    					"modificador_actual" 	=>  $this->input->post ('modificador_actual', true),
	    					"estado_id" 		=>   $this->input->post ('estado_id', true),
	    					"cuenta_pasivo_id" 		=>  isset($cuenta_pasivo_id)?$cuenta_pasivo_id:0,
	    					"creado_por" 	=>  $usuario->id,
	    					
	    			);
 	    			$beneficio = Beneficios_orm::create($fieldset);
 	    			$mensaje = "Se ha creado exitosamente el benficio.";
	    		}	
	    		else if( $id  > 0 ) //Si se esta editando}
	    		{
	    			
  	    			$nombre	 = $this->input->post ('nombre', true);
	    			$descripcion	 = $this->input->post ('descripcion', true);
	    			$modificador_actual	 		 = $this->input->post ('modificador_actual', true);
	    			$estado_id	 		 = $this->input->post ('estado_id', true);
	    			
	    			$beneficio = Beneficios_orm::where('id', $id)->update([
	    				'nombre' => $nombre,
	    				'descripcion' => $descripcion,
	    				'modificador_actual' => $modificador_actual, 
	    				'estado_id' => $estado_id, 
	    				'cuenta_pasivo_id' => isset($cuenta_pasivo_id)?$cuenta_pasivo_id:0, 
	    			]);
	    			$mensaje = "Se ha editado exitosamente el benficio.";
 	    		}
 	    	
 	    		
 	    		//Operacion con los acumulados
 	    		if( count($acumulados)>0 )
 	    		{
 	    			 
 	    			if( $id  == 0)  //Si se esta creando uno nuevo
 	    			{
 	    				if(Util::is_array_empty($acumulados) == false){
 	    					foreach ($acumulados AS $acumulado){
 	    						$fieldset["id_acumulado"] = $acumulado;
 	    						$fieldset["fecha_creacion"] = date('Y-m-d H:i:s');
 	    		
 	    						$_acumulados[] = new Beneficios_acumulados_orm($fieldset);
 	    					}
 	    				}
  	    				$beneficio->acumulados()->saveMany($_acumulados);
 	    			}
 	    			else{
 	    				if(Util::is_array_empty($acumulados) == false){
 	    					foreach ($acumulados AS $acumulado){
 	    						$fieldset["id_acumulado"] = $acumulado;
 	    						$fieldset["id_beneficio"] = $id;
 	    						$fieldset["fecha_creacion"] = date('Y-m-d H:i:s');
 	    		
 	    						$_acumulados[] = new Beneficios_acumulados_orm($fieldset);
 	    					}
 	    				}
 	    		
 	    				$beneficios = new Beneficios_acumulados_orm();
 	    				Beneficios_acumulados_orm::where('id_beneficio', $id)->delete();
 	    				$beneficios->acumulados()->saveMany($_acumulados);
 	    			}
 	    		}else{
 	    			Beneficios_acumulados_orm::where('id_beneficio', $id)->delete();
 	    		}
 	    		 
 	    		//Operacion con las deducciones
 	    		if( count($deducciones)>0 )
 	    		{
 	    			 
 	    			if( $this->input->post('id_beneficio', true)  == 0)  //Si se esta creando uno nuevo
 	    			{
 	    				if(Util::is_array_empty($deducciones) == false){
 	    					foreach ($deducciones AS $deduccion){
 	    						$fieldset["id_deduccion"] = $deduccion;
 	    						$fieldset["fecha_creacion"] = date('Y-m-d H:i:s');
 	    						 
 	    						$_deducciones[] = new Beneficios_deducciones_orm($fieldset);
 	    					}
 	    				}
 	    				$beneficio->deducciones()->saveMany($_deducciones);
 	    			}
 	    			else{
 	    				if(Util::is_array_empty($deducciones) == false){
 	    					foreach ($deducciones AS $deduccion){
 	    						$fieldset["id_deduccion"] = $deduccion;
 	    						$fieldset["id_beneficio"] = $id;
 	    						$fieldset["fecha_creacion"] = date('Y-m-d H:i:s');
 	    						 
 	    						$_deducciones[] = new Beneficios_deducciones_orm($fieldset);
 	    					}
 	    				}
  	    				$beneficios = new Beneficios_deducciones_orm();
 	    				Beneficios_deducciones_orm::where('id_beneficio', $id)->delete();
 	    				$beneficios->deducciones()->saveMany($_deducciones);
 	    			}
 	    		}else{
 	    			Beneficios_deducciones_orm::where('id_beneficio', $id)->delete();
 	    		}
 	    		
	    	} catch(ValidationException $e){
	    		// Rollback
	    		Capsule::rollback();
	    		 
	    		echo json_encode(array(
	    				"response" => false,
	    				"mensaje" => "Hubo un error tratando de actualizar el beneficio."
	    		));
	    		exit;
 	    	}
	    	 
 	    	Capsule::commit();
    	
	    	echo json_encode(array(
	    			"response" => true,
	    			"mensaje" => $mensaje
	    	));
	    	exit;
    }
       
    public function ajax_crear_diaferiado() {
    	// Just Allow ajax request
    	if (! $this->input->is_ajax_request ()) {
    		return false;
    	}

    	Capsule::beginTransaction();
    
    	try {
     		//Obtener el id de usuario de session
    		$uuid_usuario = $this->session->userdata('huuid_usuario');
    		 
    		$usuario = Usuario_orm::findByUuid($uuid_usuario);
    
    		//Obtener el id_empresa de session
    		$uuid_empresa = $this->session->userdata('uuid_empresa');
    		$empresa = Empresa_orm::findByUuid($uuid_empresa);
    		
     		$_date = $this->input->post ('fecha_fecha_oficial', true);
    		$_date = str_replace('/', '-', $_date);
      		 
    		$_date = date("Y-m-d", strtotime( $_date ));
     	
    		$acumulados = $this->input->post ('acumulados', true);
    		$deducciones = $this->input->post ('deducciones', true);
    		//$id_diaferiado =  $this->input->post('id_diaferiado', true);
    		$id 				= $this->input->post ('id_diaferiado', true);
    		
    		if( $this->input->post('id_diaferiado', true)  == 0)  //Si se esta creando uno nuevo
    		{
    			$fieldset = array(
    					"nombre" 		=>  $this->input->post ('fecha_nombre', true),
    					"descripcion" 	=>  $this->input->post ('descripcion', true),
     					"fecha_oficial" =>  $_date,
    					"horas_no_laboradas" =>  $this->input->post ('horas_no_laboradas', true),
    					"empresa_id" 		=>  $this->empresa_id,
    					"estado_id" 		=>  $this->input->post ('estado_id', true),
    					"creado_por" 		=>  $usuario->id,
    					"cuenta_pasivo_id" 	=>  $this->input->post ('cuenta_pasivo_id', true)
    
    			);
    			$diaFeriado = Diasferiados_orm::create($fieldset);
      			$mensaje = "Se ha creado exitosamente el dia feriado";
    		}
    		else if( $this->input->post ('id_diaferiado', true)  > 0 ) //Si se esta editando}
    		{
     			$fecha_nombre 		= $this->input->post ('fecha_nombre', true);
    			$fecha_descripcion	= $this->input->post ('descripcion', true);
    			$fecha_fecha_oficial= $_date;
    			$fecha_horas	 	= $this->input->post ('fecha_horas', true);
    			$horas_no_laboradas	 	= $this->input->post ('horas_no_laboradas', true);
    			$estado_id	 		= $this->input->post ('estado_id', true);
    			$cuenta_pasivo_id	 		= $this->input->post ('cuenta_pasivo_id', true);
    			
    
    			$diaFeriado = Diasferiados_orm::where('id', $id)->update(['nombre' => $fecha_nombre,'descripcion' => $fecha_descripcion,'fecha_oficial' => $fecha_fecha_oficial,'horas_no_laboradas' => $horas_no_laboradas,'estado_id' => $estado_id,'cuenta_pasivo_id' => $cuenta_pasivo_id ]);
       			$mensaje = "Se ha editado exitosamente el dia feriado";
    		}
    		
    		//Operacion con los acumulados
    		if( count($acumulados)>0 )
    		{
    			
    			if( $this->input->post('id_diaferiado', true)  == 0)  //Si se esta creando uno nuevo
    			{
    				if(Util::is_array_empty($acumulados) == false){
    					foreach ($acumulados AS $acumulado){
    						$fieldset["id_acumulado"] = $acumulado;
    						$fieldset["fecha_creacion"] = date('Y-m-d H:i:s');
    				
    						$_acumulados[] = new Diasferiados_acumulados_orm($fieldset);
    					}
    				}
    				$diaFeriado->acumulados()->saveMany($_acumulados);
    			}
    			else{
    				if(Util::is_array_empty($acumulados) == false){
    					foreach ($acumulados AS $acumulado){
    						$fieldset["id_acumulado"] = $acumulado;
    						$fieldset["id_dia_feriado"] = $id;
    						$fieldset["fecha_creacion"] = date('Y-m-d H:i:s');
    				
    						$_acumulados[] = new Diasferiados_acumulados_orm($fieldset);
    					}
    				}
    				
    				$feriados = new Diasferiados_acumulados_orm();
    				Diasferiados_acumulados_orm::where('id_dia_feriado', $id)->delete();
    				$feriados->acumulados()->saveMany($_acumulados);
    			}
     		}else{
     			Diasferiados_acumulados_orm::where('id_dia_feriado', $id)->delete();
     		}
     		
     		//Operacion con las deducciones
     		if( count($deducciones)>0 )
     		{
     			 
     			if( $this->input->post('id_diaferiado', true)  == 0)  //Si se esta creando uno nuevo
     			{
     				if(Util::is_array_empty($deducciones) == false){
     					foreach ($deducciones AS $deduccion){
     						$fieldset["id_deduccion"] = $deduccion;
     						$fieldset["fecha_creacion"] = date('Y-m-d H:i:s');
     		
     						$_deducciones[] = new Diasferiados_deducciones_orm($fieldset);
     					}
     				}
     				$diaFeriado->deducciones()->saveMany($_deducciones);
     			}
     			else{
     				if(Util::is_array_empty($deducciones) == false){
     					foreach ($deducciones AS $deduccion){
     						$fieldset["id_deduccion"] = $deduccion;
     						$fieldset["id_dia_feriado"] = $id;
     						$fieldset["fecha_creacion"] = date('Y-m-d H:i:s');
     		
     						$_deducciones[] = new Diasferiados_deducciones_orm($fieldset);
     					}
     				}
     		
     				$feriados = new Diasferiados_deducciones_orm();
     				Diasferiados_deducciones_orm::where('id_dia_feriado', $id)->delete();
     				$feriados->deducciones()->saveMany($_deducciones);
     			}
     		}else{
     			Diasferiados_deducciones_orm::where('id_dia_feriado', $id)->delete();
     		}
     		 
    	} catch(ValidationException $e){
    
    		// Rollback
    		Capsule::rollback();
    	}
    	 
    	Capsule::commit();
    
    	echo json_encode(array(
    			"response" => true,
    			"mensaje" => $mensaje
    	));
    	exit;
    }
    
   
   
    public function ocultotabladeducciones()
    {
    	//If ajax request
    	$this->assets->agregar_js(array(
    			'public/assets/js/modules/configuracion_planilla/tabla-deducciones.js'
    	));
    
    	$this->load->view('tabla_deducciones');
    }
    public function ocultotablaacumulados()
    {
    	//If ajax request
    	$this->assets->agregar_js(array(
    			'public/assets/js/modules/configuracion_planilla/tabla-acumulados.js'
    	));
    
    	$this->load->view('tabla-acumulados');
    }
    
    public function ocultotablaliquidacion()
    {
     	//If ajax request
    	$this->assets->agregar_js(array(
    			'public/assets/js/modules/configuracion_planilla/tabla_liquidaciones.js'
    	));
    
    	$this->load->view('tabla_liquidaciones');
    }
    
     public function ocultotablarecargos()
    {
     	//If ajax request
    	$this->assets->agregar_js(array(
    			'public/assets/js/modules/configuracion_planilla/tabla-recargos.js'
    	));
    
    	$this->load->view('tabla-recargos');
    }
    
     
    
    /**
     * Cargar Vista Parcial de Tabla
     *
     * @return void
     */
    public function ocultotablabeneficios()
    {
    	//If ajax request
    	$this->assets->agregar_js(array(
    			'public/assets/js/modules/configuracion_planilla/tabla_beneficios.js'
    	));
    	 
    	$this->load->view('tabla_beneficios');
    }
    
    
 
    
     /**
     * Cargar Vista Parcial de Tabla
     *
     * @return void
     */
    public function ocultotabladiasferiados()
    {
     	//If ajax request
    	$this->assets->agregar_js(array(
    			'public/assets/js/modules/configuracion_planilla/tabla_diasferiados.js'
    	));
    
    	$this->load->view('tabla_diasferiados');
    }
     
    public function ajax_get_liquidacion() {

    	//Just Allow ajax request
    	if(!$this->input->is_ajax_request()){
    		return false;
    	}
    	 $arr = $arr2 = [];
    	
    	//$response = new stdClass();
    	 
    
    	$liquidacion_id = $this->input->post('liquidacion_id', true);
    	
    	$info = $this->configuracionPlanillaLiquidacionRepository->find($liquidacion_id);
    	
    	
    	
    	$liquidacion_info1 = $this->configuracionPlanillaLiquidacionRepository->pagos_aplicables($liquidacion_id);
    	$liquidacion_info2 = $this->configuracionPlanillaLiquidacionRepository->pagos_acumulados($liquidacion_id);
    	
    	
    	foreach($liquidacion_info1 as $key => $item)
    	{
     		
       		$arr[$item->tipo_pago_id][] = array(
    				"id"	=>$item->id,
    				"deduccion_id"	=>$item->deduccion_id,
    				"pago_id"		=>$item->pago_id,
    				"tipo_pago_id"		=>$item->tipo_pago_id
    		); 
    	}
    	 
    	foreach($liquidacion_info2 as $key => $item)
    	{
       		$arr2[$item->tipo_pago_id][] = array(
       				"id"	=>$item->id,
    				"deduccion_id"	=>$item->deduccion_id,
    				"pago_id"		=>$item->pago_id,
       				"tipo_pago_id"		=>$item->tipo_pago_id
    		); 
    	}
    	
    	$response['general'] = $info;
    	$response['pagos'] = $arr;
    	 $response['acumulados'] = $arr2;
    	
     	
    	echo json_encode($response);
    	exit;
      	
    }
 
    
   
}

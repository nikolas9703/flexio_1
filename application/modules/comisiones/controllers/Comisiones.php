<?php
 use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon;
use Flexio\Modulo\Comisiones\Repository\ComisionesRepository as comisionesRep;
use Flexio\Modulo\Comisiones\Repository\ColaboradorRepository;
use Flexio\Modulo\Pagos\Repository\PagosRepository;

//transacciones
use Flexio\Modulo\Comisiones\Transacciones\PagosComisiones;
class Comisiones extends CRM_Controller
{
    private $comisionesRep;
    private $id_usuario;
    protected $PagosComisiones;
    protected $pagoGuardar;
    protected $ColaboradorRepository;

	function __construct()
    {
        parent::__construct();

        $this->load->model('comisiones_orm');
        $this->load->model('comision_acumulados_orm');
        $this->load->model('comision_deducciones_orm');
        $this->load->model('comision_colaborador_orm');
        $this->load->model('estado_comision_orm');
        $this->load->model('configuracion_rrhh/Departamentos_orm');
        $this->load->model('colaboradores/estado_orm');
        $this->load->model('centros/centros_orm');
        $this->load->model('colaboradores/colaboradores_orm');
        $this->load->model('usuarios/usuario_orm');
        $this->load->model('usuarios/empresa_orm');
        $this->load->model('contabilidad/tipo_cuentas_orm');
        $this->load->model('pagos/Pagos_orm');
        $this->load->model('pagos/Pago_metodos_pago_orm');
        $this->load->library('Repository/Pagos/Guardar_pago');

        $this->pagoGuardar = new Guardar_pago;

        //Obtener el id_empresa de session
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $this->id_usuario = $this->session->userdata('id_usuario');
        $this->empresa_id = $empresa->id;
        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();
        $this->comisionesRep = new comisionesRep();
        $this->PagosComisiones = new PagosComisiones();
        $this->ColaboradorRepository = new ColaboradorRepository();
     }

    public function listar()
    {
    	$data = array();

     	//Verificar permisos de acceso a esta vista
    	if(!$this->auth->has_permission('acceso', 'comisiones/listar')){
    		//Redireccionar
     		$mensaje = array('estado'=>500, 'mensaje'=>'<b>¡Error!</b> Usted no cuenta con permiso');
    		$this->session->set_flashdata('mensaje', $mensaje);
    	}


    	$uuid_empresa = $this->session->userdata('uuid_empresa');
    	$empresa = Empresa_orm::findByUuid($uuid_empresa);


    	$cat_centros = Capsule::select(Capsule::raw("SELECT id, nombre
					FROM cen_centros WHERE empresa_id = :empresa_id1 AND estado='Activo' AND id NOT IN (SELECT padre_id FROM cen_centros WHERE empresa_id = :empresa_id2 AND estado='Activo')
					 ORDER BY id ASC"),
    			 array(
    				'empresa_id1' => $this->empresa_id,
    				'empresa_id2' => $this->empresa_id
    	));
    	$data = array(
    			"centros" => $cat_centros,
    			"estados" => Estado_comision_orm::where('identificador','=','estado' )->get()
     	);

       	$this->assets->agregar_css(array(
     			'public/assets/css/default/ui/base/jquery-ui.css',
    			'public/assets/css/default/ui/base/jquery-ui.theme.css',
    			'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
    			'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
     			'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
    			'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
    			'public/assets/css/plugins/jquery/chosen/chosen.min.css',
    	));
    	$this->assets->agregar_js(array(
    			'public/assets/js/default/jquery-ui.min.js',
    			'public/assets/js/plugins/jquery/jquery.sticky.js',
    			'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
    			'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
    			'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
    			'public/assets/js/plugins/jquery/chosen.jquery.min.js',
    			'public/assets/js/moment-with-locales-290.js',
    			'public/assets/js/plugins/bootstrap/daterangepicker.js',
    			'public/assets/js/plugins/jquery/chosen.jquery.min.js',
    			'public/assets/js/default/toast.controller.js'
     	));


    	if(!is_null($this->session->flashdata('mensaje'))){
    		$mensaje = json_encode($this->session->flashdata('mensaje'));
    	}else{
    		$mensaje = '';
    	}
    	$this->assets->agregar_var_js(array(
    			"toast_mensaje" => $mensaje
    	));



     	$breadcrumb = array(
    			"titulo" => '<i class="fa fa-institution"></i> Pagos extraordinarios',
     			"filtro" => false,
    			"menu" => array(
  					"nombre" => $this->auth->has_permission('acceso', 'comisiones/crear')?"Crear":'',
  					"url"	 => $this->auth->has_permission('acceso', 'comisiones/crear')?"comisiones/crear":'',
  					"opciones" => array(),
 				),
        "ruta" => array(
           0 => array(
               "nombre" => "Nómina",
               "activo" => false,
           ),
           1 => array(
               "nombre" => "<b>Pagos extraordinarios</b>",
               "activo" => true,
            )
       ),
    	);

      	if ($this->auth->has_permission('listar__exportarComision', 'comisiones/listar')){
     		        	$breadcrumb["menu"]["opciones"]["#ExportarBtnComision"] = "Exportar";
     	}

    	$this->template->agregar_titulo_header('Listado de pagos extraordinarios');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar($breadcrumb);
    }
    function crear($comision_uuid=NULL)
    {


    	if(!$this->auth->has_permission('acceso', 'comisiones/crear')){
    		redirect(base_url('/'));
    	}

    	$data = array();
    	$mensaje = array();

    	if(!empty($_POST["campo"])){
    		$this->guardar_comision();
    	}

    	$this->assets->agregar_css(array(
    			'public/assets/css/default/ui/base/jquery-ui.css',
    			'public/assets/css/default/ui/base/jquery-ui.theme.css',
    			'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
    			'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
    			'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
    			'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
    			'public/assets/css/plugins/jquery/chosen/chosen.min.css',
    			'public/assets/css/plugins/jquery/multiselect-master/style.css',
    	));
    	$this->assets->agregar_js(array(
    			'public/assets/js/default/jquery-ui.min.js',
    			'public/assets/js/plugins/jquery/jquery.sticky.js',
    			'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
    			'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
    			'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
    			'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
    			'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
    			'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
    			'public/assets/js/plugins/jquery/chosen.jquery.min.js',
    			'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
    			'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
    			'public/assets/js/moment-with-locales-290.js',
    			'public/assets/js/plugins/jquery/multiselect-master/multiselect.js',
    			'public/assets/js/default/formulario.js'
    	));

      $breadcrumb = array(
          "titulo" => '<i class="fa fa-institution"></i>  Pagos extraordinarios',
          "filtro" => false,
           "ruta" => array(
            0 => array(
                "nombre" => "Nómina",
                "activo" => false,
            ),
              1 => array(
                  "nombre" => "Pagos extraordinarios",
                  "activo" => false,
                  "url" => 'comisiones/listar'
              ),
              2=> array(
                  "nombre" => '<b>Crear</b>',
                  "activo" => true
              )
          ),
      );

    	$this->template->agregar_titulo_header('Pagos extraordinarios');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar();
    }

    function editar($comision_uuid=NULL)
    {



      if(!$this->auth->has_permission('acceso', 'comisiones/ver/(:any)')){
    		redirect(base_url('/'));
    	}

    	$data = $mensaje = array();

    	$comision_info = array();
    	if($comision_uuid!=NULL){

    		$permiso_editar =  $this->auth->has_permission('ver__editarComision', 'comisiones/ver/(:any)')?1:0;

    		$comision_info = Comisiones_orm::with(array( 'empresa', 'centro_contable', 'estado','deducciones','acumulados','area_negocio' => function($query){
    		}))->where(Capsule::raw("HEX(uuid_comision)"), "=", $comision_uuid)->get()->toArray();

        if($comision_info[0]['estado_id'] == 19){
          $permiso_editar = 0;
        }
     		$comision_info[0]['uuid_cuenta_activo'] = strtoupper (bin2hex($comision_info[0]['uuid_cuenta_activo']));

    		$centro_contable 	=  !empty($comision_info[0]['centro_contable']['nombre'])?$comision_info[0]['centro_contable']['nombre']:'';
    		$area_negocio		=  !empty($comision_info[0]['area_negocio']['nombre'])?'/'.$comision_info[0]['area_negocio']['nombre']:'';

    		$columna_centro = $centro_contable.$area_negocio;
           $comision = $this->comisionesRep->findByUuid($comision_uuid);
            $comision->load('comentario_timeline', 'comisiones_asignados',
     'deducciones','acumulados'
          );
    		$this->assets->agregar_var_js(array(

    				"comision_id" => $comision_info[0]['id'],
    				"acumulados_id" => !empty($comision_info[0]['acumulados']) ? json_encode($comision_info[0]['acumulados']):"",
    				"deduccion_id" => !empty($comision_info[0]['deducciones'])? json_encode($comision_info[0]['deducciones']):"",
    				"permiso_editar" => $permiso_editar,
    				"columna_centro" => $columna_centro,
            "vista"          => 'ver',
            "coment" =>(isset($comision->comentario_timeline)) ? $comision->comentario_timeline : "",
    		));


    		//Colaboradores con que estan creados la comision
    		$result = Capsule::table('com_colaboradores as cc')
    		->where('cc.comision_id', $comision_info[0]['id'])
    		->distinct()
    		->get(array('cc.colaborador_id'));
    		$colaboradores_activos = (!empty($result) ? array_map(function($result){ return array( $result->colaborador_id); }, $result) : array());

    		$colaboradores_noactivados = Capsule::table('col_colaboradores as c')
    		->where('c.centro_contable_id',  $comision_info[0]['centro_contable_id'])
    		->where('c.empresa_id',  $this->empresa_id)
   		  ->whereNotIn('c.id', $colaboradores_activos)
    		->distinct()
    		->get(array('c.id','c.codigo','c.nombre','c.apellido','c.cedula'));

    		if($comision_info[0]['area_negocio_id'] > 0){

    			$colaboradores_noactivados = Capsule::table('col_colaboradores as c')
    			->where('c.centro_contable_id',  $comision_info[0]['centro_contable_id'])
    			->where('c.empresa_id',  $this->empresa_id)
    			//->where('c.departamento_id',  $comision_info[0]['area_negocio_id'])
    			->whereNotIn('c.id', $colaboradores_activos)
    			->distinct()
    			->get(array('c.id','c.codigo','c.nombre','c.apellido','c.cedula'));
    		}
    		$data["colaboradores_noactivados"] = $colaboradores_noactivados;


    	}
       ;
    	$this->assets->agregar_css(array(
    			'public/assets/css/default/ui/base/jquery-ui.css',
    			'public/assets/css/default/ui/base/jquery-ui.theme.css',
    			'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
    			'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
    			'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
    			'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
    			'public/assets/css/plugins/jquery/chosen/chosen.min.css',
    			'public/assets/css/plugins/jquery/multiselect-master/style.css',

    	));
    	$this->assets->agregar_js(array(
    			'public/assets/js/default/jquery-ui.min.js',
    			'public/assets/js/plugins/jquery/jquery.sticky.js',
    			'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
    			'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
    			'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
    			'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
    			'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
    			'public/assets/js/plugins/jquery/combodate/combodate.js',
    			'public/assets/js/plugins/jquery/combodate/momentjs.js',
    			'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
    			'public/assets/js/plugins/jquery/chosen.jquery.min.js',
    			'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
    			'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
    			'public/assets/js/moment-with-locales-290.js',
    			'public/assets/js/plugins/bootstrap/daterangepicker.js',
    			'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
    			'public/assets/js/default/tabla-dinamica.jquery.js',
    			'public/assets/js/default/formulario.js',
    			'public/assets/js/plugins/jquery/multiselect-master/multiselect.js',
    	));
       if($comision_info[0]['fecha_programada_pago'] == '30/11/-0001'){
        $comision_info[0]['fecha_programada_pago'] ='';
      }
    	$data["info"] = $comision_info[0];

    	$data["permiso_editar"] = $permiso_editar;



      $breadcrumb = array(
          "titulo" => '<i class="fa fa-institution"></i> Pagos extraordinarios: '.$data["info"]["numero"],
          "filtro" => false,
          "menu" => array(
              "url"	 => '#',
              "nombre" => "Acci&oacute;n"
          ),
           "ruta" => array(
            0 => array(
                "nombre" => "Nómina",
                "activo" => false,
            ),
              1 => array(
                  "nombre" => "Pagos extraordinarios",
                  "activo" => false,
                  "url" => 'comisiones/listar'
              ),
              2=> array(
                  "nombre" => '<b>Detalle</b>',
                  "activo" => true
              )
          ),
      );
    	if ($this->auth->has_permission('ver__agregarComision', 'comisiones/ver/(:any)') && $permiso_editar == 1){
    		$breadcrumb["menu"]["opciones"]["#agregarColaborador"] = "Agregar colaborador";
    	}

    	if ($this->auth->has_permission('ver__eliminarComision', 'comisiones/ver/(:any)') && $permiso_editar == 1){
    		$breadcrumb["menu"]["opciones"]["#EliminarBtnComisionColaborador"] = "Eliminar";
    	}
      $this->template->agregar_titulo_header('Pagos extraordinarios');

    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar();
    }

    public function ajax_listar_comisiones()
    {
        if(!$this->input->is_ajax_request()){return false;}

        $columna_centro 				= $this->input->post('columna_centro', true);

        $clause = array();
       	$centro_contable	  = $this->input->post('centro_contable', true);
        $estado 			      = $this->input->post('estado_id', true);
        $fecha1 				    = $this->input->post('fecha1', true);
        $fecha2 				    = $this->input->post('fecha2', true);

      	$clause["empresa_id"] = $this->empresa_id ;

       	if( !empty($centro_contable)){
      		$clause["centro_contable_id"] = $centro_contable;
      	}

      	if( !empty($estado)){
      		$clause["estado_id"] = $estado;
      	}
      	if( !empty($fecha1) && !empty($fecha2)){
      		$fecha_inicio = explode("/", $fecha1);
      		$fecha_final  = explode("/", $fecha2);

      		$inicio = $fecha_inicio[2].'-'.$fecha_inicio[1].'-'.$fecha_inicio[0];
      		$fin = $fecha_final[2].'-'.$fecha_final[1].'-'.$fecha_final[0];

      		$clause["fecha_pago"] = array('>=', $inicio);
      		$clause["fecha_pago@"] = array('<=', $fin);
      	}

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = $this->comisionesRep->count($clause);

        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $pagos_extraordinarios = $this->comisionesRep->get($clause ,$sidx, $sord, $limit, $start);

        $response          = new stdClass();
        $response->page    = $page;
        $response->total   = $total_pages;
        $response->records = $count;

          if($count > 0){
            $i=0;
           foreach ($pagos_extraordinarios as $i => $pago){
              $hidden_options = "";
       	 	    $centro_contable 	=  !empty($pago->centro_contable->nombre)?$pago->centro_contable->nombre:'';
      	      $area_negocio		=  !empty($pago->area_negocio->nombre)?'/'.$pago->area_negocio->nombre:'';
       			  $columna_centro = $centro_contable.$area_negocio;

              $estado = '<span style="color:white; background-color:'.$pago->estado->color_estado.'" class="btn btn-xs btn-block">'.$pago->estado->etiqueta.'</span>';
              $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $pago->id .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

           			if ($this->auth->has_permission('acceso', 'comisiones/ver/(:any)')){
           				$hidden_options .= '<a href="'. $pago->enlace .'" class="btn btn-block btn-outline btn-success">Ver Detalle</a>';
           			}
          			else{
          				$link_detalles =  $row['numero'];
          			}

          			if($pago->estado->etiqueta  != 'Anulada'){
          				if ($this->auth->has_permission('listar__anularComision', 'comisiones/listar')){
          					$hidden_options .= '<a href="#" id="confirmAnular"   data-id="' . $pago->id . '" class="btn btn-block btn-outline btn-success" type="button">Anular Comision</a>';
          				}
          			}
                 if($pago->estado->etiqueta  == 'Pendiente'){
                    $hidden_options .= '<a id="pagarPagoExtraordinario" href="#" data-salario="$'. number_format($pago->monto_neto,2).'"  data-id="'. $pago->id .'" class="btn btn-block btn-outline btn-success">Pagar</a>';
                }

         $color_fecha ='';

         $fecha_programada_pago_myd = date('Y-m-d', strtotime(str_replace('/', '-', $pago->fecha_programada_pago)));
         //&& && $pago->estado->etiqueta!='Pendiente'
        if($fecha_programada_pago_myd < date("Y-m-d")){
          $color_fecha ='style="color: #ff0000;"';
        }
         $fecha_programada_pago = ($pago->fecha_programada_pago == '30/11/-0001')?'Pendiente':$pago->fecha_programada_pago;
         $fecha_pago = ($pago->fecha_pago == '0000-00-00 00:00:00')?'Pendiente':date("d/m/Y", strtotime($pago->fecha_pago));
    			$response->rows[$i]["id"] = $pago['id'];
          $response->rows[$i]["cell"] = array(
    				$pago->numero_documento_enlace,
     				$centro_contable,
    				$pago->descripcion,
           '<span '.$color_fecha.'>'.$fecha_programada_pago.'</span>',
     				$fecha_pago,
     				count($pago->colaboradores),
            '$'.number_format($pago->monto_neto,2),
    				$estado,
            $link_option,
            $hidden_options
    			);

    			$i++;
    		}
    	}

     	echo json_encode($response);
    	exit;
    }

      public function ajax_listar_colaboradores_detalle()
    {

      if(!$this->input->is_ajax_request()){return false;}

      $columna_centro 				= $this->input->post('columna_centro', true);

      $clause                 = [];
      $clause['comision_id']   = $this->input->post('comision_id', true);
	    $clause["estado"] 			= 1;

      list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
      $count = $this->ColaboradorRepository->count($clause);

      list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
      $colaboradores = $this->ColaboradorRepository->get($clause ,$sidx, $sord, $limit, $start);

      $response          = new stdClass();
      $response->page    = $page;
      $response->total   = $total_pages;
      $response->records = $count;

      	if($count > 0){
          $i=0;
         foreach ($colaboradores as $i => $colaborador){

 	    			$nombre =  $colaborador->colaborador->nombre_completo;

   	    		$response->rows[$i]["id"] = $colaborador->id;
	    			$response->rows[$i]["cell"] = array(
	    					$colaborador->id,
	    					$nombre,
	    				  $colaborador->colaborador->cedula,
	    					isset($columna_centro)?$columna_centro:'',
	    					$colaborador->descripcion,
   	    				$colaborador->monto_total
   	    				//$colaborador->monto_total."=>".$colaborador->monto_deducido."=>".$colaborador->monto_neto
	    		 );
	    		 $i++;
     		}
    	}
     	echo json_encode($response);
    	exit;
    }


     function ajax_anular_comision() {

    	//Just Allow ajax request
    	if(!$this->input->is_ajax_request()){
    		return false;
    	}

    	$clause = array();
    	$id = $this->input->post('id_comision', true);

    	if(empty($id)){
    		return false;
    	}

    	/**
    	 * Inicializar Transaccion
    	 */
    	Capsule::beginTransaction();

    	try {
     		$response = Comisiones_orm::where('id', '=', $id);
    		$response->update(array("estado_id" => 16));

    	} catch(ValidationException $e){

     		Capsule::rollback();

    		echo json_encode(array(
    				"response" => false,
    				"mensaje" => "Hubo un error tratando de anular la comisi&oacute;n."
    		));
    		exit;
    	}

     	Capsule::commit();

    	echo json_encode(array(
    			"response" => $response,
    			"mensaje" => "Se ha anulado la comisi&oacute;n satisfactoriamente."
    	));
    	exit;
    }

    function ajax_eliminar_colaborador() {

    	//Just Allow ajax request
    	if(!$this->input->is_ajax_request()){
    		return false;
    	}

     	$ids = $this->input->post('colaboradoresComision', true);

    	if(empty($ids)){
    		return false;
    	}

    	Capsule::beginTransaction();

    	try {

            $response = $this->ColaboradorRepository->eliminar_colaborador($ids);
            $response = true;

     	} catch(ValidationException $e){

    		Capsule::rollback();

    		echo json_encode(array(
    				"response" => false,
    				"mensaje" => "Hubo un error tratando de eliminar el colaborador."
    		));
    		exit;
    	}

    	Capsule::commit();

    	echo json_encode(array(
    			"response" => $response,
    			"mensaje" => "Se ha eliminado el colaborador satisfactoriamente."
    	));
    	exit;
    }

    function ajax_editar_monto(){

     	//Just Allow ajax request
    	if(!$this->input->is_ajax_request()){
    		return false;
    	}
      $post = $this->input->post();
      $id = $this->input->post('id', true);

    	if(empty($id)){
    		return false;
    	}

    	Capsule::beginTransaction();

    	try {
        $collectionColaborador = $this->ColaboradorRepository->find($id);
        $response = $this->ColaboradorRepository->editar_calculos($collectionColaborador, $post);
    		$response = true;

    	} catch(ValidationException $e){

    		Capsule::rollback();

     		$response = false;
    		exit;
    	}

    	Capsule::commit();
    	echo $response;

    	exit;

    }

    public function ajax_por_aprobar() {
      // Just Allow ajax request
      if (! $this->input->is_ajax_request ()) {
        return false;
      }
     Capsule::beginTransaction();
      try {
        $post = $this->input->post();
        $comision_id			= $this->input->post('comision_id', true);

        $comision = $this->comisionesRep->find($comision_id);

           $post['estado']['por_pagar'] = 1;
           $comision->load("colaboradores");
           $comision = $this->comisionesRep->ajax_por_aprobar($comision, $post);
           $this->PagosComisiones->haceTransaccion($comision);
           $this->_createPago( $comision );
           $comision->estado_id = 19;
           $comision->save();
       } catch(ValidationException $e){

         Capsule::rollback();
        $mensaje = array('estado'=>500, 'mensaje'=>'<b>Hubo un error tratando de actualizar la comision.</b> ');
        echo json_encode(array(
            "response" => false,
            "mensaje" => $mensaje
        ));
        exit;
      }

      Capsule::commit();

      $mensaje = array('estado'=>200, 'mensaje'=>'<b>Se ha actualizado satisfactoriamente.</b> ');
      $this->session->set_flashdata('mensaje', $mensaje);

    echo json_encode(array(
          "response" 	=> true,
            //"mensaje" 	=> "Se ha actualizado satisfactoriamente."
      ));
       exit;
    }
    //Esta funcion se usa para editar la comision. atraves de ajax

    public function ajax_editar_comision() {


    	// Just Allow ajax request
    	if (! $this->input->is_ajax_request ()) {
    		return false;
    	}

    	/**
    	 * Inicializar Transaccion
    	 */
    	Capsule::beginTransaction();

    	try {
        $post = $this->input->post();
     		$comision_id			= $this->input->post('comision_id', true);
    		//$uuid_cuenta_activo		= $this->input->post('campo[uuid_cuenta_activo]', true);
        $cuenta_id_activo 	=  $this->input->post('campo[cuenta_id_activo]', true);
     		$metodo_pago			= $this->input->post('campo[metodo_pago]', true);
     		$estado_id				= $this->input->post('campo[estado_id]', true);
     		$descripcion			= $this->input->post('campo[descripcion]', true);

     		$deducciones 			 = $this->input->post('deducciones', true);
    		$acumulados 			 = $this->input->post('acumulados', true);
        $fecha_programada_pago = $this->input->post('campo[fecha_programada_pago]', true);

        $comision = $this->comisionesRep->find($comision_id);
        //$comision->uuid_cuenta_activo = hex2bin($uuid_cuenta_activo);
        $comision->cuenta_id_activo   = $cuenta_id_activo;
        $comision->metodo_pago        = $metodo_pago;
        $comision->estado_id          = $estado_id;
        $comision->descripcion        = $descripcion;
        $comision->fecha_programada_pago = $fecha_programada_pago;
        $comision->save();

        if($estado_id == 19) //Se crean las transacciones
        {
           //$this->PagosRepository
           $post['estado']['por_pagar'] = 1;
           $comision->load("colaboradores");
           $comision = $this->comisionesRep->editar($comision, $post);
           $this->PagosComisiones->haceTransaccion($comision);
           $this->_createPago( $comision );
         }else{
           $post['estado']['por_pagar'] = 0;
           $comision = $this->comisionesRep->editar($comision, $post);
         }

     	} catch(ValidationException $e){

    		// Rollback
    		Capsule::rollback();
    		$mensaje = array('estado'=>500, 'mensaje'=>'<b>Hubo un error tratando de actualizar la comision.</b> ');
    		echo json_encode(array(
    				"response" => false,
    				//"mensaje" => "Hubo un error tratando de actualizar la comisi�n."
    		));
    		exit;
    	}

    	Capsule::commit();

    	$mensaje = array('estado'=>200, 'mensaje'=>'<b>Se ha actualizado satisfactoriamente.</b> ');
    	$this->session->set_flashdata('mensaje', $mensaje);

 		echo json_encode(array(
    			"response" 	=> true,
      			//"mensaje" 	=> "Se ha actualizado satisfactoriamente."
    	));
    	 exit;


    }
    //Esta funcion se usa cuando se agrega un colaborador a un pago ya existente
    function ajax_agregar_colaborador()
    {
       	Capsule::beginTransaction();
     	try {
        $post = $this->input->post();

        $comision_editar = $this->comisionesRep->find($post["comision_id"]);

          $response = $this->ColaboradorRepository->agregar_colaboradores($comision_editar, $post);
          if($response){
            $response = true;
          }
          else{
            $response = false;
            Capsule::rollback();
             echo json_encode(array(
                "response" => false,
                "mensaje" => "Hubo un error tratando de agregar colaboradores."
            ));
            exit;
          }

    		Capsule::commit();

    	} catch(ValidationException $e){

    		Capsule::rollback();
    		echo json_encode(array(
    				"response" => false,
    				"mensaje" => "Hubo un error tratando de agregar colaboradores."
    		));
    		exit;
    	}
    	echo json_encode(array(
    			"response" => true,
    			"mensaje" => "Se ha agregado satisfactoriamente."
    	));
    	exit;
    }
    //Esta funcion solo se usa al momento de crear una comision, NO AL EDITAR
    private function  guardar_comision()
    {
        unset($_POST["campo"]["guardarFormBtn"]);
       	unset($_POST["campo"]["agregarFormBtn"]);

    	Capsule::beginTransaction();

    	try {

        $post = $this->input->post();

        $post['campo']['empresa_id'] = $this->empresa_id;
        $post['campo']['estado_id'] =20;
        $post['campo']['total_colaboradores'] = count($post['colaboradores_to']);
        $post["uuid_comision"] = Capsule::raw("ORDER_UUID(uuid())");
        $comision = $this->comisionesRep->create($post);
      	$comision_creada =  Comisiones_orm::where('id','=',$comision->id)->get()->toArray();
    		$uuid_comision = $comision_creada[0]['uuid_comision'];

    	} catch(ValidationException $e){

    		// Rollback
    		Capsule::rollback();

    		log_message("error", "MODULO: ". __METHOD__ .", Linea: ". __LINE__ ." --> ". $e->getMessage()."\r\n");

    		echo json_encode(array(
    				"guardado" => false,
    				"mensaje" => "Hubo un error tratando de crear el pago"
    		));
    		exit;
    	}

     	Capsule::commit();
    	redirect(base_url('comisiones/ver/'.$uuid_comision));
    }

    public function ajax_cargar_codigo()
    {

    	if(!$this->input->is_ajax_request()){
    		return false;
    	}

    	$codigo = $this->ajax_cargar_codigo_numero();
     	$json = json_encode($codigo);
    	echo $json;
    	exit;
    }

    public function ajax_cargar_codigo_numero(){
    	$uuid_empresa = $this->session->userdata('uuid_empresa');
    	$empresa = Empresa_orm::findByUuid($uuid_empresa);
    	$codigo = 0;
    	$response = new stdClass();
    	$response = Comisiones_orm::where("empresa_id", "=",$empresa->id)
    	->orderBy('id', 'DESC')
    	->limit(1)
    	->get();

     	if(!empty($response->toArray())){
      		$codigo = $response[0]['numero'];
    	}
    	else
    		$codigo = 'PE0';
      	$codigo= str_replace("PE","", $codigo);
      	$codigo = (int) $codigo + 1;
      	$codigo = Util::zerofill($codigo,4);
     	$codigo = 'PE'.$codigo;

    	return $codigo;
    }

     public function ajax_listar_departamento_x_centro()
    {
     	if(!$this->input->is_ajax_request()){
    		return false;
    	}

    	$clause = array();
    	$centro_id = $this->input->post('centro_id', true);
    	//$empresa_id = !empty($_POST["empresa_id"]) ? $this->input->post('empresa_id', true) : $this->empresa_id;
    	$uuid_empresa = $this->session->userdata('uuid_empresa');
    	$empresa = Empresa_orm::findByUuid($uuid_empresa);

     	if(empty($centro_id)){
    		return false;
    	}

    	$response = new stdClass();
    	$response->result = Departamentos_orm::departamento_centro3($centro_id, $empresa->id);
    	$json = json_encode($response);
    	echo $json;
    	exit;

    }
    //Se usa en la lista del formulario
    public function ajax_listar_colaboradores()
    {
     	if(!$this->input->is_ajax_request()){
    		return false;
    	}
    	$uuid_empresa = $this->session->userdata('uuid_empresa');
    	$empresa = Empresa_orm::findByUuid($uuid_empresa);

    	$centro_contable_id 	= $this->input->post('centro_contable_id', true);
    	$departamento_id 	= $this->input->post('departamento_id', true);
    	$response = new stdClass();

    	if( (int) $departamento_id > 0 ){
    		$response->result = Colaboradores_orm::where("centro_contable_id", "=", $centro_contable_id)
    			->where('empresa_id', $empresa->id)
    			->where('departamento_id', $departamento_id)
    			//->select('id', 'no_colaborador','nombre','apellido', 'cedula')->get()->toArray();
                         ->select('id', 'codigo','nombre','apellido', 'cedula')->get()->toArray();
    	}else{
    		$response->result = Colaboradores_orm::where("centro_contable_id", "=", $centro_contable_id)
    		->where('empresa_id', $empresa->id)
                    ->select('id', 'codigo','nombre','apellido', 'cedula')->get()->toArray();
    	}

       	echo json_encode($response);
    	exit;

    }

    public function ocultotablacomisiones()
    {
    	$this->assets->agregar_js(array(
    			'public/assets/js/modules/comisiones/tabla-comisiones.js'
    	));
    	$this->load->view('tabla-comisiones');
    }


    public function ocultotablacolaboradores()
    {
    	$this->assets->agregar_js(array(
    			'public/assets/js/modules/comisiones/tabla-colaborador.js'
    	));
    	$this->load->view('tabla-colaboradores');
    }

    public function ocultoformulario($data=NULL)
    {

    	if(!empty($data)){
    		/*$this->assets->agregar_js(array(
    				'public/assets/js/modules/comisiones/editar.js'
    		));*/
    	}else{
    		$this->assets->agregar_js(array(
    				'public/assets/js/modules/comisiones/crear.js'
    		));
    	}

    	$this->load->view('formulario', $data);
    }

    function ocultoformulariocomentarios() {

        $data = array();

        $this->assets->agregar_js(array(
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/modules/comisiones/vue.comentario.js',
            'public/assets/js/modules/comisiones/formulario_comentario.js'
        ));

        $this->load->view('formulario_comentarios');
        $this->load->view('comentarios');

    }
    private function _createPago( $comisionInfo) {

        $total = Pagos_orm::deEmpresa($comisionInfo->empresa_id)->count();
        $year = Carbon::now()->format('y');
        $comisionInfo->load('colaboradores.colaborador','cuenta_info');

        $post['campo']['cuenta_id'] = $comisionInfo->cuenta_info->id;

        $total = Pagos_orm::deEmpresa($comisionInfo->empresa_id)->count();
        $year = Carbon::now()->format('y');
        $contador = 1;
         if(count($comisionInfo->colaboradores)>0){
         foreach ($comisionInfo->colaboradores as $key => $value) {
           if($value->monto_neto > 0){
             $aux = [];
             $pago = new Pagos_orm;
             $codigo = Util::generar_codigo('PGO' . $year, $total + $contador);
             $total_pagado_nuevo = (float)str_replace(",","",$value->monto_neto);
             $pago->codigo = $codigo;

             $pago->empresa_id = $comisionInfo->empresa_id;
             $pago->fecha_pago = date("Y-m-d");
             $pago->proveedor_id = $value->colaborador_id;
             $pago->monto_pagado = $total_pagado_nuevo;
             $pago->cuenta_id = $comisionInfo->cuenta_info->id;
             $pago->depositable_id = $comisionInfo->id;
             $pago->depositable_type = 'Flexio\Modulo\Comisiones\Models\Comisiones';
             $pago->formulario = 'pago_extraordinario';
             $pago->estado = 'por_aplicar';

             $pago->save();

             $aux[$comisionInfo->id] = array(
                 "pagable_type" =>'Flexio\\Modulo\\Comisiones\\Models\\Comisiones',
                 "monto_pagado" =>  $total_pagado_nuevo,
                 "empresa_id" => $comisionInfo->empresa_id
             );
              $pago->pagos_extraordinarios()->sync($aux);

             $item_pago = new Pago_metodos_pago_orm;

             $referencia = $this->pagoGuardar->tipo_pago('ach', array(
               'nombre_banco_ach'=>$value->colaborador->banco_id,
               'cuenta_proveedor'=>$value->colaborador->numero_cuenta
             ));

             $item_pago->tipo_pago = 'ach';
             $item_pago->total_pagado = $value->monto_total;
             $item_pago->referencia = $referencia;
             $pago->metodo_pago()->save($item_pago);
             ++$contador;
           }
          }
        }
      }
    function ajax_guardar_comentario() {

        if(!$this->input->is_ajax_request()){
            return false;
        }
        $model_id   = $this->input->post('modelId');
        $comentario = $this->input->post('comentario');
        $comentario = ['comentario'=>$comentario,'usuario_id'=>$this->id_usuario];
        $comisiones = $this->comisionesRep->agregarComentario($model_id, $comentario);
        $comisiones->load('comentario_timeline');

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($comisiones->comentario_timeline->toArray()))->_display();
        exit;
    }
 }

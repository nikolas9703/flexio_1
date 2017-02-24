<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon;
use League\Csv\Writer as Writer;
use Flexio\Modulo\Colaboradores\Repository\ColaboradoresRepository;
use Flexio\Modulo\Planilla\Repository\PlanillaRepository;
use Flexio\Modulo\Planilla\Repository\VacacionRepository;
use Flexio\Modulo\Planilla\Repository\CalculosRepository;
use Flexio\Modulo\Planilla\Repository\CalculosCerradoRepository;
use Flexio\Modulo\ConfiguracionPlanilla\Repository\ConfiguracionPlanillaLiquidacionRepository;
use Flexio\Modulo\Planilla\Repository\LiquidacionPagadaRepository;
use Flexio\Modulo\Planilla\Repository\PagadasRepository;
use Flexio\Modulo\Planilla\Repository\PagadasVacacionesRepository;
use Flexio\Modulo\Planilla\Repository\Regular\PlanillaRegularRepository;
//use Flexio\Modulo\Pagos\Repository\PagosRepository as pagosRep;
use Flexio\Modulo\Modulos\Repository\ModulosRepository;
use Flexio\Modulo\CentrosContables\Repository\CentrosContablesRepository;
use Flexio\Modulo\ConfiguracionRrhh\Repository\RrhhAreasRepository;
use Flexio\Modulo\Planilla\Acciones\CrearPlanilla;
use Flexio\Modulo\Vacaciones\Repository\VacacionesRepository as ModelVacacionRep;
use Flexio\Modulo\ConfiguracionContabilidad\Repository\CuentaPlanillaRepository;


//transacciones
use Flexio\Modulo\Planilla\Transacciones\PagosPlanilla;
use Flexio\Modulo\Planilla\Transacciones\PagosPlanillaVacacion;
use Flexio\Library\Toast;
use Dompdf\Dompdf;

 class Planilla extends CRM_Controller{

 	  protected $empresa_id;
 	  protected $colaboradoresRepository;
  	protected $planillaRepository;
  	protected $configuracionPlanillaLiquidacionRepository;
  	protected $liquidacionPagadaRepository;
  	protected $pagadasRepository;
  	protected $PagadasVacacionesRepository;
  	protected $planillaRegularRepository;
    protected $pagoGuardar;
    protected $CalculosRepository;
    protected $CalculosCerradoRepository;
    protected $ModulosRepository;
    protected $PagosPlanilla;
    protected $PagosPlanillaVacacion;
    protected $CentrosContablesRepository;
    protected $CrearPlanilla;
    protected $VacacionRepository;
    protected $ModelVacacionRep;
    protected $CuentaPlanillaRepository;

    protected $RrhhAreasRepository;
    protected $Toast;
    private   $id_usuario;

	function __construct() {
        parent::__construct();
        $this->load->driver('session');
        $this->load->model('configuracion_planilla/Acumulados_orm');
        $this->load->model('configuracion_planilla/Deducciones_orm');
        $this->load->model('configuracion_planilla/Recargos_orm');
        $this->load->model('configuracion_planilla/Acumulados_contructores_orm');
        $this->load->model('configuracion_planilla/Deducciones_contructores_orm');
        $this->load->model('configuracion_planilla/Beneficios_orm');
        $this->load->model('planilla_orm');
        $this->load->model('Planilla_deducciones_orm');
        $this->load->model('Planilla_acumulados_orm');
        $this->load->model('Planilla_colaborador_orm');
        $this->load->model('Planilla_vacacion_orm');
        $this->load->model('Planilla_liquidacion_orm');
        $this->load->model('Planilla_licencia_orm');
        $this->load->model('planilla/estado_orm');
        $this->load->model('Pagadas_colaborador_orm');
        $this->load->model('Pagadas_ingresos_orm');
        $this->load->model('Pagadas_descuentos_orm');
        $this->load->model('Pagadas_acumulados_orm');
        $this->load->model('Pagadas_deducciones_orm');
        $this->load->model('Pagadas_calculos_orm');
        $this->load->model('modulos/Catalogos_orm');
        $this->load->model('colaboradores/colaboradores_orm');
        $this->load->model('colaboradores/colaboradores_contratos_orm');
        $this->load->model('colaboradores/estado_orm');
        $this->load->model('accion_personal/Accion_personal_orm');
        $this->load->model('liquidaciones/Liquidaciones_orm');
        $this->load->model('vacaciones/Vacaciones_orm');
        $this->load->model('licencias/Licencias_orm');
        $this->load->model('Ingreso_horas_orm');
        $this->load->model('Ingreso_horas_dias_orm');
        $this->load->model('usuarios/usuario_orm');
        $this->load->model('usuarios/empresa_orm');
     	  $this->load->model('configuracion_rrhh/cargos_orm');
        $this->load->model('configuracion_rrhh/departamentos_orm');
        $this->load->model('contabilidad/centros_orm');
        $this->load->model('contabilidad/cuentas_orm');
        $this->load->model('pagos/Pagos_orm');
        $this->load->model('pagos/Pago_metodos_pago_orm');
        $this->load->library('Repository/Pagos/Guardar_pago');
        $this->pagoGuardar = new Guardar_pago;

        Carbon::setLocale('es');
        setlocale(LC_TIME, 'es_ES.utf8');
        $this->load->library('orm/catalogo_orm');
        $this->load->dbutil();

        $uuid_empresa       = $this->session->userdata('uuid_empresa');
        $this->empresa      = Empresa_orm::findByUuid($uuid_empresa);
        $this->id_usuario   = $this->session->userdata("id_usuario");
        $this->empresa_id   = $this->empresa->id;

       	$this->colaboradoresRepository = new ColaboradoresRepository();
        $this->planillaRepository = new PlanillaRepository();
        $this->configuracionPlanillaLiquidacionRepository = new ConfiguracionPlanillaLiquidacionRepository();
        $this->liquidacionPagadaRepository = new LiquidacionPagadaRepository();
        $this->pagadasRepository = new PagadasRepository();
        $this->planillaRegularRepository = new PlanillaRegularRepository();
        $this->CalculosRepository = new CalculosRepository();
        $this->CalculosCerradoRepository = new CalculosCerradoRepository();
        $this->PagosPlanilla = new PagosPlanilla();
        $this->PagosPlanillaVacacion = new PagosPlanillaVacacion();
        $this->ModulosRepository = new ModulosRepository();
        $this->CentrosContablesRepository = new CentrosContablesRepository();
        $this->RrhhAreasRepository = new RrhhAreasRepository();
        $this->CrearPlanilla = new CrearPlanilla();
        $this->Toast = new Toast;
        $this->VacacionRepository = new VacacionRepository();
        $this->PagadasVacacionesRepository = new PagadasVacacionesRepository();
        $this->ModelVacacionRep = new ModelVacacionRep();
        $this->CuentaPlanillaRepository = new CuentaPlanillaRepository();
  	}


     public function listar() {
     	if(!$this->auth->has_permission('acceso', 'planilla/listar')){

     		$mensaje = array('estado'=>500, 'mensaje'=>'<b>¡Error!</b> Usted no cuenta con permiso');
     		$this->session->set_flashdata('mensaje', $mensaje);
     	}



       	$data = array();

     	$lista_deducciones =  Centros_orm::where('empresa_id','=', $this->empresa_id )
     	->get(array('padre_id'));

     	$cat_centros = Capsule::select(Capsule::raw("SELECT * FROM cen_centros WHERE empresa_id = :empresa_id1 AND estado='Activo' AND id NOT IN (SELECT padre_id FROM cen_centros WHERE empresa_id = :empresa_id2 AND estado='Activo') ORDER BY nombre ASC"), array(
     			'empresa_id1' => $this->empresa_id,
     			'empresa_id2' => $this->empresa_id
     	));
     	$cat_centros = (!empty($cat_centros) ? array_map(function($cat_centros){ return array("id" => $cat_centros->id, "nombre" => $cat_centros->nombre); }, $cat_centros) : "");

       	 $data = array(
     	 		"codigos" => Planilla_orm::lista_codigos($this->empresa_id),
     	 		"centros" => $cat_centros,
     	 		"estados" => Estado_orm::where('identificador','=', 'estado')->get(array('id_cat','etiqueta')),
          'areas_negocio' => $this->RrhhAreasRepository->getAll(["empresa_id"=>$this->empresa_id])
      	 );
       	$this->_css();
     	$this->_js();

     	if(!is_null($this->session->flashdata('mensaje'))){
      		$mensaje = json_encode($this->session->flashdata('mensaje'));
     	}else{
      		$mensaje = '';
     	}
    	$this->assets->agregar_var_js(array(
     			"toast_mensaje" => $mensaje
     	));
      	$breadcrumb = array(
     			"titulo" => '<i class="fa fa-institution"></i> Planilla',
     			"filtro" => false,
     			"menu" => array(
     				"nombre" => $this->auth->has_permission('acceso', 'planilla/crear')?"Crear":'',
					"url"	 => $this->auth->has_permission('acceso', 'planilla/crear')?"planilla/crear":'',
     				"opciones" => array()
     			),
          "ruta" => array(
            0 => array(
                "nombre" => "Nómina",
                "activo" => false,
             ),
               1=> array(
                  "nombre" => '<b>Planillas</b>',
                  "activo" => false
               )
          ),
     	);
      	if ($this->auth->has_permission('listar__exportarPlanilla', 'planilla/listar')){
	    	      $breadcrumb["menu"]["opciones"]["#ExportarBtnComision"] = "Exportar";
      	}

      $this->template->agregar_titulo_header('Planilla: Listado de Planilla');
     	$this->template->agregar_breadcrumb($breadcrumb);
     	$this->template->agregar_contenido($data);
     	$this->template->visualizar($breadcrumb);
     }

     function crear($planilla_uuid = NULL) {


     	if($planilla_uuid!=NULL){ //Ver Detalles
     		if(!$this->auth->has_permission('acceso', 'planilla/ver/(:any)')){  redirect(base_url('/')); }
     	}else{
     		if(!$this->auth->has_permission('acceso', 'planilla/crear')){ redirect(base_url('/'));}
     	}

     	$data = $centro_contables = $mensaje =  array();
     	$nombre_planilla = '';

     //	$data['lista_colaboradores'] = $this->colaboradoresRepository->getAll(["empresa_id"=>$this->empresa_id]);
      $cuentas_debito = Cuentas_orm::transaccionalesDeEmpresa($this->empresa_id)->deTipoDeCuenta([2,5,6])->activas()->orderBy('codigo')->get();
      $cuentas = Cuentas_orm::transaccionalesDeEmpresa($this->empresa_id)->deTipoDeCuenta([2])->activas()->orderBy('codigo')->get();
     	$data['areas_negocio'] = $this->RrhhAreasRepository->getAll(["empresa_id"=>$this->empresa_id]);
     	$data['ciclos'] = $this->ModulosRepository->getCicloPlanilla();
     	$data['tipo_planilla'] = $this->ModulosRepository->getTipoPlanilla();
     	$data['deducciones'] = Deducciones_orm::where("estado_id","=",1)->where("empresa_id","=",$this->empresa_id)->get();
     	$data['acumulados'] = Acumulados_orm::where("estado_id","=",1)->where("empresa_id","=",$this->empresa_id)->get();
     	$data['cuentas_pasivos'] = $cuentas;
     	$data['cuentas_debito'] = $cuentas_debito;
      $clause_centro= array('transaccionales'=>true, 'empresa_id' => $this->empresa_id);//,'estado'=>'Activo'
      $data['centro_contables'] = $this->CentrosContablesRepository->get($clause_centro);
     	$this->_css();
     	$this->_js();

     	if($planilla_uuid!=NULL){ //Ver Detalles

     		$permiso_editar =  $this->auth->has_permission('ver__editarPlanilla', 'planilla/ver/(:any)')?1:0;
     		$planilla_info = $colaboradores_noactivados = array();
     		$estado ='';
     		$cantidad_no_validados = -1;

        $planilla_info = $this->planillaRepository->findByUuid($planilla_uuid);


      	$fecha1 =  date("d/m/Y", strtotime($planilla_info->rango_fecha1));
     		$fecha2 =  date("d/m/Y", strtotime($planilla_info->rango_fecha2));
     		$interval = $this->date_diff($fecha1, $fecha2);
        $permiso_validacion = 1;
     		//Listado de catalogos en el subgrid
     		$data['recargos'] = Recargos_orm::listarRecargosPorEmpresa($this->empresa_id);
     		$data['beneficios'] = Beneficios_orm::listarBeneficiosPorEmpresa($this->empresa_id);
     		$data['cuenta_costos'] = Cuentas_orm::transaccionalesDeEmpresa($this->empresa_id)->activas()->orderBy('codigo')->get();
     		$data['cuenta_gastos'] = Cuentas_orm::transaccionalesDeEmpresa($this->empresa_id)->activas()->orderBy('codigo')->get();

     		$nombre_planilla = $planilla_info->codigo;
     		$estado = $planilla_info->estado->valor;

     		if(trim($estado) =='cerrada' || trim($estado) =='anulado'){
     			$permiso_editar = 0; //Se desatciva las opciones
     		}

        $array_colaboradores_activos = array_pluck($planilla_info->colaboradores_planilla->toArray(), 'colaborador_id');
        $array_centros = array_pluck($planilla_info->centros_contables->toArray(), 'centro_contable_id');
        $colaboradores_todos = $this->colaboradoresRepository->getAll(
             ["empresa_id"=>$planilla_info->empresa_id,
              "centro_contable_id"=>$array_centros,
              "departamento_id" =>!empty($planilla_info->area_negocio)?array($planilla_info->area_negocio):array(),
              "ciclo_id" =>$planilla_info->ciclo_id
            ]
         );
          $colaboradores_noactivados = $colaboradores_todos->filter(function ($value, $key) use($array_colaboradores_activos) {
              if(!in_array($value->id, $array_colaboradores_activos)) {
                return $value;
              }
        });
     		$data["colaboradores_noactivados"] = $colaboradores_noactivados;
     		$data["info"]  = $planilla_info;
     		$data["info"]['key'] = $nombre_planilla;
     		$data['tipo_planilla_creacion'] = $planilla_info->tipo->valor;

        $planilla_coment = $this->planillaRepository->findByUuid($planilla_uuid);
        $planilla_coment->load('comentario_timeline','planillas_asignados');

     		$this->assets->agregar_var_js(array(
     				"rango1" =>$fecha1,
     				"rango2" => $fecha2,
     				"permiso_editar" => $permiso_editar,
     				"tipo_planilla_creacion" => $planilla_info->tipo->valor,
     				"tipo_planilla_id" => $planilla_info->tipo->id_cat,
     				"planilla_id" =>!empty($planilla_info->id)?$planilla_info->id:0,
     				"estado_planilla" => $planilla_info->estado->valor,
            "coment" =>(isset($planilla_coment->comentario_timeline)) ? $planilla_coment->comentario_timeline : "",
            "vista" => "ver"
     		));

     		$breadcrumb = array(
     				"titulo" => '<i class="fa fa-institution"></i> Planilla '.$nombre_planilla,
     				"filtro" => false,
     				"menu" => array(
     						"nombre" => 'Acci&oacute;n',
     						"url"	 => '#',
     						"opciones" => array()
     				),
            "ruta" => array(
              0 => array(
                  "nombre" => "Nómina",
                  "activo" => false,
              ),
                1 => array(
                    "nombre" => "Planillas",
                    "activo" => false,
                    "url" => 'planilla/listar'
                ),
                2=> array(
                    "nombre" => '<b>Detalle</b>',
                    "activo" => true
                )
            ),
     		);
      		if( $data['tipo_planilla_creacion'] == "vacaciones"  || $data['tipo_planilla_creacion'] == "liquidaciones" || $data['tipo_planilla_creacion'] == "licencias" )
     		{

     			if(!empty($planilla_info)){

      				$id_array = array();
      				foreach($planilla_info[$planilla_info->tipo->valor] as $key=>$value) {
     					      $id_array[] = $value['id'];
     				 }

     				$data['lista_seleccionada'][$data['tipo_planilla_creacion']] = $id_array;
     			}
      		if ( $estado == 'validada' ){
    	     			$breadcrumb["menu"]["opciones"]["#exportarPlanillaAbierta"] = "Exportar";
         				/*if($planilla_info->tipo->valor == "vacaciones"){
         					$breadcrumb["menu"]["opciones"]["#pagarVacacionBtn"] = "Cerrar planilla";
         				}*/
         				  if($planilla_info->tipo->valor == "liquidaciones"){
         					$breadcrumb["menu"]["opciones"]["#pagarLiquidacionBtn"] = "Cerrar planilla";
         				}
         				else if($planilla_info->tipo->valor == "licencias"){
         					$breadcrumb["menu"]["opciones"]["#pagarLicenciaBtn"] = "Cerrar planilla";
         				}
                 else if($planilla_info->tipo->valor == "xiii_mes"){
         					$breadcrumb["menu"]["opciones"]["#pagarDecimoBtn"] = "Cerrar planilla";
         				}
     			}
     			else if( $estado == 'cerrada' ){
     				$breadcrumb["menu"]["opciones"]["#exportarPlanillaRegularCerrada"] = "Exportar";
     				$breadcrumb["menu"]["opciones"]["#imprimirTalonarios"] = "Imprimir talonario";
     			}
     			else if( $estado == 'abierta' ){
     				$breadcrumb["menu"]["opciones"]["#exportarPlanillaAbierta"] = "Exportar";
     				$breadcrumb["menu"]["opciones"]["#imprimirTalonarios"] = "Imprimir talonario";
      			}
          else{
				    $breadcrumb["menu"]["opciones"]["#exportarPlanillaRegularCerrada"] = "Exportar";
     				$breadcrumb["menu"]["opciones"]["#imprimirTalonarios"] = "Imprimir talonario";
 			    }

     		}
     		else{//En caso de que sea regular la planilla

     			if($estado == 'abierta'){
     				$breadcrumb["menu"]["opciones"]["#agregarColaborador"] = "Agregar colaborador";
     				$breadcrumb["menu"]["opciones"]["#exportarPlanillaAbierta"] = "Exportar";
            $breadcrumb["menu"]["opciones"]["#validaMultipleColab"] = "Validar todos";
     			}else if( $estado == 'validada' ){
     				$breadcrumb["menu"]["opciones"]["#pagarPlanilla"] = "Cerrar planilla";
            $breadcrumb["menu"]["opciones"]["#agregarColaborador"] = "Agregar colaborador";
     				$breadcrumb["menu"]["opciones"]["#exportarPlanillaAbierta"] = "Exportar";
     			}
     			else if( $estado == 'cerrada'){
     				$breadcrumb["menu"]["opciones"]["#exportarPlanillaRegularCerrada"] = "Exportar";
     				$breadcrumb["menu"]["opciones"]["#imprimirTalonarios"] = "Imprimir talonario";
     			}else{
            $breadcrumb["menu"]["opciones"]["#exportarPlanillaRegularCerrada"] = "Exportar";
     				$breadcrumb["menu"]["opciones"]["#imprimirTalonarios"] = "Imprimir talonario";
          }

     		}
     		$this->assets->agregar_js(array(
     				'public/assets/js/modules/planilla/editar.js',
     		));
        $this->assets->agregar_css(array(
            'public/assets/js/modules/planilla/editar.js',
        ));
     		$this->session->set_userdata(
     				array("estado_planilla"=>$planilla_info->estado->valor)
     		);
        $data['disabled'] = 'disabled';


     	}else{
          $data['disabled'] = '';
     		//echo "Creando planilla nueva";
     		$tipo_planilla_creacion = 'regular';
     		if(!empty($_POST)){ //Esta es la condicion para crear planillas Vacaciones/Liquidaciones
     			if(!empty($_POST['vacaciones'])){
     				$data['lista_seleccionada'] = $_POST;
     				$tipo_planilla_creacion = 'vacaciones';
     			}
     			else if(!empty($_POST['liquidaciones'])){
     				$data['lista_seleccionada'] = $_POST;
     				$tipo_planilla_creacion = 'liquidaciones';
     			}
     			else if(!empty($_POST['licencias'])){
     				$data['lista_seleccionada'] = $_POST;
     				$tipo_planilla_creacion = 'licencias';
     			}
     		}

        $data['tipo_planilla_creacion'] = $tipo_planilla_creacion;
        $collection = $data['tipo_planilla'];

        $filtered = $collection->filter(function ($value, $key) use ($tipo_planilla_creacion) {
               return $value->valor == $tipo_planilla_creacion;
        })->first();
        $data["info"]['tipo']['id_cat'] = $filtered->id_cat;
     		$this->assets->agregar_var_js(array(
     				"tipo_planilla_creacion" => $tipo_planilla_creacion,
     				"tipo_planilla_id" => $filtered->id_cat
     		));

     		$this->assets->agregar_js(array(
     				'public/assets/js/modules/planilla/crear.js'
     		));

     		$breadcrumb = array(
     				"titulo" => '<i class="fa fa-institution"></i> Planilla ',
     				"filtro" => false,
            "ruta" => array(
              0 => array(
                  "nombre" => "Nómina",
                  "activo" => false,
              ),
                1 => array(
                    "nombre" => "Planillas",
                    "activo" => false,
                    "url" => 'planilla/listar'
                ),
                2=> array(
                    "nombre" => '<b>Crear</b>',
                    "activo" => true
                )
            )
     		);
     	}
	    $this->session->set_userdata(array('uuid_planilla'=>$planilla_uuid)); //Se usa en accione personales
     	$this->template->agregar_breadcrumb($breadcrumb);
     	$this->template->agregar_contenido($data);
     	$this->template->visualizar();
     }

     public function ajax_listar_colaboradores_dependiente() {
       if(!$this->input->is_ajax_request()){
         return false;
       }
        $colaboradores = [];
        $centro_contable_id = $this->input->post ('centro_contable_id', true);
        $ciclo_id           = $this->input->post ('ciclo_id', true);
        $area_negocio_id           = $this->input->post ('area_negocio_id', true);

        $fecha_inicio_planilla           = $this->input->post ('fecha_inicio_planilla', true);
        $fecha_final_planilla           = $this->input->post ('fecha_final_planilla', true);

        $fecha_final_planilla= empty($fecha_final_planilla)?"": Carbon::parse(str_replace("/", "-", $fecha_final_planilla));

        if($centro_contable_id != '' || $ciclo_id != ''  || $area_negocio_id != '' ){
            $colaboradores = $this->colaboradoresRepository->getAll(
                [
                    "empresa_id" => $this->empresa_id,
                    "centro_contable_id" => $centro_contable_id,
                    "departamento_id" => !empty($area_negocio_id) ? array($area_negocio_id) : array(),
                    "ciclo_id" => $ciclo_id,
                    "fecha_inicio_planilla" => $fecha_inicio_planilla,
                    "fecha_final_planilla" => $fecha_final_planilla,

                ]
            );

        }

       echo json_encode(array(
           "response"=>true,
           "colaboradores" => $colaboradores
         ));
        exit;
     }

     public function ajax_listar_decimo() {
     	if(!$this->input->is_ajax_request()){
     		return false;
     	}
     	$clause = array(
     			"empresa_id" =>  $this->empresa_id
     	);
     	$estado_planilla =   $this->session->userdata('estado_planilla');

     	$planilla_id 			= $this->input->post('planilla_id', true);
     	$colaboradores 			=  Planilla_colaborador_orm::where('planilla_id','=', $planilla_id )
     	->get();

     	if(!empty($colaboradores->toArray())){
     		foreach ($colaboradores->toArray() AS $i => $row){

     			$colaborador[] = $row['colaborador_id'];
     			$estado[$row['colaborador_id']] = $row['estado_ingreso_horas'];
     		}
     		$clause["colaborador"] =  $colaborador;
     	}else{
     		$clause["colaborador"] =  array(-1);
     	}

     	list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();

     	$count = Colaboradores_orm::listar($clause, NULL, NULL, NULL, NULL)->count();

     	list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

     	$rows = Colaboradores_orm::listar($clause, $sidx, $sord, $limit, $start);
     	//Constructing a JSON
     	$response = new stdClass();
     	$response->page     = $page;
     	$response->total    = $total_pages;
     	$response->records  = $count;
     	$i=$salario_bruto=0;

     	if(!empty($rows->toArray())){
     		foreach ($rows->toArray() AS $i => $row){
      			$totalHorasPrint= 0;
     			$hidden_options = "";

     			$label_estado = '';
     			if($estado[$row['id']] == 2){
     				$label_estado = '<span class="label"  style="background-color: #5bc0de;color:#FFFFFF;">Por validar</span>';
     			}
     			else if($estado[$row['id']] == 3){
     				$label_estado = '<span class="label"  style="background-color: #5cb85c;color:#FFFFFF;">Validado</span>';
     			}
     			else if( $estado[$row['id']] == 1 || $estado[$row['id']] == 0){
     				$label_estado = '<span class="label"  style="background-color: #f0ad4e;color:#FFFFFF;">Pendiente</span>';
     			}

     			$salario_bruto += $row["salario_mensual"];
     			$uuid_colaborador = $row['uuid_colaborador'];
     			$nombre = Util::verificar_valor($row['nombre']);
     			$apellido = Util::verificar_valor($row['apellido']);


     			$link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row['id'] .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
     			if ($this->auth->has_permission('acceso', 'planilla/ver/(:any)')){

     				if($estado_planilla == "cerrada")
     				{
     					$hidden_options .= '<a href="'. base_url('planilla/ver-reporte-cerradas/'.$uuid_colaborador."~".$planilla_id) .'" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success">Ver detalle</a>';
              $link = '<a href="'. base_url('planilla/ver-reporte-cerradas/'.$uuid_colaborador."~".$planilla_id) .'" data-id="'. $row['id'] .'"  >'.$row['codigo'].'</a>';
     				}else {
     					$hidden_options .= '<a href="'. base_url('planilla/ver-reporte-decimo/'.$uuid_colaborador."~".$planilla_id) .'" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success">Ver detalle</a>';
              $link = '<a href="'. base_url('planilla/ver-reporte-decimo/'.$uuid_colaborador."~".$planilla_id) .'" data-id="'. $row['id'] .'" >'.$row['codigo'].'</a>';
     				}
     			}

     			if ($this->auth->has_permission('ver__eliminarColaborador', 'planilla/ver/(:any)')){
     				$hidden_options .= '<a href="#"    data-id="' . $row ['id'] . '" class="btn btn-block btn-outline btn-success" id="confirmEliminar" type="button">Eliminar</a>';
     			}
     			$response->rows[$i]["id"] = $row['id'];
      			$response->rows[$i]["cell"] = array(
     					$row['id'],
              $link,
              '<a href="'. base_url('colaboradores/ver/'. $uuid_colaborador) .'"  class="link" target="_blank">'.$row['nombre_completo'].'</a>',
     					Util::verificar_valor($row["centro_contable"]["nombre"]),
     					Util::verificar_valor($row['departamento']['nombre']),
     					Util::verificar_valor($row['cargo']['nombre']),
     					$link_option,
     					$hidden_options
     			);
     			$i++;
     		}
     	}
     	echo json_encode($response);
     	exit;
     }
     //Funcion  que se usa para el data entry
     public function ajax_listar_planilla_colaboradores() {

     	if(!$this->input->is_ajax_request()){
     		return false;
     	}
     	$estado_planilla =   $this->session->userdata('estado_planilla');
     	$clause = array(
     			"empresa_id" =>  $this->empresa_id
     	);

     	$colaborador = $estado  =  $totalHoras = array();
     	$cantidad_semanas 	    = $this->input->post('cantidad_semanas', true);
     	$ciclo_colaboradores 	  = $this->input->post('ciclo_colaboradores', true);
     	$planilla_id 			      = $this->input->post('planilla_id', true);
     	$tipo_planilla 			    = $this->input->post('tipo_planilla', true);
     	$colaboradores 			    =  Planilla_colaborador_orm::where('planilla_id','=', $planilla_id )
     	->get();

     	if(!empty($colaboradores->toArray())){
     		foreach ($colaboradores->toArray() AS $i => $row){

     			$colaborador[] = $row['colaborador_id'];
     			$estado[$row['colaborador_id']] = $row['estado_ingreso_horas'];
     			$totalHoras[$row['colaborador_id']] = Ingreso_horas_dias_orm::totalHoras($row['id']);
     		}
     		$clause["colaborador"] =  $colaborador;
     	}else{
     		$clause["colaborador"] =  array(-1);
     	}


     	list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();

     	$count = Colaboradores_orm::listar($clause, NULL, NULL, NULL, NULL)->count();

     	list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

     	$rows = Colaboradores_orm::listar($clause, $sidx, $sord, $limit, $start);
     	//Constructing a JSON
     	$response = new stdClass();
     	$response->page     = $page;
     	$response->total    = $total_pages;
     	$response->records  = $count;
     	$i=$salario_bruto=0;

     	if(!empty($rows->toArray())){
     		foreach ($rows->toArray() AS $i => $row){
     			$totalHorasPrint= 0;
     			$hidden_options = "";

     			$label_estado = '';
     			if($estado[$row['id']] == 2){
     				$label_estado = '<span class="label"  style="background-color: #5bc0de;color:#FFFFFF;">Por validar</span>';
     			}
     			else if($estado[$row['id']] == 3){
     				$label_estado = '<span class="label"  style="background-color: #5cb85c;color:#FFFFFF;">Validado</span>';
     			}
     			else if( $estado[$row['id']] == 1 || $estado[$row['id']] == 0){
     				$label_estado = '<span class="label"  style="background-color: #f0ad4e;color:#FFFFFF;">Pendiente</span>';
     			}

     			if($row['tipo_salario'] == 'Mensual'){
     				$totalHorasPrint = $row['horas_semanales']*$cantidad_semanas;

     			}else{
     				$totalHorasPrint = $totalHoras[$row['id']];
     			}

     			$salario_bruto += $row["salario_mensual"];
     			$uuid_colaborador = $row['uuid_colaborador'];
     			$nombre = Util::verificar_valor($row['nombre']);
     			$apellido = Util::verificar_valor($row['apellido']);


     			$link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row['id'] .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';

     			if($estado_planilla == 'abierta' ||  $estado_planilla == 'anulada' ||  $estado_planilla == 'validada'){
     				$link_detalle  = '<a href="'. base_url('planilla/ver-reporte2/'. $uuid_colaborador."~".$planilla_id) .'"   class="link">'.$row['codigo'].'</a>';
     				$hidden_options .= '<a href="'. base_url('planilla/ver-reporte2/'. $uuid_colaborador."~".$planilla_id) .'" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success">Ver detalle</a>';
     			}else{
     				$hidden_options .= '<a href="'. base_url('planilla/ver-reporte-cerradas/'. $uuid_colaborador."~".$planilla_id) .'" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success">Ver detalle</a>';
            $link_detalle  =   '<a href="'. base_url('planilla/ver-reporte-cerradas/'. $uuid_colaborador."~".$planilla_id) .'"   class="link">'.$row['codigo'].'</a>';
     			}

     			//if(persmiso){
     			if($row['tipo_salario'] != 'Mensual' && ( $estado[$row['id']] == 0 || $estado[$row['id']] == 1 || $estado[$row['id']] == 2)){
     				$hidden_options .= '<a href="#"    data-id="' . $row ['id'] . '" class="btn btn-block btn-outline btn-success" id="confirmValidar" type="button">Validar</a>';
     			}
     			//}
     			if ($this->auth->has_permission('ver__eliminarColaborador', 'planilla/ver/(:any)') && ($estado[$row['id']] != 3 || $row['tipo_salario'] == 'Mensual')){
     				$hidden_options .= '<a href="#"    data-id="' . $row ['id'] . '" class="btn btn-block btn-outline btn-success" id="confirmEliminar" type="button">Eliminar</a>';
     			}
     			$response->rows[$i]["id"] = $row['id'];

     			$response->rows[$i]["cell"] = array(
     					$link_detalle,
               '<a href="'. base_url('colaboradores/ver/'. $uuid_colaborador) .'"  class="link" target="_blank">'.$nombre. " ". $apellido.'</a>',
     					Util::verificar_valor($row["centro_contable"]["nombre"]),
     					Util::verificar_valor($row['cedula']),
     					Util::verificar_valor($row['tipo_salario']),
     					$label_estado,
     					$totalHorasPrint,
     					$link_option,
     					$hidden_options
     			);
     			$i++;
     		}
     	}
     	echo json_encode($response);
     	exit;
     }

     public function ajax_listar_acumulados() {
     	$i = $total_calculo = $total_calculo_restante=  0;
     	$tabla = $this->session->userdata('tabla_acumulados');

     	$response = new stdClass();
     	$response->records  = count($tabla);
     	$response->total = count($tabla)>0?1:0;

     	if(!empty($tabla)){
     		foreach ($tabla AS $row){

     			$response->rows[$i]["cell"] = array(
     					$row['nombre'],
     					number_format($row['acumulado_planilla'],2),
     					number_format($row['acumulado'],2),
     			);
     			//$total_calculo_restante += $row['acumulado'];
     			++$i;
     		}

     	/*	$response->rows[$i]["cell"] = array(
     				"<b>Total</b>",
     				"<b>".number_format($total_calculo_restante,2)."</b>"
     		);*/
     	}
     	echo json_encode($response);
     	exit;
     }

     public function ajax_listar_deducciones() {

     	$i = $total_calculo = $total_saldo = 0;
     	$tabla = $this->session->userdata('tabla_deducciones');

     	$response = new stdClass();
     	$response->records  = count($tabla);
     	$response->total = count($tabla)>0?1:0;


     	if(!empty($tabla)){
     		foreach ($tabla AS $row){
          if (strtoupper($row['nombre']) == strtoupper('Descuentos directos')) { // no se deben mostrar los descuentos directos en la tabla de deducciones.
          continue;
        }
     			$response->rows[$i]["cell"] = array(
     					$row['nombre'],
     					number_format($row['descuento'],2),
     					number_format($row['saldo'],2)
     			);
     			$total_calculo += $row['descuento'];
     			$total_saldo += $row['saldo'];
     			++$i;
     		}

     		$response->rows[$i]["cell"] = array(
     				"<b>Total</b>",
     				"<b>".number_format($total_calculo,2)."</b>" ,
     				"<b>".number_format($total_saldo,2)."</b>"
     		);
     	}
     	echo json_encode($response);
     	exit;
     }

     public function ajax_listar_deducciones_liquidacion() {
     	$i = $total_calculo = $total_saldo = 0;
     	$tabla = $this->session->userdata('tabla_deducciones');
      	$response = new stdClass();
     	$response->records  = count($tabla);
     	$response->total = count($tabla)>0?1:0;


     	if(!empty($tabla)){
     		foreach ($tabla AS $row){

     			$response->rows[$i]["cell"] = array(
      					$row['nombre'],
     					$row['descuento'],
     					$row['tipo']
      			);
     			$total_calculo += $row['descuento'];
      			++$i;
     		}
      	}
      	echo json_encode($response);
     	exit;
     }

     public function ajax_listar_calculos_liquidacion() {
     	$i = 0;
      	$tabla = $this->session->userdata('calculos');
     	$response = new stdClass();
     	$response->records  = count($tabla);
     	$response->total = count($tabla)>0?1:0;


     	if(!empty($tabla)){
     		foreach ($tabla AS $row){
     			$response->rows[$i]["cell"] = array(
     					$row['detalle'],
     					$row['monto']
     			);
     			++$i;
     		}
      	}
     	echo json_encode($response);
     	exit;
     }
     public function ajax_listar_calculos() {

        	//Constructing a JSON
     	$response = new stdClass();
     	$response->page     = 1;
     	$response->total    = 1;
     	$response->records  = 4;
     	$i =  0;

     	$titulo_array[] = "Salario mensual promedio";
     	$titulo_array[] = "Salario anual promedio";
     	$titulo_array[] = "Total Devengado";
	 	  $titulo_array[] = "Indemnizaci&oacute;n Proporcional";

 		$calculo[] = $this->session->userdata('salario_mensual_promedio');
		$calculo[] = $this->session->userdata('salario_anual_promedio');
		$calculo[] = $this->session->userdata('total_devengado');
		$calculo[] = $this->session->userdata('indemnizacion_proporcional');

        if(!empty($titulo_array)){
     		foreach ($titulo_array   AS $row){
     			$valor = $calculo[$i];
       			$response->rows[$i]["cell"] = array(
     					$titulo_array[$i],
     					'$'.number_format($valor,2)
      			);
     		 	++$i;
     		}
       	}
        	echo json_encode($response);
     	exit;
     }
     //Funcion Nueva
      public function ajax_listar_ingresos_liquidacion() {
     	$i = $total_calculo = 0;
     	$tabla = $this->session->userdata('tabla_ingresos');

     	$response = new stdClass();
     	$response->records  = count($tabla);
     	$response->total = count($tabla)>0?1:0;


     	if(!empty($tabla)){
     		foreach ($tabla AS $row){
     			$response->rows[$i]["cell"] = array(
     					$row['detalle'],
      					$row['calculo']
     			);
     			$total_calculo += $row['calculo'];
      			++$i;
     		}
       	}
       	$response->total_calculo = $total_calculo;
     	echo json_encode($response);
     	exit;
     }

     //Funcion Nueva
     public function ajax_listar_ingresos() {
     	$i = $total_calculo = 0;
      	$tabla = $this->session->userdata('tabla_ingresos');

      /*  if(preg_match("/ver-reporte-cerradas/i", $_SERVER['HTTP_REFERER'])){
          $ingresos = collect($tabla)->flatten(1);
          $tabla = $ingresos->values()->all();
        }*/

      	$response = new stdClass();
      	$response->records  = count($tabla);
      	$response->total = count($tabla)>0?1:0;


     	if(!empty($tabla)){
     		foreach ($tabla AS $row){

       			$response->rows[$i]["cell"] = array(
     					$row['detalle'],
     					$row['cantidad_horas'],
     					number_format($row['rata'],2),
     					number_format($row['calculo'],2)
     			);
     			$total_calculo += $row['calculo'];
     			++$i;
     		}

     		$response->rows[$i]["cell"] = array(
     				"<b>Total</b>",
     				"",
     				"",
     				number_format($total_calculo,2)
     		);
     	}
     	echo json_encode($response);
     	exit;
      }


      public function ajax_listar_ingresos_decimo() {
      	$i = $total_calculo = 0;
      	$tabla = $this->session->userdata('tabla_ingresos');

      	$response = new stdClass();
      	$response->records  = count($tabla);
      	$response->total = count($tabla)>0?1:0;


      	if(!empty($tabla)){
      		foreach ($tabla AS $row){
      			$response->rows[$i]["cell"] = array(
      					$row['detalle'],
       					"$ ".number_format($row['calculo'],2)
       			);
      			$total_calculo += $row['calculo'];
      			++$i;
      		}

      		$response->rows[$i]["cell"] = array(
      				"<b>Total Ingresos</b>",
       				number_format($total_calculo,2)
      		);
      		$response->rows[$i+1]["cell"] = array(
      				"<b>Decimo tercer mes (8.333%)</b>",
      				number_format($total_calculo*0.08333,2)
      		);
      	}
      	echo json_encode($response);
      	exit;
      }

      public function ajax_listar_descuentos_liquidacion() {
      	$i = $total_calculo =   0;

      	$tabla = $this->session->userdata('tabla_descuentos');

      	$response = new stdClass();
      	$response->records  = count($tabla);
      	$response->total = count($tabla)>0?1:0;

      	if(!empty($tabla)){
      		foreach ($tabla AS $row){
      			$response->rows[$i]["cell"] = array(
      					$row['codigo'],
      					$row['acreedor'],
      					$row['monto'],
      					$row['tipo'],
      					$row['monto_adeudado']
      			);
       			++$i;
      		}

      	}
      	echo json_encode($response);
      	exit;
      }

      public function ajax_listar_descuentos_directos() {
       	$i = $total_calculo = $total_calculo_restante=  0;

      	$tabla = $this->session->userdata('tabla_descuentos_directos');

      	$response = new stdClass();
       	$response->records  = count($tabla);
       	$response->total = count($tabla)>0?1:0;

      	if(!empty($tabla)){
      		foreach ($tabla AS $row){
      			 $response->rows[$i]["cell"] = array(
      					$row['codigo'],
      					$row['acreedor'],
      					number_format($row['monto_ciclo'],2),
      					number_format($row['saldo_restante'],2)
      			);
      			 $total_calculo += $row['monto_ciclo'];
      			 $total_calculo_restante += $row['saldo_restante'];
      			++$i;
      		}
       		$response->rows[$i]["cell"] = array(
      				"<b>Total</b>",
      				"",
      				"<b>".number_format($total_calculo,2)."</b>" ,
      				"<b>".number_format($total_calculo_restante,2)."</b>"
       		);
      	}
      	echo json_encode($response);
      	exit;
      }


     public function ajax_listar_planilla() {
     	$clause =  array();

      $centro_contable_id 	= $this->input->post('centro_contable_id', true);
     	$estado_id 				= $this->input->post('estado_id', true);
     	$fecha1 				= $this->input->post('fecha1', true);
     	$fecha2 				= $this->input->post('fecha2', true);
     	$area_negocio 			= $this->input->post('area_negocio', true);
      $codigo 			= $this->input->post('codigo', true);
     	$clause["activo"] = 1;
     	$clause["empresa_id"] = $this->empresa_id;

     	if( !empty($fecha1) && !empty($fecha2)){
     		$fecha_inicio = explode("/", $fecha1);
     		$fecha_final  = explode("/", $fecha2);

     		$inicio = $fecha_inicio[2].'-'.$fecha_inicio[1].'-'.$fecha_inicio[0];
     		$fin = $fecha_final[2].'-'.$fecha_final[1].'-'.$fecha_final[0];

     		$clause["rango_fecha1"] = array('=', $inicio);
     		$clause["rango_fecha2"] = array('=', $fin);
     	}

     	if( !empty($centro_contable_id)){
     		$clause["sub_centro_contable_id"] = $centro_contable_id;
     	}
     	if( !empty($area_negocio)){
     		$clause["area_negocio"] = $area_negocio;
     	}
      	if( !empty($estado_id)){
     		$clause["estado_id"] = $estado_id;
     	}
      if( !empty($codigo)){
        $clause["codigo"] = $codigo;
      }
     	list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
     	$count = Planilla_orm::listar($clause, NULL, NULL, NULL, NULL)->count();
     	list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
     	$rows = Planilla_orm::listar($clause, $sidx, $sord, $limit, $start);

     	//Constructing a JSON
     	$response = new stdClass();
     	$response->page     = $page;
     	$response->total    = $total_pages;
     	$response->records  = $count;
     	$i=0;
     	$estado = "DES";
     	$etiqueta = (!empty($estado[0]->etiqueta))?$estado[0]->etiqueta:'';


     	if(!empty($rows->toArray())){
     		foreach ($rows->toArray() AS $i => $row){



     			$uuid_planilla = $row['uuid_planilla'];
     			$estado = $link_detalles = '';

 	        $estado = '<span style="color:white; background-color:'.$row['estado']['color_estado'].'" class="btn btn-xs btn-block">'.$row['estado']['etiqueta'].'</span>';

     			$hidden_options = "";
     			$link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row['id'] .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';


     			if ($this->auth->has_permission('acceso', 'planilla/ver/(:any)')){
     				$hidden_options .= '<a href="'. base_url('planilla/ver/'. $uuid_planilla) .'" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success">Ver detalle</a>';
     				$link_detalles = '<a  style="color:blue; text-decoration: underline;" href="'. base_url('planilla/ver/'. $uuid_planilla) .'">'.$row['codigo'].'</a>';
     			}
     			else{
     				$link_detalles =  $row['codigo'];
     			}
      				if ($this->auth->has_permission('listar__anularPlanilla', 'planilla/listar')){
      					if($row['estado']['valor'] == 'abierta' ||  $row['estado']['valor'] == 'validada'){
     						$hidden_options .= '<a id="confirmAnular" href="'. base_url('planilla/ver/'. $uuid_planilla) .'" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success">Anular</a>';
      					}
                if( $row['estado']['valor'] == 'validada'){

                      if($row['tipo']['valor'] == 'regular' || $row['tipo']['valor'] == 'xiii_mes'){
                 				$hidden_options .= '<a id="cerrarPlanilla" data-tipo="regular" href="#" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success">Cerrar</a>';
                 			}
                 			else if($row['tipo']['valor'] == 'vacaciones'){
                 				$hidden_options .= '<a id="cerrarPlanillaVacacion" data-tipo="vacacion"  href="#" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success">Cerrar</a>';
                  		}
       					}


      				}
       			$cantidad_acciones = 0;
      			if($row['tipo']['valor'] == 'regular' || $row['tipo']['valor'] == 'xiii_mes'){
      				$cantidad_acciones = count($row['colaboradores']);
              if ($row['estado']['valor'] == 'cerrada'){
                   $hidden_options .= '<a id="pagarPlanilla" href="#" data-salario="$'. number_format($row['salario_neto'],2).'"  data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success">Pagar</a>';
              }
      			}
      			else if($row['tipo']['valor'] == 'vacaciones'){
      				$cantidad_acciones = count($row['vacaciones']);
              if ($row['estado']['valor'] == 'cerrada'){
                   $hidden_options .= '<a id="pagarPlanilla" href="#" data-salario="$'. number_format($row['salario_neto'],2).'"  data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success">Pagar</a>';
              }
       			}
      			else if($row['tipo']['valor'] == 'liquidaciones'){
      				$cantidad_acciones = count($row['liquidaciones']);
      			}
      			else if($row['tipo']['valor'] == 'licencias'){
      				$cantidad_acciones = count($row['licencias']);
      			}

     			$rango1 =  ($row['rango_fecha1'] =='')?'':date("d/m/Y", strtotime( $row['rango_fecha1'] ));
     			$rango2 =  ($row['rango_fecha2'] =='')?'':'-'.date("d/m/Y", strtotime( $row['rango_fecha2'] ));

     			//$nombre = $this->ajax_nombre_centro_contable($row['centro_contable_id'], $row['sub_centro_contable_id'], $row['area_negocio']);
      			if($row['fecha_pago'] !='0000-00-00 00:00:00'){
     				$fecha_pago  = date('d/m/Y', strtotime($row['fecha_pago']));
     			}else{
     				$fecha_pago ='Pendiente';
     			}
           $array_centros = array_pluck(array_pluck($row['centros_contables'],'centro_info'),'nombre');
      			$response->rows[$i]["id"] = $row['id'];
      			$response->rows[$i]["cell"] = array(
     					$row['id'],
     					$link_detalles,
     					$row['tipo']['etiqueta'],
     					$fecha_pago,
     					$rango1.$rango2,
     					count($array_centros)<5? $array_centros: '<a href="javascript:;" title="'.implode("'",$array_centros).'">' .count($array_centros).' Centros seleccionados </a>',
     					$cantidad_acciones,
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
     //Vacaciones Dic 2016
      public function ajax_crear_planillaNoRegulares() {
     	Capsule::beginTransaction();

     	try {

     		$campos = $_POST;
       		//Obtener el id_empresa de session
     		$uuid_empresa = $this->session->userdata('uuid_empresa');
     		$empresa = Empresa_orm::findByUuid($uuid_empresa);

     		$fieldset = array(
     				"codigo" 	 => 'PL0'.(int)date("y").$this->ajax_cargar_numero_secuencial(),
     				"identificador" 	 =>  'PL',
     				"semana" 			 =>  0,
     				"ano" 			 	  => (int)date("y"),
     				"fecha_pago" 		 =>  '',
      			"empresa_id" 		 =>  $this->empresa_id,
     				"activo" 	  		 =>  1,
     				"estado_id" 		 =>  29,
     				"pasivo_id" 		 =>  $campos['pasivo_id'],
     				"cuenta_debito_id" 		 =>  $campos['cuenta_debito_id'],
     				"fecha_creacion" 	 =>  date("Y-m-d H:m:s"),
      			"tipo_id" 		 	 =>  $campos['tipo_id'],
            "total_colaboradores"=>count($campos['seleccionados'])
     		);

     		if(!empty($campos['seleccionados'])){
     			foreach ($campos['seleccionados'] as $id_accion_personal){
     				$accion = Accion_personal_orm::find( $id_accion_personal );
     				$acciones_selecccionados[] = $accion->accionable_id;
     			}
     		}

     		if($campos['tipo_creacion'] == 'liquidaciones'){

     			$fieldset['secuencial'] 		= $this->ajax_cargar_numero_secuencial();
     			$fieldset["uuid_planilla"] = Capsule::raw("ORDER_UUID(uuid())");
     			$planilla = Planilla_orm::create($fieldset);

     			$planilla->liquidaciones()->attach($acciones_selecccionados);

     		}else if($campos['tipo_creacion'] == 'vacaciones'){

     			$fieldset['secuencial'] 		= $this->ajax_cargar_numero_secuencial();
     			$fieldset["uuid_planilla"] = Capsule::raw("ORDER_UUID(uuid())");
      			$planilla = Planilla_orm::create($fieldset);

      			$planilla->vacaciones()->attach($acciones_selecccionados);
     		}
     		else if($campos['tipo_creacion'] == 'licencias'){

     			$fieldset['secuencial'] 		= $this->ajax_cargar_numero_secuencial();
     			$fieldset["uuid_planilla"] = Capsule::raw("ORDER_UUID(uuid())");
     			$planilla = Planilla_orm::create($fieldset);

     			$planilla->licencias()->attach($acciones_selecccionados);
     		}

     			//--------- Parte de Deducciones
     			$deducciones = array();
     			if(!empty($campos['deducciones']))
     			{
     					//Recorrer los dependientes
     					$j=0;
     					foreach ($campos["deducciones"] AS $deduccion){
      						  $fieldset["fecha_creacion"] = date('Y-m-d H:i:s');
     						    $fieldset["deduccion_id"] 	= $deduccion;

     						    $deducciones[] 				= new Planilla_deducciones_orm($fieldset);
     						    $j++;
     					}
     				$planilla->deducciones()->saveMany($deducciones);
     			}


     			// -------- Parte de Acumualados
     			$acumulados = array();
     			if(!empty($campos['acumulados']))
     			{

     					$j=0;
     					foreach ($campos["acumulados"]  AS $acumulado){
     						//$fieldset = array();
     						$fieldset["fecha_creacion"] = date('Y-m-d H:i:s');
     						$fieldset["acumulado_id"] 	= $acumulado;

     						$acumulados[] 				= new Planilla_acumulados_orm($fieldset);
     						$j++;
     					}
      				$planilla->acumulados()->saveMany($acumulados);
     			}

     			unset($fieldset['centro_contable_id']);
     			unset($fieldset['secuencial']);




     	} catch(ValidationException $e){
     		// Rollback
     		Capsule::rollback();
     		$mensaje = array('estado'=>500, 'mensaje'=>'<b>Hubo un error tratando de crear la planilla.</b> ');
     		$this->session->set_flashdata('mensaje', $mensaje);
     		echo json_encode(array(
     				"response" => false
     				//"mensaje" => "Hubo un error tratando de crear la planilla."
     		));
     		exit;
     	}

     	Capsule::commit();

       	$mensaje = array('estado'=>200, 'mensaje'=>'&Eacute;xito! Se ha creado correctamente la planilla.');
     	$this->session->set_flashdata('mensaje', $mensaje);

     	echo json_encode(array(
     			"response" => true,
     	));
     	exit;
     }


  public function ajax_crear_planilla() {
     	Capsule::beginTransaction();

     	try {
     		$campos = $_POST;
     		$campos['empresa_id'] = $this->empresa_id;
        $planilla =   $this->CrearPlanilla->crear($campos);

        if($planilla->tipo_id == 79){
          $this->ajax_validar_planilla($planilla->id);
        }
     	} catch(ValidationException $e){
     		// Rollback
     		Capsule::rollback();
     		$mensaje = array('estado'=>500, 'mensaje'=>'<b>Hubo un error tratando de crear la planilla.</b> ');
     		$this->session->set_flashdata('mensaje', $mensaje);
     		echo json_encode(array(
     				"response" => false
     		));
     		exit;
     	}
     	Capsule::commit();

     	$mensaje = array('estado'=>200, 'mensaje'=>'&Eacute;xito! Se ha creado correctamente la planilla.');
     	$this->session->set_flashdata('mensaje', $mensaje);

     	echo json_encode(array(
     			"response" => true,
      	));
     	exit;
       }

     public function ajax_editar_planilla() {
     	// Just Allow ajax request
     	if (! $this->input->is_ajax_request ()) {
     		return false;
     	}

     	Capsule::beginTransaction();

     	try {
        $campos = $_POST;
        $planilla = $this->planillaRepository->find($campos['planilla_id']);
        $planilla =   $this->CrearPlanilla->editar($planilla, $campos);
        $this->ajax_validar_planilla($planilla->id);

     	} catch(ValidationException $e){

     		// Rollback
     		Capsule::rollback();
     		$mensaje = array('estado'=>500, 'mensaje'=>'<b>Hubo un error tratando de actualizar la planilla.</b> ');

     		echo json_encode(array(
     				"response" => false,
     				//"mensaje" => "Hubo un error tratando de actualizar la planilla."
     		));
     		exit;
     	}

     	Capsule::commit();
      $mensaje = array('estado'=>200, 'mensaje'=>'&Eacute;xito! Se ha actualizado correctamente las <<Planilla>> ');
     	$this->session->set_flashdata('mensaje', $mensaje);

     	echo json_encode(array(
     			"response" => true,
     	));
     	exit;
     }


     public function ajax_editar_planillaNoRegular_liquidacion() {
     	// Just Allow ajax request
     	if (! $this->input->is_ajax_request ()) {
     		return false;
     	}

     	Capsule::beginTransaction();

     	try {

     		$planilla_id			 = $this->input->post('planilla_id', true);
     		$pasivo_id				 = $this->input->post('pasivo_id', true);

     		$fieldsetPlanilla["pasivo_id"]	  = $pasivo_id;

     		Planilla_orm::where('id', '=',$planilla_id)->update($fieldsetPlanilla);

     	} catch(ValidationException $e){

     		// Rollback
     		Capsule::rollback();
     		$mensaje = array('estado'=>500, 'mensaje'=>'<b>Hubo un error tratando de actualizar la planilla.</b> ');

     		echo json_encode(array(
     				"response" => false,
     				//"mensaje" => "Hubo un error tratando de actualizar la planilla."
     		));
     		exit;
     	}

     	Capsule::commit();

     	$mensaje = array('estado'=>200, 'mensaje'=>'&Eacute;xito! Se ha actualizado correctamente las <<Planilla>> ');
     	$this->session->set_flashdata('mensaje', $mensaje);

      	echo json_encode(array(
     			"response" => true,
      	));
     	exit;
     }

     public function ajax_editar_planillaNoRegular() {
     	// Just Allow ajax request
     	if (! $this->input->is_ajax_request ()) {
     		return false;
     	}

     	Capsule::beginTransaction();

     	try {

       		$planilla_id			 = $this->input->post('planilla_id', true);
     		$pasivo_id				 = $this->input->post('pasivo_id', true);

      		$deducciones 			 = $this->input->post('deducciones', true);
     		$acumulados 			 = $this->input->post('acumulados', true);

     		$rango_fecha1 			     = $this->input->post('rango_fecha1', true);
     		$rango_fecha2 			     = $this->input->post('rango_fecha2', true);

     		$fecha1 = explode("/", trim($rango_fecha1));
     		$rango_1 = $fecha1[2].'-'.$fecha1[1].'-'.$fecha1[0];

     		$fecha2 = explode("/", trim($rango_fecha2));
     		$rango_2 = $fecha2[2].'-'.$fecha2[1].'-'.$fecha2[0];

     		$fecha_rango_1 =  date("Y-m-d", strtotime($rango_1));
     		$fecha_rango_2 =  date("Y-m-d", strtotime($rango_2));

      		$fieldsetPlanilla["pasivo_id"]	  = $pasivo_id;
      		$fieldsetPlanilla["rango_fecha1"] = $fecha_rango_1;
      		$fieldsetPlanilla["rango_fecha2"] = $fecha_rango_2;

     		Planilla_orm::where('id', '=', $planilla_id)->update($fieldsetPlanilla);


        	Planilla_deducciones_orm::where('planilla_id', $planilla_id)->delete();

     		if(!empty($deducciones['deducciones'])){
     			//--------- Parte de Deducciones
     			if(!empty($deducciones['deducciones']))
     			{
     				if(Util::is_array_empty($deducciones['deducciones']) == false){
     					$j=0;
     					foreach ($deducciones['deducciones'] AS $deduccion){
     						$fieldset["fecha_creacion"] = date('Y-m-d H:i:s');
     						$fieldset["deduccion_id"] 	= $deduccion;
     						$fieldset["planilla_id"] 	= $planilla_id;

     						Planilla_deducciones_orm::create($fieldset);
     						$j++;
     					}
     				}
     			}
     		}
     		Planilla_acumulados_orm::where('planilla_id', $planilla_id)->delete();
     		if(!empty($acumulados['acumulados'])){
      			// -------- Parte de Acumualados
      			if(!empty($acumulados['acumulados']))
     			{
     				if(Util::is_array_empty($acumulados['acumulados']) == false){
      					$j=0;
     					foreach ($acumulados['acumulados'] AS $acumulado){
      						$fieldset["fecha_creacion"] = date('Y-m-d H:i:s');
     						$fieldset["acumulado_id"] 	= $acumulado;
     						$fieldset["planilla_id"] 	= $planilla_id;

     						Planilla_acumulados_orm::create($fieldset);
     						$j++;
     					}
     				}
     			}
     		}
     	} catch(ValidationException $e){

     		// Rollback
     		Capsule::rollback();
     		$mensaje = array('estado'=>500, 'mensaje'=>'<b>Hubo un error tratando de actualizar la planilla.</b> ');

     		echo json_encode(array(
     				"response" => false,
     				//"mensaje" => "Hubo un error tratando de actualizar la planilla."
     		));
     		exit;
     	}

     	Capsule::commit();

     	$mensaje = array('estado'=>200, 'mensaje'=>'&Eacute;xito! Se ha actualizado correctamente las <<Planilla>> ');
     	$this->session->set_flashdata('mensaje', $mensaje);


     	echo json_encode(array(
     			"response" => true,
     			//"mensaje" => "Se ha actualizado satisfactoriamente."
     	));
     	exit;
     }



     public function formulario_pagoespecial_liquidacion($data=NULL) {

     	$this->load->view('formulariopagoespecial_liquidacion', $data);
     }

     public function ajax_nombre_centro_contable($centro_contable = NULL, $sub_centro_contable_id = NULL, $area_negocio_id = NULL) {
      	if (trim($centro_contable) == 0 && trim($sub_centro_contable_id)  == 0 &&  trim($area_negocio_id) ==0){
       		return 'Por Colaborador';
     	}
       	$cadena_nombre = $centro = $sub_centros = $area = '';

     	$centros = Capsule::select(Capsule::raw("SELECT c.nombre  AS centro_contable_nombre FROM cen_centros as c
    			 WHERE c.id = :centro_contable_id"),
     			array(
     					'centro_contable_id' =>  $centro_contable
     			)
     	);
      	if(!empty($centros[0]->centro_contable_nombre)){
     		$centro = $centros[0]->centro_contable_nombre;
     	}

      	$subcentros = Capsule::select(Capsule::raw("SELECT c.nombre  AS centro_contable_nombre FROM cen_centros as c
    			 WHERE c.id = :centro_contable_id"),
     			array(
     					'centro_contable_id' =>  $sub_centro_contable_id
     			)
     	);

     	if(!empty($subcentros[0]->centro_contable_nombre)){
     		$sub_centros = '/'.$subcentros[0]->centro_contable_nombre;
     	}

      	$area_negocio = Capsule::select(Capsule::raw("SELECT d.nombre  AS area FROM dep_departamentos as d
    			 WHERE d.id = :area_negocio_id"),
     			array(
     					'area_negocio_id' =>  $area_negocio_id
     			)
     	);
      	if(!empty($area_negocio[0]->area)){
     		$area = '/'.$area_negocio[0]->area;
     	}

      	$cadena_nombre = $centro.$sub_centros.$area;
      	return $cadena_nombre;
     }

     public function ajax_seleccionar_ingreso_horas() {

     	//Obtener el id_empresa de session
     	$uuid_empresa = $this->session->userdata('uuid_empresa');
     	$empresa = Empresa_orm::findByUuid($uuid_empresa);

     	$this->empresa_id = $empresa->id;

     	$planilla_id 	= $this->input->post('planilla_id', true);
     	$colaborador_id 	= $this->input->post('colaborador_id', true);


     	$planilla = Planilla_orm::with(array('centro'))
     	->where("id","=", $planilla_id)->get()->toArray();

     	$start_date 	=   $planilla[0]['rango_fecha1'];
     	$end_date 		=   $planilla[0]['rango_fecha2'];
      	$nuevafecha = new DateTime($end_date);
     	$nuevafecha->modify('+1 day');
     	$end_date =  $nuevafecha->format('Y-m-d');

      	$start    = new DateTime($start_date);
     	$end      = new DateTime($end_date);
     	$interval = new DateInterval('P1D'); // 1 day interval
     	$period   = new DatePeriod($start, $interval, $end);


     	//Sacando el Centro contable de este colaborador
     	$colaborador_centro = Colaboradores_orm::with(array('centro_contable'))
     	->where("id","=", $colaborador_id)->get()->toArray();
      	$centro_contable = isset($colaborador_centro[0]['centro_contable']['nombre'])?$colaborador_centro[0]['centro_contable']['nombre']:'';
     	$planilla_colaborador =  Planilla_colaborador_orm::where('planilla_id','=', $planilla_id )->where("colaborador_id",'=',$colaborador_id)
     	->get()->toArray();

      	$planilla_colaborador_id = $planilla_colaborador[0]['id'];


     	 $horas_ingresadas = array();

     	$result_horas = Capsule::table('pln_planilla_ingresohoras AS ih')
     	->rightJoin('pln_planilla_ingresohoras_dias AS d', 'd.ingreso_horas_id', '=', 'ih.id')
     	->where('ih.id_planilla_colaborador', $planilla_colaborador_id)
     	->where('d.fecha',">=", $planilla[0]['rango_fecha1'])
     	->where('d.fecha',"<=", $planilla[0]['rango_fecha2'])
     	->distinct()
     	->get(array('ih.id','d.fecha','d.comentario','d.horas'));



      	if(!empty($result_horas)){
     		foreach ($result_horas AS $i => $row){
      			$fecha_ident =  date("d", strtotime($row->fecha));
      			$horas_ingresadas[$row->id][$fecha_ident] = number_format($row->horas,2);
      			$horas_comentarios[$row->id][$fecha_ident] = !empty($row->comentario)?1:0;
     		}
     	}
     	$clause =  array(
     			'empresa_id' => $this->empresa_id,
     			'id_planilla_colaborador' => $planilla_colaborador_id
     	);




       	list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
     	$count = Ingreso_horas_orm::listar($clause, NULL, NULL, NULL, NULL)->count();
     	list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
     	$rows = Ingreso_horas_orm::listar($clause, $sidx, $sord, $limit, $start);

     	 $response = new stdClass();
     	$response->page     = $page;
     	$response->total    = $total_pages;
     	$response->records  = count($horas_ingresadas);
     	//$response->estadoColaborador  = $planilla_colaborador[0]['estado_ingreso_horas'];
     	$i=0;
     	//$variable = 1;
       	if(!empty($rows->toArray())){
     		foreach ($rows->toArray() AS $i => $row){

     			$cuenta_gasto_nombre = $cuenta_costo_nombre = '';
     			if(!empty($row['cuenta_gasto'])){
     				$cuenta_gasto_nombre = $row['cuenta_gasto']['codigo'].' - '.$row['cuenta_gasto']['nombre'];
     			}

          if(!empty($row['cuenta_costo'])){
     				$cuenta_costo_nombre = $row['cuenta_costo']['codigo'].' - '.$row['cuenta_costo']['nombre'];
     			}

       			$response->rows[$i]["id"] = $row['id'];
     			$response->rows[$i]["cell"] = array(
     					'',
     					$row['centro_contable']['nombre'],
     					$row['recargo']['nombre'],
     				  $cuenta_costo_nombre,
     					$row['beneficio']['nombre'],
     					$cuenta_gasto_nombre
        			);
      			foreach ($period as $day) {

     				$dia_formato_corto = date("d", strtotime($day->format('Y-m-d')));
     				$dia_formato_largo = date("Y-m-d", strtotime($day->format('Y-m-d')));

          			if(isset($horas_ingresadas[$row['id']][$dia_formato_corto])){
      				 	$hora_ingresada = $horas_ingresadas[$row['id']][$dia_formato_corto];
      				 	$horas_comentario = $horas_comentarios[$row['id']][$dia_formato_corto];
       				 	array_push($response->rows[$i]["cell"], $hora_ingresada.'->'.$horas_comentario.'->'.$dia_formato_largo);
      				} else{
      					array_push($response->rows[$i]["cell"], '');
      				}
      			}
      			$i++;
     		}
     	}

     	echo json_encode($response);
     	exit;
     }

     public function ajax_seleccionar_informacion_columnas() {

     	$uuid_empresa = $this->session->userdata('uuid_empresa');
     	$empresa = Empresa_orm::findByUuid($uuid_empresa);

     	$this->empresa_id = $empresa->id;

     	$dias_rango = array();
     	$clause =  array(
     			'empresa_id' => $this->empresa_id
     	);

     	$colaborador_id = $this->input->post('colaborador_id', true);
     	$planilla_id 	= $this->input->post('planilla_id', true);

     	$planilla =  Planilla_orm::where('id','=', $planilla_id )
     	->get()->toArray();

     	$start_date 	=   $planilla[0]['rango_fecha1'];
     	$end_date 		=   $planilla[0]['rango_fecha2'];
      	$nuevafecha = new DateTime($end_date);
     	$nuevafecha->modify('+1 day');
     	$end_date =  $nuevafecha->format('Y-m-d');


     	$start    = new DateTime($start_date);
     	$end      = new DateTime($end_date);
     	$interval = new DateInterval('P1D'); // 1 day interval
     	$period   = new DatePeriod($start, $interval, $end);


     	$response['colModel'] = array();
 	 	$planilla_colaborador =  Planilla_colaborador_orm::where('planilla_id','=', $planilla_id )->where("colaborador_id",'=',$colaborador_id)
		 ->get()->toArray();
  	 	$response['fechas'] = !empty($dias_rango)?$dias_rango:'';
 	 	$response['estado'] = isset($planilla_colaborador[0]['estado_ingreso_horas'])?$planilla_colaborador[0]['estado_ingreso_horas']:'0';
  	 	$response['colNombres'] =  array("TKN","Centro Contable","Recargo","Cuenta de costo * ","Beneficio","Cuenta de costo beneficio");

     	foreach ($period as $day) {
     		$dia_formato_largo = date("Y-m-d", strtotime($day->format('Y-m-d')));
      		array_push($response['colNombres'],date("D-d", strtotime($day->format('Y-m-d'))) );
      		//array_push($response['colModel'],"ingreso~".$dia_formato_largo.'~');
      		array_push($response['colModel'],"ingreso[][$dia_formato_largo]");
      	}



     	echo json_encode($response);
     	exit;
     }

      public function ajax_guardar_entrar_horas() {
     	// Just Allow ajax request
     	if (! $this->input->is_ajax_request ()) {
     		return false;
     	}


     	$uuid_empresa = $this->session->userdata('uuid_empresa');
     	$empresa = Empresa_orm::findByUuid($uuid_empresa);


      	$tipo_formulario		 = $this->input->post('oper', true); //Nuevo o Edicion
     	$ingresohoras_id		 = $this->input->post('id', true); //Nuevo o Edicion

     	Capsule::beginTransaction();


     	try {

     		$ingreso_horas = $_POST['ingreso'];
      		 if($tipo_formulario == 'edit'){ //En Caso de Edicion

     			$mensaje   = "�&Eacute;xito! Se ha actualizado correctamente las <<Horas/Planilla>>";

     			if(!empty($_POST['ingreso'])){


     				$field = array(
     						"recargo_id" 			=> $_POST['Recargo'],
     						"cuenta_costo_id" 		=> $_POST['Cuenta_Costo'],
     						"beneficio_id" 			=> $_POST['Beneficio'],
     						"cuenta_gasto_id" 			=> $_POST['CuentaGasto'],
                "centro_contable_id" 			=> $_POST['Centro_contable']
      				);

     				Ingreso_horas_orm::where('id', $ingresohoras_id)->update($field);

      				foreach ($ingreso_horas as $clave => $valor){
     					foreach ($valor as $fecha=>$horas){

     						$result = Capsule::table('pln_planilla_ingresohoras_dias AS hd')
     						->where('hd.ingreso_horas_id', $ingresohoras_id)
     						->where('hd.fecha', $fecha)
     						->distinct()
     						->get(array('hd.id'));

     						$valor_hora = $ingreso_horas[$clave][$fecha];

     						if(is_numeric($valor_hora)){
     							if(!empty($result)){ //Ya exitse este Datos
     								$ingreso_horas_dias_id = $result[0]->id;
     								Ingreso_horas_dias_orm::where('id', $ingreso_horas_dias_id)->update([
     								'horas' => $ingreso_horas[$clave][$fecha]]);
     							}else{ //Esta Data es nuevo
     								$fieldset["ingreso_horas_id"] = $ingresohoras_id;
     								$fieldset["fecha"] = $fecha;
     								$fieldset["horas"] = $valor_hora;
     								$fieldset["creado_por"] =1;

     								Ingreso_horas_dias_orm::create($fieldset);
     							}
     						}else{
     							if(!empty($result)){ //X si acaso se debe borrar la fila
     								$ingreso_horas_dias_id = $result[0]->id;
     								Ingreso_horas_dias_orm::where('id', $ingreso_horas_dias_id)->delete();
     							}
     						}
       					}
      				}
     			}



      		}
     		else if($tipo_formulario == 'add'){
     			$colaborador_id		 = $this->input->post('colaborador_id', true); //Nuevo
     			$planilla_id		 = $this->input->post('planilla_id', true); //Nuevo

      			$mensaje   = "�&Eacute;xito! Se ha creado correctamente las <<Horas/Planilla>>";

      			if(!empty($_POST['ingreso'])){



      				$planila_col_info = Planilla_colaborador_orm::where("colaborador_id","=",$colaborador_id)->where("planilla_id","=",$planilla_id)->get(array('id'));
      				$id_planilla_colaborador = $planila_col_info[0]->id;


      				$field = array(
      						"centro_contable_id" 	=> 1,
      						"recargo_id" 			=> $_POST['Recargo'],
      						"cuenta_costo_id" 		=> $_POST['Cuenta_Costo'],
      						"beneficio_id" 			=> $_POST['Beneficio'],
      						"cuenta_gasto_id" 		=> $_POST['CuentaGasto'],
                  "centro_contable_id" 			=> $_POST['Centro_contable'],
      						"fecha_creacion" 		=> date("Y-m-d"),
      						"id_planilla_colaborador" =>$id_planilla_colaborador,
      						"empresa_id" 			=> $empresa->id
      				);

      				$ingreso_horas_new = Ingreso_horas_orm::create($field);

       				foreach ($ingreso_horas as $clave => $valor){
      					foreach ($valor as $fecha=>$horas){

 							if(!empty(trim($horas))){

 								$fieldset["fecha"] = $fecha;
 								$fieldset["horas"] = $horas;
   								$horas_dias[] 	= new Ingreso_horas_dias_orm($fieldset);
							}
       					}
      				}
      				$ingreso_horas_new->dias()->saveMany($horas_dias);
      			}
     		}


     	} catch(ValidationException $e){

     		// Rollback
     		Capsule::rollback();

     		echo json_encode(array(
     				"response" => false,
     				"mensaje" => "Hubo un error tratando de crear el ingreso de Horas."
     		));
     		exit;
     	}

     	Capsule::commit();

     	echo json_encode(array(
     			"response" => true
     	));

     	//echo true;

     	exit;
     }



     public function ajax_eliminar_ingreso_horas() {
	     // Just Allow ajax request
	     if (! $this->input->is_ajax_request ()) {
	     	return false;
	     }

	     Capsule::beginTransaction();

	     try {
	     	$id = $this->input->post ('id', true);
	     	Ingreso_horas_orm::where('id', $id)->delete();
	     	Ingreso_horas_dias_orm::where('ingreso_horas_id', $id)->delete();

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
	     		"mensaje" => "Se ha eliminado el registro satisfactoriamente."
	     ));
	     exit;
	}

	private function ajax_validar_planilla($planilla_id = NULL) {
		$contador =  Planilla_colaborador_orm::where('planilla_id', "=",$planilla_id)->where('estado_ingreso_horas',"!=", '3')->count();

		if($contador == 0){ //todos son 3 osea todos validados,
			Planilla_orm::where('id', $planilla_id)->update(['estado_id' =>29]);
		}else{ //Abrir la planilla
			Planilla_orm::where('id', $planilla_id)->update(['estado_id' =>13]);

		}
		return true;

	}

	public function ajax_anular_planilla() {
		// Just Allow ajax request
		if (! $this->input->is_ajax_request ()) {
			return false;
		}
		$id_planilla			 = $this->input->post('id_planilla', true);


		Capsule::beginTransaction();

		try {
			Planilla_orm::where('id', $id_planilla)->update(['estado_id' => 15]);
		} catch(ValidationException $e){

			// Rollback
			Capsule::rollback();

			echo json_encode(array(
					"response" => false,
					"mensaje" => "Hubo un error tratando de cambiar el estado."
			));
			exit;
		}
		Capsule::commit();

		echo json_encode(array(
				"response" => true,
				"mensaje" => "Se ha actualizado con &eacute;xito los cambios."
		));
		exit;

	}
	public function ajax_validar_multiples() {
    // Just Allow ajax request
    if (! $this->input->is_ajax_request ()) {
      return false;
    }

    Capsule::beginTransaction();

    try {
      $planilla_id = $this->input->post ('planilla_id', true);
      $planillaInfo =  $this->planillaRepository->find($planilla_id);
      $planillaInfo->load([
          'colaboradores_planilla'=> function ($query)  {
              $query->where('estado_ingreso_horas', "=",0);
          },
          'colaboradores_planilla.colaborador'=> function ($query)  {
               $query->where('tipo_salario', 'hora');
         }
       ]);

      $lista_colaboradores  =  $this->planillaRepository->getColaboradoresValidados($planillaInfo);
     $resultado  =  $this->planillaRepository->validacion_multiple($lista_colaboradores, $planilla_id);
      $this->ajax_validar_planilla($planilla_id);

    } catch(ValidationException $e){
      Capsule::rollback();

      $mensaje = array('estado' => 500, 'mensaje' => '<strong>¡Error!</strong> Su solicitud no fue procesada');
      $this->session->set_flashdata('mensaje', $mensaje);
      echo json_encode(array(
          "response" => false,
      ));
      exit;

    }
    Capsule::commit();
    $mensaje = array('estado' => 200, 'mensaje' => 'Se ha validado los colaboradores satisfactoriamente.');
    $this->session->set_flashdata('mensaje', $mensaje);
    echo json_encode(array(
        "response" => true,
    ));

    exit;
  }

	public function ajax_validar_colaborador_planilla() {
		// Just Allow ajax request
		if (! $this->input->is_ajax_request ()) {
			return false;
		}

		Capsule::beginTransaction();

		try {
			$planilla_id = $this->input->post ('planilla_id', true);
			$colaborador_id = $this->input->post ('colaborador_id', true);
 			Planilla_colaborador_orm::where('planilla_id', $planilla_id)->where('colaborador_id', $colaborador_id)->update(['estado_ingreso_horas' =>'3']);

 			$this->ajax_validar_planilla($planilla_id);

 		} catch(ValidationException $e){
			Capsule::rollback();
			echo json_encode(array(
					"response" => false,
					"mensaje" => "Hubo un error tratando de actualizar el estado."
			));
			exit;
		}
		Capsule::commit();

		echo json_encode(array(
				"response" => true,
				"mensaje" => "Se ha validado el colaborador satisfactoriamente."
		));
		exit;
	}
	public function ajax_eliminar_colaborador_planilla() {
		// Just Allow ajax request
		if (! $this->input->is_ajax_request ()) {
			return false;
		}

		Capsule::beginTransaction();

		try {
			$colaborador_id = $this->input->post ('colaborador_id', true);
			$planilla_id = $this->input->post ('planilla_id', true);

			$planilla_colaborador_id =  Planilla_colaborador_orm::where('colaborador_id','=', $colaborador_id )->where("planilla_id",'=',$planilla_id)
			->get()->toArray();
			if(isset($planilla_colaborador_id[0]['id']))
			{

				Ingreso_horas_orm::where('id_planilla_colaborador', $planilla_colaborador_id[0]['id'])
				->delete();

				Planilla_colaborador_orm::where('id', $planilla_colaborador_id[0]['id'])
				->delete();

				$this->ajax_validar_planilla($planilla_id);
			}

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
				"response" => true,
				"mensaje" => "Se ha eliminado el colaborador satisfactoriamente."
		));
		exit;
	}


	public function ajax_exportar_talonarios_multiples() {

		$planilla_id = $_GET['planilla_id'];
		$tipo_planilla = $_GET['tipo_planilla'];
		if($tipo_planilla == 'regular'){

			$this->ajax_exportar_talonario_regular($planilla_id);

		}
		else if($tipo_planilla == 'xiii_mes'){

			$this->ajax_exportar_talonario_decimo($planilla_id);

		}
		else if($tipo_planilla == 'vacaciones'){

			$this->ajax_exportar_talonario_vacaciones($planilla_id);

		}
		else if($tipo_planilla == 'liquidaciones'){
			$this->ajax_exportar_talonario_liquidaciones($planilla_id);
 		}
 	}
//Codigo actualizado Octubre 2016
  public function exportar_ver_reporte_cerrada(){

      $planilla_id = $_GET['planilla_id'];
      $colaborador_id = isset($_GET['colaborador_id'])?$_GET['colaborador_id']:NULL;
      $planilla_info = $this->planillaRepository->find($planilla_id);
      $planilla_info->load(['colaboradores_pagadas'
              => function ($query) use ($colaborador_id)  {
              if($colaborador_id != NULL)
                  $query->where('pln_pagadas_colaborador.colaborador_id', '=', $colaborador_id);
              },
              'colaboradores_pagadas.ingresos',
              'colaboradores_pagadas.deducciones',
              'colaboradores_pagadas.descuentos',
              'colaboradores_pagadas.colaborador.centro_contable',
              'colaboradores_pagadas.colaborador.cargo'
            ]
        );
      $csvdata = $this->CalculosCerradoRepository->collecion_excel_cerrada_regular($planilla_info);
      $csv = Writer::createFromFileObject(new SplTempFileObject());
      $csv->insertOne(['','','','Rango de Fechas:',$planilla_info->rango_fecha1.' - '.$planilla_info->rango_fecha2,'','Centro contable:','','','','','']);
      $csv->insertOne(['Planilla No.  ',$planilla_info->codigo,'','Fecha de pago:','','','Area de negocios:','','','','','']);
      $csv->insertOne(['Centro Contable','Posición','Nombre','Cédula','Rata x Hora / Salario fijo','H.R.','H.E.','$H.R.','$H.E.','ISR','S.E.','S.S.','C.S.','Deducciones','Desc. Directo','Sal. Bruto','Sal. Neto']);
      $csv->insertAll($csvdata);
      $csv->output("planilla-". date('ymd') .".csv");
      exit;
    }

	//Multiples
	public function ajax_exportar_talonario_regular($planilla_id = NULL) {

		$html = '';

  		$colaboradores 	=  Planilla_colaborador_orm::where('planilla_id','=', $planilla_id )->get();

 		$contador 	=  Planilla_colaborador_orm::where('planilla_id','=', $planilla_id )->count();

		if(!empty($colaboradores->toArray())){
				foreach($colaboradores->toArray() AS $i => $row){

					$colaborador_id = $row['colaborador_id'];
					$data = array();
					$pagos_info =Pagadas_colaborador_orm::with('acumulados','deducciones','descuentos','ingresos','calculos','planilla.tipo')
					->where("planilla_id", "=", $planilla_id)
					->where("colaborador_id", "=", $colaborador_id)
					->get()->toArray();

   					$planilla_numero = $pagos_info[0]['planilla']['identificador'].$pagos_info[0]['planilla']['semana'].$pagos_info[0]['planilla']['ano'].$pagos_info[0]['planilla']['secuencial'];

					$colaborador_info = Colaboradores_orm::with('ciclo','cargo','centro_contable','departamento','forma_pago')->where(Capsule::raw("id"), "=", $colaborador_id)->get()->toArray();
					$fecha_inicial = Util::verificar_valor(isset($pagos_info[0]['fecha_inicial'])?$pagos_info[0]['fecha_inicial']:'0');
					$fecha_final = Util::verificar_valor(isset($pagos_info[0]['fecha_final'])?$pagos_info[0]['fecha_final']:'0');

					$fecha_pago = Util::verificar_valor(isset($pagos_info[0]['fecha_pago'])?$pagos_info[0]['fecha_pago']:'0000-00-00');

					$inicio =  date("d/m/Y", strtotime($fecha_inicial));
					$final =  date("d/m/Y", strtotime($fecha_final));
					$fecha_pago =  date("d/m/Y", strtotime($fecha_pago));

					$dompdf = new Dompdf();
	 				$nombre  = isset($colaborador_info[0]['nombre_completo'])?$colaborador_info[0]['nombre_completo']:'_';
					$planilla_codigo =  isset($pagos_info[0]['planilla_codigo'])?$pagos_info[0]['planilla_codigo']:'0';
 					$data = array(
							"tipo_planilla"	=>	 $pagos_info[0]['planilla']['tipo']['etiqueta'],
							"planilla_numero"	=>$planilla_numero,
							"ciclo_de_pago"		=>isset($colaborador_info[0]['ciclo']['etiqueta'])?$colaborador_info[0]['ciclo']['etiqueta']:'-',
							"no_colaborador"	=>isset($colaborador_info[0]['codigo'])?$colaborador_info[0]['codigo']:'0',
							"centro_contable"	=>isset($colaborador_info[0]['centro_contable']['nombre'])?$colaborador_info[0]['centro_contable']['nombre']:'0',
							"cedula"			=>isset($colaborador_info[0]['cedula'])?$colaborador_info[0]['cedula']:'0',
							"puesto"			=>isset($colaborador_info[0]['cargo']['nombre'])?$colaborador_info[0]['cargo']['nombre']:'0',
							"area_negocio"			=>isset($colaborador_info[0]['departamento']['nombre'])?$colaborador_info[0]['departamento']['nombre']:'0',
							"tipo_salario"			=>isset($colaborador_info[0]['tipo_salario'])?$colaborador_info[0]['tipo_salario']:'0',
							"fecha_pago"			=>$fecha_pago,
							"tipo_pago"			=>isset($colaborador_info[0]['forma_pago']['etiqueta'])?$colaborador_info[0]['forma_pago']['etiqueta']:'0',
							"periodo"			=>$inicio .'-'. $final,
							"nombre_completo"	=>isset($colaborador_info[0]['nombre_completo'])?$colaborador_info[0]['nombre_completo']:'',
							"no_ss"				=>isset($colaborador_info[0]['seguro_social'])?$colaborador_info[0]['seguro_social']:'0',
							"codigo_ss"			=>isset($colaborador_info[0]['seguro_social'])?$colaborador_info[0]['seguro_social']:'0',
							//"rata"				=>($colaborador_info[0]['tipo_salario']=='Hora')?$pagos_info[0]['rata']:'0.00',
              "rata" =>($colaborador_info[0]['tipo_salario']=='Hora')?$colaborador_info[0]['rata_hora']:'0.00',
							"salario_bruto"		=>isset($pagos_info[0]['salario_bruto'])?$pagos_info[0]['salario_bruto']:'0',
							"deducciones"		=>isset($pagos_info[0]['deducciones_total'])?$pagos_info[0]['deducciones_total']:'0',
							"salario_neto"		=>isset($pagos_info[0]['salario_neto'])?$pagos_info[0]['salario_neto']:'0',
							"lista_ingresos"		=>isset($pagos_info[0]['ingresos'])?$pagos_info[0]['ingresos']:array(),
							"lista_deducciones"		=>isset($pagos_info[0]['deducciones'])?$pagos_info[0]['deducciones']:array(),
							"lista_acumulados"		=>isset($pagos_info[0]['acumulados'])?$pagos_info[0]['acumulados']:array(),
							"lista_descuentos"		=>isset($pagos_info[0]['descuentos'])?$pagos_info[0]['descuentos']:array()
					);

	 				$html .= $this->load->view('talonario', $data, true);
	 				if( $i < $contador-1){
 	 					$html .= '<div style="page-break-before: always;" ></div>';
	 				}

	 				++$i;
 				}
		}

 		$dompdf->loadHtml($html);
 		$dompdf->setPaper('A4', 'landscape');

 		$dompdf->render();

 		$dompdf->stream($planilla_numero);

	}
 	public function ajax_exportar_talonario_liquidaciones($planilla_id = NULL) {
		$html = '';
		$i= 0;
		$planilla = $this->planillaRepository->find($planilla_id);
		$planilla->load("liquidaciones","liquidaciones.colaborador")->toArray();
 	}

	public function ajax_exportar_talonario_vacaciones($planilla_id = NULL) {
		$html = '';
		$i= 0;


		//Datos Generales de la planilla

		$fecha_pago = Util::verificar_valor(isset($planilla_info->fecha_pago)?$planilla_info->fecha_pago:'0000-00-00');
		$fecha_inicial = Util::verificar_valor(isset($planilla_info->rango_fecha1)?$planilla_info->rango_fecha1:'0');
		$fecha_final = Util::verificar_valor(isset($planilla_info->rango_fecha2)?$planilla_info->rango_fecha2:'0');
		$inicio =  date("d/m/Y", strtotime($fecha_inicial));
		$final =  date("d/m/Y", strtotime($fecha_final));

		$pagadas =Pagadas_colaborador_orm::with('acumulados','deducciones','descuentos','ingresos','calculos','planilla.tipo')
		->where("planilla_id", "=", $planilla_id)
		->get()->toArray();

		$codigo_planilla = $pagadas[0]['planilla']['identificador'].$pagadas[0]['planilla']['semana'].$pagadas[0]['planilla']['ano'].$pagadas[0]['planilla']['secuencial'];


		if(!empty($pagadas)){
			foreach($pagadas as $pagos_info){
				$dompdf = new Dompdf();
				$colaborador_id = $pagos_info['colaborador_id'];
				$lista_salarios_decimo = array();
				$lista_salarios_decimo = Pagadas_colaborador_orm::salarios_ganados_decimo($colaborador_id, array(
						"rango_fecha1"=>$fecha_inicial,
						"rango_fecha2"=>$fecha_final
				));


				$colaborador_info = Colaboradores_orm::with('ciclo','cargo','centro_contable','departamento','forma_pago')->where(Capsule::raw("id"), "=", $colaborador_id)->get()->toArray();

				$data = array(
						"tipo_planilla"	=>	 $pagos_info['planilla']['tipo']['etiqueta'],
						"planilla_numero"	=> $codigo_planilla,
						"ciclo_de_pago"		=>isset($colaborador_info[0]['ciclo']['etiqueta'])?$colaborador_info[0]['ciclo']['etiqueta']:'-',
						"no_colaborador"	=>isset($colaborador_info[0]['codigo'])?$colaborador_info[0]['codigo']:'0',
						"centro_contable"	=>isset($colaborador_info[0]['centro_contable']['nombre'])?$colaborador_info[0]['centro_contable']['nombre']:'0',
						"cedula"			=>isset($colaborador_info[0]['cedula'])?$colaborador_info[0]['cedula']:'0',
						"puesto"			=>isset($colaborador_info[0]['cargo']['nombre'])?$colaborador_info[0]['cargo']['nombre']:'0',
						"area_negocio"			=>isset($colaborador_info[0]['departamento']['nombre'])?$colaborador_info[0]['departamento']['nombre']:'0',
						"tipo_salario"			=>isset($colaborador_info[0]['tipo_salario'])?$colaborador_info[0]['tipo_salario']:'0',
						"fecha_pago"			=>$fecha_pago,
						"tipo_pago"			=>isset($colaborador_info[0]['forma_pago']['etiqueta'])?$colaborador_info[0]['forma_pago']['etiqueta']:'0',
						"periodo"			=>$inicio .'-'. $final,
						"nombre_completo"	=>isset($colaborador_info[0]['nombre_completo'])?$colaborador_info[0]['nombre_completo']:'',
						"no_ss"				=>isset($colaborador_info[0]['seguro_social'])?$colaborador_info[0]['seguro_social']:'0',
						"codigo_ss"			=>isset($colaborador_info[0]['seguro_social'])?$colaborador_info[0]['seguro_social']:'0',
						"rata"				=>($colaborador_info[0]['tipo_salario']=='Hora')?$pagos_info['rata']:'0.00',
						"salario_bruto"		=>isset($pagos_info['salario_bruto'])?$pagos_info['salario_bruto']:'0',
						"deducciones"		=>isset($pagos_info['deducciones_total'])?$pagos_info['deducciones_total']:'0',
						"salario_neto"		=>isset($pagos_info['salario_neto'])?$pagos_info['salario_neto']:'0',
 						"lista_deducciones"		=>isset($pagos_info['deducciones'])?$pagos_info['deducciones']:array(),
						"lista_acumulados"		=>isset($pagos_info['acumulados'])?$pagos_info['acumulados']:array(),
						"lista_descuentos"		=>isset($pagos_info['descuentos'])?$pagos_info['descuentos']:array()
				);


				$html .= $this->load->view('talonario_vacacion', $data, true);
				if( $i < count($pagadas)-1){
					$html .= '<div style="page-break-before: always;" ></div>';
				}

				++$i;
			}
		}
 		$dompdf->loadHtml($html);
		$dompdf->setPaper('A4', 'landscape');
 		$dompdf->render();
 		$dompdf->stream("Talonarios-".$codigo_planilla);
	}

	public function ajax_exportar_talonario_decimo($planilla_id = NULL) {
		$data = array();
		$html = '';
		$i= 0;

  		$fecha_pago = Util::verificar_valor(isset($planilla_info->fecha_pago)?$planilla_info->fecha_pago:'0000-00-00');
		$fecha_inicial = Util::verificar_valor(isset($planilla_info->rango_fecha1)?$planilla_info->rango_fecha1:'0');
		$fecha_final = Util::verificar_valor(isset($planilla_info->rango_fecha2)?$planilla_info->rango_fecha2:'0');
 		$inicio =  date("d/m/Y", strtotime($fecha_inicial));
		$final =  date("d/m/Y", strtotime($fecha_final));

   		$pagadas =Pagadas_colaborador_orm::with('acumulados','deducciones','descuentos','ingresos','calculos','planilla.tipo')
 		->where("planilla_id", "=", $planilla_id)
 		->get()->toArray();

   		$codigo_planilla = $pagadas[0]['planilla']['identificador'].$pagadas[0]['planilla']['semana'].$pagadas[0]['planilla']['ano'].$pagadas[0]['planilla']['secuencial'];

   		if(!empty($pagadas)){
 			foreach($pagadas as $pagos_info){

   				$dompdf = new Dompdf();
  				$colaborador_id = $pagos_info['colaborador_id'];
  				$lista_salarios_decimo = array();
  				$lista_salarios_decimo = Pagadas_colaborador_orm::salarios_ganados_decimo($colaborador_id, array(
  						"rango_fecha1"=>$fecha_inicial,
  						"rango_fecha2"=>$fecha_final
   				));

   				$colaborador_info = Colaboradores_orm::with('ciclo','cargo','centro_contable','departamento','forma_pago')->where(Capsule::raw("id"), "=", $colaborador_id)->get()->toArray();

    				  $data = array(
    				  	"tipo_planilla"	=>	 $pagos_info['planilla']['tipo']['etiqueta'],
 						"planilla_numero"	=> $codigo_planilla,
 						"ciclo_de_pago"		=>isset($colaborador_info[0]['ciclo']['etiqueta'])?$colaborador_info[0]['ciclo']['etiqueta']:'-',
 						"no_colaborador"	=>isset($colaborador_info[0]['codigo'])?$colaborador_info[0]['codigo']:'0',
 						"centro_contable"	=>isset($colaborador_info[0]['centro_contable']['nombre'])?$colaborador_info[0]['centro_contable']['nombre']:'0',
 						"cedula"			=>isset($colaborador_info[0]['cedula'])?$colaborador_info[0]['cedula']:'0',
 						"puesto"			=>isset($colaborador_info[0]['cargo']['nombre'])?$colaborador_info[0]['cargo']['nombre']:'0',
 						"area_negocio"			=>isset($colaborador_info[0]['departamento']['nombre'])?$colaborador_info[0]['departamento']['nombre']:'0',
 						"tipo_salario"			=>isset($colaborador_info[0]['tipo_salario'])?$colaborador_info[0]['tipo_salario']:'0',
 						"fecha_pago"			=>$fecha_pago,
 						"tipo_pago"			=>isset($colaborador_info[0]['forma_pago']['etiqueta'])?$colaborador_info[0]['forma_pago']['etiqueta']:'0',
 						"periodo"			=>$inicio .'-'. $final,
 						"nombre_completo"	=>isset($colaborador_info[0]['nombre_completo'])?$colaborador_info[0]['nombre_completo']:'',
 						"no_ss"				=>isset($colaborador_info[0]['seguro_social'])?$colaborador_info[0]['seguro_social']:'0',
 						"codigo_ss"			=>isset($colaborador_info[0]['seguro_social'])?$colaborador_info[0]['seguro_social']:'0',
 						"rata"				=>($colaborador_info[0]['tipo_salario']=='Hora')?$pagos_info['rata']:'0.00',
 						"salario_bruto"		=>isset($pagos_info['salario_bruto'])?$pagos_info['salario_bruto']:'0',
 						"deducciones"		=>isset($pagos_info['deducciones_total'])?$pagos_info['deducciones_total']:'0',
 						"salario_neto"		=>isset($pagos_info['salario_neto'])?$pagos_info['salario_neto']:'0',
 						"lista_ingresos"	=>$lista_salarios_decimo,
 						"lista_deducciones"		=>isset($pagos_info['deducciones'])?$pagos_info['deducciones']:array(),
 						"lista_acumulados"		=>isset($pagos_info['acumulados'])?$pagos_info['acumulados']:array(),
 						"lista_descuentos"		=>isset($pagos_info['descuentos'])?$pagos_info['descuentos']:array()
 				);



    				  $html .= $this->load->view('talonario_decimo', $data, true);
    				  if( $i < count($pagadas)-1){
    				  	$html .= '<div style="page-break-before: always;" ></div>';
    				  }

    				  ++$i;


 			}
 		}

 		$dompdf->loadHtml($html);
 		$dompdf->setPaper('A4', 'landscape');

 		$dompdf->render();

 		$dompdf->stream("Talonario-decimo");


	}

  //CODIGO CAMBIAR **** //
	public function ajax_imprimir_talonario() {


 		$planilla_id = $_GET['planilla_id'];
 		$colaborador_id = $_GET['colaborador_id'];

		$pagos_info =Pagadas_colaborador_orm::with('acumulados','deducciones','descuentos','ingresos','calculos','planilla.tipo')
			->where("planilla_id", "=", $planilla_id)
			->where("colaborador_id", "=", $colaborador_id)
			->get()->toArray();

		$codigo_planilla = $pagos_info[0]['planilla']['identificador'].$pagos_info[0]['planilla']['semana'].$pagos_info[0]['planilla']['ano'].$pagos_info[0]['planilla']['secuencial'];

	 	$colaborador_info = Colaboradores_orm::with('estado','cargo','centro_contable','departamento','forma_pago')->where(Capsule::raw("id"), "=", $colaborador_id)->get()->toArray();
  		$fecha_inicial = Util::verificar_valor(isset($pagos_info[0]['fecha_inicial'])?$pagos_info[0]['fecha_inicial']:'0');
		$fecha_final = Util::verificar_valor(isset($pagos_info[0]['fecha_final'])?$pagos_info[0]['fecha_final']:'0');

		$fecha_pago = Util::verificar_valor(isset($pagos_info[0]['fecha_pago'])?$pagos_info[0]['fecha_pago']:'0000-00-00');

		$inicio =  date("d/m/Y", strtotime($fecha_inicial));
		$final =  date("d/m/Y", strtotime($fecha_final));
		$fecha_pago =  date("d/m/Y", strtotime($fecha_pago));

  		$dompdf = new Dompdf();

  		$nombre  = isset($colaborador_info[0]['nombre_completo'])?$colaborador_info[0]['nombre_completo']:'_';
  		$planilla_codigo =  isset($pagos_info[0]['planilla_codigo'])?$pagos_info[0]['planilla_codigo']:'0';

 		$data = array(
				"planilla_numero"	=>$codigo_planilla,
        "tipo_planilla"	=>	 $pagos_info[0]['planilla']['tipo']['etiqueta'],
				"ciclo_de_pago"		=>isset($pagos_info[0]['ciclo_de_pago'])?$pagos_info[0]['ciclo_de_pago']:'0',
				"no_colaborador"	=>isset($colaborador_info[0]['codigo'])?$colaborador_info[0]['codigo']:'0',
				"centro_contable"	=>isset($colaborador_info[0]['centro_contable']['nombre'])?$colaborador_info[0]['centro_contable']['nombre']:'0',
				"cedula"			=>isset($colaborador_info[0]['cedula'])?$colaborador_info[0]['cedula']:'0',
				"puesto"			=>isset($colaborador_info[0]['cargo']['nombre'])?$colaborador_info[0]['cargo']['nombre']:'0',
				"area_negocio"			=>isset($colaborador_info[0]['departamento']['nombre'])?$colaborador_info[0]['departamento']['nombre']:'0',
				"tipo_salario"			=>isset($colaborador_info[0]['tipo_salario'])?$colaborador_info[0]['tipo_salario']:'0',
				"fecha_pago"			=>$fecha_pago,
				"tipo_pago"			=>isset($colaborador_info[0]['forma_pago']['etiqueta'])?$colaborador_info[0]['forma_pago']['etiqueta']:'0',
				"periodo"			=>$inicio .'-'. $final,
				"nombre_completo"	=>isset($colaborador_info[0]['nombre_completo'])?$colaborador_info[0]['nombre_completo']:'',
				"no_ss"				=>isset($colaborador_info[0]['seguro_social'])?$colaborador_info[0]['seguro_social']:'0',
				"codigo_ss"			=>isset($colaborador_info[0]['seguro_social'])?$colaborador_info[0]['seguro_social']:'0',
				"rata"				=>isset($pagos_info[0]['rata'])?$pagos_info[0]['rata']:'0',
				"salario_bruto"		=>isset($pagos_info[0]['salario_bruto'])?$pagos_info[0]['salario_bruto']:'0',
 				"deducciones"		=>isset($pagos_info[0]['deducciones_total'])?$pagos_info[0]['deducciones_total']:'0',
				"salario_neto"		=>isset($pagos_info[0]['salario_neto'])?$pagos_info[0]['salario_neto']:'0',
				"lista_ingresos"		=>isset($pagos_info[0]['ingresos'])?$pagos_info[0]['ingresos']:array(),
				"lista_deducciones"		=>isset($pagos_info[0]['deducciones'])?$pagos_info[0]['deducciones']:array(),
				"lista_acumulados"		=>isset($pagos_info[0]['acumulados'])?$pagos_info[0]['acumulados']:array(),
				"lista_descuentos"		=>isset($pagos_info[0]['descuentos'])?$pagos_info[0]['descuentos']:array()
 		);

 		$html = $this->load->view('talonario', $data, true);
		$dompdf->loadHtml($html);

 		$dompdf->setPaper('A4', 'landscape');

 		$dompdf->render();

 		$dompdf->stream($nombre.$planilla_codigo);
  	}

    //CODIGO CAMBIAR **** //
  	public function ajax_imprimir_talonario_decimo() {

   		$planilla_id = $_GET['planilla_id'];
  		$colaborador_id = $_GET['colaborador_id'];

  		$pagos_info =Pagadas_colaborador_orm::with('acumulados','deducciones','descuentos','ingresos','calculos')
  		->where("planilla_id", "=", $planilla_id)
  		->where("colaborador_id", "=", $colaborador_id)
  		->get()->toArray();

  		$codigo_planilla = $pagos_info[0]['planilla']['identificador'].$pagos_info[0]['planilla']['semana'].$pagos_info[0]['planilla']['ano'].$pagos_info[0]['planilla']['secuencial'];
  		$colaborador_info = Colaboradores_orm::with('estado','cargo','centro_contable','departamento','forma_pago')->where(Capsule::raw("id"), "=", $colaborador_id)->get()->toArray();
  		$fecha_inicial = Util::verificar_valor(isset($pagos_info[0]['fecha_inicial'])?$pagos_info[0]['fecha_inicial']:'0');
  		$fecha_final = Util::verificar_valor(isset($pagos_info[0]['fecha_final'])?$pagos_info[0]['fecha_final']:'0');

  		$fecha_pago = Util::verificar_valor(isset($pagos_info[0]['fecha_pago'])?$pagos_info[0]['fecha_pago']:'0000-00-00');

  		$inicio =  date("d/m/Y", strtotime($fecha_inicial));
  		$final =  date("d/m/Y", strtotime($fecha_final));
  		$fecha_pago =  date("d/m/Y", strtotime($fecha_pago));

  		$dompdf = new Dompdf();

  		$nombre  = isset($colaborador_info[0]['nombre_completo'])?$colaborador_info[0]['nombre_completo']:'_';
  		$planilla_codigo =  isset($pagos_info[0]['planilla_codigo'])?$pagos_info[0]['planilla_codigo']:'0';

  		$data = array(
  				"planilla_numero"	=>$codigo_planilla,
  				"ciclo_de_pago"		=>isset($pagos_info[0]['ciclo_de_pago'])?$pagos_info[0]['ciclo_de_pago']:'0',
  				"no_colaborador"	=>isset($colaborador_info[0]['codigo'])?$colaborador_info[0]['codigo']:'0',
  				"centro_contable"	=>isset($colaborador_info[0]['centro_contable']['nombre'])?$colaborador_info[0]['centro_contable']['nombre']:'0',
  				"cedula"			=>isset($colaborador_info[0]['cedula'])?$colaborador_info[0]['cedula']:'0',
  				"puesto"			=>isset($colaborador_info[0]['cargo']['nombre'])?$colaborador_info[0]['cargo']['nombre']:'0',
  				"area_negocio"			=>isset($colaborador_info[0]['departamento']['nombre'])?$colaborador_info[0]['departamento']['nombre']:'0',
  				"tipo_salario"			=>isset($colaborador_info[0]['tipo_salario'])?$colaborador_info[0]['tipo_salario']:'0',
  				"fecha_pago"			=>$fecha_pago,
  				"tipo_pago"			=>isset($colaborador_info[0]['forma_pago']['etiqueta'])?$colaborador_info[0]['forma_pago']['etiqueta']:'0',
  				"periodo"			=>$inicio .'-'. $final,
  				"nombre_completo"	=>isset($colaborador_info[0]['nombre_completo'])?$colaborador_info[0]['nombre_completo']:'',
  				"no_ss"				=>isset($colaborador_info[0]['seguro_social'])?$colaborador_info[0]['seguro_social']:'0',
  				"codigo_ss"			=>isset($colaborador_info[0]['seguro_social'])?$colaborador_info[0]['seguro_social']:'0',
  				"rata"				=>isset($pagos_info[0]['rata'])?$pagos_info[0]['rata']:'0',
  				"salario_bruto"		=>isset($pagos_info[0]['salario_bruto'])?$pagos_info[0]['salario_bruto']:'0',
  				"deducciones"		=>isset($pagos_info[0]['deducciones_total'])?$pagos_info[0]['deducciones_total']:'0',
  				"salario_neto"		=>isset($pagos_info[0]['salario_neto'])?$pagos_info[0]['salario_neto']:'0',
  				"lista_ingresos"		=>isset($pagos_info[0]['ingresos'])?$pagos_info[0]['ingresos']:array(),
  				"lista_deducciones"		=>isset($pagos_info[0]['deducciones'])?$pagos_info[0]['deducciones']:array(),
  				"lista_acumulados"		=>isset($pagos_info[0]['acumulados'])?$pagos_info[0]['acumulados']:array(),
  				"lista_descuentos"		=>isset($pagos_info[0]['descuentos'])?$pagos_info[0]['descuentos']:array()
  		);


  		$html = $this->load->view('talonario-decimo', $data, true);
  		$dompdf->loadHtml($html);

  		$dompdf->setPaper('A4', 'landscape');

  		$dompdf->render();

  		$dompdf->stream($nombre.$planilla_codigo);
  	}

  	private function buscando_tablas_vacaciones($planilla_id = NULL, $vacacion_id = NULL) {

   		$vacacion_info = Vacaciones_orm::find($vacacion_id);

  		$informacion_general = $this->calculos_salarios_brutos_especiales($planilla_id, 0 , 'vacaciones');

  		$lista_deducciones 		= Planilla_deducciones_orm::lista_deducciones_planilla($planilla_id);
  		$lista_acumulados 		= Planilla_acumulados_orm::lista_acumulados_planilla($planilla_id);

   		$tabla_deducciones= $this->calculos_deducciones_colaborador($informacion_general, $lista_deducciones);


   		$tabla_acumulados = $this->calculos_acumulados_colaborador($informacion_general, $lista_acumulados);

   		$tabla_pagadas_colaborador[] = array(
   				'salario_bruto' => $informacion_general[0]['salario_bruto'],
   				'planilla_id' => $planilla_id,
   				'uuid_colaborador' => '',
   				'colaborador_id' => $vacacion_info->colaborador_id,
   				'rata' => 0,
   				'salario_neto' => 0,
   				'fecha_inicial' => "",
   				'fecha_final' => "",
   				'fecha_creacion' => date("Y-m-d H.i:s")
   		);
   		$tabla_calculos[] = array(
   				'salario_mensual_promedio' 	=> $informacion_general[0]['salario_mensual_promedio_doce_meses'],
   				'salario_anual_promedio'	=> $informacion_general[0]['salario_bruto_doce_meses'],
   				'total_devengado' 			=>  $informacion_general[0]['total_devengado'],
   				'indemnizacion_proporcional' =>  $informacion_general[0]['indemnizacion_proporcional'],
   				'fecha_creacion' => date("Y-m-d H.i:s")
   		);

    	$tablas_principales  = array(
  				'pagadas_colaborador' => $tabla_pagadas_colaborador,
  				'tabla_calculos' => $tabla_calculos,
  				'tabla_ingresos' => array(),
  				'tabla_deducciones' => isset($tabla_deducciones[0]['deducciones'])?$tabla_deducciones[0]['deducciones']:array(),
  				'tabla_acumulados' => isset($tabla_acumulados[0]['acumulados'])?$tabla_acumulados[0]['acumulados']:array(),
  				'tabla_descuentos_directos' => array()
  		);


  		return $tablas_principales;
  	}

  	private function buscando_tablas_decimo($planilla_id = NULL, $cantidad_semanas = NULL, $colaborador_id = NULL, $fecha_planilla = array()) {
             //calculos_salarios_brutos_especiales($planilla_id = NULL,$cantidad_semanas=NULL, $tipo_accion=NULL )
  		$informacion_general 	= $this->calculos_salarios_brutos_especiales($planilla_id, $cantidad_semanas,'decimo');



    		$lista_deducciones 		= Planilla_deducciones_orm::lista_deducciones_planilla($planilla_id);
  		$lista_acumulados 		= Planilla_acumulados_orm::lista_acumulados_planilla($planilla_id);

  		$tabla_acumulados = $this->calculos_acumulados_colaborador($informacion_general, $lista_acumulados);


  		$tabla_ingresos = isset($informacion_general[0]['lista_ingresos'])?$informacion_general[0]['lista_ingresos']: array();

  		$informacion_general[0]['total_devengado_decimo_8.33'] = $informacion_general[0]['total_devengado_decimo']*0.08333;
   		$tabla_descuentos_directos = array();

                $tabla_deducciones= $this->calculos_deducciones_decimo($informacion_general, $lista_deducciones);
    	 //Error no sirve para n colaborasores abajo
   		$tabla_deducciones = isset($tabla_deducciones[0]['deducciones'])?$tabla_deducciones[0]['deducciones']: array();


  		if(isset($tabla_deducciones['Descuentos directos'])){
  			if(!empty($tabla_deducciones['Descuentos directos']['descuentos_directos'])){
  				$tabla_descuentos_directos = isset($tabla_deducciones['Descuentos directos']['descuentos_directos'])?$tabla_deducciones['Descuentos directos']['descuentos_directos']: array();
  			}
  		}

  		$tabla_acumulados = isset($tabla_acumulados[0]['acumulados'])?$tabla_acumulados[0]['acumulados']: array();

  		$colaborador_info = Colaboradores_orm::find($colaborador_id);

  		$tabla_pagadas_colaborador[] = array(
  				'salario_bruto' => $informacion_general[0]['salario_bruto'],
  				'planilla_id' => $planilla_id,
  				'uuid_colaborador' => hex2bin($colaborador_info->uuid_colaborador),
  				'colaborador_id' => $colaborador_id,
  				//'rata' => $colaborador_info->rata_hora,
  				//'salario_neto' => 0,
  				//'fecha_inicial' => $fecha_planilla['inicial'],
  				'fecha_cierre_planilla' => $fecha_planilla['terminacion'],
  				'fecha_creacion' => date("Y-m-d H.i:s")
  		);

  		$tabla_calculos[] = array(
  				'salario_mensual_promedio' 	=> $informacion_general[0]['salario_mensual_promedio_doce_meses'],
  				'salario_anual_promedio'	=> $informacion_general[0]['salario_bruto_doce_meses'],
  				'total_devengado' 			=>  $informacion_general[0]['total_devengado'],
  				'indemnizacion_proporcional' =>  $informacion_general[0]['indemnizacion_proporcional'],
  				'fecha_creacion' => date("Y-m-d H.i:s")
  		);
  		$tablas_principales  = array(
  				'pagadas_colaborador' => $tabla_pagadas_colaborador,
  				'tabla_calculos' => $tabla_calculos,
  				'tabla_ingresos' => $tabla_ingresos,
  				'tabla_deducciones' => $tabla_deducciones,
  				'tabla_acumulados' => $tabla_acumulados,
  				'tabla_descuentos_directos' => $tabla_descuentos_directos
  		);

  		return 	$tablas_principales;
  	}

//Se crean los pagos en el modulo de Pagos
public function ajax_crear_pagos_planilla() {


  // Just Allow ajax request
  if (! $this->input->is_ajax_request ()) {
    return false;
  }
  //$tablas_devueltos = array();
  $planilla_id			 = $this->input->post('planilla_id', true);

  //Informacion General de planilla
  $planilla_info = $this->planillaRepository->find($planilla_id);
	//	$planilla_info = Planilla_orm::find($planilla_id);



  Capsule::beginTransaction();

  try {

    $this->_createPago( $planilla_info );
    Planilla_orm::where('id', '=', $planilla_id)->update(array("estado_id"=> 32));
    $this->pagadasRepository->cambiando_estado_pagada($planilla_id);

  } catch(ValidationException $e){

    // Rollback
    Capsule::rollback();

    echo json_encode(array(
        "response" => false,
        "mensaje" => "Hubo un error tratando de crear pagos."
    ));
    exit;
  }
  Capsule::commit();

  echo json_encode(array(
      "response" => true,
      "mensaje" => "Se ha creado con &eacute;xito los pagos."
  ));
  exit;
}

private function _createPago( $planillaInfo) {

    $total = Pagos_orm::deEmpresa($planillaInfo->empresa_id)->count();
    $year = Carbon::now()->format('y');
    $planillaInfo->load([
      'colaboradores_pagadas'=> function ($query)  {
          $query->where('estado_pago', 'no_pagado');
      },
      'colaboradores_pagadas.colaborador'
    ]);


    $post['campo']['cuenta_id'] = $planillaInfo->pasivo_id;

    $total = Pagos_orm::deEmpresa($planillaInfo->empresa_id)->count();
    $year = Carbon::now()->format('y');
    $contador = 1;
     if(count($planillaInfo->colaboradores_pagadas)>0){
     foreach ($planillaInfo->colaboradores_pagadas as $key => $value) {

        $aux = [];
       $pago = new Pagos_orm;
       $codigo = Util::generar_codigo('PGO' . $year, $total + $contador);
       $total_pagado_nuevo = (float)str_replace(",","",$value->salario_bruto);
       $pago->codigo = $codigo;

       $pago->empresa_id = $planillaInfo->empresa_id;
       $pago->fecha_pago = date("Y-m-d");
       $pago->proveedor_id = $value->colaborador_id;
       $pago->monto_pagado = $value->salario_neto;
       $pago->cuenta_id = $planillaInfo->pasivo_id;
       $pago->depositable_id = $planillaInfo->id;
       $pago->depositable_type = 'Flexio\Modulo\Planilla\Models\Planilla';
       $pago->formulario = 'planilla';
       $pago->estado = 'por_aplicar';


       $pago->save();

       $aux[$planillaInfo->id] = array(
           "pagable_type" =>'Flexio\\Modulo\\Planilla\\Models\\Planilla',
           "monto_pagado" => $value->salario_bruto,
           "empresa_id" => $planillaInfo->empresa_id
       );

       $pago->planillas()->sync($aux);

       $item_pago = new Pago_metodos_pago_orm;

       $referencia = $this->pagoGuardar->tipo_pago('ach', array(
         'nombre_banco_ach'=>$value->colaborador->banco_id,
         'cuenta_proveedor'=>$value->colaborador->numero_cuenta
       ));

       $item_pago->tipo_pago = 'ach';
       $item_pago->total_pagado = $value->salario_bruto;
       $item_pago->referencia = $referencia;
       $pago->metodo_pago()->save($item_pago);
       ++$contador;
     }

   }

}
//En realidad es cerrar planilla ***
public function ajax_pagar_vacacion() {

    if (! $this->input->is_ajax_request ()) {
    return false;
  }
  $planilla_id	 = $this->input->post('planilla_id', true);
  $planilla_info = $this->planillaRepository->find($planilla_id);
  $planilla_info->load(['vacaciones2',
       'vacaciones2.colaborador',
       'vacaciones2.colaborador.colaboradores_contratos',
       'vacaciones2.colaborador.salarios_devengados' => function ($query)  {
             $query->join('col_colaboradores_contrato', 'col_colaboradores_contrato.id', '=', 'pln_pagadas_colaborador.contrato_id');
        },
        'vacaciones2.colaborador.descuentos_directos' => function ($query)  {
              $query->where('fecha_inicio', '<', date("Y-m-d"));
        },
       'deducciones2.deduccion_info',
       'acumulados2.acumulado_info.formula',
     ]
 );


      $calculos_globales = $this->VacacionRepository->reporte_colaborador($planilla_info);

        Capsule::beginTransaction();
  try {

         $planilla_creada = $this->PagadasVacacionesRepository->crear($calculos_globales);

        //Despues que sea crea la planilla, la informacion se debe halar de las tablas pagadas_*
        if(count($planilla_creada)){

             $planilla_pagada = $this->planillaRepository->find($planilla_creada->planilla_id);
             $planilla_pagada->load(
             "colaboradores_pagadas.planilla",
             "colaboradores_pagadas.colaborador",
             "colaboradores_pagadas.acumulados.acumulado_info",
             "colaboradores_pagadas.deducciones.deduccion_info",
             "colaboradores_pagadas.descuentos.info_descuento",
             "colaboradores_pagadas.ingresos",
             "colaboradores_pagadas.calculos");



        //     Vacaciones::where('id', $id)->update(['comentario' => $comentario]);

             $this->PagosPlanillaVacacion->haceTransaccion($planilla_pagada);
            Capsule::commit();
            $this->Toast->run("success",['CODIGO']);
         }
         else{
            Capsule::rollback();
            $this->Toast->run("error");
        }

    } catch(ValidationException $e){

    Capsule::rollback();
    echo json_encode(array(
        "response" => false,
        "mensaje" => "Hubo un error tratando de cambiar el estado."
    ));
    exit;
  }




  echo json_encode(array(
      "response" => true,
      "mensaje" => "Se ha actualizado con &eacute;xito los cambios."
  ));
  exit;
}

	public function ajax_pagar_planilla() {

    	if (! $this->input->is_ajax_request ()) {
  			return false;
  		}

      $planilla_id	 = $this->input->post('planilla_id', true);
      $planilla_info = $this->planillaRepository->find($planilla_id);

      $validando_pagadas = $this->pagadasRepository->findBy(array('planilla_id'=>$planilla_id));

      if($this->CuentaPlanillaRepository->tieneCuenta(["empresa_id"=>$this->empresa_id]) == false){
        echo json_encode(array(
            "response" => false,
            "mensaje" => "Disculpe, usted no tiene configurado la cuenta de planilla para esta empresa."
        ));
        exit;
      }
      if(count($validando_pagadas) > 0){
        echo json_encode(array(
            "response" => false,
            "mensaje" => "Esta planilla ya tiene data cerrada."
        ));
        exit;
      }
      if($planilla_info->tipo_id == 79){ //Regular

      $planilla_info->load(['colaboradores_planilla',
              'colaboradores_planilla.ingreso_horas.recargo',
              'colaboradores_planilla.ingreso_horas.beneficio',
              'colaboradores_planilla.ingreso_horas.dias',
              'colaboradores_planilla.colaborador.cargo',
              'colaboradores_planilla.colaborador.base_acumulados',
              'colaboradores_planilla.colaborador.descuentos_directos.acreedor',
              'colaboradores_planilla.colaborador.colaboradores_contratos',
              'colaboradores_planilla.colaborador.centro_contable',
              'deducciones2.deduccion_info',
              'acumulados2.acumulado_info.formula'
            ]
        );
      }else if($planilla_info->tipo_id == 96){

          $fecha_desde = $planilla_info->rango_fecha1;
          $fecha_hasta = $planilla_info->rango_fecha2;
          $planilla_info->load([
              'colaboradores_planilla.colaborador',
              'colaboradores_planilla.colaborador.descuentos_directos' => function ($query){
                    $query->where('fecha_inicio', '>', date("Y-m-d"));
              },
              'colaboradores_planilla.colaborador.descuentos_directos.acreedor',
              'colaboradores_planilla.colaborador.colaboradores_contratos',
              'colaboradores_planilla.colaborador.colaboradores_contratos.salarios_devengados_contrato_decimo' => function ($query) use ($fecha_desde, $fecha_hasta)  {
                    $query->where('fecha_cierre_planilla','>=',$fecha_desde)
                    ->where('fecha_cierre_planilla','<=',$fecha_hasta);
               },
              'deducciones2.deduccion_info',
              'acumulados2.acumulado_info.formula'
            ]
        );
      }


      $calculos_globales = $this->planillaRepository->reporte_colaborador($planilla_info);

      Capsule::beginTransaction();
 		try {
          $success = false;
          $planilla_creada = $this->pagadasRepository->crear($calculos_globales, $planilla_info);

          //Despues que sea crea la planilla, la informacion se debe halar de las tablas pagadas_*
          if(count($planilla_creada)){
               $planilla_pagada = $this->planillaRepository->find($planilla_creada->planilla_id);

               $planilla_pagada->load('deducciones2',
               "colaboradores_pagadas.colaborador",
               "colaboradores_pagadas.acumulados.acumulado_info",
               "colaboradores_pagadas.deducciones.deduccion_info",
               "colaboradores_pagadas.descuentos.info_descuento",
               "colaboradores_pagadas.ingresos",
               "colaboradores_pagadas.calculos");

               $success = true;
              if($planilla_info->tipo_id == 79){ //Regular
                     $this->PagosPlanilla->haceTransaccion($planilla_pagada);
              }
            }

           if ($success == false) {
               Capsule::rollback();
               echo json_encode(array(
                   'response' => false,
                   'mensaje' => 'Hubo un error tratando de cambiar la planilla.',
               ));
               exit;
           }


  		}
      catch (\Exception $e) {
          Capsule::rollback();
          echo json_encode(array(
              'response' => false,
              "mensaje" => "Hubo un error tratando de cambiar el estado."
          ));
          exit;
      }
 Capsule::commit();

         echo json_encode(array(
            "response" => true,
            "mensaje" => "Se ha actualizado con &eacute;xito los cambios."
        ));
        exit;
	}

	function ajax_agregar_colaborador_planilla() {
		Capsule::beginTransaction();

		try {
 			$nombres = array();

			if(!empty($_POST["colaboradores"]))
			{
				$planilla = Planilla_orm::find($_POST["planilla_id"]);

				if(Util::is_array_empty($_POST["colaboradores"]) == false){
					$j=0;
					foreach ($_POST["colaboradores"]  AS $colaborador){
 							$tipo =  Colaboradores_orm::where('id','=',  $colaborador )
							->get(array('tipo_salario'));

							$fieldset["estado_ingreso_horas"] = 0;
 							if($tipo[0]->tipo_salario == 'Mensual')
								$fieldset['estado_ingreso_horas'] 	= 3;

							$fieldset["planilla_id"] = $_POST["planilla_id"];
							$fieldset["colaborador_id"] = $colaborador;

							$nombres[] = new Planilla_colaborador_orm($fieldset);
							$j++;
					}
				}

 				$planilla->colaboradores_planilla()->saveMany($nombres);
 				$this->ajax_validar_planilla($_POST["planilla_id"]);
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

 	public function ajax_seleccionar_centro_colaborador() {

		$uuid_empresa = $this->session->userdata('uuid_empresa');
		$empresa = Empresa_orm::findByUuid($uuid_empresa);

		$colaborador_id = $this->input->post ('colaborador_id', true);

 		$response = new stdClass();
		$response = Colaboradores_orm::where("id", "=",$colaborador_id)
		->get();

		echo json_encode(array(
				"response"=>true,
				"id" => 1,
				"centro" => "Centro X"
		));
		exit;
	}
///***
//81: liquidaciones;
//80: vacaciones;
//79: regular;
//83: licnecias
//96: Decimo
public function exportar_csv_abierta2($planilla_id = NULL, $colaborador_id=NULL ) {
            $planilla_id = $_GET['planilla_id'];
            $colaborador_id = isset($_GET['colaborador_id'])?$_GET['colaborador_id']:NULL;
            $planilla_info = $this->planillaRepository->find($planilla_id);


            if($planilla_info->tipo_id == 79 || $planilla_info->tipo_id == 96){
              $planilla_info->load(['colaboradores_planilla'
                      => function ($query) use ($colaborador_id)  {
                      if($colaborador_id != NULL)
                          $query->where('pln_planilla_colaborador.colaborador_id', '=', $colaborador_id);
                      },
                      'colaboradores_planilla.ingreso_horas.recargo',
                      'colaboradores_planilla.ingreso_horas.beneficio',
                      'colaboradores_planilla.ingreso_horas.dias',
                      'colaboradores_planilla.colaborador.cargo',
                      'colaboradores_planilla.colaborador.descuentos_directos.acreedor',
                      'colaboradores_planilla.colaborador.colaboradores_contratos',
                      'colaboradores_planilla.colaborador.centro_contable',
                      'deducciones2.deduccion_info',
                      'acumulados2.acumulado_info'
                    ]
                );
                $calculos_globales = $this->planillaRepository->reporte_colaborador($planilla_info);
                $csvdata = $this->planillaRepository->coleccion_datos_csv($calculos_globales);
            }else if($planilla_info->tipo_id == 80){

              $planilla_info->load('vacaciones_planilla.vacacion.colaborador.centro_contable',
              'vacaciones_planilla.vacacion.colaborador.cargo',
              'vacaciones_planilla.vacacion.colaborador.descuentos_directos.acreedor',
              'vacaciones_planilla.vacacion.colaborador.colaboradores_contratos',
              'vacaciones_planilla.vacacion.colaborador.centro_contable',
              'deducciones2.deduccion_info',
              'acumulados2.acumulado_info'
            );



              $calculos_globales =  $this->VacacionRepository->reporte_colaborador($planilla_info);

              $csvdata = $this->VacacionRepository->coleccion_datos_csv($calculos_globales);
              /*echo "En desarroollo";
             die;*/
            }



            $csv = Writer::createFromFileObject(new SplTempFileObject());
            $csv->insertOne(['','','','Rango de Fechas:',$planilla_info->rango_fecha1.' - '.$planilla_info->rango_fecha2,'','Centro contable:','','','','','']);
            $csv->insertOne(['Planilla No.  ',$planilla_info->codigo,'','Fecha de pago:','','','Area de negocios:','','','','','']);
            $csv->insertOne(['Centro Contable','Posición','Nombre','Cédula','Rata x Hora / Salario fijo','H.R.','H.E.','$H.R.','$H.E.','ISR','S.E.','S.S.','C.S.','Deducciones','Desc. Directo','Sal. Bruto','Sal. Neto']);
            $csv->insertAll($csvdata);
            $csv->output("planilla-". date('ymd') .".csv");
            exit;
}


	public function exportar_csv_abierta() {

		$planilla_id = $_GET['planilla_id'];
		$colaborador_id = isset($_GET['colaborador_id'])?$_GET['colaborador_id']:NULL;

		if($_GET['tipo_planilla_creacion'] == 'regular')
			$this->exportar_csv_abierta_regular($planilla_id, $colaborador_id);
		else if($_GET['tipo_planilla_creacion'] == 'xiii_mes')
			$this->exportar_csv_abierta_decimo($planilla_id);
		else if($_GET['tipo_planilla_creacion'] == 'vacaciones')
			$this->exportar_csv_abierta_vacaciones($planilla_id);

 	}

	public function exportar_csv_cerrada() {

		$planilla_id = $_GET['planilla_id'];

		if($_GET['tipo_planilla_creacion'] == 'regular')
		    $this->exportar_csv_cerrada_regular($planilla_id);
		else if($_GET['tipo_planilla_creacion'] == 'xiii_mes')
			 $this->exportar_csv_cerrada_decimo($planilla_id);
		else if($_GET['tipo_planilla_creacion'] == 'regular')
			$this->exportar_csv_cerrada_regular($planilla_id);
		else if($_GET['tipo_planilla_creacion'] == 'vacaciones')
			$this->exportar_csv_cerrada_decimo($planilla_id);
	}


	private function exportar_csv_abierta_vacaciones($planilla_id = NULL) {
		$planilla_info = Planilla_orm::with(array( 'centro','subcentro','area_negocios','vacaciones.colaborador.cargo'))
		->where('id', "=", $planilla_id)->get()->toArray();

		$centro_contable = isset($planilla_info[0]['centro'])?$planilla_info[0]['centro']['nombre']:'';
		$planilla_numero = $planilla_info[0]['identificador'].$planilla_info[0]['semana'].$planilla_info[0]['ano'].$planilla_info[0]['secuencial'];
		$fecha1 =  date("m/d/Y", strtotime($planilla_info[0]['rango_fecha1']));
		$fecha2 =  date("m/d/Y", strtotime($planilla_info[0]['rango_fecha2']));
		$fecha_pago =  '';

		$lista_deducciones = Planilla_deducciones_orm::lista_deducciones_planilla($planilla_id);

		$i = 0;

		if(!empty($planilla_info[0]['vacaciones'])){
			foreach ($planilla_info[0]['vacaciones'] AS $row)
			{


  				$salario = $this->calculo_salarios_vacaciones_vencidas($row['colaborador_id'], array("inicial"=>$row['fecha_desde'],"final"=>$row['fecha_hasta']));

				 $lista_operadores[0]['salario_bruto'] = $salario['salario_bruto'];
				 $lista_operadores[0]['colaborador_id'] = $row['colaborador_id'];


				if($row['colaborador']['tipo_salario'] == 'Hora'){
					$rata_mensual = utf8_decode(Util::verificar_valor($row['colaborador']['rata_hora']));

				}else{
					$rata_mensual = utf8_decode(Util::verificar_valor($row['colaborador']['salario_mensual']));
				}

				$deducciones = $this->calculos_deducciones_abiertas($lista_operadores,$lista_deducciones);



				$total_desc = $desc_ss = $desc_isr= $desc_se =$total_salario = $desc_cs = $ingreso_hr = $ingreso_nohr = 0;
				//Deducciones
				if(!empty($deducciones[0]['deducciones'])){
					foreach($deducciones[0]['deducciones'] as $deduccion){
                                                if($deduccion['key'] == 1){ //Seguro Social
							$desc_ss = $deduccion['descuento'];
						}
						if($deduccion['key'] == 2){ //Impuesto Sobre la Renta
							$desc_isr = $deduccion['descuento'];
						}
						if($deduccion['key'] == 3){ //eguro Educativo
							$desc_se = $deduccion['descuento'];
						}
						if($deduccion['key'] == 5){//Cuota Sindical
							$desc_cs = $deduccion['descuento'];
						}


						$total_desc += $deduccion['descuento'];
					}
				}

				$nombre = Util::verificar_valor($row['colaborador']['nombre']);
				$apellido = Util::verificar_valor($row['colaborador']['apellido']);

				$csvdata[$i]['posicion'] = Util::verificar_valor($row['colaborador']['cargo']['nombre']);
				$csvdata[$i]['nombre'] = $nombre. " ". $apellido;
				$csvdata[$i]["cedula"] = utf8_decode(Util::verificar_valor($row['colaborador']['cedula']));
				$csvdata[$i]["rata_hora"] = $rata_mensual;
				$csvdata[$i]["hr"] = number_format($lista_operadores[0]['salario_bruto'],2);
				$csvdata[$i]["he"] = 0.00;
				$csvdata[$i]["isr"] = number_format($desc_isr,2);
				$csvdata[$i]["se"] = number_format($desc_se,2);
				$csvdata[$i]["ss"] = number_format($desc_ss,2);
				$csvdata[$i]["cs"] = number_format($desc_cs,2);
				$csvdata[$i]["deducciones"] = number_format($total_desc,2);
				$csvdata[$i]["descuento"] = 0;
				$csvdata[$i]["salario_bruto"] = number_format($lista_operadores[0]['salario_bruto'],2);
				$csvdata[$i]["salario_neto"] = number_format($lista_operadores[0]['salario_bruto']-$total_desc,2);
				$i++;
			}
 		}
 		$csv = Writer::createFromFileObject(new SplTempFileObject());
		$csv->insertOne(['','','','Rango de Fechas:',$fecha1.' - '.$fecha2,'','Centro contable:',$centro_contable,'','','','']);
		$csv->insertOne(['Planilla No.  ',$planilla_numero,'','Fecha de pago:',$fecha_pago,'','Area de negocios:',$planilla_info[0]['area_negocios']['nombre'],'','','','']);
		$csv->insertOne([
				'Posición',
				'Nombre',
				'Cédula',
				'Rata x Hora / Salario fijo',
				'Vacaciones',
				'H.E.',
				'ISR',
				'S.E.',
				'S.S.',
				'C.S.',
				'Deducciones',
				'Desc. Directo',
				'Sal. Bruto',
				'Sal. Neto',
				]);
		$csv->insertAll($csvdata);
		$csv->output("planilla-". date('ymd') .".csv");
		die;
	}

	private function exportar_csv_abierta_decimo($planilla_id = NULL) {
		$planilla_info = Planilla_orm::with(array( 'centro','subcentro','area_negocios','colaboradores.cargo'))
		->where('id', "=", $planilla_id)->get()->toArray();

		$centro_contable = isset($planilla_info[0]['centro'])?$planilla_info[0]['centro']['nombre']:'';
		$planilla_numero = $planilla_info[0]['identificador'].$planilla_info[0]['semana'].$planilla_info[0]['ano'].$planilla_info[0]['secuencial'];
		$fecha1 =  date("m/d/Y", strtotime($planilla_info[0]['rango_fecha1']));
		$fecha2 =  date("m/d/Y", strtotime($planilla_info[0]['rango_fecha2']));
		$fecha_pago =  '';

		$lista_deducciones = Planilla_deducciones_orm::lista_deducciones_planilla($planilla_id);
		$lista_acumulados = Planilla_acumulados_orm::lista_acumulados_planilla($planilla_id);

		$i = 0;

		if(!empty($planilla_info[0]['colaboradores'])){
			foreach ($planilla_info[0]['colaboradores'] AS $row)
			{
 				$lista_operadores = $this->operadores_lista($row['id'], $planilla_id);
 				$lista_operadores[0]['salario_bruto'] = $lista_operadores[0]['total_devengado_decimo']*0.0833;

				if($row['tipo_salario'] == 'Hora'){
  					$rata_mensual = utf8_decode(Util::verificar_valor($row['rata_hora']));

				}else{
 					$rata_mensual = utf8_decode(Util::verificar_valor($row['salario_mensual']));
				}

 				$deducciones = $this->calculos_deducciones_abiertas($lista_operadores,$lista_deducciones);
				$total_desc = $desc_ss = $desc_isr= $desc_se =$total_salario = $desc_cs = $ingreso_hr = $ingreso_nohr = 0;
				//Deducciones
				if(!empty($deducciones[0]['deducciones'])){
					foreach($deducciones[0]['deducciones'] as $deduccion){

            if($deduccion['key'] == 1){ //Seguro Social
							$desc_ss = $deduccion['descuento'];
						}
						if($deduccion['key'] == 2){ //Impuesto Sobre la Renta
							$desc_isr = $deduccion['descuento'];
						}
						if($deduccion['key'] == 3){ //eguro Educativo
							$desc_se = $deduccion['descuento'];
						}
						if($deduccion['key'] == 5){//Cuota Sindical
							$desc_cs = $deduccion['descuento'];
						}

						$total_desc += $deduccion['descuento'];
					}
				}

				$nombre = Util::verificar_valor($row['nombre']);
				$apellido = Util::verificar_valor($row['apellido']);

				$csvdata[$i]['posicion'] = Util::verificar_valor($row['cargo']['nombre']);
				$csvdata[$i]['nombre'] = $nombre. " ". $apellido;
				$csvdata[$i]["cedula"] = utf8_decode(Util::verificar_valor($row['cedula']));
				$csvdata[$i]["rata_hora"] = $rata_mensual;
				$csvdata[$i]["hr"] = number_format($lista_operadores[0]['salario_bruto'],2);
				$csvdata[$i]["he"] = 0.00;
				$csvdata[$i]["isr"] = number_format($desc_isr,2);
				$csvdata[$i]["se"] = number_format($desc_se,2);
				$csvdata[$i]["ss"] = number_format($desc_ss,2);
				$csvdata[$i]["cs"] = number_format($desc_cs,2);
				$csvdata[$i]["deducciones"] = number_format($total_desc,2);
				$csvdata[$i]["descuento"] = 0;
				$csvdata[$i]["salario_bruto"] = number_format($lista_operadores[0]['salario_bruto'],2);
				$csvdata[$i]["salario_neto"] = number_format($lista_operadores[0]['salario_bruto']-$total_desc,2);
				$i++;
			}
 		}

		$csv = Writer::createFromFileObject(new SplTempFileObject());
		$csv->insertOne(['','','','Rango de Fechas:',$fecha1.' - '.$fecha2,'','Centro contable:',$centro_contable,'','','','']);
		$csv->insertOne(['Planilla No.  ',$planilla_numero,'','Fecha de pago:',$fecha_pago,'','Area de negocios:',$planilla_info[0]['area_negocios']['nombre'],'','','','']);
		$csv->insertOne([
				'Posición',
				'Nombre',
				'Cédula',
				'Rata x Hora / Salario fijo',
				'Décimo',
				'H.E.',
				'ISR',
				'S.E.',
				'S.S.',
				'C.S.',
				'Deducciones',
				'Desc. Directo',
				'Sal. Bruto',
				'Sal. Neto',
				]);
		$csv->insertAll($csvdata);
		$csv->output("planilla-". date('ymd') .".csv");
		die;
	}

	private function exportar_csv_abierta_regular($planilla_id = NULL, $colaborador_id = NULL) {

    if($colaborador_id != NULL){
        $planilla_info = Planilla_orm::with(array( 'centro','subcentro','area_negocios','colaboradores' => function($query) use ($colaborador_id)
        {
           $query->where('colaborador_id', $colaborador_id);
         },'colaboradores.cargo'))->where('id', "=", $planilla_id)->get()->toArray();

    }else{
        $planilla_info = Planilla_orm::with(array( 'centro','subcentro','area_negocios','colaboradores.cargo'))->where('id', "=", $planilla_id)->get()->toArray();
    }


 		$centro_contable = isset($planilla_info[0]['centro'])?$planilla_info[0]['centro']['nombre']:'';
		$planilla_numero = $planilla_info[0]['identificador'].$planilla_info[0]['semana'].$planilla_info[0]['ano'].$planilla_info[0]['secuencial'];
		$fecha1 =  date("m/d/Y", strtotime($planilla_info[0]['rango_fecha1']));
		$fecha2 =  date("m/d/Y", strtotime($planilla_info[0]['rango_fecha2']));
		$fecha_pago =  '';

 		$lista_deducciones = Planilla_deducciones_orm::lista_deducciones_planilla($planilla_id);
 		$lista_acumulados = Planilla_acumulados_orm::lista_acumulados_planilla($planilla_id);

		$i = 0;

 		if(!empty($planilla_info[0]['colaboradores'])){
			foreach ($planilla_info[0]['colaboradores'] AS $row)
			{


				$lista_operadores = $this->operadores_lista($row['id'], $planilla_id);

  				if($row['tipo_salario'] == 'Hora'){
					$ingresos = $this->listado_ingresos_regular(
							$planilla_id,
							0,
							$row['id'],
							array('inicial'=>$planilla_info[0]['rango_fecha1'],
									'terminacion'=>$planilla_info[0]['rango_fecha1']
							),
							$row['rata_hora']
					);

					$lista_operadores[0]['salario_bruto'] = $ingresos['salario_bruto'];
					$rata_mensual = utf8_decode(Util::verificar_valor($row['rata_hora']));

				}else{
					$ingresos= array();
					$rata_mensual = utf8_decode(Util::verificar_valor($row['salario_mensual']));
				}

				$deducciones = $this->calculos_deducciones_abiertas($lista_operadores,$lista_deducciones);

 				$total_desc = $desc_ss = $desc_isr= $desc_se =$total_salario = $desc_cs = $ingreso_hr = $ingreso_nohr = 0;
				//Deducciones

				if(!empty($deducciones[0]['deducciones'])){
					foreach($deducciones[0]['deducciones'] as $deduccion){


             if($deduccion['key'] == 1){ //Seguro Social
							$desc_ss = $deduccion['descuento'];
						}
						if($deduccion['key'] == 2){ //Impuesto Sobre la Renta
							$desc_isr = $deduccion['descuento'];
						}
						if($deduccion['key'] == 3){ //eguro Educativo
							$desc_se = $deduccion['descuento'];
						}
						if($deduccion['key'] == 5){//Cuota Sindical
							$desc_cs = $deduccion['descuento'];
						}

						$total_desc += $deduccion['descuento'];
					}
				}

				$nombre = Util::verificar_valor($row['nombre']);
				$apellido = Util::verificar_valor($row['apellido']);

 				$csvdata[$i]['posicion'] = Util::verificar_valor($row['cargo']['nombre']);
				$csvdata[$i]['nombre'] = $nombre. " ". $apellido;
				$csvdata[$i]["cedula"] = utf8_decode(Util::verificar_valor($row['cedula']));
				$csvdata[$i]["rata_hora"] = $rata_mensual;
				$csvdata[$i]["hr"] = number_format($lista_operadores[0]['salario_bruto'],2);
				$csvdata[$i]["he"] = 0;
				$csvdata[$i]["isr"] = number_format($desc_isr,2);
				$csvdata[$i]["se"] = number_format($desc_se,2);
				$csvdata[$i]["ss"] = number_format($desc_ss,2);
				$csvdata[$i]["cs"] = number_format($desc_cs,2);
				$csvdata[$i]["deducciones"] = number_format($total_desc,2);
				$csvdata[$i]["descuento"] = 0;
				$csvdata[$i]["salario_bruto"] = number_format($lista_operadores[0]['salario_bruto'],2);
				$csvdata[$i]["salario_neto"] = number_format($lista_operadores[0]['salario_bruto']-$total_desc,2);
				$i++;
			}

		}

  		$csv = Writer::createFromFileObject(new SplTempFileObject());
		$csv->insertOne(['','','','Rango de Fechas:',$fecha1.' - '.$fecha2,'','Centro contable:',$centro_contable,'','','','']);
		$csv->insertOne(['Planilla No.  ',$planilla_numero,'','Fecha de pago:',$fecha_pago,'','Area de negocios:',$planilla_info[0]['area_negocios']['nombre'],'','','','']);
		$csv->insertOne([
      'Posición',
      'Nombre',
      'Cédula',
				'Rata x Hora / Salario fijo',
				'H.R.',
				'H.E.',
				'ISR',
				'S.E.',
				'S.S.',
				'C.S.',
				'Deducciones',
				'Desc. Directo',
				'Sal. Bruto',
				'Sal. Neto',
				]);
		$csv->insertAll($csvdata);
		$csv->output("planilla-". date('ymd') .".csv");
		exit;

	}

	private function exportar_csv_cerrada_decimo($planilla_id = NULL) {

		$planilla_info = Planilla_orm::with(array( 'centro','subcentro','area_negocios'))
		->where('id', "=", $planilla_id)->get()->toArray();

		$centro_contable = isset($planilla_info[0]['centro'])?$planilla_info[0]['centro']['nombre']:'';
  		$planilla_numero = $planilla_info[0]['identificador'].$planilla_info[0]['semana'].$planilla_info[0]['ano'].$planilla_info[0]['secuencial'];
 		$fecha1 =  date("m/d/Y", strtotime($planilla_info[0]['rango_fecha1']));
		$fecha2 =  date("m/d/Y", strtotime($planilla_info[0]['rango_fecha2']));
		$fecha_pago =  '';

		$i = 0;
		$pagos_info =Pagadas_colaborador_orm::with('acumulados','deducciones','descuentos','ingresos','calculos')
		->where("planilla_id", "=", $planilla_id)
		->get()->toArray();


		if(!empty($pagos_info)){
			foreach ($pagos_info AS $row)
			{

				$total_desc = $desc_ss = $desc_isr= $desc_se = $desc_cs  =0;
				//Deducciones
				if(!empty($row['deducciones'])){
					foreach($row['deducciones'] as $deduccion){
						/*if($deduccion['nombre'] == 'Seguro Social'){
							$desc_ss = $deduccion['descuento'];
						}
						if($deduccion['nombre'] == 'Impuesto sobre la Renta'){
							$desc_isr = $deduccion['descuento'];
						}
						if($deduccion['nombre'] == 'Seguro Educativo'){
							$desc_se = $deduccion['descuento'];
						}
						if($deduccion['nombre'] == 'Cuota Sindical'){
							$desc_cs = $deduccion['descuento'];
						}*/
                                                if($deduccion['key'] == 1){ //Seguro Social
							$desc_ss = $deduccion['descuento'];
						}
						if($deduccion['key'] == 2){ //Impuesto Sobre la Renta
							$desc_isr = $deduccion['descuento'];
						}
						if($deduccion['key'] == 3){ //eguro Educativo
							$desc_se = $deduccion['descuento'];
						}
						if($deduccion['key'] == 5){//Cuota Sindical
							$desc_cs = $deduccion['descuento'];
						}

						$total_desc += $deduccion['descuento'];
					}
				}


				//$colaborador_info = Colaboradores_orm::find($row['colaborador_id']);
				$colaborador_info =Colaboradores_orm::with('cargo')
				->where("id", "=", $row['colaborador_id'])
				->get()->toArray();

				$nombre = Util::verificar_valor($colaborador_info[0]['nombre']);
				$apellido = Util::verificar_valor($colaborador_info[0]['apellido']);

 				$csvdata[$i]['posicion'] = Util::verificar_valor($colaborador_info[0]['cargo']['nombre']);
 				$csvdata[$i]['nombre'] = $nombre. " ". $apellido;
				$csvdata[$i]["cedula"] = utf8_decode(Util::verificar_valor($colaborador_info[0]['cedula']));
				$csvdata[$i]["rata_hora"] = utf8_decode(Util::verificar_valor($colaborador_info[0]['rata_hora'])).'/'.utf8_decode(Util::verificar_valor($colaborador_info[0]['salario_mensual']));
				$csvdata[$i]["decimo"] = number_format($row['salario_bruto'],2);
 				$csvdata[$i]["isr"] = number_format($desc_isr,2);
				$csvdata[$i]["se"] = number_format($desc_se,2);
				$csvdata[$i]["ss"] = number_format($desc_ss,2);
				$csvdata[$i]["cs"] = number_format($desc_cs,2);
				$csvdata[$i]["deducciones"] = number_format($total_desc,2);
				$csvdata[$i]["descuento"] = 0;
				$csvdata[$i]["salario_bruto"] = number_format($row['salario_bruto'],2);
				$csvdata[$i]["salario_neto"] = number_format($row['salario_bruto']-$total_desc,2);
				$i++;
			}
		}
		//we create the CSV into memory
		$csv = Writer::createFromFileObject(new SplTempFileObject());
		$csv->insertOne(['','','','Rango de Fechas:',$fecha1.' - '.$fecha2,'','Centro contable:',$centro_contable,'','','','']);
		$csv->insertOne(['Planilla No.  ',$planilla_numero,'','Fecha de pago:',$fecha_pago,'','Area de negocios:',$planilla_info[0]['area_negocios']['nombre'],'','','','']);
		$csv->insertOne([
      'Posición',
      'Nombre',
      'Cédula',
				'Rata x Hora / Salario fijo',
				'Décimo.',
 				'ISR',
				'S.E.',
				'S.S.',
				'C.S.',
				'Deducciones',
				'Desc. Directo',
				'Sal. Bruto',
				'Sal. Neto',
				]);
		$csv->insertAll($csvdata);
		$csv->output("planilla-". date('ymd') .".csv");
		die;

	}

 	private function date_diff($date1, $date2) {
		$current = $date1;
		$datetime2 = date_create($date2);
		$count = 0;
		while(date_create($current) < $datetime2){
			$current = gmdate("Y-m-d", strtotime("+1 day", strtotime($current)));
			$count++;
		}
		return $count;
	}

public function ajax_informacion_total_horas() {
  $planilla_id    = $this->input->post ('planilla_id', true);
  $colaborador_id = $this->input->post ('colaborador_id', true);

  $planilla_colaborador 			=  Planilla_colaborador_orm::where('planilla_id','=', $planilla_id )->where('colaborador_id','=', $colaborador_id )->get();
   $total_horas = Ingreso_horas_dias_orm::totalHoras($planilla_colaborador[0]->id);

  echo json_encode(array(
      "response"=>true,
      "totalHoras" =>$total_horas,
   ));
  exit;
}

	public function ajax_seleccionar_comentario() {


		$ingreso_horas_id = $this->input->post ('ingreso_horas_id', true);
		$fecha = $this->input->post ('fecha', true);

 		$resultado = Ingreso_horas_dias_orm::where("ingreso_horas_id", "=",$ingreso_horas_id)->where("fecha", "=",$fecha)
		->get(array("comentario","id"));

 		echo json_encode(array(
				"response"=>true,
 				"comentario" =>$resultado[0]->comentario,
 				"id" =>$resultado[0]->id
		));
		exit;
	}

	public function ajax_guardar_comentario() {
 		if (! $this->input->is_ajax_request ()) {
			return false;
		}

		Capsule::beginTransaction();

 		$id		 = $this->input->post('ingresohoras_dias_id', true); //Nuevo o Edicion
 		$comentario		 = $this->input->post('comentario', true); //Nuevo o Edicion

 		try {
 				Ingreso_horas_dias_orm::where('id', $id)->update(['comentario' => $comentario]);

		} catch(ValidationException $e){

			// Rollback
			Capsule::rollback();

			echo json_encode(array(
					"response" => false,
 			));
			exit;
		}

		Capsule::commit();

		echo json_encode(array(
				"response" => true,
 		));
		exit;

	}

	private function creando_tablas_historial_vacaciones($planilla_id = NULL) {
		$tablas_devueltos = array();

		$planilla_info = Planilla_orm::find($planilla_id);

 		$vacaciones 			=  Planilla_vacacion_orm::where('planilla_id','=', $planilla_id )->get();

		if(!empty($vacaciones->toArray())){
			foreach ($vacaciones->toArray() AS $i => $row){
 				$vacacion[] = $row['vacacion_id'];
				$tablas_devueltos[] = $this->buscando_tablas_vacaciones($planilla_id, $row['vacacion_id']);
			}
		}
		$fieldset = array();

		foreach ($tablas_devueltos as $tabla ){
			//$salario_bruto = 0;
			$calculos = $ingresos = $deducciones = $acumulados =  $descuentos = array();
			$acumulado_total = 0;
			if( !empty($tabla['pagadas_colaborador']) ){

				if(!empty($tabla['tabla_acumulados'])){
					foreach ($tabla['tabla_acumulados'] as $acumulado_personal){
						$acumulado_total += $acumulado_personal['acumulado'];
					}
				}
				$tabla['pagadas_colaborador'][0]['salario_bruto'] = $acumulado_total;

				$pagadas = Pagadas_colaborador_orm::create($tabla['pagadas_colaborador'][0]);
			}
			if( !empty($tabla['tabla_ingresos']) ){

				foreach ($tabla['tabla_ingresos'] as $ingreso){

					$ingreso['fecha_creacion'] = date("Y-m-d H:i:s");
					$ingresos[] 				= new Pagadas_ingresos_orm($ingreso);

				}
				$pagadas->ingresos()->saveMany($ingresos);
			}
			if( !empty($tabla['tabla_deducciones']) ){
				foreach ($tabla['tabla_deducciones'] as $deduccion){

					$deduccion['deduccion_id'] = $deduccion['id'];
					$deduccion['fecha_creacion'] = date("Y-m-d H:i:s");
					$deducciones[] 				= new Pagadas_deducciones_orm($deduccion);
				}
				$pagadas->deducciones()->saveMany($deducciones);
			}
			if( !empty($tabla['tabla_acumulados']) ){

				foreach ($tabla['tabla_acumulados'] as $acumulado){
					$acumulado['acumulado_id'] = $acumulado['id'];
					$acumulado['saldo'] = $acumulado['acumulado'];
					$acumulado['fecha_creacion'] = date("Y-m-d H:i:s");
					$acumulados[] 				= new Pagadas_acumulados_orm($acumulado);
				}
				$pagadas->acumulados()->saveMany($acumulados);
			}

			if( !empty($tabla['tabla_descuentos_directos']) ){

				foreach ($tabla['tabla_descuentos_directos'] as $descuento){

					$descuento['descuento_id'] = $descuento['id'];
					$descuento['fecha_creacion'] = date("Y-m-d H:i:s");
					$descuentos[] 				= new Pagadas_descuentos_orm($descuento);
				}
				$pagadas->descuentos()->saveMany($descuentos);
			}

			if( !empty($tabla['tabla_calculos']) ){

				$calculos[]			= new Pagadas_calculos_orm( $tabla['tabla_calculos'][0] );
				$pagadas->calculos()->saveMany($calculos);
			}

		}
		return null;
	}


	private function creando_tablas_historial_decimo($planilla_id = NULL) {
		$tablas_devueltos = array();

		$planilla_info = Planilla_orm::find($planilla_id);
		$fecha1 =  date("m/d/Y", strtotime($planilla_info->rango_fecha1));
		$fecha2 =  date("m/d/Y", strtotime($planilla_info->rango_fecha2));

		$fecha_planilla 	= array("inicial"=>$planilla_info->rango_fecha1,"terminacion"=>$planilla_info->rango_fecha2);
		$interval 			= $this->date_diff($fecha1, $fecha2);

		$colaboradores 			=  Planilla_colaborador_orm::where('planilla_id','=', $planilla_id )->get();

		if(!empty($colaboradores->toArray())){
			foreach ($colaboradores->toArray() AS $i => $row){

				$colaborador[] = $row['colaborador_id'];
				$tablas_devueltos[] = $this->buscando_tablas_decimo($planilla_id, 0, $row['colaborador_id'], $fecha_planilla);
 			}
		}
		$fieldset = array();

		foreach ($tablas_devueltos as $tabla ){

 			//$salario_bruto = 0;
			$calculos = $ingresos = $deducciones = $acumulados =  $descuentos = array();
			$acumulado_total = 0;
			if( !empty($tabla['pagadas_colaborador']) ){

				if(!empty($tabla['tabla_acumulados'])){
					foreach ($tabla['tabla_acumulados'] as $acumulado_personal){
						$acumulado_total += $acumulado_personal['acumulado'];
					}
				}
				$tabla['pagadas_colaborador'][0]['salario_bruto'] = $acumulado_total;

				$pagadas = Pagadas_colaborador_orm::create($tabla['pagadas_colaborador'][0]);
			}
			if( !empty($tabla['tabla_ingresos']) ){

				foreach ($tabla['tabla_ingresos'] as $ingreso){

					$ingreso['fecha_creacion'] = date("Y-m-d H:i:s");
					$ingresos[] 				= new Pagadas_ingresos_orm($ingreso);

				}
				$pagadas->ingresos()->saveMany($ingresos);
			}
			if( !empty($tabla['tabla_deducciones']) ){

 				foreach ($tabla['tabla_deducciones'] as $deduccion){
 					$deduccion['deduccion_id'] = $deduccion['id'];
					$deduccion['fecha_creacion'] = date("Y-m-d H:i:s");

					$deducciones[] 				= new Pagadas_deducciones_orm($deduccion);
				}
				$pagadas->deducciones()->saveMany($deducciones);
			}
			if( !empty($tabla['tabla_acumulados']) ){

				foreach ($tabla['tabla_acumulados'] as $acumulado){
					$acumulado['acumulado_id'] = $acumulado['id'];
					$acumulado['saldo'] = $acumulado['acumulado'];
					$acumulado['fecha_creacion'] = date("Y-m-d H:i:s");
					$acumulados[] 				= new Pagadas_acumulados_orm($acumulado);
				}
				$pagadas->acumulados()->saveMany($acumulados);
			}

			if( !empty($tabla['tabla_descuentos_directos']) ){

				foreach ($tabla['tabla_descuentos_directos'] as $descuento){

					$descuento['descuento_id'] = $descuento['id'];
					$descuento['fecha_creacion'] = date("Y-m-d H:i:s");
					$descuentos[] 				= new Pagadas_descuentos_orm($descuento);
				}
				$pagadas->descuentos()->saveMany($descuentos);
			}

			if( !empty($tabla['tabla_calculos']) ){

				$calculos[]			= new Pagadas_calculos_orm( $tabla['tabla_calculos'][0] );
				$pagadas->calculos()->saveMany($calculos);
			}

		}
 		return null;
	}
	//funcion Nueva ***
	public function ajax_cerrar_planilla_liquidacion() {
		// Just Allow ajax request
		if (! $this->input->is_ajax_request ()) {
			return false;
		}
		$planilla_id			 = $this->input->post('planilla_id', true);

		Capsule::beginTransaction();
		try {
  				$planilla = $this->planillaRepository->find($planilla_id);
 				$planilla->load([
							"liquidaciones.colaborador.forma_pago",
							"liquidaciones.colaborador.descuentos_directos.acreedor",
							"liquidaciones.colaborador.colaboradores_contratos",
							"liquidaciones.colaborador.salarios_devengados"
				]
				)->get();

 		 		$configuracion = $this->configuracionPlanillaLiquidacionRepository->findByTipoPago($planilla['liquidaciones'][0]->tipo_liquidacion_id);
 		 		$configuracion->load("pagos.tipoPago","pagos.deducciones", "pagos.deducciones.deduccion_info");

 		 		$calculos = $this->liquidacionPagadaRepository->cerrando_planilla($planilla, $configuracion);
 		 		$calculos = $this->liquidacionPagadaRepository->actualizando_planilla($planilla);

		} catch(ValidationException $e){

			// Rollback
			Capsule::rollback();

			$mensaje = array('estado'=>500, 'mensaje'=>'<b>Hubo un error tratando de actualizar la planilla.</b> ');
			$this->session->set_flashdata('mensaje', $mensaje);
			echo json_encode(array(
					"response" => false
					//"mensaje" => "Hubo un error tratando de crear la planilla."
			));
			exit;
		}
		Capsule::commit();

		$mensaje = array('estado'=>200, 'mensaje'=>'&Eacute;xito! Se ha creado cerrado la planilla.');
		$this->session->set_flashdata('mensaje', $mensaje);

		echo json_encode(array(
				"response" => true,
		));
		exit;
	}
	public function ajax_cerrar_planilla_especial() {

		// Just Allow ajax request
		if (! $this->input->is_ajax_request ()) {
			return false;
		}
		$planilla_id			 = $this->input->post('planilla_id', true);
		$tipo_planilla_creacion			 = $this->input->post('tipo_planilla_creacion', true);


 		Capsule::beginTransaction();
		try {

			if( $tipo_planilla_creacion == 'vacaciones'){
				$this->creando_tablas_historial_vacaciones($planilla_id);

			}else{
				$this->creando_tablas_historial_decimo($planilla_id);
			}

 			$planila = Planilla_orm::where('id', '=', $planilla_id)->update(array("estado_id"=> 14));

		} catch(ValidationException $e){

			// Rollback
			Capsule::rollback();

			$mensaje = array('estado'=>500, 'mensaje'=>'<b>Hubo un error tratando de cambiar el planilla.</b> ');
     		$this->session->set_flashdata('mensaje', $mensaje);
     		echo json_encode(array(
     				"response" => false
     				//"mensaje" => "Hubo un error tratando de crear la planilla."
     		));
     		exit;
		}
		Capsule::commit();

		$mensaje = array('estado'=>200, 'mensaje'=>'&Eacute;xito! Se ha creado cerrado la planilla.');
		$this->session->set_flashdata('mensaje', $mensaje);

		echo json_encode(array(
				"response" => true,
		));
		exit;
	}
  //***
  public function ajax_detalles_pago2() {

     if(!$this->input->is_ajax_request()){
      return false;
    }
    $response = new stdClass();

    $planilla_id 	= $this->input->post('planilla_id', true);
    $colaborador_id = NULL;

    $planilla_info = $this->planillaRepository->find($planilla_id);
    $planilla_info->load(['colaboradores_planilla'
            => function ($query) use ($colaborador_id)  {
            if($colaborador_id != NULL)
                $query->where('pln_planilla_colaborador.colaborador_id', '=', $colaborador_id);
            },
            'colaboradores_planilla.ingreso_horas.recargo',
            'colaboradores_planilla.ingreso_horas.beneficio',
            'colaboradores_planilla.ingreso_horas.dias',
            'colaboradores_planilla.colaborador.cargo',
            'colaboradores_planilla.colaborador.descuentos_directos.acreedor',
            'colaboradores_planilla.colaborador.colaboradores_contratos',
            'colaboradores_planilla.colaborador.centro_contable',
            'deducciones2.deduccion_info',
            'acumulados2.acumulado_info.formula'
          ]
      );

    $calculos_globales = $this->planillaRepository->reporte_colaborador($planilla_info);
   	$response->response = true;
    $response->calculos = $this->CalculosRepository->collecion_cerrar_planilla_regular($calculos_globales);

    echo json_encode($response);
    exit;
  }


 	public function ajax_detalles_pago_decimo() {

 		if(!$this->input->is_ajax_request()){
 			return false;
 		}
  		$planilla_id 	= $this->input->post('planilla_id', true);
  		$this->calculo_para_decimo( $planilla_id);

  	}



	private function calculo_para_decimo($planilla_id = NULL) {
 		//Just Allow ajax request
		if(!$this->input->is_ajax_request()){
			return false;
		}

		$decimo_total = $deduccion_total = 0;

		$colaboradores = $this->calculos_salarios_brutos_especiales( $planilla_id, 0 , 'decimo' );

   		if(!empty($colaboradores)){
			foreach($colaboradores as $colaborador){
				$decimo_total +=  $colaborador['total_devengado_decimo_8.33'];
  			}
		}

		$lista_deducciones = Planilla_deducciones_orm::lista_deducciones_planilla($planilla_id);
		$calculo_deducciones_por_colaborador = $this->calculos_deducciones_decimo($colaboradores, $lista_deducciones);

   		if(!empty($calculo_deducciones_por_colaborador)){
			foreach ($calculo_deducciones_por_colaborador as $deducciones){
				if(!empty($deducciones['deducciones'])){
					foreach ($deducciones['deducciones'] as $calculo){
 						$deduccion_total += $calculo['descuento'];
					}

				}
			}
		}

		$response = new stdClass();
		$response->response = true;
		$response->cantidad_colaboradores 	=  count($colaboradores);
		$response->salario_bruto 			=  number_format( $decimo_total,2);

		$response->deducciones 				=  number_format($deduccion_total,2);
		$response->deducciones_porcentaje 	=  $decimo_total>0?number_format(($deduccion_total/$decimo_total)*100,2):0;
		$response->deducciones_progress_bar =  $decimo_total>0?number_format(($deduccion_total/$decimo_total)*100,2):0;

		$response->salario_neto 			=  number_format($decimo_total-$deduccion_total,2);
		$response->salario_neto_porcentaje 	=  $decimo_total>0?number_format((($decimo_total-$deduccion_total)/$decimo_total)*100,2):0;
		$response->salario_neto_progress_bar=  $decimo_total>0?number_format((($decimo_total-$deduccion_total)/$decimo_total)*100,2):0;


		echo json_encode($response);
		exit;
	}


	//Nueva Funcion
	public function ajax_modal_liquidacion() {
		 if(!$this->input->is_ajax_request()){
			return false;
		 }

		 $response = new stdClass();
 		 $planilla_id 	= $this->input->post('planilla_id', true);

		 $planilla = $this->planillaRepository->find($planilla_id);
 		 $planilla->load(
 		     "liquidaciones",
 		     "liquidaciones.colaborador.colaboradores_contratos",
 		     "liquidaciones.colaborador.salarios_devengados_ultimos_cinco_anos",
 		     "liquidaciones.contrato",
 		     "liquidaciones.colaborador.planilla_activa.colaboradores_planilla.ingreso_horas.recargo",
 		     "liquidaciones.colaborador.planilla_activa.colaboradores_planilla.ingreso_horas.dias"
 		  )->toArray();

 		 $configuracion = $this->configuracionPlanillaLiquidacionRepository->findByTipoPago($planilla['liquidaciones'][0]->tipo_liquidacion_id);
  		 $configuracion->load("pagos.tipoPago","pagos.deducciones", "pagos.deducciones.deduccion_info");


 		 $calculos = $this->liquidacionPagadaRepository->calculo_valores($planilla, $configuracion);
   		 $response->response = true;
 		 $response->total_colaboradores 	 = $calculos['cantidad_colaboradores'];
 		 $response->salario_bruto 			 = number_format($calculos['total_planilla'],2);//number_format(0,2)
 		 $response->deducciones 			 = $calculos['total_deduccion'];
 		 $response->deducciones_porcentaje 	 = $calculos['deducciones_porcentaje'];
 		 $response->deducciones_progress_bar = $calculos['deducciones_porcentaje'];
 		 $response->salario_neto 			 = $calculos['salario_neto'];
 		 $response->salario_neto_porcentaje  = $calculos['salario_neto_porcentaje'];
 		 $response->salario_neto_progress_bar= $calculos['salario_neto_porcentaje'];
 		 echo json_encode($response);
 		 exit;
	}


	function buildTree( $ar, $pid = null ) {

		$op = array();
		foreach( $ar as $item ) {
			if( (int)$item['padre_id'] == (int)$pid ) {
				$op[$item['id']] = array(
						'id' => $item['id'],
						'name' => $item['nombre'],
						'padre_id' => $item['padre_id']
				);
				// using recursion


				$children =  $this->buildTree( $ar, $item['id'] );

 				if( $children ) {
					$contador_hijos = 0;
					foreach( $children as $item_chi ) {

						$op[$item['id']]['children'][$contador_hijos]['id'] = $item_chi['id'];
						$op[$item['id']]['children'][$contador_hijos]['name'] = $item_chi['name'];
						$op[$item['id']]['children'][$contador_hijos]['padre_id'] = $item_chi['padre_id'];

						$dpto = Planilla_orm::lista_departamentos_centro( $this->empresa_id , $item_chi['id']);
						$op[$item['id']]['children'][$contador_hijos]['area_negocio'] = array();
						foreach($dpto as $dpto_data){

							$clause["empresa_id"] 			= $this->empresa_id;
							$clause["centro_contable_id"] 	= $item_chi['id']; //Subcentroo
							$clause["departamento_id"] 		= array($dpto_data->id);    // o area de Negocio

							$cantidad_colaboradores = colaboradores_orm::listar($clause)->count();
							if($cantidad_colaboradores > 0){
								$op[$item['id']]['children'][$contador_hijos]['area_negocio'][] = array(
										'id' => $dpto_data->id,
										'nombre' => $dpto_data->nombre

								);
							}

						}
						++$contador_hijos;

					}
				}
			}
		}
 		return $op;
	}
  //BOrrar esta funcion
 	private function calculos_acumulados_colaborador($colaboradores = array(), $acumulados = array()) {

    $calculo= 0;
 		$resultado_acumulados = array();
		if(!empty($colaboradores)){
			foreach($colaboradores as $colaborador){

 				$acumulado_total = 0;
				$acumulados_colaborador = array();
				if(!empty($acumulados)){
					foreach($acumulados as $acumulado){

 						if($acumulado['operador'] == 'total_devengado_decimo_mas_proporcional'){
							//Esta operacion no deberia ser asi:
							$variable_operador = $colaborador['total_devengado_decimo'] + ($colaborador['total_devengado']/11);
							//Final de la operacion

						}
						else if($acumulado['operador'] == 'total_devengado_mas_proporcional'){
							//Esta operacion no deberia ser asi:
							$variable_operador = $colaborador['total_devengado'] + ($colaborador['total_devengado']/11);
							//Final de la operacion

						}
						 else{
							//$variable_operador = $colaborador[$acumulado['operador']];
              $variable_operador = isset($colaborador[$acumulado['operador']])?$colaborador[$acumulado['operador']]:0;

						}


						if($acumulado['tipo_calculo_uno'] != ''){
							if($acumulado['tipo_calculo_uno'] == 'Multiplicado por' )
								$calculo = $variable_operador*$acumulado['valor_calculo_uno'];

							elseif($acumulado['tipo_calculo_uno'] == 'Dividido por' )
							$calculo = $variable_operador/$acumulado['valor_calculo_uno'];
						}

						if($acumulado['tipo_calculo_dos'] != ''){
							if($acumulado['tipo_calculo_dos'] == 'Multiplicado por' )
								$calculo = $calculo*$acumulado['valor_calculo_dos'];

							elseif($acumulado['tipo_calculo_dos'] == 'Dividido por' )
							$calculo = $calculo/$acumulado['valor_calculo_dos'];
						}
 						$acumulados_colaborador[] = array(
								"id" 		=>$acumulado['id'],
								"nombre" 	=>$acumulado['nombre'],
								"acumulado" =>$calculo
						);

					}
				}


				$resultado_acumulados[] = array(
						"colaborador_id" => $colaborador['colaborador_id'],
						"acumulado_total" => $acumulado_total,
						"acumulados" => $acumulados_colaborador
				);
			}
		}
		return $resultado_acumulados;
	}

 	private function calculos_deducciones_colaborador($colaboradores = array(), $deducciones= array()) {
  		$resultado_deducciones = array();
		if(!empty($colaboradores)){
			foreach($colaboradores as $colaborador){

 				$deduccion_total = 0;
				$deducciones_colaborador = array();
				if(!empty($deducciones)){
					foreach($deducciones as $deduccion){
						$descuentos_directos =  array();
 						if( $deduccion['nombre']== 'Descuentos directos'){
  							$descuentos_directos = Planilla_deducciones_orm::lista_descuentos_colaborador($colaborador['colaborador_id']);

  							$deducciones_colaborador[$deduccion['nombre']] = array(
  									"id" 		=> $deduccion['id'],
  									"nombre" 	=> $deduccion['nombre'],
  									"descuento" => 0,
  									"saldo" 	=> 0,
  									"descuentos_directos" 	=> $descuentos_directos,
  							);
  						}
  						else{
                                                        if($deduccion['rata_colaborador_tipo'] == 'Porcentual'){
  								$rata =  $deduccion['rata_colaborador']/100;
  							}

  							if( trim(strtolower($deduccion['nombre'])) != 'impuesto sobre la renta' ){
   								if($deduccion['rata_colaborador_tipo'] == 'Porcentual'){
  									$rata =  $deduccion['rata_colaborador']/100;
   									$_deduccion_valor = $colaborador['salario_bruto'] * $rata;
   								}
  								else if($deduccion['rata_colaborador_tipo'] == 'Monto'){
  									$rata =  $deduccion['rata_colaborador'];
  									$_deduccion_valor = $rata;
  								}

   							}else{

                                                            if($colaboradores[0]['tipo_salario'] == 'Hora'){
                                                                 $variable_islr = $colaboradores[0]['salario_mensual_promedio_doce_meses']*13;
                                                            }
                                                            else{

                                                                 $variable_islr = $colaboradores[0]['salario_mensual']*13;
                                                            }
    								$_deduccion_valor = $this->impuesto_sobre_renta($deduccion['limite1'], $deduccion['limite2'], $variable_islr, $rata);
   							}
  							$acumulado_deduccion = Planilla_colaborador_orm::calculando_deduccion_totales($colaborador['colaborador_id'],$deduccion['id'] );

  							$deduccion_total += $_deduccion_valor;
  							$deducciones_colaborador[] = array(
  									"id" 		=> $deduccion['id'],
  									"nombre" 	=> $deduccion['nombre'],
  									"descuento" => $_deduccion_valor,
  									"saldo" 	=> $acumulado_deduccion,
  							);
  						}
 					}
				}


				$resultado_deducciones[] = array(
						"colaborador_id" => $colaborador['colaborador_id'],
						"deduccion_total" => $deduccion_total,
						"deducciones" => $deducciones_colaborador
				);
  			}
		}

		return $resultado_deducciones;
	}


	//$salario_bruto :: Salario de los ultimos 13 meses
	private function impuesto_sobre_renta($limite1 = NULL, $limite2 =NULL,  $salario_bruto_trecemeses= NULL, $rata = NULL) {
  		$monto_excedente =  ($limite2-$limite1)*$rata;

		$impuesto_sr = 0;
		//Formula Para sacar Impuesto Sobre la Renta
		if($salario_bruto_trecemeses > 0 && $salario_bruto_trecemeses <= $limite1){
			$impuesto_sr = 0;
		}
		else if($salario_bruto_trecemeses > ($limite1 +1)  && $salario_bruto_trecemeses <= $limite2){
			$excedente = $salario_bruto_trecemeses - $limite1;


			$impuesto_sr = ($excedente*$rata)/13;
			$impuesto_sr = $impuesto_sr/2;
		}
		else if( $salario_bruto_trecemeses >  ($limite2+1)) {
			$excedente = $salario_bruto_trecemeses - ($limite2+1);
			$impuesto_sr = $monto_excedente + $excedente*0.25;
		}

		return $impuesto_sr;
	}

  //CAMBIOS CODIGO *** //
	private function calculos_salarios_brutos($planilla_id = NULL,  $cantidad_semanas=NULL, $colaborador_id = NULL, $fechas) {


			$planilla_info = Planilla_orm::find( $planilla_id );


			$ingresos = array();
			if($colaborador_id == NULL){
				$colaborador = array();
				$colaboradores 			=  Planilla_colaborador_orm::where('planilla_id','=', $planilla_id)->get();

				if(!empty($colaboradores->toArray())){
					foreach ($colaboradores->toArray() AS $i => $row){

						$colaborador[] = $row['colaborador_id'];
						$planilla_colaborador_id[$row['colaborador_id']] = $row['id'];
					}
					$clause["colaborador"] =  $colaborador;
				}else{
					$clause["colaborador"] =  array(-1);
				}
			}
 			else{
 				$planilla_col 			=  Planilla_colaborador_orm::where('planilla_id','=', $planilla_id)->where('colaborador_id','=', $colaborador_id)->get()->toArray();
   				$planilla_colaborador_id[$colaborador_id] = $planilla_col[0]['id'];
  				$clause["colaborador"] =  array($colaborador_id);
 			}


			//Buscando la informacion de cada colaborador
			$rows = Colaboradores_orm::listar($clause);

			if(!empty($rows->toArray())){
				foreach ($rows->toArray() AS $i => $row){



					$cantidad_horas_total= 0;
					$nombre = Util::verificar_valor($row['nombre']);
					$apellido = Util::verificar_valor($row['apellido']);

 					$dias_laborados	= (strtotime($row['fecha_inicio_labores'])-strtotime(date("Y-m-d")))/86400;
					$dias_laborados = abs($dias_laborados);
					$dias_laborados = floor($dias_laborados);


 					$salario_bruto_anual = Pagadas_colaborador_orm::salario_bruto_anual($row['id'] );
 					$salario_total_devengado = Pagadas_colaborador_orm::salario_total_devengado($row['id'] );
 					$salario_total_devengado_decimo = Pagadas_colaborador_orm::salario_total_devengado_decimo(
 							$row['id'],
 							array(),
 							true
 							);

  					if($row['tipo_salario'] == 'Mensual'){ //MENSUAL

                                            //Martillado
                                            $sumando_salario_colaborador = 0;
                                            if($row['ciclo_id'] == 64 ) //quincenal
                                                $sumando_salario_colaborador = $row['salario_mensual']/2;
                                            if($row['ciclo_id'] == 63 ) //semanal
                                                $sumando_salario_colaborador = $row['salario_mensual']/4;
                                            if($row['ciclo_id'] == 62 ) //mensual
                                                $sumando_salario_colaborador = $row['salario_mensual'];
                                            if($row['ciclo_id'] == 61) //bi-semanal
                                              $sumando_salario_colaborador = ($row['salario_mensual']*12)/26;

 						$calculos = array();
						//$sumando_salario_colaborador = ($row['salario_mensual']/4)*$cantidad_semanas;//Numero total de horas trabajas Fijo
  						$calculos[] = array(
									"detalle" => 'HR',
									"cantidad_horas" =>$row['horas_semanales']*$cantidad_semanas,
									"rata" => 1,
									"calculo" =>$sumando_salario_colaborador
 						);

						$ingresos[] = array(
								"rata_hora" => 1,
								"colaborador_id" =>$row['id'],
								"nombre" =>$nombre." ".$apellido,
								"tipo_salario" => $row['tipo_salario'],
								"salario_mensual" => $row['salario_mensual'],
								"salario_bruto" => $sumando_salario_colaborador,
								"salario_bruto_doce_meses" => $salario_bruto_anual,
								"salario_mensual_promedio_doce_meses" => $salario_bruto_anual/12,
								"salario_devengado_vacacion" =>
								Planilla_vacacion_orm::salario_total_devengado_vacacion(
										$row['id'],
										array(),
										$row['fecha_inicio_labores']
								),
 								"cantidad_horas_total" => $row['horas_semanales']*$cantidad_semanas,
								"total_devengado" => $salario_total_devengado,
								"indemnizacion_proporcional" => $salario_total_devengado*0.0653846,
								"dias_laborados"	=> $dias_laborados,
								"total_devengado_decimo"	=> $salario_total_devengado_decimo,
								"bonificaciones" => 0,
								"lista_ingresos" => $calculos,
								//"descuentos_directos" => $descuentos_directos,

						);

 					}
					else if($row['tipo_salario'] == 'Hora'){ //RATA
						$calculos = array();

 						$clauseIH =  array(
 								'empresa_id' => $this->empresa_id,
								'id_planilla_colaborador' => $planilla_colaborador_id[$row['id']]
						);
						$acumulado_salario_bruto = 0;



						$ingresoHoras = Ingreso_horas_orm::listar($clauseIH, NULL, NULL, NULL, NULL, $fechas);


						$bonificaciones = 0;
						if(!empty($ingresoHoras->toArray())){
							//$salario_por_recargo = 0;
							foreach ($ingresoHoras->toArray() AS $i => $ingreso){

   								 $suma_horas_x_recargo = 0;
								 if(!empty($ingreso['horas_dias'])){
										foreach($ingreso['horas_dias'] as $horas){
											$suma_horas_x_recargo += $horas['horas'];
										}
									}
  								$salario_por_recargo = $suma_horas_x_recargo*($ingreso['recargo']['porcentaje_hora']*$row['rata_hora']);
                                                                 if(!empty($ingreso['beneficio'])){
                                                                     $salario_por_recargo = $salario_por_recargo + ($salario_por_recargo*($ingreso['beneficio']['modificador_actual']/100));
                                                                }

 								if($ingreso['recargo']['nombre']!='HR'){
 									$bonificaciones  = $salario_por_recargo;
 								}
								$acumulado_salario_bruto += $salario_por_recargo;
								$cantidad_horas_total += $suma_horas_x_recargo;
								$rata = $ingreso['recargo']['porcentaje_hora']*$row['rata_hora'];
								$calculos[] = array(
										"detalle" => $ingreso['recargo']['nombre'],
										"cantidad_horas" =>$suma_horas_x_recargo,
										"rata" => $rata,
										"calculo" =>$salario_por_recargo,
										//"bonificaciones" =>$bonificaciones
 								);


							}
						}

						//$bonificaciones = $acumulado_salario_bruto -($cantidad_horas_total*$row['rata_hora']);

						$ingresos[] = array(
								"rata_hora" => $row['rata_hora'],
                                                                "tipo_salario" => $row['tipo_salario'],
								"colaborador_id" =>$row['id'],
								"nombre" =>$nombre." ".$apellido,
								"salario_bruto" => $acumulado_salario_bruto,
                                                                "salario_mensual" => $acumulado_salario_bruto,
								"salario_bruto_doce_meses" => $salario_bruto_anual,
								"salario_mensual_promedio_doce_meses" => $salario_bruto_anual/12,
								"salario_devengado_vacacion" =>
									Planilla_vacacion_orm::salario_total_devengado_vacacion(
										$row['id'],
										array(),
										$row['fecha_inicio_labores']
									),
								"cantidad_horas_total" => $cantidad_horas_total,
								"total_devengado" => $salario_total_devengado,
								"indemnizacion_proporcional" => $salario_total_devengado*0.0653846,
								"dias_laborados"	=> $dias_laborados,
								"total_devengado_decimo"	=> $salario_total_devengado_decimo,
								"bonificaciones" => $bonificaciones,
								"lista_ingresos" => $calculos,
								//"descuentos_directos" => $descuentos_directos
 						);


					}
 					$i++;
				}
			}
   		return $ingresos;
	}

	private function calculos_salarios_brutos_especiales($planilla_id = NULL,$cantidad_semanas=NULL, $tipo_accion=NULL ) {


 		$ingresos =    array();

 		$planilla_info = Planilla_orm::find( $planilla_id );

 		if($tipo_accion == 'vacaciones'){
 			$vacacion = $lista_vacaciones = array();
			$vacacion_calculo = 0;

			$vacaciones 	=  Planilla_vacacion_orm::where('planilla_id','=', $planilla_id)->get();




				if(!empty($vacaciones->toArray())){
					foreach ($vacaciones->toArray() AS $i => $row){


						$vacacion[] = $row['vacacion_id'];
						$planilla_vacacion_id[$row['vacacion_id']] = $row['id'];
					}
 				}
 				$lista_vacaciones = Vacaciones_orm::whereIn('id',  $vacacion )->get(); //Obtengo la lista de colaboradores solo de esos subcentros

				$i = 0;
				if(!empty($lista_vacaciones->toArray())){
					foreach ($lista_vacaciones->toArray() AS $i => $row){

   						$salario_bruto_anual 		= Pagadas_colaborador_orm::salario_bruto_anual($row['colaborador_id'] );
   						$salario_total_devengado 	= Pagadas_colaborador_orm::salario_total_devengado($row['colaborador_id'] );
   						$total_devengado_vacaciones = Planilla_vacacion_orm::salario_total_devengado_vacacion(
								   						$row['colaborador_id'],
								   						array("inicial"=>$row['fecha_desde'],'final'=>$row['fecha_hasta']),
								   						NULL
						);
     					$ingresos[] = array(
								"rata_hora" =>0,
								"colaborador_id" =>$row['colaborador_id'],
								"salario_bruto" => 0,
								"salario_bruto_doce_meses" => $salario_bruto_anual,
								"salario_mensual_promedio_doce_meses" => $salario_bruto_anual/12,
								"salario_devengado_vacacion" =>  $total_devengado_vacaciones,
  								"salario_devengado_vacacion_entre11" => $total_devengado_vacaciones/11,
								"cantidad_horas_total" => 0,
								"total_devengado" => $salario_total_devengado,
								"indemnizacion_proporcional" => $salario_total_devengado*0.0653846,
								"dias_laborados"	=> 0,
								"total_devengado_decimo"	=> 0,
								"bonificaciones" => 0,
								"lista_ingresos" => array(),
  						);
						$i++;
					}

				}



		}
		else if($tipo_accion == 'liquidaciones'){
 			$liquidacion = $lista_liquidaciones = array();

			$liquidaciones 	=  Planilla_liquidacion_orm::where('planilla_id','=', $planilla_id)->get();

				if(!empty($liquidaciones->toArray())){
					foreach ($liquidaciones->toArray() AS $i => $row){

						$liquidacion[] = $row['liquidacion_id'];
						$planilla_liquidacion_id[$row['liquidacion_id']] = $row['id'];
					}
 				}

				$lista_liquidaciones = Liquidaciones_orm::whereIn('id',  $liquidacion )->get(); //Obtengo la lista de colaboradores solo de esos subcentros

				$i = 0;
				if(!empty($lista_liquidaciones->toArray())){
					foreach ($lista_liquidaciones->toArray() AS $i => $row){


   						$salario_total_devengado_decimo = Pagadas_colaborador_orm::salario_total_devengado_decimo($row['colaborador_id'],
  								array(	'fecha_rango1'=>$planilla_info->rango_fecha1,
  										'fecha_rango2'=>$planilla_info->rango_fecha2
  								),
   								false
  						);

   						$salario_bruto_anual = Pagadas_colaborador_orm::salario_bruto_anual($row['colaborador_id'] );

 					    $calculos[] = array(
 					    		"liquidacion_id" => $row['id']
 					    );

 						$ingresos[] = array(
 								"liquidacion_id" => $row['id'],
 								"colaborador_id" => $row['colaborador_id'],
 								"salario_promedio_anual" => 0, 	//No se usa en este caso
 								"salario_promedio_mensual" =>0, //No se usa en este caso
 								"total_devengado" => Pagadas_colaborador_orm::salario_total_devengado($row['colaborador_id']),
 								"dias_laborados" => 0,//No se usa en este caso
 								"salario_bruto" => 0, //Operadores, Empieza 0
 								"total_devengado_decimo" => $salario_total_devengado_decimo,
 								"salario_bruto_trecemeses" => $salario_bruto_anual, //Esta cifra se usa para el impuesto sobre la renta
 								"lista_liquidaciones" => $calculos
 						);

						$i++;
					}

 				}
		}
		else if($tipo_accion == 'licencias'){


			$licencia =  $lista_licencias = array();

			$licencias 	=  Planilla_licencia_orm::where('planilla_id','=', $planilla_id)->get();

			if(!empty($licencias->toArray())){
				foreach ($licencias->toArray() AS $i => $row){

					$licencia[] = $row['licencia_id'];
					$planilla_licencia_id[$row['licencia_id']] = $row['id'];
				}
			}

			$lista_licencias = Licencias_orm::whereIn('id',  $licencia )->get(); //Obtengo la lista de colaboradores solo de esos subcentros

			$i = 0;
			if(!empty($lista_licencias->toArray())){
				foreach ($lista_licencias->toArray() AS $i => $row){

  					$salario_total_devengado_decimo = Pagadas_colaborador_orm::salario_total_devengado_decimo($row['colaborador_id'],
							array(),
 							true
					);


 					$salario_bruto_anual = Pagadas_colaborador_orm::salario_bruto_anual($row['colaborador_id'] );

					$calculos[] = array(
							"liquidacion_id" => $row['id']
					);

					$ingresos[] = array(
							"liquidacion_id" => $row['id'],
							"colaborador_id" => $row['colaborador_id'],
							"salario_promedio_anual" => 0, 	//No se usa en este caso
							"salario_promedio_mensual" =>0, //No se usa en este caso
							"total_devengado" => Pagadas_colaborador_orm::salario_total_devengado($row['colaborador_id']),
							"dias_laborados" => 0,//No se usa en este caso
							"salario_bruto" => 0, //Operadores, Empieza 0
							"total_devengado_decimo" => $salario_total_devengado_decimo,
							"salario_bruto_trecemeses" => $salario_bruto_anual, //Esta cifra se usa para el impuesto sobre la renta
							"lista_liquidaciones" => $calculos
					);

					$i++;
				}

			}
		}
 		else if($tipo_accion == 'decimo'){
  			$planilla_colaborador_id=  array();
 			$colaboradores 	=  Planilla_colaborador_orm::where('planilla_id','=', $planilla_id)->get();

			if(!empty($colaboradores->toArray())){
				foreach ($colaboradores->toArray() AS $i => $row){

					$colaboradores[] = $row['colaborador_id'];
					$planilla_colaborador_id[$row['colaborador_id']] = $row['id'];

					$salario_total_devengado_decimo = Pagadas_colaborador_orm::salario_total_devengado_decimo($row['colaborador_id'],
							array(	'fecha_rango1'=>$planilla_info->rango_fecha1,
									'fecha_rango2'=>$planilla_info->rango_fecha2
							),
							false
					);

  					$salario_bruto_anual = Pagadas_colaborador_orm::salario_bruto_anual($row['colaborador_id'] );
					$ingresos[] = array(
 							"planilla_colaborador_id" => $row['id'],
							"colaborador_id" => $row['colaborador_id'],
							"salario_promedio_anual" => 0, 	//No se usa en este caso
							"salario_promedio_mensual" =>0, //No se usa en este caso
							"total_devengado" => Pagadas_colaborador_orm::salario_total_devengado($row['colaborador_id']),
							"dias_laborados" => 0,//No se usa en este caso
							"salario_bruto" => 0, //Operadores, Empieza 0
							"total_devengado_decimo" => $salario_total_devengado_decimo,
							"total_devengado_decimo_8.33" => $salario_total_devengado_decimo*0.08333,
							"salario_bruto_trecemeses" => $salario_bruto_anual, //Esta cifra se usa para el impuesto sobre la renta
							"salario_devengado_vacacion" => 0, //Esta cifra se usa para el impuesto sobre la renta
                                                        "salario_mensual_promedio_doce_meses" => $salario_bruto_anual/12,
                                                        "salario_bruto_doce_meses" => $salario_bruto_anual,
                                                        "indemnizacion_proporcional" =>  Pagadas_colaborador_orm::salario_total_devengado($row['colaborador_id'])*0.0653846,
 					);
 					$i++;
				}
			}
 		}
		return $ingresos;
	}



 	public function ajax_cargar_numero_secuencial() {
		$uuid_empresa = $this->session->userdata('uuid_empresa');
		$empresa = Empresa_orm::findByUuid($uuid_empresa);
		$codigo = 0;
		$response = new stdClass();
		$response = Planilla_orm::where("empresa_id", "=",$this->empresa_id)
		->orderBy('id', 'DESC')
		->limit(1)
		->get();
		if(!empty($response->toArray())){
			$codigo = (int)$response[0]['secuencial'] + 1;
		}
		else
			$codigo = 1;

		return $codigo;
	}


	public function operadores_lista($colaborador_id = NULL, $planilla_id = NULL) {

		$planilla_info = Planilla_orm::find( $planilla_id );
		$colaborador_info = Colaboradores_orm::find( $colaborador_id );


		$nombre = Util::verificar_valor($colaborador_info->nombre);
		$apellido = Util::verificar_valor($colaborador_info->apellido);

		$dias_laborados	= (strtotime($colaborador_info->fecha_inicio_labores)-strtotime(date("Y-m-d")))/86400;
		$dias_laborados = abs($dias_laborados);
		$dias_laborados = floor($dias_laborados);

		$salario_bruto_anual = Pagadas_colaborador_orm::salario_bruto_anual($colaborador_id );
		$salario_total_devengado = Pagadas_colaborador_orm::salario_total_devengado($colaborador_id);

		$salario_total_devengado_decimo = Pagadas_colaborador_orm::salario_total_devengado_decimo($colaborador_id,
			array(),
			true
		);
 		$operadores[] = array(
				"colaborador_id" =>$colaborador_id,
				"tipo_salario" =>$colaborador_info->tipo_salario,
				"salario_bruto" =>($colaborador_info->tipo_salario == 'Mensual')?$colaborador_info->salario_mensual:0,
				"salario_mensual" =>isset($colaborador_info->salario_mensual)?$colaborador_info->salario_mensual:0,
				"nombre" =>$nombre." ".$apellido,
				"salario_bruto_doce_meses" => $salario_bruto_anual,
				"salario_mensual_promedio_doce_meses" => $salario_bruto_anual/12,
				"salario_devengado_vacacion" =>
						Planilla_vacacion_orm::salario_total_devengado_vacacion(
								$colaborador_id,
								array(),
								$colaborador_info->fecha_inicio_labores
								),
 				"total_devengado" => $salario_total_devengado,
				"indemnizacion_proporcional" => $salario_total_devengado*0.0653846,
				"dias_laborados"	=> $dias_laborados,
				"total_devengado_decimo"	=> $salario_total_devengado_decimo,
		);

		return $operadores;
	}

	private function _js() {
		$this->assets->agregar_js(array(
      		 	'public/assets/js/default/jquery-ui.min.js',
      			'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
      			'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
      			'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
      			'public/assets/js/plugins/jquery/combodate/momentjs.js',
      			'public/assets/js/plugins/jquery/chosen.jquery.min.js',
      			'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
      			'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
      	 	   'public/assets/js/moment-with-locales-290.js',
        		 'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
      		 'public/assets/js/plugins/bootstrap/daterangepicker.js',
      		//	'public/assets/js/default/tabla-dinamica.jquery.js',
      			'public/assets/js/plugins/jquery/switchery.min.js',
      			'public/assets/js/default/formulario.js',
      			'public/assets/js/plugins/jquery/tree-multiselect/jquery.tree-multiselect.js',
      			'public/assets/js/plugins/jquery/multiselect-master/multiselect.js',
      			'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
      			'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
      			'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/default/toast.controller.js',
            'public/assets/js/plugins/bootstrap/select2/select2.min.js',
            'public/assets/js/plugins/bootstrap/select2/es.js',
            'public/assets/js/default/vue/directives/select2.js'
      	));
	}



	private function _css() {
		$this->assets->agregar_css(array(
				'public/assets/css/default/ui/base/jquery-ui.css',
				'public/assets/css/default/ui/base/jquery-ui.theme.css',
				'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
				'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
				'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
				'public/assets/css/plugins/jquery/chosen/chosen.min.css',
				'public/assets/css/plugins/jquery/tree-multiselect/jquery.tree-multiselect.min.css',
				'public/assets/css/plugins/jquery/multiselect-master/style.css',
				'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
				'public/assets/css/plugins/jquery/switchery.min.css',
        'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
        'public/assets/css/plugins/bootstrap/select2.min.css',
		));


	}


	public function ocultotablaplanilla() {
		//If ajax request
		$this->assets->agregar_js(array(
				'public/assets/js/modules/planilla/tabla-planilla.js'
		));

		$this->load->view('tabla-planilla');
	}

	public function ocultotabladecimo() {
		//If ajax request
		$this->assets->agregar_js(array(
				'public/assets/js/modules/planilla/tabla-decimo.js'
		));

		$this->load->view('tabla-decimo');
	}

	public function ocultotablacalculos($info = NULL) {
		$this->assets->agregar_js(array(
				'public/assets/js/modules/planilla/tabla-calculos.js'
		));

		$this->load->view('tabla-calculos');

	}
	public function ocultotablacalculosliquidacion($info = NULL) {
		$this->assets->agregar_js(array(
				'public/assets/js/modules/planilla/tabla-calculos-liquidacion.js'
		));

		$this->load->view('tabla-calculos-liquidacion');

	}

	public function ocultotablaacumulados($info = NULL) {
		$this->assets->agregar_js(array(
				'public/assets/js/modules/planilla/tabla-acumulados.js'
		));

		$this->load->view('tabla-acumulados');

	}

	//Tabla que se forma al entrar en los detalles de la planilla
	public function ocultotablacolaborador($info = NULL) {
		$this->assets->agregar_var_js(array(
				"planilla_id" =>$info['id'],
		));

		$this->assets->agregar_js(array(
				'public/assets/js/modules/planilla/tabla-colaborador.js'
		));

		$this->load->view('tabla-ver-colaborador');
	}

	public function ocultotablaingresosliquidacion() {
		$this->assets->agregar_js(array(
				'public/assets/js/modules/planilla/tabla-ingresos-liquidacion.js'
		));

		$this->load->view('tabla-ingresos-liquidacion');

	}
	public function ocultotablaingresos($info = NULL) {
		$this->assets->agregar_js(array(
				'public/assets/js/modules/planilla/tabla-ingresos.js'
		));

		$this->load->view('tabla-ingresos');

	}
	public function ocultotablaingresosdecimo($info = NULL) {
		$this->assets->agregar_js(array(
				'public/assets/js/modules/planilla/tabla-ingresosdecimo.js'
		));

		$this->load->view('tabla-ingresosdecimo');

	}

	public function ocultotabladeducciones($info = NULL) {

 		$this->assets->agregar_js(array(
				'public/assets/js/modules/planilla/tabla-deducciones.js'
		));

		$this->load->view('tabla-deducciones');

	}
	public function ocultotabladeduccionesliquidacion($info = NULL) {
		$this->assets->agregar_js(array(
				'public/assets/js/modules/planilla/tabla-deduccionesliquidaciones.js'
		));

		$this->load->view('tabla-deducccionesliquidaciones');

	}


	public function ocultotabladescuentosdirectosliquidaciones($info = NULL) {
		$this->assets->agregar_js(array(
				'public/assets/js/modules/planilla/tabla-descuentosdirectos-liquidaciones.js'
		));

		$this->load->view('tabla-descuentosdirectos-liquidaciones');
	}
	public function ocultotabladescuentosdirectos($info = NULL) {
		$this->assets->agregar_js(array(
				'public/assets/js/modules/planilla/tabla-descuentosdirectos.js'
		));

		$this->load->view('tabla-descuentosdirectos');

	}
 	//Funcion Nueva ***
	public function reporte_liquidacion($colaborador_uuid = NULL) {


	   if(!$this->auth->has_permission('acceso', 'planilla/reporte_liquidacion/(:any)')) {
			redirect(base_url('/'));
		}
		$data =[];
		$uuid_planilla =  $this->session->userdata("uuid_planilla");
 		if($colaborador_uuid!=NULL)
		{
			$uuid = explode("~", $colaborador_uuid);
 			$colaborador_uuid = $uuid[0];
			$id_liquidacion =  $uuid[1];
 			$planilla = $this->planillaRepository->findByUuid($uuid_planilla);

   			$planilla->load([
  					"liquidaciones"  => function ($query) use ($id_liquidacion)  {
  				 		$query->where('liquidacion_id', $id_liquidacion);},
 					"liquidaciones.colaborador.forma_pago",
 					"liquidaciones.colaborador.descuentos_directos.acreedor",
 					"liquidaciones.colaborador.colaboradores_contratos",
 					"liquidaciones.colaborador.planilla_activa.colaboradores_planilla.ingreso_horas.recargo",
 					"liquidaciones.colaborador.planilla_activa.colaboradores_planilla.ingreso_horas.beneficio",
 					"liquidaciones.colaborador.planilla_activa.colaboradores_planilla.ingreso_horas.dias",
 					"liquidaciones.contrato",
 					"liquidaciones.colaborador.salarios_devengados_ultimos_cinco_anos" => function ($query) use ($id_liquidacion)  {
   				 		$query->join('col_colaboradores_contrato', 'pln_pagadas_colaborador.contrato_id', '=', 'col_colaboradores_contrato.id');}]
 			)->get();


 			$configuracion = $this->configuracionPlanillaLiquidacionRepository->findByTipoPago($planilla['liquidaciones'][0]->tipo_liquidacion_id);
	 		$configuracion->load("pagos.tipoPago", "pagos.deducciones.deduccion_info");

  	 		$calculos_globales = $this->liquidacionPagadaRepository->reporte_colaborador($planilla, $configuracion);

   	 		$tabla = $this->liquidacionPagadaRepository->coleccion_tablas($calculos_globales);

 	 		$tablas_principales  = array(
					'tabla_ingresos' 	=> $tabla['ingresos'],
					'tabla_deducciones' 	=> $tabla['deducciones'],
					'tabla_descuentos' 	=> $tabla['descuentos'],
					'calculos' => $tabla['calculos'],

			);
 			$this->session->set_userdata($tablas_principales);
 		}
   		$data['info'] = $planilla->liquidaciones[0]->colaborador;
   		$this->_css();
		$this->_js();

		$this->assets->agregar_var_js(array(
				 "planilla_id" => 14,
				"colaborador_id" => 1
 		));

    $breadcrumb = array(
        "titulo" => '<i class="fa fa-institution"></i> Reporte de colaborador ',
        "filtro" => false,
        "menu" => array(
            "nombre" => 'Acci&oacute;n',
            "url"	 => '#',
            "opciones" => array()
        ),
        "ruta" => array(
              0 => array(
                  "nombre" => "Nómina",
                  "activo" => false
              ),
              1 => array(
                  "nombre" => "Planillas",
                  "activo" => false,
                  "url" => 'planilla/listar'
              ),
             2 => array(
                "nombre" => 'Detalle',
                "activo" => true,
                "url" => 'planilla/ver/'.$uuid_planilla
            ),
            3 => array(
               "nombre" => '<b>Reporte</b>',
               "activo" => true,
           )
        ),
    );
 		$breadcrumb["menu"]["opciones"]["#ExportarBtnComision"] = "Exportar";
 		$breadcrumb["menu"]["opciones"]["#ExportarBtnImprimirTalonario"] = "Imprimir PDF";

		$this->template->agregar_breadcrumb($breadcrumb);
	 	$this->template->agregar_contenido($data);
	 	$this->template->visualizar();
  	}

    //***

	public function ver_reporte2($colaborador_uuid = NULL) {

		if($colaborador_uuid!=NULL){ //Ver Detalles

			$uuid = explode("~", $colaborador_uuid);
			$colaborador_uuid = isset($uuid[0])?$uuid[0]:'';
			$planilla_id  = isset($uuid[1])?$uuid[1]:'0';
		}else{
			if(!$this->auth->has_permission('acceso', 'planilla/crear')){
				redirect(base_url('/'));
			}
		}

		$data = array();
		$colaborador_info = array();
		$colaborador_id = NULL;
		if($colaborador_uuid!=NULL)
		{
  		 $colaborador_info = Colaboradores_orm::where(Capsule::raw("HEX(uuid_colaborador)"), "=", $colaborador_uuid)
 			->get()->toArray();
      $colaborador_id = $colaborador_info[0]['id'];

 		$planilla_info = $this->planillaRepository->find($planilla_id);
                $fecha_inicio = $planilla_info['attributes']['rango_fecha1'];
                $fecha_final = $planilla_info['attributes']['rango_fecha2'];


     $planilla_info->load(['colaboradores_planilla'
          => function ($query) use ($colaborador_id)  {
            $query->where('pln_planilla_colaborador.colaborador_id', '=', $colaborador_id);
          },
          'colaboradores_planilla.ingreso_horas.recargo',
          'colaboradores_planilla.ingreso_horas.beneficio',
          'colaboradores_planilla.ingreso_horas.dias',
          'colaboradores_planilla.colaborador',
          'colaboradores_planilla.colaborador.descuentos_directos' => function ($query) use ($fecha_inicio, $fecha_final) {
                $query->where('fecha_inicio', '<', $fecha_final);
          },
          'colaboradores_planilla.colaborador.descuentos_directos.acreedor',
          'colaboradores_planilla.colaborador.colaboradores_contratos',
          'colaboradores_planilla.colaborador.base_acumulados',
          'colaboradores_planilla.colaborador.salarios_devengados' => function ($query)  {
                $query->join('col_colaboradores_contrato', 'col_colaboradores_contrato.id', '=', 'pln_pagadas_colaborador.contrato_id');
           },
          'deducciones2.deduccion_info',
          'acumulados2.acumulado_info.formula',
        ]
    );

      $data['info'] = isset($planilla_info['colaboradores_planilla'][0]['colaborador'])?$planilla_info['colaboradores_planilla'][0]['colaborador']:array();


      $calculos_globales = $this->planillaRepository->reporte_colaborador($planilla_info);

      $tabla = $this->planillaRepository->coleccion_tablas($calculos_globales); //Solo para ver detalles

    			$tablas_principales  = array(
					'tabla_ingresos' => $tabla['ingresos'],
					'tabla_deducciones' => $tabla['deducciones'],
					'tabla_acumulados' => $tabla['acumulados'],
					'tabla_descuentos_directos' => $tabla['descuentos'],
				 	'salario_mensual_promedio' =>  $tabla['calculos']['salario_mensual_promedio']['monto'],
					'salario_anual_promedio' => $tabla['calculos']['salario_anual_promedio']['monto'],
					'total_devengado' =>  $tabla['calculos']['total_devengado']['monto'],
					'indemnizacion_proporcional' => $tabla['calculos']['indemnizacion_proporcional']['monto']
			);

			$this->session->set_userdata($tablas_principales);

		}

		$this->_css();
		$this->_js();

		$this->assets->agregar_var_js(array(
				"planilla_id" => isset($planilla_id)?$planilla_id:'0',
				"colaborador_id" => isset($colaborador_info[0]['id'])?$colaborador_info[0]['id']:'',
				"cantidad_semanas" => 2
				//"cantidad_semanas" => $cantidad_semanas
		));
 		$breadcrumb = array(
				"titulo" => '<i class="fa fa-institution"></i> '.$planilla_info->codigo.' - Reporte de colaborador',
				"filtro" => false,
				"menu" => array(
						"nombre" => 'Acci&oacute;n',
						"url"	 => '#',
						"opciones" => array()
				),
        "ruta" => array(
              0 => array(
                  "nombre" => "Nómina",
                  "activo" => false
              ),
              1 => array(
                  "nombre" => "Planillas",
                  "activo" => false,
                  "url" => 'planilla/listar'
              ),
             2 => array(
                "nombre" => 'Detalle',
                "activo" => true,
                "url" => 'planilla/ver/'.$planilla_info->uuid_planilla
            ),
            3 => array(
               "nombre" => '<b>Reporte</b>',
               "activo" => true,
           )
        ),
		);
		/*if ($this->auth->has_permission('listar__exportarPlanilla', 'planilla/listar')){*/
		$breadcrumb["menu"]["opciones"]["#ExportarExcelBtn2"] = "Exportar";
		//$breadcrumb["menu"]["opciones"]["#ExportarBtnImprimir"] = "Imprimir";
	//	$breadcrumb["menu"]["opciones"]["#ExportarBtnImprimirTalonario"] = "Imprimir PDF";
		/*}*/

		$uuid_empresa = $this->session->userdata('uuid_empresa');
		$empresa = Empresa_orm::findByUuid($uuid_empresa);
		$this->template->agregar_breadcrumb($breadcrumb);
		$this->template->agregar_contenido($data);
		$this->template->visualizar();

	}
  //Nueva funcion separa de la regular
  public function ver_reporte_vacaciones($colaborador_uuid = NULL) {
     if($colaborador_uuid!=NULL){ //Ver Detalles

      $uuid = explode("~", $colaborador_uuid);
      $colaborador_uuid = isset($uuid[0])?$uuid[0]:'';
      $accion_personal_id  = isset($uuid[1])?$uuid[1]:'0';
    }else{
      if(!$this->auth->has_permission('acceso', 'planilla/crear')){
        redirect(base_url('/'));
      }
    }

    $uuid_planilla =  $this->session->userdata("uuid_planilla");

     $data = array();
    $colaborador_info = array();
    $colaborador_id = NULL;
    if($colaborador_uuid!=NULL)
    {
       $colaborador_info = Colaboradores_orm::where(Capsule::raw("HEX(uuid_colaborador)"), "=", $colaborador_uuid)
      ->get()->toArray();
       $colaborador_id = $colaborador_info[0]['id'];
       $planilla_info = $this->planillaRepository->findByUuid($uuid_planilla);

       $data['info'] = isset($planilla_info->vacaciones2->first()->colaborador)?$planilla_info->vacaciones2->first()->colaborador:array();
       $vacacion_info  = isset($planilla_info->vacaciones2)?$planilla_info->vacaciones2->first():array();

       $fecha_desde = $vacacion_info->fecha_desde;
      $fecha_hasta = $vacacion_info->fecha_hasta;

     $planilla_info->load(['vacaciones2'
          => function ($query) use ($accion_personal_id)  {
            $query->where('pln_planilla_vacacion.vacacion_id', '=', $accion_personal_id);
          },
            'vacaciones2.colaborador.colaboradores_contratos',
           'vacaciones2.colaborador.colaboradores_contratos.salarios_devengados_contrato_vacaciones' => function ($query) use ($fecha_desde, $fecha_hasta)  {
                 $query->where('fecha_cierre_planilla','>=',$fecha_desde)
                 ->where('fecha_cierre_planilla','<=',$fecha_hasta);
            },
           'vacaciones2.colaborador.descuentos_directos' => function ($query)  {
                 $query->where('fecha_inicio', '<', date("Y-m-d"));
           },
          'deducciones2.deduccion_info',
          'acumulados2.acumulado_info.formula',
        ]
    );


      $calculos_globales = $this->VacacionRepository->reporte_colaborador($planilla_info);

         $tabla = $this->VacacionRepository->coleccion_tablas($calculos_globales); //Solo para ver detalles

           $tablas_principales  = array(
          'tabla_ingresos' => $tabla['ingresos'],
          'tabla_deducciones' => $tabla['deducciones'],
          'tabla_acumulados' => $tabla['acumulados'],
          'tabla_descuentos_directos' => $tabla['descuentos'],
          'salario_mensual_promedio' =>  0,
          'salario_anual_promedio' => 0,
          'total_devengado' =>  0,
          'indemnizacion_proporcional' => 0
      );
       $this->session->set_userdata($tablas_principales);

    }

    $this->_css();
    $this->_js();

    $this->assets->agregar_var_js(array(
        "planilla_id" => isset($planilla_info->id)?$planilla_info->id:'0',
        "colaborador_id" => isset($colaborador_info[0]['id'])?$colaborador_info[0]['id']:''
    ));
    $breadcrumb = array(
        "titulo" => '<i class="fa fa-institution"></i> Reporte de colaborador ',
        "filtro" => false,
        "menu" => array(
            "nombre" => 'Acci&oacute;n',
            "url"	 => '#',
            "opciones" => array()
        ),
        "ruta" => array(
              0 => array(
                  "nombre" => "Nómina",
                  "activo" => false
              ),
              1 => array(
                  "nombre" => "Planillas",
                  "activo" => false,
                  "url" => 'planilla/listar'
              ),
             2 => array(
                "nombre" => 'Detalle',
                "activo" => true,
                "url" => 'planilla/ver/'.$planilla_info->uuid_planilla
            ),
            3 => array(
               "nombre" => '<b>Reporte</b>',
               "activo" => true,
           )
        ),
    );
    /*if ($this->auth->has_permission('listar__exportarPlanilla', 'planilla/listar')){*/
    $breadcrumb["menu"]["opciones"]["#ExportarExcelBtn2"] = "Exportar";
    //$breadcrumb["menu"]["opciones"]["#ExportarBtnImprimir"] = "Imprimir";
    $breadcrumb["menu"]["opciones"]["#ExportarBtnImprimirTalonario"] = "Imprimir PDF";
    /*}*/

    $uuid_empresa = $this->session->userdata('uuid_empresa');
    $empresa = Empresa_orm::findByUuid($uuid_empresa);
    $this->template->agregar_breadcrumb($breadcrumb);
    $this->template->agregar_contenido($data);
    $this->template->visualizar();

  }
	public function ver_reporte_decimo($colaborador_uuid = NULL) {

    	if($colaborador_uuid!=NULL){ //Ver Detalles
      			if(!$this->auth->has_permission('acceso', 'planilla/ver/(:any)')){
      				redirect(base_url('/'));
      			}
      			$uuid = explode("~", $colaborador_uuid);
      			$colaborador_uuid = isset($uuid[0])?$uuid[0]:'';
      			$planilla_id  = isset($uuid[1])?$uuid[1]:'0';
		}else{
			if(!$this->auth->has_permission('acceso', 'planilla/crear')){
				redirect(base_url('/'));
			}
		}

		$total_salario = $salario_bruto = 0;
		$colaborador_info = $data = $tabla_ingresos =  array();
		if($colaborador_uuid!=NULL)
		{
			$colaborador_info = Colaboradores_orm::with('cargo','centro_contable','forma_pago')->where(Capsule::raw("HEX(uuid_colaborador)"), "=", $colaborador_uuid)->get()->toArray();

      $colaborador_id = $colaborador_info[0]['id'];
			$data['info'] = isset($colaborador_info[0])?$colaborador_info[0]:array();

			//$planilla_info = Planilla_orm::find($planilla_id);
	    $planilla_info = $this->planillaRepository->find($planilla_id);
			//$fecha1 =  date("m/d/Y", strtotime($planilla_info->rango_fecha1));
			//$fecha2 =  date("m/d/Y", strtotime($planilla_info->rango_fecha2));
			//$fecha_planilla = array("rango_fecha1"=>$planilla_info->rango_fecha1,"rango_fecha2"=>$planilla_info->rango_fecha2);
 			//$lista_ingresos = Pagadas_colaborador_orm::salarios_ganados_decimo($colaborador_info[0]['id'], $fecha_planilla);
			/*if(!empty($lista_ingresos)){
				foreach($lista_ingresos as $ingreso){

					$tabla_ingresos[] = array(
							"fecha_pago" => date("F j, Y", strtotime($ingreso['fecha_final'])),
							"ingreso"   => $ingreso['salario_bruto']
					);

					$total_salario += $ingreso['salario_bruto'];
				}
        $salario_bruto = $total_salario*0.08333;

				$total_salario = $ingreso['salario_bruto']*0.08333;

				$info_general[] = array("salario_bruto"=>$total_salario, 'colaborador_id'=>$colaborador_info[0]['id']);
			}*/

			//$datos_generales = $this->operadores_lista( $colaborador_info[0]['id'] , $planilla_id );
			//$datos_generales[0]['salario_bruto'] = $total_salario;
//Vacaciones
/*   $planilla_info->load(['vacaciones2'
        => function ($query) use ($accion_personal_id)  {
          $query->where('pln_planilla_vacacion.vacacion_id', '=', $accion_personal_id);
        },
          'vacaciones2.colaborador.colaboradores_contratos',
         'vacaciones2.colaborador.colaboradores_contratos.salarios_devengados_contrato_vacaciones' => function ($query) use ($fecha_desde, $fecha_hasta)  {
               $query->where('fecha_cierre_planilla','>=',$fecha_desde)
               ->where('fecha_cierre_planilla','<=',$fecha_hasta);
          },
         'vacaciones2.colaborador.descuentos_directos' => function ($query)  {
               $query->where('fecha_inicio', '<', date("Y-m-d"));
         },
        'deducciones2.deduccion_info',
        'acumulados2.acumulado_info.formula',
      ]
  );
*/
            $fecha_desde = $planilla_info->rango_fecha1;
            $fecha_hasta = $planilla_info->rango_fecha2;
            $planilla_info->load(['colaboradores_planilla'
                => function ($query) use ($colaborador_id)  {
                  $query->where('pln_planilla_colaborador.colaborador_id', '=', $colaborador_id);
                },
                'colaboradores_planilla.colaborador',
                'colaboradores_planilla.colaborador.descuentos_directos' => function ($query){
                      $query->where('fecha_inicio', '>', date("Y-m-d"));
                },
                'colaboradores_planilla.colaborador.descuentos_directos.acreedor',
                'colaboradores_planilla.colaborador.colaboradores_contratos',
                'colaboradores_planilla.colaborador.colaboradores_contratos.salarios_devengados_contrato_decimo' => function ($query) use ($fecha_desde, $fecha_hasta)  {
                      $query->where('fecha_cierre_planilla','>=',$fecha_desde)
                      ->where('fecha_cierre_planilla','<=',$fecha_hasta);
                 },
                'deducciones2.deduccion_info',
                'acumulados2.acumulado_info.formula'
              ]
          );
              $data['info'] = isset($planilla_info['colaboradores_planilla'][0]['colaborador'])?$planilla_info['colaboradores_planilla'][0]['colaborador']:array();


       //$salario_bruto = array('salario_bruto'=>$salario_bruto);


            $calculos_globales = $this->planillaRepository->reporte_colaborador($planilla_info);


              $tabla = $this->planillaRepository->coleccion_tablas($calculos_globales); //Solo para ver detalles

             $tablas_principales  = array(
                'tabla_ingresos' => $tabla['ingresos'],
                'tabla_deducciones' => $tabla['deducciones'],
                'tabla_acumulados' => $tabla['acumulados'],
                'tabla_descuentos_directos' => $tabla['descuentos'],
                'salario_mensual_promedio' =>  $tabla['calculos']['salario_mensual_promedio']['monto'],
                'salario_anual_promedio' => $tabla['calculos']['salario_anual_promedio']['monto'],
                'total_devengado' =>  $tabla['calculos']['total_devengado']['monto'],
                'indemnizacion_proporcional' => $tabla['calculos']['indemnizacion_proporcional']['monto']
            );
            $this->session->set_userdata($tablas_principales);

		}
		$this->_css();
		$this->_js();

		$this->assets->agregar_var_js(array(
				"planilla_id" => isset($planilla_id)?$planilla_id:'0',
				"colaborador_id" => isset($colaborador_info[0]['id'])?$colaborador_info[0]['id']:'',
				"cantidad_semanas" => 0
		));

    $breadcrumb = array(
        "titulo" => '<i class="fa fa-institution"></i> Reporte de colaborador ',
        "filtro" => false,
        "menu" => array(
            "nombre" => 'Acci&oacute;n',
            "url"	 => '#',
            "opciones" => array()
        ),
        "ruta" => array(
          0 => array(
              "nombre" => "Nómina",
              "activo" => false,
          ),
            1 => array(
                "nombre" => "Planillas",
                "activo" => false,
                "url" => 'planilla/listar'
            ),
            2=> array(
                "nombre" => 'Detalle',
                "activo" => false,
                "url" => 'planilla/ver/'.$planilla_info->uuid_planilla
            ),
            3=> array(
                "nombre" => '<b>Reporte de colaborador</b>',
                "activo" => true
            )
        ),
    );

		$breadcrumb["menu"]["opciones"]["#ExportarBtnDetalles"] = "Exportar";
		$breadcrumb["menu"]["opciones"]["#ImprimirBtnTalonarioDecimo"] = "Imprimir Talonario";

		$uuid_empresa = $this->session->userdata('uuid_empresa');
		$empresa = Empresa_orm::findByUuid($uuid_empresa);

		$this->template->agregar_breadcrumb($breadcrumb);
		$this->template->agregar_contenido($data);
		$this->template->visualizar();

	}

	public function ver_reporte_cerradas($colaborador_planilla_uuid = NULL) {


    if(!$this->auth->has_permission('acceso', 'planilla/ver/(:any)')){
      redirect(base_url('/'));
    }

		if($colaborador_planilla_uuid!=NULL){ //Ver Detalles

			$uuid = explode("~", $colaborador_planilla_uuid);
			$colaborador_uuid = isset($uuid[0])?$uuid[0]:'';
			$planilla_id  = isset($uuid[1])?$uuid[1]:'0';
			$tipo_planilla  = isset($uuid[2])?$uuid[2]:'0';
      if($tipo_planilla == 'vacacion'){
        $vaca = $this->ModelVacacionRep->find($planilla_id);
         $planilla_id = $vaca->planilla[0]->pivot->planilla_id;
      }

		}else{
			if(!$this->auth->has_permission('acceso', 'planilla/crear')){
				redirect(base_url('/'));
			}
		}

		$colaborador_info = $data = array();
		$colaborador_id = NULL;

		if($colaborador_uuid!=NULL)
		{

 			$colaborador_info = Colaboradores_orm::with('estado','cargo','centro_contable','departamento','forma_pago')->where(Capsule::raw("HEX(uuid_colaborador)"), "=", $colaborador_uuid)->get()->toArray();
 			$data['info'] = isset($colaborador_info[0])?$colaborador_info[0]:array();

      $clause= array(
          'colaborador_id'=>$colaborador_info[0]['id'],
          'planilla_id'=>$planilla_id,
        );

       $pagadas = $this->pagadasRepository->findBy($clause);

      $pagadas->load("deducciones","acumulados","descuentos","ingresos","calculos");


 			$tablas_principales  = array(
					'tabla_ingresos' 		    =>  isset($pagadas->ingresos)?$pagadas->ingresos->toArray(): array(),
					'tabla_deducciones' 	  => isset($pagadas->deducciones)?$pagadas->deducciones->toArray(): array(),
	 			  'tabla_descuentos_directos' => isset($pagadas->descuentos)?$pagadas->descuentos->toArray(): array(),
					'tabla_calculos'	 	    => isset($pagadas->calculos)?$pagadas->calculos->toArray(): array(),
					'tabla_acumulados' 		  => isset($pagadas->acumulados)?$pagadas->acumulados->toArray(): array(),
					'salario_mensual_promedio' =>  isset($pagadas->calculos[0]->salario_mensual_promedio)?$pagadas->calculos[0]->salario_mensual_promedio:0,
					'salario_anual_promedio' =>   isset($pagadas->calculos[0]->salario_anual_promedio)?$pagadas->calculos[0]->salario_anual_promedio:0,
					'total_devengado'        =>   isset($pagadas->calculos[0]->total_devengado)?$pagadas->calculos[0]->total_devengado:0,
					'indemnizacion_proporcional' =>  isset($pagadas->calculos[0]->indemnizacion_proporcional)?$pagadas->calculos[0]->indemnizacion_proporcional:0,
			);

    			$this->session->set_userdata($tablas_principales);
 		}
		$this->_css();
		$this->_js();
		$this->assets->agregar_js(array(
				'public/assets/js/modules/planilla/ver-reporte-cerrada.js'
		));

 		$this->assets->agregar_var_js(array(
				"planilla_id" => isset($planilla_id)?$planilla_id:'0',
				"colaborador_id" => isset($colaborador_info[0]['id'])?$colaborador_info[0]['id']:'',
				"cantidad_semanas" => 1
		));

    $breadcrumb = array(
      "titulo" => '<i class="fa fa-institution"></i> '.$pagadas->planilla->codigo.' - Reporte de colaborador ',
      "filtro" => false,
      "menu" => array(
          "nombre" => 'Acci&oacute;n',
          "url"	 => '#',
          "opciones" => array()
      ),
      "ruta" => array(
            0 => array(
                "nombre" => "Nómina",
                "activo" => false,
            ),
            1 => array(
                "nombre" => "Planillas",
                "activo" => false,
                "url" => 'planilla/listar'
            ),
           2 => array(
              "nombre" => '<b>Detalle</b>',
              "activo" => true,
              "url" => 'planilla/listar'
          ),
          3 => array(
             "nombre" => '<b>Reporte</b>',
             "activo" => true,

         )
      ),
  );

 		$breadcrumb["menu"]["opciones"]["#ExportarBtnDetalles"] = "Exportar";
		$breadcrumb["menu"]["opciones"]["#ImprimirBtnTalonario"] = "Imprimir Talonario";

		$this->template->agregar_breadcrumb($breadcrumb);
		$this->template->agregar_contenido($data);
		$this->template->visualizar();
	}


	public function ver_reporte_decimo_cerrada($colaborador_planilla_uuid = NULL) {
		if($colaborador_planilla_uuid!=NULL){ //Ver Detalles
			if(!$this->auth->has_permission('acceso', 'planilla/ver/(:any)')){
				redirect(base_url('/'));
			}
			$uuid = explode("~", $colaborador_planilla_uuid);
			$colaborador_uuid = isset($uuid[0])?$uuid[0]:'';
			$planilla_id  = isset($uuid[1])?$uuid[1]:'0';
		}else{
			if(!$this->auth->has_permission('acceso', 'planilla/crear')){
				redirect(base_url('/'));
			}
		}

		$data = array();
		$colaborador_info = array();
		//$colaborador_id = NULL;

		if($colaborador_uuid!=NULL)
		{
 			$colaborador_info = Colaboradores_orm::with('estado','cargo','centro_contable','departamento','forma_pago')->where(Capsule::raw("HEX(uuid_colaborador)"), "=", $colaborador_uuid)->get()->toArray();

			$total_salario = 0;
			$data['info'] = isset($colaborador_info[0])?$colaborador_info[0]:array();

			$pagos_info =Pagadas_colaborador_orm::with('acumulados','deducciones','descuentos','ingresos','calculos')
			->where("planilla_id", "=", $planilla_id)
			->where(Capsule::raw("HEX(uuid_colaborador)"), "=", $colaborador_uuid)
			->get()->toArray();

			$planilla_info = Planilla_orm::find($planilla_id);

			$fecha_planilla = array("rango_fecha1"=>$planilla_info->rango_fecha1,"rango_fecha2"=>$planilla_info->rango_fecha2);

 		/*	$lista_ingresos = Pagadas_colaborador_orm::salarios_ganados_decimo($colaborador_info[0]['id'], $fecha_planilla);


			$i = 0;
			if(!empty($lista_ingresos)){
				foreach($lista_ingresos as $ingreso){

					$tabla_ingresos[$i] = array(
							"fecha_pago" => date("F j, Y", strtotime($ingreso['fecha_final'])),
							"ingreso"   => $ingreso['salario_bruto']
					);

					$total_salario += $ingreso['salario_bruto'];
					++$i;
				}
				$total_salario = $total_salario*0.08333;

				$info_general[] = array("salario_bruto"=>$total_salario, 'colaborador_id'=>$colaborador_info[0]['id']);
			}
*/

			$tabla_deducciones 	= isset($pagos_info[0]['deducciones'])?$pagos_info[0]['deducciones']: array();
			$tabla_descuentos_directos = isset($pagos_info[0]['descuentos'])?$pagos_info[0]['descuentos']: array();
			$tabla_calculos 	= isset($pagos_info[0]['calculos'])?$pagos_info[0]['calculos']: array();
			$tabla_acumulados 	= isset($pagos_info[0]['acumulados'])?$pagos_info[0]['acumulados']: array();
			unset($tablas_principales);
			$tablas_principales  = array(
					'tabla_ingresos' 		=> $tabla_ingresos,
					'tabla_deducciones' 	=> $tabla_deducciones,
					'tabla_descuentos_directos' => $tabla_descuentos_directos,
					'tabla_calculos'	 	=> $tabla_calculos,
					'tabla_acumulados' 		=> $tabla_acumulados,
					'salario_mensual_promedio' =>  isset($pagos_info[0]['calculos'][0]['salario_mensual_promedio'])?$pagos_info[0]['calculos'][0]['salario_mensual_promedio']:0,
					'salario_anual_promedio' =>  isset($pagos_info[0]['calculos'][0]['salario_anual_promedio'])?$pagos_info[0]['calculos'][0]['salario_anual_promedio']:0,
					'total_devengado' =>   isset($pagos_info[0]['calculos'][0]['total_devengado'])?$pagos_info[0]['calculos'][0]['total_devengado']:0,
					'indemnizacion_proporcional' =>  isset($pagos_info[0]['calculos'][0]['indemnizacion_proporcional'])?$pagos_info[0]['calculos'][0]['indemnizacion_proporcional']:0,
			);

			$this->session->set_userdata($tablas_principales);

		}

		$this->_css();
		$this->_js();
		$this->assets->agregar_js(array(
				'public/assets/js/modules/planilla/ver-reporte-cerrada.js'
		));

		$this->assets->agregar_var_js(array(
				"planilla_id" => isset($planilla_id)?$planilla_id:'0',
				"colaborador_id" => isset($colaborador_info[0]['id'])?$colaborador_info[0]['id']:'',
				"cantidad_semanas" => 1
		));

    $breadcrumb = array(
        "titulo" => '<i class="fa fa-institution"></i> Reporte de colaborador ',
        "filtro" => false,
        "menu" => array(
            "nombre" => 'Acci&oacute;n',
            "url"	 => '#',
            "opciones" => array()
        ),
        "ruta" => array(
          0 => array(
              "nombre" => "Nómina",
              "activo" => false,
          ),
            1 => array(
                "nombre" => "Planillas",
                "activo" => false,
                "url" => 'planilla/listar'
            ),
            2=> array(
                "nombre" => 'Detalle',
                "activo" => false,
                "url" => 'planilla/ver/'.$planilla_info->uuid_planilla
            ),
            3=> array(
                "nombre" => '<b>Reporte de colaborador</b>',
                "activo" => true
            )
        ),
    );

		$breadcrumb["menu"]["opciones"]["#ExportarBtnDetalles"] = "Exportar";
		$breadcrumb["menu"]["opciones"]["#ImprimirBtnTalonario"] = "Imprimir Talonario";

		$this->template->agregar_breadcrumb($breadcrumb);
		$this->template->agregar_contenido($data);
		$this->template->visualizar();
	}


	private function listado_ingresos_regular($planilla_id = NULL,  $cantidad_semanas=NULL, $colaborador_id = NULL, $fechas = array(), $rata_hora) {


 		$ingresos = $planilla_colaborador_id = $calculos = array();
 		$acumulado_salario_bruto =  $cantidad_horas_total =  0;
		$planilla_col 			=  Planilla_colaborador_orm::where('planilla_id','=', $planilla_id)->where('colaborador_id','=', $colaborador_id)->get()->toArray();

		$clauseIH =  array(
				'empresa_id' => $this->empresa_id,
				'id_planilla_colaborador' => $planilla_col[0]['id']
		);

		$ingresoHoras = Ingreso_horas_orm::listar($clauseIH, NULL, NULL, NULL, NULL, $fechas);

		if(!empty($ingresoHoras->toArray())){
			//$salario_por_recargo = 0;
			foreach ($ingresoHoras->toArray() AS $i => $ingreso){

				$suma_horas_x_recargo = 0;
				if(!empty($ingreso['horas_dias'])){
					foreach($ingreso['horas_dias'] as $horas){


						$suma_horas_x_recargo += $horas['horas'];
					}
				}

				$salario_por_recargo = $suma_horas_x_recargo*($ingreso['recargo']['porcentaje_hora']*$rata_hora);
				if($ingreso['recargo']['nombre']!='HR'){
					$bonificaciones  = $salario_por_recargo;
				}
				$acumulado_salario_bruto += $salario_por_recargo;
				$cantidad_horas_total += $suma_horas_x_recargo;
				$rata = $ingreso['recargo']['porcentaje_hora']*$rata_hora;
				$calculos[] = array(
						"detalle" => $ingreso['recargo']['nombre'],
						"cantidad_horas" =>$suma_horas_x_recargo,
						"rata" => $rata,
						"calculo" =>$salario_por_recargo,
				);


			}
		}


		return array('salario_bruto'=>$acumulado_salario_bruto, 'listado_ingresos'=>$calculos);
	}

	private function calculos_deducciones_abiertas($colaboradores = array(), $deducciones= array()) {

		$resultado_deducciones = array();
		if(!empty($colaboradores)){
			foreach($colaboradores as $colaborador){



				$deduccion_total = 0;
				$deducciones_colaborador = array();
				if(!empty($deducciones)){
					foreach($deducciones as $deduccion){
						$descuentos_directos =  array();
						if( $deduccion['nombre']== 'Descuentos directos'){
							$descuentos_directos = Planilla_deducciones_orm::lista_descuentos_colaborador($colaborador['colaborador_id']);

							$deducciones_colaborador[$deduccion['nombre']] = array(
									"id" 		=> $deduccion['id'],
									"nombre" 	=> $deduccion['nombre'],
									"descuento" => 0,
									"saldo" 	=> 0,
									"descuentos_directos" 	=> $descuentos_directos,
							);
						}
						else{
							if($deduccion['rata_colaborador_tipo'] == 'Porcentual'){
								$rata =  $deduccion['rata_colaborador']/100;
							}

							if( $deduccion['nombre'] != 'Impuesto sobre la Renta' ){

								if($deduccion['rata_colaborador_tipo'] == 'Porcentual'){
									$rata =  $deduccion['rata_colaborador']/100;
									$_deduccion_valor = $colaborador['salario_bruto'] * $rata;
								}
								else if($deduccion['rata_colaborador_tipo'] == 'Monto'){
									$rata =  $deduccion['rata_colaborador'];
									$_deduccion_valor = $rata;
								}

							}else{

								$_deduccion_valor = $this->impuesto_sobre_renta($deduccion['limite1'], $deduccion['limite2'], ($colaborador['salario_bruto']*13), $rata);
							}
							$acumulado_deduccion = Planilla_colaborador_orm::calculando_deduccion_totales($colaborador['colaborador_id'],$deduccion['id'] );

							$deduccion_total += $_deduccion_valor;
							$deducciones_colaborador[] = array(
									"id" 		=> $deduccion['id'],
									"key" 		=> $deduccion['key'],
									"nombre" 	=> $deduccion['nombre'],
									"descuento" => $_deduccion_valor,
									"saldo" 	=> $acumulado_deduccion,
							);
						}
					}
				}


				$resultado_deducciones[] = array(
						"colaborador_id" => $colaborador['colaborador_id'],
						"deduccion_total" => $deduccion_total,
						"deducciones" => $deducciones_colaborador
				);
			}
		}

		return $resultado_deducciones;
	}
	private function calculo_salarios_vacaciones_vencidas($colaborador_id, $periodo ) {

		$sumatoria_devengado = 0;

		$listado_vacaciones = Planilla_vacacion_orm::salario_devengado_vacaciones_vencidas(
		$colaborador_id,
		$periodo);

		if(!empty($listado_vacaciones)){
			foreach ($listado_vacaciones as $row){
				$sumatoria_devengado += $row->salario_bruto;
 			}
		}
		return array("salario_bruto"=>$sumatoria_devengado/11);
 	}
      function ocultoformulariocomentarios() {

         $data = array();

         $this->assets->agregar_js(array(
             'public/assets/js/plugins/ckeditor/ckeditor.js',
             'public/assets/js/plugins/ckeditor/adapters/jquery.js',
             'public/assets/js/modules/planilla/vue.comentario.js',
             'public/assets/js/modules/planilla/formulario_comentario.js'
         ));

         $this->load->view('formulario_comentarios');
         $this->load->view('comentarios');

     }
     function ajax_guardar_comentario_planilla() {
         if(!$this->input->is_ajax_request()){return false;}
         $model_id   = $this->input->post('modelId');
         $comentario = $this->input->post('comentario');
         $comentario = ['comentario'=>$comentario,'usuario_id'=>$this->id_usuario];
         $planilla = $this->planillaRepository->agregarComentario($model_id, $comentario);
         $planilla->load('comentario_timeline');

         $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
             ->set_output(json_encode($planilla->comentario_timeline->toArray()))->_display();
         exit;
     }
     private function removeElementWithValue($array, $array_unset) {

      		if(!empty($array) && !empty($array_unset)){
      			foreach ($array as $key => $subArr) {
      				foreach ($array_unset as $key_valor){
      					unset($subArr[$key_valor]);

      				}
      				$data[$key] = $subArr;

      			}
      		}
      		return $data;
	}

  public function ajax_detalles_pago_especiales() {

    //Just Allow ajax request
    if(!$this->input->is_ajax_request()){
      return false;
    }

    $response = new stdClass();
    $vacacion_planilla = $deduccion_total = 0;
    $planilla_id 	= $this->input->post('planilla_id', true);
    $cantidad_semanas 	= $this->input->post('cantidad_semanas', true);
    $tipo 	= $this->input->post('tipo', true);

    if( $tipo == 'vacaciones' ){
      $this->calculo_para_vacaciones($planilla_id);
    }
    else if( $tipo == 'liquidaciones' ){
      $this->calculo_para_liquidaciones($planilla_id, $cantidad_semanas,$tipo );
    }
    else if( $tipo == 'licencias' ){
      $this->calculo_para_licencias($planilla_id, $cantidad_semanas,$tipo );
    }
  }
  //Calculos que se muestran en modal antes de cerrar una planilla
/*	private function calculo_para_vacaciones($planilla_id = NULL ) {

    $vacacion_planilla = $deduccion_total = $vacacion_total = 0;

    $datos_generales = $this->calculos_salarios_brutos_especiales( $planilla_id, 0, 'vacaciones' );

    if(!empty($datos_generales)){
      foreach($datos_generales as $row){
        $vacacion_planilla +=  $row['salario_devengado_vacacion']/11;
      }
    }

    $lista_deducciones = Planilla_deducciones_orm::lista_deducciones_planilla($planilla_id);

    //Funcion que se encargar� de calcular las deducciones por cada colaborador
    $calculo_deducciones_por_colaborador = $this->calculos_deducciones_colaborador_vacaciones($datos_generales, $lista_deducciones);
    if(!empty($calculo_deducciones_por_colaborador)){
      foreach ($calculo_deducciones_por_colaborador as $deducciones){
        if(!empty($deducciones['deducciones'])){
          foreach ($deducciones['deducciones'] as $calculo){
            //$vacacion_planilla +=  $calculo['acumulado'];
            $deduccion_total += $calculo['descuento'];
          }

        }
      }
    }
    $response = new stdClass();
    $response->response = true;
    $response->total_colaboradores 		= count($datos_generales);
    $response->salario_bruto 			= number_format( $vacacion_planilla,2);

    $response->deducciones 				= number_format($deduccion_total,2);
    $response->deducciones_porcentaje 	= ($vacacion_planilla>0)?number_format(($deduccion_total/$vacacion_planilla)*100,2):0;
    $response->deducciones_progress_bar 	=($vacacion_planilla>0)? number_format(($deduccion_total/$vacacion_planilla)*100,2)."%":'0%';

    $response->salario_neto 			=  number_format($vacacion_planilla-$deduccion_total,2);
    $response->salario_neto_porcentaje 	=   ($vacacion_planilla>0)?number_format((($vacacion_planilla-$deduccion_total)/$vacacion_planilla)*100,2):0;
    $response->salario_neto_progress_bar=   ($vacacion_planilla>0)?number_format((($vacacion_planilla-$deduccion_total)/$vacacion_planilla)*100,2)."%":"0%";


    echo json_encode($response);
    exit;
  }*/


 /*	private function calculo_para_licencias($planilla_id = NULL, $cantidad_semanas= NULL,$tipo= NULL) {

    $bruto_total = $deduccion_total = 0;

    $datos_generales = $this->calculos_salarios_brutos_especiales( $planilla_id, $cantidad_semanas, $tipo );

    $deduccion_total = 1;
    $bruto_total = 1;

    $response = new stdClass();
    $response->response = true;
    $response->total_colaboradores 		= count($datos_generales);
    $response->salario_bruto 			= number_format( $bruto_total,2);

    $response->deducciones 				= number_format($deduccion_total,2);
    $response->deducciones_porcentaje 	= number_format(($deduccion_total/$bruto_total)*100,2);
    $response->deducciones_progress_bar 	= number_format(($deduccion_total/$bruto_total)*100,2)."%";

    $response->salario_neto 			=  number_format($bruto_total-$deduccion_total,2);
    $response->salario_neto_porcentaje 	=  number_format((($bruto_total-$deduccion_total)/$bruto_total)*100,2);
    $response->salario_neto_progress_bar=  number_format((($bruto_total-$deduccion_total)/$bruto_total)*100,2)."%";

  // ($vacacion_planilla>0)?number_format((($vacacion_planilla-$deduccion_total)/$vacacion_planilla)*100,2):0;
    echo json_encode($response);
    exit;
  }*/

  //ESTA FUNCION SOLO SIRVE CUANDO LA PLANILLA ESTA ABIERTA
/*	private function calculos_deducciones_colaborador_vacaciones($colaboradores = array(), $deducciones= array()) {


     		$resultado_deducciones = array();
		if(!empty($colaboradores)){
			foreach($colaboradores as $colaborador){

				$deduccion_total = 0;
				$deducciones_colaborador = array();
				if(!empty($deducciones)){
					foreach($deducciones as $deduccion){
						if($deduccion['rata_colaborador_tipo'] == 'Porcentual'){
							$rata =  $deduccion['rata_colaborador']/100;
						}

						if( $deduccion['nombre'] != 'Impuesto sobre la Renta' ){

							if($deduccion['rata_colaborador_tipo'] == 'Porcentual'){
								$rata =  $deduccion['rata_colaborador']/100;
 								$_deduccion_valor = $colaborador['salario_devengado_vacacion_entre11'] * $rata;
							}
							else if($deduccion['rata_colaborador_tipo'] == 'Monto'){
								$rata =  $deduccion['rata_colaborador'];
								$_deduccion_valor = $rata;
							}
						}else{

							$_deduccion_valor = $this->impuesto_sobre_renta($deduccion['limite1'], $deduccion['limite2'], ($colaborador['salario_devengado_vacacion_entre11']*13), $rata);
						}
						$acumulado_deduccion = Planilla_colaborador_orm::calculando_deduccion_totales($colaborador['colaborador_id'],$deduccion['id'] );

						$deduccion_total += $_deduccion_valor;
						$deducciones_colaborador[] = array(
								"id" 		=> $deduccion['id'],
								"nombre" 	=> $deduccion['nombre'],
								"descuento" => $_deduccion_valor,
								"saldo" 	=> $acumulado_deduccion
						);
					}
				}


				$resultado_deducciones[] = array(
						"colaborador_id" => $colaborador['colaborador_id'],
						"deduccion_total" => $deduccion_total,
						"deducciones" => $deducciones_colaborador
				);
			}
		}

		return $resultado_deducciones;
	}


 	private function calculos_deducciones_decimo($colaboradores = array(), $deducciones= array()) {
   		$resultado_deducciones = array();
		if(!empty($colaboradores)){
			foreach($colaboradores as $colaborador){

				//$colaborador['total_devengado_decimo']
				$deduccion_total = 0;
				$deducciones_colaborador = array();
				if(!empty($deducciones)){
					foreach($deducciones as $deduccion){
						if($deduccion['rata_colaborador_tipo'] == 'Porcentual'){
							$rata =  $deduccion['rata_colaborador']/100;
						}

						if( $deduccion['nombre'] != 'Impuesto sobre la Renta' ){


							if($deduccion['rata_colaborador_tipo'] == 'Porcentual'){
								$rata =  $deduccion['rata_colaborador']/100;
								$_deduccion_valor = $colaborador['total_devengado_decimo_8.33'] * $rata;

 							}
							else if($deduccion['rata_colaborador_tipo'] == 'Monto'){
								$rata =  $deduccion['rata_colaborador'];
								$_deduccion_valor = $rata;


							}

 						}else{
							$_deduccion_valor = $this->impuesto_sobre_renta($deduccion['limite1'], $deduccion['limite2'], ($colaborador['total_devengado_decimo_8.33']*13), $rata);
						}
						$acumulado_deduccion = Planilla_colaborador_orm::calculando_deduccion_totales($colaborador['colaborador_id'],$deduccion['id'] );

						$deduccion_total += $_deduccion_valor;
						$deducciones_colaborador[] = array(
								"id" 		=> $deduccion['id'],
								"nombre" 	=> $deduccion['nombre'],
								"descuento" => $_deduccion_valor,
								"saldo" 	=> $acumulado_deduccion
						);
					}
				}

				$resultado_deducciones[] = array(
						"colaborador_id" => $colaborador['colaborador_id'],
						"deduccion_total" => $deduccion_total,
						"deducciones" => $deducciones_colaborador
				);
			}
		}



 		return $resultado_deducciones;
	}*/
}

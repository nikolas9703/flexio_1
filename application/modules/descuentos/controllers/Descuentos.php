<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Descuentos
 *
 * Modulo para administrar los descuentos a colaboradores.
 *
 * @package    PensaApp
 * @subpackage Controller
 * @category   Controllers
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  02/16/2016
 */

use Illuminate\Database\Capsule\Manager as Capsule;
use League\Csv\Writer as Writer;
use Carbon\Carbon;
use Flexio\Modulo\Acreedores\Repository\AcreedoresRepository as acreedoresRep;
use Flexio\Modulo\DescuentosDirectos\Repository\DescuentosDirectosRepository as descuentoRep;



class Descuentos extends CRM_Controller
{


        private $descuentoRep;
        private $acreedoresRep;
	/**
	 * @var int
	 */
	protected $usuario_id;

	/**
	 * @var int
	 */
	protected $empresa_id;

	/**
	 * @var string
	 */
	protected $nombre_modulo;

	/**
	 * @var string
	 */
	protected $upload_folder = './public/uploads/';

	function __construct()
    {
        parent::__construct();

        $this->load->model('descuentos_orm');
        $this->load->model('DescuentosCat_orm');
        $this->load->model('centros/Centros_orm');
        $this->load->model('estado_orm');
        $this->load->model('configuracion_rrhh/Cargos_orm');
        $this->load->model('contabilidad/cuentas_orm');
        $this->load->model('colaboradores/colaboradores_orm');
        $this->load->model('configuracion_rrhh/departamentos_orm');
        $this->load->model('acreedores/acreedores_orm');
        $this->load->model('usuarios/usuario_orm');
        $this->load->model('usuarios/empresa_orm');
        $this->load->model('planilla/Pagadas_descuentos_orm');
        $this->load->model('planilla/Pagadas_orm');
        $this->load->library('orm/catalogo_orm');
        $this->acreedoresRep    = new acreedoresRep();
        $this->descuentoRep = new descuentoRep();
        //Obtener el id de usuario de session
        $uuid_usuario = $this->session->userdata('huuid_usuario');
        $usuario = Usuario_orm::findByUuid($uuid_usuario);

        $this->usuario_id = $usuario->id;

        //Obtener el id_empresa de session
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);

        $this->empresa_id = $empresa->id;


        $colaborador = Colaboradores_orm::where('empresa_id', $this->empresa_id);

        $this->colaborador_list = $colaborador->get()->toArray();

       /* echo '<h2>Consultando Antes colaboradores:</h2><pre>';
            print_r($this->colaborador_list);
            echo '</pre>';
        */
        $this->nombre_modulo = $this->router->fetch_class();
    }

    /*public function listaAcreedores(){
		return Capsule::table('acr_acreedores AS acr')
			->get(array('acr.id', 'acr.nombre'));
	} */

    public function listar()
    {
    	$data = array(
    		"estados" => Estado_orm::lista(),
    		"lista_departamentos" => Departamentos_orm::lista($this->empresa_id),
                "descuentos" => Descuentoscat_orm::listaDescuentos(),
                //"acreedores_list" => Acreedores_orm::lista($this->empresa_id)
                "acreedores_list" => $this->acreedoresRep->get(array('empresa_id' => $this->empresa_id))
    	);



    	$this->assets->agregar_css(array(
    		'public/assets/css/default/ui/base/jquery-ui.css',
    		'public/assets/css/default/ui/base/jquery-ui.theme.css',
    		'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
    		'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
    		'public/assets/css/plugins/jquery/chosen/chosen.min.css',
    		'public/assets/css/plugins/jquery/jquery.webui-popover.css',
    		'public/assets/css/plugins/bootstrap/jquery.bootstrap-touchspin.css',
    		'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
        	'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
    	));
    	$this->assets->agregar_js(array(
    		'public/assets/js/default/jquery-ui.min.js',
    		'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
    		'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
    		'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
    		'public/assets/js/plugins/jquery/jquery.webui-popover.js',
    		'public/assets/js/plugins/jquery/jquery.sticky.js',
    		'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
    		'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
    		'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
    		'public/assets/js/plugins/jquery/chosen.jquery.min.js',
    		'public/assets/js/moment-with-locales-290.js',
    		'public/assets/js/plugins/bootstrap/jquery.bootstrap-touchspin.js',
    		'public/assets/js/plugins/bootstrap/daterangepicker.js',
    		'public/assets/js/default/formulario.js',
    		'public/assets/js/modules/descuentos/tabla.js',
    	));

    	//Agregra variables PHP como variables JS
    	$this->assets->agregar_var_js(array(
    		"grid_url" => 'descuentos/ajax-listar/descuentosgrid'

    	));

    	//Opcion Default
    	$menuOpciones = array();


    	//Breadcrum Array
    	$breadcrumb = array(
    		"titulo" => '<i class="fa fa-users"></i> Descuentos Directos'

    	);


       /* echo '<h2>Consultando Antes:</h2><pre>';
            print_r($descuentos);
            echo '</pre>';
        */
    	//Verificar permisos para crear
    	if($this->auth->has_permission('acceso', 'descuentos/crear')){
    		$breadcrumb["menu"] = array(
    			"url"	 => 'descuentos/crear',
    			"nombre" => "Crear"
    		);
    		$menuOpciones["#exportarDescuentoLnk"] = "Exportar";
    	}

    	$breadcrumb["menu"]["opciones"] = $menuOpciones;


    	$this->template->agregar_titulo_header('Descuentos Directos');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar($breadcrumb);
    }

    public function listar_estado($uuid_descuento=NULL)
    {

        $descuentos_info = array();
    	$descuentos_info['info'] = Descuentos_orm::with(array('estado', 'colaborador', 'plan_contable', 'ciclo', 'acreedores', 'tipo_descuento' => function($query){
    		}))->where(Capsule::raw("HEX(uuid_descuento)"), "=", $uuid_descuento)->get()->toArray();

        $id_colaborador = $descuentos_info['info'][0]['colaborador']['id'];
        $id_descuento = $descuentos_info['info'][0]['id'];

        $descuentos_info['centro_contable'] = Colaboradores_orm::with(array('centro_contable', 'cargo' => function($query){
    		}))->where(Capsule::raw("id"), "=", $id_colaborador)->get()->toArray();

        $descuentos_info['cantidad'] = Descuentos_orm::where(Capsule::raw("colaborador_id"), "=", $id_colaborador)->where(Capsule::raw("estado_id"), "=", "5")->get()->count();


    	$this->assets->agregar_css(array(
    		'public/assets/css/default/ui/base/jquery-ui.css',
    		'public/assets/css/default/ui/base/jquery-ui.theme.css',
    		'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
    		'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
    		'public/assets/css/plugins/jquery/chosen/chosen.min.css',
    		'public/assets/css/plugins/jquery/jquery.webui-popover.css',
    		'public/assets/css/plugins/bootstrap/jquery.bootstrap-touchspin.css',
    		'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
        	'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
                'public/assets/css/modules/descuentos/estado_cuenta.css',
    	));
    	$this->assets->agregar_js(array(
    		'public/assets/js/default/jquery-ui.min.js',
    		'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
    		'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
    		'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
    		'public/assets/js/plugins/jquery/jquery.webui-popover.js',
    		'public/assets/js/plugins/jquery/jquery.sticky.js',
    		'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
    		'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
    		'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
    		'public/assets/js/plugins/jquery/chosen.jquery.min.js',
    		'public/assets/js/moment-with-locales-290.js',
    		'public/assets/js/plugins/bootstrap/jquery.bootstrap-touchspin.js',
    		'public/assets/js/plugins/bootstrap/daterangepicker.js',
    		'public/assets/js/default/formulario.js',
    		'public/assets/js/modules/descuentos/estado_cuenta.js',
    		'public/assets/js/modules/descuentos/jspdf.min.js'

    	));

    	//Agregra variables PHP como variables JS
    	$this->assets->agregar_var_js(array(
    		"grid_url" => 'descuentos/ajax-listar/descuentosgrid',
    		"id_descuento" => $id_descuento

    	));

    	//Opcion Default
    	$menuOpciones = array();


    	//Breadcrum Array
    	$breadcrumb = array(
    		"titulo" => 'Descuentos Directos: Estado de Cuenta',

    	);


       /* echo '<h2>Consultando Antes:</h2><pre>';
            print_r($descuentos);
            echo '</pre>';
        */
    	//Verificar permisos para crear
    	if($this->auth->has_permission('acceso', 'descuentos/crear')){
    		$breadcrumb["menu"] = array(
    			"url"	 => 'descuentos/crear',
    			"nombre" => "Crear"
    		);
    		$menuOpciones["#exportarDescuentoLnk"] = "Exportar";
    	}

    	$breadcrumb["menu"]["opciones"] = $menuOpciones;


    	$this->template->agregar_titulo_header('Descuentos Directos');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($descuentos_info);
    	$this->template->visualizar($breadcrumb);
    }

    public function ajax_listar($grid=NULL)
    {
    	Capsule::enableQueryLog();

        $colaborador = Colaboradores_orm::lista($this->empresa_id);

    	$numero 	= $this->input->post('numero', true);
    	$cedula         = $this->input->post('cedula', true);
    	$tipo_descuento = $this->input->post('tipo_descuento', true);
      $nombre_colaborador         = $this->input->post('nombre_colaborador', true);
    	$acreedor       = $this->input->post('acreedor', true);
        $estado         = $this->input->post('estado_id', true);
        $colaborador_id = $this->input->post('colaborador_id', true);
        $fecha_desde 	= $this->input->post('fecha_desde', true);
        $fecha_hasta 	= $this->input->post('fecha_hasta', true);

    	$clause = array(
    		"empresa_id" =>  $this->empresa_id
    		//"colaborador_id" => $this->uuid_colaborador
    	);

    	if(!empty($numero)){

    		//$clause["numero"] = array('LIKE', "%$numero%");
        $clause['codigo'] = $numero;
    	}
      if(!empty($cedula)){
              $clause['cedula'] = array($cedula);
      }

      if(!empty($nombre_colaborador)){
        $clause['colaborador_nombre'] = array($nombre_colaborador);
      }

     	if(!empty($tipo_descuento)){
    		$clause["tipo_descuento_id"] = $tipo_descuento;
    	}
    	if(!empty($acreedor)){
    		$clause["acreedor_id"] = $acreedor;
    	}
    	if(!empty($estado)){
    		$clause["estado_id"] = $estado;
    	}
    	if(!empty($colaborador_id)){
    		$clause["colaborador"] = $colaborador_id;
    	}
    	if( !empty($fecha_desde)){
    		$fecha_desde = str_replace('/', '-', $fecha_desde);
    		$fecha_inicio = date("Y-m-d H:i:s", strtotime($fecha_desde));
    		$clause["fecha_inicio"] = array('>=', $fecha_inicio);
    	}
    	if( !empty($fecha_hasta)){
    		$fecha_hasta = str_replace('/', '-', $fecha_hasta);
    		$fecha_fin = date("Y-m-d 23:59:59", strtotime($fecha_hasta));
    		$clause["fecha_inicio@"] = array('<=', $fecha_fin);
    	}

    	list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();

    	$count = Descuentos_orm::listar($clause, NULL, NULL, NULL, NULL)->count();

    	list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

    	$rows = Descuentos_orm::listar($clause, $sidx, $sord, $limit, $start);

        foreach($rows AS $info){

            $info->colaborador;
          //  $info->acreedor;

        }


    	//Constructing a JSON
    	$response = new stdClass();
    	$response->page     = $page;
    	$response->total    = $total_pages;
    	$response->records  = $count;
    	$response->result 	= array();
    	$i=0;

    	if(!empty($rows->toArray())){
    		foreach ($rows->toArray() AS $i => $row){

                        $acreedor = $this->acreedoresRep->find($row['acreedor_id']);
                        $uuid_descuento = $row['uuid_descuento'];

    			        $uuid_colaborador = $row['colaborador']['uuid_colaborador'];
                                if($row["estado"]["etiqueta"]=="Pendiente"){
                                    $color = "warning";
                                 }
                                 elseif($row["estado"]["etiqueta"]=="Rechazado"){
                                     $color = "danger";
                                 }
                                else{
                                    $color = "primary";
                                }

                   $link_option = '<center><button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row['id'] .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button></center>';
    				$hidden_options = "";

    				//verificar si carga desde colaboradores
    				if(preg_match("/colaboradores/i", $_SERVER['HTTP_REFERER'])){
    					$hidden_options .= '<a href="#" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success verDetalleDescuento">Ver Detalle</a>';
    				}else{
    					$hidden_options .= '<a href="'. base_url('descuentos/ver/'. $uuid_descuento) .'" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success">Ver Detalle</a>';
    				}

                    $hidden_options .= '<a href="'. base_url('descuentos/estado/'.$uuid_descuento).'" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success">Estado de cuenta</a>';
    				$hidden_options .= '<a href="#" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success descargarAdjuntoBtn">Descargar</a>';


    				$response->rows[$i]["id"] = $row['id'];
    				$response->rows[$i]["cell"] = array(
    					'<a href="'. base_url('descuentos/ver/'. $uuid_descuento) .'" data-id="'. $row['id'] .'" style="color:blue;">' . Util::verificar_valor($row['codigo']) . '</a>',
    					Util::verificar_valor($row['tipo_descuento']['etiqueta']),
    					Util::verificar_valor($acreedor['nombre']),
                                        '<a style="color:blue; text-decoration:underline;" href="'. base_url('colaboradores/ver/'. $uuid_colaborador) .'">' . Util::verificar_valor($row['colaborador']['nombre'] . " " . $row['colaborador']['apellido']) . '</a>',
                                        Util::verificar_valor($row['colaborador']['cedula']),
    					$row['fecha_inicio'] !="" ? Carbon::createFromFormat('Y-m-d', $row['fecha_inicio'])->format('d/m/Y') : "",
    					Util::verificar_valor($row["monto_ciclo"]),
                                        '<span class="label label-'. $color .'">'. Util::verificar_valor($row["estado"]["etiqueta"]) .'</span>',

                                        $link_option,
    					$hidden_options,
    					Util::verificar_valor($row['archivo_ruta']),
    					Util::verificar_valor($row['archivo_nombre'])
    				);


    			$i++;
    		}
    	}
    	echo json_encode($response);
    	exit;
    }


    public function ajax_descargar()
    {
    	if(empty($_POST)){
    		die();
    	}

    	$ids =  $this->input->post('ids', true);
		$id = explode(",", $ids);

		if(empty($id)){
			return false;
		}

		$csv = array();
		$clause = array(
			"descuetos" => $id
		);
		$colaboradores = Descuento_orm::listar($clause, NULL, NULL, NULL, NULL)->toArray();

		if(empty($colaboradores)){
			return false;
		}

		$i=0;
		foreach ($colaboradores AS $row)
		{
			$nombre = Util::verificar_valor($row['nombre']);
			$apellido = Util::verificar_valor($row['apellido']);

			$csvdata[$i]['codigo'] = "";
			$csvdata[$i]['nombre'] = $nombre. " ". $apellido;
			$csvdata[$i]["cedula"] = utf8_decode(Util::verificar_valor($row['cedula']));
			$csvdata[$i]["fecha"] = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d/m/Y');
			$csvdata[$i]["centro_contable"] = utf8_decode(Util::verificar_valor($row["centro_contable"]["nombre"]));
			$csvdata[$i]["departamento"] = utf8_decode(Util::verificar_valor($row["departamento"]["nombre"]));
			$csvdata[$i]["cargo"] = utf8_decode(Util::verificar_valor($row["cargo"]["nombre"]));
			$csvdata[$i]["tipo_salario"] = utf8_decode(Util::verificar_valor($row["tipo_salario"]));
			$csvdata[$i]["etiqueta"] = utf8_decode(Util::verificar_valor($row["estado"]["etiqueta"]));
			$i++;
		}

		//we create the CSV into memory
		$csv = Writer::createFromFileObject(new SplTempFileObject());
		$csv->insertOne([
			'No. Colaborador',
			'Nombre',
			'Cedula',
			'Fecha de Contratacion',
			'C. Contable',
			'Area de Negocio',
			'Cargo',
			'Tipo de Salario',
			'Estado'
		]);
		$csv->insertAll($csvdata);
		$csv->output("colaboradores-". date('ymd') .".csv");
		die;
    }

    public function exportar()
    {
    	if(empty($_POST)){
    		die();
    	}


    	$ids =  $this->input->post('ids', true);
		$id = explode(",", $ids);

		if(empty($id)){
			return false;
		}

		$csv = array();
		$clause = array("id" => $id);
		$descuentos = Descuentos_orm::listar($clause, NULL, NULL, NULL, NULL)->toArray();


		if(empty($descuentos)){
			return false;
		}

		$i=0;
		foreach ($descuentos AS $row)
		{

                $colaborador = Colaboradores_orm::where(Capsule::raw("id"), "=", $row['colaborador_id'])->get()->toArray();
                $acreedor = $this->acreedoresRep->find($row['acreedor_id']);

			$csvdata[$i]['codigo'] = utf8_decode(Util::verificar_valor($row["codigo"]));
			$csvdata[$i]['tipo_descuento'] = utf8_decode(Util::verificar_valor($row["tipo_descuento"]["etiqueta"]));
			$csvdata[$i]["acreedor"] = utf8_decode(Util::verificar_valor($acreedor->nombre));
                        $csvdata[$i]["nombre"] = utf8_decode(Util::verificar_valor($colaborador[0]['nombre'] . $colaborador[0]['apellido']));
                        $csvdata[$i]["cedula"] = utf8_decode(Util::verificar_valor($colaborador[0]['cedula']));
                        $csvdata[$i]["fecha"] = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d/m/Y');
			$csvdata[$i]["monto_ciclo"] = utf8_decode(Util::verificar_valor($row['monto_ciclo']));
			$csvdata[$i]["estado"] = utf8_decode(Util::verificar_valor($row['estado']['etiqueta']));
			$i++;
		}

		//we create the CSV into memory
		$csv = Writer::createFromFileObject(new SplTempFileObject());
		$csv->insertOne([
			'No. Descuento',
			'Tipo de Descuento',
			'Acreedor',
			'Nombre',
			'Cedula',
			'Fecha',
			'Monto por Ciclo',
			'Estado'
		]);
		$csv->insertAll($csvdata);
		$csv->output("descuentos-". date('ymd') .".csv");
		die;
    }

    /**
     * Cargar Vista Parcial de Tabla
     *
     * @return void
     */
    public function ocultotabla()
    {
    	$this->assets->agregar_js(array(
    		'public/assets/js/modules/descuentos/tabla.js'
    	));

    	$this->load->view('tabla');
    }

    /**
     * Cargar Vista Parcial de Formulario
     *
     * @return void
     */
    public function ocultoformulario($data=NULL)
    {
    	$this->assets->agregar_js(array(
    		'public/assets/js/modules/descuentos/crear.js'
    	));

    	$this->load->view('formulario', $data);
    }

    public function crear($descuento_uuid=NULL)
    {
    	$data = array();
    	$mensaje = array();
    	$titulo_formulario = '<i class="fa fa-users"></i> Descuentos directos: Crear';

    	//Verificar si existe variable $descuento_uuid
    	if(!empty($descuento_uuid)){
          //  $descuento_info = Descuentos_orm::where(Capsule::raw("HEX(uuid_descuento)"), "=", $descuento_uuid)->get()->toArray();
        //  $descuento_info = Descuentos::
        //$descuento_info = $this->descuentoRep->findByUuid($descuento_uuid);
        $descuento = $this->descuentoRep->findByUuid($descuento_uuid);
    		if(count($descuento)){

            $estado_finalizado = $descuento->monto_adeudado < 1 ? 1 : 0;
            $descuento->load('comentario_timeline','descuentos_asignados');
            $this->assets->agregar_var_js(array(
      				"descuento_id" => $descuento->id,
      				"colaborador_id" => $descuento->colaborador_id,
              "estado_finalizado" => !empty($estado_cuenta[0]) ? $estado_finalizado : "",
              'vista' => 'ver',
              "coment" =>(isset($descuento->comentario_timeline)) ? $descuento->comentario_timeline : "",
    			  ));
    			  $titulo_formulario = '<i class="fa fa-users"></i> Descuentos directos: '. $descuento->codigo;
    		}
    	}

    	//Verificar si existe POST de colaborador_id
    	//Este valor viene desde modulo de Colaborador
    	$colaborador_id = $this->input->post('colaborador_id', true);
    	if(!empty($colaborador_id)){
    		$data["colaborador_id_selected"] = $colaborador_id;
    		$this->assets->agregar_var_js(array(
    			"colaborador_id_selected" => $colaborador_id,
    		));
    	}

        $clause["empresa_id"]   = $this->empresa_id;
        $acreedores = $this->acreedoresRep->get($clause);

        //$datos = array();
        $data["colaborador_list"] =  Colaboradores_orm::lista($this->empresa_id);
        $data["cuentas_pasivo"] =   Cuentas_orm::transaccionalesDeEmpresa($this->empresa_id)->deTipoDeCuenta([2])->activas()->get();
        $data["tipo_descuento"] = Descuentoscat_orm::listaDescuentos();
        $data["acreedores_list"] = $acreedores;

    	$this->assets->agregar_css(array(
    		'public/assets/css/plugins/bootstrap/awesome-bootstrap-checkbox.css',
    		'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
    		'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
    		'public/assets/css/default/ui/base/jquery-ui.css',
    		'public/assets/css/default/ui/base/jquery-ui.theme.css',
    		'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
    		'public/assets/css/plugins/bootstrap/jquery.bootstrap-touchspin.css',
    		'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
    		'public/assets/css/plugins/jquery/jquery.webui-popover.css',
    		'public/assets/css/plugins/jquery/chosen/chosen.min.css',
        'public/assets/css/plugins/jquery/jquery.fileupload.css',
    	));
    	$this->assets->agregar_js(array(
    		'public/assets/js/default/jquery-ui.min.js',
    		'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
    		'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
    		'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
    		'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
    		'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
    		'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
    		'public/assets/js/plugins/jquery/combodate/momentjs.js',
    		'public/assets/js/plugins/jquery/chosen.jquery.min.js',
    		'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
    		'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
    		'public/assets/js/plugins/jquery/jquery.webui-popover.js',
    		'public/assets/js/plugins/bootstrap/jquery.bootstrap-touchspin.js',
    		'public/assets/js/moment-with-locales-290.js',
    		'public/assets/js/plugins/bootstrap/daterangepicker.js',
    		'public/assets/js/default/tabla-dinamica.jquery.js',
    		'public/assets/js/default/toast.controller.js',

    	));

    	$breadcrumb = array(
    		"titulo" => $titulo_formulario,
    	);

    	$this->template->agregar_titulo_header('Descuentos');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar();
    }

    /**
     * Cargar Vista Parcial de Formulario de Descuento
     *
     * @return void
     */
    public function formulario_descuento()
    {
    	$this->assets->agregar_js(array(
    		'public/assets/js/plugins/jquery/fileupload/jquery.fileupload.js',
    		'public/assets/js/modules/descuentos/formulario.controller.js'
    	));

    	$this->template->vista_parcial(array (
    		'descuentos',
    		'formulario'
    	));
    }

    function ajax_seleccionar_descuento()
    {
    	$descuento_id =  $this->input->post('id', true);

    	if(empty($descuento_id)){
    		return false;
    	}

    	$descuento =  Descuentos_orm::where("id", $descuento_id)->where("empresa_id", $this->empresa_id)->get()->toArray();

    	if(!empty($descuento)){
    		$descuento = $descuento[0];
    		unset($descuento["uuid_descuento"]);

    		if(!empty($descuento["fecha_inicio"])){
    			$descuento["fecha_inicio"] = date("d/m/Y", strtotime($descuento["fecha_inicio"]));
    		}
    	}

    	echo json_encode($descuento);
    	exit;
    }

    function ajax_estado_cuenta()
    {
    $id_descuento =  $this->input->post('id_descuento', true);
    $fecha_desde =  $this->input->post('fecha_desde', true);
    $fecha_hasta =  $this->input->post('fecha_hasta', true);

    if(empty($id_descuento)){
    		return false;
    	}

    if(!empty($id_descuento)){
    		$clause["descuento_id"] = $id_descuento;
    	}
    	if( !empty($fecha_desde)){
    		$fecha_desde = str_replace('/', '-', $fecha_desde);
    		$fecha_inicio = date("Y-m-d H:i:s", strtotime($fecha_desde));
    		$clause["fecha_creacion"] = array(">=", $fecha_inicio);
    	}
    	if( !empty($fecha_hasta)){
    		$fecha_hasta = str_replace('/', '-', $fecha_hasta);
    		$fecha_fin = date("Y-m-d 23:59:59", strtotime($fecha_hasta));
    		$clause["fecha_creacion@"] = array('<=', $fecha_fin);
    	}

    $descuentos = Pagadas_descuentos_orm::estados($clause)->toArray();

    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($descuentos))->_display();
    exit;
    }

    public function ajax_guardar_descuento()
    {

    	/**
    	 * Inicializar Transaccion
    	 */
    	Capsule::beginTransaction();

    	try {

    		$descuento_id		= $this->input->post('descuento_id', true);
    		$colaborador_id 	= $this->input->post('colaborador_id', true);
    		$cuenta_pasivo_id 	= $this->input->post('cuenta_pasivo_id', true);
    		$fecha_inicio 		= $this->input->post('fecha_inicio', true);
    		$fecha_inicio		= !empty($fecha_inicio) ? str_replace('/', '-', $fecha_inicio) : "";
    		$fecha_inicio 		= !empty($fecha_inicio) ? date("Y-m-d", strtotime($fecha_inicio)) : "";
    		$tipo_descuento_id 	= $this->input->post('tipo_descuento_id', true);
    		$acreedor_id 		= $this->input->post('acreedor_id', true);
    		$ciclo_id 			= $this->input->post('ciclo_id', true);
    		$monto_total 		= $this->input->post('monto_total', true);
    		$monto_ciclo 		= $this->input->post('monto_ciclo', true);
    		$detalle 			= $this->input->post('detalle', true);
    		$referencia 		= $this->input->post('referencia', true);
    		$estado_id 			= $this->input->post('estado_id', true);
    		$descuento_diciembre = $this->input->post('descuento_diciembre', true);
    		$descuento_diciembre = $descuento_diciembre == true ? 1 : 0;
    		$carta_descuento = $this->input->post('carta_descuento', true);
    		$carta_descuento = $carta_descuento == true ? 1 : 0;
    		$porcentaje_capacidad = $this->input->post('porcentaje_capacidad', true);

    		//Verificar si existe $descuento_id
    		//$descuento = Descuentos_orm::find($descuento_id);
            $descuento = $this->descuentoRep->find($descuento_id);

    		if(!empty($descuento))
    		{
    			$descuento->empresa_id 			= $this->empresa_id;
    			$descuento->plan_contable_id	= $cuenta_pasivo_id;
    			$descuento->tipo_descuento_id 	= $tipo_descuento_id;
          $descuento->no_referencia       = $referencia;
    			$descuento->acreedor_id 		= $acreedor_id;
    			$descuento->ciclo_id 			= $ciclo_id;
          $descuento->estado_id           = $estado_id;
    			$descuento->monto_adeudado 		= $monto_total;
    			$descuento->monto_ciclo 		= $monto_ciclo;
    			$descuento->detalle 			= $detalle;
    			$descuento->fecha_inicio 		= $fecha_inicio;
    			$descuento->descuento_diciembre = $descuento_diciembre;
          $descuento->carta_descuento     = $carta_descuento;
    			$descuento->porcentaje_capacidad = $porcentaje_capacidad;
    			$descuento->creado_por 			= $this->usuario_id;
    			$descuento->save();

    		}else{

    			$fieldset = array(
    				"empresa_id" 		=> $this->empresa_id,
    				"colaborador_id" 	=> $colaborador_id,
    				"plan_contable_id" 	=> $cuenta_pasivo_id,
    				"tipo_descuento_id" => $tipo_descuento_id,
                    "no_referencia"     => $referencia,
    				"acreedor_id" 		=> $acreedor_id,
                     "estado_id"        => $estado_id,
    				"ciclo_id" 			=> $ciclo_id,
    				"monto_inicial"	=> $monto_total,
    				"monto_adeudado"	=> $monto_total,
    				"monto_ciclo"		=> $monto_ciclo,
    				"detalle" 			=> $detalle,
    				"fecha_inicio" 		=> $fecha_inicio,
    				"descuento_diciembre"   => $descuento_diciembre,
                    "carta_descuento"       => $carta_descuento,
    				"porcentaje_capacidad"  => $porcentaje_capacidad,
    				"creado_por" 			=> $this->usuario_id,
                    "codigo"                => Capsule::raw("CODIGO_DESCUENTOS('DCT', ". $this->empresa_id .")"),
                    "uuid_descuento"        => Capsule::raw("ORDER_UUID(uuid())")
    			);
     			//--------------------
    			// Guardar Descuento
    			//--------------------
    			$descuento = Descuentos_orm::create($fieldset);
    		}

    		//--------------------
    		// Subir documento
    		//--------------------
    		if(!empty($_FILES))
    		{
	    		list($modulo_folder, $empresa_folder, $archivo_ruta) = $this->verificar_carpeta_upload();

	    		$config = new \Flow\Config(array(
	    			'tempDir' => $modulo_folder
	    		));

	    		//Inicializar Flow
	    		$request = new \Flow\Request();

	    		//Armar Nomre de archivo corto.
	    		$filename = $this->input->post('flowFilename', true);
	    		$extension = pathinfo($filename, PATHINFO_EXTENSION);
	    		$file_name = "desc-". rand().time() . "." . $extension;

	    		foreach ($_FILES AS $field => $_FILE)
	    		{
	    			$filename = $_FILE["name"];
	    			$extension = pathinfo($filename, PATHINFO_EXTENSION);
	    			$file_name = "desc-". rand().time() . "." . $extension;

	    			//Subir Archivo
	    			if(move_uploaded_file($_FILE["tmp_name"], $empresa_folder . '/' . $file_name)) {

	    				$descuento = Descuentos_orm::find($descuento->id);
		    			$descuento->archivo_ruta = $archivo_ruta;
		    			$descuento->archivo_nombre = $file_name;
		    			$descuento->save();

	    			} else{
	    				log_message("error", "MODULO: ". __METHOD__ .", Linea: ". __LINE__ ." --> No se pudo subir el $field.\r\n");
	    			}
	    		}
    		}

    	} catch(ValidationException $e){

    		// Rollback
    		Capsule::rollback();

    		log_message("error", "MODULO: ". __METHOD__ .", Linea: ". __LINE__ ." --> ". $e->getMessage().".\r\n");

    		echo json_encode(array(
    			"guardado" => false,
    			"mensaje" => "Hubo un error tratando de ". (!empty($descuento_id) ? "actualizar" : "guardar") ." el descuento."
    		));
    		exit;
    	}

    	// If we reach here, then
    	// data is valid and working.
    	// Commit the queries!
    	Capsule::commit();

    	if(empty($descuento_id)){
    		$this->session->set_flashdata('mensaje', "Se ha guardado el descuento satisfactoriamente.");
    		$this->session->set_flashdata('seccion-accordion', "descuentos-seccion");
    	}

    	echo json_encode(array(
    		"guardado" => true,
    		"mensaje" => "Se ha ". (!empty($descuento_id) ? "actualizado" : "guardado") ." el descuento satisfactoriamente."
    	));
    	exit;
    }

    /**
     * Calcular capacidad de endeudamiento
     * de todos los descuentos o de aalgun
     * descuento espcifico del Colaborador.
     *
     * @return array
     */
    private function calcular_capacidad_endeudamiento($colaborador_id=NULL, $monto=NULL)
    {
    	if(empty($colaborador_id)){
    		return false;
    	}

    	//Seleccionar monto total de descuentos aplicados al colaborador
    	if($monto==NULL){
    		$descuentos = Descuentos_orm::where("colaborador_id", $colaborador_id)->where("empresa_id", $this->empresa_id)->get(array(Capsule::raw("SUM(monto_adeudado) AS total")))->toArray();
    		$descuentos_total = !empty($descuentos) ? $descuentos[0]["total"] : 0;
    	}else{
    		$descuentos_total = $monto;
    	}

    	//Seleccionar salario del colaborador (mensual/hora)
    	$colaboradorINFO = Colaboradores_orm::where("id", $colaborador_id)->get(array("id", "tipo_salario", "salario_mensual", "rata_hora"))->toArray();
    	$salario_mensual = !empty($colaboradorINFO[0]["salario_mensual"]) ? $colaboradorINFO[0]["salario_mensual"] : 0;
    	$rata_hora = !empty($colaboradorINFO[0]["rata_hora"]) ? $colaboradorINFO[0]["rata_hora"] : 0;

    	//Verificar si existe tipo de salario
    	if(empty($colaboradorINFO[0]["tipo_salario"])){
    		return array(
    			"completo" => false, //Para saber si el usuario tiene sus datos completos o no.
    			"campos" => array(
    				"- &Aacute;rea de negocio",
    				"- Cargo"
    			)
    		);
    	}

    	//Verificar que tipo de salrio tiene el colaborador
    	//y calcular salario mensual
    	if(preg_match("/mensual/i", $colaboradorINFO[0]["tipo_salario"])){

    		//Capacidad Endeudamiento
    		$capacidad_endeudamiento = ($monto==NULL ? 100 : 0) - ($descuentos_total / $salario_mensual * 100);

    	}else{

    		//Calcular Salario Mensual
    		$salario_mensual = $rata_hora * 208;

    		//Capacidad Endeudamiento
    		$capacidad_endeudamiento = ($monto==NULL ? 100 : 0) - ($descuentos_total / $salario_mensual * 100);
    	}

    	return array(
    		"completo" => true, //Para saber si el usuario tiene sus datos completos o no.
    		"capacidad" => number_format(abs($capacidad_endeudamiento), 0, '', '')
    	);
    }

    /**
     * Calcular capacidad de endeudamiento
     * de un colaborador.
     */
    public function ajax_calcular_capacidad_endeudamiento()
    {
    	$response = new stdClass();
    	$capacidad_endeudamiento = "";
    	$colaborador_id =  $this->input->post('colaborador_id', true);

    	if(empty($colaborador_id)){
    		return false;
    	}

    	$capacidad = $this->calcular_capacidad_endeudamiento($colaborador_id);

    	$response->result = $capacidad;
    	$json = json_encode($response);
    	echo $json;
    	exit;
    }

    /**
     * Verificar si existe la carpeta
     * para subir archivos de este modulo.
     *
     * @return array
     */
    private function verificar_carpeta_upload()
    {
    	$modulo_folder = $this->upload_folder . trim($this->nombre_modulo);
    	$empresa_folder = $modulo_folder ."/". $this->empresa_id;
    	$archivo_ruta = "public/uploads/" . trim($this->nombre_modulo) ."/". $this->empresa_id;

    	//Verificar si existe la carpeta
    	//del modulo de colaboradores en uploads
    	if (!file_exists($modulo_folder)) {
    		try{
    			mkdir($modulo_folder, 0777, true);
    		} catch (Exception $e) {
    			log_message("error", "MODULO:  ". __METHOD__ .", Linea: ". __LINE__ ." --> ". $e->getMessage().".\r\n");
    		}
    	}

    	//Verificar si existe la carpeta
    	//de la empresa existe, dentro
    	//del modulo.
    	if (!file_exists($empresa_folder)) {
    		try{
    			mkdir($empresa_folder, 0777, true);
    		} catch (Exception $e) {
    			log_message("error", "MODULO:  ". __METHOD__ .", Linea: ". __LINE__ ." --> ". $e->getMessage().".\r\n");
    		}
    	}

    	return array(
    		$modulo_folder,
    		$empresa_folder,
    		$archivo_ruta
    	);
    }

    function ocultoformulariocomentarios() {

        $data = array();

        $this->assets->agregar_js(array(
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/modules/descuentos/vue.comentario.js',
            'public/assets/js/modules/descuentos/formulario_comentario.js'
        ));

        $this->load->view('formulario_comentarios');
        $this->load->view('comentarios');

    }

    function ajax_guardar_comentario() {

        if(!$this->input->is_ajax_request()){
            return false;
        }
        $model_id   = $this->input->post('modelId');
        $comentario = $this->input->post('comentario');
        $comentario = ['comentario'=>$comentario,'usuario_id'=>$this->usuario_id];
        $descuento = $this->descuentoRep->agregarComentario($model_id, $comentario);
        $descuento->load('comentario_timeline');

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($descuento->comentario_timeline->toArray()))->_display();
        exit;
    }

}

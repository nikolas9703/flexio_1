<?php
/**
 * Presupuesto
 *
 * Modulo para administrar la creacion, edicion de ajustes
 *
 * @package    PensaApp
 * @subpackage Controller
 * @category   Controllers
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  10/16/2015
 */

use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use League\Csv\Writer as Writer;
use League\Csv\Writer as Reader;
use Flexio\Library\Util\FormRequest;
use Flexio\Modulo\CentrosContables\Repository\CentrosContablesRepository;
use Flexio\Modulo\Presupuesto\Services\ComponenteTabla;
use Flexio\Modulo\Presupuesto\Services\ComponenteTablaVer;
use Flexio\Modulo\Presupuesto\Services\ComponenteTablaDetalle;
use Flexio\Modulo\Presupuesto\Repository\PresupuestoRepository;

class Presupuesto extends CRM_Controller
{
  private $id_empresa;
  private $usuario_id;
  private $empresaObj;
  private $presupuestoRepo;
  private $centroContableRepo;
  private $presupuestoJqGrid;

  public function __construct()
  {
      parent::__construct();
      $this->load->model("usuarios/Empresa_orm");
      $this->load->model("Presupuesto_catalogo_orm");
      $this->load->model("Centro_cuenta_presupuesto_orm");
      $this->load->model("inventarios/Items_orm");

      //Cargar Clase Util de Base de Datos
      $this->load->dbutil();

      $uuid_empresa       = $this->session->userdata('uuid_empresa');
      $this->empresaObj  = Empresa_orm::findByUuid($uuid_empresa);
      $this->usuario_id   = $this->session->userdata("id_usuario");
      $this->id_empresa   = $this->empresaObj->id;
      Carbon::setLocale('es');
      setlocale(LC_TIME, 'Spanish');
      $this->centroContableRepo = new CentrosContablesRepository;
      $this->presupuestoJqGrid = new Flexio\Modulo\Presupuesto\Services\JqGrid;
      $this->presupuestoRepo = new PresupuestoRepository;
  }

  function listar(){
    $data = array();
    $this->assets->agregar_css(array(
          'public/assets/css/default/ui/base/jquery-ui.css',
          'public/assets/css/default/ui/base/jquery-ui.theme.css',
          'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
          'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
          'public/assets/css/plugins/jquery/chosen/chosen.min.css',
          'public/assets/css/plugins/bootstrap/bootstrap-tagsinput.css',
          'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
          'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
          'public/assets/js/plugins/jquery/sweetalert/sweetalert.css',
          'public/assets/css/modules/stylesheets/presupuesto.css',
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
          'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
          'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
          'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
          'public/assets/js/plugins/toastr.min.js',
          'public/assets/js/default/formulario.js',
          'public/assets/js/default/jqgrid-toggle-resize.js',
          'public/assets/js/plugins/jquery/sweetalert/sweetalert.min.js',
          'public/assets/js/modules/presupuesto/routes.js',
          /* Archivos js del propio modulo*/
          'public/assets/js/modules/presupuesto/listar.js',
      ));

      //Breadcrum Array
      $breadcrumb = array(
          "titulo"    => '<i class="fa fa-calculator"></i> Presupuesto',
          "ruta" => array(
              0 => array(
                  "nombre" => "presupuesto",
                  "activo" => false
              ),
              1 => array(
                  "nombre" => '<b>Items</b>',
                  "activo" => true
              )
          ),
          "filtro"    => false, //sin vista grid
          "menu"      => array(
            'url' => 'presupuesto/crear',
            'nombre' => "Crear",
            "opciones" => array()
          ),

      );
      $condicion = array('empresa_id'=>$this->empresaObj->id,'estado'=>'Activo','transaccionales'=>true);
      //lista de centros contables
      $centros_contables =    $this->centroContableRepo->get($condicion);
      $data['centros_contables'] = $centros_contables;
      $breadcrumb["menu"]["opciones"]["#exportarListaPresupuesto"] = "Exportar";
      $this->template->agregar_titulo_header('Listado de Presupuesto');
      $this->template->agregar_breadcrumb($breadcrumb);
      $this->template->agregar_contenido($data);
      $this->template->visualizar($breadcrumb);


  }

  function ajax_listar(){
    if(!$this->input->is_ajax_request()){
      return false;
    }

    $centro = $this->input->post('centro');
    $referencia = (string)$this->input->post('referencia');
    $fecha1 = (string)$this->input->post('fecha1');
    $fecha2 = (string)$this->input->post('fecha2');


    list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();

    $clause= array('empresa_id' => $this->empresaObj->id);
    if(!empty($centro) ) $clause['centro_contable_id'] = $centro;
		if(!empty($referencia)) $clause['nombre'] = $referencia;
		if(!empty($fecha1)) $clause['fecha1'] =  Carbon::createFromFormat('m/d/Y', $fecha1)->format('Y-m-d')." 00:00:00";
		if(!empty($fecha2)) $clause['fecha2'] = Carbon::createFromFormat('m/d/Y', $fecha2)->format('Y-m-d')." 23:59:59";

    $count = $this->presupuestoJqGrid->listar($clause)->count();
    list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
    $presupuestos = $this->presupuestoJqGrid->listar($clause ,$sidx, $sord, $limit, $start)->get();
    //Constructing a JSON
    $response = new stdClass();
    $response->page     = $page;
    $response->total    = $total_pages;
    $response->record  = $count;
    $i=0;

   if(!empty($presupuestos->toArray())){
     foreach($presupuestos as $row){
       $hidden_options = "";
       $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row->id .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
       $hidden_options = '<a href="'. base_url('presupuesto/detalle/'. $row->uuid_presupuesto) .'" data-id="'. $row->uuid_presupuesto .'" class="btn btn-block btn-outline btn-success">Ver Presupuesto</a>';
       $hidden_options .= '<a href="'. base_url('presupuesto/ver/'. $row->uuid_presupuesto) .'" data-id="'. $row->uuid_presupuesto .'" class="btn btn-block btn-outline btn-success">Actualizar Presupuesto</a>';
       $hidden_options .= '<a href="javascript:" data-id="'. $row->uuid_presupuesto .'" class="exportarTablaPresupuesto btn btn-block btn-outline btn-success">Exportar Presupuesto</a>';

       $response->rows[$i]["id"] = $row->id;
       $response->rows[$i]["cell"] = array(
        $row->id,
        '<a href="'. base_url('presupuesto/ver/'. $row->uuid_presupuesto) .'" class="link">'.$row->codigo.'</a>',
        $row->centro_contable->nombre,
        $row->nombre,
        $row->fecha_inicio,
        $row->present()->estado_label,
         $link_option,
         $hidden_options
       );
       $i++;
     }

   }
   echo json_encode($response);
   exit;
  }

  function ocultotabla(){
    $this->assets->agregar_js(array(
      'public/assets/js/modules/presupuesto/tabla.js'
    ));

    $this->load->view('tabla');
  }

  function crear(){

      $acceso = 1;
      $mensaje = array();
      if (!$this->auth->has_permission('acceso')) {
          // No, tiene permiso
          $acceso = 0;
          $mensaje = array('estado' => 500, 'mensaje' => ' <b>Usted no cuenta con permiso para esta solicitud</b>', 'clase' => 'alert-danger');
      }
    $this->_css();
    $this->_js();
    $this->assets->agregar_js(array(
      'public/assets/js/modules/presupuesto/routes.js',
      'public/assets/js/modules/presupuesto/tabla_presupuesto.js',
      'public/assets/js/modules/presupuesto/aplicar_formula.js',
      'public/assets/js/modules/presupuesto/totalizador.js',
      'public/assets/js/modules/presupuesto/jqgrid.presupuesto.periodo.js',
      'public/assets/js/modules/presupuesto/vue.crear_formulario.js',
      'public/assets/js/modules/presupuesto/vue.componente.periodo.js',
    ));

    $data=array();
    $breadcrumb = array(
      "titulo" => '<i class="fa fa-calculator"></i> Presupuesto',
    );
    $this->assets->agregar_var_js(array(
        "vista" => 'crear',
        "acceso" => $acceso
    ));
    ///setear, centro,inicio,periodo
    $data['mensaje'] = $mensaje;
    $this->template->agregar_titulo_header('Presupuesto');
    $this->template->agregar_breadcrumb($breadcrumb);
    $this->template->agregar_contenido($data);
    $this->template->visualizar();
  }

  function ocultoformulario($presupuesto=NULL){

    $data=array();
    if(!is_null($presupuesto))$data['info'] = $presupuesto;
    $condicion = array('empresa_id'=>$this->empresaObj->id,'estado'=>'Activo','transaccionales'=>true);
    $periodo = Presupuesto_catalogo_orm::all();
    $meses_array = array();

    //lista de centros contables
    $centros_contables =    $this->centroContableRepo->get($condicion);
    //$mes_inicial = $fecha->month;
    /* buscar la fecha mas antigua del pesupuesto de la empresa, para el total cantidad del meses del año actual
    */

    if(empty($presupuesto)){
        $fecha = Carbon::now()->startOfMonth();
    }else{
      $fecha = Carbon::parse($presupuesto['info']['fecha'])->startOfMonth();
    }
    for($i=0;$i<=11;$i++){

      $fechaObj = $fecha->copy()->addMonths($i);
      array_push($meses_array, array('id'=>$fechaObj->formatLocalized('%m-%Y'),'valor'=> ucfirst($fechaObj->formatLocalized('%B %Y'))));
    }

    $data['centros_contables'] = $centros_contables;
    $data['periodos'] = $periodo;
    $data['inicio'] = $meses_array;
    $this->load->view('formulario', $data);
  }

  //carga los template de los componentes
  function ocultocomponente_presupuesto(){
    $this->load->view('componente_periodo');
  }

  function ocultotimeline(){
    $this->load->view('timeline');
  }

  function ajax_armarPresupuesto(){

    $request = Illuminate\Http\Request::createFromGlobals();

    $datos_presupuesto = $request->all();
    $datos_formulario = FormRequest::data_formulario($datos_presupuesto);
    $datos_formulario['empresa_id'] = $this->empresaObj->id;
    $componente = new ComponenteTabla();
    $response = $componente->generarComponente($datos_formulario);

    echo json_encode($response);
    exit;
  }

   private function _armarJqgripPresupuestoVer($presupuesto=null){
    if(is_null($presupuesto)){
      return [];
    }
    $jqgridPresupuesto = new ComponenteTablaVer($presupuesto);
    return $jqgridPresupuesto->ArmarJqgrid();

  }

  private function _armarJqgripPresupuestoDetalle($presupuesto=null){
   if(is_null($presupuesto)){
     return [];
   }
   $jqgridPresupuesto = new ComponenteTablaDetalle($presupuesto);
   return $jqgridPresupuesto->ArmarJqgrid();

 }

  function guardar()
  {
    if(!empty($_POST)){
      // form http request
      $condicion = ['empresa_id'=>$this->empresaObj->id];
      $codigo = $this->presupuestoRepo->getLastCodigo($condicion);
      $formGuardar = new Flexio\Modulo\Presupuesto\HttpRequest\PresupuestoGuardar($this->empresaObj->id,$codigo,$this->usuario_id);
      try {
        $presupuesto = $formGuardar->procesarGuardar();
      }catch (\Exception $e) {
        log_message('error', $e);
        $presupuesto = null;
      }

      if(!is_null($presupuesto)){
        $mensaje = array('clase' =>'alert-success', 'contenido' =>'<b>¡&Eacute;xito!</b> Se ha guardado correctamente '.$presupuesto->nombre);
      }else{
        $mensaje = array('clase' =>'alert-danger', 'contenido' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
      }
   }else{
     $mensaje = array('clase' =>'alert-warning', 'contenido' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
   }

   $this->session->set_flashdata('mensaje', $mensaje);
   redirect(base_url('presupuesto/listar'));
  }

  function exportar(){

    if(!empty($_POST)){
        $ids =  $this->input->post('ids');
        $id = explode(",",$ids[0]);
        $presupuestos=$this->presupuestoRepo->inId($id);
        $datos_excel= array();
        $i=0;
        foreach($presupuestos as $presupuesto){
          $datos_excel[$i]['codigo'] = $presupuesto->codigo;
          $datos_excel[$i]['centro_contable'] = utf8_decode($presupuesto->centro_contable->nombre);
          $datos_excel[$i]['referencia'] = utf8_decode($presupuesto->nombre);
          $datos_excel[$i]['fecha_inicio'] = $presupuesto->fecha_inicio;
          $datos_excel[$i]['cantidad_meses'] = $presupuesto->cantidad_meses;
          $i++;
        }
         //header("Content-Type: binary/octet-stream");
        //we create the CSV into memory
        $csv = Writer::createFromFileObject(new SplTempFileObject());
        $csv->insertOne(['Codigo', 'Centro Contable', 'Referencia','Fecha Inicio','Meses']);
        $csv->insertAll($datos_excel);

        $csv->output('Presupuesto.csv');
        die;
    }else{
      die;
    }
  }

  function exportar_presupuesto(){

    if(!empty($_POST)){
        $uuid =  $this->input->post('presupuesto_exportar');
        $presupuestos = $this->presupuestoRepo->findByUuid($uuid);

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

  function ver($uuid=null){

    $acceso = 1;
    $mensaje = array();
    $data = array();
    $presupuesto = $this->presupuestoRepo->findByUuid($uuid);
    if(!$this->auth->has_permission('acceso','presupuesto/ver/(:any)') && is_null($presupuesto)){
      // No, tiene permiso
        $acceso = 0;
        $mensaje = array('estado'=>500, 'mensaje'=>' <b>Usted no cuenta con permiso para esta solicitud</b>','clase'=>'alert-danger');
    }
    $this->_css();
    $this->_js();
    $this->assets->agregar_js(array(
      'public/assets/js/modules/presupuesto/routes.js',
      'public/assets/js/modules/presupuesto/tabla_presupuesto_ver.js',
      'public/assets/js/modules/presupuesto/aplicar_formula.js',
      'public/assets/js/modules/presupuesto/totalizador.js',
      'public/assets/js/modules/presupuesto/jqgrid.presupuesto.periodo.js',
      'public/assets/js/modules/presupuesto/vue.componente.periodo.js',
      'public/assets/js/modules/presupuesto/vue.crear_formulario.js'

    ));

    $breadcrumb = array(
      "titulo" => '<i class="fa fa-calculator"></i> Presupuesto: '.$presupuesto->codigo ,
    );

    $presupuesto->load('lista_presupuesto.cuentas');
    $data_presupuestado = $this->_armarJqgripPresupuestoVer($presupuesto);

    $this->assets->agregar_var_js(array(
      "vista" => 'ver',
      "acceso" => $acceso,
      "presupuesto" => $presupuesto,
      "datosTabla" => collect($data_presupuestado)
    ));
      $data['info'] = $presupuesto;
      $this->template->agregar_titulo_header('Presupuesto');
      $this->template->agregar_breadcrumb($breadcrumb);
      $this->template->agregar_contenido($data);
      $this->template->visualizar();

  }


  function detalle($uuid = null){

      $acceso = 1;
      $mensaje = array();
      $data = array();
      $presupuesto = $this->presupuestoRepo->findByUuid($uuid);
      if(!$this->auth->has_permission('acceso','presupuesto/detalle/(:any)') && is_null($presupuesto)){
        // No, tiene permiso
          $acceso = 0;
          $mensaje = array('estado'=>500, 'mensaje'=>' <b>Usted no cuenta con permiso para esta solicitud</b>','clase'=>'alert-danger');
      }
      $this->_css();
      $this->_js();
      $this->assets->agregar_js(array(
        'public/assets/js/modules/presupuesto/routes.js',
        'public/assets/js/modules/presupuesto/tabla_presupuesto_ver.js',
        'public/assets/js/modules/presupuesto/aplicar_formula.js',
        'public/assets/js/modules/presupuesto/totalizador.js',
        'public/assets/js/modules/presupuesto/jqgrid.presupuesto.periodo.js',
        'public/assets/js/modules/presupuesto/vue.componente.periodo.js',
        'public/assets/js/modules/presupuesto/vue.crear_formulario.js',
        'public/assets/js/modules/presupuesto/ver_historial.js'

      ));

      $breadcrumb = array(
        "titulo" => '<i class="fa fa-calculator"></i> Presupuesto: '.$presupuesto->codigo,
        "historial" => true
      );

      $presupuesto->load('lista_presupuesto.cuentas');
      $data_presupuestado = $this->_armarJqgripPresupuestoDetalle($presupuesto);

      $this->assets->agregar_var_js(array(
        "vista" => 'detalle',
        "acceso" => $acceso,
        "presupuesto" => $presupuesto,
        "datosTabla" => collect($data_presupuestado)
      ));
      $data['id'] = $presupuesto->id;
      $data['info'] = $presupuesto;
      $this->template->agregar_titulo_header('Presupuesto');
      $this->template->agregar_breadcrumb($breadcrumb);
      $this->template->agregar_contenido($data);
      $this->template->visualizar();
  }

  function historial(){
      $acceso = 1;
      $mensaje = array();
      $data = array();
      $id = $this->input->post('id');
      $presupuesto = $this->presupuestoRepo->find($id);
      if(!$this->auth->has_permission('acceso','presupuesto/historial') && is_null($presupuesto)){
        // No, tiene permiso
          $acceso = 0;
          $mensaje = array('estado'=>500, 'mensaje'=>' <b>Usted no cuenta con permiso para esta solicitud</b>','clase'=>'alert-danger');
      }

      $this->_css();
      $this->_js();
      $this->assets->agregar_js(array(
          'public/assets/js/modules/presupuesto/vue.componente.timeline.js',
          'public/assets/js/modules/presupuesto/vue.timeline.js',
      ));

      $breadcrumb = array(
        "titulo" => '<i class="fa fa-calculator"></i> Historial de presupuesto: '.$presupuesto->codigo,
      );

      $presupuesto->load('historial');

      $this->assets->agregar_var_js(array(
        "timeline" => $presupuesto,
      ));
      $this->template->agregar_titulo_header('Presupuesto');
      $this->template->agregar_breadcrumb($breadcrumb);
      $this->template->agregar_contenido($data);
      $this->template->visualizar();
  }


  private function _js(){
    $this->assets->agregar_js(array(
      'public/assets/js/default/jquery-ui.min.js',
      'public/assets/js/plugins/jquery/jquery.sticky.js',
      'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
      'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
      'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
      'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
      'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
      'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
      'public/assets/js/default/accounting.min.js',
      'public/assets/js/default/jquery.inputmask.bundle.min.js',
      'public/assets/js/plugins/jquery/sweetalert/sweetalert.min.js',
      'public/assets/js/moment-with-locales-290.js',
      'public/assets/js/default/es.datepicker.js',
      'public/assets/js/default/vue/directives/datepicker2.js',
      'public/assets/js/default/vue-validator.min.js',
    ));
  }

  private function _css(){
    $this->assets->agregar_css(array(
      'public/assets/css/default/ui/base/jquery-ui.css',
      'public/assets/css/default/ui/base/jquery-ui.theme.css',
      'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
      'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
      'public/assets/js/plugins/jquery/sweetalert/sweetalert.css',
      'public/assets/css/modules/stylesheets/presupuesto.css',
    ));
  }

}

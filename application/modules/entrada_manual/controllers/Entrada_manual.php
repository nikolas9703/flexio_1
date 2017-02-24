<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Erp
 * @subpackage Controller
 * @category   Entrada_manual
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  10/22/2015
 */

use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use League\Csv\Writer as Writer;
use Flexio\Modulo\EntradaManuales\HttpRequest\EntradaManualRequest;
use Flexio\Modulo\EntradaManuales\Repository\EntradaManualRepository;

class Entrada_manual extends CRM_Controller
{
  protected $entradaManual;
  protected $empresa_id;
  protected $empresaObj;


  function __construct(){
    parent::__construct();
    $this->load->model('usuarios/Empresa_orm');
    $this->load->model('usuarios/Usuario_orm');
    $this->load->model('usuarios/Relacion_orm');
    $this->load->model('Entrada_orm');
    $this->load->model('Transaccion_orm');
    $this->load->model('Comentario_orm');
    Carbon::setLocale('es');
    setlocale(LC_TIME, 'Spanish');
    //Cargar Clase Util de Base de Datos
    //$this->load->dbutil();
    $uuid_empresa = $this->session->userdata('uuid_empresa');
    $empresaObj  = new Buscar(new Empresa_orm,'uuid_empresa');
  	$this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
  	$this->empresa_id = $this->empresaObj->id;
    $this->entradaManual = new EntradaManualRepository;
  }

  function listar(){
    $data=array();
    $this->assets->agregar_css(array(
      'public/assets/css/default/ui/base/jquery-ui.css',
      'public/assets/css/default/ui/base/jquery-ui.theme.css',
      'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
      'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
      'public/assets/css/plugins/bootstrap/select2.min.css',
      'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
    ));
    $this->assets->agregar_js(array(
      'public/assets/js/plugins/jquery/jquery.progresstimer.min.js',
      'public/assets/js/modules/entrada_manual/routes.js',
      'public/assets/js/plugins/bootstrap/select2/select2.min.js',
      'public/assets/js/plugins/bootstrap/select2/es.js',
      'public/assets/js/modules/entrada_manual/listar.js',
    ));
    $menuOpciones = array(
      //"#activarLnk" => "Habilitar",
      //"#inactivarLnk" => "Deshabilitar",
      "#exportarEntradasList" => '<i class="fa fa-print"></i> Imprimir',
    );
    //Breadcrum Array

    $uuid_empresa = $this->session->userdata('uuid_empresa');
    $empresa = Empresa_orm::findByUuid($uuid_empresa);
    //dd($empresa->toArray());
    $breadcrumb = array(
      "titulo" => '<i class="fa fa-calculator"></i> Entrada Manual',
      "filtro" => false,
      "menu" => array(
         "nombre" => "Crear",
         "url"	 => 'entrada_manual/crear',
        "opciones" => $menuOpciones
      )
    );

    $centrosContableObj = new Flexio\Modulo\CentrosContables\Repository\CentrosContablesRepository;
    $centros_contables = $centrosContableObj->get(['empresa_id'=>$this->empresa_id,'transaccionales'=>true]);
    $data['centros_contable'] = $centros_contables;
    $this->template->agregar_titulo_header('Entrada Manual');
    $this->template->agregar_breadcrumb($breadcrumb);
    $this->template->agregar_contenido($data);
    $this->template->visualizar($breadcrumb);
  }

  function ocultotabla(){
    $this->assets->agregar_js(array(
      'public/assets/js/modules/entrada_manual/subgridPanel.js',
      'public/assets/js/modules/entrada_manual/tabla.js'
    ));

    $this->load->view('tabla');
  }

  public function ajax_listar(){
    if(!$this->input->is_ajax_request()){
      return false;
    }
    $uuid_empresa = $this->session->userdata('uuid_empresa');
    $empresa = Empresa_orm::findByUuid($uuid_empresa);

    $clause = [];
    $clause = ['empresa' => $empresa->id];

    $jqgrid = new Flexio\Modulo\EntradaManuales\Services\EntradaManualJqgrid;
    $response = $jqgrid->listar($clause);
    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))->_display();
    exit;
  }

  public function exportar()
    {
    	if(empty($_POST)){
    		die();
    	}
        $request = Illuminate\Http\Request::capture();
        $campo = $request->only('fecha_min','fecha_max', 'centro_contable');

        $campo['empresa'] = $this->empresa_id;
        $campo = array_filter($campo);
        $entradaManualGets = new Flexio\Modulo\EntradaManuales\Repository\RepositorioEntradaManual;
        $entradaManual = $entradaManualGets->conFiltro($campo)
                                           ->sort('codigo')
                                           ->fetch();
        $entradaManual->load('transacciones');

        $templatePdf =  $this->load->view('pdf/entrada_manual',['entradaManual'=>$entradaManual,'empresa'=>$this->empresaObj->nombre],true);
        $nombre_pdf = "Entrada_manuales-".time();
        (new  Flexio\Provider\FlexioPdf)->render($templatePdf,$nombre_pdf);
        die;

        /*dd($campo,$entradaManual->toArray());
        $csv = $entradaManualExcell->crear($campo);



		$csv = array();
		$clause = array("id" => $id);

		$entradas = $this->entradaManual->exportar($clause);


		if(empty($entradas)){
			return false;
		}

		$i=0;
		foreach ($entradas AS $row)
		{
                    //dd($row->codigo);
			$csvdata[$i]['no_entrada'] = utf8_decode(Util::verificar_valor($row->codigo));
			$csvdata[$i]['narracion'] = utf8_decode(Util::verificar_valor($row->nombre));
			$csvdata[$i]["fecha_entrada"] = utf8_decode(Carbon::createFromFormat('Y-m-d H:i:s', Util::verificar_valor($row->created_at))->format('d/m/Y'));
                        $csvdata[$i]["debito"] = utf8_decode(Util::verificar_valor(number_format(($row->transacciones->sum('debito')), 2, '.', ',')));
                        $csvdata[$i]["credito"] = utf8_decode(Util::verificar_valor(number_format(($row->transacciones->sum('credito')), 2, '.', ',')));

			$i++;
		}

		//we create the CSV into memory
		$csv = Writer::createFromFileObject(new SplTempFileObject());
		$csv->insertOne([
			'No. de Entrada',
			'Narracion',
			'Fecha de entrada',
			'Debito',
			'Credito'
		]);
		$csv->insertAll($csvdata);
		$csv->output("entradasManuales-". date('ymd') .".csv");*/
		die;
    }

  public function ocultoformulario($data=NULL)
  {
    $this->assets->agregar_js(array(
      'public/assets/js/modules/entrada_manual/crear.js'
    ));
    $this->load->view('formulario', $data);
  }

  function crear($entrada_uuid = NULL){
    $this->assets->agregar_css(array(
      'public/assets/css/default/ui/base/jquery-ui.css',
      'public/assets/css/default/ui/base/jquery-ui.theme.css',
      'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
      'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
      'public/assets/css/plugins/jquery/chosen/chosen.min.css',
      'public/assets/css/modules/stylesheets/entrada_manual_crear.css',
    ));
    $this->assets->agregar_js(array(
      //'public/assets/js/default/jquery-ui.min.js',
      'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
      'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
      'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
      'public/assets/js/plugins/jquery/combodate/combodate.js',
      'public/assets/js/plugins/jquery/combodate/momentjs.js',
      'public/assets/js/plugins/jquery/chosen.jquery.min.js',
      'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
      'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
      'public/assets/js/moment-with-locales-290.js',
      'public/assets/js/plugins/bootstrap/daterangepicker.js',
      'public/assets/js/modules/entrada_manual/t.dinamica.js',
      'public/assets/js/default/formulario.js',
      'public/assets/js/modules/entrada_manual/routes.js'
    ));
    $data=array();
    $breadcrumb = array(
      "titulo" => '<i class="fa fa-calculator"></i> Crear Entrada Manual',

    );

    if(!is_null($entrada_uuid)){
      //$entrada = Entrada_orm::findByUuid($entrada_uuid);
      $entrada =  $this->entradaManual->findByUuid($entrada_uuid);
      $transacciones = $entrada->transacciones;
      $data['info'] = $entrada;
      $data['info']['transacciones']= $transacciones;
      $breadcrumb = array(
      "titulo" => '<i class="fa fa-calculator"></i> Entrada Manual: ' . $entrada->codigo,
    );
      $this->assets->agregar_var_js(array(
     "entrada_id" => $entrada->id
        ));
    }

    $this->template->agregar_titulo_header('Entrada Manual');
    $this->template->agregar_breadcrumb($breadcrumb);
    $this->template->agregar_contenido($data);
    $this->template->visualizar();

  }

  function ajax_listar_transacciones(){
    if(!$this->input->is_ajax_request()){
      return false;
    }
    $uuid_empresa = $this->session->userdata('uuid_empresa');
    $empresa = Empresa_orm::findByUuid($uuid_empresa);

    $clause = [];
    $clause = ['empresa' => $this->empresa_id];

    $jqgrid = new Flexio\Modulo\EntradaManuales\Services\TransaccionJqgrid;
    $response = $jqgrid->listar($clause);
    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))->_display();
    exit;
  }

  function ajax_guardar_entada_manual(){
    if(!$this->input->is_ajax_request()){
      return false;
    }
    $response = array();
    //Obtener el id_empresa de session
    $uuid_empresa = $this->session->userdata('uuid_empresa');
    $empresa = Empresa_orm::findByUuid($uuid_empresa);
    //$array_entrada = Util::set_fieldset("campo");
  //  $array_entrada['empresa_id'] = $empresa->id;
    $codigo_entrada = Entrada_orm::where('empresa_id','=',$empresa->id)->count();
    $codigo_transaccion = Transaccion_orm::where(function($query) use ($empresa){
        $query->where('empresa_id',$empresa->id);
        $query->where('codigo','like',"TR%");
    })->count();
    $request = new EntradaManualRequest;
    list($entrada, $transaciones) = $request->datos($empresa->id, $codigo_entrada,$codigo_transaccion);
    Capsule::beginTransaction();
    try {
        $entrada_manual = $request->save($entrada, $transaciones);
        Capsule::commit();
    $uuid_empresa = $this->session->userdata('uuid_empresa');
    $empresa = Empresa_orm::findByUuid($uuid_empresa);
    $uuid_usuario = $this->session->userdata('huuid_usuario');
    $usuario = Usuario_orm::findByUuid($uuid_usuario);
    $datos = array();
    $datos['comentario'] = $_POST['campo']['comentarios'];
    $datos['entrada_id'] = $entrada_manual->id;
    $datos['usuario_id'] = $usuario->id;
    $datos['empresa_id'] = $empresa->id;
    Comentario_orm::create($datos);
     }catch(\Exception $e){
        log_message('error', __METHOD__." ->". ", Linea: ". __LINE__ ." --> ". $e->getMessage()."\r\n");
        Capsule::rollback();
    }
    if(!is_null($entrada_manual)){
      $model = $entrada_manual->fresh();
      $mensaje = array('clase' =>'alert-success', 'contenido' =>'<b>¡&Eacute;xito!</b> Se ha guardado correctamente '.$entrada_manual->nombre);
      $this->session->set_flashdata('mensaje', $mensaje);
      $response = array('estado'=>200, 'mensaje'=>'<b>¡&Eacute;xito!</b> Se ha guardado correctamente '.$entrada_manual->nombre, 'redireccionar' => base_url('entrada_manual/crear/'.$model->uuid_entrada));
    }else{
      $response = array('estado'=>500, 'mensaje'=>'<b>¡Error! Su solicitud no fue procesada</b> ');
    }
    echo json_encode($response);
  	exit;
  }

  function ocultoformulariocomentario($data=NULL){
     $this->assets->agregar_js(array(
       'public/assets/js/plugins/ckeditor/ckeditor.js',
       'public/assets/js/plugins/ckeditor/adapters/jquery.js',
       'public/assets/js/modules/entrada_manual/controller_comentario.js'
     ));

    $this->load->view('formulario_comentario', $data);
  }
  function ajax_getComentario(){
    $uuid_empresa = $this->session->userdata('uuid_empresa');
    $empresa = Empresa_orm::findByUuid($uuid_empresa);
    $entrada_id = $this->input->post('entrada_id');
    $condicion = array('entrada_id'=>$entrada_id);
    $comentarios = Comentario_orm::where($condicion)->orderBy('created_at','DESC')->get();

    if(!is_null($comentarios)){
      $response =  $comentarios->toArray();
    }else{
      $response = array();
    }
    //$a = Comentario_orm::find(1);

    //print_r($comentarios->toArray());
    echo json_encode($response);
  	exit;
  }

  function ajax_postComentario(){
    $datos = array();
    $uuid_empresa = $this->session->userdata('uuid_empresa');
    $empresa = Empresa_orm::findByUuid($uuid_empresa);
    $uuid_usuario = $this->session->userdata('huuid_usuario');
    $usuario = Usuario_orm::findByUuid($uuid_usuario);
    $datos['comentario'] = $this->input->post('comentario');
    $datos['entrada_id'] = $this->input->post('entrada_id');
    $datos['usuario_id'] = $usuario->id;
    $datos['empresa_id'] = $empresa->id;
    $comentario = Comentario_orm::create($datos);

    $usuario->comentario()->save($comentario->fresh());
    $response = array();
    if(!is_null($comentario)){
      $condicion = array('entrada_id'=>$datos['entrada_id']);
      $response = array('estado' => 200, 'mensaje'=>'<b>¡&Eacute;xito!</b> Se ha guardado correctamente','comentario'=>$comentario->fresh()->toArray());
    }else{
      $response = array('estado'=> 500, 'mensaje'=>'<b>¡Error!</b> Su solicitud no fue procesada ');
    }
    echo json_encode($response);
  	exit;
  }

}

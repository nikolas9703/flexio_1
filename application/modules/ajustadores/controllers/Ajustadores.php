<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Flexio
 * @subpackage Controller
 * @category   Ajustadores
 * @author     Pensanomica Team
 * @link       http://www.pensanomica.com
 * @copyright  04/18/2016
 */

use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use League\Csv\Writer as Writer;
use Flexio\Modulo\Ajustadores\Repository\AjustadoresRepository;
use Flexio\Modulo\Ajustadores\Repository\ContactoRepository;
use Flexio\Modulo\Ajustadores\Models\Ajustadores as AjustadoresModel;
use Flexio\Modulo\Ajustadores\Models\AjustadoresContacto as ContactoModel;
use Flexio\Library\Util\FormRequest;

class Ajustadores extends CRM_Controller
{
  protected $catalogo;
  protected $ajustadoresRepository;
  protected $contactoRepository;
  protected $ajustadoresModel;
  protected $contactoModel;

  function __construct() {
    parent::__construct();
    $this->load->model('usuarios/Usuario_orm');
    $this->load->model('usuarios/Empresa_orm');
    $this->load->model('usuarios/Roles_usuarios_orm');
    $this->load->model('roles/Rol_orm');
    $this->load->model('clientes/Cliente_orm');    
    $this->load->model('modulos/Catalogos_orm');    
    Carbon::setLocale('es');
    setlocale(LC_TIME, 'Spanish');
    $uuid_empresa = $this->session->userdata('uuid_empresa');
    $empresaObj  = new Buscar(new Empresa_orm,'uuid_empresa');
    $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
    $this->empresa_id   = $this->empresaObj->id;
    $this->ajustadoresRepository = new AjustadoresRepository;
    $this->contactoRepository = new ContactoRepository;
    $this->ajustadoresModel = new AjustadoresModel;
    $this->contactoModel = new ContactoModel;
  }

  public function listar() {
    if (! $this->auth->has_permission ( 'acceso' )) {
      // No, tiene permiso, redireccionarlo.
      redirect ( '/' );
    }

      $data = array();
      $this->_css();
      $this->_js();
      $this->assets->agregar_js(array(
        'public/assets/js/modules/ajustadores/listar.js',        
        'public/assets/js/modules/ajustadores/tabla.js'
      ));

      $breadcrumb = array( "titulo" => '<i class="fa fa-archive"></i> Ajustadores',
            "menu" => array(
            "nombre" => "Crear",
            "url"	 => "ajustadores/crear",
            "opciones" => array()
          ),
            "ruta" => array(
                0 => array("nombre" => "Seguros", "url" => "#",  "activo" => false),
                1 => array("nombre" => "<b>Ajustadores</b>", "activo" => true)                
            )
     );
      

     $mensaje = !empty($this->session->flashdata('mensaje')) ? json_encode(array('estado' => 200, 'mensaje' => $this->session->flashdata('mensaje'))) : '';
        $this->assets->agregar_var_js(array(
            "toast_mensaje" => $mensaje
        ));
    
      $breadcrumb["menu"]["opciones"]["#exportarAjustadores"] = "Exportar";
      $this->template->agregar_titulo_header('Listado de Ajustadores');
      $this->template->agregar_breadcrumb($breadcrumb);
      $this->template->agregar_contenido($data);
      $this->template->visualizar($breadcrumb);
  }

  public function ocultotabla() {
    $this->assets->agregar_js(array(
      'public/assets/js/modules/ajustadores/tabla.js'
    ));
    $this->load->view('tabla');
  }
  
  public function ocultotablacontacto() {
    $this->assets->agregar_js(array(
      'public/assets/js/modules/ajustadores/tablacontacto.js'
    ));
    $this->load->view('tablacontacto');
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
		$clause = array(
                        "empresa_id"  => $this->empresa_id			
		);
                $clause['ajustadores'] = $id;
                
		$ajustadores = $this->ajustadoresRepository->listar($clause, NULL, NULL, NULL, NULL);
		if(empty($ajustadores)){
			return false;
		}
		$i=0;
		foreach ($ajustadores AS $row)
		{
			$csvdata[$i]['nombre'] = $row->nombre;
			$csvdata[$i]["ruc"] = utf8_decode(Util::verificar_valor($row->ruc));
			$csvdata[$i]["telefono"] = utf8_decode(Util::verificar_valor($row->telefono));
			$csvdata[$i]["email"] = utf8_decode(Util::verificar_valor($row->email));
			$csvdata[$i]["direccion"] = utf8_decode(Util::verificar_valor($row->direccion));
			$i++;
		}
		//we create the CSV into memory
		$csv = Writer::createFromFileObject(new SplTempFileObject());
		$csv->insertOne([
			'Nombre',
			'Ruc',
			'Telefono',
			'Email',
			'Direccion'
		]);
		$csv->insertAll($csvdata);
		$csv->output("ajustadores-". date('ymd') .".csv");
		exit();
    }
    
    public function exportar_contactos() {
    	if(empty($_POST)){
    		exit();
    	}
    	$ids =  $this->input->post('ids', true);
		$id = explode(",", $ids);	
		if(empty($id)){
			return false;
		}
                
		$csv = array();
                $clause['id'] = $id;
		$contactos = $this->contactoRepository->listar($clause, NULL, NULL, NULL, NULL);
		if(empty($contactos)){
			return false;
		}
		$i=0;
		foreach ($contactos AS $row)
		{
			$csvdata[$i]['nombre'] = $row->nombre;
			$csvdata[$i]["cargo"] = utf8_decode(Util::verificar_valor($row->cargo));
			$csvdata[$i]["correo"] = utf8_decode(Util::verificar_valor($row->email));
			$csvdata[$i]["celular"] = utf8_decode(Util::verificar_valor($row->celular));
			$csvdata[$i]["telefono"] = utf8_decode(Util::verificar_valor($row->telefono));
			$csvdata[$i]["ultimo_contacto"] = utf8_decode(Util::verificar_valor($row->created_at));
			$i++;
		}
		//we create the CSV into memory
		$csv = Writer::createFromFileObject(new SplTempFileObject());
		$csv->insertOne([
			'Nombre',
			'Cargo',
			'Correo',
			'Celular',
			'Telefono',			
			'Ultimo Contacto'
		]);
		$csv->insertAll($csvdata);
		$csv->output("contactos-". date('ymd') .".csv");
		exit();
    }


  public function ajax_listar() {
    if(!$this->input->is_ajax_request()){
    return false;
    }
    $nombre = $this->input->post('nombre', true);
    $telefono = $this->input->post('telefono', true);
    $email = $this->input->post('email', true);    
    $clause = array('empresa_id' => $this->empresaObj->id);    
    if(!empty($nombre)) $clause['nombre'] = $nombre;
    if(!empty($telefono)) $clause['telefono'] = $telefono;
    if(!empty($email)) $clause['email'] = $email;
    list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
    $count = $this->ajustadoresRepository->listar($clause, NULL, NULL, NULL, NULL)->count();
    list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
    $ajustadores = $this->ajustadoresRepository->listar($clause ,$sidx, $sord, $limit, $start);   
   
    $response = new stdClass();
    $response->page     = $page;
    $response->total    = $total_pages;
    $response->records  = $count;
    if(!is_null($ajustadores)){
      $i=0;
      foreach($ajustadores as $row){        
        $hidden_options = "";
        $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row->id .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
        $hidden_options .= '<a href="'. base_url('ajustadores/ver/'. $row->uuid_ajustadores) .'" data-id="'. $row->id .'" class="btn btn-block btn-outline btn-success">Ver detalle</a>';
        $hidden_options .= '<a href="#" class="btn btn-block btn-outline btn-success agregarContacto" data-id="'. $row['id'] .'" data-uuid="'. $row['uuid_ajustadores'] .'">Agregar contacto</a>';
        $response->rows[$i]["id"] = $row->id;
        $response->rows[$i]["cell"] = array( 
           $row->id,
           '<a class="link" href="'. base_url('ajustadores/ver/'. $row->uuid_ajustadores) .'" >'.$row->nombre.'</a>',
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

    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))->_display();
    exit;


  }
  
  public function ajax_listar_contacto() {
    if(!$this->input->is_ajax_request()){
    return false;
    }
    $nombre = $this->input->post('nombre', true);
    $telefono = $this->input->post('telefono', true);
    $email = $this->input->post('correo', true);    
    $cargo = $this->input->post('cargo', true);
    $celular = $this->input->post('celular', true);
    $fecha_desde = $this->input->post('ultimo_contacto_desde', true);
    $fecha_hasta = $this->input->post('ultimo_contacto_hasta', true);
    $ajustador_id = $this->input->post('ajustador_id', true);
    $clause["ajustador_id"] = $ajustador_id;
    if(!empty($nombre)) $clause['nombre'] = $nombre;
    if(!empty($telefono)) $clause['telefono'] = $telefono;
    if(!empty($email)) $clause['email'] = $email;
    if(!empty($cargo)) $clause['cargo'] = $cargo;
    if(!empty($celular)) $clause['celular'] = $celular;
    if( !empty($fecha_desde)){
        $fecha_desde = str_replace('/', '-', $fecha_desde);
        $fecha_inicio = date("Y-m-d H:i:s", strtotime($fecha_desde));
        $clause["created_at"] = array('>=', $fecha_inicio);
    	}
    	if( !empty($fecha_hasta)){
        $fecha_hasta = str_replace('/', '-', $fecha_hasta);
        $fecha_fin = date("Y-m-d 23:59:59", strtotime($fecha_hasta));
        $clause["created_at@"] = array('<=', $fecha_fin);
    	}
    list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
    $count = $this->contactoRepository->listar($clause, NULL, NULL, NULL, NULL)->count();
    list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
    $contacto = $this->contactoRepository->listar($clause ,$sidx, $sord, $limit, $start);   
   
    $response = new stdClass();
    $response->page     = $page;
    $response->total    = $total_pages;
    $response->records  = $count;   
    if(!is_null($contacto)){
      $i=0;
      foreach($contacto as $row){  
        $label_principal = ($row->principal == 1)? '<span class="label label-warning">Principal</span>':'';
        $hidden_options = "";
        $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row->id .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
        $hidden_options .= '<a id="detalleContacto" href="#" data-idContacto="'. $row->id .'"'
                . ' data-nombre="'. $row->nombre .'"data-apellido="'. $row->apellido .'"data-cargo="'. $row->cargo .'"'
                . 'data-telefono="'. $row->telefono .'"data-celular="'. $row->celular .'" data-email="'. $row->email .'" class="btn btn-block btn-outline btn-success">Ver detalle</a>';
        $response->rows[$i]["id"] = $row->id;
        $response->rows[$i]["cell"] = array( 
           $row->principal, 
           $row->nombre . " " . $row->apellido . $label_principal,
           $row->cargo,
           $row->email,
           $row->celular,
           $row->telefono,
           $row->created_at,
           $link_option,
           $hidden_options
        );
       $i++;
      }
    }

    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))->_display();
    exit;


  }

  function crear() {
    $acceso = 1;
    $mensaje = array();

    if(!$this->auth->has_permission('acceso')){
      // No, tiene permiso, redireccionarlo.
      $acceso = 0;
      $mensaje = array('estado'=>500, 'mensaje'=>' <b>Usted no cuenta con permiso para esta solicitud</b>','clase'=>'alert-danger');
    }

    $this->_Css();
    $this->assets->agregar_css(array(
      'public/assets/css/modules/stylesheets/animacion.css'
    ));
    $this->_js();
    $this->assets->agregar_js(array(
        'public/assets/js/default/vue-validator.min.js',
        'public/assets/js/default/vue-resource.min.js',
        'public/assets/js/modules/ajustadores/crear.js',
        'public/assets/js/modules/ajustadores/crear_ajustadores.js',
        'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js'
    ));

      $data=array();
      $clause = array('empresa_id'=> $this->empresa_id);
      $this->assets->agregar_var_js(array(
        "vista" => 'crear',
        "acceso" => $acceso
      ));
    $breadcrumb = array(
      "titulo" => '<i class="fa fa-archive"></i> Ajustadores: Crear',
        "ruta" => array(
    			0 => array(
    				"nombre" => "Seguros",
    				"activo" => false
    			),
    			1 => array(
    				"nombre" => 'Ajustadores',
    				"url"	=> 'ajustadores/listar',
    				"activo" => false
    			),
    			2 => array(
    				"nombre" => "Crear",
    				"activo" => true
    			)
    		)
    );
    $data['mensaje'] = $mensaje;
    $this->template->agregar_titulo_header('Ajustadores: Crear');
    $this->template->agregar_breadcrumb($breadcrumb);
    $this->template->agregar_contenido($data);
    $this->template->visualizar();
  }

  function ver($uuid=null) {
    $acceso = 1;
    $mensaje = array();    
    $ajustadores = $this->ajustadoresRepository->findByUuid($uuid);
    if(!$this->auth->has_permission('acceso','ajustadores/ver/(:any)') && !is_null($ajustadores)){
      // No, tiene permiso
        $acceso = 0;
        $mensaje = array('estado'=>500, 'mensaje'=>' <b>Usted no cuenta con permiso para esta solicitud</b>','clase'=>'alert-danger');
    }
    
    $ajustadores->toArray();
    unset($ajustadores->uuid_ajustadores);
    $this->_Css();
    $this->assets->agregar_css(array(
      'public/assets/css/modules/stylesheets/animacion.css'
    ));
    $this->_js();
    $this->assets->agregar_js(array(//
        'public/assets/js/default/vue-validator.min.js',
        'public/assets/js/default/vue-resource.min.js',
        'public/assets/js/modules/ajustadores/crear.js',
        'public/assets/js/modules/ajustadores/crear_ajustadores.js',
        'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
        'public/assets/js/modules/ajustadores/ajustadoresTablaContactoFunciones.js',
    ));
      $data=array();   
      $agregar_contacto = !empty($_POST['agregar_contacto']) ? 1 : '';
      $clause = array('empresa_id'=> $this->empresa_id);
      $this->assets->agregar_var_js(array(
        "vista" => 'ver',
        "acceso" => $acceso,
        "ajustadores" => $ajustadores,
        "agregar_contacto" => $agregar_contacto
      ));
    $breadcrumb = array(
      "titulo" => '<i class="fa fa-archive"></i> Ajustadores: Ver '.$ajustadores->nombre,
        "ruta" => array(
    			0 => array(
    				"nombre" => "Seguros",
    				"activo" => false
    			),
    			1 => array(
    				"nombre" => 'Ajustadores',
    				"url"	=> 'ajustadores/listar',
    				"activo" => false
    			),
    			2 => array(
    				"nombre" => $ajustadores->nombre,
    				"activo" => true
    			)
    		)
    );
    
    $breadcrumb["menu"] = array(
    			"url"	 => '#',
    			"nombre" => "Accion"                        
    		);
    $menuOpciones["#agregarContacto"] = "Agregar Contacto";
    $menuOpciones["#exportarContactos"] = "Exportar";
    $breadcrumb["menu"]["opciones"] = $menuOpciones;
    $data['mensaje'] = $mensaje;
    $this->template->agregar_titulo_header('Ajustadores: Ver');
    $this->template->agregar_breadcrumb($breadcrumb);
    $this->template->agregar_contenido($data);
    $this->template->visualizar();
  }

  function ocultoformulario() {
    $data = array();
    $clause = array('empresa_id'=> $this->empresa_id);
    $data['info']['provincias'] = Catalogos_orm::where('identificador','like','Provincias')->orderBy("orden")->get(array('id_cat','etiqueta'));
    $data['info']['letras'] = Catalogos_orm::where('identificador','like','Letra')->get(array('id_cat','etiqueta'));
    $data['tipo_identificacion'] = Catalogos_orm::where('identificador','like','Identificacion')->orderBy("orden")->get(array('id_cat','etiqueta'));
    $this->load->view('formulario', $data);
  }
  
  public function ajax_guardar_contacto() {
    	/**
    	 * Inicializar Transaccion
    	 */
    	Capsule::beginTransaction();
    	
    	try {

    		$contacto_id		= $this->input->post('contacto_id', true);
                $ajustador_id           = $this->input->post('ajustador_id', true);
    		$nombre          	= $this->input->post('nombre', true);
    		$apellido        	= $this->input->post('apellido', true);
    		$cargo       		= $this->input->post('cargo', true);    		
    		$telefono        	= $this->input->post('telefono', true);
    		$celular 		= $this->input->post('celular', true);
    		$email 			= $this->input->post('email', true);
                //Verificar si existe $descuento_id
    		$contacto = $this->contactoRepository->find($contacto_id);
      
    		if(!empty($contacto))
    		{
    			$contacto->nombre 		= $nombre;
    			$contacto->apellido     	= $apellido;
    			$contacto->cargo         	= $cargo;
                        $contacto->telefono             = $telefono;    			
                        $contacto->celular              = $celular;    			
                        $contacto->email                = $email;    			
    			$contacto->save();
    			
    		}else{
                
    			$fieldset = array(
    				"nombre" 		=> $nombre,
    				"apellido"      	=> $apellido,
    				"cargo"          	=> $cargo,
    				"telefono"              => $telefono,
                                "celular"               => $celular,
                                "email"                 => $email,
                                "ajustador_id"          => $ajustador_id
    			);
    			
    			//--------------------
    			// Guardar Descuento
    			//--------------------
    			$contacto = $this->contactoModel->create($fieldset);
    		}
    	} catch(ValidationException $e){
    				
    		// Rollback
    		Capsule::rollback();
    				
    		log_message("error", "MODULO: ". __METHOD__ .", Linea: ". __LINE__ ." --> ". $e->getMessage().".\r\n");
    				
    		echo json_encode(array(
    			"guardado" => false,
    			"mensaje" => "Hubo un error tratando de ". (!empty($contacto_id) ? "actualizar" : "guardar") ." el contacto."
    		));
    		exit;
    	}
    	
    	// If we reach here, then
    	// data is valid and working.
    	// Commit the queries!
    	Capsule::commit();
    	
    	if(empty($contacto_id)){
    		$this->session->set_flashdata('mensaje', "Se ha guardado el contacto satisfactoriamente.");
    		$this->session->set_flashdata('seccion-accordion', "contacto-seccion");
    	}
    	
    	echo json_encode(array(
    		"guardado" => true,
    		"mensaje" => "Se ha ". (!empty($contacto_id) ? "actualizado" : "guardado") ." el contacto satisfactoriamente."
    	));
    	exit;
    }


  function guardar() {   
    if($_POST){
    unset($_POST["campo"]["guardar"]);
    $campo = Util::set_fieldset("campo");
    //formato de identificacion
    if(!empty($campo['letra'])){
    if($campo['letra'] == '0'){
    $cedula = $campo['provincia']."-".$campo['letra']."-".$campo['tomo']."-".$campo['asiento'];    
    $campo['ruc'] = $cedula;
    $campo['estado_id'] = 1;   
    }else{   
    $cedula = $campo['provincia']."-".$campo['letra']."-".$campo['tomo']."-".$campo['asiento'];
    $campo['ruc'] = $cedula;
    $campo['estado_id'] = 1;
    }    
    }
    if($campo['identificacion'] == '45'){
         $cedula = $campo['tomo_ruc']."-".$campo['folio']."-".$campo['asiento_ruc']."-".$campo['digito'];
         $campo['ruc'] = $cedula;
         $campo['estado_id'] = 1;
    }if(!empty($campo['pasaporte']) || $campo['letra'] == 'PAS'){
        $cedula = $campo['pasaporte'];
        $campo['ruc'] = $cedula;
        $campo['estado_id'] = 1;    
    }if($campo['identificacion'] == 'RUC'){
        $cedula = $campo['tomo_ruc']."-".$campo['folio']."-".$campo['asiento_ruc']."-".$campo['digito'];
        $campo['ruc'] = $cedula;
        $campo['estado_id'] = 1;
    }    
    if(!isset($campo['uuid'])){
    $campo['empresa_id'] = $this->empresa_id;
    $campo['fecha_creacion'] = date('Y-m-d H:i:s');
    }
    Capsule::beginTransaction();
    try {
    if(empty($campo['id'])){       
    $ajustadoresObj  = $this->ajustadoresRepository->buscar($campo['ruc']);    
    $ruc_existente = $ajustadoresObj->ruc;   
        if($ruc_existente == $campo['ruc']){
    redirect(base_url('ajustadores/listar'));
        }else{
    $campo["uuid_ajustadores"] = Capsule::raw("ORDER_UUID(uuid())");       
    $ajustadores = $this->ajustadoresModel->create($campo);        
        }    
    }else{
    $ajustadoresObj  = $this->ajustadoresRepository->buscar($campo['ruc']);
    $ajustadores = $ajustadoresObj->find($campo['id']);
    if(is_null($ajustadores)){
    $mensaje = array('class' =>'alert-warning', 'mensaje' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
    $this->session->set_flashdata('mensaje', $mensaje);
    redirect(base_url('agentes/listar'));
    }else{
    unset($campo['uuid']);
    $ajustadores->update($campo);
    }
    }
    Capsule::commit();
    }catch(ValidationException $e){
    log_message('error', $e);
    Capsule::rollback();
    }

    if(!is_null($ajustadores)){
            $this->session->set_flashdata('mensaje', "Se ha creado satisfactoriamente.");
    }else{
            $mensaje = array('class' =>'alert-danger', 'contenido' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
    }


    }else{
            $mensaje = array('class' =>'alert-warning', 'contenido' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
    }

    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($mensaje))->_display();
    redirect(base_url('ajustadores/listar'));
  }
  
  function ajax_guardar_comentario() {
    if(!$this->input->is_ajax_request()){
      return false;
    }

    $model_id   = $this->input->post('modelId');
    $comentario = $this->input->post('comentario');
    $uuid_usuario = $this->session->userdata('huuid_usuario');
    $usuario = Usuario_orm::findByUuid($uuid_usuario);
    $comentario = ['comentario'=>$comentario,'usuario_id'=>$usuario->id];

    $nota_credito = $this->notaCreditoRepository->agregarComentario($model_id, $comentario);
    $nota_credito->load('comentario');
    $lista_comentario = $nota_credito->comentario()->orderBy('created_at','desc')->get();
    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($lista_comentario->toArray()))->_display();
    exit;
  }
  private function _generar_codigo() {
    $clause_empresa = ['empresa_id'=>$this->empresa_id];
    $numero = $this->notaCreditoRepository->lista_totales($clause_empresa);
    return $numero + 1;
  }
  
  function asignar_contacto_principal() {
        //Just Allow ajax request
    	if(!$this->input->is_ajax_request()){
    		return false;
    	}
      $clause = array();
      $ajustador_id = $this->input->post('ajustador_id', true);
      $contacto_id = $this->input->post('contacto_id', true);
      $ajustador = $this->ajustadoresModel->find($ajustador_id);
      $contacto = $this->contactoModel->find($contacto_id);

      $clause['ajustador_id'] = $ajustador->id;
      $clause['id'] = $contacto->id;
      $response = $this->contactoRepository->asignar_contacto_principal($clause);
    	//$response = $this->contactos_model->asignar_contacto_principal();

    	$json = json_encode($response);
    	echo $json;
    	exit;
    }
  
  private function _js() {
    $this->assets->agregar_js(array(
        'public/assets/js/default/jquery-ui.min.js',
        'public/assets/js/plugins/jquery/jquery.sticky.js',
        'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
        'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
        'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
        'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
        'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
        'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
        'public/assets/js/default/lodash.min.js',
        'public/assets/js/default/accounting.min.js',
        'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
        'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
        'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.numeric.extensions.js',
        'public/assets/js/plugins/jquery/sweetalert/sweetalert.min.js',
        'public/assets/js/moment-with-locales-290.js',
        'public/assets/js/plugins/bootstrap/select2/select2.min.js',
        'public/assets/js/plugins/bootstrap/select2/es.js',
        'public/assets/js/plugins/bootstrap/daterangepicker.js',
        'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
        'public/assets/js/plugins/ckeditor/ckeditor.js',
        'public/assets/js/plugins/ckeditor/adapters/jquery.js',
        'public/assets/js/default/toast.controller.js',     
        'public/assets/js/default/formulario.js',
  ));
  }

  private function _css() {
    $this->assets->agregar_css(array(
        'public/assets/css/default/ui/base/jquery-ui.css',
        'public/assets/css/default/ui/base/jquery-ui.theme.css',
        'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
        'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
        'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
        'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
        'public/assets/css/plugins/jquery/chosen/chosen.min.css',
        'public/assets/js/plugins/jquery/sweetalert/sweetalert.css',
        'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
        'public/assets/css/plugins/bootstrap/select2.min.css',
      'public/assets/css/modules/stylesheets/nota_credito.css',
    ));
  }



}

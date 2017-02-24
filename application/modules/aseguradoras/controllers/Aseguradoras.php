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
use Dompdf\Dompdf;
use Carbon\Carbon;
use Flexio\Modulo\aseguradoras\Repository\AseguradorasRepository as AseguradorasRepository;
use Flexio\Modulo\aseguradoras\Models\Aseguradoras as AseguradorasModel;
use Flexio\Modulo\SegAseguradoraContacto\Models\SegAseguradoraContacto as SegAseguradoraContactoModel;
use Flexio\Modulo\SegAseguradoraContacto\Repository\SegAseguradoraContactoRepository as SegAseguradoraContactoRepository;
use Flexio\Modulo\Politicas\Repository\PoliticasRepository as PoliticasRepository;


class aseguradoras extends CRM_Controller
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
	
	/**
	 * @var array
	 */
	protected $roles;
	
	/**
	 * @var array
	 */
	protected $politicas;
	
	/**
	 * @var array
	 */
	protected $politicas_general;
	
    protected $AseguradorasRepository;    
	protected $AseguradorasModel; 
	protected $SegAseguradoraContactoModel;
	protected $SegAseguradoraContactoRepository;
	protected $PoliticasRepository;

	/**
	 * @var string
	 */
	protected $upload_folder = './public/uploads/';
	
	function __construct() {
        parent::__construct();
		
        //$this->load->model('Planes_orm');
        //$this->load->model('Coberturas_orm');
        $this->load->model('usuarios/usuario_orm');
        
        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();
        
        //Obtener el id de usuario de session
        $uuid_usuario = $this->session->userdata('huuid_usuario');
        $usuario = Usuario_orm::findByUuid($uuid_usuario);
        
        $this->usuario_id = $usuario->id;
         
        //Obtener el id_empresa de session
        $uuid_empresa = $this->session->userdata('uuid_empresa');
		
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $this->empresa_id = $empresa->id;
		
		$this->AseguradorasRepository = new AseguradorasRepository();
		$this->AseguradorasModel = new AseguradorasModel();
		$this->SegAseguradoraContactoModel= new SegAseguradoraContactoModel();
		$this->SegAseguradoraContactoRepository=new SegAseguradoraContactoRepository();
		$this->PoliticasRepository= new PoliticasRepository();
		
		$this->roles=$this->session->userdata("roles");
		//$roles=implode(",", $this->roles);
		
		$clause['empresa_id']=$this->empresa_id;
		$clause['modulo']='aseguradora';
		$clause['usuario_id']=$this->usuario_id;
		$clause['role_id']=$this->roles;
		
		$politicas_transaccion=$this->PoliticasRepository->getAllPoliticasRoles($clause);
		
		$politicas_transaccion_general=count($this->PoliticasRepository->getAllPoliticasRolesModulo($clause));
		$this->politicas_general=$politicas_transaccion_general;
		
		$estados_politicas=array();
		foreach($politicas_transaccion as $politica_estado)
		{
			$estados_politicas[]=$politica_estado->politica_estado;
		}
		
		$this->politicas=$estados_politicas;
}
public function listar() {
	
	//Definir mensaje
    	if(!is_null($this->session->flashdata('mensaje'))){
            $mensaje = $this->session->flashdata('mensaje');
        }else{
            $mensaje = [];
        }
        $this->assets->agregar_var_js(array(
            "flexio_mensaje" =>  collect($mensaje)
        ));
	
		if(!$this->auth->has_permission('acceso','aseguradoras/listar')){
			// No, tiene permiso, redireccionarlo.
			$acceso = 0;
			$mensaje = array('tipo'=>"error", 'mensaje'=>'<b>¡Error!</b> No tiene permisos para ingresar al modulo' ,'titulo'=>'Aseguradora ');
			
			redirect(base_url(''));
		}
	
    	$data = array();
    	
        $this->_Css();   
        $this->_js();
        
    	$this->assets->agregar_js(array(
		'public/assets/js/plugins/jquery/context-menu/jquery.contextMenu.min.js',
        'public/assets/js/modules/aseguradoras/listar.js',
		'public/assets/js/modules/aseguradoras/routes.js',
      ));
    	
    	//Verificar permisos para crear
    	$breadcrumb = array(
            "titulo" => '<i class="fa fa-archive"></i> Aseguradoras',
            "ruta" => array(
                0 => array("nombre" => "Seguros", "url" => "#",  "activo" => false),
                1 => array("nombre" => '<b>Aseguradoras</b>', "activo" => true)
            ),
            "filtro"    => false,
            "menu"      => array()
        );
		
        $breadcrumb["menu"] = array(
    		"url"	=> 'aseguradoras/crear',
    		"nombre" => "Crear"
    	);
		$menuOpciones["#cambiarEstadoAseguradoraLnk"] = "Cambiar Estado";
		$menuOpciones["#exportarAseguradorasLnk"] = "Exportar";
		$breadcrumb["menu"]["opciones"] = $menuOpciones;
        
        $clause = array('empresa_id' => $this->empresa_id);   
        
    	$this->template->agregar_titulo_header('Listado de Aseguradoras');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar($breadcrumb);
}

public function obtener_politicas(){
	echo json_encode($this->politicas);
	exit;
}

public function obtener_politicas_general(){
	echo json_encode($this->politicas_general);
	exit;
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
		$estado    	= $this->input->post('estado', true);
		
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
		
		if(!empty($estado)){
    		$clause["estado"] = $estado;
    	}
       
    	list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
		
    	$count = $this->AseguradorasRepository->listar_aseguradoras($clause, NULL, NULL, NULL, NULL)->count();
    		
    	list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
    		 
    	$rows = $this->AseguradorasRepository->listar_aseguradoras($clause, $sidx, $sord, $limit, $start);
    	
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
			
			if($this->auth->has_permission('acceso','aseguradoras/editar') || $this->auth->has_permission('acceso','aseguradoras/ver')){
				$hidden_options .= '<a href="'. base_url('aseguradoras/editar/'. $uuid_aseguradora) .'" data-id="'. $row->id .'" class="btn btn-block btn-outline btn-success">Ver detalle</a>';
			}
			
			$hidden_options .= '<a href="'. base_url('aseguradoras/agregarcontacto/'. $uuid_aseguradora.'?opt=1') .'" data-id="'. $row->id .'" class="btn btn-block btn-outline btn-success">Agregar Contacto</a>';
			
            $response->rows[$i]["id"] = $row->id;
            $response->rows[$i]["cell"] = array(
					$row->id,
                    '<a href="'. base_url('aseguradoras/editar/'. $uuid_aseguradora) .'" style="color:blue;">'. $row->nombre.'</a>', 
					$row->ruc,
					$row->telefono,
					$row->email,
					$row->direccion,
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

public function crear() { 
    $acceso = 1;
    $mensaje = array();

    if(!$this->auth->has_permission('acceso','aseguradoras/crear')){
			// No, tiene permiso, redireccionarlo.
		$acceso = 0;
		$mensaje = array('tipo'=>"error", 'mensaje'=>'<b>¡Error!</b> No tiene permisos para crear aseguradoras' ,'titulo'=>'Aseguradora ');
		$this->session->flashdata('mensaje',$mensaje);
		redirect(base_url('aseguradoras/listar'));
	}

    $this->_Css();   
    $this->_js();
    $this->assets->agregar_js(array(       
     'public/assets/js/modules/aseguradoras/formulario.js',   
     'public/assets/js/modules/aseguradoras/crear.js',
	 //'public/assets/js/default/vue-validator.min.js',	 
    ));

      $data=array();      
      $this->assets->agregar_var_js(array(
        "vista" => 'crear',
        "acceso" => $acceso,
      ));
	  
	  $data["campos"] = array(
			"campos" => array(
			"created_at" => '',
			"uuid_aseguradora" => '',
			"nombre" => '',
			"tomo" => '',
			"folio" => '',
			"asiento" => '',
			"digverificador" => '',
			"telefono" => '',
			"email" => '',
			"direccion" => '',
			"estado" => '',
			'guardar' => 1,
			'politicas'=>'',
			'politicas_general'=>''
		),

	);
      
     
    $breadcrumb = array(
      "titulo" => '<i class="fa fa-archive"></i> Aseguradoras: Crear ',
      "ruta" => array(
            0 => array("nombre" => "Seguros", "url" => "#",  "activo" => false),
            1 => array("nombre" => 'Aseguradoras',"url" => "aseguradoras/listar", "activo" => false),
            2 => array("nombre" => '<b>Crear</b>', "activo" => true)
        ),
      "filtro"    => false,
      "menu"      => array()
    );
    $data['mensaje'] = $mensaje;
    $this->template->agregar_titulo_header('Aseguradoras: Crear');
    $this->template->agregar_breadcrumb($breadcrumb);
    $this->template->agregar_contenido($data);
    $this->template->visualizar();    
    
}

 function editar($uuid = NULL, $opcion = NULL) {
	 
	if(!is_null($this->session->flashdata('mensaje'))){
		$mensaje = $this->session->flashdata('mensaje');
		}else{
			$mensaje = [];
		}
	 
	 if(!$this->auth->has_permission('acceso','aseguradoras/editar') && !$this->auth->has_permission('acceso','aseguradoras/ver')){
			// No, tiene permiso, redireccionarlo.
		$mensaje = array('tipo'=>"error", 'mensaje'=>'<b>¡Error!</b> Usted no tiene permisos para ingresar a editar' ,'titulo'=>'Aseguradora ');
		
		$this->session->set_flashdata('mensaje', $mensaje);
		
		redirect(base_url('aseguradoras/listar'));
		}

	$this->_Css();   
	$this->_js();
	
	$data = array();
	
	if($uuid=="")
		$uuid_aseguradora=$_POST["campo"]["uuid"];
	else
		$uuid_aseguradora=$uuid;
	
	$aseguradora = $this->AseguradorasRepository->verAseguradora(hex2bin(strtolower($uuid_aseguradora)));
	
	$this->assets->agregar_js(array(       
     'public/assets/js/modules/aseguradoras/formulario.js',   
     'public/assets/js/modules/aseguradoras/editar.js',
	 'public/assets/js/default/vue-validator.min.js',	
    ));
	
	$this->assets->agregar_var_js(array(
             'politica_transaccion' => $aseguradora->politica(),
			 "flexio_mensaje" =>  collect($mensaje)
    ));
	
	if (!empty($_POST)) {
				//$aseguradora = $this->AseguradorasRepository->verAseguradora(hex2bin(strtolower($_POST["campo"]["uuid"])));
				$campo = $this->input->post("campo");
				
				$ruc="";
				
				if($_POST["campo"]["tomo"]!="")
				{
					$ruc=$_POST["campo"]["tomo"];  	
				}
				if($_POST["campo"]["folio"]!="")
				{
					$ruc.="-".$_POST["campo"]["folio"];	
				}
				if($_POST["campo"]["folio"]!="")
				{
					$ruc.="-".$_POST["campo"]["asiento"];	
				}
				if($_POST["campo"]["digverificador"]!="")
				{
					$ruc.=" DV ".$_POST["campo"]["digverificador"];   	
				}
				
				$aseguradora->nombre = $campo["nombre"];
				$aseguradora->ruc = $ruc;
				$aseguradora->telefono = $campo["telefono"];
				$aseguradora->email = $campo["email"];
				$aseguradora->direccion = $campo["direccion"];
				$aseguradora->tomo = $campo["tomo"];
				$aseguradora->folio = $campo["folio"];
				$aseguradora->asiento = $campo["asiento"];
				$aseguradora->estado = $campo["estado"];
				//var_dump($campo["estado"]);exit();
				$aseguradora->digverificador = $campo["digverificador"];
				if($aseguradora->save()){
					$mensaje = array('tipo'=>"success", 'mensaje'=>'<b>¡&Eacute;xito!</b> Se ha guardado correctamente' ,'titulo'=>'Aseguradora '. $_POST["campo"]["nombre"]);	
				}
				else{
					$mensaje = array('tipo'=>"error", 'mensaje'=>'<b>¡&Eacute;xito!</b> Su solicitud no fue procesada' ,'titulo'=>'Aseguradora '.$_POST["campo"]["nombre"] );
				}
				$this->session->set_flashdata('mensaje', $mensaje);
				redirect(base_url('aseguradoras/listar'));

	}
	
	$breadcrumb = array(
		"titulo" => '<i class="fa fa-archive"></i> Aseguradora ' . $aseguradora->nombre,
		"filtro" => false, //sin vista grid
		"menu" => array(
			'url' => 'javascipt:',
			'nombre' => "Acción",
			"opciones" => array(
				"#datosAseguradoraBtn" => "Datos de Aseguradora",
				"#agregarContactoBtn" => "Nuevo Contacto",
				"#agregarPlanBtn" => "Nuevo Plan",
				"#exportarBtn" => "Exportar",
			)
		),
		"ruta" => array(
			0 => array("nombre" => "Seguros", "url" => "#",  "activo" => false),
			1 => array("nombre" => "Aseguradoras", "url" => "aseguradoras/listar",  "activo" => false),
			2 => array("nombre" => $aseguradora->nombre, "activo" => true)
		)
	);
	
	if($this->auth->has_permission('acceso','aseguradoras/editar')){
		$guardar =1;
	}
	else {
		$guardar =0;
	}
	
	$data["opcion"] = $opcion;
	$data["campos"] = array(
			"campos" => array(
			"created_at" => $aseguradora->created_at,
			"uuid_aseguradora" => $uuid,
			"nombre" => $aseguradora->nombre,
			"tomo" => $aseguradora->tomo,
			"folio" => $aseguradora->folio,
			"asiento" => $aseguradora->asiento,
			"digverificador" => $aseguradora->digverificador,
			"telefono" => $aseguradora->telefono,
			"email" => $aseguradora->email,
			"direccion" => $aseguradora->direccion,
			"estado" => $aseguradora->estado,
			"guardar" => $guardar,
			'politicas' => $this->politicas,
			'politicas_general'=>$this->politicas_general
		),

	);
	
	$data['subpanels'] = [];
	
	$this->template->agregar_titulo_header('Aseguradoras');
	$this->template->agregar_breadcrumb($breadcrumb);
	$this->template->agregar_contenido($data);
	$this->template->visualizar();
}

 function agregarcontacto($uuid = NULL, $opcion = NULL) {
	$this->_Css();   
	$this->_js();
	$data = array();
	$mensaje = array();

	$this->assets->agregar_js(array(       
     'public/assets/js/modules/aseguradoras/formulario.js',   
     'public/assets/js/modules/aseguradoras/crearcontacto.js',
	 'public/assets/js/default/vue-validator.min.js',	
	 "flexio_mensaje" =>  collect($mensaje)	 
    ));
	
	if (!empty($_POST)) {
			if($_POST["campo"]["uuid"]!="")
			{
				$contacto=$this->SegAseguradoraContactoRepository->verContactoUiid(hex2bin(strtolower($_POST["campo"]["uuid"])));
				$contacto->nombre=$_POST["campo"]["nombre"];
				$contacto->email=$_POST["campo"]["email"];
				$contacto->cargo=$_POST["campo"]["cargop"];
				$contacto->celular=$_POST["campo"]["celular"];
				$contacto->telefono=$_POST["campo"]["telefono"];
				$contacto->direccion=$_POST["campo"]["direccion"];
				$contacto->comentarios=$_POST["campo"]["comentarios"];
				$contacto->estado=$_POST["campo"]["estado"];
				$date = Carbon::now();
				$date = $date->format('Y-m-d');
				$contacto->updated_at=$date;
				
				if($contacto->save()){
					$mensaje = array('tipo'=>"success", 'mensaje'=>'<b>¡&Eacute;xito!</b> Se ha guardado correctamente' ,'titulo'=>'Contacto '. $_POST["campo"]["nombre"]);	
				}
				else{
					$mensaje = array('tipo'=>"error", 'mensaje'=>'<b>¡&Eacute;xito!</b> Su solicitud no fue procesada' ,'titulo'=>'Contacto '.$_POST["campo"]["nombre"] );
				}
				
				$this->session->set_flashdata('mensaje', $mensaje);
				$url='aseguradoras/editar/'.$_POST["campo"]["uuid_aseguradora"];
				redirect(base_url($url));

			}
			else
			{
				$aseguradora = $this->AseguradorasRepository->verAseguradora(hex2bin(strtolower($_POST["campo"]["uuid_aseguradora"])));
				
				$campo = $this->input->post("campo");
				$campo["uuid_contacto"] = Capsule::raw("ORDER_UUID(uuid())");
				$campo['aseguradora_id'] = $aseguradora->id;
				$year = Carbon::now()->format('y');
				$campo["creado_por"] = $this->session->userdata['id_usuario'];   
				$date = Carbon::now();
				$date = $date->format('Y-m-d');
				$campo['created_at'] = $date;
				
				if($this->SegAseguradoraContactoModel->create($campo))
				{
					 $mensaje = array('tipo'=>"success", 'mensaje'=>'<b>¡&Eacute;xito!</b> Se ha guardado correctamente' ,'titulo'=>'Aseguradora: Contacto '. $_POST["campo"]["nombre"]);
				}	
				else{
						$mensaje = array('tipo'=>"error", 'mensaje'=>'<b>¡Error!</b> Su solicitud no fue procesada' ,'titulo'=>'Aseguradora: Contacto '.$_POST["campo"]["nombre"] );	
					}
					
				if($_POST["campo"]["opt"]==1)
				{
					$this->session->set_flashdata('mensaje', $mensaje);
					redirect(base_url('aseguradoras/listar'));
				}
				
				else if($_POST["campo"]["opt"]==2)
				{
					$this->session->set_flashdata('mensaje', $mensaje);
					$url='aseguradoras/editar/'.$_POST["campo"]["uuid_aseguradora"];
					redirect(base_url($url));
				}
			}	
	}
	if(isset($uuid))
	{
		$aseguradora = $this->AseguradorasRepository->verAseguradora(hex2bin(strtolower($uuid)));
		$bread=$aseguradora->nombre;
	}
	else
		$bread='';
		
	$breadcrumb = array(
		"titulo" => '<i class="fa fa-archive"></i> Aseguradora: Crear Contacto ',
		"filtro" => false, //sin vista grid
		"ruta" => array(
			0 => array("nombre" => "Seguros", "url" => "#",  "activo" => false),
			1 => array("nombre" => "Aseguradoras", "url" => "aseguradoras/listar",  "activo" => false),
			2 => array("nombre" => $bread, "url" => "aseguradoras/editar/".$uuid,"activo" => true)
			)
		);
	
	if(isset($_GET['opt']))	
		$opt=$_GET['opt'];
	else
		$opt=$_POST["campo"]["opt"];
	$data["opcion"] = $opcion;
	$data["campos"] = array(
			"campos" => array(
			"uuid_aseguradora" => $uuid,
			"nombre" => '',
			"email" => '',
			"telefono" => '',
			"celular" => '',
			"cargo" => '',
			"direccion" => '',
			"comentarios" => '',
			"uuid_contacto" => '',
			"opt"=>$opt
		),

	);
	
	$this->template->agregar_titulo_header('Crear Contacto');
	$this->template->agregar_breadcrumb($breadcrumb);
	$this->template->agregar_contenido($data);
	$this->template->visualizar();
}

function ocultoformulario($data = array()) {
        $clause = array('empresa_id' => $this->empresa_id);        
        $this->assets->agregar_js(array(
		'public/assets/js/modules/aseguradoras/components/detalle.js',
        ));
        
        $this->load->view('formulario',$data);
}

function ocultoformulariocontacto($data = array()) {
        $clause = array('empresa_id' => $this->empresa_id);        
        $this->assets->agregar_js(array(
			'public/assets/js/modules/aseguradoras/crearcontacto.js',
        ));
        
        $this->load->view('formulariocontacto',$data);
}

function guardar() {
    if($_POST){
    unset($_POST["campo"]["guardar"]);
    $campo = Util::set_fieldset("campo");    
    Capsule::beginTransaction();
    try {
    if(empty($campo['uuid'])){ 
    $campo["uuid_aseguradora"] = Capsule::raw("ORDER_UUID(uuid())");
    $clause['empresa_id'] = $this->empresa_id;
    $total = $this->AseguradorasRepository->listar($clause);
    $year = Carbon::now()->format('y');
    //$codigo = Util::generar_codigo($_POST['codigo_ramo'] . "-" . $year , count($total) + 1);
    //$campo["numero"] = $codigo;
    $campo["creado_por"] = $this->session->userdata['id_usuario'];
    $campo["empresa_id"] = $this->empresa_id;    
    $date = Carbon::now();
    $date = $date->format('Y-m-d');
    $campo['fecha_creacion'] = $date;
	
	if($_POST["campo"]["tomo"]!="")
	{
		$ruc=$_POST["campo"]["tomo"];  	
	}
	if($_POST["campo"]["folio"]!="")
	{
		$ruc.="-".$_POST["campo"]["folio"];	
	}
	if($_POST["campo"]["folio"]!="")
	{
		$ruc.="-".$_POST["campo"]["asiento"];	
	}
	if($_POST["campo"]["digverificador"]!="")
	{
		$ruc.=" DV ".$_POST["campo"]["digverificador"];   	
	}
	
	$campo['ruc'] = $ruc;
	
    $aseguradoras = $this->AseguradorasModel->create($campo); 
    }else{
    echo "hola mundo";
    }
    Capsule::commit();
    }catch(ValidationException $e){
    log_message('error', $e);
    Capsule::rollback();
    }
	
    if(!is_null($aseguradoras)){   
		$mensaje = array('tipo'=>"success", 'mensaje'=>'<b>¡&Eacute;xito!</b> Se ha guardado correctamente' ,'titulo'=>'Aseguradora '. $_POST["campo"]["nombre"]);	

    }else{
        $mensaje = array('tipo'=>"error", 'mensaje'=>'<b>¡&Eacute;xito!</b> Su solicitud no fue procesada' ,'titulo'=>'Aseguradora '.$_POST["campo"]["nombre"] );	
    }


    }else{
          $mensaje = array('tipo'=>"error", 'mensaje'=>'<b>¡&Eacute;xito!</b> Su solicitud no fue procesada' ,'titulo'=>'Aseguradora '.$_POST["campo"]["nombre"] );
    }
    
    $this->session->set_flashdata('mensaje', $mensaje);
    redirect(base_url('aseguradoras/listar'));
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
        $clause['id'] = $id;
                
		$aseguradoras = $this->AseguradorasRepository->exportar($clause, NULL, NULL, NULL, NULL);
		if(empty($aseguradoras)){
			return false;
		}
		$i=0;
		foreach ($aseguradoras AS $row)
		{
			$csvdata[$i]['nombre'] = $row->nombre;
			$csvdata[$i]["ruc"] = utf8_decode(Util::verificar_valor($row->ruc));
			$csvdata[$i]["telefono"] = utf8_decode(Util::verificar_valor($row->telefono));
			$csvdata[$i]["email"] = utf8_decode(Util::verificar_valor($row->email));
			$csvdata[$i]["direccion"] = utf8_decode(Util::verificar_valor($row->direccion));
			$csvdata[$i]["estado"] = $row->estado;
			$i++;
		}
		//we create the CSV into memory
		$csv = Writer::createFromFileObject(new SplTempFileObject());
		$csv->insertOne([
			'Nombre',
			'Ruc',
			'Telefono',
			'Email',
			'Direccion',
			'Estado'
		]);
		$csv->insertAll($csvdata);
		$csv->output("aseguradoras-". date('ymd') .".csv");
		exit();
    }
	
	public function exportarContactos() {
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
                
		$contactos = $this->SegAseguradoraContactoRepository->listar_contactos($clause, NULL, NULL, NULL, NULL);
		if(empty($contactos)){
			return false;
		}
		$i=0;
		foreach ($contactos AS $row)
		{
			$csvdata[$i]['nombre'] = $row->nombre;
			$csvdata[$i]["cargo"] = utf8_decode(Util::verificar_valor($row->cargo));
			$csvdata[$i]["email"] = utf8_decode(Util::verificar_valor($row->email));
			$csvdata[$i]["celular"] = utf8_decode(Util::verificar_valor($row->celular));
			$csvdata[$i]["telefono"] = utf8_decode(Util::verificar_valor($row->telefono));
			$csvdata[$i]["estado"] = $row->estado;
			$i++;
		}
		//we create the CSV into memory
		$csv = Writer::createFromFileObject(new SplTempFileObject());
		$csv->insertOne([
			'Nombre',
			'Cargo',
			'Email',
			'Celular',
			'Telefono',
			'Estado'
		]);
		$csv->insertAll($csvdata);
		$csv->output("contactos-". date('ymd') .".csv");
		exit();
    }

	public function ocultotabla() {
    	$this->assets->agregar_js(array(
    		'public/assets/js/modules/aseguradoras/tabla.js'
    	));
    	
    	$this->load->view('tabla');
    }
	
	function ajax_cambiar_estados(){

     $FormRequest = new Flexio\Modulo\aseguradoras\FormRequest\GuardarAseguradoraEstados;
     try{
        $aseguradora = $FormRequest->guardar($this->politicas);
        //formatear el response
        $res = $aseguradora->map(function($ant){
          return[
            'id'=>$ant->id,'estado'=>$ant->present()->estado_label
          ];
        });
     }catch(\Exception $e){
         log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");

     }

     $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
     ->set_output($res)->_display();
     exit;
   }
   
     function tabladetalles($data = array()) {
        /*$clause = array('empresa_id' => $this->empresa_id);        
        $this->assets->agregar_var_js(array(
        ));
        */
        $this->load->view('tabladetalles',$data);
	}
	 function tabladetallescontactos($data = array()) {
		 
        $this->assets->agregar_js(array(
            'public/assets/js/modules/aseguradoras/tablacontacto.js',
        ));
		
		$this->load->view('tabladetallescontactos',$data);
	}
	
public function ajax_listar_contacto($grid=NULL) {  


        //print_r("uuid=".$this->aseguradora_id);
        $aseguradora=$this->AseguradorasRepository->verAseguradora(hex2bin(strtolower($this->input->post('uuid_aseguradora'))));
		
		$id_aseguradora=$aseguradora->id;
		
		$nombre = $this->input->post('nombre', true);
		$cargo = $this->input->post('cargo', true);
		$email = $this->input->post('email', true);
		$celular = $this->input->post('celular', true);
		$telefono = $this->input->post('telefono', true);
		$estado = $this->input->post('estado', true);
		
		if($nombre!="")
			$clause['nombre'] = array('LIKE', '%'.$nombre.'%');
		if($cargo!="")
			$clause['cargo'] = array('LIKE', '%'.$cargo.'%');
		if($email!="")
			$clause['email'] = array('LIKE', '%'.$email.'%');
		if($celular!="")
			$clause['celular'] = array('LIKE', '%'.$celular.'%');
		if($telefono!="")
			$clause['telefono'] = array('LIKE', '%'.$telefono.'%');
		if($estado=="Activo" || $estado=="Inactivo" || $estado=="Por aprobar")
		{
			$clause['estado'] = $estado;
		}
			

		list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
		
		$clause['aseguradora_id'] = $id_aseguradora;
		
		$count = $this->SegAseguradoraContactoRepository->listar_contactos($clause, NULL, NULL, NULL, NULL)->count();
		
		list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
		
		$contactos = $this->SegAseguradoraContactoRepository->listar_contactos($clause ,$sidx, $sord, $limit, $start);   
	
        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        

        list($total_pages, $page, $start) = Jqgrid::paginacion($count, 10, 2);

        //Constructing a JSON
        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->record = $count;
        $i = 0;

        if (!empty($contactos)) {
            foreach ($contactos as $row) {
                $tituloBoton = ($row['estado'] != 1) ? 'Habilitar' : 'Deshabilitar';
                $estado = ($row['estado'] == 1) ? 0 : 1;
                $hidden_options = "";
                $link_option = '<button class="aseguradoraopciones btn btn-success btn-sm" type="button" data-id="'.$row['id'].'"><i class="fa fa-cog" id="aseguradoraopciones"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $hidden_options .= '<a href="" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success detallecontacto">Ver detalle</a>';
				
				if($row['estado']=='Activo')
					$datoestado='Inactivar';
				else
					$datoestado='Activar';
				
				$hidden_options .= '<a href="" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success verdetalleestado cambiarestadoseparado">'.$datoestado.'</a>';

				
                $level = substr_count($row['nombre'], ".");
				$spanStyle ="";
				
				if($row['estado'] == 'Inactivo')

					$spanStyle='label label-danger';
				else if($row['estado'] == 'Activo')
					$spanStyle='label label-successful';
				else
					$spanStyle='label label-warning';
				
				if($row['contacto_principal']==1)
					$principal='<label class="label label-warning">Principal</label>';
				else
					$principal='';
					
                $response->rows[$i] = array("id" => $row['id'], 'cell' => array(
                    'id' => $row['id'],
                    'nombre' => "<a href='' class='verdetallenombre' data-id='".$row['id']."'><span style='".$spanStyle."'>".$row['nombre']."</span></a> ".$principal,
					'cargo' => $row['cargo'],
                    'email' => $row['email'],
                    'celular' => $row['celular'],
                    'telefono' => $row['telefono'],
                    'estado' => "<label class='".$spanStyle." verdetalleestado cambiarestadoseparado' data-id='".$row['id']."'>".$row['estado']."</label>",
					'estadoestado'=>$row['estado'],
					'principal'=>$row['contacto_principal'],
					'options' => $hidden_options,
                    'link' => $link_option,
                    ));
                $i++;
            }
        }

        echo json_encode($response);
        exit;
}

function ajax_cargar_contacto(){
	$id=$this->input->post('id');

	$contacto=$this->SegAseguradoraContactoRepository->verContacto($id);
	$nombre_aseguradora=$contacto->nombreAseguradora->nombre;
	$contacto=$contacto->toArray();
	$resources['datos']=$contacto;
	$resources['datos']['uuid_contacto']=bin2hex($contacto['uuid_contacto']);
	$resources['datos']['nombre_aseguradora']=$nombre_aseguradora;
	
	$this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($resources))->_display();
	exit;
}

function ajax_cambiar_estado_contacto(){
	$id=$this->input->post('id');

	$contacto=$this->SegAseguradoraContactoRepository->verContacto($id);
	$nombre_aseguradora=$contacto->nombreAseguradora->nombre;
	
	if($contacto->estado=='Activo')
	{
		$spanStyle='label label-danger';
		$nuevoestado='Inactivo';
	}
	else
	{
		$nuevoestado='Activo';
		$spanStyle='label label-successful';
	}
	
	$contacto->estado=$nuevoestado;
	$contacto->save();
	$estadoestado=$contacto->estado;
	$contacto=$contacto->toArray();
	$resources['datos']=$contacto;
	$resources['datos']['uuid_contacto']=bin2hex($contacto['uuid_contacto']);
	$resources['datos']['nombre_aseguradora']=$nombre_aseguradora;
	$resources['datos']['estadoestado']=$estadoestado;
	$resources['datos']['labelestado']=$spanStyle;
	
	$this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($resources))->_display();
	exit;
}

function ajax_cambiar_contacto_principal(){
	$id=$this->input->post('id');

	$contacto=$this->SegAseguradoraContactoRepository->verContacto($id);
	$cambiarprincipal=$this->SegAseguradoraContactoRepository->cambiarPrincipal($contacto->aseguradora_id);
	$nombre_aseguradora=$contacto->nombreAseguradora->nombre;
	
	$contacto->contacto_principal=1;
	$contacto->save();
	$contacto=$contacto->toArray();
	$resources['datos']=$contacto;
	$resources['datos']['uuid_contacto']=bin2hex($contacto['uuid_contacto']);
	$resources['datos']['nombre_aseguradora']=$nombre_aseguradora;
	$resources['datos']['principal']=1;
	
	$this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($resources))->_display();
	exit;
}

public function imprimirContacto($uuid=null)
{
	if($uuid==null){
		return false;
	}
	//$uuid=$this->input->post('id');
	
	$contacto=$this->SegAseguradoraContactoRepository->verContactoUiid(hex2bin(strtolower($uuid)));
	$data   = ['contacto'=>$contacto];
	$dompdf = new Dompdf();
	$html = $this->load->view('pdf/formulariocontacto', $data,true);
	$dompdf->loadHtml($html);
	$dompdf->setPaper('A4', 'portrait');
	$dompdf->render();
	$dompdf->stream($contacto->nombre);
	
	
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
        'public/assets/js/plugins/bootstrap/select2/select2.min.js',
        'public/assets/js/plugins/bootstrap/select2/es.js',
		'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
        'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
		'public/assets/js/default/vue/directives/inputmask.js',
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
        'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
        'public/assets/css/plugins/bootstrap/select2.min.css',
    ));
  }    
}
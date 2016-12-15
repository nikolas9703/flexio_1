<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
use Illuminate\Database\Capsule\Manager as Capsule;
use League\Csv\Writer as Writer;
class Agentes extends CRM_Controller
{
	private $id_empresa;
	private $id_usuario;
	private $empresaObj;

	function __construct() {
        parent::__construct();

        $this->load->model('agentes_orm');
        $this->load->model('Catalogo_orm');
        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();
		$uuid_empresa = $this->session->userdata('uuid_empresa');
		//$this->empresaObj  = Empresa_orm::findByUuid($uuid_empresa);
		$empresaObj  = new Buscar(new Empresa_orm,'uuid_empresa');
		$this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
		$this->id_usuario   = $this->session->userdata("huuid_usuario");
		$this->id_empresa   = $this->empresaObj->id;
        }
    public function listar() {

    	$data = array();

    	$this->assets->agregar_css(array(
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
			'public/assets/css/modules/stylesheets/agentes.css',
            'public/assets/css/plugins/bootstrap/bootstrap-tagsinput.css',
    	));
    	$this->assets->agregar_js(array(
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/plugins/bootstrap/bootstrap-tagsinput.js',
    		'public/assets/js/modules/agentes/listar_agentes.js',
			'public/assets/js/default/formulario.js',
            //'public/assets/js/default/grid.js',
    	));       

    	//Breadcrum Array
    	$breadcrumb = array(
    		"titulo" => '<i class="fa fa-child"></i> Agentes',
            //"filtro" => true,
            "menu" => array(
                'nombre' => "Crear",
				'url' => "agentes/crear",
                "opciones" => array(
                    "#exportarBtn" => "Exportar",
                )
            ),
            "ruta" => array(
                0 => array("nombre" => "Seguros", "url" => "#",  "activo" => false),
                1 => array("nombre" => "<b>Agentes</b>", "activo" => true)                
            ),
            "filtro"    => false,
            "menu"      => array()
    	);
        $breadcrumb["menu"] = array(
    		"url"	=> 'agentes/crear',
    		"clase" => 'crearBoton',
    		"nombre" => "Crear"
    	);
        $breadcrumb["menu"]["opciones"]["#exportarBtn"] = "Exportar";
        
        $data['mensaje'] = $this->session->flashdata('mensaje');
    	$this->template->agregar_contenido($data);
    	$this->template->agregar_titulo_header('Listado de Agentes');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar($breadcrumb);
    }
    /**
     * Se usa en propiedades
     * @access	public
     * @param
     * @return	tabla
     */
    public function ajax_seleccionar_porcentaje() {
    	//Si es una peticion AJAX
    	if($this->input->is_ajax_request()){
    		$uuid_agente = $this->input->post('uuid_agente', true);
    		$response = $this->agentes_model->seleccionar_informacion_agente($uuid_agente);
    		 
    		$json = '{"results":['.json_encode($response).']}';
    		echo $json;
    		exit;
    		 
    	}
    }
    public function ajax_listar() {
        //$uuid_usuario = $this->session->userdata('huuid_usuario');
        //$usuario = Agentes_orm::findByUuid($uuid_usuario);
        /*$usuario_org = $usuario->organizacion;

        $orgid = $usuario_org->map(function($org){
            return $org->id;
        });*/

        $clause = array(
            "nombre"    => $this->input->post("nombre"),
			"apellido"  => $this->input->post("apellido"),
            "telefono"  => $this->input->post("telefono"),
            "correo"    => $this->input->post("correo"),
        );


        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = Agentes_orm::listar($clause, NULL, NULL, NULL, NULL)->count();
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $rows = Agentes_orm::listar($clause, $sidx, $sord, $limit, $start);


        //Constructing a JSON
        $response = new stdClass();
        $response->page     = $page;
        $response->total    = $total_pages;
        $response->records  = $count;
        $i=0;

        if(!empty($rows->toArray())){
            foreach ($rows->toArray() AS $i => $row){
                $hidden_options = "<a href=". base_url('agentes/ver/'.strtoupper($row['uuid_agente'])) ." class='btn btn-block btn-outline btn-success'>Ver Agente</a>";
                 $hidden_options .= '<a href="#" id="cambiarEtapaConfirmBtn" class="btn btn-block btn-outline btn-success">Crear Reporte de Comisión</a>';
                 $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'.$row['id'].'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $response->rows[$i]["id"] = $row['id'];
				$nombre_agente =  $row["nombre"] ." ".$row["apellido"];
                $response->rows[$i]["cell"] = array(
                    $row['id'],
                    "<a href='" . base_url('agentes/ver/'.($row['uuid_agente'])) . "'>" . $nombre_agente  . "</a>",
                    $row['identificacion'],
                    $row['telefono'],
                    $row['correo'],
                    $row['porcentaje_participacion'].'%',
                    $link_option,
                    $hidden_options
                );
            $i++;    
            }
        }

        echo json_encode($response);
        exit;
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
		
                $clause['agentes'] = $id;
                
		$ajustadores = Agentes_orm::listar($clause, NULL, NULL, NULL, NULL);
		if(empty($ajustadores)){
			return false;
		}
		$i=0;
		foreach ($ajustadores AS $row)
		{
			$csvdata[$i]['nombre'] = $row->nombre . " " . $row->apellido;
			$csvdata[$i]["cedula"] = utf8_decode(Util::verificar_valor($row->identificacion));
			$csvdata[$i]["telefono"] = utf8_decode(Util::verificar_valor($row->telefono));
			$csvdata[$i]["email"] = utf8_decode(Util::verificar_valor($row->correo));
			$csvdata[$i]["participacion"] = utf8_decode(Util::verificar_valor($row->porcentaje_participacion));
			$i++;
		}
		//we create the CSV into memory
		$csv = Writer::createFromFileObject(new SplTempFileObject());
		$csv->insertOne([
			'Nombre',
			'Cedula',
			'Telefono',
			'Email',
			'Participacion'
		]);                
		$csv->insertAll($csvdata);
		$csv->output("agentes-". date('ymd') .".csv");
		exit();
    }
    
    /**
     * Cargar Vista Parcial de Tabla
     *
     * @return void
     */
    public function ocultotabla() {
    	//If ajax request
    	$this->assets->agregar_js(array(
    		'public/assets/js/modules/agentes/tabla.js'
    	));
    	
    	$this->load->view('tabla');
    }    
    function crear() {
    	$data = array();
    	$mensaje = array();

    	$this->assets->agregar_js(array(
    		'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
    		'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
    		'public/assets/js/default/formulario.js',
            'public/assets/js/modules/agentes/crear.js'
    	));

        //$total = Cliente_orm::where('empresa_id','=',$this->id_empresa)->count();

        //$data['info']['codigo'] = Util::generar_codigo('CUS', $total+ 1);
        //$data['info']['identificacion'] = Catalogo_orm::where('identificador','=','Identificacion')->get(array('id_cat','valor'));
        $data['info']['provincias'] = Catalogo_orm::where('identificador','like','Provincias')->orderBy("orden")->get(array('key','etiqueta'));
        $data['info']['letras'] = Catalogo_orm::where('identificador','like','Letra')->get(array('key','etiqueta'));
        $data['info']['tipo_identificacion'] = $tipo_identificacion = Catalogo_orm::where('identificador','like','tipo_identificacion')->orderBy("orden")->get(array('key','etiqueta'));
    	 
    	$this->template->agregar_titulo_header('Nuevo Agente');
    	$this->template->agregar_breadcrumb(array(
    		"titulo" => '<i class="fa fa-child"></i> Agentes',
    		"ruta" => array(
    			0 => array(
    				"nombre" => "Seguros",
    				"activo" => false
    			),
    			1 => array(
    				"nombre" => 'Agentes',
    				"url"	=> 'agentes/listar',
    				"activo" => false
    			),
    			2 => array(
    				"nombre" => '<b>Crear</b>',
    				"activo" => true
    			)
    		)
    	));
        $data['mensaje'] = $this->session->flashdata('mensaje');
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar();
    }
	function guardar() {
            
            if($_POST){
			unset($_POST["campo"]["guardar"]);
                        
			$campo = Util::set_fieldset("campo");                        
			//formato de identificacion
            if($campo['letra'] == '0' || !isset($campo['letra'])){
                $cedula = $campo['provincia']."-".$campo['tomo']."-".$campo['asiento'];
                $campo['letra'] = '0';
                //$natural = array_merge($natural, array('identificacion'=>array('letra'=>$natural['letra'],'cedula'=> $cedula)));
                $campo['identificacion'] = $cedula;
            }else if($campo['letra'] == 'E' || $campo['letra'] == 'N' || $campo['letra'] == 'PE' || $campo['letra'] == 'PI' || $campo['letra'] == 'PAS'){
                //buscar la letra
                $cedula = $campo['letra']."-".$campo['tomo']."-".$campo['asiento'];
                if($campo['letra'] == 'PI') $cedula =  $campo['provincia'].$campo['letra']."-".$campo['tomo']."-".$campo['asiento'];
                //$natural = array_merge($natural, array('identificacion'=>array('letra'=>$natural['letra'],'cedula'=> $cedula)));
                $campo['identificacion'] = $cedula;
            }
            if($campo['tipo_identificacion'] == 'PAS'){
                $cedula = $campo['pasaporte'];
                $campo['identificacion'] = $cedula;
                $campo['letra'] = "PAS";
            }
            if($campo['tipo_identificacion'] == 'RUC'){
                $cedula = $campo['tomo_ruc']."-".$campo['folio']."-".$campo['asiento_ruc']."-".$campo['digito'];
                $campo['identificacion'] = $cedula;
                $campo['letra'] = "RUC";
            }

            
			if(!isset($campo['uuid'])){
				$campo['empresa_id'] = $this->id_empresa;
                $campo['fecha_creacion'] = date('Y-m-d H:i:s');
			}
            
			Capsule::beginTransaction();
			try {
				if(!isset($campo['uuid'])){//crear agente
                                    $agente = Agentes_orm::create($campo);
                                        
				}else{
                                    $agenteObj  = new Buscar(new Agentes_orm(),'uuid_agente');
                                    $agente = $agenteObj->findByUuid($campo['uuid']);
                                    if(is_null($agente)){
					$mensaje = array('clase' =>'alert-warning', 'contenido' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
					$this->session->set_flashdata('mensaje', $mensaje);
                                        redirect(base_url('agentes/listar'));
                                    }else{
					unset($campo['uuid']);
                                        $agente->update($campo);
                                    }
				}
				Capsule::commit();
			}catch(ValidationException $e){
				log_message('error', $e);
				Capsule::rollback();
			}

			if(!is_null($agente)){
				$mensaje = array('clase' =>'alert-success', 'contenido' =>'<b>¡&Eacute;xito!</b> Se ha guardado correctamente '.$agente->nombre);
			}else{
				$mensaje = array('clase' =>'alert-danger', 'contenido' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
			}


		}else{
			$mensaje = array('clase' =>'alert-warning', 'contenido' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
		}

		$this->session->set_flashdata('mensaje', $mensaje);
		redirect(base_url('agentes/listar'));

	}
    public function existsIdentificacion() {
            $campo = Util::set_fieldset("campo");
            $response = new stdClass();
            if(isset($campo['letra'])){
                if($campo['letra'] == '0' || !isset($campo['letra'])){
                    $cedula = $campo['provincia']."-".$campo['tomo']."-".$campo['asiento'];
                    $campo['letra'] = '0';
                    //$natural = array_merge($natural, array('identificacion'=>array('letra'=>$natural['letra'],'cedula'=> $cedula)));
                    $campo['identificacion'] = $cedula;
                }else if($campo['letra'] == 'E' || $campo['letra'] == 'N' || $campo['letra'] == 'PE' || $campo['letra'] == 'PI' || $campo['letra'] == 'PI' || $campo['letra'] == 'PAS'){
                    //buscar la letra
                    $cedula = $campo['letra']."-".$campo['tomo']."-".$campo['asiento'];
                    if($campo['letra'] == 'PI') $cedula =  $campo['provincia'].$campo['letra']."-".$campo['tomo']."-".$campo['asiento'];
                    //$natural = array_merge($natural, array('identificacion'=>array('letra'=>$natural['letra'],'cedula'=> $cedula)));
                    $campo['identificacion'] = $cedula;
                }
            }
            
            
            if($campo['tipo_identificacion'] == 'PAS'){
                $cedula = $campo['pasaporte'];
                $campo['identificacion'] = $cedula;
                $campo['letra'] = "PAS";
            }
            
            if($campo['tipo_identificacion'] == 'RUC'){
                $cedula = $campo['tomo_ruc']."-".$campo['folio']."-".$campo['asiento_ruc']."-".$campo['digito'];
                $campo['identificacion'] = $cedula;
                $campo['letra'] = "RUC";
            }
            
        $agenteObj  = new Buscar(new Agentes_orm(),'identificacion');
        $agente = $agenteObj->findById($campo['identificacion']);
        if(is_null($agente)){
            $response->existe =  false;
        }else{
            $response->existe =  true;
        }
        echo json_encode($response);
        exit;
    }
    
    public function ocultoformulario($data=NULL) {
        $this->assets->agregar_js(array(
            //'public/assets/js/modules/agentes/crear.js'
        ));
        $this->load->view('formulario', $data);
    }    
    function ver($uuid=NULL) {
		$data=array();

    	$this->assets->agregar_css(array(
    		'public/assets/css/default/ui/base/jquery-ui.css',
    		'public/assets/css/default/ui/base/jquery-ui.theme.css',
    		'public/assets/css/plugins/bootstrap/bootstrap-tagsinput.css',
    		'public/assets/css/plugins/jquery/switchery.min.css',
    	));
    	$this->assets->agregar_js(array(
    		'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
    		'public/assets/js/plugins/ckeditor/adapters/jquery.js',
    		'public/assets/js/plugins/bootstrap/bootstrap-tagsinput.js',
    		'public/assets/js/plugins/jquery/switchery.min.js',
    		'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
    		'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
    		'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
    		'public/assets/js/default/formulario.js',
            'public/assets/js/modules/agentes/ver.js'
    	));

    	//Agregra variables PHP como variables JS
    	$this->assets->agregar_var_js(array(
    		"id_agente" => $uuid,
    		"permiso_editar_agente" => $this->auth->has_permission('ver-agente__editarAgente', 'agentes/ver-agente/(:any)') == true ? 'true' : 'false',
    	));
        $data['info']['tipo_identificacion'] = $tipo_identificacion = Catalogo_orm::where('identificador','like','tipo_identificacion')->orderBy("orden")->get(array('key','etiqueta'));
        $data['info']['provincias'] = $provincias = Catalogo_orm::where('identificador','like','Provincias')->orderBy("orden")->get(array('key','etiqueta'));   
        $data['info']['letras'] = Catalogo_orm::where('identificador','like','Letra')->get(array('key','etiqueta'));

        if(is_null($uuid)){
            $mensaje = array('clase' =>'alert-warning', 'contenido' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('agentes/listar'));
        }else{
            $agenteObj  = new Buscar(new Agentes_orm,'uuid_agente');
            $agente = $agenteObj->findByUuid($uuid);
            
            if(is_null($agente)){
                $mensaje = array('clase' =>'alert-warning', 'contenido' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
                $this->session->set_flashdata('mensaje', $mensaje);
                redirect(base_url('agentes/listar'));
            }else{

                $data['info']['agente'] = $agente->toArray();
                $data['info']['agente']['letraUnica']= $agente['letra'];
                $identificacion = $agente['identificacion'];
                if($agente['letra'] == '0' || empty($agente['letra']) || !isset($agente['letra'])){
                    list($provincia, $tomo, $asiento) =  explode("-", $identificacion);
					$data['info']['agente']['provincia'] = $provincia;
                    $data['info']['agente']['letra'] = "0";
                    $data['info']['agente']['tomo'] = $tomo;
                    $data['info']['agente']['asiento'] = $asiento;
                }elseif($agente['letra'] == 'N' || $agente['letra'] == 'PE' || $agente['letra'] == 'E'){
                    list($letra, $tomo, $asiento) =  explode("-", $identificacion);
                    $data['info']['agente']['letra'] = $letra;
                    $data['info']['agente']['tomo'] = $tomo;
                    $data['info']['agente']['asiento'] = $asiento;
                }elseif($agente['letra'] == 'PI'){
                    list($provincia, $tomo, $asiento) =  explode("-", $identificacion);
					$provincia = str_replace("PI","",$provincia);
					$data['info']['agente']['provincia'] = $provincia;
                    $data['info']['agente']['letra'] = 'PI';
                    $data['info']['agente']['tomo'] = $tomo;
                    $data['info']['agente']['asiento'] = $asiento;
                }elseif($agente['letra'] == 'PAS'){
                    $data['info']['agente']['letra'] = 'PAS';
                    $data['info']['agente']['pasaporte'] = $identificacion;
                }
                if($agente['letra'] == 'RUC'){
                    list($tomo_ruc, $folio, $asiento_ruc, $digito) =  explode("-", $identificacion);					
		    $data['info']['agente']['tomo_ruc'] = $tomo_ruc;
                    $data['info']['agente']['folio'] = $folio;
                    $data['info']['agente']['asiento_ruc'] = $asiento_ruc;
                    $data['info']['agente']['digito'] = $digito;    
                }
            }
        }
        $data['uuid_agente'] = $agente['uuid_agente'];

        $this->template->agregar_breadcrumb(array(
    		"titulo" => '<i class="fa fa-child"></i> '.$data['info']['agente']['nombre']." ".$data['info']['agente']['apellido'],
    		"ruta" => array(
    			0 => array(
    				"nombre" => "Seguros",
    				"activo" => false
    			),
    			1 => array(
    				"nombre" => 'Agentes',
    				"url"	=> 'agentes/listar',
    				"activo" => false
    			),
    			2 => array(
    				"nombre" => $data['info']['agente']['nombre']." ".$data['info']['agente']['apellido'],
    				"activo" => true
    			)
    		)
    	));
        
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar();
    }
}
?>
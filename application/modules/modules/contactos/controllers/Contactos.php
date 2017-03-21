<?php
/**
 * Configuracion
 *
 * Módulo de gestión de contactos
 *
 * @package    PensaApp
 * @subpackage Controller
 * @category   Controllers
 * @author     Pensanomica Team
 * @link       http://www.pensanomica.com
 *
 */
 use Illuminate\Database\Capsule\Manager as Capsule;
class Contactos extends CRM_Controller {
  private $id_empresa;
  private $id_usuario;
  private $empresaObj;

  function __construct() {
      parent::__construct();

      $this->load->model('Contacto_orm');
      $this->load->model('clientes/Cliente_orm');
      $this->load->model('aseguradoras/Aseguradoras_orm');
      $this->load->model('clientes/Catalogo_orm');
        //HMVC Load Modules
        //$this->load->module(array('actividades'));
      $uuid_empresa = $this->session->userdata('uuid_empresa');
    	//$this->empresaObj  = Empresa_orm::findByUuid($uuid_empresa);
    	$empresaObj  = new Buscar(new Empresa_orm,'uuid_empresa');
    	$this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
    	$this->id_usuario   = $this->session->userdata("huuid_usuario");
    	$this->id_empresa   = $this->empresaObj->id;
}

    public function index() {

        /*
         * if (! $this->auth->has_permission ( 'acceso' )) { // No, tiene permiso, redireccionarlo. redirect ( '/' ); }
         */
        $this->template->agregar_titulo_header('Dashboard');
        $this->template->agregar_breadcrumb(array(
            "path" => array(
                0 => array(
                    "name" => '<b>Inicio</b>',
                    "active" => true
                )
            )
        ));
        $this->template->visualizar();
    }

    function ajax_guardar_contacto() {

      $campo =  $_POST;
      $campos = $this->input->post("campos");
       // $campo = $this->input->post("campo");
       // dd($campo);
        $cedula = null;
      $response = [];
      //dd($campo);
      if(!empty($campo)){
          //Aseguradoras - Contacto nuevo
          if(isset($campo['vista']) && $campo['vista'] == "aseguradoras"){ //Aseguradoras
            if(!isset($campo['id'])) {
                unset($campo['vista']);
                $uuid = $campo['uuidcliente'];
                unset($campo['uuidcliente']);
                //Objeto para Vista Aseguradoras
                $seguroObj = new Buscar(new Aseguradoras_orm, 'uuid_aseguradora');
                $seguro = $seguroObj->findByUuid($uuid);
                //Relacion Contacto-Aseguradora
                $campo['id_aseguradora'] = $seguro->id;
                $campo['empresa_id'] = $this->id_empresa;
                $contacto = Contacto_orm::create($campo);

                //Clientes - Contacto nuevo
            } else{
                $contacto = Contacto_orm::find($campo['id']);
                unset($campo['uuidcliente']);
                unset($campo['created_at']);
                unset($campo['id']);
                unset($campo['empresa_id']);
                unset($campo['id_aseguradora']);
                $contacto->update($campo);
            }
        }else if(isset($campos['id']) && empty($campos['id'])) { //Clientes

            $campos['empresa_id'] = $this->id_empresa;
            $contacto = \Flexio\Modulo\Contactos\Models\Contacto::create($campos);

        }else if(isset($campos['id']) && !empty($campos['id'])){

            $contacto = \Flexio\Modulo\Contactos\Models\Contacto::find($campos['id']);
            $contacto->update($campos);

        }

        if(!is_null($contacto)){
            $response = array('clase'=>'alert-success','contenido'=> '<b>Exito</b> Se ha guardado correctamente '.$contacto->nombre);
        }else{
            $response = array('clase'=>'alert-error','contenido'=> '<b>Error</b> No se ha guardado correctamente ');
        }
        echo json_encode($response);

      }
      exit;
    }

    function ajax_contacto_info() {

      //dd($this->input->is_ajax_request());

      $response = array();
      $uuid = $this->input->post('uuid_contacto');
      $contactoObj  = new Buscar(new Contacto_orm,'uuid_contacto');
      $contacto = $contactoObj->findByUuid($uuid);
      if(!is_null($contacto)){
        $response = $contacto->toArray();
      }
      echo json_encode($response);

        exit;
    }

    function ajax_contacto_inactivo() {

        //dd($this->input->is_ajax_request());

        $response = array();
        $uuid = $this->input->post('uuid_contacto');
        $contactoObj  = new Buscar(new Contacto_orm,'uuid_contacto');
        $contacto = $contactoObj->findByUuid($uuid);
        if(!is_null($contacto->id)){
            Contacto_orm::contacto_inactivo($contacto->id);
        }
        echo json_encode($response);
        exit;
    }
    /**
     * Cargar Vista de Tabla
     *
     * @return void
     */
    public function ocultotabla($id_cliente = NULL) {

        // If ajax request
        $this->assets->agregar_js(array(
            'public/assets/js/modules/contactos/tabla.js',
        ));

        if(is_array($id_cliente))
        {
            $this->assets->agregar_var_js([
                "campo" => collect($id_cliente)
            ]);
        }
        elseif(!empty($id_cliente))
        {
            // Agregra variables PHP como variables JS
            $this->assets->agregar_var_js(array(
                "id_cliente" => $id_cliente
            ));

        }

        $this->load->view('tabla');
    }
    /**
     * Funcion para exportar los clientes potenciales
     * seleccionados a formato CSV.
     *
     * return void
     */
    function ajax_exportar($id_contactos=NULL) {

    	if($id_contactos==NULL){
    		return false;
    	}

    	$id_contactos = explode("-", $id_contactos);

    	$contactos_csv = $this->contactos_model->generar_csv($id_contactos);

    	if(empty($contactos_csv)){
    		return false;
    	}

    	header('Set-Cookie: fileDownload=true; path=/');
    	header('Cache-Control: max-age=60, must-revalidate');
    	header("Content-type: text/csv");
    	header('Content-Disposition: attachment; filename="clientes__'. date('ymdi') . '.csv"');

    	echo $contactos_csv;
    }
    /**
     * Cargar Vista de Grid
     *
     * @return void
     */
    public function grid() {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/contactos/grid.js'
        ));

        $data = array(
            "contactos" => $this->input->post("contactos")
        );

        $this->load->view('grid', $data);
    }

    public function listar_contactos() {
        if (!$this->auth->has_permission('acceso')) {
            // No, tiene permiso, redireccionarlo.
            redirect('/');
        }

        $this->assets->agregar_css(array(
          'public/assets/css/default/ui/base/jquery-ui.css',
          'public/assets/css/default/ui/base/jquery-ui.theme.css',
          'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
          'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
          'public/assets/css/plugins/jquery/awesome-bootstrap-checkbox.css',
        	'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
        	'public/assets/css/plugins/jquery/fileinput/fileinput.css',
            'public/assets/css/modules/stylesheets/contactos.css'
        ));
        $this->assets->agregar_js(array(
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/modules/contactos/listar_contactos.js',
         		'public/assets/js/plugins/jquery/fileinput/fileinput.js',
        		'public/assets/js/plugins/jquery/fileinput/fileinput_locale_es.js',
            )
        );



        $datos = array();

        $this->template->agregar_titulo_header('Listado de Contactos');
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-user"></i> Contactos',
            "ruta" => array(
                0 => array(
                    "nombre" => "Ventas",
                    "activo" => false
                ),
                1 => array(
                    "nombre" => '<b>Contactos</b>',
                    "url" => 'contactos/listar-contactos',
                    "activo" => true
                )
            ),
            "filtro" => true,
            "menu" => array(
                "nombre" => "Crear",
                "url" => "contactos/crear-contacto/0",
                "opciones" => array()
                /*"opciones" => array(
                    "contactos/campana" 	=> "Agregar a Campaña de Mercadeo",
                    "#exportarContactosBtn" => "Exportar",
                )*/
            )
        );

        $this->template->agregar_contenido($datos);
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->visualizar($breadcrumb);
    }

    public function ajax_listar_contactos() {

        // Just Allow ajax request
        if(!$this->input->is_ajax_request()){
  	      return false;
  	    }

        $id_cliente = $this->input->post("id_cliente");

        $clause = array('empresa_id' => $this->empresaObj->id);
        if(!empty($id_cliente)) {
            //Objeto para Vista clientes
            $clienteObj = new Buscar(new Cliente_orm, 'uuid_cliente');
            $cliente = $clienteObj->findByUuid($id_cliente);
            //Objeto para Vista Aseguradoras
            $seguroObj = new Buscar(new Aseguradoras_orm(), 'uuid_aseguradora');
            $seguro = $seguroObj->findByUuid($id_cliente);


            if (!empty($cliente)) { //Relacion Contacto-Cliente
                $clause['cliente_id'] = $cliente->id;

            } else if (!empty($seguro)) { //Relacion Contacto-Aseguradora
                $clause['id_aseguradora'] = $seguro->id;
            }
        }

        $clause["campo"] = $this->input->post('campo', TRUE);
        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = Contacto_orm::lista_totales($clause);
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
  			$contactos = Contacto_orm::listar($clause ,$sidx, $sord, $limit, $start);

        // Constructing a JSON
        $response = new stdClass ();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
        $i = 0;

            if (!empty ($contactos->toArray())) {
                foreach ($contactos as  $row) {
                    $hidden_options = "";
                    $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-nombre="' . $row->nombre . '" data-contacto="' . $row->uuid_contacto . '"><i class="fa fa-cog"></i> <span class="hidden-sm hidden-xs">Opciones</span></button>';
                    $response->rows[$i]["id"] = $row->uuid_contacto;
                    $url = preg_match("/contactos/i", $_SERVER['HTTP_REFERER']) ? base_url('contactos/ver-contacto/' . $row->uuid_contacto) : "#";
                    $nombre_link = $row->nombre;
                    if (preg_match("/contactos/i", $_SERVER['HTTP_REFERER'])) {
                        $nombre_link = "<a href='" . $url . "'>" . $row->nombre . "</a> ";
                    }
                    $label_contacto_principal = !preg_match("/contactos/i", $_SERVER['HTTP_REFERER']) && $row->principal == 1 ? '<span class="label label-warning">Principal</span>' : "";
                    if (preg_match("/aseguradoras/i", $_SERVER['HTTP_REFERER'])){
                        $hidden_options = '<a href="javascript:" data-contactouuid="' . $row->uuid_contacto . '" class="btn btn-block btn-outline btn-success aseguradoraEditarContacto">Editar</a>';
                         $hidden_options .= '<a href="javascript:" data-contactouuid="' . $row->uuid_contacto . '" class="btn btn-block btn-outline btn-success aseguradoraEstadoContacto">Desactivar</a>';
                    }else if(preg_match("/clientes/i", $_SERVER['HTTP_REFERER'])){
                        $hidden_options = '<a href="javascript:" data-id="' . $row->id . '" data-uuid_contacto="' . $row->uuid_contacto . '" class="btn btn-block btn-outline btn-success clienteVerContacto">Ver Contacto</a>';
                        //$hidden_options .= '<a href="javascript:" data-id="' . $row->uuid_contacto . '" class="exportarTablaCliente btn btn-block btn-outline btn-success">Nueva Oportunidad</a>'; //COMENTADO TEMPORALMENTE HASTA NUEVO AVISO
                        //$hidden_options .= '<a href="javascript:" data-id="' . $row->uuid_contacto . '" class="exportarTablaCliente btn btn-block btn-outline btn-success">Registrar Actividad</a>'; //COMENTADO TEMPORALMENTE HASTA NUEVO AVISO
                        //$hidden_options .= '<a href="javascript:" data-id="' . $row->uuid_contacto . '" class="exportarTablaCliente btn btn-block btn-outline btn-success">Subir Documento</a>'; //COMENTADO TEMPORALMENTE HASTA NUEVO AVISO
                        //$hidden_options .= '<a href="javascript:" data-id="' . $row->uuid_contacto . '" class="exportarTablaCliente btn btn-block btn-outline btn-success">Agregar Caso</a>'; //COMENTADO TEMPORALMENTE HASTA NUEVO AVISO
                        //$hidden_options .= '<a href="javascript:" data-id="' . $row->uuid_contacto . '" class="exportarTablaCliente btn btn-block btn-outline btn-success">Agregar a Campaña de Mercadeo</a>'; //COMENTADO TEMPORALMENTE HASTA NUEVO AVISO
                    }
                    $this->assets->agregar_var_js(array(
                        "tipo_id_cont" => $row->tipo_identificacion
                    ));
                   $label_principal = ($row->principal == 1)? '<span class="label label-warning">Principal</span>':'';
                    $response->rows [$i]["cell"] = array(
                        //$nombre_link.$label_contacto_principal,
                        $row->principal,
                        '<a href="javascript:" class="link editarContacto clienteVerContacto"  data-contactoUuid="'. $row->uuid_contacto .'"  data-uuid_contacto="'. $row->uuid_contacto .'" data-id="'. $row->id .'">'.$row->nombre.'</a> ' .$label_principal ,
                        $row->cargo,
                        $row->correo,
                        $row->celular,
                        $row->telefono,
                        $row->ultimo_contacto,
                        $link_option,
                        $hidden_options
                    );
                    $i++;
                }
            }
            echo json_encode($response);
            exit ();

    }

    function ajax_get_uuid_nombre_comercial($clause = array()) {

        //Just Allow ajax request
        if ($this->input->is_ajax_request()) {
            $clause = 'uuid_cliente=UNHEX("' . $_GET["uuid_cliente"] . '") AND uuid_contacto=UNHEX("' . $_GET["uuid_contacto"] . '")';
            $result = $this->contactos_model->get_uuid_nombre_comercial($clause);

            foreach ($result as $key => $value) {
                if (!empty($result[$key]["uuid_sociedad"])) {
                    $result[$key]["uuid_sociedad"] = bin2hex($result[$key]["uuid_sociedad"]);
                }
                if (!empty($result[$key]["uuid_cliente"])) {
                    $result[$key]["uuid_cliente"] = bin2hex($result[$key]["uuid_cliente"]);
                }

                if (!empty($result[$key]["uuid_contacto"])) {
                    $result[$key]["uuid_contacto"] = bin2hex($result[$key]["uuid_contacto"]);
                }
            }

            $json = '{"results":[' . json_encode(Util::utf8ize($result)) . ']}';
            echo $json;
            exit;
        } else {
            return $this->clientes_model->comp__seleccionar_sociedad($clause);
        }
    }

    function ajax_seleccionar_contacto() {
        //Just Allow ajax request
        if(!$this->input->is_ajax_request()){
            return false;
        }

        $response = $this->contactos_model->seleccionar_informacion_de_contacto();

        $json = '{"results":['.json_encode($response).']}';
        echo $json;
        exit;
    }
    function ajax_seleccionar_nombres_comerciales() {
    	//Si es una peticion AJAX
    	if($this->input->is_ajax_request()){

    		$uuid_cliente = $this->input->post('uuid_cliente', true);
    		$uuid_contacto = $this->input->post('uuid_contacto', true);

    		$response = $this->contactos_model->seleccionar_nombres_comerciales($uuid_cliente, $uuid_contacto);

    		$json = '{"results":['.json_encode($response).']}';
    		echo $json;
    		exit;

    	}
    }
    function ajax_seleccionar_todos_contacto() {
    	//Just Allow ajax request
    	if(!$this->input->is_ajax_request()){
    		return false;
    	}

    	$response = $this->contactos_model->seleccionar_todos_contactos();

    	$json = '{"results":['.json_encode($response).']}';
    	echo $json;
    	exit;
    }
    /**
     * Funcion para eliminar un nombre comercial
     * de la relacion de cliente contacto.
     *
     * @author: jluispinilla
     * @return boolean
     */
    function ajax_eliminar_cliente_nombre_comercial() {
    	//Just Allow ajax request
    	if(!$this->input->is_ajax_request()){
    		return false;
    	}

    	$response = $this->contactos_model->eliminar_cliente_nombre_comercial();

    	$json = '{"results":['.json_encode($response).']}';
    	echo $json;
    	exit;
    }

    /**
     * Funcion para eliminar un cliente
     * de la relacion de cliente contacto.
     *
     * @author: jluispinilla
     * @return boolean
     */
    function ajax_eliminar_cliente() {
    	//Just Allow ajax request
    	if(!$this->input->is_ajax_request()){
    		return false;
    	}

    	$response = $this->contactos_model->eliminar_cliente();

    	$json = '{"results":['.json_encode($response).']}';
    	echo $json;
    	exit;
    }

    /**
     * Funcion para marcar un contacto
     * como contacto principal de un
     * cliente.
     *
     * @author: jluispinilla
     * @return boolean
     */
    function ajax_asignar_contacto_principal() {
    	//Just Allow ajax request
    	if(!$this->input->is_ajax_request()){
    		return false;
    	}
      $clause = array();
      $uuid_contacto = $this->input->post('uuid_contacto', true);
      $uuid_cliente = $this->input->post('uuid_cliente', true);

      $contactoObj  = new Buscar(new Contacto_orm,'uuid_contacto');
      $contacto = $contactoObj->findByUuid($uuid_contacto);

      if($uuid_cliente){
          $seguroObj = new Buscar(new Aseguradoras_orm, 'uuid_aseguradora');
          $seguro = $seguroObj->findByUuid($uuid_cliente);
          $clause['cliente_id'] = $seguro->id;
          $campo = 'id_aseguradora';
      }else{
          $clause['cliente_id'] = $contacto->cliente_id;
          $campo = 'cliente_id';
      }

      $clause['id'] = $contacto->id;
      $response = Contacto_orm::asignar_contacto_principal($campo,$clause);
      $json = json_encode($response);
      echo $json;
      exit;
    }

    function editar_contacto($id_contacto = NULL) {
        $data = array(
        	"contacto" => $this->contactos_model->seleccionar_informacion_de_contacto($id_contacto)
        );

        $mensaje = array();

        if (!empty($_POST))
        {
        	$response = $this->contactos_model->editar_contacto($id_contacto);

            if($this->input->is_ajax_request()){
            	$json = '{"results":['.json_encode($response).']}';
            	echo $json;
            	exit;
            }else{
            	// Guardar Contacto
            	// Ejecutar funcion del Modulo de Contacto
            	if ($response == true) {
            		redirect(base_url('contactos/ver-contacto/'.$id_contacto));
            	} else {
            		// Establecer el mensaje a mostrar
            		$data ["mensaje"] ["clase"] = "alert-danger";
            		$data ["mensaje"] ["contenido"] = "Hubo un error al tratar de actualizar el contacto.";
            	}
            }
        }

        // Introducir mensaje de error al arreglo
        // para mostrarlo en caso de haber error
        $data ["message"] = $mensaje;

        $this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/bootstrap/bootstrap-tagsinput.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
            'public/assets/css/plugins/jquery/switchery.min.css'
        ));
        $this->assets->agregar_js(array(
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/plugins/bootstrap/bootstrap-tagsinput.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/switchery.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/bootstrap/bootstrap-tabdrop.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/default/formulario.js',
            'public/assets/js/modules/contactos/editar_contacto.js'
        ));

        // Agregra variables PHP como variables JS
        $this->assets->agregar_var_js(array(
        	"uuid_usuario" => !empty(CRM_Controller::$uuid_user)?CRM_Controller::$uuid_user:'',
        	"uuid_contacto" => $id_contacto,
            "permiso_editar_contacto" => $this->auth->has_permission('ver-contacto__editarContacto', 'contactos/ver-contacto/(:any)') == true ? 'true' : 'false'
        ));

        $this->template->agregar_titulo_header('Contactos');
        $this->template->agregar_breadcrumb(array(
            "titulo" => '<i class="fa fa-user"></i> Contactos',
            "ruta" => array(
                0 => array(
                    "nombre" => "Ventas",
                    "activo" => false
                ),
                1 => array(
                    "nombre" => 'Contactos',
                    "url" => 'contactos/listar-contactos',
                    "activo" => false
                ),
                2 => array(
                    "nombre" => '<b>Editar</b>',
                    "activo" => true
                )
            )
        ));



         /*
         * Verificar si existe alguna variable de session
        * proveniente de algun formulario de crear/editar
        */
        if($this->session->userdata('actualizadoContacto')){
        	//Borrar la variable de session
        	$this->session->unset_userdata('actualizadoContacto');

        	//Establecer el mensaje a mostrar
        	$data["mensaje"]["clase"] = "alert-success";
        	$data["mensaje"]["contenido"] = "Se ha actualizado el Contacto satisfactoriamente.";
        }



        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    public function editarsubpanel($id_cliente = NULL) {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/contactos/editar_contacto.js'
        ));

        // Agregra variables PHP como variables JS
        $this->assets->agregar_var_js(array(
        	"permiso_editar_contacto" => $this->auth->has_permission('ver-contacto__editarContacto', 'contactos/ver-contacto/(:any)') == true ? 'true' : 'false'
        ));

        $this->template->vista_parcial(array(
            'contactos',
            'editar_contacto'
        ));
    }

    function crear_contacto( $uuid_cliente=NULL ) {
        // Verificar si tiene permiso de crear
        if (!$this->auth->has_permission('acceso', 'contactos/crear-contacto/(:any)')) {
            redirect("/");
        }

        $data = array();
        $mensaje = array();

        if (!empty ($_POST)) {

        	$response = $this->contactos_model->guardar_contacto($_POST);

            if($this->input->is_ajax_request()){
            	$json = '{"results":['.json_encode($response).']}';
            	echo $json;
            	exit;
            }else{
	            if ($response == true) {
	                redirect(base_url('contactos/listar-contactos'));
	            } else {
	                // Establecer el mensaje a mostrar
	                $data ["mensaje"] ["clase"] = "alert-success";
	                $data ["mensaje"] ["contenido"] = "Se ha creado el Contacto satisfactoriamente.";
	            }
            }
        }

        // Introducir mensaje de error al arreglo
        // para mostrarlo en caso de haber error
        $data ["message"] = $mensaje;

        $this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/bootstrap/bootstrap-tagsinput.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
            'public/assets/css/plugins/jquery/switchery.min.css'
        ));
        $this->assets->agregar_js(array(
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/plugins/bootstrap/bootstrap-tagsinput.js',
            'public/assets/js/plugins/jquery/switchery.min.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/bootstrap/bootstrap-tabdrop.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/default/formulario.js',
            'public/assets/js/modules/contactos/crear_contacto.js'
        ));
       // $data['info']['provincias'] = Catalogo_orm::where('tipo', '=', 'provincias')->get(array('id', 'valor'));
      //  $data['info']['letras'] = Catalogo_orm::where('tipo', '=', 'letras')->get(array('key', 'valor'));
        //Agregra variables PHP como variables JS
        $this->assets->agregar_var_js(array(
        	"uuid_usuario" => CRM_Controller::$uuid_user,
        	"uuid_cliente" => $uuid_cliente,
            "tipo_id_cont" => ''
        ));

        $this->template->agregar_titulo_header('Contactos');
        $this->template->agregar_breadcrumb(array(
            "titulo" => '<i class="fa fa-user"></i> Contactos',
            "ruta" => array(
                0 => array(
                    "nombre" => "Ventas",
                    "activo" => false
                ),
                1 => array(
                    "nombre" => 'Contactos',
                    "url" => 'contactos/listar-contactos',
                    "activo" => false
                ),
                2 => array(
                    "nombre" => '<b>Nuevo</b>',
                    "activo" => true
                )
            )
        ));
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    function crearsubpanel() {
        $this->assets->agregar_js(array(
           // 'public/assets/js/modules/contactos/crear_contacto.js',
        ));

        $this->template->vista_parcial(array(
            'contactos',
            'crear_contacto'
        ));

    }

    function crear_contacto_boolean() {
        if (!empty ($_POST)) {
            $response = $this->contactos_model->guardar_contacto($_POST);
        } else {
            $response = false;
        }
        return $response;
    }

    /**
     * *******************************************
     *
     * FUNCIONES PARA USAR DESDE OTROS MODULOS
     *
     * *******************************************
     */
    /**
     * Guardar un contacto exportado desde
     * Clientes Potenciales
     *
     * @param array $clause
     */
    function comp__guardar_contacto($fieldset = NULL) {
        if ($fieldset == NULL) {
            return false;
        }

        return $this->contactos_model->comp__guardar_contacto($fieldset);
    }

    function ocultoformulario(){
        $this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/bootstrap/bootstrap-tagsinput.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
            'public/assets/css/plugins/jquery/switchery.min.css'
        ));
        $this->assets->agregar_js(array(
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/plugins/bootstrap/bootstrap-tagsinput.js',
            'public/assets/js/plugins/jquery/switchery.min.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/bootstrap/bootstrap-tabdrop.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/default/formulario.js',
            'public/assets/js/modules/contactos/crear_contacto.js'
        ));
        $this->assets->agregar_var_js(array(
            "uuid_usuario" => CRM_Controller::$uuid_user,
            //"uuid_cliente" => $uuid_cliente,
            "tipo_id_cont" => ''
        ));
        $this->load->view('formulario');
    }
}
?>

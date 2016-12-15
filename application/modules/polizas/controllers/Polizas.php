<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
use Illuminate\Database\Capsule\Manager as Capsule;
class Polizas extends CRM_Controller {

    private $id_empresa;
    private $id_usuario;
    private $empresaObj;
  
  
    function __construct(){
        parent::__construct();

        $this->load->helper(array('file', 'string','util'));
        $this->load->model('Polizas_orm');
        $this->load->model('usuarios/usuario_orm');
        $this->load->model('usuarios/organizacion_orm');


        $this->load->dbutil();
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresaObj  = new Buscar(new Empresa_orm,'uuid_empresa');
        $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
        $this->id_usuario   = $this->session->userdata("huuid_usuario");
	$this->id_empresa   = $this->empresaObj->id;

    }

    public function listar(){



        $data = array();
        $clause = array('empresa_id'=> $this->id_empresa);
        $rows =Polizas_orm::where($clause)->get();
        if(!empty($rows->toArray())){
            foreach ($rows->toArray() AS $i => $row){
                $hidden_options = "";

                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-poliza="'. $row['uuid_polizas'] .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';


                $hidden_options .= '<a href="#" id="cambiarEtapaConfirmBtn" data-poliza="'. $row['uuid_polizas'] .'" class="btn btn-block btn-outline btn-success">Ver Aseguradora</a>';

                $hidden_options .= '<a href="#" id="cambiarEtapaConfirmBtn" data-poliza="'. $row['uuid_polizas'] .'" class="btn btn-block btn-outline btn-success">Agregar Contacto</a>';

                $hidden_options .= '<a href="#" id="cambiarEtapaConfirmBtn" data-poliza="'. $row['uuid_polizas'] .'" class="btn btn-block btn-outline btn-success">Crear Reporte de Remesas</a>';

                $hidden_options .= '<a href="#" id="cambiarEtapaConfirmBtn" data-poliza="'. $row['uuid_polizas'] .'" class="btn btn-block btn-outline btn-success">Crear Reporte de Comisión</a>';

                $nombre_aseguradora = !empty($row["nombre"]) ? $row["nombre"] : "";



        	$camposGrid[$i]["info"][0]["name"] ="Teléfono";
        	$camposGrid[$i]["info"][0]["value"] = !empty($row["telefono"]) ? $row["telefono"] : "";
        
        	$camposGrid[$i]["info"][1]["name"] = "Dirección ";
        	$camposGrid[$i]["info"][1]["value"] = !empty($row["direccion"]) ? $row["direccion"] : "";
        
        	$camposGrid[$i]["info"][2]["name"] = "E-mail ";
        	$camposGrid[$i]["info"][2]["value"] = !empty($row["email"]) ? $row["email"] : "";
        	
        	
                $camposGrid[$i]["id"] = $row["id"];
        	$camposGrid[$i]["opcion"] = $hidden_options;
            
                

                $camposGrid[$i]["uuid"] = isset($row["uuid_polizas"]) ? $row["uuid_polizas"] : NULL;
                $camposGrid[$i]["titulo"]["name"] = "Nombre";
                $camposGrid[$i]["titulo"]["value"] = $nombre_aseguradora;
                $camposGrid[$i]["subtitulo"]["name"] = "RUC ";
                $camposGrid[$i]["subtitulo"]["value"] = !empty($row["ruc"]) ? $row["ruc"] : "";

                $camposGrid[$i]["info"][0]["name"] ="Teléfono";
                $camposGrid[$i]["info"][0]["value"] = !empty($row["telefono"]) ? $row["telefono"] : "";

                $camposGrid[$i]["info"][1]["name"] = "Dirección ";
                $camposGrid[$i]["info"][1]["value"] = !empty($row["direccion"]) ? $row["direccion"] : "";

                $camposGrid[$i]["info"][2]["name"] = "E-mail ";
                $camposGrid[$i]["info"][2]["value"] = !empty($row["email"]) ? $row["email"] : "";


                $camposGrid[$i]["id"] = $row["id"];
                $camposGrid[$i]["opcion"] = $hidden_options;



            }
            $data["grid"] = $camposGrid;

        }


        $this->assets->agregar_css(array(
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/modules/stylesheets/polizas.css',
            'public/assets/css/plugins/bootstrap/bootstrap-tagsinput.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',


        ));
        $this->assets->agregar_js(array(
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/plugins/bootstrap/bootstrap-tagsinput.js',
            'public/assets/js/modules/polizas/listar_polizas.js',
            'public/assets/js/default/grid.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',

        ));

        //Breadcrum Array
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-file-text"></i> Pólizas',
            "filtro" => true,
            "menu" => array(
                "nombre" => "Crear",
                "url"	 => "polizas/crear",
                "opciones" => array()
            )
        );
        $breadcrumb["botones"]["Polizas"] = '<i class="fa fa-tasks"></i> Pipeline';
        $breadcrumb["botones"]["Polizas"] = '<i class="fa fa-star"></i> Score';

        $this->template->agregar_titulo_header('Listado de Pólizas');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);


        $this->template->visualizar($breadcrumb);

    }

    public function crear()
    {
        $data = array();
        $mensaje = array();

        if(!empty($_POST["campo"])){
            $this->guardar_aseguradora();
        }

        $this->assets->agregar_css(array(
            'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
            'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',

        ));
        $this->assets->agregar_js(array(
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/combodate/combodate.js',
            'public/assets/js/plugins/jquery/combodate/momentjs.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            'public/assets/js/default/tabla-dinamica.jquery.js',
            'public/assets/js/default/formulario.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/modules/polizas/crear.js',


        ));

        $breadcrumb = array(
            "titulo" => '<i class="fa fa-book"></i> Crear Aseguradora',
            "ruta" =>array(
                0 => array(
                    "nombre" => "Aseguradora",
                    "activo" => false,
                    "url" => '')
            )
        );
        $this->template->agregar_titulo_header('Aseguradoras');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    public function ocultoformulario($data = array())
    {
        $this->assets->agregar_js(array(
            'public/assets/js/modules/aseguradores/crear.js'
        ));

        $this->load->view('formulario',$data);
    }

    function ajax_listar(){


        $uuid_usuario = $this->session->userdata('huuid_usuario');
        $usuario = Usuario_orm::findByUuid($uuid_usuario);
        $usuario_org = $usuario->organizacion;

        $orgid = $usuario_org->map(function($org){
            return $org->id;
        });

        $clause = array(
            "nombre"=>$this->input->post("nombre"),
            "ruc"=>$this->input->post("ruc"),
            "telefono"=>$this->input->post("telefono"),
            "email"=>$this->input->post("email"),
        );


        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = Polizas_orm::listar($clause, NULL, NULL, NULL, NULL)->count();
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $rows = Polizas_orm::listar($clause,$sidx, $sord, $limit, $start);


        //Constructing a JSON
        $response = new stdClass();
        $response->page     = $page;
        $response->total    = $total_pages;
        $response->records  = $count;
        $i=0;

        if(!empty($rows->toArray())){
            foreach ($rows->toArray() AS $i => $row){
                $hidden_options = "<a href=". base_url('polizas/editar/'.strtoupper(bin2hex($row['uuid_polizas']))) ." class='btn btn-block btn-outline btn-success'>Ver Póliza</a>";

                $hidden_options .= '<a href="#" id="cambiarEtapaConfirmBtn" class="btn btn-block btn-outline btn-success">Agregar Contacto</a>';

                $hidden_options .= '<a href="#" id="cambiarEtapaConfirmBtn"  class="btn btn-block btn-outline btn-success">Crear Reporte de Remesas</a>';

                $hidden_options .= '<a href="#" id="cambiarEtapaConfirmBtn" class="btn btn-block btn-outline btn-success">Crear Reporte de Comisión</a>';
                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'.$row['id'].'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $response->rows[$i]["id"] = $row['id'];
                $response->rows[$i]["cell"] = array(
                    $row['id'],
                    "<a href='" . base_url('polizas/editar/'.strtoupper(bin2hex($row['uuid_polizas']))) . "'>" . $row['numero']  . "</a>",
                    $row['cliente'],
                    $row['ramo'],
                    $row['usuario'],
                    $row['estado'],
                    $row['inicio_vigencia'],
                    $row['fin_vigencia'],
                    $link_option,
                    $hidden_options
                );
            }
        }

        echo json_encode($response);
        exit;


    }

    function guardar_aseguradora()
    {
        unset($_POST["campo"]["guardarFormBtn"]);

        /**
         * Inicializar Transaccion
         */
        Capsule::beginTransaction();

        try {
            $fieldset = Util::set_fieldset("campo");
            $fieldset['empresa_id'] = $this->id_empresa;
            $fieldset["uuid_aseguradora"] = Capsule::raw("ORDER_UUID(uuid())");
            $fieldset["created_at"] = date('Y-m-d H:i:s');
            
            /**
             * Guardar Aseguradora
             */
            $colaborador = Aseguradoras_orm::create($fieldset);

        } catch(ValidationException $e){

            // Rollback
            Capsule::rollback();
        }

        // If we reach here, then
        // data is valid and working.
        // Commit the queries!
        Capsule::commit();

        //Redireccionar
        redirect(base_url('aseguradoras/listar'));
    }

    function editar($uuid=NULL)
    {
        if(!$uuid)
        {
            echo "Error.";
            die();
        }

        $data       = array();
        $mensaje    = array();

        $aseguradora  = new Aseguradoras_orm();
        $aseguradora  = $aseguradora
            ->where("uuid_aseguradora", "=", hex2bin(strtolower($uuid)))
            ->first();


        if(!empty($_POST))
        {
            $response = false;
            $response = Capsule::transaction(
                function()
                {
                    $aseguradora  = new Aseguradoras_orm;
                    $aseguradora = $aseguradora
                        ->where("uuid_aseguradora", "=", hex2bin(strtolower($this->uri->segment(3, 0))))
                        ->first();

                    $campo = $this->input->post("campo");

                    //DATOS GENERALES DE LA ASEGURADORA
                    $aseguradora->nombre    = $campo["nombre"];
                    $aseguradora->ruc       = $campo["ruc"];
                    $aseguradora->telefono  = $campo["telefono"];
                    $aseguradora->email     = $campo["email"];
                    $aseguradora->direccion = $campo["direccion"];
                    $aseguradora->direccion = hex2bin($campo["uuid_cuenta_pagar"]);
                    $aseguradora->direccion = hex2bin($campo["uuid_cuenta_cobrar"]);

                    $aseguradora->save();

                    return true;
                }
            );


            if($response){
                $this->session->set_userdata('updatedAseguradora', $aseguradora->id);
                redirect(base_url('aseguradoras/listar'));
            }else{
                //Establecer el mensaje a mostrar
                $data["mensaje"]["clase"] = "alert-danger";
                $data["mensaje"]["contenido"] = "Hubo un error al tratar de editar la aseguradora.";
            }
        }

        //Introducir mensaje de error al arreglo
        //para mostrarlo en caso de haber error
        $data["message"] = $mensaje;

        $this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/plugins/jquery/switchery.min.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
            'public/assets/css/plugins/bootstrap/bootstrap-tagsinput.css',
            'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
            'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
            'public/assets/css/plugins/jquery/fileinput/fileinput.css',
            'public/assets/css/modules/stylesheets/aseguradoras.css',
        ));

        $this->assets->agregar_js(array(
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/combodate/combodate.js',
            'public/assets/js/plugins/jquery/combodate/momentjs.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/default/tabla-dinamica.jquery.js',
            'public/assets/js/default/formulario.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/modules/aseguradoras/acciones_editar.js',
            'public/assets/js/modules/aseguradoras/provider.js',
        ));


        $breadcrumb = array(
            "titulo" => '<i class="fa fa-book"></i> Aseguradora '.$aseguradora->nombre,
            "filtro"    => false, //sin vista grid
            "menu"      => array(
                'url' => 'javascipt:',
                'nombre' => "Acción",
                "opciones" => array(
                    "#datosAseguradoraBtn" => "Datos de Aseguradora",
                    "#agregarContactoBtn" => "Nuevo Contacto",
                )
            )
        );


        $data["campos"] = array(
            "campos"    => array(
                "created_at"        => $aseguradora->created_at,
                "uuid_aseguradora"  => strtoupper(bin2hex($aseguradora->uuid_aseguradora)),
                "nombre"            => $aseguradora->nombre,
                "ruc"               => $aseguradora->ruc,
                "telefono"          => $aseguradora->telefono,
                "email"             => $aseguradora->email,
                "direccion"         => $aseguradora->direccion,
                "uuid_cuenta_pagar"         => strtoupper(bin2hex($aseguradora->uuid_cuenta_pagar)),
                "uuid_cuenta_cobrar"         => strtoupper(bin2hex($aseguradora->uuid_cuenta_cobrar)),
                "descuenta_comision"=> $aseguradora->descuenta_comision,
                "imagen_archivo"    => $aseguradora->imagen_archivo,
            ),

        );



        $this->template->agregar_titulo_header('Aseguradoras');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    function configuracion($form=null){
        $data=array();
        if($form=="planes"){
            Capsule::beginTransaction();
            try {
                $fieldset['nombre'] = $this->input->post('nombre_plan');
                $fieldset["id_aseguradora"] = $this->input->post('idAseguradora');
                $fieldset["id_ramo"] = $this->input->post('codigo');
                $fieldset["created_at"] = date('Y-m-d H:i:s');
                $res = Planes_orm::create($fieldset);
                $fieldset= array();
                foreach ($this->input->post('coberturas') as $value) {
                    $fieldset['nombre'] = $value;
                    $fieldset["id_planes"] = $res->id;
                    $fieldset["created_at"] = date('Y-m-d H:i:s');
                    Coberturas_orm::create($fieldset);
                }
            } catch(ValidationException $e){

                Capsule::rollback();
            }
            if(!is_null($res)){
	        $data["mensaje"]["clase"] = "alert-success";
                $data["mensaje"]["contenido"] = '<b>¡&Eacute;xito!</b> Se ha guardado correctamente '.$res->nombre;
	      }else{
	        $data["mensaje"]["clase"] = "alert-danger";
                $data["mensaje"]["contenido"] = '<strong>¡Error!</strong> Su solicitud no fue procesada';
	      }
            Capsule::commit();
        }
        
        $this->assets->agregar_css(array(

            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/plugins/jquery/jstree/default/style.min.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
            'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
            'public/assets/css/modules/stylesheets/aseguradoras.css',

        ));
        $this->assets->agregar_js(array(
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/default/lodash.min.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/plugins/jquery/jquery.progresstimer.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/jstree.min.js',
            'public/assets/js/modules/aseguradoras/routes.js',
            'public/assets/js/modules/aseguradoras/configuracion.js',
            'public/assets/js/default/formulario.js',
        ));
        $menuOpciones = array(
            "#activarLnk" => "Habilitar",
            "#inactivarLnk" => "Deshabilitar",
            "#exportarLnk" => "Exportar",
        );
        //Breadcrum Array

        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        //dd($empresa->toArray());
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-gear"></i> Seguros: Configuraci&oacute;n',
            "filtro" => false,
            "menu" => array(
                "nombre" => "Crear",
                "url"	 => 'entrada_manual/crear',
                "opciones" => $menuOpciones
            )
        );
        $clause = array('empresa_id'=> $this->id_empresa);
        $data['aseguradoras'] = Aseguradoras_orm::where($clause)->get();

        $this->template->agregar_titulo_header('Entrada Manual');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }
    public function ocultotabla_ramos()
    {
        //If ajax request
        $this->assets->agregar_js(array(
            'public/assets/js/modules/aseguradoras/tabla_ramos.js'
        ));//'public/assets/js/modules/aseguradoras/tabla_ramos.js'

        $this->load->view('tabla_ramos');
    }
    public function ajax_listar_ramos()
    {
        //Just Allow ajax request

        if(!$this->input->is_ajax_request()){
            return false;
        }

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $tipo = $this->input->post('tipo');
        $nombre = (string)$this->input->post('nombre');
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        //fix count
        $count = Ramos_orm::where('empresa_id',$empresa->id)->count();

        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);

        $clause= array('empresa_id' => $empresa->id);
        if(!empty($tipo)) $clause['tipo_cuenta_id'] = $tipo;
        //if(!empty($nombre)) $clause['nombre'] = array('like',"%$nombre%");

        $cuentas = Ramos_orm::listar($clause, $nombre ,$sidx, $sord, $limit, $start);

        //Constructing a JSON
        $response = new stdClass();
        $response->page     = $page;
        $response->total    = $total_pages;
        $response->record  = $count;
        $i=0;

        if(!empty($cuentas)){
            foreach ($cuentas as  $row){
                $tituloBoton = ($row['estado']!=1)?'Habilitar':'Deshabilitar';
                $estado = ($row['estado']==1)?0:1;
                $hidden_options = "";
                $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'. $row['id'] .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $hidden_options .= '<a href="javascript:" data-id="'. $row['id'] .'" class="btn btn-block btn-outline btn-success editarRamoBtn">Editar Ramo</a>';
                $hidden_options .= '<a href="javascript:" data-id="'. $row['id'] .'" data-estado="'.$estado.'" class="btn btn-block btn-outline btn-success cambiarEstadoRamoBtn">'.$tituloBoton.' Ramo</a>';
                $level = substr_count($row['nombre'],".");
                $response->rows[$i] = array("id" => $row['id'], 'cell' => array(
                    'id' => $row['id'],
                    'nombre'=> $row['nombre'],
                    'descripcion' => $row['descripcion'],
                    'estado' =>($row['estado']==1)?'Habilitado':'Deshabilitado',
                    'opciones' =>$link_option,
                    'link' => $hidden_options,
                    "level" => isset($row["level"]) ? $row["level"] : "0", //level
                    'parent' => $row["padre_id"]==0? "NULL": (string)$row["padre_id"], //parent
                    'isLeaf' =>(Ramos_orm::is_parent($row['id']) == true)? false: true, //isLeaf
                    'expanded' =>  false, //expended
                    'loaded' => true, //loaded
                ) );
                $i++;
            }
        }

        echo json_encode($response);
        exit;
    }

    function ajax_cambiar_estado_ramo(){
        if(!$this->input->is_ajax_request()){
            return false;
        }

        $response=array();
        $estado = $this->input->post('estado');
        $id = $this->input->post('id');


        $total =  Ramos_orm::cambiar_estado($id,$estado);

        if($total > 0){
            $response= array('estado'=>200,'mensaje' => '<b>¡&Eacute;xito!</b> La actualizaci&oacute;n de estado');
        }else{
            $response= array('estado'=>500,'mensaje' => '<b>¡Error!</b> Su solicitud no fue Procesada');
        }
        echo json_encode($response);
        exit;
    }

    function ajax_buscar_ramo(){
        if(!$this->input->is_ajax_request()){
            return false;
        }
        $id = $this->input->post('id');
        $cuenta = Ramos_orm::find($id);
        $response = array();


        $response['id'] = $cuenta->id;
        $response['codigo'] = $cuenta->id;
        $response['nombre'] = $cuenta->nombre;
        $response['descripcion'] =$cuenta->descripcion;
        $response['padre_id'] = $cuenta->padre_id;
        $response['formulario_solic'] = $cuenta->formulario_solic;

        echo json_encode($response);
        exit;

    }

    public function ajax_listar_ramos_tree(){
        if(!$this->input->is_ajax_request()){
            return false;
        }

        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $tipo = $this->input->post('tipo');
        $clause= array('empresa_id' => $empresa->id);
        if(!empty($tipo)) $clause['tipo_cuenta_id'] = $tipo;
        $cuentas = Ramos_orm::listar_cuentas($clause);
        //Constructing a JSON
        $response = new stdClass();
        $response->plugins = [ "contextmenu" ];
        $response->core->check_callback[0] = true;
        
        $i = 0;
        if(!empty($cuentas)){
            foreach ($cuentas as  $row){
                $response->core->data[$i] = array(
                    'id' => (string)$row['id'],
                    'parent'=> $row["padre_id"]==0? "#": (string)$row["padre_id"],
                    'text' => $row["nombre"],
                    'icon' => 'fa fa-folder',
                    'codigo' => $row["id"]
                    //'state' =>array('opened' => true)
                );

                $i++;
            }

        }

        echo json_encode($response);
        exit;

    }

    function ajax_guardar_ramos(){
        if(!$this->input->is_ajax_request()){
            return false;
        }
        $response = new stdClass();
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $id = $this->input->post('id');
        $nombre = $this->input->post('nombre');
        $descripcion = $this->input->post('descripcion');
        $form_solicitud = $this->input->post('form_solicitud');
        $padre_id = $this->input->post('codigo');
        $cuenta_id = $this->input->post('cuenta_id');
        if(!isset($id)){
            $datos = array();
            $datos['nombre'] = $nombre;
            $datos['descripcion'] =  $descripcion;
            $datos['formulario_solic'] =  $form_solicitud;
            $datos['empresa_id'] = $empresa->id;
            $datos['padre_id'] = $padre_id;
            $impuesto_save = Ramos_orm::create($datos);
            $response->estado = 200;
            $response->mensaje = '<b>¡&Eacute;xito!</b> Se ha guardado correctamente  '.$impuesto_save->nombre;
        }else{
            $impuesto_save = Ramos_orm::find($id);
            $impuesto_save->nombre = $nombre;
            $impuesto_save->descripcion = $descripcion;
            $impuesto_save->formulario_solic = $form_solicitud;
            $impuesto_save->save();
            $response->estado = 200;
            $response->mensaje = '<b>¡&Eacute;xito!</b> Se ha actualizado correctamente  '.$impuesto_save->nombre;
        }

        echo json_encode($response);
        exit;
    }
    
    public function reporte_remesas(){

        $data = array();
        $this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
            'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
            'public/assets/js/plugins/jquery/sweetalert/sweetalert.css',
          ));
        $this->assets->agregar_js(array(
            'public/assets/js/default/jquery-ui.min.js',
            'public/assets/js/plugins/jquery/jquery.sticky.js',
            'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/plugins/jquery/jqgrid/plugins/jQuery.jqGrid.columnToggle.js',
            'public/assets/js/default/subir_documento_modulo.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/default/formulario.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            'public/assets/js/plugins/bootstrap/bootstrap-datetimejs.js',
            'public/assets/js/default/lodash.min.js',
            'public/assets/js/plugins/jquery/sweetalert/sweetalert.min.js',
            'public/assets/js/modules/aseguradoras/remesas.js',
            'public/assets/js/default/toast.controller.js'
        ));

        //Breadcrum Array
        $breadcrumb = array(
            "titulo" => '<i class="fa fa-book"></i> Aseguradoras',
            "filtro" => false,
        );

        $this->template->agregar_titulo_header('Reporte de remesas');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);


        $this->template->visualizar($breadcrumb);
    }

}
?>

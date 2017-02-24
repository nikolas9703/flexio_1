<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');


use Illuminate\Database\Capsule\Manager as Capsule;
use League\Csv\Writer as Writer;
use Flexio\Modulo\Agentes\Models\Agentes as AgentesModel;
use Flexio\Modulo\Polizas\Models\Polizas as PolizasModel;
use Flexio\Modulo\Agentes\Models\AgentesRamos as AgentesRamosModel;
use Flexio\Modulo\Ramos\Models\Ramos as Ramos;
use Flexio\Modulo\Agentes\Models\AgentesCatalogo as AgentesCatalogoModel;
use Flexio\Modulo\Politicas\Repository\PoliticasRepository as PoliticasRepository;
use Flexio\Modulo\Ramos\Repository\RamoRepository as RamoRepository;

use Illuminate\Http\Request;
use Flexio\Library\Util\FormRequest;

class Agentes extends CRM_Controller
{
    private $id_empresa;
    private $id_usuario;
    private $empresaObj;

    protected $politicas;

    protected $roles;

    protected $request;
    
    /**
     * @var array
     */
    protected $politicas_general;

    protected $AgentesModel;
    protected $AgentesRamosModel;
    protected $Ramos;
    protected $AgentesCatalogoModel;
    protected $PoliticasRepository;
    protected $ramoRepository;


    function __construct() {
        parent::__construct();
        
        
        //Obtener el id_empresa de session
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresa = Empresa_orm::findByUuid($uuid_empresa);
        $this->empresa_id = $empresa->id;

        //$this->load->model('agentes_orm');
        //$this->load->model('Catalogo_orm');

        $this->AgentesModel = new AgentesModel();
        $this->AgentesCatalogoModel = new AgentesCatalogoModel();
        $this->AgentesRamosModel = new AgentesRamosModel();
        $this->Ramos = new Ramos();
        $this->PoliticasRepository= new PoliticasRepository();
         $this->ramoRepository = new RamoRepository();

        $this->request = Request::capture();

        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        //$this->empresaObj  = Empresa_orm::findByUuid($uuid_empresa);
        $empresaObj  = new Buscar(new Empresa_orm,'uuid_empresa');
        $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
        $this->id_usuario   = $this->session->userdata("huuid_usuario");
        $this->id_empresa   = $this->empresaObj->id;

        $this->roles=$this->session->userdata("roles");
        //$roles=implode(",", $this->roles);
        
        $clause['empresa_id']=$this->id_empresa;
        $clause['modulo']='agentes';
        $clause['usuario_id']=$this->id_usuario;
        $clause['role_id']=$this->roles;

        $politicas_transaccion=$this->PoliticasRepository->getAllPoliticasRoles($clause);
        
        $politicas_transaccion_general=count($this->PoliticasRepository->getAllPoliticasRolesModulo($clause));
        $this->politicas_general=$politicas_transaccion_general;
		
        $politicas_transaccion_general2 = $this->PoliticasRepository->getAllPoliticasRolesModulo($clause);
        
        $estados_politicas=array();
        foreach($politicas_transaccion as $politica_estado)
        {
            $estados_politicas[]=$politica_estado->politica_estado;
        }
        $estados_politicasgenerales = array();
        foreach ($politicas_transaccion_general2 as $politica_estado_generales) {
            $estados_politicasgenerales[] = $politica_estado_generales->politica_estado;
        }
        
        $this->politicas=$estados_politicas;
        $this->politicas_generales = $estados_politicasgenerales;
    }


    public function listar() {

        if ($this->auth->has_permission('acceso', 'agentes/listar') == false) {
            redirect(base_url('/'));
        }


        if (!is_null($this->session->flashdata('mensaje'))) {
            $mensaje = $this->session->flashdata('mensaje');
        } else {
            $mensaje = [];
        }

        $this->assets->agregar_var_js(array(
            "flexio_mensaje" => collect($mensaje)
        ));

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
            "url"   => 'agentes/crear',
            "clase" => 'crearBoton',
            "nombre" => "Crear"
        );
        $breadcrumb["menu"]["opciones"]["#exportarBtn"] = "Exportar";
        $breadcrumb["menu"]["opciones"]["#cambiarEstadosBtn"] = "Cambiar Estados";
        
        $data['mensaje'] = $this->session->flashdata('mensaje');
        $this->template->agregar_contenido($data);
        $this->template->agregar_titulo_header('Listado de Agentes');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }


    function ajax_cambiar_estados(){

     $FormRequest = new Flexio\Modulo\Agentes\Models\GuardarAgentesEstados;

     try {
            $Agentes = $FormRequest->guardar();
            //formatear el response
            /*$res = $Agentes->map(function($ant) {
                return[
                    'id' => $ant->id, 'estado' => $ant->present()->estado_label
                ];
            });*/
        } catch (\Exception $e) {
            log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
        }

        //$this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output($res)->_display();
        exit;
   }

   

    /**
     * Se usa en propiedades
     * @access  public
     * @param
     * @return  tabla
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
        //$usuario = AgentesModel::findByUuid($uuid_usuario);
        /*$usuario_org = $usuario->organizacion;

        $orgid = $usuario_org->map(function($org){
            return $org->id;
        });*/

        $clause = array(
            "nombre"    => $this->input->post("nombre"),
            "apellido"  => $this->input->post("nombre"),
            "telefono"  => $this->input->post("telefono"),
            "correo"    => $this->input->post("correo"),
            "identificacion"    => $this->input->post("identificacion"),
            "porcentaje_participacion"    => $this->input->post("porcentaje_participacion"),
            'id_empresa' => $this->id_empresa,
        );


        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = AgentesModel::listar($clause, NULL, NULL, NULL, NULL)->count();
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $rows = AgentesModel::listar($clause, $sidx, $sord, $limit, $start);


        //Constructing a JSON
        $response = new stdClass();
        $response->page     = $page;
        $response->total    = $total_pages;
        $response->records  = $count;
        $i=0;

        if(!empty($rows->toArray())){
            foreach ($rows->toArray() AS $i => $row){

                $agtram = AgentesRamosModel::where('id_agente', $row['id'])->get();
                $agtramos = $agtram->toArray();

                $partramos="";
                foreach ($agtramos as $ar) {
                    $partramos.=$ar['participacion'].", ";
                }
                $partramos=trim($partramos,', ');

                if($row['estado'] == 'Inactivo')
                    $spanStyle='label label-danger';
                else if($row['estado'] == 'Activo')
                    $spanStyle='label label-successful';
                else
                    $spanStyle='label label-warning';
				
				if($row['principal']==1)
				{
					$principal="<label class='label label-warning'>Principal</label>";
				}
				else{
					$principal="";
				}

                $hidden_options = "<a href=". base_url('agentes/ver/'.strtoupper($row['uuid_agente'])) ." class='btn btn-block btn-outline btn-success'>Ver Agente</a>";
                $hidden_options .= '<a href="#" id="cambiarAgentePrincipal" class="btn btn-block btn-outline btn-success cambiarAgentePrincipal" data-id="'.$row['id'].'">Asignar como principal</a>';
                 $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="'.$row['id'].'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
                $response->rows[$i]["id"] = $row['id'];
                $nombre_agente =  $row["nombre"] ." ".$row["apellido"];
                $response->rows[$i]["cell"] = array(
                    $row['id'],
                    "<a href='" . base_url('agentes/ver/'.($row['uuid_agente'])) . "'>" . $nombre_agente  . "</a> ".$principal,
                    $row['identificacion'],
                    $row['telefono'],
                    $row['correo'],
                    //$row['porcentaje_participacion'].'%',
                    $partramos.'%',
                    "<label class='".$spanStyle." cambiarestadoseparado' data-id='".$row['id']."'>".$row['estado']."</label>",
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
        $clause = array();
        $clause['ids'] = $id;
                
        $agentes = AgentesModel::exportaragentes($clause, NULL, NULL, NULL, NULL);
        if(empty($agentes)){
            return false;
        }
        $i=0;
        foreach ($agentes AS $row)
        {

            $agtram = AgentesRamosModel::where('id_agente', $row->id)->get();
            $agtramos = $agtram->toArray();
            $partramos="";
            foreach ($agtramos as $ar) {
                $partramos.=$ar['participacion'].", ";
            }
            $partramos=trim($partramos,', ');


            $csvdata[$i]['nombre'] = $row->nombre . " " . $row->apellido;
            $csvdata[$i]["cedula"] = utf8_decode(Util::verificar_valor($row->identificacion));
            $csvdata[$i]["telefono"] = utf8_decode(Util::verificar_valor($row->telefono));
            $csvdata[$i]["email"] = utf8_decode(Util::verificar_valor($row->correo));
            $csvdata[$i]["participacion"] = utf8_decode(Util::verificar_valor($partramos));
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

    public function exportarPolizas() {        
        if(empty($_POST)){
            exit();
        }
        $ids =  $this->input->post('ids', true);
        $id = explode(",", $ids);
    
        if(empty($id)){
            return false;
        }
        $csv = array();
        $clause = array();
        $clause['polizas'] = $id;
                
        $polizas = PolizasModel::exportarPolizasAgt($clause, NULL, NULL, NULL, NULL);
        if(empty($polizas)){
            return false;
        }
        $i=0;
        foreach ($polizas AS $row)
        {
            $csvdata[$i]['numero'] = $row->numero;
            $csvdata[$i]["cliente"] = utf8_decode(Util::verificar_valor($row->cliente));
            $csvdata[$i]["aseguradora"] = utf8_decode(Util::verificar_valor($row->aseguradora));
            $csvdata[$i]["ramo"] = utf8_decode(Util::verificar_valor($row->ramo));
            $csvdata[$i]["inicio_vigencia"] = utf8_decode(Util::verificar_valor($row->inicio_vigencia));
            $csvdata[$i]["fin_vigencia"] = utf8_decode(Util::verificar_valor($row->fin_vigencia));
            $csvdata[$i]["fecha_creacion"] = utf8_decode(Util::verificar_valor($row->fecha_creacion));
            $csvdata[$i]["estado"] = utf8_decode(Util::verificar_valor($row->estado));
            $i++;
        }
        //we create the CSV into memory
        $csv = Writer::createFromFileObject(new SplTempFileObject());
        $csv->insertOne([
            'No. Póliza',
            'Cliente',
            'Aseguradora',
            'Ramo',
            'Inicio de vigencia',
            'Fin de vigencia',
            'Fecha de creación',
            'Estado'
        ]);                
        $csv->insertAll($csvdata);
        $csv->output("polizas-". date('ymd') .".csv");
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

        if ($this->auth->has_permission('acceso', 'agentes/crear') == false) {
            redirect(base_url('agentes/listar'));
        }

        if (!is_null($this->session->flashdata('mensaje'))) {
            $mensaje = $this->session->flashdata('mensaje');
        } else {
            $mensaje = [];
        }

        $this->assets->agregar_var_js(array(
            "flexio_mensaje" => collect($mensaje)
        ));

        $this->assets->agregar_js(array(
            'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
            'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/default/formulario.js',
            'public/assets/js/modules/agentes/crear.js'

        ));

        $this->assets->agregar_css(array(
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
        ));

        //$total = Cliente_orm::where('empresa_id','=',$this->id_empresa)->count();
        $clause = array('empresa_id' => $this->empresa_id);

        //$data['info']['codigo'] = Util::generar_codigo('CUS', $total+ 1);
        //$data['info']['identificacion'] = AgentesCatalogoModel::where('identificador','=','Identificacion')->get(array('id_cat','valor'));
        $data['info']['provincias'] = AgentesCatalogoModel::where('identificador','like','Provincias')->orderBy("orden")->get(array('key','etiqueta'));
        $data['info']['letras'] = AgentesCatalogoModel::where('identificador','like','Letra')->get(array('key','etiqueta'));
        $data['info']['tipo_identificacion'] = $tipo_identificacion = AgentesCatalogoModel::where('identificador','like','Identificacion')->orderBy("orden")->get(array('valor','etiqueta'));
        $data['info']['estado'] = $estadoagt = AgentesCatalogoModel::where('identificador','like','Estado')->orderBy("orden")->get(array('valor','etiqueta'));
        $data['info']['id_empresa']=$this->id_empresa;
        $data['info']['menu_crearramos'] = $this->ramoRepository->listar_cuentas($clause);
        
        $data['info']['menu_crear'] = Ramos::where('padre_id','<>','0')
                ->where('padre_id','<>','"id"')
                ->where('empresa_id','=',$this->empresa_id)
                ->orderBy("nombre")->get();
        
        $data['info']['ramos'] = Ramos::where('padre_id','<>','0')->get();

        $data['info']['politicas']=$this->politicas;
        $data['info']['politicasgeneral']=$this->politicas_general;
        $data['info']['politicasgenerales']= $this->politicas_generales;
        $data['info']['guardar']=1;

        if ($this->auth->has_permission('ver__estadoAgente', 'agentes/ver/(:any)') ==  true) {
            $data['info']['estadoAgente'] = 1;
        }else{
            $data['info']['estadoAgente'] = 0;
        }
         
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
                    "url"   => 'agentes/listar',
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

    public function obtener_politicas(){
        echo json_encode($this->politicas);
        exit;
    }

    public function obtener_politicas_general(){
        echo json_encode($this->politicas_general);
        exit;
    }
     public function obtener_politicasgenerales() {
        echo json_encode($this->politicas_generales);
        exit;
    }

    function guardar() {
            
            if($_POST){
            unset($_POST["campo"]["guardar"]);
                        
            $campo = Util::set_fieldset("campo");   
            $camporamo = $this->input->post('camporamo');   


            if($campo['tipo_identificacion'] == 'natural'){
                //formato de identificacion
                if ($campo['letra']=='cero') {
                    $campo['letra']='0';
                }
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
            }
            if($campo['tipo_identificacion'] == 'pasaporte'){
                $cedula = $campo['pasaporte'];
                $campo['identificacion'] = $cedula;
                $campo['letra'] = "PAS";
            }
            if($campo['tipo_identificacion'] == 'juridico'){
                $cedula = $campo['tomo_ruc']."-".$campo['folio']."-".$campo['asiento_ruc']."-".$campo['digito'];
                $campo['identificacion'] = $cedula;
                $campo['letra'] = "RUC";
            }

            
            if(!isset($campo['uuid'])){
                $campo['id_empresa'] = $campo['empresa_id'];
                $campo['fecha_creacion'] = date('Y-m-d H:i:s');
                $campo['estado'] = 'Por Aprobar';
            }
            
            Capsule::beginTransaction();
            try {
                if(!isset($campo['uuid'])){//crear agente
                    $agente = AgentesModel::create($campo);
                    $fieldset = array();
                    if($camporamo != NULL && $camporamo != "" ){
                        $camporamo = trim($camporamo, "-");
                        $ramoindex = explode("-", $camporamo);
                        $participacion = $this->input->post('porcentaje_participacion');

                        foreach ($ramoindex as $key => $value) {
                            if ($value!="" AND $value!=NULL AND !empty($value)) {

                                $value = trim($value, ",");
                                $ramoval = explode(",", $value);

                                foreach ($ramoval as $k => $v) {
                                    $fieldset['id_ramo'] = $v;
                                    $fieldset["id_agente"] = $agente->id;
                                    $fieldset["participacion"] = $participacion[$key];
                                    AgentesRamosModel::create($fieldset);
                                }                                        
                            }                                
                        }

                    }
                }else{
                    //Actualizar Agente
                    $agenteObj  = new Buscar(new AgentesModel(),'uuid_agente');
                    $agente = $agenteObj->findByUuid($campo['uuid']);
                    if(is_null($agente)){
                           $mensaje = array('tipo' =>'error', 'mensaje' =>' Su solicitud no fue procesada', 'titulo'=>'<strong>¡Error!</strong>');
                           $this->session->set_flashdata('mensaje', $mensaje);
                            redirect(base_url('agentes/listar'));
                    }else{
                        unset($campo['uuid']);
                        $agente->update($campo);
                        if (isset($agente)) {
                            AgentesRamosModel::where('id_agente', $agente->id)->delete();
                            $fieldset = array();
                            if($camporamo != NULL && $camporamo != "" ){
                                $camporamo = trim($camporamo, "-");
                                $ramoindex = explode("-", $camporamo);
                                $participacion = $this->input->post('porcentaje_participacion');

                                foreach ($ramoindex as $key => $value) {
                                    if ($value!="" AND $value!=NULL AND !empty($value)) {

                                        $value = trim($value, ",");
                                        $ramoval = explode(",", $value);

                                        foreach ($ramoval as $k => $v) {
                                            $fieldset['id_ramo'] = $v;
                                            $fieldset["id_agente"] = $agente->id;
                                            //$fieldset["created_at"] = date('Y-m-d H:i:s');
                                            $fieldset["participacion"] = $participacion[$key];
                                            AgentesRamosModel::create($fieldset);
                                        }                                        
                                    }                                
                                }
                                
                            }
                        }
                    }
                }
                Capsule::commit();
            }catch(ValidationException $e){
                log_message('error', $e);
                Capsule::rollback();
            }


            if(!is_null($agente)){
                //$mensaje = array('clase' =>'alert-success', 'contenido' =>'<b>¡&Eacute;xito!</b> Se ha guardado correctamente '.$agente->nombre);
                $mensaje = array('tipo' =>'success', 'mensaje' =>' Se ha guardado correctamente '.$agente->nombre, 'titulo'=>'<b>¡&Eacute;xito!</b>');
            }else{
                $mensaje = array('tipo' =>'error', 'mensaje' =>' Su solicitud no fue procesada', 'titulo' => '<strong>¡Error!</strong>');
            }


        }else{
            $mensaje = array('tipo' =>'error', 'mensaje' =>' Su solicitud no fue procesada', 'titulo' => '<strong>¡Error!</strong>');
        }

        $this->session->set_flashdata('mensaje', $mensaje);
        redirect(base_url('agentes/listar'));

    }

    public function existsIdentificacion() {
            $campo = Util::set_fieldset("campo");
            $response = new stdClass();
            
            if ($campo['tipo_identificacion'] == 'natural') {
                if(isset($campo['letra'])){
                    if ($campo['letra']=='cero') {
                        $campo['letra']='0';
                    }
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
            }

            if($campo['tipo_identificacion'] == 'pasaporte'){
                $cedula = $campo['pasaporte'];
                $campo['identificacion'] = $cedula;
                $campo['letra'] = "PAS";
            }
            if($campo['tipo_identificacion'] == 'juridico'){
                $cedula = $campo['tomo_ruc']."-".$campo['folio']."-".$campo['asiento_ruc']."-".$campo['digito'];
                $campo['identificacion'] = $cedula;
                $campo['letra'] = "RUC";
            }

        if (isset($campo['uuid'])) {

            $agenteObj2  = new Buscar(new AgentesModel,'uuid_agente');
            $agente2 = $agenteObj2->findByUuid($campo['uuid']);

            $agt = AgentesModel::where('id', $agente2->id)->where('identificacion',$campo['identificacion'])->count();

            if ($agt==0) {
                $agenteObj  = new Buscar(new AgentesModel(),'identificacion');
                //$agente = $agenteObj->findById($campo['identificacion']);
                
                $agente = AgentesModel::where("identificacion", $campo['identificacion'])->where("id_empresa", $this->empresa_id)->count();

                if(is_null($agente) || $agente==0){
                    $response->existe =  false;
                }else{
                    //$response->existe =  true;
                    $response->existe =  true;
                }
            }else{
                $response->existe =  false;
            }            
        }else{
            $agenteObj  = new Buscar(new AgentesModel(),'identificacion');
            //$agente = $agenteObj->findById($campo['identificacion']);
            $agente = AgentesModel::where("identificacion", $campo['identificacion'])->where("id_empresa", $this->empresa_id)->count();
            //$agente =  AgentesModel::findById($campo['identificacion']);

            if(is_null($agente) || $agente==0){
                $response->existe =  false;
            }else{
                //$response->existe =  true;
                $response->existe =  true;
            }
        }            
        
        
        
        echo json_encode($response);
        //echo json_encode($campo);
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

        /*if ($this->auth->has_permission('acceso', 'agentes/ver-agente/(:any)') == false) {
            redirect(base_url('agentes/listar'));
        }*/
        if ($this->auth->has_permission('acceso', 'agentes/ver/(:any)') == false) {
            redirect(base_url('agentes/listar'));
        }

        $this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/bootstrap/bootstrap-tagsinput.css',
            'public/assets/css/plugins/jquery/switchery.min.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/modules/stylesheets/agentes.css',
            'public/assets/css/plugins/bootstrap/bootstrap-tagsinput.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
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
            'public/assets/js/modules/agentes/ver.js',
            'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
            'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
        ));


        if ($this->auth->has_permission('ver__estadoAgente', 'agentes/ver/(:any)') ==  true) {
            $data['info']['estadoAgente'] = 1;
        }else{
            $data['info']['estadoAgente'] = 0;
        }
         $clause = array('empresa_id' => $this->empresa_id);
        
        $data['info']['provincias'] = AgentesCatalogoModel::where('identificador','like','Provincias')->orderBy("orden")->get(array('key','etiqueta'));
        $data['info']['letras'] = AgentesCatalogoModel::where('identificador','like','Letra')->get(array('key','etiqueta'));
        $data['info']['tipo_identificacion'] = $tipo_identificacion = AgentesCatalogoModel::where('identificador','like','Identificacion')->orderBy("orden")->get(array('valor','etiqueta'));
        $data['info']['estado'] = $estadoagt = AgentesCatalogoModel::where('identificador','like','Estado')->orderBy("orden")->get(array('valor','etiqueta'));
        $data['info']['id_empresa']=$this->id_empresa;
        $data['info']['ramos'] = Ramos::where('padre_id','<>','0')->get();
       

        if($this->auth->has_permission('ver__editarAgente', 'agentes/ver/(:any)')==true){ $guardar =1; } else { $guardar =0; }

        $data['info']['politicas']=$this->politicas;
        $data['info']['politicasgeneral']=$this->politicas_general;
        $data['info']['politicasgenerales']= $this->politicas_generales;
        $data['info']['guardar']=$guardar;

        if(is_null($uuid)){
            //$mensaje = array('clase' =>'alert-warning', 'contenido' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
            $mensaje = array('tipo' =>'error', 'mensaje' =>' Su solicitud no fue procesada', 'titulo'=>'<strong>¡Error!</strong>');
            $this->session->set_flashdata('mensaje', $mensaje);
            redirect(base_url('agentes/listar'));
        }else{
            $agenteObj  = new Buscar(new AgentesModel,'uuid_agente');
            $agente = $agenteObj->findByUuid($uuid);
            
            if(is_null($agente)){
                //$mensaje = array('clase' =>'alert-warning', 'contenido' =>'<strong>¡Error!</strong> Su solicitud no fue procesada');
                $mensaje = array('tipo' =>'error', 'mensaje' =>' Su solicitud no fue procesada' , 'titulo' => '<strong>¡Error!</strong>');
                $this->session->set_flashdata('mensaje', $mensaje);
                redirect(base_url('agentes/listar'));
            }else{

                $data['info']['agente'] = $agente->toArray();
                $data['info']['agente']['letraUnica']= $agente['letra'];
                $identificacion = $agente['identificacion'];

				
                if ($agente['tipo_identificacion']=="natural") {
                    if($agente['letra'] == '0' || empty($agente['letra']) || !isset($agente['letra'])){
                        list($provincia, $tomo, $asiento) =  explode("-", $identificacion);
                        $data['info']['agente']['provincia'] = $provincia;
                        $data['info']['agente']['letra'] = "0";
                        $data['info']['agente']['tomo'] = $tomo;
                        $data['info']['agente']['asiento'] = $asiento;
                        $data['info']['agente']['tipo_identificacion'] = "natural";
                    }elseif($agente['letra'] == 'N' || $agente['letra'] == 'PE' || $agente['letra'] == 'E'){
                        list($letra, $tomo, $asiento) =  explode("-", $identificacion);
                        $data['info']['agente']['letra'] = $letra;
                        $data['info']['agente']['tomo'] = $tomo;
                        $data['info']['agente']['asiento'] = $asiento;
                        $data['info']['agente']['tipo_identificacion'] = "natural";
                    }elseif($agente['letra'] == 'PI'){
                        list($provincia, $tomo, $asiento) =  explode("-", $identificacion);
                        $provincia = str_replace("PI","",$provincia);
                        $data['info']['agente']['provincia'] = $provincia;
                        $data['info']['agente']['letra'] = 'PI';
                        $data['info']['agente']['tomo'] = $tomo;
                        $data['info']['agente']['asiento'] = $asiento;
                        $data['info']['agente']['tipo_identificacion'] = "natural";
                    }
                }else if ($agente['tipo_identificacion']=="juridico") {
                    list($tomo_ruc, $folio, $asiento_ruc, $digito) =  explode("-", $identificacion);                   
                    $data['info']['agente']['tomo_ruc'] = $tomo_ruc;
                    $data['info']['agente']['folio'] = $folio;
                    $data['info']['agente']['asiento_ruc'] = $asiento_ruc;
                    $data['info']['agente']['digito'] = $digito;  
                    $data['info']['agente']['tipo_identificacion'] = "juridico"; 
                }else if ($agente['tipo_identificacion'] == "pasaporte") {
                    $data['info']['agente']['letra'] = 'PAS';
                    $data['info']['agente']['pasaporte'] = $identificacion;
                    $data['info']['agente']['tipo_identificacion'] = "pasaporte";
                }

                $agtramos = AgentesRamosModel::where('id_agente','=', $agente->id)->orderBy("participacion", "ASC")->get(array('id_ramo','participacion'));
                $agtramos2 = AgentesRamosModel::where('id_agente','=', $agente->id)->groupBy("participacion")->orderBy("participacion", "ASC")->get(array('id_ramo','participacion'));

                $arr = array();
                

                foreach ($agtramos as $value) {
                    $p = $value['participacion'];
                    if ( !isset($arr[''.$p.''])) {
                        $arr[''.$p.''] = array();
                    }
                    array_push($arr[''.$p.''], $value['id_ramo']);
                }
                $data['info']['agente']['ramosp'] = $arr;


                $data['info']['agente']['ramos'] = $agtramos->toArray();
                $data['info']['agente']['countramos'] = AgentesRamosModel::where('id_agente','=', $agente->id)-> count();
                $data['info']['agente']['ramosgroup'] = $agtramos2->toArray();
                $data['info']['menu_crearramos'] = $this->ramoRepository->listar_cuentas($clause);
                 $data['info']['menu_crear'] = Ramos::where('padre_id','<>','0')
                ->where('padre_id','<>','"id"')
                ->where('empresa_id','=',$this->empresa_id)
                ->orderBy("nombre")->get();
  
            }
        }
        $data['uuid_agente'] = $agente['uuid_agente'];


        //Agregra variables PHP como variables JS
        $this->assets->agregar_var_js(array(
            "id_agente" => $uuid,
            "permiso_editar_agente" => $this->auth->has_permission('ver__editarAgente', 'agentes/ver/(:any)') == true ? 'true' : 'false',
            "countramos" => AgentesRamosModel::where('id_agente','=', $agente->id)-> count(),
        ));
      

        $menubreadcrumb = array(
            "url"   => '#',
            "clase" => 'accionBoton',
            "nombre" => "Accion"
        );
        $menubreadcrumb["opciones"]["#exportarBtn"] = "Exportar";


        $this->template->agregar_breadcrumb(array(
            "titulo" => '<i class="fa fa-child"></i> '.$data['info']['agente']['nombre']." ".$data['info']['agente']['apellido'],
            "ruta" => array(
                0 => array(
                    "nombre" => "Seguros",
                    "activo" => false
                ),
                1 => array(
                    "nombre" => 'Agentes',
                    "url"   => 'agentes/listar',
                    "activo" => false
                ),
                2 => array(
                    "nombre" => $data['info']['agente']['nombre']." ".$data['info']['agente']['apellido'],
                    "activo" => true
                )
            ),
            "menu" => $menubreadcrumb
        ));
        
        $data["subpanels"] = [];
        
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }
	
	function ajax_cambiar_agente_principal(){
		$id=$this->input->post('id');

		$principal['principal']=0;
		$agentes_no_principales=$this->AgentesModel->where('id_empresa',$this->empresa_id)->update($principal);
		$agente=$this->AgentesModel->find($id);
		$agente->principal=1;
		$agente->save();
		
		
		$agente_actualizado=$agente->toArray();
		$resources['datos']=$agente_actualizado;
		/*$resources['datos']['uuid_contacto']=bin2hex($contacto['uuid_contacto']);
		$resources['datos']['nombre_aseguradora']=$nombre_aseguradora;
		$resources['datos']['principal']=1;*/
		
		$this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($resources))->_display();
		exit;
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
    function tabladetalles($data = array()) {
        $this->load->view('tabladetalles', $data);
    }
}
?>
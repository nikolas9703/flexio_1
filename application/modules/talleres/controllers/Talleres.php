<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Grupos de Clientes
 *
 * Modulo para administrar la creacion, edicion de grupos de clientes.
 *
 * @package    PensaApp
 * @subpackage Controller
 * @category   Controllers
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  06/13/2016
 */
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Talleres\Repository\EquipoTrabajoRepository as EquipoTrabajoRepository;
use Flexio\Modulo\Colaboradores\Repository\ColaboradoresRepository as ColaboradoresRepository;
use Flexio\Modulo\Planilla\Repository\PlanillaRepository;
use Flexio\Modulo\Talleres\Models\EquipoColaboradores as ColaboradoresModel;
use Flexio\Modulo\Talleres\Repository\EquipoTrabajoCatalogoRepository;
use Flexio\Library\Util\FormRequest;

class Talleres extends CRM_Controller
{
    private $id_empresa;
    private $empresaObj;
    private $id_usuario;
    private $usuarioID;
    protected $equipoTrabajoRepository;
    protected $colaboradoresModel;
	protected $PlanillaRepository;
    protected $ColaboradoresRepository;
	protected $EquipoTrabajoCatalogoRepository;

    function __construct() {

        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();
        $uuid_empresa = $this->session->userdata('uuid_empresa');
        $empresaObj = new Buscar(new Empresa_orm, 'uuid_empresa');
        $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
        $this->id_usuario = $this->session->userdata("huuid_usuario");
        $this->usuarioID = $this->session->userdata("id_usuario");
        $this->id_empresa = $this->empresaObj->id;

        $this->load->model('colaboradores/colaboradores_orm');
        $this->load->model('configuracion_rrhh/departamentos_orm');

        //HMVC Load Modules
        $this->load->module(array('documentos'));

        $this->equipoTrabajoRepository = new EquipoTrabajoRepository();

       // $this->Colaboradores = new ColaboradoresRepository();
        $this->colaboradoresModel = new ColaboradoresModel();

        $this->load->model('contabilidad/centros_orm');

        $this->PlanillaRepository = new PlanillaRepository();

        $this->ColaboradoresRepository = new ColaboradoresRepository();

        $this->EquipoTrabajoCatalogoRepository = new EquipoTrabajoCatalogoRepository();
    }

    public function index() {
        redirect("talleres/listar");
    }

    public function listar() {
        //echo "<br>listar";
        // Verificar si tiene permiso para crear cliente Natural
        if (!$this->auth->has_permission('acceso')) {
            // No, tiene permiso, redireccionarlo.
            redirect('/');
        }
        //variables
        $data = array();

        $this->_Css();
        $this->_js();
        $this->assets->agregar_css(array(
        	'public/assets/css/plugins/jquery/jquery.fileupload.css',
        ));
        $this->assets->agregar_js(array(
        	'public/assets/js/plugins/jquery/fileupload/jquery.fileupload.js',
        ));

        $breadcrumb = array(
            "titulo" => '<i class="fa fa-wrench"></i> Equipo de trabajo',
            "ruta" => array(
                0 => array(
                    "nombre" => "Talleres",
                    "activo" => false
                ),
                1 => array(
                    "nombre" => '<b>Equipo de trabajo</b>',
                    "activo" => true
                )
            ),
            "menu" => array(
                "nombre" => "Crear",
                "url" => "talleres/crear",
                "opciones" => array()
            )
        );
        $menuOpciones["#exportarEquiposTrabajoBtn"] = "Exportar";
        $breadcrumb["menu"]["opciones"] = $menuOpciones;
        unset($data["mensaje"]);

        $estados = $this->_catalogoEstados();
        $data["estados"] = $estados;

        $this->template->agregar_titulo_header('Listado de Talleres');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar($breadcrumb);
    }

    public function ocultotabla() {
//If ajax request
//echo "ocultar tabla";
        $this->assets->agregar_js(array(
            'public/assets/js/modules/talleres/tabla.js'
        ));

        $this->load->view('tabla');
    }

    public function ajax_listar() {
        if (!$this->input->is_ajax_request()) {
            return false;
        }

        $nombre = $this->input->post("nombre");
        $numero = $this->input->post("codigo");
        $estado_id = $this->input->post("estado_id");

        $clause = array(
        	'empresa_id' => $this->empresaObj->id
        );

        if (!empty($numero)) $clause['codigo'] = array('LIKE', "%$numero%");
        if (!empty($nombre)) $clause['nombre'] = array('LIKE', "%$nombre%");
        if (!empty($estado_id)) $clause['estado_id'] = $estado_id;

        list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
        $count = $this->equipoTrabajoRepository->listar($clause, NULL, NULL, NULL, NULL)->count();
        list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
        $equipos = $this->equipoTrabajoRepository->listar($clause, $sidx, $sord, $limit, $start);

        //Constructing a JSON
        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->record = $count;

        if (!empty($equipos->toArray())) {
            $i = 0;
            foreach ($equipos as $row) {
                //dd($equipos);
                $id = Util::verificar_valor($row->id);

            	$ids_colaboradores = $this->colaboradoresModel->idsEquipoColaborador($id)->count();
                $estado = !empty($row->estado) && !empty($row->estado->etiqueta) ? $row->estado->etiqueta : "";
                $codigo = Util::verificar_valor($row->codigo);
                $nombre = Util::verificar_valor($row->nombre);

            	$url = base_url('talleres/ver/'.$row->uuid_equipo);
                $hidden_options = "";
               	$link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $id . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
               	$hidden_options .= '<a href="' . $url . '" data-id="' . $id . '" class="btn btn-block btn-outline btn-success">Ver detalle</a>';
               	$hidden_options .= '<a href="#" class="btn btn-block btn-outline btn-success subirArchivoBtn" data-id="'. $id .'">Subir archivo</a>';
               	$label_estado = !empty($estado) && $estado == "Activo" ? 'background:#5CB85C;color:#fff;' : 'background:red;color:#fff;';

                $response->rows[$i]["id"] = $id;
                $response->rows[$i]["cell"] = array(
                    '<a class="link" href="' . $url . '" >' . $codigo . '</a>',
                    $nombre,
                    $ids_colaboradores,
                    '',
                    '<span class="label col-sm-12 p-xxs" style="'. $label_estado .'">' . $estado .'</span>',
                    $link_option,
                    $hidden_options
                );
                $i++;
            }
        }
        //dd($response);
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response))->_display();
        exit;

    }

    public function crear($uuid_equipo = NULL) {

        $mensaje = array();
        $breadcrumb = array();
      //  $breadcrumb["titulo"] = '<i class="fa fa-wrench"></i> Equipo de Trabajo: Crear';

        if (!$this->auth->has_permission('acceso')) {
            // No, tiene permiso, redireccionarlo.
            $mensaje = array('estado' => 500, 'mensaje' => ' <b>Usted no cuenta con permiso para esta solicitud</b>', 'clase' => 'alert-danger');
        }

        $clause = array('empresa_id'=> $this->empresaObj->id);
        $colaboradores = Colaboradores_orm::where($clause)->get(array('id','nombre','apellido','email'));

        $lista_colaboradores = $colaboradores->map(function($item){
            return ['id'=>$item->id,'nombre_completo'=>$item->nombre." ".$item->apellido,'email'=>$item->email];
        });

        //CONSULTA CENTROS CONTABLES
        $clause= array('empresa_id' => $this->id_empresa,'estado'=>'Activo');
        $centros = Centros_orm::listar($clause);
        $centro_contables= $this->_buildTree($centros);

        $centro_contables_opciones = array();
        foreach($centro_contables as $centro_contable){
        	if(!empty($centro_contable['children'])){
        		foreach($centro_contable['children']  as $subcentro){
        			if(!empty($subcentro['area_negocio'])){
        				foreach($subcentro['area_negocio'] as $area_data){
        					$llave_unica = $centro_contable['id'].'-'.$subcentro['id'].'-'.$area_data['id']; //Centro-subcentro-area
        					$header =  $centro_contable['name'].'/'.$subcentro['name'];
        					$centro_contables_opciones[] = array(
        						"texto" => $area_data['nombre'],
        						"value" => $llave_unica,
        						"data_section" => $header,
        						"data_index" => $area_data['id']
        					);
        				}
        			}
        		}
        	}
        }

        //---------------------
        // Estados de Ordenes
        //---------------------
        $estados = $this->_catalogoEstados();

        $colaboradores = $this->ColaboradoresRepository->getAll(array("empresa_id" => $this->id_empresa))->toArray();

        //---------------------
        // SI EXISTE UUID
        //---------------------
        if(!empty($uuid_equipo)) {

        	$equipo = $this->equipoTrabajoRepository->findByUuid($uuid_equipo);
            $equipo->load('comentario_timeline');
        	$data['equipo_id'] = $equipo->id;
        	$data['ordenes_atender'] = $equipo->ordenes_atender;

        	//dd($equipo);

        	//---------------------
        	// DATOS DE COLABORADORES
        	//---------------------
        	//Colaboradores Relacionados al Equipo
        	$colaboradoresSeleccionados = !empty($equipo->colaboradores) ? $equipo->colaboradores->toArray() : array();

        	//Lista de Ids de Colaboradores Relacionados al Equipo
        	$colaboradoresSeleccionadosIds = (!empty($colaboradoresSeleccionados) ? array_map(function($colaboradoresSeleccionados) {
        		return $colaboradoresSeleccionados["colaborador"]["id"];
        	}, $colaboradoresSeleccionados) : "");

        	if(!empty($colaboradoresSeleccionadosIds)){
        		$colaboradores = Colaboradores_orm::whereNotIn('id', $colaboradoresSeleccionadosIds)->where("empresa_id",$this->id_empresa)->get(array('id','nombre','apellido','cedula'))->toArray();
        	}

        	//Filtro de Colaboradores Relacionados al Equipo
        	$colaboradoresSeleccionados = (!empty($colaboradoresSeleccionados) ? array_map(function($colaboradoresSeleccionados) {
        		return array(
        			"id" => $colaboradoresSeleccionados["colaborador"]["id"],
        			"nombre" => $colaboradoresSeleccionados["colaborador"]["nombre"] ." ". $colaboradoresSeleccionados["colaborador"]["apellido"],
      //        "cedula"=>$colaboradoresSeleccionados["colaborador"]["cedula"]
        		);
        	}, $colaboradoresSeleccionados) : "");


 			//---------------------
			// DATOS DE CENTROS RELACIONADOS
			//---------------------
			//Colaboradores Relacionados al Equipo
			$centrosSeleccionados = !empty($equipo->centros) ? $equipo->centros->toArray() : array();
			$centrosSeleccionadosIds = (!empty($centrosSeleccionados) ? array_map(function($centrosSeleccionados) {
				$value = $centrosSeleccionados["centro_padre_id"]."-".$centrosSeleccionados["centro_id"]."-".$centrosSeleccionados["departamento_id"];
				return array($value);
			}, $centrosSeleccionados) : "");

        	$this->assets->agregar_var_js(array(
        		'colaboradores'=>$lista_colaboradores,
        		'nombreEquipo' => $equipo->nombre,
        		'ordenesAtender' => $equipo->ordenes_atender,
        		'equipoID' => $equipo->id,
        		'estadoId' => !empty($equipo->estado_id) ? $equipo->estado_id : "",
        		"colaboradoresSeleccionadosArray" => !empty($colaboradoresSeleccionados) ? json_encode($colaboradoresSeleccionados) : '',
        		"centrosSeleccionadosArray" => !empty($centrosSeleccionadosIds) ? json_encode($centrosSeleccionadosIds) : '',
                'vista' => 'ver',
                "coment" =>(isset($equipo->comentario_timeline)) ? $equipo->comentario_timeline : "",
        	));
        	/*$breadcrumb["menu"] = array(
        		"nombre" => 'Acci&oacute;n',
        		"url" => '#',
        		"opciones" => array()
        	);*/
        //	$breadcrumb["menu"]["opciones"]["#agregarColaboradorLnk"] = "Agregar colaborador";
        	//$breadcrumb["titulo"] = '<i class="fa fa-wrench"></i> Equipos de Trabajo: '. $equipo->codigo;

          $breadcrumb = array(
              "titulo" => '<i class="fa fa-wrench"></i> Equipos de Trabajo: '. $equipo->codigo,
              "filtro" => false,
              "menu" => array(
                  "nombre" => 'Acci&oacute;n',
                  "url"	 => '#',
                  "opciones" => array(
                    "#agregarColaboradorLnk" => "Agregar colaborador",
                  )
              ),

              "ruta" => array(
                0 => array(
                    "nombre" => "Talleres",
                    "activo" => false,
                ),
                  1 => array(
                      "nombre" => "Equipo de trabajo",
                      "activo" => false,
                      "url" => 'talleres/listar'
                  ),
                  2=> array(
                      "nombre" => '<b>Detalle</b>',
                      "activo" => true
                  )
              ),
          );

        }else{

          $breadcrumb = array(
              "titulo" => '<i class="fa fa-wrench"></i> Equipo de Trabajo: Crear',
              "filtro" => false,
              "menu" => array(
                  "nombre" => 'Acci&oacute;n',
                  "url"	 => '#',
                  "opciones" => array()
              ),

              "ruta" => array(
                0 => array(
                    "nombre" => "Talleres",
                    "activo" => false,
                ),
                  1 => array(
                      "nombre" => "Equipo de trabajo",
                      "activo" => false,
                      "url" => 'talleres/listar'
                  ),
                  2=> array(
                      "nombre" => '<b>Crear</b>',
                      "activo" => true
                  )
              ),
          );
            $this->assets->agregar_var_js(array(
                "vista" => 'crear',
            ));
        }

        //---------------------
        // Colaboradores
        //---------------------

        $colaboradores = (!empty($colaboradores) ? array_map(function($colaboradores) {

        	return array(
        		"id" => $colaboradores["id"],
        		"nombre" => $colaboradores["nombre"] ." ". $colaboradores["apellido"].' - '. $colaboradores["cedula"]
        	);
        }, $colaboradores) : "");

        $this->_Css();
        $this->assets->agregar_css(array(
        	'public/assets/css/modules/stylesheets/animacion.css',
        	'public/assets/css/plugins/jquery/tree-multiselect/jquery.tree-multiselect.min.css',
        ));
        $this->_js();
        $this->assets->agregar_js(array(
        	'public/assets/js/default/tabla-dinamica.jquery.js',
        	'public/assets/js/plugins/jquery/tree-multiselect/jquery.tree-multiselect.js',
        	'public/assets/js/plugins/jquery/multiselect-master/multiselect.js',
        	'public/assets/js/default/vue.js',
        	'public/assets/js/default/vue-resource.min.js',
        	'public/assets/js/modules/talleres/vue.agregar_colaboradores.js',
        	'public/assets/js/modules/talleres/vue.colaboradores-talleres.js',
        ));

        //dd($colaboradores);
        $this->assets->agregar_var_js(array(
        	//"acceso" => $acceso,
        	"lista_colaboradores" => $lista_colaboradores,
        	"centrosArray" => json_encode($centro_contables_opciones),
        	"estadosArray" => json_encode($estados),
        	"colaboradoresArray" => json_encode($colaboradores)
        ));


        $data['mensaje'] = $mensaje;
        $this->template->agregar_titulo_header('Crear Equipo de Trabajo');
        $this->template->agregar_breadcrumb($breadcrumb);
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }

    public function vue_cargar_templates() {
    	$this->load->view('componente_agregar_colaboradores');
    }

    private function _Css() {
        $this->assets->agregar_css(array(
            'public/assets/css/default/ui/base/jquery-ui.css',
            'public/assets/css/default/ui/base/jquery-ui.theme.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
            'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
            'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
            'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
            'public/assets/css/plugins/jquery/switchery.min.css',
            'public/assets/css/plugins/jquery/chosen/chosen.min.css',
            'public/assets/css/plugins/jquery/fileinput/fileinput.css',
        ));

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
            'public/assets/js/plugins/jquery/combodate/combodate.js',
            'public/assets/js/plugins/jquery/combodate/momentjs.js',
        	'public/assets/js/plugins/jquery/switchery.min.js',
            'public/assets/js/default/lodash.min.js',
            'public/assets/js/default/accounting.min.js',
            'public/assets/js/plugins/jquery/chosen.jquery.min.js',
            'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
            'public/assets/js/moment-with-locales-290.js',
            'public/assets/js/plugins/bootstrap/daterangepicker.js',
            'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
            'public/assets/js/default/formulario.js',
        ));

    }

    private function _buildTree( $ar, $pid = null ) {

    	$op = array();
    	foreach( $ar as $item ) {
    		if((int)$item['padre_id'] == (int)$pid ) {
    			$op[$item['id']] = array(
    					'id' => $item['id'],
    					'name' => $item['nombre'],
    					'padre_id' => $item['padre_id']
    			);
    			// using recursion
    			$children =  $this->_buildTree( $ar, $item['id'] );

    			if( $children ) {
    				$contador_hijos = 0;
    				foreach( $children as $item_chi ) {

    					$op[$item['id']]['children'][$contador_hijos]['id'] = $item_chi['id'];
    					$op[$item['id']]['children'][$contador_hijos]['name'] = $item_chi['name'];
    					$op[$item['id']]['children'][$contador_hijos]['padre_id'] = $item_chi['padre_id'];

    					$dpto = $this->PlanillaRepository->findDepartamentosCentro($this->id_empresa, $item_chi['id']);
    					$op[$item['id']]['children'][$contador_hijos]['area_negocio'] = array();
    					foreach($dpto as $dpto_data){

    						$clause["empresa_id"] 			= $this->id_empresa;
    						$clause["centro_contable_id"] 	= array($item_chi['id']); //Subcentroo
    						$clause["departamento_id"] 		= array($dpto_data->id);    // o area de Negocio

    						$cantidad_colaboradores = $this->ColaboradoresRepository->getAll($clause)->count();
    						//$cantidad_colaboradores = Colaboradores_orm::listar($clause)->count();
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

    private function _catalogoEstados() {

    	$estados = $this->EquipoTrabajoCatalogoRepository->getEstados()->toArray();
    	return (!empty($estados) ? array_map(function($estados) {
    		return array(
    				"id" => $estados["id"],
    				"nombre" => $estados["etiqueta"]
    		);
    	}, $estados) : "");
    }

    public function ocultoformulario() {
    	$this->assets->agregar_js(array(
    			'public/assets/js/modules/talleres/crear'
    	));
    	$data=array();
    	$this->load->view('formulario');

    }

    public function guardar() {

        if($_POST){
            $equipo = NULL;
            Capsule::beginTransaction();
            try{

            	$clause = array('empresa_id' => $this->empresaObj->id);
                $total = $this->equipoTrabajoRepository->listar($clause, NULL, NULL, NULL, NULL)->count();
                $codigo = $total + 1;
                $equipo = $this->equipoTrabajoRepository->guardar( $this->empresaObj->id, $codigo);
                //dd($equipo['result']);
                Capsule::commit();
            }catch (ValidationException $e){
                log_message('error', $e);
                Capsule::rollback();
            }

            if (!is_null($equipo['result']) && $equipo['odt_abierta'] != 'odt_abierta') {
                $mensaje = array('clase' => 'alert-success', 'contenido' => '<b>¡&Eacute;xito!</b> Se ha guardado correctamente ' . $equipo->nombre);
            } elseif($equipo['odt_abierta'] == 'odt_abierta'){

                $mensaje = array('clase' => 'alert-danger', 'contenido' => '<strong>¡Error!</strong> Su solicitud no fue procesada, ya que, tiene ODT activas o puso un valor inferior a la capacidad máxima a atender.');
            } else {
                $mensaje = array('clase' => 'alert-danger', 'contenido' => '<strong>¡Error!</strong> Su solicitud no fue procesada');
            }
        } else {
            $mensaje = array('clase' => 'alert-warning', 'contenido' => '<strong>¡Error!</strong> Su solicitud no fue procesada');
        }

        $this->session->set_flashdata('mensaje', $mensaje);
        redirect(base_url('talleres/listar'));

    }

    function ajax_eliminar_colaborador() {
        if (!$this->input->is_ajax_request()) {
            return false;
        }
        $equipo_id = $this->input->post('equipo_id');
        $colaborador_id = $this->input->post('colaborador_id');

        $result = $this->colaboradoresModel->borrar($equipo_id, $colaborador_id);
        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($result))->_display();
        exit;
    }

    /**
     * Retornar arreglo con los
     * campos que se mostraran
     * en el formulario de subir archivos.
     *
     * @return array
     */
    function documentos_campos() {

    	return array(array(
    		"type"		=> "hidden",
    		"name" 		=> "equipo_id",
    		"id" 		=> "equipo_id",
    		"model" 	=> "campos.equipo_id",
    		"class"		=> "",
    		"readonly"	=> "",
    		"ng-model" 	=> "campos.equipo_id",
    		"label"		=> ""
    	));
    }

    function ajax_guardar_documentos() {
    	if(empty($_POST)){
    		return false;
    	}

    	//dd($_FILES);

    	$id = $this->input->post('equipo_id', true);
    	$modeloInstancia = $this->equipoTrabajoRepository->find($id);

    	$this->documentos->subir($modeloInstancia);
    }

    function ajax_guardar_colaboradores(){

    	if(empty($_POST)){
    		return false;
    	}
    	$colaboradores = array();

    	$equipo_id = $this->input->post('id');
    	//$colaboradores = $this->input->post('colaboradore');
    	/**
    	 * Inicializar Transaccion
    	 */
    	Capsule::beginTransaction();

    	try {

    		$this->equipoTrabajoRepository->guardar();

    	} catch(ValidationException $e){

    		// Rollback
    		Capsule::rollback();

    		log_message("error", "MODULO: ". __METHOD__ .", Linea: ". __LINE__ ." --> ". $e->getMessage()."\r\n");

    		echo json_encode(array(
    			"guardado" => false,
    			"mensaje" => "Hubo un error tratando de eliminar el dependiente."
    		));
    		exit;
    	}

    	// If we reach here, then
    	// data is valid and working.
    	// Commit the queries!
    	Capsule::commit();

    	//---------------------
    	// DATOS DE COLABORADORES
    	//---------------------
    	//Colaboradores Relacionados al Equipo
    	$equipo = $this->equipoTrabajoRepository->find($equipo_id);
    	$colaboradoresSeleccionados = !empty($equipo->colaboradores) ? $equipo->colaboradores->toArray() : array();

    	//Lista de Ids de Colaboradores Relacionados al Equipo
    	$colaboradoresSeleccionadosIds = (!empty($colaboradoresSeleccionados) ? array_map(function($colaboradoresSeleccionados) {
        	return $colaboradoresSeleccionados["colaborador"]["id"];
        }, $colaboradoresSeleccionados) : "");

    	if(!empty($colaboradoresSeleccionadosIds)){

    		$colaboradores = Colaboradores_orm::whereNotIn('id', $colaboradoresSeleccionadosIds)->get(array('id','nombre','apellido'))->toArray();
    		$colaboradores = (!empty($colaboradores) ? array_map(function($colaboradores) {
    			return array(
    				"id" => $colaboradores["id"],
    				"nombre" => $colaboradores["nombre"] ." ". $colaboradores["apellido"]
    			);
    		}, $colaboradores) : "");
    	}

    	echo json_encode(array(
    		"guardado" => true,
    		"colaboradores" => json_encode($colaboradores)
    	));
    	exit;
    }

    function ocultoformulariocomentarios() {

        $data = array();

        $this->assets->agregar_js(array(
            'public/assets/js/plugins/ckeditor/ckeditor.js',
            'public/assets/js/plugins/ckeditor/adapters/jquery.js',
            'public/assets/js/modules/talleres/vue.comentario.js',
            'public/assets/js/modules/talleres/formulario_comentario.js'
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
        $comentario = ['comentario'=>$comentario,'usuario_id'=>$this->usuarioID];
        $equipo = $this->equipoTrabajoRepository->agregarComentario($model_id, $comentario);
        $equipo->load('comentario_timeline');

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($equipo->comentario_timeline->toArray()))->_display();
        exit;
    }
}

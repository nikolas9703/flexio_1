<?php
/**
 * Administrador Modulos
 *
 * Administra los modulos adicionales que pueden ser instalados o
 * desintalados en el sistema.
 *
 * @package    PensaApp
 * @subpackage Controller
 * @category   Controllers
 * @author     Pensanomica Team
 * @link       http://www.pensanomica.com
 *
 */
class Modulos extends CRM_Controller
{
	/**
	 * @var
	 */
	private $cache;
	
	function __construct()
    {
        parent::__construct();
        $this->load->model('modulos_model');
        $this->load->model('modulos_orm');
        
       //Inicializar variable cache
        $this->cache = Cache::inicializar();
    }

    public function listar_modulos()
    {
    	$this->assets->agregar_css(array(
    		'public/assets/css/default/ui/base/jquery-ui.css',
    		'public/assets/css/default/ui/base/jquery-ui.theme.css',
    		'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
    		'public/assets/css/plugins/bootstrap/bootstrap-progressbar-3.3.0.min.css',
    		'public/assets/css/plugins/jquery/switchery.min.css',
    		'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
    	));
    	$this->assets->agregar_js(array(
    		'public/assets/js/default/jquery-ui.min.js',
    		'public/assets/js/plugins/jquery/jquery.sticky.js',
    		'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
    		'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
    		'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
    		'public/assets/js/plugins/jquery/jqgrid/plugins/jQuery.jqGrid.columnToggle.js',
    		'public/assets/js/plugins/bootstrap/bootstrap-progressbar.min.js',
    		'public/assets/js/plugins/jquery/jquery.ajaxQueue.min.js',
    		'public/assets/js/plugins/jquery/switchery.min.js',
    		'public/assets/js/plugins/jquery/bootstrap-multiselect.js',
    		'public/assets/js/modules/modulos/listar_modulos.js'
    	));
    	
    	$data = array(
    		"estados" => field_enums('modulos', 'estado')
    	);
    	

    	$this->template->agregar_titulo_header('Listado de M&oacute;dulos');

        $this->template->agregar_breadcrumb(array(
        	"titulo" => '<i class="fa fa-cogs"></i> Administraci&oacute;n de M&oacute;dulos',
        	"ruta" => array (
				0 => array (
					"nombre" => "Administraci&oacute;n",
					"activo" => false,
					"url" =>'configuracion' 
				),
				1 => array (
					"nombre" => '<b>M&oacute;dulos</b>',
					"activo" => true 
				) 
			)
        ));
        $this->template->agregar_contenido($data);
        $this->template->visualizar();
    }
    

    public function ajax_listar($grid=NULL)
    {

    	$clause = array();
    	 
    	/**
    	 * Verificar si existe algun $_POST
    	* de los campos de busqueda
    	*/
    	$nombre = $this->input->post('nombre', true);
    	$estado = $this->input->post('estado', true);
    	$descripcion = $this->input->post('descripcion', true);
    	
    	if( !empty($nombre)){
    	    $clause["nombre"] = array('LIKE', "%$nombre%");
    	}
    	if( !empty($estado) && is_int($estado)){
    		$clause["estado"] = $estado;
    	}
    	if( !empty($descripcion)){
    		$clause["descripcion"] = array('LIKE', "%$descripcion%");
    	}
    	 
    	list($page, $limit, $sidx, $sord) = Jqgrid::inicializar();
    
    	$count = Modulos_orm::listar($clause, NULL, NULL, NULL, NULL)->count();
    
    	list($total_pages, $page, $start) = Jqgrid::paginacion($count, $limit, $page);
    
    	$rows = Modulos_orm::listar($clause, $sidx, $sord, $limit, $start);
    	 
    	//Constructing a JSON
    	$response = new stdClass();
    	$response->page     = $page;
    	$response->total    = $total_pages;
    	$response->records  = $count;
    	$i=0;
    	 
    	if(!empty($rows->toArray())){
    		foreach ($rows->toArray() AS $i => $row){
    			 
    			if(!empty($grid)){

    			}else{
    
    				$hidden_options = "";
    				$link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-nombre="'.$row["nombre"].'" data-rol="'. $row['id'] .'"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
    	
    				$activar_desactivar_btn_id = $row["estado"] == 1 ? "desactivar_modulo" : "activar_modulo";
    				$activar_desactivar_btn_text = $row["estado"] == 1 ? "Deshabilitar" : "Habilitar";
    				$activar_desactivar_btn_status = $row["estado"] == 1 ? "inactivo" : "activo";
    				$activar_desactivar_btn_msg = $row["estado"] == 1 ? "desactivado" : "activado";

    				
    				$hidden_options .= '<a href="#" id="'. $activar_desactivar_btn_id .'" data-modulo="'.$row['id'] .'" data-status="'. $activar_desactivar_btn_status .'" data-msg="'. $activar_desactivar_btn_msg .'" class="btn btn-block btn-outline btn-success">'. $activar_desactivar_btn_text .'</a>';
    				
    				$response->rows[$i]["id"] = $row['id'];
    				$response->rows[$i]["cell"] = array(
    					Util::verificar_valor($row["nombre"]),
    					Util::verificar_valor($row["version"]),
    					Util::verificar_valor($row["descripcion"]),
    					$row["estado"] == 1 ? '<span class="label" style="color:white;background-color:#5CB85C">Activo</span>' : '<span class="label label-default">Inactivo</span>',
    					$link_option,
    					$hidden_options
    				);
    			}
    
    			$i++;
    		}
    	}
    
    	echo json_encode($response);
    	exit;
    }
    
    /*
     * Paso 1 - Instalacion de Modulo
     * 
     * Verificar las rutas o urls del modulo
     * si no existen, registrarlas en DB.
     */
    function ajax_instalar_1()
    {
    	//Just Allow ajax request
    	if(!$this->input->is_ajax_request()){
    		return false;
    	}
    	
    	//$response = $this->modulos_model->instalar_rutas();
    	//Limpiar Cache
    	$this->cache->clean();
    	
    	//Activar modulo
    	$id_modulo = $this->input->post('id_modulo', true);
    	$this->modulos_model->activar_modulo($id_modulo);
    	
    	$response = array(
    		"respuesta" => true,
    		"mensaje" => ""
    	);

    	$json = '{"results":['.json_encode($response).']}';
    	echo $json;
    	exit;
    }
    
    /*
     * Paso 2 - Instalacion de Modulo
     *
     * Instalar el SQL que tiene las tablas
     * que seran usadas por el modulo.
     */
    function ajax_instalar_2()
    {
    	//Just Allow ajax request
    	if(!$this->input->is_ajax_request()){
    		return false;
    	}
    	 
    	$response = $this->modulos_model->instalar_db();
    	//$response = array("completado" => $result);
    	
    	$json = '{"results":['.json_encode($response).']}';
    	echo $json;
    	exit;
    }
    
    function ajax_desactivar_modulo()
    {
    	//Just Allow ajax request
    	if(!$this->input->is_ajax_request()){
    		return false;
    	}
    	
    	$response = $this->modulos_model->desactivar_modulo();
    	
    	//Limpiar Cache
    	$this->cache->clean();
 
    	$json = '{"results":['.json_encode($response).']}';
    	echo $json;
    	exit;
    }
    
    /**
     * 
     * Funciones para usar en otros Modulos.
     * --
     * Estas funciones deberan empezar con el
     * prefijo comp_ que indica que son funciones
     * para compartir o ser usadas desde otros modulos.
     */
    function comp_listar_modulos_activos()
    {
     	//Just Allow ajax request
    	if($this->input->is_ajax_request())
    	{
    		$response = $this->modulos_model->comp_listar_modulos_activos();
    	
    		$json = '{"results":['.json_encode($response).']}';
    		echo $json;
    		exit;
    	}
    	else
    	{
    		return $this->modulos_model->comp_listar_modulos_activos();
    	}
    }
    
    
    
}
?>
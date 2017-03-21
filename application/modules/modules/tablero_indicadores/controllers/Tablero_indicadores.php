<?php
/**
 * Tablero de Indicadores.
 *
 * Modulo para ver los indicadores, graficas y reportes de los distintos modulos
 * activados den el sistema.
 *
 * @package    PensaApp
 * @subpackage Controller
 * @category   Controllers
 * @author     Pensanomica Team
 * @link       http://www.pensanomca.com
 * @copyright  08/04/2015
 */


class Tablero_indicadores extends CRM_Controller
{
	function __construct()
    {
         parent::__construct();
 
        $this->load->model('tablero_indicadores_model');

        //HMVC Load Modules
        $this->load->module(array('oportunidades', 'propiedades', 'actividades'));

        //Cargar Clase Util de Base de Datos
        $this->load->dbutil();
    }

    public function tablero()
    {
    	//Verificar si tiene permiso
    	/*if (! $this->auth->has_permission ('acceso')) {
    		//No tiene permiso, redireccionarlo.
    		redirect ( '/' );
    	}*/
     	$data = array(
    		/*"monto_total_ganado_venta" => $this->oportunidades->comp__seleccionar_montototal_oportunidades(array("Ganado"), "Venta", array("este_mes" => date("F", strtotime(date('Y-m-d'))))),
    		"monto_total_ganado_alquiler" => $this->oportunidades->comp__seleccionar_montototal_oportunidades(array("Ganado"), "Alquiler", array("este_mes" => date("F", strtotime(date('Y-m-d'))))),
    		"monto_total_comision_venta" => $this->oportunidades->comp__seleccionar_montototal_comisiones_ganadas("Venta"),
    		"monto_total_comision_alquiler" => $this->oportunidades->comp__seleccionar_montototal_comisiones_ganadas("Alquiler"),
    		"monto_total_abierto_venta" => $this->oportunidades->comp__seleccionar_montototal_oportunidades(array("Prospecto", "Cotizaci�n", "Negociaci�n"), "Venta"),
    		"monto_total_abierto_alquiler" => $this->oportunidades->comp__seleccionar_montototal_oportunidades(array("Prospecto", "Cotizaci�n", "Negociaci�n"), "Alquiler"),
    		"cantidad_propiedad_disponible_venta" => $this->propiedades->comp__seleccionar_propiedades_disponibles("Venta"),
    		"cantidad_propiedad_disponible_alquiler" => $this->propiedades->comp__seleccionar_propiedades_disponibles("Alquiler"),
    		"notificaciones" => $this->tablero_indicadores_model->seleccionar_historial_modulos(),
    		"actividades_pendientes" => $this->actividades->comp__seleccionar_actividades_pendientes(),*/
    		//"listado_subordinados" => $this->tablero_indicadores_model->listar_subordinados()
    	);

    	/*echo "<pre>";
    	print_r($filtroSubordinados);
    	echo "</pre>";*/

    	$this->assets->agregar_css(array(
    		'public/assets/css/default/ui/base/jquery-ui.css',
    		'public/assets/css/default/ui/base/jquery-ui.theme.css',
    		'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
    		'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
    	));
    	$this->assets->agregar_js(array(
    		'public/assets/js/plugins/highcharts/highcharts.js',
    		'public/assets/js/plugins/highcharts/highcharts-more.js',
    		'public/assets/js/plugins/highcharts/modules/solid-gauge.src.js',
    		'public/assets/js/plugins/highcharts/modules/no-data-to-display.js',
    		'public/assets/js/moment-with-locales.js',
    		'public/assets/js/plugins/bootstrap/daterangepicker.js',
    		'public/assets/js/plugins/jquery/jquery.slimscroll.js',
            'public/assets/js/modules/usuarios/perfil.js',
            'public/assets/js/modules/tablero_indicadores/graficas.js',
    		'public/assets/js/modules/tablero_indicadores/tablero.js',
    	));
     	//Agregra variables PHP como variables JS
    	$this->assets->agregar_var_js(array(
    		 "uuid_usuario" => 1,
    		//"uuid_usuario" => CRM_Controller::$uuid_usuario,
    	));
    	echo "dsd";
     	//Breadcrum Array
    	$breadcrumb = array(
    		"titulo" => '<i class="fa fa-tachometer"></i> Tablero de Indicadores',
    		"ruta" => array(
    			0 => array(
    				"nombre" => "Tablero de Indicadores",
    				"activo" => false
    			),
    			1 => array(
    				"nombre" => '<b>Indicadores</b>',
    				"activo" => true
    			)
    		),
    		"filtro" => false
    	);
      // $this->load->model('tests');
     // $model = Tests::findOrFail(1);
     	$this->template->agregar_titulo_header('Tablero de Indicadores');
    	$this->template->agregar_breadcrumb($breadcrumb);
    	$this->template->agregar_contenido($data);
    	$this->template->visualizar($breadcrumb);
    }

    /**
     * Seleccionar mas notificaciones
     *
     * @access	public
     * @param
     * @return	tabla
     */
    public function ajax_seleccionar_notificaciones_recientes()
    {
    	//Si es una peticion AJAX
    	if(!$this->input->is_ajax_request()){
    		return false;
    	}

    	$response = $this->tablero_indicadores_model->seleccionar_historial_modulos();

    	$json = '{"results":['.json_encode($response).']}';
    	echo $json;
    	exit;
    }

    /**
     * Cargar Vista Parcial de Tabla
     *
     * @return void
     */
    public function ocultotabla()
    {
    	//If ajax request
    	$this->assets->agregar_js(array(
    		'public/assets/js/modules/agentes/tabla.js'
    	));

    	$this->load->view('tabla');
    }


}

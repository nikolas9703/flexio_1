<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Jqgrid Class
 *
 * Para general jqgrid html y hacer consulta en db.
 *
 *
 * @package    PensaApp
 * @subpackage Library
 * @category   Libraries
 * @author     Pensanomica Team
 * @link       http://www.pensanomica.com
 * @since      Version 1.0
 */
class Jqgrid
{
	protected static $ci;

	/**
	 * Nombre del Modulo Actual
	 *
	 * @var $modulo
	 */
	private static $modulo;

	/**
	 * Ruta de la carpeta modules
	 *
	 *  @var $ruta_modulos
	 */
	private static $ruta_modulos;


    /**
     * Constructor
     *
     * @return void
     */
    public function __construct(){

    	//Instancia del core de CI
    	self::$ci =& get_instance();

    	//Nombre del Modulo (HMVC)
    	self::$modulo = self::$ci->router->fetch_module();

    	//Ruta donde estan los modulos
    	self::$ruta_modulos = self::$ci->config->item('modules_locations');
    }

    /**
     * Inicializar variables de paginacion
     *
     * return array
     */
    public static function inicializar()
    {
    	$page = (int)self::$ci->input->post('page', true);
    	$limit = (int)self::$ci->input->post('rows', true);
    	$sidx = self::$ci->input->post('sidx', true);
    	$sord = self::$ci->input->post('sord', true);

    	return array(
    		$page,
    		$limit,
    		$sidx,
    		$sord
    	);
    }

    /**
     * Esta funcion carga el html del jqgrid.
     *
     * @param string $id_grid
     *
     * @return string HTML
     */
    public static function cargar($id_grid=NULL)
    {
    	if($id_grid==NULL){
    		return false;
    	}

    	$id_no_records = $id_grid ."NoRecords";
    	$id_pager =  $id_grid. "Pager";

    	return '<div>
			<div id="'. $id_no_records .'" class="text-center lead"></div>

			<!-- grid table -->
			<table class="table table-striped" id="'. $id_grid .'"></table>

			<!-- pager  -->
			<div id="'. $id_pager .'"></div>
		</div>';
    }

	/**
	 * Esta funcion retorna las variables para la paginacion
	 *
	 * @return array
	 */
	public static function paginacion($count = 0, $limit = 10, $page = 1){
		//Calcule total pages if $coutn is higher than zero.
		$total_pages = ($count > 0 ? ceil($count/$limit) : 0);

		// if for some reasons the requested page is greater than the total
		if ($page > $total_pages) $page = $total_pages;

		//calculate the starting position of the rows
		$start = $limit * $page - $limit; // do not put $limit*($page - 1)

		// if for some reasons start position is negative set it to 0
		if($start < 0) $start = 0;

		return array($total_pages, $page, $start );
	}

}

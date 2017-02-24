<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Subpanel Class
 *
 * Muestra subpanel de modulos.
 *
 *
 * @package    PensaApp
 * @subpackage Library
 * @category   Libraries
 * @author     Pensanomica Team
 * @link       http://www.pensanomica.com
 * @since      Version 1.0
 */
class Subpanel
{
    protected $ci;

    /**
     * Nombre del Modulo Actual
     *
     * @var $modulo
     */
    private static $modulo;

    /**
     * Ruta de la carpeta modules
     *
     * @var $ruta_modulos
     */
    private static $ruta_modulos;

    /**
     * Contendra un arreglo
     * de los Modulos Activos
     * que podran mostrase en los
     * SubPaneles
     *
     * @var $subpaneles
     */
    public static $subpaneles = array();
    
    private static $subpanelArr = array();

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct() {

        //Instancia del core de CI
        $this->ci =& get_instance();

        //Nombre del Modulo (HMVC)
        self::$modulo = $this->ci->router->fetch_module();

        //Ruta donde estan los modulos
        self::$ruta_modulos = $this->ci->config->item('modules_locations');

        self::cargar_subpanel_activos();
    }

    /**
     * Esta funcion verifica el archivo subpanel
     * del modulo actual y carga en la variable
     * $subpaneles los datos de los modulos
     * que estan activos y que pueden ser visto
     * como subpaneles.
     *
     * @param
     * @return
     */
    private function cargar_subpanel_activos() {
        //Verificar si el modulo actual
        //Tiene en la carpeta config
        //el archivo de subpanels.php
        $subpanel_file = realpath(self::$ruta_modulos . self::$modulo . "/config/subpanels.php");

        if (file_exists($subpanel_file)) {

            /*
             * Incluir Archivo
             */
            include_once($subpanel_file);

            //Verificar si no esta vacio el arreglo
            if (!empty($config)) {
                self::$subpanelArr = !empty($config['subpanel']) ? $config['subpanel'] : array('');

                /**
                 * Buscar los modulos que deben aparecer como subpanel
                 * y que esten activos.
                 */
                $fields = array(
                    "nombre",
                    "controlador",
                    "icono",
                );
                $clause = array(
                    "estado" => "1"
                );
                $modulos = $this->ci->db->select($fields)
                    ->from('modulos')
                    ->where($clause)
                    ->where_in("controlador", self::$subpanelArr)
                    ->get()
                    ->result_array();

                if (!empty($modulos)) {
                    $i = 0;
                    foreach ($modulos AS $modulo) {

                        self::$subpaneles[$modulo["controlador"]] = array(
                            "nombre" => $modulo["nombre"],
                            "modulo" => $modulo["controlador"],
                            "icono" => $modulo["icono"],
                        );
                        ++$i;
                    }
                    
                    //---------------------------
					// Ordenar resultados
					// a como esta nuestro
					// array de config
                    //---------------------------
                    $order = array();
                    foreach(self::$subpanelArr as $subpanel){
                    	if(empty(self::$subpaneles[$subpanel])){
                    		continue;
                    	}
                    	$order[] = self::$subpaneles[$subpanel];
                    }
                    self::$subpaneles = $order;
                }
            }
        }
    }
    
    /**
     * Esta funcion retorna el arreglo de los modulo
     * que estan activos y relacionados al modulo actual.
     *
     * @return array
     */
    public static function lista_modulos_activos_relacionados() {

        $modulos = array();
        if (!empty(self::$subpaneles)) {
            foreach (self::$subpaneles AS $subpanel) {
                $modulos[] = $subpanel["modulo"];
            }
        }
        return $modulos;
    }

    /*
     * Esta function retorna el HTML
     * de grupo de subpanel en elemento Tabs
     */
    public static function visualizar_grupo_subpanel($id_modulo_data = NULL) {

        if ($id_modulo_data == NULL || empty(self::$subpaneles)) {
            return false;
        }
        
        //SUBPANEL FORMULARIO CONTENIDO
        $html = '<div id="sub-panel">';
        $html .= '<div id="sub-panel-formulario-modulos" class="tab-content">';
        foreach (self::$subpaneles AS $key => $subpanel) {
            
            //$activo = $key == 0 ? 'active' : '';
            $activo = "";
            $views_folder = realpath(self::$ruta_modulos . $subpanel["modulo"] . "/views/");
            $oculto = !empty($subpanel["modulo"]) && $subpanel["modulo"] == "contactos" ? "" : "";

            if (file_exists($views_folder)) {
                $handler = opendir($views_folder);
                $handler2 = opendir($views_folder);

                //CREO UNA LISTA PARA SABER SI EXISTE formulario.php
                $lista = array();
                while ($file = readdir($handler2)) {
                    if ($file != "." && $file != ".." && !preg_match("/modal/i", $file)) {

                        $file = str_replace("_", "-", str_replace(".php", "", $file));
                        $lista[$file] = 1;

                    }
                }

                // open directory and walk through the filenames
                while ($file = readdir($handler)) {

                    // if file isn't this directory or its parent, add it to the results
                    if ($file != "." && $file != ".." && !preg_match("/modal/i", $file)) {

                        $file = str_replace("_", "-", str_replace(".php", "", $file));

                        // check with regex that the file format is what we're expecting and not something else
                        
                        if (isset($lista["formulario"])) {
                            if (preg_match("/crear/i", $file) and self::$modulo != "pedidos" && self::$modulo != "facturas") {
                                
                                $html .= '<div class="tab-pane ' . $activo . ' ' . $oculto . '" id="crear' . ucfirst($subpanel["modulo"]) . '">' . modules::run($subpanel["modulo"] . '/crearsubpanel', array()) . '</div>';
                            }

                            if (preg_match("/editar/i", $file) and self::$modulo != "pedidos" && self::$modulo != "facturas") {
                                
                                $html .= '<div class="tab-pane ' . $activo . ' ' . $oculto . '" id="editar' . ucfirst($subpanel["modulo"]) . '">' . modules::run($subpanel["modulo"] . '/editarsubpanel', array()) . '</div>';
                            }
                        } else {
                            if ($subpanel["modulo"] != "contactos" && $subpanel["modulo"] != "ordenes_ventas" && $subpanel["modulo"] != "cotizaciones") {
                                if (preg_match("/crear/i", $file)) {

                                    $html .= '<div class="tab-pane ' . $activo . ' ' . $oculto . '" id="crear' . ucfirst($subpanel["modulo"]) . '">' . modules::run($subpanel["modulo"] . '/crearsubpanel', $id_modulo_data) . '</div>';
                                }
                                if (preg_match("/editar/i", $file)) {

                                    $html .= '<div class="tab-pane ' . $oculto . '" id="editar' . ucfirst($subpanel["modulo"]) . '">' . modules::run($subpanel["modulo"] . '/editarsubpanel', $id_modulo_data) . '</div>';
                                }
                            }
                        }
                    }
                }
            }

        }
        $html .= '</div>';

        //SUBAPNEL TABS
        $html .= '<div id="sub-panel-grid-modulos">';
        $html .= '<div class="panel-heading white-bg" style="padding-bottom:0px !important">
    				<!--<span class="panel-title">&nbsp;</span>-->
    					<ul class="nav nav-tabs nav-tabs-xs" role="tablist" style="bottom: -4px !important">';

        foreach (self::$subpaneles AS $key => $subpanel) {
            if($subpanel['nombre'] == "Comisiones"){
                $subpanel['nombre'] = "Comision";
            }

            $activo = $key == 0 ? 'active' : '';
            $html .= '<li class="dropdown ' . $activo . '">
                <a href="#tabla' . ucfirst($subpanel["modulo"]) . '" data-toggle="tab" aria-controls="tabla' . ucfirst($subpanel["modulo"]) . '" role="tab">' . $subpanel['nombre'] . '</a>
             	<ul class="dropdown-menu sub-panel-dropdown-contenido hide" role="menu">
                	<li class="' . $activo . '" ><a href="#tabla' . ucfirst($subpanel["modulo"]) . '" data-toggle="tab" data-targe="#tabla' . ucfirst($subpanel["modulo"]) . '">Tabla</a></li>
                	<li><a href="#crear' . ucfirst($subpanel["modulo"]) . '" data-toggle="tab" data-targe="#crear' . ucfirst($subpanel["modulo"]) . '">Crear</a></li>
					<li><a href="#editar' . ucfirst($subpanel["modulo"]) . '" data-toggle="tab" data-targe="#editar' . ucfirst($subpanel["modulo"]) . '">Editar</a></li>
				</ul>
			</li>';
        }

        //SUBPANEL TABLA CONTENT
        $html .= '</ul></div>';
        $html .= '<div class="tab-content white-bg">';
        foreach (self::$subpaneles AS $key => $subpanel) {
            $activo = $key == 0 ? 'active' : '';
            $html .= '<div role="tabpanel" class="tab-pane ' . $activo . '" id="tabla' . ucfirst($subpanel["modulo"]) . '">' . modules::run($subpanel["modulo"] . '/ocultotabla', $id_modulo_data, self::$modulo) . '</div>';
        }
        $html .= '</div></div>';
        $html .= '</div>';

        //echo "<script>console.log(".  json_encode($html).");</script>";

        echo $html;
    }

}

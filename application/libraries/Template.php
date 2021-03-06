<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CRMBase Template
 *
 * Esta clase es para administrar los templates que estaran es la carpeta de temas.
 *
 * @package    PensaApp
 * @category   Libraries
 * @author     Pensanomica Dev Team
 * @version    1.5 - 16/11/2015
 * @link       http://www.pensanomca.com
 * @since      13/03/2015
 *
 */

//Repositorios
use Flexio\Modulo\Bodegas\Repository\BodegasRepository as bodegasRep;




class Template
{
        //repositorios
        private static $bodegasRep;
        private $id_usuario;
	protected static $ci;
	public static $theme_default = '';
	protected static $theme_path = '';
	private static $module = '';
	private static $modulo_id = '';
	private static $module_file = '';
	private static $module_path = '';
	public static $modulo_vista = '';
	public static $module_view_path = '';
	private static $controller = '';
	private static $method = '';
	protected $title = '';
	protected $description = '';
	protected $breadcrumb = array();
	protected $content = array();
	protected static $empresa_id;
	protected static $cache;
	/*
	 * variables para la funciones
	 * de armar formulario
	 */
	private static $values_from_DB = array();
	private static $modulo_campos = array();
	private static $tipo_campo = array();

	private static $tipo_campo_attrs = array(
		'text' => array(
			'class'	  => 'form-control',
			'type'	  => 'text'
		),
		'textarea' => array(
			'class'	  => 'form-control ck-editor',
			'type'	  => 'textarea'
		),
		'select-checkbox-addon' => array(
			'class'	  => 'form-control',
			'type'	  => 'select-checkbox-addon'
		),
		'select-right-button-addon' => array(
			'class'	  => 'form-control',
			'type'	  => 'select-right-button-addon'
		),
		'select-checkbox-button-addon' => array(
			'class'	  => 'form-control',
			'type'	  => 'select-checkbox-button-addon'
		),
		'email' => array(
			'class'	  => 'form-control',
			'type'	  => 'email'
		),
		'file' => array(
			'class'	  => 'form-control',
			'type'	  => 'file'
		),
		'file_imagen' => array(
			'class'	  => 'btn btn-block btn-white',
			'type'	  => 'file_imagen'
		),
		'input-left-addon' => array(
			'class'	  => 'form-control',
			'type'	  => 'input-left-addon'
		),
		'input-right-addon' => array(
			'class'	  => 'form-control',
			'type'	  => 'input-right-addon'
		),
		'password' => array(
			'class'	  => 'form-control',
			'type'	  => 'password'
		),
		'checkbox' => array(
			'class'	  => 'form-control',
			'type'	  => 'checkbox'
		),
		'radio' => array(
			'type'	  => 'radio',
		),
		'relate' => array(
			'type'	  => 'relate',
			'class'	  => 'form-control',
		),
		'relate-right-button' => array (
			'type' => 'relate-right-button',
			'class' => 'form-control'
		),
		'select' => array(
			'class'	  => 'form-control',
			'type'	  => 'select',
			'selected' => ''
		),
		'hidden' => array(
			'type'	  => 'hidden'
		),
		'submit' => array(
			'class'	  => 'btn btn-primary btn-block',
			'type'	  => 'submit',
			'id'	  => 'guardarFormBtn'
		),
		'button' => array(
			'class'	  => 'btn btn-default btn-block',
			'type'	  => 'button',
		),
		'button-cancelar' => array(
			'class'	  => 'btn btn-default btn-block',
			'type'	  => 'button-cancelar',
		),
		'button-guardar' => array(
			'class'	  => 'btn btn-default btn-block',
			'type'	  => 'button-guardar',
		),
		'button-label' => array(
			'class'	  => 'btn btn-default btn-block',
			'type'	  => 'button-label',
		),
		'link' => array(
			'class'	  => 'btn btn-default btn-block',
			'type'	=> 'link',
		),
		'tagsinput' => array(
			'class'	  => 'form-control',
			'type'	  => 'tagsinput',
		),
		'google_maps' => array(
			'class'	  => 'form-control',
			'type'	  => 'google_maps',
		),
		'fecha' => array(
			'class'	  => 'form-control',
			'type'	  => 'fecha',
		),
		'groups-radio-button' => array(
			'class' => 'form-group',
			'type' => 'groups-radio-button'
		),
		'head_title' => array(
			'type'	  => 'head_title'
		),
		'p-text' => array(
			'type'	  => 'p-text'
		),
		'input-select' => array(
			'class'	  => 'form-control',
			'type'	  => 'input-select'
		),
		'input-daterange' => array(
			'class'	  => 'form-control',
			'type'	  => 'input-daterange'
		),
		'firma' => array(
			'class'	  => 'form-control',
			'type'	  => 'firma'
		),
		'select-multiple' => array(
			'class'	  => 'form-control',
			'type'	  => 'select-multiple'
		),
		'date-range-picker' => array(
			'class'	  => 'form-control',
			'type'	  => 'date-range-picker'
		),
	);

	/**
	 * El formulario se arma por default
	 * estaticamente con 4 columnas, utilizando
	 * el sistema grid del bootstrap
	 * URL: http://getbootstrap.com/css/#grid
	 *
	 * Esta variable contiene las clases que
	 * utilizaran los campos en base a 4 columnas.
	 */
	private static $campo_columnas = array(
		1 => "col-xs-12 col-sm-6 col-md-6 col-lg-3",
		2 => "col-xs-12 col-sm-6 col-md-6 col-lg-6",
		3 => "col-xs-12 col-sm-6 col-md-6 col-lg-9",
		4 => "col-xs-12 col-sm-12 col-md-12 col-lg-12"
	);

	/**
	 * Constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{


		//Instancia del core de CI
		self::$ci =& get_instance();

		//Cargar Template Parser Class
		self::$ci->load->helper('file');

		//Ruta donde estan los templates
		self::$theme_path = self::$ci->config->item('theme_path');

		//Template que se cargara por defecto.
		self::$theme_default = self::$ci->config->item('theme_default');

		//Nombre del Modulo (HMVC)
  		self::$module = self::$ci->router->fetch_module();

		//Nombre del metodo que esta corriendo actualmente
		//El nombre de la vista, debe ser igual al metodo.
		self::$module_file = self::$ci->router->method .".php";

		//Ruta donde estan los modulos
		self::$module_path = self::$ci->config->item('modules_locations');
		self::$modulo_vista = self::$ci->router->method;
		self::$module_view_path = empty(self::$module) ? "" : self::$module_path . self::$module."/views/";

 		//Nombre del Controlador
		self::$controller = self::$ci->router->fetch_class();

		//Nombre del Metodo
		self::$method = self::$ci->router->fetch_method();

		self::$ci->load->model('usuarios/empresa_orm');

		//Obtener el uuid_empresa de session
		$uuid_empresa = self::$ci->session->userdata('uuid_empresa');
		self::$empresa_id = !empty(Empresa_orm::findByUuid($uuid_empresa)->id) ? Empresa_orm::findByUuid($uuid_empresa)->id : "";
                // $this->id_usuario = self::$ci->session->userdata("id_usuario");
		//Inicializar variable cache
		self::$cache = Cache::inicializar();

                //repositorios
                self::$bodegasRep = new bodegasRep();
                //echo '<br>';print_r(self::$ci->session->userdata);echo '</br>';
	}

	public static function seleccionar_modulo_id()
	{
		$moduloINFO = self::$ci->db->select("id")
			->distinct()
			->from('modulos')
			->where("controlador", self::$module)
			->get()
			->result_array();
		return !empty($moduloINFO[0]["id"]) ? $moduloINFO[0]["id"] : "";
	}

 	/**
	 * Establecer el titulo de la seccion actual que esta viendo.
	 * Este titulo va en el <header> en la etiqueta <title>
	 *
	 * @access public
	 * @param string $title
	 *
	 * @return void
	 */
	public function agregar_titulo_header($title)
	{
		$this->title = $title;
	}
	/**
	 * Establecer descripcion de la seccion actual que esta viendo.
	 *
	 * @access public
	 * @param string $description
	 *
	 * @return void
	 */
	public function agregar_descripcion($description)
	{
		$this->description = $description;
	}
	/**
	 * Establecer la ruta de navegacion, de la seccion actual.
	 *
	 * @access public
	 * @param array $breadcrumb
	 *
	 * @return void
	 */
	public function agregar_breadcrumb($breadcrumb=NULL)
	{
		if($breadcrumb==NULL){
			return false;
		}
		$this->breadcrumb = $breadcrumb;
	}
	/**
	 * Establecer el contenido que se mostrara en el body.
	 *
	 * @access public
	 * @param array $description
	 *
	 * @return void
	 */
	public function agregar_contenido($data)
	{
		$this->content = $data;
	}
	/**
	 * Cagar la vista del tema por defecto o del modulo actual.
	 *
	 * @param string $file Nombre de la vista a cargar, sin incluir la extension (.php)
	 * @param array $vars variables a cargar en la vista.
	 * @return void
	 */
	public function cargar_vista($file="", $vars=array())
	{
		//Ruta donde estan los templates
		self::$theme_path = self::$ci->config->item('theme_path');
		//Template que se cargara por defecto.
		self::$theme_default = self::$ci->config->item('theme_default');
		$vista_parcial = false;
		/**
		 * Si el nombre del archivo es un arreglo
		 * se trata de una vista parcial.
		 */
		if(is_array($file)){
			self::$module = self::$controller = $file[0];
			self::$modulo_vista = $file[1];
			$file = $file[1];
			$vista_parcial = true;
		}
		//Verificar si esta navegando en un modulo o en un
		//controlador fuera de la carpeta modules.

		$module_view_path = empty(self::$module) ? '' : self::$module_path . self::$module."/views/";
		if($vars){
			extract($vars);
		}
		//echo $module_view_path."<br>";
		/*
		 * Verificar si el archivo existe en la carpeta del tema actual.
		 */
		if(file_exists(self::$theme_path . self::$theme_default ."/$file.php")){
			include_once(self::$theme_path . self::$theme_default ."/$file.php");
		}else{
			/*
			 * De lo contrario verificar si se trata de una vista de un modulo o controlador
			 */
			if(empty($module_view_path)){
				//Vista de un controlador fuera de la carpeta "modules"
				/**
				 *buscar la vista en su folder que es el nombre del controlador
				 *
				 */
				include_once APPPATH."views/".self::$controller."/".$file;
			}else{
				if($vista_parcial == true){
					/*echo $module_view_path . $file." ". read_file($module_view_path . $file."");
					die();*/
					include_once($module_view_path . $file.".php");
				}else{
					if(file_exists($module_view_path . $file)) {
						include_once($module_view_path . $file);
					}
				}
			}
		}
	}
	/**
	 * Esta funcion hace un query consultando segun el modulo y la vista que se esta viendo
	 * actualmente, la cantidad de pestanas, formularios, paneles y campos para luego
	 * armar el formulario HTML.
	 *
	 * @param  string
	 * @return array
	 */
	public static function cargar_formulario($valores=array())
	{
		$module_config_path = empty(self::$module) ? "" : self::$module_path . self::$module."/config/";
	 	include($module_config_path ."/config.php") ;

	 	$modulo_id = self::seleccionar_modulo_id();

	 	$clause = array(
 			"v.id_modulo" => $modulo_id,
 			"v.vista" => self::$modulo_vista,
 			"uc.estado" => "activo",
			"p.estado" => "activo"
	 	);

 	 	$tabla_campos = $config['modulo_config']['prefijo'] == '' ? self::$controller.'_campos' : $config['modulo_config']['prefijo'].'_'. self::$controller .'_campos';

 	 	//Destruir variable
 	 	unset($config);
 	 	self::$values_from_DB = array();

		//Si $valores viene con data
		//inicializar variable global
		if(!empty($valores)){
			self::$values_from_DB = $valores;
		}
		self::$modulo_campos = array();

		//Consultar Cache de campo de formulario
		$results = self::$cache->get("form-". md5($tabla_campos . $modulo_id . self::$modulo_vista));

		//Verificar si existe Cache
		if($results == null)
		{
	 	 	//Query
	 	 	/*$fields = array(
	 	 		"v.id_vista",
	 	 		"v.id_modulo",
	 	 		"v.vista",
	 	 		"p.id_pestana",
	 	 		"p.pestana",
	 	 		"f.id_formulario",
	 	 		"f.nombre_formulario",
	 	 		"f.atributos AS atributos_formulario",
	 	 		"f.remoto",
	 	 		"pa.id_panel",
	 	 		"pa.panel",
	 	 		"pc.id_panel_campo",
	 	 		"pc.id_campo",
	 	 		"uc.nombre_campo",
	 	 		"uc.etiqueta",
	 	 		"uc.longitud",
	 	 		"tc.nombre AS tipo" ,
	 	 		"uc.estado",
	 	 		"uc.atributos",
	 	 		"uc.agrupador_campo",
	 	 		"uc.tabla_relacional",
	 	 		"uc.contenedor",
	 	 		"uc.requerido",
	 	 		"uc.link_url",
	 	 		"uc.fecha_cracion",
	 	 		"uc.posicion"
	 	 	);
			$results = self::$ci->db->select($fields)
					->from('mod_vistas v' )
					->join('mod_pestanas p', 'p.id_vista = v.id_vista', 'LEFT OUTER')
					->join('mod_formularios f', 'f.id_pestana = p.id_pestana', 'LEFT OUTER')
					->join('mod_paneles pa', 'pa.id_formulario = f.id_formulario', 'LEFT OUTER')
					->join('mod_panel_campos pc', 'pc.id_panel=pa.id_panel', 'LEFT OUTER')
					->join($tabla_campos."  uc", "uc.id_campo = pc.id_campo", 'LEFT OUTER')
					->join('mod_tipo_campos tc', 'tc.id_tipo_campo = uc.id_tipo_campo', 'LEFT OUTER')
					->where($clause)
					->order_by('p.orden', 'ASC')
					->order_by('uc.posicion', 'ASC')
					->get()
					->result_array();*/

			//Formularios tabla dinamica
			$query1 = "SELECT DISTINCT `v`.`id_vista`, `v`.`id_modulo`, `v`.`vista`, `p`.`id_pestana`, `p`.`pestana`, p.orden, `f`.`id_formulario`, `f`.`nombre_formulario`, `f`.`atributos` AS `atributos_formulario`, `f`.`remoto`, `pa`.`id_panel`, `pa`.`panel`, `pc`.`id_panel_campo`, `pc`.`id_campo`, `uc`.`nombre_campo`, `uc`.`etiqueta`, `uc`.`longitud`, `tc`.`nombre` AS `tipo`, `uc`.`estado`, `uc`.`atributos`, `uc`.`agrupador_campo`, `uc`.`tabla_relacional`, `uc`.`contenedor`, `uc`.`requerido`, `uc`.`link_url`, `uc`.`fecha_cracion`, `uc`.`posicion`
						FROM `mod_vistas` `v`
						LEFT OUTER JOIN `mod_pestanas` `p` ON `p`.`id_vista` = `v`.`id_vista`
						LEFT OUTER JOIN `mod_formularios` `f` ON `f`.`id_pestana` = `p`.`id_pestana`
						LEFT OUTER JOIN `mod_paneles` `pa` ON `pa`.`id_formulario` = `f`.`id_formulario`
						LEFT OUTER JOIN `mod_panel_campos` `pc` ON `pc`.`id_panel`=`pa`.`id_panel`
						LEFT OUTER JOIN `$tabla_campos` `uc` ON `uc`.`id_campo` = `pc`.`id_campo`
						LEFT OUTER JOIN `mod_tipo_campos` `tc` ON `tc`.`id_tipo_campo` = `uc`.`id_tipo_campo`
						WHERE `v`.`id_modulo` = $modulo_id
						AND `v`.`vista` = '".self::$modulo_vista."'
						AND `uc`.`estado` = 'activo'
						AND `p`.`estado` = 'activo'";

			//Formularios Remoto
			$query2 = "SELECT DISTINCT `v`.`id_vista`, `v`.`id_modulo`, `v`.`vista`, `p`.`id_pestana`, `p`.`pestana`, p.orden, `f`.`id_formulario`, `f`.`nombre_formulario`, `f`.`atributos` AS `atributos_formulario`, `f`.`remoto`, `pa`.`id_panel`, `pa`.`panel`, '' AS id_panel_campo, '' AS id_campo, '' AS nombre_campo, '' AS etiqueta, '' AS longitud, '' AS tipo, '' AS estado, '' AS atributos, '' AS agrupador_campo, '' AS tabla_relacional, '' AS contenedor, '' AS requerido, '' AS link_url, '' AS fecha_cracion, '' AS posicion
						FROM `mod_vistas` `v`
						LEFT OUTER JOIN `mod_pestanas` `p` ON `p`.`id_vista` = `v`.`id_vista`
						LEFT OUTER JOIN `mod_formularios` `f` ON `f`.`id_pestana` = `p`.`id_pestana`
						LEFT OUTER JOIN `mod_paneles` `pa` ON `pa`.`id_formulario` = `f`.`id_formulario`
						WHERE `v`.`id_modulo` = $modulo_id
						AND `v`.`vista` = '".self::$modulo_vista."'
						AND `p`.`estado` = 'activo'
						AND `f`.`remoto` <> ''";

			$sql = "SELECT td.* FROM (($query1)
					UNION ($query2)) AS td
					ORDER BY td.orden ASC, CAST(td.posicion AS UNSIGNED) ASC";
			$results = self::$ci->db->query($sql)->result_array();

			/*echo "<pre>";
			print_r($results);
			echo "</pre>";*/

			//Si no existe guardar cache
			self::$cache->set("form-". md5($tabla_campos . $modulo_id . self::$modulo_vista), $results);
		}

 		if(!empty($results))
		{
			$i=0;
			foreach($results AS $result)
			{
				if(!empty($result["contenedor"]) && $result["contenedor"] == "tabla-dinamica" || !empty($result["contenedor"]) && $result["contenedor"] == "tabla-dinamica-sumativa")
				{
					$existing_key = Util::array_search_key(self::$modulo_campos, $result["agrupador_campo"]);
					if(!empty(self::$modulo_campos[$existing_key])){
						self::$modulo_campos[$existing_key][$result["agrupador_campo"]][] = $result;
					}else{
						self::$modulo_campos[$i]["tipo"] = $result["contenedor"];
						self::$modulo_campos[$i][$result["agrupador_campo"]][] = $result;
					}
				}
				else
				{
					self::$modulo_campos[$i] = $result;
				}
				$i++;
			}
		}

		/*echo "<pre>";
		print_r($clause);
		print_r($results);
		echo "</pre>";
		die();*/
		//echo self::$ci->db->last_query();

		echo self::armar_html();
	}
	/**
	 * Buscar en la tabla catalogo del modulo "_cat"
	 * los <option> a mostrar en los campos <select>
	 *
	 * @return array
	 */
	private static function buscar_catalogo_campo($id_campo=NULL)
	{
		if($id_campo==NULL){
			return false;
		}
		$module_config_path = empty(self::$module) ? "" :self::$module_path . self::$module."/config/";
		include($module_config_path ."/config.php") ;
		/**
		 * Primero verificar si el campo
		 * tiene valor en Catalogo General
		 */
		$fields = array(
			"cat.id_cat",
			"cat.etiqueta"
		);
		$clause = array(
			"catmod.id_modulo" => self::seleccionar_modulo_id(),
			"catmod.id_campo" => $id_campo,
		);
		$catalogoGeneral = self::$ci->db->select()
				->distinct()
				->from('mod_catalogos AS cat')
				->join('mod_catalogo_modulos AS catmod', 'catmod.id_cat = cat.id_cat', 'LEFT')
				->where($clause)
				->order_by("cat.orden", "ASC")
				->get()
				->result_array();

		/*if(CRM_Controller::$id_modulo == 216 && $id_campo == 6){
			echo "DESCUENTOS <pre>";
			print_r($catalogoGeneral);
			print_r($clause);
			echo "</pre>";
			die();
		}*/
		if(!empty($catalogoGeneral)){

			return $catalogoGeneral;

		}else{

			$modulo_tabla_catalogo = $config['modulo_config']['prefijo'] == '' ? self::$controller.'_cat' : $config['modulo_config']['prefijo'].'_'.self::$controller.'_cat';

			//Destruir variable
			unset($config);

            $orden='etiqueta';

            //If para ordenar por campo orden en el catálogo de término de pago en proveedores
            //El orden por etiqueta no es deseado en ese módulo
            if($modulo_tabla_catalogo=="pro_proveedores_cat"){
                $orden='orden';
            }

			//Query
			$fields = array(
				"id_cat",
				"etiqueta"
			);
			$clause = array(
				"id_campo" => $id_campo
			);
			return self::$ci->db->select($fields)
				->distinct()
				->from($modulo_tabla_catalogo)
				->where($clause)
				->order_by($orden, 'ASC')
				->get()
				->result_array();
		}
	}

	/**
	 * Consultar la tabla relacional y obtener el uuid y nombre de campo
	 * para armar los <option> a mostrar en el campo <select>
	 *
	 * @return array
	 */
	private static function buscar_relacion_campo($tabla_relacional=NULL)
	{
		if($tabla_relacional==NULL){
			return false;
		}

		/**
		 * Al es singular de estas tablas no se les quita la terminacion "es"
		 * sino solamnete la letra "s".
		 */
		$nombre_tablas_singular_excepcion = array("clientes", "proy_proyectos");

		//Verificar si el nombre de la tabla contiene el caracter "_" raya abajo.
		if(preg_match("/(\[\[_[^]]*\]\])/im", $tabla_relacional) || preg_match("/_/im", $tabla_relacional)){
			$tablaArr = explode('_', $tabla_relacional, 2);
			//Poner en singualr el nombre de la tabla, para poder armar el campo uuid de la tabla.
			$uuid_tabla = !empty($tablaArr[1]) && is_string($tablaArr[1]) ? (preg_match('/es$/', $tablaArr[1]) && !in_array($tablaArr[1], $nombre_tablas_singular_excepcion) ? preg_replace('/es$/', "", $tablaArr[1]) : preg_replace('/s$/', "", $tablaArr[1])) : "";
		}else{
			//Poner en singualr el nombre de la tabla, para poder armar el campo uuid de la tabla.
			$uuid_tabla = (preg_match('/es$/', $tabla_relacional) ? preg_replace('/es$/', "", $tabla_relacional) : preg_replace('/s$/', "", $tabla_relacional));
		}
                //*********************************************************************************
                if($tabla_relacional == "evaluadopor")
		{
			// Array con los uuid de usuarios
			// que el usario actual puede ver.
	       	//$ver_usuarios = @CRM_Controller::andrea_ACL();
	       	$fields = array(
	       		"CONCAT_WS(' ', IF(usu.nombre != '', usu.nombre, ''), IF(usu.apellido != '', usu.apellido, '')) AS nombre"
	       	);

	       	//Verificar nombre tabla, para usar id o uuid
	       	if($tabla_relacional == "evaluadopor"){
	       		$fields[] = "usu.id";
	       	}else{
	       		$fields[] = "HEX(uuid_$uuid_tabla) AS uuid";
	       	}
	       	$clause = array(
	       		"usu.nombre <> ''" => NULL,
	       		"usu.estado" => "Activo",
                         "usuemp.empresa_id" => self::$empresa_id
	       	);
			self::$ci->db->select($fields)
				 ->distinct()
				 ->from('usuarios usu')
                                 ->join('usuarios_has_empresas AS usuemp', 'usu.id = usuemp.usuario_id', 'LEFT')
  				 ->where($clause)
				 ->order_by('nombre', 'ASC');
			return self::$ci->db->get()->result_array();
		}
                //**************************************************************

		//Campos y clausula
		if($tabla_relacional == "usuarios" || $tabla_relacional == "usuarios_id")
		{
			// Array con los uuid de usuarios
			// que el usario actual puede ver.
	       	//$ver_usuarios = @CRM_Controller::andrea_ACL();
	       	$fields = array(
	       		"CONCAT_WS(' ', IF(nombre != '', nombre, ''), IF(apellido != '', apellido, '')) AS nombre"
	       	);

	       	//Verificar nombre tabla, para usar id o uuid
	       	if($tabla_relacional == "usuarios_id"){
	       		$fields[] = "id";
	       	}else{
	       		$fields[] = "HEX(uuid_$uuid_tabla) AS uuid";
	       	}

	       	$clause = array(
	       		"nombre <> ''" => NULL,
	       		"estado" => "Activo"
	       	);
			self::$ci->db->select($fields)
				 ->distinct()
				 ->from('usuarios')
				 ->where($clause)
				 ->order_by('nombre', 'ASC');
			return self::$ci->db->get()->result_array();
		}
		else{
			if($tabla_relacional == "proy_proyectos" || $tabla_relacional == "proy_tipo_transaccion"){
				$fields = array(
					"HEX(uuid_$uuid_tabla) AS uuid",
					"nombre"
				);
				$clause = array(
					"nombre <> ''" => NULL
				);
			}
			else if($tabla_relacional == "proy_tipo_transaccion"){
				$fields = array(
					"HEX(uuid_tipo_transaccion) AS uuid",
					"nombre"
				);
				$clause = array(
					"nombre <> ''" => NULL
				);
			}
			else if($tabla_relacional == "cl_clientes_sociedades"){
				$fields = array(
					"HEX(uuid_sociedad) AS uuid",
					"nombre_comercial AS nombre"
				);
				$clause = array(
					"nombre_comercial <> ''" => NULL
				);
			}
			 else if($tabla_relacional == "usuarios_categoria"){
				$fields = array(
					"HEX(uuid_$uuid_tabla) AS uuid",
					"nombre"
				);
				$clause = array(
					"nombre <> ''" => NULL,
					"key <> 'admin'" => NULL
				);
			}
			else if($tabla_relacional == "act_tipo_actividades" ){
				$fields = array(
					"HEX(uuid_tipo_actividad) AS uuid",
					"nombre",
					"icono"
				);
				$clause = array(
					"nombre <> ''" => NULL
				);
			}else if($tabla_relacional == "agt_agentes"){
				$fields = array(
					"HEX(uuid_agente) AS uuid",
					"CONCAT_WS(' ', IF(nombre != '', nombre, ''), IF(apellido != '', apellido, '')) AS nombre"
				);
				$clause = array(
					"nombre <> ''" => NULL
				);
			}
			else if($tabla_relacional == "col_colaboradores"){
				$fields = array(
						"id",
						"CONCAT_WS(' ', IF(nombre != '', nombre, ''), IF(apellido != '', apellido, '')) AS nombre"
				);
 				$clause = array(
 						"empresa_id = '".self::$empresa_id."'" => NULL
 				);
			}

                        else if($tabla_relacional == "cob_cobro_catalogo"){
				$fields = array(
						"id",
                                                "tipo",
						"valor AS nombre"
				);
 				$clause = array(
 						"tipo = 'pago'" => NULL
 				);
			}

			else if($tabla_relacional == "dep_departamentos"){
				$fields = array(
						"id",
						"CONCAT_WS(' ', IF(nombre != '', nombre, '')) AS nombre"
				);
				$clause = array(
						"empresa_id = '".self::$empresa_id."'" => NULL
				);
			}
			elseif($tabla_relacional == "cen_centros_formulario"){
          $fields = array(
              "id",
              "CONCAT_WS(' ', IF(nombre != '', nombre, '')) AS nombre"
              );
           $clause = array(
              "nombre <> ''"=> NULL,
              "estado = 'Activo'"  => NULL,
			  "padre_id = '0'"  => NULL,
              "empresa_id = '".self::$empresa_id."'" => NULL
            );
						$tabla_relacional ="cen_centros";
			}


                        else if($tabla_relacional == "pro_proveedores"){
                            $fields = array(
                                "HEX(uuid_proveedor) AS uuid",
                                "CONCAT_WS(' ', IF(nombre != '', nombre, '')) AS nombre"
                            );
                            $clause = array(
                                "nombre <> ''"=> NULL,
                                "estado = '1'"  => NULL,
                                "id_empresa = '".self::$ci->session->userdata("id_empresa")."'" => NULL
                            );
			}
                        else if($tabla_relacional == "pro_categorias"){
                            $fields = array(
                                "HEX(uuid_categoria) AS uuid",
                                "CONCAT_WS(' ', IF(nombre != '', nombre, '')) AS nombre"
                            );
                            $clause = array(
                                "nombre <> ''"=> NULL,
                                "estado = '1'"  => NULL,
                                "id_empresa = '".self::$ci->session->userdata("id_empresa")."'" => NULL
                            );
			}
                        else if($tabla_relacional == "pro_tipos"){
                            $fields = array(
                                "HEX(uuid_tipo) AS uuid",
                                "CONCAT_WS(' ', IF(nombre != '', nombre, '')) AS nombre"
                            );
                            $clause = array(
                                "nombre <> ''"=> NULL,
                                "estado = '1'"  => NULL,
                                "id_empresa = '".self::$ci->session->userdata("id_empresa")."'" => NULL
                            );
			}
                        else if($tabla_relacional == "ped_pedidos"){
                            $fields = array(
                                "HEX(uuid_pedido) AS uuid",
                                "CONCAT_WS(' ', IF(numero != '', CONCAT('PD',numero), '')) AS nombre"
                            );
                            $clause = array(
                                "numero <> ''"=> NULL,
                                "id_estado > '1' AND id_estado < '4'"  => NULL,//Pedido En Cotizacion || Parcial
                                "id_empresa = '".self::$ci->session->userdata("id_empresa")."'" => NULL
                            );
			}

                        //listado de acreedores

                         else if($tabla_relacional == "acreedor_id"){

                            $tabla_relacional = "pro_proveedores";
                            $fields = array(
                                "id",
                                "CONCAT_WS(' ', IF(nombre != '', nombre, '')) AS nombre"
                            );
                            $clause = array(
                                "nombre <> ''"=> NULL,
                                "estado = 'activo'"  => NULL,
                                "id_empresa = '".self::$empresa_id."'" => NULL,
                                "acreedor = 'SI'" => NULL
                            );
			}

                        //listado de estados para descuentos directos

                         else if($tabla_relacional == "desc_descuentos_cat"){

                            $fields = array(
                                "id_cat AS id",
                                "etiqueta AS nombre"
                            );
                            $clause = array(
                                "id_campo = '15'" => NULL
                            );
			}

                        //ESTA CONDICION ES TEMPORAL HASTA QUE RAFAEL CAMBIE
                        //EL CAMPO ESTADO A ENTERO - ACTUALMENT ESTA STRING
                        else if($tabla_relacional == "contab_impuestos"){
                            $fields = array(
                                "HEX(uuid_impuesto) AS uuid",
                                "CONCAT_WS(' ', IF(nombre != '', nombre, ''), ' ', IF(impuesto != '', CONCAT(impuesto,'%'), '')) AS nombre"
                            );
                            $clause = array(
                                "nombre <> ''"=> NULL,
                                "estado = 'Activo'"  => NULL,
                                "empresa_id = '".self::$empresa_id."'" => NULL
                            );
			}
                        else if($tabla_relacional == "activos" || $tabla_relacional == "ingresos" || $tabla_relacional == "gastos" || $tabla_relacional == "variantes"  || $tabla_relacional == "pasivos"){
                            $tipo_cuenta_id = array(1);
                            switch ($tabla_relacional) {
                                case "activos":
                                    $tipo_cuenta_id = array(1);
                                    break;
                                case "pasivos":
                                   	$tipo_cuenta_id = array(2);
                                   	break;
                                case "ingresos":
                                    $tipo_cuenta_id = array(4);
                                    break;
                                case "gastos":
                                    $tipo_cuenta_id = array(5);
                                    break;
                                case "variantes":
                                    $tipo_cuenta_id = array(5);
                                    break;
                            }

                            $tabla_relacional = "contab_cuentas";
                            $fields = array(
                                "id",
                                "HEX(uuid_cuenta) AS uuid",
                                "CONCAT_WS(' ', IF(codigo != '', codigo, ''), IF(nombre != '', nombre, '')) AS nombre"
                            );
                            $clause = array(
                                "codigo <> ''"=> NULL,
                                "nombre <> ''"=> NULL,
                                "estado = '1'"  => NULL,
                                "tipo_cuenta_id IN (".  implode(',',$tipo_cuenta_id).")"  => NULL,
                                "id NOT IN (SELECT padre_id FROM $tabla_relacional WHERE empresa_id = '".self::$empresa_id."')"  => NULL,
                                "empresa_id = '".self::$empresa_id."'" => NULL
                            );
			}


      else if($tabla_relacional == "activos_cuentas_extra"){
          $tipo_cuenta_id = array(1);
        $tabla_relacional = "contab_cuentas";
      /*  $fields = array(
            "id",
            "CONCAT_WS(' ', IF(codigo != '', codigo, ''), IF(nombre != '', nombre, '')) AS nombre"
        );
        $clause = array(
            "codigo <> ''"=> NULL,
            "nombre <> ''"=> NULL,
            "estado = '1'"  => NULL,
            "tipo_cuenta_id IN (".  implode(',',$tipo_cuenta_id).")"  => NULL,
            "id NOT IN (SELECT padre_id FROM $tabla_relacional WHERE empresa_id = '".self::$empresa_id."')"  => NULL,
            "empresa_id = '".self::$empresa_id."'" => NULL
        );
*/
        ///seleciona los centros hijos
          $query = self::$ci->db->query("SELECT id, CONCAT_WS(' ', IF(codigo != '', codigo, ''), IF(nombre != '', nombre, '')) AS nombre
          FROM contab_cuentas WHERE empresa_id = ".self::$empresa_id." AND
           codigo <> '' AND
          nombre <> '' AND
          estado = '1' AND
          tipo_cuenta_id IN (".  implode(',',$tipo_cuenta_id).")   AND
          id NOT IN (SELECT padre_id FROM $tabla_relacional WHERE empresa_id = ".self::$empresa_id.
          " ORDER BY id ASC)");
          return $query->result_array();

      }
			else if($tabla_relacional == "pasivos_planilla"){
				$tipo_cuenta_id = array(2);
				switch ($tabla_relacional) {

					case "pasivos":
						$tipo_cuenta_id = array(2);
						break;

				}

				$tabla_relacional = "contab_cuentas";
				$fields = array(
						"id AS id",
						"CONCAT_WS(' ', IF(codigo != '', codigo, ''), IF(nombre != '', nombre, '')) AS nombre"
				);
				$clause = array(
						"codigo <> ''"=> NULL,
						"nombre <> ''"=> NULL,
						"estado = '1'"  => NULL,
						"tipo_cuenta_id IN (".  implode(',',$tipo_cuenta_id).")"  => NULL,
						"id NOT IN (SELECT padre_id FROM $tabla_relacional WHERE empresa_id = '".self::$empresa_id."')"  => NULL,
						"empresa_id = '".self::$empresa_id."'" => NULL
				);
			}

      else if($tabla_relacional == "cuentas_bancos"){

          $query = self::$ci->db->query("SELECT cc.id, CONCAT(cc.codigo, ' ', cc.nombre) As nombre FROM contab_cuenta_banco AS cb
          LEFT OUTER JOIN  contab_cuentas AS cc ON cc.id = cb.cuenta_id
          WHERE cb.empresa_id = ".self::$empresa_id);
          return $query->result_array();

      }
      else if($tabla_relacional == 'ban_bancos'){
           $tabla_relacional = "ban_bancos";
           $fields = array(
           "id AS id",
           "CONCAT_WS(' ', IF(ruta_transito != '', ruta_transito, ''), '-' , IF(nombre != '', nombre, '')) AS nombre"
           );
           $clause = array();
    }


			else if($tabla_relacional == 'opp_oportunidades'){
				$fields = array(
					"HEX(opp.uuid_oportunidad) AS uuid",
					"opp.nombre AS nombre"
				);
				$clause = array(
					"nombre <> ''" => NULL,
					"valor NOT IN('vendido', 'venta_perdida')" => NULL
				);
				 if(CRM_Controller::$categoria_usuario_key != 'admin'){
					  $uuid_usuarios = @CRM_Controller::andrea_ACL();
				 }
				 self::$ci->db->select($fields);
				 self::$ci->db->distinct();
				 self::$ci->db->from('opp_oportunidades AS opp');
				 self::$ci->db->join('opp_oportunidades_cat AS cat', 'cat.id_cat = opp.id_etapa_venta', 'LEFT');
				 self::$ci->db->where($clause);

				 if(CRM_Controller::$categoria_usuario_key != 'admin' && !empty($ver_usuarios["uuid_usuario"])){
				 	self::$ci->db->where_in("HEX(id_asignado)", $ver_usuarios["uuid_usuario"]);
				 }

				 $query = self::$ci->db->get();
				 return $query->result_array();

			 }elseif($tabla_relacional == 'cuenta_contable_pasiva'){
			 	///seleciona la cuentas transaccionales
			 	$query = self::$ci->db->query("SELECT id, CONCAT(codigo, ' ', nombre) As nombre FROM contab_cuentas WHERE empresa_id = ".self::$empresa_id." AND estado='1' AND tipo_cuenta_id = 2 AND id NOT IN (SELECT padre_id FROM contab_cuentas WHERE empresa_id = ".self::$empresa_id.") ORDER BY codigo ASC");
			 	return $query->result_array();
			}elseif($tabla_relacional == 'entrada_manual_cuenta'){
				///seleciona la cuentas transaccionales
					$query = self::$ci->db->query("SELECT id, CONCAT(codigo, ' ', nombre) As nombre FROM contab_cuentas WHERE empresa_id = ".self::$empresa_id." AND estado='1' AND id NOT IN (SELECT padre_id FROM contab_cuentas WHERE empresa_id = ".self::$empresa_id.") ORDER BY codigo ASC");
					return $query->result_array();
			}elseif($tabla_relacional == 'entrada_manual_centro'){
				///seleciona los centros hijos
					$query = self::$ci->db->query("SELECT id, nombre
					FROM cen_centros WHERE empresa_id = ".self::$empresa_id."
					AND estado='Activo'
					AND id NOT IN (SELECT padre_id FROM cen_centros WHERE empresa_id = ".self::$empresa_id." AND estado='Activo')
					ORDER BY id ASC");
					return $query->result_array();
			}
                        elseif($tabla_relacional == 'cen_centros'){
				///seleciona los centros hijos
					$query = self::$ci->db->query("SELECT HEX(uuid_$uuid_tabla) AS uuid, nombre FROM cen_centros WHERE empresa_id = ".self::$empresa_id." AND estado='Activo' AND id NOT IN (SELECT padre_id FROM cen_centros WHERE empresa_id = ".self::$empresa_id.") ORDER BY nombre ASC");
					return $query->result_array();
			}
                        elseif($tabla_relacional == 'bod_bodegas'){
                            ///seleciona las bodegas transaccionales -> que no tienen hijos
                            $query = self::$ci->db->query("SELECT HEX(uuid_$uuid_tabla) AS uuid, nombre FROM bod_bodegas WHERE empresa_id = ".self::$empresa_id." AND estado='1' AND id NOT IN (SELECT padre_id FROM bod_bodegas WHERE empresa_id = ".self::$empresa_id.") ORDER BY nombre ASC");
                            return $query->result_array();
			}
                        elseif($tabla_relacional == 'bod_bodegas2'){//solo muestra bodegas manuales
                            $bodegas2 = [];
                            $bodegasA = self::$bodegasRep->get(["empresa_id" => self::$empresa_id, "transaccionales" => "SI"]);

                            foreach($bodegasA as $bodega)
                            {
                                if($bodega->raiz->entrada_id == "1")//bodegas manuales
                                {
                                    $bodegas2[] = [
                                        "uuid"      => $bodega->uuid_bodega,
                                        "nombre"    => $bodega->nombre
                                    ];
                                }
                            }

                            return $bodegas2;
			}
                        elseif($tabla_relacional == 'fac_factura_catalogo_termino_pago'){
                            $query = self::$ci->db->query("SELECT etiqueta AS id, valor AS nombre FROM fac_factura_catalogo WHERE tipo = 'termino_pago'");
                            return $query->result_array();
			}
                        else{

				$fields = array();

				/**
				 * Consultar los campos
				 * de la tabla relacional.
				 */
				$clause = array(
					"TABLE_SCHEMA" => self::$ci->db->database,
					"TABLE_NAME" => $tabla_relacional
				);
				$columns = self::$ci->db->select("COLUMN_NAME")
						->distinct()
						->from("information_schema.COLUMNS")
						->where($clause)
						->get()
						->result_array();
				$columns = (!empty($columns) ? array_map(function($columns){ return $columns["COLUMN_NAME"]; }, $columns) : array());

				//Verificar si existe campos: nombre y apellido
				if(in_array("codigo", $columns)){
					$fields[] = "CONCAT_WS(' ', IF(codigo != '', CONCAT_WS('-', codigo), ''), ' ', IF(nombre != '', nombre, '')) AS nombre";
                }else{
					$fields[] = in_array("apellido", $columns) ? "CONCAT_WS(' ', IF(nombre != '', nombre, ''), IF(apellido != '', apellido, '')) AS nombre" : "IF(nombre != '', nombre, '') AS nombre";
                }

				//Verificar si existe campos: id o uuid
                //las tablas que esten en el arreglo prioridadId
                //devolveran el campo uuid=id int(10) primarykey
                $prioridadId = array("inv_categorias");
				$fields[] = (in_array("uuid_$uuid_tabla", $columns) and !in_array($tabla_relacional, $prioridadId)) ? "HEX(uuid_$uuid_tabla) AS uuid" : "id AS uuid";

				$clause = array(
					"nombre <> ''" => NULL
				);

				//Verificar si existe campos: empresa_id
				if(in_array("empresa_id", $columns)){
					$clause["empresa_id"] = self::$empresa_id;
				}

				//Verificar campos: estado o empresa_id
				if(in_array("estado", $columns)){
					in_array("estado", $columns) ? $clause["estado"] = 1 : (in_array("id_estado", $columns) ? $clause["id_estado"] = 1 : $clause["estado_id"] = 1);
				}
			}

			//Query
			return self::$ci->db->select($fields)
				->distinct()
				->from($tabla_relacional)
				->where($clause)
				->order_by('nombre', 'ASC')
				->get()
				->result_array();
		}
	}
	/**
	 * Esta funcion verifica   el tipo de campo y le agrega
	 * al array $tipo_campo los atributos necesarios y en caso
	 * tal que sea una vista de "Ver/Editar" los valores que
	 * provienenn de DB.
	 *
	 * @return void
	 */
	private static function asignar_campo_atributos($campo)
	{
		//Tipo de Campo
		self::$tipo_campo[$campo['tipo']] =  array(
			"type" => self::$tipo_campo_attrs[$campo['tipo']]["type"],
		);

		//Verificar atributo class para el campo actual
		if(!empty(self::$tipo_campo_attrs[$campo['tipo']]["class"])){
			self::$tipo_campo[$campo['tipo']]["class"] =  self::$tipo_campo_attrs[$campo['tipo']]["class"];
		}

		//Resetear variable $tipo_campo
		if(!empty(self::$tipo_campo[$campo['tipo']])){
			foreach(self::$tipo_campo[$campo['tipo']] AS $index => $value){
				if($index != "class" && $index != "type"){
					unset(self::$tipo_campo[$campo['tipo']][$index]);
				}
			}
		}

		/**
		 * Esto aplica para todos los tipos de campos
		 * Si en DB tiene asignado atributos custom
		 * agregarlo al arreglo.
		 */
		if(!empty($campo['atributos'])){
			$atributos = (array)json_decode($campo['atributos']);
			foreach($atributos AS $index => $atributo){
				self::$tipo_campo[$campo['tipo']][$index] = $atributo;
			}
		}

		/**
		 * Verificar si el campo es requerido para agregarle el atributo
		 * de campo requerido, para validar con el plugin jQuery Validate.
		 */
		if(!empty($campo['requerido']) && $campo['requerido'] == 1){
			self::$tipo_campo[$campo['tipo']]["data-rule-required"] = "true";
		}
		/**
		 * Verificar si el campo tiene el atributo "data-columns"
		 * Para ocupar una cantidad especifica de columnas
		 */
		if(!empty(self::$tipo_campo[$campo['tipo']]["data-columns"])){
			self::$tipo_campo[$campo['tipo']]["data-columns"] = self::$campo_columnas[self::$tipo_campo[$campo['tipo']]["data-columns"]];
		}else{
			//De lo contrario asignar 1 columna por default.
			self::$tipo_campo[$campo['tipo']]["data-columns"] = self::$campo_columnas[1];
		}

		/**
		 * Establecer atributo name del campo
		 */
		if(!empty($campo['agrupador_campo']) && $campo['contenedor'] != "tabla-dinamica"){
			self::$tipo_campo[$campo['tipo']]['id'] = $campo['agrupador_campo'] .'['.$campo['nombre_campo'].']';
			self::$tipo_campo[$campo['tipo']]['name'] = $campo['agrupador_campo'] .'['.$campo['nombre_campo'].']';
		}else{
			self::$tipo_campo[$campo['tipo']]['id'] = 'campo['.$campo['nombre_campo'].']';
			self::$tipo_campo[$campo['tipo']]['name'] = 'campo['.$campo['nombre_campo'].']';
		}
		//Verificar si el campo tiene agrupador y el contenedor es "tabla dinamica"
		if(!empty($campo['agrupador_campo']) && $campo['contenedor'] == "tabla-dinamica" || !empty($campo['agrupador_campo']) && $campo['contenedor'] == "tabla-dinamica-sumativa"){
			self::$tipo_campo[$campo['tipo']]['id'] = $campo['nombre_campo'];
			self::$tipo_campo[$campo['tipo']]['nombre_campo'] = $campo['nombre_campo'];
			self::$tipo_campo[$campo['tipo']]['name'] = $campo['agrupador_campo'] .'[0]['.$campo['nombre_campo'].']';
			self::$tipo_campo[$campo['tipo']]['agrupador'] = !empty($campo['etiqueta']) ? $campo['agrupador_campo'] : "";
		}

		switch ($campo['tipo'])
        {
        	case 'link':
        		//remover atribuhto name
        		unset(self::$tipo_campo[$campo['tipo']]['name']);
        		//si no tiene clase, asignarle la clase de default.
        		if(empty(self::$tipo_campo[$campo['tipo']]['class']) && self::$tipo_campo[$campo['tipo']]['class'] == ""){
        			self::$tipo_campo[$campo['tipo']]['attr']['class'] = "btn btn-default btn-block";
        		}else{
        			self::$tipo_campo[$campo['tipo']]['attr']['class'] = self::$tipo_campo[$campo['tipo']]['class'];
        		}
        		if(!empty(self::$tipo_campo[$campo['tipo']]['ng-model'])){
        			self::$tipo_campo[$campo['tipo']]['attr']['ng-model'] = self::$tipo_campo[$campo['tipo']]['ng-model'];
        		}
        		if(!empty(self::$tipo_campo[$campo['tipo']]['ng-click'])){
        			self::$tipo_campo[$campo['tipo']]['attr']['ng-click'] = self::$tipo_campo[$campo['tipo']]['ng-click'];
        		}
				//Agregar url
				self::$tipo_campo[$campo['tipo']]['url'] = !empty($campo['link_url']) ? $campo['link_url'] : "#";
				self::$tipo_campo[$campo['tipo']]['attr']['id'] = $campo['nombre_campo'];
				self::$tipo_campo[$campo['tipo']]['label'] = !empty($campo['etiqueta']) ? $campo['etiqueta'] : "";
				break;
			case 'radio':
				self::$tipo_campo[$campo['tipo']]['label'] = !empty($campo['etiqueta']) ? $campo['etiqueta'] : "";
				//Verificar si hay un valor que mostrar en el campo
				if(!empty(self::$values_from_DB[$campo['nombre_campo']])){
					self::$tipo_campo[$campo['tipo']]['checked'] =  self::$values_from_DB[$campo['nombre_campo']];
				}
				//Style para corregir que no se vaya hacia la izquierda
				self::$tipo_campo[$campo['tipo']]["style"] = "position:relative; margin-left:0px;";
				//Si el campo es de tipo select
				//consultar los valores que iran en el option
				//segun la tabla catalogo del modulo.
				$catalogo = self::buscar_catalogo_campo($campo["id_campo"]);
				if(!empty($catalogo)){
					$options = array();
					foreach($catalogo AS $catalogo){
						$options[$catalogo["id_cat"]] = $catalogo["etiqueta"];
					}
					self::$tipo_campo[$campo['tipo']]['values'] = $options;
				}
				break;
			case 'checkbox':
				self::$tipo_campo[$campo['tipo']]['label'] = !empty($campo['etiqueta']) ? $campo['etiqueta'] : "";
				//Verificar si hay un valor que mostrar en el campo
				if(!empty(self::$values_from_DB[$campo['nombre_campo']])){
					self::$tipo_campo[$campo['tipo']]['checked'] =  !empty(self::$values_from_DB[$campo['nombre_campo']]) ? true : falses;
				}
				//Si el campo es de tipo select
				//consultar los valores que iran en el option
				//segun la tabla catalogo del modulo.
				$catalogo = self::buscar_catalogo_campo($campo["id_campo"]);
				if(!empty($catalogo)){
					$options = array();
					foreach($catalogo AS $catalogo){
						$options[$catalogo["id_cat"]] = $catalogo["etiqueta"];
					}
					self::$tipo_campo[$campo['tipo']]['values'] = $options;
				}
				break;
			case 'select':
			case 'input-select':
                $nombrecampo = str_replace("][", "", $campo["nombre_campo"]);

				self::$tipo_campo[$campo['tipo']]['id'] = $nombrecampo;
				self::$tipo_campo[$campo['tipo']]['label'] = !empty($campo['etiqueta']) ? $campo['etiqueta'] : "";
				//Verificar si hay un valor que mostrar en el campo
				if(!empty(self::$values_from_DB[$nombrecampo])){
					self::$tipo_campo[$campo['tipo']]['selected'] =  self::$values_from_DB[$nombrecampo];
				}
				//Si el campo es de tipo select
				//consultar los valores que iran en el option
				//segun la tabla catalogo del modulo.
				$catalogo = self::buscar_catalogo_campo($campo["id_campo"]);
				if(!empty($catalogo)){
					$options = array(
						"" => "Seleccione"
					);
					foreach($catalogo AS $catalogo){
						$options[$catalogo["id_cat"]] = $catalogo["etiqueta"];
					}
					self::$tipo_campo[$campo['tipo']]['options'] = $options;
				}

				break;
			case 'tagsinput':
				$nombre_campo = str_replace("][", "", $campo['nombre_campo']);
				//Verificar si hay un valor que mostrar en el campo
				if(!empty(self::$values_from_DB[$nombre_campo])){
					$options = array();
					$DataArray = self::$values_from_DB[$nombre_campo];
					foreach($DataArray AS $index => $data){
						$options[$data[$nombre_campo]] = $data[$nombre_campo];
					}
					self::$tipo_campo[$campo['tipo']]['options'] = $options;
				}
				self::$tipo_campo[$campo['tipo']]['id'] = $campo['nombre_campo'];
				self::$tipo_campo[$campo['tipo']]['label'] = !empty($campo['etiqueta']) ? $campo['etiqueta'] : "";
				break;
			case 'relate':
				$nombrecampo = str_replace("][", "", $campo["nombre_campo"]);


				self::$tipo_campo[$campo['tipo']]['id'] = $nombrecampo;
				self::$tipo_campo[$campo['tipo']]['label'] = !empty($campo['etiqueta']) ? $campo['etiqueta'] : "";

				//Verificar si hay un valor que mostrar en el campo
				if(!empty(self::$values_from_DB[$nombrecampo])){
					self::$tipo_campo[$campo['tipo']]['selected'] =  self::$values_from_DB[$nombrecampo];

					//Remover atributo disabled
					unset(self::$tipo_campo[$campo['tipo']]['disabled']);
				}

				//Si el campo es de tipo select
				//consultar los valores que iran en el option
				//segun la tabla catalogo del modulo.
				$catalogo = self::buscar_relacion_campo($campo["tabla_relacional"]);
				if(!empty($catalogo)){
					$options = array(
						"" => "Seleccione"
					);
					foreach($catalogo AS $catalogo){
						if(isset($catalogo["uuid"])){
                        	$options[$catalogo["uuid"]] = $catalogo["nombre"];
						}else{
                        	$options[$catalogo["id"]] = $catalogo["nombre"];
						}
					}
  					self::$tipo_campo[$campo['tipo']]['options'] = $options;
				}
				break;
			case 'select-multiple':

 					$nombrecampo = str_replace("][", "", $campo["nombre_campo"]);


					self::$tipo_campo[$campo['tipo']]['id'] = $nombrecampo;
					self::$tipo_campo[$campo['tipo']]['label'] = !empty($campo['etiqueta']) ? $campo['etiqueta'] : "";

					//Verificar si hay un valor que mostrar en el campo
					if(!empty(self::$values_from_DB[$nombrecampo])){
						self::$tipo_campo[$campo['tipo']]['selected'] =  self::$values_from_DB[$nombrecampo];

						//Remover atributo disabled
						unset(self::$tipo_campo[$campo['tipo']]['disabled']);
					}

					//Si el campo es de tipo select
					//consultar los valores que iran en el option
					//segun la tabla catalogo del modulo.
					$catalogo = self::buscar_relacion_campo($campo["tabla_relacional"]);
 					if(!empty($catalogo)){
						$options = array(
								"" => "Seleccione"
						);
						foreach($catalogo AS $catalogo){
							if(isset($catalogo["uuid"])){
								$options[$catalogo["uuid"]] = $catalogo["nombre"];
							}else{
								$options[$catalogo["id"]] = $catalogo["nombre"];
							}
						}
						self::$tipo_campo[$campo['tipo']]['options'] = $options;


					}
			break;
			case 'relate-right-button' :
				self::$tipo_campo[$campo ['tipo']]['id'] = $campo['nombre_campo'];
				self::$tipo_campo[$campo ['tipo']]['label'] = ! empty($campo['etiqueta']) ? $campo['etiqueta'] : "";
				// Verificar si hay un valor que mostrar en el campo
				if (! empty( self::$values_from_DB[$campo['nombre_campo']] )) {
					self::$tipo_campo[$campo['tipo']]['selected'] = self::$values_from_DB [$campo['nombre_campo']];
				}
				// Si el campo es de tipo select
				// consultar los valores que iran en el option
				// segun la tabla catalogo del modulo.
				$catalogo = self::buscar_relacion_campo ( $campo ["tabla_relacional"] );
				if (! empty ( $catalogo )) {
					$options = array (
						"" => "Seleccione"
					);
					foreach ( $catalogo as $catalogo ) {
						$options[$catalogo["uuid"]] = $catalogo ["nombre"];
					}
					self::$tipo_campo[$campo ['tipo']]['options'] = $options;
				}
				break;
			case 'file':

				$nombrecampo = str_replace("][", "", $campo["nombre_campo"]);

				self::$tipo_campo[$campo['tipo']]['id'] = $nombrecampo;
				self::$tipo_campo[$campo['tipo']]['name'] = $nombrecampo;
				self::$tipo_campo[$campo['tipo']]['class'] = "btn btn-default btn-block";
				self::$tipo_campo[$campo['tipo']]['label'] = !empty($campo['etiqueta']) ? $campo['etiqueta'] : "";
				break;

			case 'file_imagen':

				$nombrecampo = str_replace("][", "", $campo["nombre_campo"]);

				self::$tipo_campo[$campo['tipo']]['id'] = $nombrecampo;
				self::$tipo_campo[$campo['tipo']]['url'] = (!empty(self::$values_from_DB['imagen_archivo']) ? self::$values_from_DB['imagen_archivo'] : "");
				self::$tipo_campo[$campo['tipo']]['label'] = !empty($campo['etiqueta']) ? $campo['etiqueta'] : "";
				break;

			case ($campo['tipo'] == "head_title" || $campo['tipo'] == "p-text"):

				self::$tipo_campo[$campo['tipo']]['label'] = !empty($campo['etiqueta']) ? $campo['etiqueta'] : "";
				break;

			case ($campo['tipo'] == "button" || $campo['tipo'] == "button-guardar" || $campo['tipo'] == "button-cancelar" || $campo['tipo'] == "button-label"):
				//si no tiene clase, asignarle la clase de default.
				if(empty(self::$tipo_campo[$campo['tipo']]['class'])){
					self::$tipo_campo[$campo['tipo']]['class'] = "btn btn-default btn-block";
				}

				self::$tipo_campo[$campo['tipo']]['label'] = !empty($campo['etiqueta']) ? $campo['etiqueta'] : "";
				self::$tipo_campo[$campo['tipo']]['content'] = ($campo['tipo'] == "button-label" ? "Seleccione" : $campo['etiqueta']);
				self::$tipo_campo[$campo['tipo']]['id'] = $campo['nombre_campo'];
				break;

			case 'textarea':

				//Verificar si hay un valor que mostrar en el campo
				if(!empty(self::$values_from_DB[$campo['nombre_campo']])){
					self::$tipo_campo[$campo['tipo']]['value'] =  self::$values_from_DB[$campo['nombre_campo']];
				}
				if(!empty(self::$tipo_campo[$campo['tipo']]['rows'])){
					self::$tipo_campo[$campo['tipo']]['rows'] = self::$tipo_campo[$campo['tipo']]['rows'];
				}
				if(!empty(self::$tipo_campo[$campo['tipo']]['cols'])){
					self::$tipo_campo[$campo['tipo']]['cols'] = self::$tipo_campo[$campo['tipo']]['cols'];
				}
				if(!empty(self::$tipo_campo[$campo['tipo']]['ng-model'])){
					self::$tipo_campo[$campo['tipo']]['ng-model'] = self::$tipo_campo[$campo['tipo']]['ng-model'];
				}
				self::$tipo_campo[$campo['tipo']]['label'] 	= !empty($campo['etiqueta']) ? $campo['etiqueta'] : "";
				break;

			case ($campo['tipo'] == 'select-checkbox-addon' || $campo['tipo'] == 'select-right-button-addon' || $campo['tipo'] == 'select-checkbox-button-addon'):

				self::$tipo_campo[$campo['tipo']]['id'] = $campo['nombre_campo'];
				self::$tipo_campo[$campo['tipo']]['label'] = !empty($campo['etiqueta']) ? $campo['etiqueta'] : "";
				self::$tipo_campo[$campo['tipo']]['options'] = array("" => "Seleccione");

				//Verificar si hay un valor que mostrar en el campo
				if(!empty(self::$values_from_DB[$campo['nombre_campo']])){
					self::$tipo_campo[$campo['tipo']]['selected'] =  self::$values_from_DB[$campo['nombre_campo']];
				}
        		// Si el campo es de tipo select
				// consultar los valores que iran en el option
				// segun la tabla catalogo del modulo.
				$catalogo = self::buscar_relacion_campo($campo["tabla_relacional"]);
				if (!empty($catalogo)){
					$options = array(
						"" => "Seleccione"
					);
					foreach ($catalogo as $catalogo) {
						if(isset($catalogo["uuid"])){
							$options[$catalogo["uuid"]] = $catalogo["nombre"];
						}else{
							$options[$catalogo["id"]] = $catalogo["nombre"];
						}

					}
					self::$tipo_campo[$campo['tipo']]['options'] = $options;
				}

				break;
			case 'groups-radio-button':
						self::$tipo_campo[$campo['tipo']]['name'] = $campo['nombre_campo'];
							$catalogo = self::buscar_relacion_campo($campo["tabla_relacional"]);
							if (! empty( self::$values_from_DB[$campo['nombre_campo']] )) {
								self::$tipo_campo [$campo ['tipo']] ['checked'] = self::$values_from_DB [$campo ['nombre_campo']];
							}else{
								self::$tipo_campo [$campo ['tipo']] ['checked']='';
							}
							$radios = array();
							$j = 1;
							foreach ($catalogo as $catalogo) {
								$radios[] = array('id' =>$campo['nombre_campo'].$j++,'label' =>$catalogo['nombre'], 'value'=> $catalogo['uuid'],'icono' => $catalogo['icono']);
							}
								self::$tipo_campo[$campo['tipo']]['groups-radio-button'] = $radios;
				break;
			case 'date-range-picker':

				self::$tipo_campo[$campo['tipo']]['id'] = $campo['nombre_campo'];
				self::$tipo_campo[$campo['tipo']]['label'] = !empty($campo['etiqueta']) ? $campo['etiqueta'] : "";
				self::$tipo_campo[$campo['tipo']]['name'] = $campo['nombre_campo'];
				break;

			default:
				//Si el campo es tipo email, validar formato de email
				if($campo['tipo'] == "email"){
					//self::$tipo_campo[$campo['tipo']]["data-rule-email"] = "true";
					self::$tipo_campo[$campo['tipo']]["class"] = self::$tipo_campo[$campo['tipo']]["class"] . " validEmailFormat";
					self::$tipo_campo[$campo['tipo']]["type"] = "text";
				}
				//Campo Tipo Input Text
				if(count(self::$values_from_DB) > 0 && $campo['tipo'] != 'password'){
					if(!empty(self::$values_from_DB[$campo['nombre_campo']])){
						self::$tipo_campo[$campo['tipo']]['value'] =  self::$values_from_DB[$campo['nombre_campo']];
					}
				}
				if(empty(self::$tipo_campo[$campo['tipo']]['ng-model']) && isset(self::$tipo_campo[$campo['tipo']]['ng-model'])){
					self::$tipo_campo[$campo['tipo']]['ng-model'] = !empty($campo['ng-model']) ? $campo['ng-model'] : "";
				}
				self::$tipo_campo[$campo['tipo']]['label'] = !empty($campo['etiqueta']) ? $campo['etiqueta'] : "";
				break;
		}
	}
	private static function armar_formulario_array()
	{
		/*echo "<pre>";
		print_r(self::$modulo_campos);
		echo "</pre>";
		die();*/

		$i=0;
		$field = array();
		foreach(self::$modulo_campos AS $campo)
		{
			if(!empty($campo['remoto'])){

				if(empty($campo['id_pestana'])){
					continue;
				}

				$field['pestana'][$campo['id_pestana']]['pestana_nombre'] = !empty($campo['pestana']) ? $campo['pestana'] : "";
				$field['pestana'][$campo['id_pestana']]['formularios'][$campo['id_formulario']]['formulario_nombre'] = !empty($campo['nombre_formulario']) ? $campo['nombre_formulario'] : "";
				$field['pestana'][$campo['id_pestana']]['formularios'][$campo['id_formulario']]['atributos'] = !empty($campo['atributos_formulario']) ? $campo['atributos_formulario'] : "";
				$field['pestana'][$campo['id_pestana']]['formularios'][$campo['id_formulario']]['paneles'][$campo['id_panel']]['panel_nombre'] = !empty($campo['panel']) ? $campo['panel'] : "";
				$field['pestana'][$campo['id_pestana']]['formularios'][$campo['id_formulario']]['remoto'] = !empty($campo['remoto']) ? $campo['remoto'] : "";
			}
			//Verificar si el conteo del array de campo es igual a 1
			//Si es igual a uno se trata de un array formado para tabla dinamicas.
			else if(count($campo) == 2)
			{
				if(empty($campo['tipo'])){
					continue;
				}

				foreach($campo AS $indice => $tabla_campos)
				{
					if($indice == "tipo"){
						continue;
					}
					foreach($tabla_campos AS $key => $tabla_campo){
						self::asignar_campo_atributos($tabla_campo);
						//Verificar si no existe el indice "pestana_nombre" y no es vacio
						if(empty($field['pestana'][$tabla_campo['id_pestana']]['pestana_nombre'])){
							//agregar nombre de pestana
							$field['pestana'][$tabla_campo['id_pestana']]['pestana_nombre'] = !empty($tabla_campo['pestana']) ? $tabla_campo['pestana'] : "";
						}
						//Verificar si no existe el indice "formulario_nombre" y no es vacio
						if(empty($field['pestana'][$tabla_campo['id_pestana']]['formularios'][$tabla_campo['id_formulario']]['formulario_nombre'])){
							//agregar nombre de formulario
							$field['pestana'][$tabla_campo['id_pestana']]['formularios'][$tabla_campo['id_formulario']]['formulario_nombre'] = !empty($campo['nombre_formulario']) ? $campo['nombre_formulario'] : "";
						}

						//Verificar si no existe el indice "atributos" de formulario y no es vacio
						if(empty($field['pestana'][$tabla_campo['id_pestana']]['formularios'][$tabla_campo['id_formulario']]['atributos_formulario'])){
							//agregar nombre de formulario
							$field['pestana'][$tabla_campo['id_pestana']]['formularios'][$tabla_campo['id_formulario']]['atributos'] = !empty($campo['atributos_formulario']) ? $campo['atributos_formulario'] : "";
						}
						$field['pestana'][$tabla_campo['id_pestana']]['formularios'][$tabla_campo['id_formulario']]['paneles'][$tabla_campo['id_panel']]['campos'][$i][$tabla_campo['contenedor']][] = self::$tipo_campo[$tabla_campo['tipo']];
					}
				}
			}
			else
			{
				if(empty($campo['tipo'])){
					continue;
				}

				self::asignar_campo_atributos($campo);
				if(empty($campo['id_pestana'])){
					continue;
				}
				//self::$tipo_campo[$campo['tipo']]['label'] = !empty($campo['etiqueta']) ? $campo['etiqueta'] : "";
				$field['pestana'][$campo['id_pestana']]['formularios'][$campo['id_formulario']]['paneles'][$campo['id_panel']]['campos'][$i] = self::$tipo_campo[$campo['tipo']];
				$field['pestana'][$campo['id_pestana']]['pestana_nombre'] = !empty($campo['pestana']) ? $campo['pestana'] : "";
				$field['pestana'][$campo['id_pestana']]['formularios'][$campo['id_formulario']]['formulario_nombre'] = !empty($campo['nombre_formulario']) ? $campo['nombre_formulario'] : "";
				$field['pestana'][$campo['id_pestana']]['formularios'][$campo['id_formulario']]['atributos'] = !empty($campo['atributos_formulario']) ? $campo['atributos_formulario'] : "";
				$field['pestana'][$campo['id_pestana']]['formularios'][$campo['id_formulario']]['paneles'][$campo['id_panel']]['panel_nombre'] = !empty($campo['panel']) ? $campo['panel'] : "";
			}
			$i++;
		}

		return $field;
	}
	/**
	 * Esta funcion arma el HTML de una tabla dinamica.
	 * Tambien hace la funcion de tabla sumativa, para
	 * mostrar totales en el footer de la tabla.
	 *
	 * @param $campos
	 * @return string
	 */
	private static function armar_tabla_dinamica($campos=array())
	{
 		if(empty($campos)){
			return false;
		}

		$tipo_tabla = !empty($campos["tabla-dinamica"]) ? "tabla-dinamica" : "tabla-dinamica-sumativa";
		$campos = !empty($campos["tabla-dinamica"]) ? $campos["tabla-dinamica"] : $campos["tabla-dinamica-sumativa"];
		$tabla = '<div class="table-responsive"><table id="'. $campos[0]["agrupador"] .'Table" class="table table-noline tabla-dinamica">';

		/**
		 * Armar los titulos <THEAD>
		 */
		$tabla .= '<thead><tr>';
		$botones = false;

		//Calcular el width de las columnas, segun cantidad de campos
		//no tomar en cuenta campos hidden
		$total_campos = array_filter($campos, function($campo) {
		    return $campo["type"] != "hidden";
		});

		$colwidth =  95/count($total_campos);

		foreach ($campos AS $campo){
			if(empty($campo["type"])){
				continue;
			}
			if($campo["type"] == "button")
			{
				if($botones == false){
					$tabla .= '<th colspan="3" width="5%">&nbsp;</th>';
					$botones = true;
				}
			}else{
				//Si campo es hidden
				if($campo['type'] == "hidden"){
					continue;
				}

				$requerido = !empty($campo["data-rule-required"]) ? '<span class="required">*</span>' : '';
				$fieldhide = !empty($campo["data-hide-field"]) ? 'hide' : '';
				$fieldclass = !empty($campo["id"]) ? $campo["id"] : '';

				$tabla .= '<th width="'. $colwidth .'%" class="'. $fieldclass .' '. $fieldhide .'">'. $campo["label"] .' '. $requerido .'</th>';
			}
		}
		$tabla .= '</tr></thead>';
		/**
		 * Armar las filas que van dentro de <TBODY>
		 */
		$tabla .= '<tbody>';

		$fromDB = !empty(self::$values_from_DB[$campos[0]["agrupador"]]) ? self::$values_from_DB[$campos[0]["agrupador"]] : array();

		/*echo "<pre>";
		print_r($fromDB);
		echo "</pre>";*/

		//Verificar si hay un valor que mostrar de la DB
		if(!empty($fromDB) && count($fromDB) > 0)
		{
			$j=0;
			$button_load = false;
			foreach(self::$values_from_DB[$campos[0]["agrupador"]] AS $data)
			{
				/**
				 * Armar las filas que van dentro de <TBODY>
				 */
				$tabla .= '<tr id="'. $campos[0]["agrupador"] . $j .'">';
				foreach ($campos AS $campo){
					$nombre_campo = !empty($campo["nombre_campo"]) ? str_replace("][", "", $campo["nombre_campo"]) : "";

					$fieldhide = !empty($campo["data-hide-field"]) ? 'hide' : '';
					$fieldclass = !empty($campo["id"]) ? $campo["id"].$j : '';

					//Verificar si hay valor que mostrar para este campo
					$valor = !empty($data[$nombre_campo]) ? $data[$nombre_campo] : "";
					if($campo["type"] == "text"){
  						$campo['name'] = str_replace("[0]", '['.$j.']', $campo['name']);
						$campo["value"] = $valor;
						$campo["id"] = $campo["id"].$j;
						unset($campo["label"]);
						unset($campo["agrupador"]);
						unset($campo["data-columns"]);
						unset($campo["nombre_campo"]);
						$tabla .= '<td class="'. $fieldclass .' '. $fieldhide .'">'. form_input($campo) .'</td>';
					}
					if($campo["type"] == "hidden"){
						$campo['name'] = str_replace("[0]", '['.$j.']', $campo['name']);
						$campo["value"] = $valor;
						unset($campo["label"]);
						unset($campo["agrupador"]);
						unset($campo["data-columns"]);
						unset($campo["nombre_campo"]);
						unset($campo["id"]);

						$tabla .= '<td class="hide">'. form_input($campo) .'</td>';
					}
					if($campo["type"] == "fecha"){
						$campo['name'] = str_replace("[0]", '['.$j.']', $campo['name']);
						$campo["value"] = $valor;
						$campo["id"] = $campo["id"].$j;
						unset($campo["label"]);
						unset($campo["agrupador"]);
						unset($campo["data-columns"]);
						unset($campo["nombre_campo"]);
						$tabla .= '<td class="'. $fieldclass .' '. $fieldhide .'">'. form_input($campo) .'</td>';
					}
					if($campo["type"] == "input-right-addon"){
						$campo['name'] = str_replace("[0]", '['.$j.']', $campo['name']);
						$campo["value"] = $valor;
						$campo["id"] = $campo["id"].$j;
						unset($campo["label"]);
						unset($campo["agrupador"]);
						unset($campo["data-columns"]);
						unset($campo["nombre_campo"]);

						$button_text_icon = !empty($campo['data-addon-icon']) && $campo['data-addon-icon'] != "" ? '<i class="fa '. $campo['data-addon-icon'] .'"></i>' : (!empty($campo['data-addon-text']) ? $campo['data-addon-text'] : "");

						$tabla .= '<td class="'. $fieldclass .' '. $fieldhide .'">'.'<div class="input-group">'.
								form_input($campo).'<span class="input-group-addon">'. $button_text_icon .'</span>'.
							'</div></td>';
					}
 					if($campo["type"] == "input-left-addon"){
						$campo['name'] = str_replace("[0]", '['.$j.']', $campo['name']);
						$campo["value"] = $valor;
						$campo["id"] = $campo["id"].$j;
						$campo["type"] = "text";

						unset($campo["label"]);
						unset($campo["agrupador"]);
						unset($campo["data-columns"]);
						unset($campo["nombre_campo"]);
						$button_text_icon = !empty($campo['data-addon-icon']) && $campo['data-addon-icon'] != "" ? '<i class="fa '. $campo['data-addon-icon'] .'"></i>' : (!empty($campo['data-addon-text']) ? $campo['data-addon-text'] : "");
						$tabla .= '<td class="'. $fieldhide .' '. $fieldclass .'">'.'<div class="input-group">'.
								'<span class="input-group-addon">'. $button_text_icon .'</span>'.form_input($campo).'</div></td>';
					}
					else if($campo['type'] == 'input-daterange')
					{
						$button_text_icon = !empty($campo['data-addon-icon']) && $campo['data-addon-icon'] != "" ? '<i class="fa '. $campo['data-addon-icon'] .'"></i>' : (!empty($campo['data-addon-text']) ? $campo['data-addon-text'] : "");

						$input_1_id = $campo["id"]."_desde$j";
						$input_1_name = $campo['agrupador'] .'['.$j.']['.$campo['nombre_campo'].'_desde]';
						$input_1_campo = $campo['nombre_campo'].'_desde';
						$input_1_value = !empty($data[$input_1_campo]) ? date("d/m/Y", strtotime($data[$input_1_campo])) : "";

						$input_2_id = $campo["id"]."_hasta$j";
						$input_2_name = $campo['agrupador'] .'['.$j.']['.$campo['nombre_campo'].'_hasta]';
						$input_2_campo = $campo['nombre_campo'].'_hasta';
						$input_2_value = !empty($data[$input_2_campo]) ? date("d/m/Y", strtotime($data[$input_2_campo])) : "";

						unset($campo['label']);
						unset($campo['data-addon-icon']);
						unset($campo['data-addon-text']);
						unset($campo['data-columns']);
						unset($campo["nombre_campo"]);

						//Fecha Desde
						$campo["name"] = $input_1_name;
						$campo["id"] = $input_1_id;
						$campo["value"] = $input_1_value;

						$tabla .= '<td class="'. $fieldclass .' '. $fieldhide .'">';
						$tabla .= '<div class="input-group">
							    '. form_input($campo);
						$tabla .= '<span class="input-group-addon">'. $button_text_icon .'</span>';

						//Fecha Hasta
						$campo["name"] = $input_2_name;
						$campo["id"] = $input_2_id;
						$campo["value"] = $input_2_value;

						$tabla .= form_input($campo) .'
							    </div>';
						$tabla .= '</td>';
					}
					else if($campo['type'] == 'select-right-button-addon')
					{
						$campo['name'] = str_replace("[0]", '['.$j.']', $campo['name']);
						preg_match('/\[(.*?)\]/i', $campo['name'], $matches);
						$id = !empty($matches) ? $matches[1] : "";
						$id = str_replace("]", "", $id);
						$button_text_icon = !empty($campo['data-button-icon']) && $campo['data-button-icon'] != "" ? '<i class="fa '. $campo['data-button-icon'] .'"></i>' : $campo['data-button-text'];
						$selected = !empty($valor) ? $valor : "";

						$attrs = 'id="'. $campo["id"] .$j.'" class="'. $campo['class'] .' chosen-select" data-placeholder="Seleccione" '. (!empty($campo['multiple']) ? 'multiple="multiple" ' : "") . (!empty($campo['disabled']) ? 'disabled="disabled"' : "");

						unset($campo['data-button-icon']);
						unset($campo['data-button-text']);
						unset($campo['label']);
						unset($campo['data-addon-icon']);
						unset($campo['data-addon-text']);
						unset($campo['data-columns']);
						unset($campo["nombre_campo"]);

						$tabla .= '<td class="'. $fieldclass .' '. $fieldhide .'">';
						$tabla .= '<div class="input-group">
							      '. form_dropdown($campo['name'], $campo['options'], $selected, $attrs) .'
								  <span class="input-group-btn">
										<button id="'. $id .'Btn" class="btn btn-default" type="button">'. $button_text_icon .'</button>
									</span>
							    </div>';
						$tabla .= '</td>';
					}
					else if($campo['type'] == 'select-checkbox-addon')
					{
						$campo['name'] = str_replace("[0]", '['.$j.']', $campo['name']);
						preg_match('/\[(.*?)\]/i', $campo['name'], $matches);
						$id = !empty($matches) ? $matches[1] : "";
						$id = str_replace("]", "", $id);
						$label = !empty($campo['label']) ? $campo['label']. " ". $requerido : "";
						$attrs = 'id="'. $campo["id"] .$j.'" class="'. $campo['class'] .' chosen-select" data-placeholder="Seleccione" '. (!empty($campo['multiple']) ? 'multiple="multiple" ' : "")  . (!empty($campo['disabled']) ? 'disabled="disabled"' : "");
						$selected = !empty($valor) ? $valor : "";
						unset($campo['label']);
						unset($campo['data-button-icon']);
						unset($campo['data-button-text']);
						unset($campo['label']);
						unset($campo['data-addon-icon']);
						unset($campo['data-addon-text']);
						unset($campo['data-columns']);
						unset($campo["nombre_campo"]);
						$tabla .= '<td class="'. $fieldclass .' '. $fieldhide .'">';
						$tabla .= '<div class="input-group">
								  <span class="input-group-addon">
							      	<input type="checkbox" id="'. $id .'Check">
							      </span>';
						//Vertificar si es un select multiple
						if(!empty($campo['multiple'])){
							$tabla .= form_multiselect($campo['name'], $campo['options'], $selected, $attrs);
						}else{
							$tabla .= form_dropdown($campo['name'], $campo['options'], $selected, $attrs);
						}
						$tabla .= '</div>';
						$tabla .= '</td>';
					}
					else if($campo['type'] == 'select')
					{
						$campo['name'] = str_replace("[0]", '['.$j.']', $campo['name']);
						$attrs = 'id="'. $campo["id"] .$j.'" class="'. $campo['class'] .'"' . (!empty($campo["disabled"]) ? ' disabled="disabled"' : '');
						$selected = !empty($valor) ? $valor : "";

                        if(!isset($campo['options'])){
                        	$campo["options"] = array();
                        }

  						$tabla .= '<td class="'. $fieldclass .' '. $fieldhide .'">'. form_dropdown($campo['name'], $campo['options'], $selected, $attrs) .'</td>';
 						$selected = '';
					}
					else if($campo['type'] == 'radio')
					{

						$div = '';
						if(!empty($campo['values']))
						{
							foreach($campo['values'] AS $id_cat => $valor)
							{
								if( empty($valor)){
									continue;
								}
                                $campo['name'] = str_replace("[0]", '['.$j.']', $campo['name']);
                                $campo["id"] = $campo["id"].$j;
								$campo["value"] = $id_cat;
								unset($campo['values']);
								$div .= '<label class="radio" style="margin-top:0px;margin-bottom:0px;">';
								$div .= form_radio($campo)." ". $valor;
								$div .= '</label>';
							}
						}
						$tabla .= '<td class="'. $fieldclass .' '. $fieldhide .'">'. $div .'</td>';
					}
 					else if($campo['type'] == 'relate')
					{
                        $campo['name'] = str_replace("[0]", '['.$j.']', $campo['name']);
						$attrs = 'class="'. $campo['class'] .'" id="'.$campo["id"].'0"';
						$selected = !empty($valor) ? $valor : "";
						$tabla .= '<td class="'. $fieldclass .' '. $fieldhide .'">';
						$tabla .= form_dropdown($campo['name'], $campo['options'], $selected, $attrs);
						$tabla .= '</td>';
					}
 					else if($campo["type"] == "button"){
						//Si el boton es "Agregar", cargarlo solo UNA vez
						if(!preg_match("/eliminar/i", $campo["class"]) && $button_load == true){
							continue;
						}
						if(!preg_match("/eliminar/i", $campo["class"]) && $button_load == false){
							$button_load = true;
							$input_hidden = '<input type="hidden" name="" />';
						}
						$campo["content"] = html_entity_decode($campo["content"]);
						$campo["data-index"] = $j;
						//Borrar atributos inecesarios
						unset($campo["name"]);
						unset($campo["data-columns"]);
						unset($campo["id"]);
						//Recorrer el arreglo para encontrar el
						//campo uuid_ y asignarlo como atributo
						foreach ($data AS $indice => $valor){
							if(preg_match("/id_/i", $indice)){
								$campo["data-id"] = $valor;
								break;
							}
						}
						$tabla .= '<td>'. form_button($campo) .'</td>';
					}
                    else if($campo['type'] == 'file')
                    {
                        $campo['name'] = str_replace("[0]", '['.$j.']', $campo['name']);
                        $campo["value"] = $valor;
                        $campo["id"] = $campo["id"].$j;
                        unset($campo["label"]);
                        unset($campo["agrupador"]);
                        unset($campo["data-columns"]);
                        unset($campo["nombre_campo"]);
                        $tabla .= '<td class="'. $fieldclass .' '. $fieldhide .'">'. form_input($campo) .'</td>';
                    }
				}
				$j++;
			}
		}
		else
		{
			/**
			 * Armar las filas que van dentro de <TBODY>
			 */
			$j=0;
			$tabla .= '<tr id="'. $campos[0]["agrupador"] .'0">';
			foreach ($campos AS $campo){

				$fieldhide = !empty($campo["data-hide-field"]) ? 'hide' : '';
				$fieldclass = !empty($campo["id"]) ? $campo["id"]."0" : '';
				$options = array("" => "Seleccione");

				if(!empty($campo['options'])){
					$options = $campo['options'];
				}
				if(empty($campo["type"])){
					return false;
				}
				if($campo["type"] == "text"){
					$campo["id"] = $campo["id"]."0";
					$campo["value"] = "";
					unset($campo["label"]);
					unset($campo["agrupador"]);
					unset($campo["data-columns"]);
					unset($campo["nombre_campo"]);
					$tabla .= '<td class="'. $fieldclass .' '. $fieldhide .'">'. form_input($campo) .'</td>';
				}
				if($campo["type"] == "fecha"){
					$campo["id"] = $campo["id"]."0";
					unset($campo["label"]);
					unset($campo["agrupador"]);
					unset($campo["data-columns"]);
					unset($campo["nombre_campo"]);
					$tabla .= '<td class="'. $fieldclass .' '. $fieldhide .'">'. form_input($campo) .'</td>';
				}
				else if($campo['type'] == 'select')
				{
					$attrs = 'id="'. $campo["id"] .'0" class="'. $campo['class'] .'"' . (!empty($campo["disabled"]) ? ' disabled="disabled"' : '');
					$selected = "";

					unset($campo["label"]);
					unset($campo["agrupador"]);
					unset($campo["data-columns"]);
					unset($campo["nombre_campo"]);
					$tabla .= '<td class="'. $fieldclass .' '. $fieldhide .'">'. form_dropdown($campo['name'], $options, $selected, $attrs) .'</td>';
				}
				else if($campo["type"] == "fecha"){
					 $campo["id"] = $campo["id"]."0";
					 unset($campo["label"]);
					 unset($campo["agrupador"]);
					 unset($campo["data-columns"]);
					 unset($campo["nombre_campo"]);
					 $tabla .= '<td class="'. $fieldclass .' '. $fieldhide .'">'. form_input($campo) .'</td>';
				}
				else if($campo['type'] == 'input-left-addon')
				{
					$campo["id"] = $campo["id"]."0";
					$button_text_icon = !empty($campo['data-addon-icon']) && $campo['data-addon-icon'] != "" ? '<i class="fa '. $campo['data-addon-icon'] .'"></i>' : $campo['data-addon-text'];
					$campo['type'] = 'text';
					unset($campo['label']);
					unset($campo['data-addon-icon']);
					unset($campo['data-addon-text']);
					unset($campo['data-columns']);
					unset($campo["nombre_campo"]);
					$tabla .= '<td class="'. $fieldclass .' '. $fieldhide .'">';
					$tabla .= '<div class="input-group">
								<span class="input-group-addon">'. $button_text_icon .'</span>
							      '. form_input($campo) .'
							    </div>';
					$tabla .= '</td>';
				}
				else if($campo['type'] == 'input-right-addon')
				{
					$campo["id"] = $campo["id"]."0";
					$button_text_icon = !empty($campo['data-addon-icon']) && $campo['data-addon-icon'] != "" ? '<i class="fa '. $campo['data-addon-icon'] .'"></i>' : (!empty($campo['data-addon-text']) ? $campo['data-addon-text'] : "");
					unset($campo['label']);
					unset($campo['data-addon-icon']);
					unset($campo['data-addon-text']);
					unset($campo['data-columns']);
					unset($campo["nombre_campo"]);
					$tabla .= '<td class="'. $fieldclass .' '. $fieldhide .'">';
					$tabla .= '<div class="input-group">
							      '. form_input($campo) .'
								  <span class="input-group-addon">'. $button_text_icon .'</span>
							    </div>';
					$tabla .= '</td>';
				}
				else if($campo['type'] == 'input-daterange')
				{
					$button_text_icon = !empty($campo['data-addon-icon']) && $campo['data-addon-icon'] != "" ? '<i class="fa '. $campo['data-addon-icon'] .'"></i>' : (!empty($campo['data-addon-text']) ? $campo['data-addon-text'] : "");

					$input_1_id = $campo["id"]."_desde0";
					$input_1_name = $campo['agrupador'] .'[0]['.$campo['nombre_campo'].'_desde]';

					$input_2_id = $campo["id"]."_hasta0";
					$input_2_name = $campo['agrupador'] .'[0]['.$campo['nombre_campo'].'_hasta]';

					unset($campo['label']);
					unset($campo['data-addon-icon']);
					unset($campo['data-addon-text']);
					unset($campo['data-columns']);
					unset($campo["nombre_campo"]);

					//Fecha Desde
					$campo["name"] = $input_1_name;
					$campo["id"] = $input_1_id;

					$tabla .= '<td class="'. $fieldclass .' '. $fieldhide .'">';
					$tabla .= '<div class="input-group">
							    '. form_input($campo);
							$tabla .= '<span class="input-group-addon">'. $button_text_icon .'</span>';

							//Fecha Hasta
							$campo["name"] = $input_2_name;
							$campo["id"] = $input_2_id;

						$tabla .= form_input($campo) .'
							    </div>';
					$tabla .= '</td>';
				}
				else if($campo['type'] == 'select-right-button-addon')
				{
					$attrs = 'class="'. $campo['class'] .' chosen-select" data-placeholder="Seleccione" '. (!empty($campo['multiple']) ? 'multiple="multiple" ' : "") . (!empty($campo['disabled']) ? 'disabled="disabled"' : "");
					preg_match('/\[(.*?)\]/i', $campo['name'], $matches);
					$id = !empty($matches) ? $matches[1] : "";
					$id = str_replace("]", "", $id);
					$button_text_icon = !empty($campo['data-button-icon']) && $campo['data-button-icon'] != "" ? '<i class="fa '. $campo['data-button-icon'] .'"></i>' : $campo['data-button-text'];
					$selected = !empty($campo['selected']) ? $campo['selected'] : "";
					unset($campo['data-button-icon']);
					unset($campo['data-button-text']);
					unset($campo['label']);
					unset($campo['data-addon-icon']);
					unset($campo['data-addon-text']);
					unset($campo['data-columns']);
					unset($campo["nombre_campo"]);
					$tabla .= '<td class="'. $fieldclass .' '. $fieldhide .'">';
					$tabla .= '<div class="input-group">
							      '. form_dropdown($campo['name'], $campo['options'], $selected, $attrs) .'
								  <span class="input-group-btn">
										<button id="'. $id .'Btn" class="btn btn-default" type="button">'. $button_text_icon .'</button>
									</span>
							    </div>';
					$tabla .= '</td>';
				}

				else if($campo['type'] == 'select-checkbox-addon')
				{
					$attrs = 'class="'. $campo['class'] .' chosen-select" data-placeholder="Seleccione" '. (!empty($campo['multiple']) ? 'multiple="multiple" ' : ""). (!empty($campo['disabled']) ? 'disabled="disabled"' : "");
					$selected = !empty($campo['selected']) ? $campo['selected'] : "";

					preg_match('/\[(.*?)\]/i', $campo['name'], $matches);
					$id = !empty($matches) ? $matches[1] : "";
					$id = str_replace("]", "", $id);
					$label = !empty($campo['label']) ? $campo['label']. " ". $requerido : "";

					//Eliminar atributos innecesarios
					unset($campo['label']);
					unset($campo['data-button-icon']);
					unset($campo['data-button-text']);
					unset($campo['label']);
					unset($campo['data-addon-icon']);
					unset($campo['data-addon-text']);
					unset($campo['data-columns']);
					unset($campo["nombre_campo"]);
					$tabla .= '<td class="'. $fieldclass .' '. $fieldhide .'">';
					$tabla .= '<div class="input-group">
								  <span class="input-group-addon">
							      	<input type="checkbox" id="'. $id .'Check">
							      </span>
								'. form_dropdown($campo['name'], $campo['options'], $selected, $attrs) .'
							    </div>';
					$tabla .= '</td>';
				}
				else if($campo['type'] == 'input-select')
				{
					$attrs = 'class="'. $campo['class'] .' chosen-select" data-placeholder="Seleccione" '. (!empty($campo['multiple']) ? 'multiple="multiple" ' : ""). (!empty($campo['disabled']) ? 'disabled="disabled"' : "");
					$selected = !empty($campo['selected']) ? $campo['selected'] : "";

					preg_match('/\[(.*?)\]/i', $campo['name'], $matches);
					$id = !empty($matches) ? $matches[1] : "";
					$id = str_replace("]", "", $id);
					$label = !empty($campo['label']) ? $campo['label']. " ". $requerido : "";

					//Eliminar atributos innecesarios
					unset($campo['label']);
					unset($campo['data-button-icon']);
					unset($campo['data-button-text']);
					unset($campo['label']);
					unset($campo['data-addon-icon']);
					unset($campo['data-addon-text']);
					unset($campo['data-columns']);
					unset($campo["nombre_campo"]);

					$tabla .= '<td class="'. $fieldclass .' '. $fieldhide .'">';
					$tabla .= '<div class="input-group form-inline">
							      	<input size="3"  type="text" id="'. $id .'input" class="form-control">
								'. form_dropdown($campo['name'], $campo['options'], $selected, $attrs) .'
							    </div>';
					$tabla .= '</td>';
				}
				else if($campo['type'] == 'relate')
				{
					$attrs = 'class="'. $campo['class'] .'" id="'.$campo["id"].'0"';
					$selected = !empty($campo['selected']) ? $campo['selected'] : "";
					$options = !empty($campo['options']) ? $campo['options'] : array();

					$tabla .= '<td class="'. $fieldclass .' '. $fieldhide .'">';
					$tabla .= form_dropdown($campo['name'], $options, $selected, $attrs);
					$tabla .= '</td>';
				}
				 else if($campo['type'] == 'radio')
				{
   					$div = '';
					if(!empty($campo['values']))
					{
						foreach($campo['values'] AS $id_cat => $valor)
						{
 							if( empty($valor)){
								continue;
							}

 							$campo["value"] = $id_cat;
  							unset($campo['values']);
  							$div .= '<label class="radio" style="margin-top:0px;margin-bottom:0px;">';
							$div .= form_radio($campo)." ". $valor;
							$div .= '</label>';
						}
 					}
 					$tabla .= '<td class="'. $fieldclass .' '. $fieldhide .'">'. $div .'</td>';
 				}
				else if($campo["type"] == "button"){
					$campo["content"] = html_entity_decode($campo["content"]);

					//Borrar atributos inecesarios
					unset($campo["name"]);
					unset($campo["data-columns"]);
					unset($campo["id"]);
					unset($campo["nombre_campo"]);
					$tabla .= '<td>'. form_button($campo) .'</td>';
				}
				else if($campo["type"] == "hidden"){
					$campo["value"] = "";
					$campo["class"] = "form-control";
					unset($campo["label"]);
					unset($campo["agrupador"]);
					unset($campo["data-columns"]);
					unset($campo["nombre_campo"]);

					$tabla .= '<td class="hide">'. form_input($campo) .'</td>';
				}
                else if($campo['type'] == 'file')
                {
                    $campo["id"] = $campo["id"]."0";
                    $campo["value"] = "";
                    unset($campo["label"]);
                    unset($campo["agrupador"]);
                    unset($campo["data-columns"]);
                    unset($campo["nombre_campo"]);
                    $tabla .= '<td class="'. $fieldclass .' '. $fieldhide .'">'. form_input($campo) .'</td>';
                }
				$j++;
			}
		}
		$tabla .= '</tr></tbody>';

		/**
		 * Si la tabla es sumativa y se necesitan
		 * mostrar valores en el footer de la tabla
		 * verificar los atributos de los campos.
		 */
		if(preg_match("/sumativa/i", $tipo_tabla)){
			$tabla .= '<tfoot><tr>';
			foreach ($campos AS $campo){

				if($campo["type"] == "button" || $campo["type"] == "hidden" || $campo["type"] == "submit"){
					continue;
				}

				if(!empty($campo["data-table-footer-sum-column"]) && $campo["data-table-footer-sum-column"] == true){

					$field = array(
						"readonly" => "readonly",
						"id" => "total". str_replace(" ", "", ucwords(str_replace("_", " ", $campo["id"]))),
						"class" => "form-control",
					);
					if($campo['type'] == 'input-left-addon')
					{
						$campo["id"] = $campo["id"]."0";
						$button_text_icon = !empty($campo['data-addon-icon']) && $campo['data-addon-icon'] != "" ? '<i class="fa '. $campo['data-addon-icon'] .'"></i>' : $campo['data-addon-text'];
						$tabla .= '<td>';
						$tabla .= '<div class="input-group">
							<span class="input-group-addon">'. $button_text_icon .'</span>
						      '. form_input($field) .'
						    </div>';
						$tabla .= '</td>';
					}
					else if($campo['type'] == 'input-right-addon')
					{
						$campo["id"] = $campo["id"]."0";
						$button_text_icon = !empty($campo['data-addon-icon']) && $campo['data-addon-icon'] != "" ? '<i class="fa '. $campo['data-addon-icon'] .'"></i>' : (!empty($campo['data-addon-text']) ? $campo['data-addon-text'] : "");
						$tabla .= '<td>';
						$tabla .= '<div class="input-group">
						      '. form_input($field) .'
							  <span class="input-group-addon">'. $button_text_icon .'</span>
						    </div>';
						$tabla .= '</td>';
					}
					else{
						$tabla .= '<td>'. form_input($field) .'</td>';
					}

				}else{

					$text_or_empty = (!empty($campo["data-table-footer-text"]) ? '<h4>'.$campo["data-table-footer-text"].'</h4>' : "&nbsp;");
					$tabla .= '<td>'. $text_or_empty .'</td>';
				}
			}
			$tabla .= '</tr></tfoot>';
		}
		$tabla .= '</table></div>';
		return $tabla;
	}
	private static function armar_html()
	{
		$campo = self::armar_formulario_array();
  		$html ='';

  		/* echo "<pre>";
  		print_r($campo);
  		echo "</pre>";
  		die();*/

		$html .= '<div class="tab-content">';
		if(count($campo['pestana']) > 1)
		{
			$html .='<div class="panel-heading white-bg">
				<span class="panel-title"></span>
				<ul class="nav nav-tabs nav-tabs-xs formTabs">';
			$i=0;
			foreach($campo['pestana'] AS $indice => $valores){
				$class = ($i==0)?'class="active"':'';
				$id_tab = (!empty($valores['pestana_nombre']) ? strtolower(str_replace(" ", "", $valores['pestana_nombre']))."-".$indice : "");
				$html .='<li '.$class.'><a data-toggle="tab" href="#'. $id_tab .'">'. (!empty($valores['pestana_nombre']) ? $valores['pestana_nombre'] : "&nbsp;") .'</a></li>';
				++$i;
			}
			$html .='</ul>
				</div>';
		}
		$i=0;
		foreach($campo['pestana'] AS $indice => $valores2)
		{
			$active = ($i==0)? 'active' : '';
			$id_tab = (!empty($valores2['pestana_nombre']) ? strtolower(str_replace(" ", "", $valores2['pestana_nombre'])) ."-". $indice : "");
			$html .='  <div id="'. $id_tab .'" class="tab-pane '.$active.'" >';

			foreach($valores2['formularios']  AS $form)
			{
				$atributos = !empty($form['atributos']) ? (array)json_decode($form['atributos']) : array();

				$formAttr = array(
					'method'       => 'POST',
					'id'           => (!empty($form['formulario_nombre']) ? $form['formulario_nombre'] : ""),
					'autocomplete' => 'off',
					'enctype'	   => 'multipart/form-data'
				);

				//Verificar atributos para agregarlos a la etiqueta form
				if(!empty($atributos["ng-controller"])){
					$formAttr["ng-controller"] = $atributos["ng-controller"];
				}
				if(!empty($atributos["id"])){
					$formAttr["id"] = $atributos["id"];
				}
				if(!empty($atributos["class"])){
					$formAttr["class"] = $atributos["class"];
				}
				if(!empty($atributos["method"])){
					$formAttr["method"] = $atributos["method"];
				}
				if(!empty($atributos["flow-init"])){
					$formAttr["flow-init"] = $atributos["flow-init"];
				}
				if(!empty($atributos["flow-file-added"])){
					$formAttr["flow-file-added"] = $atributos["flow-file-added"];
				}

				$html .= form_open(base_url(uri_string()), $formAttr);
				$m=0;


				foreach($form['paneles']  as $panel_valores)
				{
                    $block_panel    = 'style="display: block; border:0px"';
                    $fa_chevron     = "fa-chevron-up";

					//El primer panel mostrarlo abierto, los siguiente mostrarlos cerrados
					if( $m > 0){
						$block_panel    = 'style="display: none; border:0px"';
                        $fa_chevron     = "fa-chevron-down";
					}
					$html .= '<div class="ibox">';

					$panel_nombre = !empty($panel_valores['panel_nombre']) && $panel_valores['panel_nombre'] != "" ? $panel_valores['panel_nombre'] : "&nbsp;";

					if($panel_nombre != "&nbsp;"){
						$html .= '<div class="ibox-title border-bottom">
							<h5>'.$panel_nombre .'</h5>
							<div class="ibox-tools">
								<a class="collapse-link"><i class="fa '.$fa_chevron.'"></i></a>
							</div>
						</div>';
					}

					$html .= '<div class="ibox-content m-b-sm" '. $block_panel .'>
								<div class="row">';

						if(!empty($form['remoto'])){

							//-------------------------------------
							// Si tiene valor este campo
							// Cargar formulario remoto.
							//-------------------------------------
							$html .= file_get_contents(self::$module_view_path . $form['remoto'].".php");
							continue;
						}

						$initial = 0;
						foreach($panel_valores['campos'] AS $index => $campo)
						{
							/*
							 * Bootstrap Predefined grid classes.
							 */
                            $columnas = !empty($campo["data-columns"]) ? $campo["data-columns"] : "";
							unset($campo['data-columns']);

							$requerido = !empty($campo['data-rule-required']) ? '<span required>*</span>' : "";
							$label = !empty($campo['label']) ? $campo['label']. " ". $requerido : "";
							unset($campo['label']);

							//Ocultar el campo si tiene el atributo "data-hide-field"
							$hide_field = !empty($campo['data-hide-field']) && $campo['data-hide-field'] != "" ? 'hide' : "";
							unset($campo['data-hide-field']);

							//Clase Identificadora para el campo
							$identificadorCampo = str_replace(" ", "", $label);
							$identificadorCampo = str_replace("é", "e", $identificadorCampo);

							/**
							 * Valores para campos checkbox/radio
							 */
							$valores = array();
							if(!empty($campo['values'])){
								$valores = $campo['values'];
								unset($campo['values']);
							}
							if(!empty($campo["tabla-dinamica"]) || !empty($campo["tabla-dinamica-sumativa"]))
							{
								$html .= '</div><div class="row"><div class="col-lg-12">';
								$html .= self::armar_tabla_dinamica($campo);
								$html .= '</div></div><div class="row">';
							}
							else if($campo['type'] == "head_title")
							{
								$html .= '</div><div class="row"><div class="col-lg-12">';
								$html .= '<h4 class="m-b-xs">'. $label .'</h4><div class="hr-line-dashed m-t-xs"></div>';
								$html .= '</div></div><div class="row">';
							}
							else if($campo['type'] == "p-text")
							{
								$html .= '</div><div class="row"><div class="col-lg-12">';
								$html .= '<p '. (!empty($campo["class"]) ? 'class="'. $campo["class"] .'"' : '') . '>'. $label .'</p>';
								$html .= '</div></div><div class="row">';
							}
							else if($campo['type'] == 'file')
							{
								$html .= '<div  class="form-group '. $columnas .' '. $identificadorCampo .' '. $hide_field .'">';
								$html .= '<span class="success fileinput-button btn btn-default">';
								$html .= form_label($label);
								$html .= form_input($campo);
								$html .= '</span>';
								$html .='</div>';
							}
							else if($campo['type'] == 'file_imagen')
							{
								$campo['type'] = 'file';
								$html .= '<div class="form-group '. $columnas .' fileimage">
										  '. form_label($label);
								$img_circle = !empty($campo['data-img-circle']) ? "img-circle" : "";
								$img_path = !empty($campo['url']) ? $campo['url'] : "";
								// Si es pantalla de VER/EDITAR - Mostrar Imagen
								if(!empty($img_path)){
									$html .= '<div class="nailthumb thumb-image-90"><img alt="image" class="img-responsive '. $img_circle .'" src="'.base_url("public/uploads/". $img_path).'"></div>';
								}
								unset($campo['url']);
								unset($campo['data-img-circle']);
								$html .= form_input($campo).'</div>';
							}
							else if($campo['type'] == 'select')
							{
								$attrs = 'class="'. $campo['class'] .'" id="'. $campo['id'] .'" '. (!empty($campo["disabled"]) ? 'disabled="disabled"' : '') . (!empty($campo['data-rule-required']) ? 'data-rule-required="true"' : "") . (!empty($campo['ng-model']) ? 'ng-model="'. $campo['ng-model'] .'"' : "") . (!empty($campo['ng-change']) ? 'ng-change="'. $campo['ng-change'] .'"' : "") . (!empty($campo['data-placeholder']) ? ' data-placeholder="'. $campo['data-placeholder'] .'"' : "");

								$selected = !empty($campo['selected']) ? $campo['selected'] : "";
								$options = !empty($campo['options']) ? $campo['options'] : array();
								$html .= '<div class="form-group '. $columnas .' '. $identificadorCampo .' '. $hide_field .'">';
								$html .= form_label($label);

                               	//Vertificar si es un select multiple
                                $html .= !empty($campo['multiple']) ? form_multiselect($campo['name'], $options, $selected, $attrs) : form_dropdown($campo['name'], $options, $selected, $attrs);

								//Si existe el valor del campo que viene de DB $selected
								//pero las opciones estan vacias, guardar el valor
								//en un input oculto
								$attr = array(
									"type" => "hidden",
									"id" => "hidden_".$campo['id'],
									"value" => $selected
								);
								$html .= form_input($attr);
								$html .='</div>';
							}
							else if($campo['type'] == 'google_maps')
							{
 								$html .= '<div class="form-group '. $columnas .' '. $identificadorCampo .' '. $hide_field .'">';
								$html .= '<div id="google_map" class="google_map" style="width: 550px; height: 400px;"></div>';
								$html .= '<input type="hidden" name="google_map[address]" class="form-control" id="us3-address"/>';
								$html .= '<input type="hidden" name="google_map[radius]"  class="form-control" id="us3-radius"/>';
								$html .= '<input type="hidden" name="google_map[latitud]" class="form-control" style="width: 110px" id="us3-lat"/>';
								$html .= '<input type="hidden" name="google_map[longitud]" class="form-control" style="width: 110px" id="us3-lon"/>';
								$html .='</div>';
							}
							else if($campo['type'] == 'tagsinput')
							{
								$attr = 'class="'. $campo['class'] .'" multiple="'. $campo['multiple'] .'" data-role="'. $campo['data-role'] .'"';
								$options = !empty($campo['options']) ? $campo['options'] : array();
								$html .= '<div class="form-group '. $columnas .' '. $identificadorCampo .'">';
								$html .= form_label($label);
								$html .= form_dropdown($campo['name'], $options, '', $attr);
								$html .='</div>';
							}
							else if($campo['type'] == 'relate')
							{
								$attrs = 'class="'. $campo['class'] .'" '.(!empty($campo['id']) ? ' id="'.$campo['id'].'"' : "") . (!empty($campo['data-rule-required']) ? ' data-rule-required="true"' : "") . (!empty($campo['disabled']) ? ' disabled="disabled"' : "") . (!empty($campo['ng-model']) ? ' ng-model="'. $campo['ng-model'] .'"' : "") . (!empty($campo['ng-change']) ? ' ng-change="'. $campo['ng-change'] .'"' : "") . (!empty($campo['data-placeholder']) ? ' data-placeholder="'. $campo['data-placeholder'] .'"' : "");
								$selected = !empty($campo['selected']) ? $campo['selected'] : "";
								$options = !empty($campo['options']) ? $campo['options'] : array();
								$html .= '<div class="form-group '. $columnas .' '. $hide_field .'">';
								$html .= form_label($label);

                                if(!empty($campo['multiple'])){
                                	$html .= form_multiselect($campo["name"], $options, $selected, $attrs);
                                }else{
                                	$html .= form_dropdown($campo['name'], $options, $selected, $attrs);
                                }

								//Si existe el valor del campo que viene de DB $selected
								//pero las opciones estan vacias, guardar el valor
								//en un input oculto
								$attr = array(
									"type" => "hidden",
									"id" => "hidden_".$campo['id'],
									"value" => $selected
								);
								$html .= form_input($attr);
								$html .='</div>';
							}
							else if($campo['type'] == 'groups-radio-button')
							{
									$attrs = 'class="'. $campo['class'] .'" '.(!empty($campo['id']) ? 'id="'.$campo['id'].'"' : "") . (!empty($campo['data-rule-required']) ? 'data-rule-required="true"' : "") . (!empty($campo['disabled']) ? 'disabled="disabled"' : "");

									$btn = '';
									$html .= '<div class="form-group '. $columnas .' '. $hide_field .'">';
									$html .= '<div class="btn-group" role="group" data-toggle="buttons">';
									$activo = "";
									$check ="";

									foreach ($campo['groups-radio-button'] as $radio) {
										$activo = $campo['checked'] == $radio['value'] ? 'active': '';
										$check = $campo['checked'] == $radio['value'] ? 'checked': '';
										$btn .='<label  class="btn btn-default '.$activo.'"><i class="fa '.$radio['icono'].'"></i>';
					                    $btn .='<input type="radio" id="'.$radio['id'].'" name="campo['.$campo['name'].']" value="'.$radio['value'].'" '.$check.' /> '. $radio['label'];
					                    $btn .='</label>';
									}
									$html.= $btn;
									$html .='</div>';
									$html .='</div>';
							}
				 			else if ($campo ['type'] == 'relate-right-button')
				 			{
								$attrs = 'class="'. $campo['class'] .'" '.(!empty($campo['id']) ? ' id="'.$campo['id'].'"' : "") . (!empty($campo['data-rule-required']) ? ' data-rule-required="true"' : "") . (!empty($campo['disabled']) ? ' disabled="disabled"' : "") . (!empty($campo['ng-model']) ? ' ng-model="'. $campo['ng-model'] .'"' : "") . (!empty($campo['ng-change']) ? ' ng-change="'. $campo['ng-change'] .'"' : "") . (!empty($campo['data-placeholder']) ? ' data-placeholder="'. $campo['data-placeholder'] .'"' : "");
								preg_match ( '/\[(.*?)\]/i', $campo ['name'], $matches );
								$id = ! empty ( $matches ) ? $matches [1] : "";
								$id = str_replace ( "]", "", $id );
								$button_text_icon = ! empty ( $campo ['data-button-icon'] ) && $campo ['data-button-icon'] != "" ? '<i class="fa ' . $campo ['data-button-icon'] . '"></i>' : $campo ['data-button-text'];
								$selected = ! empty ( $campo ['selected'] ) ? $campo ['selected'] : "";
								$options = !empty($campo['options']) ? $campo['options'] : array();
								// input-group
								$html .= '<div class="form-group ' . $columnas . ' '. $hide_field .'">';
								$html .= form_label ( $label );
								$html .= '<div class="input-group">';
								$html .= form_dropdown ( $campo ['name'], $options, $selected, $attrs );
								$html .= '<span class="input-group-btn">
											<button id="' . $id . 'Btn" class="btn btn-default" type="button">' . $button_text_icon . '</button>
										 </span>';
								$html .= '</div></div>';
							}
							else if($campo['type'] == 'checkbox')
							{
								$html .= '<div class="form-group '. $columnas .' '. $hide_field .'">';
								if(!empty($valores))
								{
									$campo_nombre_limpio = str_replace("campo[", "",$campo['name']);
									$campo_nombre_limpio = str_replace("][]", "",$campo_nombre_limpio);

 									foreach($valores AS $id_cat => $valor)
									{
 										if(empty($valor)){
											continue;
										}
										if(isset(  self::$values_from_DB[$campo_nombre_limpio] )){
											foreach( self::$values_from_DB[$campo_nombre_limpio]  AS $valores_db){
												if($id_cat == $valores_db['id_cat']){
													$campo["checked"] = $valores_db['id_cat'];
												}
											}
										}
  										$campo["value"] = $id_cat;
										$html .= '<div class="checkbox checkbox-default">';
										$html .= '<label class="checkbox" for="'. (!empty($campo['id']) ? $campo['id'] : "") .'">';
										$html .= form_checkbox($campo) ." ". $valor;
										$html .= $label.'</label>';
										unset($campo["checked"]);
										$html .='</div>';
									}

								}else{
									$html .= '<div class="checkbox m-r-xs">';
									$html .= form_checkbox($campo);
									$html .= '<label class="checkbox" for="'. (!empty($campo['id']) ? $campo['id'] : "") .'">';
									$html .= $label;
									$html .= '</label>';
									$html .= '</div>';
								}

								$html .='</div>';
							}
							else if($campo['type'] == 'radio')
							{
                                $html .= '<div class="form-group '. $columnas .' '. $hide_field .'">';
								if(!empty($valores))
								{
									foreach($valores AS $id_cat => $valor)
									{
										if(empty($valor)){
											continue;
										}
										$campo["value"] = $id_cat;
										$html .= '<label class="radio">';
										$html .= form_radio($campo) ." ". $valor;
										$html .= '</label>';
									}
								}
								$html .='</div>';
							}
							else if($campo['type'] == 'link')
							{
								$html .= '</div><div class="row"> <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>';
								$html .= '<div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">';
								$html .=  anchor($campo['url'], $label, $campo['attr']).' ';
								$html .= '</div>';
							}
							else if($campo['type'] == 'button-label')
							{
								$campo['type'] = "button";
								$html .= '<div class="form-group '. $columnas .' '. $hide_field .'">';
								$html .=  form_label($label);
								$html .=   form_button($campo) .' ';
								$html .= '</div>';
							}
							else if($campo['type'] == 'button-cancelar')
							{
								$campo['type'] = "button";
								$html .= '</div><div class="row"> <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>';

								$html .= '<div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">';
								$html .=   form_button($campo) .' ';
								$html .= '</div>';
							}
							else if($campo['type'] == 'button-guardar')
							{
								$campo['type'] = "button";
								$html .= '<div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">';
								$html .=   form_button($campo) .' ';
 								$html .= '</div>';
							}
							else if($campo['type'] == 'button')
							{
								$html .= '<div class="form-group '. $columnas .' '. $hide_field .'">';
								$html .=   form_button($campo) .' ';
//								$html .= '</div></div>';
								$html .= '</div>';
							}
							else if($campo['type'] == 'submit')
							{
								$campo['value'] = $label;
								$html .= '<div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">';
								$html .=  form_input($campo);
								$html .= '</div>';
							}
							else if($campo['type'] == 'textarea')
							{
                                //ESTO ES PARA LOS CASOS DONDE ME INTERESA QUE EL TEXTAREA ESTE
                                //FUERA DE UN ROW - LUEGO TIEMBIEN SE PUEDE PASAR EL DATACOLUMNS
                                //PARA CONTROLAR EL ANCHO.
                                if($campo["class"] == "form-control")
                                {
                                    $html .= '<div class="form-group '. $columnas .'">';
                                    $html .=  form_label($label);
                                    $html .=  form_textarea($campo);
                                    $html .= '<div style="clear:both;"></div>';
                                	$html .= '</div>';
                                }
                                else
                            	{
                                	$html .= '</div><div class="row"><div class="col-lg-12">';
                                    $html .=  form_label($label);
                                    $html .=  form_textarea($campo);
                                    $html .=  '<div class="clearfix">&nbsp;</div>';
                                    $html .= '</div></div><div class="row">';
                            	}
							}
							else if($campo['type'] == 'select-multiple')
							{
 								$attrs = 'size="8" class="'. $campo['class'] .'" id="'. $campo['id'] .'" '. (!empty($campo["disabled"]) ? 'disabled="disabled"' : '') . (!empty($campo['data-rule-required']) ? 'data-rule-required="true"' : "") . (!empty($campo['ng-model']) ? 'ng-model="'. $campo['ng-model'] .'"' : "") . (!empty($campo['ng-change']) ? 'ng-change="'. $campo['ng-change'] .'"' : "") . (!empty($campo['data-placeholder']) ? ' data-placeholder="'. $campo['data-placeholder'] .'"' : "");
								$options = !empty($campo['options']) ? $campo['options'] : array();

								$html .= '</div><div class="row" style="height: 225px;">';
 								$html .= '<div class="form-group col-xs-12 col-sm-5 col-md-5 col-lg-5">';
								$html .=	form_multiselect($campo['name'], $options, $selected, $attrs);
   								$html .='</div>';


								$html .= '<div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
											<button type="button" id="'.$campo['id'].'_rightAll" class="btn btn-block"><i class="fa fa-forward"></i></button>
											<button type="button" id="'.$campo['id'].'_rightSelected" class="btn btn-block"><i class="fa fa-chevron-right"></i></button>
											<button type="button" id="'.$campo['id'].'_leftSelected" class="btn btn-block"><i class="fa fa-chevron-left"></i></button>
											<button type="button" id="'.$campo['id'].'_leftAll" class="btn btn-block"><i class="fa fa-backward"></i></button>
											</div>
 										<div class="form-group col-xs-12 col-sm-5 col-md-5 col-lg-5">
											<select name="'.$campo['id'].'_to[]" id="'.$campo['id'].'_to'.'" class="form-control" size="8" multiple="multiple"></select>
										</div></div>';
 								$html .= '<div class="row">';
 							}
							else if($campo['type'] == 'select-checkbox-addon')
							{
								$attrs = 'class="'. $campo['class'] .' chosen-select" data-placeholder="Seleccione" '. (!empty($campo['disabled']) ? 'disabled="disabled"' : "") . (!empty($campo['data-rule-required']) ? 'data-rule-required="true"' : "");
								$selected = !empty($campo['selected']) ? $campo['selected'] : "";
								preg_match('/\[(.*?)\]/i', $campo['name'], $matches);
								$id = !empty($matches) ? $matches[1] : "";
								$id = str_replace("]", "", $id);
								$options = !empty($campo['options']) ? $campo['options'] : array();
								$html .= '<div class="form-group '. $columnas .' '. $hide_field .'">';
								$html .=  form_label($label);
								$html .= '<div class="input-group">
							      <span class="input-group-addon">
							      	<input type="checkbox" id="'. $id .'Check">
							      </span>';
								$html .= form_dropdown($campo['name'], $options, $selected, $attrs);
							    $html .= '</div>';
								$html .='</div>';
							}
							else if($campo['type'] == 'input-select')
							{
								$attrs = 'class="'. $campo['class'] .' chosen-select" data-placeholder="Seleccione" '. (!empty($campo['disabled']) ? 'disabled="disabled"' : "") . (!empty($campo['data-rule-required']) ? 'data-rule-required="true"' : "");
								$selected = !empty($campo['selected']) ? $campo['selected'] : "";
								preg_match('/\[(.*?)\]/i', $campo['name'], $matches);
								$id = !empty($matches) ? $matches[1] : "";
								$id = str_replace("]", "", $id);
								$options = !empty($campo['options']) ? $campo['options'] : array();
								$html .= '<div class="form-group '. $columnas .' '. $hide_field .'">';
								$html .=  form_label($label);
								$html .= '<div class="input-group form-inline">
							      	<input size="3" type="text" id="'. $id .'input" class="form-control">';
								$html .= form_dropdown($campo['name'], $options, $selected, $attrs);
							    $html .= '</div>';
								$html .='</div>';
							}
							else if($campo['type'] == 'select-right-button-addon')
							{
								$attrs = 'class="'. $campo['class'] .'" '.(!empty($campo['id']) ? ' id="'.$campo['id'].'"' : "") . (!empty($campo['data-rule-required']) ? ' data-rule-required="true"' : "") . (!empty($campo['disabled']) ? ' disabled="disabled"' : "") . (!empty($campo['ng-model']) ? ' ng-model="'. $campo['ng-model'] .'"' : "") . (!empty($campo['ng-change']) ? ' ng-change="'. $campo['ng-change'] .'"' : "") . (!empty($campo['data-placeholder']) ? ' data-placeholder="'. $campo['data-placeholder'] .'"' : "") . (!empty($campo['multiple']) ? 'multiple="multiple" ' : "");

								preg_match('/\[(.*?)\]/i', $campo['name'], $matches);
								$id = !empty($matches) ? $matches[1] : "";
								$id = str_replace("]", "", $id);

								$button_text_icon = !empty($campo['data-button-icon']) && $campo['data-button-icon'] != "" ? '<i class="fa '. $campo['data-button-icon'] .'"></i>' : (!empty($campo['data-button-text']) ? $campo['data-button-text'] : "");
								$button_class = !empty($campo['data-addon-class']) ? $campo['data-addon-class']  : "btn btn-default";

								$selected = !empty($campo['selected']) ? $campo['selected'] : "";
								$options = !empty($campo['options']) ? $campo['options'] : array();

								unset($campo['data-button-icon']);
								unset($campo['data-button-text']);
								unset($campo['data-addon-class']);

								$html .= '<div class="form-group '. $columnas .' '. $hide_field .'">';
								$html .=  form_label($label);
								$html .= '<div class="input-group">
							      '. form_dropdown($campo['name'], $options, $selected, $attrs) .'
								  <span class="input-group-btn">';

									if(!empty($button_text_icon)){
										$html .= '<button id="'. $id .'Btn" class="'. $button_class .'" type="button" '. (!empty($campo['disabled']) ? 'disabled="disabled"' : "") .'>'. $button_text_icon .'</button>';
									}else{
										$html .= '&nbsp;';
									}

									$html .= '</span>
							    </div>';
								$html .='</div>';
							}
							else if($campo['type'] == 'select-checkbox-button-addon')
							{
								$attrs = 'class="'. $campo['class'] .' chosen-select" data-placeholder="Seleccione" '. (!empty($campo['multiple']) ? 'multiple="multiple" ' : "") . (!empty($campo['disabled']) ? 'disabled="disabled"' : "") . (!empty($campo['data-rule-required']) ? 'data-rule-required="true"' : "");
								preg_match('/\[(.*?)\]/i', $campo['name'], $matches);
								$id = !empty($matches) ? $matches[1] : "";
								$id = str_replace("]", "", $id);
								$button_text_icon = !empty($campo['data-button-icon']) && $campo['data-button-icon'] != "" ? '<i class="fa '. $campo['data-button-icon'] .'"></i>' : $campo['data-button-text'];
								$selected = !empty($campo['selected']) ? $campo['selected'] : "";
								$options = !empty($campo['options']) ? $campo['options'] : array();
								unset($campo['data-button-icon']);
								unset($campo['data-button-text']);
								$html .= '<div class="form-group '. $columnas .' '. $hide_field .'">';
								$html .=  form_label($label);
								$html .= '<div class="input-group">
							      <span class="input-group-addon" style="border:0;">
							        <input type="checkbox" id="'. $id .'Check" aria-label="">
							      </span>
							      '. form_dropdown($campo['name'], $options, $selected, $attrs) .'
								  <span class="input-group-btn">
									<button id="'. $id .'Btn" class="btn btn-default" type="button">'. $button_text_icon .'</button>
									</span>
							    </div>';
								$html .='</div>';
							}
							else if($campo['type'] == 'fecha')
							{
  								$html .= '<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">';
								$html .= form_label($label).form_input($campo);
								$html .='</div>';
							}
							else if($campo['type'] == 'input-left-addon')
							{
								$button_text_icon = !empty($campo['data-addon-icon']) && $campo['data-addon-icon'] != "" ? '<i class="fa '. $campo['data-addon-icon'] .'"></i>' : $campo['data-addon-text'];
								$button_class = !empty($campo['data-addon-class']) ? $campo['data-addon-class']  : "btn btn-default";
								$campo['type'] = 'text';

								unset($campo['data-addon-icon']);
								unset($campo['data-addon-text']);
								$html .= '<div class="form-group '. $columnas .' '. $hide_field .'">';
								$html .=  form_label($label);
								$html .= '<div class="input-group">
								<span class="input-group-addon">'. $button_text_icon .'</span>
							      '. form_input($campo) .'
							    </div>';
								$html .='</div>';
							}
							else if($campo['type'] == 'input-right-addon')
							{
								preg_match('/\[(.*?)\]/i', $campo['name'], $matches);
								$id = !empty($matches) ? $matches[1] : "";
								$id = str_replace("]", "", $id);

								$button_text_icon = !empty($campo['data-addon-icon']) && $campo['data-addon-icon'] != "" ? '<i class="fa '. $campo['data-addon-icon'] .'"></i>' : $campo['data-addon-text'];
								$button_class = !empty($campo['data-addon-class']) ? $campo['data-addon-class']  : "btn btn-default";
								$is_button = !empty($campo['data-addon-button']) ? $campo['data-addon-button']  : "";
								$attr = !empty($campo['data-addon-attr']) ? $campo['data-addon-attr']  : "";

								//Verificar si existe atributos de boton y armarlo
								$attrs = "";
								if(!empty($attr)){
									foreach($attr AS $index => $value){
										$attrs .= $index.'="'. $value .'" ';
									}
								}

								unset($campo['data-addon-icon']);
								unset($campo['data-addon-text']);
								unset($campo['data-addon-class']);
								unset($campo['data-addon-disabled']);
								unset($campo['data-addon-attr']);

								$html .= '<div class="form-group '. $columnas .' '. $hide_field .'">';
								$html .=  form_label($label);
								$html .= '<div class="input-group">
							      '. form_input($campo) .'
								  <span class="'. (!empty($is_button) ? "input-group-btn" : "input-group-addon") .'">';

								  if(!empty($is_button)){
								  	$html .= '<button id="'. $id .'Btn" class="'. $button_class .'" type="button" '. $attrs .'>'. $button_text_icon .'</button>';
								  }else{
								  	$html .= $button_text_icon;
								  }

								  $html .= '</span>
							    </div>';
								$html .='</div>';
							}
							else if($campo['type'] == 'date-range-picker')
							{
								$button_text_icon = !empty($campo['data-addon-icon']) && $campo['data-addon-icon'] != "" ? '<i class="fa '. $campo['data-addon-icon'] .'"></i>' : $campo['data-addon-text'];
								unset($campo['data-addon-icon']);
								unset($campo['data-addon-text']);
								$campo['type'] = "text";

								$campo1 = $campo;
								$campo2 = $campo;

								$html .= '<div class="form-group '. $columnas .' '. $hide_field .'">';
								$html .=  form_label($label);
								$html .= '<div class="input-group">
						    				<span class="input-group-addon">'. $button_text_icon .'</span>';

								$campo1["name"] = 'campo['. $campo1["name"] .'_desde]';
								$campo1["id"] = $campo1["id"] ."_desde";
								if($campo1["ng-model"]){
									$campo1["ng-model"] = $campo1["ng-model"] ."_desde";
								}


								$html .= form_input($campo1);
								$html .= '<span class="input-group-addon">a</span>';

								$campo2["name"] = 'campo['. $campo2["name"] .'_hasta]';
								$campo2["id"] = $campo2["id"] ."_hasta";
								if($campo2["ng-model"]){
									$campo2["ng-model"] = $campo2["ng-model"] ."_hasta";
								}

								$html .= form_input($campo2);
						    	$html .= '</div>
								</div>';
							}
							else if($campo['type'] == 'input-right-addon')
							{
								$button_text_icon = !empty($campo['data-addon-icon']) && $campo['data-addon-icon'] != "" ? '<i class="fa '. $campo['data-addon-icon'] .'"></i>' : $campo['data-addon-text'];
								unset($campo['data-addon-icon']);
								unset($campo['data-addon-text']);
								$html .= '<div class="form-group '. $columnas .' '. $hide_field .'">';
								$html .=  form_label($label);
								$html .= '<div class="input-group">
							      '. form_input($campo) .'
								  <span class="input-group-addon">'. $button_text_icon .'</span>
							    </div>';
								$html .='</div>';
							}
							else if($campo['type'] == 'firma')
							{
								$campo["style"] = 'margin: 24px 0 8px 0 !important; border-left:0 !important; border-top:0px !important; border-right:0px !important;';

								$html .= '<div class="form-group '. $columnas .' '. $identificadorCampo .' '. $hide_field .'" style="height:auto !important;">';
							    $html .= form_input($campo);
								$html .=  form_label($label);
								$html .='</div>';
							}
							else
							{
								// Campos default: Input text
								$html .= '<div class="form-group '. $columnas .' '. $identificadorCampo .' '. $hide_field .'">'.form_label($label)
								.form_input($campo);
								$html .='</div>';
							}
						}
					$html .='</div>';
					$html .= '</div></div>';
					$m++;
				}
				$html .= form_close();
			}
			$html .= '</div>';
			++$i;
		}
		$html .='</div>';
		return $html;
	}
	/**
	 * Visualizar el tema completo.
	 *
	 * @access public
	 * @return void or string (result of template build)
	 */
	public function visualizar()
	{
		self::cargar_vista('header');
		self::cargar_vista(self::$module_file, $this->content);
		self::cargar_vista('footer');
	}
	/**
	 * Visualizar Solo la Vista
	 *
	 * @access public
	 * @return void or string (result of view build)
	 */
	public function vista_parcial($vista=NULL){
		if($vista==NULL){
			return false;
		}
		self::cargar_vista($vista, $this->content);
	}
}

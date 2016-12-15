<?php defined ('BASEPATH') or exit ('No direct script access allowed');

if (! class_exists ( 'MX_Controller', false )) {
	require APPPATH.'third_party/MX/Controller.php';
}

class CRM_Controller extends MX_Controller {

	protected static $ci;
	public static $id_modulo;
	public $uuid_usuario;
	public static $uuid_user;

	public function __construct()
	{
 		//Construct Padre
		parent::__construct ();

		//Instancia del core de CI
		self::$ci =& get_instance();

		/*
		 * Cargar el id del modulo actual
		 */
		self::$id_modulo = $this->id_modulo();

		/*
		 * Cargar el uuid del usuario que esta loguiado.
		 */
		$this->uuid_usuario = $this->session->userdata('huuid_usuario');
		self::$uuid_user = $this->session->userdata('huuid_usuario');
		(new \Dotenv\Dotenv('./'))->load();
	}

	/**
	 * Consultar el id del modulo
	 * actual que se esta mostrando.
	 *
	 * @return array
	 */
	private function id_modulo()
	{

		$clause = array(
			"controlador" => $this->router->fetch_class()
		);
		$result = $this->db->select("id")
			->from('modulos' )
			->where($clause)
			->get()
			->result_array();
		return !empty($result) ? $result[0]["id"] : "";
	}

 }

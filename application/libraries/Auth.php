<?php defined('BASEPATH') || exit('No direct script access allowed');

use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;

/**
 * CRM Base Auth
 *
 * Libreria para manejar:
 *
 * 1. Validacion de Sesiones
 * 2. Privilegios y Permisos
 *
 * @package    PensaApp
 * @category   Libraries
 * @author     Pensanomica Dev Team
 * @version    2.5 - 22/1/2016
 * @link       http://www.pensanomica.com
 * @copyright  @jluispinilla
 * @since      23/10/2014
 *
 */

class Auth {

	protected static $acl;
    protected static $ci;

    protected static $user_id; //id del usuario
    public static $user_roles = array(); //roles del usuario
    protected static $user_resources; //recursos del usuario
    protected static $user_rol_id; //id del rol de usuario
    protected static $user_rol_name; //nombre del rol de usuario
    protected static $super_usuario;

    protected static $modules = array();
    protected static $resources = array();
    protected static $recursos_libres = array(
    	"menu/sidebar",
    	"/index",
    	"/indexERROR"
    );
    protected static $resource_name;

    protected static $cache;
    public static $id_empresa;
    private static $uuid_empresa;
    private static $uuid_usuario;
    
    /**
     * @var int
     */
    protected static $empresa_id;
    
	function __construct()
	{
        //Instancia de Codeigniter
		self::$ci =& get_instance();

		//Instancia de Libreria ACL de Zend Framework
		self::$acl = new Acl();

        //id del Usuario
        self::$user_id = self::$ci->session->userdata('id_usuario');
        
        //uuid del Usuario
        self::$uuid_usuario = self::$ci->session->userdata('huuid_usuario');

        //Recurso Actual
        self::$resource_name = self::$ci->uri->uri_string;
        
        //Establecer uuid de empresa
        self::$uuid_empresa = self::$ci->session->userdata('uuid_empresa');
        
        //Inicializar variable cache
        self::$cache = Cache::inicializar();
        
        //Obtener el id_empresa de session
        self::$ci->load->model('usuarios/empresa_orm');
        $empresa = Empresa_orm::findByUuid(self::$uuid_empresa);
        self::$empresa_id = is_object($empresa) ? $empresa->id : "";

        //Verificar si la session existe
        self::check_session();

        //Inicializar permisos del usuario
        self::init_acl();
	}

    /**
     * Verifica si la session existe,
     * si no existe, redirecciona al
     * usuario al login page.
     *
     * Si la peticion a la pagina se hizo
     * a traves de ajax, enviar un json
     * de respuesta con variable que indique
     * que la sesion a expirado.
     *
     * @return none
     */
    protected function check_session()
    {
        //Verificar si se esta accesando por medio
        //de una peticion ajax.
        if(self::$ci->input->is_ajax_request())
        {
            //Solo enviar json cuando no exista la session
            if(empty(self::$user_id))
            {
                $json = '{"session":['.json_encode(array("expired" => true)).']}';
                echo $json;
                exit;
            }
        }
        /**
         * Checks to see if the application was run from the command-line interface.
         */
        else if(self::$ci->input->is_cli_request()){
        	
        	return true;
        	
        }else {
            if(empty(self::$user_id)){
                if(function_exists('redirect'))
                {
                    //Verificar si esta en otro modulo
                    if(self::$ci->router->fetch_class() != "login") {
                        redirect('login', 'refresh');
                    }
                }else{

                    self::$ci->load->helper('url');

                    //Verificar si esta en otro modulo
                    if(self::$ci->router->fetch_class() != "login"){
                        redirect('login', 'refresh');
                    }
                }
            }
        }
    }

	private function init_acl()
    {
        //Verificar que exista la session
    	if(empty(self::$uuid_empresa)){
            return false;
        }

        //Consultar Cache de Roles
        self::$user_roles = self::$cache->get("Acl-usuario-roles-". self::$uuid_usuario . self::$empresa_id);
        
        //Verificar si existe Cache
        if(self::$user_roles == null){
        	
        	self::$user_roles = self::get_user_rol();
        	
        	//Si no existe guardar cache
        	self::$cache->set("Acl-usuario-roles-". self::$uuid_usuario . self::$empresa_id, self::$user_roles);
        }
        
        /*echo "Acl-usuario-roles-". self::$uuid_usuario . self::$empresa_id. "<pre>";
        print_r(self::$user_roles);
        echo "</pre>";*/
       
        if(!empty(self::$user_roles))
        {
            /*
             * Contador empieza en 1
             */
        	$contador = 1;

            /**
			 * Contar Total de Roles del Usuario
             */
            $total_roles = count(self::$user_roles);

            /**
             * Se usara para hacer un match de los roles
             * que tenga el usuario, si son varios.
             */
            $roles_inicializados = array();

            //Recorrer los roles del usuario
        	foreach (self::$user_roles AS $rol)
            {
                //Iinicializar el rol del usuario
                $id_rol = $rol["id_rol"];
                $superusuario = $rol["superuser"];
                $nombre_rol = $rol["nombre_rol"];
                $nombre_rol = strtolower(str_replace(" ", "_", $nombre_rol));
                $nombre_rol = strtolower(str_replace("ñ", "n", $nombre_rol));
                
                if(!empty($rol["superuser"])){
                	self::$super_usuario = (int)$rol["superuser"];
                }
                
                //Returns true if and only if the Role exists in the registry
                if(!self::$acl->hasRole($nombre_rol))
                {
                    /**
                     * Si el contador es igual al total de roles
                     * y la variable $roles_inicializados
                     * contiene otros roles.
                     */
                	if($contador == $total_roles && !empty($roles_inicializados)){

                     	//Inicializar el rol y
                     	//hacer herencia de los permisos
                     	//de los otros roles.
                     	self::setRole($nombre_rol, $roles_inicializados);

                     	//inicializar estas variables con la informacion
                     	//de este ultimo rol.
                     	self::$user_rol_id = $id_rol;
                     	self::$user_rol_name = $nombre_rol;

                     }else{

                     	//Adds a Role having an identifier unique to the registry
                     	self::setRole($nombre_rol);

                     	/**
	                     * Si el contador es igual al total de roles
	                     * y la variable $roles_inicializados esta vacia.
	                     *
	                     */
                     	if($contador == $total_roles && empty($roles_inicializados)){

                     		//inicializar estas variables con la informacion
                     		//de este rol.
                     		self::$user_rol_id = $id_rol;
                     		self::$user_rol_name = $nombre_rol;
                     	}
                     }
                }

                //Consultar Cache de Recursos
        		$all_resources = self::$cache->get("Acl-recursos-". self::$uuid_usuario . self::$empresa_id);
        
        		//Verificar si existe Cache
        		if($all_resources == null){
        			 
        			$all_resources = self::get_resources();
        			 
        			//Si no existe guardar cache
        			self::$cache->set("Acl-recursos-". self::$uuid_usuario . self::$empresa_id, $all_resources);
        		}
        		
                //Inicializar todos los recursos existentes
                foreach ($all_resources AS $index => $resources) {
                	foreach ($resources AS $resource_uri => $resource){

                        //Returns true if and only if the Resource exists in the ACL
                        if(!self::$acl->hasResource($resource_uri))
                        {
                            //Adds a Resource having an identifier unique to the ACL
                           self::setResource($resource_uri);
                        }
                	}
                }

                //Verificar que el usuario no sea Super Usuario
                if(empty($superusuario) && $superusuario==0)
                {
                    //Consultar Cache de Recursos de Roles
                    self::$user_resources = self::$cache->get("Acl-roles-recursos-". self::$uuid_usuario . self::$empresa_id);

                    //Verificar si existe Cache
                    if(self::$user_resources == null){
                    
                    	//Buscar los recursos a los que el usuario tiene accesos y sus permisos
                    	self::$user_resources = $this->get_rol_recursos_permisos($id_rol);
                    	
                    	//Si no existe guardar cache
                    	self::$cache->set("Acl-roles-recursos-". self::$uuid_usuario . self::$empresa_id, self::$user_resources);
                    }

                    if(!empty(self::$user_resources))
                    {
                        foreach (self::$user_resources AS $recurso => $permisos)
                        {
                            if(!empty($permisos))
                            {
                                $permisosArr[]['permiso'] = $permisos;
                                $permisosArr[]['recurso'] = $recurso;

                                //Inicializar permisos de este usuario
                                self::setPermission($nombre_rol, NULL, $recurso, $permisos);
                            }
                        }
                    }
                }
                else
                {
                    //Darle permisos completo al administrador
                    self::setPermission($nombre_rol, $superusuario);
                }

                /*
                 * Si hay mas roles
                 * introducir en el arreglo
                 * $roles_inicializados
                 * el nombre del rol actual.
                 */
                if($contador < $total_roles){
                	$roles_inicializados[] = $nombre_rol;
                }

                $contador++;
            }
            
        }
    }

    /**
     * Obtener todos los recursos de cada modulo.
     *
     * @return [type] [description]
     */
    protected function get_resources()
	{
        $resources = array();
        if($handle = opendir(self::$ci->config->item('modules_locations'))){
            while(($section = readdir($handle)) !== false){
                if($section === '.' || $section === '..') {
                    continue;
                }
                if(!empty($section))
                {
                    $checkRouteFile = Modules::find('routes.php', $section, 'config/');

                    if(!empty($checkRouteFile))
                    {
                        $routePath = self::$ci->config->item('modules_locations') . $section .'/config/routes.php';

                        if(file_exists($routePath))
                        {
                            include($routePath);

                            if(isset($route) && !empty($route))
                            {
                                $resources[] = $route;
                                unset($route);
                            }
                        }
                    }
                }
            }
            closedir($handle);
        }
        self::$resources = $resources;

        return self::$resources;
    }

    /**
     * Obtener el rol del usuario que esta loguiado
     *
     * @return [type] [description]
     */
    protected function get_user_rol()
    {
        //Verificar que exista la session
        if(empty(self::$uuid_empresa)){
            return false;
        }

        $fields = array(
            "rol.id as id_rol",
            "rol.nombre as nombre_rol",
        	"rol.superuser"
        );
        $clause = array(
            "usr.id" => self::$user_id
        );
        $clause["usrrol.empresa_id"] = self::$empresa_id;

        $result = self::$ci->db->select($fields)
        			->distinct()
                    ->from('usuarios AS usr')
                    ->join('usuarios_has_roles AS usrrol', 'usrrol.usuario_id = usr.id', 'LEFT')
                    ->join('usuarios_has_empresas AS uemp', 'uemp.usuario_id = usr.id', 'LEFT')
                    ->join('empresas AS emp', 'emp.id = uemp.empresa_id', 'LEFT')
                    ->join('empresas_has_roles AS emproles', 'emproles.empresa_id = emp.id', 'LEFT')
                    ->join('roles AS rol', 'rol.id = usrrol.role_id', 'LEFT')
                    ->where($clause)
                    ->get()
                    ->result_array();
        
        /*echo self::$ci->db->last_query() . "<pre>";
        print_r($result);
        echo "</pre>";
        die();*/
        
        //echo self::$ci->db->last_query() . PHP_EOL;
        return $result;
    }
    
    /**
     * Obtener los nombre de los modulos
     * al cual el usuario tiene acceso.
     *
     * @return array
     */
    private static function get_user_modules()
    {
        $result = array();
        $fields = array(
            "mods.controlador",
        );
        $clause = array(
            "usr.id" => self::$user_id
        );
        
        if(self::$super_usuario == 0){
        	$clause["usrrol.empresa_id"] = self::$empresa_id;
        }
        
        $userModule = self::$ci->db->select($fields)
                    ->distinct()
                    ->from('usuarios AS usr')
                    ->join('usuarios_has_roles AS usrrol', 'usrrol.usuario_id = usr.id', 'LEFT')
                    ->join('roles_permisos AS rop', 'rop.rol_id = usrrol.role_id', 'LEFT')
                    ->join('permisos AS perm', 'perm.id = rop.permiso_id', 'LEFT')
                    ->join('recursos AS rec', 'rec.id = perm.recurso_id', 'LEFT')
                    ->join('modulos AS mods', 'mods.id = rec.modulo_id', 'LEFT')
                    ->where($clause)
                    ->get()
                    ->result_array();

        if(!empty($userModule))
        {
            $i=0;
            foreach ($userModule AS $module)
            {
                $result[$i] = $module["controlador"];
                $i++;
            }
        }
        return $result;
    }

    /**
     * Obetener segun el rol, los recursos a los que tiene acceso
     * y los permiso que tiene en cada recurso.
     *
     * @param  integer $rol_id
     * @return array
     */
    public function get_rol_recursos_permisos($rol_id=NULL)
    {
         if($rol_id==NULL){
            return false;
        }
        $permissions = array();

        $fields = array(
            "perm.id",
            "perm.nombre AS nombre_permiso",
            "rec.nombre AS nombre_recurso"
        );
        $clause = array(
            "rop.rol_id" => $rol_id
        );
        $results = self::$ci->db->select($fields)
                    ->from('roles_permisos AS rop')
                    ->join('permisos AS perm', 'perm.id = rop.permiso_id', 'LEFT')
                    ->join('recursos AS rec', 'rec.id = perm.recurso_id', 'LEFT')
                    ->where($clause)
                    ->get()
                    ->result_array();
        
        if(!empty($results))
        {
            foreach ($results AS $result)
            {
                if(!empty($result["nombre_recurso"])){
                   $permissions[$result["nombre_recurso"]][] = $result["nombre_permiso"];
                }
            }
        }

        return $permissions;
    }
    
    public static function limpiar_cache()
    {
    	self::$cache->delete("Acl-usuario-roles-". self::$uuid_usuario);
    	self::$cache->delete("Acl-recursos-". self::$uuid_usuario);
    	self::$cache->delete("Acl-roles-recursos-". self::$uuid_usuario);
    }

	protected function setRole($role_id=NULL, $parents=array())
	{
		if($role_id == NULL){
			return false;
		}

		$role = new Role($role_id);

		if(empty($parents)){
			self::$acl->addRole($role);
		}else{
			self::$acl->addRole($role, $parents);
		}
	}

	protected function setResource($resource_name=NULL)
	{
		if($resource_name == NULL){
			return false;
		}

		$resource = new Resource($resource_name);
		self::$acl->addResource($resource);
	}

	public function setPermission($role_name=NULL, $superusuario=NULL, $resource_name=NULL, $privileges=NULL)
	{
		if($role_name==NULL){
			return false;
		}

        if($superusuario != NULL){
            // Super Usuario is allowed all privileges
            self::$acl->allow($role_name);
   
        }else{
        	self::$acl->allow($role_name, $resource_name, $privileges);
        }
	}

	/**
    * Has Permission - returns true if the user has the received
    * permission. Simply pass the name of the permission.
    *
    * @param string $permission_name - The name of the permission
    * @param string $resource_name - The name of the resource (URL)
    * @return boolean
    */
    public static function has_permission($nombre_permiso=NULL, $resource_name=NULL)
    {
        if($nombre_permiso==NULL){
            return false;
        }

        //Verificar si existe variable $resource_name, si no tomar el recursos
        //que se esta mostrando actualmente.
        $resource_name = $resource_name != NULL ? $resource_name : self::$resource_name;

        //reemplazar algun numero del recurso por el texto "(:num)", que es como se muestra en los $routes
        $resource_name = preg_replace('/(\/(\d))/', "/(:num)", $resource_name);
        
        //Consultar Cache de Roles
        self::$user_roles = self::$cache->get("Acl-usuario-roles-". self::$uuid_usuario . self::$empresa_id);
        
        /*echo "Acl-usuario-roles-". self::$uuid_usuario . self::$empresa_id. "<pre>";
        print_r(self::$user_roles);
        echo "</pre>";*/

        $tienepermiso = array();
        if(!empty(self::$user_roles))
        {
        	//Recorrer los roles del usuario
        	foreach (self::$user_roles AS $rol)
        	{
        		if(empty($rol["nombre_rol"]) || in_array($resource_name, self::$recursos_libres)){
        			continue;	
        		}
        		
        		$nombre_rol = $rol["nombre_rol"];
        		$nombre_rol = strtolower(str_replace(" ", "_", $nombre_rol));
        		$nombre_rol = strtolower(str_replace("ñ", "n", $nombre_rol));

        		try {
        			if(self::$super_usuario){
        				//echo "SUPER USUARIO: ". (int)self::$super_usuario . PHP_EOL .PHP_EOL;
        				$tienepermiso[] = self::$acl->isAllowed($nombre_rol) ? 1 : 0;
        			}else{
        				$tienepermiso[] = self::$acl->isAllowed($nombre_rol, $resource_name, $nombre_permiso)? 1 : 0;
        			}
        		} catch (Exception $e) {
        			log_message("error", "LIBRERIA: ". __METHOD__ .", Linea: ". __LINE__ ." --> ". $e->getMessage()."\r\n");
        			$tienepermiso[] = 0;
        		}
        	}
        	
        	// Recorrer arreglo y retornar
        	// true en caso de que exista
        	// un permiso positivo (1)
        	foreach($tienepermiso AS $permiso){
        		if($permiso == 1){
        			return true;
        		}
        	}
        	
        	//De lo contrario retornar false
        	return false;
        }
        
    }

    /**
     * Verificar si el usuario tiene acceso al modulo actual.
     * De lo contrario lo redirecciona.
     *
     * @return void
     */
    public static function has_module_access($module=NULL)
    {
    	if($module==NULL){
            return false;
        }
        
        if(self::$super_usuario){
        	return true;
        }

        self::$modules = self::get_user_modules();
        
        //Verificar si $module es un array
        if(is_array($module)){
            return array_intersect($module, self::$modules) || self::$super_usuario ? true : false;
        }else{
             return in_array($module, self::$modules) || self::$super_usuario ? true : false;
        }
    }
}

/* End of file Pacl.php */

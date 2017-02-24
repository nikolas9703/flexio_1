<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

//rutas para accion de organizacion
$route['usuarios/organizacion'] 			= 'usuarios/organizacion';
$route['usuarios/crear-organizacion'] 			= 'usuarios/crear_organizacion';
$route['usuarios/ajax-listar-organizacion'] 	= 'usuarios/ajax_listar_organizacion';
//rutas para acciones de empresa
$route['usuarios/listar-empresa'] 			= 'usuarios/listar_empresa';
$route['usuarios/crear-empresa'] 			= 'usuarios/crear_empresa';
$route['usuarios/editar-empresa'] 			= 'usuarios/editar_empresa';
//rutas para accciones del usuario
$route['usuarios/agregar-usuarios'] 		= 'usuarios/agregar_usuarios';
$route['usuarios/ajax-listar-usuarios'] 	= 'usuarios/ajax_listar_usuarios';
$route['usuarios/empresas-usuario'] 		= 'usuarios/empresas_usuario';
$route['usuarios/ajax-empresa-usuario'] 	= 'usuarios/ajax_empresa_usuario';
$route['usuarios/ajax-guardar-usuario'] 	= 'usuarios/ajax_guardar_usuario';


$route['usuarios/ver-perfil/(:num)'] 			= 'usuarios/ver_perfil/$1';

$route['usuarios/politicas'] 				= 'usuarios/politicas';
$route['usuarios/ver-usuario/(:num)'] 				= 'usuarios/ver_usuario/$1';
$route['usuarios/ver-usuario-admin/(:num)'] = 'usuarios/ver_usuario_admin/$1';
$route['usuarios/ajax-listar-usuarios'] 	= 'usuarios/ajax_listar_usuarios';
$route['usuarios/ajax-verificar-usuario'] 	= 'usuarios/ajax_verficar_usuario';
$route['usuarios/ajax-crear-usuario'] 		= 'usuarios/ajax_crear_usuario';
$route['usuarios/editar-usuario/(:num)'] 	= 'usuarios/editar/$1';
$route['usuarios/ajax-validando-contrasenas'] 	= 'usuarios/ajax_validando_contrasenas';
$route['usuarios/ajax-toggle-estado'] 	= 'usuarios/ajax_toggle_estado';
$route['usuarios/ajax-catalogo'] 	= 'usuarios/ajax_catalogo';



//FUnciones para activar y desactivar usuarios
$route['usuarios/ajax-activar-usuario'] 	= 'usuarios/ajax_activar_usuario';
$route['usuarios/ajax-descativar-usuario'] 	= 'usuarios/ajax_descativar_usuario';
$route['usuarios/ajax-list-roles'] 			= 'usuarios/ajax_list_roles';
$route['usuarios/ajax-oportunidades'] 			= 'usuarios/ajax_oportunidades';
$route['usuarios/ajax-actividades'] 			= 'usuarios/ajax_actividades';
$route['usuarios/ajax-tabla-agentes'] 			= 'usuarios/ajax_tabla_agentes';
$route['usuarios/ajax-polular-tabla'] 			= 'usuarios/ajax_polular_tabla';
$route['usuarios/ajax-listar-empresas'] = 'usuarios/ajax_listar_empresas';
$route['usuarios/ajax-notifications'] = 'usuarios/ajax_notifications';
/* End of file routes.php */
/* Location: ./application/config/routes.php */

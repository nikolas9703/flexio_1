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

//rutas para accion de polizas
$route['polizas/listar'] 			= 'polizas/listar';
$route['polizas/ajax-listar'] 	    = 'polizas/ajax_listar';
$route['polizas/ajax-listar-ramos'] 	    = 'polizas/ajax_listar_ramos';
$route['polizas/ajax-listar-ramos-tree'] = 'polizas/ajax_listar_ramos_tree';
$route['polizas/ajax-cambiar-estado-ramo'] = 'polizas/ajax_cambiar_estado_ramo';
$route['polizas/ajax-guardar-ramos'] = 'polizas/ajax_guardar_ramos';
$route['polizas/ajax-buscar-ramo'] = 'polizas/ajax_buscar_ramo';
$route['polizas/crear'] 			= 'polizas/crear';
$route['polizas/editar'] 			= 'polizas/editar';



/* End of file routes.php */
/* Location: ./application/config/routes.php */

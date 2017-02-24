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

//rutas para accion de reclamos
$route['reclamos/listar'] 			= 'reclamos/listar';
$route['reclamos/crear'] 			= 'reclamos/crear';
$route['reclamos/editar'] 			= 'reclamos/editar';
$route['reclamos/ver'] 				= 'reclamos/ver';
$route['reclamos/ajax-listar'] 	    = 'reclamos/ajax_listar';
$route['reclamos/ajax-listar-ramos'] 	    = 'reclamos/ajax_listar_ramos';
$route['reclamos/ajax-listar-ramos-tree'] = 'reclamos/ajax_listar_ramos_tree';
$route['reclamos/ajax-cambiar-estado-ramo'] = 'reclamos/ajax_cambiar_estado_ramo';
$route['reclamos/ajax-guardar-ramos'] = 'reclamos/ajax_guardar_ramos';
$route['reclamos/ajax-buscar-ramo'] = 'reclamos/ajax_buscar_ramo';




/* End of file routes.php */
/* Location: ./application/config/routes.php */

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

//rutas para accion de aseguradoras
$route['ajustadores/listar']                = 'ajustadores/listar';
$route['ajustadores/ajax-listar'] 	    = 'ajustadores/ajax_listar';
$route['ajustadores/ajax-listar-contacto']  = 'ajustadores/ajax_listar_contacto';
$route['ajustadores/crear']                 = 'ajustadores/crear';
$route['ajustadores/ver/(:any)']            = 'ajustadores/ver/$1';
$route['ajustadores/ajax-guardar-contacto'] = 'ajustadores/ajax_guardar_contacto';
$route['ajustadores/ajax-contacto-principal'] = 'ajustadores/asignar_contacto_principal';
/* End of file routes.php */
/* Location: ./application/config/routes.php */

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

//Formulario crear/editar
$route['cajas/crear'] = 'cajas/crear';
$route['cajas/ver/(:any)'] = 'cajas/crear/$1';
$route['cajas/transferir'] = 'cajas/transferir';
$route['cajas/guardar-transferir-desde'] = 'cajas/guardar_transferir_desde';
$route['cajas/transferir-desde-caja/(:any)'] = 'cajas/transferir_desde_caja/$1';

$route['cajas/listar'] = 'cajas/listar';
$route['cajas/ajax-listar'] = 'cajas/ajax_listar';
$route['cajas/ajax-guardar-caja'] = 'cajas/ajax_guardar_caja';
$route['cajas/ajax-guardar-transferencia'] = 'cajas/ajax_guardar_transferencia';
$route['cajas/ajax-listar-transferencias'] = 'cajas/ajax_listar_transferencias';
$route['cajas/ajax-listar-facturas-ventas'] = 'cajas/ajax_listar_facturas_ventas';
$route['cajas/ajax-guardar-comentario'] = 'cajas/ajax_guardar_comentario';
$route['cajas/ajax-guardar-documentos'] = 'cajas/ajax_guardar_documentos';



/* End of file routes.php */
/* Location: ./application/config/routes.php */

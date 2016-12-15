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

$route['clientes_potenciales/listar'] = 'clientes_potenciales/listar_clientes_potenciales';
$route['clientes_potenciales/ajax-listar-clientes-potenciales'] = 'clientes_potenciales/ajax_listar_clientes_potenciales';
$route['clientes_potenciales/ocultotabla'] = 'clientes_potenciales/ocultotabla';
$route['clientes_potenciales/crear'] = 'clientes_potenciales/crear_cliente_potencial';
$route['clientes_potenciales/ver-cliente-potencial/(:any)'] = 'clientes_potenciales/editar_cliente_potencial/$1';
$route['clientes_potenciales/editar-cliente-potencial/(:any)'] = 'clientes_potenciales/editar_cliente_potencial/$1';
$route['clientes_potenciales/editar/(:any)'] = 'clientes_potenciales/editar/$1';

$route['clientes_potenciales/exportar'] = 'clientes_potenciales/exportar';
$route['clientes_potenciales/eliminar'] = 'clientes_potenciales/eliminar';
$route['clientes_potenciales/ajax-convertir-juridico'] = 'clientes_potenciales/ajax_convertir_juridico';
$route['clientes_potenciales/ajax-convertir-natural'] = 'clientes_potenciales/ajax_convertir_natural';
$route['clientes_potenciales/ajax-seleccionar-cliente-potencial'] = 'clientes_potenciales/ajax_seleccionar_cliente_potencial';
$route['clientes_potenciales/ajax-eliminar'] = 'clientes_potenciales/ajax_eliminar';
$route['clientes_potenciales/ajax-guardar-comentario'] = 'clientes_potenciales/ajax_guardar_comentario';

/* End of file routes.php */
/* Location: ./application/config/routes.php */

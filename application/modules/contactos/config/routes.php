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

$route['contactos/listar-contactos'] = 'contactos/listar_contactos';
$route['contactos/ocultotabla'] = 'contactos/ocultotabla';
$route['contactos/crear-contacto/(:any)'] = 'contactos/crear_contacto/$1';
$route['contactos/crearsubpanel'] = 'contactos/crearsubpanel';
$route['contactos/editarsubpanel'] = 'contactos/editarsubpanel';
$route['contactos/ajax-listar-contactos'] 	= 'contactos/ajax_listar_contactos';
$route['contactos/ajax-seleccionar-todos-contacto'] 	= 'contactos/ajax_seleccionar_todos_contacto';
$route['contactos/ajax-seleccionar-contacto'] 	= 'contactos/ajax_seleccionar_contacto';
$route['contactos/ver-contacto/(:any)'] = 'contactos/editar_contacto/$1';
$route['contactos/editar-contacto-subpanel'] = 'contactos/editar_contacto_subpanel';
$route['contactos/ajax-eliminar-cliente-nombre-comercial'] = 'contactos/ajax_eliminar_cliente_nombre_comercial';
$route['contactos/ajax-eliminar-cliente'] = 'contactos/ajax_eliminar_cliente';
$route['contactos/ajax-asignar-contacto-principal'] = 'contactos/ajax_asignar_contacto_principal';
$route['contactos/ajax-seleccionar-nombres-comerciales'] = 'contactos/ajax_seleccionar_nombres_comerciales';
$route['contactos/ajax-exportar/(:any)'] = 'contactos/ajax_exportar/$1';

$route['contactos/ajax-guardar-contacto'] = 'contactos/ajax_guardar_contacto';
$route['contactos/ajax-contacto-info'] = 'contactos/ajax_contacto_info';
$route['contactos/ajax-contacto-inactivo'] = 'contactos/ajax_contacto_inactivo';
/* End of file routes.php */
/* Location: ./application/config/routes.php */

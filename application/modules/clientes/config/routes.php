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

$route['clientes/listar'] = 'clientes/listar';
$route['clientes/crear/(:any)'] = 'clientes/crear/$1';
$route['clientes/ver/(:any)'] = 'clientes/ver/$1';
$route['clientes/guardar'] = 'clientes/guardar';
$route['clientes/exportar'] = 'clientes/exportar';
$route['clientes/guardar-agrupador'] = 'clientes/guardar_agrupador';

//ajax
$route['clientes/ajax-listar'] = 'clientes/ajax_listar';
$route['clientes/ajax-cliente-potencial'] = 'clientes/ajax_cliente_potencial';
$route['clientes/ajax-centro-facturable'] = 'clientes/ajax_centro_facturable';
$route['clientes/ocultoformulariocomentarios'] = 'clientes/ocultoformulariocomentarios';
$route['clientes/ajax-guardar-comentario'] = 'clientes/ajax_guardar_comentario';
$route['clientes/ajax-guardar-documentos'] = 'clientes/ajax_guardar_documentos';
$route['clientes/ajax-get-montos'] = 'clientes/ajax_get_montos';
$route['clientes/ajax-verificar-identificacion'] = 'clientes/ajax_verificar_identificacion';

//Creacion y Edicion de los formularios


//Rutas y Permisos Adicionales


//Rutas que no son conciderados permisos


/* End of file routes.php */
/* Location: ./application/config/routes.php */

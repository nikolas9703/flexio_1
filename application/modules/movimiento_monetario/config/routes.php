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
$route['movimiento_monetario/crear_recibos'] = 'movimiento_monetario/crear_recibos';
$route['movimiento_monetario/crear_retiros'] = 'movimiento_monetario/crear_retiros';
$route['movimiento_monetario/ver/(:any)'] = 'movimiento_monetario/crear_recibos/$1';
$route['movimiento_monetario/ver_retiros/(:any)'] = 'movimiento_monetario/crear_retiros/$1';
$route['movimiento_monetario/ajax_eliminar_recibos'] = 'movimiento_monetario/ajax_eliminar_recibos';
$route['movimiento_monetario/ajax_eliminar_retiros'] = 'movimiento_monetario/ajax_eliminar_retiros';


$route['movimiento_monetario/ajax-getComentarioRecibos'] = 'movimiento_monetario/ajax_getComentarioRecibos';
$route['movimiento_monetario/ajax-postComentarioRecibos'] = 'movimiento_monetario/ajax_postComentarioRecibos';

$route['movimiento_monetario/ajax-getComentarioRetiros'] = 'movimiento_monetario/ajax_getComentarioRetiros';
$route['movimiento_monetario/ajax-postComentarioRetiros'] = 'movimiento_monetario/ajax_postComentarioRetiros';

$route['movimiento_monetario/listar_recibos'] = 'movimiento_monetario/listar_recibos';
$route['movimiento_monetario/listar_retiros'] = 'movimiento_monetario/listar_retiros';
$route['movimiento_monetario/ajax-listar-recibos'] = 'movimiento_monetario/ajax_listar_recibos';
$route['movimiento_monetario/ajax-listar-retiros'] = 'movimiento_monetario/ajax_listar_retiros';
$route['movimiento_monetario/ajax-cliente-proveedor'] = 'movimiento_monetario/ajax_cliente_proveedor';
$route['movimiento_monetario/ocultotabla'] = 'movimiento_monetario/ocultotabla';
$route['movimiento_monetario/ocultotabla_retiros'] = 'movimiento_monetario/ocultotabla_retiros';
$route['movimiento_monetario/ajax-guardar-documentos'] = 'movimiento_monetario/ajax_guardar_documentos';
$route['movimiento_monetario/ajax-cuenta-contable'] = 'movimiento_monetario/ajax_cuenta_contable';

/* End of file routes.php */
/* Location: ./application/config/routes.php */
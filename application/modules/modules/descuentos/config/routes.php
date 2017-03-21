<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING...
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
$route['descuentos/listar'] = 'descuentos/listar';
$route['descuentos/estado/(:any)'] = 'descuentos/listar_estado/$1';
$route['descuentos/ajax-listar'] = 'descuentos/ajax_listar';
$route['descuentos/ajax-listar-estado'] = 'descuentos/ajax_estado_cuenta';
$route['descuentos/ajax-listar/(:any)'] = 'descuentos/ajax_listar/$1';
$route['descuentos/ocultotabla'] = 'descuentos/ocultotabla';
$route['descuentos/ajax-toggle-colaborador'] = 'descuentos/ajax_toggle_colaborador';
$route['descuentos/exportar'] = 'descuentos/exportar';
$route['descuentos/ajax-descargar'] = 'descuentos/descargar';
$route['descuentos/ajax-guardar-comentario'] = 'descuentos/ajax_guardar_comentario';


//Formulario crear/editar
$route['descuentos/crear'] = 'descuentos/crear';
$route['descuentos/ver/(:any)'] = 'descuentos/crear/$1';
$route['descuentos/ocultoformulario'] = 'descuentos/ocultoformulario';

//Ajax
$route['descuentos/ajax-calcular-capacidad-endeudamiento'] = 'descuentos/ajax_calcular_capacidad_endeudamiento';
$route['descuentos/ajax-guardar-descuento'] = 'descuentos/ajax_guardar_descuento';
$route['descuentos/ajax-seleccionar-descuento'] = 'descuentos/ajax_seleccionar_descuento';

/* End of file routes.php */
/* Location: ./application/config/routes.php */
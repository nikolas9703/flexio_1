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
| This route will tell the Router what URI segments to use if tho- se provided
| in the URL cannot be matched to a valid route.
|
*/

$route['talleres/listar'] = 'talleres/listar';
$route['talleres/crear'] = 'talleres/crear';
$route['talleres/ver/(:any)'] = 'talleres/crear/$1';
$route['talleres/guardar'] = 'talleres/guardar';
//ajax
$route['talleres/ajax-listar'] = 'talleres/ajax_listar';
$route['talleres/ajax-eliminar-colaborador'] = 'talleres/ajax_eliminar_colaborador';
$route['talleres/ajax-guardar-documentos'] = 'talleres/ajax_guardar_documentos';
$route['talleres/ajax-guardar-colaboradores'] = 'talleres/ajax_guardar_colaboradores';
$route['talleres/ajax-guardar-comentario'] = 'talleres/ajax_guardar_comentario';

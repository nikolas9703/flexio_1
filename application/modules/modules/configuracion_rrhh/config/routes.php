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

//configuracion
$route['configuracion_rrhh/listar'] = 'configuracion_rrhh/listar';
$route['configuracion_rrhh/ajax-listar-cargos'] = 'configuracion_rrhh/ajax_listar_cargos';
$route['configuracion_rrhh/ajax-listar-liquidaciones'] = 'configuracion_rrhh/ajax_listar_liquidaciones';
$route['configuracion_rrhh/ajax-lista-cargos'] = 'configuracion_rrhh/ajax_lista_cargos';
$route['configuracion_rrhh/ocultotablacargos'] = 'configuracion_rrhh/ocultotablacargos';
$route['configuracion_rrhh/ajax-duplicar-cargo'] = 'configuracion_rrhh/conf_duplicar_cargo';
$route['configuracion_rrhh/ajax-toggle-cargo'] = 'configuracion_rrhh/conf_toggle_cargo';
$route['configuracion_rrhh/ajax-guardar-cargo'] = 'configuracion_rrhh/conf_guardar_cargo';
$route['configuracion_rrhh/ajax-guardar-liquidaciones'] = 'configuracion_rrhh/conf_guardar_liquidaciones';
$route['configuracion_rrhh/ajax-guardar-departamento'] = 'configuracion_rrhh/conf_guardar_departamento';
$route['configuracion_rrhh/ajax-cambiar-estado-departamento'] = 'configuracion_rrhh/conf_cambiar_estado_departamento';
$route['configuracion_rrhh/ajax-relacionar-departamento-centros'] = 'configuracion_rrhh/conf_relacionar_departamento_centros';
$route['configuracion_rrhh/ajax-guardar-tiempo-contratacion'] = 'configuracion_rrhh/conf_guardar_tiempo_contratacion';
$route['configuracion_rrhh/ajax-eliminar-tiempo-contratacion'] = 'configuracion_rrhh/conf_eliminar_tiempo_contratacion';

$route['configuracion_rrhh/ajax-listar-area-negocio'] = 'configuracion_rrhh/ajax_listar_area_negocio';
$route['configuracion_rrhh/ajax-lista-area-negocio'] = 'configuracion_rrhh/ajax_lista_area_negocio';
$route['configuracion_rrhh/ocultotablacargos'] = 'configuracion_rrhh/ocultotablaareanegocio';
$route['configuracion_rrhh/ajax-toggle-area-negocio'] = 'configuracion_rrhh/conf_toggle_area_negocio';
/* End of file routes.php */
/* Location: ./application/config/routes.php */

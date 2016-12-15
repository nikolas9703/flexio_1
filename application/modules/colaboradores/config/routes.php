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

$route['colaboradores/listar'] = 'colaboradores/listar';
$route['colaboradores/ajax-listar'] = 'colaboradores/ajax_listar';
$route['colaboradores/ajax-listar-recontratacion'] = 'colaboradores/ajax_listar_recontratacion';
$route['colaboradores/ajax-listar/(:any)'] = 'colaboradores/ajax_listar/$1';
$route['colaboradores/ocultotabla'] = 'colaboradores/ocultotabla';
$route['colaboradores/ocultorecontrataciontabla'] = 'colaboradores/ocultorecontrataciontabla';
$route['colaboradores/ajax-toggle-colaborador'] = 'colaboradores/ajax_toggle_colaborador';
$route['colaboradores/exportar'] = 'colaboradores/exportar';

//Formulario crear/editar
$route['colaboradores/crear'] = 'colaboradores/crear';
$route['colaboradores/ver/(:any)'] = 'colaboradores/crear/$1';
$route['colaboradores/recontratacion/(:any)'] = 'colaboradores/crear/$1';
$route['colaboradores/ocultoformulario'] = 'colaboradores/ocultoformulario';

$route['colaboradores/ajax-eliminar-beneficiario'] = 'colaboradores/ajax_eliminar_beneficiario';
$route['colaboradores/ajax-eliminar-dependiente'] = 'colaboradores/ajax_eliminar_dependiente';
$route['colaboradores/ajax-eliminar-familia'] = 'colaboradores/ajax_eliminar_familia';
$route['colaboradores/ajax-eliminar-estudio'] = 'colaboradores/ajax_eliminar_estudio';
$route['colaboradores/ajax-lista-departamentos-asociado-centro'] = 'colaboradores/ajax_lista_departamentos_asociado_centro';
$route['colaboradores/ajax-eliminar-deduccion'] = 'colaboradores/ajax_eliminar_deduccion';
$route['colaboradores/ajax-guardar-seleccion-requisito'] = 'colaboradores/ajax_guardar_seleccion_requisito';
$route['colaboradores/ajax-guardar-fecha-requisito'] = 'colaboradores/ajax_guardar_fecha_requisito';
$route['colaboradores/ajax-subir-documento-requisito'] = 'colaboradores/ajax_subir_documento_requisito';
$route['colaboradores/ajax-eliminar-adjunto-requisito'] = 'colaboradores/ajax_eliminar_adjunto_requisito';
$route['colaboradores/ajax-guardar-evaluacion'] = 'colaboradores/ajax_guardar_evaluacion';
$route['colaboradores/ajax-listar-evaluaciones'] = 'colaboradores/ajax_listar_evaluaciones';
$route['colaboradores/ajax-seleccionar-evaluacion'] = 'colaboradores/ajax_seleccionar_evaluacion';
$route['colaboradores/ajax-lista-items-por-categoria'] = 'colaboradores/ajax_seleccionar_items_segun_categoria';
$route['colaboradores/ajax-guardar-entrega'] = 'colaboradores/ajax_guardar_entrega';
$route['colaboradores/ajax-listar-entrega-inventario'] = 'colaboradores/ajax_listar_entrega_inventario';
$route['colaboradores/ajax-seleccionar-entrega-inventario'] = 'colaboradores/ajax_seleccionar_entrega_inventario';
$route['colaboradores/ajax-colaborador-info'] = 'colaboradores/ajax_colaborador_info';
$route['colaboradores/ajax-guardar-documentos'] = 'colaboradores/ajax_guardar_documentos';
$route['colaboradores/ajax-guardar-comentario'] = 'colaboradores/ajax_guardar_comentario';


/* End of file routes.php */
/* Location: ./application/config/routes.php */

<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

 	$route['comisiones/listar'] 	= 'comisiones/listar';
	$route['comisiones/crear'] 				= 'comisiones/crear';
	$route['comisiones/ver/(:any)'] 		= 'comisiones/editar/$1';
	$route['comisiones/ajax-listar-comisiones'] 	= 'comisiones/ajax_listar_comisiones';
	$route['comisiones/ajax-listar-colaboradores-detalle'] 		= 'comisiones/ajax_listar_colaboradores_detalle';
	$route['comisiones/ajax-listar-colaboradores'] 	= 'comisiones/ajax_listar_colaboradores';

	$route['comisiones/ajax-editar-comision'] 		= 'comisiones/ajax_editar_comision';
	$route['comisiones/ajax-editar-monto'] 				= 'comisiones/ajax_editar_monto';
	$route['comisiones/ajax-por-aprobar'] 				= 'comisiones/ajax_por_aprobar';

	$route['comisiones/ajax-eliminar-colaborador'] 		= 'comisiones/ajax_eliminar_colaborador';
	$route['comisiones/ajax-cargar-codigo'] 			= 'comisiones/ajax_cargar_codigo';
	$route['comisiones/ajax-listar-departamento-x-centro'] = 'comisiones/ajax_listar_departamento_x_centro';
	$route['comisiones/ajax-guardar-comision'] 			   = 'comisiones/ajax_guardar_comision';
	$route['comisiones/ajax-agregar-colaborador'] 		   = 'comisiones/ajax_agregar_colaborador';
	$route['comisiones/ajax-anular-comision'] 			   = 'comisiones/ajax_anular_comision';
    $route['comisiones/ajax-guardar-comentario'] 		   = 'comisiones/ajax_guardar_comentario';

	$route['comisiones/ocultotablacomisiones'] 			= 'comisiones/ocultotablacomisiones';
	$route['comisiones/ocultotablacolaboradores'] 		= 'comisiones/ocultotablacolaboradores';
	$route['comisiones/ocultoformulario'] 				= 'comisiones/ocultoformulario';



/* End of file routes.php */
/* Location: ./application/config/routes.php */

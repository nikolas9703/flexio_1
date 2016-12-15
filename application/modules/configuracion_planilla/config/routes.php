<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 

$route['configuracion_planilla/configuracion'] 			= 'configuracion_planilla/configuracion';
$route['configuracion_planilla/acumulado-constructor/(:any)'] = 'configuracion_planilla/acumulado_constructor/$1';
$route['configuracion_planilla/deduccion-constructor/(:any)'] = 'configuracion_planilla/deduccion_constructor/$1';

 
$route['configuracion_planilla/ajax-listar-diasferiados']  = 'configuracion_planilla/ajax_listar_diasferiados';
$route['configuracion_planilla/ajax-listar-recargos'] = 'configuracion_planilla/ajax_listar_recargos';
$route['configuracion_planilla/ajax-listar-beneficios'] = 'configuracion_planilla/ajax_listar_beneficios';
$route['configuracion_planilla/ajax-listar-deducciones'] = 'configuracion_planilla/ajax_listar_deducciones';
$route['configuracion_planilla/ajax-listar-acumulados'] = 'configuracion_planilla/ajax_listar_acumulados';
$route['configuracion_planilla/ajax-listar-liquidaciones'] = 'configuracion_planilla/ajax_listar_liquidaciones';

$route['configuracion_planilla/ajax-crear-recargo'] = 'configuracion_planilla/ajax_crear_recargo';
$route['configuracion_planilla/ajax-crear-diaferiado'] 	= 'configuracion_planilla/ajax_crear_diaferiado';
$route['configuracion_planilla/ajax-crear-beneficio'] 	= 'configuracion_planilla/ajax_crear_beneficio';
$route['configuracion_planilla/ajax-crear-deduccion'] 	= 'configuracion_planilla/ajax_crear_deduccion';
$route['configuracion_planilla/ajax-crear-acumulado'] 	= 'configuracion_planilla/ajax_crear_acumulado';
$route['configuracion_planilla/ajax-crear-liquidacion'] 	= 'configuracion_planilla/ajax_crear_liquidacion';

$route['configuracion_planilla/ajax-eliminar-constructor-acumulado'] 	= 'configuracion_planilla/ajax_eliminar_constructor_acumulado';
$route['configuracion_planilla/ajax-eliminar-pago-acumulado'] 	= 'configuracion_planilla/ajax_eliminar_pago_acumulado';
$route['configuracion_planilla/ajax-eliminar-liquidacion'] 	= 'configuracion_planilla/ajax_eliminar_liquidacion';

 
$route['configuracion_planilla/ajax-duplicar-diasferiados'] = 'configuracion_planilla/ajax_duplicar_diasferiados';
 
$route['configuracion_planilla/ocultotablarecargos'] 		= 'configuracion_planilla/ocultotablarecargos';
$route['configuracion_planilla/ocultotabladiasferiados']   = 'configuracion_planilla/ocultotabladiasferiados';
$route['configuracion_planilla/ocultotablabeneficios']   = 'configuracion_planilla/ocultotablabeneficios';
$route['configuracion_planilla/ocultotabladeducciones']   = 'configuracion_planilla/ocultotabladeducciones';
$route['configuracion_planilla/ocultotablaacumulados']   = 'configuracion_planilla/ocultotablaacumulados';
$route['configuracion_planilla/ocultotablaliquidacion']   = 'configuracion_planilla/ocultotablaliquidacion';

$route['configuracion_planilla/ajax-get-liquidacion'] 	= 'configuracion_planilla/ajax_get_liquidacion';

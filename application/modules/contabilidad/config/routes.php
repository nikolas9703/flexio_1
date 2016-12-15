<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$route['contabilidad/listar'] = 'contabilidad/listar';
$route['contabilidad/listar_centros_contables'] = 'contabilidad/listar_centros_contables';
$route['contabilidad/ajax-listar'] = 'contabilidad/ajax_listar';
$route['contabilidad/ocultotabla'] = 'contabilidad/ocultotabla';

//Formulario crear/editar
$route['contabilidad/crear'] = 'contabilidad/crear';
$route['contabilidad/ver/(:any)'] = 'contabilidad/editar/$1';
$route['contabilidad/ocultoformulario'] = 'contabilidad/ocultoformulario';

//configuracion
$route['contabilidad/configuracion'] = 'contabilidad/configuracion';

$route['contabilidad/ajax-cargar-plan-contable'] = 'contabilidad/ajax_cargar_plan_contable';
$route['contabilidad/ajax-listar-cuentas'] = 'contabilidad/ajax_listar_cuentas';
$route['contabilidad/ajax-codigo'] = 'contabilidad/ajax_codigo';
$route['contabilidad/ajax-guardarCuenta'] = 'contabilidad/ajax_guardarCuenta';
$route['contabilidad/ajax-listar-centros-contable'] = 'contabilidad/ajax_listar_centros_contable';
$route['contabilidad/ajax-guardarCentro'] = 'contabilidad/ajax_guardarCentro';
$route['contabilidad/ajax-buscar-centro'] = 'contabilidad/ajax_buscar_centro';
$route['contabilidad/ajax-buscar-cuenta'] = 'contabilidad/ajax_buscar_cuenta';
$route['contabilidad/ajax-cambiar-estado-centro-contable'] = 'contabilidad/ajax_cambiar_estado_centro_contable';
$route['contabilidad/ajax-cambiar-estado-cuenta-contable'] = 'contabilidad/ajax_cambiar_estado_cuenta_contable';
$route['contabilidad/ajax-listar-impuestos'] = 'contabilidad/ajax_listar_impuestos';
$route['contabilidad/ajax-guardar-impuesto'] = 'contabilidad/ajax_guardar_impuesto';
$route['contabilidad/ajax-historial/(:any)'] = 'contabilidad/ajax_historial/$1';
$route['contabilidad/ajax-cambiar-estado-impuesto'] = 'contabilidad/ajax_cambiar_estado_impuesto';
$route['contabilidad/ajax-lista-centros-contables'] = 'contabilidad/ajax_lista_centros_contables';
$route['contabilidad/ajax-get-impuesto-exonerado'] = 'contabilidad/ajax_get_impuesto_exonerado';

/* End of file routes.php */
/* Location: ./application/config/routes.php */

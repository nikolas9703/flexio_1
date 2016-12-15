<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//configuracion
$route['configuracion_contabilidad'] = 'configuracion_contabilidad/index';


//ajax request
$route['configuracion_contabilidad/ajax-cuenta-activo'] = 'configuracion_contabilidad/ajax_cuenta_activo';
$route['configuracion_contabilidad/ajax-guardar-por-cobrar'] = 'configuracion_contabilidad/ajax_guardar_por_cobrar';
$route['configuracion_contabilidad/ajax-get-cuenta-por-cobrar'] = 'configuracion_contabilidad/ajax_get_cuenta_por_cobrar';
$route['configuracion_contabilidad/ajax-eliminar-cuenta-cobrar'] = 'configuracion_contabilidad/ajax_eliminar_cuenta_cobrar';
$route['configuracion_contabilidad/ajax-seleccionar-caja-menuda'] = 'configuracion_contabilidad/ajax_seleccionar_caja_menuda';
$route['configuracion_contabilidad/ajax-guardar-cuenta-caja-menuda'] = 'configuracion_contabilidad/ajax_guardar_cuenta_caja_menuda';
$route['configuracion_contabilidad/ajax-eliminar-cuenta-caja-menuda'] = 'configuracion_contabilidad/ajax_eliminar_cuenta_caja_menuda';
$route['configuracion_contabilidad/ajax-cuenta-pasivo'] = 'configuracion_contabilidad/ajax_cuenta_pasivo';
$route['configuracion_contabilidad/ajax-get-cuenta-por-pagar'] = 'configuracion_contabilidad/ajax_get_cuenta_por_pagar';
$route['configuracion_contabilidad/ajax-guardar-cuenta-proveedor-por-pagar'] = 'configuracion_contabilidad/ajax_guardar_cuenta_proveedor_por_pagar';
$route['configuracion_contabilidad/ajax-eliminar-cuenta-por-pagar'] = 'configuracion_contabilidad/ajax_eliminar_cuenta_por_pagar';
$route['configuracion_contabilidad/ajax-cuenta-banco'] = 'configuracion_contabilidad/ajax_cuenta_banco';
$route['configuracion_contabilidad/ajax-guardar-cuenta-banco'] = 'configuracion_contabilidad/ajax_guardar_cuenta_banco';
$route['configuracion_contabilidad/ajax-eliminar-cuenta-banco'] = 'configuracion_contabilidad/ajax_eliminar_cuenta_banco';
$route['configuracion_contabilidad/ajax-get-cuenta-abono'] = 'configuracion_contabilidad/ajax_get_cuenta_abono';
$route['configuracion_contabilidad/ajax-guardar-cuenta-abono'] = 'configuracion_contabilidad/ajax_guardar_cuenta_abono';
$route['configuracion_contabilidad/ajax-eliminar-cuenta-abono'] = 'configuracion_contabilidad/ajax_eliminar_cuenta_abono';
$route['configuracion_contabilidad/ajax-get-cuenta-inventario'] = 'configuracion_contabilidad/ajax_get_cuenta_inventario';
$route['configuracion_contabilidad/ajax-guardar-cuenta-inventario'] = 'configuracion_contabilidad/ajax_guardar_cuenta_inventario';
$route['configuracion_contabilidad/ajax-eliminar-cuenta-inventario'] = 'configuracion_contabilidad/ajax_eliminar_cuenta_inventario';

$route['configuracion_contabilidad/ajax-catalogo-cuentas'] = 'configuracion_contabilidad/ajax_catalogo_cuentas';

$route['configuracion_contabilidad/ajax-cuenta-planilla'] = 'configuracion_contabilidad/ajax_cuenta_planilla';
$route['configuracion_contabilidad/ajax-eliminar-cuenta-planilla'] = 'configuracion_contabilidad/ajax_eliminar_cuenta_planilla';
$route['configuracion_contabilidad/ajax-guardar-planilla'] = 'configuracion_contabilidad/ajax_guardar_planilla';
$route['configuracion_contabilidad/ajax-get-cuenta-planilla'] = 'configuracion_contabilidad/ajax_get_cuenta_planilla';
//configuracion_contabilidad/ocultotablacajamenuda

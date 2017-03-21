<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');


//Formulario crear/editar
$route['movimiento_monetario/crear_recibos'] = 'movimiento_monetario/crear_recibos';
$route['movimiento_monetario/crear_retiros'] = 'movimiento_monetario/crear_retiros';
$route['movimiento_monetario/ver/(:any)'] = 'movimiento_monetario/ver_recibos/$1';
$route['movimiento_monetario/ver_retiros/(:any)'] = 'movimiento_monetario/ver_retiros/$1';
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

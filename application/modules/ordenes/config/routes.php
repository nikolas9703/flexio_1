<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$route['ordenes/listar']        = 'ordenes/listar';
$route['ordenes/historial']        = 'ordenes/historial';
$route['ordenes/ocultotabla']   = 'ordenes/ocultotabla';
$route['ordenes/ocultoformulariocomentarios']   = 'ordenes/ocultoformulariocomentarios';

//Ajax
$route['ordenes/ajax-exportar']             = 'ordenes/ajax_exportar';
$route['ordenes/ajax-listar']               = 'ordenes/ajax_listar';
$route['ordenes/ajax-obtener-item']         = 'ordenes/ajax_obtener_item';
$route['ordenes/ajax-obtener-pedido']       = 'ordenes/ajax_obtener_pedido';
$route['ordenes/ajax-obtener-impuesto']     = 'ordenes/ajax_obtener_impuesto';
$route['ordenes/ajax-obtener-orden-item']   = 'ordenes/ajax_obtener_orden_item';
$route['ordenes/ajax-obtener-resto-items']  = 'ordenes/ajax_obtener_resto_items';
$route['ordenes/ajax-anular']               = 'ordenes/ajax_anular';
$route['ordenes/ajax-eliminar-orden-item']  = 'ordenes/ajax_eliminar_orden_item';
$route['ordenes/ajax-reabrir']              = 'ordenes/ajax_reabrir';
$route['ordenes/ajax-guardar-documentos'] = 'ordenes/ajax_guardar_documentos';
$route['ordenes/ajax-enviar-correo'] = 'ordenes/ajax_enviar_correo';
$route['ordenes/ajax-get-empezable'] = 'ordenes/ajax_get_empezable';


//Formulario crear/editar
$route['ordenes/crear']         = 'ordenes/crear';
$route['ordenes/ver/(:any)']    = 'ordenes/editar/$1';
$route['ordenes/ajax-guardar-comentario']    = 'ordenes/ajax_guardar_comentario';

//Otros

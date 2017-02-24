<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$route['pedidos/listar']        = 'pedidos/listar';
$route['pedidos/ocultotabla']   = 'pedidos/ocultotabla';
$route['pedidos/ocultoformulariocomentariosr']              = 'pedidos/ocultoformulariocomentarios';

//Ajax
$route['pedidos/ajax-exportar']             = 'pedidos/ajax_exportar';
$route['pedidos/ajax-listar']               = 'pedidos/ajax_listar';
$route['pedidos/ajax-obtener-item']         = 'pedidos/ajax_obtener_item';
$route['pedidos/ajax-obtener-pedido-item']  = 'pedidos/ajax_obtener_pedido_item';
$route['pedidos/ajax-anular']               = 'pedidos/ajax_anular';
$route['pedidos/ajax-eliminar-pedido-item'] = 'pedidos/ajax_eliminar_pedido_item';
$route['pedidos/ajax-reabrir']              = 'pedidos/ajax_reabrir';
$route['pedidos/ajax-guardar-comentario'] 	= 'pedidos/ajax_guardar_comentario';
$route['pedidos/ajax-guardar-documentos']   = 'pedidos/ajax_guardar_documentos';
$route['pedidos/ajax-validar-pedido']       = 'pedidos/ajax_validar_pedido';


//Formulario crear/editar
$route['pedidos/crear']         = 'pedidos/crear';
$route['pedidos/ver/(:any)']    = 'pedidos/editar/$1';
$route['pedidos/historial/(:any)']    = 'pedidos/historial/$1';
//Otros

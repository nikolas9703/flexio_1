<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$route['politicas/listar/(:any)']        = 'politicas/listar/$1';
$route['politicas/ajax-listar']               = 'politicas/ajax_listar';
$route['politicas/ajax-guardar-politica'] 	= 'politicas/ajax_guardar_politica';
$route['politicas/ajax-get-politica'] 	= 'politicas/ajax_get_politica';


//FIN
$route['politicas/ocultotabla']   = 'politicas/ocultotabla';
$route['politicas/ocultoformulariocomentariosr']              = 'politicas/ocultoformulariocomentarios';

//Ajax
$route['politicas/ajax-exportar']             = 'politicas/ajax_exportar';
$route['politicas/ajax-obtener-item']         = 'politicas/ajax_obtener_item';
$route['politicas/ajax-obtener-pedido-item']  = 'politicas/ajax_obtener_pedido_item';
$route['politicas/ajax-anular']               = 'politicas/ajax_anular';
$route['politicas/ajax-eliminar-pedido-item'] = 'politicas/ajax_eliminar_pedido_item';
$route['politicas/ajax-reabrir']              = 'politicas/ajax_reabrir';
$route['politicas/ajax-guardar-documentos'] = 'politicas/ajax_guardar_documentos';


//Formulario crear/editar
$route['politicas/crear']         = 'pedidos/crear';
$route['politicas/ver/(:any)']    = 'pedidos/editar/$1';

//Otros

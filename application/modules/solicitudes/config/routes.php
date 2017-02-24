<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$route['solicitudes/listar']        = 'solicitudes/listar';
$route['solicitudes/ocultotabla']   = 'solicitudes/ocultotabla';
$route['solicitudes/ocultoformulario']   = 'solicitudes/ocultoformulario';
$route['solicitudes/editar participación']   = 'solicitudes/editar_participacion';
$route['solicitudes/editar asignación']   = 'solicitudes/editar_asignacion';

//Ajax
$route['solicitudes/ajax-listar']        = 'solicitudes/ajax_listar';
$route['solicitudes/ajax-get-clientes']        = 'solicitudes/ajax_get_clientes';
$route['solicitudes/ajax-get-cliente']        = 'solicitudes/ajax_get_cliente';
$route['solicitudes/ajax-get-planes']        = 'solicitudes/ajax_get_planes';
$route['solicitudes/ajax-get-coberturas']        = 'solicitudes/ajax_get_coberturas';
$route['solicitudes/ajax-get-comision']        = 'solicitudes/ajax_get_comision';
$route['solicitudes/ajax-get-porcentaje']        = 'solicitudes/ajax_get_porcentaje';
$route['solicitudes/ajax-get-centro-facturable']        = 'solicitudes/ajax_get_centro_facturable';
$route['solicitudes/ajax-get-direccion']        = 'solicitudes/ajax_get_direccion';
$route['solicitudes/ajax-guardar-documentos']  = 'solicitudes/ajax_guardar_documentos';

//Formulario crear/editar
$route['solicitudes/crear']         = 'solicitudes/crear';
$route['solicitudes/ver/(:any)']    = 'solicitudes/editar/$1';
$route['solicitudes/editar/(:any)']         = 'solicitudes/editar/$1';

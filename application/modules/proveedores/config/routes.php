<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$route['proveedores/listar']        = 'proveedores/listar';
$route['proveedores/ocultotabla']   = 'proveedores/ocultotabla';

//Ajax
$route['proveedores/ajax-exportar']             = 'proveedores/ajax_exportar';
$route['proveedores/ajax-listar']               = 'proveedores/ajax_listar';
$route['proveedores/ajax-get-proveedor']        = 'proveedores/ajax_get_proveedor';
$route['proveedores/ajax-get-proveedores'] = 'proveedores/ajax_get_proveedores';
$route['proveedores/ajax-get-montos'] = 'proveedores/ajax_get_montos';
$route['proveedores/ajax-valida-identificacion'] = 'proveedores/ajax_valida_proveedor';

//Formulario crear/editar
$route['proveedores/crear']         = 'proveedores/crear';
$route['proveedores/ver/(:any)']    = 'proveedores/editar/$1';

//Otros
$route['ordenes_compras/crear/(:any)']  = 'ordenes_compras/crear/$1';
$route['facturas/crear/(:any)']         = 'facturas/crear/$1';
$route['pagos/crear/(:any)']            = 'pagos/crear/$1';
$route['proveedores/ajax-guardar-documentos'] = 'proveedores/ajax_guardar_documentos';
$route['proveedores/ajax-guardar-comentario'] = 'proveedores/ajax_guardar_comentario';

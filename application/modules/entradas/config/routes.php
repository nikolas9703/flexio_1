<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$route['entradas/listar']           = 'entradas/listar';
$route['entradas/ocultotabla']      = 'entradas/ocultotabla';

//Ajax
$route['entradas/ajax-exportar'] = 'entradas/ajax_exportar';
$route['entradas/ajax-listar'] = 'entradas/ajax_listar';
$route['entradas/ajax-listar-historial-item'] = 'entradas/ajax_listar_historial_item';
$route['entradas/ajax-get-origenes'] = 'entradas/ajax_get_origenes';

//Formulario crear/editar
$route['entradas/crear']            = 'entradas/crear';
$route['entradas/ver/(:any)']       = 'entradas/editar/$1';

//Otros

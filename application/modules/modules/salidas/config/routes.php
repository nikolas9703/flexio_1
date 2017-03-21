<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$route['salidas/listar']           = 'salidas/listar';
$route['salidas/ocultotabla']      = 'salidas/ocultotabla';

//Ajax
$route['salidas/ajax-exportar'] = 'salidas/ajax_exportar';
$route['salidas/ajax-listar'] = 'salidas/ajax_listar';
$route['salidas/ajax-listar-historial-item'] = 'salidas/ajax_listar_historial_item';
$route['salidas/ajax-get-destinos'] = 'salidas/ajax_get_destinos';

//Formulario crear/editar
$route['salidas/ver/(:any)']       = 'salidas/editar/$1';

//Otros

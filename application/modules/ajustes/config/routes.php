<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$route['ajustes/listar']        = 'ajustes/listar';
$route['ajustes/ocultotabla']   = 'ajustes/ocultotabla';

//Ajax
$route['ajustes/ajax-exportar']             = 'ajustes/ajax_exportar';
$route['ajustes/ajax-listar']               = 'ajustes/ajax_listar';
$route['ajustes/ajax-get-item']             = 'ajustes/ajax_get_item';
$route['ajustes/ajax-get-articulos']        = 'ajustes/ajax_get_articulos';

//Formulario crear/editar
$route['ajustes/crear']         = 'ajustes/crear';
$route['ajustes/ver/(:any)']    = 'ajustes/editar/$1';

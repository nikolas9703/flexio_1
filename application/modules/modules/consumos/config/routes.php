<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$route['consumos/listar']        = 'consumos/listar';
$route['consumos/ocultotabla']   = 'consumos/ocultotabla';

//Ajax
$route['consumos/ajax-exportar']            = 'consumos/ajax_exportar';
$route['consumos/ajax-listar']              = 'consumos/ajax_listar';
$route['consumos/ajax-lista-departamentos-asociado-centros']              = 'consumos/ajax_lista_departamentos_asociado_centro';
$route['consumos/ajax-get-item']            = 'consumos/ajax_get_item';
$route['consumos/ajax-get-unidad']          = 'consumos/ajax_get_unidad';
$route['consumos/ajax-get-items']           = 'consumos/ajax_get_items';
$route['consumos/ajax-delete-item']         = 'consumos/ajax_delete_item';

//Formulario crear/editar
$route['consumos/crear']         = 'consumos/crear';
$route['consumos/ver/(:any)']    = 'consumos/editar/$1';

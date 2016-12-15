<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$route['bodegas/listar']        = 'bodegas/listar';
$route['bodegas/ocultotabla']   = 'bodegas/ocultotabla';

//Ajax
$route['bodegas/ajax-exportar']             = 'bodegas/ajax_exportar';
$route['bodegas/ajax-listar']               = 'bodegas/ajax_listar';
$route['bodegas/ajax-listar-bodegas']       = 'bodegas/ajax_listar_bodegas';
$route['bodegas/ajax-listar-items']         = 'bodegas/ajax_listar_items';
$route['bodegas/ajax-cambiar-estado']       = 'bodegas/ajax_cambiar_estado';

//Formulario crear/editar
$route['bodegas/crear']         = 'bodegas/crear';
$route['bodegas/ver/(:any)']    = 'bodegas/editar/$1';

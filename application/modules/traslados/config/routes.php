<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$route['traslados/listar']        = 'traslados/listar';
$route['traslados/ocultotabla']   = 'traslados/ocultotabla';

//Ajax
$route['traslados/ajax-exportar']             = 'traslados/ajax_exportar';
$route['traslados/ajax-listar']               = 'traslados/ajax_listar';

//Formulario crear/editar
$route['traslados/crear']         = 'traslados/crear';
$route['traslados/ver/(:any)']    = 'traslados/editar/$1';

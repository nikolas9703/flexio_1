<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$route['acreedores/listar'] = 'acreedores/listar';
$route['acreedores/ocultotabla'] = 'acreedores/ocultotabla';
$route['acreedores/exportar']   = 'acreedores/exportar';
$route['acreedores/reporte/(:any)'] = 'acreedores/listar_reporte/$1';
//Ajax
$route['acreedores/ajax-exportar']              = 'acreedores/ajax_exportar';
$route['acreedores/ajax-listar']                = 'acreedores/ajax_listar';
$route['acreedores/ajax-listar-colaboradores']  = 'acreedores/ajax_listar_colaboradores';
$route['acreedores/ajax-get-acreedor']          = 'acreedores/ajax_get_acreedor';
$route['acreedores/ajax-listar-reporte'] = 'acreedores/ajax_reporte_pagos';
$route['acreedores/ajax-guardar-comentario'] = 'acreedores/ajax_guardar_comentario';
//Formulario crear/editar
$route['acreedores/crear']         = 'acreedores/crear';
$route['acreedores/ver/(:any)']    = 'acreedores/editar/$1';

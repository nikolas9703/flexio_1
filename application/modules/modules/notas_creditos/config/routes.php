<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

$route['notas_creditos/listar'] = 'notas_creditos/listar';
$route['notas_creditos/crear'] = 'notas_creditos/crear';
$route['notas_creditos/ver/(:any)'] = 'notas_creditos/ver/$1';
//ajax
$route['notas_creditos/ajax-listar'] = 'notas_creditos/ajax_listar';
$route['notas_creditos/ajax-guardar-comentario'] = 'notas_creditos/ajax_guardar_comentario';

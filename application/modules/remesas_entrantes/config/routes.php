<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 01/03/16
 * Time: 03:16 PM
 */
$route['remesas_entrantes/listar'] = 'remesas_entrantes/listar';
$route['remesas_entrantes/crear'] = 'remesas_entrantes/crear';
$route['remesas_entrantes/ver/(:any)'] = 'remesas_entrantes/ver/$1';
$route['remesas_entrantes/editar/(:any)']         = 'remesas_entrantes/editar/$1';

$route['remesas_entrantes/ajax-listar-remesas-entrantes'] = 'remesas_entrantes/ajax_listar_remesas';
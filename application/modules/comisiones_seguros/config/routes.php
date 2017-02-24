<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 01/03/16
 * Time: 03:16 PM
 */
$route['comisiones_seguros/listar'] = 'comisiones_seguros/listar';
$route['comisiones_seguros/ver/(:any)'] = 'comisiones_seguros/ver/$1';

$route['comisiones_seguros/ajax-listar'] = 'comisiones_seguros/ajax_listar';
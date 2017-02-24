<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 01/03/16
 * Time: 03:16 PM
 */
$route['remesas/listar'] = 'remesas/listar';
$route['remesas/crear'] = 'remesas/crear';
$route['remesas/ver/(:any)'] = 'remesas/ver/$1';

$route['remesas/ajax-listar-remesas'] = 'remesas/ajax_listar_remesas';
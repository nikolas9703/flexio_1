<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 01/03/16
 * Time: 03:16 PM
 */
$route['endosos/listar'] = 'endosos/listar';
$route['endosos/crear'] = 'endosos/crear';
$route['endosos/ver/(:any)'] = 'endosos/ver/$1';

$route['endosos/ajax-listar-endosos'] = 'endosos/ajax_listar_endosos';
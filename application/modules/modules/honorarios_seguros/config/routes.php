<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 01/03/16
 * Time: 03:16 PM
 */
$route['honorarios_seguros/listar'] = 'honorarios_seguros/listar';
/*$route['honorarios_seguros/crear'] = 'honorarios_seguros/crear';
$route['honorarios_seguros/ver/(:any)'] = 'honorarios_seguros/ver/$1';
$route['honorarios_seguros/editar/(:any)']         = 'honorarios_seguros/editar/$1';*/

$route['honorarios_seguros/ajax-listar-tabla'] = 'honorarios_seguros/ajax_listar_honorarios';
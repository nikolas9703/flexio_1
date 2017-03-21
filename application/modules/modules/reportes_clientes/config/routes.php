<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Ivan Cubilla
 * Date: 1/12/16
 * Time: 11:44 AM
 */
//$route['reportes_financieros/reporte/estado_de_cuenta_de_cliente'] = 'reportes_financieros/reporte/estado_de_cuenta_de_cliente';
$route['reportes_financieros/reporte/estado_de_cuenta_de_cliente?modulo=ventas'] = 'reportes_financieros/reporte/estado_de_cuenta_de_cliente?modulo=ventas';
$route['reportes_financieros/reporte/cuenta_por_cobrar_por_antiguedad?modulo=ventas'] = 'reportes_financieros/reporte/cuenta_por_cobrar_por_antiguedad?modulo=ventas';
$route['reportes_financieros/reporte/reporte_caja?modulo=ventas'] = 'reportes_financieros/reporte/reporte_caja?modulo=ventas';
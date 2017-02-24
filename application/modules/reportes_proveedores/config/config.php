<?php defined('BASEPATH') || exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Ivan Cubilla
 * Date: 2/12/16
 * Time: 9:30 AM
 */
$config['modulo_config'] = array(
    'nombre'        => 'Reportes de Proveedores',
    'descripcion'	=> 'Modulo para Administracion de Reportes de Proveedores.',
    'autor'			=> 'Pensanomica Team',
    'icono'			=> 'fa-page',
    'version'		=> '1.0',
    'tipo'			=> 'addon',
    'grupo'			=> 'Reportes',
    'agrupador'		=> array(
        'Compras' => array(
            "grupo_orden" => 11
        ),
    ),
    'menu' => array(
        array(
            'nombre' => 'Estado de Cuenta de Proveedor',
            'url' => 'reportes_financieros/reporte/estado_cuenta_proveedor?modulo=compras',
            'orden' => 1
        ),
        array(
            'nombre' => 'Cuentas por pagar por antigüedad',
            'url' => 'reportes_financieros/reporte/cuenta_por_pagar_por_antiguedad?modulo=compras',
            'orden' => 2
        ),
        array(
            'nombre' => 'Reporte de compras',
            'url' => 'reportes_financieros/reporte/costo_por_centro_compras?modulo=compras',
            'orden' => 4
        ),
        array(
            'nombre' => 'Reporte de caja',
            'url' => 'reportes_financieros/reporte/reporte_caja?modulo=compras',
            'orden' => 3
        ),
        array(
            'nombre' => 'Reporte de retención de I.T.B.M.S. por proveedor',
            'url' => 'reportes_financieros/reporte/impuestos_sobre_itbms?modulo=compras',
            'orden' => 5
        )
    ),
    'prefijo'		=> 'repro',
    'permisos'      => array(
        'acceso'    => 'Acceso'
    )
);

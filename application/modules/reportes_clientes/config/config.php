<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre'        => 'Reportes de Clientes',
    'descripcion'	=> 'Modulo para Administracion de Reportes de clientes.',
    'autor'			=> 'Pensanomica Team',
    'icono'			=> 'fa-page',
    'version'		=> '1.0',
    'tipo'			=> 'addon', // core, addon
    'grupo'			=> 'Reportes',
    'agrupador'		=> array(
        'Ventas' => array(
            "grupo_orden" => 9
        ),
        'Alquileres' => array(
            "grupo_orden" => 9
        ),
        'Seguros' => array(
            "grupo_orden" => 7
        ),
    ),
    'menu' => array(
        array(
            'nombre' => 'Estado de Cuenta de Clientes',
            'url' => 'reportes_financieros/reporte/estado_de_cuenta_de_cliente?modulo=ventas',
            'orden' => 1
        ),
        array(
            'nombre' => 'Cuentas por cobrar por antigüedad',
            'url' => 'reportes_financieros/reporte/cuenta_por_cobrar_por_antiguedad?modulo=ventas',
            'orden' => 2
        ),
        array(
            'nombre' => 'Reporte de cajas',
            'url' => 'reportes_financieros/reporte/reporte_caja?modulo=ventas',
            'orden' => 3
        )
    ),
    'prefijo'		=> 'recl',
    'permisos'      => array(
        'acceso'    => 'Acceso'
    )
);

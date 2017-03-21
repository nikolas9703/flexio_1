<?php

defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre' => 'Facturas',
    'descripcion' => 'Modulo para Administracion de facturas.',
    'autor' => 'Pensanomica Team',
    'icono' => 'fa-line-chart',
    'version' => '1.0',
    'tipo' => 'addon', // core, addon
    'grupo' => 'Facturas de ventas',
    'agrupador' => array(
        'Ventas' => array(
            'grupo_orden' => 0,
        ),
        'Alquileres' => array(
            'grupo_orden' => 5,
        ),
    ),
    'prefijo' => 'fac',
    'menu' => array(
        'nombre' => 'Facturas de ventas',
        'url' => 'facturas/listar',
        'orden' => 5,
    ),
    'permisos' => array(
        'acceso' => 'Acceso',
        'listar__ver' => 'Listar',
        'editarPrecioFacturaVenta' => 'Editar Precio'
    ),
);

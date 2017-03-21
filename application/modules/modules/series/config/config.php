<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre' => 'Seriales',
    'descripcion' => 'Modulo para Administracion de Seriales.',
    'autor' => 'Pensanomica Team',
    'icono' => 'fa-cubes',
    'version' => '1.0',
    'tipo' => 'addon', // core, addon
    'grupo' => 'Inventario',
    'agrupador' => [
        'Inventario' => [
            "grupo_orden" => 0
		],
    ],
    'prefijo' => 'ser',
    'menu' => [
        'nombre' => 'Series' ,
        'url' => 'series/listar',
        'orden' => 4
    ],
    'permisos'  => [
        'acceso' => 'Acceso',
        'listar__ver' => 'Listar'
    ]
);

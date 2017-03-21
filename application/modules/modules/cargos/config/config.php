<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre' => 'Cargos',
    'descripcion' => 'Modulo para Administracion de cargos de contratos de alquiler.',
    'autor' => 'Pensanomica Team',
    'icono' => 'fa-dollar ',
    'version' => '1.0',
    'tipo' => 'addon', // core, addon
    'grupo' => 'Alquileres',
    'agrupador'		=> array(
        'Alquileres' => array(
            "grupo_orden" => 1
        ),
     ),
    'menu' => array(
        'nombre' => 'Cargos',
        'url' => 'cargos/listar',
        'orden' => 1
    ),
    'prefijo' => 'carg',
    'permisos' => array(
        'acceso' => 'Acceso'
    )
);

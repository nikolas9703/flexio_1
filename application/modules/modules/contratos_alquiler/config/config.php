<?php

defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre' => 'Contratos de alquiler',
    'descripcion' => 'Modulo para Administración de contratos de alquiler.',
    'autor' => 'Pensanomica Team',
    'icono' => 'fa-car ',
    'version' => '1.0',
    'tipo' => 'addon', // core, addon
    'grupo' => 'Alquileres',
    //'agrupador' => 'Alquileres',
    'agrupador'		=> array(
        'Alquileres' => array(
            "grupo_orden" => 1
        ),
     ),
    'menu' => array(
        'nombre' => 'Contratos de alquiler',
        'url' => 'contratos_alquiler/listar',
        'orden' => 1
    ),
    'prefijo' => 'conalq',
    'permisos' => array(
        'acceso' => 'Acceso'
    )
);

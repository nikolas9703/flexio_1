<?php

defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre' => 'Entregas de alquiler',
    'descripcion' => 'Modulo para Administración de entregas de alquiler.',
    'autor' => 'Pensanomica Team',
    'icono' => 'fa-car ',
    'version' => '1.0',
    'tipo' => 'addon', // core, addon
    'grupo' => 'Alquileres',
    //'agrupador' => 'Alquileres',
    'agrupador'		=> array(
        'Alquileres' => array(
            "grupo_orden" => 2
        ),
    ),
    'menu' => array(
        'nombre' => 'Entregas',
        'url' => 'entregas_alquiler/listar',
        'orden' => 2
    ),
    'prefijo' => 'entalq',
    'permisos' => array(
        'acceso' => 'Acceso'
    )
);

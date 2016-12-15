<?php

defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre' => 'Devoluciones de alquiler',
    'descripcion' => 'Modulo para Administración de devoluciones de alquiler.',
    'autor' => 'Pensanomica Team',
    'icono' => 'fa-car ',
    'version' => '1.0',
    'tipo' => 'addon', // core, addon
    'grupo' => 'Alquileres',
    //'agrupador' => 'Alquileres',
    'agrupador'		=> array(
        'Alquileres' => array(
            "grupo_orden" => 3
        ),
    ),
    'menu' => array(
        'nombre' => 'Retornos',
        'url' => 'devoluciones_alquiler/listar',
        'orden' => 3
    ),
    'prefijo' => 'devalq',
    'permisos' => array(
        'acceso' => 'Acceso'
    )
);

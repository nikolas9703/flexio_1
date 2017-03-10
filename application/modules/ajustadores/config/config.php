<?php

defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre' => 'Ajustadores',
    'descripcion' => 'Ajustadores',
    'autor' => 'Pensanomica Team',
    'icono' => 'fa-unlock-alt',
    'version' => '1.0',
    'tipo' => 'addon', // core, addon
    'grupo' => 'Administración',
    'agrupador' => array(
        'Seguros' => array(
            'grupo_orden' => 9
        ),
    ),
    'permisos' => array(
        'acceso' => 'Acceso'
    ),
    'prefijo' => 'sol',
    'menu' => array(
        'nombre' => 'Ajustadores',
        'url' => 'ajustadores/listar',
        'orden' => 1
    ),
);

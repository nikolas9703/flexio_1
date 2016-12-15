<?php

defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre' => 'Oportunidades',
    'descripcion' => 'Modulo para Administración de contratos de oportunidades.',
    'autor' => 'Pensanomica Team',
    'icono' => 'fa-line-chart',
    'version' => '1.0',
    'tipo' => 'addon', // core, addon
    'grupo' => 'Oportunidades',
    'agrupador' => 'Ventas',
    'menu' => array(
        'nombre' => 'Oportunidades',
        'url' => 'oportunidades/listar',
        'orden' => 5
    ),
    'prefijo' => 'conalq',
    'permisos' => array(
        'acceso' => 'Acceso'
    )
);

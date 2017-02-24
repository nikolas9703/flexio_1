<?php

defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre' => 'Cotizaciones de alquiler',
    'descripcion' => 'Modulo para Administración de cotizaciones de alquiler.',
    'autor' => 'Pensanomica Team',
    'icono' => 'fa-line-chart',
    'version' => '1.0',
    'tipo' => 'addon', // core, addon
    'grupo' => 'Alquileres',//menu izquierdo
    'agrupador' => 'Alquileres',//menu superior
    'menu' => array(
        'nombre' => 'Cotizaciones de alquileres',
        'url' => 'cotizaciones_alquiler/listar',
        'orden' => 0
    ),
    'prefijo' => 'cotalq',
    'permisos' => array(
        'acceso' => 'Acceso',
        'editarPrecioUnidad_adicional' => 'Editar Precio Unidad'
    )
);

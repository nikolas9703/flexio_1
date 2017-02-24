<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre'        => 'Pagos Seguros',
    'descripcion'   => 'Modulo para la administración de pagos',
    'autor'         => 'Pensanomica Team',
    'icono'         => 'fa-line-chart',
    'version'       => '1.0',
    'tipo'          => 'addon', // core, addon
    'grupo'         => 'Pagos',
    'agrupador'     => array(
        'Seguros' => array(
            "grupo_orden" => 4
        ),
    ),
    'prefijo'       => 'pag',
    'menu' => array(
        'nombre' =>'Pagos' ,
        'url' => 'pagos/listar/',
        'orden'=> 7
    ),
        'permisos'      => array(
        'acceso' => 'Acceso'
             //  'crear'  => 'Crear'
    )
);
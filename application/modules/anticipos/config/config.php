<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre'        => 'Anticipos',
    'descripcion'	=> 'Modulo para Administracion de Anticipos.',
    'autor'         => 'Pensanomica Team',
    'icono'         => 'fa-shopping-cart',
    'version'       => '1.0',
    'tipo'          => 'addon', // core, addon
    'grupo'         => 'Anticipos',
    'agrupador'     => [
        'Compras' => array(
           "grupo_orden" => 6
       ),
       'Ventas' => array(
           "grupo_orden" => 6
        )
        ],
    'prefijo'       => 'atc',
    'menu'          => array(
        'nombre'    => 'Anticipos' ,
        'url'       => 'anticipos/listar',
        'orden'     => 3
    ),
    'permisos'  => array(
        'acceso'        => 'Acceso',
        'listar'   => 'Listar'
    )
);

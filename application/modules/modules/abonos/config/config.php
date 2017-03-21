<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre'        => 'Abonos',
    'descripcion'	=> 'Modulo para Administracion de Abonos.',
    'autor'         => 'Pensanomica Team',
    'icono'         => 'fa-shopping-cart',
    'version'       => '1.0',
    'tipo'          => 'addon', // core, addon
    'grupo'         => 'Abonos',
    'agrupador'     => 'Compras',
    'prefijo'       => 'abo',
//    'menu'          => array(
//        'nombre'    => 'Abonos' ,
//        'url'       => 'abonos/listar'
//        //'orden'     => 3
//    ),
    'permisos'  => array(
        'acceso'        => 'Acceso',
        'listar__ver'   => 'Listar'
    )
);

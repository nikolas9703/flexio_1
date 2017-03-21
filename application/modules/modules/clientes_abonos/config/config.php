<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre'        => 'Clientes abonos',
    'descripcion'   => 'Modulo para Administracion de abonos de clientes.',
    'autor'         => 'Pensanomica Team',
    'icono'         => 'fa-line-chart',
    'version'       => '1.0',
    'tipo'          => 'addon', // core, addon
    'grupo'         => 'Abonos',
    'agrupador'     => 'Abonos',
    'prefijo'       => 'cab',
//    'menu'          => array(
//        'nombre'    => 'Abono' ,
//        'url'       => 'clientes_abonos/listar',
//        'orden'     => 5
//    ),
    'permisos'  => array(
        'acceso' => 'Acceso'
    )
);

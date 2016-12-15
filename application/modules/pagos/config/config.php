<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre'        => 'Pagos',
    'descripcion'	=> 'Modulo para Administracion de Pagos.',
    'autor'         => 'Pensanomica Team',
    'icono'         => 'fa-shopping-cart',
    'version'       => '1.0',
    'tipo'          => 'addon', // core, addon
    'grupo'         => 'Compras',
    'agrupador'		=> array(
        'Compras' => array(
            "grupo_orden" => 5
        ),
         'Contratos' => array(
            "grupo_orden" => 2
        ),
    ),
    'prefijo'       => 'pag',
    'menu'          => array(
        'nombre'    => 'Pagos' ,
        'url'       => 'pagos/listar',
        'orden'     => 4
    ),
    'permisos'  => array(
        'acceso'        => 'Acceso',
        'listar__ver'   => 'Listar'
    )
);

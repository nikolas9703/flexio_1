<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre'        => 'Catalogos de Inventario',
    'descripcion'   => 'Modulo para Administracion de Catalogos de Inventario.',
    'autor'         => 'Pensanomica Team',
    'icono'         => 'fa-cubes',
    'version'       => '1.0',
    'tipo'          => 'addon', // core, addon
    'grupo'         => 'Configuracion',
     'agrupador'		=> array(
        'Inventario' => array(
            "grupo_orden" => 4
        ),
    ),
    'prefijo'       => 'cat_inv',
    'menu' => array(
        'nombre'    => 'Configuracion' ,
        'url'       => 'catalogos_inventario/listar',
        'orden'     => 40
    ),
    'permisos'      => array(
        'acceso' => 'Acceso',
    )
);
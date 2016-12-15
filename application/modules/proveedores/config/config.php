<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre'        => 'Proveedores',
    'descripcion'   => 'Modulo para Administracion de Proveedores.',
    'autor'         => 'Pensanomica Team',
    'icono'         => 'fa-shopping-cart',
    'version'       => '1.0',
    'tipo'          => 'addon', // core, addon
    'grupo'         => 'Compras',
    //'agrupador'     => 'Compras',
    'agrupador'		=> array(
        'Compras' => array(
            "grupo_orden" => 1
        ),
         'Contratos' => array(
            "grupo_orden" => 2
        ),
     ),
    'prefijo'       => 'pro',
    'menu' => array(
        'nombre'    => 'Proveedores' ,
        'url'       => 'proveedores/listar',
    	'orden'		=> 0
    ),
    'permisos'      => array(
        'acceso'                        => 'Acceso',
    	'listar__exportarProveedores'   => 'Exportar',
    	'ver__editarProveedor'          => 'Editar',
    )
);
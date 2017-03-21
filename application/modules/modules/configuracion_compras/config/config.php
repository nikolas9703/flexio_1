<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre'        => 'Configuración',
    'descripcion'   => 'Modulo para Administracion de Configuración de Compras.',
    'autor'         => 'Pensanomica Team',
    'icono'         => 'fa-shopping-cart',
    'version'       => '1.0',
    'tipo'          => 'addon', // core, addon
    'grupo'         => 'Configuración',
	'agrupador_orden'     => 7,
    //'agrupador'     => 'Compras',
    'agrupador'		=> array(
        'Compras' => array(
            "grupo_orden" => 11
        ),
    ),
    'prefijo'       => 'che',
    'menu' => array(
        'nombre'    => 'Configuración' ,
		'url'       => 'configuracion_compras/listar'
    ),
    'permisos'      => array(
        'acceso' => 'Acceso',
    )
);
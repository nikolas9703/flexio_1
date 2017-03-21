<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Facturas de compras',
	'descripcion'	=> 'Modulo para Administracion de facturas de compras.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-shopping-cart',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Facturas de Compras',
	//'agrupador'		=> 'Compras',
    'agrupador'		=> array(
        'Compras' => array(
            "grupo_orden" => 4
        ),
    ),
	'prefijo'		=> 'faccom',
	'menu' => array(
            'nombre'    => 'Facturas de Compras' ,
            'url'       => 'facturas_compras/listar',
            'orden'     => 3
	),
        'permisos'		=> array(
            'acceso'        => 'Acceso',
            'listar__ver'   => 'Listar'
	)
);

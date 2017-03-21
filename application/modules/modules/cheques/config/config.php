<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Cheques',
	'descripcion'	=> 'Modulo para Administracion de cheques.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-shopping-cart',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Cheques',
	//'agrupador'		=> 'Compras',
    'agrupador'		=> array(
        'Compras' => array(
            "grupo_orden" => 7
        ),
    ),
	'orden_agrupador'		=> 1,
	'prefijo'		=> 'che',
	'menu' => array(
		'nombre' =>'Cheques' ,
		'url' => 'cheques/listar',
		'orden'=> 5
	),
        'permisos'		=> array(
		'acceso' => 'Acceso',
    'listar__ver' => 'Listar',
              //  'editar' => 'Editar',
              //  'crear'  => 'Crear'
	)
);

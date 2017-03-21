<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'                => 'Cheques',
	'descripcion'           => 'Modulo para Administracion de chequeras.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-shopping-cart',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Chequeras',
	'agrupador'		=> 'Compras',
	'prefijo'		=> 'che',
	'menu' => array(
		'nombre' =>'Cheques' ,
		'url' => 'cheques/listar',
		'orden'=> 3
	),
        'permisos'		=> array(
		'acceso'        => 'Acceso',
                'listar__ver'   => 'Listar',
              //  'editar' => 'Editar',
              //  'crear'  => 'Crear'
	)
);

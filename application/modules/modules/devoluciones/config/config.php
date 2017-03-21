<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Devoluciones',
	'descripcion'	=> 'Modulo para Administracion de Devoluciones.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-line-chart',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Devoluciones',
	//'agrupador'		=> 'Ventas',
    'agrupador'		=> array(
        'Ventas' => array(
            "grupo_orden" => 6
        ),
    ),
	'prefijo'		=> 'dev',
	'menu' => array(
		'nombre' =>'Devoluciones' ,
		'url' => 'devoluciones/listar',
		'orden'=> 6
	),
        'permisos'		=> array(
            'acceso'        => 'Acceso',
	)
);

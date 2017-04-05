<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Remesas Salientes',
	'descripcion'	=> 'Modulo para Administracion de Remesas.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-line-chart',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Remesas',
	'prefijo'		=> 'seg',
	'agrupador'		=> array(
        'Seguros' => array(
            "grupo_orden" => 7
        ),
    ),
	'menu' => array(
		array(
			'nombre' =>'Remesas salientes' ,
			'url' => 'remesas/listar',
			'orden'=> 2,
		),
		array(
			'nombre' =>'Remesas entrantes' ,
			'url' => 'remesas_entrantes/listar',
			'orden'=> 1
		),		
	),
	'permisos'		=> array(
		'acceso' => 'Acceso',
	)
);

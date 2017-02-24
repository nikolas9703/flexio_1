<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Endosos',
	'descripcion'	=> 'Modulo para Administracion de Endosos.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-book',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Producción',
	'prefijo'		=> 'end',
	'agrupador'		=> array(
        'Seguros' => array(
            "grupo_orden" => 1
        ),
    ),
	'menu' => array(
			'nombre' =>'Endosos' ,
			'url' => 'endosos/listar',
			'orden'=> 3,	
	),
	'permisos'		=> array(
		'acceso' => 'Acceso',
	)
);

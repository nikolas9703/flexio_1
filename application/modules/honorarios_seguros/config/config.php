<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Honorarios de seguros',
	'descripcion'	=> 'Modulo para Administracion de honorarios de seguros.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-line-chart',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Honorarios',
  	'prefijo'		=> 'hon',
  	'agrupador'		=> array(
        'Seguros' => array(
            "grupo_orden" => 8
        ),
    ),
	'menu' => array(
		'nombre' =>'Honorarios' ,
		'url' => 'honorarios_seguros/listar',
		'orden'=> 1
	),
    'permisos'		=> array(
		'acceso' => 'Acceso',
	)
);

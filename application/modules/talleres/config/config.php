<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre'        => 'Equipos de trabajo',
    'descripcion'   => 'Modulo para Administracion de Taller.',
    'autor'         => 'Pensanomica Team',
    'icono'         => 'fa-wrench',
    'version'       => '1.0',
    'tipo'          => 'addon', // core, addon
    'grupo'			=> 'Servicios',
    'agrupador'		=> array(
		'Servicios' => array(
			"grupo_orden" => 4
		),
	),
    'prefijo'       => 'tal',
    'menu' => array(
        'nombre'    => 'Equipos de trabajo' ,
        'url'       => 'talleres/listar',
        'orden'     => 0
    )
);
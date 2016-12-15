<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Acciones de Personal',
	'descripcion'	=> 'Modulo contenedor de accion de personal.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-file-text',
	'version'		=> '1.0',
	'tipo'			=> 'addon',
	'grupo'			=> 'Acciones de Personal',
	//'agrupador'		=> 'Recursos Humanos',
    'agrupador'		=> array(
        'Recursos Humanos' => array(
            "grupo_orden" => 2
        ),
     ),
    
	'prefijo'		=> 'acper',
	'menu' => array(
		'nombre' =>'Acciones de Personal' ,
		'url' => 'accion_personal/listar',
		'orden'=> 1
	),
    'permisos'		=> array(
		'acceso' => 'Acceso',
	)
);

<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Plantillas',
	'descripcion'	=> 'Modulo de administracion de plantillas de cartas para colaboradores.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-user',
	'version'		=> '1.0',
	'tipo'			=> 'addon',
	'grupo'			=> 'Plantillas',
	//'agrupador'		=> 'Recursos Humanos',
    'agrupador'		=> array(
        'Recursos Humanos' => array(
            "grupo_orden" =>6
        ),
     ),
	'prefijo'		=> 'ptl',
	'menu' => array(
		'nombre' =>'Plantillas' ,
		'url' => 'plantillas/listar',
		'orden'=> 4
	),
    'permisos'		=> array(
		'acceso' => 'Acceso',
	)
);

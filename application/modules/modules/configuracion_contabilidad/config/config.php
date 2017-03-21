<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Configuraciones de Contabilidad',
	'descripcion'	=> 'Modulo para Configuracion de Contabiliad.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-calculator',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Configuracion',
	//'agrupador'		=> 'Contabilidad',
    'agrupador'		=> array(
        'Contabilidad' => array(
            "grupo_orden" => 5
        ),
    ),
	'prefijo'		=> 'contab',
	'menu' => array(
		'nombre' =>'Configuracion' ,
		'url' => 'configuracion_contabilidad',
		'orden' => 9
	),
        'permisos'		=> array(
		    'acceso' => 'Acceso',
	)
);

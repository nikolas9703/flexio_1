<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Comisiones de seguros',
	'descripcion'	=> 'Modulo para Administracion de comisiones de seguros.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-line-chart',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Comisiones',
  	'prefijo'		=> 'fse',
  	'agrupador'		=> array(
        'Seguros' => array(
            "grupo_orden" => 4
        ),
    ),
	'menu' => array(
		'nombre' =>'Comisiones' ,
		'url' => 'comisiones_seguros/listar',
		'orden'=> 7
	),
    'permisos'		=> array(
		'acceso' => 'Acceso',
	)
);

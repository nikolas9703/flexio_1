<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Conciliaciones',
	'descripcion'	=> 'Modulo para Administración de conciliaciones bancarias.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-fa-calculator',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Trans Contables',
	'agrupador'		=> 'Contabilidad',
	'agrupador_orden'     =>4,
	'menu' => array(
            'nombre' => 'Conciliacion Bancaria',
            'url' 	 => 'conciliaciones/listar',
            'orden'	 => 2
	),
	'prefijo' => 'conc',
        'permisos'	 => array(
            'acceso' => 'Acceso'
	)
);

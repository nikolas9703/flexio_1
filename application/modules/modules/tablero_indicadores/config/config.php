<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Tablero de Indicadores',
	'descripcion'	=> 'Modulo para ver los indicadores de los distintos modulos.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-tachometer',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Dashboard',
	'prefijo'		=> 'tbl',
        'permisos'		=> array(
            'acceso'    => 'Acceso',
	)
);
<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
    'nombre'        => 'Reportes Financieros',
    'descripcion'   => 'Modulo para Administracion de Reportes Financieros.',
    'autor'         => 'Pensanomica Team',
    'icono'         => 'fa-calculator',
    'version'       => '1.0',
    'tipo'          => 'addon', // core, addon
    'grupo'         => 'Reportes Financieros',
	'agrupador'		=> array(
		'Contabilidad' => array(
			"grupo_orden" => 4
		),
    ),
    'prefijo'       => 'rep',
    'menu' => array(
        'nombre'    => 'Reportes Financieros' ,
        'url'       => 'reportes_financieros/listar',
        'orden'  => 8
    ),
    'permisos'      => array(
        'acceso'    => 'Acceso',
    )
);

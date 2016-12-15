<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Ajustadores',
	'descripcion'	=> 'Modulo para Ajustadores.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-child',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Administración',
	'agrupador'		=> array(
                'Seguros' => array(
                    "grupo_orden" => 3
                ),
        ),
	'prefijo'		=> 'seg',
	'menu' => array(
		'nombre' => 'Ajustadores' ,
		'url' => 'ajustadores/listar',
                'orden'		=> 2
	),
        'permisos'		=> array(
            'acceso' => 'Acceso',
            'listar-ajustadores__exportarAjustadores'   => 'Exportar Ajustadores',
            'ver-ajustadores__editarAjustadores'          => 'Editar Ajustadores',
	)
);
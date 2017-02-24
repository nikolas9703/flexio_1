<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Aseguradoras',
	'descripcion'	=> 'Aseguradoras',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-unlock-alt',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Administración',
        'agrupador'		=> array(
        'Seguros' => array(
            'grupo_orden' => 6
        ),
        ),
        'permisos'  => array(
			'acceso' => 'Acceso'
	),
	'prefijo'   => 'sol',
	'menu' => array(
		
	 'nombre' =>'Aseguradoras' ,
	 'url' => 'aseguradoras/listar',
	 'orden'=> 1
					   
	)
);
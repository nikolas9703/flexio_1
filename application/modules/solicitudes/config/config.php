<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Solicitudes Seguros',
	'descripcion'	=> 'Solicitudes Seguros',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-unlock-alt',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Producción',
        'agrupador'		=> array(
        'Seguros' => array(
            'grupo_orden' => 1
        ),
        ),
        'permisos'  => array(
	'acceso' => 'Acceso'
	),
        'prefijo'   => 'sol',
        'menu' => array(
            
         'nombre' =>'Solicitudes' ,
         'url' => 'solicitudes/listar',
         'orden'=> 1
                           
        ),
);
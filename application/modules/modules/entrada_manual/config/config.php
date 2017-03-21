<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Entrada Manuales',
	'descripcion'	=> 'Modulo para Administracion de entrada manual.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-calculator',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Trans Contables',
	'agrupador'		=> 'Contabilidad',
	'prefijo'		=> 'contab',
	'menu' => array(
		'nombre' =>'Entrada Manuales' ,
		'url' => 'entrada_manual/listar',
		'orden' => 1
	),
        'permisos'		=> array(
		    'acceso' => 'Acceso',
              //  'listar' => 'Listas',
              //  'editar' => 'Editar',
              //  'crear'  => 'Crear'
	)
);

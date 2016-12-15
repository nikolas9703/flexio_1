<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Contactos',
	'descripcion'	=> 'Modulo para Administracion de contactos.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-briefcase',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Ventas',
	'agrupador'		=> 'Ventas',
	'menu' => array(
		//'nombre' => 'Contactos' ,
		//'url' => 'contactos/listar-contactos'
	),
	'prefijo'		=> 'con',
        'permisos'		=> array(
            'acceso'                                        => 'Acceso',
            'listar-contactos__agregar_campana_mercadeo'    => 'Agregar a Campa&ntilde;a',
            'listar-contactos__exportar_contacto'           => 'Exportar Contacto',
            'ver-contacto__editarContacto'                  => 'Editar Contacto'
	)
);

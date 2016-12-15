<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Agentes',
	'descripcion'	=> 'Modulo para Administracion de agentes.',
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
	'prefijo'		=> 'agt',
	'menu' => array(
		'nombre' => 'Agentes' ,
		'url' => 'agentes/listar',
            'orden'=> 1
	),
        'permisos'		=> array(
            'acceso' => 'Acceso',
            'listar-agentes__exportarAgentes'   => 'Exportar Agentes',
            'ver-agente__editarAgente'          => 'Editar Agente',
	)
);
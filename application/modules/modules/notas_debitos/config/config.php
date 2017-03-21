<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Notas de Debito',
	'descripcion'	=> 'Modulo para Administracion de Notas de Debito.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-shopping-cart',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Notas de crédito de proveedor',
	//'agrupador'		=> 'Compras',
    'agrupador'		=> array(
        'Compras' => array(
            "grupo_orden" => 6
        ),
    ),
	'prefijo'		=> 'Compras',
	'menu' => array(
		'nombre' =>'Notas de crédito de proveedor' ,
		'url' => 'notas_debitos/listar',
		'orden'=> 7
	),
    'permisos'		=> array(
		'acceso' => 'Acceso'
	)
);

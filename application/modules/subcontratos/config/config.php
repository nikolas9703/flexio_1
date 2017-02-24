<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Subcontratos',
	'descripcion'	=> 'Modulo para Administración de subcontratos.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-line-chart',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Precio fijo con proveedores',
	//'agrupador'		=> 'Contratos',

        'agrupador'		=> array(

             'Contratos' => array(
                "grupo_orden" => 3
            ),
         ),
	'menu' => array(
		'nombre' =>'Subcontratos' ,
		'url' 	 => 'subcontratos/listar',
		'orden'	 => 3
	),
	'prefijo'	 => 'sub',
    'permisos'	 => array(
		'acceso' => 'Acceso',
		'ver__editarSubcontrato' => 'Editar subcontrato'
	)
);

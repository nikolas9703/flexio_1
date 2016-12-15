<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(	
	'nombre'        => 'Documentos',
	'descripcion'	=> 'Modulo para Administracion de archivos.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-child',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'prefijo'		=> 'col',
	'grupo'			=> 'Documentos',
    'agrupador'		=> array(
        'Recursos Humanos' => array(
            "grupo_orden" => 7
        ),
        'Compras' => array(
            "grupo_orden" => 9
        ),
         'Contratos' => array(
            "grupo_orden" => 9
        ),
    ),
 	'menu' => array(
		'nombre' =>'Documentos' ,
		'url' => 'documentos/listar',
		'orden'=> 5
	),
	'permisos' => array(
		'acceso'                                    => 'Acceso',
		'listar-documentos__eliminar_documentos'    => 'Eliminar Documentos',
		'listar-documentos__actualizar_archivo'     => 'Actualizar Documentos',
		'listar-documentos__menu'                   => 'Ver Link en el Menu',
	)
);
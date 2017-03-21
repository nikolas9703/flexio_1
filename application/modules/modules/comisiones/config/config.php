<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Comisiones',
	'descripcion'	=> 'Modulo para Administracion de comisiones.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-child',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Pagos Extraordinarios',
	//'agrupador'		=> 'Nomina',
    'agrupador'		=> array(
        'Nomina' => array(
            "grupo_orden" => 5
        )
    ),
    
	'agrupador_orden'     => 3,
	'prefijo'		=> 'com',
	 'menu' => array(
 						'nombre' =>'Pagos Extraordinarios' ,
						'url' => 'comisiones/listar',
	 					'orden'		=> 2
  	 ),	
      'permisos'		=> array(
		'acceso' => 'Acceso',
    	'listar__exportarComision'   => 'Exportar',
    	'listar__anularComision'   => 'Anular Comision',
    	'ver__editarComision'         => 'Editar',
    	'ver__eliminarComision'         => 'Eliminar'
 	)
);



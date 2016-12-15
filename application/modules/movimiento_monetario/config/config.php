<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Movimiento Monetario',
	'descripcion'	=> 'Modulo movimiento monetario',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-calculator',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Mov Monetario',
	//'agrupador'		=> 'Contabilidad',
    'agrupador'		=> array(
        'Contabilidad' => array(
            "grupo_orden" => 2
        ),
    ),
    'prefijo'		=> 'mov',
	'menu' => 
        array(
            array(
        		'nombre' =>'Recibos de dinero',
        		'url' => 'movimiento_monetario/listar_recibos',
        		'orden'=> 1
    	     ),
            array(
                'nombre' =>'Retiros de dinero',
        		'url' => 'movimiento_monetario/listar_retiros',
        		'orden'=> 2
            ),
       ),
	
    'permisos'		=> array(
		'acceso' => 'Acceso',
    	//'listar' => 'Clientes',
    	//'crear' => 'Crear'
	)
);

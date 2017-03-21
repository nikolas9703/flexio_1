<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['modulo_config'] = array(
	'nombre'        => 'Configuracion Planilla',
	'descripcion'	=> 'Modulo para la  Configuracion de Planilla.',
	'autor'			=> 'Pensanomica Team',
	'icono'			=> 'fa-child',
	'version'		=> '1.0',
	'tipo'			=> 'addon', // core, addon
	'grupo'			=> 'Configuración',
	//'agrupador'		=> 'Nomina',
    'agrupador_orden'     => '6',
    'agrupador'		=> array(
        'Nomina' => array(
            "grupo_orden" => 6
        )
     ),
	'prefijo'		=> 'pln',
 		'menu' => array(
				'nombre' =>'Configuración' ,
				'url' => 'configuracion_planilla/configuracion',
				'orden'		=> 6
		),
        'permisos'		=> array(
            	'acceso'    => 'Acceso',
        		'configuracion__tabDeducciones' => 'Deducciones',
        		'configuracion__tabBeneficios' => 'Beneficios',
        		'configuracion__tabAcumulados' => 'Acumulados',
        		'configuracion__tabDiasFeriados' => 'Dias Feriados',
        		'configuracion__tabRecargos' => 'Recargos'
 	)
);

 